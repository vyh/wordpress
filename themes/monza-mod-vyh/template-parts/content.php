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
        if ( 'post' === get_post_type() ) { ?>
        <div class="entry-cat">
            <?php the_category(', '); ?>
        </div><?php
        }
        if ( is_singular() ) {
            the_title( '<h1 class="entry-title">', '</h1>' );
            monza_posted_on();
        } else {
            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        }
    ?>
    </header>
    <?php if ( is_single() ) { ?>
    <div class="entry-content">
        <?php
            the_content();
            wp_link_pages( array(
                'before'      => '<div class="page-links">' . __( 'Pages:', 'monza' ),
                'after'       => '</div>',
                'link_before' => '<span class="page-number">',
                'link_after'  => '</span>',
            ) );
        ?>
    </div>
    <?php } else { ?>
    <div class="entry-content">
        <?php the_excerpt(); ?>
    </div>
    <div class="entry-more">
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php esc_html_e( 'Read More', 'monza' ); ?></a>
    </div>
    <?php } ?>
</article>
