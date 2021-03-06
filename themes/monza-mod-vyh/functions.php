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
//     wp_enqueue_style( 'monza-mod-vyh-style',
//         get_stylesheet_directory_uri() . '/style.css',
//         array( $parent_style )
//     );
}
add_action( 'wp_enqueue_scripts', 'monza_mod_enqueue_styles' );


/**
 *  Two utility functions that you should change or write where you keep your php snippets.
 */
if ( ! function_exists( 'my_separate_category' ) ) {
    function my_separate_category() {
        // Posts of this category are excluded from the home page and appear alone on searches from their category archive
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
 *  Filter taxonomy archive and search results based on:
 *  1. "post_type" query arg from current request uri; or
 *  2. "post_type" query arg from referring request uri; or
 *  3. whether the referring request uri was/was not:
 *     a. the post type archive or
 *     b. a post of the post type
 */
if ( ! function_exists( 'filter_tax_query' ) ) {

    function filter_tax_query( $query ) {
        if ( $query->is_tax() || $query->is_search() ) {
            // if a category filter is specified in the current uri params, it will be used
            if ( isset( $_GET['post_type'] ) ) return;

            // get query params from the referring uri (== current uri if wp_get_referer returns false)
            $referer = wp_get_referer();
            $referer = $referer ? $referer : $_SERVER['REQUEST_URI'];
            $query_string = parse_url( $referer, PHP_URL_QUERY );

            // if a post type filter was specified in the referring uri params, use it
            if ( $query_string ) {
                parse_str( $query_string, $params );
                if ( $params['post_type'] ) {
                    wp_redirect( esc_url_raw( add_query_arg( 'post_type', $params['post_type'] ) ) );
                    exit;
                }
            }

            // does the referring url contain the post_type?
            $referer = explode( '/', parse_url( $referer, PHP_URL_PATH ) );
            $post_type = $referer[0] ? $referer[0] : $referer[1];  // in case of front slashes
            if ( in_array( $post_type, array( 'quotes', 'works' ) ) ) {
                wp_redirect( esc_url_raw( add_query_arg( 'post_type', $post_type ) ) );
                exit;
            } elseif ( $post_type === 'photographs' ) {
                wp_redirect( esc_url_raw( add_query_arg( 'post_type', 'photos' ) ) );
                exit;
            }

            // no; ok, see if it was a post & what its type was
            $slug = array_pop( $referer );  // wordpress uses trailing slashes so this is probably empty
            $slug = $slug ? $slug : array_pop( $referer );
            $prev_post = get_post_by_slug( $slug );
            if ( ! $prev_post ) return;
            $post_type = get_post_type( $prev_post );
            if ( in_array( $post_type, array( 'photos', 'quotes', 'works' ) ) ) {
                wp_redirect( esc_url_raw( add_query_arg( 'post_type', $post_type ) ) );
                exit;
            }
        }
    }

}
add_action( 'pre_get_posts', 'filter_tax_query' );


/**
 *  Filter search results so that they only include posts...but not on admin queries.
 */
if ( ! function_exists( 'filter_search_result_type' ) ) {
    function filter_search_result_type( $query ) {
        if ( $query->is_search() && ! is_admin() && ! isset( $_GET['post_type'] ) )
            $query->set( 'post_type', 'post' );
    }
}
add_filter( 'pre_get_posts', 'filter_search_result_type' );


/**
 * Filter quote queries with ?work_quoted=ID in them
 */
if ( ! function_exists( 'filter_quote_work_lookup' )) {
    function filter_quote_work_lookup( $query ) {
        if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'quotes' ) {
            if ( isset( $_GET['work_quoted'] ) ) {
                $query->set( 'meta_query', array(
                    array(
                        'key' => 'work_quoted',
                        'value' => '"'.$_GET['work_quoted'].'"',
                        'compare' => 'LIKE'
                ) ) );
            }
        }
    }
}
add_filter( 'pre_get_posts', 'filter_quote_work_lookup' );


/**
 * Sort works by first author then title
 */
if ( ! function_exists( 'sort_works_alphabetically' ) ) {
    function sort_works_alphabetically( $query ) {
        if ( $query->is_post_type_archive( 'works' ) ) {
            $query->set( 'meta_key', 'sort_credit' );
            $query->set( 'orderby', array( 'meta_value' => 'ASC',
                                           'title' => 'ASC' ) );
        }
    }
}
add_filter( 'pre_get_posts', 'sort_works_alphabetically' );


/**
 * Replacement for builtin get_the_posts_navigation that flips 'next' and 'previous' link order.
 */
function x_get_the_posts_navigation( $args = array() ) {
    $navigation = '';

    // Don't print empty markup if there's only one page.
    if ( $GLOBALS['wp_query']->max_num_pages > 1 ) {
        $args = wp_parse_args( $args, array(
            'prev_text'          => __( 'Previous page' ),
            'next_text'          => __( 'Next page' ),
            'screen_reader_text' => __( 'Posts navigation' ),
        ) );

        // builtin literally assigns previous_posts_link to next_link and v.v. -_-
        $next_link = get_next_posts_link( $args['next_text'] );
        $prev_link = get_previous_posts_link( $args['prev_text'] );

        if ( $prev_link ) {
            $navigation .= '<div class="nav-previous">' . $prev_link . '</div>';
        }

        if ( $next_link ) {
            $navigation .= '<div class="nav-next">' . $next_link . '</div>';
        }

        $navigation = _navigation_markup( $navigation, 'posts-navigation', $args['screen_reader_text'] );
    }

    return $navigation;
}


/**
 * Sort Sources by term_order; this and next function from https://core.trac.wordpress.org/ticket/5857
 * It's for a very old version of WordPress but is linked from the codex on taxonomies.
 *
 * @param array $terms array of objects to be replaced with sorted list
 * @param integer $id post id
 * @param string $taxonomy only 'source' is changed.
 * @return array of objects
 */
function plugin_get_the_ordered_terms( $terms, $id, $taxonomy ) {
    if ( 'source' != $taxonomy )
        return $terms;

    $terms = wp_cache_get($id, "{$taxonomy}_relationships_sorted");
    if ( false === $terms ) {
        $terms = wp_get_object_terms( $id, $taxonomy, array( 'orderby' => 'term_order' ) );
        wp_cache_add($id, $terms, $taxonomy . '_relationships_sorted');
    }

    return $terms;
}
add_filter( 'get_the_terms', 'plugin_get_the_ordered_terms', 10, 4 );


/**
 * Adds sorting by term_order to source by doing a partial register replacing the default;
 * version of this from core.trac.wordpress above changed all the names from Source(s) to Tag(s);
 * this version from https://wordpress.stackexchange.com/questions/161788/how-to-modify-a-taxonomy-thats-already-registered
 * minus a 3rd arg to add_action (11) since this was overriding the already registered tax anyway
 */
function plugin_register_sorted_sources() {
    // get the arguments of the already-registered taxonomy
    $source_args = get_taxonomy( 'source' ); // returns an object

    // make changes to the args; in this example there are three changes; again, note that it's an object
    $source_args->sort = true;
    $source_args->orderby = 'term_order';

    // re-register the taxonomy
    register_taxonomy( 'source', array( 'quotes', 'works' ), (array) $source_args );
}
add_action( 'init', 'plugin_register_sorted_sources' );


/**
 * hook into handler for rest api's POST quotes endpoint, adding custom taxonomy terms
 * h/t https://stackoverflow.com/a/47267372
 */
function action_rest_insert_quotes( $post, $request, $true ) {
    $params = $request->get_json_params();
    if(array_key_exists("terms", $params)) {
        foreach($params["terms"] as $taxonomy => $terms) {
            wp_set_post_terms($post->ID, $terms, $taxonomy);
        }
    }
}
add_action("rest_insert_quotes", "action_rest_insert_quotes", 10, 3);


/**
 *  include a custom template for photos search results (uses Bootstrap card columns like the archive);
 *  redirect album and keyword archives to use photo archive template
 *  Source: https://wordpress.stackexchange.com/a/89945
 *  https://www.billerickson.net/code/use-same-template-for-taxonomy-and-cpt-archive/
 */
function mm_template_chooser( $template ) {
    global $wp_query;
    $post_type = get_query_var('post_type');
    if ( $wp_query->is_search && $post_type == 'photos' ) {
        return locate_template('search-photos.php');
    } elseif ( is_tax( 'albums' ) || is_tax( 'keywords' ) ) {
        $template = get_query_template( 'archive-photos' );
    }
    return $template;
}
add_filter('template_include', 'mm_template_chooser');


/**
 *  increase the posts_per_page to 12 for photos, keywords, and albums
 */
function mm_custom_posts_per_page( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
       return;
    }

    if ( is_post_type_archive( 'photos' ) || is_tax( 'albums' ) || is_tax( 'keywords' ) ) {
       $query->set( 'posts_per_page', 12 );
    }
}
add_filter( 'pre_get_posts', 'mm_custom_posts_per_page' );


/**
 * Register footer widget areas.
 *
 * @link https://www.tipsandtricks-hq.com/how-to-add-widgets-to-wordpress-themes-footer-1033
 */
function mm_widgets_init() {
    register_sidebar( array(
        'name' => esc_html__( 'Footer Sidebar 1', 'monza' ),
        'id' => 'footer-sidebar-1',
        'description'   => esc_html__( 'Left footer widget area', 'monza' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ) );
    register_sidebar( array(
        'name' => esc_html__( 'Footer Sidebar 2', 'monza' ),
        'id' => 'footer-sidebar-2',
        'description'   => esc_html__( 'Middle footer widget area', 'monza' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ) );
    register_sidebar( array(
        'name' => esc_html__( 'Footer Sidebar 3', 'monza' ),
        'id' => 'footer-sidebar-3',
        'description'   => esc_html__( 'Right footer widget area', 'monza' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'mm_widgets_init' );


require get_template_directory() . '/../monza-mod-vyh/inc/template-tags.php';

?>