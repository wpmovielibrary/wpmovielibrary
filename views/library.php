<!DOCTYPE html>
<!--[if IE 7]><html class="ie ie7" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 8]><html class="ie ie8" <?php language_attributes(); ?>><![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width" />

		<title><?php wp_title( '|', true, 'right' ); ?></title>

		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>

	</head>

	<body <?php body_class('wpmovielibrary'); ?>>

		<div id="wpmovielibrary">

			<div class="row row-header">
				<div class="brand"><h2 id="brand" title="WPMovieLibrary">WPMovieLibrary</h2></div>
				<div class="library-header">
					<ul>
						<li><i class="icon-film"></i> 245 Movies</li>
						<li><i class="icon-ticket"></i> 8 Collections</li>
					</ul>
				</div>
				<div class="search">
					<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
						<div><label class="screen-reader-text" for="s"><i class="icon-search"></i></label>
							<input type="text" value="" name="s" id="s" placeholder="<?php _e( 'Search', 'wpml' ); ?>" />
							<input type="submit" id="searchsubmit" value="<?php _e( 'Search', 'wpml' ); ?>" />
						</div>
					</form>
				</div>
				<div class="library-menu">
					<ul>
						<li><a id="detailled-view" href="#"><i class="icon-th"></i></a></li>
						<li><a id="flat-view" href="#"><i class="icon-th-large"></i></a></li>
					</ul>
				</div>
			</div>

			<div class="row row-content">

				<div id="sidemenu">
					<h5 class="sidemenu-h">Your Movies</h5>
					<ul>
						<li><a href="#"><i class="icon-time"></i> Recent</a></li>
						<li><a href="#"><i class="icon-star"></i> Most rated</a></li>
						<li><a href="#"><i class="icon-calendar"></i> Scheduled</a></li>
					</ul>
					<h5 class="sidemenu-h">Your Collections</h5>
					<ul>
						<li><a href="#"><i class="icon-ticket"></i> Collection A</a></li>
						<li><a href="#"><i class="icon-ticket"></i> Collection B</a></li>
						<li><a href="#"><i class="icon-ticket"></i> Collection C</a></li>
					</ul>
				</div>

				<div id="library" class="detailled">

					<div class="library-content">

						<div class="nano">

							<div id="content" class="content">

<?php 
$wpml = new WPMovieLibrary();
foreach ( $wpml->wpml_get_movies() as $movie ) :
?>
								<div class="movie">
									<div class="movie-poster">
										<a href="#"><img src="<?php echo $movie['poster']; ?>" alt="<?php echo $movie['title']; ?>" /></a>
									</div>
									<div class="movie-title"><a href="#"><?php echo $movie['title']; ?></a></div>
									<div class="movie-genres"><?php echo $movie['genres']; ?></div>
									<div class="movie-runtime"><?php echo $movie['runtime']; ?> minutes</div>
									<div class="movie-overview"><?php echo $movie['overview']; ?></div>
								</div>
<?php endforeach; ?>

								<div style="clear:both"></div>

							</div> <!-- /#content -->

						</div> <!-- /.nano -->

					</div> <!-- /.library-content -->

				</div> <!-- /#library -->

			</div> <!-- /.row-content -->

		</div> <!-- /#wpmovielibrary -->

		<div id="movie_details_fader"></div>

		<div id="movie_details" class="detailled">
			<div class="movie-expend">
				<a href="#"><i class="icon-minus-sign"></i></a>
			</div>
			<div class="movie-poster">
				<img src="http://wpthemes/wp-content/uploads/2013/08/7QlaHwXQHxUeqd45qeHXoj7R2OU-624x832.jpg" alt="Inception" />
			</div>
			<div class="details">
				<div class="movie-title"><h2>Inception</h2></div>
				<div class="movie-genres"><strong>Genres:</strong> Action, Aventure, Mystère, Science-Fiction, Thriller</div>
				<div class="movie-runtime"><strong>Runtime:</strong> 148 minutes</div>
				<div class="movie-overview">Dom Cobb est un voleur expérimenté, le meilleur dans l'art dangereux de l'extraction, voler les secrets les plus intimes enfouis au plus profond du subconscient durant une phase de rêve, lorsque l'esprit est le plus vulnérable. Les capacités de Cobb ont fait des envieux dans le monde tourmenté de l'espionnage industriel alors qu'il devient fugitif en perdant tout ce qu'il a un jour aimé. Une chance de se racheter lui est alors offerte. Une ultime mission grâce à laquelle il pourrait retrouver sa vie passée mais uniquement s'il parvient à accomplir l'impossible inception.</div>
			</div>
		</div> <!-- /#movie_details -->

<?php wp_footer(); ?>

	</body>

</html>