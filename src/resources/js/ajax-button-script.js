/**
 * Script for starting and stopping real-time logging of AJAX calls.
 *
 * @since 1.0.0
 */

jQuery( document ).ready( function ( $ ) {
	// Grab the internationalization object - there's an issue happening here where it is not properly finding this, so I temporarily removed it from renderEvents() below
	const i18n = window.i18n;

	// Container for displaying messages on the page
	let messageContainer = $( '#sdokus-ajax-message-container' );

	// Grab the dropdown menu option for which method to use to get events
	let select = $( '#sdokus-ajax-request-method' );

	// Input for events per page
	let perPage = $( '#per-page' );

	// Input for starts after
	let startsAfter = $( '#starts-after' );

	// Function to toggle fields based on selected option
	function toggleButtons() {
		// Change the
		$( '#orm-button' ).toggle( select.val() === 'orm' );
		$( '#api-button' ).toggle( select.val() === 'api' );

		// Clear the notice when fields are toggled
		$( '#sdokus-ajax-notice' ).css( 'display', 'none' );
	}

	// Add event listener to update fields on select change
	select.on( 'change', toggleButtons );

	/**
	 * Listens for click on test button to create AJAX call and grab events via ORM.
	 *
	 * @since 1.0.0
	 */
	$( '#sdokus-ajax-orm-button' ).click( function () {
		$.ajax( {
				method: 'GET',
				url: ajax_button_script_vars.ajaxurl,
				data: {
					action: 'sdokus_get_events_list',
					per_page: perPage.val(),
					starts_after: startsAfter.val(),
				}
			} )
			.done( renderEvents )
			.fail( function ( xhr, textStatus, errorThrown ) {
				messageContainer.append( '<p> An error occurred while fetching events. </p>' )
				$( '#sdokus-ajax-notice' ).css( 'display', 'block' ).removeClass('notice-success').addClass('notice-error');;
			} );
	} );

	/**
	 * Listens for click on button to create an AJAX call and grab events via TEC API.
	 *
	 * @since 1.0.0
	 */
	$( '#sdokus-ajax-api-button' ).click( function () {
		// Get the value from the per page input field
		let perPageValue = perPage.val();
		// Get the value from the starts after input field
		let currentDate = new Date().toISOString().split( 'T' )[ 0 ];
		let startsAfterValue = startsAfter.val() ? startsAfter.val() : currentDate;

		// Construct the API URL dynamically
		let apiUrl = ajax_button_script_vars.rest_endpoint.events + '?per_page=' + perPageValue + '&starts_after=' + startsAfterValue;

		$.ajax( {
				method: 'GET',
				url: apiUrl,
				beforeSend: function ( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', ajax_button_script_vars.nonce );
				},
				data: {
					action: 'sdokus_api_get_events_list',
				}
			} )
			.done( renderEvents )
			.fail( function ( xhr, textStatus, errorThrown ) {
				messageContainer.append( '<p> An error occurred while fetching events. </p>' )
				$( '#sdokus-ajax-notice' ).css( 'display', 'block' ).removeClass('notice-success').addClass('notice-error');;
			} );
	} );

	/**
	 * Outputs just the title and date of each event in the response
	 *
	 * @since 1.0.0
	 *
	 * @param response
	 */
	let renderEvents = function ( response ) {
		// Clear out the container in case it already has a response in it
		messageContainer.empty();
		messageContainer.append( "<strong>Events:</strong> <br>" );

		// From the response, add all the events (title and start date) to the container
		for ( let event of response.events ) {
			let listItem = $( '<li></li>' );
			listItem.html( sprintf( ajax_button_script_vars.event_happening_label, event.title, event.start_date ) );
			messageContainer.append( listItem );
		}

		// Show the notice by modifying its CSS
		$( '#sdokus-ajax-notice' ).css( 'display', 'block' ).removeClass('notice-error').addClass('notice-success');
	}
} );
