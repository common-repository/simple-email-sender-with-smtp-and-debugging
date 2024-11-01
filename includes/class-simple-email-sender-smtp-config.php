<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Simple_Email_Sender_SMTP_Config {
    public function init() {
        add_action('phpmailer_init', array($this, 'configure_smtp'));
    }

    public function configure_smtp($phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host = get_option('simple_email_sender_smtp_host', '');
        $phpmailer->Port = get_option('simple_email_sender_smtp_port', '587');
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = get_option('simple_email_sender_smtp_username', '');
        $phpmailer->Password = get_option('simple_email_sender_smtp_password', '');
        $phpmailer->SMTPSecure = get_option('simple_email_sender_smtp_encryption', 'tls');

        // Set the from email address
        $from_email = get_option('simple_email_sender_from_email', '');
        if (!empty($from_email)) {
            $phpmailer->setFrom($from_email, get_bloginfo('name'));
        }

        if (get_option('simple_email_sender_debug_mode', 0)) {
            $phpmailer->SMTPDebug = 2; // Enable verbose debug output
            $phpmailer->Debugoutput = function($str, $level) {
                simple_email_sender_custom_error_log("PHPMailer: $str");
            };
        }
    }
}