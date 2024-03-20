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
     * The current version of the plugin.
     *
	 * @since 1.0.0
	 */
    public const VERSION = '1.0.0';
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
	 * @return static
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initializes plugin variables and sets up WordPress hooks/actions.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
        // Intentionally left empty.
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
		add_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'get_events_callback' ] );
		add_action( 'admin_notices', [ $this, 'ajax_demo_notice' ] );


        add_action('sdokus_ajax_plugin_loaded', [$this , 'load'], 15);
	}

    public function load():void{
        Admin\Inspector_Page::get_instance();
        Admin\Logger_Page::get_instance();
        Shortcode\Ajax_Button::get_instance();
    }

	public function ajax_demo_notice() {
		?>
        <div class="notice notice-success is-dismissible" id="sdokus-ajax-notice">
            <div id="sdokus-ajax-message-container"></div>
        </div>
		<?php
	}

	/**
	 * Uses the TEC ORM to retrieve events and send back as JSON response.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_events_callback() {
		if ( !isset( $_GET['action'] ) ) {
			wp_send_json_error( 'Something went wrong' );
		}

		$per_page     = isset( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 10;
		$starts_after = isset( $_GET['starts_after'] ) ? $_GET['starts_after'] : date( 'Y-m-d' );

		$events = tribe_events()->per_page( $per_page )->page( 1 )->where( 'starts_after', $starts_after )->all();

		wp_send_json( [ 'events' => $events ] );

		wp_die();
	}
}