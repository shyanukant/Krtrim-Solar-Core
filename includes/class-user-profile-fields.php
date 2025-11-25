<?php

class SP_User_Profile_Fields {

    public function __construct() {
        add_action( 'show_user_profile', [ $this, 'add_custom_user_profile_fields' ] );
        add_action( 'edit_user_profile', [ $this, 'add_custom_user_profile_fields' ] );
        add_action( 'personal_options_update', [ $this, 'save_custom_user_profile_fields' ] );
        add_action( 'edit_user_profile_update', [ $this, 'save_custom_user_profile_fields' ] );
    }

    public function add_custom_user_profile_fields( $user ) {
        if ( ! in_array( 'area_manager', (array) $user->roles ) ) {
            return;
        }

        $current_user = wp_get_current_user();
        $can_edit = in_array( 'administrator', (array) $current_user->roles ) || in_array( 'manager', (array) $current_user->roles );
        
        $state = get_user_meta( $user->ID, 'state', true );
        $city  = get_user_meta( $user->ID, 'city', true );
        ?>
        <h3><?php _e( 'Location Information', 'krtrim-solar-core' ); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="state"><?php _e( 'State', 'krtrim-solar-core' ); ?></label></th>
                <td>
                    <input type="text" name="state" id="state" value="<?php echo esc_attr( $state ); ?>" class="regular-text" <?php if ( ! $can_edit ) echo 'disabled'; ?> />
                </td>
            </tr>
            <tr>
                <th><label for="city"><?php _e( 'City', 'krtrim-solar-core' ); ?></label></th>
                <td>
                    <input type="text" name="city" id="city" value="<?php echo esc_attr( $city ); ?>" class="regular-text" <?php if ( ! $can_edit ) echo 'disabled'; ?> />
                </td>
            </tr>
        </table>
        <?php
    }

    public function save_custom_user_profile_fields( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }

        $current_user = wp_get_current_user();
        if ( ! ( in_array( 'administrator', (array) $current_user->roles ) || in_array( 'manager', (array) $current_user->roles ) ) ) {
            return false;
        }

        if ( isset( $_POST['state'] ) ) {
            update_user_meta( $user_id, 'state', sanitize_text_field( $_POST['state'] ) );
        }

        if ( isset( $_POST['city'] ) ) {
            update_user_meta( $user_id, 'city', sanitize_text_field( $_POST['city'] ) );
        }
    }
}
