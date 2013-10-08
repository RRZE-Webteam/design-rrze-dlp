<?php
global $wp_query, $post, $pagenow, $nav_path, $nav_post_id, $nav_menu_name, $nav_menu_selected_id, $nav_menu_selected_slug;

$nav_path = array();
$nav_post_id = 0;

$nav_menu_selected_id = 0;
$nav_menu_selected_slug = '';

$nav_menus = wp_get_nav_menus( array('orderby' => 'term_group') );
if( ! empty( $nav_menus ) ) {
    $_nav_menu = current( $nav_menus );
    
    $nav_menu_selected_id = $_nav_menu->term_id;
    $nav_menu_selected_slug = $_nav_menu->slug;
}

if ( isset( $_REQUEST['menu'] ) ) {
    foreach( (array) $nav_menus as $_nav_menu ) {
        if( $_nav_menu->term_id == (int) $_REQUEST['menu'] ) {
            $nav_menu_selected_id = $_nav_menu->term_id;
            $nav_menu_selected_slug = $_nav_menu->slug;
            break;
        }
    }
    
    $_SESSION['nav_menu_selected_id'] = $nav_menu_selected_id;
    
    wp_redirect( home_url( sprintf( '%s', $nav_menu_selected_slug ) ) );
    exit;
}

if( isset( $wp_query->query_vars['menue'] ) ) {
    foreach( (array) $nav_menus as $_nav_menu ) {
        if( $wp_query->query_vars['menue'] == $_nav_menu->slug ) {

            $nav_menu_name = $_nav_menu->name;
            
            $nav_menu_selected_id = $_nav_menu->term_id;
            $nav_menu_selected_slug = $_nav_menu->slug;

            $_SESSION['nav_menu_selected_id'] = $nav_menu_selected_id;

            $items = wp_get_nav_menu_items( $nav_menu_selected_id );
            
            if( ! empty( $wp_query->query_vars['submenue'] ) ) {
                $nav_path = trim( urldecode( $wp_query->query_vars['submenue'] ) );
                $nav_path = explode( '/', $nav_path );
                $nav_post_id = _rrze_path_post_id( $nav_path, $items );
            }
            
            break;
        }
    }
}

if( isset( $wp_query->query_vars['menue'] ) && isset( $_SESSION['nav_menu_selected_id'] ) )
    $nav_menu_selected_id = (int) $_SESSION['nav_menu_selected_id'];
else
    $_SESSION['nav_menu_selected_id'] = $nav_menu_selected_id;

foreach( (array) $nav_menus as $key => $_nav_menu ) {
    $_nav_menu->truncated_name = trim( wp_html_excerpt( $_nav_menu->name, 40 ) );
    if ( $_nav_menu->truncated_name != $_nav_menu->name )
        $_nav_menu->truncated_name .= '&hellip;';

    $nav_menus[$key]->truncated_name = $_nav_menu->truncated_name;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <title><?php bloginfo( 'name' ); ?><?php wp_title( '|' ); ?></title>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />	
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
        <link rel="icon" href="<?php printf( '%s/images/favicon.ico', get_stylesheet_directory_uri() ); ?>" type="image/x-icon" />
        <link rel="shortcut icon" href="<?php printf( '%s/images/favicon.ico', get_stylesheet_directory_uri() ); ?>" type="image/x-icon" />
        <?php wp_head(); ?>        
    </head>
    <body <?php body_class(); ?>>
        
         <div id="wrapper" >     
	    <header>		                                                            
                             <h1><img src="<?php header_image(); ?>" alt=""> <span><?php bloginfo( 'name' ); ?></span></h1>
                            <?php                                                     
                             if (strlen(trim(get_bloginfo( 'description' )))>1) { ?> 
                            <p class="description slogan"><?php bloginfo( 'description' ); ?></p>
                            <?php } ?>         
	    </header>
                                
                <div id="breadcrumb">
                    <?php
                    if( is_front_page() || isset( $wp_query->query_vars['menue'] ) ) :
                        echo _rrze_index_breadcrumb_nav();
                    else:
                        echo _rrze_breadcrumb_nav();
                    endif; ?>
                </div>
                
                <div id="options">
                    <div id="auswahl">
                        <form id="select-nav-menu" method="post" action="">
                            <select  id="menu-auswahl" name="menu">
                                <?php if( isset( $wp_query->query_vars['menue'] ) ) : ?>
                                <?php foreach( (array) $nav_menus as $_nav_menu ) : ?>
                                    <option value="<?php echo esc_attr($_nav_menu->term_id) ?>" <?php selected( $nav_menu_selected_id, $_nav_menu->term_id ); ?>>
                                        <?php echo esc_html( $_nav_menu->truncated_name ); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="0" selected="selected"><?php esc_html_e( 'Bitte Menü auswählen', '_rrze' ); ?></option>
                                    <?php foreach( (array) $nav_menus as $_nav_menu ) : ?>
                                        <option value="<?php echo esc_attr($_nav_menu->term_id) ?>">
                                            <?php echo esc_html( $_nav_menu->truncated_name ); ?>
                                        </option>
                                    <?php endforeach; ?>                            
                                <?php endif; ?>
                            </select>
                            <input type="submit" id="auswahlsubmit" class="hide-if-js" name="submit" value="<?php esc_html_e( 'Auswählen', '_rrze' ); ?>">
                        </form>
                        <script type="text/javascript">
                        /* <![CDATA[ */
                        jQuery(document).ready(function($) {
                            $('#menu-auswahl').change(function() {
                                window.location = '<?php echo home_url(); ?>/?menu=' + $(this).val();
                            });
                        });
                        /* ]]> */
                        </script> 
                    </div>
                    <div id="suche">
                        <h2><a name="suche" class="skip-link"><?php esc_html_e( 'Suche', '_rrze' ); ?></a></h2>
                        <form role="search" method="get" id="searchform" action="<?php echo esc_url( home_url() ); ?>" >
                            <div><label class="screen-reader-text skip-link" for="s"><?php esc_html_e( 'Suche nach:', '_rrze' ); ?></label>
                                <input type="text" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_attr__( '', '_rrze' ); ?>" name="s" id="s" />
                                <input type="submit" id="searchsubmit" value="<?php esc_html_e( 'Suchen', '_rrze' ); ?>" />
                            </div>
                        </form>          
                    </div>   
                </div>

                <?php
                if( ( is_front_page() || isset( $wp_query->query_vars['menue'] ) ) && ! is_search() ) :
                $path = implode( '/', $nav_path );
                if( ! empty( $path) )
                    $path = sprintf( '/%s', $path );
                
                $menu = wp_get_nav_menu_object( $nav_menu_selected_id );

                $menu_items = wp_get_nav_menu_items( $nav_menu_selected_id );
                $menu_items = _rrze_nav_menu_filter( $nav_path, $menu_items );
                if( ! empty( $menu_items ) ) :
                ?>
                <div class="nav-button hide-if-no-js">&NestedLessLess; Menü zuklappen &NestedGreaterGreater;</div>
                
                <div id="hauptmenu" role="navigation">

                    <div class="skip-link"><a href="#content" title="Skip to content">Skip to content</a></div>

                     <div class="menu-header">
                        <ul id="menu-<?php echo $menu->slug; ?>" class="menu">

                            <li class="menu-item menu-item-type-post_type menu-item-object-post current-menu-ancestor">
                                <ul class="sub-menu">
                                    <?php foreach( (array) $menu_items as $key => $menu_item ): ?>
                                    <?php $post_item = get_post( $menu_item->object_id ); ?>
                                    <li class="menu-item menu-item-type-post_type menu-item-object-post current-menu-ancestor current-menu-parent">
                                        <a href="<?php echo home_url( sprintf( '%s%s/%s', $menu->slug, $path, $menu_item->ID ) ); ?>">
                                            <span class="title"><?php echo $menu_item->title; ?></span>
                                            <?php if( has_post_thumbnail( $post_item->ID ) ) : ?>
                                                <?php echo get_the_post_thumbnail( $post_item->ID ); ?>
                                            <?php endif; ?>
                                            <p><?php echo _rrze_trim_string( $post_item->post_excerpt, 25 ); ?></p>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>

                        </ul>
                    </div>

                </div><!-- #hauptmenu -->
                <?php endif; ?>
                <?php endif; ?>
                
