<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Event meta boxes for cmt_event
 */

function rsm_add_event_meta_boxes() {

    // Κύριες ενότητες περιεχομένου (Announcement, Rules, Schedule, Access, Travel)
    add_meta_box(
        'rsm_event_content',
        esc_html__( 'Event content sections', 'race-series-manager' ),
        'rsm_event_content_meta_box_callback',
        'cmt_event',
        'normal',
        'high'
    );

    // Logo & Contact
    add_meta_box(
        'rsm_event_logo_contact',
        esc_html__( 'Event logo & contact', 'race-series-manager' ),
        'rsm_event_logo_contact_meta_box_callback',
        'cmt_event',
        'side',
        'default'
    );

    // Registration / Participants / Live
    add_meta_box(
        'rsm_event_registration',
        esc_html__( 'Registration & Live', 'race-series-manager' ),
        'rsm_event_registration_meta_box_callback',
        'cmt_event',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'rsm_add_event_meta_boxes' );

/**
 * Content meta box (announcement, rules, schedule, access, travel)
 */
function rsm_event_content_meta_box_callback( $post ) {

    wp_nonce_field( 'rsm_save_event_content', 'rsm_event_content_nonce' );

    $announcement = get_post_meta( $post->ID, '_rsm_event_announcement', true );
    $rules        = get_post_meta( $post->ID, '_rsm_event_rules', true );
    $schedule     = get_post_meta( $post->ID, '_rsm_event_schedule', true );
    $access       = get_post_meta( $post->ID, '_rsm_event_access', true );
    $travel       = get_post_meta( $post->ID, '_rsm_event_travel', true );
    ?>
    <p>
        <label for="rsm_event_announcement"><strong><?php esc_html_e( 'Race announcement', 'race-series-manager' ); ?></strong></label>
        <textarea name="rsm_event_announcement" id="rsm_event_announcement" rows="4" class="large-text"><?php echo esc_textarea( $announcement ); ?></textarea>
    </p>

    <p>
        <label for="rsm_event_rules"><strong><?php esc_html_e( 'Rules & regulations', 'race-series-manager' ); ?></strong></label>
        <textarea name="rsm_event_rules" id="rsm_event_rules" rows="4" class="large-text"><?php echo esc_textarea( $rules ); ?></textarea>
    </p>

    <p>
        <label for="rsm_event_schedule"><strong><?php esc_html_e( 'Schedule / program', 'race-series-manager' ); ?></strong></label>
        <textarea name="rsm_event_schedule" id="rsm_event_schedule" rows="4" class="large-text"><?php echo esc_textarea( $schedule ); ?></textarea>
        <span class="description"><?php esc_html_e( 'This schedule is also used in the PDF race booklet.', 'race-series-manager' ); ?></span>
    </p>

    <p>
        <label for="rsm_event_access"><strong><?php esc_html_e( 'Race access', 'race-series-manager' ); ?></strong></label>
        <textarea name="rsm_event_access" id="rsm_event_access" rows="4" class="large-text"><?php echo esc_textarea( $access ); ?></textarea>
        <span class="description"><?php esc_html_e( 'Access info is also used in the PDF race booklet.', 'race-series-manager' ); ?></span>
    </p>

    <p>
        <label for="rsm_event_travel"><strong><?php esc_html_e( 'Travel & accommodation', 'race-series-manager' ); ?></strong></label>
        <textarea name="rsm_event_travel" id="rsm_event_travel" rows="4" class="large-text"><?php echo esc_textarea( $travel ); ?></textarea>
    </p>
    <?php
}

/**
 * Logo & contact meta box
 */
function rsm_event_logo_contact_meta_box_callback( $post ) {

    wp_nonce_field( 'rsm_save_event_logo_contact', 'rsm_event_logo_contact_nonce' );

    $logo_id  = get_post_meta( $post->ID, '_rsm_event_logo_id', true );
    $contact  = get_post_meta( $post->ID, '_rsm_event_contact', true );

    wp_enqueue_media();
    ?>
    <p>
        <strong><?php esc_html_e( 'Event logo', 'race-series-manager' ); ?></strong><br>
        <input type="hidden" id="rsm_event_logo_id" name="rsm_event_logo_id" value="<?php echo esc_attr( $logo_id ); ?>">
        <button type="button" class="button" id="rsm_event_logo_button">
            <?php echo $logo_id ? esc_html__( 'Change logo', 'race-series-manager' ) : esc_html__( 'Select logo', 'race-series-manager' ); ?>
        </button>
        <button type="button" class="button" id="rsm_event_logo_remove" <?php echo $logo_id ? '' : 'style="display:none;"'; ?>>
            <?php esc_html_e( 'Remove', 'race-series-manager' ); ?>
        </button>
    </p>
    <div id="rsm_event_logo_preview" style="margin-top:8px;">
        <?php
        if ( $logo_id ) {
            $img = wp_get_attachment_image_src( $logo_id, 'medium' );
            if ( $img ) {
                echo '<img src="' . esc_url( $img[0] ) . '" style="max-width:200px;height:auto;border:1px solid #ddd;padding:4px;background:#fff;">';
            }
        }
        ?>
    </div>

    <hr>

    <p>
        <label for="rsm_event_contact"><strong><?php esc_html_e( 'Contact details (for PDF footer)', 'race-series-manager' ); ?></strong></label>
        <textarea name="rsm_event_contact" id="rsm_event_contact" rows="4" class="large-text"><?php echo esc_textarea( $contact ); ?></textarea>
        <span class="description"><?php esc_html_e( 'These contact details appear at the bottom of the race booklet PDF.', 'race-series-manager' ); ?></span>
    </p>

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
                    '<img src="'+attachment.url+'" style="max-width:200px;height:auto;border:1px solid #ddd;padding:4px;background:#fff;">'
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
 * Registration / Participants / Live meta box
 */
function rsm_event_registration_meta_box_callback( $post ) {

    wp_nonce_field( 'rsm_save_event_registration', 'rsm_event_registration_nonce' );

    $reg_url   = get_post_meta( $post->ID, '_rsm_event_registration_url', true );
    $part_url  = get_post_meta( $post->ID, '_rsm_event_participants_url', true );
    $live_url  = get_post_meta( $post->ID, '_rsm_event_live_url', true );
    ?>
    <p>
        <label for="rsm_event_registration_url"><strong><?php esc_html_e( 'Registration URL', 'race-series-manager' ); ?></strong></label><br>
        <input type="url" name="rsm_event_registration_url" id="rsm_event_registration_url" class="regular-text" value="<?php echo esc_attr( $reg_url ); ?>">
        <span class="description">
            <?php esc_html_e( 'This link will be used for the Registration button on the event and race pages.', 'race-series-manager' ); ?>
        </span>
    </p>

    <p>
        <label for="rsm_event_participants_url"><strong><?php esc_html_e( 'Participants URL', 'race-series-manager' ); ?></strong></label><br>
        <input type="url" name="rsm_event_participants_url" id="rsm_event_participants_url" class="regular-text" value="<?php echo esc_attr( $part_url ); ?>">
        <span class="description">
            <?php esc_html_e( 'Optional link for a participants list page.', 'race-series-manager' ); ?>
        </span>
    </p>

    <p>
        <label for="rsm_event_live_url"><strong><?php esc_html_e( 'Live tracking URL', 'race-series-manager' ); ?></strong></label><br>
        <input type="url" name="rsm_event_live_url" id="rsm_event_live_url" class="regular-text" value="<?php echo esc_attr( $live_url ); ?>">
        <span class="description">
            <?php esc_html_e( 'Optional link for live timing/live tracking page.', 'race-series-manager' ); ?>
        </span>
    </p>
    <?php
}

/**
 * Save event meta
 */
function rsm_save_event_meta( $post_id ) {

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

    // Content sections
    if ( isset( $_POST['rsm_event_content_nonce'] ) &&
         wp_verify_nonce( $_POST['rsm_event_content_nonce'], 'rsm_save_event_content' ) ) {

        $announcement = isset( $_POST['rsm_event_announcement'] ) ? wp_kses_post( $_POST['rsm_event_announcement'] ) : '';
        $rules        = isset( $_POST['rsm_event_rules'] ) ? wp_kses_post( $_POST['rsm_event_rules'] ) : '';
        $schedule     = isset( $_POST['rsm_event_schedule'] ) ? wp_kses_post( $_POST['rsm_event_schedule'] ) : '';
        $access       = isset( $_POST['rsm_event_access'] ) ? wp_kses_post( $_POST['rsm_event_access'] ) : '';
        $travel       = isset( $_POST['rsm_event_travel'] ) ? wp_kses_post( $_POST['rsm_event_travel'] ) : '';

        update_post_meta( $post_id, '_rsm_event_announcement', $announcement );
        update_post_meta( $post_id, '_rsm_event_rules',        $rules );
        update_post_meta( $post_id, '_rsm_event_schedule',     $schedule );
        update_post_meta( $post_id, '_rsm_event_access',       $access );
        update_post_meta( $post_id, '_rsm_event_travel',       $travel );
    }

    // Logo & contact
    if ( isset( $_POST['rsm_event_logo_contact_nonce'] ) &&
         wp_verify_nonce( $_POST['rsm_event_logo_contact_nonce'], 'rsm_save_event_logo_contact' ) ) {

        $logo_id = isset( $_POST['rsm_event_logo_id'] ) ? intval( $_POST['rsm_event_logo_id'] ) : 0;
        $contact = isset( $_POST['rsm_event_contact'] ) ? wp_kses_post( $_POST['rsm_event_contact'] ) : '';

        update_post_meta( $post_id, '_rsm_event_logo_id', $logo_id );
        update_post_meta( $post_id, '_rsm_event_contact', $contact );
    }

    // Registration / Live URLs
    if ( isset( $_POST['rsm_event_registration_nonce'] ) &&
         wp_verify_nonce( $_POST['rsm_event_registration_nonce'], 'rsm_save_event_registration' ) ) {

        $reg_url  = isset( $_POST['rsm_event_registration_url'] ) ? esc_url_raw( $_POST['rsm_event_registration_url'] ) : '';
        $part_url = isset( $_POST['rsm_event_participants_url'] ) ? esc_url_raw( $_POST['rsm_event_participants_url'] ) : '';
        $live_url = isset( $_POST['rsm_event_live_url'] ) ? esc_url_raw( $_POST['rsm_event_live_url'] ) : '';

        update_post_meta( $post_id, '_rsm_event_registration_url', $reg_url );
        update_post_meta( $post_id, '_rsm_event_participants_url', $part_url );
        update_post_meta( $post_id, '_rsm_event_live_url',         $live_url );
    }
}
add_action( 'save_post_cmt_event', 'rsm_save_event_meta' );
