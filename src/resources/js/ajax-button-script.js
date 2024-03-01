/**
 * Script for starting and stopping real-time logging of AJAX calls.
 *
 * @since 1.0.0
 */

jQuery( document ).ready( function ( $ ) {
	// Container for displaying messages on the page
	let messageContainer = $( '#ajax-message-container' );

	// Grab the internationalization object - there's an issue happening here where it is not properly finding this, so I temporarily removed it from renderEvents() below
	const i18n = window.i18n;

	let select = $('#sdokus-ajax-request-method');
	let ormFields = $('#orm-fields');
	let apiFields = $('#api-fields');

	// Function to toggle fields based on selected option
	function toggleFields() {
		ormFields.toggle(select.val() === 'orm');
		apiFields.toggle(select.val() === 'api');
	}

	// Initial toggle
	toggleFields();

	// Add event listener to update fields on select change
	select.on('change', toggleFields);


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
				url: ajax_button_script_vars.rest_endpoint.events,
				beforeSend: function( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', ajax_button_script_vars.nonce );
				},
				data: {
					action: 'sdokus_api_get_events_list',
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
			listItem.html( sprintf( ajax_button_script_vars.event_happening_label, event.title, event.start_date ) );
			messageContainer.append(listItem);
		}
	}
} );
