<?php
/**
 * The template for displaying the footer.
 *
 * @package RRZE-DLP
 * @since RRZE-DLP 2.0
*/
?>

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