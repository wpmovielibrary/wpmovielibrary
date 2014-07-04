<div class="wpml_shortcodes wpml_movies">
<?php
if ( ! empty( $movies ) ) :
	foreach ( $movies as $movie ) :
?>
	<div class="wpml_movie">
		<div class="wpml_movie_poster">
			<?php echo $movie['poster']; ?>
		</div>

		<h4><?php echo $movie['title']; ?></h4>

		<div class="wpml_movie_meta">
			<?php echo $movie['meta']; ?>
		</div>

		<div class="wpml_movie_details">
			<?php echo $movie['details']; ?>
		</div>
	</div>

<?php
	endforeach;
endif;
?>
</div>