<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Retrieve races for the selected event.
 *
 * @param int $event_id
 * @param int $limit
 *
 * @return array
 */
function rsm_get_race_showcase_races( $event_id, $limit ) {
    $args = array(
        'post_type'      => 'cmt_race',
        'posts_per_page' => $limit,
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    );

    if ( $event_id ) {
        $args['meta_query'] = array(
            array(
                'key'   => '_rsm_race_event_id',
                'value' => $event_id,
            ),
        );
    }

    return get_posts( $args );
}

/**
 * Retrieve the next race date/time for an event to power countdowns.
 *
 * @param int $event_id
 *
 * @return array
 */
function rsm_get_event_next_race_datetime( $event_id ) {
    $result = array(
        'display'  => '',
        'iso'      => '',
        'race_id'  => 0,
    );

    if ( ! $event_id ) {
        return $result;
    }

    $date_format = get_option( 'date_format' );
    if ( empty( $date_format ) ) {
        $date_format = 'd-m-Y';
    }

    $time_format = get_option( 'time_format' );
    if ( empty( $time_format ) ) {
        $time_format = 'H:i';
    }

    $event_date = get_post_meta( $event_id, '_rsm_event_date', true );
    $event_time = get_post_meta( $event_id, '_rsm_event_time', true );

    if ( $event_date ) {
        $timestamp = strtotime( trim( $event_date . ' ' . $event_time ) );

        if ( ! $timestamp ) {
            $timestamp = strtotime( $event_date );
        }

        if ( $timestamp ) {
            $result['display'] = wp_date( $date_format, $timestamp );

            if ( $event_time ) {
                $result['display'] .= ' ' . wp_date( $time_format, $timestamp );
            }

            $result['iso'] = wp_date( 'c', $timestamp );

            return $result;
        }
    }

    $races = get_posts(
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

    if ( empty( $races ) ) {
        return $result;
    }

    $now          = current_time( 'timestamp' );
    $fallback     = null;
    $fallback_id  = 0;

    foreach ( $races as $race ) {
        $race_date = get_post_meta( $race->ID, '_rsm_race_date', true );

        if ( empty( $race_date ) ) {
            continue;
        }

        $start_time = get_post_meta( $race->ID, '_rsm_race_start_time', true );
        $datetime   = trim( $race_date . ' ' . $start_time );
        $timestamp  = strtotime( $datetime );

        if ( ! $timestamp ) {
            continue;
        }

        if ( $timestamp >= $now ) {
            $result['display'] = wp_date( $date_format, $timestamp );
            $result['iso']     = wp_date( 'c', $timestamp );
            $result['race_id'] = $race->ID;

            break;
        }

        if ( ! $fallback || $timestamp > $fallback ) {
            $fallback    = $timestamp;
            $fallback_id = $race->ID;
        }
    }

    if ( empty( $result['iso'] ) && $fallback ) {
        $result['display'] = wp_date( $date_format, $fallback );
        $result['iso']     = wp_date( 'c', $fallback );
        $result['race_id'] = $fallback_id;
    }

    return $result;
}

/**
 * Format a race date for the badge.
 *
 * @param string $start_datetime
 *
 * @return string
 */
function rsm_format_race_showcase_date( $start_datetime ) {
    if ( empty( $start_datetime ) ) {
        return '';
    }

    $ts = strtotime( $start_datetime );

    if ( ! $ts ) {
        return '';
    }

    return date_i18n( 'j M Y', $ts );
}

/**
 * Get the image URL for a race, falling back to an inline placeholder if needed.
 *
 * @param int $race_id
 *
 * @return string
 */
function rsm_get_race_showcase_image_url( $race_id ) {
    $thumb_id = get_post_thumbnail_id( $race_id );

    if ( $thumb_id ) {
        $thumb_src = wp_get_attachment_image_src( $thumb_id, 'large' );
        if ( $thumb_src ) {
            return $thumb_src[0];
        }
    }

    $placeholder_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 360" role="img" aria-label="Race placeholder"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="%23f05a24"/><stop offset="100%" stop-color="%2328457a"/></linearGradient></defs><rect width="600" height="360" fill="url(%23g)"/><text x="50%" y="50%" fill="white" font-size="42" font-family="Arial, sans-serif" text-anchor="middle">Race</text></svg>';

    return 'data:image/svg+xml;utf8,' . rawurlencode( $placeholder_svg );
}

/**
 * Render the race showcase cards markup.
 *
 * @param int $event_id
 * @param int $race_count
 *
 * @return string
 */
function rsm_get_race_showcase_markup( $event_id, $race_count ) {
    $races = rsm_get_race_showcase_races( $event_id, $race_count );

    ob_start();

    if ( empty( $races ) ) {
        echo '<p>' . esc_html__( 'No races found for this event yet.', 'race-series-manager' ) . '</p>';
    } else {
        ?>
        <div class="rsm-race-showcase">
            <div class="rsm-race-showcase-grid">
                <?php foreach ( $races as $race ) :
                    $distance       = get_post_meta( $race->ID, '_rsm_race_distance', true );
                    $elevation      = get_post_meta( $race->ID, '_rsm_race_elevation', true );
                    $start_datetime = get_post_meta( $race->ID, '_rsm_race_date', true );
                    $race_date      = rsm_format_race_showcase_date( $start_datetime );
                    $thumb_url      = rsm_get_race_showcase_image_url( $race->ID );
                    ?>
                    <article class="rsm-race-card">
                        <a class="rsm-race-card__link" href="<?php echo esc_url( get_permalink( $race ) ); ?>">
                            <div class="rsm-race-card__thumb" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');">
                                <?php if ( $race_date ) : ?>
                                    <div class="rsm-race-card__badge"><?php echo esc_html( $race_date ); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="rsm-race-card__body">
                                <h3 class="rsm-race-card__title"><?php echo esc_html( get_the_title( $race ) ); ?></h3>
                                <div class="rsm-race-card__meta">
                                    <?php if ( $distance ) : ?>
                                        <span class="rsm-race-card__meta-item">
                                            <?php echo esc_html( $distance ); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ( $elevation ) : ?>
                                        <span class="rsm-race-card__meta-item">
                                            <?php echo esc_html( $elevation ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span class="rsm-race-card__cta rsm-race-card__cta-button"><?php esc_html_e( 'Race details', 'race-series-manager' ); ?></span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    return ob_get_clean();
}

/**
 * Render a countdown banner for an event with action buttons.
 *
 * @param int    $event_id
 * @param string $title
 *
 * @return string
 */
function rsm_get_event_banner_markup( $event_id, $title = '' ) {
    if ( function_exists( 'rsm_enqueue_style_bundle' ) ) {
        rsm_enqueue_style_bundle();
    }

    if ( function_exists( 'rsm_enqueue_countdown_assets' ) ) {
        rsm_enqueue_countdown_assets();
    }

    $event_id = absint( $event_id );

    if ( ! $event_id ) {
        return '<p>' . esc_html__( 'No event selected.', 'race-series-manager' ) . '</p>';
    }

    $event = get_post( $event_id );

    if ( ! $event || 'cmt_event' !== $event->post_type ) {
        return '<p>' . esc_html__( 'Event not found.', 'race-series-manager' ) . '</p>';
    }

    $settings    = function_exists( 'rsm_get_settings' ) ? rsm_get_settings() : array();
    $reg_label   = isset( $settings['overview_registration_label'] ) ? $settings['overview_registration_label'] : __( 'Registration', 'race-series-manager' );
    $part_label  = isset( $settings['overview_participants_label'] ) ? $settings['overview_participants_label'] : __( 'Participants', 'race-series-manager' );
    $live_label  = isset( $settings['overview_live_label'] ) ? $settings['overview_live_label'] : __( 'Live', 'race-series-manager' );
    $open_tab    = ! empty( $settings['overview_action_new_tab'] );
    $target_attr = $open_tab ? ' target="_blank" rel="noopener"' : '';

    $reg_url  = get_post_meta( $event_id, '_rsm_event_registration_url', true );
    $part_url = get_post_meta( $event_id, '_rsm_event_participants_url', true );
    $live_url = get_post_meta( $event_id, '_rsm_event_live_url', true );

    $datetime      = rsm_get_event_next_race_datetime( $event_id );
    $background_id = $datetime['race_id'] ? $datetime['race_id'] : 0;
    $bg_url        = rsm_get_race_showcase_image_url( $background_id );
    $heading       = $title ? $title : get_the_title( $event_id );

    ob_start();
    ?>
    <div class="rsm-event-banner">
        <div class="rsm-event-banner__media" style="background-image: url('<?php echo esc_url( $bg_url ); ?>');"></div>
        <div class="rsm-event-banner__inner">
            <div class="rsm-event-banner__info">
                <span class="rsm-event-banner__eyebrow"><?php esc_html_e( 'Upcoming event', 'race-series-manager' ); ?></span>
                <h3 class="rsm-event-banner__title"><?php echo esc_html( $heading ); ?></h3>
                <?php if ( $datetime['display'] || $datetime['iso'] ) : ?>
                    <div class="rsm-event-banner__time">
                        <?php if ( $datetime['display'] ) : ?>
                            <p class="rsm-event-banner__date"><?php echo esc_html( $datetime['display'] ); ?></p>
                        <?php endif; ?>

                        <?php if ( $datetime['iso'] ) : ?>
                            <div class="rsm-countdown" data-rsm-countdown="<?php echo esc_attr( $datetime['iso'] ); ?>" aria-live="polite">
                                <div class="rsm-countdown__segment">
                                    <span class="rsm-countdown__number" data-countdown-unit="days">--</span>
                                    <span class="rsm-countdown__label"><?php esc_html_e( 'Days', 'race-series-manager' ); ?></span>
                                </div>
                                <div class="rsm-countdown__segment">
                                    <span class="rsm-countdown__number" data-countdown-unit="hours">--</span>
                                    <span class="rsm-countdown__label"><?php esc_html_e( 'Hours', 'race-series-manager' ); ?></span>
                                </div>
                                <div class="rsm-countdown__segment">
                                    <span class="rsm-countdown__number" data-countdown-unit="minutes">--</span>
                                    <span class="rsm-countdown__label"><?php esc_html_e( 'Minutes', 'race-series-manager' ); ?></span>
                                </div>
                                <span class="rsm-countdown__status"></span>
                            </div>
                        <?php else : ?>
                            <p class="rsm-countdown__status"><?php esc_html_e( 'Countdown unavailable', 'race-series-manager' ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( $reg_url || $part_url || $live_url ) : ?>
                <div class="rsm-event-banner__actions">
                    <?php if ( $reg_url ) : ?>
                        <a class="rsm-banner-btn" href="<?php echo esc_url( $reg_url ); ?>"<?php echo $target_attr; ?>>
                            <?php echo esc_html( $reg_label ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $part_url ) : ?>
                        <a class="rsm-banner-btn" href="<?php echo esc_url( $part_url ); ?>"<?php echo $target_attr; ?>>
                            <?php echo esc_html( $part_label ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $live_url ) : ?>
                        <a class="rsm-banner-btn" href="<?php echo esc_url( $live_url ); ?>"<?php echo $target_attr; ?>>
                            <?php echo esc_html( $live_label ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php

    return ob_get_clean();
}

/**
 * Race showcase widget for highlighting races from a selected event.
 */
class RSM_Race_Showcase_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'rsm_race_showcase_widget',
            __( 'RS Manager: Race Showcase', 'race-series-manager' ),
            array(
                'description' => __( 'Display a set of races from a specific event with their key details.', 'race-series-manager' ),
            )
        );
    }

    /**
     * Render the widget.
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        $event_id  = isset( $instance['event_id'] ) ? absint( $instance['event_id'] ) : 0;
        $race_count = isset( $instance['race_count'] ) ? absint( $instance['race_count'] ) : 4;

        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        echo rsm_get_race_showcase_markup( $event_id, $race_count );

        echo $args['after_widget'];
    }

    /**
     * Handle updates to the widget settings.
     *
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']      = sanitize_text_field( $new_instance['title'] );
        $instance['event_id']   = absint( $new_instance['event_id'] );
        $instance['race_count'] = min( 12, max( 1, absint( $new_instance['race_count'] ) ) );

        return $instance;
    }

    /**
     * Render the widget form in the admin.
     *
     * @param array $instance
     */
    public function form( $instance ) {
        $title      = isset( $instance['title'] ) ? $instance['title'] : __( 'Discover the races', 'race-series-manager' );
        $event_id   = isset( $instance['event_id'] ) ? absint( $instance['event_id'] ) : 0;
        $race_count = isset( $instance['race_count'] ) ? absint( $instance['race_count'] ) : 4;

        $events = get_posts(
            array(
                'post_type'      => 'cmt_event',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
            )
        );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'race-series-manager' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'event_id' ) ); ?>"><?php esc_html_e( 'Event:', 'race-series-manager' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'event_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'event_id' ) ); ?>">
                <option value="0" <?php selected( 0, $event_id ); ?>><?php esc_html_e( 'Select an event', 'race-series-manager' ); ?></option>
                <?php foreach ( $events as $event ) : ?>
                    <option value="<?php echo esc_attr( $event->ID ); ?>" <?php selected( $event->ID, $event_id ); ?>>
                        <?php echo esc_html( get_the_title( $event ) ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'race_count' ) ); ?>"><?php esc_html_e( 'Number of races to show:', 'race-series-manager' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'race_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'race_count' ) ); ?>">
                <?php for ( $i = 1; $i <= 12; $i++ ) : ?>
                    <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $race_count ); ?>><?php echo esc_html( $i ); ?></option>
                <?php endfor; ?>
            </select>
        </p>
        <?php
    }

}

/**
 * Event banner widget with countdown and action buttons.
 */
class RSM_Event_Banner_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'rsm_event_banner_widget',
            __( 'RS Manager: Event Banner', 'race-series-manager' ),
            array(
                'description' => __( 'Display a wide banner with event countdown and action buttons.', 'race-series-manager' ),
            )
        );
    }

    /**
     * Render widget output.
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        $event_id = isset( $instance['event_id'] ) ? absint( $instance['event_id'] ) : 0;
        $title    = isset( $instance['title'] ) ? $instance['title'] : '';

        echo $args['before_widget'];

        if ( ! empty( $instance['heading'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['heading'] ) . $args['after_title'];
        }

        echo rsm_get_event_banner_markup( $event_id, $title );

        echo $args['after_widget'];
    }

    /**
     * Save settings.
     *
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['heading']  = isset( $new_instance['heading'] ) ? sanitize_text_field( $new_instance['heading'] ) : '';
        $instance['title']    = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['event_id'] = isset( $new_instance['event_id'] ) ? absint( $new_instance['event_id'] ) : 0;

        return $instance;
    }

    /**
     * Render form in admin.
     *
     * @param array $instance
     */
    public function form( $instance ) {
        $heading  = isset( $instance['heading'] ) ? $instance['heading'] : __( 'Featured event', 'race-series-manager' );
        $title    = isset( $instance['title'] ) ? $instance['title'] : '';
        $event_id = isset( $instance['event_id'] ) ? absint( $instance['event_id'] ) : 0;

        $events = get_posts(
            array(
                'post_type'      => 'cmt_event',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
            )
        );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'heading' ) ); ?>"><?php esc_html_e( 'Widget title:', 'race-series-manager' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'heading' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'heading' ) ); ?>" type="text" value="<?php echo esc_attr( $heading ); ?>" />
            <small><?php esc_html_e( 'Optional heading shown above the banner.', 'race-series-manager' ); ?></small>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Banner title:', 'race-series-manager' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <small><?php esc_html_e( 'Defaults to the event title if left empty.', 'race-series-manager' ); ?></small>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'event_id' ) ); ?>"><?php esc_html_e( 'Event:', 'race-series-manager' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'event_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'event_id' ) ); ?>">
                <option value="0" <?php selected( 0, $event_id ); ?>><?php esc_html_e( 'Select an event', 'race-series-manager' ); ?></option>
                <?php foreach ( $events as $event ) : ?>
                    <option value="<?php echo esc_attr( $event->ID ); ?>" <?php selected( $event->ID, $event_id ); ?>>
                        <?php echo esc_html( get_the_title( $event ) ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }
}

/**
 * Register the widget.
 */
function rsm_register_widgets() {
    register_widget( 'RSM_Race_Showcase_Widget' );
    register_widget( 'RSM_Event_Banner_Widget' );
}
add_action( 'widgets_init', 'rsm_register_widgets' );
