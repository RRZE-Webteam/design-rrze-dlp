<?php
/**
 * RRZE-DLP functions and definitions
 *
 * @package RRZE-DLP
 * @since RRZE-DLP 2.0
 */


require( get_template_directory() . '/inc/constants.php' );
$options = rrze_dlp_initoptions();
    // adjusts variables for downwards comptability

require_once ( get_stylesheet_directory() . '/inc/theme-options.php' );
require( get_template_directory() . '/inc/custom-fields.php' );

if ( ! isset( $content_width ) )
    $content_width = 1170; /* pixels */

if ( ! function_exists( 'rrze_dlp_setup' ) ):
function rrze_dlp_setup() {

    load_theme_textdomain( 'rrze-dlp', get_template_directory() . '/languages' );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'rrze-dlp' ),
    ) );
}
endif;

add_action( 'after_setup_theme', 'rrze_dlp_setup' );
add_theme_support( 'post-thumbnails' );





function rrze_dlp_initoptions() {
    global $defaultoptions;
    global $default_contactlist;
    // $doupdate = 0;
    
    $oldoptions = get_option('rrze_dlp_theme_options');
    if (isset($oldoptions) && (is_array($oldoptions))) {
        $newoptions = array_merge($defaultoptions,$oldoptions);	  
    } else {
        $newoptions = $defaultoptions;
	$newoptions['contactlist'] = $default_contactlist;
    }    

    return $newoptions;
}


/**
 * Enqueue scripts and styles
 */
function rrze_dlp_scripts() {
    global $options;
    
    wp_enqueue_style( 'style', get_stylesheet_uri() );
    wp_enqueue_script( 'navigation', get_template_directory_uri() . '/js/navigation.js', array(), $options['jsversion'], true );
    wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array(), false);

    if ( is_singular() && wp_attachment_is_image() ) {
        wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ),$options['jsversion'] );
    }
}
add_action( 'wp_enqueue_scripts', 'rrze_dlp_scripts' );

/**
 * Changing excerpt length

function new_excerpt_length($length) {
return 55;
}
add_filter('excerpt_length', 'new_excerpt_length');
 */

/**
 * Changing excerpt more
 */
function new_excerpt_more($more) {
return ' [<a href="' . get_permalink() . '" title="' . esc_attr( sprintf( __( 'Go to "%s"', 'rrze-dlp' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark">...</a>]';
}
add_filter('excerpt_more', 'new_excerpt_more');


/*
 * Breadcrumbs
 */

class SH_BreadCrumbWalker extends Walker{
    var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
    var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );
    var $delimiter = ' &raquo; ';


    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) { 

        //Check if menu item is an ancestor of the current page
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $current_identifiers = array( 'current-menu-item', 'current-menu-parent', 'current-menu-ancestor' );
        $ancestor_of_current = array_intersect( $current_identifiers, $classes );


        if( $ancestor_of_current ){
            $title = apply_filters( 'the_title', $item->title, $item->ID );

            //Preceed the first item with 'home'.
			//if( 0 == $depth )
            //    $output = '<a href="' . home_url( '/' ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home">' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '</a>' . $this->delimiter;

			//Preceed with delimter for all but the first item.
            if( 0 != $depth )
                $output .= $this->delimiter; 

            //Link tag attributes
            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
            $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
            $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
            $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

            //Add to the HTML output
            $output .=  ' <a'. $attributes .'>'.$title.'</a>';
        }
    }
}


function dlp_breadcrumbs() {
	if (!is_front_page()) {
		echo '<div id="breadcrumbs" class="menu">'
			. '<a href="' . home_url( '/' ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home">' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '</a> &raquo; ';
		if (is_tag()) {
			echo '<span>'. single_tag_title( '', false ) . '</span>';
		}
		elseif (is_category()) {
			echo '<span>'. single_cat_title( '', false ) . '</span>';
		}
		elseif (is_day()) {
			echo __( 'Daily Archives: %s', 'rrze-dlp' ), '<span>' . get_the_date() . '</span>';
		}
		elseif (is_month()) {
			echo __( 'Monthly Archives: %s', 'rrze-dlp' ), '<span>' . get_the_date( 'F Y' ) . '</span>';
		}
		elseif (is_year()) {
			echo __( 'Yearly Archives: %s', 'rrze-dlp' ), '<span>' . get_the_date( 'Y' ) . '</span>';
		}
		elseif (is_author()) {
			/* Queue the first post, that way we know
			* what author we're dealing with (if that is the case).
		   */
		   the_post();
		   echo __( 'Author Archives: %s', 'rrze-dlp' ), '<span>' . get_the_author() . '</span>';
		   /* Since we called the_post() above, we need to
			* rewind the loop back to the beginning that way
			* we can run the loop properly, in full.
			*/
		   rewind_posts();}
		elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
			echo '<span>' . __( 'Blog Archives', 'rrze-dlp' ) . '</span>';
		}
		elseif (is_search()) {
			echo '<span>' . __( 'Search Results', 'rrze-dlp' ). '<span>';
		}
		else {
			echo wp_nav_menu( array(
				'container' => 'none',
				'theme_location' => 'primary',
				'walker'=> new SH_BreadCrumbWalker,
				'items_wrap' => '<span>%3$s</span>'
				//'items_wrap' => '<div id="breadcrumbs" class="%2$s">%3$s</div>'
			) );
		}
		echo '</div>';
	}
}


/*
 * DLP-Navigation
 */

function dlp_contextnav_post() {
	$menu_name = 'primary';
	$locations = get_nav_menu_locations();
	$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
	$menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
	$post_ID = get_the_ID();
	$menustr = '';

	foreach ( $menuitems as $item ):
		$id = get_post_meta( $item->ID, '_menu_item_object_id', true );
		$page = get_page( $id );
		$link = get_page_link( $id );
		$custom_meta = get_post_meta( $id, 'service', true );
		$custom_meta_alt = get_post_meta( $id, 'beschreibung', true );

		if ($post_ID == $item->object_id) { $menu_parent = $item->ID;}
		if (isset($menu_parent) && $item->menu_item_parent == $menu_parent) {
			$menustr .= '<section class="preview"><header class="entry-header"><h1 class="entry-title"><a href="' .  $link . '" class="title">';
			$menustr .= $page->post_title;
			$menustr .= '</a></h1></header>';
			$menustr .= '<a href="' .  $link . '" class="thumbnail-link">';
			$menustr .= get_the_post_thumbnail($id);
			$menustr .= '</a>';
			// check if the custom field has a value
			if ( ! empty( $custom_meta ) ) {
				$menustr .= '<div class="entry-summary"><p>' . wp_trim_words($custom_meta, 40, '&hellip;') . '<br /> <a href="' .  $link . '" class="readmore">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'rrze-dlp' ) . '</a></p></div>';
			} elseif ( ! empty( $custom_meta_alt )) {
				$menustr .= '<div class="entry-summary"><p>' . wp_trim_words($custom_meta_alt, 40, '&hellip;') . '<br /> <a href="' .  $link . '" class="readmore">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'rrze-dlp' ) . '</a></p></div>';
			} else {
				$menustr .= the_excerpt();
			}
			$menustr .= '</section>';
		}
	endforeach;
	if ('' != $menustr) {
		printf('<div id="context-nav" role="navigation">%s</div>', $menustr);
	}

}

function dlp_contextnav_front() {
	$menu_name = 'primary';
	$locations = get_nav_menu_locations();
	$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
	$menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
	$post_ID = get_the_ID();
	$menustr = '';

	foreach ( $menuitems as $item ):
		$id = get_post_meta( $item->ID, '_menu_item_object_id', true );
		$page = get_page( $id );
		$link = get_page_link( $id );
		$custom_meta = get_post_meta( $id, 'service', true );
		$custom_meta_alt = get_post_meta( $id, 'beschreibung', true );

		if ( $item->menu_item_parent == 0 ) :
			$menustr .= '<section class="preview"><header class="entry-header"><h1 class="entry-title"><a href="' .  $link . '" class="title">';
			$menustr .= $page->post_title;
			$menustr .= '</a></h1></header>';
			$menustr .= '<a href="' .  $link . '" class="thumbnail-link">';
			$menustr .= get_the_post_thumbnail($id);
			$menustr .= '</a>';
			// check if the custom field has a value
			if ( ! empty( $custom_meta ) ) {
				$menustr .= '<div class="entry-summary"><p>' . wp_trim_words($custom_meta, 40, '&hellip;') . '<br /> <a href="' .  $link . '" class="readmore">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'rrze-dlp' ) . '</a></p></div>';
			} elseif ( ! empty( $custom_meta_alt )) {
				$menustr .= '<div class="entry-summary"><p>' . wp_trim_words($custom_meta_alt, 40, '&hellip;') . '<br /> <a href="' .  $link . '" class="readmore">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'rrze-dlp' ) . '</a></p></div>';
			} else {
				$menustr .= the_excerpt();
			}
			$menustr .= '</section>';
		endif;
	endforeach;
	if ('' != $menustr) {
		printf('<div id="context-nav" role="navigation">%s</div>', $menustr);
	}
}

//function dlp_show_ancestor() {
//	$ancestors = get_ancestors( get_the_ID(), 'page' );
//	print_r($ancestors);
//	$root = end($ancestors);
//	print_r($ancestors);
//}

function dlp_show_ancestor() {
	global $post;
	$pageId = '';
    $menu_name = 'primary';
	$locations = get_nav_menu_locations();
	$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
	$menuItems = wp_get_nav_menu_items($menu->term_id);
        foreach($menuItems as $menuItem) {
            if($menuItem->object_id == $post->ID && $menuItem->object == $post->post_type) {
                $parentMenuId = $menuItem->menu_item_parent;
                break;
            }
        }
        foreach($menuItems as $menuItem) {
            if($menuItem->ID == $parentMenuId && $menuItem->object == 'post') {
                $pageId = $menuItem->object_id;
                $menuId = $menuItem->ID;
                break;
            }
        }
	$content = '<div class="backto"><span> &#8592; ' . __('Back to', 'rrze-dlp') . ' </span><a href="' . get_page_link($pageId) . '" class="readmore">' . get_the_title($pageId) . '</a></div>';
	echo $content;
}


function rrze_dlp_page_menu_args( $args ) {
    $args['show_home'] = true;
    return $args;
}
add_filter( 'wp_page_menu_args', 'rrze_dlp_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @since RRZE-DLP 2.0
 */
function rrze_dlp_body_classes( $classes ) {
    // Adds a class of group-blog to blogs with more than 1 published author
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }

    return $classes;
}
add_filter( 'body_class', 'rrze_dlp_body_classes' );

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 *
 * @since RRZE-DLP 2.0
 */
function rrze_dlp_enhanced_image_navigation( $url, $id ) {
    if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
        return $url;

    $image = get_post( $id );
    if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
        $url .= '#main';

    return $url;
}
add_filter( 'attachment_link', 'rrze_dlp_enhanced_image_navigation', 10, 2 );



if ( ! function_exists( 'rrze-dlp_modified' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since RRZE-DLP 2.0
 */
function rrze_dlp_modified() {
    printf( __( '<br />Modified: <time class="entry-date" datetime="%3$s" pubdate>%4$s</time>', 'rrze-dlp' ),
        esc_url( get_permalink() ),
        esc_attr( get_the_time() ),
        esc_attr( get_the_date( 'c' ) ),
        esc_html( get_the_modified_date(__('F j, Y', 'rrze-dlp') ) ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
        esc_attr( sprintf( __( 'View all posts by %s', 'rrze-dlp' ), get_the_author() ) ),
        esc_html( get_the_author() )
    );
}
endif;

