<?php
/**
 * The file that defines the plugin template functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\utils;

/**
 * Get a specific template.
 *
 * @since 3.0.0
 *
 * @param string $template Template name.
 *
 * @return \wpmoly\templates\Template
 */
function get_template( $template ) {

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
function get_js_template( $template ) {

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
 * Get a Widget public or admin template.
 *
 * @since 3.0.0
 *
 * @param string $template_id Template ID
 *
 * @return \wpmoly\templates\Template
 */
function get_widget_template( $template_id ) {

	return get_template( 'widgets/' . (string) $template_id . '.php' );
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
		'caret' => array(
			'paths' => 'm 6,8 h 8 l -4,5 z',
		),
		'category' => array(
			'paths' => 'M 9.0000001,4 10,5 h 8 V 17 H 2 V 4 Z',
		),
		'check' => array(
			'paths' => 'M 2,12 4,10 7,13 16,3 18,5 7,17 Z',
		),
		'close' => array(
			'paths' => 'M 3.636039,5.0502525 5.050252,3.636039 10,8.5857864 14.949747,3.636039 16.363961,5.0502525 11.414214,10 16.363961,14.949747 14.949747,16.363961 10,11.414214 5.050252,16.363961 3.636039,14.949747 8.585786,10 Z',
		),
		'collection' => array(
			'paths' => 'M 9,5 8,3 H 2 V 17 H 17.999628 V 7.1335653 6.6002417 L 18,5 Z M 17,7 10,7 9.5,6 H 17 Z',
		),
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
		'keyboard' => array(
			'paths' => array(
				'M 3,8 V 6 h 2 v 2 z',
				'M 17.003135,6.0047075 17,11 H 14 V 9 h 1 V 6 c 0,0 1.672723,0.00283 2.003135,0.00471 z',
				'M 0,3 V 17 H 20 V 3 Z M 1,4 H 19 V 16 H 1 Z',
				'm 3,14 v -2 h 2 v 2 z',
				'm 15,14 v -2 h 2 v 2 z',
				'M 6,8 V 6 h 2 v 2 z',
				'M 9,8 V 6 h 2 v 2 z',
				'M 12,8 V 6 h 2 v 2 z',
				'M 3,11 V 9 h 4 v 2 z',
				'M 8,11 V 9 h 2 v 2 z',
				'M 11,11 V 9 h 2 v 2 z',
				'm 6,14 v -2 h 8 v 2 z',
			),
		),
		'maximize' => array(
			'paths' => array(
				'm 2,2 v 8 H 3 V 3 h 14 v 14 h -7 v 1 h 8 V 2 Z m 0,10 v 6 H 8 V 17 12 H 3 Z m 1,1 h 4 v 4 H 3 Z',
				'M 10 5 L 10 6 L 13 6 L 9 10 L 10 11 L 14 7 L 14 10 L 15 10 L 15 5 L 10 5 z',
			),
		),
		'menu' => array(
			'paths' => array(
				'm 20,10 a 2,2 0 0 1 -2,2 2,2 0 0 1 -2,-2 2,2 0 0 1 2,-2 2,2 0 0 1 2,2 z',
				'm 12,10 a 2,2 0 0 1 -2,2 2,2 0 0 1 -2,-2 2,2 0 0 1 2,-2 2,2 0 0 1 2,2 z',
				'M 4,10 A 2,2 0 0 1 2,12 2,2 0 0 1 0,10 2,2 0 0 1 2,8 2,2 0 0 1 4,10 Z',
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
		'picture' => array(
			'paths' => 'M 3 1 L 3 19 L 17 19 L 17 1 L 3 1 z M 4 2 L 16 2 L 16 16.5 C 16 14.925 14.715625 13.575 13.140625 13.5 C 12.315625 14.4 11.2 15 10 15 C 8.8004757 15 7.6842785 14.399405 6.859375 13.5 L 6.8574219 13.5 C 5.2833183 13.576029 4 14.925655 4 16.5 L 4 2 z M 10 6 C 8.34325 6 7 7.67925 7 9.75 C 7 11.82075 8.34325 13.5 10 13.5 C 11.65675 13.5 13 11.82075 13 9.75 C 13 7.67925 11.65675 6 10 6 z',
		),
		'preview' => array(
			//'paths' => 'M 0 2 L 0 15 L 9 15 L 9 16 L 4 16 L 4 17 L 16 17 L 16 16 L 11 16 L 11 15 L 20 15 L 20 2 L 0 2 z M 1 3 L 19 3 L 19 14 L 1 14 L 1 3 z ',
			'paths' => 'm 0,2 v 13 h 8 v 1 H 3 v 2 h 14 v -2 h -5 v -1 h 8 V 2 Z m 2,2 h 16 v 9 H 2 Z',
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
				'm 17.126458,8.706458 c 0.0033,-0.07708 0.0058,-0.154375 0.0058,-0.232291 0,-2.839167 -2.301459,-5.140834 -5.140625,-5.140834 -2.056042,0 -3.829375,1.2075 -4.651459,2.951667 C 6.883508,5.958958 6.319341,5.766042 5.708508,5.766042 c -1.519792,0 -2.751667,1.192083 -2.751667,2.6625 0,0.218333 0.02771,0.430208 0.07896,0.633125 C 1.302292,9.430833 0,10.994167 0,12.874792 c 0,2.154791 1.71,3.791875 3.819375,3.791875 H 16.345 c 2.018542,0 3.655,-1.719375 3.655,-3.975417 0,-1.956458 -1.230625,-3.584583 -2.873542,-3.984792 z',
			),
		),
		'tag' => array(
			'paths' => 'm 2,2 c 0,2.3333333 0,4.6666667 0,7 3,3 6,6 9,9 2.333333,-2.333333 4.666667,-4.666667 7,-7 C 15,8 12,5 9,2 6.6666667,2 4.3333333,2 2,2 Z M 6,4 C 7.2987278,3.94904 8.340251,5.3688113 7.910152,6.591797 7.5736107,7.842203 5.9134815,8.426699 4.8710938,7.650391 3.7719354,6.9596289 3.7129981,5.2021896 4.7558628,4.433594 5.1061606,4.1538483 5.5519898,3.9996616 6,4 Z',
		),
		'trash' => array(
			'paths' => array(
				'M 5,6 V 18 H 15 V 6 Z m 1,1 h 8 V 17 H 6 Z',
				'M 7 2 L 7 4 L 4 4 L 4 5 L 16 5 L 16 4 L 13 4 L 13 2 L 7 2 z M 8 3 L 12 3 L 12 4 L 8 4 L 8 3 z',
				'm 8,8 h 1 v 8 H 8 Z',
				'm 11,8 h 1 v 8 h -1 z',
			),
		),
		'upload' => array(
			'paths' => array(
				'm 1,14 2,-2 h 3 l -2,2 h 3 v 2 h 6 v -2 h 3 l -2,-2 h 3 l 2,2 v 5 H 1 Z',
				'm 10,2 5,5 h -4 v 5 H 9 V 7 H 5 Z',
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
