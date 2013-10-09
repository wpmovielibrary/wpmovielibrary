<?php
$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$list  = $instance['list'];
$count = $instance['count'];
$css   = ( 1 == $instance['css'] ? ' class="custom"' : '' );
$actors = get_terms( array( 'actor' ) );
?>
		<?php echo $title; ?>
<?php
if ( $actors && ! is_wp_error( $actors ) ) :

	if ( 1 == $list ) {
		$before_list  = "\t\t".'<select id="actors-list"'.$css.'>';
		$before_list .= sprintf( '<option value="">%s</option>', __( 'Select an Actor', 'wpml' ) );
		$after_list   = "\n\t\t".'</select>'."\n\t\t".'<script type="text/javascript">/* <![CDATA[ */ var cdd = document.getElementById("actors-list"); function onActorChange() { if ( cdd.options[cdd.selectedIndex].value.length > 0 ) { location.href = "' . home_url( '?actor=' ) . '"+cdd.options[cdd.selectedIndex].value; } } cdd.onchange = onActorChange; /* ]]> */</script>'."\n";
	}
	else {
		$before_list = "\t\t".'<ul>';
		$after_list  = "\n\t\t".'</ul>'."\n";
	}

	echo $before_list;

	foreach ( $actors as $actor ) :

		$count = ( 1 == $count ? sprintf( '&nbsp;(%d)', $actor->count ) : '' );
		
		if ( 1 == $list ) {
			printf( '<option value="%s">%s%s</option>', $actor->slug, $actor->name, $count );
		}
		else {
			printf( '<li><a href="%s" title="%s">%s</a>%s</li>', get_term_link( $actor ), sprintf( __( 'View all movies staring "%s"', 'wpml' ), $actor->name ), $actor->name, $count );
		}

	endforeach;

	echo $after_list;
else :
	printf( '<em>%s</em>', __( 'Nothing to display for "Actor" taxonomy.', 'wpml' ) );
endif; ?>