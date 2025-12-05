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

    $race_id          = get_the_ID();

    // Meta.
    $event_id         = get_post_meta( $race_id, '_rsm_race_event_id', true );
    $distance         = get_post_meta( $race_id, '_rsm_race_distance', true );
    $elevation        = get_post_meta( $race_id, '_rsm_race_elevation', true );
    $route_url        = get_post_meta( $race_id, '_rsm_race_route_url', true );
    $route_embed_code = get_post_meta( $race_id, '_rsm_race_route_embed_code', true );
    $video_embed_code = get_post_meta( $race_id, '_rsm_race_video_embed_code', true );
    $gallery_ids_raw  = get_post_meta( $race_id, '_rsm_race_gallery_ids', true );
    $booklet_url      = get_post_meta( $race_id, '_rsm_race_booklet_url', true );
    $itra_url         = get_post_meta( $race_id, '_rsm_race_itra_url', true );
    $reg_url          = get_post_meta( $race_id, '_rsm_race_registration_url', true );
    $race_date        = get_post_meta( $race_id, '_rsm_race_date', true );
    $start_time       = get_post_meta( $race_id, '_rsm_race_start_time', true );
    $start_loc        = get_post_meta( $race_id, '_rsm_race_start_location', true );
    $finish_loc       = get_post_meta( $race_id, '_rsm_race_finish_location', true );
    $fee              = get_post_meta( $race_id, '_rsm_race_fee', true );
    $cutoff_hours     = get_post_meta( $race_id, '_rsm_race_cutoff_hours', true );
    $aid_stations_raw = get_post_meta( $race_id, '_rsm_race_aid_stations', true );

    $event_title      = $event_id ? get_the_title( $event_id ) : '';

    // Format date dd-mm-yyyy.
    $race_date_formatted = '';
    if ( $race_date ) {
        $ts = strtotime( $race_date );
        if ( $ts ) {
            $race_date_formatted = date_i18n( 'd-m-Y', $ts );
        }
    }

    // Format time with site format.
    $start_time_formatted = '';
    if ( $start_time ) {
        $ts = strtotime( $start_time );
        if ( $ts ) {
            $start_time_formatted = date_i18n( get_option( 'time_format' ), $ts );
        }
    }

    // Gallery IDs.
    $gallery_ids = array();
    if ( ! empty( $gallery_ids_raw ) ) {
        $gallery_ids = array_filter( array_map( 'intval', explode( ',', $gallery_ids_raw ) ) );
    }

    // Parse aid stations into array of rows.
    $aid_rows = array();
    if ( ! empty( $aid_stations_raw ) ) {
        $lines = preg_split( '/\r\n|\r|\n/', $aid_stations_raw );
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
    ?>

<div class="rsm-race-wrapper">
    <div class="rsm-race-layout">

        <main class="rsm-race-main">

            <!-- HERO IMAGE -->
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="rsm-hero">
                    <?php the_post_thumbnail( 'large' ); ?>
                </div>
            <?php endif; ?>

            <!-- BREADCRUMB -->
            <nav class="rsm-breadcrumb">
                <a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'race-series-manager' ); ?></a>
                <?php if ( $event_title ) : ?>
                    <span class="rsm-breadcrumb-sep">/</span>
                    <a href="<?php echo esc_url( get_permalink( $event_id ) ); ?>">
                        <?php echo esc_html( $event_title ); ?>
                    </a>
                <?php endif; ?>
                <span class="rsm-breadcrumb-sep">/</span>
                <span><?php the_title(); ?></span>
            </nav>

            <!-- TITLE + STATS -->
            <header class="rsm-race-header">

                <?php if ( $event_title ) : ?>
                    <p class="rsm-race-event"><?php echo esc_html( $event_title ); ?></p>
                <?php endif; ?>

                <h1 class="rsm-race-title"><?php the_title(); ?></h1>

                <div class="rsm-race-stats-row">

                    <?php if ( $distance ) : ?>
                        <div class="rsm-race-stat-card rsm-race-stat-card--primary">
                            <div class="rsm-race-stat-icon">🏃‍♂️</div>
                            <div class="rsm-race-stat-value"><?php echo esc_html( $distance ); ?></div>
                            <div class="rsm-race-stat-label"><?php esc_html_e( 'Distance', 'race-series-manager' ); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $elevation ) : ?>
                        <div class="rsm-race-stat-card">
                            <div class="rsm-race-stat-icon">⛰️</div>
                            <div class="rsm-race-stat-value"><?php echo esc_html( $elevation ); ?></div>
                            <div class="rsm-race-stat-label"><?php esc_html_e( 'Total ascent', 'race-series-manager' ); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $cutoff_hours ) : ?>
                        <div class="rsm-race-stat-card">
                            <div class="rsm-race-stat-icon">⏱️</div>
                            <div class="rsm-race-stat-value"><?php echo esc_html( $cutoff_hours ); ?>h</div>
                            <div class="rsm-race-stat-label"><?php esc_html_e( 'Cut-off', 'race-series-manager' ); ?></div>
                        </div>
                    <?php endif; ?>

                </div>
            </header>

            <!-- DESCRIPTION -->
            <section class="rsm-race-section rsm-race-section--content">
                <?php the_content(); ?>
            </section>

            <!-- AID STATIONS TABLE -->
            <?php if ( ! empty( $aid_rows ) ) : ?>
                <section class="rsm-race-section rsm-race-section--aid">
                    <h2 class="rsm-section-title">
                        <?php esc_html_e( 'Cut-off times & aid stations', 'race-series-manager' ); ?>
                    </h2>
                    <hr class="rsm-section-divider" />

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
                    </div>
                </section>
            <?php endif; ?>

            <!-- ROUTE MAP -->
            <?php if ( $route_embed_code ) : ?>
                <section class="rsm-race-section rsm-race-section--map">
                    <h2 class="rsm-section-title"><?php esc_html_e( 'Route map', 'race-series-manager' ); ?></h2>
                    <hr class="rsm-section-divider" />

                    <div class="rsm-race-map-embed">
                        <?php
                        // Full embed HTML (iframe etc.) saved in meta.
                        echo $route_embed_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- VIDEO -->
            <?php if ( $video_embed_code ) : ?>
                <section class="rsm-race-section rsm-race-section--video">
                    <h2 class="rsm-section-title"><?php esc_html_e( 'Race video', 'race-series-manager' ); ?></h2>
                    <hr class="rsm-section-divider" />

                    <div class="rsm-race-video-embed">
                        <?php
                        echo $video_embed_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- GALLERY -->
            <?php if ( ! empty( $gallery_ids ) ) : ?>
                <section class="rsm-race-section rsm-race-section--gallery">
                    <h2 class="rsm-section-title"><?php esc_html_e( 'Race gallery', 'race-series-manager' ); ?></h2>
                    <hr class="rsm-section-divider" />

                    <div class="rsm-gallery-grid">
                        <?php foreach ( $gallery_ids as $aid ) : ?>
                            <?php
                            $full  = wp_get_attachment_image_src( $aid, 'large' );
                            $thumb = wp_get_attachment_image_src( $aid, 'medium' );
                            if ( ! $full || ! $thumb ) {
                                continue;
                            }
                            ?>
                            <a href="<?php echo esc_url( $full[0] ); ?>" class="rsm-gallery-item">
                                <img src="<?php echo esc_url( $thumb[0] ); ?>" alt="">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </main>

        <!-- SIDEBAR -->
        <aside class="rsm-race-sidebar">
            <div class="rsm-race-sidebar-inner">

                <h2 class="rsm-sidebar-title"><?php esc_html_e( 'Race summary', 'race-series-manager' ); ?></h2>

                <ul class="rsm-race-summary-list">

                    <?php if ( $race_date_formatted || $start_time_formatted ) : ?>
                        <li>
                            <strong><?php esc_html_e( 'Start:', 'race-series-manager' ); ?></strong>
                            <span>
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
                            </span>
                        </li>
                    <?php endif; ?>

                    <?php if ( $start_loc ) : ?>
                        <li>
                            <strong><?php esc_html_e( 'Start point:', 'race-series-manager' ); ?></strong>
                            <span><?php echo esc_html( $start_loc ); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if ( $finish_loc ) : ?>
                        <li>
                            <strong><?php esc_html_e( 'Finish point:', 'race-series-manager' ); ?></strong>
                            <span><?php echo esc_html( $finish_loc ); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if ( $fee ) : ?>
                        <li>
                            <strong><?php esc_html_e( 'Entry fee:', 'race-series-manager' ); ?></strong>
                            <span><?php echo esc_html( $fee ); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if ( $cutoff_hours ) : ?>
                        <li>
                            <strong><?php esc_html_e( 'Cut-off:', 'race-series-manager' ); ?></strong>
                            <span>
                                <?php
                                echo esc_html( $cutoff_hours );
                                echo ' ';
                                esc_html_e( 'hours', 'race-series-manager' );
                                ?>
                            </span>
                        </li>
                    <?php endif; ?>

                    <?php if ( $distance ) : ?>
                        <li>
                            <strong><?php esc_html_e( 'Distance:', 'race-series-manager' ); ?></strong>
                            <span><?php echo esc_html( $distance ); ?></span>
                        </li>
                    <?php endif; ?>

                    <?php if ( $elevation ) : ?>
                        <li>
                            <strong><?php esc_html_e( 'Total ascent:', 'race-series-manager' ); ?></strong>
                            <span><?php echo esc_html( $elevation ); ?></span>
                        </li>
                    <?php endif; ?>

                </ul>

                <div class="rsm-race-sidebar-actions">

                    <?php if ( $reg_url ) : ?>
                        <a href="<?php echo esc_url( $reg_url ); ?>"
                           target="_blank"
                           rel="noopener"
                           class="rsm-button">
                            <?php esc_html_e( 'Registration', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $itra_url ) : ?>
                        <a href="<?php echo esc_url( $itra_url ); ?>"
                           target="_blank"
                           rel="noopener"
                           class="rsm-button rsm-button--secondary">
                            <?php esc_html_e( 'ITRA', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $route_url ) : ?>
                        <a href="<?php echo esc_url( $route_url ); ?>"
                           target="_blank"
                           rel="noopener"
                           class="rsm-button rsm-button--secondary">
                            <?php esc_html_e( 'Download GPX / Route', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php
                    // Booklet button: manual URL if provided, otherwise auto-generated PDF (?rsm_booklet=1).
                    $booklet_link = $booklet_url
                        ? $booklet_url
                        : add_query_arg( 'rsm_booklet', '1', get_permalink( $race_id ) );
                    ?>
                    <a href="<?php echo esc_url( $booklet_link ); ?>"
                       target="_blank"
                       rel="noopener"
                       class="rsm-button">
                        <?php esc_html_e( 'Race booklet', 'race-series-manager' ); ?>
                    </a>

                </div>

            </div>
        </aside>

    </div>
</div>

<?php
endwhile;

get_footer();
