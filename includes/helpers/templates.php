<?php
/**
 * The file that defines the plugin template functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

/**
 * Get a specific template.
 *
 * @since 3.0.0
 *
 * @param string $template Template name.
 *
 * @return \wpmoly\templates\Template
 */
function wpmoly_get_template( $template ) {

	if ( is_admin() ) {
		return new \wpmoly\templates\Admin( $template );
	}

	return new \wpmoly\templates\Front( $template );
}

/**
 * Get a specific JavaScript template.
 *
 * @since 3.0.0
 *
 * @param string $template Template name.
 *
 * @return \wpmoly\templates\JavaScript
 */
function wpmoly_get_js_template( $template ) {

	$template = new \wpmoly\templates\JavaScript( $template );

	return $template;
}

/**
 * Get an Headbox template.
 *
 * @since 3.0.0
 *
 * @param Headbox $headbox Headbox instance.
 *
 * @return \wpmoly\templates\Headbox
 */
function get_headbox_template( $headbox ) {

	if ( ! $headbox instanceof \wpmoly\nodes\headboxes\Headbox ) {
		$headbox = get_headbox( $headbox );
	}

	return new \wpmoly\templates\Headbox( $headbox );
}

/**
 * Get a Movie Headbox template.
 *
 * Simple alias for get_headbox_template().
 *
 * @since 3.0.0
 *
 * @param int $movie Movie ID, object or array
 *
 * @return \wpmoly\templates\Headbox
 */
function get_movie_headbox_template( $movie ) {

	return get_headbox_template( $movie );
}

/**
 * Get a Actor Headbox template.
 *
 * Simple alias for get_headbox_template().
 *
 * @since 3.0.0
 *
 * @param mixed $actor Actor ID, object or array
 *
 * @return \wpmoly\templates\Headbox
 */
function get_actor_headbox_template( $actor ) {

	return get_headbox_template( $actor );
}

/**
 * Get a Collection Headbox template.
 *
 * Simple alias for get_headbox_template().
 *
 * @since 3.0.0
 *
 * @param mixed $collection Collection ID, object or array
 *
 * @return   \wpmoly\templates\Headbox
 */
function get_collection_headbox_template( $collection ) {

	return get_headbox_template( $collection );
}

/**
 * Get a Genre Headbox template.
 *
 * Simple alias for get_headbox_template().
 *
 * @since 3.0.0
 *
 * @param mixed $genre Genre ID, object or array
 *
 * @return \wpmoly\templates\Headbox
 */
function get_genre_headbox_template( $genre ) {

	return get_headbox_template( $genre );
}

/**
 * Get a Grid template.
 *
 * @since 3.0.0
 *
 * @param mixed $grid Grid
 *
 * @return \wpmoly\templates\Grid
 */
function get_grid_template( $grid ) {

	return new \wpmoly\templates\Grid( $grid );
}

/**
 * Get a Widget public or admin template.
 *
 * @since 3.0.0
 *
 * @param string $template_id Template ID
 *
 * @return \wpmoly\templates\Template
 */
function get_widget_template( $template_id ) {

	return wpmoly_get_template( 'widgets/' . (string) $template_id . '.php' );
}

/**
 * Get default template SVG icons data.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_template_default_icons() {

	$icons = array(
		'discover' => array(
			'paths' => array(
				'M 9.9824219,2 C 5.5664316,2.0096984 1.9935205,5.5957228 2,10.011719 2.0064677,14.427708 5.5898663,18.003239 10.005859,18 14.421849,17.996766 18.000001,14.415991 18,10 h -1 c 1e-6,3.863706 -3.130436,6.996766 -6.994141,7 C 6.1421506,17.003239 3.006467,13.875423 3,10.011719 2.9935207,6.1472458 6.119906,3.0086206 9.984375,3 13.84809,2.9913574 16.988151,6.1402033 17,10.00391 L 18,10 C 17.985997,5.5847754 14.397662,1.9902763 9.9824219,2 Z',
				'M 14 6 L 8 8 L 6 14 L 12 12 L 14 6 z M 9.9980469 9 A 1 1 0 0 1 11 9.9960938 L 10 10 L 11 10 A 1 1 0 0 1 10 11 A 1 1 0 0 1 9 10.001953 A 1 1 0 0 1 9.9980469 9 z ',
			),
		),
		'download' => array(
			'paths' => array(
				'm 1,14 2,-2 h 3 l -2,2 h 3 v 2 h 6 v -2 h 3 l -2,-2 h 3 l 2,2 v 5 H 1 Z',
				'm 9,2 h 2 v 5 h 4 L 10.004062,12 5,7 h 4 z',
			),
		),
		'edit' => array(
			'paths' => 'M 14 2 L 3 13 L 2 18 L 7 17 L 18 6 L 14 2 z M 4.1542969 12.847656 L 7.1542969 15.847656 L 6.7441406 16.25 L 3 17 L 3.75 13.244141 L 4.1542969 12.847656 z',
		),
		'edit-post' => array(
			'paths' => 'M 16.200136,0.4665753 19.045774,3.2688023 15,7.33 V 18 H 3 V 2 H 14.67 Z M 12.23,8.68 17.6,3.32 16.18,1.9 10.82,7.27 10.11,9.39 Z',
		),
		'explore' => array(
			'paths' => 'M 19,1 C 18,1.1498866 13.380424,2.0828325 9,6 5.005332,5.4877578 1.467832,8.314736 1,10 l 3.980469,0.337891 0.191406,0.191406 C 4.718283,11.313642 3.377264,13.16795 3,14 l 3,3 C 6.857373,16.624212 8.7057,15.244967 9.470703,14.828125 L 9.662109,15.019531 9,19 c 2.807886,-0.929894 5.407421,-3.945324 5,-7 3.896605,-4.3808916 4.835966,-10 5,-11 z m -6,4 c 0.942809,0 2.007252,1.0572188 2,2 -0.0071,0.9276997 -1.07032,1.9370473 -1.998047,1.9375 C 12.073291,8.9379532 11.007596,7.9286312 11,7 10.992288,6.0572225 12.057191,5 13,5 Z',
		),
		'grid' => array(
			'paths' => array(
				'm 8,2 h 4 V 6 H 8 Z',
				'm 14,2 h 4 v 4 h -4 z',
				'M 2,2 H 6 V 6 H 2 Z',
				'm 2,8 h 4 v 4 H 2 Z',
				'm 8,8 h 4 v 4 H 8 Z',
				'm 14,8 h 4 v 4 h -4 z',
				'm 14,14 h 4 v 4 h -4 z',
				'm 8,14 h 4 v 4 H 8 Z',
				'm 2,14 h 4 v 4 H 2 Z',
			),
		),
		'maximize' => array(
			'paths' => array(
				'm 2,2 v 8 H 3 V 3 h 14 v 14 h -7 v 1 h 8 V 2 Z m 0,10 v 6 H 8 V 17 12 H 3 Z m 1,1 h 4 v 4 H 3 Z',
				'M 10 5 L 10 6 L 13 6 L 9 10 L 10 11 L 14 7 L 14 10 L 15 10 L 15 5 L 10 5 z',
			),
		),
		'minimize' => array(
			'paths' => array(
				'm 2,2 v 8 H 3 V 3 h 14 v 14 h -7 v 1 h 8 V 2 Z m 0,10 v 6 H 8 V 17 12 H 3 Z m 1,1 h 4 v 4 H 3 Z',
				'm 9,5 h 1 V 9 L 14,5 14,5 15,6 l -4,4 4,0 V 11 H 9 Z',
			),
		),
		'movie' => array(
			'paths' => 'M 0,3 V 5 H 2 V 7 H 0 v 2 h 2 v 2 H 0 v 2 h 2 v 2 H 0 v 2 h 20 v -2 h -2 v -2 h 2 V 11 H 18 V 9 h 2 V 7 L 18,7 V 5 h 2 V 3 Z m 8,4 5,3 -5,3 z',
		),
		'save' => array(
			'paths' => array(
				'M 16,2 H 15 V 8 H 5 V 2 H 2 V 18 H 18 V 4 Z m 0,15 H 4 v -7 h 12 z',
				'm 5,13 h 10 v 1 H 5 Z',
				'm 5,15 h 10 v 1 H 5 Z',
				'm 5,11 h 10 v 1 H 5 Z',
				'm 12,2 h 2 v 5 h -2 z',
			),
		),
		'snapshot' => array(
			'paths' => array(
				'M 2,2 H 8 V 3 L 3,3 3,8 H 2 Z',
				'm 12,2 h 6 V 8 H 17 L 17,3 h -5 z',
				'M 2,12 H 3 L 3,17 l 5,0 V 18 H 2 Z',
				'm 17,17 v -5 h 1 v 6 h -6 v -1 z',
			),
		),
		'trash' => array(
			'paths' => array(
				'M 5,6 V 18 H 15 V 6 Z m 1,1 h 8 V 17 H 6 Z',
				'M 7 2 L 7 4 L 4 4 L 4 5 L 16 5 L 16 4 L 13 4 L 13 2 L 7 2 z M 8 3 L 12 3 L 12 4 L 8 4 L 8 3 z',
				'm 8,8 h 1 v 8 H 8 Z',
				'm 11,8 h 1 v 8 h -1 z',
			),
		),
	);

	/**
	 * Filter default SVG icons data.
	 *
	 * @since 3.0.0
	 *
	 * @param array $icons Icons data.
	 */
	$icons = apply_filters( 'wpmoly/filter/template/default/icons', $icons );

	return $icons;
}
