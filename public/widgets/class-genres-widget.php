<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


/**
 * Genres Widget.
 * 
 * Display a list of the Movies Genres. Options: Title, Show as dropdown,
 * Show Movie count, custom style.
 * 
 * @since    1.0.0
 */
class WPML_Genres_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		parent::__construct(
			'wpml-genres-widget',
			__( 'WPML Genres', 'wpmovielibrary-admin' ),
			array(
				'classname'	=>	'wpml-genres-widget',
				'description'	=>	__( 'Display Movie Genres Lists', 'wpmovielibrary-admin' )
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
		$name = apply_filters( 'wpml_cache_name', 'genres_widget' );
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

		$title = $before_title . apply_filters( 'widget_title', $title ) . $after_title;
		$description = esc_attr( $description );
		$list  = ( 1 == $list ? true : false );
		$css = ( 1 == $css ? true : false );
		$count = ( 1 == $count ? true : false );

		$genres = get_terms( array( 'genre' ) );

		if ( $genres && ! is_wp_error( $genres ) ) {

			$items = array();
			$style = 'wpml-widget wpml-genre-list';

			if ( $css )
				$style = 'wpml-widget wpml-genre-list wpml-list custom';

			foreach ( $genres as $genre )
				$items[] = array(
					'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpmovielibrary-admin' ), $genre->name ),
					'link'        => get_term_link( sanitize_term( $genre, 'genre' ), 'genre' ),
					'title'       => esc_attr( $genre->name . ( 1 == $count ? sprintf( '&nbsp;(%d)', $genre->count ) : '' ) )
				);

			$items = apply_filters( 'wpml_widget_genre_list', $items, $list, $css );
			$attributes = array( 'items' => $items, 'description' => $description, 'default_option' => __( 'Select a genre', 'wpmovielibrary-admin' ), 'style' => $style );

			if ( $list )
				$html = WPMovieLibrary::render_template( 'genre-widget/genre-dropdown-list.php', $attributes );
			else
				$html = WPMovieLibrary::render_template( 'genre-widget/genre-list.php', $attributes );
		}
		else {
			$html = WPMovieLibrary::render_template( 'empty.php', array( 'message' => __( 'Nothing to display for "Genre" taxonomy.', 'wpmovielibrary-admin' ) ) );
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

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		$instance['list']  = intval( $new_instance['list'] );
		$instance['count'] = intval( $new_instance['count'] );
		$instance['css']   = intval( $new_instance['css'] );

		$name = apply_filters( 'wpml_cache_name', 'genres_widget' );
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

		$instance['title'] = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie Genres', 'wpmovielibrary-admin' ) );
		$instance['description'] = ( isset( $instance['description'] ) ? $instance['description'] : '' );
		$instance['list']  = ( isset( $instance['list'] ) ? $instance['list'] : 1 );
		$instance['count'] = ( isset( $instance['count'] ) ? $instance['count'] : 0 );
		$instance['css']   = ( isset( $instance['css'] ) ? $instance['css'] : 0 );

		// Display the admin form
		echo WPMovieLibrary::render_template( 'genre-widget/genre-admin.php', array( 'widget' => $this, 'instance' => $instance ), $require = 'always' );
	}

}
