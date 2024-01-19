<?php
/**
 * Plugin Name: Demo AJAX Inspector
 * Description: Creates a button that outputs the AJAX calls in a given context.
 * Version: 1.0
 * Author: Sam Dokus
 * Author URI: https://evnt.is/1x
 * Text Domain: demo-ajax-inspector
 */
define( 'DEMO_PLUGIN_FILE', __FILE__ );

add_action( 'plugins_loaded', static function() {
	// Load Composer autoload file.
	require_once __DIR__ . '/vendor/autoload.php';

	new \Demo\AjaxInspector\Plugin();
} );
