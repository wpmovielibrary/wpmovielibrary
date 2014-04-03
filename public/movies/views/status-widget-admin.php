	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wpml' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', 'wpml' ); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
	</p>
	<p>
		<label><input name="<?php echo $this->get_field_name( 'status_only' ); ?>" type="radio" value="1" <?php checked( $status_only, 1 ); ?> onclick="document.getElementById('_status_only_<?php echo $this->get_field_id( 'status_only' ); ?>').style.display='block';document.getElementById('_movies_only_<?php echo $this->get_field_id( 'status_only' ); ?>').style.display='none';" /> <?php _e( 'Show Status only', 'wpml' ); ?></label>
		<label><input name="<?php echo $this->get_field_name( 'status_only' ); ?>" type="radio" value="0" <?php checked( $status_only, 0 ); ?> onclick="document.getElementById('_status_only_<?php echo $this->get_field_id( 'status_only' ); ?>').style.display='none';document.getElementById('_movies_only_<?php echo $this->get_field_id( 'status_only' ); ?>').style.display='block';" /> <?php _e( 'Show Movies', 'wpml' ); ?></label>
	</p>
	<div id="_status_only_<?php echo $this->get_field_id( 'status_only' ); ?>" class="<?php if ( 1 !== $status_only ) echo 'hide-if-js'; ?>">
		<p>
			<!--<input id="<?php echo $this->get_field_id( 'show_icons' ); ?>" name="<?php echo $this->get_field_name( 'show_icons' ); ?>" type="checkbox" value="1" <?php checked( $show_icons, 1 ); ?> /> 
			<label for="<?php echo $this->get_field_id( 'show_icons' ); ?>"><?php _e( 'Show icons', 'wpml' ); ?></label><br />-->
		</p>
	</div>
	<div id="_movies_only_<?php echo $this->get_field_id( 'status_only' ); ?>" class="<?php if ( 0 !== $status_only ) echo 'hide-if-js'; ?>">
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Status', 'wpml' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" class="widefat">
			<?php foreach ( $wpml->wpml_get_available_movie_status() as $slug => $title ) : ?>
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
	</div>