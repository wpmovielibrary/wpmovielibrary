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
class WPML_Statistics_Widget extends WP_Widget {

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
			'wpml-statistics-widget',
			__( 'WPML Statistics', WPML_SLUG ),
			array(
				'classname'	=>	'wpml-statistics-widget',
				'description'	=>	__( 'Display some statistics about your movie library', WPML_SLUG )
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

		echo $before_widget;

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
			'%total%' 	=> sprintf( '<a href="%s">%s</a>', home_url( $movie . '/' ), sprintf( _n( 'one movie', '%s movies', $count['total'], WPML_SLUG ), '<strong>' . $count['total'] . '</strong>' ) ),
			'%collections%'	=> sprintf( '<a href="%s">%s</a>', home_url( $collection . '/' ), sprintf( _n( 'one collection', '%s collections', $count['collections'], WPML_SLUG ), '<strong>' . $count['collections'] . '</strong>' ) ),
			'%genres%'	=> sprintf( '<a href="%s">%s</a>', home_url( $genre . '/' ), sprintf( _n( 'one genre', '%s genres', $count['genres'], WPML_SLUG ), '<strong>' . $count['genres'] . '</strong>' ) ),
			'%actors%'	=> sprintf( '<a href="%s">%s</a>', home_url( $actor . '/' ), sprintf( _n( 'one actor', '%s actors', $count['actors'], WPML_SLUG ), '<strong>' . $count['actors'] . '</strong>' ) )
		);

		$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
		$description = $instance['description'];
		$format = $instance['format'];
		$content = str_replace( array_keys( $links ), array_values( $links ), $format );

		include( plugin_dir_path( __FILE__ ) . '/views/statistics-widget.php' );

		echo $after_widget;
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

		$title = ( isset( $instance['title'] ) && '' != $instance['title'] ? $instance['title'] : __( 'Statistics', WPML_SLUG ) );
		$description = ( isset( $instance['description'] ) && '' != $instance['description'] ? '<p>' . $instance['description'] . '</p>' : '' );
		$format = ( isset( $instance['format'] ) && '' != $instance['format'] ? $instance['format'] : __( 'All combined you have a total of %total% in your library, regrouped in %collections%, %genres% and %actors%.', WPML_SLUG ) );

		// Display the admin form
		include( WPML_PATH . 'admin/common/views/statistics-widget-admin.php' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}
