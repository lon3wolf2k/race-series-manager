<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Helper: resolve event id from shortcode attr (id or slug)
 *
 * [rsm_event_overview event="123"]
 * [rsm_event_overview event="corfu-mountain-trail"]
 */
function rsm_resolve_event_id( $event_attr ) {
    if ( empty( $event_attr ) ) {
        return 0;
    }

    // If numeric, treat as ID.
    if ( is_numeric( $event_attr ) ) {
        return intval( $event_attr );
    }

    // Assume slug.
    $slug  = sanitize_title( $event_attr );
    $event = get_page_by_path( $slug, OBJECT, 'cmt_event' );

    if ( $event ) {
        return $event->ID;
    }

    return 0;
}

/**
 * SHORTCODE: [rsm_event_overview event="corfu-mountain-trail"]
 *
 * Output:
 * - Event title
 * - Table of races for that event (all, ordered by date)
 */
function rsm_event_overview_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'event' => '',
        ),
        $atts,
        'rsm_event_overview'
    );

    $event_id = rsm_resolve_event_id( $atts['event'] );

    if ( ! $event_id ) {
        return '<p>No event selected (invalid event attribute).</p>';
    }

    $event = get_post( $event_id );
    if ( ! $event || 'cmt_event' !== $event->post_type ) {
        return '<p>Event not found.</p>';
    }

    // Get all races for this event.
    $races = new WP_Query(
        array(
            'post_type'      => 'cmt_race',
            'posts_per_page' => -1,
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_key'       => '_rsm_race_date',
            'meta_query'     => array(
                array(
                    'key'   => '_rsm_race_event_id',
                    'value' => $event_id,
                ),
            ),
        )
    );

    ob_start();
    ?>
    <div class="rsm-event-overview">
        <h2 class="rsm-event-title">
            <?php echo esc_html( get_the_title( $event_id ) ); ?>
        </h2>

        <?php if ( $races->have_posts() ) : ?>
            <table class="rsm-races">
                <thead>
                <tr>
                    <th><?php esc_html_e( 'Race', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Distance', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Elevation', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Start time', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Start location', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Entry fee', 'race-series-manager' ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ( $races->have_posts() ) :
                    $races->the_post();
                    $race_id    = get_the_ID();
                    $distance   = get_post_meta( $race_id, '_rsm_race_distance', true );
                    $elevation  = get_post_meta( $race_id, '_rsm_race_elevation', true );
                    $race_date  = get_post_meta( $race_id, '_rsm_race_date', true );
                    $start_time = get_post_meta( $race_id, '_rsm_race_start_time', true );
                    $start_loc  = get_post_meta( $race_id, '_rsm_race_start_location', true );
                    $fee        = get_post_meta( $race_id, '_rsm_race_fee', true );
                    ?>
                    <tr>
                        <td><?php the_title(); ?></td>
                        <td><?php echo esc_html( $distance ); ?></td>
                        <td><?php echo esc_html( $elevation ); ?></td>
                        <td><?php echo esc_html( $race_date ); ?></td>
                        <td><?php echo esc_html( $start_time ); ?></td>
                        <td><?php echo esc_html( $start_loc ); ?></td>
                        <td><?php echo esc_html( $fee ); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'No races defined for this event yet.', 'race-series-manager' ); ?></p>
        <?php endif; ?>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode( 'rsm_event_overview', 'rsm_event_overview_shortcode' );
