<?php
/*
Plugin Name: Simple Email Sender
Description: A simple plugin to send emails from WordPress admin panel using SMTP, with debugging features.
Version: 1.0
Author: stodat technologies
Author URI: https://stodat.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('SIMPLE_EMAIL_SENDER_VERSION', '1.1');
define('SIMPLE_EMAIL_SENDER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SIMPLE_EMAIL_SENDER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once SIMPLE_EMAIL_SENDER_PLUGIN_DIR . 'admin/admin-page.php';
require_once SIMPLE_EMAIL_SENDER_PLUGIN_DIR . 'admin/smtp-settings.php';
require_once SIMPLE_EMAIL_SENDER_PLUGIN_DIR . 'includes/class-simple-email-sender-email-sender.php';
require_once SIMPLE_EMAIL_SENDER_PLUGIN_DIR . 'includes/class-simple-email-sender-smtp-config.php';
require_once SIMPLE_EMAIL_SENDER_PLUGIN_DIR . 'utils/helpers.php';

// Initialize the plugin
function simple_email_sender_init() {
    // Add admin menu items
    add_action('admin_menu', 'simple_email_sender_add_menu_items');

    // Initialize SMTP configuration
    $smtp_config = new Simple_Email_Sender_SMTP_Config();
    $smtp_config->init();
}
add_action('plugins_loaded', 'simple_email_sender_init');