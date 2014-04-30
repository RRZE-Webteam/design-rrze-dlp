<?php
/**
 * RRZE-DLP functions and definitions
 *
 * @package RRZE-DLP
 * @since RRZE-DLP 2.0
 */


require_once ( get_stylesheet_directory() . '/inc/constants.php' );
require_once ( get_stylesheet_directory() . '/inc/theme-options.php' );

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since RRZE-DLP 2.0
 */
if ( ! isset( $content_width ) )
    $content_width = 1170; /* pixels */

if ( ! function_exists( 'rrze_dlp_setup' ) ):

function rrze_dlp_setup() {

    // Custom template tags for this theme.
    require( get_template_directory() . '/inc/template-tags.php' );

    //Custom functions that act independently of the theme templates
    require( get_template_directory() . '/inc/tweaks.php' );

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