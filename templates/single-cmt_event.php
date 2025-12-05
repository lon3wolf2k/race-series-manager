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

    $event_id = get_the_ID();

    // Extra event sections from meta:
    $announcement  = get_post_meta( $event_id, '_rsm_event_announcement', true );
    $rules         = get_post_meta( $event_id, '_rsm_event_rules', true );
    $schedule      = get_post_meta( $event_id, '_rsm_event_schedule', true );
    $access        = get_post_meta( $event_id, '_rsm_event_access', true );
    $travel        = get_post_meta( $event_id, '_rsm_event_travel', true );

    // Fetch linked races for sidebar list.
    $linked_races = new WP_Query( array(
        'post_type'      => 'cmt_race',
        'posts_per_page' => -1,
        'meta_key'       => '_rsm_race_event_id',
        'meta_value'     => $event_id,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );

    // Results page URL (αν έχει δημιουργηθεί η σελίδα event-results και υπάρχει helper).
    $results_url = function_exists( 'rsm_get_results_page_url' )
        ? rsm_get_results_page_url( $event_id )
        : '';
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

        </main>

        <!-- SIDEBAR -->
        <aside class="rsm-race-sidebar rsm-event-sidebar">
            <div class="rsm-race-sidebar-inner rsm-event-sidebar-inner">

                <h2 class="rsm-sidebar-title">
                    <?php esc_html_e( 'Event races', 'race-series-manager' ); ?>
                </h2>

                <?php if ( $results_url ) : ?>
                    <div class="rsm-event-sidebar-actions" style="margin-bottom:12px;">
                        <a href="<?php echo esc_url( $results_url ); ?>"
                           class="rsm-summary-btn rsm-summary-btn-outline rsm-event-results-btn">
                            <?php esc_html_e( 'Results', 'race-series-manager' ); ?>
                        </a>
                    </div>
                <?php endif; ?>

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

            // Set active tab
            tabs.forEach(function(t) { t.classList.remove('is-active'); });
            this.classList.add('is-active');

            // Smooth scroll to section
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
</script>

<?php
endwhile;

get_footer();
