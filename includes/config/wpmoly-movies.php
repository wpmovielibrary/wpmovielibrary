<?php
/**
 * WPMovieLibrary Config Movies definition
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	wp_die();

$wpmoly_movie_details = array(
	'movie_media'   => array(
		'title' => __( 'Media', 'wpmovielibrary' ),
		'options' => array(
			'dvd'     => __( 'DVD', 'wpmovielibrary' ),
			'bluray'  => __( 'Blu-ray', 'wpmovielibrary' ),
			'vod'     => __( 'VoD', 'wpmovielibrary' ),
			'divx'    => __( 'DivX', 'wpmovielibrary' ),
			'vhs'     => __( 'VHS', 'wpmovielibrary' ),
			'cinema'  => __( 'Cinema', 'wpmovielibrary' ),
			'other'   => __( 'Other', 'wpmovielibrary' ),
		),
		'default' => array(
			'dvd'   => __( 'DVD', 'wpmovielibrary' ),
		),
	),
	'movie_status'  => array(
		'title' => __( 'Status', 'wpmovielibrary' ),
		'options' => array(
			'available'   => __( 'Available', 'wpmovielibrary' ),
			'loaned'      => __( 'Loaned', 'wpmovielibrary' ),
			'scheduled'   => __( 'Scheduled', 'wpmovielibrary' ),
			'unavailable' => __( 'Unvailable', 'wpmovielibrary' ),
		),
		'default' => array(
			'available' => __( 'Available', 'wpmovielibrary' ),
		)
	),
	'movie_rating'  => array(
		'title' => __( 'Rating', 'wpmovielibrary' ),
		'options' => array(
			'0.5' => __( 'Junk', 'wpmovielibrary' ),
			'1.0' => __( 'Very bad', 'wpmovielibrary' ),
			'1.5' => __( 'Bad', 'wpmovielibrary' ),
			'2.0' => __( 'Not that bad', 'wpmovielibrary' ),
			'2.5' => __( 'Average', 'wpmovielibrary' ),
			'3.0' => __( 'Not bad', 'wpmovielibrary' ),
			'3.5' => __( 'Good', 'wpmovielibrary' ),
			'4.0' => __( 'Very good', 'wpmovielibrary' ),
			'4.5' => __( 'Excellent', 'wpmovielibrary' ),
			'5.0' => __( 'Masterpiece', 'wpmovielibrary' )
		),
		'default' => array(
			'0.0' => '',
		)
	)
);

$wpmoly_movie_meta = array(
	'title' => array(
		'title' => __( 'Title', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'wp_kses',
		'filter_args' => array( 'b' => array(), 'i' => array(), 'em' => array(), 'strong' => array(), 'sup' => array(), 'sub' => array() ),
		'size' => 'half',
		'group' => 'meta'
	),
	'original_title' => array(
		'title' => __( 'Original Title', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'wp_kses',
		'filter_args' => array( 'b' => array(), 'i' => array(), 'em' => array(), 'strong' => array(), 'sup' => array(), 'sub' => array() ),
		'size' => 'half',
		'group' => 'meta'
	),
	'overview' => array(
		'title' => __( 'Overview', 'wpmovielibrary' ),
		'type' => 'textarea',
		'filter' => 'wp_kses',
		'filter_args' => array( 'b' => array(), 'i' => array(), 'em' => array(), 'strong' => array(), 'sup' => array(), 'sub' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(), 'br' => array(), 'span' => array() ),
		'size' => 'full',
		'group' => 'meta'
	),
	'release_date' => array(
		'title' => __( 'Release Date', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'meta'
	),
	'runtime' => array(
		'title' => __( 'Runtime', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'meta'
	),
	'production_companies' => array(
		'title' => __( 'Production', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'meta'
	),
	'production_countries' => array(
		'title' => __( 'Country', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'meta'
	),
	'spoken_languages' => array(
		'title' => __( 'Languages', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'meta'
	),
	'genres' => array(
		'title' => __( 'Genres', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'full',
		'group' => 'meta'
	),
	'director' => array(
		'job' => 'Director',
		'title' => __( 'Director', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'crew'
	),
	'producer' => array(
		'job' => 'Producer',
		'title' => __( 'Producer', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'crew'
	),
	'cast' => array(
		'title' => __( 'Actors', 'wpmovielibrary' ),
		'type' => 'textarea',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'full',
		'group' => 'crew'
	),
	'photography' => array(
		'job' => 'Director of Photography',
		'title' => __( 'Director of Photography', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'crew'
	),
	'composer' => array(
		'job' => 'Original Music Composer',
		'title' => __( 'Original Music Composer', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'crew'
	),
	'author' => array(
		'job' => 'Author',
		'title' => __( 'Author', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'crew'
	),
	'writer' => array(
		'job' => 'Writer',
		'title' => __( 'Writer', 'wpmovielibrary' ),
		'type' => 'text',
		'filter' => 'esc_html',
		'filter_args' => null,
		'size' => 'half',
		'group' => 'crew'
	)
);

$wpmoly_movie_meta_aliases = array(

	'country'    => 'production_countries',
	'production' => 'production_companies',
	'lang'       => 'spoken_languages',
	'language'   => 'spoken_languages',
	'languages'  => 'spoken_languages',
	'actors'     => 'cast',
	'resume'     => 'overview',
	'musician'   => 'composer',
	'date'       => 'release_date',
	'musician'   => 'composer'
);

$wpmoly_metaboxes = array(

	/*array(
		'id'            => 'wpmoly_details',
		'title'         => __( 'WPMovieLibrary − Movie Details', 'wpmovielibrary' ),
		'callback'      => 'WPMOLY_Edit_Movies::metabox_details',
		'screen'        => 'movie',
		'context'       => 'side',
		'priority'      => 'core',
		'callback_args' => null
	),
	array(
		'id'            => 'wpmoly_meta',
		'title'         => __( 'WPMovieLibrary − Movie Meta', 'wpmovielibrary' ),
		'callback'      => 'WPMOLY_Edit_Movies::metabox_meta',
		'screen'        => 'movie',
		'context'       => 'normal',
		'priority'      => 'high',
		'callback_args' => null
	),
	array(
		'id'            => 'wpmoly_images',
		'title'         => __( 'WPMovieLibrary − Movie Images', 'wpmovielibrary' ),
		'callback'      => 'WPMOLY_Edit_Movies::metabox_images',
		'screen'        => 'movie',
		'context'       => 'normal',
		'priority'      => 'high',
		'callback_args' => null
	),*/
);