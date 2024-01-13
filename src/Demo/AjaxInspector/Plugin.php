<?php
namespace Demo\AjaxInspector;

class Plugin {
	public function __construct() {
		// Your existing code here
		add_action('wp_enqueue_scripts', array($this, 'add_jquery'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_ajax_button_script'));
		add_shortcode('ajax_button', array($this, 'ajax_button_shortcode'));
	}

	public function add_jquery() {
		wp_enqueue_script('jquery');
	}

	public function enqueue_ajax_button_script() {
		wp_enqueue_script('ajax-button-script', plugin_dir_url(__FILE__) . 'ajax-button-script.js', array('jquery'), '1.0', true);
	}

	public function ajax_button_shortcode() {
		ob_start();
		?>
		<button id="ajaxButton">Click me for AJAX</button>
		<?php
		return ob_get_clean();
	}
}
