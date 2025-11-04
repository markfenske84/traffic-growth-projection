<?php
/**
 * CSV/Excel import handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGP_Importer {
    
    /**
     * Parse uploaded file
     */
    public static function parse_file($file_path, $original_filename = '') {
        // If we have the original filename, use that to check extension
        if ($original_filename) {
            $extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        } else {
            // Fall back to checking the file path
            $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        }
        
        if ($extension === 'csv') {
            return self::parse_csv($file_path);
        }
        
        return array('error' => 'Unsupported file format. Please upload a CSV file.');
    }
    
    /**
     * Parse CSV file
     */
    private static function parse_csv($file_path) {
        global $wp_filesystem;
        
        // Initialize the WP filesystem
        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        
        $data = array();
        $row = 0;
        
        // Read file contents
        $contents = $wp_filesystem->get_contents($file_path);
        
        if ($contents === false) {
            return array('error' => 'Unable to read CSV file');
        }
        
        // Parse CSV line by line
        $lines = explode("\n", $contents);
        
        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }
            
            $row++;
            
            // Skip header row
            if ($row === 1) {
                continue;
            }
            
            // Parse CSV line
            $fields = str_getcsv($line);
            
            // Validate row has enough columns
            if (count($fields) < 7) {
                continue;
            }
            
            $data[] = array(
                'keyword' => trim($fields[0]),
                'search_volume' => intval($fields[1]),
                'difficulty' => floatval($fields[2]),
                'estimated_traffic' => intval($fields[3]),
                'current_ranking' => !empty($fields[4]) ? intval($fields[4]) : null,
                'expected_ranking' => !empty($fields[5]) ? intval($fields[5]) : null,
                'category' => self::validate_category(trim($fields[6]))
            );
        }
        
        return $data;
    }
    
    /**
     * Validate and normalize category
     */
    private static function validate_category($category) {
        $valid_categories = array(
            'Select',
            'Existing Keywords (transactional terms only)',
            'Must-Have Keywords (limit to 50)',
            'New Keywords (limit to 100)'
        );
        
        // Try to match category
        foreach ($valid_categories as $valid_cat) {
            if (stripos($valid_cat, $category) !== false || stripos($category, $valid_cat) !== false) {
                return $valid_cat;
            }
        }
        
        return 'Select';
    }
    
    /**
     * Import keywords into project
     */
    public static function import_to_project($project_id, $keywords_data) {
        $db = TGP_Database::get_instance();
        $imported = 0;
        $errors = array();
        
        // Category limits
        $category_counts = array(
            'Must-Have Keywords (limit to 50)' => 0,
            'New Keywords (limit to 100)' => 0
        );
        
        foreach ($keywords_data as $keyword_data) {
            // Check category limits
            if (isset($category_counts[$keyword_data['category']])) {
                $limit = ($keyword_data['category'] === 'Must-Have Keywords (limit to 50)') ? 50 : 100;
                
                if ($category_counts[$keyword_data['category']] >= $limit) {
                    $errors[] = "Skipped '{$keyword_data['keyword']}' - category limit reached";
                    continue;
                }
                
                $category_counts[$keyword_data['category']]++;
            }
            
            // Add project_id
            $keyword_data['project_id'] = $project_id;
            
            // Insert keyword
            if ($db->insert_keyword($keyword_data)) {
                $imported++;
            } else {
                $errors[] = "Failed to import '{$keyword_data['keyword']}'";
            }
        }
        
        return array(
            'imported' => $imported,
            'errors' => $errors
        );
    }
    
    /**
     * Handle file upload
     */
    public static function handle_upload() {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is checked in AJAX handler before this method is called
        if (!isset($_FILES['tgp_import_file'])) {
            return array('error' => 'No file uploaded');
        }
        
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing -- File upload handling, nonce checked in AJAX handler
        $file = $_FILES['tgp_import_file'];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return array('error' => 'File upload error: ' . $file['error']);
        }
        
        // Validate file extension (primary check)
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($extension !== 'csv') {
            return array('error' => 'Invalid file type. Please upload a CSV file.');
        }
        
        // Validate mime type (secondary check - more lenient)
        $allowed_types = array(
            'text/csv',
            'text/plain',
            'application/csv',
            'application/vnd.ms-excel',
            'text/comma-separated-values',
            'text/x-comma-separated-values',
            'application/octet-stream'
        );
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        // Only reject if mime type is clearly wrong (not text-based)
        if ($mime_type && !in_array($mime_type, $allowed_types) && strpos($mime_type, 'text/') !== 0) {
            return array('error' => 'Invalid file type detected: ' . $mime_type . '. Please ensure you are uploading a valid CSV file.');
        }
        
        // Parse the file (pass original filename for extension check)
        $data = self::parse_file($file['tmp_name'], $file['name']);
        
        if (isset($data['error'])) {
            return $data;
        }
        
        return array('data' => $data);
    }
}

