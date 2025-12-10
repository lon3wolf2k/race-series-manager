<?php
/**
 * Single Race template (cmt_race)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    $race_id = get_the_ID();

    // Βασικά meta
    $distance       = get_post_meta( $race_id, '_rsm_race_distance', true );
    $elevation      = get_post_meta( $race_id, '_rsm_race_elevation', true );
    $cutoff_hours   = get_post_meta( $race_id, '_rsm_race_cutoff_hours', true );
    $start_datetime = get_post_meta( $race_id, '_rsm_race_date', true );
    $start_time     = get_post_meta( $race_id, '_rsm_race_start_time', true );
    $start_point      = get_post_meta( $race_id, '_rsm_race_start_location', true );
    $start_point_link = get_post_meta( $race_id, '_rsm_race_start_location_link', true );
    $finish_point     = get_post_meta( $race_id, '_rsm_race_finish_location', true );
    $finish_point_link = get_post_meta( $race_id, '_rsm_race_finish_location_link', true );

    // Race-level ITRA link
    $itra_link      = get_post_meta( $race_id, '_rsm_race_itra_link', true );

    // GPX / route download
    $gpx_url        = get_post_meta( $race_id, '_rsm_race_route_url', true );

    // Booklet URL (protected by nonce)
    $booklet_nonce  = wp_create_nonce( 'rsm_booklet_' . $race_id );
    $booklet_url    = add_query_arg(
        array(
            'rsm_booklet'       => '1',
            'rsm_booklet_nonce' => $booklet_nonce,
        ),
        get_permalink( $race_id )
    );

    // Plotaroute / route embed
    $plot_embed     = rsm_race_sanitize_embed_html( get_post_meta( $race_id, '_rsm_race_plotaroute_embed', true ) );

    // Video embed
    $video_embed    = rsm_race_sanitize_embed_html( get_post_meta( $race_id, '_rsm_race_video_embed', true ) );

    // Gallery (image IDs)
    $gallery_meta   = get_post_meta( $race_id, '_rsm_race_gallery_ids', true );
    $gallery_ids    = array();
    $show_hero      = get_post_meta( $race_id, '_rsm_race_show_hero', true );
    $static_map_id  = get_post_meta( $race_id, '_rsm_race_static_map_id', true );
    $map_print_wrap = 'rsm-map-print-stack-' . $race_id;

    if ( '' === $show_hero ) {
        $show_hero = '1';
    }

    if ( is_array( $gallery_meta ) ) {
        $gallery_ids = $gallery_meta;
    } elseif ( is_string( $gallery_meta ) && '' !== trim( $gallery_meta ) ) {
        $parts = array_filter( array_map( 'trim', explode( ',', $gallery_meta ) ) );
        foreach ( $parts as $p ) {
            $id = intval( $p );
            if ( $id ) {
                $gallery_ids[] = $id;
            }
        }
    }

    // Aid stations (table)
    $aid_stations = get_post_meta( $race_id, '_rsm_race_aid_stations', true );

    // Elevation image (static)
    $elev_chart_id = get_post_meta( $race_id, '_rsm_race_elev_chart_id', true );

    // Συνδεδεμένο event
    $event_id    = get_post_meta( $race_id, '_rsm_race_event_id', true );
    $event_link  = $event_id ? get_permalink( $event_id ) : '';
    $event_title = $event_id ? get_the_title( $event_id ) : '';

    // Event-level registration / participants / live URLs (ΜΟΝΟ URLs)
    $event_reg_url   = $event_id ? get_post_meta( $event_id, '_rsm_event_registration_url', true ) : '';
    $event_part_url  = $event_id ? get_post_meta( $event_id, '_rsm_event_participants_url', true ) : '';
    $event_live_url  = $event_id ? get_post_meta( $event_id, '_rsm_event_live_url', true ) : '';

    // URL για Event Results
    $results_url = '';
    if ( $event_id && function_exists( 'rsm_get_results_page_url' ) ) {
        $results_url = rsm_get_results_page_url( $event_id );
    }

    // Buttons logic (registration/participants/live) - μόνο URLs, same tab
    $reg_btn_url   = $event_reg_url ? $event_reg_url : '';
    $part_btn_url  = $event_part_url ? $event_part_url : '';
    $live_btn_url  = $event_live_url ? $event_live_url : '';

    // Ημερομηνία / ώρα αγώνα
    $race_date_formatted = '';
    if ( $start_datetime ) {
        $ts = strtotime( $start_datetime );
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

    ?>

<div class="rsm-race-wrapper">
    <div class="rsm-race-layout">

        <!-- MAIN -->
        <main class="rsm-race-main">

            <!-- HERO IMAGE -->
            <?php if ( has_post_thumbnail() && '1' === $show_hero ) : ?>
                <div class="rsm-hero">
                    <?php the_post_thumbnail( 'large' ); ?>
                </div>
            <?php endif; ?>

            <!-- BREADCRUMB -->
            <nav class="rsm-breadcrumb">
                <a href="<?php echo esc_url( home_url() ); ?>">
                    <?php esc_html_e( 'Home', 'race-series-manager' ); ?>
                </a>
                <?php if ( $event_link && $event_title ) : ?>
                    <span class="rsm-breadcrumb-sep">/</span>
                    <a href="<?php echo esc_url( $event_link ); ?>">
                        <?php echo esc_html( $event_title ); ?>
                    </a>
                <?php endif; ?>
                <span class="rsm-breadcrumb-sep">/</span>
                <span><?php the_title(); ?></span>
            </nav>

            <!-- ADMIN EDIT LINK -->
            <?php if ( current_user_can( 'edit_post', $race_id ) ) :
                $race_edit_url = get_edit_post_link( $race_id );
                if ( $race_edit_url ) :
                    ?>
                    <div class="rsm-admin-edit-link">
                        <a class="rsm-admin-edit-link__button" href="<?php echo esc_url( $race_edit_url ); ?>">
                            <span class="rsm-admin-edit-link__icon" aria-hidden="true">✏️</span>
                            <span><?php esc_html_e( 'Edit race', 'race-series-manager' ); ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- EVENT NAME ABOVE TITLE (small) -->
            <?php if ( $event_title ) : ?>
                <div class="rsm-race-event-label">
                    <?php echo esc_html( $event_title ); ?>
                </div>
            <?php endif; ?>

            <!-- TITLE -->
            <header class="rsm-race-header">
                <h1 class="rsm-race-title"><?php the_title(); ?></h1>
            </header>

            <!-- 1. TOP STATS CARDS -->
            <section class="rsm-race-stats">
                <div class="rsm-race-stat-card">
                    <div class="rsm-race-stat-label">
                        <?php esc_html_e( 'Distance', 'race-series-manager' ); ?>
                    </div>
                    <div class="rsm-race-stat-value">
                        <?php echo esc_html( $distance ); ?>
                    </div>
                </div>
                <div class="rsm-race-stat-card">
                    <div class="rsm-race-stat-label">
                        <?php esc_html_e( 'Total ascent', 'race-series-manager' ); ?>
                    </div>
                    <div class="rsm-race-stat-value">
                        <?php echo esc_html( $elevation ); ?>
                    </div>
                </div>
                <div class="rsm-race-stat-card">
                    <div class="rsm-race-stat-label">
                        <?php esc_html_e( 'Cut-off time', 'race-series-manager' ); ?>
                    </div>
                    <div class="rsm-race-stat-value">
                        <?php echo $cutoff_hours ? esc_html( $cutoff_hours ) . ' ' . esc_html__( 'hours', 'race-series-manager' ) : '—'; ?>
                    </div>
                </div>
            </section>

            <!-- 2. RACE DESCRIPTION -->
            <section class="rsm-race-section rsm-race-section--description">
                <h2 class="rsm-section-title">
                    <?php esc_html_e( 'Race description', 'race-series-manager' ); ?>
                </h2>
                <div class="rsm-race-description">
                    <?php the_content(); ?>
                </div>
            </section>

            <!-- 3. AID STATIONS -->
            <?php if ( ! empty( $aid_stations ) ) :
                $aid_table_id  = 'rsm-aid-table-' . $race_id;
                $aid_title_id  = 'rsm-aid-title-' . $race_id;
                ?>
                <section class="rsm-race-section rsm-race-section--aid">
                    <div class="rsm-section-title-row">
                        <h2 class="rsm-section-title" id="<?php echo esc_attr( $aid_title_id ); ?>">
                            <?php esc_html_e( 'Cut-off times & aid stations', 'race-series-manager' ); ?>
                        </h2>
                        <button
                            type="button"
                            class="rsm-icon-button rsm-print-button"
                            data-rsm-print-target="#<?php echo esc_attr( $aid_table_id ); ?>"
                            data-rsm-print-title="#<?php echo esc_attr( $aid_title_id ); ?>"
                            aria-label="<?php esc_attr_e( 'Print cut-off and aid stations table', 'race-series-manager' ); ?>"
                        >
                            <svg class="rsm-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M17 3H7v4h10V3Zm2 4V2a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v5a3 3 0 0 0-3 3v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1v-6a3 3 0 0 0-3-3h-1Zm-2 12H7v-4h10v4Zm4-9v5h-2v-3a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v3H3v-5a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1Z" />
                            </svg>
                            <span class="rsm-icon-button__label"><?php esc_html_e( 'Print', 'race-series-manager' ); ?></span>
                        </button>
                    </div>

                    <div class="rsm-aid-table-wrapper">
                        <table class="rsm-aid-table" id="<?php echo esc_attr( $aid_table_id ); ?>">
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
                                <?php
                                $lines = preg_split( '/\r\n|\r|\n/', $aid_stations );
                                foreach ( $lines as $line ) :
                                    $line = trim( $line );
                                    if ( '' === $line ) {
                                        continue;
                                    }
                                    $parts   = array_map( 'trim', explode( '|', $line ) );
                                    $station = $parts[0] ?? '';
                                    $km      = $parts[1] ?? '';
                                    $d_plus  = $parts[2] ?? '';
                                    $d_minus = $parts[3] ?? '';
                                    $cutoff  = $parts[4] ?? '';
                                    ?>
                                    <tr>
                                        <td><?php echo esc_html( $station ); ?></td>
                                        <td><?php echo esc_html( $km ); ?></td>
                                        <td><?php echo esc_html( $d_plus ); ?></td>
                                        <td><?php echo esc_html( $d_minus ); ?></td>
                                        <td><?php echo esc_html( $cutoff ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 4. ROUTE MAP -->
            <section class="rsm-race-section rsm-race-section--map">
                <?php
                $map_title_id = 'rsm-map-title-' . $race_id;
                $print_title  = '#' . $map_title_id;
                ?>
                <div class="rsm-section-title-row">
                    <h2 class="rsm-section-title" id="<?php echo esc_attr( $map_title_id ); ?>">
                        <?php esc_html_e( 'Route map', 'race-series-manager' ); ?>
                    </h2>
                    <?php if ( $static_map_id || $elev_chart_id ) : ?>
                        <button
                            type="button"
                            class="rsm-icon-button rsm-print-button"
                            data-rsm-print-images="#<?php echo esc_attr( $map_print_wrap ); ?>"
                            data-rsm-print-title="<?php echo esc_attr( $print_title ); ?>"
                            aria-label="<?php esc_attr_e( 'Print the route map and elevation images', 'race-series-manager' ); ?>"
                        >
                            <svg class="rsm-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M17 3H7v4h10V3Zm2 4V2a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v5a3 3 0 0 0-3 3v6a1 1 0 0 0 1 1h3v3a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-3h3a1 1 0 0 0 1-1v-6a3 3 0 0 0-3-3h-1Zm-2 12H7v-4h10v4Zm4-9v5h-2v-3a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v3H3v-5a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1Z" />
                            </svg>
                            <span class="rsm-icon-button__label"><?php esc_html_e( 'Print', 'race-series-manager' ); ?></span>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if ( ! empty( $plot_embed ) ) : ?>
                    <div class="rsm-race-map-embed">
                        <?php echo $plot_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                <?php else : ?>
                    <p style="font-size:13px;color:#888;">
                        <?php esc_html_e( 'No route map embed has been set for this race yet.', 'race-series-manager' ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( $static_map_id || $elev_chart_id ) :
                    $static_map_alt = '';
                    $static_map_img = '';
                    if ( $static_map_id ) {
                        $static_map_alt = get_post_meta( $static_map_id, '_wp_attachment_image_alt', true );
                        if ( '' === $static_map_alt ) {
                            $static_map_alt = get_the_title( $static_map_id );
                        }
                        $static_map_img = wp_get_attachment_image(
                            $static_map_id,
                            'large',
                            false,
                            array(
                                'class' => 'rsm-print-image',
                                'alt'   => $static_map_alt,
                            )
                        );
                    }

                    $elev_alt = '';
                    $elev_img = '';
                    if ( $elev_chart_id ) {
                        $elev_alt = get_post_meta( $elev_chart_id, '_wp_attachment_image_alt', true );
                        if ( '' === $elev_alt ) {
                            $elev_alt = get_the_title( $elev_chart_id );
                        }
                        $elev_img = wp_get_attachment_image(
                            $elev_chart_id,
                            'large',
                            false,
                            array(
                                'class' => 'rsm-print-image',
                                'alt'   => $elev_alt,
                            )
                        );
                    }
                    ?>
                    <div id="<?php echo esc_attr( $map_print_wrap ); ?>" class="rsm-print-stack" aria-hidden="true" style="display:none;">
                        <?php if ( $static_map_img ) : ?>
                            <div class="rsm-print-block">
                                <h3 class="rsm-print-block__title"><?php esc_html_e( 'Route map', 'race-series-manager' ); ?></h3>
                                <?php echo $static_map_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( $elev_img ) : ?>
                            <div class="rsm-print-block">
                                <h3 class="rsm-print-block__title"><?php esc_html_e( 'Elevation profile', 'race-series-manager' ); ?></h3>
                                <?php echo $elev_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- 5. ELEVATION PROFILE IMAGE -->
            <?php if ( $elev_chart_id ) : ?>
                <section class="rsm-race-section rsm-race-section--elev">
                    <h2 class="rsm-section-title">
                        <?php esc_html_e( 'Elevation profile', 'race-series-manager' ); ?>
                    </h2>
                    <div class="rsm-booklet-image-block">
                        <?php
                        echo wp_get_attachment_image( $elev_chart_id, 'large', false, array(
                            'style' => 'max-width:100%;height:auto;border-radius:18px;box-shadow:0 10px 26px rgba(0,0,0,0.10);',
                        ) );
                        ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 6. RACE VIDEO -->
            <?php if ( ! empty( $video_embed ) ) : ?>
                <section class="rsm-race-section rsm-race-section--video">
                    <h2 class="rsm-section-title">
                        <?php esc_html_e( 'Race video', 'race-series-manager' ); ?>
                    </h2>
                    <div class="rsm-race-video-embed">
                        <?php echo $video_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 7. RACE GALLERY -->
            <?php if ( ! empty( $gallery_ids ) ) : ?>
                <section class="rsm-race-section rsm-race-section--gallery">
                    <h2 class="rsm-section-title">
                        <?php esc_html_e( 'Race gallery', 'race-series-manager' ); ?>
                    </h2>
                    <div class="rsm-race-gallery">
                        <?php foreach ( $gallery_ids as $img_id ) :
                            $full  = wp_get_attachment_image_src( $img_id, 'large' );
                            $thumb = wp_get_attachment_image_src( $img_id, 'medium' );
                            $alt   = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
                            if ( '' === $alt ) {
                                $alt = get_the_title( $img_id );
                            }
                            if ( ! $full || ! $thumb ) {
                                continue;
                            }
                            ?>
                            <a href="<?php echo esc_url( $full[0] ); ?>"
                               class="rsm-race-gallery-item"
                               data-rsm-lightbox="race-gallery">
                                <img src="<?php echo esc_url( $thumb[0] ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </main>

        <!-- SIDEBAR / SUMMARY -->
        <aside class="rsm-race-sidebar">
            <div class="rsm-race-summary">

                <h2 class="rsm-summary-title">
                    <?php esc_html_e( 'Race summary', 'race-series-manager' ); ?>
                </h2>

                <dl class="rsm-summary-list">
                    <?php if ( $race_date_formatted || $start_time_formatted ) : ?>
                        <div class="rsm-summary-row">
                            <dt><?php esc_html_e( 'Start', 'race-series-manager' ); ?>:</dt>
                            <dd>
                                <?php
                                if ( $race_date_formatted ) {
                                    echo esc_html( $race_date_formatted );
                                }
                                if ( $race_date_formatted && $start_time_formatted ) {
                                    echo ' • ';
                                }
                                if ( $start_time_formatted ) {
                                    echo esc_html( $start_time_formatted );
                                }
                                ?>
                            </dd>
                        </div>
                    <?php endif; ?>

                    <?php if ( $start_point ) : ?>
                        <div class="rsm-summary-row">
                            <dt><?php esc_html_e( 'Start point', 'race-series-manager' ); ?>:</dt>
                            <dd>
                                <?php if ( $start_point_link ) : ?>
                                    <a href="<?php echo esc_url( $start_point_link ); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html( $start_point ); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo esc_html( $start_point ); ?>
                                <?php endif; ?>
                            </dd>
                        </div>
                    <?php endif; ?>

                    <?php if ( $finish_point ) : ?>
                        <div class="rsm-summary-row">
                            <dt><?php esc_html_e( 'Finish point', 'race-series-manager' ); ?>:</dt>
                            <dd>
                                <?php if ( $finish_point_link ) : ?>
                                    <a href="<?php echo esc_url( $finish_point_link ); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html( $finish_point ); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo esc_html( $finish_point ); ?>
                                <?php endif; ?>
                            </dd>
                        </div>
                    <?php endif; ?>

                    <?php if ( $cutoff_hours ) : ?>
                        <div class="rsm-summary-row">
                            <dt><?php esc_html_e( 'Cut-off', 'race-series-manager' ); ?>:</dt>
                            <dd><?php echo esc_html( $cutoff_hours ) . ' ' . esc_html__( 'hours', 'race-series-manager' ); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if ( $distance ) : ?>
                        <div class="rsm-summary-row">
                            <dt><?php esc_html_e( 'Distance', 'race-series-manager' ); ?>:</dt>
                            <dd><?php echo esc_html( $distance ); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if ( $elevation ) : ?>
                        <div class="rsm-summary-row">
                            <dt><?php esc_html_e( 'Total ascent', 'race-series-manager' ); ?>:</dt>
                            <dd><?php echo esc_html( $elevation ); ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>

                <div class="rsm-summary-buttons">

                    <?php if ( $reg_btn_url ) : ?>
                        <a href="<?php echo esc_url( $reg_btn_url ); ?>"
                           class="rsm-summary-btn">
                            <?php esc_html_e( 'Registration', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $part_btn_url ) : ?>
                        <a href="<?php echo esc_url( $part_btn_url ); ?>"
                           class="rsm-summary-btn">
                            <?php esc_html_e( 'Participants', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $live_btn_url ) : ?>
                        <a href="<?php echo esc_url( $live_btn_url ); ?>"
                           class="rsm-summary-btn">
                            <?php esc_html_e( 'Live', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $itra_link ) : ?>
                        <a href="<?php echo esc_url( $itra_link ); ?>"
                           class="rsm-summary-btn"
                           target="_blank"
                           rel="noopener">
                            <?php esc_html_e( 'ITRA', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $gpx_url ) : ?>
                        <a href="<?php echo esc_url( $gpx_url ); ?>"
                           class="rsm-summary-btn"
                           target="_blank"
                           rel="noopener">
                            <?php esc_html_e( 'Download GPX / Route', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $booklet_url ) : ?>
                        <a href="<?php echo esc_url( $booklet_url ); ?>"
                           class="rsm-summary-btn"
                           target="_blank"
                           rel="noopener">
                            <?php esc_html_e( 'Race booklet', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $results_url ) : ?>
                        <a href="<?php echo esc_url( $results_url ); ?>"
                           class="rsm-summary-btn rsm-event-results-btn"
                           target="_blank"
                           rel="noopener">
                            <?php esc_html_e( 'Results', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </aside>

    </div>
</div>

<?php
endwhile;

get_footer();
