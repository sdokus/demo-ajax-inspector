<?php
/**
 * Plugin class file
 */

namespace Sdokus\Ajax_Inspector;

/**
 * The Ajax Inspector Class
 *
 * This is the main class of the plugin
 */
class Plugin {

	public string $plugin_file;
	public string $plugin_dir;
	public string $plugin_path;
	public string $plugin_url;

	/**
	 * Static Singleton Holder
	 *
	 * @var self
	 */
	protected static Plugin $instance;

	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}


	/**
	 * Initializes plugin variables and sets up WordPress hooks/actions.
	 */
	protected function __construct() {
		// Intentionally empty.
	}

	/**
	 * @param string $file
	 *
	 * @return void
	 */
	public function boot( string $file ): void {
		$this->enable_hooks();

		$this->plugin_file = $file;
		$this->plugin_path = trailingslashit( dirname( $this->plugin_file ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url  = str_replace( basename( $this->plugin_file ), '', plugins_url( basename( $this->plugin_file ), $this->plugin_file ) );

		do_action( 'ajax_plugin_loaded' );
	}

	/**
	 * Enable hooks for the plugin
	 */
	public function enable_hooks() {
//		add_action( 'ajax_plugin_loaded', [$this, 'load_assets'] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_ajax_button_script' ] );
		add_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'get_events_callback' ] );
		add_shortcode( 'ajax_button', [ $this, 'ajax_button_shortcode' ] );
	}

	/**
	 * Disable hooks for the plugin
	 */
	public function disable_hooks() {
		remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_ajax_button_script' ] );
		remove_shortcode( 'ajax_button' );
	}

	/**
	 * Enqueues the custom JS script for the button
	 *
	 * @return void
	 */
	public function enqueue_ajax_button_script(): void {
		// Enqueue the CSS file.
		wp_enqueue_style(
			'sdokus-ajax-inspector-style',
			$this->plugin_url . 'src/resources/css/style.css',
			[],
			'1.0'
		);

		// Enqueue the JS script.
		wp_enqueue_script(
			'sdokus-ajax-inspector-buttons',
			$this->plugin_url . 'src/resources/js/ajax-button-script.js',
			[ 'jquery' ],
			'1.0',
			true
		);
		// Localize script with nonce to MyAjax object
		wp_localize_script( 'sdokus-ajax-inspector-buttons', 'ajax_button_script_vars', [
			'ajaxurl'  => admin_url( 'admin-ajax.php' ), // This line localizes the 'ajaxurl' variable
			'rest_url' => get_rest_url( null, '/tribe/events/v1/events' ),
		] );
	}

	public function get_events_callback() {
		// TODO: Get events here using ORM and send response as JSON
		if ( isset( $_POST['action'] ) ) {
			$action = sanitize_text_field( $_POST['action'] );

			$output = $action;

			// Return the result to the AJAX request
			wp_send_json( $output );
		}

		wp_die(); // Always include this line to terminate the script
	}

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
                <button id="get-events-button">Click to GET all Events</button>
            </div>
            <div id="ajax-message-container">Output:</div>
        </div>
		<?php
		return ob_get_clean();
	}

}