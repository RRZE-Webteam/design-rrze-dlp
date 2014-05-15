<?php
/**
 * RRZE-DLP functions and definitions
 *
 * @package RRZE-DLP
 * @since RRZE-DLP 2.0
 */


require_once ( get_stylesheet_directory() . '/theme-options.php' );

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since RRZE-DLP 2.0
 */
if ( ! isset( $content_width ) )
    $content_width = 1170; /* pixels */

if ( ! function_exists( 'rrze_dlp_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since RRZE-DLP 2.0
 */
function rrze_dlp_setup() {

    // Custom template tags for this theme.
    require( get_template_directory() . '/inc/template-tags.php' );

    //Custom functions that act independently of the theme templates
    require( get_template_directory() . '/inc/tweaks.php' );

    /**
     * Make theme available for translation
     * Translations can be filed in the /languages/ directory
     * If you're building a theme based on Shape, use a find and replace
     * to change 'shape' to the name of your theme in all the template files
     */
    load_theme_textdomain( 'rrze-dlp', get_template_directory() . '/languages' );

    //This theme uses wp_nav_menu() in one location.
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'rrze-dlp' ),
    ) );
}
endif; // rrze_dlp_setup
add_action( 'after_setup_theme', 'rrze_dlp_setup' );


/**
 * Register RRZE-DLP Widgets
 */

function rrze_dlp_widgets_init() {

    register_sidebar( array(
        'name' => __( 'Additional Information', 'rrze-dlp' ),
        'id' => 'sidebar-footer',
        'description'   => __( 'This area shows additional information in the footer. You can add useful links or other information displayed on every page. They are excluded from print layout.', 'rrze-dlp' ),
        'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));

}
add_action( 'widgets_init', 'rrze_dlp_widgets_init' );


/**
 * Enqueue scripts and styles
 */
function rrze_dlp_scripts() {
    wp_enqueue_style( 'style', get_stylesheet_uri() );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    wp_enqueue_script( 'navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array(), false);

	if ( is_singular() && wp_attachment_is_image() ) {
        wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
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
 * Meta Box
 */

// Add the Meta Box
function add_custom_meta_box() {
    add_meta_box(
        'custom_meta_box', // $id
        'Informationen zur Dienstleistung', // $title
        'show_custom_meta_box', // $callback
        'post', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'add_custom_meta_box');

//Field Array
$custom_meta_fields = array(
    array(
        'label'=> 'Service',
        'desc'  => 'Kurzbeschreibung der DL (1 Satz)',
        'id'    => 'service',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Beschreibung',
        'desc'  => 'ausführliche, erläuternde Beschreibung der DL (3-10 Sätze) Leitsätze: Wie kann man die DL treffend und umfänglich mit einem Satz beschreiben? Was kann der Kunde damit tun? Welche Voraussetzungen werden an die Nutzung der DL gestellt? Welchen Nutzen zieht der Kunde aus der Benutzung der DL? Jeder Bestandteil der DL ist in einem vollständigen Satz zu formulieren. Der Text ist so formulieren, dass ein technischer Laie es versteht (Layer 8 Kompatibilität!). Fachbegriffe erklären und ins Glossar verlinken. Zuständig für abschließende Formulierung: Redaktion',
        'id'    => 'beschreibung',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Umfang',
        'desc'  => 'Was bekommt der Kunde - differenziert nach Standard-Kundengruppen (diese sind definiert unter DLP:Kundengruppen) Für Studierende? Für Beschäftigte? Für Sonstige?',
        'id'    => 'umfang',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Links zu Dokumentation',
        'desc'  => 'Hier bitte die Seiten im Wiki verlinken, die den Kunden die Benutzung der DL erläutern. Hier gehören auch Links auf die Dokumentation der zur Erbringung der DL benötigten Server hin! Außerdem die zur Erbringung notwendige Software. Sollte die Software von uns selbst paketiert werden, so bitte auf die entsprechenden Projekte auf dem rembo (Windows) oder OBS (OpenSUSE Build Service, Linux) verlinken. notfalls erstmal Links auf entsprechende RRZE-Webseiten. Diese dann aber bei Zeiten ins RRZE-Wiki überführen!',
        'id'    => 'links_zu_dokumentation',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Basisdienstleistungen',
        'desc'  => 'Was ist für die Erbringung dieser DL direkt notwendig - Zuständig: Gruppe/Abteilung bzw. Person - Abteilung - Zuständig: Abteilungen, besser Gruppen - nur bei wirklich sehr personenbezogenen Zuständigkeiten, das Namenskürzel der Person inkl. Angabe der Abteilung',
        'id'    => 'basisdienstleistungen',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Preis Basisdienstleistungen',
        'desc'  => '«TODO: sollte hier ein kalkulatorische Preis stehen, wenn die DL für den Nutzer kostenlos ist?»',
        'id'    => 'preis_basisdienstleistungen',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Leistungserweiterungen',
        'desc'  => 'Welche Erweiterungen zur DL sind verfügbar? (optional) Zuständig: Gruppe/Abteilung bzw. Person - Abteilung - Zuständig: Abteilungen, besser Gruppen - nur bei wirklich sehr personenbezogenen Zuständigkeiten, das Namenskürzel der Person inkl. Angabe der Abteilung',
        'id'    => 'leistungserweiterungen',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Preis Leistungserweiterungen',
        'desc'  => 'Aus WIKI: «TODO: Sollte hier ein kalkulatorischer Preis stehen, wenn die DL für den Nutzer kostenlos ist oder sind Erweiterungen immer kostenpflichtig?»',
        'id'    => 'preis_leistungserweiterungen',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Kontakt',
        'desc'  => 'ansonsten: DL-spezifisches Funktionspostfach',
        'id'    => 'kontakt',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Abhängigkeiten',
        'desc'  => 'andere DL von denen diese (als Vorbedingung) abhängig ist] Aus WIKI: «TODO: Abhängigkeiten klären durchgängiges Nummerierungsssystem - angelehnt an die KLR?»',
        'id'    => 'abhaengigkeiten',
        'type'  => 'textarea'
    )
);

// The Callback
function show_custom_meta_box() {
global $custom_meta_fields, $post;
// Use nonce for verification
echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

    // Begin the field table and loop
    foreach ($custom_meta_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
        echo '<h4><label for="'.$field['id'].'">'.$field['label'].'</label></h4>';
                switch($field['type']) {
                    // text
					case 'text':
						echo '<span class="description">'.$field['desc'].'</span><br />'
							.'<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />';
						break;
					// textarea
					case 'textarea':
						echo '<span class="description">'.$field['desc'].'</span><br />';
						wp_editor($meta,$field['id']);
						break;
					// textarea ohne WYSIWYG
					/*case 'textarea':
						echo '<span class="description">'.$field['desc'].'</span><br />'
							. '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="60" rows="4">'.$meta.'</textarea>';
						break;*/
					// checkbox
					case 'checkbox':
						echo '<span class="description">'.$field['desc'].'</span><br />'
							.'<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" ',$meta ? ' checked="checked"' : '','/>';
						break;
					// select
					case 'select':
						echo '</select><br /><span class="description">'.$field['desc'].'</span>';
						echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
						foreach ($field['options'] as $option) {
							echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
						}
						break;
				} //end switch
		echo '<br /><hr />';
    } // end foreach
}

// Save the Data
function save_custom_meta($post_id) {
    global $custom_meta_fields;

    // verify nonce
    if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
        return $post_id;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }

    // loop through fields and save the data
    foreach ($custom_meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach
}
add_action('save_post', 'save_custom_meta');



/**
 * Render custom fields
 */
function rrze_dlp_fields() {
	global $post;

	$display_field = array(
	    'service'			    => 1,
	    'beschreibung'		    => 1,
	    'umfang'			    => 1,
	    'links_zu_dokumentation'	    => 1,
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
	    - Preis Basisdienstleistungen
	    - Leistungserweiterungen
	    - Preis Leistungserweiterungen
	    - Abhängigkeiten
 */

	$field_label = array(
		'service'			    => 'Service',
	    'beschreibung'		    => 'Beschreibung',
	    'umfang'			    => 'Umfang',
	    'links_zu_dokumentation'	    => 'Links zur Dokumentation',
	    'basisdienstleistungen'	    => 'Basisdienstleistungen',
	    'preis_basisdienstleistungen'   => 'Preis Basisdienstleistungen',
	    'leistungserweiterungen'	    => 'Leistungserweiterungen',
	    'preis_leistungserweiterungen'  => 'Preis Leistungserweiterungen',
	    'kontakt'			    => 'Kontakt',
	    'abhaengigkeiten'		    => 'Abhängigkeiten',
	);

	$custom_fields = get_post_meta( $post->ID);
	$str = '';

	foreach ($custom_fields as $key => $value) {
		if ((!empty( $value[0]) && substr($key,0,1) !== "_")
				&& ((is_user_logged_in() || (!is_user_logged_in() && $display_field[$key] == "1" )))) {
			$str .= sprintf( '<h2>%s</h2>', $field_label[$key] );
			$str .= sprintf( '<p>%s</p>', $value[0] );
		}
	}

	echo $str;
}

/**
 * Breadcrumbs (Quelle: http://www.qualitytuts.com/wordpress-custom-breadcrumbs-without-plugin/)
 */
	function custom_breadcrumbs() {

  $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $delimiter = '&nbsp;&raquo;&nbsp;'; // delimiter between crumbs
  $home = 'Startseite'; // text for the 'Home' link
  $showCurrent = 0; // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb

  global $post;
  $homeLink = get_bloginfo('url');

  if (is_home() || is_front_page()) {

    if ($showOnHome == 1) echo '<div id="breadcrumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';

  } else {

    echo '<div id="breadcrumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

    if ( is_category() ) {
      $thisCat = get_category(get_query_var('cat'), false);
      if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
      echo $before . __('Archive by category', 'rrze-dlp'). ' "' . single_cat_title('', false) . '"' . $after;

    } elseif ( is_search() ) {
      echo $before . __('Search results for', 'rrze-dlp'). ' "' . get_search_query() . '"' . $after;

    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('d') . $after;

    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('F') . $after;

    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;

    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
        if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
        echo $cats;
        if ($showCurrent == 1) echo $before . get_the_title() . $after;
      }

    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;

    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

    } elseif ( is_page() && !$post->post_parent ) {
      if ($showCurrent == 1) echo $before . get_the_title() . $after;

    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      for ($i = 0; $i < count($breadcrumbs); $i++) {
        echo $breadcrumbs[$i];
        if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
      }
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

    } elseif ( is_tag() ) {
      echo $before .  __('Posts tagged', 'rrze-dlp'). ' "' . single_tag_title('', false) . '"' . $after;

    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . __('Articles posted by ', 'rrze-dlp'). $userdata->display_name . $after;

    } elseif ( is_404() ) {
      echo $before . __('Error 404', 'rrze-dlp') . $after;
    }

    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page','rrze-dlp') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }

    echo '</div>';

  }
} // end breadcrumbs()
