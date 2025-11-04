<?php
/**
 * Admin interface class
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGP_Admin {
    
    private static $instance = null;
    
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
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_tgp_create_project', array($this, 'ajax_create_project'));
        add_action('wp_ajax_tgp_update_project', array($this, 'ajax_update_project'));
        add_action('wp_ajax_tgp_delete_project', array($this, 'ajax_delete_project'));
        add_action('wp_ajax_tgp_import_keywords', array($this, 'ajax_import_keywords'));
        add_action('wp_ajax_tgp_get_projections', array($this, 'ajax_get_projections'));
        add_action('wp_ajax_tgp_get_keywords', array($this, 'ajax_get_keywords'));
        add_action('wp_ajax_tgp_calculate_roi', array($this, 'ajax_calculate_roi'));
        add_action('wp_ajax_tgp_update_project_order', array($this, 'ajax_update_project_order'));
        add_action('wp_ajax_tgp_update_keyword', array($this, 'ajax_update_keyword'));
        add_action('wp_ajax_tgp_delete_keyword', array($this, 'ajax_delete_keyword'));
        add_action('wp_ajax_tgp_get_keyword', array($this, 'ajax_get_keyword'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Traffic Growth Projection',
            'Traffic Growth',
            'manage_options',
            'traffic-growth-projection',
            array($this, 'render_page'),
            'dashicons-chart-line',
            30
        );
    }
    
    /**
     * Enqueue CSS and JS
     */
    public function enqueue_assets($hook) {
        if (strpos($hook, 'traffic-growth-projection') === false) {
            return;
        }
        
        // Enqueue Chart.js (bundled version)
        wp_enqueue_script('chartjs', TGP_PLUGIN_URL . 'assets/js/chart.min.js', array(), '4.4.0', true);
        
        // Enqueue custom styles
        wp_enqueue_style('tgp-admin-styles', TGP_PLUGIN_URL . 'assets/css/admin.css', array(), TGP_VERSION);
        
        // Enqueue custom scripts
        wp_enqueue_script('tgp-admin-scripts', TGP_PLUGIN_URL . 'assets/js/admin.js', array('chartjs'), TGP_VERSION, true);
        
        // Localize script
        wp_localize_script('tgp-admin-scripts', 'tgpAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tgp_nonce')
        ));
    }
    
    /**
     * Render main page (dashboard or project view)
     */
    public function render_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only operation
        if (isset($_GET['project_id'])) {
            $this->render_project_view();
        } else {
            $this->render_dashboard();
        }
    }
    
    /**
     * Render dashboard
     */
    private function render_dashboard() {
        $db = TGP_Database::get_instance();
        $projects = $db->get_projects();
        
        include TGP_PLUGIN_DIR . 'templates/dashboard.php';
    }
    
    /**
     * Render project view
     */
    private function render_project_view() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only operation
        if (!isset($_GET['project_id'])) {
            wp_die('Project ID required');
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only operation
        $project_id = intval($_GET['project_id']);
        $db = TGP_Database::get_instance();
        $project = $db->get_project($project_id);
        
        if (!$project) {
            wp_die('Project not found');
        }
        
        $keyword_counts = $db->get_keyword_counts($project_id);
        
        include TGP_PLUGIN_DIR . 'templates/project-view.php';
    }
    
    /**
     * AJAX: Create project
     */
    public function ajax_create_project() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
        $conversion_rate_low = isset($_POST['conversion_rate_low']) ? floatval($_POST['conversion_rate_low']) : 1.00;
        $conversion_rate_high = isset($_POST['conversion_rate_high']) ? floatval($_POST['conversion_rate_high']) : 5.00;
        $cltv = isset($_POST['cltv']) ? floatval($_POST['cltv']) : 0.00;
        
        if (empty($name)) {
            wp_send_json_error('Project name is required');
        }
        
        $db = TGP_Database::get_instance();
        $project_id = $db->create_project(array(
            'name' => $name,
            'description' => $description,
            'conversion_rate_low' => $conversion_rate_low,
            'conversion_rate_high' => $conversion_rate_high,
            'cltv' => $cltv
        ));
        
        if ($project_id) {
            wp_send_json_success(array(
                'project_id' => $project_id,
                'message' => 'Project created successfully'
            ));
        } else {
            wp_send_json_error('Failed to create project');
        }
    }
    
    /**
     * AJAX: Update project
     */
    public function ajax_update_project() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $project_id = intval($_POST['project_id'] ?? 0);
        
        if (!$project_id) {
            wp_send_json_error('Project ID required');
        }
        
        $data = array();
        
        if (isset($_POST['name'])) {
            $data['name'] = sanitize_text_field(wp_unslash($_POST['name']));
        }
        if (isset($_POST['description'])) {
            $data['description'] = sanitize_textarea_field(wp_unslash($_POST['description']));
        }
        if (isset($_POST['conversion_rate_low'])) {
            $data['conversion_rate_low'] = floatval($_POST['conversion_rate_low']);
        }
        if (isset($_POST['conversion_rate_high'])) {
            $data['conversion_rate_high'] = floatval($_POST['conversion_rate_high']);
        }
        if (isset($_POST['cltv'])) {
            $data['cltv'] = floatval($_POST['cltv']);
        }
        
        $db = TGP_Database::get_instance();
        $result = $db->update_project($project_id, $data);
        
        if ($result !== false) {
            wp_send_json_success('Project updated successfully');
        } else {
            wp_send_json_error('Failed to update project');
        }
    }
    
    /**
     * AJAX: Delete project
     */
    public function ajax_delete_project() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $project_id = intval($_POST['project_id'] ?? 0);
        
        if (!$project_id) {
            wp_send_json_error('Project ID required');
        }
        
        $db = TGP_Database::get_instance();
        $result = $db->delete_project($project_id);
        
        if ($result !== false) {
            wp_send_json_success('Project deleted successfully');
        } else {
            wp_send_json_error('Failed to delete project');
        }
    }
    
    /**
     * AJAX: Import keywords
     */
    public function ajax_import_keywords() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $project_id = intval($_POST['project_id'] ?? 0);
        
        if (!$project_id) {
            wp_send_json_error('Project ID required');
        }
        
        // Handle file upload
        $upload_result = TGP_Importer::handle_upload();
        
        if (isset($upload_result['error'])) {
            wp_send_json_error($upload_result['error']);
        }
        
        // Clear existing keywords if requested
        if (isset($_POST['clear_existing']) && $_POST['clear_existing'] === 'yes') {
            $db = TGP_Database::get_instance();
            $db->delete_project_keywords($project_id);
        }
        
        // Import keywords
        $import_result = TGP_Importer::import_to_project($project_id, $upload_result['data']);
        
        wp_send_json_success(array(
            'imported' => $import_result['imported'],
            'errors' => $import_result['errors'],
            'message' => "Successfully imported {$import_result['imported']} keywords"
        ));
    }
    
    /**
     * AJAX: Get projections
     */
    public function ajax_get_projections() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $project_id = intval($_POST['project_id'] ?? 0);
        $months = intval($_POST['months'] ?? 12);
        
        if (!$project_id) {
            wp_send_json_error('Project ID required');
        }
        
        $projections = TGP_Calculator::calculate_projections_by_category($project_id, $months);
        $total = TGP_Calculator::calculate_total_projection($project_id, $months);
        
        wp_send_json_success(array(
            'projections' => $projections,
            'total' => $total
        ));
    }
    
    /**
     * AJAX: Get keywords
     */
    public function ajax_get_keywords() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        $category = isset($_POST['category']) ? sanitize_text_field(wp_unslash($_POST['category'])) : '';
        
        if (!$project_id) {
            wp_send_json_error('Project ID required');
        }
        
        $db = TGP_Database::get_instance();
        $keywords = $db->get_keywords($project_id, $category ?: null);
        
        wp_send_json_success(array('keywords' => $keywords));
    }
    
    /**
     * AJAX: Calculate ROI
     */
    public function ajax_calculate_roi() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $traffic = intval($_POST['traffic'] ?? 0);
        $conversion_rate_low = floatval($_POST['conversion_rate_low'] ?? 1.00);
        $conversion_rate_high = floatval($_POST['conversion_rate_high'] ?? 5.00);
        $cltv = floatval($_POST['cltv'] ?? 0);
        $investment = floatval($_POST['investment'] ?? 0);
        
        $roi = TGP_Calculator::calculate_roi($traffic, $conversion_rate_low, $conversion_rate_high, $cltv, $investment);
        
        wp_send_json_success($roi);
    }
    
    /**
     * AJAX: Update project order
     */
    public function ajax_update_project_order() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON data sanitized after decode
        $order_json = isset($_POST['order']) ? wp_unslash($_POST['order']) : '';
        $order = json_decode($order_json, true);
        
        if (!is_array($order)) {
            $order = array();
        }
        
        if (empty($order)) {
            wp_send_json_error('No order data provided');
        }
        
        $db = TGP_Database::get_instance();
        $result = $db->update_project_order($order);
        
        if ($result) {
            wp_send_json_success('Project order updated successfully');
        } else {
            wp_send_json_error('Failed to update project order');
        }
    }
    
    /**
     * AJAX: Get keyword
     */
    public function ajax_get_keyword() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $keyword_id = intval($_POST['keyword_id'] ?? 0);
        
        if (!$keyword_id) {
            wp_send_json_error('Keyword ID required');
        }
        
        $db = TGP_Database::get_instance();
        $keyword = $db->get_keyword($keyword_id);
        
        if ($keyword) {
            wp_send_json_success($keyword);
        } else {
            wp_send_json_error('Keyword not found');
        }
    }
    
    /**
     * AJAX: Update keyword
     */
    public function ajax_update_keyword() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $keyword_id = intval($_POST['keyword_id'] ?? 0);
        
        if (!$keyword_id) {
            wp_send_json_error('Keyword ID required');
        }
        
        $data = array();
        
        if (isset($_POST['keyword'])) {
            $data['keyword'] = sanitize_text_field(wp_unslash($_POST['keyword']));
        }
        if (isset($_POST['search_volume'])) {
            $data['search_volume'] = intval($_POST['search_volume']);
        }
        if (isset($_POST['difficulty'])) {
            $data['difficulty'] = floatval($_POST['difficulty']);
        }
        if (isset($_POST['estimated_traffic'])) {
            $data['estimated_traffic'] = intval($_POST['estimated_traffic']);
        }
        if (isset($_POST['current_ranking'])) {
            $data['current_ranking'] = $_POST['current_ranking'] !== '' ? intval($_POST['current_ranking']) : null;
        }
        if (isset($_POST['expected_ranking'])) {
            $data['expected_ranking'] = $_POST['expected_ranking'] !== '' ? intval($_POST['expected_ranking']) : null;
        }
        if (isset($_POST['category'])) {
            $data['category'] = sanitize_text_field(wp_unslash($_POST['category']));
        }
        
        $db = TGP_Database::get_instance();
        $result = $db->update_keyword($keyword_id, $data);
        
        if ($result !== false) {
            wp_send_json_success('Keyword updated successfully');
        } else {
            wp_send_json_error('Failed to update keyword');
        }
    }
    
    /**
     * AJAX: Delete keyword
     */
    public function ajax_delete_keyword() {
        check_ajax_referer('tgp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $keyword_id = intval($_POST['keyword_id'] ?? 0);
        
        if (!$keyword_id) {
            wp_send_json_error('Keyword ID required');
        }
        
        $db = TGP_Database::get_instance();
        $result = $db->delete_keyword($keyword_id);
        
        if ($result !== false) {
            wp_send_json_success('Keyword deleted successfully');
        } else {
            wp_send_json_error('Failed to delete keyword');
        }
    }
}

