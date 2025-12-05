<?php
/**
 * Single Event template (cmt_event)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    $event_id      = get_the_ID();

    // Extra event sections from meta:
    $announcement  = get_post_meta( $event_id, '_rsm_event_announcement', true );
    $rules         = get_post_meta( $event_id, '_rsm_event_rules', true );
    $schedule      = get_post_meta( $event_id, '_rsm_event_schedule', true );
    $access        = get_post_meta( $event_id, '_rsm_event_access', true );
    $travel        = get_post_meta( $event_id, '_rsm_event_travel', true );

    // Registration / live URLs:
    $reg_url       = get_post_meta( $event_id, '_rsm_event_registration_url', true );
    $part_url      = get_post_meta( $event_id, '_rsm_event_participants_url', true );
    $live_url      = get_post_meta( $event_id, '_rsm_event_live_url', true );

    // Iframe codes:
    $reg_iframe    = get_post_meta( $event_id, '_rsm_event_registration_iframe', true );
    $part_iframe   = get_post_meta( $event_id, '_rsm_event_participants_iframe', true );
    $live_iframe   = get_post_meta( $event_id, '_rsm_event_live_iframe', true );

    // Fetch linked races for sidebar list.
    $linked_races = new WP_Query( array(
        'post_type'      => 'cmt_race',
        'posts_per_page' => -1,
        'meta_key'       => '_rsm_race_event_id',
        'meta_value'     => $event_id,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );

    // Results page URL if exists helper.
    $results_url = '';
    if ( function_exists( 'rsm_get_results_page_url' ) ) {
        $results_url = rsm_get_results_page_url( $event_id );
    }

    // Helper: buttons target/URL logic
    $reg_btn_url   = '';
    $reg_btn_attr  = '';
    if ( ! empty( $reg_iframe ) ) {
        $reg_btn_url  = get_permalink( $event_id ) . '#event-registration';
        $reg_btn_attr = ''; // scroll on same page
    } elseif ( ! empty( $reg_url ) ) {
        $reg_btn_url  = $reg_url;
        $reg_btn_attr = 'target="_blank" rel="noopener"';
    }

    $part_btn_url  = '';
    $part_btn_attr = '';
    if ( ! empty( $part_iframe ) ) {
        $part_btn_url  = get_permalink( $event_id ) . '#event-participants';
        $part_btn_attr = '';
    } elseif ( ! empty( $part_url ) ) {
        $part_btn_url  = $part_url;
        $part_btn_attr = 'target="_blank" rel="noopener"';
    }

    $live_btn_url  = '';
    $live_btn_attr = '';
    if ( ! empty( $live_iframe ) ) {
        $live_btn_url  = get_permalink( $event_id ) . '#event-live';
        $live_btn_attr = '';
    } elseif ( ! empty( $live_url ) ) {
        $live_btn_url  = $live_url;
        $live_btn_attr = 'target="_blank" rel="noopener"';
    }
    ?>

<div class="rsm-race-wrapper rsm-event-wrapper">
    <div class="rsm-race-layout rsm-event-layout">

        <main class="rsm-race-main rsm-event-main">

            <!-- HERO IMAGE -->
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="rsm-hero rsm-event-hero">
                    <?php the_post_thumbnail( 'large' ); ?>
                </div>
            <?php endif; ?>

            <!-- BREADCRUMB -->
            <nav class="rsm-breadcrumb">
                <a href="<?php echo esc_url( home_url() ); ?>">
                    <?php esc_html_e( 'Home', 'race-series-manager' ); ?>
                </a>
                <span class="rsm-breadcrumb-sep">/</span>
                <span><?php the_title(); ?></span>
            </nav>

            <!-- TITLE -->
            <header class="rsm-race-header rsm-event-header">
                <h1 class="rsm-race-title rsm-event-title"><?php the_title(); ?></h1>
            </header>

            <!-- TABS / ANCHOR NAV -->
            <nav class="rsm-event-tabs">
                <a href="#event-info" class="rsm-event-tab is-active">
                    <?php esc_html_e( 'Info', 'race-series-manager' ); ?>
                </a>

                <?php if ( ! empty( $announcement ) ) : ?>
                    <a href="#event-announcement" class="rsm-event-tab">
                        <?php esc_html_e( 'Announcement', 'race-series-manager' ); ?>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $rules ) ) : ?>
                    <a href="#event-rules" class="rsm-event-tab">
                        <?php esc_html_e( 'Rules', 'race-series-manager' ); ?>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $schedule ) ) : ?>
                    <a href="#event-schedule" class="rsm-event-tab">
                        <?php esc_html_e( 'Program', 'race-series-manager' ); ?>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $access ) ) : ?>
                    <a href="#event-access" class="rsm-event-tab">
                        <?php esc_html_e( 'Access', 'race-series-manager' ); ?>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $travel ) ) : ?>
                    <a href="#event-travel" class="rsm-event-tab">
                        <?php esc_html_e( 'Travel & stay', 'race-series-manager' ); ?>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $reg_iframe ) ) : ?>
                    <a href="#event-registration" class="rsm-event-tab">
                        <?php esc_html_e( 'Registration', 'race-series-manager' ); ?>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $part_iframe ) ) : ?>
                    <a href="#event-participants" class="rsm-event-tab">
                        <?php esc_html_e( 'Participants', 'race-series-manager' ); ?>
                    </a>
                <?php endif; ?>

                <?php if ( ! empty( $live_iframe ) ) : ?>
                    <a href="#event-live" class="rsm-event-tab">
                        <?php esc_html_e( 'Live', 'race-series-manager' ); ?>
                    </a>
                <?php endif; ?>
            </nav>

            <!-- MAIN EVENT CONTENT (WordPress editor) -->
            <section id="event-info" class="rsm-race-section rsm-race-section--content rsm-event-section rsm-event-section--info">
                <?php the_content(); ?>
            </section>

            <!-- 1. RACE ANNOUNCEMENT -->
            <?php if ( ! empty( $announcement ) ) : ?>
                <section id="event-announcement" class="rsm-race-section rsm-event-section rsm-event-section--announcement">
                    <h2 class="rsm-section-title rsm-event-section-title rsm-event-section-title--announcement">
                        <span class="rsm-event-section-icon">📢</span>
                        <span><?php esc_html_e( 'Race announcement', 'race-series-manager' ); ?></span>
                    </h2>
                    <hr class="rsm-section-divider" />
                    <div class="rsm-event-section-body">
                        <?php echo wp_kses_post( wpautop( $announcement ) ); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 2. RULES & REGULATIONS -->
            <?php if ( ! empty( $rules ) ) : ?>
                <section id="event-rules" class="rsm-race-section rsm-event-section rsm-event-section--rules">
                    <h2 class="rsm-section-title rsm-event-section-title rsm-event-section-title--rules">
                        <span class="rsm-event-section-icon">⚖️</span>
                        <span><?php esc_html_e( 'Rules & regulations', 'race-series-manager' ); ?></span>
                    </h2>
                    <hr class="rsm-section-divider" />
                    <div class="rsm-event-section-body">
                        <?php echo wp_kses_post( wpautop( $rules ) ); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 3. SCHEDULE -->
            <?php if ( ! empty( $schedule ) ) : ?>
                <section id="event-schedule" class="rsm-race-section rsm-event-section rsm-event-section--schedule">
                    <h2 class="rsm-section-title rsm-event-section-title rsm-event-section-title--schedule">
                        <span class="rsm-event-section-icon">🗓</span>
                        <span><?php esc_html_e( 'Schedule', 'race-series-manager' ); ?></span>
                    </h2>
                    <hr class="rsm-section-divider" />
                    <div class="rsm-event-section-body">
                        <?php echo wp_kses_post( wpautop( $schedule ) ); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 4. RACE ACCESS -->
            <?php if ( ! empty( $access ) ) : ?>
                <section id="event-access" class="rsm-race-section rsm-event-section rsm-event-section--access">
                    <h2 class="rsm-section-title rsm-event-section-title rsm-event-section-title--access">
                        <span class="rsm-event-section-icon">🚌</span>
                        <span><?php esc_html_e( 'Race access', 'race-series-manager' ); ?></span>
                    </h2>
                    <hr class="rsm-section-divider" />
                    <div class="rsm-event-section-body">
                        <?php echo wp_kses_post( wpautop( $access ) ); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 5. TRAVEL & ACCOMMODATION -->
            <?php if ( ! empty( $travel ) ) : ?>
                <section id="event-travel" class="rsm-race-section rsm-event-section rsm-event-section--travel">
                    <h2 class="rsm-section-title rsm-event-section-title rsm-event-section-title--travel">
                        <span class="rsm-event-section-icon">🛏</span>
                        <span><?php esc_html_e( 'Travel & accommodation', 'race-series-manager' ); ?></span>
                    </h2>
                    <hr class="rsm-section-divider" />
                    <div class="rsm-event-section-body">
                        <?php echo wp_kses_post( wpautop( $travel ) ); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 6. REGISTRATION IFRAME -->
            <?php if ( ! empty( $reg_iframe ) ) : ?>
                <section id="event-registration" class="rsm-race-section rsm-event-section rsm-event-section--registration">
                    <h2 class="rsm-section-title rsm-event-section-title rsm-event-section-title--registration">
                        <span class="rsm-event-section-icon">📝</span>
                        <span><?php esc_html_e( 'Registration', 'race-series-manager' ); ?></span>
                    </h2>
                    <hr class="rsm-section-divider" />
                    <div class="rsm-event-section-body rsm-event-iframe-wrapper">
                        <?php echo $reg_iframe; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 7. PARTICIPANTS IFRAME -->
            <?php if ( ! empty( $part_iframe ) ) : ?>
                <section id="event-participants" class="rsm-race-section rsm-event-section rsm-event-section--participants">
                    <h2 class="rsm-section-title rsm-event-section-title rsm-event-section-title--participants">
                        <span class="rsm-event-section-icon">👥</span>
                        <span><?php esc_html_e( 'Participants', 'race-series-manager' ); ?></span>
                    </h2>
                    <hr class="rsm-section-divider" />
                    <div class="rsm-event-section-body rsm-event-iframe-wrapper">
                        <?php echo $part_iframe; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 8. LIVE IFRAME -->
            <?php if ( ! empty( $live_iframe ) ) : ?>
                <section id="event-live" class="rsm-race-section rsm-event-section rsm-event-section--live">
                    <h2 class="rsm-section-title rsm-event-section-title rsm-event-section-title--live">
                        <span class="rsm-event-section-icon">📡</span>
                        <span><?php esc_html_e( 'Live', 'race-series-manager' ); ?></span>
                    </h2>
                    <hr class="rsm-section-divider" />
                    <div class="rsm-event-section-body rsm-event-iframe-wrapper">
                        <?php echo $live_iframe; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </section>
            <?php endif; ?>

        </main>

        <!-- SIDEBAR -->
        <aside class="rsm-race-sidebar rsm-event-sidebar">
            <div class="rsm-race-sidebar-inner rsm-event-sidebar-inner">

                <!-- ACTION BUTTONS -->
                <div class="rsm-event-sidebar-actions" style="margin-bottom:16px;">

                    <?php if ( $reg_btn_url ) : ?>
                        <a href="<?php echo esc_url( $reg_btn_url ); ?>"
                           class="rsm-summary-btn"
                           <?php echo $reg_btn_attr; ?>>
                            <?php esc_html_e( 'Registration', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $part_btn_url ) : ?>
                        <a href="<?php echo esc_url( $part_btn_url ); ?>"
                           class="rsm-summary-btn"
                           <?php echo $part_btn_attr; ?>>
                            <?php esc_html_e( 'Participants', 'race-series-manager' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $live_btn_url ) : ?>
                        <a href="<?php echo esc_url( $live_btn_url ); ?>"
                           class="rsm-summary-btn"
                           <?php echo $live_btn_attr; ?>>
                            <?php esc_html_e( 'Live', 'race-series-manager' ); ?>
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

                <h2 class="rsm-sidebar-title">
                    <?php esc_html_e( 'Event races', 'race-series-manager' ); ?>
                </h2>

                <?php if ( $linked_races->have_posts() ) : ?>
                    <ul class="rsm-event-races-list">
                        <?php
                        while ( $linked_races->have_posts() ) :
                            $linked_races->the_post();
                            $race_id   = get_the_ID();
                            $distance  = get_post_meta( $race_id, '_rsm_race_distance', true );
                            $elevation = get_post_meta( $race_id, '_rsm_race_elevation', true );
                            ?>
                            <li class="rsm-event-race-item">
                                <a href="<?php echo esc_url( get_permalink( $race_id ) ); ?>" class="rsm-event-race-link">
                                    <span class="rsm-event-race-title"><?php echo esc_html( get_the_title( $race_id ) ); ?></span>
                                    <?php if ( $distance || $elevation ) : ?>
                                        <span class="rsm-event-race-meta">
                                            <?php if ( $distance ) : ?>
                                                <span class="rsm-event-race-distance"><?php echo esc_html( $distance ); ?></span>
                                            <?php endif; ?>
                                            <?php if ( $elevation ) : ?>
                                                <span class="rsm-event-race-elevation"> · <?php echo esc_html( $elevation ); ?></span>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    </ul>
                <?php else : ?>
                    <p><?php esc_html_e( 'No races are linked to this event yet.', 'race-series-manager' ); ?></p>
                <?php endif; ?>

            </div>
        </aside>

    </div>
</div>

<script>
(function() {
    var tabs = document.querySelectorAll('.rsm-event-tab');

    if (!tabs.length) return;

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            var targetId = this.getAttribute('href');
            if (!targetId || !targetId.startsWith('#')) return;

            var section = document.querySelector(targetId);
            if (!section) return;

            e.preventDefault();

            tabs.forEach(function(t) { t.classList.remove('is-active'); });
            this.classList.add('is-active');

            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
</script>

<?php
endwhile;

get_footer();
