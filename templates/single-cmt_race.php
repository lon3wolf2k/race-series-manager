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
    $start_point    = get_post_meta( $race_id, '_rsm_race_start_location', true );
    $finish_point   = get_post_meta( $race_id, '_rsm_race_finish_location', true );
    $fee            = get_post_meta( $race_id, '_rsm_race_fee', true );

    // Buttons
    $registration_url = get_post_meta( $race_id, '_rsm_race_registration_url', true );
    $itra_url         = get_post_meta( $race_id, '_rsm_race_itra_url', true );
    $gpx_url          = get_post_meta( $race_id, '_rsm_race_route_url', true );
    $booklet_url      = add_query_arg( 'rsm_booklet', '1', get_permalink( $race_id ) );

    // Plotaroute / route embed
    $plot_embed = get_post_meta( $race_id, '_rsm_race_plot_embed', true );
    if ( ! $plot_embed ) {
        $plot_embed = get_post_meta( $race_id, '_rsm_race_plotaroute_embed', true );
    }
    if ( ! $plot_embed ) {
        $plot_embed = get_post_meta( $race_id, '_rsm_race_route_embed', true );
    }

    // Video embed
    $video_embed = get_post_meta( $race_id, '_rsm_race_video_embed', true );
    if ( ! $video_embed ) {
        $video_embed = get_post_meta( $race_id, '_rsm_race_youtube_embed', true );
    }

    // Gallery (image IDs)
    $gallery_meta = get_post_meta( $race_id, '_rsm_race_gallery_ids', true );
    $gallery_ids  = array();

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

    // Elevation image (static, για PDF & page)
    $elev_chart_id = get_post_meta( $race_id, '_rsm_race_elev_chart_id', true );

    // Συνδεδεμένο event για breadcrumbs + results
    $event_id    = get_post_meta( $race_id, '_rsm_race_event_id', true );
    $event_link  = $event_id ? get_permalink( $event_id ) : '';
    $event_title = $event_id ? get_the_title( $event_id ) : '';

    // URL για Event Results, αν υπάρχει event και helper
    $results_url = '';
    if ( $event_id && function_exists( 'rsm_get_results_page_url' ) ) {
        $results_url = rsm_get_results_page_url( $event_id );
    }

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
            <?php if ( has_post_thumbnail() ) : ?>
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
            <?php if ( ! empty( $aid_stations ) ) : ?>
                <section class="rsm-race-section rsm-race-section--aid">
                    <h2 class="rsm-section-title">
                        <?php esc_html_e( 'Cut-off times & aid stations', 'race-series-manager' ); ?>
                    </h2>

                    <div class="rsm-aid-table-wrapper">
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
                <h2 class="rsm-section-title">
                    <?php esc_html_e( 'Route map', 'race-series-manager' ); ?>
                </h2>

                <?php if ( ! empty( $plot_embed ) ) : ?>
                    <div class="rsm-race-map-embed">
                        <?php
                        echo $plot_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    </div>
                <?php else : ?>
                    <p style="font-size:13px;color:#888;">
                        <?php esc_html_e( 'No route map embed has been set for this race yet.', 'race-series-manager' ); ?>
                    </p>
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
                            if ( ! $full || ! $thumb ) {
                                continue;
                            }
                            ?>
                            <a href="<?php echo esc_url( $full[0] ); ?>"
                               class="rsm-race-gallery-item"
                               data-rsm-lightbox="race-gallery">
                                <img src="<?php echo esc_url( $thumb[0] ); ?>" alt="">
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
                            <dd><?php echo esc_html( $start_point ); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if ( $finish_point ) : ?>
                        <div class="rsm-summary-row">
                            <dt><?php esc_html_e( 'Finish point', 'race-series-manager' ); ?>:</dt>
                            <dd><?php echo esc_html( $finish_point ); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if ( $fee ) : ?>
                        <div class="rsm-summary-row">
                            <dt><?php esc_html_e( 'Entry fee', 'race-series-manager' ); ?>:</dt>
                            <dd><?php echo esc_html( $fee ); ?></dd>
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
                    <?php if ( $registration_url ) : ?>
                        <a href="<?php echo esc_url( $registration_url ); ?>"
                           class="rsm-summary-btn"
                           target="_blank"
                           rel="noopener">
                            <?php esc_html_e( 'Registration', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $itra_url ) : ?>
                        <a href="<?php echo esc_url( $itra_url ); ?>"
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
