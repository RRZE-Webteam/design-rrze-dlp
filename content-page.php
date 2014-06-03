<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package RRZE-DLP
 * @since RRZE-DLP 2.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
    </header><!-- .entry-header -->

    <div class="entry-content">
		<?php
		if ( has_post_thumbnail() ) {	the_post_thumbnail(); }
		the_content();
		if (rrze_dlp_fields() != '') { rrze_dlp_fields();}
		wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'rrze-dlp' ), 'after' => '</div>' ) );
		edit_post_link( __( 'Edit', 'rrze-dlp' ), '<span class="edit-link">', '</span>' );?>
    </div><!-- .entry-content -->
	
</article><!-- #post-<?php the_ID(); ?> -->

<?php if (is_front_page() && function_exists('dlp_contextnav_front')) dlp_contextnav_front(); ?>