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
        <div class="col-md-9 col-sm-8">
        <?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
            if ( get_the_tags() ) { ?>
            <div class="post-tags"><?php the_tags(); ?></div><?php
            } ?>

            <div class="entry-share">
        		<?php get_template_part('template-parts/content', 'social-sharing'); ?>
        	</div>
            <?php
			/* Define my_separate_category wherever you keep your php snippets */
			$sep_cat = my_separate_category();
			if ( in_category($sep_cat) ) {
				$nav_args = array('in_same_term'   => true,
								  'taxonomy'       => 'category');
			} else {
				$nav_args = array('excluded_terms' => "$sep_cat",
								  'taxonomy'       => 'category');
			}
			the_post_navigation( $nav_args );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>
        </div>
        <div class="col-md-3 col-sm-4 sidebar">
            <?php get_sidebar(); ?>
        </div>

    </div>
</div>
<?php
get_footer();
