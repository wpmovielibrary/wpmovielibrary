<?php
/**
 * Movie Metadata view Template
 * 
 * Showing a movie's head box, IMDb style.
 * 
 * @since    2.1.4
 * 
 * @uses    
 */
?>

	<div id="movie-headbox-<?php echo $id ?>" class="wpmoly block headbox imdb contained <?php //echo $theme ?>">
		<div class="wpmoly headbox imdb movie">
			<div class="wpmoly headbox imdb movie poster">
				<img src="<?php echo $poster; ?>" alt="<?php printf( '%s (%d) %s', $meta['title'], $meta['year'], __( 'Poster', 'wpmovielibrary' ) ); ?>" title="<?php printf( '%s (%d) %s', $meta['title'], $meta['_year'], __( 'Poster', 'wpmovielibrary' ) ); ?>" />
			</div>
			<h2 class="wpmoly headbox imdb movie meta title"><?php echo $meta['title']; ?> <span class="wpmoly headbox imdb movie meta year">(<?php echo $meta['year']; ?>)</span></h2>
			<div class="wpmoly headbox imdb movie section ">
				<span class="wpmoly headbox imdb movie meta certification"><?php echo $meta['certification']; ?></span>
				<span class="wpmoly headbox imdb movie meta runtime"><?php echo $meta['_runtime']; ?> min</span> - 
				<span class="wpmoly headbox imdb movie meta genres"><?php echo $meta['genres']; ?></span> - 
				<span class="wpmoly headbox imdb movie meta release_date"><?php echo $meta['release_date']; ?></span>
			</div>
			<hr />
			<div class="wpmoly headbox imdb movie section rating">
				<span class="wpmoly headbox imdb movie rating starlined">Your rating: <?php echo $details['rating_stars']; ?></span>
			</div>
			<hr />
			<div class="wpmoly headbox imdb movie section overview">
				<p><?php echo $meta['overview']; ?></p>
				<div class="wpmoly headbox imdb movie meta director">Director: <?php echo $meta['director']; ?></div>
				<div class="wpmoly headbox imdb movie meta writer">Writers: <?php echo $meta['writer']; ?></div>
				<div class="wpmoly headbox imdb movie meta cast">Stars: <?php echo $meta['cast']; ?></div>
			</div>
			<hr />
<?php if ( ! empty( $images ) ) : ?>
			<div class="wpmoly headbox imdb movie section images">
				<h3 class="wpmoly headbox imdb movie meta sub-title"><?php _e( 'Photos', 'wpmovielibrary' ); ?></h3>
				<?php echo $images; ?>
			</div>
			<hr />
<?php endif; ?>
			<div class="wpmoly headbox imdb movie section storyline">
				<h3 class="wpmoly headbox imdb movie meta sub-title">Storyline</h3>
				<p class="wpmoly headbox imdb movie meta overview"><?php echo $meta['overview']; ?></p>
			</div>
			<div class="wpmoly headbox imdb movie section tagline">
				<h4 class="wpmoly headbox imdb movie meta sub-title">Tagline</h4>
				<p class="wpmoly headbox imdb movie meta tagline"><?php echo $meta['tagline']; ?></p>
			</div>
			<div class="wpmoly headbox imdb movie section genres">
				<h4 class="wpmoly headbox imdb movie meta sub-title">Genres</h4>
				<p class="wpmoly headbox imdb movie meta genres"><?php echo $meta['genres']; ?></p>
			</div>
			<hr />
			<div class="wpmoly headbox imdb movie section production-details">
				<h3 class="wpmoly headbox imdb movie meta sub-title">Details</h3>
				<div class="wpmoly headbox imdb movie meta homepage">Official Site: <?php echo $meta['homepage']; ?></div>
				<div class="wpmoly headbox imdb movie meta production_countries">Country: <?php echo $meta['production_countries']; ?></div>
				<div class="wpmoly headbox imdb movie meta spoken_languages">Language: <?php echo $meta['spoken_languages']; ?></div>
				<div class="wpmoly headbox imdb movie meta release_date">Release Date: <?php echo $meta['release_date']; ?></div>
			</div>
			<hr />
			<div class="wpmoly headbox imdb movie section box-office">
				<h3 class="wpmoly headbox imdb movie meta sub-title">Box Office</h3>
				<div class="wpmoly headbox imdb movie meta budget">Budget: <?php echo $meta['budget']; ?></div>
				<div class="wpmoly headbox imdb movie meta revenue">Revenue: <?php echo $meta['revenue']; ?></div>
			</div>
			<hr />
			<div class="wpmoly headbox imdb movie section companies">
				<h3 class="wpmoly headbox imdb movie meta sub-title">Company Credits</h3>
				<div class="wpmoly headbox imdb movie meta production_companies">Production Co: <?php echo $meta['production_companies']; ?></div>
			</div>
			<hr />
			<div class="wpmoly headbox imdb movie section tech-specs">
				<h3 class="wpmoly headbox imdb movie meta sub-title">Technical Specs</h3>
				<div class="wpmoly headbox imdb movie meta runtime">Runtime: <?php echo $meta['runtime']; ?></div>
			</div>
		</div>
		<div style="clear:both"></div>
	</div>
