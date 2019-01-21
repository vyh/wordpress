<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Monza
 */

get_header();
?>
<div class="container">
    <div class="row">
        <div class="col-md-2 col-sm-2"></div>
        <div class="col-md-8 col-sm-8">
        <?php
        while ( have_posts() ) :
            the_post();
            $post_type = get_post_type();
            get_template_part( 'template-parts/content', $post_type );
        ?>

            <div class="entry-share">
                <?php get_template_part('template-parts/content', 'social-sharing'); ?>
            </div>
            <?php
            the_post_navigation( $nav_args );

            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

        endwhile; // End of the loop.
        ?>
        </div>

    </div>
</div>
<?php
get_footer();
