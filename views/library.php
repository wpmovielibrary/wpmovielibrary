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

			<div class="row">
				<div class="small-6 large-2 columns brand"><h2 id="brand" title="WPMovieLibrary">WPMovieLibrary</h2></div>
				<div class="small-6 large-8 columns"></div>
				<div class="small-12 large-2 columns search">Search</div>
			</div>




			

			<div id="library">
<?php 
$wpml = new WPMovieLibrary();
foreach ( $wpml->wpml_get_movies() as $movie ) :
?>
				<div class="movie">
					<img src="<?php echo $movie['poster']; ?>" alt="<?php echo $movie['title']; ?>" />
				</div>
<?php endforeach; ?>
			</div>
		</div>

<?php wp_footer(); ?>

	</body>

</html>