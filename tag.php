<?php get_header(); ?>

<div id="main">

    <div id="container">

        <div id="content" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php
						printf( __( 'Schlagwort-Archive: %s', 'rrze-dlp' ), '<span>' . single_tag_title( '', false ) . '</span>' );
					?></h1>

					<?php
						$tag_description = tag_description();
						if ( ! empty( $tag_description ) )
							echo apply_filters( 'tag_archive_meta', '<div class="tag-archive-meta">' . $tag_description . '</div>' );
					?>
				</header>

				<?php while ( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <header class="entry-header">
                        <h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink zu %s', 'rrze-dlp' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
                    </header>

                    <?php if ( is_search() ) : ?>
                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                    </div>
                    <?php else : ?>
                    <div class="entry-content">
                        <?php the_content( __( 'Weiterlesen <span class="meta-nav">&rarr;</span>', 'rrze-dlp' ) ); ?>
                        <?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Seiten:', 'rrze-dlp' ) . '</span>', 'after' => '</div>' ) ); ?>
                    </div>
                    <?php endif; ?>

                    <footer class="entry-meta">
                        <?php
                        $utility_text = '';
                        $menu_list = rrze-dlp_menu_items_list( get_the_ID() );
                        $categories_list = get_the_category_list(', ');
                        $tag_list = get_the_tag_list('', ', ');

                        if( '' != $menu_list )
                            $utility_text .= sprintf( _n( 'Diese Seite wurde diesem Menü: %1$s zugeordnet.<br/>', 'Diese Seite wurde diesen Menüs: %1$s zugeordnet.<br/>', count( explode( ', ', $menu_list ) ), 'rrze-dlp' ), $menu_list );

                        if( '' != $categories_list )
                            $utility_text .= sprintf( __( 'Kategorien: %1$s.<br/>', 'rrze-dlp' ), $categories_list );

                        if( '' != $tag_list )
                            $utility_text .= sprintf( __( 'Schlagwörter: %1$s.<br/>', 'rrze-dlp' ), $tag_list );

                        $utility_text .= sprintf( __( 'Diesen Artikel <a href="%1$s" title="Permalink zu %2$s" rel="bookmark"> zu Ihren Lesezeichen hinzufügen</a>.<br/>', 'rrze-dlp' ), esc_url( get_permalink()), the_title_attribute( 'echo=0' ) );

                        printf( '%s%s<br/>', $utility_text, _rrze_last_modified_on() );
                        ?>
                        <?php edit_post_link( __( 'Bearbeiten', 'rrze-dlp' ), '<span class="edit-link">', '</span>' ); ?>
                    </footer>

                </article>

				<?php endwhile; ?>

			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Es konnte nichts gefunden werden.', 'rrze-dlp' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'Entschuldigen Sie bitte, aber in diesem Archiv wurden keine Ergebnisse gefunden.', 'rrze-dlp' ); ?></p>
					</div>
				</article>

			<?php endif; ?>

        </div><!-- #content -->

    </div><!-- #container -->

</div><!-- #main -->

<?php get_footer(); ?>
