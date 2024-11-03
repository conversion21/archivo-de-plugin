<?php 
class WodX_Forms {

    public function init() {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box'));
        add_shortcode('wodx_forms', array($this, 'render_form'));
        add_filter('manage_wodx_form_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_wodx_form_posts_custom_column', array($this, 'custom_column'), 10, 2);
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_form_styles'));
        add_action('wp', array($this, 'handle_email_confirmation')); // New action
    }

    public function register_post_type() {
        register_post_type('wodx_form', array(
            'labels' => array(
                'name' => __('Wodx Forms'),
                'singular_name' => __('Form'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title'),
            'menu_icon' => 'dashicons-feedback',
            'show_in_menu' => true,
        ));
    }

    public function enqueue_admin_scripts($hook) {
        wp_enqueue_script('wodx-admin-script', plugin_dir_url(__FILE__) . '../assets/js/admin.js', array('jquery'), null, true);
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    public function add_meta_boxes() {
        add_meta_box('wodx_form_steps', __('Form Steps'), array($this, 'render_steps_meta_box'), 'wodx_form', 'normal', 'high');
    }

    public function render_steps_meta_box($post) {
        $steps = get_post_meta($post->ID, 'wodx_form_steps', true);
        if (!is_array($steps)) {
            $steps = array();
        }

        $final_title = get_post_meta($post->ID, 'wodx_form_final_title', true);
        $final_text = get_post_meta($post->ID, 'wodx_form_final_text', true);
        $button_txt = get_post_meta($post->ID, 'wodx_form_btn_link_txt', true);
        $button_link = get_post_meta($post->ID, 'wodx_form_btn_link', true);
        $form_list = get_post_meta($post->ID, 'wodx_form_list', true);
        $welcome_email = get_post_meta($post->ID, 'wodx_form_welcome_email', true);
        $email_title = get_post_meta($post->ID, 'wodx_form_welcome_email_title', true);
        $redirection_url = get_post_meta($post->ID, 'wodx_form_redirection_url', true);
        $success_txt = get_post_meta($post->ID, 'wodx_form_success_txt', true);
        $whatsapp_share_message = get_post_meta($post->ID, 'wodx_form_whatsapp_share_message', true); 
        $whatsapp_share_count = get_post_meta($post->ID, 'wodx_form_whatsapp_share_count', true); 
        $whatsapp_button_image = get_post_meta($post->ID, 'wodx_form_whatsapp_button_image', true); 

        // New field for starting step
        $starting_step = get_post_meta($post->ID, 'wodx_form_starting_step', true);
        if (!$starting_step) {
            $starting_step = 1; // Default to 1 if not set
        }

        include plugin_dir_path(__FILE__) . '/templates/steps-template.php';

        // Add starting step input field
        echo '<div style="margin-top: 20px;">';
        echo '<label for="wodx_form_starting_step">Starting Step:</label>';
        echo '<input type="number" name="wodx_form_starting_step" value="' . esc_attr($starting_step) . '" min="1" max="' . count($steps) . '" />';
        echo '<p class="description">Specify the step where the form should start. Default is 1.</p>';
        echo '</div>';
    }



// Helper function to check if the URL is an image
public function is_image_url($url) {
    $image_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
    return in_array($extension, $image_extensions);
}



public function save_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['wodx_form_steps'])) {
        $sanitized_steps = array();
        foreach ($_POST['wodx_form_steps'] as $index => $step) {
            $sanitized_steps[$index] = array(
                'title' => sanitize_text_field($step['title']),
                'description' => wp_kses_post($step['description']),
                'subtitle' => sanitize_text_field($step['subtitle']),
                'options' => sanitize_text_field($step['options']),
                'redirect_url' => esc_url_raw($step['redirect_url']) // Save the redirect URL
            );
        }
        update_post_meta($post_id, 'wodx_form_steps', $sanitized_steps);
    }

    if (isset($_POST['wodx_form_final_title'])) {
        update_post_meta($post_id, 'wodx_form_final_title', sanitize_text_field($_POST['wodx_form_final_title']));
    }

    if (isset($_POST['wodx_form_final_text'])) {
        update_post_meta($post_id, 'wodx_form_final_text', wp_kses_post($_POST['wodx_form_final_text']));
    }

    if (isset($_POST['wodx_form_btn_link_txt'])) {
        update_post_meta($post_id, 'wodx_form_btn_link_txt', sanitize_text_field($_POST['wodx_form_btn_link_txt']));
    }

    if (isset($_POST['wodx_form_btn_link'])) {
        update_post_meta($post_id, 'wodx_form_btn_link', sanitize_text_field($_POST['wodx_form_btn_link']));
    }

    if (isset($_POST['wodx_form_list'])) {
        update_post_meta($post_id, 'wodx_form_list', sanitize_text_field($_POST['wodx_form_list']));
    }

    if (isset($_POST['wodx_form_success_txt'])) {
        update_post_meta($post_id, 'wodx_form_success_txt', sanitize_text_field($_POST['wodx_form_success_txt']));
    }

    if (isset($_POST['wodx_form_welcome_email_title'])) {
        update_post_meta($post_id, 'wodx_form_welcome_email_title', sanitize_text_field($_POST['wodx_form_welcome_email_title']));
    }

    if (isset($_POST['wodx_form_welcome_email'])) {
        update_post_meta($post_id, 'wodx_form_welcome_email', wp_kses_post($_POST['wodx_form_welcome_email']));
    }

    if (isset($_POST['wodx_form_redirection_url'])) {
        update_post_meta($post_id, 'wodx_form_redirection_url', esc_url_raw($_POST['wodx_form_redirection_url']));
    }

    if (isset($_POST['wodx_form_whatsapp_share_message'])) {
      update_post_meta($post_id, 'wodx_form_whatsapp_share_message', sanitize_textarea_field($_POST['wodx_form_whatsapp_share_message']));
	}

    if (isset($_POST['wodx_form_whatsapp_share_count'])) {
        update_post_meta($post_id, 'wodx_form_whatsapp_share_count', intval($_POST['wodx_form_whatsapp_share_count']));
    }

    if (isset($_POST['wodx_form_whatsapp_button_image'])) {
        update_post_meta($post_id, 'wodx_form_whatsapp_button_image', sanitize_text_field($_POST['wodx_form_whatsapp_button_image']));
    }
    // Save the starting step
    if (isset($_POST['wodx_form_starting_step'])) {
        update_post_meta($post_id, 'wodx_form_starting_step', intval($_POST['wodx_form_starting_step']));
    }
}



    public function render_form($atts) {
        $atts = shortcode_atts(array('id' => null), $atts, 'wodx_forms');
        $form_id = $atts['id'];

        ob_start();
        include plugin_dir_path(__FILE__) . '/templates/form-template.php';
        return ob_get_clean();
    }

    public function set_custom_columns($columns) {
        $columns['shortcode'] = __('Shortcode');
        return $columns;
    }

    public function custom_column($column, $post_id) {
        switch ($column) {
            case 'shortcode':
                echo '[wodx_forms id="' . $post_id . '"]';
                break;
        }
    }

    public function admin_menu() {
        add_submenu_page(
            'edit.php?post_type=wodx_form',
            __('Submissions', 'wodx-forms'),
            __('Submissions', 'wodx-forms'),
            'manage_options',
            'wodx-forms-submissions',
            array($this, 'submissions_page')
        );

        add_submenu_page(
            'edit.php?post_type=wodx_form',
            __('Settings', 'wodx-forms'),
            __('Settings', 'wodx-forms'),
            'manage_options',
            'wodx-forms-settings',
            array($this, 'settings_page')
        );

        add_submenu_page(
            'edit.php?post_type=wodx_form',
            __('Email Templates', 'wodx-forms'),
            __('Email Templates', 'wodx-forms'),
            'manage_options',
            'wodx-forms-email-templates',
            array($this, 'email_templates_page')
        );
    }

    public function email_templates_page() {
        if (isset($_POST['save_templates'])) {
            check_admin_referer('wodx_forms_email_templates');

            $allowed_html = wp_kses_allowed_html('post');

            for ($i = 1; $i <= 5; $i++) {
                $title = sanitize_text_field($_POST['email_template_title_' . $i]);
                $template = wp_kses($_POST['email_template_' . $i], $allowed_html);

                update_option('wodx_form_email_template_title_' . $i, $title);
                update_option('wodx_form_email_template_' . $i, $template);
            }

            echo '<div class="updated"><p>Templates saved.</p></div>';
        }

        echo '<div class="wrap">';
        echo '<h2>' . __('WodX Forms Email Templates', 'wodx-forms') . '</h2>';
        echo '<form method="post">';
        wp_nonce_field('wodx_forms_email_templates');
        for ($i = 1; $i <= 5; $i++) {
            $title = get_option('wodx_form_email_template_title_' . $i, 'WodX Form Email Template ' . $i);
            $template = get_option('wodx_form_email_template_' . $i, '');
            echo '<h3>Email Template ' . $i . '</h3>';
            echo '<label for="email_template_title_' . $i . '">Email Title</label>';
            echo '<input type="text" name="email_template_title_' . $i . '" value="' . esc_attr($title) . '" class="large-text">';
            echo '<textarea name="email_template_' . $i . '" rows="10" cols="50" class="large-text code">' . esc_textarea($template) . '</textarea>';
        }
        echo '<p><button type="submit" name="save_templates" class="button button-primary">Save Templates</button></p>';
        echo '</form>';
        echo '</div>';
    }

    public function admin_page() {
        $args = array(
            'post_type' => 'wodx_form',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
        $forms = new WP_Query($args);

        echo '<div class="wrap"><h2>' . __('WodX Forms', 'wodx-forms') . '</h2>';
        echo '<p>Welcome to WodX Forms plugin.</p>';
        
        if ($forms->have_posts()) {
            echo '<table class="widefat"><thead><tr><th>Title</th><th>Shortcode</th><th>Date</th></thead><tbody>';
            while ($forms->have_posts()) : $forms->the_post();
                echo '<tr>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>[wodx_forms id="' . get_the_ID() . '"]</td>';
                echo '<td>' . get_the_date() . '</td>';
                echo '</tr>';
            endwhile;
            echo '</tbody></table>';
        } else {
            echo '<p>No forms found.</p>';
        }

        wp_reset_postdata();
        echo '</div>';
    }

    public function submissions_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wodx_forms';

        // Handle search query
        $search_query = '';
        if (isset($_POST['s'])) {
            $search_query = sanitize_text_field($_POST['s']);
        }

        // Handle deletion
        if (isset($_POST['delete'])) {
            $ids_to_delete = implode(',', array_map('intval', $_POST['submissions']));
            $wpdb->query("DELETE FROM $table_name WHERE id IN ($ids_to_delete)");
        }

        // Handle export
        if (isset($_POST['export'])) {
            $ids_to_export = isset($_POST['submissions']) ? array_map('intval', $_POST['submissions']) : array();
            $export_url = add_query_arg('download_report', implode(',', $ids_to_export), admin_url('edit.php?post_type=wodx_form&page=wodx-forms-submissions'));
            wp_redirect($export_url);
            exit;
        }

        // Handle email sending
        if (isset($_POST['send_emails'])) {
            $template_id = intval($_POST['email_template']);
            $ids_to_email = isset($_POST['submissions']) ? array_map('intval', $_POST['submissions']) : array();
            $this->send_bulk_emails($ids_to_email, $template_id);
        }

        // Query to get the submissions
        $query = "SELECT * FROM $table_name";
        if (!empty($search_query)) {
            $query .= $wpdb->prepare(" WHERE name LIKE %s OR email LIKE %s", '%' . $wpdb->esc_like($search_query) . '%', '%' . $wpdb->esc_like($search_query) . '%');
        }

        $results = $wpdb->get_results($query);

        echo '<div class="wrap"><h2>' . __('WodX Forms Submissions', 'wodx-forms') . '</h2>';
        echo '<form method="post">';
        echo '<input type="text" name="s" value="' . esc_attr($search_query) . '" placeholder="Search users..." />';
        echo '<button type="submit" class="button">Search</button>';
        echo '</form>';

        echo '<form method="post">';
        echo '<table class="widefat"><thead><tr><th><input type="checkbox" id="select-all"></th><th>Name</th><th>Email</th><th>Status</th></tr></thead><tbody>';

        foreach ($results as $row) {
            echo '<tr>';
            echo '<td><input type="checkbox" name="submissions[]" value="' . esc_attr($row->id) . '"></td>';
            echo '<td>' . esc_html($row->name) . '</td>';
            echo '<td>' . esc_html($row->email) . '</td>';
            echo '<td>' . esc_html($row->status) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        echo '<select name="email_template">';
        for ($i = 1; $i <= 5; $i++) {
            echo '<option value="' . $i . '">Email Template ' . $i . '</option>';
        }
        echo '</select>';

        echo '<button type="submit" name="delete" class="button button-primary">Delete Selected</button>';
        echo '<button type="submit" name="export" class="button button-secondary">Export Selected</button>';
        echo '<button type="submit" name="send_emails" class="button button-secondary">Send Emails</button>';
        echo '</form>';
        echo '</div>';
    }

    public function settings_page() {
        if (isset($_POST['save_settings'])) {
            check_admin_referer('wodx_forms_settings');

            update_option('wodx_form_max_width', sanitize_text_field($_POST['max_width']));
            update_option('wodx_form_font_color', sanitize_hex_color($_POST['font_color']));
            update_option('wodx_form_button_color', sanitize_hex_color($_POST['button_color']));
            update_option('wodx_form_newsletter_form', sanitize_text_field($_POST['newsletter_form'])); // New setting
            update_option('wodx_form_success_message', sanitize_text_field($_POST['success_message']));
            update_option('wodx_form_redirect_url', esc_url_raw($_POST['redirect_url']));
            update_option('wodx_form_confirmation_redirect_url', esc_url_raw($_POST['confirmation_redirect_url'])); // New setting

            echo '<div class="updated"><p>Settings saved.</p></div>';
        }

        $max_width = get_option('wodx_form_max_width', '600px');
        $font_color = get_option('wodx_form_font_color', '#000000');
        $button_color = get_option('wodx_form_button_color', '#0000FF');
        $newsletter_form = get_option('wodx_form_newsletter_form', '[newsletter_form]');
        $success_message = get_option('wodx_form_success_message', 'Form submitted successfully. Please check your email to confirm.');
        $redirect_url = get_option('wodx_form_redirect_url', '');
        $confirmation_redirect_url = get_option('wodx_form_confirmation_redirect_url', ''); // New setting

        echo '<div class="wrap">';
        echo '<h2>' . __('WodX Forms Settings', 'wodx-forms') . '</h2>';
        echo '<form method="post">';
        wp_nonce_field('wodx_forms_settings');
        echo '<table class="form-table">';
        echo '<tr><th>Max Width</th><td><input type="text" name="max_width" value="' . esc_attr($max_width) . '"></td></tr>';
        echo '<tr><th>Font Color</th><td><input type="text" class="color-picker" name="font_color" value="' . esc_attr($font_color) . '"></td></tr>';
        echo '<tr><th>Button Color</th><td><input type="text" class="color-picker" name="button_color" value="' . esc_attr($button_color) . '"></td></tr>';
        echo '<tr><th>Newsletter plugin form</th><td><input type="text" name="newsletter_form" value="' . esc_attr($newsletter_form) . '"></td></tr>';
        echo '<tr><th>Success Message</th><td><input type="text" name="success_message" value="' . esc_attr($success_message) . '"></td></tr>';
        echo '<tr><th>Redirect URL</th><td><input type="text" name="redirect_url" value="' . esc_attr($redirect_url) . '"></td></tr>';
        echo '<tr><th>Confirmation Redirect URL</th><td><input type="text" name="confirmation_redirect_url" value="' . esc_attr($confirmation_redirect_url) . '"></td></tr>'; // New field
        echo '</table>';
        echo '<p><button type="submit" name="save_settings" class="button button-primary">Save Settings</button></p>';
        echo '</form>';
        echo '</div>';

        echo '<script type="text/javascript">
            jQuery(document).ready(function($){
                $(".color-picker").wpColorPicker();
            });
        </script>';
    }

    public function enqueue_form_styles() {
        $max_width = get_option('wodx_form_max_width', '600px');
        $font_color = get_option('wodx_form_font_color', '#000000');
        $button_color = get_option('wodx_form_button_color', '#0000FF');

        $custom_css = "
            #wodx-form {
                max-width: {$max_width};
                margin: 0 auto;
            }
            #wodx-form h2, #wodx-form h3{
                color: {$font_color}
            }
            #wodx-form div.tnp-subscription, form.tnp-subscription, form.tnp-profile {
                max-width: {$max_width} !important;
            }
            #wodx-form label {
                display: block;
                margin-bottom: 5px;
                color: {$font_color};
            }
            #wodx-form input {
                width: 100%;
                padding: 8px;
                margin-bottom: 10px;
            }
            #wodx-form button {
                padding: 10px 40px;
                border: 1px solid {$button_color};
                color: {$font_color};
            }
            #wodx-form input{
                border: 1px solid {$button_color};
            }
            #wodx-form button:hover {
                padding: 10px 40px;
                border: 1px solid {$button_color};
                background-color: {$button_color};
                color: #fff;
            }
            #wodx-form-response {
                margin-top: 20px;
            }
            .step {
                display: none; /* Initially hide all steps */
            }
            .step[data-step='1'] {
                display: block; /* Show the first step initially */
            }
            #progress-bar {
                width: 100%;
                background-color: #e0e0e0;
                border-radius: 5px;
                margin-bottom: 10px;
                overflow: hidden;
            }

            .progress-bar-fill {
                height: 10px;
                background-color: {$button_color};
                width: 0;
                transition: width 0.3s ease-in-out;
            }

            .step-count {
                text-align: center;
                margin-bottom: 20px;
            }

        ";

        wp_add_inline_style('wodx-form-style', $custom_css);
    }

    public function query_vars($vars) {
        $vars[] = 'download_report';
        return $vars;
    }

    public function parse_request($wp) {
        if (array_key_exists('download_report', $wp->query_vars)) {
            $this->download_report();
            exit;
        }
    }

    public function download_report() {
        if (!isset($_GET['download_report'])) {
            return;
        }

        $ids = explode(',', sanitize_text_field($_GET['download_report']));

        $csv = $this->generate_csv($ids);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"report.csv\";");
        header("Content-Transfer-Encoding: binary");

        echo $csv;
        exit;
    }

    public function generate_csv($ids) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wodx_forms';

        if (empty($ids)) {
            $results = $wpdb->get_results("SELECT * FROM $table_name");
        } else {
            $ids = implode(',', array_map('intval', $ids));
            $results = $wpdb->get_results("SELECT * FROM $table_name WHERE id IN ($ids)");
        }

        $csv_output = '';
        $csv_output .= "ID,Name,Email,Status\n"; // Headers

        foreach ($results as $row) {
            $csv_output .= "{$row->id},{$row->name},{$row->email},{$row->status}\n";
        }

        return $csv_output;
    }

    public function handle_email_confirmation() {
        if (isset($_GET['action']) && $_GET['action'] === 'wodx_confirm_email' && isset($_GET['ne'])) {
            global $wpdb;
            $email = sanitize_email($_GET['ne']);
            $table_name = $wpdb->prefix . 'wodx_forms';

            // Retrieve the form ID associated with this email
            $form_id = $wpdb->get_var($wpdb->prepare("SELECT form_id FROM $table_name WHERE email = %s", $email));

            if ($form_id) {
                $wpdb->update(
                    $table_name,
                    array('status' => 'confirmed'),
                    array('email' => $email),
                    array('%s'),
                    array('%s')
                );

                // Get the form-specific confirmation redirect URL
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
                            }, 3000); // Redirect after 3 seconds
                        });
                    </script>
                </body>
                </html>';
                exit;
            }
        }
    }

    public function wodx_set_content_type() {
        return "text/html";
    }

    public function send_bulk_emails($ids_to_email, $template_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wodx_forms';

        add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));

        foreach ($ids_to_email as $id) {
            $submission = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
            if ($submission) {
                // Retrieve the form-specific email template and title
                $form_id = $submission->form_id;
                $template = get_post_meta($form_id, 'wodx_form_welcome_email', true);
                $title = get_post_meta($form_id, 'wodx_form_welcome_email_title', true);

                // Replace placeholders in the template
                $message = str_replace('[name]', $submission->name, $template);

                // Send the email
                wp_mail($submission->email, $title, $message);
            }
        }

        remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));

        echo '<div class="updated"><p>Emails sent successfully.</p></div>';
    }

    public function set_html_content_type() {
        return 'text/html';
    }
}
?>
