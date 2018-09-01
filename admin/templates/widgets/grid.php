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
		<label for="<?php echo $widget->get_field_id( 'grid_id' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Grid', 'wpmovielibrary' ); ?></strong></label>
		<select class="widefat" id="<?php echo $widget->get_field_id( 'grid_id' ); ?>" name="<?php echo $widget->get_field_name( 'grid_id' ); ?>">
			<option value=""><?php echo esc_html__( 'Select a Grid', 'wpmovielibrary' ); ?></option>
<?php foreach ( $data['grids'] as $grid ) : ?>
			<option value="<?php echo $grid->ID ?>" <?php selected( $grid->ID, $widget->get_attr( 'grid_id' ) ); ?>><?php echo esc_html( $grid->post_title ); ?></option>

<?php endforeach; ?>
		</select>
		<p><?php printf( esc_html__( 'Select a Grid to show in the Widget. Or maybe %s?', 'wpmovielibrary' ), sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( admin_url( 'post-new.php?post_type=grid' ) ), __( 'create a new one', 'wpmovielibrary' ) ) ) ?></p>
	</p>
