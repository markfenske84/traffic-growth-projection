<?php
/**
 * Plugin Name: Traffic Growth Projection
 * Plugin URI: https://wordpress.org/plugins/traffic-growth-projection
 * Description: A comprehensive tool for projecting keyword-based traffic growth, ROI calculations, and conversion tracking.
 * Version: 1.0.2
 * Author: Webfor Agency
 * Author URI: https://profiles.wordpress.org/webforagency
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: traffic-growth-projection
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Tested up to: 6.8
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TGP_VERSION', '1.0.2');
define('TGP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TGP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TGP_PLUGIN_FILE', __FILE__);

// Require necessary files
require_once TGP_PLUGIN_DIR . 'includes/class-tgp-database.php';
require_once TGP_PLUGIN_DIR . 'includes/class-tgp-admin.php';
require_once TGP_PLUGIN_DIR . 'includes/class-tgp-calculator.php';
require_once TGP_PLUGIN_DIR . 'includes/class-tgp-importer.php';
require_once TGP_PLUGIN_DIR . 'includes/class-tgp-frontend.php';
require_once TGP_PLUGIN_DIR . 'includes/class-tgp-blocks.php';

// Initialize Plugin Update Checker
require_once TGP_PLUGIN_DIR . 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$tgpUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/markfenske84/traffic-growth-projection',
    __FILE__,
    'traffic-growth-projection'
);

// Set the branch to check for updates
$tgpUpdateChecker->setBranch('main');

/**
 * Main plugin class
 */
class Traffic_Growth_Projection {
    
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
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(TGP_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(TGP_PLUGIN_FILE, array($this, 'deactivate'));
        
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Check for database upgrades
        $this->check_version();
        
        // Initialize database
        TGP_Database::get_instance();
        
        // Initialize admin
        if (is_admin()) {
            TGP_Admin::get_instance();
        }
        
        // Initialize frontend
        TGP_Frontend::get_instance();
        
        // Initialize Gutenberg blocks
        TGP_Blocks::get_instance();
    }
    
    /**
     * Check version and run upgrades if needed
     */
    private function check_version() {
        $installed_version = get_option('tgp_version', '0.0.0');
        
        if (version_compare($installed_version, TGP_VERSION, '<')) {
            // Run upgrade
            TGP_Database::get_instance()->upgrade_database();
            
            // Update version
            update_option('tgp_version', TGP_VERSION);
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        TGP_Database::get_instance()->create_tables();
        
        // Run upgrade to ensure schema is current
        TGP_Database::get_instance()->upgrade_database();
        
        // Set default options
        update_option('tgp_version', TGP_VERSION);
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
function tgp_init() {
    return Traffic_Growth_Projection::get_instance();
}

// Kick off the plugin
tgp_init();

