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
                if ( is_post_type_archive( 'works' ) ) {
                    echo '<h1 class="page-title">Bibliography</h1>';
                } else if ( is_post_type_archive( 'quotes' ) ) {
                    echo '<h1 class="page-title">Quotes</h1>';
                } else {
                    the_archive_title( '<h1 class="page-title">', '</h1>' );
                }
                the_archive_description( '<div class="archive-description">', '</div>' );
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
