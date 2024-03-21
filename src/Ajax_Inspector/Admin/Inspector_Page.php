<?php

namespace Sdokus\Ajax_Inspector\Admin;

use Sdokus\Ajax_Inspector\Singleton_Abstract;

/**
 * Class Inspector_Page
 *
 * @since   1.0.0
 *
 * @package Sdokus\Ajax_Inspector\Admin
 */
class Inspector_Page extends Singleton_Abstract {

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
     * Adds the menu page for the AJAX Inspector Page
     *
     * @since 1.0.0
     *
	 * @return void
	 */
    public function add_to_wordpress(): void {
		$this->registered_hook = add_menu_page(
			__( 'AJAX Inspector', 'ajax-inspector' ),
			__( 'AJAX Inspector', 'ajax-inspector' ),
			static::CAPABILITY,
			$this->get_menu_slug(),
			[ $this, 'render' ],
			'',
			99
		);
	}

	/**
     * Slug for the menu.
     *
     * @since 1.0.0
     *
	 * @return string
	 */
    public function get_menu_slug(): string {
        return 'ajax-inspector';
    }

	/**
	 * Output for the AJAX Inspector Admin Page.
	 *
     * @since 1.0.0
     *
	 * @return void
	 */
    public function render(): void {
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
}