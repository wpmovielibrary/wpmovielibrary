<?php
/**
 * WPMovieLibrary Default Config
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$wpml_settings = array(
	'settings_revision' => WPML_SETTINGS_REVISION,
	'wpml' => array(
		'settings' => array(
			'meta_in_posts'    => 'posts_only',
			'details_in_posts' => 'posts_only',
			'details_as_icons' => 1,
			'default_movie_meta' => array(
				'director',
				'genres',
				'runtime',
				'overview',
				'rating'
			),
			'default_movie_details' => array(
				'movie_media',
				'movie_status'
			),
			'show_in_home'          => 1,
			'enable_collection'     => 1,
			'enable_actor'          => 1,
			'enable_genre'          => 1,
			'taxonomy_autocomplete' => 1,
			'deactivate' => array(
				'movies'      => 'conserve',
				'collections' => 'conserve',
				'genres'      => 'conserve',
				'actors'      => 'conserve',
				'cache'       => 'empty'
			),
			'uninstall' => array(
				'movies'      => 'convert',
				'collections' => 'convert',
				'genres'      => 'convert',
				'actors'      => 'convert',
				'cache'       => 'empty'
			)
		)
	),
	'tmdb' => array(
		'settings' => array(
			'APIKey'          => '',
			'dummy'           => 1,
			'lang'            => 'en',
			'scheme'          => 'https',
			'caching'         => 1,
			'caching_time'    => 15,
			'poster_size'     => 'original',
			'poster_featured' => 1,
			'images_size'     => 'original',
			'images_max'      => 12,
		)
	),
);

$wpml_movie_details = array(
	'movie_media'   => array(
		'title' => __( 'Media', 'wpml' ),
		'options' => array(
			'dvd'     => __( 'DVD', 'wpml' ),
			'bluray'  => __( 'BluRay', 'wpml' ),
			'vod'     => __( 'VOD', 'wpml' ),
			'vhs'     => __( 'VHS', 'wpml' ),
			'cinema'  => __( 'Cinema', 'wpml' ),
			'other'   => __( 'Other', 'wpml' ),
		),
		'default' => array(
			'dvd'   => __( 'DVD', 'wpml' ),
		),
	),
	'movie_status'  => array(
		'title' => __( 'Status', 'wpml' ),
		'options' => array(
			'available' => __( 'Available', 'wpml' ),
			'loaned'    => __( 'Loaned', 'wpml' ),
			'scheduled' => __( 'Scheduled', 'wpml' ),
		),
		'default' => array(
			'available' => __( 'Available', 'wpml' ),
		)
	),
	'movie_rating'  => array(
		'title' => __( 'Rating', 'wpml' ),
		'options' => array(
			'0.5' => __( 'Junk', 'wpml' ),
			'1.0' => __( 'Very bad', 'wpml' ),
			'1.5' => __( 'Bad', 'wpml' ),
			'2.0' => __( 'Not that bad', 'wpml' ),
			'2.5' => __( 'Average', 'wpml' ),
			'3.0' => __( 'Not bad', 'wpml' ),
			'3.5' => __( 'Good', 'wpml' ),
			'4.0' => __( 'Very good', 'wpml' ),
			'4.5' => __( 'Excellent', 'wpml' ),
			'5.0' => __( 'Masterpiece', 'wpml' )
		),
		'default' => array(
			'0.0' => '',
		)
	)
);

$wpml_movie_meta = array(
	'meta' => array(
		'type' => __( 'Type', 'wpml' ),
		'value' => __( 'Value', 'wpml' ),
		'data' => array(
			'title' => array(
				'title' => __( 'Title', 'wpml' ),
				'type' => 'text'
			),
			'original_title' => array(
				'title' => __( 'Original Title', 'wpml' ),
				'type' => 'text'
			),
			'overview' => array(
				'title' => __( 'Overview', 'wpml' ),
				'type' => 'textarea'
			),
			'production_companies' => array(
				'title' => __( 'Production', 'wpml' ),
				'type' => 'text'
			),
			'production_countries' => array(
				'title' => __( 'Country', 'wpml' ),
				'type' => 'text'
			),
			'spoken_languages' => array(
				'title' => __( 'Languages', 'wpml' ),
				'type' => 'text'
			),
			'runtime' => array(
				'title' => __( 'Runtime', 'wpml' ),
				'type' => 'text'
			),
			'genres' => array(
				'title' => __( 'Genres', 'wpml' ),
				'type' => 'text'
			),
			'release_date' => array(
				'title' => __( 'Release Date', 'wpml' ),
				'type' => 'text'
			)
		)
	),
	'crew' => array(
		'type' => __( 'Job', 'wpml' ),
		'value' => __( 'Name(s)', 'wpml' ),
		'data' => array(
			'director' => array(
				'title' => __( 'Director', 'wpml' ),
				'type' => 'text'
			),
			'producer' => array(
				'title' => __( 'Producer', 'wpml' ),
				'type' => 'text'
			),
			'photography' => array(
				'title' => __( 'Director of Photography', 'wpml' ),
				'type' => 'text'
			),
			'composer' => array(
				'title' => __( 'Original Music Composer', 'wpml' ),
				'type' => 'text'
			),
			'author' => array(
				'title' => __( 'Author', 'wpml' ),
				'type' => 'text'
			),
			'writer' => array(
				'title' => __( 'Writer', 'wpml' ),
				'type' => 'text'
			),
			'cast' => array(
				'title' => __( 'Actors', 'wpml' ),
				'type' => 'textarea'
			)
		)
	)
);