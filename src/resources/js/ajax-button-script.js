/**
 * Script for starting and stopping real-time logging of AJAX calls.
 *
 * @since 1.0.0
 */

jQuery( document ).ready( function ( $ ) {
	// Container for displaying messages on the page
	var messageContainer = $( '#ajax-message-container' );

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
	let renderEvents = function ( response ) {
		for (let event of response.events) {
			let listItem = $('<li></li>');
			listItem.html(event.title + ' happening on ' + event.start_date);
			messageContainer.append(listItem);
		}
	}
} );
