<?php
/**
 * Database management class
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGP_Database {
    
    private static $instance = null;
    private $table_keywords;
    private $table_projects;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        global $wpdb;
        $this->table_keywords = $wpdb->prefix . 'tgp_keywords';
        $this->table_projects = $wpdb->prefix . 'tgp_projects';
    }
    
    /**
     * Upgrade database schema
     */
    public function upgrade_database() {
        global $wpdb;
        
        // Check if sort_order column exists
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time schema check during upgrade
        $column_exists = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = %s 
                AND TABLE_NAME = %s 
                AND COLUMN_NAME = 'sort_order'",
                DB_NAME,
                $this->table_projects
            )
        );
        
        // Add sort_order column if it doesn't exist
        if (empty($column_exists)) {
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            // Schema upgrade required - adding sort_order column for drag-and-drop functionality
            $wpdb->query(
                "ALTER TABLE {$this->table_projects} 
                ADD COLUMN sort_order int(11) DEFAULT 0 AFTER cltv,
                ADD INDEX sort_order (sort_order)"
            );
            // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        }
        
        // Clear cache after upgrade
        wp_cache_delete('tgp_all_projects', 'tgp');
    }
    
    /**
     * Create database tables
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Projects table
        $sql_projects = "CREATE TABLE IF NOT EXISTS {$this->table_projects} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            conversion_rate_low decimal(5,2) DEFAULT 1.00,
            conversion_rate_high decimal(5,2) DEFAULT 5.00,
            cltv decimal(10,2) DEFAULT 0.00,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY sort_order (sort_order)
        ) $charset_collate;";
        
        // Keywords table
        $sql_keywords = "CREATE TABLE IF NOT EXISTS {$this->table_keywords} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            project_id bigint(20) NOT NULL,
            keyword varchar(500) NOT NULL,
            search_volume int(11) DEFAULT 0,
            difficulty decimal(5,2) DEFAULT 0.00,
            estimated_traffic int(11) DEFAULT 0,
            current_ranking int(11) DEFAULT NULL,
            expected_ranking int(11) DEFAULT NULL,
            category varchar(100) DEFAULT 'Select',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY project_id (project_id),
            KEY category (category)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_projects);
        dbDelta($sql_keywords);
    }
    
    /**
     * Create a new project
     */
    public function create_project($data) {
        global $wpdb;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table, direct query needed
        $wpdb->insert(
            $this->table_projects,
            array(
                'name' => sanitize_text_field($data['name']),
                'description' => sanitize_textarea_field($data['description'] ?? ''),
                'conversion_rate_low' => floatval($data['conversion_rate_low'] ?? 1.00),
                'conversion_rate_high' => floatval($data['conversion_rate_high'] ?? 5.00),
                'cltv' => floatval($data['cltv'] ?? 0.00)
            ),
            array('%s', '%s', '%f', '%f', '%f')
        );
        
        $project_id = $wpdb->insert_id;
        
        // Clear cache
        wp_cache_delete('tgp_all_projects', 'tgp');
        
        return $project_id;
    }
    
    /**
     * Get all projects
     */
    public function get_projects() {
        global $wpdb;
        
        // Try to get cached results
        $cache_key = 'tgp_all_projects';
        $cached = wp_cache_get($cache_key, 'tgp');
        
        if ($cached !== false) {
            return $cached;
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table with caching implemented
        $results = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}tgp_projects ORDER BY sort_order ASC, created_at DESC" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        );
        
        // Cache for 1 hour
        wp_cache_set($cache_key, $results, 'tgp', HOUR_IN_SECONDS);
        
        return $results;
    }
    
    /**
     * Get a single project
     */
    public function get_project($id) {
        global $wpdb;
        
        // Try to get cached result
        $cache_key = 'tgp_project_' . $id;
        $cached = wp_cache_get($cache_key, 'tgp');
        
        if ($cached !== false) {
            return $cached;
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table with caching implemented
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}tgp_projects WHERE id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $id
            )
        );
        
        // Cache for 1 hour
        wp_cache_set($cache_key, $result, 'tgp', HOUR_IN_SECONDS);
        
        return $result;
    }
    
    /**
     * Update project
     */
    public function update_project($id, $data) {
        global $wpdb;
        
        $update_data = array();
        $update_format = array();
        
        if (isset($data['name'])) {
            $update_data['name'] = sanitize_text_field($data['name']);
            $update_format[] = '%s';
        }
        if (isset($data['description'])) {
            $update_data['description'] = sanitize_textarea_field($data['description']);
            $update_format[] = '%s';
        }
        if (isset($data['conversion_rate_low'])) {
            $update_data['conversion_rate_low'] = floatval($data['conversion_rate_low']);
            $update_format[] = '%f';
        }
        if (isset($data['conversion_rate_high'])) {
            $update_data['conversion_rate_high'] = floatval($data['conversion_rate_high']);
            $update_format[] = '%f';
        }
        if (isset($data['cltv'])) {
            $update_data['cltv'] = floatval($data['cltv']);
            $update_format[] = '%f';
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, cache cleared below
        $result = $wpdb->update(
            $this->table_projects,
            $update_data,
            array('id' => $id),
            $update_format,
            array('%d')
        );
        
        // Clear cache
        wp_cache_delete('tgp_project_' . $id, 'tgp');
        wp_cache_delete('tgp_all_projects', 'tgp');
        
        return $result;
    }
    
    /**
     * Delete project and its keywords
     */
    public function delete_project($id) {
        global $wpdb;
        
        // Delete keywords first
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, cache cleared below
        $wpdb->delete($this->table_keywords, array('project_id' => $id), array('%d'));
        
        // Delete project
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, cache cleared below
        $result = $wpdb->delete($this->table_projects, array('id' => $id), array('%d'));
        
        // Clear cache
        wp_cache_delete('tgp_project_' . $id, 'tgp');
        wp_cache_delete('tgp_all_projects', 'tgp');
        wp_cache_delete('tgp_keywords_' . $id, 'tgp');
        
        return $result;
    }
    
    /**
     * Insert keyword
     */
    public function insert_keyword($data) {
        global $wpdb;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table, cache cleared below
        $wpdb->insert(
            $this->table_keywords,
            array(
                'project_id' => intval($data['project_id']),
                'keyword' => sanitize_text_field($data['keyword']),
                'search_volume' => intval($data['search_volume'] ?? 0),
                'difficulty' => floatval($data['difficulty'] ?? 0),
                'estimated_traffic' => intval($data['estimated_traffic'] ?? 0),
                'current_ranking' => !empty($data['current_ranking']) ? intval($data['current_ranking']) : null,
                'expected_ranking' => !empty($data['expected_ranking']) ? intval($data['expected_ranking']) : null,
                'category' => sanitize_text_field($data['category'] ?? 'Select')
            ),
            array('%d', '%s', '%d', '%f', '%d', '%d', '%d', '%s')
        );
        
        $keyword_id = $wpdb->insert_id;
        
        // Clear cache
        wp_cache_delete('tgp_keywords_' . $data['project_id'], 'tgp');
        
        return $keyword_id;
    }
    
    /**
     * Get keywords for a project
     */
    public function get_keywords($project_id, $category = null) {
        global $wpdb;
        
        $cache_key = 'tgp_keywords_' . $project_id . ($category ? '_' . md5($category) : '');
        $cached = wp_cache_get($cache_key, 'tgp');
        
        if ($cached !== false) {
            return $cached;
        }
        
        if ($category) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table with caching implemented
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}tgp_keywords WHERE project_id = %d AND category = %s ORDER BY keyword ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $project_id,
                    $category
                )
            );
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table with caching implemented
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}tgp_keywords WHERE project_id = %d ORDER BY keyword ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $project_id
                )
            );
        }
        
        wp_cache_set($cache_key, $results, 'tgp', HOUR_IN_SECONDS);
        
        return $results;
    }
    
    /**
     * Delete all keywords for a project
     */
    public function delete_project_keywords($project_id) {
        global $wpdb;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, cache cleared below
        $result = $wpdb->delete($this->table_keywords, array('project_id' => $project_id), array('%d'));
        
        // Clear cache
        wp_cache_delete('tgp_keywords_' . $project_id, 'tgp');
        
        return $result;
    }
    
    /**
     * Update project sort order
     */
    public function update_project_order($project_orders) {
        global $wpdb;
        
        foreach ($project_orders as $project_id => $order) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, cache cleared below
            $wpdb->update(
                $this->table_projects,
                array('sort_order' => intval($order)),
                array('id' => intval($project_id)),
                array('%d'),
                array('%d')
            );
        }
        
        // Clear cache
        wp_cache_delete('tgp_all_projects', 'tgp');
        
        return true;
    }
    
    /**
     * Get keyword count by category
     */
    public function get_keyword_counts($project_id) {
        global $wpdb;
        
        $cache_key = 'tgp_keyword_counts_' . $project_id;
        $cached = wp_cache_get($cache_key, 'tgp');
        
        if ($cached !== false) {
            return $cached;
        }
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table with caching implemented
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT category, COUNT(*) as count 
                FROM {$wpdb->prefix}tgp_keywords 
                WHERE project_id = %d 
                GROUP BY category", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $project_id
            ),
            OBJECT_K
        );
        
        wp_cache_set($cache_key, $results, 'tgp', HOUR_IN_SECONDS);
        
        return $results;
    }
    
    /**
     * Update keyword
     */
    public function update_keyword($keyword_id, $data) {
        global $wpdb;
        
        $update_data = array();
        $update_format = array();
        
        if (isset($data['keyword'])) {
            $update_data['keyword'] = sanitize_text_field($data['keyword']);
            $update_format[] = '%s';
        }
        if (isset($data['search_volume'])) {
            $update_data['search_volume'] = intval($data['search_volume']);
            $update_format[] = '%d';
        }
        if (isset($data['difficulty'])) {
            $update_data['difficulty'] = floatval($data['difficulty']);
            $update_format[] = '%f';
        }
        if (isset($data['estimated_traffic'])) {
            $update_data['estimated_traffic'] = intval($data['estimated_traffic']);
            $update_format[] = '%d';
        }
        if (isset($data['current_ranking'])) {
            $update_data['current_ranking'] = !empty($data['current_ranking']) ? intval($data['current_ranking']) : null;
            $update_format[] = '%d';
        }
        if (isset($data['expected_ranking'])) {
            $update_data['expected_ranking'] = !empty($data['expected_ranking']) ? intval($data['expected_ranking']) : null;
            $update_format[] = '%d';
        }
        if (isset($data['category'])) {
            $update_data['category'] = sanitize_text_field($data['category']);
            $update_format[] = '%s';
        }
        
        // Get the keyword to find its project_id for cache clearing
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, cache cleared below
        $keyword = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT project_id FROM {$wpdb->prefix}tgp_keywords WHERE id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $keyword_id
            )
        );
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, cache cleared below
        $result = $wpdb->update(
            $this->table_keywords,
            $update_data,
            array('id' => $keyword_id),
            $update_format,
            array('%d')
        );
        
        // Clear cache
        if ($keyword) {
            wp_cache_delete('tgp_keywords_' . $keyword->project_id, 'tgp');
        }
        
        return $result;
    }
    
    /**
     * Delete keyword
     */
    public function delete_keyword($keyword_id) {
        global $wpdb;
        
        // Get the keyword to find its project_id for cache clearing
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, cache cleared below
        $keyword = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT project_id FROM {$wpdb->prefix}tgp_keywords WHERE id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $keyword_id
            )
        );
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table, cache cleared below
        $result = $wpdb->delete($this->table_keywords, array('id' => $keyword_id), array('%d'));
        
        // Clear cache
        if ($keyword) {
            wp_cache_delete('tgp_keywords_' . $keyword->project_id, 'tgp');
        }
        
        return $result;
    }
    
    /**
     * Get a single keyword
     */
    public function get_keyword($keyword_id) {
        global $wpdb;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time lookup
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}tgp_keywords WHERE id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $keyword_id
            )
        );
        
        return $result;
    }
}

