<?php
extract( $instance );
?>
	<p>
		<label for="<?php echo $widget->get_field_id( 'title' ); ?>"><h4 class="wpmoly-widget-title"><?php _e( 'Title', 'wpmovielibrary' ); ?></h4></label> 
		<input class="widefat" id="<?php echo $widget->get_field_id( 'title' ); ?>" name="<?php echo $widget->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'description' ); ?>"><h4 class="wpmoly-widget-title"><?php _e( 'Description', 'wpmovielibrary' ); ?></h4></label> 
		<textarea class="widefat" id="<?php echo $widget->get_field_id( 'description' ); ?>" name="<?php echo $widget->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'taxonomy' ); ?>"><h4 class="wpmoly-widget-title"><?php _e( 'Taxonomy', 'wpmovielibrary' ); ?></h4></label> 
		<select class="widefat" id="<?php echo $widget->get_field_id( 'taxonomy' ); ?>" name="<?php echo $widget->get_field_name( 'taxonomy' ); ?>">
			<option value="collection" <?php selected( $taxonomy, 'collection' ); ?>><?php _e( 'Collections', 'wpmovielibrary' ); ?></option>
			<option value="genre" <?php selected( $taxonomy, 'genre' ); ?>><?php _e( 'Genres', 'wpmovielibrary' ); ?></option>
			<option value="actor" <?php selected( $taxonomy, 'actor' ); ?>><?php _e( 'Actors', 'wpmovielibrary' ); ?></option>
		</select>
		<label for="<?php echo $widget->get_field_id( 'orderby' ); ?>"><h4 class="wpmoly-widget-title"><?php _e( 'Order taxonomies by ...', 'wpmovielibrary' ); ?></h4></label> 
		<select class="widefat" id="<?php echo $widget->get_field_id( 'orderby' ); ?>" name="<?php echo $widget->get_field_name( 'orderby' ); ?>">
			<option value="count" <?php selected( $orderby, 'count' ); ?>><?php _e( 'Movie count', 'wpmovielibrary' ); ?></option>
			<option value="name" <?php selected( $orderby, 'name' ); ?>><?php _e( 'Taxonomy name', 'wpmovielibrary' ); ?></option>
		</select>
		<label for="<?php echo $widget->get_field_id( 'order' ); ?>"><h4 class="wpmoly-widget-title"><?php _e( 'Sort taxonomies ...', 'wpmovielibrary' ); ?></h4></label>
		<select class="widefat" id="<?php echo $widget->get_field_id( 'order' ); ?>" name="<?php echo $widget->get_field_name( 'order' ); ?>">
			<option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php _e( 'Ascending' ); ?></option>
			<option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php _e( 'Descending' ); ?></option>
		</select>
	</p>
	<p>
		<input id="<?php echo $widget->get_field_id( 'list' ); ?>" name="<?php echo $widget->get_field_name( 'list' ); ?>" type="checkbox" value="1" <?php checked( $list, 1 ); ?> /> 
		<label for="<?php echo $widget->get_field_id( 'list' ); ?>"><?php _e( 'Show as dropdown', 'wpmovielibrary' ); ?></label><br />
		<input id="<?php echo $widget->get_field_id( 'count' ); ?>" name="<?php echo $widget->get_field_name( 'count' ); ?>" type="checkbox" value="1" <?php checked( $count, 1 ); ?> /> 
		<label for="<?php echo $widget->get_field_id( 'count' ); ?>"><?php _e( 'Show movies count', 'wpmovielibrary' ); ?></label><br />
		<input id="<?php echo $widget->get_field_id( 'css' ); ?>" name="<?php echo $widget->get_field_name( 'css' ); ?>" type="checkbox" value="1" <?php checked( $css, 1 ); ?> /> 
		<label for="<?php echo $widget->get_field_id( 'css' ); ?>"><?php _e( 'Custom Style (only for dropdown)', 'wpmovielibrary' ); ?></label>
		<label for="<?php echo $widget->get_field_id( 'limit' ); ?>"><h4 class="wpmoly-widget-title"><?php _e( 'Number of collections to show', 'wpmovielibrary' ); ?></h4></label>
		<input id="<?php echo $widget->get_field_id( 'limit' ); ?>" name="<?php echo $widget->get_field_name( 'limit' ); ?>" type="text" size="3" value="<?php echo $limit; ?>" /> <em><?php _e( 'Set to 0 to disable limit. Not recommended.', 'wpmovielibrary' ); ?></em>
	</p>