<?php
class RRZE_Widget_Meta extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_meta', 'description' => __( 'An-/Abmelden, Adminbereich', '_rrze' ) );
		parent::__construct( 'meta', __('Meta'), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title'] ) ? __( 'Meta', '_rrze' ) : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
?>
			<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<?php wp_meta(); ?>
			</ul>
<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags( $instance['title'] );
?>
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Titel:', '_rrze' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
<?php
	}
}

class RRZE_Widget_Tag_Cloud extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => __( 'Ihre Schlagworte in einer Wolke', '_rrze' ) );
		parent::__construct( 'tag_cloud', __( 'Schlagwörterwolke', '_rrze' ), $widget_ops );
	}

	function widget( $args, $instance ) {
        global $nav_menu_selected_id;
        
		extract( $args );
		$current_taxonomy = $this->_get_current_taxonomy( $instance );
		if ( !empty($instance['title']) ) {
			$title = $instance['title'];
		} else {
			if ( 'post_tag' == $current_taxonomy ) {
				$title = __( 'Schlagwörter', '_rrze' );
			} else {
				$tax = get_taxonomy( $current_taxonomy );
				$title = $tax->labels->name;
			}
		}
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<div class="tagcloud">';
		if( ! empty( $nav_menu_selected_id ) )
            _rrze_wp_tag_cloud( array( 'taxonomy' => $current_taxonomy ) );
        else
            wp_tag_cloud( apply_filters( 'widget_tag_cloud_args', array( 'taxonomy' => $current_taxonomy ) ) );
		echo "</div>\n";
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );
		return $instance;
	}

	function form( $instance ) {
		$current_taxonomy = $this->_get_current_taxonomy( $instance );
?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Titel:', '_rrze' ) ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if (isset ( $instance['title'] ) ) { echo esc_attr( $instance['title'] ); } ?>" /></p>
	<p><label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomie:', '_rrze' ) ?></label>
	<select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>">
	<?php foreach ( get_taxonomies() as $taxonomy ) :
				$tax = get_taxonomy( $taxonomy );
				if ( ! $tax->show_tagcloud || empty($tax->labels->name ) )
					continue;
	?>
		<option value="<?php echo esc_attr( $taxonomy ) ?>" <?php selected( $taxonomy, $current_taxonomy ) ?>><?php echo $tax->labels->name; ?></option>
	<?php endforeach; ?>
	</select></p><?php
	}

	function _get_current_taxonomy( $instance ) {
		if ( ! empty( $instance['taxonomy'] ) && taxonomy_exists( $instance['taxonomy'] ) )
			return $instance['taxonomy'];

		return 'post_tag';
	}
}
