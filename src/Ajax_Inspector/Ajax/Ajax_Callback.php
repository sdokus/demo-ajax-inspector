<?php

namespace Sdokus\Ajax_Inspector\Ajax;

use Sdokus\Ajax_Inspector\Singleton_Abstract;

/**
 * Class Ajax_Callback
 *
 * @since   1.0.0
 *
 * @package Sdokus\Ajax_Inspector\Ajax
 */
class Ajax_Callback extends Singleton_Abstract {
	/**
	 * @inheritDoc
	 */
	protected function register(): void {
		add_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'get_events_callback' ] );
	}

	/**
	 * Callback function for AJAX call using the TEC ORM to return events
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_events_callback(): void {
		if ( ! isset( $_GET['action'] ) ) {
			wp_send_json_error( 'Something went wrong' );
		}

		$per_page     = isset( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 10;
		$starts_after = isset( $_GET['starts_after'] ) ? $_GET['starts_after'] : date( 'Y-m-d' );

		$events = tribe_events()->per_page( $per_page )->page( 1 )->where( 'starts_after', $starts_after )->all();

		wp_send_json( [ 'events' => $events ] );

		wp_send_json_error();
	}
}