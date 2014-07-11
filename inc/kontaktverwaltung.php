<?php

/* 
 * Defines visiting cards / personal info pages using custom post types and meta boxes
 */

// Register Custom Post Type
function rrze_dlp_kontakt_post_type() {
	$labels = array(
		'name'                => _x( 'Kontaktinfos', 'Verwaltung von Kontaktinformationen', 'rrze-dlp' ),
		'singular_name'       => _x( 'Kontaktinfos', 'Verwaltung von Kontaktinformationen', 'rrze-dlp' ),
		'menu_name'           => __( 'Kontaktinfos', 'rrze-dlp' ),            
	);
	$args = array(
		'label'               => __( 'Kontakt', 'rrze-dlp' ),
		'description'	      => __( 'Verwaltung von Kontaktinformationen', 'rrze-dlp' ),
		'labels'              => $labels,
		'supports'            => array( 'title'),
		'hierarchical'        => false,
		'public'              => true,
		'menu_position'       => 7,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
	);
	register_post_type( 'kontakt', $args );
}

// Hook into the 'init' action
add_action( 'init', 'rrze_dlp_kontakt_post_type', 0 );

function rrze_dlp_taxonomies_kontakt() {
	$labels = array();
	$args = array(
		'labels'	=> $labels,
		'hierarchical' => true,
	);
	register_taxonomy( 'kontakt_category', 'kontakt', $args );
}
add_action( 'init', 'rrze_dlp_taxonomies_kontakt', 0 );


function add_menu_icons_styles(){
?>
 <style>
#adminmenu .menu-icon-kontakt div.wp-menu-image:before {
content: "\f466";
}
</style>
 <?php
}
add_action( 'admin_head', 'add_menu_icons_styles' );


/*
 * Metabox fuer weitere Personeninfo
 */


function rrze_dlp_kontakt_metabox() {
    add_meta_box(
        'rrze_dlp_kontakt_metabox',
        __( 'Kontakt Information', 'rrze-dlp' ),
        'rrze_dlp_kontakt_metabox_content',
        'kontakt',
        'normal',
        'high'
    );
}
function rrze_dlp_kontakt_metabox_content( $post ) {
    global $defaultoptions;
    global $post;
    global $kontaktdata;
   
    wp_nonce_field( plugin_basename( __FILE__ ), 'kontakt_metabox_content_nonce' );
	
    foreach($kontaktdata as $field => $value) {   
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
add_action( 'add_meta_boxes', 'rrze_dlp_kontakt_metabox' );


function rrze_dlp_kontakt_metabox_save( $post_id ) {
    global $options;
    if (  'kontakt'!= get_post_type()  ) {
	return;
    }


	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	return;

	if ( !wp_verify_nonce( $_POST['kontakt_metabox_content_nonce'], plugin_basename( __FILE__ ) ) )
	return;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	global $kontaktdata;
	
	foreach($kontaktdata as $field => $value) {   
	    rrze_dlp_updatedata($post_id, $field, $value['type'], $_POST[$field] );
	}
	/*	rrze_dlp_updatedata($post_id, 'kontakt_name', 'text', $_POST['kontakt_name'] );
	rrze_dlp_updatedata($post_id, 'kontakt_telefon', 'text',$_POST['kontakt_telefon'] );
	rrze_dlp_updatedata($post_id, 'kontakt_email', 'email',$_POST['kontakt_email'] );
	rrze_dlp_updatedata($post_id, 'kontakt_url', 'url',$_POST['kontakt_url'] );
	rrze_dlp_updatedata($post_id, 'kontakt_addresse', 'text',$_POST['kontakt_addresse'] );
	*/
}
add_action( 'save_post', 'rrze_dlp_kontakt_metabox_save' );


function rrze_dlp_updatedata($post_id, $fieldname, $type = 'text', $input = '') {
    
    $value= '';
    switch($type) {
        // text
	case 'text':
	    $value = sanitize_text_field( $input ); 
	    break;
	case 'textarea':
	    $value =  $input ; 
	    break;
	case 'email':
	    if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
		$value = $input;
	    }
	    break;
	case 'url':
	    if (filter_var($input, FILTER_VALIDATE_URL)) {
		$value = $input;
	    }	
	    break;
	case 'intval':
	    $value = intval($input);
	    break;
    } //end switch
    
    $oldvalue = get_post_meta( $post_id, $fieldname, true );

    if ( $value && '' == $oldvalue )
	add_post_meta( $post_id, $fieldname, $value, true );
    elseif ( $value && $value != $oldvalue )
	update_post_meta( $post_id, $fieldname, $value );
    elseif ( '' == $value && $oldvalue )
	delete_post_meta( $post_id, $fieldname, $oldvalue );
}




function rrze_dlp_display_kontakt ($post_id = 0) {
    global $options;
    
    
   
    $kontakt_name = get_post_meta( $post_id, 'kontakt_name', true );
    $kontakt_telefon = get_post_meta( $post_id, 'kontakt_telefon', true );
    $kontakt_addresse = get_post_meta( $post_id, 'kontakt_addresse', true ); 
    $kontakt_url = get_post_meta( $post_id, 'kontakt_url', true );
    $kontakt_email = get_post_meta( $post_id, 'kontakt_email', true );

    $out = "<div class=\"kontakt\">\n";
    $out .= "<h2>Kontakt $kontakt_name</h2>\n";
   
    $c = '';
    if (isset($kontakt_email)) {
	$c .= "<li>E-Mail: <a class=\"email\" href=\"mailto:$kontakt_email\">$kontakt_email</a></li>\n";
    }
    if (isset($kontakt_telefon)) {
	$c .= "<li>Telefon: <span class=\"tel\">$kontakt_telefon</span></li>\n";
    }
    if (isset($kontakt_url)) {
	$c .= "<li>Web: <a class=\"url\" href=\"$kontakt_url\">$kontakt_url</a></li>\n";
    }
    if (isset($kontakt_addresse)) {
	$c .= "<li>Adresse: <address>$kontakt_addresse</address></li>\n";
    }
    if (strlen($c)>1) {
	$out .= $c;
	$out .= "</div>\n";
    } else {
	$out = '';
    }
    return $out;
}

/*
 * Shortcode CPT Person
 */


function rrze_dlp_kontakt_shortcode( $atts ) {
    global $options;

	extract( shortcode_atts( array(
		'id'	=> '',
	
	), $atts ) );
	$out = '';
	if ((isset($id)) && ( strlen(trim($id))>0)) {
		$args = array(
			'post_type' => 'kontakt',
			'p' => $id
		);
		
		$person = new WP_Query( $args );
		if( $person->have_posts() ) { 
		    while ($person->have_posts() ) {
			    $person->the_post();	   
			    $post_id = $person->post->ID;
			    $out .= rrze_dlp_display_kontakt($post_id);
			 
		    }
		}  
		wp_reset_query();
	}
	return $out;
}
add_shortcode( 'kontakt', 'rrze_dlp_kontakt_shortcode' );


/* Adding Metabox for setting a link from posts to people */

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'rrze_dlp_post_metabox_kontakt_setup' );
add_action( 'load-post-new.php', 'rrze_dlp_post_metabox_kontakt_setup' );

/* Meta box setup function. */
function rrze_dlp_post_metabox_kontakt_setup() {
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'rrze_dlp_add_post_metabox_kontakt' );	
		/* Save post meta on the 'save_post' hook. */
	add_action( 'save_post', 'rrze_dlp_save_post_class_meta', 10, 2 );
}
/* Create one or more meta boxes to be displayed on the post editor screen. */
function rrze_dlp_add_post_metabox_kontakt() {

	add_meta_box(
		'rrze_dlp_post-class-person',			// Unique ID
		esc_html__( 'Kontakt Informationen', 'rrze-dlp' ),		// Title
		'rrze_dlp_post_class_metabox_kontakt',		// Callback function
		'post',					// Admin page (or post type)
		'advanced',					// Context
		'default'					// Priority
	);
}
/* Display the post meta box. */
function rrze_dlp_post_class_metabox_kontakt( $object, $box ) { 
	global $defaultoptions;
	
	wp_nonce_field( basename( __FILE__ ), 'rrze_dlp_post_class_nonce' ); 
	?>
	<p>
		<label for="rrze_dlp_id-kontakt">Kontaktangabe auswählen</label>
		<br />
		<select name="rrze_dlp_id-kontakt" id="rrze_dlp_id-kontakt">
		    <option value="">Keine Angabe</option>
		    <?php
		    
			$notice = '';
			 $oldid = esc_attr( get_post_meta( $object->ID, 'rrze_dlp_id-kontakt', true ) );
		    	    $args = array(
					'post_type' => 'kontakt',
					'order' => 'ASC',
					'meta_key' => 'kontakt_name',
					'orderby' => 'meta_value',
					'posts_per_page' => 30,

				);
	    
			    $out = '';
			    $personlist = new WP_Query( $args );
			    if( $personlist->have_posts() ) {
				while ($personlist->have_posts() ) {
				    $personlist->the_post();	   
				    $listid = $personlist->post->ID;
				    $fullname = get_post_meta( $listid, 'kontakt_name', true );
				    $out .= '<option value="'.$listid.'"';
				    if ($oldid && $oldid==$listid) {
					$out .= ' selected="selected';
				    }
				    $out .= '">'.$fullname.'</option>'."\n";
				}
			    } else {
				$notice = __('Keine Kontaktdaten verf&uuml;gbar.', 'rrze-dlp');
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

	$newid = ( isset( $_POST['rrze_dlp_id-kontakt'] ) ? sanitize_key( $_POST['rrze_dlp_id-kontakt'] ) : '' );
	$oldid = get_post_meta( $post_id, 'rrze_dlp_id-kontakt', true );

	if ( $newid && '' == $oldid )
		add_post_meta( $post_id, 'rrze_dlp_id-kontakt', $newid, true );
	elseif ( $newid && $newid != $oldid )
		update_post_meta( $post_id, 'rrze_dlp_id-kontakt', $newid );
	elseif ( '' == $newid && $oldid )
		delete_post_meta( $post_id, 'rrze_dlp_id-kontakt', $oldid );
	


}

