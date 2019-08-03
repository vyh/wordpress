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
        if ( 'works' === get_post_type() ) { ?>
        <div class="entry-cat">
            <?php
            $genres = get_the_terms( $post->ID, 'genre' );
            if ( $genres ) {
                echo '<a href="/genre/' . $genres[0]->slug . '?post_type=quotes">' . $genres[0]->name . '</a>';
                foreach ( array_slice($genres, 1) as $genre ) {
                    echo ', <a href="/genre/' . $genre->slug . '?post_type=quotes">' . $genre->name . '</a>';
                }
            }
            ?>
        </div><?php
        }
        $authors = get_the_terms($post->ID, 'source');
        $translators = $post->translators; ?>
        <h4><?php
        // include authors (each linked to 'source' archive of quotes) on title line, first one last-name-first
        if ( $authors ) {
            $author = explode(' ', $authors[0]->name);
            if ( isset($author[1]) && !empty($author[1]) )
                $author = join(', ', array(array_pop($author), join(' ', $author)));  // L.N. first
            else
                $author = $author[0];  // one-word name, e.g. Aristotle
            echo  '<a href="/source/' . $authors[0]->slug . '?post_type=quotes">' . $author . '</a>';
            // the other authors
            foreach ( array_slice($authors, 1) as $author ) {
                echo '; <a href="/source/' . $author->slug . '?post_type=quotes">' . $author->name . '</a>';
            }
            echo ': ';
        }
        // link title to quote search instead of work "post"
        the_title( '<em><a href="/quotes/?work_quoted=' . $post->ID . '">', '</a></em>' ); ?>
        </h4><?php
        $translators = get_field( 'translators' );
        if ( $translators ) {
        ?>
        <h5><?php
            // print out the translators, if any, below the author/title line
            if ( isset($translators[1]) ) echo 'translators: ';
            else echo 'translator: ';
            echo $translators[0]->name;
            foreach ( array_slice( $translators, 1 ) as $translator ) {
                echo '; ' . $translator->name;
            } ?>
        </h5><?php
        } ?>
        <span class="posted-on"><?php echo $post->year; ?></span>
    </header>
    <div class="entry-content">
        <?php
            the_content();
            // link to an external page about the book
            $url = $post->url;
            $e = explode('.', parse_url($url, PHP_URL_HOST));
            array_pop($e);
            $site_name = array_pop($e);
        ?>
        <div>
            (see @ <a href="<?php echo $url; ?>"><?php echo $site_name; ?></a>)
        </div>
    </div>
</article>
