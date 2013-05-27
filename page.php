<?php get_header(); ?>

<div id="main">

    <div id="container">
        
        <div id="content" role="main">
            <?php while( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <header class="entry-header">
                        <h2><?php the_title(); ?></h2>
                    </header>

                    <div class="entry-content">
                        <?php the_content( __( 'Weiterlesen <span class="meta-nav">&rarr;</span>', '_rrze' ) ); ?>
                        <?php _rrze_the_fields(); ?>
                        <?php wp_link_pages( array( 'before' => '<nav id="nav-pages"><div class="ym-wbox"><span>' . __( 'Seiten:', '_rrze' ) . '</span>', 'after' => '</div></nav>' ) ); ?>
                    </div>

                    <footer class="entry-meta">
                        <?php
                        $utility_text = '';
                        $menu_list = _rrze_menu_items_list( get_the_ID() );
                        $categories_list = get_the_category_list(', ');
                        $tag_list = get_the_tag_list('', ', ');                        

                        if( '' != $menu_list )
                            $utility_text .= sprintf( _n( 'Diese Seite wurde diesem Menü: %1$s zugeordnet.<br/>', 'Diese Seite wurde diesen Menüs: %1$s zugeordnet.<br/>', count( explode( ', ', $menu_list ) ), '_rrze' ), $menu_list );

                        $utility_text .= sprintf( __( 'Diese Seite <a href="%1$s" title="Permalink zu %2$s" rel="bookmark"> zu Ihren Lesezeichen hinzufügen</a>.<br/>', '_rrze' ), esc_url( get_permalink()), the_title_attribute( 'echo=0' ) );
                        
                        printf( '%s%s<br/>', $utility_text, _rrze_last_modified_on() );
                        ?>
                        <?php edit_post_link( __( 'Bearbeiten', '_rrze' ), '<span class="edit-link">', '</span>' ); ?>
                    </footer>

                </article>


            <?php endwhile; ?>

        </div><!-- #content -->
        
    </div><!-- #container -->

</div><!-- #main -->
