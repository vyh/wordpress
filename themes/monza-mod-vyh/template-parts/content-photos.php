<?php
/**
 * Template part for displaying photos
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Monza
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php monza_post_thumbnail(); ?>
    <header class="entry-header">
    <?php
        if ( is_singular() ) {
            the_title( '<h4>', '</h4>' );
        } else {
            the_title( '<h4><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' );
        }
    ?>
    </header>
    <?php 
    if ( is_single() ) { 
        echo '
    <div class="entry-content">
        '; the_content(); echo '
    </div>';
    } else {
        echo '<br />';
    } ?>
</article>
