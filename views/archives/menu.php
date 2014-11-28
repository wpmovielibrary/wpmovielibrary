
				<ul id="wpmoly-movie-grid-menu" class="wpmoly movies grid menu list">

<?php
$current = $letter;
foreach ( $default as $_letter ) :
	$_url = add_query_arg( array( 'letter' => $_letter ), $letter_url );
?>
					<li id="wpmoly-movie-grid-menu-item-<?php echo $_letter ?>" class="wpmoly movies grid menu list item<?php if ( strtolower( $_letter ) == strtolower( $current ) ) echo ' active'; ?>"><?php if ( in_array( $_letter, $letters ) ) { ?><a href="<?php echo $_url; ?>"><?php echo $_letter; ?></a><?php } else { echo $_letter; } ?></li>

<?php endforeach; ?>
					<li id="wpmoly-movie-grid-menu-item-all" class="wpmoly movies grid menu list item<?php if ( 'all' == $current ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'letter' => '', 'number' => -1 ), $default_url ) ?>"><?php _e( 'All', 'wpmovielibrary' ) ?></a></li>
				</ul>

				<form action="">
					<input type="hidden" name="order" value="<?php echo $order; ?>" />
					<input type="hidden" name="orderby" value="<?php echo $orderby; ?>" />
					<input type="hidden" name="letter" value="<?php echo $current; ?>" />
					<input type="submit" value="" style="display:none" />
					<ul id="wpmoly-movie-grid-menu-2" class="wpmoly movies grid menu list">
						<li id="wpmoly-movie-grid-menu-item-alpha-asc" class="wpmoly movies grid menu list item<?php if ( 'ASC' == $order && 'title' == $orderby ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'order' => 'ASC', 'orderby' => 'title' ), $default_url ) ?>" title="<?php _e( 'List alphabetically, ascendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-asc"></span></a></li>
						<li id="wpmoly-movie-grid-menu-item-alpha-desc" class="wpmoly movies grid menu list item<?php if ( 'DESC' == $order && 'title' == $orderby ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'order' => 'DESC', 'orderby' => 'title' ), $default_url ) ?>" title="<?php _e( 'List alphabetically, descendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-desc"></span></a></li>
						<li id="wpmoly-movie-grid-menu-item-numeric-asc" class="wpmoly movies grid menu list item<?php if ( 'ASC' == $order && 'count' == $orderby ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'order' => 'ASC', 'orderby' => 'count' ), $default_url ) ?>" title="<?php _e( 'List by movies count, ascendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-numeric-asc"></span></a></li>
						<li id="wpmoly-movie-grid-menu-item-numeric-desc" class="wpmoly movies grid menu list item<?php if ( 'DESC' == $order && 'count' == $orderby ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'order' => 'DESC', 'orderby' => 'count' ), $default_url ) ?>" title="<?php _e( 'List by movies count, descendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-numeric-desc"></span></a></li>
 <?php if ( '1' == $editable ) : ?>
						<li id="wpmoly-movie-grid-menu-item-count" class="wpmoly movies grid menu list item"><?php _e( 'Items per page:', 'wpmovielibrary' ) ?> <input name="number" type="text" size="3" maxlength="3" placeholder="50" value="<?php echo $number; ?>" /></li>

<?php endif; ?>
					</ul>
				</form>
