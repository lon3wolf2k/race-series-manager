<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Determine whether a post's meta should be publicly readable over REST.
 *
 * @param bool        $allowed   Existing allowed value.
 * @param string      $meta_key  Meta key.
 * @param int         $object_id Post ID.
 * @param int         $user_id   Current user ID.
 * @param string|array $cap      Capability name(s).
 * @param array       $caps      Primitive capabilities.
 *
 * @return bool
 */
function rsm_rest_allow_public_meta( $allowed, $meta_key, $object_id, $user_id, $cap, $caps ) {
    $post = get_post( $object_id );

    if ( $post && is_post_publicly_viewable( $post ) ) {
        return true;
    }

    return current_user_can( 'edit_post', $object_id );
}

/**
 * Apply REST visibility preferences to custom post types.
 *
 * @param array  $args      Post type args.
 * @param string $post_type Post type slug.
 *
 * @return array
 */
function rsm_apply_rest_api_visibility_settings( $args, $post_type ) {
    $settings = function_exists( 'rsm_get_settings' ) ? rsm_get_settings() : array();

    $flags = array(
        'cmt_event'  => ! empty( $settings['rest_api_events'] ),
        'cmt_race'   => ! empty( $settings['rest_api_races'] ),
        'cmt_result' => ! empty( $settings['rest_api_results'] ),
    );

    if ( isset( $flags[ $post_type ] ) ) {
        $args['show_in_rest'] = (bool) $flags[ $post_type ];
    }

    return $args;
}
add_filter( 'register_post_type_args', 'rsm_apply_rest_api_visibility_settings', 10, 2 );

/**
 * Register REST-visible meta fields for events and races when enabled.
 */
function rsm_register_rest_api_fields() {
    if ( ! function_exists( 'rsm_get_settings' ) ) {
        return;
    }

    $settings = rsm_get_settings();

    if ( ! empty( $settings['rest_api_events'] ) ) {
        rsm_register_event_rest_meta();
    }

    if ( ! empty( $settings['rest_api_races'] ) ) {
        rsm_register_race_rest_meta();
    }
}
add_action( 'rest_api_init', 'rsm_register_rest_api_fields' );

/**
 * Register REST meta for events.
 */
function rsm_register_event_rest_meta() {
    $meta_fields = array(
        '_rsm_event_announcement'      => array( 'type' => 'string' ),
        '_rsm_event_rules'             => array( 'type' => 'string' ),
        '_rsm_event_schedule'          => array( 'type' => 'string' ),
        '_rsm_event_access'            => array( 'type' => 'string' ),
        '_rsm_event_travel'            => array( 'type' => 'string' ),
        '_rsm_event_logo_id'           => array( 'type' => 'integer' ),
        '_rsm_event_contact'           => array( 'type' => 'string' ),
        '_rsm_event_registration_url'  => array( 'type' => 'string' ),
        '_rsm_event_participants_url'  => array( 'type' => 'string' ),
        '_rsm_event_live_url'          => array( 'type' => 'string' ),
        '_rsm_event_date'              => array( 'type' => 'string' ),
        '_rsm_event_time'              => array( 'type' => 'string' ),
        '_rsm_event_display_date'      => array( 'type' => 'string' ),
    );

    foreach ( $meta_fields as $key => $schema ) {
        register_post_meta(
            'cmt_event',
            $key,
            array(
                'single'        => true,
                'type'          => $schema['type'],
                'show_in_rest'  => array(
                    'schema' => array(
                        'type' => $schema['type'],
                    ),
                ),
                'auth_callback' => 'rsm_rest_allow_public_meta',
            )
        );
    }
}

/**
 * Register REST meta for races.
 */
function rsm_register_race_rest_meta() {
    $meta_fields = array(
        '_rsm_race_event_id'             => array( 'type' => 'integer' ),
        '_rsm_race_distance'             => array( 'type' => 'string' ),
        '_rsm_race_elevation'            => array( 'type' => 'string' ),
        '_rsm_race_date'                 => array( 'type' => 'string' ),
        '_rsm_race_start_time'           => array( 'type' => 'string' ),
        '_rsm_race_start_location'       => array( 'type' => 'string' ),
        '_rsm_race_start_location_link'  => array( 'type' => 'string' ),
        '_rsm_race_finish_location'      => array( 'type' => 'string' ),
        '_rsm_race_finish_location_link' => array( 'type' => 'string' ),
        '_rsm_race_cutoff_hours'         => array( 'type' => 'string' ),
        '_rsm_race_itra_link'            => array( 'type' => 'string' ),
        '_rsm_race_registration_link'    => array( 'type' => 'string' ),
        '_rsm_race_aid_stations'         => array( 'type' => 'string' ),
        '_rsm_race_plotaroute_embed'     => array( 'type' => 'string' ),
        '_rsm_race_route_url'            => array( 'type' => 'string' ),
        '_rsm_race_video_embed'          => array( 'type' => 'string' ),
        '_rsm_race_gallery_ids'          => array(
            'type'   => 'array',
            'schema' => array(
                'type'  => 'array',
                'items' => array(
                    'type' => 'integer',
                ),
            ),
        ),
        '_rsm_race_static_map_id'        => array( 'type' => 'integer' ),
        '_rsm_race_elev_chart_id'        => array( 'type' => 'integer' ),
        '_rsm_race_gpx_file_id'          => array( 'type' => 'integer' ),
        '_rsm_race_show_hero'            => array( 'type' => 'boolean' ),
    );

    foreach ( $meta_fields as $key => $schema ) {
        $schema_definition = isset( $schema['schema'] ) ? $schema['schema'] : array( 'type' => $schema['type'] );

        register_post_meta(
            'cmt_race',
            $key,
            array(
                'single'        => true,
                'type'          => $schema['type'],
                'show_in_rest'  => array(
                    'schema' => $schema_definition,
                ),
                'auth_callback' => 'rsm_rest_allow_public_meta',
            )
        );
    }

    register_rest_field(
        'cmt_race',
        'gpx_file_url',
        array(
            'get_callback' => 'rsm_get_race_gpx_file_url',
            'schema'       => array(
                'description' => __( 'Absolute URL of the GPX file', 'race-series-manager' ),
                'type'        => 'string',
                'format'      => 'uri',
                'context'     => array( 'view', 'edit' ),
            ),
        )
    );
}

/**
 * Return the GPX file URL for a race.
 *
 * @param array $post REST post array.
 *
 * @return string
 */
function rsm_get_race_gpx_file_url( $post ) {
    $race_id     = isset( $post['id'] ) ? absint( $post['id'] ) : 0;
    $attachment  = $race_id ? get_post_meta( $race_id, '_rsm_race_gpx_file_id', true ) : 0;
    $gpx_url     = $attachment ? wp_get_attachment_url( $attachment ) : '';

    return $gpx_url ? $gpx_url : '';
}
