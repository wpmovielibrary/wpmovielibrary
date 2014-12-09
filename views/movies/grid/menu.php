
				<ul id="wpmoly-movie-grid-menu" class="wpmoly movies grid menu list">

<?php
$current = $letter;
foreach ( $default as $l ) :
	$_url = str_replace( '{letter}', $l, $urls['letter'] );
?>
					<li id="wpmoly-movie-grid-menu-item-<?php echo $l ?>" class="wpmoly movies grid menu list item<?php if ( strtolower( $l ) == strtolower( $current ) ) echo ' active'; ?>"><?php if ( in_array( $l, $letters ) ) { ?><a href="<?php echo $_url; ?>"><?php echo $l; ?></a><?php } else { echo $l; } ?></li>

<?php endforeach; ?>
					<li id="wpmoly-movie-grid-menu-item-all" class="wpmoly movies grid menu list item<?php if ( 'all' == $current ) echo ' active'; ?>"><a href="<?php echo  $urls['all']; ?>"><?php _e( 'All', 'wpmovielibrary' ) ?></a></li>
				</ul>

				<form action="">
					<input type="hidden" name="order" value="<?php echo $order; ?>" />
					<input type="hidden" name="letter" value="<?php echo $current; ?>" />
					<input type="hidden" name="meta" value="<?php echo $meta; ?>" />
					<input type="hidden" name="detail" value="<?php echo $detail; ?>" />
					<input type="hidden" name="value" value="<?php echo $value; ?>" />
					<input type="submit" value="" style="display:none" />
					<ul id="wpmoly-movie-grid-menu-2" class="wpmoly movies grid menu list">
						<li id="wpmoly-movie-grid-menu-item-alpha-asc" class="wpmoly movies grid menu list item<?php if ( 'ASC' == $order ) echo ' active'; ?>"><a href="<?php echo str_replace( array( 'DESC', 'desc' ), 'ASC', $urls['asc'] ) ?>" title="<?php _e( 'List ascendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-asc"></span></a></li>
						<li id="wpmoly-movie-grid-menu-item-alpha-desc" class="wpmoly movies grid menu list item<?php if ( 'DESC' == $order ) echo ' active'; ?>"><a href="<?php echo str_replace( array( 'ASC', 'asc' ), 'DESC', $urls['desc'] ) ?>" title="<?php _e( 'List descendingly', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-desc"></span></a></li>
 <?php if ( '1' == $editable ) : ?>
						<li id="wpmoly-movie-grid-menu-item-count" class="wpmoly movies grid menu list item"><?php _e( 'Items per page:', 'wpmovielibrary' ) ?> <input name="number" type="text" size="3" maxlength="3" placeholder="50" value="<?php echo $number; ?>" /></li>
						<li id="wpmoly-movie-grid-menu-item-columns" class="wpmoly movies grid menu list item"><?php _e( 'Columns:', 'wpmovielibrary' ) ?> <input name="columns" type="text" size="3" maxlength="3" placeholder="4" value="<?php echo $columns; ?>" /></li>

<?php endif; ?>
					</ul>
				</form>
