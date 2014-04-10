<?php
/**
 * The main template file.
 *
 * @package RRZE-DLP
 * @since RRZE-DLP 2.0
 */

get_header(); ?>

<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">
		<?php if ( have_posts() ) : ?>
		<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				 <?php
				 /* Include the Post-Format-specific template for the content.
				 * If you want to overload this in a child theme then include a file
				 * called content-___.php (where ___ is the Post Format name) and    that will be used instead.
				 */
				 get_template_part( 'content', get_post_format() );
				 ?>
			<?php endwhile; ?>
		<?php endif; ?>
    </div><!-- #content .site-content -->
</div><!-- #primary .content-area -->

<?php get_footer(); ?>