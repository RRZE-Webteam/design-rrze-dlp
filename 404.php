<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package RRZE-DLP
 * @since RRZE-DLP 2.0
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">

            <article id="post-0" class="post error404 not-found">
                <header class="entry-header">
                    <h1 class="entry-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'rrze-dlp' ); ?></h1>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <p><?php _e( 'It looks like nothing was found at this location. Perhaps searching can help.', 'rrze-dlp' ); ?></p>

                    <?php get_search_form(); ?>

                    <?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>

                </div><!-- .entry-content -->
            </article><!-- #post-0 .post .error404 .not-found -->

        </div><!-- #content .site-content -->
    </div><!-- #primary .content-area -->

<?php get_footer(); ?>