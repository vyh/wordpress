<?php
/*
Plugin Name: Photoblog Kit
Description: Post types, taxonomies, and tools for a photography blog. Exif data icons courtesy icons8.com.
Version: 0.1.1
Author: Nicki Hoffman
Author URI: https://arestelle.net
Text Domain: photoblog-kit
*/

/**
 *  Register Photo post type, Album and Keyword taxonomies
 */
function pk_register_type_and_tax() {
    register_post_type(
        'photos',
        array(
            'labels' => array(
                'name' => __( 'Photographs', 'photoblog-kit' ),
                'singular_name' => __( 'Photograph', 'photoblog-kit' )
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'comments',
                'post-formats'
            ),
            'rewrite' => array( 'slug' => 'photographs' ),
            'query_var' => 'photo'
        )
    );

    register_taxonomy(
        'keywords',
        'photos',
        array(
            'label' => __( 'Keywords', 'photoblog-kit' ),
            'rewrite' => array( 'slug' => 'keyword' )
        )
    );

    register_taxonomy(
        'albums',
        'photos',
        array(
            'label' => __( 'Albums', 'photoblog-kit' ),
            'rewrite' => array( 'slug' => 'album' )
        )
    );
}
add_action( 'init', 'pk_register_type_and_tax' );


/**
 *  Source: https://codex.wordpress.org/Function_Reference/register_post_type#Flushing_Rewrite_on_Activation
 */
function pk_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry,
    // when you add a post of this CPT.
    pk_register_type_and_tax();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pk_rewrite_flush' );


/**
 * What custom fields will exist for Photos
 */
$pk_meta_fields = array(
    'taken' => array(
        'id' => 'timestamp',
        'label' => 'Date Taken',
        'type' => 'text',
        'format' => 'datetime'
    ),
    'link' => array(
        'id' => '_product_link',
        'label' => 'Product Pages',
        'type' => 'repeatable',
        'format' => 'url'
    )
);


/**
 *  Register an admin meta box for the custom fields
 */
function pk_add_meta_boxes( $post ) {
    add_meta_box(
        'pk-meta-box',
        __( 'Photo Metadata', 'photoblog-kit' ),
        'render_pk_meta_box',
        'photos',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes_photos', 'pk_add_meta_boxes' );


/**
 *  Display the meta box in the editor page.
 *
 *  Sources: https://code.tutsplus.com/articles/reusable-custom-meta-boxes-part-1-intro-and-basic-fields--wp-23259,
 *           https://code.tutsplus.com/articles/reusable-custom-meta-boxes-part-3-extra-fields--wp-23821
 */
function render_pk_meta_box( $post ){
    global $pk_meta_fields;

    wp_nonce_field( basename( __FILE__ ), 'pk_meta_box_nonce' );

    foreach ( $pk_meta_fields as $field ) {
        $meta = get_post_meta($post->ID, $field['id'], true);
        echo '<div>';
        echo '<h4>'.$field['label'].'</h4>';
        switch ( $field['type'] ) {
            case 'repeatable':
                echo '<a class="repeatable-add button" href="#">+</a>
                        <ul id="'.$field['id'].'-repeatable" class="custom_repeatable">';
                $i = 0;
                if ($meta) {
                    $meta = array_values( $meta );
                    foreach($meta as $row) {
                        echo '<li><span class="sort hndle">|||</span>
                                    <input type="text" name="'.$field['id'].'['.$i.']" id="'.$field['id'].'" value="'.$row.'" size="30" />
                                    <a class="repeatable-remove button" href="#">-</a></li>';
                        $i++;
                    }
                } else {
                    echo '<li><span class="sort hndle">|||</span>
                                <input type="text" name="'.$field['id'].'['.$i.']" id="'.$field['id'].'" value="" size="30" />
                                <a class="repeatable-remove button" href="#">-</a></li>';
                }
                break;
            case 'text':
                echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />';
                break;
        }
        echo '</div>';
    }
}


/**
 *  Save or clear meta fields upon Photo post save
 *  Source: https://code.tutsplus.com/articles/reusable-custom-meta-boxes-part-1-intro-and-basic-fields--wp-23259
 */
function pk_save_meta_boxes_data( $post_id ) {
    global $pk_meta_fields;

    // verify nonce; skip for autosave; check user permission
    if ( ! isset( $_POST['pk_meta_box_nonce'] ) )
        return;
    if ( ! wp_verify_nonce( $_POST['pk_meta_box_nonce'], basename( __FILE__ ) ) )
        return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;

    // loop through fields and save the data
    foreach ($pk_meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ( $field['type'] == 'repeatable' && $field['format'] == 'url' )
            $new = array_map( esc_url, array_values($new) );
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}
add_action( 'save_post_photos', 'pk_save_meta_boxes_data', 10, 2 );


/**
 *  Enqueue script enabling editor support for a multi-value custom field
 */
function pk_admin_scripts() {
    wp_enqueue_script(
        'photoblog-js', plugin_dir_url( __FILE__ ) . 'js/photoblog-js.js', array( 'jquery' ), '0.1.0', true
    );
}
add_action( 'admin_enqueue_scripts', 'pk_admin_scripts' );


function pk_extract_exif_to_post( $post_id, $post, $update ) {
    global $pk_meta_fields;

    // check that it's not a revision & not autosave
    if ( wp_is_post_revision( $post_id ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // check that it's a Photo post
    $post_type = get_post_type( $post_id );
    if ( $post_type !== 'photos' ) return;

    // check that a featured image is set
    $img_id = get_post_meta( $post_id, '_thumbnail_id', true);
    if ( ! $img_id ) return $data;

    // check that we found the image
    $url = wp_get_attachment_image_src( $img_id, 'full' );
    if ( ! $url ) return $data;
    else $url = $url[0];

    // description and timestamp should be available through exif reader
    $exif = exif_read_data( $url, null, true );
    $desc = $exif['IFD0']['ImageDescription'] ? $exif['IFD0']['ImageDescription'] : '';
    $taken = $exif['EXIF']['DateTimeOriginal'] ? $exif['EXIF']['DateTimeOriginal'] : '';

    // Lightroom sets title and keywords in an iptc block
    $size = getimagesize( $url, $info );
    if ( isset( $info['APP13'] ) ) {
        $iptc = iptcparse( $info['APP13'] );
        $title = $iptc['2#005'] ? $iptc['2#005'][0] : '';
        $keywords = $iptc['2#025'] ? $iptc['2#025'] : array();
    } else {
        $title = '';
        $keywords = array();
    }

    // unhook this function so it doesn't loop infinitely
    remove_action( 'save_post', 'pk_extract_exif_to_post', 10, 3 );

    // update the post, which calls save_post again
    if ( $title && $desc && ! $post->post_title && ! $post->post_content ) {
        wp_update_post( array( 'ID' => $post_id, 'post_title' => $title, 'post_content' => $desc ) );
    } else if ( $title && ! $post->post_title ) {
        wp_update_post( array( 'ID' => $post_id, 'post_title' => $title ) );
    } else if ( $desc && ! $post->post_content ) {
        wp_update_post( array( 'ID' => $post_id, 'post_content' => $desc ) );
    }
    if ( $keywords && ! wp_get_post_terms( $post_id, 'keywords', array( 'fields' => 'names' ) ) )
        wp_set_post_terms( $post_id, $keywords, 'keywords' );
    if ( $taken && ! get_post_meta( $post_id, $pk_meta_fields['taken']['id'], true ) )
        update_post_meta( $post_id, $pk_meta_fields['taken']['id'], $taken );

    // re-hook this function
    add_action( 'save_post', 'pk_extract_exif_to_post', 10, 3 );
}
add_filter( 'save_post', 'pk_extract_exif_to_post', 10, 3 );


/**
 *  Get EXIF/meta data from featured image. Response contains as much as is available of:
 *  array( 'aperture', 'camera', 'shutter-speed', 'iso', 'focal-length', 'lens' )
 */
function pk_get_exif_array( $post_id ) {
    global $pk_meta_fields;
    $url = get_the_post_thumbnail_url( $post_id, 'full' );
    if ( ! $url ) return false;

    $exif = exif_read_data( $url, null, true );
    $arr = array();

    if ( $exif['COMPUTED']['ApertureFNumber'] ) $arr['aperture'] = $exif['COMPUTED']['ApertureFNumber'];
    if ( $exif['IFD0']['Model'] ) $arr['camera'] = $exif['IFD0']['Model'];
    if ( $exif['EXIF']['ExposureTime'] ) {
        // exposure time is a fraction even if >= 1, so we'll check for that case
        $exposure = $exif['EXIF']['ExposureTime'];
        if ( is_string( $exposure ) ) {
            $vals = explode( '/', $exposure );
            if ( count( $vals ) > 1 ) {
                $quotient = $vals[0] / $vals[1];
                $exposure = $quotient >= 1 ? $quotient : $exposure;
            }
        }
        $arr['shutter-speed'] = $exposure;
    }
    if ( $exif['EXIF']['ISOSpeedRatings'] ) $arr['iso'] = $exif['EXIF']['ISOSpeedRatings'];
    if ( $exif['EXIF']['FocalLength'] ) {
        // as with exposure time, a fraction; we always want an int here so we'll cast, divide, and restringify
        $f_len = $exif['EXIF']['FocalLength'];
        if ( is_string( $f_len ) ) {
            $vals = explode( '/', $f_len );
            if ( count( $vals ) > 1 )
                $f_len = ''.($vals[0] / $vals[1]);
        }
        $arr['focal-length'] = ''.$f_len.' mm';
    }
    if ( $exif['EXIF']['UndefinedTag:0xA434'] ) $arr['lens'] = $exif['EXIF']['UndefinedTag:0xA434'];

    // Date taken, from post or exif, priority given to post
    $taken = get_post_meta( $post_id, $pk_meta_fields['taken']['id'], true );
    if ( ! $taken ) $taken = $exif['EXIF']['DateTimeOriginal'];
    if ( $taken ) $arr['taken'] = date_i18n('j F Y', strtotime( $taken ) );

    return $arr;
}


/**
 *  Get EXIF/meta data from featured image (as formatted html, including icons).
 */
function pk_get_exif_formatted( $post_id ) {
    $exif = pk_get_exif_array( $post_id );
    $wide_fields = array( 'taken', 'camera', 'lens' );
    $narrow_fields = array( 'aperture', 'shutter-speed', 'focal-length', 'iso' );
    $field_html = '';

    // camera and lens will be full-width within the exif div
    foreach ( $wide_fields as $field ) {
        if ( $exif[$field] ) {
            $field_html .= '<div class="pk-full">';
            $field_html .= '<img class="pk-icon" alt="' . __( $field, 'photoblog-kit' ) . '" src="' . plugin_dir_url(__FILE__) . 'static/' . $field . '.png" />';
            $field_html .= ' ' . $exif[$field] . ' </div>' . "\n";
        }
    }

    // other fields will be half-width
    $i = 0;
    foreach ( $narrow_fields as $field ) {
        if ( $exif[$field] ) {
            $field_html .= '<div class="pk-half pk-'. ( $i % 2 ? 'right' : 'left' ) .'">';
            $field_html .= '<img class="pk-icon" alt="' . __( $field, 'photoblog-kit' ) . '" src="' . plugin_dir_url(__FILE__) . 'static/' . $field . '.png" />';
            $field_html .= ' ' . $exif[$field] . ' </div>' . "\n";
            $i += 1;
        }
    }

    $html = $field_html ? '<div class="pk-exif">' . "\n" . $field_html . '</div>' . "\n" : '';
    return $html;
}


/**
 *  Register and enqueue stylesheet for this plugin.
 */
function pk_set_up_styles() {
    wp_register_style( 'pk-styles', plugin_dir_url(__FILE__) . 'static/photoblog-styles.css' );
    wp_enqueue_style( 'pk-styles' );
}
add_action('wp_enqueue_scripts', 'pk_set_up_styles');


/**
 *  For photo posts, we'll add the exif div (if available) to the top right of the post content.
 *  If this is not desired, use remove_filter to un-hook the function.
 */
function pk_display_exif( $title, $post_id ) {
    // only try to add exif on photo posts
    $post_type = get_post_type( $post_id );
    if ( $post_type !== 'photos' || ! is_single() )
        return $title;

    $exif = pk_get_exif_formatted( $post_id );
    if ( $exif )
        return $exif . "\n" . $title;
    else
        return $title;
}
add_filter( 'the_content', 'pk_display_exif' );


/**
 *  Define a mapping function for domain -> site name, allowing override by other scripts.
 */
if ( ! function_exists( 'product_page_name_map' ) ) {
    function product_page_name_map () {
        return array(
            'fineartamerica' => 'fine art america'
        );
    }
}


/**
 *  Given a simple url (ends with a one-part top-level domain like .com or .org),
 *  return the domain part of the hostname. Will not work with multi-part top-level
 *  domains like .co.nz.
 */
function pk_domain_from_url( $url ) {
    $host = parse_url($url, PHP_URL_HOST);
    if ( ! $host ) return '';

    $host_parts = explode( '.', $host );
    $len = count($host_parts);

    if ( $len > 1 ) return $host_parts[$len - 2];
    else return $host;
}


/**
 *  For photo posts, we'll add any product links after the main content.
 *  We'll also add any Albums and Keywords for this photo.
 *  If this is not desired, use remove_filter to un-hook the function.
 */
function pk_append_content( $content ) {
    global $pk_meta_fields, $post;

    // only try to add this content on photo posts (single page, not archive)
    if ( get_post_type( $post->ID ) !== 'photos' || ! is_single() )
        return $content;

    // product links
    $links = get_post_meta( $post->ID, $pk_meta_fields['link']['id'], true );
    $link_prefix = '<p style="padding-top: 40px;">' . __( 'Prints', 'photoblog-kit' ) . ': ';
    $link_html = __( 'not yet available for this image; comment/contact me with requests.', 'photoblog-kit' ) . '</p>';
    if ( $links ) {
        $name_map = product_page_name_map();
        $link_arr = array();
        foreach ( $links as $url ) {
            if ( $url ) {
                $name = pk_domain_from_url( $url );
                if ( $name_map[$name] ) $name = $name_map[$name];
                array_push( $link_arr, '<a href="' . $url . '">' . ucwords( $name ) . '</a>' );
            }
        }
        if ( $link_arr )
            $link_html = implode( ', ', $link_arr ) . '</p>';
    }
    $link_html = $link_prefix . $link_html . "\n";

    // albums
    $albums = wp_get_post_terms( $post->ID, 'albums' );
    if ( ! $albums ) {
        $album_html = '';
    } else {
        $album_arr = array();
        foreach ( $albums as $album ) {
            array_push( $album_arr, '<a href="/album/' . $album->slug . '">' . $album->name . '</a>');
        }
        $album_html = get_taxonomy('albums')->label . ': ' . implode( ', ', $album_arr );
    }

    // keywords
    $keywords = wp_get_post_terms( $post->ID, 'keywords' );
    if ( ! $keywords ) {
        $keyword_html = '';
    } else {
        $keyword_arr = array();
        foreach ( $keywords as $kw ) {
            array_push( $keyword_arr, '<a href="/keyword/' . $kw->slug . '">' . $kw->name . '</a>');
        }
        $keyword_html = get_taxonomy('keywords')->label . ': ' . implode( ', ', $keyword_arr );
    }

    if ( $album_html && $keyword_html )
        $tax_html = '<p>' . $album_html . "<br />\n" . $keyword_html . "</p>\n";
    else if ( $album_html || $keyword_html )
        $tax_html = '<p>' . $album_html . $keyword_html . "</p>\n";
    else
        $tax_html = '';

    return $content . "\n" . $link_html . $tax_html;
}
add_filter( 'the_content', 'pk_append_content' );

?>