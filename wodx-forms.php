<?php
/*
Plugin Name: WodX Forms
Description: Custom forms plugin with email confirmation.
Version: 1.0
Author: Samuel Sutera
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include the main class
include_once plugin_dir_path(__FILE__) . 'includes/class-wodx-forms.php';

// Include the form handler class
include_once plugin_dir_path(__FILE__) . 'includes/class-wodx-form-handler.php';

// Include the email handler class
include_once plugin_dir_path(__FILE__) . 'includes/class-wodx-email-handler.php';

// Include the steps handler class
include_once plugin_dir_path(__FILE__) . 'includes/class-wodx-steps-handler.php';

// Initialize the plugin
function wodx_forms_init() {
    $wodx_forms = new WodX_Forms();
    $wodx_forms->init();
}
add_action('plugins_loaded', 'wodx_forms_init');

register_activation_hook(__FILE__, 'wodx_forms_create_table');

function wodx_forms_create_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'wodx_forms';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email text NOT NULL,
        form_id int(11) NOT NULL,
        status tinytext NOT NULL,
        responses longtext NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


add_action('admin_post_export_submissions', 'wodx_export_submissions');

function wodx_export_submissions() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized user');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'wodx_forms';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    $filename = 'wodx_form_submissions_' . date('Y-m-d') . '.csv';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);

    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Name', 'Email', 'Status', 'Responses'));

    foreach ($results as $row) {
        fputcsv($output, array($row->id, $row->name, $row->email, $row->status, maybe_unserialize($row->responses)));
    }

    fclose($output);
    exit;
}



