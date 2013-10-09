<?php
$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$list  = $instance['list'];
$count = $instance['count'];
$css   = $instance['css'];
$css   = ( 1 == $instance['css'] ? ' class="custom"' : '' );
$collections = get_terms( array( 'collection' ) );
?>
		<?php echo $title; ?>
<?php
if ( $collections && ! is_wp_error( $collections ) ) :

	if ( 1 == $list ) {
		$before_list  = "\t\t".'<select id="collections-list"'.$css.'>';
		$before_list .= sprintf( '<option value="">%s</option>', __( 'Select a Collection', 'wpml' ) );
		$after_list   = "\n\t\t".'</select>'."\n\t\t".'<script type="text/javascript">/* <![CDATA[ */ var cdd = document.getElementById("collections-list"); function onCollectionChange() { if ( cdd.options[cdd.selectedIndex].value.length > 0 ) { location.href = "' . home_url( '?collection=' ) . '"+cdd.options[cdd.selectedIndex].value; } } cdd.onchange = onCollectionChange; /* ]]> */</script>'."\n";
	}
	else {
		$before_list = "\t\t".'<ul>';
		$after_list  = "\n\t\t".'</ul>'."\n";
	}

	echo $before_list;

	foreach ( $collections as $collection ) :

		$count = ( 1 == $count ? sprintf( '&nbsp;(%d)', $collection->count ) : '' );
		
		if ( 1 == $list ) {
			printf( '<option value="%s">%s%s</option>', $collection->slug, $collection->name, $count );
		}
		else {
			printf( '<li><a href="%s" title="%s">%s</a>%s</li>', get_term_link( $collection ), sprintf( __( 'View all movies from "%s" Collection', 'wpml' ), $collection->name ), $collection->name, $count );
		}

	endforeach;

	echo $after_list;
else :
	printf( '<em>%s</em>', __( 'Nothing to display for "Collection" taxonomy.', 'wpml' ) );
endif; ?>