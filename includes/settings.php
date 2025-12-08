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
        'overview_action_new_tab'     => 1,
        'theme'                       => 'light',
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

    $output['overview_action_new_tab'] = empty( $input['overview_action_new_tab'] ) ? 0 : 1;

    $output['theme'] = ( isset( $input['theme'] ) && 'dark' === $input['theme'] ) ? 'dark' : 'light';

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

    add_settings_field(
        'overview_action_new_tab',
        esc_html__( 'Open action links in new tab', 'race-series-manager' ),
        'rsm_render_checkbox_setting_field',
        'rsm-settings',
        'rsm_overview_settings',
        array(
            'name'        => 'overview_action_new_tab',
            'description' => esc_html__( 'Open event-level action buttons (registration, participants, live, results) in a new tab.', 'race-series-manager' ),
        )
    );

    add_settings_section(
        'rsm_display_settings',
        esc_html__( 'Display options', 'race-series-manager' ),
        function() {
            echo '<p>' . esc_html__( 'Choose whether plugin elements render with the light or dark theme.', 'race-series-manager' ) . '</p>';
        },
        'rsm-settings'
    );

    add_settings_field(
        'theme',
        esc_html__( 'Plugin theme', 'race-series-manager' ),
        'rsm_render_select_setting_field',
        'rsm-settings',
        'rsm_display_settings',
        array(
            'name'    => 'theme',
            'options' => array(
                'light' => esc_html__( 'Light', 'race-series-manager' ),
                'dark'  => esc_html__( 'Dark', 'race-series-manager' ),
            ),
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
 * Render select field for settings.
 */
function rsm_render_select_setting_field( $args ) {
    $settings = rsm_get_settings();
    $name     = isset( $args['name'] ) ? $args['name'] : '';
    $options  = isset( $args['options'] ) ? (array) $args['options'] : array();

    if ( ! $name || empty( $options ) ) {
        return;
    }

    $value = isset( $settings[ $name ] ) ? $settings[ $name ] : '';
    ?>
    <select name="rsm_settings[<?php echo esc_attr( $name ); ?>]" id="rsm_settings_<?php echo esc_attr( $name ); ?>">
        <?php foreach ( $options as $option_value => $label ) : ?>
            <option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
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
add_action( 'admin_menu', 'rsm_register_settings_submenu', 30 );

/**
 * Enqueue admin-only styles for the settings screen layout.
 *
 * @param string $hook Current admin page hook suffix.
 */
function rsm_enqueue_settings_admin_assets( $hook ) {
    if ( 'rs_manager_page_rsm-settings' !== $hook ) {
        return;
    }

    wp_enqueue_style(
        'rsm-admin-settings',
        RSM_PLUGIN_URL . 'assets/css/rsm-admin-settings.css',
        array(),
        RSM_PLUGIN_VERSION
    );
}
add_action( 'admin_enqueue_scripts', 'rsm_enqueue_settings_admin_assets' );

/**
 * Render the settings page.
 */
function rsm_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap rsm-settings-wrap">
        <h1><?php esc_html_e( 'RS Manager Settings', 'race-series-manager' ); ?></h1>
        <?php settings_errors( 'rsm_settings' ); ?>
        <div class="rsm-settings-grid">
            <div class="rsm-settings-main">
                <form action="options.php" method="post" class="rsm-settings-card">
                    <h2 class="title"><?php esc_html_e( 'General options', 'race-series-manager' ); ?></h2>
                    <?php
                    settings_fields( 'rsm_settings' );
                    do_settings_sections( 'rsm-settings' );
                    submit_button();
                    ?>
                </form>
            </div>
            <div class="rsm-settings-actions">
                <div class="rsm-settings-card">
                    <h2 class="title"><?php esc_html_e( 'Export / Import settings', 'race-series-manager' ); ?></h2>
                    <p><?php esc_html_e( 'Back up your RS Manager settings or import them from another site.', 'race-series-manager' ); ?></p>
                    <div class="rsm-export-import">
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <?php wp_nonce_field( 'rsm_export_settings' ); ?>
                            <input type="hidden" name="action" value="rsm_export_settings" />
                            <?php submit_button( esc_html__( 'Export settings', 'race-series-manager' ), 'secondary', 'submit', false ); ?>
                        </form>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                            <?php wp_nonce_field( 'rsm_import_settings' ); ?>
                            <input type="hidden" name="action" value="rsm_import_settings" />
                            <label for="rsm_settings_import" class="screen-reader-text"><?php esc_html_e( 'Import settings file', 'race-series-manager' ); ?></label>
                            <input type="file" name="rsm_settings_import" id="rsm_settings_import" accept="application/json" />
                            <?php submit_button( esc_html__( 'Import settings', 'race-series-manager' ), 'secondary', 'submit', false ); ?>
                        </form>
                    </div>
                </div>
                <div class="rsm-settings-card">
                    <h2 class="title"><?php esc_html_e( 'Export / Import content', 'race-series-manager' ); ?></h2>
                    <p><?php esc_html_e( 'Download all Events, Races, Results, and settings as a single JSON file or import them from another site.', 'race-series-manager' ); ?></p>
                    <div class="rsm-export-import">
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <?php wp_nonce_field( 'rsm_export_data' ); ?>
                            <input type="hidden" name="action" value="rsm_export_data" />
                            <?php submit_button( esc_html__( 'Export all data', 'race-series-manager' ), 'primary', 'submit', false ); ?>
                        </form>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                            <?php wp_nonce_field( 'rsm_import_data' ); ?>
                            <input type="hidden" name="action" value="rsm_import_data" />
                            <label for="rsm_data_import" class="screen-reader-text"><?php esc_html_e( 'Import RS Manager export', 'race-series-manager' ); ?></label>
                            <input type="file" name="rsm_data_import" id="rsm_data_import" accept="application/json" />
                            <?php submit_button( esc_html__( 'Import all data', 'race-series-manager' ), 'primary', 'submit', false ); ?>
                        </form>
                    </div>
                </div>
                <div class="rsm-settings-card">
                    <?php rsm_render_dompdf_status_panel(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Handle exporting settings as JSON.
 */
function rsm_handle_settings_export() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to export settings.', 'race-series-manager' ) );
    }

    check_admin_referer( 'rsm_export_settings' );

    $settings  = rsm_get_settings();
    $filename  = 'rsm-settings-' . gmdate( 'Y-m-d' ) . '.json';
    $json_data = wp_json_encode( $settings, JSON_PRETTY_PRINT );

    if ( false === $json_data ) {
        wp_die( esc_html__( 'Unable to export settings.', 'race-series-manager' ) );
    }

    nocache_headers();
    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=' . $filename );
    echo $json_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    exit;
}
add_action( 'admin_post_rsm_export_settings', 'rsm_handle_settings_export' );

/**
 * Handle importing settings from JSON upload.
 */
function rsm_handle_settings_import() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to import settings.', 'race-series-manager' ) );
    }

    check_admin_referer( 'rsm_import_settings' );

    $errors = array();

    if ( empty( $_FILES['rsm_settings_import'] ) || UPLOAD_ERR_OK !== $_FILES['rsm_settings_import']['error'] ) {
        $errors[] = esc_html__( 'Upload failed. Please choose a JSON file to import.', 'race-series-manager' );
    } else {
        $tmp_name = $_FILES['rsm_settings_import']['tmp_name'];
        $contents = file_get_contents( $tmp_name );
        $data     = json_decode( $contents, true );

        if ( null === $data || ! is_array( $data ) ) {
            $errors[] = esc_html__( 'Invalid JSON file. Please export settings from RS Manager and try again.', 'race-series-manager' );
        } else {
            $sanitized = rsm_sanitize_settings( $data );
            update_option( 'rsm_settings', $sanitized );
        }
    }

    if ( empty( $errors ) ) {
        add_settings_error( 'rsm_settings', 'rsm_settings_imported', esc_html__( 'Settings imported successfully.', 'race-series-manager' ), 'updated' );
    } else {
        foreach ( $errors as $error ) {
            add_settings_error( 'rsm_settings', 'rsm_settings_import_error', $error, 'error' );
        }
    }

    set_transient( 'settings_errors', get_settings_errors(), 30 );

    wp_safe_redirect( add_query_arg( 'page', 'rsm-settings', admin_url( 'admin.php' ) ) );
    exit;
}
add_action( 'admin_post_rsm_import_settings', 'rsm_handle_settings_import' );

/**
 * Prepare full data export payload for events, races, results, and settings.
 */
function rsm_build_full_export_payload() {
    $types  = array(
        'events'  => 'cmt_event',
        'races'   => 'cmt_race',
        'results' => 'cmt_result',
    );
    $export = array(
        'type'      => 'rsm_export',
        'version'   => 1,
        'generated' => gmdate( 'c' ),
        'settings'  => rsm_get_settings(),
    );

    foreach ( $types as $key => $post_type ) {
        $posts = get_posts(
            array(
                'post_type'      => $post_type,
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            )
        );

        $export[ $key ] = array();

        foreach ( $posts as $post ) {
            $meta = array();
            $raw  = get_post_custom( $post->ID );

            foreach ( $raw as $meta_key => $values ) {
                $meta[ $meta_key ] = array_map( 'maybe_unserialize', $values );
            }

            $export[ $key ][] = array(
                'post' => array(
                    'ID'             => $post->ID,
                    'post_title'     => $post->post_title,
                    'post_content'   => $post->post_content,
                    'post_excerpt'   => $post->post_excerpt,
                    'post_status'    => $post->post_status,
                    'post_name'      => $post->post_name,
                    'menu_order'     => $post->menu_order,
                    'post_date'      => $post->post_date,
                    'post_date_gmt'  => $post->post_date_gmt,
                    'post_author'    => $post->post_author,
                    'comment_status' => $post->comment_status,
                ),
                'meta' => $meta,
            );
        }
    }

    return $export;
}

/**
 * Handle exporting full RS Manager data.
 */
function rsm_handle_data_export() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to export data.', 'race-series-manager' ) );
    }

    check_admin_referer( 'rsm_export_data' );

    $payload  = rsm_build_full_export_payload();
    $filename = 'rsm-data-' . gmdate( 'Y-m-d' ) . '.json';
    $json     = wp_json_encode( $payload, JSON_PRETTY_PRINT );

    if ( false === $json ) {
        wp_die( esc_html__( 'Unable to export data.', 'race-series-manager' ) );
    }

    nocache_headers();
    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=' . $filename );
    echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    exit;
}
add_action( 'admin_post_rsm_export_data', 'rsm_handle_data_export' );

/**
 * Import posts and meta, returning map of old to new IDs.
 *
 * @param array  $items     Exported items for a post type.
 * @param string $post_type Target post type.
 * @param array  $event_map Map of old event IDs to new ones for relationship remapping.
 *
 * @return array
 */
function rsm_import_posts_from_payload( $items, $post_type, $event_map = array() ) {
    $id_map = array();

    if ( empty( $items ) || ! is_array( $items ) ) {
        return $id_map;
    }

    foreach ( $items as $item ) {
        if ( empty( $item['post'] ) || ! is_array( $item['post'] ) ) {
            continue;
        }

        $post_data = wp_parse_args(
            $item['post'],
            array(
                'post_title'     => '',
                'post_content'   => '',
                'post_excerpt'   => '',
                'post_status'    => 'draft',
                'post_name'      => '',
                'menu_order'     => 0,
                'post_date'      => '',
                'post_date_gmt'  => '',
                'post_author'    => get_current_user_id(),
                'comment_status' => 'closed',
            )
        );

        $post_data['post_type'] = $post_type;

        $new_id = wp_insert_post( wp_slash( $post_data ), true );

        if ( is_wp_error( $new_id ) ) {
            continue;
        }

        $old_id = isset( $item['post']['ID'] ) ? absint( $item['post']['ID'] ) : 0;

        if ( $old_id ) {
            $id_map[ $old_id ] = $new_id;
        }

        if ( ! empty( $item['meta'] ) && is_array( $item['meta'] ) ) {
            foreach ( $item['meta'] as $meta_key => $values ) {
                delete_post_meta( $new_id, $meta_key );

                $values = (array) $values;

                foreach ( $values as $value ) {
                    // Remap related event IDs for races and results.
                    if ( in_array( $meta_key, array( '_rsm_race_event_id', '_rsm_res_event_id' ), true ) ) {
                        $old_event_id = absint( $value );
                        if ( $old_event_id && isset( $event_map[ $old_event_id ] ) ) {
                            $value = $event_map[ $old_event_id ];
                        }
                    }

                    add_post_meta( $new_id, $meta_key, maybe_unserialize( $value ) );
                }
            }
        }
    }

    return $id_map;
}

/**
 * Handle importing full RS Manager data export.
 */
function rsm_handle_data_import() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to import data.', 'race-series-manager' ) );
    }

    check_admin_referer( 'rsm_import_data' );

    $errors = array();
    $data   = array();

    if ( empty( $_FILES['rsm_data_import'] ) || UPLOAD_ERR_OK !== $_FILES['rsm_data_import']['error'] ) {
        $errors[] = esc_html__( 'Upload failed. Please choose a JSON file to import.', 'race-series-manager' );
    } else {
        $tmp_name = $_FILES['rsm_data_import']['tmp_name'];
        $content  = file_get_contents( $tmp_name );
        $data     = json_decode( $content, true );

        if ( null === $data || ! is_array( $data ) || empty( $data['type'] ) || 'rsm_export' !== $data['type'] ) {
            $errors[] = esc_html__( 'Invalid JSON file. Please export data from RS Manager and try again.', 'race-series-manager' );
        }
    }

    if ( empty( $errors ) ) {
        if ( ! empty( $data['settings'] ) && is_array( $data['settings'] ) ) {
            $sanitized = rsm_sanitize_settings( $data['settings'] );
            update_option( 'rsm_settings', $sanitized );
        }

        $event_map = rsm_import_posts_from_payload( isset( $data['events'] ) ? $data['events'] : array(), 'cmt_event' );
        rsm_import_posts_from_payload( isset( $data['races'] ) ? $data['races'] : array(), 'cmt_race', $event_map );
        rsm_import_posts_from_payload( isset( $data['results'] ) ? $data['results'] : array(), 'cmt_result', $event_map );

        add_settings_error( 'rsm_settings', 'rsm_data_imported', esc_html__( 'Data imported successfully.', 'race-series-manager' ), 'updated' );
    } else {
        foreach ( $errors as $error ) {
            add_settings_error( 'rsm_settings', 'rsm_data_import_error', $error, 'error' );
        }
    }

    set_transient( 'settings_errors', get_settings_errors(), 30 );

    wp_safe_redirect( add_query_arg( 'page', 'rsm-settings', admin_url( 'admin.php' ) ) );
    exit;
}
add_action( 'admin_post_rsm_import_data', 'rsm_handle_data_import' );
