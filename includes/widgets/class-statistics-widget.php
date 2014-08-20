<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


/**
 * Statistics Widget.
 * 
 * Display some public statistics about the plugin usage.
 * 
 * @since    1.0
 */
class WPML_Statistics_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		parent::__construct(
			'wpml-statistics-widget',
			__( 'WPMovieLibrary Statistics', 'wpmovielibrary' ),
			array(
				'classname'	=>	'wpml-statistics-widget',
				'description'	=>	__( 'Display some statistics about your movie library', 'wpmovielibrary' )
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
		$name = apply_filters( 'wpml_cache_name', 'statistics_widget' );
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

		$count = (array) wp_count_posts( 'movie' );
		$count = array(
			'movie'       => $count['publish'],
			'imported'    => $count['import-draft'],
			'queued'      => $count['import-queued'],
			'draft'       => $count['draft'],
			'total'       => 0,
		);
		$count['total'] = array_sum( $count );
		$count['collections'] = wp_count_terms( 'collection' );
		$count['genres'] = wp_count_terms( 'genre' );
		$count['actors'] = wp_count_terms( 'actor' );

		$movie = WPML_Settings::wpml__movie_rewrite();
		$movie = ( '' != $movie ? $movie : 'collection' );
		$collection = WPML_Settings::taxonomies__collection_rewrite();
		$collection = ( '' != $collection ? $collection : 'collection' );
		$genre = WPML_Settings::taxonomies__genre_rewrite();
		$genre = ( '' != $genre ? $genre : 'genre' );
		$actor = WPML_Settings::taxonomies__actor_rewrite();
		$actor = ( '' != $actor ? $actor : 'actor' );

		$links = array(
			'%total%' 	=> sprintf( '<a href="%s">%s</a>', home_url( $movie . '/' ), sprintf( _n( 'one movie', '%s movies', $count['total'], 'wpmovielibrary' ), '<strong>' . $count['total'] . '</strong>' ) ),
			'%collections%'	=> sprintf( '<a href="%s">%s</a>', home_url( $collection . '/' ), sprintf( _n( 'one collection', '%s collections', $count['collections'], 'wpmovielibrary' ), '<strong>' . $count['collections'] . '</strong>' ) ),
			'%genres%'	=> sprintf( '<a href="%s">%s</a>', home_url( $genre . '/' ), sprintf( _n( 'one genre', '%s genres', $count['genres'], 'wpmovielibrary' ), '<strong>' . $count['genres'] . '</strong>' ) ),
			'%actors%'	=> sprintf( '<a href="%s">%s</a>', home_url( $actor . '/' ), sprintf( _n( 'one actor', '%s actors', $count['actors'], 'wpmovielibrary' ), '<strong>' . $count['actors'] . '</strong>' ) )
		);

		$title = $before_title . apply_filters( 'widget_title', $title ) . $after_title;
		$description = esc_attr( $description );
		$format = wpautop( wp_kses( $format, array( 'ul', 'ol', 'li', 'p', 'span', 'em', 'i', 'p', 'strong', 'b', 'br' ) ) );

		$content = str_replace( array_keys( $links ), array_values( $links ), $format );
		$style = 'wpml-widget wpml-statistics';

		$attributes = array( 'content' => $content, 'description' => $description, 'style' => $style );

		$html = WPMovieLibrary::render_template( 'statistics-widget/statistics.php', $attributes, $require = 'always' );

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
		$instance['format'] = wp_kses( $new_instance['format'], array( 'ul' => array(), 'ol' => array(), 'li' => array(), 'p' => array(), 'span' => array(), 'em' => array(), 'i' => array(), 'p' => array(), 'strong' => array(), 'b' => array(), 'br' => array() ) );

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

		$instance['title'] = ( isset( $instance['title'] ) && '' != $instance['title'] ? $instance['title'] : __( 'Statistics', 'wpmovielibrary' ) );
		$instance['description'] = ( isset( $instance['description'] ) && '' != $instance['description'] ? '<p>' . $instance['description'] . '</p>' : '' );
		$instance['format'] = ( isset( $instance['format'] ) && '' != $instance['format'] ? $instance['format'] : __( 'All combined you have a total of %total% in your library, regrouped in %collections%, %genres% and %actors%.', 'wpmovielibrary' ) );

		// Display the admin form
		echo WPMovieLibrary::render_template( 'statistics-widget/statistics-admin.php', array( 'widget' => $this, 'instance' => $instance ), $require = 'always' );
	}

}
