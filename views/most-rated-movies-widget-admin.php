	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wpml' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', 'wpml' ); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of movies to display', 'wpml' ); ?></label> 
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'display_rating' ); ?>"><?php _e( 'Display movie rating', 'wpml' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'display_rating' ); ?>" name="<?php echo $this->get_field_name( 'display_rating' ); ?>">
			<option value="no" <?php selected( $display_rating, 'no' ); ?>><?php _e( 'No', 'wpml' ); ?></option>
			<option value="below" <?php selected( $display_rating, 'below' ); ?>><?php _e( 'Below Poster', 'wpml' ); ?></option>
			<option value="above" <?php selected( $display_rating, 'above' ); ?>><?php _e( 'Above Poster', 'wpml' ); ?></option>
		</select>
	</p>
