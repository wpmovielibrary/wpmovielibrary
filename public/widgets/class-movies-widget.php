<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


/**
 * Movies Widget.
 * 
 * Display a list of the Movies from various sources: Status, Media, Rating…
 * 
 * @since    1.2
 */
class WPML_Movies_Widget extends WPML_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget. No
	 * stylesheets or JavaScript needed, localization loaded in public class.
	 */
	public function __construct() {

		$this->widget_name        = __( 'WPMovieLibrary Movies', 'wpmovielibrary' );
		$this->widget_description = __( 'Display a list of movies from a specific taxonomy, media, status, rating…', 'wpmovielibrary' );
		$this->widget_css         = 'wpmovielibrary wpml-movies-widget';
		$this->widget_id          = 'wpmovielibrary_movies_widget';
		$this->widget_form        = 'movies-widget/movies-admin.php';

		$this->widget_params      = array(
			'title'  => array(
				'type' => 'text',
				'std'  => __( 'Movies', 'wpmovielibrary' )
			),
			'description' => array(
				'type' => 'text',
				'std'  => ''
			),
			'limit' =>  array(
				'type' => 'number',
				'std'  => 4
			),
			'rating' =>  array(
				'type' => 'select',
				'std'  => 'below'
			)
		);

		$this->movies_by = array(
			'status'  => __( 'Status', 'wpmovielibrary' ),
			'media'   => __( 'Media', 'wpmovielibrary' ),
			'rating'  => __( 'Rating', 'wpmovielibrary' ),
			'title'   => __( 'Title', 'wpmovielibrary' ),
			'date'    => __( 'Date', 'wpmovielibrary' )
		);

		$this->status = WPML_Settings::get_available_movie_status();
		$this->media  = WPML_Settings::get_available_movie_media();
		$this->rating = WPML_Settings::get_available_movie_rating();

		parent::__construct();
	}

	/**
	 * Output the Widget content.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		// Caching
		$name = apply_filters( 'wpml_cache_name', 'movies_widget' );
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

		$title = apply_filters( 'widget_title', $title );

		$html = '';

			$movies = new WP_Query(
				array(
					'posts_per_page' => $limit,
					'post_type'      => 'movie',
					'order'          => 'DESC',
					'orderby'        => 'meta_value_num',
					'meta_key'       => '_wpml_movie_rating',
				)
			);

			$style = 'wpml-widget wpml-rating-movies-list wpml-movies wpml-movies-with-thumbnail';

			if ( ! empty( $movies->posts ) ) {

				$items = array();
				$style = 'wpml-widget wpml-media-rating-list wpml-movies wpml-movies-with-thumbnail';

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
				$attributes = array( 'items' => $items, 'description' => $description, 'style' => $style, 'rating' => $rating );

				$html = WPMovieLibrary::render_template( 'movies-widget/movies.php', $attributes );
			}
			else {
				$html = WPMovieLibrary::render_template( 'empty.php', array( 'message' => __( 'Nothing to display.', 'wpmovielibrary' ) ) );
			}


		return $before_widget . $before_title . $title . $after_title . $html . $after_widget;
	}

}
