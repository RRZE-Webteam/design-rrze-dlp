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



// The Callback
function show_custom_meta_box() {
    global $custom_meta_fields;
    global $post;
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
		echo '<br /><hr />';
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


