<?php
/**
 * Frontend display class
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGP_Frontend {
    
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
        add_shortcode('traffic_projection', array($this, 'render_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Only load on pages with the shortcode
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'traffic_projection')) {
            return;
        }
        
        // Enqueue Chart.js
        wp_enqueue_script('chartjs', TGP_PLUGIN_URL . 'assets/js/chart.min.js', array(), '4.4.0', true);
        
        // Enqueue frontend styles
        wp_enqueue_style('tgp-frontend-styles', TGP_PLUGIN_URL . 'assets/css/frontend.css', array(), TGP_VERSION);
        
        // Enqueue frontend scripts
        wp_enqueue_script('tgp-frontend-scripts', TGP_PLUGIN_URL . 'assets/js/frontend.js', array('chartjs'), TGP_VERSION, true);
        
        // Localize script
        wp_localize_script('tgp-frontend-scripts', 'tgpFrontend', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }
    
    /**
     * Render shortcode
     * Usage: [traffic_projection id="1"]
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts);
        
        $project_id = intval($atts['id']);
        
        if (!$project_id) {
            return '<div class="tgp-frontend-error">Please specify a project ID: [traffic_projection id="1"]</div>';
        }
        
        $db = TGP_Database::get_instance();
        $project = $db->get_project($project_id);
        
        if (!$project) {
            return '<div class="tgp-frontend-error">Project not found.</div>';
        }
        
        // Get projections data
        $projections = TGP_Calculator::calculate_projections_by_category($project_id, 12);
        $total = TGP_Calculator::calculate_total_projection($project_id, 12);
        $keywords = $db->get_keywords($project_id);
        
        // Calculate summary stats
        $current_traffic = 0;
        $projected_traffic = 0;
        
        if (!empty($projections['Current Trajectory'])) {
            $current_traffic = $projections['Current Trajectory'][11]['traffic'] ?? 0;
        }
        
        if (!empty($total)) {
            $projected_traffic = $total[11]['traffic'] ?? 0;
        }
        
        $growth_percentage = $current_traffic > 0 ? (($projected_traffic - $current_traffic) / $current_traffic) * 100 : 0;
        
        // Calculate ROI for final month
        $roi_data = TGP_Calculator::calculate_roi(
            $projected_traffic,
            floatval($project->conversion_rate_low),
            floatval($project->conversion_rate_high),
            floatval($project->cltv),
            0
        );
        
        ob_start();
        include TGP_PLUGIN_DIR . 'templates/frontend-view.php';
        return ob_get_clean();
    }
}

