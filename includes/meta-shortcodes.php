<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function rsm_register_shortcode_metaboxes() {
    add_meta_box(
        'rsm-event-shortcodes',
        esc_html__( 'RS Manager Shortcodes', 'race-series-manager' ),
        'rsm_render_event_shortcode_box',
        'cmt_event',
        'side',
        'default'
    );

    add_meta_box(
        'rsm-race-shortcodes',
        esc_html__( 'RS Manager Shortcodes', 'race-series-manager' ),
        'rsm_render_race_shortcode_box',
        'cmt_race',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'rsm_register_shortcode_metaboxes' );

function rsm_render_event_shortcode_box( $post ) {
    $event_id   = (int) $post->ID;
    $shortcodes = array(
        array(
            'label' => esc_html__( 'Event overview', 'race-series-manager' ),
            'code'  => sprintf( '[rsm_event_overview event="%d"]', $event_id ),
        ),
        array(
            'label' => esc_html__( 'Event banner', 'race-series-manager' ),
            'code'  => sprintf( '[rsm_event_banner event="%d" title="%s"]', $event_id, esc_attr__( 'Countdown to race day', 'race-series-manager' ) ),
        ),
        array(
            'label' => esc_html__( 'Race showcase', 'race-series-manager' ),
            'code'  => sprintf( '[rsm_race_showcase event="%d" count="4"]', $event_id ),
        ),
        array(
            'label' => esc_html__( 'Event link', 'race-series-manager' ),
            'code'  => sprintf( '[rsm_event_link event="%d"]', $event_id ),
        ),
    );

    rsm_render_shortcode_list( $shortcodes );
}

function rsm_render_race_shortcode_box( $post ) {
    $race_id         = (int) $post->ID;
    $event_id        = (int) get_post_meta( $race_id, '_rsm_race_event_id', true );
    $shortcodes      = array(
        array(
            'label' => esc_html__( 'Race link', 'race-series-manager' ),
            'code'  => sprintf( '[rsm_race_link race="%d"]', $race_id ),
        ),
    );

    if ( $event_id ) {
        $shortcodes[] = array(
            'label' => esc_html__( 'Event overview for this race\'s event', 'race-series-manager' ),
            'code'  => sprintf( '[rsm_event_overview event="%d"]', $event_id ),
        );
        $shortcodes[] = array(
            'label' => esc_html__( 'Race showcase for this race\'s event', 'race-series-manager' ),
            'code'  => sprintf( '[rsm_race_showcase event="%d" count="4"]', $event_id ),
        );
        $shortcodes[] = array(
            'label' => esc_html__( 'Event banner for this race\'s event', 'race-series-manager' ),
            'code'  => sprintf( '[rsm_event_banner event="%d" title="%s"]', $event_id, esc_attr__( 'Countdown to race day', 'race-series-manager' ) ),
        );
    }

    rsm_render_shortcode_list( $shortcodes );
}

function rsm_render_shortcode_list( $shortcodes ) {
    ?>
    <ul class="rsm-shortcode-list">
        <?php foreach ( $shortcodes as $shortcode ) : ?>
            <li class="rsm-shortcode-item">
                <p class="rsm-shortcode-label"><?php echo esc_html( $shortcode['label'] ); ?></p>
                <div class="rsm-shortcode-row">
                    <input type="text"
                           class="widefat rsm-shortcode-field"
                           readonly
                           value="<?php echo esc_attr( $shortcode['code'] ); ?>"
                           aria-label="<?php echo esc_attr( $shortcode['label'] ); ?>" />
                    <button type="button"
                            class="button rsm-copy-shortcode"
                            data-label-default="<?php esc_attr_e( 'Copy', 'race-series-manager' ); ?>"
                            data-label-copied="<?php esc_attr_e( 'Copied!', 'race-series-manager' ); ?>"
                            aria-label="<?php esc_attr_e( 'Copy shortcode', 'race-series-manager' ); ?>">
                        <?php esc_html_e( 'Copy', 'race-series-manager' ); ?>
                    </button>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
}

function rsm_enqueue_admin_shortcodes_assets( $hook ) {
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }

    $screen = get_current_screen();
    if ( empty( $screen->post_type ) || ! in_array( $screen->post_type, array( 'cmt_event', 'cmt_race' ), true ) ) {
        return;
    }

    wp_enqueue_script(
        'rsm-admin-shortcodes',
        RSM_PLUGIN_URL . 'assets/js/rsm-admin-shortcodes.js',
        array(),
        '0.4.0',
        true
    );
}
add_action( 'admin_enqueue_scripts', 'rsm_enqueue_admin_shortcodes_assets' );
