<?php
/**
 * Admin settings page for Vendor Registration
 */

// Render the settings page content
function sp_render_vendor_settings_page() {
    ?>
    <div class="wrap">
        <h1>Vendor Registration Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('sp_vendor_settings_group');
            do_settings_sections('vendor-registration-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings, sections, and fields
function sp_register_vendor_settings() {
    // Register the main setting group
    register_setting('sp_vendor_settings_group', 'sp_vendor_options');

    // Razorpay Section
    add_settings_section(
        'sp_razorpay_section',
        'Razorpay API Settings',
        'sp_razorpay_section_callback',
        'vendor-registration-settings'
    );

    add_settings_field(
        'razorpay_mode',
        'Razorpay Mode',
        'sp_razorpay_mode_callback',
        'vendor-registration-settings',
        'sp_razorpay_section'
    );

    add_settings_field(
        'razorpay_test_key_id',
        'Test Key ID',
        'sp_razorpay_test_key_id_callback',
        'vendor-registration-settings',
        'sp_razorpay_section'
    );

    add_settings_field(
        'razorpay_test_key_secret',
        'Test Key Secret',
        'sp_razorpay_test_key_secret_callback',
        'vendor-registration-settings',
        'sp_razorpay_section'
    );

    add_settings_field(
        'razorpay_live_key_id',
        'Live Key ID',
        'sp_razorpay_live_key_id_callback',
        'vendor-registration-settings',
        'sp_razorpay_section'
    );

    add_settings_field(
        'razorpay_live_key_secret',
        'Live Key Secret',
        'sp_razorpay_live_key_secret_callback',
        'vendor-registration-settings',
        'sp_razorpay_section'
    );

    // Fee Section
    add_settings_section(
        'sp_fee_section',
        'Coverage Fee Settings',
        'sp_fee_section_callback',
        'vendor-registration-settings'
    );

    add_settings_field(
        'per_state_fee',
        'Per-State Fee (₹)',
        'sp_per_state_fee_callback',
        'vendor-registration-settings',
        'sp_fee_section'
    );

    add_settings_field(
        'per_city_fee',
        'Per-City Fee (₹)',
        'sp_per_city_fee_callback',
        'vendor-registration-settings',
        'sp_fee_section'
    );
}
add_action('admin_init', 'sp_register_vendor_settings');

// Section callbacks
function sp_razorpay_section_callback() {
    echo 'Configure your Razorpay API credentials for test and live environments.';
}

function sp_fee_section_callback() {
    echo 'Set the fees for state and city coverage.';
}

// Field callbacks
function sp_razorpay_mode_callback() {
    $options = get_option('sp_vendor_options');
    $mode = isset($options['razorpay_mode']) ? $options['razorpay_mode'] : 'test';
    ?>
    <label><input type="radio" name="sp_vendor_options[razorpay_mode]" value="test" <?php checked($mode, 'test'); ?>> Test</label>
    <br>
    <label><input type="radio" name="sp_vendor_options[razorpay_mode]" value="live" <?php checked($mode, 'live'); ?>> Live</label>
    <?php
}

function sp_razorpay_test_key_id_callback() {
    $options = get_option('sp_vendor_options');
    $key_id = isset($options['razorpay_test_key_id']) ? esc_attr($options['razorpay_test_key_id']) : '';
    echo "<input type='text' name='sp_vendor_options[razorpay_test_key_id]' value='$key_id' size='50' />";
}

function sp_razorpay_test_key_secret_callback() {
    $options = get_option('sp_vendor_options');
    $key_secret = isset($options['razorpay_test_key_secret']) ? esc_attr($options['razorpay_test_key_secret']) : '';
    echo "<input type='password' name='sp_vendor_options[razorpay_test_key_secret]' value='$key_secret' size='50' />";
}

function sp_razorpay_live_key_id_callback() {
    $options = get_option('sp_vendor_options');
    $key_id = isset($options['razorpay_live_key_id']) ? esc_attr($options['razorpay_live_key_id']) : '';
    echo "<input type='text' name='sp_vendor_options[razorpay_live_key_id]' value='$key_id' size='50' />";
}

function sp_razorpay_live_key_secret_callback() {
    $options = get_option('sp_vendor_options');
    $key_secret = isset($options['razorpay_live_key_secret']) ? esc_attr($options['razorpay_live_key_secret']) : '';
    echo "<input type='password' name='sp_vendor_options[razorpay_live_key_secret]' value='$key_secret' size='50' />";
}

function sp_per_state_fee_callback() {
    $options = get_option('sp_vendor_options');
    $state_fee = isset($options['per_state_fee']) ? esc_attr($options['per_state_fee']) : '500';
    echo "<input type='number' name='sp_vendor_options[per_state_fee]' value='$state_fee' />";
}

function sp_per_city_fee_callback() {
    $options = get_option('sp_vendor_options');
    $city_fee = isset($options['per_city_fee']) ? esc_attr($options['per_city_fee']) : '100';
    echo "<input type='number' name='sp_vendor_options[per_city_fee]' value='$city_fee' />";
}
