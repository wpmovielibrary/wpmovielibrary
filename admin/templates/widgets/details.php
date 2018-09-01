<?php
/**
 * Statistics Widget admin template.
 *
 * @since 3.0.0
 *
 * @uses $widget
 * @uses $data
 */

?>

	<p>
		<label for="<?php echo $widget->get_field_id( 'title' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Title', 'wpmovielibrary' ); ?></strong></label> 
		<input class="widefat" id="<?php echo $widget->get_field_id( 'title' ); ?>" name="<?php echo $widget->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $widget->get_attr( 'title' ) ); ?>" />
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'description' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Description', 'wpmovielibrary' ); ?></strong></label> 
		<textarea class="widefat" id="<?php echo $widget->get_field_id( 'description' ); ?>" name="<?php echo $widget->get_field_name( 'description' ); ?>"><?php echo esc_textarea( $widget->get_attr( 'description' ) ); ?></textarea>
	</p>
	<p>
		<label for="<?php echo $widget->get_field_id( 'detail' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Detail', 'wpmovielibrary' ); ?></strong></label>
		<select class="widefat" id="<?php echo $widget->get_field_id( 'detail' ); ?>" name="<?php echo $widget->get_field_name( 'detail' ); ?>">
<?php foreach ( $data['details'] as $slug => $detail ) : ?>
			<option value="<?php echo $slug ?>" <?php selected( $slug, $widget->get_attr( 'detail' ) ); ?>><?php echo esc_html( $detail, 'wpmovielibrary' ); ?></option>

<?php endforeach; ?>
		</select>
	</p>
