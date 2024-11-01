<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function simple_email_sender_add_menu_items() {
    add_menu_page(
        'Simple Email Sender',
        'Simple Email Sender',
        'manage_options',
        'simple-email-sender',
        'simple_email_sender_admin_page',
        'dashicons-email',
        100
    );
    add_submenu_page(
        'simple-email-sender',
        'SMTP Settings',
        'SMTP Settings',
        'manage_options',
        'simple-email-sender-smtp',
        'simple_email_sender_smtp_settings_page'
    );
}

function simple_email_sender_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Simple Email Sender', 'simple-email-sender'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('send_email_action', 'send_email_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="to_email"><?php echo esc_html__('To:', 'simple-email-sender'); ?></label></th>
                    <td><input type="email" name="to_email" id="to_email" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="subject"><?php echo esc_html__('Subject:', 'simple-email-sender'); ?></label></th>
                    <td><input type="text" name="subject" id="subject" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="message"><?php echo esc_html__('Message:', 'simple-email-sender'); ?></label></th>
                    <td><textarea name="message" id="message" class="large-text" rows="10" required></textarea></td>
                </tr>
            </table>
            <?php submit_button(esc_html__('Send Email', 'simple-email-sender')); ?>
        </form>
    </div>
    <?php

// Handle form submission
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['send_email_nonce']) &&
        wp_verify_nonce(sanitize_key(wp_unslash($_POST['send_email_nonce'])), 'send_email_action') &&
        isset($_POST['to_email']) &&
        isset($_POST['subject']) &&
        isset($_POST['message'])
    ) {
        $to = sanitize_email(wp_unslash($_POST['to_email']));
        $subject = sanitize_text_field(wp_unslash($_POST['subject']));
        $message = wp_kses_post(wp_unslash($_POST['message']));
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $result = Simple_Email_Sender_Email_Sender::send_email($to, $subject, $message, $headers);
        if ($result === true) {
            echo '<div class="notice notice-success"><p>' . esc_html__('Email sent successfully!', 'simple-email-sender') . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__('Failed to send email.', 'simple-email-sender') . ' ' . esc_html($result) . '</p>';
            echo '<p>' . esc_html__('Additional debug information:', 'simple-email-sender') . '</p>';
            echo '<pre>' . esc_html(simple_email_sender_get_debug_info()) . '</pre></div>';
        }
    } else {
        echo '<div class="notice notice-error"><p>' . esc_html__('Invalid form submission.', 'simple-email-sender') . '</p></div>';
    }
}
}