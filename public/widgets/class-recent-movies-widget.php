<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

/**
 * Recent Movies Widget.
 * 
 * Display a list of the most recently added Movies. Options: Title, Description,
 * Number of movies to show.
 * 
 * @since    1.0.0
 */
class WPML_Recent_Movies_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		parent::__construct(
			'wpml-recent-movies-widget',
			__( 'WPML Recent Movies', 'wpmovielibrary' ),
			array(
				'classname'	=>	'wpml-recent-movies-widget',
				'description'	=>	__( 'Display most recently added Movies.', 'wpmovielibrary' )
			)
		);
	}

	/**
	 * Output the Widget content.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		// Caching
		$name = apply_filters( 'wpml_cache_name', 'recent_widget' );
		$content = WPML_Cache::output( $name, function() use ( $args, $instance ) {

			return $this->widget_content( $args, $instance );
		});

		echo $content;
	}

	/**
	 * Generate the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	private function widget_content( $args, $instance ) {

		extract( $args, EXTR_SKIP );
		extract( $instance );

		$title       = $before_title . apply_filters( 'widget_title', $title ) . $after_title;
		$description = esc_attr( $description );
		$number      = intval( $number );

		$movies = new WP_Query(
			array(
				'posts_per_page' => $number,
				'post_type'      => 'movie',
				'order'          => 'DESC',
				'orderby'        => 'date'
			)
		);

		$style = 'wpml-widget wpml-latest-movies-list wpml-movies';

		if ( ! empty( $movies->posts ) ) {

			$items = array();
			$style = 'wpml-widget wpml-media-latest-list wpml-movies wpml-movies-with-thumbnail';

			foreach ( $movies->posts as $movie ) {
				$item = array(
					'ID'          => $movie->ID,
					'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpmovielibrary' ), $movie->post_title ),
					'link'        => get_permalink( $movie->ID ),
					'rating'      => get_post_meta( $movie->ID, '_wpml_movie_rating', true ),
					'thumbnail'   => get_the_post_thumbnail( $movie->ID, 'thumbnail' )
				);
				$item['rating_str'] = ( '' == $item['rating'] ? "stars_0_0" : 'stars_' . str_replace( '.', '_', $item['rating'] ) );
				$items[] = $item;
			}

			$items = apply_filters( 'wpml_widget_most_rated_movies', $items );
			$attributes = array( 'items' => $items, 'description' => $description, 'style' => $style );

			$html = WPMovieLibrary::render_template( 'latest-widget/latest-movies.php', $attributes );
		}
		else {
			$html = WPMovieLibrary::render_template( 'empty.php' );
		}

		return $before_widget . $title . $html . $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		$instance['number']      = intval( $new_instance['number'] );

		$name = apply_filters( 'wpml_cache_name', 'recent_widget' );
		WPML_Cache::delete( $name );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		$instance['title']        = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Recent Movies', 'wpmovielibrary' ) );
		$instance['description']  = ( isset( $instance['description'] ) ? $instance['description'] : __( 'Movies I recently added to my library', 'wpmovielibrary' ) );
		$instance['number']       = ( isset( $instance['number'] ) ? $instance['number'] : 4 );

		// Display the admin form
		echo WPMovieLibrary::render_template( 'latest-widget/latest-movies-admin.php', array( 'widget' => $this, 'instance' => $instance ), $require = 'always' );
	}

}