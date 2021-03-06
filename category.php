<?php get_header(); ?>

	<div id="container">

        <div id="content" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php
						printf( __( 'Archive by category: %s', 'rrze-dlp' ), '<span><em>' . single_cat_title( '', false ) . '</em></span>' );
					?></h1>

					<?php
						$category_description = category_description();
						if ( ! empty( $category_description ) )
							echo apply_filters( 'category_archive_meta', '<div class="category-archive-meta">' . $category_description . '</div>' );
					?>
				</header>

				<?php while ( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <header class="entry-header">
                        <h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'rrze-dlp' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
                    </header>

                    <div class="entry-summary">
                        <?php $custom_meta = get_post_meta( get_the_ID(), 'service' );
						// check if the custom field has a value
						if( ! empty( $custom_meta ) ) {
							echo '<p>' . $custom_meta[0] . '</p>';
						} else {
							the_excerpt(); } ?>
						<a href="<?php echo get_permalink(); ?>" class="readmore"><?php echo __('Read More...', 'rrze-dlp')?></a>
                    </div>

                    <footer class="entry-meta">
						<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
							<?php
								/* translators: used between list items, there is a space after the comma */
								$categories_list = get_the_category_list( __( ', ', 'rrze-dlp' ) );
								if ( $categories_list ) :
							?>
							<span class="cat-links">
								<?php printf( __( 'Posted in %1$s', 'rrze-dlp' ), $categories_list ); ?>
							</span>
							<?php endif; // End if categories ?>

							<?php
								/* translators: used between list items, there is a space after the comma */
								$tags_list = get_the_tag_list( '', __( ', ', 'rrze-dlp' ) );
								if ( $tags_list ) :
							?>
							<span class="sep"> | </span>
							<span class="tag-links">
								<?php printf( __( 'Tagged %1$s', 'rrze-dlp' ), $tags_list ); ?>
							</span>
							<?php endif; // End if $tags_list ?>
						<?php endif; // End if 'post' == get_post_type() ?>

						<?php rrze_dlp_modified(); ?>

						<?php edit_post_link( __( 'Edit', 'rrze-dlp' ), '<span class="sep"> | </span><span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->

                </article>

				<?php endwhile; ?>

				<span class="page-nav-prev"><?php previous_posts_link(); ?></span>
				<span class="page-nav-next"><?php next_posts_link(); ?></span>

			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'rrze-dlp' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'Sorry, but there are no results in this archive.', 'rrze-dlp' ); ?></p>
					</div>
				</article>

			<?php endif; ?>

        </div><!-- #content -->

    </div><!-- #container -->

</div><!-- #main -->

<?php get_footer(); ?>

