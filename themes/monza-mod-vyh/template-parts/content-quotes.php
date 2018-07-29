<?php
/**
 * Template part for displaying posts
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Monza
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php monza_post_thumbnail(); ?>
    <header class="entry-header">
    <?php
        if ( 'quotes' === get_post_type() ) { ?>
        <div class="entry-cat">
            <?php the_terms( $post->ID, 'genre', '', ', '); ?>
        </div><?php
        }
        if ( is_singular() ) {
            the_title( '<h4>', '</h4>' );
            monza_posted_on();
        } else {
            the_title( '<h4><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' );
        }
    ?>
    </header>
    <div class="entry-content">
        <?php the_content();  ?>
    </div>
</article>
