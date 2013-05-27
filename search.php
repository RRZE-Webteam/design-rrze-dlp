<?php get_header(); ?>

<?php
global $wpdb;

$keyword = get_search_query();
$keyword = "%{$keyword}%";

// Search in all custom fields
$post_ids_meta = $wpdb->get_col( $wpdb->prepare( "
    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
    WHERE meta_value LIKE '%s'
", $keyword ) );

// Search in post_title and post_content
$post_ids_post = $wpdb->get_col( $wpdb->prepare( "
    SELECT DISTINCT ID FROM {$wpdb->posts}
    WHERE post_title LIKE '%s'
    OR post_content LIKE '%s'
", $keyword, $keyword ) );
    
$post_ids = array_merge( $post_ids_meta, $post_ids_post );

$args = array(
    'post_type'   => 'post',
    'post_status' => 'publish',
    'post__in'    => $post_ids,
);

$query = new WP_Query( $args );
?>
<div id="main">

    <div id="container">
        
        <div id="content" role="main">
            
        <?php if( $query->have_posts() ) : ?>

            <header class="page-header">
                <h2 class="page-title"><?php printf( __( 'Suchergebnisse für: %s', '_rrze' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
            </header>

            <?php while( $query->have_posts() ) : $query->the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <header class="entry-header">
                        <h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink zu %s', '_rrze' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
                    </header>

                    <div class="entry-content">
                        <?php the_content( __( 'Weiterlesen <span class="meta-nav">&rarr;</span>', '_rrze' ) ); ?>
                        <?php wp_link_pages( array( 'before' => '<nav id="nav-pages"><div class="ym-wbox"><span>' . __( 'Seiten:', '_rrze' ) . '</span>', 'after' => '</div></nav>' ) ); ?>
                    </div>
                    
                    <footer class="entry-meta">
                        <?php
                        $utility_text = '';
                        if( get_post_type() == 'post' ):
                            $menu_list = _rrze_menu_items_list( get_the_ID() );
                            $categories_list = get_the_category_list(', ');
                            $tag_list = get_the_tag_list('', ', ');                        

                            if( '' != $menu_list )
                                $utility_text .= sprintf( _n( 'Diese Seite wurde diesem Menü: %1$s zugeordnet.<br/>', 'Diese Seite wurde diesen Menüs: %1$s zugeordnet.<br/>', count( explode( ', ', $menu_list ) ), '_rrze' ), $menu_list );

                            if( '' != $categories_list )
                                $utility_text .= sprintf( __( 'Kategorien: %1$s.<br/>', '_rrze' ), $categories_list );

                            if( '' != $tag_list )
                                $utility_text .= sprintf( __( 'Schlagwörter: %1$s.<br/>', '_rrze' ), $tag_list );

                            $utility_text .= sprintf( __( 'Diesen Artikel <a href="%1$s" title="Permalink zu %2$s" rel="bookmark"> zu Ihren Lesezeichen hinzufügen</a>.<br/>', '_rrze' ), esc_url( get_permalink()), the_title_attribute( 'echo=0' ) );
                        endif;
                        printf( '%s%s<br/>', $utility_text, _rrze_last_modified_on() );
                        ?>
                        <?php edit_post_link( __( 'Bearbeiten', '_rrze' ), '<span class="edit-link">', '</span>' ); ?>
                    </footer>

                </article>

            <?php endwhile; ?>

            <?php echo _rrze_pages_nav(); ?>

        <?php else : ?>

            <article id="post-0" class="post no-results not-found">
                <header class="entry-header">
                    <h2 class="entry-title"><?php _e( 'Es konnte nichts gefunden werden.', '_rrze' ); ?></h2>
                </header>

                <div class="entry-content">
                    <p><?php _e( 'Entschuldigen Sie, aber es konnte nichts gefunden werden. Versuchen Sie es mit anderen Schlüsselwörtern erneut.', '_rrze' ); ?></p>
                </div>
            </article>

        <?php endif; ?>

        </div><!-- #content -->
        
    </div><!-- #container -->

</div><!-- #main -->

<?php get_footer(); ?>
