<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Provide clone actions for Events and Races.
 */

/**
 * Add a Clone link to the row actions for supported post types.
 *
 * @param array   $actions Row actions.
 * @param WP_Post $post    Current post object.
 *
 * @return array
 */
function rsm_add_clone_row_action( $actions, $post ) {
    if ( ! $post instanceof WP_Post ) {
        return $actions;
    }

    if ( ! in_array( $post->post_type, array( 'cmt_event', 'cmt_race' ), true ) ) {
        return $actions;
    }

    if ( ! current_user_can( 'edit_post', $post->ID ) ) {
        return $actions;
    }

    $url = wp_nonce_url(
        admin_url( 'admin.php?action=rsm_clone_post&post=' . $post->ID ),
        'rsm_clone_post_' . $post->ID
    );

    $actions['rsm_clone'] = sprintf(
        '<a href="%s">%s</a>',
        esc_url( $url ),
        esc_html__( 'Clone', 'race-series-manager' )
    );

    return $actions;
}
add_filter( 'post_row_actions', 'rsm_add_clone_row_action', 10, 2 );

/**
 * Handle post cloning for supported post types.
 */
function rsm_handle_clone_request() {
    if ( ! isset( $_GET['post'] ) ) {
        return;
    }

    $original_id = absint( $_GET['post'] );
    $original    = get_post( $original_id );

    if ( ! $original || ! in_array( $original->post_type, array( 'cmt_event', 'cmt_race' ), true ) ) {
        wp_die( esc_html__( 'Invalid post to clone.', 'race-series-manager' ) );
    }

    if ( ! current_user_can( 'edit_post', $original_id ) ) {
        wp_die( esc_html__( 'You are not allowed to clone this item.', 'race-series-manager' ) );
    }

    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'rsm_clone_post_' . $original_id ) ) {
        wp_die( esc_html__( 'Invalid clone request.', 'race-series-manager' ) );
    }

    $author = get_current_user_id();
    $title  = sprintf(
        /* translators: %s: Original post title. */
        esc_html__( '%s (Copy)', 'race-series-manager' ),
        $original->post_title
    );

    $clone_args = array(
        'post_author'    => $author ? $author : $original->post_author,
        'post_content'   => $original->post_content,
        'post_title'     => $title,
        'post_excerpt'   => $original->post_excerpt,
        'post_status'    => 'draft',
        'post_type'      => $original->post_type,
        'post_parent'    => $original->post_parent,
        'comment_status' => $original->comment_status,
        'ping_status'    => $original->ping_status,
    );

    $new_post_id = wp_insert_post( $clone_args, true );

    if ( is_wp_error( $new_post_id ) ) {
        wp_die( esc_html__( 'Failed to create the clone. Please try again.', 'race-series-manager' ) );
    }

    rsm_clone_post_meta( $original_id, $new_post_id );
    rsm_clone_post_terms( $original, $new_post_id );

    wp_safe_redirect(
        add_query_arg(
            array(
                'post'        => $new_post_id,
                'action'      => 'edit',
                'rsm_cloned'  => 1,
                'rsm_source'  => $original_id,
            ),
            admin_url( 'post.php' )
        )
    );
    exit;
}
add_action( 'admin_action_rsm_clone_post', 'rsm_handle_clone_request' );

/**
 * Copy post meta from the original post to the clone.
 *
 * @param int $original_id Original post ID.
 * @param int $new_post_id New post ID.
 */
function rsm_clone_post_meta( $original_id, $new_post_id ) {
    $meta = get_post_meta( $original_id );

    if ( empty( $meta ) ) {
        return;
    }

    foreach ( $meta as $key => $values ) {
        if ( '_wp_old_slug' === $key ) {
            continue;
        }

        foreach ( $values as $value ) {
            add_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );
        }
    }
}

/**
 * Copy taxonomy term assignments to the cloned post.
 *
 * @param WP_Post $original    Original post object.
 * @param int     $new_post_id Newly created post ID.
 */
function rsm_clone_post_terms( $original, $new_post_id ) {
    $taxonomies = get_object_taxonomies( $original->post_type );

    if ( empty( $taxonomies ) ) {
        return;
    }

    foreach ( $taxonomies as $taxonomy ) {
        $terms = wp_get_object_terms( $original->ID, $taxonomy, array( 'fields' => 'ids' ) );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            continue;
        }

        wp_set_object_terms( $new_post_id, $terms, $taxonomy );
    }
}

/**
 * Surface a success notice after cloning.
 */
function rsm_clone_admin_notice() {
    if ( ! isset( $_GET['rsm_cloned'], $_GET['post'], $_GET['rsm_source'] ) ) {
        return;
    }

    $post_id   = absint( $_GET['post'] );
    $source_id = absint( $_GET['rsm_source'] );

    if ( ! $post_id ) {
        return;
    }

    $post_type = get_post_type_object( get_post_type( $post_id ) );

    if ( ! $post_type ) {
        return;
    }

    $label = isset( $post_type->labels->singular_name ) ? $post_type->labels->singular_name : $post_type->label;

    printf(
        '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
        esc_html(
            sprintf(
                /* translators: 1: Post type label, 2: cloned post ID, 3: source post ID. */
                __( '%1$s cloned. New draft ID: %2$d (from #%3$d).', 'race-series-manager' ),
                $label,
                $post_id,
                $source_id
            )
        )
    );
}
add_action( 'admin_notices', 'rsm_clone_admin_notice' );
