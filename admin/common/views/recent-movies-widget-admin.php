	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><h4 class="wpml-widget-title"><?php _e( 'Title', WPML_SLUG ); ?></h4></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'description' ); ?>"><h4 class="wpml-widget-title"><?php _e( 'Description', WPML_SLUG ); ?></h4></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><h4 class="wpml-widget-title"><?php _e( 'Number of movies to show', WPML_SLUG ); ?></h4></label> 
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
	</p>
