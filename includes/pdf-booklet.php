<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PDF race booklet generation (Dompdf) with local image support.
 */

// -----------------------------------------------------------------------------
// Load Dompdf
// -----------------------------------------------------------------------------

if ( ! class_exists( '\Dompdf\Dompdf' ) ) {
    $dompdf_autoload = RSM_PLUGIN_DIR . 'lib/dompdf/autoload.inc.php';
    if ( file_exists( $dompdf_autoload ) ) {
        require_once $dompdf_autoload;
    }
}

use Dompdf\Dompdf;
use Dompdf\Options;

// -----------------------------------------------------------------------------
// Helper: get PDF-safe image src (relative path under ABSPATH)
// -----------------------------------------------------------------------------

function rsm_race_get_pdf_image_src_from_attachment( $attachment_id ) {
    $attachment_id = intval( $attachment_id );
    if ( ! $attachment_id ) {
        return '';
    }

    $file_path = get_attached_file( $attachment_id );
    if ( ! $file_path || ! file_exists( $file_path ) ) {
        return '';
    }

    $file_path = str_replace( '\\', '/', $file_path );
    $root      = str_replace( '\\', '/', ABSPATH );

    if ( strpos( $file_path, $root ) === 0 ) {
        $relative = substr( $file_path, strlen( $root ) );
        $relative = '/' . ltrim( $relative, '/' );
        return $relative;
    }

    return '';
}

// -----------------------------------------------------------------------------
// Hook: generate PDF on ?rsm_booklet=1
// -----------------------------------------------------------------------------

function rsm_maybe_output_race_booklet_pdf() {

    if ( ! is_singular( 'cmt_race' ) ) {
        return;
    }

    if ( ! isset( $_GET['rsm_booklet'] ) ) {
        return;
    }

    if ( ! class_exists( '\Dompdf\Dompdf' ) ) {
        wp_die(
            esc_html__(
                'Dompdf PDF library is not loaded. Check that /lib/dompdf/autoload.inc.php exists and is readable.',
                'race-series-manager'
            )
        );
    }

    $race_id = get_queried_object_id();
    if ( ! $race_id ) {
        wp_die( esc_html__( 'Race not found for PDF generation.', 'race-series-manager' ) );
    }

    $html = rsm_build_race_booklet_html( $race_id );

    $options = new Options();
    $options->set( 'isRemoteEnabled', true );
    // Χρησιμοποιούμε DejaVu Sans για υποστήριξη ελληνικών
    $options->set( 'defaultFont', 'DejaVu Sans' );
    $options->setChroot( ABSPATH );

    $dompdf = new Dompdf( $options );
    $dompdf->loadHtml( $html, 'UTF-8' );
    $dompdf->setPaper( 'A4', 'portrait' );
    $dompdf->render();

    $filename = sanitize_title( get_the_title( $race_id ) ) . '-booklet.pdf';

    $dompdf->stream( $filename, array( 'Attachment' => true ) );
    exit;
}
add_action( 'template_redirect', 'rsm_maybe_output_race_booklet_pdf' );

// -----------------------------------------------------------------------------
// Build HTML
// -----------------------------------------------------------------------------

function rsm_build_race_booklet_html( $race_id ) {

    $post           = get_post( $race_id );
    $title          = get_the_title( $race_id );

    $distance       = get_post_meta( $race_id, '_rsm_race_distance', true );
    $elevation      = get_post_meta( $race_id, '_rsm_race_elevation', true );
    $route_url      = get_post_meta( $race_id, '_rsm_race_route_url', true );
    $race_date      = get_post_meta( $race_id, '_rsm_race_date', true );
    $start_time     = get_post_meta( $race_id, '_rsm_race_start_time', true );
    $start_loc      = get_post_meta( $race_id, '_rsm_race_start_location', true );
    $finish_loc     = get_post_meta( $race_id, '_rsm_race_finish_location', true );
    $fee            = get_post_meta( $race_id, '_rsm_race_fee', true );
    $cutoff_hours   = get_post_meta( $race_id, '_rsm_race_cutoff_hours', true );
    $aid_stations   = get_post_meta( $race_id, '_rsm_race_aid_stations', true );
    $static_map_id  = get_post_meta( $race_id, '_rsm_race_static_map_id', true );
    $elev_chart_id  = get_post_meta( $race_id, '_rsm_race_elev_chart_id', true );
    $event_id       = get_post_meta( $race_id, '_rsm_race_event_id', true );

    // Event-level data.
    $event_logo_src   = '';
    $event_schedule   = '';
    $event_access     = '';
    $event_contact    = '';

    if ( $event_id ) {
        $event_logo_id = get_post_meta( $event_id, '_rsm_event_logo_id', true );
        if ( $event_logo_id ) {
            $event_logo_src = rsm_race_get_pdf_image_src_from_attachment( $event_logo_id );
        }

        $event_schedule = get_post_meta( $event_id, '_rsm_event_schedule', true );
        $event_access   = get_post_meta( $event_id, '_rsm_event_access', true );
        $event_contact  = get_post_meta( $event_id, '_rsm_event_contact', true );
    }

    // Static map / elevation chart.
    $static_map_src = rsm_race_get_pdf_image_src_from_attachment( $static_map_id );
    $elev_chart_src = rsm_race_get_pdf_image_src_from_attachment( $elev_chart_id );

    // Date / time formatted.
    $race_date_formatted = '';
    if ( $race_date ) {
        $ts = strtotime( $race_date );
        if ( $ts ) {
            $race_date_formatted = date_i18n( 'd-m-Y', $ts );
        }
    }

    $start_time_formatted = '';
    if ( $start_time ) {
        $ts = strtotime( $start_time );
        if ( $ts ) {
            $start_time_formatted = date_i18n( get_option( 'time_format' ), $ts );
        }
    }

    // Aid stations.
    $aid_rows = array();
    if ( ! empty( $aid_stations ) ) {
        $lines = preg_split( '/\r\n|\r|\n/', $aid_stations );
        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( '' === $line ) {
                continue;
            }
            $parts    = array_map( 'trim', explode( '|', $line ) );
            $station  = $parts[0] ?? '';
            $km       = $parts[1] ?? '';
            $d_plus   = $parts[2] ?? '';
            $d_minus  = $parts[3] ?? '';
            $cutoff   = $parts[4] ?? '';

            if ( '' === $station && '' === $km ) {
                continue;
            }

            $aid_rows[] = array(
                'station' => $station,
                'km'      => $km,
                'dplus'   => $d_plus,
                'dminus'  => $d_minus,
                'cutoff'  => $cutoff,
            );
        }
    }

    ob_start();
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo esc_html( $title ); ?></title>
    <style>
        body {
            font-family: "DejaVu Sans", Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 30px 40px;
        }
        h1 {
            font-size: 24px;
            margin: 0 0 4px;
        }
        h2 {
            font-size: 16px;
            margin: 20px 0 6px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }
        p {
            margin: 0 0 8px;
        }
        .rsm-booklet-logo {
            text-align: center;
            margin-bottom: 10px;
        }
        .rsm-booklet-logo img {
            max-height: 60px;
            width: auto;
        }
        .rsm-booklet-header {
            text-align: center;
            margin-bottom: 16px;
        }
        .rsm-summary-grid {
            width: 100%;
            border: 1px solid #ccc;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .rsm-summary-grid th,
        .rsm-summary-grid td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            font-size: 11px;
        }
        .rsm-summary-grid th {
            background: #f2f2f2;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .rsm-aid-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .rsm-aid-table th,
        .rsm-aid-table td {
            border: 1px solid #ddd;
            padding: 5px 6px;
        }
        .rsm-aid-table th {
            background: #f2f2f2;
        }
        .rsm-booklet-image-block {
            margin: 12px 0 16px;
            text-align: center;
        }
        .rsm-booklet-image-block img {
            max-width: 100%;
            height: auto;
        }
        .rsm-section-text {
            margin-bottom: 14px;
        }
        .rsm-footnote {
            margin-top: 12px;
            font-size: 10px;
        }
        .rsm-contact {
            margin-top: 10px;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <?php if ( $event_logo_src ) : ?>
        <div class="rsm-booklet-logo">
            <img src="<?php echo esc_attr( $event_logo_src ); ?>" alt="">
        </div>
    <?php endif; ?>

    <div class="rsm-booklet-header">
        <h1><?php echo esc_html( $title ); ?></h1>
        <?php if ( $race_date_formatted || $start_time_formatted ) : ?>
            <p>
                <?php
                if ( $race_date_formatted ) {
                    echo esc_html( $race_date_formatted );
                }
                if ( $race_date_formatted && $start_time_formatted ) {
                    echo ' · ';
                }
                if ( $start_time_formatted ) {
                    echo esc_html( $start_time_formatted );
                }
                ?>
            </p>
        <?php endif; ?>
    </div>

    <table class="rsm-summary-grid">
        <tr>
            <th><?php esc_html_e( 'Distance', 'race-series-manager' ); ?></th>
            <td><?php echo esc_html( $distance ); ?></td>
            <th><?php esc_html_e( 'Total ascent', 'race-series-manager' ); ?></th>
            <td><?php echo esc_html( $elevation ); ?></td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Start point', 'race-series-manager' ); ?></th>
            <td><?php echo esc_html( $start_loc ); ?></td>
            <th><?php esc_html_e( 'Finish point', 'race-series-manager' ); ?></th>
            <td><?php echo esc_html( $finish_loc ); ?></td>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Entry fee', 'race-series-manager' ); ?></th>
            <td><?php echo esc_html( $fee ); ?></td>
            <th><?php esc_html_e( 'Cut-off', 'race-series-manager' ); ?></th>
            <td><?php echo $cutoff_hours ? esc_html( $cutoff_hours ) . ' ' . esc_html__( 'hours', 'race-series-manager' ) : ''; ?></td>
        </tr>
        <?php if ( $route_url ) : ?>
        <tr>
            <th><?php esc_html_e( 'Route / GPX', 'race-series-manager' ); ?></th>
            <td colspan="3"><?php echo esc_html( $route_url ); ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <?php if ( $static_map_src ) : ?>
        <h2><?php esc_html_e( 'Route map', 'race-series-manager' ); ?></h2>
        <div class="rsm-booklet-image-block">
            <img src="<?php echo esc_attr( $static_map_src ); ?>" alt="">
        </div>
    <?php endif; ?>

    <?php if ( $elev_chart_src ) : ?>
        <h2><?php esc_html_e( 'Elevation profile', 'race-series-manager' ); ?></h2>
        <div class="rsm-booklet-image-block">
            <img src="<?php echo esc_attr( $elev_chart_src ); ?>" alt="">
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $aid_rows ) ) : ?>
        <h2><?php esc_html_e( 'Cut-off times & aid stations', 'race-series-manager' ); ?></h2>
        <table class="rsm-aid-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Station', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'KM', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'D+', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'D-', 'race-series-manager' ); ?></th>
                    <th><?php esc_html_e( 'Cut-off', 'race-series-manager' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $aid_rows as $row ) : ?>
                    <tr>
                        <td><?php echo esc_html( $row['station'] ); ?></td>
                        <td><?php echo esc_html( $row['km'] ); ?></td>
                        <td><?php echo esc_html( $row['dplus'] ); ?></td>
                        <td><?php echo esc_html( $row['dminus'] ); ?></td>
                        <td><?php echo esc_html( $row['cutoff'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ( ! empty( $event_schedule ) ) : ?>
        <h2><?php esc_html_e( 'Race schedule', 'race-series-manager' ); ?></h2>
        <div class="rsm-section-text">
            <?php echo wp_kses_post( wpautop( $event_schedule ) ); ?>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $event_access ) ) : ?>
        <h2><?php esc_html_e( 'Race access', 'race-series-manager' ); ?></h2>
        <div class="rsm-section-text">
            <?php echo wp_kses_post( wpautop( $event_access ) ); ?>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $event_contact ) ) : ?>
        <h2><?php esc_html_e( 'Contact details', 'race-series-manager' ); ?></h2>
        <div class="rsm-contact">
            <?php echo wp_kses_post( wpautop( $event_contact ) ); ?>
        </div>
    <?php endif; ?>

    <p class="rsm-footnote">
        <?php esc_html_e( 'This booklet is automatically generated from the race information on the website. Always consult the official briefing for last-minute changes.', 'race-series-manager' ); ?>
    </p>

</body>
</html>
    <?php
    return ob_get_clean();
}
