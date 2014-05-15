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

		<?php the_content() ?>
		<?php
			if (rrze_dlp_fields() != '') {
				rrze_dlp_fields();
			}
		?>

		<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'rrze-dlp' ), 'after' => '</div>' ) ); ?>

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
        <?php edit_post_link( __( 'Edit', 'rrze-dlp' ), '<div class="edit-link">', '</div>' ); ?>
    </footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->

	<?php
			$num_comments = get_comments_number(); // get_comments_number returns only a numeric value
			if ( comments_open() ) { ?>
				<div id="comments">
				<?php if ( $num_comments == 0 ) {
					$write_comments = __('Comments', 'rrze-dlp');
				} elseif ( $num_comments > 1 ) {
					$write_comments = $num_comments . __(' Comments', 'rrze-dlp');
				} else {
					$write_comments = __('1 Comment', 'rrze-dlp');
				} ?>

					<h2><?php echo $write_comments ?></h2>
					<?php comment_form(); ?>
					<ol class="commentlist">
					<?php
						//Gather comments for a specific page/post
						$comments = get_comments(array(
							'post_id' => $post->ID,
							'status' => 'approve'
						));
						//Display the list of comments
						wp_list_comments(array('type' => 'all'), $comments);
						//Display Prev/Next Links ?>
					</ol>
					<span class="comments-nav-prev"><?php previous_comments_link(); ?></span>
					<span class="comments-nav-next"><?php next_comments_link(); ?></span>

				</div><!-- #comments -->
			<?php } ?>