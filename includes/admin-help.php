<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the RS Manager Help submenu.
 */
function rsm_register_help_submenu() {
    add_submenu_page(
        'rsm-manager',
        esc_html__( 'RS Manager Help', 'race-series-manager' ),
        esc_html__( 'Help', 'race-series-manager' ),
        'edit_posts',
        'rsm-help',
        'rsm_render_help_page'
    );
}
add_action( 'admin_menu', 'rsm_register_help_submenu' );

/**
 * Render the backend help page populated with inline guidance.
 */
function rsm_render_help_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }
    $content  = '<h2>' . esc_html__( 'Getting Started', 'race-series-manager' ) . '</h2>';
    $content .= '<p>' . esc_html__( 'Use the RS Manager menu to create Events and Races, attach races to their parent event, and publish when ready. Page Attributes let you set a numeric “Order” that controls manual ordering in admin lists.', 'race-series-manager' ) . '</p>';

    $content .= '<h2>' . esc_html__( 'Shortcodes', 'race-series-manager' ) . '</h2>';
    $content .= '<ul>';
    $content .= '<li><code>[rsm_event_overview event_slug="my-event" show_excerpt="true"]</code> ' . esc_html__( 'shows an event with its races, action buttons, race dates/times, and optional excerpts. Use event_id as an alternative parameter.', 'race-series-manager' ) . '</li>';
    $content .= '<li><code>[rsm_event_link id="123"]</code> ' . esc_html__( 'outputs a link to a specific event.', 'race-series-manager' ) . '</li>';
    $content .= '<li><code>[rsm_race_link id="456"]</code> ' . esc_html__( 'outputs a link to a specific race.', 'race-series-manager' ) . '</li>';
    $content .= '<li><code>[rsm_race_showcase event="123" count="4" title="Featured races"]</code> ' . esc_html__( 'embeds the race showcase grid used by the widget so you can drop it onto any page.', 'race-series-manager' ) . '</li>';
    $content .= '</ul>';
    $content .= '<p>' . esc_html__( 'Event and Race edit screens include a “Shortcodes” box with copy-to-clipboard buttons you can paste into pages or posts.', 'race-series-manager' ) . '</p>';

    $content .= '<h2>' . esc_html__( 'Settings', 'race-series-manager' ) . '</h2>';
    $content .= '<ul>';
    $content .= '<li>' . esc_html__( 'Customize event overview button labels (Registration, Participants, Live, Results).', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Toggle showing event excerpts in the overview table.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Choose whether action links open in a new tab.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Pick the plugin theme (light or dark) to match your site.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Review the “PDF Generator Status” panel to confirm the bundled Dompdf library is available.', 'race-series-manager' ) . '</li>';
    $content .= '</ul>';

    $content .= '<h2>' . esc_html__( 'Admin Lists & Ordering', 'race-series-manager' ) . '</h2>';
    $content .= '<ul>';
    $content .= '<li>' . esc_html__( 'ID and Order columns appear on Events, Races, and Results list tables and can be sorted.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Set the Order value via Page Attributes when editing a post to control manual ordering.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Clone row actions let you duplicate Events or Races as drafts, including metadata and taxonomies.', 'race-series-manager' ) . '</li>';
    $content .= '</ul>';

    $content .= '<h2>' . esc_html__( 'Race & Event Pages', 'race-series-manager' ) . '</h2>';
    $content .= '<ul>';
    $content .= '<li>' . esc_html__( 'Race galleries open in a lightbox with previous/next navigation and keyboard support.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Use the Hero image toggle on races to show or hide the featured image banner.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Event overview tables show races with dates, times, and configured action buttons that honor settings.', 'race-series-manager' ) . '</li>';
    $content .= '</ul>';

    $content .= '<h2>' . esc_html__( 'Homepage widgets', 'race-series-manager' ) . '</h2>';
    $content .= '<ul>';
    $content .= '<li>' . esc_html__( 'Add the “RS Manager: Race Showcase” widget in Appearance → Widgets to highlight races on your front page.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Choose the parent Event from the dropdown and the number of races to show; races follow your manual order.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Race cards reuse each race’s featured image (or the default race image) and show distance, elevation, and a details link.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'Use the [rsm_race_showcase] shortcode with the same attributes (event, count, optional title) anywhere a widget is not available.', 'race-series-manager' ) . '</li>';
    $content .= '</ul>';

    $content .= '<h2>' . esc_html__( 'PDF Booklet', 'race-series-manager' ) . '</h2>';
    $content .= '<p>' . esc_html__( 'The PDF generator relies on the bundled Dompdf library. If generation fails, check the status panel in settings and reinstall the /lib/dompdf directory if needed.', 'race-series-manager' ) . '</p>';

    $content .= '<h2>' . esc_html__( 'Troubleshooting', 'race-series-manager' ) . '</h2>';
    $content .= '<ul>';
    $content .= '<li>' . esc_html__( 'If shortcodes render blank, verify the Event or Race slug/ID is correct and published.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'If buttons disappear, confirm the link fields are filled on the event edit screen and saved.', 'race-series-manager' ) . '</li>';
    $content .= '<li>' . esc_html__( 'For Dompdf errors, ensure /lib/dompdf/autoload.inc.php exists and is readable.', 'race-series-manager' ) . '</li>';
    $content .= '</ul>';
    ?>
    <div class="wrap rsm-help-wrap">
        <h1><?php esc_html_e( 'RS Manager Help', 'race-series-manager' ); ?></h1>
        <?php if ( $content ) : ?>
            <div class="rsm-help-content">
                <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        <?php else : ?>
            <div class="notice notice-warning inline">
                <p><?php esc_html_e( 'Help content is unavailable.', 'race-series-manager' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <style>
        .rsm-help-content h1,
        .rsm-help-content h2,
        .rsm-help-content h3,
        .rsm-help-content h4,
        .rsm-help-content h5,
        .rsm-help-content h6 {
            margin-top: 1.5em;
        }

        .rsm-help-content ul,
        .rsm-help-content ol {
            margin-left: 1.4em;
        }

        .rsm-help-content code {
            background: #f6f7f7;
            padding: 0.15em 0.35em;
            border-radius: 3px;
        }

        .rsm-help-content pre {
            background: #f6f7f7;
            padding: 12px;
            border-radius: 4px;
            overflow: auto;
            white-space: pre-wrap;
        }
    </style>
    <?php
}
