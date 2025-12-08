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
 * Gather Dompdf autoload and class availability info for diagnostics.
 */
function rsm_get_dompdf_status_data() {
    $autoload_path      = RSM_PLUGIN_DIR . 'lib/dompdf/autoload.inc.php';
    $autoload_relative  = '/lib/dompdf/autoload.inc.php';
    $autoload_exists    = file_exists( $autoload_path );
    $autoload_readable  = is_readable( $autoload_path );
    $dompdf_loaded      = class_exists( '\\Dompdf\\Dompdf', false );
    $loaded_from_bundle = false;

    if ( $autoload_exists && $autoload_readable && ! $dompdf_loaded ) {
        require_once $autoload_path;
        $dompdf_loaded      = class_exists( '\\Dompdf\\Dompdf', false );
        $loaded_from_bundle = $dompdf_loaded;
    }

    $version = defined( 'DOMPDF_VERSION' ) ? DOMPDF_VERSION : '';

    if ( ! $autoload_exists ) {
        $message = sprintf(
            /* translators: %s: plugin-relative Dompdf autoload path */
            esc_html__( 'Autoload file missing at %s. Upload the Dompdf folder bundled with the plugin.', 'race-series-manager' ),
            esc_html( $autoload_relative )
        );
    } elseif ( ! $autoload_readable ) {
        $message = sprintf(
            /* translators: %s: plugin-relative Dompdf autoload path */
            esc_html__( 'Autoload file exists but is not readable (%s). Check file permissions on the server.', 'race-series-manager' ),
            esc_html( $autoload_relative )
        );
    } elseif ( ! $dompdf_loaded ) {
        $message = esc_html__( 'Autoload file loaded but Dompdf classes are still unavailable. Reinstall the Dompdf library.', 'race-series-manager' );
    } else {
        /* translators: %s: detected Dompdf version */
        $message = $version
            ? sprintf( esc_html__( 'Dompdf is available (version %s).', 'race-series-manager' ), esc_html( $version ) )
            : esc_html__( 'Dompdf is available.', 'race-series-manager' );
    }

    return array(
        'autoload_exists'   => $autoload_exists,
        'autoload_readable' => $autoload_readable,
        'dompdf_loaded'     => $dompdf_loaded,
        'loaded_from_bundle' => $loaded_from_bundle,
        'version'           => $version,
        'message'           => $message,
        'autoload_relative' => $autoload_relative,
    );
}

/**
 * Render a diagnostics panel highlighting Dompdf availability.
 */
function rsm_render_dompdf_status_panel() {
    $status = rsm_get_dompdf_status_data();
    ?>
    <div class="rsm-diagnostics">
        <h2><?php esc_html_e( 'PDF Generator Status', 'race-series-manager' ); ?></h2>
        <p><?php esc_html_e( 'Use this quick check to confirm the bundled Dompdf library is installed and readable for race booklets.', 'race-series-manager' ); ?></p>
        <table class="widefat striped">
            <tbody>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Autoload file', 'race-series-manager' ); ?></th>
                    <td><?php echo $status['autoload_exists'] ? esc_html__( 'Found', 'race-series-manager' ) : esc_html__( 'Missing', 'race-series-manager' ); ?> (<?php echo esc_html( $status['autoload_relative'] ); ?>)</td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Readable', 'race-series-manager' ); ?></th>
                    <td><?php echo $status['autoload_readable'] ? esc_html__( 'Yes', 'race-series-manager' ) : esc_html__( 'No', 'race-series-manager' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Dompdf classes loaded', 'race-series-manager' ); ?></th>
                    <td>
                        <?php
                        if ( $status['dompdf_loaded'] ) {
                            echo esc_html__( 'Loaded', 'race-series-manager' );
                            if ( $status['version'] ) {
                                echo ' (' . esc_html( $status['version'] ) . ')';
                            }

                            if ( $status['loaded_from_bundle'] ) {
                                echo ' — ' . esc_html__( 'Loaded from bundled library', 'race-series-manager' );
                            }
                        } else {
                            esc_html_e( 'Unavailable', 'race-series-manager' );
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <p><?php echo esc_html( $status['message'] ); ?></p>
    </div>
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
        <?php rsm_render_dompdf_status_panel(); ?>
    </div>
    <?php
}
