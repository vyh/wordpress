<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Monza
 */

get_header();
?>

<!--     <section id="primary" class="content-area">
        <main id="main" class="site-main"> -->
<div class="container">
    <div class="row">
        <div class='col-md-12 col-sm-12'>

        <?php if ( have_posts() ) : ?>

            <header class="page-header">
                <h1 class="page-title">
                    <?php
                    /* translators: %s: search query. */
                    printf( esc_html__( 'Search Results for: %s', 'monza' ), '<span>' . get_search_query() . '</span>' );
                    ?>
                </h1>
            </header>
        </div><!-- .page-header -->
        <div class="col-md-1 col-sm-0"></div>
        <div class="col-md-10 col-sm-12 card-columns">

            <?php
            /* Start the Loop */
            while ( have_posts() ) :
                the_post();

                /**
                 * Run the loop for the search to output the results.
                 * If you want to overload this in a child theme then include a file
                 * called content-search.php and that will be used instead.
                 */
                echo '<div class="card text-center">';
                get_template_part( 'template-parts/content', 'search' );
                echo '</div>';

            endwhile;

            the_posts_navigation();

        else :

            get_template_part( 'template-parts/content', 'none' );

        endif;
        ?>

        </div>

    </div>
</div>

<!--         </main> --><!-- #main -->
<!--    </section> --><!-- #primary -->

<?php
get_footer();
