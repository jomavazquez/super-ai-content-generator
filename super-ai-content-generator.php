<?php
/**
 * Plugin Name: Super AI Content Generator
 * Description: Supercharge your website with AI-powered content generation. Generate marketing copy, SEO articles, or any custom content directly from the post editor. Just write a title, click the button, and let the AI do the rest.
 * Version: 1.4.0
 * Author: José María Vázquez
 * Author URI: https://www.josemariavazquez.com/
 * License: GPLv2 or later
 * Text Domain: super-ai-content-generator
 */

if( ! defined( 'ABSPATH' ) ){
    exit;
}

// Global constants for the plugin
define( 'SACG_VERSION', '1.4.0' );
define( 'SACG_DIR', plugin_dir_path( __FILE__ ) );
define( 'SACG_URL', plugin_dir_url( __FILE__ ) );

// Load dependencies
require_once SACG_DIR . 'includes/class-settings.php';
require_once SACG_DIR . 'includes/class-assets.php';
require_once SACG_DIR . 'includes/class-ajax.php';

// Load plugin textdomain for translations
add_action( 'init', function () {
    load_plugin_textdomain(
        'super-ai-content-generator',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
} );

// Initialize plugin
new SACG_Settings();
new SACG_Assets();
new SACG_Ajax();