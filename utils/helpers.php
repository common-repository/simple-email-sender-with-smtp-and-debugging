<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function simple_email_sender_custom_error_log($message) {
    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/simple_email_sender_error.log';
    error_log(gmdate('[Y-m-d H:i:s] ') . $message . "\n", 3, $log_file);
}

function simple_email_sender_get_debug_info() {
    global $phpmailer;
    $debug_info = "";

    if (isset($phpmailer) && isset($phpmailer->ErrorInfo)) {
        $debug_info .= "PHPMailer Error: " . $phpmailer->ErrorInfo . "\n";
    }

    $debug_info .= "SMTP Settings:\n";
    $debug_info .= "Host: " . esc_html(get_option('simple_email_sender_smtp_host', '')) . "\n";
    $debug_info .= "Port: " . esc_html(get_option('simple_email_sender_smtp_port', '')) . "\n";
    $debug_info .= "Username: " . esc_html(get_option('simple_email_sender_smtp_username', '')) . "\n";
    $debug_info .= "Encryption: " . esc_html(get_option('simple_email_sender_smtp_encryption', '')) . "\n";
    $debug_info .= "From Email: " . esc_html(get_option('simple_email_sender_from_email', '')) . "\n";

    return $debug_info;
}

function simple_email_sender_log_mailer_errors($wp_error) {
    $debug_mode = get_option('simple_email_sender_debug_mode', 0);
    if ($debug_mode) {
        $error_message = $wp_error->get_error_message();
        simple_email_sender_custom_error_log('WordPress Mailer Error: ' . $error_message);
        return $error_message;
    }
}

function simple_email_sender_set_wp_mail_from($original_email_address) {
    $from_email = get_option('simple_email_sender_from_email', '');
    return !empty($from_email) ? $from_email : $original_email_address;
}
add_filter('wp_mail_from', 'simple_email_sender_set_wp_mail_from');