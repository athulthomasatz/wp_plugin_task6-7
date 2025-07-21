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

            $output .= '<div class="job-item">';
            $output .= '<h2><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';

            $output .= '<div>' . get_the_content() . '</div>';
            $output .= '</div>';
        }
        $output .= '</div>';
        wp_reset_postdata();
    } else {
        $output .= '<p>No jobs found.</p>';
    }



    return $output;
}
add_shortcode('job_listings', 'career_job_listing_shortcode');
