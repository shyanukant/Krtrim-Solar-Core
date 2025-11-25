<?php
/**
 * Admin settings page for Notification Configuration.
 */

// Render the settings page content
function sp_render_notification_settings_page() {
    ?>
    <div class="wrap">
        <h1>Notification Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('sp_notification_settings_group');
            do_settings_sections('notification-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings, sections, and fields
function sp_register_notification_settings() {
    // Register the main setting group
    register_setting('sp_notification_settings_group', 'sp_notification_options');

    // Email Settings Section
    add_settings_section(
        'sp_email_section',
        'Email Notifications',
        'sp_email_section_callback',
        'notification-settings'
    );

    add_settings_field(
        'email_vendor_approved',
        'Vendor Approved Email',
        'sp_email_vendor_approved_callback',
        'notification-settings',
        'sp_email_section'
    );
    
    add_settings_field(
        'email_vendor_rejected',
        'Vendor Rejected Email',
        'sp_email_vendor_rejected_callback',
        'notification-settings',
        'sp_email_section'
    );

    add_settings_field(
        'email_submission_approved',
        'Submission Approved Email',
        'sp_email_submission_approved_callback',
        'notification-settings',
        'sp_email_section'
    );

    add_settings_field(
        'email_submission_rejected',
        'Submission Rejected Email',
        'sp_email_submission_rejected_callback',
        'notification-settings',
        'sp_email_section'
    );

    // WhatsApp Settings Section
    add_settings_section(
        'sp_whatsapp_section',
        'WhatsApp Click-to-Chat',
        'sp_whatsapp_section_callback',
        'notification-settings'
    );

    add_settings_field(
        'whatsapp_enable',
        'Enable WhatsApp Buttons',
        'sp_whatsapp_enable_callback',
        'notification-settings',
        'sp_whatsapp_section'
    );

    add_settings_field(
        'whatsapp_vendor_approved',
        'On Vendor Approved',
        'sp_whatsapp_vendor_approved_callback',
        'notification-settings',
        'sp_whatsapp_section'
    );
    
    add_settings_field(
        'whatsapp_vendor_rejected',
        'On Vendor Rejected',
        'sp_whatsapp_vendor_rejected_callback',
        'notification-settings',
        'sp_whatsapp_section'
    );

    add_settings_field(
        'whatsapp_submission_approved',
        'On Submission Approved',
        'sp_whatsapp_submission_approved_callback',
        'notification-settings',
        'sp_whatsapp_section'
    );

    add_settings_field(
        'whatsapp_submission_rejected',
        'On Submission Rejected',
        'sp_whatsapp_submission_rejected_callback',
        'notification-settings',
        'sp_whatsapp_section'
    );
}
add_action('admin_init', 'sp_register_notification_settings');

// Section callbacks
function sp_email_section_callback() {
    echo 'Configure which email notifications are sent automatically.';
}

function sp_whatsapp_section_callback() {
    echo 'Enable "Click-to-Chat" buttons, which open a pre-filled WhatsApp chat in a new tab.';
}

// Field callbacks (Email)
function sp_email_vendor_approved_callback() {
    $options = get_option('sp_notification_options');
    $checked = isset($options['email_vendor_approved']) ? 'checked' : '';
    echo "<input type='checkbox' name='sp_notification_options[email_vendor_approved]' value='1' $checked />";
}

function sp_email_vendor_rejected_callback() {
    $options = get_option('sp_notification_options');
    $checked = isset($options['email_vendor_rejected']) ? 'checked' : '';
    echo "<input type='checkbox' name='sp_notification_options[email_vendor_rejected]' value='1' $checked />";
}

function sp_email_submission_approved_callback() {
    $options = get_option('sp_notification_options');
    $checked = isset($options['email_submission_approved']) ? 'checked' : '';
    echo "<input type='checkbox' name='sp_notification_options[email_submission_approved]' value='1' $checked />";
}

function sp_email_submission_rejected_callback() {
    $options = get_option('sp_notification_options');
    $checked = isset($options['email_submission_rejected']) ? 'checked' : '';
    echo "<input type='checkbox' name='sp_notification_options[email_submission_rejected]' value='1' $checked />";
}

// Field callbacks (WhatsApp)
function sp_whatsapp_enable_callback() {
    $options = get_option('sp_notification_options');
    $checked = isset($options['whatsapp_enable']) ? 'checked' : '';
    echo "<input type='checkbox' name='sp_notification_options[whatsapp_enable]' value='1' $checked /> Enable all WhatsApp buttons globally";
}

function sp_whatsapp_vendor_approved_callback() {
    $options = get_option('sp_notification_options');
    $checked = isset($options['whatsapp_vendor_approved']) ? 'checked' : '';
    echo "<input type='checkbox' name='sp_notification_options[whatsapp_vendor_approved]' value='1' $checked />";
}

function sp_whatsapp_vendor_rejected_callback() {
    $options = get_option('sp_notification_options');
    $checked = isset($options['whatsapp_vendor_rejected']) ? 'checked' : '';
    echo "<input type='checkbox' name='sp_notification_options[whatsapp_vendor_rejected]' value='1' $checked />";
}

function sp_whatsapp_submission_approved_callback() {
    $options = get_option('sp_notification_options');
    $checked = isset($options['whatsapp_submission_approved']) ? 'checked' : '';
    echo "<input type='checkbox' name='sp_notification_options[whatsapp_submission_approved]' value='1' $checked />";
}

function sp_whatsapp_submission_rejected_callback() {
    $options = get_option('sp_notification_options');
    $checked = isset($options['whatsapp_submission_rejected']) ? 'checked' : '';
    echo "<input type='checkbox' name='sp_notification_options[whatsapp_submission_rejected]' value='1' $checked />";
}
