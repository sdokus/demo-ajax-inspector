<?php

namespace Sdokus\Ajax_Inspector\Shortcode;

use Sdokus\Ajax_Inspector\Singleton_Abstract;
use Sdokus\Ajax_Inspector\Plugin;

/**
 * Class Ajax_Button Shortcode
 *
 * @since   1.0.0
 *
 * @package Sdokus\Ajax_Inspector\Shortcode
 */
class Ajax_Button extends Singleton_Abstract {
	/**
	 * @inheritDoc
	 */
	protected function register(): void {
		add_shortcode( 'ajax_button', [ $this, 'get_output' ] );
		add_action( 'init', [ $this, 'register_assets' ] );
	}

	/**
     * Registers the assets for the Ajax inspector functionality.
     *
     * @since 1.0.0
     *
	 * @return void
	 */
    public function register_assets(): void {
		wp_register_style(
			'sdokus-ajax-inspector-style',
			Plugin::get_instance()->plugin_url . 'src/resources/css/style.css',
			[],
			Plugin::VERSION
		);
		wp_register_style(
			'sdokus-ajax-inspector-admin-style',
			Plugin::get_instance()->plugin_url . 'src/resources/css/ajax-admin.css',
			[],
			Plugin::VERSION
		);

		wp_register_script(
			'sdokus-ajax-inspector-buttons',
			Plugin::get_instance()->plugin_url . 'src/resources/js/ajax-button-script.js',
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
        wp_enqueue_script( 'sdokus-ajax-inspector-buttons' );
        if (is_admin()){
            wp_enqueue_style( 'sdokus-ajax-inspector-admin-style' );
        } else {
            wp_enqueue_style( 'sdokus-ajax-inspector-style' );
        }

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
     * Returns the HTML for the AJAX inspector
     *
     * @since 1.0.0
     *
	 * @return string
	 */
    public function get_output(): string {
        $this->enqueue_assets();
		ob_start();
		?>
        <div class="sdokus-ajax-inspector">
            <h3><?php esc_html_e( 'AJAX Inspector', 'sdokus-ajax-inspector' ); ?></h3>

            <!-- Form for Parameters -->
            <form class="sdokus-ajax-parameters">
                <fieldset>
                    <legend><?php esc_html_e( 'Event Parameters', 'sdokus-ajax-inspector' ); ?></legend>

                    <div class="form-group">
                        <label for="per-page"><?php esc_html_e( 'Per Page: ', 'sdokus-ajax-inspector' ); ?></label>
                        <input type="number" name="per-page" id="per-page" value="10">
                    </div>

                    <div class="form-group">
                        <label for="starts-after"><?php esc_html_e( 'Starts After: ', 'sdokus-ajax-inspector' ); ?></label>
                        <input type="date" name="starts-after" id="starts-after">
                    </div>
                </fieldset>
            </form>

            <!-- DropDown for which method to use in AJAX call-->
            <div class="sdokus-ajax-request-method">
                <label for="sdokus-ajax-request-method"><?php esc_html_e( 'How Do You Want to Get Events?', 'sdokus-ajax-inspector' ); ?></label>
                <select name="sdokus-ajax-request-method" id="sdokus-ajax-request-method">
                    <option value="orm"><?php esc_html_e( 'ORM', 'sdokus-ajax-inspector' ); ?></option>
                    <option value="api"><?php esc_html_e( 'API', 'sdokus-ajax-inspector' ); ?></option>
                </select>
            </div>

            <!-- Button for ORM Calls -->
            <div id="orm-button" class="ajax-button">
                <div class="sdokus-ajax-buttons">
					<?php submit_button( 'Get Events Using TEC ORM', 'primary', 'sdokus-ajax-orm-button' ); ?>
                </div>
            </div>

            <!-- Button for API Calls -->
            <div id="api-button" class="ajax-button" style="display: none;">
                <div class="sdokus-ajax-buttons">
					<?php submit_button( 'Get Events Using TEC REST API', 'primary', 'sdokus-ajax-api-button' ); ?>
                </div>
            </div>

            <!-- If the shortcode is on the frontend, we need the output to be directly on the page instead of a WP notice -->
			<?php
			if ( ! is_admin() ) {
				echo '<div id="sdokus-ajax-notice"><div id="sdokus-ajax-message-container"></div></div>';
			}
			?>

        </div>
		<?php
		return ob_get_clean();
	}
}