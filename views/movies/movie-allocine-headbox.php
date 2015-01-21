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

	<div id="movie-headbox-<?php echo $id ?>" class="wpmoly block headbox allocine contained <?php //echo $theme ?>">
		<div class="wpmoly headbox allocine movie">
			<div class="wpmoly headbox allocine movie section title">
<?php if ( ! empty( $meta['title'] ) ) : ?>
				<h2 class="wpmoly headbox allocine movie meta title"><?php echo $meta['title']; ?></h2>
<?php endif; ?>
			</div>
			<div class="wpmoly headbox allocine movie menu">
				<ul>
					<li><a href="#"><span class="wpmolicon icon-home"></span></a></li><li><a href="#">Synopsis</a></li><li><a href="#">Casting</a></li><li><a href="#">Photos</a></li>
				</ul>
			</div>
			<div class="wpmoly headbox allocine movie poster">
<?php if ( ! empty( $poster ) ) : ?>
				<?php echo $poster; ?>
<?php endif; ?>
			</div>
			<div class="wpmoly headbox allocine movie section main">
				<div class="wpmoly headbox allocine movie meta release_date">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Release Date', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><strong><?php echo $meta['release_date']; ?></strong></span>
					<span class="wpmoly headbox allocine movie meta value">(<?php echo $meta['runtime']; ?>)</span>
				</div>
				<div class="wpmoly headbox allocine movie meta director">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Directed by', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['director']; ?></span>
				</div>
				<div class="wpmoly headbox allocine movie meta cast">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Starring', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['_cast']; ?></span>
				</div>
				<div class="wpmoly headbox allocine movie meta writer">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Writer', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['writer']; ?></span>
				</div>
				<div class="wpmoly headbox allocine movie meta composer">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Composer', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['composer']; ?></span>
				</div>
				<div class="wpmoly headbox allocine movie meta genres">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Genres', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['genres']; ?></span>
				</div>
				<div class="wpmoly headbox allocine movie meta production_countries">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Country', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['production_countries']; ?></span>
				</div>
				<div class="wpmoly headbox allocine movie meta rating">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Rating', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $details['rating_stars']; ?></span>
				</div>
			</div>
			<div class="wpmoly headbox allocine movie section details">
				<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Synopsis and Details', 'wpmovielibrary' ); ?></h3>
				<p><?php echo $meta['overview']; ?></p>
				<div class="wpmoly headbox allocine movie subsection">
					<ul>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Year of production', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['year']; ?></span>
						</li>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Release Date', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['release_date']; ?></span>
						</li>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Local Release Date', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['local_release_date']; ?></span>
						</li>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Producer', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['producer']; ?></span>
						</li>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Production Companies', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['production_companies']; ?></span>
						</li>
					</ul>
				</div>
				<div class="wpmoly headbox allocine movie subsection">
					<ul>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Revenue', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['revenue']; ?></span>
						</li>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Budget', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['budget']; ?></span>
						</li>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Language', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['spoken_languages']; ?></span>
						</li>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Certification', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['certification']; ?></span>
						</li>
						<li>
							<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Official Website', 'wpmovielibrary' ); ?>&nbsp;</span>
							<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['homepage']; ?></span>
						</li>
					</ul>
				</div>
			</div>
			<div class="wpmoly headbox allocine movie section images">
				<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Photos', 'wpmovielibrary' ); ?></h3>
				<?php echo $images; ?>
			</div>





			<!--<div class="wpmoly headbox allocine movie section ">
<?php if ( ! empty( $meta['title'] ) ) : ?>
				<h2 class="wpmoly headbox allocine movie meta title"><?php echo $meta['title']; ?><?php if ( ! empty( $meta['year'] ) ) : ?> <span class="wpmoly headbox allocine movie meta year">(<?php echo $meta['year']; ?>)</span><?php endif; ?></h2>
<?php endif; if ( ! empty( $meta['certification'] ) ) : ?>
				<span class="wpmoly headbox allocine movie meta certification"><?php echo $meta['certification']; ?></span>
<?php endif; if ( ! empty( $meta['_runtime'] ) ) : ?>
				<span class="wpmoly headbox allocine movie meta runtime"><?php echo $meta['_runtime']; ?> min</span> - 
<?php endif; if ( ! empty( $meta['genres'] ) ) : ?>
				<span class="wpmoly headbox allocine movie meta genres"><?php echo $meta['genres']; ?></span> - 
<?php endif; if ( ! empty( $meta['release_date'] ) ) : ?>
				<span class="wpmoly headbox allocine movie meta release_date"><?php echo $meta['release_date']; ?></span>
<?php endif; ?>
				<hr />
<?php if ( ! empty( $meta['rating_stars'] ) ) : ?>
				<span class="wpmoly headbox allocine movie rating starlined"><?php _e( 'Your rating:', 'wpmovielibrary' ); ?> <?php echo $details['rating_stars']; ?></span>
				<hr />
<?php endif; if ( ! empty( $meta['overview'] ) ) : ?>
				<p><?php echo $meta['overview']; ?></p>
<?php endif; if ( ! empty( $meta['director'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta director">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Director:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['director']; ?></span>
				</div>
<?php endif; if ( ! empty( $meta['writer'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta writer">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Writers:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['writer']; ?></span>
				</div>
<?php endif; if ( ! empty( $meta['cast'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta cast">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Stars:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['cast']; ?></span>
				</div>
			</div>
<?php if ( ! empty( $images ) ) : ?>
			<div class="wpmoly headbox allocine movie section images">
				<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Photos', 'wpmovielibrary' ); ?></h3>
				<?php echo $images; ?>
			</div>
<?php endif; ?>
			<div class="wpmoly headbox allocine movie section storyline">
				<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Storyline', 'wpmovielibrary' ); ?></h3>
<?php endif; if ( ! empty( $meta['overview'] ) ) : ?>
				<p class="wpmoly headbox allocine movie meta overview"><?php echo $meta['overview']; ?></p>
				<hr />
<?php endif; if ( ! empty( $meta['collections'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta collections">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Collections:', 'wpmovielibrary' ); ?></span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['collections']; ?></span>
				</div>
				<hr />
<?php endif; if ( ! empty( $meta['tagline'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta tagline">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Tagline:', 'wpmovielibrary' ); ?></span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['tagline']; ?></span>
				</div>
				<hr />
<?php endif; if ( ! empty( $meta['genres'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta genres">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Genres:', 'wpmovielibrary' ); ?></span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['genres']; ?></span>
				</div>
<?php endif; ?>
			</div>
			<div class="wpmoly headbox allocine movie section production-details">
				<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Details', 'wpmovielibrary' ); ?></h3>
<?php if ( ! empty( $meta['homepage'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta homepage">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Official Website:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['homepage']; ?></span>
				</div>
<?php endif; if ( ! empty( $meta['production_countries'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta production_countries">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Country:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['production_countries']; ?></span>
				</div>
<?php endif; if ( ! empty( $meta['spoken_languages'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta spoken_languages">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Language:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['spoken_languages']; ?></span>
				</div>
<?php endif; if ( ! empty( $meta['release_date'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta release_date">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Release Date:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['release_date']; ?></span>
				</div>
<?php endif; ?>
			</div>
			<div class="wpmoly headbox allocine movie section box-office">
				<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Box Office', 'wpmovielibrary' ); ?></h3>
<?php if ( ! empty( $meta['budget'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta budget">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Budget:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['budget']; ?></span>
				</div>
<?php endif; if ( ! empty( $meta['revenue'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta revenue">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Revenue:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['revenue']; ?></span>
				</div>
<?php endif; ?>
			</div>
			<div class="wpmoly headbox allocine movie section companies">
				<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Company Credits', 'wpmovielibrary' ); ?></h3>
<?php if ( ! empty( $meta['production_companies'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta production_companies">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Production Companies:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['production_companies']; ?></span>
				</div>
<?php endif; ?>
			</div>
			<div class="wpmoly headbox allocine movie section tech-specs">
				<h3 class="wpmoly headbox allocine movie meta sub-title"><?php _e( 'Technical Specs', 'wpmovielibrary' ); ?></h3>
<?php if ( ! empty( $meta['runtime'] ) ) : ?>
				<div class="wpmoly headbox allocine movie meta runtime">
					<span class="wpmoly headbox allocine movie meta label"><?php _e( 'Runtime:', 'wpmovielibrary' ); ?>&nbsp;</span>
					<span class="wpmoly headbox allocine movie meta value"><?php echo $meta['runtime']; ?></span>
				</div>
<?php endif; ?>
			</div>-->
		</div>
		<div style="clear:both"></div>
	</div>
