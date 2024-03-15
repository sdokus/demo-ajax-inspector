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
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_ajax_button_script' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_ajax_button_script' ] );
		add_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'get_events_callback' ] );
		add_action( 'admin_menu', [ $this, 'add_shortcode_menu_page' ] );
		add_shortcode( 'ajax_button', [ $this, 'ajax_button_shortcode' ] );
	}

	public function enqueue_ajax_button_script(): void {
		// Enqueue the appropriate CSS file depending if on the frontend or backend
		if ( ! is_admin() ) {
			wp_enqueue_style(
				'sdokus-ajax-inspector-style',
				$this->plugin_url . 'src/resources/css/style.css',
				[],
				'1.0'
			);
		} else {
			wp_enqueue_style(
				'sdokus-ajax-inspector-admin-style',
				$this->plugin_url . 'src/resources/css/ajax-admin.css',
				[],
				'1.0'
			);
        }

		// Enqueue the JS script.
		wp_enqueue_script(
			'sdokus-ajax-inspector-buttons',
			$this->plugin_url . 'src/resources/js/ajax-button-script.js',
			[ 'jquery', 'wp-i18n' ],
			'1.0',
			true
		);

		// Localize script with nonce to MyAjax object
		wp_localize_script(
			'sdokus-ajax-inspector-buttons',
			'ajax_button_script_vars',
			[
				'ajaxurl'               => admin_url( 'admin-ajax.php' ),
				'rest_endpoint'         => [
					'base'   => get_rest_url(),
					'events' => tribe_events_rest_url( '/events' ),
					'tags'   => get_rest_url( null, '/wp/v2/tags' ),
				],
				'nonce'                 => wp_create_nonce( 'wp_rest' ),
				'event_happening_label' => esc_html__( '%1$s happening on %2$s', 'sdokus-ajax-inspector' ),
			]
		);

		// Set up translations for the script
		wp_set_script_translations( 'sdokus-ajax-inspector-buttons', 'sdokus-ajax-inspector' );
	}

	/**
	 * Adds a menu page to the WordPress dashboard.
	 *
	 * @since 1.0.0
	 */
	public function add_shortcode_menu_page() {
		add_menu_page(
			'AJAX Demo',
			'AJAX Demo',
			'administrator',
			'ajax-demo',
			[ $this, 'render' ],
		);

		add_submenu_page(
			'ajax-demo',
			'AJAX Logger',
			'AJAX Logger',
			'administrator',
			'ajax-demo-2',
			[ $this, 'ajax_logger' ],
		);
	}

	/**
	 * Callback function to render the shortcode content on an admin menu page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		?>
        <div class="sdokus-ajax-demo-settings">
            <h1><?php esc_html_e( 'AJAX Demo', 'sdokus-ajax-inspector' ); ?></h1>
            <div class="sdokus-ajax-demo-settings-header">
                <h2><?php esc_html_e( 'Grab Events Using AJAX!', 'sdokus-ajax-inspector' ); ?></h2>
                <p><?php
					esc_html_e( 'Use the form below to create an AJAX call to return events using either the TEC ORM or TEC REST API', 'sdokus-ajax-inspector' )
					?>
                </p>
            </div>

			<?php echo do_shortcode( '[ajax_button]' ); ?>
        </div>
		<?php
	}

	/**
	 * @return void
	 */
	public function ajax_logger() {
		$pp_time   = microtime( true );
		$last_time = microtime( true );
		$uuid      = uniqid();

		add_action( 'all', function () use ( $pp_time, $uuid, $last_time ) {
			$ct        = microtime( true );
			$delta     = $ct - $last_time;
			$last_time = microtime( true );

			if ( is_admin() & $delta > 0.002 ) {
				// Get the current filter
				$current_filter = current_filter();

				// Format the output with filter and time
				$output = sprintf( '<strong>%s:</strong> %s seconds', $current_filter, number_format( $delta, 5 ) );

				// Enqueue the script
				wp_enqueue_script(
					'ajax-logger-script',
					$this->plugin_url . 'src/resources/js/ajax-log-script.js', [ 'jquery' ],
					'1.0',
					true
				);

				// Use JavaScript to append the content to the sdokus-ajax-log-container when the button is clicked
				wp_add_inline_script( 'ajax-logger-script', "
                jQuery(document).ready(function($) {
                    $('#sdokus-ajax-log-container').append('$output<br>');
                });
            " );
			}
		} );
		// Output HTML for your submenu along with the captured content and the button
		?>
        <div>
            <h1><?php esc_html_e( 'Filter Log', 'sdokus-ajax-inspector' ); ?></h1>
            <label for="sdokus-ajax-log-container"><?php esc_html_e( 'Output:', 'sdokus-demo-ajax-inspector' ); ?></label>
            <div id="sdokus-ajax-log-container"></div>
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
		if ( isset( $_GET['action'] ) ) {
			$per_page     = isset( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 10;
			$starts_after = $_GET['starts_after'] ?? '01 January 1970';

			$events = tribe_events()->per_page( $per_page )->page( 1 )->where( 'starts_after', $starts_after )->all();

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
        <div class="sdokus-ajax-inspector">
            <h3><?php esc_html_e( 'AJAX Demo', 'sdokus-demo-ajax-inspector' ); ?></h3>

            <!-- Form for Parameters -->
            <form class="sdokus-ajax-parameters">
                <fieldset>
                    <legend><?php esc_html_e( 'Event Parameters', 'sdokus-demo-ajax-inspector' ); ?></legend>

                    <div class="form-group">
                        <label for="per-page"><?php esc_html_e( 'Per Page: ', 'sdokus-demo-ajax-inspector' ); ?></label>
                        <input type="number" name="per-page" id="per-page" value="10">
                    </div>

                    <div class="form-group">
                        <label for="starts-after"><?php esc_html_e( 'Starts After: ', 'sdokus-demo-ajax-inspector' ); ?></label>
                        <input type="date" name="starts-after" id="starts-after">
                    </div>
                </fieldset>
            </form>

            <!-- DropDown for which method to use in AJAX call-->
            <div class="sdokus-ajax-request-method">
                <label for="sdokus-ajax-request-method"><?php esc_html_e( 'How Do You Want to Get Events?', 'sdokus-demo-ajax-inspector' ); ?></label>
                <select name="sdokus-ajax-request-method" id="sdokus-ajax-request-method">
                    <option value="orm"><?php esc_html_e( 'ORM', 'sdokus-demo-ajax-inspector' ); ?></option>
                    <option value="api"><?php esc_html_e( 'API', 'sdokus-demo-ajax-inspector' ); ?></option>
                </select>
            </div>

            <!-- Button for ORM Calls -->
            <div id="orm-button" class="ajax-button">
                <div class="sdokus-ajax-buttons">
                    <?php submit_button('Get Events Using TEC ORM', 'primary', 'sdokus-ajax-orm-button');?>
                </div>
            </div>

            <!-- Button for API Calls -->
            <div id="api-button" class="ajax-button" style="display: none;">
                <div class="sdokus-ajax-buttons">
	                <?php submit_button('Get Events Using TEC REST API', 'primary', 'sdokus-ajax-api-button');?>
                </div>
            </div>

            <label for="sdokus-ajax-message-container"><?php esc_html_e( 'Output:', 'sdokus-demo-ajax-inspector' ); ?></label>
            <div id="sdokus-ajax-message-container"></div>
        </div>
		<?php
		return ob_get_clean();
	}
}