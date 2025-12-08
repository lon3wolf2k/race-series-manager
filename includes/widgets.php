<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
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

        $races = $this->get_races( $event_id, $race_count );

        if ( empty( $races ) ) {
            echo '<p>' . esc_html__( 'No races found for this event yet.', 'race-series-manager' ) . '</p>';
            echo $args['after_widget'];
            return;
        }

        ?>
        <div class="rsm-race-showcase">
            <div class="rsm-race-showcase-grid">
                <?php foreach ( $races as $race ) :
                    $distance       = get_post_meta( $race->ID, '_rsm_race_distance', true );
                    $elevation      = get_post_meta( $race->ID, '_rsm_race_elevation', true );
                    $start_datetime = get_post_meta( $race->ID, '_rsm_race_date', true );
                    $race_date      = $this->format_race_date( $start_datetime );
                    $thumb_url      = $this->get_race_image_url( $race->ID );
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
                                <span class="rsm-race-card__cta"><?php esc_html_e( 'Race details', 'race-series-manager' ); ?></span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php

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

    /**
     * Retrieve races for the selected event.
     *
     * @param int $event_id
     * @param int $limit
     *
     * @return array
     */
    private function get_races( $event_id, $limit ) {
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
     * Format a race date for the badge.
     *
     * @param string $start_datetime
     *
     * @return string
     */
    private function format_race_date( $start_datetime ) {
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
    private function get_race_image_url( $race_id ) {
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
}

/**
 * Register the widget.
 */
function rsm_register_widgets() {
    register_widget( 'RSM_Race_Showcase_Widget' );
}
add_action( 'widgets_init', 'rsm_register_widgets' );
