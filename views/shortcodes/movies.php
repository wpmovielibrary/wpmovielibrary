<?php
/**
 * Movies Shortcode view Template
 * 
 * Showing a list of movies.
 * 
 * @since    1.2
 * 
 * @uses    $movies
 */
?>

	<div class="wpml_shortcodes wpml_movies">
<?php foreach ( $movies as $movie ) : ?>
		<div class="wpml_movie">
			<div class="wpml_movie_poster">
				<a href="<?php echo $movie['url']; ?>"><?php echo $movie['poster']; ?></a>
			</div>

			<a href="<?php echo $movie['url']; ?>"><h4><?php echo $movie['title']; ?></h4></a>

			<div class="wpml_movie_meta">
<?php
	if ( ! is_null( $movie['meta'] ) ) :
		foreach ( $movie['meta'] as $slug => $meta ) :
?>
				<dt class="wpml_<?php echo $slug ?>_field_title"><?php echo $meta['title'] ?></dt>
				<dd class="wpml_<?php echo $slug ?>_field_value"><?php echo $meta['value'] ?></dd>
<?php
		endforeach;
	endif;
?>
			</div>

			<div class="wpml_movie_details">
<?php
	if ( ! is_null( $movie['details'] ) )
			echo $movie['details'];
?>
			</div>
		</div>

<?php endforeach; ?>
	</div>
