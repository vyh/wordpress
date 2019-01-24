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
        <div class='col-md-12 col-sm-12'>
            <?php
            if ( have_posts() ) : ?>
            <header class="page-header">
                <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
                <?php
                the_archive_description( '<div class="archive-description">', '</div>' );
                $add_br = false;
                if ( is_tax() ) {
                    $tname = get_query_var('taxonomy');
                    $tterm = get_query_var('term');
                    $tterm = get_term_by( 'slug', $tterm, $tname )->name;
                    if ( $tname || $tterm ) {
                        echo '<h3>' . ucwords($tname) . ': ' . $tterm . '</h3>';
                        $add_br = true;
                    }
                } 
                if ( get_query_var( 'featured' ) ) {
                    $filter = get_query_var( 'featured' ) == 'true' ? 'featured' : 'not featured';
                    echo '<h3>' . 'Showing: ' . $filter . '</h3><br />';
                    $add_br = true;
                } elseif ( get_query_var( 'prints' ) ) {
                    $filter = get_query_var( 'prints' ) == 'true' ? 'prints available' : 'prints not yet available';
                    echo '<h3>' . 'Showing: ' . $filter . '</h3><br />';
                    $add_br = true;
                }
                if ( $add_br ) echo '<br />';
                ?>
            </header>
        </div><!-- .page-header -->
        <div class="col-md-1 col-sm-0"></div>
        <div class="col-md-10 col-sm-12 card-columns">
            <?php

                /* Start the Loop */
                while ( have_posts() ) :
                    the_post();

                    /*
                     * Include the Post-Type-specific template for the content.
                     * If you want to override this in a child theme, then include a file
                     * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                     */
                    echo '<div class="card text-center">';
                    get_template_part( 'template-parts/content', get_post_type() );
                    echo '</div>';

                endwhile;

                // previous link on left, next on right, refer to 'page' not 'posts'
                echo '
        </div>
        <div class="col-md-1 col-sm-0"></div>
        <div class="col-md-1 col-sm-0"></div>
        <div class="col-md-10 col-sm-12">';
                echo x_get_the_posts_navigation();
                echo '
        </div>';

            else :

                get_template_part( 'template-parts/content', 'none' );

            endif;
            ?>
        </div>
    </div>
</div>
<?php
get_footer();
