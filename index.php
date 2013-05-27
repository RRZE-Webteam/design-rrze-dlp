<?php get_header(); ?>
<?php 
global $nav_menu_selected_id, $nav_path, $nav_post_id; 
?>
<div id="main">

    <div id="container">
        
        <div id="content" role="main">
        <?php if( ! empty( $nav_post_id ) ) : 
            $post = get_post( $nav_post_id );

            if( $post->post_type == 'post' )
               query_posts( 'p=' . $post->ID );
            elseif( $post->post_type == 'page' )
               query_posts( 'page_id=' . $post->ID );

        ?>
            <?php while( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <header class="entry-header">
                        <h1><?php the_title(); ?></h1>
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

                        if( '' != $categories_list )
                            $utility_text .= sprintf( __( 'Kategorien: %1$s.<br/>', '_rrze' ), $categories_list );
                                                
                        if( '' != $tag_list )
                            $utility_text .= sprintf( __( 'Schlagwörter: %1$s.<br/>', '_rrze' ), $tag_list );
                        
                        $utility_text .= sprintf( __( 'Diesen Artikel <a href="%1$s" title="Permalink zu %2$s" rel="bookmark"> zu Ihren Lesezeichen hinzufügen</a>.<br/>', '_rrze' ), esc_url( get_permalink()), the_title_attribute( 'echo=0' ) );
                        
                        printf( '%s%s<br/>', $utility_text, _rrze_last_modified_on() );
                        ?>
                        <?php edit_post_link( __( 'Bearbeiten', '_rrze' ), '<span class="edit-link">', '</span>' ); ?>
                    </footer>

                </article>

            <?php endwhile; ?>
        <?php elseif( ! empty( $nav_menu_selected_id ) ) : ?>
            <div class="entry-content">
                <?php
                $menus = get_option( '_rrze_nav_menus' );
                $post_id = isset( $menus[$nav_menu_selected_id] ) ? $menus[$nav_menu_selected_id]['menu_description_id'] : 0;
                $post = get_post( $post_id );
                if( ! empty( $post ) && $post->post_type == 'page' ) :
                    query_posts( 'page_id=' . $post->ID );
                    
                ?>
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
                    
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </div><!-- #content -->
        
    </div><!-- #container -->

</div><!-- #main -->

<?php get_footer(); ?>
