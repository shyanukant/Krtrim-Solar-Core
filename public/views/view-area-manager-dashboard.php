<?php
/**
 * Shortcode and logic for the Area Manager frontend dashboard.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sp_area_manager_dashboard_shortcode() {
    // Security check: Must be a logged-in Area Manager
    if (!is_user_logged_in() || !in_array('area_manager', (array)wp_get_current_user()->roles)) {
        return '<div class="notice notice-error"><p>Access Denied. This page is for Area Managers only.</p></div>';
    }

    // Enqueue styles and scripts
    wp_enqueue_style('unified-dashboard-styles');
    wp_enqueue_script('unified-dashboard-scripts');
    wp_localize_script('unified-dashboard-scripts', 'sp_area_dashboard_vars', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'create_project_nonce' => wp_create_nonce('sp_create_project_nonce'),
        'project_details_nonce' => wp_create_nonce('sp_project_details_nonce'),
        'review_submission_nonce' => wp_create_nonce('sp_review_nonce'), // Reuse existing nonce
        'award_bid_nonce' => wp_create_nonce('award_bid_nonce'), // Reuse existing nonce
    ]);
    
    $user = wp_get_current_user();

    ob_start();
    ?>
    <div id="modernDashboard" class="modern-solar-dashboard area-manager-dashboard">
        <!-- Sidebar -->
        <div class="dashboard-sidebar">
            <div class="sidebar-brand">
                <?php
                if ( has_custom_logo() ) {
                    echo get_custom_logo();
                } else {
                    echo '<span class="logo">☀️</span>';
                }
                ?>
                <span><?php echo get_bloginfo('name'); ?></span>
            </div>
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" data-section="dashboard"><span>🏠</span> Dashboard</a>
                <a href="#" class="nav-item" data-section="projects"><span>🏗️</span> Projects</a>
                <a href="#" class="nav-item" data-section="create-project"><span>➕</span> Create Project</a>
            </nav>
            <div class="sidebar-profile">
                <div class="profile-info">
                    <h4><?php echo esc_html($user->display_name); ?></h4>
                    <p>Area Manager</p>
                </div>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="logout-btn" title="Logout">🚪</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <header class="dashboard-header-top">
                <h1 id="section-title">Dashboard</h1>
            </header>
            <main class="dashboard-content">
                <!-- Dashboard Section -->
                <section id="dashboard-section" class="section-content">
                    <h2>Welcome, <?php echo esc_html($user->display_name); ?>!</h2>
                    <p>This is your dashboard. Use the menu to manage your projects.</p>
                    <!-- Stats will be added here in a later phase -->
                </section>

                <!-- Projects List Section -->
                <section id="projects-section" class="section-content" style="display:none;">
                    <h2>Your Projects</h2>
                    <div id="area-project-list-container">
                        <p>Loading projects...</p>
                    </div>
                </section>

                <!-- Project Detail Section -->
                <section id="project-detail-section" class="section-content" style="display:none;">
                    <button class="btn btn-secondary" id="back-to-projects-list">← Back to Projects</button>
                    <div class="card project-detail-card">
                        <h2 id="project-detail-title"></h2>
                        <div class="detail-grid" id="project-detail-meta">
                            <!-- Project meta will be loaded here -->
                        </div>

                        <div class="tabs-wrapper">
                            <div class="tabs-nav">
                                <button class="tab-button active" data-tab="progress">Progress</button>
                                <button class="tab-button" data-tab="bids">Bids</button>
                            </div>
                            <div class="tabs-content">
                                <div id="progress-tab" class="tab-pane active">
                                    <h3>Vendor Submissions</h3>
                                    <div id="vendor-submissions-list">Loading submissions...</div>
                                </div>
                                <div id="bids-tab" class="tab-pane">
                                    <h3>Project Bids</h3>
                                    <div id="project-bids-list">Loading bids...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Create Project Section -->
                <section id="create-project-section" class="section-content" style="display:none;">
                    <div class="card">
                        <h3>Create New Solar Project</h3>
                        <form id="create-project-form">
                            <?php wp_nonce_field('sp_create_project_nonce', 'sp_create_project_nonce_field'); ?>
                            <div class="form-group">
                                <label for="project_title">Project Title</label>
                                <input type="text" id="project_title" name="project_title" required>
                            </div>
                            <div class="form-group">
                                <label for="system_size">System Size (kW)</label>
                                <input type="number" id="system_size" name="system_size" step="0.1" required>
                            </div>
                            <div class="form-group">
                                <label for="client_name">Client Full Name</label>
                                <input type="text" id="client_name" name="client_name" required>
                            </div>
                            <div class="form-group">
                                <label for="client_email">Client Email</label>
                                <input type="email" id="client_email" name="client_email" required>
                            </div>
                            <div class="form-group">
                                <label for="client_address">Client Address</label>
                                <textarea id="client_address" name="client_address" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Project</button>
                            <div id="create-project-feedback" style="margin-top:15px;"></div>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('area_manager_dashboard', 'sp_area_manager_dashboard_shortcode');