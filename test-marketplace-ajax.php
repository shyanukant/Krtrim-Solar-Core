<?php
/**
 * Direct test of marketplace AJAX endpoint
 * Visit: http://your-site.com/wp-content/plugins/kritim-solar-core/test-marketplace-ajax.php
 */

// Load WordPress
require_once('../../../wp-load.php');

// Simulate AJAX request
$_POST['action'] = 'filter_marketplace_projects';
$_POST['state'] = '';
$_POST['city'] = '';

// Call the handler directly
do_action('wp_ajax_nopriv_filter_marketplace_projects');
do_action('wp_ajax_filter_marketplace_projects');
