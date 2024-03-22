<?php

namespace Sdokus\Ajax_Inspector\Admin;


use Sdokus\Ajax_Inspector\Plugin;
use Sdokus\Ajax_Inspector\Singleton_Abstract;

/**
 * Class Logger_Page
 *
 * @since   1.0.0
 *
 * @package Sdokus\Ajax_Inspector\Admin
 */
class Logger_Page extends Singleton_Abstract {
	/**
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $registered_hook;

	/**
	 * Option name for the AJAX logging setting.
	 */
	const OPTION_NAME = 'enable_ajax_logging';

	/**
	 * Capability required to access the page.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const CAPABILITY = 'manage_options';

	/**
	 * @inheritDoc
	 */
	protected function register(): void {
		add_action( 'admin_menu', [ $this, 'add_to_wordpress' ] );
		add_action( 'init', [ $this, 'register_assets' ] );
		add_action( 'wp_ajax_toggle_ajax_listener', [ $this, 'enable_listening' ] );
	}

	/**
	 * Adds the submenu page for the AJAX Logger
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_to_wordpress(): void {
		$this->registered_hook = add_submenu_page(
			Inspector_Page::get_instance()->get_menu_slug(),
			'AJAX Logger',
			'AJAX Logger',
			static::CAPABILITY,
			$this->get_menu_slug(),
			[ $this, 'render' ],
		);
	}

	/**
	 * Registers the assets for the Ajax Logger functionality.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_enqueue_style( 'sdokus-ajax-logger-admin-style',
			Plugin::get_instance()->plugin_url . 'src/resources/css/ajax-admin.css',
			[],
			Plugin::VERSION );

		wp_register_script(
			'sdokus-ajax-logger',
			Plugin::get_instance()->plugin_url . 'src/resources/js/ajax-log-script.js',
			[ 'jquery', 'wp-i18n' ],
			Plugin::VERSION,
			true
		);
	}

	/**
	 * Enqueues the assets for the Ajax inspector functionality.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function enqueue_assets(): void {
		wp_enqueue_style( 'sdokus-ajax-logger-admin-style' );

		wp_enqueue_script( 'sdokus-ajax-logger' );

		wp_localize_script(
			'sdokus-ajax-logger',
			'ajax_log_script_vars',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			]
		);
	}

	/**
	 * Gets the menu slug for this logger page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_menu_slug(): string {
		return 'ajax-inspector-logger';
	}

	/**
	 * Gets the registered hook for the page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_registered_hook(): string {
		return $this->registered_hook;
	}

	public function enable_listening() {
		// Check if current user has capability to manage options
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}

		// Get enable_logging parameter from AJAX request
		$enable_logging = isset( $_POST['enable_logging'] ) && $_POST['enable_logging'] === '1' ? true : false;

		// Perform action based on enable_logging value
		if ( $enable_logging ) {
			echo "I am listening!";
			// Send response
			wp_send_json_success( 'Success' );
		}

        echo "LISTENING STOPPED";


	}


	/**
	 * Returns the output for the AJAX Logger functionality.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render(): void {
		$this->enqueue_assets();
		?>
        <div>
        <div class="sdokus-ajax-settings">
            <h1><?php esc_html_e( 'AJAX Logger', 'sdokus-ajax-inspector' ); ?></h1>
            <div class="sdokus-ajax-settings-header">
                <h2><?php esc_html_e( 'Enable Logging of AJAX', 'sdokus-ajax-inspector' ); ?></h2>
                <p><?php
					esc_html_e( 'Enable the setting below to track AJAX calls happening on your site.', 'sdokus-ajax-inspector' )
					?>
                </p>
            </div>
            <h3><?php esc_html_e( 'Settings', 'sdokus-ajax-inspector' ); ?></h3>
            <form id="filter-form">
                <fieldset id="sdokus-ajax-logger-enable-logging" class="sdokus-ajax-field-checkbox_bool">
                    <legend><?php esc_html_e( 'Enable Logging of AJAX', 'sdokus-ajax-inspector' ); ?></legend>
                    <div class="sdokus-ajax-wrap">
                        <label>
                            <input type="checkbox" name="enable_ajax_logging" id="enable_ajax_logging">
                        </label>
                    </div>
                </fieldset>
                <div id="ajax-logger-button-submit" class="ajax-logger-button">
                    <button type="button" id="submit-form" class="button-primary"><?php esc_html_e( 'Save Changes', 'sdokus-ajax-inspector' ); ?></button>
                </div>
            </form>
        </div>
		<?php
	}
}