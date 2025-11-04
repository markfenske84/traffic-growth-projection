<?php
/**
 * Gutenberg Blocks class
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGP_Blocks {
    
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
        add_action('init', array($this, 'register_blocks'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }
    
    /**
     * Check if Gutenberg is available
     */
    public function is_gutenberg_available() {
        return function_exists('register_block_type');
    }
    
    /**
     * Register blocks
     */
    public function register_blocks() {
        if (!$this->is_gutenberg_available()) {
            return;
        }
        
        // Register block editor script
        wp_register_script(
            'tgp-block-editor',
            TGP_PLUGIN_URL . 'assets/js/block-editor.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-api-fetch'),
            TGP_VERSION,
            true
        );
        
        // Register block editor styles
        wp_register_style(
            'tgp-block-editor',
            TGP_PLUGIN_URL . 'assets/css/block-editor.css',
            array('wp-edit-blocks'),
            TGP_VERSION
        );
        
        // Register frontend styles for the block
        wp_register_style(
            'tgp-frontend-styles',
            TGP_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            TGP_VERSION
        );
        
        // Register Chart.js for frontend
        wp_register_script(
            'chartjs-block',
            TGP_PLUGIN_URL . 'assets/js/chart.min.js',
            array(),
            '4.4.0',
            true
        );
        
        // Register frontend script for the block
        wp_register_script(
            'tgp-frontend-scripts',
            TGP_PLUGIN_URL . 'assets/js/frontend.js',
            array('chartjs-block'),
            TGP_VERSION,
            true
        );
        
        // Register the block
        register_block_type('traffic-growth-projection/project-display', array(
            'editor_script' => 'tgp-block-editor',
            'editor_style' => 'tgp-block-editor',
            'style' => 'tgp-frontend-styles',
            'script' => 'tgp-frontend-scripts',
            'render_callback' => array($this, 'render_block'),
            'attributes' => array(
                'projectId' => array(
                    'type' => 'string',
                    'default' => ''
                )
            )
        ));
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('traffic-growth-projection/v1', '/projects', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_projects_list'),
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));
    }
    
    /**
     * Get projects list for REST API
     */
    public function get_projects_list() {
        $db = TGP_Database::get_instance();
        $projects = $db->get_projects();
        
        $projects_list = array();
        foreach ($projects as $project) {
            $projects_list[] = array(
                'value' => strval($project->id),
                'label' => $project->name
            );
        }
        
        return rest_ensure_response($projects_list);
    }
    
    /**
     * Render block on frontend
     */
    public function render_block($attributes) {
        if (empty($attributes['projectId'])) {
            return '<div class="tgp-block-placeholder">Please select a project from the block settings.</div>';
        }
        
        // Enqueue Chart.js
        wp_enqueue_script('chartjs-block');
        
        // Use the existing shortcode rendering
        return do_shortcode('[traffic_projection id="' . esc_attr($attributes['projectId']) . '"]');
    }
}

