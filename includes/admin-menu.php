<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the main RS Manager menu to group plugin post types.
 */
function rsm_register_admin_menu() {
    add_menu_page(
        esc_html__( 'RS Manager', 'race-series-manager' ),
        esc_html__( 'RS Manager', 'race-series-manager' ),
        'edit_posts',
        'rsm-manager',
        'rsm_render_admin_home',
        'dashicons-flag',
        6
    );
}
add_action( 'admin_menu', 'rsm_register_admin_menu' );

/**
 * Add submenus for Events, Races, and Results under the RS Manager parent.
 */
function rsm_register_content_submenus() {
    // Keep the dashboard/landing page accessible as the first submenu item.
    add_submenu_page(
        'rsm-manager',
        esc_html__( 'RS Manager', 'race-series-manager' ),
        esc_html__( 'Dashboard', 'race-series-manager' ),
        'edit_posts',
        'rsm-manager',
        'rsm_render_admin_home'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Events', 'race-series-manager' ),
        esc_html__( 'All Events', 'race-series-manager' ),
        'edit_posts',
        'edit.php?post_type=cmt_event'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Add New Event', 'race-series-manager' ),
        esc_html__( 'Add New Event', 'race-series-manager' ),
        'edit_posts',
        'post-new.php?post_type=cmt_event'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Races', 'race-series-manager' ),
        esc_html__( 'All Races', 'race-series-manager' ),
        'edit_posts',
        'edit.php?post_type=cmt_race'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Add New Race', 'race-series-manager' ),
        esc_html__( 'Add New Race', 'race-series-manager' ),
        'edit_posts',
        'post-new.php?post_type=cmt_race'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Results', 'race-series-manager' ),
        esc_html__( 'All Results', 'race-series-manager' ),
        'edit_posts',
        'edit.php?post_type=cmt_result'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Add New Result', 'race-series-manager' ),
        esc_html__( 'Add New Result', 'race-series-manager' ),
        'edit_posts',
        'post-new.php?post_type=cmt_result'
    );
}
add_action( 'admin_menu', 'rsm_register_content_submenus', 11 );

/**
 * Render a simple landing page for the RS Manager menu.
 */
function rsm_render_admin_home() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }

    $events_url   = admin_url( 'edit.php?post_type=cmt_event' );
    $races_url    = admin_url( 'edit.php?post_type=cmt_race' );
    $results_url  = admin_url( 'edit.php?post_type=cmt_result' );
    $settings_url = admin_url( 'admin.php?page=rsm-settings' );
    $help_url     = admin_url( 'admin.php?page=rsm-help' );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'RS Manager', 'race-series-manager' ); ?></h1>
        <p><?php esc_html_e( 'Manage your events, races, and results from one place.', 'race-series-manager' ); ?></p>
        <p>
            <a class="button button-primary" href="<?php echo esc_url( $events_url ); ?>">
                <?php esc_html_e( 'View Events', 'race-series-manager' ); ?>
            </a>
            <a class="button" href="<?php echo esc_url( $races_url ); ?>">
                <?php esc_html_e( 'View Races', 'race-series-manager' ); ?>
            </a>
            <a class="button" href="<?php echo esc_url( $results_url ); ?>">
                <?php esc_html_e( 'View Results', 'race-series-manager' ); ?>
            </a>
            <a class="button" href="<?php echo esc_url( $settings_url ); ?>">
                <?php esc_html_e( 'Settings', 'race-series-manager' ); ?>
            </a>
            <a class="button" href="<?php echo esc_url( $help_url ); ?>">
                <?php esc_html_e( 'Help', 'race-series-manager' ); ?>
            </a>
        </p>
    </div>
    <?php
}
