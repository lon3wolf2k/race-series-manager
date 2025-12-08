<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add ID and Order columns for RS Manager post types and make them sortable.
 */
function rsm_register_admin_columns( $columns ) {
    $new_columns = array();

    foreach ( $columns as $key => $label ) {
        // Insert ID right after the checkbox column.
        if ( 'cb' === $key ) {
            $new_columns['cb'] = $label;
            $new_columns['rsm_id'] = esc_html__( 'ID', 'race-series-manager' );
            continue;
        }

        // Insert Order right after the title column for better visibility.
        $new_columns[ $key ] = $label;
        if ( 'title' === $key ) {
            $new_columns['rsm_order'] = esc_html__( 'Order', 'race-series-manager' );
        }
    }

    return $new_columns;
}

function rsm_render_admin_columns( $column, $post_id ) {
    if ( 'rsm_id' === $column ) {
        echo esc_html( $post_id );
        return;
    }

    if ( 'rsm_order' === $column ) {
        $menu_order = (int) get_post_field( 'menu_order', $post_id );
        echo esc_html( $menu_order );
        return;
    }
}

function rsm_sortable_admin_columns( $columns ) {
    $columns['rsm_id']    = 'ID';
    $columns['rsm_order'] = 'menu_order';

    return $columns;
}

function rsm_admin_columns_query( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $screen = get_current_screen();
    if ( empty( $screen->post_type ) || ! in_array( $screen->post_type, array( 'cmt_event', 'cmt_race', 'cmt_result' ), true ) ) {
        return;
    }

    $orderby = $query->get( 'orderby' );
    if ( 'menu_order' === $orderby ) {
        $query->set( 'orderby', 'menu_order title' );
    }
}

add_filter( 'manage_cmt_event_posts_columns', 'rsm_register_admin_columns' );
add_filter( 'manage_cmt_race_posts_columns', 'rsm_register_admin_columns' );
add_filter( 'manage_cmt_result_posts_columns', 'rsm_register_admin_columns' );

add_action( 'manage_cmt_event_posts_custom_column', 'rsm_render_admin_columns', 10, 2 );
add_action( 'manage_cmt_race_posts_custom_column', 'rsm_render_admin_columns', 10, 2 );
add_action( 'manage_cmt_result_posts_custom_column', 'rsm_render_admin_columns', 10, 2 );

add_filter( 'manage_edit-cmt_event_sortable_columns', 'rsm_sortable_admin_columns' );
add_filter( 'manage_edit-cmt_race_sortable_columns', 'rsm_sortable_admin_columns' );
add_filter( 'manage_edit-cmt_result_sortable_columns', 'rsm_sortable_admin_columns' );

add_action( 'pre_get_posts', 'rsm_admin_columns_query' );
