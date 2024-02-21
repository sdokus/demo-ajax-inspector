<?php
/**
 * Plugin Name: Demo AJAX Inspector
 * Description: Creates a button that outputs the AJAX calls in a given context.
 * Version: 1.0
 * Author: Sam Dokus
 * Author URI: https://www.linkedin.com/in/sam-dokus/
 * Text Domain: demo-ajax-inspector
 */

use Sdokus\Ajax_Inspector\Plugin;

/**
 * Action to load the plugin
 *
 * @since 1.0.0
 *
 * @version TBD
 */
add_action(
	'plugins_loaded',
	static function () {
		// Load Composer autoload file.
		require_once __DIR__ . '/vendor/autoload.php';
		Plugin::get_instance()->boot( __FILE__ );
	} 
);
