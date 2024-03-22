<?php

namespace Sdokus\Ajax_Inspector\Ajax;

use Sdokus\Ajax_Inspector\Singleton_Abstract;

/**
 * Class Ajax_Callback - handles the AJAX request for returning events using the TEC ORM.
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
		add_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'get_per_page_param' ] );
		add_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'get_starts_after_param' ] );
		add_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'get_events' ] );
		add_action( 'wp_ajax_sdokus_get_events_list', [ $this, 'send_events' ] );

	}

	// @todo - Method to create nonce
	// @todo - Variable to store nonce
	// @todo - Method to verify nonce

	/**
	 * Parameter for how many events to show per page. Defaults to 10.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public int $per_page;

	/**
	 * Parameter for the start date to return events after. Defaults to today.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $starts_after;

	public array $events;

	/**
	 * Returns the per_page parameter sent in the AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_per_page_param(): int {
		$this->per_page = absint( $_GET['per_page'] );


		return $this->per_page;
	}

	/**
	 * Returns the starts_after parameter sent in the AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_starts_after_param(): string {
		if ( $_GET['starts_after'] === "" ) {
			$this->starts_after = date( 'Y-m-d' );
		} else {
			$this->starts_after = $_GET['starts_after'];
		}

		return $this->starts_after;
	}

	/**
	 * Uses parameters from the AJAX call to get events using the TEC ORM.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_events(): array {
		$this->events = tribe_events()
			->per_page( $this->get_per_page_param() )
			->page( 1 )
			->where( 'starts_after', $this->get_starts_after_param() )
			->all();

		return $this->events;
	}

	/**
	 * Sends events in response to AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function send_events(): void {
		wp_send_json( [ 'events' => $this->get_events() ] );

		wp_send_json_error();
	}
}