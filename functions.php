<?php
/**
 * _rrze functions and definitions
 *
 * @package _rrze
 */

define('_RRZE_PHP_VERSION', '5.3' );

define('_RRZE_WP_VERSION', '3.4.2' );

define('_RRZE_THEME_OPTIONS_NAME', '_rrze_theme_options' );

add_action( 'after_setup_theme', '_rrze_setup' );

if ( ! function_exists( '_rrze_setup' ) ):
function _rrze_setup() {
    
	if ( version_compare( PHP_VERSION, _RRZE_PHP_VERSION, '<' ) ) {
		//add_action('admin_notices', '_rrze_php_version_error');
		$fail = true;
	}

	if ( version_compare( $GLOBALS['wp_version'], _RRZE_WP_VERSION, '<' ) ) {
		//add_action('admin_notices', '_rrze_wp_version_error');
		$fail = true;
	}
    
    load_theme_textdomain( '_rrze', get_template_directory() . '/languages' );
    
    require( get_template_directory() . '/inc/widgets.php' );
    
	add_theme_support( 'automatic-feed-links' );
        
    add_theme_support( 'post-thumbnails' );
    
      $args = array(
            'width'         => 154,
            'height'        => 60,
            'default-image' => get_template_directory_uri().'/rrze-logo-154x60.gif',
            'uploads'       => true,
            'random-default' => false,                      
            'flex-height' => true,
            'flex-width' => true,
	    'header-text'   => false,
            'max-width' => 350,           
        );
       add_theme_support( 'custom-header', $args );
    
    
}
endif;

function _rrze_php_version_error() {
	printf( '<div class="error fade"><p><b>%s</b></p></div>', sprintf( __('Ihre PHP-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die PHP-Version %s', '_rrze'), PHP_VERSION, _RRZE_PHP_VERSION ) );
}

function _rrze_wp_version_error() {
	printf( '<div class="error fade"><p><b>%s</b></p></div>', sprintf( __('Ihre Wordpress-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die Wordpress-Version %s', '_rrze'), $GLOBALS['wp_version'], _RRZE_WP_VERSION ) );
}

function _rrze_widgets_init() {
    unregister_widget( 'WP_Widget_Meta' );
    unregister_widget( 'WP_Widget_Tag_Cloud' );
    
    register_widget( 'RRZE_Widget_Meta' );
    register_widget( 'RRZE_Widget_Tag_Cloud' );
    
    register_sidebar( array(
        'name' => __( 'Zusatzinformationen', '_rrze' ),
        'id' => 'sidebar-footer',
        'description'   => __( 'Dieser Bereich ist f&uuml;r die Zusatzinformationen (im Footer) vorgesehen. Hier könnten hilfreiche Links oder sonstige Informationen stehen, welche auf jeder Seite eingeblendet werden sollen. Diese Angaben werden bei der Ausgabe auf dem Drucker nicht mit ausgegeben!', '_rrze' ),
        'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',      
    ));
    
}
add_action( 'widgets_init', '_rrze_widgets_init' );

function _rrze_scripts() {
    //wp_enqueue_style( 'style', get_stylesheet_uri() );
    
    wp_register_style( 'all-style', sprintf('%s/css/style.css', get_template_directory_uri() ), array(), false, 'all' );
    wp_enqueue_style( 'all-style' );

    wp_register_style( 'print-style', sprintf('%s/css/print/print.css', get_template_directory_uri() ), array(), false, 'print' );
    wp_enqueue_style( 'print-style' );

    wp_register_style( 'patch-style', sprintf('%s/css/iehacks.css', get_template_directory_uri() ) );
    $GLOBALS['wp_styles']->add_data( 'patch-style', 'conditional', 'lte IE 9' );
    wp_enqueue_style( 'patch-style' );
    
    wp_enqueue_script( 'jquery' );
    
    wp_register_script( 'all-script', sprintf( '%s/js/script.js', get_template_directory_uri() ), array(), false);
    wp_enqueue_script( 'all-script' );    
}
add_action( 'wp_enqueue_scripts', '_rrze_scripts' );

function _rrze_nav_menus_print_styles() {
    wp_register_style( 'nav-menus-style', sprintf('%s/css/admin/nav-menus.css', get_template_directory_uri() ), array(), false, 'all' );
    wp_enqueue_style( 'nav-menus-style' );
}
//add_action( 'admin_print_styles-appearance_nav_menus', '_rrze_nav_menus_print_styles', 11 );
add_action( 'admin_print_styles', '_rrze_nav_menus_print_styles', 11 );

function category_count_inline( $links ) {
    $links = str_replace('</a> (', ' (', $links );
    $links = str_replace(')', ')</a>', $links);
    return $links;
}
add_filter( 'wp_list_categories', 'category_count_inline' );

function archive_count_inline( $links ) {
    $links = str_replace( '</a>&nbsp;(', ' (', $links );
    $links = str_replace( ')', ')</a>', $links );
    return $links;
}
add_filter( 'get_archives_link', 'archive_count_inline' );

function _rrze_session_start() {
    if( ! session_id() ) {
        session_start();
    }
}
add_action( 'init', '_rrze_session_start', 1 );

function _rrze_session_destroy() {
    session_destroy ();
}
add_action( 'wp_logout', '_rrze_session_destroy' );
add_action( 'wp_login', '_rrze_session_destroy' );

/**
 * Breadcrumb
 */
function _rrze_breadcrumb_nav() {
    global $post;
    $list = sprintf( '<p><span>%s</span>', __( 'Sie befinden sich hier: ', '_rrze' ) );
    
        
    if ( ! is_front_page() ) {
        $list .= sprintf( '<span><a href="%s">%s</a><span> &raquo; </span></span>', get_bloginfo('url'), __('Startseite', '_rrze' ) );
        
        if ( is_404() ) {
            $list .= sprintf( '<span>%s</span>', __( 'Seite nicht gefunden', '_rrze' ) );
        
        } elseif ( is_category() ) {
            $list .= sprintf( '<span>%s %s</span>', __('Kategorie', '_rrze' ), single_cat_title( '', false) );
            
        } elseif ( is_tag() ) {
            $list .= sprintf( '<span>%s %s</span>', __('Tag', '_rrze' ), single_cat_title( '', false) );

        } elseif ( is_archive() ) {
            $list .= sprintf( '<span>%s %s</span>', __( 'Archive', '_rrze' ), single_cat_title( '', false) );

        } elseif ( is_author() ) {
            $list .= sprintf( '<span>%s %s</span>', __( 'Autor', '_rrze' ), single_cat_title( '', false) );
            
        } elseif ( is_single() ) {
            if ( get_option( 'page_for_posts') )
                $list .= sprintf( '<span><a href="%s">%s</a><span> &raquo; </span></span>', get_permalink( get_option( 'page_for_posts' ) ), get_the_title( get_option( 'page_for_posts' ) ) );
                        
            $list .= sprintf( '<span>%s</span>', get_the_title( $post->ID) );
            
        } elseif ( ( is_home() || is_date () ) && get_option( 'page_for_posts' ) ) {            
            $list .= sprintf( '<span>%s</span>', get_the_title(get_option( 'page_for_posts') ) );
            
        } elseif ( is_page() ) {
            if ( $post->post_parent ) {
                $home = get_page( $post->ID );
                for ( $i = count( $post->ancestors) - 1; $i >= 0; $i-- ) {
                    if ( $home->ID != $post->ancestors[$i] ) {
                        $list .= sprintf('<span><a href="%s">%s</a><span> &raquo; </span></span>', get_permalink( $post->ancestors[$i] ), get_the_title( $post->ancestors[$i] ) );
                    }
                }
            }
            $list .= sprintf('<span>%s</span>', get_the_title($post->ID));
            
        } elseif ( is_search() ) {
            $list .= sprintf( '<span>%s</span>', sprintf( __( 'Suchergebnisse für: %s', '_rrze' ), '<span>' . get_search_query() . '</span>') );
        }
    } else {
        $list .= sprintf( '<span>%s</span>', __( 'Startseite', '_rrze' ) );
    }
    
    $list .= '</p>';

    return $list;
}

/**
 * Breadcrumb index
 */
function _rrze_index_breadcrumb_nav() {
    global $wp_query, $nav_menu_selected_id, $nav_menu_selected_slug, $nav_post_id;

    $menu = wp_get_nav_menu_object( $nav_menu_selected_id );

    $list = sprintf( '<p><span>%s</span>', __( 'Sie befinden sich hier: ', '_rrze' ) );

    if( isset( $wp_query->query_vars['menue'] ) ) {
        $list .= sprintf( '<span><a href="%s">%s</a><span> &raquo; </span></span>', get_bloginfo('url'), __('Startseite', '_rrze' ) );
        
        if( ! empty( $wp_query->query_vars['submenue'] ) ) {
            $post = get_post( $nav_post_id );
            $menu_parents_items = _rrze_menu_parent_items( $nav_menu_selected_id, $nav_post_id );
            $path = array();
            $path[] = $nav_menu_selected_slug;

            $list .= sprintf('<span><a href="%s">%s</a><span> &raquo; </span></span>', home_url( $menu->slug ), $menu->name );

            foreach( $menu_parents_items as $_item ) {
                $path[] = $_item->ID;

                $list .= sprintf('<span><a href="%s">%s</a><span> &raquo; </span></span>', home_url( implode( '/', $path ) ), $_item->title );
            }

            $list .= sprintf( '<span>%s</span>', $post->post_title );
        } else {
            $list .= sprintf( '<span>%s</span>', $menu->name );
        }
    } else {
        $list .= sprintf( '<span>%s</span>', __( 'Startseite', '_rrze' ) );
    }

    $list .= '</p>';

    return $list;
}

function _rrze_pages_nav() {
    global $wp_query;

    if ( $wp_query->max_num_pages > 1 ) :
        ?>

        <nav id="nav-pages">
            <h3 class="ym-skip"><?php _e( 'Suchergebnissenavigation', '_rrze' ); ?></h3>
            <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Vorherige', '_rrze' ) ); ?></div>
            <div class="nav-next"><?php previous_posts_link( __( 'Nächste <span class="meta-nav">&rarr;</span>', '_rrze' ) ); ?></div>
        </nav>

    <?php
    endif;
}

/**
 * Posted on
 */
function _rrze_posted_on() {
    return sprintf(__( 'Veröffentlicht am </span><time class="entry-date" datetime="%1$s" pubdate>%2$s</time></a>', '_rrze' ), 
            esc_attr( get_the_date('c') ), 
            esc_html( get_the_date() )
    );
}

/**
 * Last modified on
 */
function _rrze_last_modified_on() {
    return sprintf(__( 'Zuletzt bearbeitet am </span><time class="entry-date" datetime="%1$s" editdate>%2$s</time></a>', '_rrze' ), 
            esc_attr( get_the_modified_date('c') ), 
            esc_html( get_the_modified_date() )
    );
}

/**
 * Text trim by words
 */
function _rrze_trim_string( $text, $max_words = 55 ) { 
    $text = str_replace(']]>', ']]&gt;', $text);
    $text = strip_tags( $text );
    $words = explode( ' ', $text, $max_words + 1 );
    if ( count( $words ) > $max_words ) {
        array_pop( $words );
        array_push( $words, '[...]' );
        $text = implode( ' ', $words );
    }
    return $text;
}

/**
 * Menu items filter
 */
function _rrze_nav_menu_filter( $path = array(), $items = array() ) {

    $menu_items = array();
    
    $level = (int) end( $path );
    
    foreach ( $items as $_item ) {
        if ( $_item->menu_item_parent == $level )
            $menu_items[] = $_item;
    }

    return $menu_items;
}

/**
 * Menu items list
 */
function _rrze_menu_items_list( $post_id = 0 ) {

    $items_list = array();
    $nav_menus = wp_get_nav_menus( array( 'orderby' => 'ID' ) );
    
    foreach( (array) $nav_menus as $_nav_menu ) {
        
        $items = wp_get_nav_menu_items( $_nav_menu->term_id );
        
        foreach( $items as $_item ) {
            
            if ( $_item->object_id == $post_id ) {
                
                $title = $_nav_menu->name;
                $path = array();
                
                $menu_parents_items = _rrze_find_parents_path( $items, $post_id );
                
                foreach( $menu_parents_items as $_parents_item ) {
                    $title = $_parents_item->title;
                    $path[] = $_parents_item->ID;
                }
                
                $path = implode( '/', $path );
                $url = home_url( sprintf( '%s/%s', $_nav_menu->slug, $path ) );
                $items_list[] = sprintf( '<a href="%1$s" rel="menu item" title="%2$s">%3$s</a>', esc_url( $url ), esc_attr( $title ), $title );
            }
        }
        
    }
    
    return implode( ', ', $items_list );
    
}

/**
 * Find menu parents items recursively
 */
function _rrze_find_parents_path( $items, $value = 0, $key = 'object_id' ) {
    $parents = array();
    foreach ( $items as $_item ) {
        if ( $_item->$key == $value ) {
            $parents = _rrze_find_parents( $items, $_item->menu_item_parent, 'ID' );
            if( $key != 'object_id' )
                $parents[] = $_item;
        } elseif( empty( $value ) ) {
            $parents[] = $_item;
        }
    }
    return $parents;
}

/**
 * Get menu parents items by post id
 */
function _rrze_menu_parent_items( $term_id = 0, $post_id = 0 ) {
    $menu_parent_items = array();
    $nav_menus = wp_get_nav_menus();
    $passed = false;
    
    foreach( (array) $nav_menus as $_nav_menu ) {
        if( $_nav_menu->term_id == (int) $term_id ) {
            $passed = true;
            break;
        }
    }    
    
    if( $passed ) {
        $items = wp_get_nav_menu_items( $term_id );
        $menu_parent_items = _rrze_find_parents( $items, $post_id );
    }
    
    return $menu_parent_items;
}

/**
 * Get post id from menu path
 */
function _rrze_path_post_id( $path = array(), $items = array() ) {

    $post_id = 0;
    
    $menu_items = _rrze_path_menu_items( $path, $items );
    $_menu_item = array_shift( $menu_items );
    
    if( isset( $_menu_item->object_id ) )
        $post_id = $_menu_item->object_id;
    
    return $post_id;
}

/**
 * Get menu path recursively
 */
function _rrze_path_menu_items( $path, $items, $value = 0 ) {
    $menu_items = array();
    
    foreach ( $items as $_item ) {
        if ( $_item->ID == current( $path ) && $_item->menu_item_parent == $value ) {
            next( $path );
            $menu_items = _rrze_path_menu_items( $path, $items, $_item->ID );
            $menu_items[] = $_item;
        }
    }
    
    return $menu_items;
}

/**
 * Find menu parents items recursively
 */
function _rrze_find_parents( $items, $value = 0, $key = 'object_id' ) {
    $parents = array();
    foreach ( $items as $_item ) {
        if ( $_item->$key == $value ) {
            $parents = _rrze_find_parents( $items, $_item->menu_item_parent, 'ID' );
            if( $key != 'object_id' )
                $parents[] = $_item;
        }
    }
    return $parents;
}

/**
 * Find menu parents items recursively
 */
function _rrze_find_items( $items, $value, $key = 'object_id' ) {
    $parents = array();
    foreach ( $items as $_item ) {
        if ( $_item->$key == $value ) {
            $parents = _rrze_find_parents( $items, $_item->menu_item_parent, 'ID' );
            if( $key != 'object_id' )
                $parents[] = $_item;
        }
    }
    return $parents;
}

function _rrze_get_menu_object_id( $path ) {
    
    $path_array = explode( '/', $path );
    $nav_menus = wp_get_nav_menus();
    $passed = false;
    
    foreach( (array) $nav_menus as $_nav_menu ) {
        if( $_nav_menu->slug == (int) $path_array[0] ) {
            $passed = true;
            break;
        }
    }
    
}

/**
function _rrze_menu_item_filter( $menu_item ) {
    if( $menu_item->object == 'post' ) {
        $menu_item->post_name = sanitize_title( $menu_item->title );
    }
    
    return $menu_item;
}

add_filter( 'wp_setup_nav_menu_item', '_rrze_menu_item_filter' );
 * 
 */

/**
 * Remove metaboxes from nav menus screen
 */
function _rrze_remove_nav_menus_metaboxes( $columns ) {
    remove_meta_box('nav-menu-theme-locations', 'nav-menus', 'side');
    remove_meta_box('add-custom-links', 'nav-menus', 'side');
    remove_meta_box('add-page', 'nav-menus', 'side');
    remove_meta_box('add-category', 'nav-menus', 'side');
    remove_meta_box('add-attachment_category', 'nav-menus', 'side');
    remove_meta_box('add-post_tag', 'nav-menus', 'side');
    remove_meta_box('add-attachment_tag', 'nav-menus', 'side');
    
    return $columns;
}
add_action( 'manage_nav-menus_columns', '_rrze_remove_nav_menus_metaboxes' );

/**
 * Flush rules when update a menu
 */
add_action('wp_update_nav_menu', '_rrze_update_nav_menu');

function _rrze_update_nav_menu( $menu_id ) {
    
    flush_rewrite_rules();
    
    return $menu_id;
}

/**
 * Flush rules when delete a menu
 */
add_action('wp_delete_nav_menu', '_rrze_delete_nav_menu');

function _rrze_delete_nav_menu( $menu_id ) {
    
    flush_rewrite_rules();
}

/**
 * Add custom query vars
 */
add_filter( 'query_vars', '_rrze_add_query_vars' );

function _rrze_add_query_vars( $vars ) {
    $vars[] = 'menue';
    $vars[] = 'submenue';
    
    return $vars;
}

/**
 * Add rewrite rules
 */
add_filter( 'rewrite_rules_array', '_rrze_add_rewrite_rules' );

function _rrze_add_rewrite_rules( $rules ) {
    $new_rules = array();
    $nav_menus = wp_get_nav_menus( array('orderby' => 'name') );
    foreach( (array) $nav_menus as $_nav_menu ) {
        $new_rules += array( sprintf( '%s/?(([0-9]+/?)+)?$', $_nav_menu->slug ) => sprintf( 'index.php?menue=%s&submenue=$matches[1]', $_nav_menu->slug) );
    }
    $rules = $new_rules + $rules;
    return $rules;
}

/**
 * Render custom fields
 */
function _rrze_the_fields() {
	global $post, $wpdb;
	
	$display_field = array(
	    'service'			    => 1,
	    'beschreibung'		    => 1,
	    'umfang'			    => 1,
	    'links_zur_dokumentation'	    => 1,
	    'basisdienstleistungen'	    => 0,
	    'preis_basisdienstleistungen'   => 0,
	    'leistungserweiterungen'	    => 0,
	    'preis_leistungserweiterungen'  => 0,
	    'kontakt'			    => 1,
	    'abhaengigkeiten'		    => 0,
	);
/*
 *	Absprache vom 2.10.2013:
  	    Folgende Abschnitte werden NICHT allgemein angezeigt:
	    - Basisdienstleistungen
	    -   Preis Basisdienstleistungen
	    - Leistungserweiterungen
	    - Preis Leistungserweiterungen
	    - Abhängigkeiten
 */

		
    if ( ! function_exists( 'get_field_object' ) )
        return '';
    
    $post_id = isset( $post->ID ) ? $post->ID : 0;


     $allkeys = $wpdb->get_col($wpdb->prepare(     
        "SELECT meta_key FROM $wpdb->postmeta WHERE post_id = %d and meta_key NOT LIKE %s",
        $post_id,
        '\_%'
    )); 	

    
	if( ! $allkeys )
		return '';
	
    $str = '';   
    foreach( $display_field as $_key => $value ) {
        $_field = get_field_object( $_key );

	if (($value==1) ||
	    ( is_user_logged_in() && ($value==0))) {
	    if( ! empty( $_field[ 'value' ] ) ) {
		$str .= sprintf( '<p><b>%s</b></p>', $_field[ 'label' ] );
		$str .= sprintf( '<p>%s</p>', $_field[ 'value' ] );
	    }
	}
    }
     foreach( $allkeys as $_key  ) {
	if (!isset($display_field[$_key])) {    
	    $_field = get_field_object( $_key );
	    if( ! empty( $_field[ 'value' ] ) ) {
		$str .= sprintf( '<p><b>%s</b></p>', $_field[ 'label' ] );
		$str .= sprintf( '<p>%s</p>', $_field[ 'value' ] );
	    }
	
	}
    }
    echo $str;
}

function _rrze_wp_tag_cloud( $args = '' ) {
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
		'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
		'exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'echo' => true
	);
	$args = wp_parse_args( $args, $defaults );
    
    $siblings = _rrze_menu_siblings_object_ids();

    $tags = array();
    
	foreach( $siblings as $_post_id ) { 
        
        $terms = get_the_terms( $_post_id, $args['taxonomy'] );
        if( empty( $terms ) )
            continue;
        
        foreach( $terms as $_term ) {
            $tags[] = $_term;
        }
	}
    
	foreach ( $tags as $key => $tag ) { 
		$link = get_term_link( intval($tag->term_id), $tag->taxonomy );

		$tags[ $key ]->link = $link;
		$tags[ $key ]->id = $tag->term_id;
	}

	echo wp_generate_tag_cloud( $tags, $args );
}

/**
 * Menu siblings object_ids
 */
function _rrze_menu_siblings_object_ids() {
    global $wp_query, $nav_menu_selected_id, $nav_path, $nav_post_id;
    
    $siblings = array( $nav_post_id );
    
    if( empty( $nav_menu_selected_id ) )
        return $siblings;
    
    
    $items = wp_get_nav_menu_items( $nav_menu_selected_id );
    $items = _rrze_nav_menu_filter( $nav_path, $items );
    
    foreach( (array) $items as $key => $_item ) {
        $siblings[] = $_item->object_id;
     }
    
    return $siblings;
}

add_filter( 'manage_posts_columns', '_rrze_set_featured_image_column', 5 );
add_filter( 'manage_pages_columns', '_rrze_set_featured_image_column', 5 );

function _rrze_set_featured_image_column( $columns ) {
  $columns['_rrze_featured_image'] = __( 'Artikelbild', '_rrze' );
  return $columns;
}

add_action( 'manage_posts_custom_column', '_rrze_display_featured_image_column', 5 );
add_action( 'manage_pages_custom_column', '_rrze_display_featured_image_column', 5 );

function _rrze_display_featured_image_column( $column ) {
  switch( $column ) {
    case '_rrze_featured_image':
      if( function_exists( 'the_post_thumbnail' ) )
        echo the_post_thumbnail( array( 64, 64 ) );
      else
        _e( 'Nicht im Thema unterstützt', '_rrze' );
      break;
  }
}

/**
 * Menu metaboxes
 */
add_action( 'admin_init', array( 'RRZE_Nav_Menus', 'init' ) );

class RRZE_Nav_Menus {
    const option_name = '_rrze_nav_menus';
    const textdomain = '_rrze';
        
    public static function init() {
        global $pagenow;
        if ( 'nav-menus.php' !== $pagenow )
            return;
        
        self::add_meta_box();
    }
    
    public static function wp_ajax_menu_description_save() {
        if ( ! current_user_can( 'edit_theme_options' ) )
            wp_die( -1 );
        check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );
        if ( ! isset( $_POST['menu-description'] ) )
            wp_die( 0 );
        //set_theme_mod( 'nav_menu_description', array_map( 'absint', $_POST['menu-description'] ) );
        wp_die( 1 );
    }
    
    public static function initial_nav_menu_meta_boxes() {
        $user = wp_get_current_user();
        update_user_option( $user->ID, 'metaboxhidden_nav-menus', array(), true );
    }
    
    public static function add_meta_box(){
        add_meta_box(
            'nav-menu-theme-description'
            ,__( 'Einstellungen', self::textdomain )
            ,array( __CLASS__, 'render_meta_box_content' )
            ,'nav-menus'
            ,'side'
            ,'high'
        );
    }

    public static function render_meta_box_content() {
        global $pagenow, $nav_menu_selected_id;
        
        self::initial_nav_menu_meta_boxes();
        
        $action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

        $menus = get_option( self::option_name );
        $menu_description_id = isset( $menus[$nav_menu_selected_id] ) ? $menus[$nav_menu_selected_id]['menu_description_id'] : 0;
        
        $term = get_term( $nav_menu_selected_id, 'nav_menu' );
        $menu_term_group = $term->term_group;
        
        if( $action == 'add-menu-item' ) {
            check_admin_referer( 'add-menu_item', 'menu-settings-column-nonce' );
            if ( isset( $_REQUEST['menu-description-id'] ) ) {

                $menu_description_id = (int) $_REQUEST['menu-description-id'];
                $menus[$nav_menu_selected_id]['menu_description_id'] = $menu_description_id;

                update_option( self::option_name, $menus );
            } 
            
            if ( isset( $_REQUEST['menu-term-group'] ) ) {

                $menu_term_group = (int) $_REQUEST['menu-term-group'];
                
                wp_update_term( $nav_menu_selected_id, 'nav_menu', array('term_group' => $menu_term_group));
            }      
            
        }

        // Register Ajax call
        if ( ! empty( $_GET['action'] ) && $_GET['action'] == 'menu-description-save' )
            add_action( 'wp_ajax_' . $_GET['action'], 'wp_ajax_' . str_replace( '-', '_', $_GET['action'] ), 1 );        

        ?>
        <p>
            <label for="nav-menu-theme-description">
                <p><?php _e( 'Beschreibung', self::textdomain ); ?></p>
                <?php echo wp_dropdown_pages( array( 'name' => 'menu-description-id', 'echo' => 0, 'show_option_none' => __( '&mdash; Auswählen &mdash;', '_rrze' ), 'option_none_value' => '0', 'selected' => $menu_description_id ) ); ?>
                <p class="howto"><?php _e( 'Wählen Sie eine (statische) Seite aus, die als Beschreibung des Menüs angezeigt werden soll.', self::textdomain ); ?></p>
            </label>
        </p>
        
        <p>
            <label for="nav-menu-theme-term-group">
				<p><?php _e( 'Anordnung', self::textdomain ); ?></p>
				<input id="custom-menu-item-url" name="menu-term-group" type="text" class="code menu-item-textbox" value="<?php echo $menu_term_group; ?>">
                <p class="howto"><?php _e( 'Bitte, geben Sie eine ganze Zahl ein um das Menü anzuordnen.', self::textdomain ); ?></p>			
            </label>
        </p>
        <p class="button-controls">
            <?php submit_button( __( 'Speichern', '_rrze' ), 'primary', 'nav-menu-description', false, disabled( $nav_menu_selected_id, 0, false ) ); ?>
            <span class="spinner"></span>
        </p>
        <?php
    }
    
}
