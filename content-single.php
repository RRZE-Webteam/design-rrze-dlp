<?php
/**
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
		
		$beschreibung = get_post_meta($post->ID, 'beschreibung', true);
		$kurzbeschreibung = get_post_meta($post->ID, 'service', true);

		$content = apply_filters( 'the_content', get_the_content() );	
		$content = str_replace( ']]>', ']]&gt;', $content );
		
		
		if (isset($kurzbeschreibung) && (!empty($kurzbeschreibung))) {
		    echo $kurzbeschreibung;
		}
		
		if (isset($content) && (strlen($content)>2)) {
		    if (!empty($kurzbeschreibung)) {
			echo "<h2>Beschreibung</h2>";
		    }
		    echo $content;
		} elseif (isset($beschreibung) && (!empty($beschreibung))) {
		    if (!empty($kurzbeschreibung)) {
			echo "<h2>Beschreibung</h2>";
		    }
		    echo $beschreibung;
		}
		
		
		if (rrze_dlp_fields() != '') { rrze_dlp_fields();}
		wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'rrze-dlp' ), 'after' => '</div>' ) ); ?>
    </div><!-- .entry-content -->
    <footer class="entry-meta">
        <?php
            /* translators: used between list items, there is a space after the comma */
            $category_list = get_the_category_list( __( ', ', 'rrze-dlp' ) );

            /* translators: used between list items, there is a space after the comma */
			$tag_list = get_the_tag_list( '', __( ', ', 'rrze-dlp' ) );

            if ( '' != $tag_list ) {
				$meta_text = __( 'Categories: %1$s<br />Tags: %2$s<br/>Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'rrze-dlp' );
			} elseif ( '' != $category_list ) {
				$meta_text = __( 'Categories: %1$s<br />Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'rrze-dlp' );
			} else {
				$meta_text = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'rrze-dlp' );
			}

            printf(
                $meta_text,
                $category_list,
                $tag_list,
                get_permalink(),
                the_title_attribute( 'echo=0' )
            );
        ?>
		<?php rrze_dlp_modified(); ?>
		<?php edit_post_link( __( 'Edit', 'rrze-dlp' ), '<div style="margin-top: 10px;font-size: 1.2em;"><span class="edit-link">', '</span></div>' ); ?>
    </footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->

<?php if (function_exists('dlp_contextnav_post')) dlp_contextnav_post();?>
<?php if (function_exists('dlp_show_ancestor')) dlp_show_ancestor(); ?>
