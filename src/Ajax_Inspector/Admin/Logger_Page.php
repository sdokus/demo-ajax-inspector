<?php

namespace Sdokus\Ajax_Inspector\Admin;



use Sdokus\Ajax_Inspector\Singleton_Abstract;

/**
 * Class Logger_Page
 *
 * @since 1.0.0
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
	}

	/**
	 * Adds the submenu page for the AJAX Logger
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_to_wordpress(): void {
		$this->registered_hook =  add_submenu_page(
			Inspector_Page::get_instance()->get_menu_slug(),
			'AJAX Logger',
			'AJAX Logger',
			static::CAPABILITY,
			$this->get_menu_slug(),
			[ $this, 'render' ],
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
	public function get_registered_hook ():string {
		return $this->registered_hook;
	}

	public function render():void {
		$pp_time   = microtime( true );
		$last_time = microtime( true );
		$uuid      = uniqid();

		add_action( 'all', function () use ( $pp_time, $uuid, $last_time ) {
			if ( ! is_admin() ) {
				return;
			}

			$ct        = microtime( true );
			$delta     = $ct - $last_time;
			$last_time = microtime( true );

			if ( $delta > 0.002 ) {
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
}