<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Use our custom template for single cmt_race posts.
 */
function rsm_single_race_template( $single_template ) {
    if ( is_singular( 'cmt_race' ) ) {
        $plugin_template = RSM_PLUGIN_DIR . 'templates/single-cmt_race.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }

    return $single_template;
}
add_filter( 'single_template', 'rsm_single_race_template' );

/**
 * Enqueue frontend styles for race pages.
 */
function rsm_enqueue_race_styles() {
    if ( is_singular( 'cmt_race' ) ) {
        if ( function_exists( 'rsm_enqueue_style_bundle' ) ) {
            rsm_enqueue_style_bundle();
        }
    }
}
add_action( 'wp_enqueue_scripts', 'rsm_enqueue_race_styles' );
