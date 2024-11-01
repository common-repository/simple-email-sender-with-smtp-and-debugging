<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function simple_email_sender_smtp_settings_page() {
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (
            isset($_POST['smtp_settings_nonce']) &&
            wp_verify_nonce(sanitize_key(wp_unslash($_POST['smtp_settings_nonce'])), 'save_smtp_settings')
        ) {
            // Validate and sanitize all inputs
            $smtp_host = isset($_POST['smtp_host']) ? sanitize_text_field(wp_unslash($_POST['smtp_host'])) : '';
            $smtp_port = isset($_POST['smtp_port']) ? absint(wp_unslash($_POST['smtp_port'])) : 587;
            $smtp_username = isset($_POST['smtp_username']) ? sanitize_text_field(wp_unslash($_POST['smtp_username'])) : '';
            $smtp_password = isset($_POST['smtp_password']) ? sanitize_text_field(wp_unslash($_POST['smtp_password'])) : '';
            $smtp_encryption = isset($_POST['smtp_encryption']) ? sanitize_text_field(wp_unslash($_POST['smtp_encryption'])) : 'tls';
            $from_email = isset($_POST['from_email']) ? sanitize_email(wp_unslash($_POST['from_email'])) : '';
            $debug_mode = isset($_POST['debug_mode']) ? 1 : 0;

            // Update options
            update_option('simple_email_sender_smtp_host', $smtp_host);
            update_option('simple_email_sender_smtp_port', $smtp_port);
            update_option('simple_email_sender_smtp_username', $smtp_username);
            update_option('simple_email_sender_smtp_password', $smtp_password);
            update_option('simple_email_sender_smtp_encryption', $smtp_encryption);
            update_option('simple_email_sender_from_email', $from_email);
            update_option('simple_email_sender_debug_mode', $debug_mode);

            echo '<div class="notice notice-success"><p>' . esc_html__('SMTP settings saved successfully!', 'simple-email-sender') . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__('Invalid nonce. SMTP settings not saved.', 'simple-email-sender') . '</p></div>';
        }
    }

    // Fetch current settings
    $smtp_host = get_option('simple_email_sender_smtp_host', '');
    $smtp_port = get_option('simple_email_sender_smtp_port', '587');
    $smtp_username = get_option('simple_email_sender_smtp_username', '');
    $smtp_password = get_option('simple_email_sender_smtp_password', '');
    $smtp_encryption = get_option('simple_email_sender_smtp_encryption', 'tls');
    $from_email = get_option('simple_email_sender_from_email', '');
    $debug_mode = get_option('simple_email_sender_debug_mode', 0);

    // Settings form
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('SMTP Settings', 'simple-email-sender'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('save_smtp_settings', 'smtp_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="smtp_host"><?php echo esc_html__('SMTP Host:', 'simple-email-sender'); ?></label></th>
                    <td><input type="text" name="smtp_host" id="smtp_host" value="<?php echo esc_attr($smtp_host); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="smtp_port"><?php echo esc_html__('SMTP Port:', 'simple-email-sender'); ?></label></th>
                    <td><input type="number" name="smtp_port" id="smtp_port" value="<?php echo esc_attr($smtp_port); ?>" class="small-text" required></td>
                </tr>
                <tr>
                    <th><label for="smtp_username"><?php echo esc_html__('SMTP Username:', 'simple-email-sender'); ?></label></th>
                    <td><input type="text" name="smtp_username" id="smtp_username" value="<?php echo esc_attr($smtp_username); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="smtp_password"><?php echo esc_html__('SMTP Password:', 'simple-email-sender'); ?></label></th>
                    <td><input type="password" name="smtp_password" id="smtp_password" value="<?php echo esc_attr($smtp_password); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="smtp_encryption"><?php echo esc_html__('Encryption:', 'simple-email-sender'); ?></label></th>
                    <td>
                        <select name="smtp_encryption" id="smtp_encryption">
                            <option value="tls" <?php selected($smtp_encryption, 'tls'); ?>><?php echo esc_html__('TLS', 'simple-email-sender'); ?></option>
                            <option value="ssl" <?php selected($smtp_encryption, 'ssl'); ?>><?php echo esc_html__('SSL', 'simple-email-sender'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="from_email"><?php echo esc_html__('From Email:', 'simple-email-sender'); ?></label></th>
                    <td><input type="email" name="from_email" id="from_email" value="<?php echo esc_attr($from_email); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="debug_mode"><?php echo esc_html__('Debug Mode:', 'simple-email-sender'); ?></label></th>
                    <td>
                        <input type="checkbox" name="debug_mode" id="debug_mode" <?php checked($debug_mode, 1); ?>>
                        <span class="description"><?php echo esc_html__('Enable debug mode (logs errors and displays detailed error messages)', 'simple-email-sender'); ?></span>
                    </td>
                </tr>
            </table>
            <?php submit_button(esc_html__('Save SMTP Settings', 'simple-email-sender')); ?>
        </form>
    </div>
    <?php

    // Handle test email submission
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (
            isset($_POST['test_email_nonce']) &&
            wp_verify_nonce(sanitize_key(wp_unslash($_POST['test_email_nonce'])), 'test_email_action') &&
            isset($_POST['test_email'])
        ) {
            $to = sanitize_email(wp_unslash($_POST['test_email']));
            $subject = esc_html__('Test Email from Simple Email Sender Plugin', 'simple-email-sender');
            $message = esc_html__('This is a test email sent from the Simple Email Sender plugin.', 'simple-email-sender');
            $headers = array('Content-Type: text/html; charset=UTF-8');

            $result = Simple_Email_Sender_Email_Sender::send_email($to, $subject, $message, $headers);
            if ($result === true) {
                echo '<div class="notice notice-success"><p>' . esc_html__('Test email sent successfully!', 'simple-email-sender') . '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>' . esc_html__('Failed to send test email. Error:', 'simple-email-sender') . ' ' . esc_html($result) . '</p>';
                echo '<p>' . esc_html__('Additional debug information:', 'simple-email-sender') . '</p>';
                echo '<pre>' . esc_html(simple_email_sender_get_debug_info()) . '</pre></div>';
            }
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__('Invalid test email submission.', 'simple-email-sender') . '</p></div>';
        }
    }

    // Test email form
    ?>
    <h2><?php echo esc_html__('Test Email', 'simple-email-sender'); ?></h2>
    <form method="post" action="">
        <?php wp_nonce_field('test_email_action', 'test_email_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="test_email"><?php echo esc_html__('To Email:', 'simple-email-sender'); ?></label></th>
                <td><input type="email" name="test_email" id="test_email" class="regular-text" required></td>
            </tr>
        </table>
        <?php submit_button(esc_html__('Send Test Email', 'simple-email-sender')); ?>
    </form>
    <?php
}