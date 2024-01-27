/**
 * Script for starting and stopping real-time logging of AJAX calls
 *
 * @since 1.0.0
 */

jQuery(document).ready(function ($) {
	// Variable to keep track of whether logging is active or not
	var isLoggingActive = false;

	// Container for displaying messages on the page
	var messageContainer = $('#ajax-message-container');

	// Function to start or stop real-time logging
	function toggleAjaxLogging() {
		if (isLoggingActive) {
			stopAjaxLogging();
		} else {
			startAjaxLogging();
		}
	}

	// Function to start real-time logging
	function startAjaxLogging() {
		// If logging is not active, start it
		$(document).on('ajaxSend.ajaxLogger', function (event, jqxhr, settings) {
			// Append the message to the container
			messageContainer.append('<p>AJAX request started: ' + JSON.stringify(settings) + '</p>');
		});

		$(document).on('ajaxComplete.ajaxLogger', function (event, jqxhr, settings) {
			// Append the message to the container
			messageContainer.append('<div>AJAX request completed: ' + JSON.stringify(settings) + '</div>');
			messageContainer.append('<div>Response: ' + jqxhr.responseText + '</div>');
			messageContainer.append('<div>Status: ' + jqxhr.status + '</div>');
		});

		// Append the message to the container
		messageContainer.append('<p>Real-time AJAX logging started</p>');

		// Change the button text
		$('#ajax-button').text('Stop Listening');
		// Toggle the logging status
		isLoggingActive = true;
	}

	// Function to stop real-time logging
	function stopAjaxLogging() {
		// If logging is active, stop it
		$(document).off('ajaxSend.ajaxLogger');
		$(document).off('ajaxComplete.ajaxLogger');

		// Append the message to the container
		messageContainer.append('<p>Real-time AJAX logging stopped</p>');

		// Change the button text
		$('#ajax-button').text('Start Listening for AJAX calls');
		// Toggle the logging status
		isLoggingActive = false;
	}

	// Event listener for button click
	$('#ajax-button').click(function () {
		toggleAjaxLogging();
	});

	// Event listener for test button click
	$('#test-ajax-button').click(function () {
		// Send a test AJAX call
		$.ajax({
			type: 'GET',
			url: ajax_button_script_vars.ajaxurl,
			data: {
				action: 'test_ajax_action'
			},
			success: function (response) {
				console.log('Test AJAX request successful:', response);
			},
			error: function (xhr, status, error) {
				console.error('Test AJAX request failed:', status, error);
			}
		});
	});
});
