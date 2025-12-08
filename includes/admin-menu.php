<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the main RS Manager menu to group plugin post types.
 */
function rsm_register_admin_menu() {
    add_menu_page(
        esc_html__( 'RS Manager', 'race-series-manager' ),
        esc_html__( 'RS Manager', 'race-series-manager' ),
        'edit_posts',
        'rsm-manager',
        'rsm_render_admin_home',
        'dashicons-flag',
        6
    );
}
add_action( 'admin_menu', 'rsm_register_admin_menu' );

/**
 * Remove the default submenu that duplicates the parent menu entry.
 */
function rsm_remove_parent_submenu_duplicate() {
    remove_submenu_page( 'rsm-manager', 'rsm-manager' );
}
add_action( 'admin_menu', 'rsm_remove_parent_submenu_duplicate', 20 );

/**
 * Add submenus for Events, Races, and Results under the RS Manager parent.
 */
function rsm_register_content_submenus() {
    // Keep the dashboard/landing page accessible as the first submenu item.
    add_submenu_page(
        'rsm-manager',
        esc_html__( 'RS Manager', 'race-series-manager' ),
        esc_html__( 'Dashboard', 'race-series-manager' ),
        'edit_posts',
        'rsm-manager',
        'rsm_render_admin_home'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Events', 'race-series-manager' ),
        esc_html__( 'All Events', 'race-series-manager' ),
        'edit_posts',
        'edit.php?post_type=cmt_event'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Add New Event', 'race-series-manager' ),
        esc_html__( 'Add New Event', 'race-series-manager' ),
        'edit_posts',
        'post-new.php?post_type=cmt_event'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Races', 'race-series-manager' ),
        esc_html__( 'All Races', 'race-series-manager' ),
        'edit_posts',
        'edit.php?post_type=cmt_race'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Add New Race', 'race-series-manager' ),
        esc_html__( 'Add New Race', 'race-series-manager' ),
        'edit_posts',
        'post-new.php?post_type=cmt_race'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Results', 'race-series-manager' ),
        esc_html__( 'All Results', 'race-series-manager' ),
        'edit_posts',
        'edit.php?post_type=cmt_result'
    );

    add_submenu_page(
        'rsm-manager',
        esc_html__( 'Add New Result', 'race-series-manager' ),
        esc_html__( 'Add New Result', 'race-series-manager' ),
        'edit_posts',
        'post-new.php?post_type=cmt_result'
    );
}
add_action( 'admin_menu', 'rsm_register_content_submenus', 11 );

/**
 * Enqueue dashboard-only styles and scripts.
 *
 * @param string $hook The current admin page hook.
 */
function rsm_enqueue_dashboard_assets( $hook ) {
    if ( 'toplevel_page_rsm-manager' !== $hook ) {
        return;
    }

    wp_enqueue_style(
        'rsm-admin-settings',
        RSM_PLUGIN_URL . 'assets/css/rsm-admin-settings.css',
        array(),
        RSM_PLUGIN_VERSION
    );

    wp_enqueue_style(
        'rsm-admin-dashboard',
        RSM_PLUGIN_URL . 'assets/css/rsm-admin-dashboard.css',
        array( 'rsm-admin-settings' ),
        RSM_PLUGIN_VERSION
    );

    wp_enqueue_script(
        'rsm-admin-dashboard',
        RSM_PLUGIN_URL . 'assets/js/rsm-admin-dashboard.js',
        array(),
        RSM_PLUGIN_VERSION,
        true
    );
}
add_action( 'admin_enqueue_scripts', 'rsm_enqueue_dashboard_assets' );

/**
 * Render the RS Manager dashboard with tabbed cards.
 */
function rsm_render_admin_home() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }

    $events_url   = admin_url( 'edit.php?post_type=cmt_event' );
    $races_url    = admin_url( 'edit.php?post_type=cmt_race' );
    $results_url  = admin_url( 'edit.php?post_type=cmt_result' );
    $settings_url = admin_url( 'admin.php?page=rsm-settings' );
    $help_url     = admin_url( 'admin.php?page=rsm-help' );

    $event_counts  = wp_count_posts( 'cmt_event' );
    $race_counts   = wp_count_posts( 'cmt_race' );
    $result_counts = wp_count_posts( 'cmt_result' );

    $dompdf_status = function_exists( 'rsm_get_dompdf_status_data' ) ? rsm_get_dompdf_status_data() : array();
    ?>
    <div class="wrap rsm-dashboard-wrap">
        <h1><?php esc_html_e( 'RS Manager Dashboard', 'race-series-manager' ); ?></h1>
        <p class="description"><?php esc_html_e( 'Manage events, races, results, shortcodes, and tools from one place.', 'race-series-manager' ); ?></p>

        <div class="rsm-dashboard-tabs" role="tablist">
            <button type="button" class="rsm-tab-button is-active" role="tab" aria-selected="true" data-rsm-tab="overview"><?php esc_html_e( 'Overview', 'race-series-manager' ); ?></button>
            <button type="button" class="rsm-tab-button" role="tab" aria-selected="false" data-rsm-tab="shortcodes"><?php esc_html_e( 'Shortcodes', 'race-series-manager' ); ?></button>
            <button type="button" class="rsm-tab-button" role="tab" aria-selected="false" data-rsm-tab="tools"><?php esc_html_e( 'Settings & Tools', 'race-series-manager' ); ?></button>
            <button type="button" class="rsm-tab-button" role="tab" aria-selected="false" data-rsm-tab="help"><?php esc_html_e( 'Help', 'race-series-manager' ); ?></button>
        </div>

        <div class="rsm-dashboard-panels">
            <section id="rsm-tab-overview" class="rsm-dashboard-panel is-active" role="tabpanel">
                <div class="rsm-dashboard-grid">
                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Content', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'At a glance', 'race-series-manager' ); ?></h2>
                        </div>
                        <div class="rsm-stat-grid">
                            <div class="rsm-stat">
                                <div class="rsm-stat-value"><?php echo isset( $event_counts->publish ) ? intval( $event_counts->publish ) : 0; ?></div>
                                <div class="rsm-stat-label"><?php esc_html_e( 'Published events', 'race-series-manager' ); ?></div>
                            </div>
                            <div class="rsm-stat">
                                <div class="rsm-stat-value"><?php echo isset( $race_counts->publish ) ? intval( $race_counts->publish ) : 0; ?></div>
                                <div class="rsm-stat-label"><?php esc_html_e( 'Published races', 'race-series-manager' ); ?></div>
                            </div>
                            <div class="rsm-stat">
                                <div class="rsm-stat-value"><?php echo isset( $result_counts->publish ) ? intval( $result_counts->publish ) : 0; ?></div>
                                <div class="rsm-stat-label"><?php esc_html_e( 'Published results', 'race-series-manager' ); ?></div>
                            </div>
                        </div>
                        <div class="rsm-card-actions">
                            <a class="button button-primary" href="<?php echo esc_url( $events_url ); ?>"><?php esc_html_e( 'Manage events', 'race-series-manager' ); ?></a>
                            <a class="button" href="<?php echo esc_url( $races_url ); ?>"><?php esc_html_e( 'Manage races', 'race-series-manager' ); ?></a>
                            <a class="button" href="<?php echo esc_url( $results_url ); ?>"><?php esc_html_e( 'Manage results', 'race-series-manager' ); ?></a>
                        </div>
                    </div>

                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Shortcuts', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Create content fast', 'race-series-manager' ); ?></h2>
                        </div>
                        <ul class="rsm-list">
                            <li><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=cmt_event' ) ); ?>"><?php esc_html_e( 'Add new Event', 'race-series-manager' ); ?></a></li>
                            <li><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=cmt_race' ) ); ?>"><?php esc_html_e( 'Add new Race', 'race-series-manager' ); ?></a></li>
                            <li><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=cmt_result' ) ); ?>"><?php esc_html_e( 'Add new Result', 'race-series-manager' ); ?></a></li>
                            <li><a href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Open settings', 'race-series-manager' ); ?></a></li>
                            <li><a href="<?php echo esc_url( $help_url ); ?>"><?php esc_html_e( 'Open help', 'race-series-manager' ); ?></a></li>
                        </ul>
                    </div>

                    <div class="rsm-card rsm-card-highlight">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Widgets & banners', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Front page tools', 'race-series-manager' ); ?></h2>
                        </div>
                        <p><?php esc_html_e( 'Use the race showcase and event banner widgets to highlight your schedule anywhere on the site.', 'race-series-manager' ); ?></p>
                        <ul class="rsm-list">
                            <li><?php esc_html_e( 'Race showcase widget or [rsm_race_showcase] shortcode with event, count, and optional title.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Event banner widget or [rsm_event_banner] shortcode for countdowns and action buttons.', 'race-series-manager' ); ?></li>
                        </ul>
                        <p class="rsm-note"><?php esc_html_e( 'Shortcode boxes on Event and Race edit screens include ready-to-copy embeds.', 'race-series-manager' ); ?></p>
                    </div>
                </div>
            </section>

            <section id="rsm-tab-shortcodes" class="rsm-dashboard-panel" role="tabpanel" aria-hidden="true">
                <div class="rsm-dashboard-grid">
                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Displays', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Embed shortcodes', 'race-series-manager' ); ?></h2>
                        </div>
                        <ul class="rsm-code-list">
                            <li><code>[rsm_event_overview event_slug="my-event" show_excerpt="true"]</code><span><?php esc_html_e( 'Show one event with its races, action buttons, and optional excerpts. Use event_id as an alternative.', 'race-series-manager' ); ?></span></li>
                            <li><code>[rsm_event_link id="123"]</code><span><?php esc_html_e( 'Output a link to a specific event.', 'race-series-manager' ); ?></span></li>
                            <li><code>[rsm_race_link id="456"]</code><span><?php esc_html_e( 'Output a link to a specific race.', 'race-series-manager' ); ?></span></li>
                            <li><code>[rsm_race_showcase event="123" count="4" title="Featured races"]</code><span><?php esc_html_e( 'Embed the race showcase grid used by the widget.', 'race-series-manager' ); ?></span></li>
                            <li><code>[rsm_event_banner event="123" title="Race weekend"]</code><span><?php esc_html_e( 'Render the countdown banner with Registration / Participants / Live buttons.', 'race-series-manager' ); ?></span></li>
                        </ul>
                        <p class="rsm-note"><?php esc_html_e( 'Each Event or Race edit screen also includes copy-ready shortcode buttons.', 'race-series-manager' ); ?></p>
                    </div>

                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Ordering & cloning', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Quick tips', 'race-series-manager' ); ?></h2>
                        </div>
                        <ul class="rsm-list">
                            <li><?php esc_html_e( 'Use the ID and Order columns on Events, Races, and Results lists to sort items.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Set the Order value via Page Attributes to control manual ordering.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Clone actions duplicate Events or Races (including metadata) as drafts.', 'race-series-manager' ); ?></li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="rsm-tab-tools" class="rsm-dashboard-panel" role="tabpanel" aria-hidden="true">
                <div class="rsm-dashboard-grid">
                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Configuration', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Settings overview', 'race-series-manager' ); ?></h2>
                        </div>
                        <ul class="rsm-list">
                            <li><?php esc_html_e( 'Customize event overview button labels and choose whether links open in a new tab.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Toggle event overview excerpts on or off.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Pick the light or dark theme for plugin styling.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Set event date, time, and optional banner date label on each event for countdowns.', 'race-series-manager' ); ?></li>
                        </ul>
                        <div class="rsm-card-actions">
                            <a class="button button-primary" href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Open full settings', 'race-series-manager' ); ?></a>
                        </div>
                    </div>

                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Exports', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Backup your setup', 'race-series-manager' ); ?></h2>
                        </div>
                        <div class="rsm-export-import">
                            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                                <?php wp_nonce_field( 'rsm_export_settings' ); ?>
                                <input type="hidden" name="action" value="rsm_export_settings" />
                                <?php submit_button( esc_html__( 'Export settings', 'race-series-manager' ), 'secondary', 'submit', false ); ?>
                            </form>
                            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                                <?php wp_nonce_field( 'rsm_export_data' ); ?>
                                <input type="hidden" name="action" value="rsm_export_data" />
                                <?php submit_button( esc_html__( 'Export all data', 'race-series-manager' ), 'primary', 'submit', false ); ?>
                            </form>
                        </div>
                    </div>

                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Imports', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Restore from a file', 'race-series-manager' ); ?></h2>
                        </div>
                        <div class="rsm-export-import">
                            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                                <?php wp_nonce_field( 'rsm_import_settings' ); ?>
                                <input type="hidden" name="action" value="rsm_import_settings" />
                                <label for="rsm_settings_import_dashboard" class="screen-reader-text"><?php esc_html_e( 'Import settings file', 'race-series-manager' ); ?></label>
                                <input type="file" name="rsm_settings_import" id="rsm_settings_import_dashboard" accept="application/json" />
                                <?php submit_button( esc_html__( 'Import settings', 'race-series-manager' ), 'secondary', 'submit', false ); ?>
                            </form>
                            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                                <?php wp_nonce_field( 'rsm_import_data' ); ?>
                                <input type="hidden" name="action" value="rsm_import_data" />
                                <label for="rsm_data_import_dashboard" class="screen-reader-text"><?php esc_html_e( 'Import full data file', 'race-series-manager' ); ?></label>
                                <input type="file" name="rsm_data_import" id="rsm_data_import_dashboard" accept="application/json" />
                                <?php submit_button( esc_html__( 'Import all data', 'race-series-manager' ), 'primary', 'submit', false ); ?>
                            </form>
                        </div>
                    </div>

                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'PDF generator', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Dompdf status', 'race-series-manager' ); ?></h2>
                        </div>
                        <?php if ( ! empty( $dompdf_status ) ) : ?>
                            <ul class="rsm-list">
                                <li>
                                    <strong><?php esc_html_e( 'Autoloader path:', 'race-series-manager' ); ?></strong>
                                    <?php echo $dompdf_status['autoload_exists'] ? esc_html__( 'Found', 'race-series-manager' ) : esc_html__( 'Missing', 'race-series-manager' ); ?>
                                </li>
                                <li>
                                    <strong><?php esc_html_e( 'Readable:', 'race-series-manager' ); ?></strong>
                                    <?php echo $dompdf_status['autoload_readable'] ? esc_html__( 'Yes', 'race-series-manager' ) : esc_html__( 'No', 'race-series-manager' ); ?>
                                </li>
                                <li>
                                    <strong><?php esc_html_e( 'Loaded:', 'race-series-manager' ); ?></strong>
                                    <?php echo $dompdf_status['dompdf_loaded'] ? esc_html__( 'Yes', 'race-series-manager' ) : esc_html__( 'No', 'race-series-manager' ); ?>
                                </li>
                                <?php if ( ! empty( $dompdf_status['version'] ) ) : ?>
                                    <li>
                                        <strong><?php esc_html_e( 'Version:', 'race-series-manager' ); ?></strong>
                                        <?php echo esc_html( $dompdf_status['version'] ); ?>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <strong><?php esc_html_e( 'Note:', 'race-series-manager' ); ?></strong>
                                    <?php echo esc_html( $dompdf_status['message'] ); ?>
                                </li>
                            </ul>
                        <?php else : ?>
                            <p class="rsm-note"><?php esc_html_e( 'Status data is unavailable right now.', 'race-series-manager' ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section id="rsm-tab-help" class="rsm-dashboard-panel" role="tabpanel" aria-hidden="true">
                <div class="rsm-dashboard-grid">
                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Editing tips', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Events & races', 'race-series-manager' ); ?></h2>
                        </div>
                        <ul class="rsm-list">
                            <li><?php esc_html_e( 'Attach races to their parent event and publish when ready.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Set race hero images to show or hide the featured banner per race.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Provide start and finish Google Maps links so labels become clickable.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Add event date/time and an optional banner date label for countdown banners.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Upload route map and elevation images for printable route details.', 'race-series-manager' ); ?></li>
                        </ul>
                    </div>

                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Frontend behavior', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'What visitors see', 'race-series-manager' ); ?></h2>
                        </div>
                        <ul class="rsm-list">
                            <li><?php esc_html_e( 'Race galleries open in a keyboard-friendly lightbox with navigation arrows.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Aid-station and route map print buttons offer quick printable views.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Event banners hide the countdown automatically after race day passes.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'Race lists on events appear as prominent buttons for easy navigation.', 'race-series-manager' ); ?></li>
                        </ul>
                    </div>

                    <div class="rsm-card">
                        <div class="rsm-card-header">
                            <span class="rsm-card-kicker"><?php esc_html_e( 'Troubleshooting', 'race-series-manager' ); ?></span>
                            <h2><?php esc_html_e( 'Common fixes', 'race-series-manager' ); ?></h2>
                        </div>
                        <ul class="rsm-list">
                            <li><?php esc_html_e( 'If a shortcode is blank, confirm the slug/ID is correct and published.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'If buttons disappear, fill in registration/participants/live URLs on the event.', 'race-series-manager' ); ?></li>
                            <li><?php esc_html_e( 'If PDF generation fails, confirm /lib/dompdf/autoload.inc.php exists and is readable.', 'race-series-manager' ); ?></li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php
}
