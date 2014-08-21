<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
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
		$this->widget_css         = 'wpml-widget wpml-movies-widget wpml-movies';
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
			'select' =>  array(
				'type' => 'select',
				'std'  => 'date'
			),
			'select_status' =>  array(
				'type' => 'select',
				'std'  => 'all'
			),
			'select_media' =>  array(
				'type' => 'select',
				'std'  => 'all'
			),
			'select_rating' =>  array(
				'type' => 'select',
				'std'  => 'all'
			),
			'sort' =>  array(
				'type' => 'select',
				'std'  => 'DESC'
			),
			'limit' =>  array(
				'type' => 'number',
				'std'  => 4
			),
			'show_poster' =>  array(
				'type' => 'select',
				'std'  => 'normal'
			),
			'show_title' =>  array(
				'type' => 'select',
				'std'  => 'no'
			),
			'show_rating' =>  array(
				'type' => 'select',
				'std'  => 'starsntext'
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
		$name = apply_filters( 'wpml_cache_name', 'movies_widget', $args );
		// Naughty PHP 5.3 fix
		$widget = &$this;
		$content = WPML_Cache::output( $name, function() use ( $widget, $args, $instance ) {

			return $widget->widget_content( $args, $instance );
		});

		echo $content;
	}

	/**
	 * Generate the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget_content( $args, $instance ) {

		extract( $args, EXTR_SKIP );
		extract( $instance );

		$title = apply_filters( 'widget_title', $title );

		if ( 'no' != $show_poster )
			$this->widget_css .= ' wpml-movies-with-thumbnail';

		if ( 'normal' == $show_poster )
			$thumbnail = 'medium';
		else
			$thumbnail = 'thumbnail';

		switch ( $select ) {
			case 'status':
				$args = array( 'orderby' => 'meta_value', 'meta_key' => '_wpml_movie_status' );
				if ( 'all' != $select_status )
					$args['meta_value'] = $select_status;
				break;
			case 'media':
				$args = array( 'orderby' => 'meta_value', 'meta_key' => '_wpml_movie_media' );
				if ( 'all' != $select_media )
					$args['meta_value'] = $select_media;
				break;
			case 'rating':
				$args = array( 'orderby' => 'meta_value_num', 'meta_key' => '_wpml_movie_rating' );
				if ( 'all' != $select_rating )
					$args['meta_value'] = $select_rating;
				break;
			case 'title':
				$args = array( 'orderby' => 'title' );
				break;
			case 'date':
			default:
				$args = array( 'orderby' => 'date' );
				break;
		}

		$args = array_merge(
			array(
				'posts_per_page' => $limit,
				'post_type'      => 'movie',
				'order'          => $sort
			),
			$args
		);

		$movies = new WP_Query( $args );

		if ( empty( $movies->posts ) ) {
			$html = WPMovieLibrary::render_template( 'empty.php', array( 'message' => __( 'Nothing to display.', 'wpmovielibrary' ) ), $require = 'always' );
			return $before_widget . $before_title . $title . $after_title . $html . $after_widget;
		}

		$items = array();

		foreach ( $movies->posts as $movie ) {
			$item = array(
				'ID'          => $movie->ID,
				'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpmovielibrary' ), $movie->post_title ),
				'title'       => $movie->post_title,
				'link'        => get_permalink( $movie->ID ),
				'rating'      => get_post_meta( $movie->ID, '_wpml_movie_rating', true ),
				'thumbnail'   => get_the_post_thumbnail( $movie->ID, $thumbnail )
			);
			$item['rating_str'] = ( '' == $item['rating'] ? "stars_0_0" : 'stars_' . str_replace( '.', '_', $item['rating'] ) );
			$items[] = $item;
		}

		$attributes = array( 'items' => $items, 'description' => $description, 'show_rating' => $show_rating, 'show_title' => $show_title, 'show_poster' => $show_poster, 'style' => $this->widget_css );
		$html = WPMovieLibrary::render_template( 'movies-widget/movies.php', $attributes, $require = 'always' );

		return $before_widget . $before_title . $title . $after_title . $html . $after_widget;
	}

}
