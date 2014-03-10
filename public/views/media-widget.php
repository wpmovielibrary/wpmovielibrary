<?php
$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$description = $instance['description'];
$type = $instance['type'];
$list = ( 1 == $instance['list'] ? true : false );
$css = ( 1 == $instance['css'] ? true : false );
$thumbnails = ( 1 == $instance['thumbnails'] ? true : false );

$movies = apply_filters( 'wpml_get_movies_from_media', $type );
?>
		<?php echo $title; ?>
<?php
if ( ! empty( $movies ) ) :

	$items = array();

	foreach ( $movies as $movie )
		$items[] = array(
			'ID'          => $movie->ID,
			'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpml' ), $movie->post_title ),
			'link'        => get_permalink( $movie->ID ),
			'title'       => $movie->post_title,
		);

	if ( $thumbnails )
		$html = apply_filters( 'wpml_format_widget_lists_thumbnails', $items );
	else
		$html = apply_filters( 'wpml_format_widget_lists', $items, $list, $css, __( 'Select a Movie', 'wpml' ) );

	echo $html;
else :
	printf( '<em>%s</em>', __( 'Nothing to display.', 'wpml' ) );
endif; ?>