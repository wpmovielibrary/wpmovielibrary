
		<?php echo $title; ?>
<?php

if ( $status_only ) :

	$status = WPML_Settings::get_available_movie_status();
	$movies = WPML_Settings::wpml__movie_rewrite();
	$rewrite = WPML_Settings::wpml__details_rewrite();

	if ( ! empty( $status ) ) :

		$items = array();

		foreach ( $status as $slug => $status_title ) :
			$_slug = ( $rewrite ? __( $slug, WPML_SLUG ) : $slug );
			$items[] = array(
				'ID'          => $slug,
				'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $status_title ),
				'link'        => home_url( "/{$movies}/{$_slug}/" ),
				'title'       => esc_attr( $status_title ),
			);
		endforeach;

		$html = apply_filters( 'wpml_format_widget_lists', $items, array( 'dropdown' => $list, 'styling' => $css, 'title' => __( 'Select a status', WPML_SLUG ) ) );

		echo $html;
	else :
		printf( '<em>%s</em>', __( 'Nothing to display.', WPML_SLUG ) );
	endif;

else :
	$movies = WPML_Movies::get_movies_from_status();

	if ( ! empty( $movies ) ) :

		$items = array();

		foreach ( $movies as $movie )
			$items[] = array(
				'ID'          => $movie->ID,
				'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $movie->post_title ),
				'link'        => get_permalink( $movie->ID ),
				'title'       => esc_attr( $movie->post_title ),
			);

		if ( $thumbnails )
			$html = apply_filters( 'wpml_format_widget_lists_thumbnails', $items );
		else
			$html = apply_filters( 'wpml_format_widget_lists', $items, $list, $css, __( 'Select a Movie', WPML_SLUG ) );

		echo $html;
	else :
		printf( '<em>%s</em>', __( 'Nothing to display.', WPML_SLUG ) );
	endif;
endif;
?>