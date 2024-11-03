<?php
class WodX_Email_Handler {

    public function __construct() {
        add_action('init', array($this, 'handle_email_confirmation'));
    }

   public function handle_email_confirmation() {
		if (isset($_GET['action']) && $_GET['action'] === 'wodx_confirm_email' && isset($_GET['ne']) && isset($_GET['form_id'])) {
			error_log('handle_email_confirmation executed'); // Debugging line
			global $wpdb;
			$email = sanitize_email($_GET['ne']);
			$form_id = intval($_GET['form_id']);
			$table_name = $wpdb->prefix . 'wodx_forms';
			$wpdb->update(
				$table_name,
				array('status' => 'confirmed'),
				array('email' => $email),
				array('%s'),
				array('%s')
			);

			$confirmation_redirect_url = get_post_meta($form_id, 'wodx_form_redirection_url', true);

			// Output the HTML with JavaScript for redirection
			echo '<!DOCTYPE html>
			<html>
			<head>
				<title>Email Confirmation</title>
			</head>
			<body>
				<p>Email confirmed successfully</p>
				<script type="text/javascript">
					document.addEventListener("DOMContentLoaded", function() {
						var redirectUrl = "' . esc_url($confirmation_redirect_url) . '";
						setTimeout(function() {
							window.location.href = redirectUrl;
						}, 500); // Redirect after 3 seconds
					});
				</script>
			</body>
			</html>';
			exit;
		}
	}


}

new WodX_Email_Handler();