<?php
/**
 * Movies-by-Rating Default Template
 * 
 * Display a list of movies links with thumbnails
 * 
 * @since    1.2
 * 
 * @uses    $style current Widget style
 * @uses    $description Widget's description
 * @uses    $items array of movies
 * @uses    $show_poster which way to show posters
 * @uses    $show_title which way to show titles
 * @uses    $show_rating which way to show ratings
 */
?>

	<div class="<?php echo $style ?>">

<?php if ( '' != $description ) : ?>
		<div class="wpmoly movie description"><?php echo $description ?></div>
<?php endif; ?>

<?php foreach ( $items as $item ) : ?>
		<a class="wpmoly movie link" href="<?php echo $item['link'] ?>" title="<?php echo __( 'Read more about', 'wpmovielibrary' ) . ' ' . $item['title'] ?>">
			<figure id="movie-<?php echo $item['ID']; ?>" class="wpmoly movie">
<?php if ( 'no' == $show_poster || 'before' == $show_title ) : ?>
				<div id="movie-<?php echo $item['ID']; ?>-title" class="wpmoly movie title"><?php echo $item['title'] ?></div>
<?php endif; ?>

<?php if ( 'small' == $show_poster || 'normal' == $show_poster ) : ?>
				<?php echo $item['thumbnail']; ?>
<?php endif; ?>

<?php if ( 'no' != $show_rating ) : ?>
				<div class="wpmoly movie rating"><?php echo $item['_rating'] ?><?php if ( 'starsntext' == $show_rating && '0.0' != $item['rating'] ) echo '<span class="wpmoly movie rating label">' . $item['rating'] . '/5</span>' ?></div>
<?php endif; ?>

<?php if ( 'after' == $show_title ) : ?>
				<div id="movie-<?php echo $item['ID']; ?>-title" class="wpmoly movie title"><?php echo $item['title'] ?></div>
<?php endif; ?>

			</figure>
		</a>

<?php endforeach; ?>
	</div>
