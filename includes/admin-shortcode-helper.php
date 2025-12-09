<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add "Shortcode" meta boxes to Events and Races (sidebar).
 */
function rsm_add_shortcode_helper_meta_boxes() {
    // For Events.
    add_meta_box(
        'rsm_event_shortcode_helper',
        __( 'Shortcode', 'race-series-manager' ),
        'rsm_event_shortcode_helper_callback',
        'cmt_event',
        'side',
        'default'
    );

    // For Races.
    add_meta_box(
        'rsm_race_shortcode_helper',
        __( 'Shortcode', 'race-series-manager' ),
        'rsm_race_shortcode_helper_callback',
        'cmt_race',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'rsm_add_shortcode_helper_meta_boxes' );

/**
 * Event edit screen: show event overview shortcode.
 */
function rsm_event_shortcode_helper_callback( $post ) {
    // Use slug if available, otherwise fallback to ID.
    $slug        = $post->post_name;
    $event_id    = $post->ID;
    $shortcode_slug = '';
    $shortcode_id   = '';

    if ( ! empty( $slug ) ) {
        $shortcode_slug = '[rsm_event_overview event="' . esc_attr( $slug ) . '"]';
    }

    if ( $event_id ) {
        $shortcode_id = '[rsm_event_overview event="' . intval( $event_id ) . '"]';
    }
    ?>

    <p><?php esc_html_e( 'Copy this shortcode and paste it into any page or post to display the races of this event.', 'race-series-manager' ); ?></p>

    <?php if ( $shortcode_slug ) : ?>
        <p>
            <strong><?php esc_html_e( 'By slug:', 'race-series-manager' ); ?></strong><br>
            <input type="text"
                   readonly
                   onclick="this.select();"
                   value="<?php echo esc_attr( $shortcode_slug ); ?>"
                   style="width: 100%; font-family: monospace;">
        </p>
    <?php endif; ?>

    <?php if ( $shortcode_id ) : ?>
        <p>
            <strong><?php esc_html_e( 'By ID:', 'race-series-manager' ); ?></strong><br>
            <input type="text"
                   readonly
                   onclick="this.select();"
                   value="<?php echo esc_attr( $shortcode_id ); ?>"
                   style="width: 100%; font-family: monospace;">
        </p>
    <?php endif; ?>

    <?php if ( empty( $shortcode_slug ) && empty( $shortcode_id ) ) : ?>
        <p><?php esc_html_e( 'Save the event first to generate a shortcode.', 'race-series-manager' ); ?></p>
    <?php endif; ?>

    <?php
}

/**
 * Race edit screen: show the event shortcode (for its parent event).
 */
function rsm_race_shortcode_helper_callback( $post ) {
    $race_id  = $post->ID;
    $event_id = get_post_meta( $race_id, '_rsm_race_event_id', true );

    if ( ! $event_id ) {
        ?>
        <p><?php esc_html_e( 'This race is not linked to an event yet. Select an event in the Race Details box, save, and the shortcode will appear here.', 'race-series-manager' ); ?></p>
        <?php
        return;
    }

    $event = get_post( $event_id );
    if ( ! $event || 'cmt_event' !== $event->post_type ) {
        ?>
        <p><?php esc_html_e( 'The linked event could not be found.', 'race-series-manager' ); ?></p>
        <?php
        return;
    }

    $slug          = $event->post_name;
    $shortcode_slug = '';
    $shortcode_id   = '';

    if ( ! empty( $slug ) ) {
        $shortcode_slug = '[rsm_event_overview event="' . esc_attr( $slug ) . '"]';
    }

    if ( $event_id ) {
        $shortcode_id = '[rsm_event_overview event="' . intval( $event_id ) . '"]';
    }
    ?>

    <p>
        <?php
        printf(
            /* translators: %s: event title */
            esc_html__( 'This race belongs to event: %s', 'race-series-manager' ),
            '<strong>' . esc_html( get_the_title( $event_id ) ) . '</strong>'
        );
        ?>
    </p>

    <p><?php esc_html_e( 'Use this shortcode to show all races of that event on a page.', 'race-series-manager' ); ?></p>

    <?php if ( $shortcode_slug ) : ?>
        <p>
            <strong><?php esc_html_e( 'By slug:', 'race-series-manager' ); ?></strong><br>
            <input type="text"
                   readonly
                   onclick="this.select();"
                   value="<?php echo esc_attr( $shortcode_slug ); ?>"
                   style="width: 100%; font-family: monospace;">
        </p>
    <?php endif; ?>

    <?php if ( $shortcode_id ) : ?>
        <p>
            <strong><?php esc_html_e( 'By ID:', 'race-series-manager' ); ?></strong><br>
            <input type="text"
                   readonly
                   onclick="this.select();"
                   value="<?php echo esc_attr( $shortcode_id ); ?>"
                   style="width: 100%; font-family: monospace;">
        </p>
    <?php endif; ?>

    <?php
}
