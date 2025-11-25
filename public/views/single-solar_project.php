<?php
/**
 * The template for displaying single Solar Project posts
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

    <?php
    while ( have_posts() ) :
        the_post();
        $project_id = get_the_ID();
        $current_user = wp_get_current_user();
        $is_vendor = in_array('solar_vendor', (array)$current_user->roles);

        // --- Get Project Data ---
        $client_amount = get_post_meta($project_id, '_client_amount', true) ?: 'N/A';
        $vendor_paid_amount = get_post_meta($project_id, '_vendor_paid_amount', true) ?: 'N/A';
        $location = get_post_meta($project_id, '_installation_location', true) ?: 'N/A';
        $cities = get_the_terms($project_id, 'project_city');
        $city_name = !empty($cities) ? $cities[0]->name : 'N/A';

        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('solar-project-single'); ?>>
            <header class="entry-header">
                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            </header>

            <div class="entry-content">
                <div class="project-details-grid">
                    <div class="detail-item"><strong>Budget:</strong> ₹<?php echo is_numeric($client_amount) ? number_format($client_amount) : esc_html($client_amount); ?></div>
                    <div class="detail-item"><strong>City:</strong> <?php echo esc_html($city_name); ?></div>
                    <div class="detail-item"><strong>Location:</strong> <?php echo esc_html($location); ?></div>
                </div>

                <?php the_content(); ?>

                <hr>

                <!-- Bidding Section -->
                <section id="bidding-section" class="bidding-section">
                    <h2>Bids</h2>

                    <!-- Open Bids List -->
                    <div id="open-bids-list" class="open-bids-list">
                        <h3>Public Bids</h3>
                        <?php
                        global $wpdb;
                        $bids_table = $wpdb->prefix . 'project_bids';
                        $open_bids = $wpdb->get_results($wpdb->prepare(
                            "SELECT b.*, u.display_name FROM {$bids_table} b JOIN {$wpdb->users} u ON b.vendor_id = u.ID WHERE b.project_id = %d AND b.bid_type = 'open' ORDER BY b.created_at DESC",
                            $project_id
                        ));

                        if ($open_bids) {
                            foreach ($open_bids as $bid) {
                                ?>
                                <div class="bid-card">
                                    <div class="bid-amount">₹<?php echo number_format($bid->bid_amount); ?></div>
                                    <div class="bid-vendor">by <?php echo esc_html($bid->display_name); ?></div>
                                    <div class="bid-details"><?php echo esc_html($bid->bid_details); ?></div>
                                    <div class="bid-time"><?php echo human_time_diff(strtotime($bid->created_at), current_time('timestamp')) . ' ago'; ?></div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p>No public bids have been placed yet.</p>';
                        }
                        ?>
                    </div>

                    <!-- Place Bid Form -->
                    <div class="place-bid-form-wrapper">
                        <h3>Place Your Bid</h3>
                        <?php if (is_user_logged_in()): ?>
                            <?php if ($is_vendor): ?>
                                <form id="place-bid-form" class="place-bid-form">
                                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                    <?php wp_nonce_field('submit_bid_nonce_' . $project_id, 'submit_bid_nonce'); ?>

                                    <div class="form-group">
                                        <label for="bid_amount">Your Bid Amount (₹)</label>
                                        <input type="number" id="bid_amount" name="bid_amount" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="bid_details">Details (Optional)</label>
                                        <textarea id="bid_details" name="bid_details" rows="3"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Bid Type</label>
                                        <div class="radio-group">
                                            <label><input type="radio" name="bid_type" value="open" checked> Open Bid (Visible to everyone)</label>
                                            <label><input type="radio" name="bid_type" value="hidden"> Hidden Bid (Visible to managers only)</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Submit Bid</button>
                                    <div id="bid-form-feedback" style="display:none; margin-top:15px;"></div>
                                </form>
                            <?php else: ?>
                                <p>Only registered vendors are able to place bids on projects.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>You must be <a href="<?php echo wp_login_url(get_permalink()); ?>">logged in</a> as a vendor to place a bid. <a href="<?php echo wp_registration_url(); ?>">Register here</a>.</p>
                        <?php endif; ?>
                    </div>
                </section>

            </div><!-- .entry-content -->
        </article><!-- #post-<?php the_ID(); ?> -->
    <?php
    endwhile; // End of the loop.
    ?>

    </main><!-- #main -->
</div><!-- #primary -->

<style>
/* Basic Single Project Styles */
.solar-project-single .entry-content {
    max-width: 800px;
    margin: 0 auto;
}
.project-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 2em;
}
.detail-item {
    font-size: 16px;
}
.bidding-section {
    margin-top: 2em;
    padding-top: 2em;
    border-top: 1px solid #eee;
}
.open-bids-list {
    margin-bottom: 2em;
}
.bid-card {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-left: 4px solid #667eea;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.bid-amount {
    font-size: 20px;
    font-weight: 700;
    color: #333;
}
.bid-vendor {
    font-size: 14px;
    color: #777;
    margin-bottom: 10px;
}
.bid-details {
    font-size: 15px;
    color: #555;
    margin-bottom: 10px;
}
.bid-time {
    font-size: 12px;
    color: #999;
    text-align: right;
}
.place-bid-form-wrapper {
    background: #f9f9f9;
    padding: 30px;
    border-radius: 8px;
}
.place-bid-form .form-group {
    margin-bottom: 1.5em;
}
.place-bid-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5em;
}
.place-bid-form input[type="number"],
.place-bid-form textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
}
.place-bid-form .radio-group label {
    font-weight: normal;
    display: block;
    margin-bottom: 0.5em;
}
#bid-form-feedback {
    padding: 15px;
    border-radius: 8px;
}
#bid-form-feedback.success {
    background-color: #d4edda;
    color: #155724;
}
#bid-form-feedback.error {
    background-color: #f8d7da;
    color: #721c24;
}
</style>

<?php
get_sidebar();
get_footer();
