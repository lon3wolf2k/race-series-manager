<?php
/*
Plugin Name: Race Series Manager
Description: Manage trail running events and races, similar to Corfu Mountain Trail.
Version: 0.3.0
Author: Nikos
Text Domain: race-series-manager
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -----------------------------------------------------------------------------
// Constants
// -----------------------------------------------------------------------------
if ( ! defined( 'RSM_PLUGIN_DIR' ) ) {
    define( 'RSM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'RSM_PLUGIN_URL' ) ) {
    define( 'RSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// -----------------------------------------------------------------------------
// Load textdomain
// -----------------------------------------------------------------------------
function rsm_load_textdomain() {
    load_plugin_textdomain(
        'race-series-manager',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}
add_action( 'plugins_loaded', 'rsm_load_textdomain' );

// -----------------------------------------------------------------------------
// Includes
// -----------------------------------------------------------------------------
require_once RSM_PLUGIN_DIR . 'includes/post-types.php';
require_once RSM_PLUGIN_DIR . 'includes/meta-event.php';
require_once RSM_PLUGIN_DIR . 'includes/meta-race.php';
require_once RSM_PLUGIN_DIR . 'includes/shortcodes.php';
require_once RSM_PLUGIN_DIR . 'includes/pdf-booklet.php';

// -----------------------------------------------------------------------------
// Front-end assets
// -----------------------------------------------------------------------------
function rsm_enqueue_assets() {

    if (
        is_singular( 'cmt_race' ) ||
        is_singular( 'cmt_event' ) ||
        is_post_type_archive( 'cmt_race' )
    ) {
        wp_enqueue_style(
            'rsm-styles',
            RSM_PLUGIN_URL . 'assets/css/rsm-styles.css',
            array(),
            '0.3.0'
        );

        // lightbox μόνο όπου χρειάζεται, δεν πειράζει να φορτώνει και στα events
        wp_enqueue_script(
            'rsm-lightbox',
            RSM_PLUGIN_URL . 'assets/js/rsm-lightbox.js',
            array( 'jquery' ),
            '0.3.0',
            true
        );
    }
}

add_action( 'wp_enqueue_scripts', 'rsm_enqueue_assets' );

// -----------------------------------------------------------------------------
// Template loader για Event & Race
// -----------------------------------------------------------------------------
function rsm_template_loader( $template ) {

    // Custom single για Race
    if ( is_singular( 'cmt_race' ) ) {
        $race_template = RSM_PLUGIN_DIR . 'templates/single-cmt_race.php';
        if ( file_exists( $race_template ) ) {
            return $race_template;
        }
    }

    // (προαιρετικά) custom single για Event
    $event_template = RSM_PLUGIN_DIR . 'templates/single-cmt_event.php';
    if ( is_singular( 'cmt_event' ) && file_exists( $event_template ) ) {
        return $event_template;
    }

    return $template;
}
add_filter( 'template_include', 'rsm_template_loader' );
