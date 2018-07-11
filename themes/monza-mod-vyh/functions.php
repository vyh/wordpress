<?php

/**
 *  Enqueue styles; recommended version from wordpress.org plus code from parent...
 */
function monza_mod_enqueue_styles() {

    $parent_style = 'monza-style';
    $parent_dir = get_template_directory_uri();

    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/libs/bootstrap/css/bootstrap.min.css');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/libs/font-awesome/css/font-awesome.min.css');
    wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/libs/owl/owl.carousel.css');
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

    if ( function_exists('monza_custom_style')) {
        wp_add_inline_style( 'monza-style', monza_custom_style() );
    }

    // enqueue the modification last
    wp_enqueue_style( 'monza-mod-vyh-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'monza_mod_enqueue_styles' );


/**
 *  Two utility functions that you should change or write where you keep your php snippets.
 */
if ( ! function_exists( 'my_separate_category' ) ) {
    function my_separate_category() {
        // Posts of this category are excluded from the home page and appear alone on their category archive
        return 1;
    }
}
if ( ! function_exists( 'my_social_links' ) ) {
    function my_social_links() {
        // echo a string of html with your social media links to appear in the footer
        echo '';
    }
}


/**
 *  Filter out "separate" category posts from home page results;
 *  my_separate_category should be stored where you keep php snippets and return an int.
 */
if ( ! function_exists( 'filter_home_posts' ) ) {
    function filter_home_posts( $query ) {
        if( $query->is_home() && $query->is_main_query() ) {
            $my_home_categories = array( - my_separate_category() );
            $query->set( 'cat', $my_home_categories );
        }
    }
}
add_filter( 'pre_get_posts', 'filter_home_posts' );


/**
 *  Retrieve a post object by its slug; returns null if not found.
 */
function get_post_by_slug($slug) {
    if (!$slug) return false;
    $args = array(
        'name'           => $slug,
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1
    );
    $my_posts = get_posts( $args );
    if ( $my_posts ) return $my_posts[0];
}


/**
 *  Return the object's term_id; for array_mapping w/o a dynamically defined function.
 */
function id_from_obj( $term ) {
    return $term->term_id;
}


/**
 *  Return true if the post has the specified category, false otherwise.
 */
function has_cat($cat_id, $post) {
    if ( ! $post ) return false;
    $category_ids = array_map( id_from_obj, get_the_category($post->ID) );
    return in_array( $cat_id, $category_ids );
}


/**
 *  Filter tag archive results based on:
 *  1. "cat" query arg from current request uri; or
 *  2. "cat" query arg from referring request uri; or
 *  3. whether the referring request uri was/was not:
 *     a. the "separate" category archive or
 *     b. a post with the "separate" category
 */
if ( ! function_exists( 'filter_tag_query' ) ) {

    function filter_tag_query( $query ) {
        if ( $query->is_tag() || $query->is_search() || $query->is_month() ) {
            // if a category filter is specified in the current uri params, it will be used
            if ( isset( $_GET['cat'] ) ) return;

            // get query params from the referring uri (== current uri if wp_get_referer returns false)
            $referer = wp_get_referer();
            $referer = $referer ? $referer : $_SERVER['REQUEST_URI'];
            $query_string = parse_url( $referer, PHP_URL_QUERY );

            // if a category filter was specified in the referring uri params, use it
            if ( $query_string ) {
                parse_str( $query_string, $params );
                if ( $params['cat'] ) {
                    wp_redirect( esc_url_raw( add_query_arg( 'cat', $params['cat'] ) ) );
                    exit;
                }
            }

            // we don't have a filter from the current or referring uri; determine the filter and redirect
            $sep_cat = my_separate_category();
            $referer = explode( '/', parse_url( $referer, PHP_URL_PATH ) );
            $slug = array_pop( $referer );  // wordpress uses trailing slashes so this is probably empty
            $slug = $slug ? $slug : array_pop( $referer );
            $type = array_pop( $referer );
            $is_quote = ( $type == 'category' && $slug == 'quotes' ) || has_cat( $sep_cat, get_post_by_slug( $slug ) );
            $category_filter = $is_quote ? $sep_cat : - $sep_cat;
            wp_redirect( esc_url_raw( add_query_arg( 'cat', $category_filter ) ) );
            exit;
        }
    }

}
add_action( 'pre_get_posts', 'filter_tag_query' );


/**
 *  Filter search results so that they only include posts.
 */
if ( ! function_exists( 'filter_search_result_type' ) ) {
    function filter_search_result_type( $query ) {
        if ( $query->is_search() ) $query->set( 'post_type', 'post' );
    }
}
add_filter( 'pre_get_posts', 'filter_search_result_type' );


require get_template_directory() . '/../monza-mod-vyh/inc/template-tags.php';

?>