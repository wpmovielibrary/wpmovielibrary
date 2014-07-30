<?php
extract( $instance );
?>
	<p>
		<label for="<?php echo $widget->get_field_id( 'title' ); ?>"><strong class="wpml-widget-title"><?php _e( 'Title', 'wpmovielibrary' ); ?></strong></label> 
		<input class="widefat" id="<?php echo $widget->get_field_id( 'title' ); ?>" name="<?php echo $widget->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'description' ); ?>"><strong class="wpml-widget-title"><?php _e( 'Description', 'wpmovielibrary' ); ?></strong></label> 
		<textarea class="widefat" id="<?php echo $widget->get_field_id( 'description' ); ?>" name="<?php echo $widget->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'detail' ); ?>"><strong class="wpml-widget-title"><?php _e( 'Detail', 'wpmovielibrary' ); ?></strong></label><br />
		<select id="<?php echo $widget->get_field_id( 'detail' ); ?>" name="<?php echo $widget->get_field_name( 'detail' ); ?>">
			<option value="status" <?php selected( 'status', $detail ); ?>><?php _e( 'Status', 'wpmovielibrary' ); ?></option>
			<option value="media" <?php selected( 'media', $detail ); ?>><?php _e( 'Media', 'wpmovielibrary' ); ?></option>
			<option value="rating" <?php selected( 'rating', $detail ); ?>><?php _e( 'Rating', 'wpmovielibrary' ); ?></option>

		</select>
	</p>
	<p>
		<input id="<?php echo $widget->get_field_id( 'list' ); ?>" name="<?php echo $widget->get_field_name( 'list' ); ?>" type="checkbox" value="1" <?php checked( $list, 1 ); ?> /> 
		<label for="<?php echo $widget->get_field_id( 'list' ); ?>"><?php _e( 'Show as dropdown', 'wpmovielibrary' ); ?></label><br />
		<input id="<?php echo $widget->get_field_id( 'css' ); ?>" name="<?php echo $widget->get_field_name( 'css' ); ?>" type="checkbox" value="1" <?php checked( $css, 1 ); ?> /> 
		<label for="<?php echo $widget->get_field_id( 'css' ); ?>"><?php _e( 'Custom Style (only for dropdown)', 'wpmovielibrary' ); ?></label>
	</p>