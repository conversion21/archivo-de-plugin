<?php

class WodX_Steps_Handler {

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

    $redirect_url = get_option('wodx_form_redirect_url', '');
    wp_localize_script('wodx-steps-script', 'wodxForm', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'redirect_url' => $redirect_url,
        'nonce' => wp_create_nonce('wodx_form_nonce') // Add the nonce to the localized script
    ));
}




    public function handle_form_steps() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wodx_steps_nonce')) {
            wp_send_json_error(array('message' => 'Nonce verification failed'));
        }

        $steps = sanitize_text_field($_POST['steps']);
        $post_id = intval($_POST['post_id']);

        update_post_meta($post_id, 'wodx_form_steps', $steps);

        wp_send_json_success(array('message' => 'Steps saved successfully'));
    }

    public function handle_form_submission() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wodx_form_nonce')) {
            wp_send_json_error(array('message' => 'Nonce verification failed'));
        }

        $name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $responses = array(); // Capture the responses from previous steps

        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Invalid email address'));
        }

        $status = 'pending';
        $data = array('name' => $name, 'email' => $email, 'status' => $status, 'responses' => maybe_serialize($responses));

        global $wpdb;
        $table_name = $wpdb->prefix . 'wodx_forms';
        $wpdb->insert($table_name, $data);

        //$this->send_confirmation_email($email);

        wp_send_json_success(array('message' => 'Form submitted successfully. Please check your email to confirm.'));
    }


   /* public function send_confirmation_email($email) {
        $confirmation_link = add_query_arg(array('email' => urlencode($email)), home_url('/wodx-confirm-email'));
        $subject = 'Email Confirmation';
        $message = 'Please click the following link to confirm your email: ' . $confirmation_link;
        wp_mail($email, $subject, $message);
    } */
}

new WodX_Steps_Handler();
