<?php
/**
 * Plugin Name: Solar Project Marketplace
 * Description: Displays a filterable list of solar projects for vendors to bid on.
 * Version: 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sp_project_marketplace_shortcode() {
    // Enqueue styles
    wp_enqueue_style( 'unified-dashboard-styles' );

    $json_file = plugin_dir_path( __FILE__ ) . '../../assets/data/indian-states-cities.json';
    $states_cities = json_decode( file_get_contents( $json_file ), true );
    $locations = [];
    if (isset($states_cities['states'])) {
        foreach ($states_cities['states'] as $state) {
            $locations[$state['state']] = $state['districts'];
        }
    }

    ob_start();
    ?>
    <div class="project-marketplace-wrapper">
        
        <!-- Filters -->
        <div class="marketplace-filters">
            <h2>Find Projects</h2>
            
            <div class="filter-group">
                <label for="state-filter">State</label>
                <select id="state-filter" name="state-filter">
                    <option value="">All States</option>
                    <?php foreach ( $locations as $state => $cities ) : ?>
                        <option value="<?php echo esc_attr( $state ); ?>"><?php echo esc_html( $state ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="city-filter">City</label>
                <select id="city-filter" name="city-filter">
                    <option value="">All Cities</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="budget-filter">Max Budget (₹)</label>
                <input type="number" id="budget-filter" name="budget-filter" placeholder="e.g., 500000">
            </div>

            <button id="apply-filters-btn" class="btn btn-primary">Apply Filters</button>
        </div>

        <!-- Project Listings -->
        <div id="project-listings-container" class="project-listings">
            <div class="loading-spinner" style="display: none;">Loading...</div>
            <?php // AJAX will populate this section ?>
        </div>

    </div>
    <script>
        jQuery(document).ready(function($) {
            var locations = <?php echo json_encode($locations); ?>;

            $('#state-filter').on('change', function() {
                var state = $(this).val();
                var cityDropdown = $('#city-filter');
                cityDropdown.empty().append('<option value="">All Cities</option>');

                if (locations[state]) {
                    $.each(locations[state], function(index, city) {
                        cityDropdown.append($('<option></option>').attr('value', city).text(city));
                    });
                }
            });

            function loadProjects() {
                $('#project-listings-container').html('<div class="loading-spinner">Loading...</div>');
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: {
                        action: 'filter_marketplace_projects',
                        state: $('#state-filter').val(),
                        city: $('#city-filter').val(),
                        budget: $('#budget-filter').val(),
                        nonce: '<?php echo wp_create_nonce("filter_projects_nonce"); ?>',
                    },
                    success: function(response) {
                        if (response.success) {
                            let html = '';
                            if (response.data.projects.length > 0) {
                                response.data.projects.forEach(project => {
                                    html += `
                                        <div class="project-card">
                                            <h3>${project.title}</h3>
                                            <p><strong>Location:</strong> ${project.location}</p>
                                            <p><strong>Budget:</strong> ₹${project.budget}</p>
                                            <a href="${project.link}" class="btn btn-primary">View Project</a>
                                        </div>
                                    `;
                                });
                            } else {
                                html = '<p>No projects found matching your criteria.</p>';
                            }
                            $('#project-listings-container').html(html);
                        } else {
                            $('#project-listings-container').html(`<p class="text-danger">Error: ${response.data.message}</p>`);
                        }
                    }
                });
            }

            $('#apply-filters-btn').on('click', loadProjects);

            // Load all projects on page load
            loadProjects();
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'solar_project_marketplace', 'sp_project_marketplace_shortcode' );
