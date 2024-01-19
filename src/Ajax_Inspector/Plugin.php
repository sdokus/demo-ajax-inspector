<?php

/**
 * Plugin class file
 */

namespace src\Ajax_Inspector;

// Make sure that the file is being accessed in the WP environment
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Make sure that the class is not being duplicated
if ( ! class_exists( 'Dokus__Ajax__Inspector' ) ) {
	/**
	 * The Ajax Inspector Class
	 *
	 * This is where the Singleton instance of the plugin is created
	 */
	class Dokus__Ajax__Inspector {

		/**
		 * Static Singleton Holder
		 *
		 * @var self
		 */
		private static $instance = null;

		/**
		 * Get (and instantiate, if necessary) the instance of the class
		 *
		 * @return self
		 */
		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new Dokus__Ajax__Inspector();
			}

			return self::$instance;
		}

		/**
		 * Initializes plugin variables and sets up WordPress hooks/actions.
		 */
		protected function __construct() {
			add_action( 'wp_enqueue_scripts', [ $this, 'add_jquery' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_ajax_button_script' ] );
			add_shortcode( 'ajax_button', [ $this, 'ajax_button_shortcode' ] );
		}

		public function add_jquery() {
			wp_enqueue_script( 'jquery' );
		}

		public function enqueue_ajax_button_script() {
			wp_enqueue_script( 'ajax-button-script', plugin_dir_url( __FILE__ ) . 'ajax-button-script.js', [ 'jquery' ], '1.0', true );
		}

		public function ajax_button_shortcode() {
			ob_start();
			?>
            <button id="ajaxButton">Click me for AJAX</button>
			<?php
			return ob_get_clean();
		}
	}
}
