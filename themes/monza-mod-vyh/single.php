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
            $post_type = get_post_type();
            get_template_part( 'template-parts/content', $post_type );
            if ( 'post' == $post_type && get_the_tags() ) { ?>
            <div class="post-tags"><?php the_tags(); ?></div><?php
            } else if ( 'quotes' == $post_type ) {
                $works = get_field('work_quoted');
                echo '<div class="post-tags">Works: ';
                echo '<a href="/quotes/?work_quoted='. $works[0]->ID .'">' . $works[0]->post_title . '</a>';
                foreach ( array_slice($works, 1) as $work )
                    echo ', <a href="/quotes/?work_quoted='. $work->ID .'">' . $work->post_title . '</a>';
                echo '</div>';
                echo '<div class="post-tags">';
                the_terms($post->ID, 'source', 'Authors: ');
                echo '</div>';
                if ( get_the_terms( $post->ID, 'topic' ) ) {
                    echo '<div class="post-tags">';
                    the_terms($post->ID, 'topic', 'Topics: ');
                    echo '</div>';
                }
            } ?>

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
        <div class="col-md-3 col-sm-4 sidebar">
            <?php get_sidebar(); ?>
        </div>

    </div>
</div>
<?php
get_footer();
