<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

/**
 * Most Rated Movies Widget.
 * 
 * Display a list of the most rated Movies. Options: Title, Description,
 * Number of movies to show, where to show the rating.
 * 
 * @since    1.0.0
 */
class WPML_Most_Rated_Movies_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'wpml-most-rated-movies-widget',
			__( 'WPML Most Rated Movies', WPML_SLUG ),
			array(
				'classname'	=>	'wpml-most-rated-movies-widget',
				'description'	=>	__( 'Display most rated Movies.', WPML_SLUG )
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );
		extract( $instance );

		$title = $before_title . apply_filters( 'widget_title', $title ) . $after_title;
		$description = esc_attr( $description );
		$number = intval( $number );
		$display_rating = esc_attr( $display_rating );
		$rating_only = ( 1 == $rating_only ? true : false );

		$html = '';

		if ( $rating_only ) {

			$ratings = array_reverse( WPML_Settings::get_available_movie_rating() );

			if ( ! empty( $ratings ) ) {

				$items = array();
				$movies = WPML_Settings::wpml__movie_rewrite();

				$items = array();
				$style = 'wpml-widget wpml-rating-list';

				foreach ( $ratings as $slug => $rating_title )
					$items[] = array(
							'ID'          => $slug,
							'attr_title'  => sprintf( __( 'Permalink for &laquo; %s Rated Movies &raquo;', WPML_SLUG ), esc_attr__( $rating_title, WPML_SLUG ) ),
							'link'        => home_url( "/{$movies}/{$slug}/" ),
							'title'       => '<div class="movie_rating_display stars_' . str_replace( '.', '_', $slug ) . '"><div class="stars_labels"><span class="stars_label stars_label_' . str_replace( '.', '_', $slug ) . '">' . esc_attr__( $rating_title, WPML_SLUG ) . '</span></div></div>'
						);

				$items = apply_filters( 'wpml_widget_rating_items', $items );
				$attributes = array( 'items' => $items, 'description' => $description, 'style' => $style );

				$html = WPMovieLibrary::render_template( 'rating-widget/rating-widget.php', $attributes );
			}
			else {
				$html = WPMovieLibrary::render_template( 'empty.php', array( 'message' => __( 'Nothing to display.', WPML_SLUG ) ) );
			}
		}
		else {
			$movies = new WP_Query(
				array(
					'posts_per_page' => $number,
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
						'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $movie->post_title ),
						'link'        => get_permalink( $movie->ID ),
						'rating'      => get_post_meta( $movie->ID, '_wpml_movie_rating', true ),
						'thumbnail'   => get_the_post_thumbnail( $movie->ID, 'thumbnail' )
					);
					$item['rating_str'] = ( '' == $item['rating'] ? "stars_0_0" : 'stars_' . str_replace( '.', '_', $item['rating'] ) );
					$items[] = $item;
				}

				$items = apply_filters( 'wpml_widget_most_rated_movies', $items, $list, $css );
				$attributes = array( 'items' => $items, 'description' => $description, 'style' => $style, 'display_rating' => $display_rating );

				$html = WPMovieLibrary::render_template( 'rating-widget/movies-by-rating.php', $attributes );
			}
			else {
				$html = WPMovieLibrary::render_template( 'empty.php', array( 'message' => __( 'Nothing to display.', WPML_SLUG ) ) );
			}
		}

		echo $before_widget . $title . $html . $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		$instance['number'] = intval( $new_instance['number'] );
		$instance['display_rating'] = strip_tags( $new_instance['display_rating'] );
		$instance['rating_only'] = intval( $new_instance['rating_only'] );

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

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Most Rated Movies', WPML_SLUG ) );
		$description = ( isset( $instance['description'] ) ? $instance['description'] : __( 'Movies I really enjoyed', WPML_SLUG ) );
		$number = ( isset( $instance['number'] ) ? $instance['number'] : 4 );
		$display_rating = ( isset( $instance['display_rating'] ) ? $instance['display_rating'] : 'no' );
		$rating_only = ( isset( $instance['rating_only'] ) ? $instance['rating_only'] : 0 );

		// Display the admin form
		include( WPML_PATH . 'admin/common/views/most-rated-movies-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}
