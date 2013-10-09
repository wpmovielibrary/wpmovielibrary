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
		$after_list   = "\n\t\t".'</select>'."\n\t\t".'<script type="text/javascript">/* <![CDATA[ */ var cdd = document.getElementById("genres-list"); function onGenreChange() { if ( cdd.options[cdd.selectedIndex].value.length > 0 ) { location.href = "' . home_url( '?genre=' ) . '"+cdd.options[cdd.selectedIndex].value; } } cdd.onchange = onGenreChange; /* ]]> */</script>'."\n";
	}
	else {
		$before_list = "\t\t".'<ul>';
		$after_list  = "\n\t\t".'</ul>'."\n";
	}

	echo $before_list;

	foreach ( $genres as $genre ) :

		$count = ( 1 == $count ? sprintf( '&nbsp;(%d)', $genre->count ) : '' );
		
		if ( 1 == $list ) {
			printf( '<option value="%s">%s%s</option>', $genre->slug, $genre->name, $count );
		}
		else {
			printf( '<li><a href="%s" title="%s">%s</a>%s</li>', get_term_link( $genre ), sprintf( __( 'View all movies from "%s" Genre', 'wpml' ), $genre->name ), $genre->name, $count );
		}

	endforeach;

	echo $after_list;
else :
	printf( '<em>%s</em>', __( 'Nothing to display for "Genre" taxonomy.', 'wpml' ) );
endif; ?>