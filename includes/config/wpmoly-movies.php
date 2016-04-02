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

$default_details = array(
	'status' => array(
		'type'     => 'select',
		'size'     => 'half',
		'title'    => __( 'Movie Status', 'wpmovielibrary' ),
		'desc'     => __( 'Select a status for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-status',
		'options'  => array(
			'available'   => __( 'Available', 'wpmovielibrary' ),
			'loaned'      => __( 'Loaned', 'wpmovielibrary' ),
			'scheduled'   => __( 'Scheduled', 'wpmovielibrary' ),
			'unavailable' => __( 'Unvailable', 'wpmovielibrary' ),
		),
		'default'  => '',
		'multi'    => false,
		'rewrite'  => array( 'status' => __( 'status', 'wpmovielibrary' ) ),

		'sanitize' => 'sanitize_key',
		'sanitize_args' => null,
		'escape'   => 'esc_attr'
	),
	'media' => array(
		'type'     => 'select',
		'size'     => 'half',
		'title'    => __( 'Movie Media', 'wpmovielibrary' ),
		'desc'     => __( 'Select a media for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-video',
		'options'  => array(
			'dvd'     => __( 'DVD', 'wpmovielibrary' ),
			'bluray'  => __( 'Blu-ray', 'wpmovielibrary' ),
			'vod'     => __( 'VoD', 'wpmovielibrary' ),
			'divx'    => __( 'DivX', 'wpmovielibrary' ),
			'vhs'     => __( 'VHS', 'wpmovielibrary' ),
			'cinema'  => __( 'Cinema', 'wpmovielibrary' ),
			'other'   => __( 'Other', 'wpmovielibrary' ),
		),
		'default'  => 'dvd',
		'multi'    => true,
		'rewrite'  => array( 'media' => __( 'media', 'wpmovielibrary' ) ),

		'sanitize' => 'sanitize_key',
		'sanitize_args' => null,
		'escape'   => 'esc_attr'
	),
	'rating' => array(
		'type'     => 'select',
		'size'     => 'half',
		'title'    => __( 'Movie Rating', 'wpmovielibrary' ),
		'desc'     => __( 'Select a rating for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-star-half',
		'options'  => array(
			'0.0' => __( 'Not rated', 'wpmovielibrary' ),
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
		'default'  => '0.0',
		'multi'    => false,
		'rewrite'  => array( 'rating' => __( 'rating', 'wpmovielibrary' ) ),

		'sanitize' => 'sanitize_key',
		'sanitize_args' => null,
		'escape'   => 'esc_attr'
	),
	'language' => array(
		'type'     => 'select',
		'size'     => 'half',
		'title'    => __( 'Movie Language', 'wpmovielibrary' ),
		'desc'     => __( 'Select a language for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-lang',
		'options'  => $supported_languages,
		'default'  => '',
		'multi'    => true,
		'rewrite'  => array( 'lang' => __( 'lang', 'wpmovielibrary' ) ),

		'sanitize' => 'sanitize_key',
		'sanitize_args' => null,
		'escape'   => 'esc_attr'
	),
	'subtitles' => array(
		'type'     => 'select',
		'size'     => 'half',
		'title'    => __( 'Movie Subtitles', 'wpmovielibrary' ),
		'desc'     => __( 'Select a subtitle for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-subtitles',
		'options'  => array_merge( array( 'none' => __( 'None', 'wpmovielibrary' ) ), $supported_languages ),
		'default'  => 'none',
		'multi'    => true,
		'rewrite'  => array( 'subtitles' => __( 'subtitles', 'wpmovielibrary' ) ),

		'sanitize' => 'sanitize_key',
		'sanitize_args' => null,
		'escape'   => 'esc_attr'
	),
	'format' => array(
		'type'     => 'select',
		'size'     => 'half',
		'title'    => __( 'Movie Format', 'wpmovielibrary' ),
		'desc'     => __( 'Select a format for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-format',
		'options'  => array(
			'3d' => __( '3D', 'wpmovielibrary' ),
			'sd' => __( 'SD', 'wpmovielibrary' ),
			'hd' => __( 'HD', 'wpmovielibrary' ),
		),
		'default'  => '',
		'multi'    => true,
		'rewrite'  => array( 'format' => __( 'format', 'wpmovielibrary' ) ),

		'sanitize' => 'sanitize_key',
		'sanitize_args' => null,
		'escape'   => 'esc_attr'
	)
);

$default_meta = array(
	'tmdb_id' => array(
		'title'         => __( 'TMDb ID', 'wpmovielibrary' ),
		'type'          => 'hidden',
		'size'          => 'hidden',
		'group'         => 'meta',
		'rewrite'       => array( 'tmdb' => __( 'tmdb', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_key',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'title' => array(
		'title'         => __( 'Title', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'title' => __( 'title', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'original_title' => array(
		'title'         => __( 'Original Title', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'originaltitle' => __( 'originaltitle', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'tagline' => array(
		'title'         => __( 'Tagline', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'full',
		'group'         => 'meta',
		'rewrite'       => array( 'tagline' => __( 'tagline', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'overview' => array(
		'title'         => __( 'Overview', 'wpmovielibrary' ),
		'type'          => 'textarea',
		'size'          => 'full',
		'group'         => 'meta',
		'rewrite'       => array( 'overview' => __( 'overview', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'release_date' => array(
		'title'         => __( 'Release Date', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'date' => __( 'date', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'local_release_date' => array(
		'title'         => __( 'Local Release Date', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'local_date' => __( 'localdate', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'runtime' => array(
		'title'         => __( 'Runtime', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'runtime' => __( 'runtime', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'production_companies' => array(
		'title'         => __( 'Production', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'production' => __( 'production', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'production_countries' => array(
		'title'         => __( 'Country', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'country' => __( 'country', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'spoken_languages' => array(
		'title'         => __( 'Languages', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'language' => __( 'language', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'genres' => array(
		'title'         => __( 'Genres', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'full',
		'group'         => 'meta',
		'rewrite'       => array( 'genres' => __( 'genres', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'director' => array(
		'job' => 'Director',
		'title'         => __( 'Director', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'crew',
		'rewrite'       => array( 'director' => __( 'director', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'producer' => array(
		'job' => 'Producer',
		'title'         => __( 'Producer', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'crew',
		'rewrite'       => array( 'producer' => __( 'producer', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'cast' => array(
		'title'         => __( 'Actors', 'wpmovielibrary' ),
		'type'          => 'textarea',
		'size'          => 'full',
		'group'         => 'crew',
		'rewrite'       => array( 'actor' => __( 'actor', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'photography' => array(
		'job' => 'Director of Photography',
		'title'         => __( 'Director of Photography', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'crew',
		'rewrite'       => array( 'photography' => __( 'photography', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'composer' => array(
		'job' => 'Original Music Composer',
		'title'         => __( 'Original Music Composer', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'crew',
		'rewrite'       => array( 'composer' => __( 'composer', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'author' => array(
		'job' => 'Author',
		'title'         => __( 'Author', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'crew',
		'rewrite'       => array( 'author' => __( 'author', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'writer' => array(
		'job' => 'Writer',
		'title'         => __( 'Writer', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'crew',
		'rewrite'       => array( 'writer' => __( 'writer', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'certification' => array(
		'title'         => __( 'Certification', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'certification' => __( 'certification', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'budget' => array(
		'title'         => __( 'Budget', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'budget' => __( 'budget', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'revenue' => array(
		'title'         => __( 'Revenue', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'revenue' => __( 'revenue', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'imdb_id' => array(
		'title'         => __( 'IMDb Id', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'imdb' => __( 'imdb', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'adult' => array(
		'title'         => __( 'Adult', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => array( 'adult' => __( 'adult', 'wpmovielibrary' ) ),

		'sanitize'      => 'sanitize_text_field',
		'sanitize_args' => null,
		'escape'        => 'esc_attr'
	),
	'homepage' => array(
		'title'         => __( 'Homepage', 'wpmovielibrary' ),
		'type'          => 'text',
		'size'          => 'half',
		'group'         => 'meta',
		'rewrite'       => null
,
		'sanitize'      => 'esc_url_raw',
		'sanitize_args' => null,
		'escape'        => 'esc_url'

	)
);

$meta_aliases = array(

	'country'    => 'production_countries',
	'production' => 'production_companies',
	'lang'       => 'spoken_languages',
	'language'   => 'spoken_languages',
	'languages'  => 'spoken_languages',
	'actors'     => 'cast',
	'resume'     => 'overview',
	'date'       => 'release_date',
	'musician'   => 'composer'
);

$tags = array(
	'media'              => __( 'Media', 'wpmovielibrary' ),
	'status'             => __( 'Status', 'wpmovielibrary' ),
	'rating'             => __( 'Rating', 'wpmovielibrary' ),
	'language'           => __( 'Language (detail)', 'wpmovielibrary' ),
	'subtitles'          => __( 'Subtitles', 'wpmovielibrary' ),
	'format'             => __( 'Video Format', 'wpmovielibrary' ),
	'director'           => __( 'Director', 'wpmovielibrary' ),
	'runtime'            => __( 'Runtime', 'wpmovielibrary' ),
	'release_date'       => __( 'Release date', 'wpmovielibrary' ),
	'genres'             => __( 'Genres', 'wpmovielibrary' ),
	'overview'           => __( 'Overview', 'wpmovielibrary' ),
	'title'              => __( 'Title', 'wpmovielibrary' ),
	'original_title'     => __( 'Original Title', 'wpmovielibrary' ),
	'production'         => __( 'Production', 'wpmovielibrary' ),
	'countries'          => __( 'Country', 'wpmovielibrary' ),
	'languages'          => __( 'Languages', 'wpmovielibrary' ),
	'producer'           => __( 'Producer', 'wpmovielibrary' ),
	'local_release_date' => __( 'Local release date', 'wpmovielibrary' ),
	'photography'        => __( 'Director of Photography', 'wpmovielibrary' ),
	'composer'           => __( 'Original Music Composer', 'wpmovielibrary' ),
	'author'             => __( 'Author', 'wpmovielibrary' ),
	'writer'             => __( 'Writer', 'wpmovielibrary' ),
	'cast'               => __( 'Actors', 'wpmovielibrary' ),
	'certification'      => __( 'Certification', 'wpmovielibrary' ),
	'budget'             => __( 'Budget', 'wpmovielibrary' ),
	'revenue'            => __( 'Revenue', 'wpmovielibrary' ),
	'tagline'            => __( 'Tagline', 'wpmovielibrary' ),
	'imdb_id'            => __( 'IMDb Id', 'wpmovielibrary' ),
	'adult'              => __( 'Adult', 'wpmovielibrary' ),
	'homepage'           => __( 'Homepage', 'wpmovielibrary' )
);
