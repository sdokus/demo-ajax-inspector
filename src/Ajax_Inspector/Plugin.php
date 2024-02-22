<?php
/**
 * Plugin class service provider to bootstrap the plugin.
 *
 * @since 1.0.0
 */

namespace Sdokus\Ajax_Inspector;

/**
 * The Ajax Inspector Class.
 *
 * This is the main class of the plugin.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin File.
	 */
	public string $plugin_file;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin Directory.
	 */
	public string $plugin_dir;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin Path.
	 */
	public string $plugin_path;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin URL.
	 */
	public string $plugin_url;

	/**
	 * Static Singleton Holder.
	 *
	 * @since 1.0.0
	 *
	 * @var self
	 */
	protected static Plugin $instance;

	/**
	 * Gets (and instantiates, if necessary) the instance of the class.
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.0.0
	 *
	 */
	protected function __construct() {
		// Intentionally empty.
	}

	/**
	 * Boots up the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file
	 *
	 * @return void
	 */
	public function boot( string $file ): void {
		// Sets up the hooks for the plugin.
        $this->enable_hooks();

		// Set up the plugin provider properties.
		$this->plugin_file = $file;
		$this->plugin_path = trailingslashit( dirname( $this->plugin_file ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url  = str_replace( basename( $this->plugin_file ), '', plugins_url( basename( $this->plugin_file ), $this->plugin_file ) );

        // Now that the plugin is set up, enqueue the assets.
		do_action( 'sdokus_ajax_plugin_loaded' );
	}

	/**
	 * Enables hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function enable_hooks() {
	//	add_action( 'sdokus_ajax_plugin_loaded', [$this, 'load_assets'] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_ajax_button_script' ] );
		add_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'get_events_callback' ] );
		add_shortcode( 'ajax_button', [ $this, 'ajax_button_shortcode' ] );
	}

	/**
	 * Disables hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function disable_hooks() {
		remove_action( 'sdokus_ajax_plugin_loaded', [$this, 'load_assets'] );
		remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_ajax_button_script' ] );
		remove_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'get_events_callback' ] );
		remove_shortcode( 'ajax_button' );
	}

	/**
	 * Enqueues the custom JS script for the button.
	 *
	 * @since 1.0.0
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
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'rest_url' => get_rest_url( null, '/tribe/events/v1/events/?per_page=10' ),
		] );
	}

	/**
	 * Uses the TEC ORM to retrieve events and send back as JSON response.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_events_callback() {
		if ( isset( $_GET['action'] ) ) {
			$events = tribe_events()->per_page( 10 )->page( 1 )->all();
			wp_send_json( [ 'events' => $events ] );
		}
		wp_die();
	}

	/**
	 * Creates the shortcode that outputs the button.
	 *
	 * @since 1.0.0
	 */
	public function ajax_button_shortcode() {
		ob_start();
		?>
        <div class="ajax-inspector">
            <div class="ajax-buttons">
                <button id="ajax-button"><?php esc_html_e( 'Click to Listen for AJAX', 'sdokus-demo-ajax-inspector' ); ?></button>
                <button id="test-ajax-button"><?php esc_html_e( 'Click to do a WP AJAX Call', 'sdokus-demo-ajax-inspector' ); ?></button>
                <button id="get-events-button"><?php esc_html_e( 'Click to do an API Call', 'sdokus-demo-ajax-inspector' ); ?></button>
            </div>
            <div id="ajax-message-container"><?php esc_html_e( 'Output:', 'sdokus-demo-ajax-inspector' ); ?></div>
        </div>
		<?php
		return ob_get_clean();
	}

}