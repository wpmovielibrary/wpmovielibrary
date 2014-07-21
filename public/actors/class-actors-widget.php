<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


/**
 * Actors Widget.
 * 
 * Display a list of the Movies Actors. Options: Title, Show as dropdown,
 * Show Movie count, custom style.
 * 
 * @since    1.0.0
 */
class WPML_Actors_Widget extends WP_Widget {

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
			'wpml-actors-widget',
			__( 'WPML Actors', WPML_SLUG ),
			array(
				'classname'	=>	'wpml-actors-widget',
				'description'	=>	__( 'Display Movie Actors Lists', WPML_SLUG )
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

		$list  = ( 1 == $list ? true : false );
		$css   = ( 1 == $css ? true : false );
		$count = ( 1 == $count ? true : false );
		$limit = ( isset( $limit ) ? intval( $limit ) : WPML_MAX_TAXONOMY_LIST );
		$archive = WPML_Settings::taxonomies__actor_rewrite();

		$args = '';
		if ( 0 < $limit )
			$args = 'order=DESC&orderby=count&number=' . $limit;

		$actors = get_terms( array( 'actor' ), $args );

		if ( $actors && ! is_wp_error( $actors ) ) {

			$items = array();
			$style = 'wpml-widget wpml-actor-list';

			if ( $css )
				$style = 'wpml-widget wpml-actor-list wpml-list custom';

			foreach ( $actors as $actor )
				$items[] = array(
					'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $actor->name ),
					'link'        => get_term_link( sanitize_term( $actor, 'actor' ), 'actor' ),
					'title'       => esc_attr( $actor->name . ( $count ? sprintf( '&nbsp;(%d)', $actor->count ) : '' ) )
				);

			if ( $limit )
				$items[] = array(
					'attr_title'  => __( 'View all actors', WPML_SLUG ),
					'link'        => home_url( '/' . $archive ),
					'title'       => __( 'View the complete list', WPML_SLUG )
				);

			$items = apply_filters( 'wpml_widget_actor_list', $items, $list, $css );
			$attributes = array( 'items' => $items, 'description' => $description, 'default_option' => __( 'Select an actor', WPML_SLUG ), 'style' => $style );

			if ( $list )
				$html = WPMovieLibrary::render_template( 'actor-widget/actor-dropdown-list.php', $attributes );
			else
				$html = WPMovieLibrary::render_template( 'actor-widget/actor-list.php', $attributes );
		}
		else {
			$html = WPMovieLibrary::render_template( 'empty.php', array( 'message' => __( 'Nothing to display for "Actor" taxonomy.', WPML_SLUG ) ) );
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
		$instance['list']  = intval( $new_instance['list'] );
		$instance['count'] = intval( $new_instance['count'] );
		$instance['css']   = intval( $new_instance['css'] );
		$instance['limit'] = intval( $new_instance['limit'] );

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

		$instance['title'] = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie Actors', WPML_SLUG ) );
		$instance['list']  = ( isset( $instance['list'] ) && 1 == $instance['list'] ? true : false );
		$instance['count'] = ( isset( $instance['count'] )  && 1 == $instance['count'] ? true : false );
		$instance['css']   = ( isset( $instance['css'] )  && 1 == $instance['css'] ? true : false );
		$instance['limit'] = ( isset( $instance['limit'] ) ? $instance['limit'] : WPML_MAX_TAXONOMY_LIST );

		// Display the admin form
		echo WPMovieLibrary::render_template( 'actor-widget/actor-admin.php', array( 'widget' => $this, 'instance' => $instance ), $require = 'always' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}
