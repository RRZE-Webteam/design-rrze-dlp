<?php
/**
 * The template for displaying the footer.
 *
 * @package RRZE-DLP
 * @since RRZE-DLP 2.0
*/
?>

<nav role="navigation" class="site-navigation main-navigation" id="hauptmenu">
					 <h1 class="assistive-text"><?php _e( 'Menu', 'rrze-dlp' ); ?></h1>
					 <div class="assistive-text skip-link">
						 <a href="#content" title="<?php esc_attr_e( 'Skip to content', 'rrze-dlp' ); ?>"><?php _e( 'Skip to content', 'rrze-dlp' ); ?></a>
					 </div>
					 <?php wp_nav_menu( array( 'theme_location' => 'primary') ); ?>
				 </nav><!-- .site-navigation .main-navigation -->

</div><!-- #main .site-main -->

<footer id="colophon" class="site-footer" role="contentinfo">
    <div id="zusatzinfo">
		<?php if ( ! is_404() ): ?>
			<?php if( ! dynamic_sidebar( 'sidebar-footer' ) ) : ?>
				<p>
					<?php _e( 'This area shows additional information in the footer. You can add useful links or other information displayed on every page. They are excluded from print layout.', 'rrze-dlp' ); ?>
				</p>
			<?php endif; ?>

		<?php endif; ?>
		</div><!-- .zusatzinfo -->
</footer><!-- #colophon .site-footer -->

</div><!-- #page .hfeed .site -->

<?php wp_footer(); ?>

</body>
</html>