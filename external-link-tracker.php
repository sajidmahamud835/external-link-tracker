<?php
/*
Plugin Name: External Link Tracker
Description: Monitor and monetize external link clicks effortlessly. Tracks clicks, logs data, and displays a countdown warning before redirecting—perfect for insights and revenue optimization.
Version: 1.0
Author: Sajid Mahamud
Author URI: https://github.com/sajidmahamud835
Plugin URI: https://github.com/sajidmahamud835/external-link-tracker
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: external-link-tracker
Icon: icon-80x80.png
Support URL: https://github.com/sajidmahamud835/external-link-tracker/issues/new
Installation: Upload to your plugins directory and activate through the WordPress Admin > Plugins menu.
*/
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define plugin paths
define('ELT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ELT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include functions file
require_once ELT_PLUGIN_DIR . 'includes/link-tracker-functions.php';

// Activation hook: create custom table
register_activation_hook(__FILE__, 'elt_create_link_tracking_table');

// Enqueue dashboard scripts and styles
function elt_enqueue_dashboard_assets() {
    wp_enqueue_style('elt-admin-style', ELT_PLUGIN_URL . 'assets/css/admin-dashboard.css');
    wp_enqueue_script('elt-admin-script', ELT_PLUGIN_URL . 'assets/js/admin-dashboard.js', array('jquery'), false, true);
}
add_action('admin_enqueue_scripts', 'elt_enqueue_dashboard_assets');

// Register admin menu
function elt_add_admin_menu() {
    add_menu_page(
        'Link Tracker Dashboard',
        'Link Tracker',
        'manage_options',
        'elt-dashboard',
        'elt_render_dashboard',
        'dashicons-chart-bar'
    );
}
add_action('admin_menu', 'elt_add_admin_menu');
