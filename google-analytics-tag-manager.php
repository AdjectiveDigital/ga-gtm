<?php
/**
 * Plugin Name:     Adjective Digital - Google Analytics and Tag Manager
 * Plugin URI:      https://adjectivedigital.com.au/ga-wp
 * Description:     A simple plugin to easily add Google Analytics or Google Tag Manager tracking code to your WordPress website.
 * Author:          Adjective Digital
 * Author URI:      https://adjectivedigital.com.au
 * Text Domain:     google-analytics-tag-manager
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Google_Analytics_Tag_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define constants
define( 'GATM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GATM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include main plugin class
require_once GATM_PLUGIN_DIR . 'includes/class-google-analytics-tag-manager.php';

// Run the plugin
$gatm_plugin = new Google_Analytics_Tag_Manager();
$gatm_plugin->run();
