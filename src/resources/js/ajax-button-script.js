/**
 * Script for starting and stopping real-time logging of AJAX calls.
 *
 * @since 1.0.0
 */

jQuery( document ).ready( function ( $ ) {
	// Variable to keep track of whether logging is active or not
	var isLoggingActive = false;

	// Container for displaying messages on the page
	var messageContainer = $( '#ajax-message-container' );

	/**
	 * Toggles whether to listen or not for AJAX calls.
	 *
	 * @since 1.0.0
	 */
	function toggleAjaxLogging() {
		if ( isLoggingActive ) {
			stopAjaxLogging();
		} else {
			startAjaxLogging();
		}
	}

	/**
	 * Starts logging AJAX calls.
	 *
	 * @since 1.0.0
	 */
	function startAjaxLogging() {
		// If logging is not active, turn it on
		$( document ).on( 'ajaxSend.ajaxLogger' );
		$( document ).on( 'ajaxComplete.ajaxLogger' );

		// Append the message to the container
		messageContainer.append( '<p>Real-time AJAX logging started</p>' );

		// Change the button text
		$( '#ajax-button' ).text( 'Stop Listening' );
		// Toggle the logging status
		isLoggingActive = true;
	}

	/**
	 * Stops logging AJAX calls.
	 *
	 * @since 1.0.0
	 */
	function stopAjaxLogging() {
		// If logging is active, stop it
		$( document ).off( 'ajaxSend.ajaxLogger' );
		$( document ).off( 'ajaxComplete.ajaxLogger' );

		// Append the message to the container
		messageContainer.append( '<p>Real-time AJAX logging stopped</p>' );

		// Change the button text
		$( '#ajax-button' ).text( 'Start Listening for AJAX calls' );
		// Toggle the logging status
		isLoggingActive = false;
	}

	/**
	 * Listens for click on button to toggle listening for AJAX calls.
	 *
	 * @since 1.0.0
	 */
	$( '#ajax-button' ).click( function () {
		toggleAjaxLogging();
	} );

	/**
	 * Listens for click on test button to create AJAX call and grab events via ORM.
	 *
	 * @since 1.0.0
	 */
	$( '#test-ajax-button' ).click( function () {
		$.ajax( {
			method: 'GET',
			url: ajax_button_script_vars.ajaxurl,
			data: {
				action: 'sdokus_get_events_list',
			}
		} )
		.done( renderEvents );
	} );

	/**
	 * Listens for click on button to create an AJAX call and grab events via TEC API.
	 *
	 * @since 1.0.0
	 */
	$( '#get-events-button' ).click( function () {
		$.ajax( {
				method: 'GET',
				url: ajax_button_script_vars.rest_url,
				data: {
					'page': 1,
					'per_page': 10,
				}
			} )
			.done( renderEvents );
	} );

	/**
	 * Outputs just the title and date of each event in the response
	 *
	 * @since 1.0.0
	 *
	 * @param response
	 */
	var renderEvents = function ( response ) {
		for ( var event of response.events ) {
			messageContainer.append( '<li>' + event.title + ' happening on ' + event.start_date + '</li>' );
		}
	}
} );
