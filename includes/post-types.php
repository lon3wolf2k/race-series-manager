<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register custom post types: Event (cmt_event) and Race (cmt_race)
 */

function rsm_register_post_types() {

    // ---------------------------------------------------------------------
    // Event
    // ---------------------------------------------------------------------
    $event_labels = array(
        'name'                  => esc_html__( 'Events', 'race-series-manager' ),
        'singular_name'         => esc_html__( 'Event', 'race-series-manager' ),
        'menu_name'             => esc_html__( 'Events', 'race-series-manager' ),
        'name_admin_bar'        => esc_html__( 'Event', 'race-series-manager' ),
        'add_new'               => esc_html__( 'Add New', 'race-series-manager' ),
        'add_new_item'          => esc_html__( 'Add New Event', 'race-series-manager' ),
        'edit_item'             => esc_html__( 'Edit Event', 'race-series-manager' ),
        'new_item'              => esc_html__( 'New Event', 'race-series-manager' ),
        'view_item'             => esc_html__( 'View Event', 'race-series-manager' ),
        'view_items'            => esc_html__( 'View Events', 'race-series-manager' ),
        'search_items'          => esc_html__( 'Search Events', 'race-series-manager' ),
        'not_found'             => esc_html__( 'No events found.', 'race-series-manager' ),
        'not_found_in_trash'    => esc_html__( 'No events found in Trash.', 'race-series-manager' ),
        'all_items'             => esc_html__( 'All Events', 'race-series-manager' ),
        'archives'              => esc_html__( 'Event archives', 'race-series-manager' ),
        'attributes'            => esc_html__( 'Event attributes', 'race-series-manager' ),
        'insert_into_item'      => esc_html__( 'Insert into event', 'race-series-manager' ),
        'uploaded_to_this_item' => esc_html__( 'Uploaded to this event', 'race-series-manager' ),
        'featured_image'        => esc_html__( 'Event image', 'race-series-manager' ),
        'set_featured_image'    => esc_html__( 'Set event image', 'race-series-manager' ),
        'remove_featured_image' => esc_html__( 'Remove event image', 'race-series-manager' ),
        'use_featured_image'    => esc_html__( 'Use as event image', 'race-series-manager' ),
    );

    $event_args = array(
        'labels'             => $event_labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'events' ),
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'menu_icon'          => 'dashicons-flag',
    );

    register_post_type( 'cmt_event', $event_args );

    // ---------------------------------------------------------------------
    // Race
    // ---------------------------------------------------------------------
    $race_labels = array(
        'name'                  => esc_html__( 'Races', 'race-series-manager' ),
        'singular_name'         => esc_html__( 'Race', 'race-series-manager' ),
        'menu_name'             => esc_html__( 'Races', 'race-series-manager' ),
        'name_admin_bar'        => esc_html__( 'Race', 'race-series-manager' ),
        'add_new'               => esc_html__( 'Add New', 'race-series-manager' ),
        'add_new_item'          => esc_html__( 'Add New Race', 'race-series-manager' ),
        'edit_item'             => esc_html__( 'Edit Race', 'race-series-manager' ),
        'new_item'              => esc_html__( 'New Race', 'race-series-manager' ),
        'view_item'             => esc_html__( 'View Race', 'race-series-manager' ),
        'view_items'            => esc_html__( 'View Races', 'race-series-manager' ),
        'search_items'          => esc_html__( 'Search Races', 'race-series-manager' ),
        'not_found'             => esc_html__( 'No races found.', 'race-series-manager' ),
        'not_found_in_trash'    => esc_html__( 'No races found in Trash.', 'race-series-manager' ),
        'all_items'             => esc_html__( 'All Races', 'race-series-manager' ),
        'archives'              => esc_html__( 'Race archives', 'race-series-manager' ),
        'attributes'            => esc_html__( 'Race attributes', 'race-series-manager' ),
        'insert_into_item'      => esc_html__( 'Insert into race', 'race-series-manager' ),
        'uploaded_to_this_item' => esc_html__( 'Uploaded to this race', 'race-series-manager' ),
        'featured_image'        => esc_html__( 'Race image', 'race-series-manager' ),
        'set_featured_image'    => esc_html__( 'Set race image', 'race-series-manager' ),
        'remove_featured_image' => esc_html__( 'Remove race image', 'race-series-manager' ),
        'use_featured_image'    => esc_html__( 'Use as race image', 'race-series-manager' ),
    );

    $race_args = array(
        'labels'             => $race_labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'races' ),
        'show_in_rest'       => true,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'menu_icon'          => 'dashicons-location-alt',
    );

    register_post_type( 'cmt_race', $race_args );
}
add_action( 'init', 'rsm_register_post_types' );
