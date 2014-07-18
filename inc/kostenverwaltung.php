<?php

/* 
 * Kostenverwaltung
 */

// Register Custom Post Type
function rrze_dlp_kosten_post_type() {
	$labels = array(
		'name'                => _x( 'Kosten', 'Verwaltung von Kosten und Preisen zu Dienstleistungen', 'rrze-dlp' ),
		'singular_name'       => _x( 'Kosten', 'Verwaltung von Kosten und Preisen zu Dienstleistungen', 'rrze-dlp' ),
		'menu_name'           => __( 'Kosten', 'rrze-dlp' ),            
	);
	$args = array(
		'label'               => __( 'Kosten', 'rrze-dlp' ),
		'description'	      => __( 'Verwaltung von Kosten und Preisen zu Dienstleistungen', 'rrze-dlp' ),
		'labels'              => $labels,
		'supports'            => array( 'title'),
		'hierarchical'        => true,
		'public'              => true,
		'menu_position'       => 7,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
	);
	register_post_type( 'kosten', $args );
}

// Hook into the 'init' action
add_action( 'init', 'rrze_dlp_kosten_post_type', 0 );

function rrze_dlp_taxonomies_kosten() {
	$labels = array();
	$args = array(
		'labels'	=> $labels,
		'hierarchical' => true,
	);
	register_taxonomy( 'kosten_category', 'kosten', $args );
}
add_action( 'init', 'rrze_dlp_taxonomies_kosten', 0 );


function add_menu_icons_styles(){
?>
 <style>
#adminmenu .menu-icon-kosten div.wp-menu-image:before {
content: "\f466";
}
</style>
 <?php
}
add_action( 'admin_head', 'add_menu_icons_styles' );


/*
 * Metabox fuer weitere Personeninfo
 */


function rrze_dlp_kosten_metabox() {
    add_meta_box(
        'rrze_dlp_kosten_metabox',
        __( 'kosten Information', 'rrze-dlp' ),
        'rrze_dlp_kosten_metabox_content',
        'kosten',
        'normal',
        'high'
    );
}
function rrze_dlp_kosten_metabox_content( $post ) {
    global $defaultoptions;
    global $post;
    global $kostendata;
   
    wp_nonce_field( plugin_basename( __FILE__ ), 'kosten_metabox_content_nonce' );
	
    foreach($kostendata as $field => $value) {   
	echo '<p>';
	echo '<label for="'.$field.'">'.$value['title'].':</label><br>';
	echo "\n";
	switch($value['type']) {
	    // text
	    case 'text':
		echo '<input class="widefat" type="text" name="'.$field.'" id="'.$field.'" value="'.esc_attr( get_post_meta( $post->ID, $field, true ) ).'" size="15" />';
		break;
	     case 'textarea':
		echo '<textarea name="'.$field.'" id="'.$field.'" cols="60" rows="5" />'.esc_attr( get_post_meta( $post->ID, $field, true ) ).'</textarea>';
		break;
	    case 'email':
		echo '<input class="widefat email" type="text" name="'.$field.'" id="'.$field.'" value="'.esc_attr( get_post_meta( $post->ID, $field, true ) ).'" size="10" />';
		break;
	    case 'url':
		echo '<input class="widefat url" type="text" name="'.$field.'" id="'.$field.'" value="'.esc_attr( get_post_meta( $post->ID, $field, true ) ).'" size="15" />';	
		break;
	    case 'intval':
		echo '<input class="number" type="text" name="'.$field.'" id="'.$field.'" value="'.esc_attr( get_post_meta( $post->ID, $field, true ) ).'" size="5" />';	
		break;
	} //end switch
	echo "\n";
	echo "</p>\n";
    }

}
add_action( 'add_meta_boxes', 'rrze_dlp_kosten_metabox' );


function rrze_dlp_kosten_metabox_save( $post_id ) {
    global $options;
    if (  'kosten'!= get_post_type()  ) {
	return;
    }


	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	return;

	if ( !wp_verify_nonce( $_POST['kosten_metabox_content_nonce'], plugin_basename( __FILE__ ) ) )
	return;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	global $kostendata;
	
	foreach($kostendata as $field => $value) {   
	    rrze_dlp_updatedata($post_id, $field, $value['type'], $_POST[$field] );
	}
	
}
add_action( 'save_post', 'rrze_dlp_kosten_metabox_save' );



function rrze_dlp_display_kosten ($post_id = 0) {
    global $options;
    
    
   
    $kosten_name = get_post_meta( $post_id, 'kosten_name', true );
    $kosten_telefon = get_post_meta( $post_id, 'kosten_telefon', true );
    $kosten_addresse = get_post_meta( $post_id, 'kosten_addresse', true ); 
    $kosten_url = get_post_meta( $post_id, 'kosten_url', true );
    $kosten_email = get_post_meta( $post_id, 'kosten_email', true );

    $out = "<div class=\"kosten\">\n";
    $out .= "<h2>kosten</h2><h3>$kosten_name</h3>\n";
   
    $c = '';
    if (isset($kosten_email) && !empty($kosten_email)) {
	$c .= "<li>E-Mail: <a class=\"email\" href=\"mailto:$kosten_email\">$kosten_email</a></li>\n";
    }
    if (isset($kosten_telefon) && !empty($kosten_telefon)) {
	$c .= "<li>Telefon: <span class=\"tel\">$kosten_telefon</span></li>\n";
    }
    if (isset($kosten_url) && !empty($kosten_url)) {
	$c .= "<li>Web: <a class=\"url\" href=\"$kosten_url\">$kosten_url</a></li>\n";
    }
    if (isset($kosten_addresse) && !empty($kosten_addresse)) {
	$c .= "<li>Adresse: <address>$kosten_addresse</address></li>\n";
    }
    if (strlen($c)>1) {
	$out .= "<ul>";
	$out .= $c;
	$out .= "</ul>";
	$out .= "</div>\n";
    } else {
	$out = '';
    }
    return $out;
}

/*
 * Shortcode CPT Person
 */


function rrze_dlp_kosten_shortcode( $atts ) {
    global $options;

	extract( shortcode_atts( array(
		'id'	=> '',
	
	), $atts ) );
	$out = '';
	if ((isset($id)) && ( strlen(trim($id))>0)) {
		$args = array(
			'post_type' => 'kosten',
			'p' => $id
		);
		
		$person = new WP_Query( $args );
		if( $person->have_posts() ) { 
		    while ($person->have_posts() ) {
			    $person->the_post();	   
			    $post_id = $person->post->ID;
			    $out .= rrze_dlp_display_kosten($post_id);
			 
		    }
		}  
		wp_reset_query();
	}
	return $out;
}
add_shortcode( 'kosten', 'rrze_dlp_kosten_shortcode' );


/* Adding Metabox for setting a link from posts to people */

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'rrze_dlp_post_metabox_kosten_setup' );
add_action( 'load-post-new.php', 'rrze_dlp_post_metabox_kosten_setup' );

/* Meta box setup function. */
function rrze_dlp_post_metabox_kosten_setup() {
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'rrze_dlp_add_post_metabox_kosten' );	
		/* Save post meta on the 'save_post' hook. */
	add_action( 'save_post', 'rrze_dlp_save_post_class_meta', 10, 2 );
}
/* Create one or more meta boxes to be displayed on the post editor screen. */
function rrze_dlp_add_post_metabox_kosten() {

	add_meta_box(
		'rrze_dlp_post-class-person',			// Unique ID
		esc_html__( 'kosten Informationen', 'rrze-dlp' ),		// Title
		'rrze_dlp_post_class_metabox_kosten',		// Callback function
		'post',					// Admin page (or post type)
		'advanced',					// Context
		'default'					// Priority
	);
}
/* Display the post meta box. */
function rrze_dlp_post_class_metabox_kosten( $object, $box ) { 
	global $defaultoptions;
	
	wp_nonce_field( basename( __FILE__ ), 'rrze_dlp_post_class_nonce' ); 
	?>
	<p>
		<label for="rrze_dlp_id-kosten">kostenangabe auswählen</label>
		<br />
		<select name="rrze_dlp_id-kosten" id="rrze_dlp_id-kosten">
		    <option value="">Keine Angabe</option>
		    <?php
		    
			$notice = '';
			 $oldid = esc_attr( get_post_meta( $object->ID, 'rrze_dlp_id-kosten', true ) );
		    	    $args = array(
					'post_type' => 'kosten',
					'order' => 'ASC',
					'meta_key' => 'kosten_name',
					'orderby' => 'meta_value',
					'posts_per_page' => 30,

				);
	    
			    $out = '';
			    $personlist = new WP_Query( $args );
			    if( $personlist->have_posts() ) {
				while ($personlist->have_posts() ) {
				    $personlist->the_post();	   
				    $listid = $personlist->post->ID;
				    $fullname = get_post_meta( $listid, 'kosten_name', true );
				    $out .= '<option value="'.$listid.'"';
				    if ($oldid && $oldid==$listid) {
					$out .= ' selected="selected';
				    }
				    $out .= '">'.$fullname.'</option>'."\n";
				}
			    } else {
				$notice = __('Keine kostendaten verf&uuml;gbar.', 'rrze-dlp');
			    }
			    wp_reset_query();
			    if (isset($out)) {
				echo $out;
			    }		    
			    ?>
		</select>	
		<?php
		   if (isset($notice)) {
		       echo '<span class="info">'.$notice."</span>\n";
		   }
		?>
	</p>


	    
<?php }

/* Save the meta box's post metadata. */
function rrze_dlp_save_post_class_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['rrze_dlp_post_class_nonce'] ) || !wp_verify_nonce( $_POST['rrze_dlp_post_class_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	$newid = ( isset( $_POST['rrze_dlp_id-kosten'] ) ? sanitize_key( $_POST['rrze_dlp_id-kosten'] ) : '' );
	$oldid = get_post_meta( $post_id, 'rrze_dlp_id-kosten', true );

	if ( $newid && $newid != $oldid ) {
		update_post_meta( $post_id, 'rrze_dlp_id-kosten', $newid );
	} elseif ( '' == $newid && $oldid ) {
		delete_post_meta( $post_id, 'rrze_dlp_id-kosten', $oldid );
	}


}

