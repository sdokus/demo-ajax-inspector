/**
 * Script for starting and stopping real-time logging of AJAX calls
 *
 * @since 1.0.0
 */

jQuery(document).ready(function ($) {
	// Variable to keep track of whether logging is active or not
	var isLoggingActive = false;

	// Function to start or stop real-time logging
	function toggleAjaxLogging() {
		// Container for displaying messages on the page
		var messageContainer = $('#ajax-message-container');

		if (isLoggingActive) {
			// If logging is active, stop it
			$(document).off('ajaxSend.ajaxLogger');
			$(document).off('ajaxComplete.ajaxLogger');

			// Append the message to the container
			messageContainer.append('<p>Real-time AJAX logging stopped</p>');
		} else {
			// If logging is not active, start it
			$(document).on('ajaxSend.ajaxLogger', function (event, jqxhr, settings) {
				// Append the message to the container
				messageContainer.append('<p>AJAX request started: ' + JSON.stringify(settings) + '</p>');
			});

			$(document).on('ajaxComplete.ajaxLogger', function (event, jqxhr, settings) {
				// Append the message to the container
				messageContainer.append('<p>AJAX request completed: ' + JSON.stringify(settings) + '</p>');
				messageContainer.append('<p>Response: ' + jqxhr.responseText + '</p>');
				messageContainer.append('<p>Status: ' + jqxhr.status + '</p>');
			});

			// Append the message to the container
			messageContainer.append('<p>Real-time AJAX logging started</p>');
		}

		// Toggle the logging status
		isLoggingActive = !isLoggingActive;
	}

	// Event listener for button click
	$('#ajax-button').click(function () {
		toggleAjaxLogging();
	});
});
