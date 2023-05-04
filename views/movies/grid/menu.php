
				<ul id="wpmoly-movie-grid-menu" class="wpmoly movies grid menu list<?php echo $theme; ?>">

<?php
$current = $letter;
foreach ( $default as $l ) :
	$_url = str_replace( ':letter:', $l, $urls['letter'] );
?>
					<li id="wpmoly-movie-grid-menu-item-<?php echo $l ?>" class="wpmoly movies grid menu list item<?php if ( strtolower( $l ) == strtolower( $current ) ) echo ' active'; ?>"><?php if ( in_array( $l, $letters ) ) { ?><a href="<?php echo $_url; ?>"><?php echo $l; ?></a><?php } else { echo $l; } ?></li>

<?php endforeach; ?>
					<li id="wpmoly-movie-grid-menu-item-all" class="wpmoly movies grid menu list item<?php if ( 'all' == $current ) echo ' active'; ?>"><a href="<?php echo  $urls['all']; ?>"><?php _e( 'All', 'wpmovielibrary' ) ?></a></li>
				</ul>

				<form id="wpmoly-grid-form" action="">
					<input type="submit" value="" style="display:none" />
					<ul id="wpmoly-movie-grid-menu-2" class="wpmoly movies grid menu list<?php echo $theme; ?>">
						<li id="wpmoly-movie-grid-menu-item-alpha-asc" class="wpmoly movies grid menu list item<?php if ( 'ASC' == $order ) echo ' active'; ?>"><a href="<?php echo str_replace( array( 'DESC', 'desc' ), 'ASC', $urls['asc'] ) ?>" title="<?php _e( 'List ascendingly alphabetically', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-asc"></span></a></li>
						<li id="wpmoly-movie-grid-menu-item-alpha-desc" class="wpmoly movies grid menu list item<?php if ( 'DESC' == $order ) echo ' active'; ?>"><a href="<?php echo str_replace( array( 'ASC', 'asc' ), 'DESC', $urls['desc'] ) ?>" title="<?php _e( 'List descendingly alphabetically', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-sort-alpha-desc"></span></a></li>
<?php if ( '1' == $editable ) : ?>
						<li id="wpmoly-movie-grid-menu-item-list" class="wpmoly movies grid menu list item<?php if ( 'list' == $view ) echo ' active'; ?>"><a href="<?php echo $urls['list']; ?>" title="<?php _e( 'Show movies as an extended list', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-align-justify"></span></a></li>
						<li id="wpmoly-movie-grid-menu-item-archives" class="wpmoly movies grid menu list item<?php if ( 'archives' == $view ) echo ' active'; ?>"><a href="<?php echo $urls['archives']; ?>" title="<?php _e( 'Show movies as a list of titles', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-th-list"></span></a></li>
						<li id="wpmoly-movie-grid-menu-item-grid" class="wpmoly movies grid menu list item<?php if ( 'grid' == $view ) echo ' active'; ?>"><a href="<?php echo $urls['grid']; ?>" title="<?php _e( 'Show movies as a poster grid', 'wpmovielibrary' ) ?>"><span class="wpmolicon icon-grid"></span></a></li>

<?php endif; ?>

					</ul>
				</form>
