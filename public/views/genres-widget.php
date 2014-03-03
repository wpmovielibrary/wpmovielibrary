<?php
$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$list  = $instance['list'];
$count = $instance['count'];
$css   = ( 1 == $instance['css'] ? ' class="custom"' : '' );
$genres = get_terms( array( 'genre' ) );
?>
		<?php echo $title; ?>
<?php
if ( $genres && ! is_wp_error( $genres ) ) :

	if ( 1 == $list ) {
		$before_list  = "\t\t".'<select id="genres-list"'.$css.'>';
		$before_list .= sprintf( '<option value="">%s</option>', __( 'Select a Genre', 'wpml' ) );
		$after_list   = "\n\t\t".'</select>'."\n";
	}
	else {
		$before_list = "\t\t".'<ul>';
		$after_list  = "\n\t\t".'</ul>'."\n";
	}

	echo $before_list;

	foreach ( $genres as $genre ) :

		$count = ( 1 == $count ? sprintf( '&nbsp;(%d)', $genre->count ) : '' );
		$link  = get_term_link( sanitize_term( $genre, 'genre' ), 'genre' );
		
		if ( 1 == $list ) {
			printf( '<option value="%s">%s%s</option>', $link, $genre->name, $count );
		}
		else {
			printf( '<li><a href="%s" title="%s">%s</a>%s</li>', $link, sprintf( __( 'View all movies from "%s" Genre', 'wpml' ), $genre->name ), $genre->name, $count );
		}

	endforeach;

	echo $after_list;
else :
	printf( '<em>%s</em>', __( 'Nothing to display for "Genre" taxonomy.', 'wpml' ) );
endif; ?>