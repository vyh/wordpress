<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Monza
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="entry-summary">
        <?php get_template_part( 'template-parts/content', get_post_type() ); ?>
    </div><!-- .entry-summary -->

</article><!-- #post-<?php the_ID(); ?> -->
