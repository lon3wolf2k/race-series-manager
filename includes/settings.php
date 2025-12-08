<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Default plugin settings.
 */
function rsm_get_settings_defaults() {
    return array(
        'overview_registration_label' => esc_html__( 'Registration', 'race-series-manager' ),
        'overview_participants_label' => esc_html__( 'Participants', 'race-series-manager' ),
        'overview_live_label'         => esc_html__( 'Live', 'race-series-manager' ),
        'overview_results_label'      => esc_html__( 'Results', 'race-series-manager' ),
        'overview_show_excerpt'       => 1,
    );
}

/**
 * Retrieve settings merged with defaults.
 */
function rsm_get_settings() {
    $saved    = get_option( 'rsm_settings', array() );
    $defaults = rsm_get_settings_defaults();

    if ( ! is_array( $saved ) ) {
        $saved = array();
    }

    return wp_parse_args( $saved, $defaults );
}

/**
 * Sanitize settings before saving.
 */
function rsm_sanitize_settings( $input ) {
    $output   = array();
    $defaults = rsm_get_settings_defaults();

    $output['overview_registration_label'] = isset( $input['overview_registration_label'] )
        ? sanitize_text_field( $input['overview_registration_label'] )
        : $defaults['overview_registration_label'];

    $output['overview_participants_label'] = isset( $input['overview_participants_label'] )
        ? sanitize_text_field( $input['overview_participants_label'] )
        : $defaults['overview_participants_label'];

    $output['overview_live_label'] = isset( $input['overview_live_label'] )
        ? sanitize_text_field( $input['overview_live_label'] )
        : $defaults['overview_live_label'];

    $output['overview_results_label'] = isset( $input['overview_results_label'] )
        ? sanitize_text_field( $input['overview_results_label'] )
        : $defaults['overview_results_label'];

    $output['overview_show_excerpt'] = empty( $input['overview_show_excerpt'] ) ? 0 : 1;

    return $output;
}

/**
 * Register plugin settings.
 */
function rsm_register_settings() {
    register_setting(
        'rsm_settings',
        'rsm_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'rsm_sanitize_settings',
            'default'           => rsm_get_settings_defaults(),
        )
    );

    add_settings_section(
        'rsm_overview_settings',
        esc_html__( 'Event overview shortcode', 'race-series-manager' ),
        function() {
            echo '<p>' . esc_html__( 'Configure how the event overview shortcode renders its buttons and description.', 'race-series-manager' ) . '</p>';
        },
        'rsm-settings'
    );

    add_settings_field(
        'overview_registration_label',
        esc_html__( 'Registration button label', 'race-series-manager' ),
        'rsm_render_text_setting_field',
        'rsm-settings',
        'rsm_overview_settings',
        array(
            'name'        => 'overview_registration_label',
            'placeholder' => esc_html__( 'Registration', 'race-series-manager' ),
        )
    );

    add_settings_field(
        'overview_participants_label',
        esc_html__( 'Participants button label', 'race-series-manager' ),
        'rsm_render_text_setting_field',
        'rsm-settings',
        'rsm_overview_settings',
        array(
            'name'        => 'overview_participants_label',
            'placeholder' => esc_html__( 'Participants', 'race-series-manager' ),
        )
    );

    add_settings_field(
        'overview_live_label',
        esc_html__( 'Live button label', 'race-series-manager' ),
        'rsm_render_text_setting_field',
        'rsm-settings',
        'rsm_overview_settings',
        array(
            'name'        => 'overview_live_label',
            'placeholder' => esc_html__( 'Live', 'race-series-manager' ),
        )
    );

    add_settings_field(
        'overview_results_label',
        esc_html__( 'Results button label', 'race-series-manager' ),
        'rsm_render_text_setting_field',
        'rsm-settings',
        'rsm_overview_settings',
        array(
            'name'        => 'overview_results_label',
            'placeholder' => esc_html__( 'Results', 'race-series-manager' ),
        )
    );

    add_settings_field(
        'overview_show_excerpt',
        esc_html__( 'Show event excerpt above table', 'race-series-manager' ),
        'rsm_render_checkbox_setting_field',
        'rsm-settings',
        'rsm_overview_settings',
        array(
            'name'        => 'overview_show_excerpt',
            'description' => esc_html__( 'Display the event excerpt when present to introduce the race list.', 'race-series-manager' ),
        )
    );
}
add_action( 'admin_init', 'rsm_register_settings' );

/**
 * Render text input field for settings.
 */
function rsm_render_text_setting_field( $args ) {
    $settings = rsm_get_settings();
    $name     = isset( $args['name'] ) ? $args['name'] : '';

    if ( ! $name ) {
        return;
    }

    $value       = isset( $settings[ $name ] ) ? $settings[ $name ] : '';
    $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
    ?>
    <input type="text"
           class="regular-text"
           name="rsm_settings[<?php echo esc_attr( $name ); ?>]"
           id="rsm_settings_<?php echo esc_attr( $name ); ?>"
           value="<?php echo esc_attr( $value ); ?>"
           placeholder="<?php echo esc_attr( $placeholder ); ?>" />
    <?php
}

/**
 * Render checkbox field for settings.
 */
function rsm_render_checkbox_setting_field( $args ) {
    $settings = rsm_get_settings();
    $name     = isset( $args['name'] ) ? $args['name'] : '';

    if ( ! $name ) {
        return;
    }

    $checked     = ! empty( $settings[ $name ] );
    $description = isset( $args['description'] ) ? $args['description'] : '';
    ?>
    <label for="rsm_settings_<?php echo esc_attr( $name ); ?>">
        <input type="checkbox"
               name="rsm_settings[<?php echo esc_attr( $name ); ?>]"
               id="rsm_settings_<?php echo esc_attr( $name ); ?>"
               value="1" <?php checked( $checked ); ?> />
        <?php echo esc_html( $description ); ?>
    </label>
    <?php
}

/**
 * Register the RS Manager settings submenu.
 */
function rsm_register_settings_submenu() {
    add_submenu_page(
        'rsm-manager',
        esc_html__( 'RS Manager Settings', 'race-series-manager' ),
        esc_html__( 'Settings', 'race-series-manager' ),
        'manage_options',
        'rsm-settings',
        'rsm_render_settings_page'
    );
}
add_action( 'admin_menu', 'rsm_register_settings_submenu' );

/**
 * Render the settings page.
 */
function rsm_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'RS Manager Settings', 'race-series-manager' ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'rsm_settings' );
            do_settings_sections( 'rsm-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
