
		<?php echo $title; ?>
<?php
if ( $actors && ! is_wp_error( $actors ) ) :

	$items = array();

	foreach ( $actors as $actor )
		$items[] = array(
			'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $actor->name ),
			'link'        => get_term_link( sanitize_term( $actor, 'actor' ), 'actor' ),
			'title'       => esc_attr( $actor->name . ( 1 == $count ? sprintf( '&nbsp;(%d)', $actor->count ) : '' ) )
		);

	if ( $limit )
		$items[] = array(
			'attr_title'  => __( 'View all actors', WPML_SLUG ),
			'link'        => home_url( '/' . $archive ),
			'title'       => __( 'View the complete list', WPML_SLUG )
		);

	$html = apply_filters( 'wpml_format_widget_lists', $items, array( 'dropdown' => $list, 'styling' => $css, 'title' => __( 'Select an Actor', WPML_SLUG ) ) );

	echo $html;
else :
	printf( '<em>%s</em>', __( 'Nothing to display for "Actor" taxonomy.', WPML_SLUG ) );
endif; ?>