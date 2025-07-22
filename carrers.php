<?php
/**
 * Plugin Name: Career Job Listing Plugin
 * Description: A Plugin for listing Jobs
 * Version: 1.0.1
 * Author: Athul Thomas
 */


function career_register_job_post_type() {
    $labels = array(
        'name' => 'Jobs',
        'singular_name' => 'Job',
        'add_new' => 'Add New Job',
        'add_new_item' => 'Add New Job',
        'edit_item' => 'Edit Job',
        'new_item' => 'New Job',
        'view_item' => 'View Job',
        'search_items' => 'Search Jobs',
        'not_found' => 'No jobs found',
        'not_found_in_trash' => ' Oooppsss !! No jobs found in Trash',
        'all_items' => 'All Jobs',
        'menu_name' => 'Jobs',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'jobs'),
        'show_in_rest' => false,
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-businessperson',
    );

    register_post_type('job', $args);
}
add_action('init', 'career_register_job_post_type');



function career_add_job_metabox() {
    add_meta_box(
        'career_job_meta',
        'Job Details',
        'career_job_meta_callback',
        'job',
        'normal',
        'default'
    );
}

add_action('add_meta_boxes', 'career_add_job_metabox');


function career_job_meta_callback($post) {
    $company = get_post_meta($post->ID, '_career_company', true);
    $show_job = get_post_meta($post->ID, '_career_show_job', true);
    ?>
    <p>
        <label for="career_company"><strong>Company Name:</strong></label><br>
        <input type="text" name="career_company" id="career_company" value="<?php echo esc_attr($company); ?>" style="width:100%;">
    </p>
    <p>
        <label><input type="checkbox" name="career_show_job" value="yes" <?php checked($show_job, 'yes'); ?>> Show this company on the site</label>
    </p>
    <?php
}


function career_save_job_meta($post_id) {
    // Save company name
    if (isset($_POST['career_company'])) {
        update_post_meta($post_id, '_career_company', sanitize_text_field($_POST['career_company']));
    }

    // Save checkbox value
    if (isset($_POST['career_show_job']) && $_POST['career_show_job'] === 'yes') {
        update_post_meta($post_id, '_career_show_job', 'yes');
    } else {
        delete_post_meta($post_id, '_career_show_job'); // If not checked, remove it
    }
}
add_action('save_post', 'career_save_job_meta');

function career_job_listing_shortcode() {
    $args = array(
        'post_type' => 'job',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);
    $output = '';

    if ($query->have_posts()) {
        $output .= '<div class="job-listings">';
        while ($query->have_posts()) {
            $query->the_post();

            $show = get_post_meta(get_the_ID(), '_career_show_job', true);
            $company = get_post_meta(get_the_ID(), '_career_company', true);
            $output .= '<div class="job-item">';
                $output .= '<h2><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';

            if ($show === 'yes') {
                
                if ($company) {
                    $output .= '<p><strong>Company:</strong> ' . esc_html($company) . '</p>';
                }
                $output .= '<div>' . get_the_content() . '</div>';
                $output .= '</div><hr>';
            }
        }
        wp_reset_postdata();
        $output .= '</div>';
    } else {
        $output .= '<p>No jobs found.</p>';
    }

    return $output;
}
add_shortcode('job_listings', 'career_job_listing_shortcode');