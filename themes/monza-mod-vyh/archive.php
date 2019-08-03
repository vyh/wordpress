<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Monza
 */
get_header(); ?>
<div class="container">
    <div class="row">
        <div class="col-md-9 col-sm-8">
            <?php
            if ( have_posts() ) : ?>
            <header class="page-header">
                <?php
                if ( is_post_type_archive( array( 'quotes', 'works' ) ) ) {
                    $p_title = is_post_type_archive('works') ? 'Bibliography' : 'Quotes';
                    echo '<h1 class="page-title">' . $p_title . '</h1>';
                    the_archive_description( '<div class="archive-description">', '</div>' );
                    if ( is_tax() ) {
                        $tname = get_query_var('taxonomy');
                        $tterm = get_query_var('term');
                        $tterm = get_term_by( 'slug', $tterm, $tname )->name;
                        $tname = 'source' == $tname ? 'author' : $tname;
                        if ( $tname || $tterm ) echo '<h3>' . ucwords($tname) . ': ' . $tterm . '</h3><br />';
                    } else {
                        $wid = isset($_GET['work_quoted']) ? $_GET['work_quoted'] : null;
                        if ( $wid ) {
                            $wtitle = get_post($wid)->post_title;
                            if ( $wtitle ) echo '<h3>Work: <em>' . $wtitle . '</em></h3><br />';
                        }
                    }
                } else {
                    the_archive_title( '<h1 class="page-title">', '</h1>' );
                    the_archive_description( '<div class="archive-description">', '</div>' );
                }
                ?>
            </header><!-- .page-header -->
            <?php

                /* Start the Loop */
                while ( have_posts() ) :
                    the_post();

                    /*
                     * Include the Post-Type-specific template for the content.
                     * If you want to override this in a child theme, then include a file
                     * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                     */
                    get_template_part( 'template-parts/content', get_post_type() );

                endwhile;

                // previous link on left, next on right, refer to 'page' not 'posts'
                echo x_get_the_posts_navigation();

            else :

                get_template_part( 'template-parts/content', 'none' );

            endif;
            ?>
        </div>
        <div class="col-md-3 col-sm-4 sidebar">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php
get_footer();
