<?php
extract( $instance );
?>
	<div data-wpmoly="movies-widget">
		<p>
			<label for="<?php echo $widget->get_field_id( 'title' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Title', 'wpmovielibrary' ); ?></strong></label> 
			<input class="widefat" id="<?php echo $widget->get_field_id( 'title' ); ?>" name="<?php echo $widget->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $widget->get_field_id( 'description' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Description', 'wpmovielibrary' ); ?></strong></label> 
			<textarea class="widefat" id="<?php echo $widget->get_field_id( 'description' ); ?>" name="<?php echo $widget->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $widget->get_field_id( 'select' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Show movies by...', 'wpmovielibrary' ); ?></strong></label><br />
			<select class="wpmoly-movies-widget-select wpmoly-movies-widget-select-select" data-select="select" id="<?php echo $widget->get_field_id( 'select' ); ?>" name="<?php echo $widget->get_field_name( 'select' ); ?>">
<?php foreach ( $widget->movies_by as $slug => $title ) : ?>
				<option value="<?php echo $slug; ?>" <?php selected( $select, $slug ); ?>><?php echo $title; ?></option>
<?php endforeach; ?>

			</select>

			<select class="wpmoly-movies-widget-select wpmoly-movies-widget-select-status<?php if ( 'status' == $select ) echo ' selected'; ?>" data-select="status" id="<?php echo $widget->get_field_id( 'select_status' ); ?>" name="<?php echo $widget->get_field_name( 'select_status' ); ?>" >
				<option value="all" <?php selected( $select_status, 'all' ); ?>><?php _e( 'All', 'wpmovielibrary' ); ?></option>
<?php foreach ( $widget->status as $slug => $title ) : ?>
				<option value="<?php echo $slug; ?>" <?php selected( $select_status, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

			</select>

			<select class="wpmoly-movies-widget-select wpmoly-movies-widget-select-media<?php if ( 'media' == $select ) echo ' selected'; ?>" data-select="media" id="<?php echo $widget->get_field_id( 'select_media' ); ?>" name="<?php echo $widget->get_field_name( 'select_media' ); ?>">
				<option value="all" <?php selected( $select_media, 'all' ); ?>><?php _e( 'All', 'wpmovielibrary' ); ?></option>
<?php foreach ( $widget->media as $slug => $title ) : ?>
				<option value="<?php echo $slug; ?>" <?php selected( $select_media, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

			</select>

			<select class="wpmoly-movies-widget-select wpmoly-movies-widget-select-rating<?php if ( 'rating' == $select ) echo ' selected'; ?>" data-select="rating" id="<?php echo $widget->get_field_id( 'select_rating' ); ?>" name="<?php echo $widget->get_field_name( 'select_rating' ); ?>">
				<option value="all" <?php selected( $select_rating, 'all' ); ?>><?php _e( 'All', 'wpmovielibrary' ); ?></option>
<?php foreach ( $widget->rating as $slug => $title ) : ?>
				<option value="<?php echo $slug; ?>" <?php selected( $select_rating, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>
			</select>

			<div class="wpmoly-movies-widget-select wpmoly-movies-widget-select-meta<?php if ( 'meta' == $select ) echo ' selected'; ?>">
				<select id="<?php echo $widget->get_field_id( 'select_meta' ); ?>" name="<?php echo $widget->get_field_name( 'select_meta' ); ?>" data-select-meta="select">
<?php foreach ( $widget->meta as $slug => $title ) : ?>
					<option value="<?php echo $slug; ?>" <?php selected( $select_meta, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

				</select>

				<div class="wpmoly-movies-widget-meta-select wpmoly-movies-widget-select-release_date<?php if ( 'release_date' == $select_meta ) echo ' selected'; ?> redux-field-init redux-field-container redux-field redux-container-select">
					<select name="<?php echo $widget->get_field_name( 'release_date' ); ?>">
<?php foreach ( $widget->years as $slug => $title ) : ?>
						<option value="<?php echo $slug; ?>" <?php selected( $release_date, $slug ); ?>><?php echo $title; ?></option>
<?php endforeach; ?>

					</select>
				</div>

				<div class="wpmoly-movies-widget-meta-select wpmoly-movies-widget-select-spoken_languages<?php if ( 'spoken_languages' == $select_meta ) echo ' selected'; ?> redux-field-init redux-field-container redux-field redux-container-select">
					<select name="<?php echo $widget->get_field_name( 'spoken_languages' ); ?>">
<?php foreach ( $widget->languages as $slug => $title ) : ?>
						<option value="<?php echo $slug; ?>" <?php selected( $spoken_languages, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

					</select>
				</div>

				<div class="wpmoly-movies-widget-meta-select wpmoly-movies-widget-select-production_countries<?php if ( 'production_countries' == $select_meta ) echo ' selected'; ?> redux-field-init redux-field-container redux-field redux-container-select">
					<select name="<?php echo $widget->get_field_name( 'production_countries' ); ?>">
<?php foreach ( $widget->countries as $slug => $title ) : ?>
						<option value="<?php echo $slug; ?>" <?php selected( $production_countries, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

					</select>
				</div>

				<div class="wpmoly-movies-widget-meta-select wpmoly-movies-widget-select-production_companies<?php if ( 'production_companies' == $select_meta ) echo ' selected'; ?> redux-field-init redux-field-container redux-field redux-container-select">
					<select name="<?php echo $widget->get_field_name( 'production_companies' ); ?>">
<?php foreach ( $widget->companies as $slug => $title ) : ?>
						<option value="<?php echo $slug; ?>" <?php selected( $production_companies, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

					</select>
				</div>

				<div class="wpmoly-movies-widget-meta-select wpmoly-movies-widget-select-certification<?php if ( 'certification' == $select_meta ) echo ' selected'; ?> redux-field-init redux-field-container redux-field redux-container-select">
					<select name="<?php echo $widget->get_field_name( 'certification' ); ?>">
<?php foreach ( $widget->certifications as $slug => $title ) : ?>
						<option value="<?php echo $slug; ?>" <?php selected( $certification, $slug ); ?>><?php _e( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>

					</select>
				</div>
			</div>
		</p>
		<p>
			<label for="<?php echo $widget->get_field_id( 'sort' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Sort movies ...', 'wpmovielibrary' ); ?></strong></label>
			<select class="widefat" id="<?php echo $widget->get_field_id( 'sort' ); ?>" name="<?php echo $widget->get_field_name( 'sort' ); ?>">
				<option value="ASC" <?php selected( $sort, 'ASC' ); ?>><?php _e( 'Ascending' ); ?></option>
				<option value="DESC" <?php selected( $sort, 'DESC' ); ?>><?php _e( 'Descending' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $widget->get_field_id( 'limit' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Number of movies to show', 'wpmovielibrary' ); ?></strong></label><br />
			<input id="<?php echo $widget->get_field_id( 'limit' ); ?>" name="<?php echo $widget->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $widget->get_field_id( 'show_rating' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Show movie rating', 'wpmovielibrary' ); ?></strong></label>
			<select class="widefat" id="<?php echo $widget->get_field_id( 'show_rating' ); ?>" name="<?php echo $widget->get_field_name( 'show_rating' ); ?>">
				<option value="no" <?php selected( $show_rating, 'no' ); ?>> <?php _e( 'No', 'wpmovielibrary' ); ?></option> 
				<option value="stars" <?php selected( $show_rating, 'stars' ); ?>> <?php _e( 'Stars only', 'wpmovielibrary' ); ?></option> 
				<option value="starsntext" <?php selected( $show_rating, 'starsntext' ); ?>> <?php _e( 'Stars and note', 'wpmovielibrary' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $widget->get_field_id( 'show_poster' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Show movie poster', 'wpmovielibrary' ); ?></strong></label>
			<select class="widefat" id="<?php echo $widget->get_field_id( 'show_poster' ); ?>" name="<?php echo $widget->get_field_name( 'show_poster' ); ?>">
				<option value="no" <?php selected( $show_poster, 'no' ); ?>> <?php _e( 'No', 'wpmovielibrary' ); ?></option> 
				<option value="small" <?php selected( $show_poster, 'small' ); ?>> <?php _e( 'Thumbnail' ); ?></option> 
				<option value="normal" <?php selected( $show_poster, 'normal' ); ?>> <?php _e( 'Normal', 'wpmovielibrary' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $widget->get_field_id( 'show_title' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Show movie title', 'wpmovielibrary' ); ?></strong></label>
			<select class="widefat" id="<?php echo $widget->get_field_id( 'show_title' ); ?>" name="<?php echo $widget->get_field_name( 'show_title' ); ?>">
				<option value="no" <?php selected( $show_title, 'no' ); ?>> <?php _e( 'No', 'wpmovielibrary' ); ?></option> 
				<option value="before" <?php selected( $show_title, 'before' ); ?>> <?php _e( 'Before poster', 'wpmovielibrary' ); ?></option> 
				<option value="after" <?php selected( $show_title, 'after' ); ?>> <?php _e( 'After poster', 'wpmovielibrary' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $widget->get_field_id( 'exclude_current' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Exclude current movie from results in single view', 'wpmovielibrary' ); ?></strong></label>
			<select class="widefat" id="<?php echo $widget->get_field_id( 'exclude_current' ); ?>" name="<?php echo $widget->get_field_name( 'exclude_current' ); ?>">
				<option value="yes" <?php selected( $exclude_current, 'yes' ); ?>> <?php _e( 'Yes', 'wpmovielibrary' ); ?></option> 
				<option value="no" <?php selected( $exclude_current, 'no' ); ?>> <?php _e( 'No', 'wpmovielibrary' ); ?></option>
			</select>
		</p>
	</div>
