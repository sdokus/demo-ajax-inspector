<?php
/**
 * Plugin class file
 */

namespace SDOKUS\Ajax_Inspector;

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

//		public $plugin_file;
//      public $plugin_dir;
//		public $plugin_path;
//		public $plugin_url;

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

//  Come back to this - should allow the plugin to find the assets:
//			$this->plugin_file = trailingslashit( dirname( __FILE__ ) ) . '../../demo-ajax-inspector.php';
//			$this->plugin_path = trailingslashit( dirname( $this->plugin_file ) );
//			$this->plugin_dir = trailingslashit( basename( $this->plugin_path ) );
//			$this->plugin_url = str_replace( basename( $this->plugin_file ), '', plugins_url( basename( $this->plugin_file ), $this->plugin_file ) );

			do_action( 'ajax_plugin_loaded');
		}

		/**
		 * Enable hooks for the plugin
		 */
		public function enable_hooks() {
//			add_action( 'ajax_plugin_loaded', [$this, 'load_assets'] );
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
			// Localize script with nonce to MyAjax object
			wp_localize_script('ajax-button-script', 'ajax_button_script_vars', array(
				'ajaxurl' => admin_url('admin-ajax.php'), // This line localizes the 'ajaxurl' variable
			));
		}

//    public function load_assets (){
//	    tribe_assets(
//		    $this,
//		    [
//			    [ 'ajax-button-script', plugin_dir_url( __FILE__ ) . '../resources/js/ajax-button-script.js', [ 'jquery' ] ],
//			    [ 'ajax-style', plugin_dir_url( __FILE__ ) . '../resources/css/style.css' ],
//		    ],
//		    'admin_enqueue_scripts',
//		    [
//			    'conditionals' => [ $this, 'should_enqueue_assets' ],
//			    'localize'     => (object) [
//				    'name' => 'ajax-button-script',
//				    'data' => [
//					    'check' => wp_create_nonce( 'ajax-nonce' ),
//                        'ajax-url'  => admin_url('admin-ajax.php')
//				    ]
//                    ]
//                ]
//
//	    );
//    }
//
//		/**
//		 * Checks whether the assets should be enqueued.
//		 *
//		 * @since 4.15.0
//		 *
//		 * @return boolean True if the assets should be enqueued.
//		 */
//		public function should_enqueue_assets() {
//			// Eventually need to make this to check if the shortcode or widget is on the current page
//            return true;
//		}

		/**
		 * Creates the shortcode that outputs the button
		 */
		public function ajax_button_shortcode() {
			ob_start();
			?>
            <div class="ajax-inspector">
                <div class="ajax-buttons">
                <button id="ajax-button">Click to Inspect AJAX</button>
                <button id="test-ajax-button">Click to Create an AJAX Call</button>
                </div>
                <div id="ajax-message-container">Output: </div>
            </div>
			<?php
			return ob_get_clean();
		}

	}
}
