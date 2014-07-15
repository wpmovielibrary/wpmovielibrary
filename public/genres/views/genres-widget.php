
		<?php echo $title; ?>
<?php
if ( $genres && ! is_wp_error( $genres ) ) :

	$items = array();

	foreach ( $genres as $genre )
		$items[] = array(
			'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $genre->name ),
			'link'        => get_term_link( sanitize_term( $genre, 'genre' ), 'genre' ),
			'title'       => esc_attr( $genre->name . ( 1 == $count ? sprintf( '&nbsp;(%d)', $genre->count ) : '' ) )
		);

	$html = apply_filters( 'wpml_format_widget_lists', $items, array( 'dropdown' => $list, 'styling' => $css, 'title' => __( 'Select a Genre', WPML_SLUG ) ) );

	echo $html;
else :
	printf( '<em>%s</em>', __( 'Nothing to display for "Genre" taxonomy.', WPML_SLUG ) );
endif; ?>