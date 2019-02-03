<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Monza
 */

?>

    </div><!-- #content -->

    <!-- #footer widgets if present -->
    <?php
    $footer_1_active = is_active_sidebar('footer-sidebar-1');
    $footer_2_active = is_active_sidebar('footer-sidebar-2');
    $footer_3_active = is_active_sidebar('footer-sidebar-3');
    if ( $footer_1_active || $footer_2_active || $footer_3_active ) {
    ?>
    <div id="footer-sidebar" class="secondary site-footer-widgets row">
        <div class="col-md-1 col-sm-0"></div>
        <div class="col-md-10 col-sm-12">
            <div id="footer-sidebar1" class="widget-area col-md-4 col-sm-4">
                <?php if( $footer_1_active ) dynamic_sidebar('footer-sidebar-1'); ?>
            </div>
            <div id="footer-sidebar2" class="widget-area col-md-4 col-sm-4">
                <?php if( $footer_2_active ) dynamic_sidebar('footer-sidebar-2'); ?>
            </div>
            <div id="footer-sidebar3" class="widget-area col-md-4 col-sm-4">
                <?php if( $footer_3_active ) dynamic_sidebar('footer-sidebar-3'); ?>
            </div>
        </div>
        <div class="col-md-1 col-sm-0"></div>
    </div>
    <?php } ?><!-- #endif -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="site-info">
                <!-- define my_social_links wherever you place your php snippets -->
                <?php my_social_links(); ?>
            </div><!-- .site-info -->
        </div>
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
