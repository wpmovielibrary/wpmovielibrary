
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
					<input type="hidden" name="letter" value="<?php echo $current; ?>" />
					<input type="submit" value="" style="display:none" />
					<ul id="wpmoly-movie-grid-menu-2" class="wpmoly movies grid menu list">
						<li id="wpmoly-movie-grid-menu-item-alpha-asc" class="wpmoly movies grid menu list item<?php if ( 'ASC' == $order ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'order' => 'ASC' ), $default_url ) ?>" title="<?php _e( 'List ascendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-asc"></span></a></li>
						<li id="wpmoly-movie-grid-menu-item-alpha-desc" class="wpmoly movies grid menu list item<?php if ( 'DESC' == $order ) echo ' active'; ?>"><a href="<?php echo add_query_arg( array( 'order' => 'DESC' ), $default_url ) ?>" title="<?php _e( 'List descendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-desc"></span></a></li>
 <?php if ( '1' == $editable ) : ?>
						<li id="wpmoly-movie-grid-menu-item-count" class="wpmoly movies grid menu list item"><?php _e( 'Items per page:', 'wpmovielibrary' ) ?> <input name="number" type="text" size="3" maxlength="3" placeholder="50" value="<?php echo $number; ?>" /></li>
						<li id="wpmoly-movie-grid-menu-item-columns" class="wpmoly movies grid menu list item"><?php _e( 'Columns:', 'wpmovielibrary' ) ?> <input name="columns" type="text" size="3" maxlength="3" placeholder="4" value="<?php echo $columns; ?>" /></li>

<?php endif; ?>
					</ul>
				</form>
