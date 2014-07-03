<div class="wpml_shortcodes wpml_movies">
<?php
if ( $query->have_posts() ) :
	while ( $query->have_posts() ) :
		$query->the_post();
?>
	<div class="wpml_movie">
		<div class="wpml_movie_poster">
			
		</div>

		<h4><?php the_title(); ?></h4>

		<div class="wpml_movie_meta"></div>

		<div class="wpml_movie_details"></div>
	</div>

<?php
	endwhile;
endif;
?>
</div>