<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */


/**
 * Movies Widget.
 * 
 * Display a list of the Movies from various sources: Status, Media, Rating…
 * 
 * @since    1.2
 */
class WPMOLY_Movies_Widget extends WPMOLY_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget. No
	 * stylesheets or JavaScript needed, localization loaded in public class.
	 */
	public function __construct() {

		$this->widget_name        = __( 'WPMovieLibrary Movies', 'wpmovielibrary' );
		$this->widget_description = __( 'Display a list of movies from a specific taxonomy, media, status, rating…', 'wpmovielibrary' );
		$this->widget_css         = 'wpmoly movies';
		$this->widget_id          = 'wpmovielibrary-movies-widget';
		$this->widget_form        = 'movies-widget/movies-admin.php';

		$this->widget_params      = array(
			'title' => array(
				'type' => 'text',
				'std'  => __( 'Movies', 'wpmovielibrary' )
			),
			'description' => array(
				'type' => 'text',
				'std'  => ''
			),
			'select' => array(
				'type' => 'select',
				'std'  => 'date'
			),
			'select_status' => array(
				'type' => 'select',
				'std'  => 'all'
			),
			'select_media' => array(
				'type' => 'select',
				'std'  => 'all'
			),
			'select_rating' => array(
				'type' => 'select',
				'std'  => 'all'
			),
			'select_meta' => array(
				'type' => 'select',
				'std'  => 'all'
			),
			'release_date' => array(
				'type' => 'text',
				'std'  => ''
			),
			'spoken_languages' => array(
				'type' => 'text',
				'std'  => ''
			),
			'production_companies' => array(
				'type' => 'text',
				'std'  => ''
			),
			'production_countries' => array(
				'type' => 'text',
				'std'  => ''
			),
			'certification' => array(
				'type' => 'text',
				'std'  => ''
			),
			'sort' => array(
				'type' => 'select',
				'std'  => 'DESC'
			),
			'limit' => array(
				'type' => 'number',
				'std'  => 4
			),
			'show_poster' => array(
				'type' => 'select',
				'std'  => 'normal'
			),
			'show_title' => array(
				'type' => 'select',
				'std'  => 'no'
			),
			'show_rating' => array(
				'type' => 'select',
				'std'  => 'starsntext'
			),
			'exclude_current' => array(
				'type' => 'select',
				'std'  => 'no'
			)
		);

		$this->movies_by = array(
			'status'  => __( 'Status', 'wpmovielibrary' ),
			'media'   => __( 'Media', 'wpmovielibrary' ),
			'rating'  => __( 'Rating', 'wpmovielibrary' ),
			'title'   => __( 'Title', 'wpmovielibrary' ),
			'date'    => __( 'Date', 'wpmovielibrary' ),
			'meta'    => __( 'Metadata', 'wpmovielibrary' ),
			'random'  => __( 'Random', 'wpmovielibrary' )
		);

		$this->status = WPMOLY_Settings::get_available_movie_status();
		$this->media  = WPMOLY_Settings::get_available_movie_media();
		$this->rating = WPMOLY_Settings::get_available_movie_rating();

		$this->meta   = array(
			'release_date'         => __( 'Release Date', 'wpmovielibrary' ),
			'production_companies' => __( 'Production', 'wpmovielibrary' ),
			'production_countries' => __( 'Country', 'wpmovielibrary' ),
			'spoken_languages'     => __( 'Languages', 'wpmovielibrary' ),
			'certification'        => __( 'Certification', 'wpmovielibrary' )
		);

		$this->years          = WPMOLY_Utils::get_used_years( $count = true );
		$this->languages      = WPMOLY_Utils::get_used_languages( $count = true );
		$this->companies      = WPMOLY_Utils::get_used_companies( $count = true );
		$this->countries      = WPMOLY_Utils::get_used_countries( $count = true );
		$this->certifications = WPMOLY_Utils::get_used_certifications( $count = true );

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
		$name = apply_filters( 'wpmoly_cache_name', 'movies_widget', $args );
		// Naughty PHP 5.3 fix
		$widget = &$this;

		// Skip caching if random
		if ( isset( $args['orderby'] ) && 'random' == $args['orderby'] )
			return $widget->widget_content( $args, $instance );

		$content = WPMOLY_Cache::output( $name, function() use ( $widget, $args, $instance ) {

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

		$defaults = array(
			'title'                => __( 'Movies', 'wpmovielibrary' ),
			'description'          => '',
			'select'               => 'date',
			'select_status'        => 'all',
			'select_media'         => 'all',
			'select_rating'        => 'all',
			'select_meta'          => 'all',
			'release_date'         => '',
			'spoken_languages'     => '',
			'production_companies' => '',
			'production_countries' => '',
			'certification'        => '',
			'sort'                 => 'DESC',
			'limit'                => 4,
			'show_poster'          => 'normal',
			'show_title'           => 'no',
			'show_rating'          => 'starsntext',
			'exclude_current'      => 'no'
		);
		$args = wp_parse_args( $args, $defaults );

		extract( $args, EXTR_SKIP );
		extract( $instance );

		$title = apply_filters( 'widget_title', $title );

		if ( 'no' != $show_poster )
			$this->widget_css .= ' wpmoly-movies-with-thumbnail';

		if ( 'normal' == $show_poster )
			$thumbnail = 'medium';
		else
			$thumbnail = 'thumbnail';

		switch ( $select ) {
			case 'status':
				$_select = $instance[ "select_$select" ];
				$args = array(
					'orderby'    => 'post_date',
					'meta_query' => array()
				);
				if ( 'all' != $_select ) {
					$args['meta_query'][] = array(
						'key'     => "_wpmoly_movie_$select",
						'value'   => $_select,
						'compare' => '='
					);
				} else {
					$args['meta_query'][] = array(
						'key'     => "_wpmoly_movie_$select",
						'value'   => '',
						'compare' => '!='
					);
				}
				break;
			case 'media':
				$_select = $instance[ "select_$select" ];
				$args = array(
					'orderby'    => 'post_date',
					'meta_query' => array()
				);
				if ( 'all' != $_select ) {
					$args['meta_query'][] = array(
						'key'     => "_wpmoly_movie_$select",
						'value'   => $_select,
						'compare' => 'LIKE'
					);
				} else {
					$args['meta_query'][] = array(
						'key'     => "_wpmoly_movie_$select",
						'value'   => '',
						'compare' => 'NOT LIKE'
					);
				}
				break;
			case 'rating':
				$args = array( 'orderby' => 'meta_value_num', 'meta_key' => '_wpmoly_movie_rating' );
				if ( 'all' != $select_rating )
					$args['meta_value'] = $select_rating;
				break;
			case 'title':
				$args = array( 'orderby' => 'title' );
				break;
			case 'random':
				$args = array( 'orderby' => 'rand' );
				if ( is_single() && $exclude_current ) {
					global $post;
					$args['post__not_in'] = array( $post->ID );
				}
				break;
			case 'meta':
				switch ( $select_meta ) {
					case 'release_date':
					case 'production_companies':
					case 'production_countries':
					case 'spoken_languages':
					case 'certification':
						$args = array(
							'meta_query' => array(
								array(
									'key'     => "_wpmoly_movie_{$select_meta}",
									'value'   => $instance[ $select_meta ],
									'compare' => 'LIKE'
								)
							),
						);
						break;
					default:
						break;
				}
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
				'rating'      => wpmoly_get_movie_meta( $movie->ID, 'rating' ),
				'thumbnail'   => get_the_post_thumbnail( $movie->ID, $thumbnail )
			);
			$item['_rating'] = apply_filters( 'wpmoly_movie_rating_stars', $item['rating'], $movie->ID, $base = 5 );
			$items[] = $item;
		}

		$attributes = array( 'items' => $items, 'description' => $description, 'show_rating' => $show_rating, 'show_title' => $show_title, 'show_poster' => $show_poster, 'style' => $this->widget_css );
		$html = WPMovieLibrary::render_template( 'movies-widget/movies.php', $attributes, $require = 'always' );

		return $before_widget . $before_title . $title . $after_title . $html . $after_widget;
	}
}
