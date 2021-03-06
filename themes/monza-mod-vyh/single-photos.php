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
        <div class="col-md-1 col-sm-0"></div>
        <div class="col-md-10 col-sm-12">
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
            the_post_navigation();

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
