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
 * Helper: resolve race id from shortcode attr (id or slug)
 *
 * [rsm_race_link race="123"]
 * [rsm_race_link race="sunrise-vertical"]
 */
function rsm_resolve_race_id( $race_attr ) {
    if ( empty( $race_attr ) ) {
        return 0;
    }

    if ( is_numeric( $race_attr ) ) {
        return intval( $race_attr );
    }

    $slug = sanitize_title( $race_attr );
    $race = get_page_by_path( $slug, OBJECT, 'cmt_race' );

    if ( $race ) {
        return $race->ID;
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

    // Ensure front-end assets load when shortcode is used outside custom templates.
    if ( function_exists( 'rsm_enqueue_style_bundle' ) ) {
        rsm_enqueue_style_bundle();
    }

    $settings = rsm_get_settings();
    $race_ordering = rsm_get_race_ordering_args( $settings );

    $event_id = rsm_resolve_event_id( $atts['event'] );

    if ( ! $event_id ) {
        return '<p>' . esc_html__( 'No event selected (invalid event attribute).', 'race-series-manager' ) . '</p>';
    }

    $event = get_post( $event_id );
    if ( ! $event || 'cmt_event' !== $event->post_type ) {
        return '<p>' . esc_html__( 'Event not found.', 'race-series-manager' ) . '</p>';
    }

    $reg_url     = get_post_meta( $event_id, '_rsm_event_registration_url', true );
    $part_url    = get_post_meta( $event_id, '_rsm_event_participants_url', true );
    $live_url    = get_post_meta( $event_id, '_rsm_event_live_url', true );
    $open_tab    = ! empty( $settings['overview_action_new_tab'] );
    $target_attr = $open_tab ? ' target="_blank" rel="noopener"' : '';

    $results_url = '';
    if ( function_exists( 'rsm_get_results_page_url' ) ) {
        $results_url = rsm_get_results_page_url( $event_id );
    }

    // Get all races for this event.
    $races = new WP_Query(
        array_merge(
            array(
                'post_type'      => 'cmt_race',
                'posts_per_page' => -1,
                'meta_query'     => array(
                    array(
                        'key'   => '_rsm_race_event_id',
                        'value' => $event_id,
                    ),
                ),
            ),
            $race_ordering
        )
    );

    ob_start();
    ?>
    <div class="rsm-event-overview">
        <div class="rsm-event-overview-header">
            <div class="rsm-event-overview-heading">
                <h2 class="rsm-event-title">
                    <?php echo esc_html( get_the_title( $event_id ) ); ?>
                </h2>
                <?php if ( ! empty( $settings['overview_show_excerpt'] ) && $event->post_excerpt ) : ?>
                    <p class="rsm-event-overview-excerpt"><?php echo esc_html( $event->post_excerpt ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( $reg_url || $part_url || $live_url || $results_url ) : ?>
                <div class="rsm-event-overview-actions">
                    <?php if ( $reg_url ) : ?>
                        <a class="rsm-summary-btn" href="<?php echo esc_url( $reg_url ); ?>"<?php echo $target_attr; ?>>
                            <?php echo esc_html( rsm_get_setting_label( $settings, 'overview_registration_label', 'Registration' ) ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $part_url ) : ?>
                        <a class="rsm-summary-btn" href="<?php echo esc_url( $part_url ); ?>"<?php echo $target_attr; ?>>
                            <?php echo esc_html( rsm_get_setting_label( $settings, 'overview_participants_label', 'Participants' ) ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $live_url ) : ?>
                        <a class="rsm-summary-btn" href="<?php echo esc_url( $live_url ); ?>"<?php echo $target_attr; ?>>
                            <?php echo esc_html( rsm_get_setting_label( $settings, 'overview_live_label', 'Live' ) ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $results_url ) : ?>
                        <a class="rsm-summary-btn rsm-event-results-btn"
                           href="<?php echo esc_url( $results_url ); ?>"<?php echo $target_attr; ?>>
                            <?php echo esc_html( rsm_get_setting_label( $settings, 'overview_results_label', 'Results' ) ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( $races->have_posts() ) : ?>
            <table class="rsm-races rsm-event-overview-table">
                <thead>
                <tr>
                    <th><?php esc_html_e( 'Race', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Distance', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Elevation', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Start time', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Start location', 'race-series-manager' ); ?></th>
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

                    $date_format = get_option( 'date_format' );
                    if ( empty( $date_format ) ) {
                        $date_format = 'd-m-Y';
                    }

                    $race_date_formatted = '';
                    if ( $race_date ) {
                        $ts = strtotime( $race_date );
                        if ( $ts ) {
                            $race_date_formatted = wp_date( $date_format, $ts );
                        }
                    }

                    $start_time_formatted = '';
                    if ( $start_time ) {
                        $ts = strtotime( $start_time );
                        if ( $ts ) {
                            $start_time_formatted = wp_date( get_option( 'time_format' ), $ts );
                        }
                    }
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo esc_url( get_permalink() ); ?>" class="rsm-event-overview-race-link">
                                <?php echo esc_html( get_the_title() ); ?>
                            </a>
                        </td>
                        <td><?php echo $distance ? esc_html( $distance ) : '—'; ?></td>
                        <td><?php echo $elevation ? esc_html( $elevation ) : '—'; ?></td>
                        <td><?php echo $race_date_formatted ? esc_html( $race_date_formatted ) : '—'; ?></td>
                        <td><?php echo $start_time_formatted ? esc_html( $start_time_formatted ) : '—'; ?></td>
                        <td><?php echo $start_loc ? esc_html( $start_loc ) : '—'; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p class="rsm-event-overview-empty"><?php esc_html_e( 'No races defined for this event yet.', 'race-series-manager' ); ?></p>
        <?php endif; ?>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode( 'rsm_event_overview', 'rsm_event_overview_shortcode' );

/**
 * SHORTCODE: [rsm_race_showcase event="123" count="4" title="Featured races"]
 *
 * Outputs the same grid used by the homepage widget.
 */
function rsm_race_showcase_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'event' => '',
            'count' => 4,
            'title' => '',
        ),
        $atts,
        'rsm_race_showcase'
    );

    $event_id = rsm_resolve_event_id( $atts['event'] );

    if ( ! $event_id ) {
        return '<p>' . esc_html__( 'No event selected (invalid event attribute).', 'race-series-manager' ) . '</p>';
    }

    $race_count = min( 12, max( 1, absint( $atts['count'] ) ) );

    if ( function_exists( 'rsm_enqueue_style_bundle' ) ) {
        rsm_enqueue_style_bundle();
    }

    ob_start();

    echo '<div class="rsm-race-showcase-shortcode">';

    if ( ! empty( $atts['title'] ) ) {
        echo '<h2 class="rsm-race-showcase-heading">' . esc_html( $atts['title'] ) . '</h2>';
    }

    echo rsm_get_race_showcase_markup( $event_id, $race_count );

    echo '</div>';

    return ob_get_clean();
}
add_shortcode( 'rsm_race_showcase', 'rsm_race_showcase_shortcode' );

/**
 * SHORTCODE: [rsm_event_banner event="123" title="Event banner title"]
 */
function rsm_event_banner_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'event' => '',
            'title' => '',
        ),
        $atts,
        'rsm_event_banner'
    );

    $event_id = rsm_resolve_event_id( $atts['event'] );

    if ( ! $event_id ) {
        return '<p>' . esc_html__( 'No event selected (invalid event attribute).', 'race-series-manager' ) . '</p>';
    }

    return rsm_get_event_banner_markup( $event_id, $atts['title'] );
}
add_shortcode( 'rsm_event_banner', 'rsm_event_banner_shortcode' );

/**
 * SHORTCODE: [rsm_event_link event="123" label="Custom text"]
 */
function rsm_event_link_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'event' => '',
            'label' => '',
        ),
        $atts,
        'rsm_event_link'
    );

    $event_id = rsm_resolve_event_id( $atts['event'] );
    if ( ! $event_id ) {
        return '';
    }

    $event = get_post( $event_id );
    if ( ! $event || 'cmt_event' !== $event->post_type ) {
        return '';
    }

    $label = $atts['label'];
    if ( '' === $label ) {
        $label = get_the_title( $event_id );
    }

    return sprintf(
        '<a href="%1$s">%2$s</a>',
        esc_url( get_permalink( $event_id ) ),
        esc_html( $label )
    );
}
add_shortcode( 'rsm_event_link', 'rsm_event_link_shortcode' );

/**
 * SHORTCODE: [rsm_race_link race="123" label="Custom text"]
 */
function rsm_race_link_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'race'  => '',
            'label' => '',
        ),
        $atts,
        'rsm_race_link'
    );

    $race_id = rsm_resolve_race_id( $atts['race'] );
    if ( ! $race_id ) {
        return '';
    }

    $race = get_post( $race_id );
    if ( ! $race || 'cmt_race' !== $race->post_type ) {
        return '';
    }

    $label = $atts['label'];
    if ( '' === $label ) {
        $label = get_the_title( $race_id );
    }

    return sprintf(
        '<a href="%1$s">%2$s</a>',
        esc_url( get_permalink( $race_id ) ),
        esc_html( $label )
    );
}
add_shortcode( 'rsm_race_link', 'rsm_race_link_shortcode' );
