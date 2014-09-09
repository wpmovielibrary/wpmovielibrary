
		<div id="wpml-home" class="wrap about-wrap">

			<h2><?php printf( '%s <small>v%s</small>', __( 'Welcome to WPMovieLibrary ', 'wpmovielibrary' ), WPML_VERSION ); ?></h2>

			<div class="about-text">
				<?php _e( 'This page will allow you update your library to the new metadata format introduced in WPMovieLibrary 1.3.', 'wpmovielibrary' ); ?>
			</div>

			<div class="update-movies">

				<div class="update-movies-header">
					<h4><span class="dashicons dashicons-marker"></span> <?php printf( _n( 'One deprecated movie', '%d deprecated movies', $deprecated, 'wpmovielibrary' ), count( $deprecated ) ); ?></h4>
					<h4><span class="dashicons dashicons-yes"></span> <?php printf( _n( 'One updated movie', '%d updated movies', $deprecated, 'wpmovielibrary' ), count( $updated ) ); ?></h4>
				</div>

				<div class="update-movies-legend">
					<h5><?php _e( 'Deprecated', 'wpmovielibrary' ); ?></h5>
					<h5><?php _e( 'Updated', 'wpmovielibrary' ); ?></h5>
				</div>

				<ul class="deprecated-movies">
<?php
global $post;
foreach ( $deprecated as $post ) :
	setup_postdata( $post );
?>
					<li id="post-<?php the_ID(); ?>"><?php the_title(); ?> <span class="dashicons dashicons-marker"></span></li>

<?php
endforeach;
wp_reset_postdata();
?>
				</ul>

				<ul class="updated-movies">
<?php
global $post;
foreach ( $updated as $post ) :
	setup_postdata( $post );
?>
					<li id="post-<?php the_ID(); ?>"><?php the_title(); ?> <span class="dashicons dashicons-yes"></span></li>

<?php
endforeach;
wp_reset_postdata();
?>
				</ul>
			</div>

		</div>
