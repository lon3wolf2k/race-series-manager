<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Race meta boxes
 */

function rsm_add_race_meta_boxes() {
    add_meta_box(
        'rsm_race_details',
        esc_html__( 'Race details', 'race-series-manager' ),
        'rsm_race_details_meta_box_callback',
        'cmt_race',
        'normal',
        'high'
    );

    add_meta_box(
        'rsm_race_media',
        esc_html__( 'Race media & map', 'race-series-manager' ),
        'rsm_race_media_meta_box_callback',
        'cmt_race',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'rsm_add_race_meta_boxes' );

/**
 * Main race details meta box (distance, ascent, etc.)
 */
function rsm_race_details_meta_box_callback( $post ) {

    wp_nonce_field( 'rsm_save_race_details', 'rsm_race_details_nonce' );

    $event_id      = get_post_meta( $post->ID, '_rsm_race_event_id', true );
    $distance      = get_post_meta( $post->ID, '_rsm_race_distance', true );
    $elevation     = get_post_meta( $post->ID, '_rsm_race_elevation', true );
    $date          = get_post_meta( $post->ID, '_rsm_race_date', true );
    $start_time    = get_post_meta( $post->ID, '_rsm_race_start_time', true );
    $start_loc     = get_post_meta( $post->ID, '_rsm_race_start_location', true );
    $finish_loc    = get_post_meta( $post->ID, '_rsm_race_finish_location', true );
    $fee           = get_post_meta( $post->ID, '_rsm_race_fee', true );
    $cutoff_hours  = get_post_meta( $post->ID, '_rsm_race_cutoff_hours', true );
    $itra_link     = get_post_meta( $post->ID, '_rsm_race_itra_link', true );
    $reg_link      = get_post_meta( $post->ID, '_rsm_race_registration_link', true );
    $aid_stations  = get_post_meta( $post->ID, '_rsm_race_aid_stations', true );

    $events = get_posts( array(
        'post_type'      => 'cmt_event',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );
    ?>
    <p>
        <label for="rsm_race_event_id"><strong><?php esc_html_e( 'Parent event', 'race-series-manager' ); ?></strong></label><br>
        <select name="rsm_race_event_id" id="rsm_race_event_id">
            <option value=""><?php esc_html_e( '— None —', 'race-series-manager' ); ?></option>
            <?php foreach ( $events as $event ) : ?>
                <option value="<?php echo esc_attr( $event->ID ); ?>" <?php selected( $event_id, $event->ID ); ?>>
                    <?php echo esc_html( get_the_title( $event ) ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><small><?php esc_html_e( 'Link this race to an event (for schedule, access, logo, etc.).', 'race-series-manager' ); ?></small>
    </p>

    <table class="form-table">
        <tr>
            <th><label for="rsm_race_distance"><?php esc_html_e( 'Distance', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="text" name="rsm_race_distance" id="rsm_race_distance" value="<?php echo esc_attr( $distance ); ?>" class="regular-text">
                <p class="description"><?php esc_html_e( 'e.g. 44km', 'race-series-manager' ); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_elevation"><?php esc_html_e( 'Total ascent', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="text" name="rsm_race_elevation" id="rsm_race_elevation" value="<?php echo esc_attr( $elevation ); ?>" class="regular-text">
                <p class="description"><?php esc_html_e( 'e.g. 2000m', 'race-series-manager' ); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_date"><?php esc_html_e( 'Race date', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="date" name="rsm_race_date" id="rsm_race_date" value="<?php echo esc_attr( $date ); ?>">
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_start_time"><?php esc_html_e( 'Start time', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="time" name="rsm_race_start_time" id="rsm_race_start_time" value="<?php echo esc_attr( $start_time ); ?>">
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_start_location"><?php esc_html_e( 'Start point', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="text" name="rsm_race_start_location" id="rsm_race_start_location" value="<?php echo esc_attr( $start_loc ); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_finish_location"><?php esc_html_e( 'Finish point', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="text" name="rsm_race_finish_location" id="rsm_race_finish_location" value="<?php echo esc_attr( $finish_loc ); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_fee"><?php esc_html_e( 'Entry fee', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="text" name="rsm_race_fee" id="rsm_race_fee" value="<?php echo esc_attr( $fee ); ?>" class="regular-text">
                <p class="description"><?php esc_html_e( 'e.g. 40€', 'race-series-manager' ); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_cutoff_hours"><?php esc_html_e( 'Cut-off time (hours)', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="number" step="0.5" min="0" name="rsm_race_cutoff_hours" id="rsm_race_cutoff_hours" value="<?php echo esc_attr( $cutoff_hours ); ?>" class="small-text">
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_itra_link"><?php esc_html_e( 'ITRA link', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="url" name="rsm_race_itra_link" id="rsm_race_itra_link" value="<?php echo esc_attr( $itra_link ); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_registration_link"><?php esc_html_e( 'Registration link', 'race-series-manager' ); ?></label></th>
            <td>
                <input type="url" name="rsm_race_registration_link" id="rsm_race_registration_link" value="<?php echo esc_attr( $reg_link ); ?>" class="regular-text">
            </td>
        </tr>

        <tr>
            <th><label for="rsm_race_aid_stations"><?php esc_html_e( 'Aid stations & cut-offs', 'race-series-manager' ); ?></label></th>
            <td>
                <textarea name="rsm_race_aid_stations" id="rsm_race_aid_stations" rows="8" class="large-text code"><?php echo esc_textarea( $aid_stations ); ?></textarea>
                <p class="description">
                    <?php esc_html_e( 'One station per line, use pipe-separated fields:', 'race-series-manager' ); ?><br>
                    <code><?php esc_html_e( 'Station name | KM | D+ | D- | Cut-off', 'race-series-manager' ); ?></code>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Race media & map (plotaroute embed, video, gallery, static images)
 */
function rsm_race_media_meta_box_callback( $post ) {

    wp_nonce_field( 'rsm_save_race_media', 'rsm_race_media_nonce' );

    $plotaroute_embed = get_post_meta( $post->ID, '_rsm_race_plotaroute_embed', true );
    $route_url        = get_post_meta( $post->ID, '_rsm_race_route_url', true );
    $video_embed      = get_post_meta( $post->ID, '_rsm_race_video_embed', true );
    $gallery_ids      = get_post_meta( $post->ID, '_rsm_race_gallery_ids', true );
    $static_map_id    = get_post_meta( $post->ID, '_rsm_race_static_map_id', true );
    $elev_chart_id    = get_post_meta( $post->ID, '_rsm_race_elev_chart_id', true );

    if ( ! is_array( $gallery_ids ) ) {
        $gallery_ids = array();
    }

    wp_enqueue_media();
    ?>
    <h4><?php esc_html_e( 'Plotaroute / route map embed code', 'race-series-manager' ); ?></h4>
    <p class="description">
        <?php esc_html_e( 'Paste the iframe code from Plotaroute (or similar). It will be embedded on the race page.', 'race-series-manager' ); ?>
    </p>
    <textarea name="rsm_race_plotaroute_embed" id="rsm_race_plotaroute_embed" rows="5" class="large-text code"><?php echo esc_textarea( $plotaroute_embed ); ?></textarea>

    <h4 style="margin-top:1.5em;"><?php esc_html_e( 'GPX / route download URL', 'race-series-manager' ); ?></h4>
    <input type="url" name="rsm_race_route_url" id="rsm_race_route_url" value="<?php echo esc_attr( $route_url ); ?>" class="regular-text">
    <p class="description"><?php esc_html_e( 'Used for the “Download GPX / Route” button.', 'race-series-manager' ); ?></p>

    <h4 style="margin-top:1.5em;"><?php esc_html_e( 'Race video embed code', 'race-series-manager' ); ?></h4>
    <p class="description">
        <?php esc_html_e( 'Paste a YouTube iframe embed code. It will appear in the Race Video section.', 'race-series-manager' ); ?>
    </p>
    <textarea name="rsm_race_video_embed" id="rsm_race_video_embed" rows="5" class="large-text code"><?php echo esc_textarea( $video_embed ); ?></textarea>

    <h4 style="margin-top:1.5em;"><?php esc_html_e( 'Race gallery', 'race-series-manager' ); ?></h4>
    <p class="description">
        <?php esc_html_e( 'Choose images for the race gallery. They will appear with lightbox on the race page.', 'race-series-manager' ); ?>
    </p>
    <input type="hidden" id="rsm_race_gallery_ids" name="rsm_race_gallery_ids" value="<?php echo esc_attr( implode( ',', $gallery_ids ) ); ?>">
    <button type="button" class="button" id="rsm_race_gallery_button"><?php esc_html_e( 'Select images', 'race-series-manager' ); ?></button>
    <button type="button" class="button" id="rsm_race_gallery_clear" <?php echo ! empty( $gallery_ids ) ? '' : 'style="display:none;"'; ?>>
        <?php esc_html_e( 'Clear gallery', 'race-series-manager' ); ?>
    </button>

    <div id="rsm_race_gallery_preview" style="margin-top:10px;display:flex;flex-wrap:wrap;gap:6px;">
        <?php
        if ( ! empty( $gallery_ids ) ) :
            foreach ( $gallery_ids as $id ) :
                $thumb = wp_get_attachment_image_src( $id, 'thumbnail' );
                if ( ! $thumb ) {
                    continue;
                }
                ?>
                <div style="border:1px solid #ddd;padding:2px;background:#fff;">
                    <img src="<?php echo esc_url( $thumb[0] ); ?>" style="display:block;width:70px;height:auto;">
                </div>
                <?php
            endforeach;
        endif;
        ?>
    </div>

    <h4 style="margin-top:1.5em;"><?php esc_html_e( 'Static map image (for PDF only)', 'race-series-manager' ); ?></h4>
    <p class="description">
        <?php esc_html_e( 'Upload a static map image to be used in the race booklet PDF. It will not appear on the race page.', 'race-series-manager' ); ?>
    </p>
    <input type="hidden" id="rsm_race_static_map_id" name="rsm_race_static_map_id" value="<?php echo esc_attr( $static_map_id ); ?>">
    <button type="button" class="button" id="rsm_race_static_map_button">
        <?php echo $static_map_id ? esc_html__( 'Change image', 'race-series-manager' ) : esc_html__( 'Select image', 'race-series-manager' ); ?>
    </button>
    <button type="button" class="button" id="rsm_race_static_map_remove" <?php echo $static_map_id ? '' : 'style="display:none;"'; ?>>
        <?php esc_html_e( 'Remove', 'race-series-manager' ); ?>
    </button>
    <div id="rsm_race_static_map_preview" style="margin-top:10px;">
        <?php
        if ( $static_map_id ) :
            $img = wp_get_attachment_image_src( $static_map_id, 'medium' );
            if ( $img ) :
                ?>
                <img src="<?php echo esc_url( $img[0] ); ?>" style="max-width:200px;height:auto;border:1px solid #ddd;padding:4px;background:#fff;">
                <?php
            endif;
        endif;
        ?>
    </div>

    <h4 style="margin-top:1.5em;"><?php esc_html_e( 'Elevation profile image (for PDF only)', 'race-series-manager' ); ?></h4>
    <p class="description">
        <?php esc_html_e( 'Upload a static elevation chart image for the race booklet PDF. It will not appear on the race page.', 'race-series-manager' ); ?>
    </p>
    <input type="hidden" id="rsm_race_elev_chart_id" name="rsm_race_elev_chart_id" value="<?php echo esc_attr( $elev_chart_id ); ?>">
    <button type="button" class="button" id="rsm_race_elev_chart_button">
        <?php echo $elev_chart_id ? esc_html__( 'Change image', 'race-series-manager' ) : esc_html__( 'Select image', 'race-series-manager' ); ?>
    </button>
    <button type="button" class="button" id="rsm_race_elev_chart_remove" <?php echo $elev_chart_id ? '' : 'style="display:none;"'; ?>>
        <?php esc_html_e( 'Remove', 'race-series-manager' ); ?>
    </button>
    <div id="rsm_race_elev_chart_preview" style="margin-top:10px;">
        <?php
        if ( $elev_chart_id ) :
            $img2 = wp_get_attachment_image_src( $elev_chart_id, 'medium' );
            if ( $img2 ) :
                ?>
                <img src="<?php echo esc_url( $img2[0] ); ?>" style="max-width:200px;height:auto;border:1px solid #ddd;padding:4px;background:#fff;">
                <?php
            endif;
        endif;
        ?>
    </div>

    <script>
    jQuery(document).ready(function($){
        var galleryFrame, mapFrame, elevFrame;

        $('#rsm_race_gallery_button').on('click', function(e){
            e.preventDefault();

            if (galleryFrame) {
                galleryFrame.open();
                return;
            }

            galleryFrame = wp.media({
                title: '<?php echo esc_js( __( 'Select gallery images', 'race-series-manager' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use these images', 'race-series-manager' ) ); ?>' },
                multiple: true
            });

            galleryFrame.on('select', function(){
                var selection = galleryFrame.state().get('selection');
                var ids = [];
                var html = '';
                selection.each(function(attachment){
                    attachment = attachment.toJSON();
                    ids.push(attachment.id);
                    if (attachment.sizes && attachment.sizes.thumbnail) {
                        html += '<div style="border:1px solid #ddd;padding:2px;background:#fff;"><img src="'+attachment.sizes.thumbnail.url+'" style="display:block;width:70px;height:auto;" /></div>';
                    } else {
                        html += '<div style="border:1px solid #ddd;padding:2px;background:#fff;"><img src="'+attachment.url+'" style="display:block;width:70px;height:auto;" /></div>';
                    }
                });
                $('#rsm_race_gallery_ids').val(ids.join(','));
                $('#rsm_race_gallery_preview').html(html);
                $('#rsm_race_gallery_clear').show();
            });

            galleryFrame.open();
        });

        $('#rsm_race_gallery_clear').on('click', function(e){
            e.preventDefault();
            $('#rsm_race_gallery_ids').val('');
            $('#rsm_race_gallery_preview').empty();
            $('#rsm_race_gallery_clear').hide();
        });

        $('#rsm_race_static_map_button').on('click', function(e){
            e.preventDefault();

            if (mapFrame) {
                mapFrame.open();
                return;
            }

            mapFrame = wp.media({
                title: '<?php echo esc_js( __( 'Select map image', 'race-series-manager' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use this image', 'race-series-manager' ) ); ?>' },
                multiple: false
            });

            mapFrame.on('select', function(){
                var attachment = mapFrame.state().get('selection').first().toJSON();
                $('#rsm_race_static_map_id').val(attachment.id);
                $('#rsm_race_static_map_preview').html(
                    '<img src="'+attachment.url+'" style="max-width:200px;height:auto;border:1px solid #ddd;padding:4px;background:#fff;" />'
                );
                $('#rsm_race_static_map_remove').show();
                $('#rsm_race_static_map_button').text('<?php echo esc_js( __( 'Change image', 'race-series-manager' ) ); ?>');
            });

            mapFrame.open();
        });

        $('#rsm_race_static_map_remove').on('click', function(e){
            e.preventDefault();
            $('#rsm_race_static_map_id').val('');
            $('#rsm_race_static_map_preview').empty();
            $('#rsm_race_static_map_remove').hide();
            $('#rsm_race_static_map_button').text('<?php echo esc_js( __( 'Select image', 'race-series-manager' ) ); ?>');
        });

        $('#rsm_race_elev_chart_button').on('click', function(e){
            e.preventDefault();

            if (elevFrame) {
                elevFrame.open();
                return;
            }

            elevFrame = wp.media({
                title: '<?php echo esc_js( __( 'Select elevation image', 'race-series-manager' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use this image', 'race-series-manager' ) ); ?>' },
                multiple: false
            });

            elevFrame.on('select', function(){
                var attachment = elevFrame.state().get('selection').first().toJSON();
                $('#rsm_race_elev_chart_id').val(attachment.id);
                $('#rsm_race_elev_chart_preview').html(
                    '<img src="'+attachment.url+'" style="max-width:200px;height:auto;border:1px solid #ddd;padding:4px;background:#fff;" />'
                );
                $('#rsm_race_elev_chart_remove').show();
                $('#rsm_race_elev_chart_button').text('<?php echo esc_js( __( 'Change image', 'race-series-manager' ) ); ?>');
            });

            elevFrame.open();
        });

        $('#rsm_race_elev_chart_remove').on('click', function(e){
            e.preventDefault();
            $('#rsm_race_elev_chart_id').val('');
            $('#rsm_race_elev_chart_preview').empty();
            $('#rsm_race_elev_chart_remove').hide();
            $('#rsm_race_elev_chart_button').text('<?php echo esc_js( __( 'Select image', 'race-series-manager' ) ); ?>');
        });
    });
    </script>
    <?php
}

/**
 * Save race meta.
 */
function rsm_save_race_meta( $post_id ) {

    // -----------------------------------------------------------------
    // Details
    // -----------------------------------------------------------------
    if ( isset( $_POST['rsm_race_details_nonce'] ) &&
         wp_verify_nonce( $_POST['rsm_race_details_nonce'], 'rsm_save_race_details' ) ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset( $_POST['post_type'] ) && 'cmt_race' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        } else {
            return;
        }

        $event_id     = isset( $_POST['rsm_race_event_id'] ) ? intval( $_POST['rsm_race_event_id'] ) : 0;
        $distance     = isset( $_POST['rsm_race_distance'] ) ? sanitize_text_field( $_POST['rsm_race_distance'] ) : '';
        $elevation    = isset( $_POST['rsm_race_elevation'] ) ? sanitize_text_field( $_POST['rsm_race_elevation'] ) : '';
        $date         = isset( $_POST['rsm_race_date'] ) ? sanitize_text_field( $_POST['rsm_race_date'] ) : '';
        $start_time   = isset( $_POST['rsm_race_start_time'] ) ? sanitize_text_field( $_POST['rsm_race_start_time'] ) : '';
        $start_loc    = isset( $_POST['rsm_race_start_location'] ) ? sanitize_text_field( $_POST['rsm_race_start_location'] ) : '';
        $finish_loc   = isset( $_POST['rsm_race_finish_location'] ) ? sanitize_text_field( $_POST['rsm_race_finish_location'] ) : '';
        $fee          = isset( $_POST['rsm_race_fee'] ) ? sanitize_text_field( $_POST['rsm_race_fee'] ) : '';
        $cutoff_hours = isset( $_POST['rsm_race_cutoff_hours'] ) ? sanitize_text_field( $_POST['rsm_race_cutoff_hours'] ) : '';
        $itra_link    = isset( $_POST['rsm_race_itra_link'] ) ? esc_url_raw( $_POST['rsm_race_itra_link'] ) : '';
        $reg_link     = isset( $_POST['rsm_race_registration_link'] ) ? esc_url_raw( $_POST['rsm_race_registration_link'] ) : '';
        $aid          = isset( $_POST['rsm_race_aid_stations'] ) ? wp_kses_post( $_POST['rsm_race_aid_stations'] ) : '';

        update_post_meta( $post_id, '_rsm_race_event_id',          $event_id );
        update_post_meta( $post_id, '_rsm_race_distance',          $distance );
        update_post_meta( $post_id, '_rsm_race_elevation',         $elevation );
        update_post_meta( $post_id, '_rsm_race_date',              $date );
        update_post_meta( $post_id, '_rsm_race_start_time',        $start_time );
        update_post_meta( $post_id, '_rsm_race_start_location',    $start_loc );
        update_post_meta( $post_id, '_rsm_race_finish_location',   $finish_loc );
        update_post_meta( $post_id, '_rsm_race_fee',               $fee );
        update_post_meta( $post_id, '_rsm_race_cutoff_hours',      $cutoff_hours );
        update_post_meta( $post_id, '_rsm_race_itra_link',         $itra_link );
        update_post_meta( $post_id, '_rsm_race_registration_link', $reg_link );
        update_post_meta( $post_id, '_rsm_race_aid_stations',      $aid );
    }

    // -----------------------------------------------------------------
    // Media
    // -----------------------------------------------------------------
    if ( isset( $_POST['rsm_race_media_nonce'] ) &&
         wp_verify_nonce( $_POST['rsm_race_media_nonce'], 'rsm_save_race_media' ) ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset( $_POST['post_type'] ) && 'cmt_race' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        } else {
            return;
        }

        // ΠΡΟΣΟΧΗ: Εδώ ΔΕΝ κάνουμε wp_kses_post, για να μην κόβονται τα iframes
        $plot_embed  = isset( $_POST['rsm_race_plotaroute_embed'] )
            ? wp_unslash( $_POST['rsm_race_plotaroute_embed'] )
            : '';

        $route_url   = isset( $_POST['rsm_race_route_url'] )
            ? esc_url_raw( $_POST['rsm_race_route_url'] )
            : '';

        $video_embed = isset( $_POST['rsm_race_video_embed'] )
            ? wp_unslash( $_POST['rsm_race_video_embed'] )
            : '';

        $gallery_ids = isset( $_POST['rsm_race_gallery_ids'] )
            ? sanitize_text_field( $_POST['rsm_race_gallery_ids'] )
            : '';

        $static_map  = isset( $_POST['rsm_race_static_map_id'] ) ? intval( $_POST['rsm_race_static_map_id'] ) : 0;
        $elev_chart  = isset( $_POST['rsm_race_elev_chart_id'] ) ? intval( $_POST['rsm_race_elev_chart_id'] ) : 0;

        $gallery_array = array();
        if ( ! empty( $gallery_ids ) ) {
            $parts = array_map( 'trim', explode( ',', $gallery_ids ) );
            foreach ( $parts as $pid ) {
                $pid = intval( $pid );
                if ( $pid ) {
                    $gallery_array[] = $pid;
                }
            }
        }

        update_post_meta( $post_id, '_rsm_race_plotaroute_embed', $plot_embed );
        update_post_meta( $post_id, '_rsm_race_route_url',        $route_url );
        update_post_meta( $post_id, '_rsm_race_video_embed',      $video_embed );
        update_post_meta( $post_id, '_rsm_race_gallery_ids',      $gallery_array );
        update_post_meta( $post_id, '_rsm_race_static_map_id',    $static_map );
        update_post_meta( $post_id, '_rsm_race_elev_chart_id',    $elev_chart );
    }
}
add_action( 'save_post_cmt_race', 'rsm_save_race_meta' );
