<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Results meta boxes
 *
 * Κάθε Result post κρατά:
 * - Event name (κείμενο, π.χ. Corfu Mountain Trail)
 * - Edition / year (π.χ. 2025 ή 14th edition)
 * - Optional linked Event (cmt_event)
 * - PDF αρχείο με ΟΛΑ τα αποτελέσματα του event
 * - External URL (σελίδα αποτελεσμάτων)
 */

function rsm_add_results_meta_boxes() {
    add_meta_box(
        'rsm_result_details',
        esc_html__( 'Result details', 'race-series-manager' ),
        'rsm_result_details_meta_box_callback',
        'cmt_result',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'rsm_add_results_meta_boxes' );

/**
 * Render meta box.
 */
function rsm_result_details_meta_box_callback( $post ) {

    wp_nonce_field( 'rsm_save_result_details', 'rsm_result_details_nonce' );
    wp_enqueue_media();

    $event_name   = get_post_meta( $post->ID, '_rsm_res_event_name', true );
    $edition      = get_post_meta( $post->ID, '_rsm_res_edition', true );
    $linked_event = get_post_meta( $post->ID, '_rsm_res_event_id', true );
    $pdf_id       = get_post_meta( $post->ID, '_rsm_res_pdf_id', true );
    $external_url = get_post_meta( $post->ID, '_rsm_res_external_url', true );

    $pdf_url = '';
    if ( $pdf_id ) {
        $pdf_src = wp_get_attachment_url( $pdf_id );
        if ( $pdf_src ) {
            $pdf_url = $pdf_src;
        }
    }

    // Events για dropdown (προαιρετικό link)
    $events = get_posts( array(
        'post_type'      => 'cmt_event',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="rsm_res_event_name"><?php esc_html_e( 'Event name (text)', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="text" name="rsm_res_event_name" id="rsm_res_event_name" class="regular-text" value="<?php echo esc_attr( $event_name ); ?>">
                <p class="description">
                    <?php esc_html_e( 'e.g. Corfu Mountain Trail. Used even if the actual event does not exist on the site.', 'race-series-manager' ); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th><label for="rsm_res_edition"><?php esc_html_e( 'Edition / year', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="text" name="rsm_res_edition" id="rsm_res_edition" class="regular-text" value="<?php echo esc_attr( $edition ); ?>">
                <p class="description">
                    <?php esc_html_e( 'e.g. 2025, 14th edition, etc.', 'race-series-manager' ); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th><label for="rsm_res_event_id"><?php esc_html_e( 'Linked event (optional)', 'race-series-manager' ); ?></label></th>
            <td>
                <select name="rsm_res_event_id" id="rsm_res_event_id">
                    <option value=""><?php esc_html_e( '— None —', 'race-series-manager' ); ?></option>
                    <?php foreach ( $events as $event ) : ?>
                        <option value="<?php echo esc_attr( $event->ID ); ?>" <?php selected( $linked_event, $event->ID ); ?>>
                            <?php echo esc_html( get_the_title( $event ) ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">
                    <?php esc_html_e( 'Optionally link this result record to an existing Event post for easier grouping/filtering.', 'race-series-manager' ); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th><?php esc_html_e( 'Results PDF (whole event)', 'race-series-manager' ); ?></th>
            <td>
                <input type="hidden" id="rsm_res_pdf_id" name="rsm_res_pdf_id" value="<?php echo esc_attr( $pdf_id ); ?>">
                <button type="button" class="button" id="rsm_res_pdf_button">
                    <?php echo $pdf_id ? esc_html__( 'Change PDF', 'race-series-manager' ) : esc_html__( 'Select PDF', 'race-series-manager' ); ?>
                </button>
                <button type="button" class="button" id="rsm_res_pdf_remove" <?php echo $pdf_id ? '' : 'style="display:none;"'; ?>>
                    <?php esc_html_e( 'Remove', 'race-series-manager' ); ?>
                </button>
                <div id="rsm_res_pdf_preview" style="margin-top:6px;font-size:12px;">
                    <?php if ( $pdf_url ) : ?>
                        <a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( basename( $pdf_url ) ); ?></a>
                    <?php endif; ?>
                </div>
                <p class="description">
                    <?php esc_html_e( 'Upload one PDF containing all race results for this event/edition.', 'race-series-manager' ); ?>
                </p>
            </td>
        </tr>

        <tr>
            <th><label for="rsm_res_external_url"><?php esc_html_e( 'External results URL', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="url" name="rsm_res_external_url" id="rsm_res_external_url" class="regular-text" value="<?php echo esc_attr( $external_url ); ?>">
                <p class="description">
                    <?php esc_html_e( 'If results are hosted on another site (timing provider, PDF viewer, etc.). Can be used alone or together with the uploaded PDF.', 'race-series-manager' ); ?>
                </p>
            </td>
        </tr>
    </table>

    <script>
    jQuery(document).ready(function($){
        var pdfFrame;

        $('#rsm_res_pdf_button').on('click', function(e){
            e.preventDefault();

            if (pdfFrame) {
                pdfFrame.open();
                return;
            }

            pdfFrame = wp.media({
                title: '<?php echo esc_js( __( 'Select results PDF', 'race-series-manager' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use this file', 'race-series-manager' ) ); ?>' },
                multiple: false
            });

            pdfFrame.on('select', function(){
                var attachment = pdfFrame.state().get('selection').first().toJSON();
                $('#rsm_res_pdf_id').val(attachment.id);
                $('#rsm_res_pdf_preview').html(
                    '<a href="'+attachment.url+'" target="_blank" rel="noopener">'+attachment.filename+'</a>'
                );
                $('#rsm_res_pdf_remove').show();
                $('#rsm_res_pdf_button').text('<?php echo esc_js( __( 'Change PDF', 'race-series-manager' ) ); ?>');
            });

            pdfFrame.open();
        });

        $('#rsm_res_pdf_remove').on('click', function(e){
            e.preventDefault();
            $('#rsm_res_pdf_id').val('');
            $('#rsm_res_pdf_preview').empty();
            $('#rsm_res_pdf_remove').hide();
            $('#rsm_res_pdf_button').text('<?php echo esc_js( __( 'Select PDF', 'race-series-manager' ) ); ?>');
        });
    });
    </script>
    <?php
}

/**
 * Save results meta.
 */
function rsm_save_result_details( $post_id ) {

    if ( ! isset( $_POST['rsm_result_details_nonce'] ) ||
         ! wp_verify_nonce( $_POST['rsm_result_details_nonce'], 'rsm_save_result_details' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['post_type'] ) && 'cmt_result' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    } else {
        return;
    }

    $event_name   = isset( $_POST['rsm_res_event_name'] )   ? sanitize_text_field( $_POST['rsm_res_event_name'] )   : '';
    $edition      = isset( $_POST['rsm_res_edition'] )      ? sanitize_text_field( $_POST['rsm_res_edition'] )      : '';
    $linked_event = isset( $_POST['rsm_res_event_id'] )     ? intval( $_POST['rsm_res_event_id'] )                  : 0;
    $pdf_id       = isset( $_POST['rsm_res_pdf_id'] )       ? intval( $_POST['rsm_res_pdf_id'] )                    : 0;
    $external_url = isset( $_POST['rsm_res_external_url'] ) ? esc_url_raw( $_POST['rsm_res_external_url'] )         : '';

    update_post_meta( $post_id, '_rsm_res_event_name',   $event_name );
    update_post_meta( $post_id, '_rsm_res_edition',      $edition );
    update_post_meta( $post_id, '_rsm_res_event_id',     $linked_event );
    update_post_meta( $post_id, '_rsm_res_pdf_id',       $pdf_id );
    update_post_meta( $post_id, '_rsm_res_external_url', $external_url );
}
add_action( 'save_post_cmt_result', 'rsm_save_result_details' );
