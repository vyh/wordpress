<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Monza
 */

get_header();
?>
    <div class="container">
        <div id="primary" class="content-area row">
            <div class='col-md-12 col-sm-12'>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header><!-- .entry-header -->
            </div><!-- .page-header -->
            <div class="col-md-1 col-sm-0"></div>
            <div class="col-md-10 col-sm-12">
                <main id="main" class="site-main">
                <?php
                while ( have_posts() ) :
                    the_post();

                    get_template_part( 'template-parts/content', 'page' );

                    // If comments are open or we have at least one comment, load up the comment template.
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;

                endwhile; // End of the loop.
                ?>
                </main><!-- #main -->
            </div>
            <div class="col-md-1 col-sm-0"></div>
        </div><!-- #primary -->
    </div>
<?php
get_footer();
