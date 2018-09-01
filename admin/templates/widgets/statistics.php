<?php
/**
 * Statistics Widget admin template.
 *
 * @since 3.0.0
 *
 * @uses $widget
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
		<label for="<?php echo $widget->get_field_id( 'content' ); ?>"><strong class="wpmoly-widget-title"><?php _e( 'Format', 'wpmovielibrary' ); ?></strong></label>
		<em><?php _e( 'You can edit the form of the Widget’s content; basic HTML tags are allowed (ul, ol, li, p, span, em, i, p, strong, b, br). Format Tags are:', 'wpmovielibrary' ) ?></em>
	</p>
		<ul>
			<li><em><code>{total}</code>: <?php _e( 'Total number of Movies', 'wpmovielibrary' ) ?>.</em></li>
			<li><em><code>{genres}</code>: <?php _e( 'Number of Genres.', 'wpmovielibrary' ) ?></em></li>
			<li><em><code>{actors}</code>: <?php _e( 'Number of Actors.', 'wpmovielibrary' ) ?></em></li>
		</ul>
		<textarea class="widefat" id="<?php echo $widget->get_field_id( 'content' ); ?>" name="<?php echo $widget->get_field_name( 'content' ); ?>"><?php
			echo wp_kses( $widget->get_attr( 'content' ), array(
				'ul'     => array(),
				'ol'     => array(),
				'li'     => array(),
				'p'      => array(),
				'span'   => array(),
				'em'     => array(),
				'i'      => array(),
				'p'      => array(),
				'strong' => array(),
				'b'      => array(),
				'br'     => array(),
			) ); ?></textarea>
