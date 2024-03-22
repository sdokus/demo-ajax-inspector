/**
 * Script for logging AJAX calls happening on the site.
 *
 * @since 1.0.0
 */

jQuery(document).ready(function ($) {
	// Function to handle checkbox change
	// $('#filter-form input[name="enable_ajax_logging"]').change(function() {
	// 	var isChecked = $(this).prop('checked');
	// 	// Make AJAX request to toggle AJAX listener
	// 	$.ajax({
	// 		url: ajax_log_script_vars.ajaxurl,
	// 		type: 'POST',
	// 		data: {
	// 			action: 'toggle_ajax_listener', // Action to handle this AJAX request on the server-side
	// 			enable_logging: isChecked ? '1' : '0' // Pass whether to enable or disable the AJAX listener
	// 		},
	// 		success: function(response) {
	// 			// Handle success response if needed
	// 		},
	// 		error: function(xhr, status, error) {
	// 			// Handle error if needed
	// 		}
	// 	});
	// });

	$('#submit-form').on('click', function() {
		var enableLogging = $('#enable_ajax_logging').is(':checked') ? '1' : '0';

		$.ajax({
			url: ajax_log_script_vars.ajaxurl,
			type: 'POST',
			data: {
				action: 'toggle_ajax_listener',
				enable_logging: enableLogging,
				nonce: ajax_log_script_vars.nonce
			},
			success: function(response) {
				alert('Changes Saved'); // this should be a WP notification instead of a HTML one
			},
			error: function(xhr, status, error) {
				console.error(xhr.responseText);
			}
		});
	});
});
