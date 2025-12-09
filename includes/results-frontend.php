<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Front-end για Event results ΧΩΡΙΣ extra WordPress page.
 *
 * Λογική:
 * - Στο single Event βάζουμε κουμπί που πάει στο:
 *     /event-slug/?rsm_event_results=1
 * - Αυτό το αρχείο “πιάνει” το query param στο template_redirect
 *   και εμφανίζει μια σελίδα με όλα τα editions/years (cmt_result)
 *   που είναι συνδεδεμένα με το συγκεκριμένο Event.
 */

/**
 * Δίνει URL αποτελεσμάτων για ένα event:
 *   /event-slug/?rsm_event_results=1
 */
function rsm_get_results_page_url( $event_id ) {
    $event_id = intval( $event_id );
    if ( ! $event_id ) {
        return '';
    }

    $url = get_permalink( $event_id );
    if ( ! $url ) {
        return '';
    }

    $url = add_query_arg( 'rsm_event_results', '1', $url );
    return $url;
}

/**
 * Χτίζει το HTML block με τα αποτελέσματα ενός event.
 */
function rsm_build_event_results_block( $event_id ) {
    $event_id = intval( $event_id );

    $args = array(
        'post_type'      => 'cmt_result',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'meta_value',
        'meta_key'       => '_rsm_res_edition',
        'order'          => 'DESC',
        'meta_query'     => array(
            array(
                'key'   => '_rsm_res_event_id',
                'value' => $event_id,
            ),
        ),
    );

    $q = new WP_Query( $args );

    ob_start();
    ?>
    <div class="rsm-event-results-page">
        <h1 class="rsm-event-results-title">
            <?php
            $event_title = get_the_title( $event_id );
            if ( $event_title ) {
                echo esc_html(
                    sprintf(
                        /* translators: %s: event title */
                        __( 'Results for %s', 'race-series-manager' ),
                        $event_title
                    )
                );
            } else {
                esc_html_e( 'Event results', 'race-series-manager' );
            }
            ?>
        </h1>

        <?php if ( $q->have_posts() ) : ?>
            <p class="rsm-event-results-intro">
                <?php esc_html_e( 'Select an edition/year to view the full results file or external results page.', 'race-series-manager' ); ?>
            </p>

            <div class="rsm-event-results-list">
                <?php
                while ( $q->have_posts() ) :
                    $q->the_post();
                    $res_id   = get_the_ID();
                    $event_name  = get_post_meta( $res_id, '_rsm_res_event_name', true );
                    $edition  = get_post_meta( $res_id, '_rsm_res_edition', true );
                    $pdf_id   = get_post_meta( $res_id, '_rsm_res_pdf_id', true );
                    $external = get_post_meta( $res_id, '_rsm_res_external_url', true );

                    $pdf_url = '';
                    if ( $pdf_id ) {
                        $pdf_url = wp_get_attachment_url( $pdf_id );
                    }

                    // Επιλέγουμε πού θα πάει ο χρήστης: προτεραιότητα στο external URL
                    $target_url   = '';
                    $target_label = '';

                    if ( $external ) {
                        $target_url   = $external;
                        $target_label = __( 'Open results page', 'race-series-manager' );
                    } elseif ( $pdf_url ) {
                        $target_url   = $pdf_url;
                        $target_label = __( 'Open results PDF', 'race-series-manager' );
                    }

                    ?>
                    <div class="rsm-event-result-item">
                        <div class="rsm-event-result-text">
                            <?php if ( $edition ) : ?>
                                <h2 class="rsm-event-result-edition"><?php echo esc_html( $edition ); ?></h2>
                            <?php else : ?>
                                <h2 class="rsm-event-result-edition"><?php esc_html_e( 'Results', 'race-series-manager' ); ?></h2>
                            <?php endif; ?>

                            <?php if ( $event_name ) : ?>
                                <div class="rsm-event-result-name"><?php echo esc_html( $event_name ); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="rsm-event-result-actions">
                            <?php if ( $target_url ) : ?>
                                <a href="<?php echo esc_url( $target_url ); ?>"
                                   class="rsm-btn rsm-btn-outline"
                                   target="_blank"
                                   rel="noopener">
                                    <?php echo esc_html( $target_label ); ?>
                                </a>
                            <?php else : ?>
                                <span class="rsm-event-result-missing">
                                    <?php esc_html_e( 'No file or link set for this edition.', 'race-series-manager' ); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            <p>
                <?php esc_html_e( 'No results found for this event yet.', 'race-series-manager' ); ?>
            </p>
        <?php endif; ?>
    </div>
    <?php

    return ob_get_clean();
}

/**
 * template_redirect: αν είμαστε σε single Event ΚΑΙ υπάρχει ?rsm_event_results=1,
 * τότε δείχνουμε τη σελίδα αποτελεσμάτων του event.
 */
function rsm_maybe_render_event_results_page() {
    if ( ! is_singular( 'cmt_event' ) ) {
        return;
    }

    if ( ! isset( $_GET['rsm_event_results'] ) ) {
        return;
    }

    $event_id = get_queried_object_id();
    if ( ! $event_id ) {
        return;
    }

    // Βάζουμε status 200 και φορτώνουμε κανονικά το header/footer του theme.
    status_header( 200 );

    get_header();

    echo '<div class="rsm-event-results-page-wrapper">';
    echo rsm_build_event_results_block( $event_id );
    echo '</div>';

    get_footer();
    exit;
}
add_action( 'template_redirect', 'rsm_maybe_render_event_results_page' );
