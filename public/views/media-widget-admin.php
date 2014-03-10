	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wpml' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', 'wpml' ); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Media', 'wpml' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" class="widefat">
		<?php foreach ( $wpml->wpml_get_available_movie_media() as $slug => $title ) : ?>
			<option value="<?php echo $slug ?>" <?php selected( $type, $slug ) ?>><?php echo $title ?></option>
		<?php endforeach; ?>
		</select>
		
	</p>
	<p>
		<input id="<?php echo $this->get_field_id( 'list' ); ?>" name="<?php echo $this->get_field_name( 'list' ); ?>" type="checkbox" value="1" <?php checked( $list, 1 ); ?> /> 
		<label for="<?php echo $this->get_field_id( 'list' ); ?>"><?php _e( 'Display as dropdown', 'wpml' ); ?></label><br />
		<input id="<?php echo $this->get_field_id( 'thumbnails' ); ?>" name="<?php echo $this->get_field_name( 'thumbnails' ); ?>" type="checkbox" value="1" <?php checked( $thumbnails, 1 ); ?> /> 
		<label for="<?php echo $this->get_field_id( 'thumbnails' ); ?>"><?php _e( 'Show thumbnails', 'wpml' ); ?></label><br />
		<input id="<?php echo $this->get_field_id( 'css' ); ?>" name="<?php echo $this->get_field_name( 'css' ); ?>" type="checkbox" value="1" <?php checked( $css, 1 ); ?> /> 
		<label for="<?php echo $this->get_field_id( 'css' ); ?>"><?php _e( 'Custom Style (only for dropdown)', 'wpml' ); ?></label>
	</p>