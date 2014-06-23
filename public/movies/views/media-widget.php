<?php
$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$description = $instance['description'];
$type = $instance['type'];
$list = ( 1 == $instance['list'] ? true : false );
$css = ( 1 == $instance['css'] ? true : false );
$thumbnails = ( 1 == $instance['thumbnails'] ? true : false );
$media_only = ( 1 == $instance['media_only'] ? true : false );
//$show_icons = ( 1 == $instance['show_icons'] ? true : false );

?>
		<?php echo $title; ?>
<?php

if ( $media_only ) :

	$media = WPML_Settings::get_available_movie_media();
	$movies = WPML_Settings::wpml__movie_rewrite();
	$rewrite = WPML_Settings::wpml__details_rewrite();

	if ( ! empty( $media ) ) :

		$items = array();

		foreach ( $media as $slug => $media_title ) :
			$_slug = ( $rewrite ? __( $slug, WPML_SLUG ) : $slug );
			$items[] = array(
				'ID'          => $slug,
				'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $media_title ),
				'link'        => home_url( "/{$movies}/{$_slug}/" ),
				'title'       => $media_title,
			);
		endforeach;

		$html = apply_filters( 'wpml_format_widget_lists', $items, array( 'dropdown' => $list, 'styling' => $css, 'title' => __( 'Select a media', WPML_SLUG ) ) );

		echo $html;
	else :
		printf( '<em>%s</em>', __( 'Nothing to display.', WPML_SLUG ) );
	endif;

else :

	$movies = WPML_Movies::get_movies_from_media( $type );
	if ( ! empty( $movies ) ) :

		$items = array();

		foreach ( $movies as $movie )
			$items[] = array(
				'ID'          => $movie->ID,
				'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $movie->post_title ),
				'link'        => get_permalink( $movie->ID ),
				'title'       => $movie->post_title,
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