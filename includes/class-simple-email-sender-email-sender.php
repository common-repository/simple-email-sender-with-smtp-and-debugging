<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Simple_Email_Sender_Email_Sender {
    public static function send_email($to, $subject, $message, $headers) {
        $debug_mode = get_option('simple_email_sender_debug_mode', 0);

        // Enable debug output
        if ($debug_mode) {
            global $phpmailer;
            $phpmailer = null; // Reset phpmailer instance
        }

        add_action('wp_mail_failed', 'simple_email_sender_log_mailer_errors', 10, 1);

        // Attempt to send email
        $result = wp_mail($to, $subject, $message, $headers);

        // Check for errors
        if (!$result) {
            global $phpmailer;
            $error = "Unknown error";
            if (isset($phpmailer) && isset($phpmailer->ErrorInfo)) {
                $error = $phpmailer->ErrorInfo;
            } elseif (function_exists('error_get_last')) {
                $error_get_last = error_get_last();
                if ($error_get_last !== NULL) {
                    $error = $error_get_last['message'];
                }
            }
            simple_email_sender_custom_error_log("Email sending failed: $error");
            return $debug_mode ? $error : esc_html__('Failed to send email. Check the error log for more details.', 'simple-email-sender');
        }

        return true;
    }
}