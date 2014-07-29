<?php
extract( $instance );
?>
	<p>
		<label for="<?php echo $widget->get_field_id( 'title' ); ?>"><h4 class="wpml-widget-title"><?php _e( 'Title', 'wpmovielibrary' ); ?></h4></label> 
		<input class="widefat" id="<?php echo $widget->get_field_id( 'title' ); ?>" name="<?php echo $widget->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'description' ); ?>"><h4 class="wpml-widget-title"><?php _e( 'Description', 'wpmovielibrary' ); ?></h4></label> 
		<textarea class="widefat" id="<?php echo $widget->get_field_id( 'description' ); ?>" name="<?php echo $widget->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'select' ); ?>"><h4 class="wpml-widget-title"><?php _e( 'Show movies by...', 'wpmovielibrary' ); ?></h4></label>
		<select class="" id="<?php echo $widget->get_field_id( 'select' ); ?>" name="<?php echo $widget->get_field_name( 'select' ); ?>">
<?php foreach ( $widget->movies_by as $slug => $title ) : ?>
			<option value="<?php echo $slug; ?>" <?php selected( $select, '' ); ?>><?php echo $title; ?></option>
<?php endforeach; ?>

		</select>
		<select class="" id="<?php echo $widget->get_field_id( 'select_status' ); ?>" name="<?php echo $widget->get_field_name( 'select_status' ); ?>">
<?php foreach ( $widget->status as $slug => $title ) : ?>
			<option value="<?php echo $slug; ?>" <?php selected( $select_status, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

		</select>
		<select class="" id="<?php echo $widget->get_field_id( 'select_media' ); ?>" name="<?php echo $widget->get_field_name( 'select_media' ); ?>">
<?php foreach ( $widget->media as $slug => $title ) : ?>
			<option value="<?php echo $slug; ?>" <?php selected( $select_media, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

		</select>
		<select class="" id="<?php echo $widget->get_field_id( 'select_rating' ); ?>" name="<?php echo $widget->get_field_name( 'select_rating' ); ?>">
<?php foreach ( $widget->rating as $slug => $title ) : ?>
			<option value="<?php echo $slug; ?>" <?php selected( $select_rating, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

		</select>
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'sort' ); ?>"><h4 class="wpml-widget-title"><?php _e( 'Sort movies ...', 'wpmovielibrary' ); ?></h4></label>
		<select class="widefat" id="<?php echo $widget->get_field_id( 'sort' ); ?>" name="<?php echo $widget->get_field_name( 'sort' ); ?>">
			<option value="ASC" <?php selected( $sort, 'ASC' ); ?>><?php _e( 'Ascending' ); ?></option>
			<option value="DESC" <?php selected( $sort, 'DESC' ); ?>><?php _e( 'Descending' ); ?></option>
		</select>
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'limit' ); ?>"><h4 class="wpml-widget-title"><?php _e( 'Number of movies to show', 'wpmovielibrary' ); ?></h4></label> 
		<input id="<?php echo $widget->get_field_id( 'limit' ); ?>" name="<?php echo $widget->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" size="3" />
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'rating' ); ?>"><h4 class="wpml-widget-title"><?php _e( 'Display movie rating', 'wpmovielibrary' ); ?></h4></label>
		<select id="<?php echo $widget->get_field_id( 'rating' ); ?>" name="<?php echo $widget->get_field_name( 'rating' ); ?>">
			<option value="no" <?php selected( $rating, 'no' ); ?>><?php _e( 'No', 'wpmovielibrary' ); ?></option>
			<option value="below" <?php selected( $rating, 'below' ); ?>><?php _e( 'Below Poster', 'wpmovielibrary' ); ?></option>
			<option value="above" <?php selected( $rating, 'above' ); ?>><?php _e( 'Above Poster', 'wpmovielibrary' ); ?></option>
		</select>
	</p>