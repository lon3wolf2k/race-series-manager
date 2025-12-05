<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Event meta boxes (announcement, rules, schedule, access, travel, logo, contact)
 */

function rsm_add_event_meta_boxes() {
    add_meta_box(
        'rsm_event_sections',
        esc_html__( 'Event sections', 'race-series-manager' ),
        'rsm_event_sections_meta_box_callback',
        'cmt_event',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'rsm_add_event_meta_boxes' );

/**
 * Render Event Sections meta box.
 */
function rsm_event_sections_meta_box_callback( $post ) {

    wp_nonce_field( 'rsm_save_event_sections', 'rsm_event_sections_nonce' );

    wp_enqueue_media();

    $logo_id      = get_post_meta( $post->ID, '_rsm_event_logo_id', true );
    $announcement = get_post_meta( $post->ID, '_rsm_event_announcement', true );
    $rules        = get_post_meta( $post->ID, '_rsm_event_rules', true );
    $schedule     = get_post_meta( $post->ID, '_rsm_event_schedule', true );
    $access       = get_post_meta( $post->ID, '_rsm_event_access', true );
    $travel       = get_post_meta( $post->ID, '_rsm_event_travel', true );
    $contact      = get_post_meta( $post->ID, '_rsm_event_contact', true );

    $logo_src = '';
    if ( $logo_id ) {
        $img = wp_get_attachment_image_src( $logo_id, 'medium' );
        if ( $img ) {
            $logo_src = $img[0];
        }
    }
    ?>
    <p style="margin-bottom: 12px;">
        <?php esc_html_e( 'These sections are additional rich-text blocks for the event page, separate from the main content editor.', 'race-series-manager' ); ?>
    </p>

    <h3 style="margin-top: 0;"><?php esc_html_e( 'Event logo (for PDFs and branding)', 'race-series-manager' ); ?></h3>
    <p><small><?php esc_html_e( 'This logo will be used in generated PDFs. It will not automatically appear on the event page.', 'race-series-manager' ); ?></small></p>

    <div style="margin-bottom: 16px;">
        <input type="hidden" id="rsm_event_logo_id" name="rsm_event_logo_id" value="<?php echo esc_attr( $logo_id ); ?>">
        <button type="button" class="button" id="rsm_event_logo_button">
            <?php
            echo $logo_id
                ? esc_html__( 'Change logo', 'race-series-manager' )
                : esc_html__( 'Select logo', 'race-series-manager' );
            ?>
        </button>
        <button type="button" class="button" id="rsm_event_logo_remove" <?php echo $logo_id ? '' : 'style="display:none;"'; ?>>
            <?php esc_html_e( 'Remove', 'race-series-manager' ); ?>
        </button>

        <div id="rsm_event_logo_preview" style="margin-top:10px;">
            <?php if ( $logo_src ) : ?>
                <img src="<?php echo esc_url( $logo_src ); ?>" alt="" style="max-width:180px;height:auto;border:1px solid #ddd;padding:4px;background:#fff;">
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <h3 style="margin-top: 0.8em;"><?php esc_html_e( 'Race announcement', 'race-series-manager' ); ?></h3>
    <p><small><?php esc_html_e( 'Introductory text, general info and highlights of the event.', 'race-series-manager' ); ?></small></p>
    <?php
    wp_editor(
        $announcement,
        'rsm_event_announcement',
        array(
            'textarea_name' => 'rsm_event_announcement',
            'media_buttons' => true,
            'textarea_rows' => 6,
        )
    );
    ?>

    <h3 style="margin-top: 1.8em;"><?php esc_html_e( 'Rules & regulations', 'race-series-manager' ); ?></h3>
    <p><small><?php esc_html_e( 'Official rules, mandatory equipment, terms and conditions.', 'race-series-manager' ); ?></small></p>
    <?php
    wp_editor(
        $rules,
        'rsm_event_rules',
        array(
            'textarea_name' => 'rsm_event_rules',
            'media_buttons' => true,
            'textarea_rows' => 8,
        )
    );
    ?>

    <h3 style="margin-top: 1.8em;"><?php esc_html_e( 'Schedule', 'race-series-manager' ); ?></h3>
    <p><small><?php esc_html_e( 'Race weekend schedule, registrations, briefings, bus departures, etc.', 'race-series-manager' ); ?></small></p>
    <?php
    wp_editor(
        $schedule,
        'rsm_event_schedule',
        array(
            'textarea_name' => 'rsm_event_schedule',
            'media_buttons' => true,
            'textarea_rows' => 8,
        )
    );
    ?>

    <h3 style="margin-top: 1.8em;"><?php esc_html_e( 'Race access', 'race-series-manager' ); ?></h3>
    <p><small><?php esc_html_e( 'How to reach the start area, transport routes, parking, shuttles.', 'race-series-manager' ); ?></small></p>
    <?php
    wp_editor(
        $access,
        'rsm_event_access',
        array(
            'textarea_name' => 'rsm_event_access',
            'media_buttons' => true,
            'textarea_rows' => 6,
        )
    );
    ?>

    <h3 style="margin-top: 1.8em;"><?php esc_html_e( 'Travel & accommodation', 'race-series-manager' ); ?></h3>
    <p><small><?php esc_html_e( 'How to get to the region, accommodation suggestions and local info.', 'race-series-manager' ); ?></small></p>
    <?php
    wp_editor(
        $travel,
        'rsm_event_travel',
        array(
            'textarea_name' => 'rsm_event_travel',
            'media_buttons' => true,
            'textarea_rows' => 8,
        )
    );
    ?>

    <h3 style="margin-top: 1.8em;"><?php esc_html_e( 'Contact details (PDF only)', 'race-series-manager' ); ?></h3>
    <p><small><?php esc_html_e( 'Email, phone, website or social links for the booklet footer. This field is only used in the generated PDF, not on the event page.', 'race-series-manager' ); ?></small></p>
    <textarea
        name="rsm_event_contact"
        id="rsm_event_contact"
        rows="4"
        style="width:100%;"><?php echo esc_textarea( $contact ); ?></textarea>

    <script>
    jQuery(document).ready(function($){
        var logoFrame;

        $('#rsm_event_logo_button').on('click', function(e){
            e.preventDefault();

            if (logoFrame) {
                logoFrame.open();
                return;
            }

            logoFrame = wp.media({
                title: '<?php echo esc_js( __( 'Select event logo', 'race-series-manager' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use this logo', 'race-series-manager' ) ); ?>' },
                multiple: false
            });

            logoFrame.on('select', function(){
                var attachment = logoFrame.state().get('selection').first().toJSON();
                $('#rsm_event_logo_id').val(attachment.id);
                $('#rsm_event_logo_preview').html(
                    '<img src="' + attachment.url + '" style="max-width:180px;height:auto;border:1px solid #ddd;padding:4px;background:#fff;" />'
                );
                $('#rsm_event_logo_remove').show();
                $('#rsm_event_logo_button').text('<?php echo esc_js( __( 'Change logo', 'race-series-manager' ) ); ?>');
            });

            logoFrame.open();
        });

        $('#rsm_event_logo_remove').on('click', function(e){
            e.preventDefault();
            $('#rsm_event_logo_id').val('');
            $('#rsm_event_logo_preview').empty();
            $('#rsm_event_logo_remove').hide();
            $('#rsm_event_logo_button').text('<?php echo esc_js( __( 'Select logo', 'race-series-manager' ) ); ?>');
        });
    });
    </script>

    <?php
}

/**
 * Save Event meta.
 */
function rsm_save_event_sections( $post_id ) {

    if ( ! isset( $_POST['rsm_event_sections_nonce'] ) ||
         ! wp_verify_nonce( $_POST['rsm_event_sections_nonce'], 'rsm_save_event_sections' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['post_type'] ) && 'cmt_event' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    } else {
        return;
    }

    $logo_id = isset( $_POST['rsm_event_logo_id'] ) ? intval( $_POST['rsm_event_logo_id'] ) : 0;
    update_post_meta( $post_id, '_rsm_event_logo_id', $logo_id );

    $announcement = isset( $_POST['rsm_event_announcement'] ) ? wp_kses_post( $_POST['rsm_event_announcement'] ) : '';
    $rules        = isset( $_POST['rsm_event_rules'] )        ? wp_kses_post( $_POST['rsm_event_rules'] )        : '';
    $schedule     = isset( $_POST['rsm_event_schedule'] )     ? wp_kses_post( $_POST['rsm_event_schedule'] )     : '';
    $access       = isset( $_POST['rsm_event_access'] )       ? wp_kses_post( $_POST['rsm_event_access'] )       : '';
    $travel       = isset( $_POST['rsm_event_travel'] )       ? wp_kses_post( $_POST['rsm_event_travel'] )       : '';
    $contact      = isset( $_POST['rsm_event_contact'] )      ? wp_kses_post( $_POST['rsm_event_contact'] )      : '';

    update_post_meta( $post_id, '_rsm_event_announcement', $announcement );
    update_post_meta( $post_id, '_rsm_event_rules',        $rules );
    update_post_meta( $post_id, '_rsm_event_schedule',     $schedule );
    update_post_meta( $post_id, '_rsm_event_access',       $access );
    update_post_meta( $post_id, '_rsm_event_travel',       $travel );
    update_post_meta( $post_id, '_rsm_event_contact',      $contact );
}
add_action( 'save_post_cmt_event', 'rsm_save_event_sections' );
