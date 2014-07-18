<?php

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

/* 
function rrze_dlp_editor_content( $content ) {
    global $post;
 
    if ( empty( $content ) ) {
	
	return    var_dump($args);
	  $meta = get_post_meta($post->ID, 'beschreibung', true);
	  if ($meta) {
	      return $meta;
	  } 
	  
    } else {
	return $content;
    }
}
add_filter( 'the_editor_content', 'rrze_dlp_editor_content' );
*/

// The Callback
function show_custom_meta_box() {
    global $custom_meta_fields;
    global $post;
	// Use nonce for verification
	echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

    $known_kontaktid = esc_attr( get_post_meta( $post->ID, 'rrze_dlp_id-kontakt', true ) );	    
    // Begin the field table and loop
    $content = apply_filters( 'the_content', get_the_content() );	
    $content = str_replace( ']]>', ']]&gt;', $content );
    
    $args = array(
    'teeny' => true,
    'quicktags' => true,
    'media_buttons' => false,
    );
    
    foreach ($custom_meta_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
	
	
	    
	if ($field['id'] == 'kontakt') {	    
	    if ($known_kontaktid && $meta) {
		echo '<h4><label for="'.$field['id'].'">'.$field['label'].'</label></h4>';
		echo "<p><strong>Bitte nutzen Sie das Auswahlfeld unten.</strong><br>";
		echo "Veralteter Eintrag: </p>";
		echo "<em>$meta</em>";
	    }	
	} elseif ($field['id'] == 'beschreibung') {
	    
	    if ($content) {
		// Zeige kein Eingabefeld und Warnung und lass es so loeschen, da alles schon in
		// Content steht
	    } elseif ($meta) {		
		 echo '<h4><label for="'.$field['id'].'">'.$field['label'].'</label></h4>';
		 echo "<p><strong>Bitte nutzen Sie das Eingabefeld ganz oben für die Beschreibung</strong>. "
		 . "<br>Kopieren Sie den Inhalt dieses Feldes bitte nach oben.</p>";
		 
		 echo '<span class="description">'.$field['desc'].'</span><br />';
		 wp_editor($meta,'beschreibung',$args);
		 
	    }
	} else {
	    echo '<h4><label for="'.$field['id'].'">'.$field['label'].'</label></h4>';
	    echo "<div>";
                switch($field['type']) {
		    // text
		    case 'text':
			    echo '<span class="description">'.$field['desc'].'</span><br />'
				    .'<input class="widefat" type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="120" />';
			    break;
		    // textarea
		    case 'textarea':
			    echo '<span class="description">'.$field['desc'].'</span><br />';
			    wp_editor($meta,$field['id'],$args);
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
		    case 'contacftlist':
			    echo '</select><br /><span class="description">'.$field['desc'].'</span>';
			    echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
			    foreach ($field['options'] as $option) {
				    echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
			    }
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
		echo '</div>';
	}
    } // end foreach
}

// Save the Data
function save_custom_meta($post_id) {
    global $custom_meta_fields;
	
	// verify nonce
    if (
		!isset( $_POST['custom_meta_box_nonce'] )
		|| !wp_verify_nonce( $_POST['custom_meta_box_nonce'], basename(__FILE__) )
		)
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

    
    foreach ($custom_meta_fields as $field) {
	    rrze_dlp_updatedata($post_id, $field['id'], $field['type'], $_POST[$field['id']] );
    }
	
     
}

add_action('save_post', 'save_custom_meta');


