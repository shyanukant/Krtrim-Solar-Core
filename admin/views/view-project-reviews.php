<?php
/**
 * Creates a custom admin page for reviewing vendor submissions on projects.
 * Based on user-provided code, refactored to use AJAX.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 2. Render the page content
function sp_render_project_reviews_page() {
    global $wpdb;
    
    // Enqueue admin scripts
    wp_enqueue_script('sp-admin-scripts');

    $current_user = wp_get_current_user();
    $is_area_manager = in_array('area_manager', $current_user->roles);
    
    $project_id = isset($_GET['project']) ? intval($_GET['project']) : 0;
    $steps_table = $wpdb->prefix . 'solar_process_steps';
    
    ?>
    <div class="wrap">
        <style>
            /* Styles from user, slightly tidied */
            .review-container { max-width: 1200px; }
            .review-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
            .review-header h1 { margin: 0; font-size: 24px; }
            .project-list, .project-detail { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .project-item { padding: 15px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
            .project-item:last-child { border-bottom: none; }
            .project-item:hover { background: #f8f9fa; }
            .project-name { font-size: 16px; font-weight: 600; }
            .project-meta { font-size: 13px; color: #666; }
            .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
            .badge-pending { background: #ffc107; color: #000; }
            .badge-approved { background: #28a745; color: white; }
            .btn-view { background: #007bff; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; }
            .back-btn { background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; margin-bottom: 20px; }
            .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
            .detail-card { background: #f8f9fa; padding: 20px; border-radius: 8px; }
            .detail-label { font-size: 12px; color: #666; text-transform: uppercase; font-weight: 600; }
            .detail-value { font-size: 16px; font-weight: 600; color: #333; margin-top: 8px; }
            .submission-toggle { border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 12px; }
            .toggle-header { padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
            .toggle-left { display: flex; align-items: center; gap: 15px; }
            .toggle-icon { transition: transform 0.3s; }
            .toggle-icon.open { transform: rotate(90deg); }
            .toggle-title { font-size: 15px; font-weight: 600; }
            .toggle-meta { font-size: 12px; color: #666; }
            .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
            .status-badge.pending { background: #ffc107; color: #000; }
            .status-badge.approved { background: #28a745; color: white; }
            .status-badge.rejected { background: #dc3545; color: white; }
            .toggle-content { display: none; padding: 20px; border-top: 1px solid #e0e0e0; }
            .submission-image { max-width: 400px; border-radius: 6px; margin: 15px 0; }
            .submission-comment { background: #f0f7ff; padding: 12px; border-left: 4px solid #007bff; border-radius: 4px; margin: 10px 0; }
            .review-form { margin-top: 15px; }
            .review-input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; }
            .btn-approve, .btn-reject { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
            .btn-approve { background: #28a745; color: white; }
            .btn-reject { background: #dc3545; color: white; }
        </style>
        
        <div class="review-container">
            <?php if (!$project_id) : // PROJECT LIST VIEW ?>
                <div class="review-header"><h1>Project Reviews</h1></div>
                <div class="project-list">
                    <?php
                    $query_args = ['post_type' => 'solar_project', 'posts_per_page' => -1, 'post_status' => 'publish'];
                    if ($is_area_manager) {
                        $query_args['author'] = $current_user->ID;
                    }
                    $projects = get_posts($query_args);
                    
                    if (empty($projects)) {
                        echo '<p>No projects found.</p>';
                    } else {
                        foreach ($projects as $proj) {
                            $pending_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$steps_table} WHERE project_id = %d AND admin_status = 'pending'", $proj->ID
                            ));
                            ?>
                            <div class="project-item">
                                <div class="project-info">
                                    <div class="project-name"><?php echo esc_html($proj->post_title); ?></div>
                                    <?php if ($pending_count > 0): ?>
                                        <div><span class="badge badge-pending"><?php echo $pending_count; ?> pending submission(s)</span></div>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo admin_url('admin.php?page=project-reviews&project=' . $proj->ID); ?>" class="btn-view">Review</a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            <?php else : // PROJECT DETAIL VIEW ?>
                <?php
                $project = get_post($project_id);
                if ($is_area_manager && $project->post_author != $current_user->ID) {
                    wp_die('You do not have permission to view this project.');
                }
                ?>
                <button onclick="window.location.href='<?php echo admin_url('admin.php?page=project-reviews'); ?>'" class="back-btn">&larr; Back to Projects</button>
                <div class="project-detail">
                    <h2><?php echo esc_html($project->post_title); ?></h2>
                    <div class="submissions-section">
                        <h3>Vendor Submissions</h3>
                        <?php
                        $submissions = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$steps_table} WHERE project_id = %d AND image_url IS NOT NULL ORDER BY step_number ASC", $project_id));
                        if (empty($submissions)) {
                            echo '<p>No vendor submissions yet.</p>';
                        } else {
                            foreach ($submissions as $submission) {
                                ?>
                                <div class="submission-toggle <?php echo esc_attr($submission->admin_status); ?>">
                                    <div class="toggle-header" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block';">
                                        <div class="toggle-left">
                                            <span class="toggle-icon">&rtrif;</span>
                                            <div class="toggle-title">Step <?php echo $submission->step_number; ?>: <?php echo esc_html($submission->step_name); ?></div>
                                        </div>
                                        <span class="status-badge <?php echo esc_attr($submission->admin_status); ?>"><?php echo $submission->admin_status === 'pending' ? 'Under Review' : ucfirst($submission->admin_status); ?></span>
                                    </div>
                                    <div class="toggle-content">
                                        <img src="<?php echo esc_url($submission->image_url); ?>" class="submission-image" alt="Submission Image">
                                        <div class="submission-comment"><strong>Vendor Comment:</strong> <?php echo esc_html($submission->vendor_comment); ?></div>
                                        
                                        <?php if ($submission->admin_status === 'pending') : ?>
                                            <div class="review-form">
                                                <input type="text" class="review-input" placeholder="Add approval/rejection reason...">
                                                <button type="button" class="btn-approve review-btn" data-decision="approved" data-step-id="<?php echo $submission->id; ?>">Approve</button>
                                                <button type="button" class="btn-reject review-btn" data-decision="rejected" data-step-id="<?php echo $submission->id; ?>">Reject</button>
                                            </div>
                                        <?php else: ?>
                                            <div class="submission-comment"><strong>Admin Comment:</strong> <?php echo esc_html($submission->admin_comment); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php wp_nonce_field('sp_review_nonce', 'sp_review_nonce_field'); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
