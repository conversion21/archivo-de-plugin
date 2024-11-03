<?php
class WodX_Form_Handler {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_wodx_form_submit', array($this, 'handle_form_submission'));
        add_action('wp_ajax_nopriv_wodx_form_submit', array($this, 'handle_form_submission'));
    }

   public function enqueue_scripts() {
    $style_path = plugin_dir_path(__FILE__) . '../assets/css/style.css';
    $script_path = plugin_dir_path(__FILE__) . '../assets/js/steps.js';

    $style_version = filemtime($style_path);
    $script_version = filemtime($script_path);

    wp_enqueue_style('wodx-form-style', plugin_dir_url(__FILE__) . '../assets/css/style.css', array(), $style_version);
    wp_enqueue_script('wodx-steps-script', plugin_dir_url(__FILE__) . '../assets/js/steps.js', array('jquery'), $script_version, true);

    // Check if form ID is provided
    if (isset($_GET['form_id'])) {
        $form_id = intval($_GET['form_id']);
        $redirect_url = get_post_meta($form_id, 'wodx_form_redirection_url', true);
    } else {
        $redirect_url = get_option('wodx_form_redirect_url', '');
    }

    wp_localize_script('wodx-steps-script', 'wodxForm', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'redirect_url' => $redirect_url,
        'nonce' => wp_create_nonce('wodx_form_nonce') // Add the nonce to the localized script
    ));
}

public function handle_form_submission() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wodx_form_nonce')) {
        wp_send_json_error(array('message' => 'Nonce verification failed'));
    }

    // Log the POST data for debugging
    error_log("POST data: " . print_r($_POST, true));

    $name = isset($_POST['nn']) ? sanitize_text_field($_POST['nn']) : '';
    $email = isset($_POST['ne']) ? sanitize_email($_POST['ne']) : '';
    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $responses = array(); // Capture the responses from previous steps

    if (empty($email) || !is_email($email)) {
        wp_send_json_error(array('message' => 'Invalid email address'));
    }

    $status = 'pending';
    $data = array(
        'name' => $name,
        'email' => $email,
        'form_id' => $form_id,
        'status' => $status,
        'responses' => maybe_serialize($responses)
    );

    global $wpdb;
    $table_name = $wpdb->prefix . 'wodx_forms';

    // Debugging line
    error_log("Inserting data into database: " . print_r($data, true));

    $result = $wpdb->insert($table_name, $data);

    if ($result === false) {
        wp_send_json_error(array('message' => 'Database insertion failed.'));
    }

    $this->send_confirmation_email($email, $form_id);

    // Get the redirection URL for the form
    $redirection_url = get_post_meta($form_id, 'wodx_form_redirection_url', true);
	$success_txt = get_post_meta($form_id, 'wodx_form_success_txt', true);
    wp_send_json_success(array('message' => $success_txt, 'redirect_url' => $redirection_url));
}

public function send_confirmation_email($email, $form_id) {
    $subject = get_post_meta($form_id, 'wodx_form_welcome_email_title', true);
    $template = get_post_meta($form_id, 'wodx_form_welcome_email', true);

    // Log the retrieved template
    error_log("Retrieved email template: " . $template);

    // Directly use the template as the email message
    $message = $template;

    // Log the final email content
    error_log("Final email content: " . $message);

    add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
    $mail_sent = wp_mail($email, $subject, $message);
    add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));

    if (!$mail_sent) {
        error_log("Failed to send email to: " . $email);
    } else {
        error_log("Email sent successfully to: " . $email);
    }
}

    public function set_html_content_type() {
        return 'text/html';
    }
}

new WodX_Form_Handler();