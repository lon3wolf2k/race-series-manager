<?php
/*
Plugin Name: Race Series Manager
Description: Manage trail running events and races, similar to Corfu Mountain Trail.
Version: 0.4.0
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
require_once RSM_PLUGIN_DIR . 'includes/admin-menu.php';
require_once RSM_PLUGIN_DIR . 'includes/admin-help.php';
require_once RSM_PLUGIN_DIR . 'includes/admin-columns.php';
require_once RSM_PLUGIN_DIR . 'includes/settings.php';
require_once RSM_PLUGIN_DIR . 'includes/clone.php';
require_once RSM_PLUGIN_DIR . 'includes/meta-event.php';
require_once RSM_PLUGIN_DIR . 'includes/meta-race.php';
require_once RSM_PLUGIN_DIR . 'includes/meta-shortcodes.php';
require_once RSM_PLUGIN_DIR . 'includes/meta-results.php';      // αποτελέσματα (backend)
require_once RSM_PLUGIN_DIR . 'includes/results-frontend.php';  // αποτελέσματα (frontend)
require_once RSM_PLUGIN_DIR . 'includes/shortcodes.php';
require_once RSM_PLUGIN_DIR . 'includes/widgets.php';
require_once RSM_PLUGIN_DIR . 'includes/pdf-booklet.php';

// -----------------------------------------------------------------------------
// Front-end assets
// -----------------------------------------------------------------------------
function rsm_register_frontend_assets() {
    wp_register_style( 'rsm-core', RSM_PLUGIN_URL . 'assets/css/rsm-core.css', array(), '0.4.0' );
    wp_register_style( 'rsm-event', RSM_PLUGIN_URL . 'assets/css/rsm-event.css', array( 'rsm-core' ), '0.4.0' );
    wp_register_style( 'rsm-showcase', RSM_PLUGIN_URL . 'assets/css/rsm-showcase.css', array( 'rsm-core' ), '0.4.0' );
    wp_register_style( 'rsm-lightbox-style', RSM_PLUGIN_URL . 'assets/css/rsm-lightbox.css', array(), '0.4.0' );
    wp_register_style( 'rsm-theme-light', RSM_PLUGIN_URL . 'assets/css/rsm-theme-light.css', array(), '0.4.0' );
    wp_register_style( 'rsm-theme-dark', RSM_PLUGIN_URL . 'assets/css/rsm-theme-dark.css', array(), '0.4.0' );

    wp_register_script(
        'rsm-lightbox',
        RSM_PLUGIN_URL . 'assets/js/rsm-lightbox.js',
        array( 'jquery' ),
        '0.4.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'rsm_register_frontend_assets', 5 );

function rsm_enqueue_theme_style() {
    if ( ! function_exists( 'rsm_get_settings' ) ) {
        return;
    }

    $settings = rsm_get_settings();
    $theme    = ( isset( $settings['theme'] ) && 'dark' === $settings['theme'] ) ? 'dark' : 'light';

    wp_enqueue_style( 'rsm-theme-' . $theme );
}

function rsm_enqueue_style_bundle() {
    wp_enqueue_style( 'rsm-core' );
    wp_enqueue_style( 'rsm-event' );
    wp_enqueue_style( 'rsm-showcase' );
    wp_enqueue_style( 'rsm-lightbox-style' );
    rsm_enqueue_theme_style();
}

function rsm_enqueue_assets() {

    $widget_active = function_exists( 'is_active_widget' ) && is_active_widget( false, false, 'rsm_race_showcase_widget', true );

    if (
        $widget_active ||
        is_singular( 'cmt_race' ) ||
        is_singular( 'cmt_event' ) ||
        is_post_type_archive( 'cmt_race' )
    ) {
        rsm_enqueue_style_bundle();

        wp_enqueue_script( 'rsm-lightbox' );

        wp_localize_script(
            'rsm-lightbox',
            'rsmLightbox',
            array(
                'i18nClose' => __( 'Close', 'race-series-manager' ),
                'i18nPrev'  => __( 'Previous', 'race-series-manager' ),
                'i18nNext'  => __( 'Next', 'race-series-manager' ),
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'rsm_enqueue_assets' );

function rsm_add_theme_body_class( $classes ) {
    $settings = rsm_get_settings();
    $theme    = ( isset( $settings['theme'] ) && 'dark' === $settings['theme'] ) ? 'dark' : 'light';

    $classes[] = 'rsm-theme-' . $theme;

    return $classes;
}
add_filter( 'body_class', 'rsm_add_theme_body_class' );

// -----------------------------------------------------------------------------
// Template loader για Event & Race & Archive Races
// -----------------------------------------------------------------------------
function rsm_template_loader( $template ) {

    // Single Race
    if ( is_singular( 'cmt_race' ) ) {
        $race_template = RSM_PLUGIN_DIR . 'templates/single-cmt_race.php';
        if ( file_exists( $race_template ) ) {
            return $race_template;
        }
    }

    // Single Event
    if ( is_singular( 'cmt_event' ) ) {
        $event_template = RSM_PLUGIN_DIR . 'templates/single-cmt_event.php';
        if ( file_exists( $event_template ) ) {
            return $event_template;
        }
    }

    // Archive Races (αν αποφασίσουμε να το χρησιμοποιήσουμε αργότερα)
    $archive_template = RSM_PLUGIN_DIR . 'templates/archive-cmt_race.php';
    if ( is_post_type_archive( 'cmt_race' ) && file_exists( $archive_template ) ) {
        return $archive_template;
    }

    return $template;
}
add_filter( 'template_include', 'rsm_template_loader' );
