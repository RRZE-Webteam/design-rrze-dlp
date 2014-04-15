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
 * Add Meta Boxes
 */

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'rrze_dlp_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'rrze_dlp_post_meta_boxes_setup' );

/* Meta box setup function. */
function rrze_dlp_post_meta_boxes_setup() {
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'rrze_dlp_add_post_meta_boxes' );
	/* Save post meta on the 'save_post' hook. */
	add_action( 'save_post', 'rrze_dlp_save_post_meta_boxes');
}
/* Create one or more meta boxes to be displayed on the post editor screen. */
function rrze_dlp_add_post_meta_boxes() {
	add_meta_box(
		'service',			// Unique ID
		esc_html__( 'Service', 'rrze-dlp' ),		// Title
		'rrze_dlp_meta_box',		// Callback function
		'post',					// Admin page (or post type)
		'normal',					// Context
		'default'					// Priority
	);
}

/* Display the post meta box. */
function rrze_dlp_meta_box( $object, $box ) { ?>
	<?php wp_nonce_field( basename( __FILE__ ), 'rrze_dlp_service_nonce' ); ?>
		<label for="rrze-dlp-service"><?php _e( "Short description of the service provided (1 sentence)", 'example' ); ?></label>
	<?php wp_editor(get_post_meta( $object->ID, 'service', true ),'wp_editor-service',array( 'textarea_rows' => 3 ));
 }

/* Save the meta box's post metadata. */
function rrze_dlp_save_post_meta_boxes( $post_id ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['rrze_dlp_service_nonce'] ) || !wp_verify_nonce( $_POST['rrze_dlp_service_nonce'], basename( __FILE__ ) ) )
		return;

	/* If this is an autosave, our form has not been submitted, so we don't want to do anything. */
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	/* Check the user's permissions. */
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
	}

	/* Make sure that it is set. */
	if ( ! isset( $_POST['wp_editor-service'] ) ) {
		return;
	}

	/* Get the meta key. */
	$meta_key = 'service';

	/* Get the posted data and sanitize it for use as an HTML class. */
	$new_meta_value = sanitize_text_field($_POST['wp_editor-service']);

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value ) {
	add_post_meta( $post_id, $meta_key, $new_meta_value, true ); }

	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
	update_post_meta( $post_id, $meta_key, $new_meta_value );}

	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value ) {
	delete_post_meta( $post_id, $meta_key, $meta_value );}
}


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