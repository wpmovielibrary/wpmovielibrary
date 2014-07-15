
		<?php echo $title; ?>
<?php
if ( $collections && ! is_wp_error( $collections ) ) :

	$items = array();

	foreach ( $collections as $collection )
		$items[] = array(
			'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $collection->name ),
			'link'        => get_term_link( sanitize_term( $collection, 'collection' ), 'collection' ),
			'title'       => esc_attr( $collection->name . ( $count ? sprintf( '&nbsp;(%d)', $collection->count ) : '' ) )
		);

	if ( $limit )
		$items[] = array(
			'attr_title'  => __( 'View all collections', WPML_SLUG ),
			'link'        => home_url( '/' . $archive ),
			'title'       => __( 'View the complete list', WPML_SLUG )
		);

	$html = apply_filters( 'wpml_format_widget_lists', $items, array( 'dropdown' => $list, 'styling' => $css, 'title' => __( 'Select a Collection', WPML_SLUG ) ) );

	echo $html;
else :
	printf( '<em>%s</em>', __( 'Nothing to display for "Collection" taxonomy.', WPML_SLUG ) );
endif; ?>