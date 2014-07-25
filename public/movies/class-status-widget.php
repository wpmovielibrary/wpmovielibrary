<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


/**
 * Status Widget.
 * 
 * Display a list of the Movies from a specific Status. Options: Title, Description,
 * Status, Show as dropdown.
 * 
 * @since    1.0.0
 */
class WPML_Status_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		parent::__construct(
			'wpml-status-widget',
			__( 'WPML Status', WPML_SLUG ),
			array(
				'classname'	=>	'wpml-status-widget',
				'description'	=>	__( 'Display Movies from a specific status', WPML_SLUG )
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
		$name = apply_filters( 'wpml_cache_name', 'status_widget' );
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
		$type = esc_attr( $type );
		$list = ( 1 == $list ? true : false );
		$css = ( 1 == $css ? true : false );
		$thumbnails = ( 1 == $thumbnails ? true : false );
		$status_only = ( 1 == $status_only ? true : false );

		$html = '';

		if ( $status_only ) {

			$status = WPML_Settings::get_available_movie_status();
			$movies = WPML_Settings::wpml__movie_rewrite();
			$rewrite = WPML_Settings::wpml__details_rewrite();

			if ( ! empty( $status ) ) {

				$items = array();
				$style = 'wpml-widget wpml-status-list';

				if ( $css )
					$style = 'wpml-widget wpml-status-list wpml-list custom';

				foreach ( $status as $slug => $status_title ) {
					$_slug = ( $rewrite ? __( $slug, WPML_SLUG ) : $slug );
					$items[] = array(
						'ID'          => $slug,
						'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $status_title ),
						'link'        => home_url( "/{$movies}/{$_slug}/" ),
						'title'       => esc_attr( $status_title ),
					);
				}

				$items = apply_filters( 'wpml_widget_media_lists', $items, $list, $css );
				$attributes = array( 'items' => $items, 'description' => $description, 'default_option' => __( 'Select a status', WPML_SLUG ), 'style' => $style );

				if ( $list )
					$html = WPMovieLibrary::render_template( 'status-widget/status-dropdown-list.php', $attributes );
				else
					$html = WPMovieLibrary::render_template( 'status-widget/status-list.php', $attributes );
			}
			else {
				$html = WPMovieLibrary::render_template( 'empty.php' );
			}

		}
		else {
			$movies = WPML_Movies::get_movies_from_status();

			if ( ! empty( $movies ) ) {

				$items = array();
				$style = 'wpml-widget wpml-status-movies-list';

				if ( $thumbnails )
					$style = 'wpml-widget wpml-status-movies-list wpml-movies wpml-movies-with-thumbnail';

				foreach ( $movies as $movie )
					$items[] = array(
						'ID'          => $movie->ID,
						'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $movie->post_title ),
						'link'        => get_permalink( $movie->ID ),
						'title'       => esc_attr( $movie->post_title ),
					);

				$items = apply_filters( 'wpml_widget_media_lists', $items, $list, $css );
				$attributes = array( 'items' => $items, 'description' => $description, 'style' => $style );

				if ( $thumbnails )
					$html = WPMovieLibrary::render_template( 'status-widget/movies-by-status.php', $attributes );
				else
					$html = WPMovieLibrary::render_template( 'status-widget/status-list.php', $attributes );
			}
			else {
				$html = WPMovieLibrary::render_template( 'empty.php' );
			}
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
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['list'] = intval( $new_instance['list'] );
		$instance['thumbnails'] = intval( $new_instance['thumbnails'] );
		$instance['css'] = intval( $new_instance['css'] );
		$instance['status_only'] = intval( $new_instance['status_only'] );

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

		$instance['title']       = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie by Status', WPML_SLUG ) );
		$instance['description'] = ( isset( $instance['description'] ) ? $instance['description'] : '' );
		$instance['type']        = ( isset( $instance['type'] ) ? $instance['type'] : null );
		$instance['list']        = ( isset( $instance['list'] ) ? $instance['list'] : 0 );
		$instance['thumbnails']  = ( isset( $instance['thumbnails'] ) ? $instance['thumbnails'] : 0 );
		$instance['css']         = ( isset( $instance['css'] ) ? $instance['css'] : 0 );
		$instance['status_only'] = ( isset( $instance['status_only'] ) ? $instance['status_only'] : 0 );

		// Display the admin form
		echo WPMovieLibrary::render_template( 'status-widget/status-admin.php', array( 'widget' => $this, 'instance' => $instance ), $require = 'always' );
	}

}
