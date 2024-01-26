<?php
/**
 * Plugin class file
 */

namespace src\Ajax_Inspector;

// Make sure that the file is being accessed in the WP environment.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Make sure that the class is not being duplicated.
if ( ! class_exists( 'Dokus__Ajax__Inspector' ) ) {
	/**
	 * The Ajax Inspector Class
	 *
	 * This is the main class of the plugin
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
			$this->enable_hooks();
		}

		/**
		 * Enable hooks for the plugin
		 */
		public function enable_hooks() {
			add_action( 'wp_enqueue_scripts', [ $this, 'add_jquery' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_ajax_button_script' ] );
			add_shortcode( 'ajax_button', [ $this, 'ajax_button_shortcode' ] );
		}

		/**
		 * Disable hooks for the plugin
		 */
		public function disable_hooks() {
			remove_action( 'wp_enqueue_scripts', [ $this, 'add_jquery' ] );
			remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_ajax_button_script' ] );
			remove_shortcode( 'ajax_button' );
		}


		/**
		 * Enqueues jquery
		 *
		 * @return void
		 */
		public function add_jquery(): void {
			wp_enqueue_script( 'jquery' );
		}

		/**
		 * Enqueues the custom JS script for the button
		 *
		 * @return void
		 */
		public function enqueue_ajax_button_script(): void {
			// Enqueue the CSS file.
			wp_enqueue_style(
				'style',
				plugin_dir_url( __FILE__ ) . '../resources/css/style.css',
				[],
				'1.0'
			);

            // Enqueue the JS script.
            wp_enqueue_script(
				'ajax-button-script',
				plugin_dir_url( __FILE__ ) . '../resources/js/ajax-button-script.js',
				[ 'jquery' ],
				'1.0',
				true
			);
		}

		/**
		 * Creates the shortcode that outputs the button
		 */
		public function ajax_button_shortcode() {
			ob_start();
			?>
            <div class="ajax-inspector">
                <button id="ajax-button">Click to Inspect AJAX</button>
                <div id="ajax-message-container">Output: </div>
            </div>
			<?php
			return ob_get_clean();
		}

	}
}
