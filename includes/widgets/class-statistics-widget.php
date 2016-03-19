<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */


/**
 * Statistics Widget.
 * 
 * Display some public statistics about the plugin usage.
 * 
 * @since    1.0
 */
class WPMOLY_Statistics_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		parent::__construct(
			'wpmoly-statistics-widget',
			__( 'WPMovieLibrary Statistics', 'wpmovielibrary' ),
			array(
				'classname'	=>	'wpmoly-statistics-widget',
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
		$name = apply_filters( 'wpmoly_cache_name', 'statistics_widget', $args );
		// Naughty PHP 5.3 fix
		$widget = &$this;
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

		extract( $args, EXTR_SKIP );
		extract( $instance );

		$count = (array) wp_count_posts( 'movie' );
		$count = array(
			'movies'      => $count['publish'],
			'imported'    => $count['import-draft'],
			'queued'      => $count['import-queued'],
			'draft'       => $count['draft'],
			'total'       => 0,
		);
		$count['total'] = array_sum( $count );
		$count['collections'] = wp_count_terms( 'collection' );
		$count['genres'] = wp_count_terms( 'genre' );
		$count['actors'] = wp_count_terms( 'actor' );
		$count = array_map( 'intval', $count );

		extract( $count );

		$links = array();
		$links['%total%'] 	= sprintf( '<a href="%s">%s</a>', get_post_type_archive_link( 'movie' ), sprintf( _n( '<strong>1</strong> movie', '<strong>%d</strong> movies', $movies, 'wpmovielibrary' ), $movies ) );
		$links['%collections%']	= WPMOLY_Utils::get_taxonomy_permalink( 'collection', sprintf( _n( '<strong>1</strong> collection', '<strong>%d</strong> collections', $collections, 'wpmovielibrary' ), $collections ) );
		$links['%genres%']	= WPMOLY_Utils::get_taxonomy_permalink( 'genre', sprintf( _n( '<strong>1</strong> genre', '<strong>%d</strong> genres', $genres, 'wpmovielibrary' ), $genres ) );
		$links['%actors%']	= WPMOLY_Utils::get_taxonomy_permalink( 'actor', sprintf( _n( '<strong>1</strong> actor', '<strong>%d</strong> actors', $actors, 'wpmovielibrary' ), $actors ) );

		$title = $before_title . apply_filters( 'widget_title', $title ) . $after_title;
		$description = esc_attr( $description );
		$format = wpautop( wp_kses( $format, array( 'ul', 'ol', 'li', 'p', 'span', 'em', 'i', 'p', 'strong', 'b', 'br' ) ) );

		$content = str_replace( array_keys( $links ), array_values( $links ), $format );
		$style = 'wpmoly-widget wpmoly-statistics';

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
