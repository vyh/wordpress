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
				/* Define my_separate_category where you keep your php snippets */
				if ( ! is_category( my_separate_category() ) ) {
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="archive-description">', '</div>' );
				} else if ( is_tag() ) {
					/* If we are on a tag archive under 'separate' category, add Tag: header */
					$uri = explode('/', explode('?', $_SERVER['REQUEST_URI'])[0]);
					$i = array_search('tag', $uri);
					if ($i) {
						$tag_slug = explode(',', $uri[$i + 1])[0];
						$tag_name = get_term_by('slug', $tag_slug, 'post_tag')->name;
						echo '<h1 class="page-title">Tag: '.$tag_name.'</h1>';
					}
				} else if ( is_month() ) {
					/* If we are on a month archive under 'separate' category, add Month: header */
					echo '<h1 class="page-title">Month:';
					single_month_title(' ');
					echo '</h1>';
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

    			the_posts_navigation();

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
