<?php
/**
 * Handles the creation of admin menus and settings pages.
 */
class SP_Admin_Menus {

    /**
     * Constructor. Hooks into WordPress.
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menus' ] );
    }

    /**
     * Register all admin menus and sub-menus.
     */
    public function register_menus() {
        // Main Vendor Approval Page
        add_menu_page(
            'Vendor Approval',
            'Vendor Approval',
            'manage_options',
            'vendor-approval',
            'sp_render_vendor_approval_page',
            'dashicons-businessperson',
            25
        );

        // Team Analysis Page
        add_menu_page(
            'Team Analysis',
            'Team Analysis',
            'manage_options',
            'team-analysis',
            'sp_render_team_analysis_page',
            'dashicons-chart-line',
            26
        );

        add_submenu_page(
            'team-analysis',
            'Leaderboard',
            'Leaderboard',
            'manage_options',
            'team-analysis',
            'sp_render_team_analysis_page'
        );

        // Project Reviews Page
        add_menu_page(
            'Project Reviews',
            'Project Reviews',
            'edit_posts',
            'project-reviews',
            'sp_render_project_reviews_page',
            'dashicons-visibility',
            27
        );

        // Vendor Registration Settings Page (under Settings)
        add_options_page(
            'Vendor Registration Settings',
            'Vendor Registration',
            'manage_options',
            'vendor-registration-settings',
            'sp_render_vendor_settings_page'
        );

        // Notification Settings Page (under Settings)
        add_options_page(
            'Notification Settings',
            'Notifications',
            'manage_options',
            'notification-settings',
            'sp_render_notification_settings_page'
        );
    }
}
