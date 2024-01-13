jQuery(document).ready(function($) {
	$('#ajaxButton').click(function() {
		$.ajax({

			type: 'GET',
			data: {
				action: 'ajax_button_action'
			},
			success: function(response) {
				console.log('AJAX request successful:', response);
				// Do something with the response
			},
			error: function(xhr, status, error) {
				console.error('AJAX request failed:', status, error);
				// Handle errors
			}
		});
	});
});
