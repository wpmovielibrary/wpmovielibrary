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
	'status' => array(
		'id'       => 'wpmoly-movie-status',
		'name'     => 'wpmoly_details[status]',
		'type'     => 'select',
		'title'    => __( 'Movie Status', 'wpmovielibrary' ),
		'desc'     => __( 'Select a status for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-status',
		'options'  => array(
			'available'   => __( 'Available', 'wpmovielibrary' ),
			'loaned'      => __( 'Loaned', 'wpmovielibrary' ),
			'scheduled'   => __( 'Scheduled', 'wpmovielibrary' ),
			'unavailable' => __( 'Unvailable', 'wpmovielibrary' ),
		),
		'default'  => ''
	),
	'media' => array(
		'id'       => 'wpmoly-movie-media',
		'name'     => 'wpmoly_details[media]',
		'type'     => 'select',
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
		//'multi'    => true
	),
	'rating' => array(
		'id'       => 'wpmoly-movie-rating',
		'name'     => 'wpmoly_details[rating]',
		'type'     => 'select',
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
		'default'  => '0.0'
	),
	'language' => array(
		'id'       => 'wpmoly-movie-language',
		'name'     => 'wpmoly_details[language]',
		'type'     => 'select',
		'title'    => __( 'Movie Language', 'wpmovielibrary' ),
		'desc'     => __( 'Select a language for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-lang',
		'options'  => $wpmoly_supported_languages,
		'default'  => ''
	),
	'subtitles' => array(
		'id'       => 'wpmoly-movie-subtitles',
		'name'     => 'wpmoly_details[subtitles]',
		'type'     => 'select',
		'title'    => __( 'Movie Subtitles', 'wpmovielibrary' ),
		'desc'     => __( 'Select a subtitle for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-subtitles',
		'options'  => $wpmoly_supported_languages,
		'default'  => ''
	),
	'format' => array(
		'id'       => 'wpmoly-movie-format',
		'name'     => 'wpmoly_details[format]',
		'type'     => 'select',
		'title'    => __( 'Movie Format', 'wpmovielibrary' ),
		'desc'     => __( 'Select a format for this movie', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-format',
		'options'  => array(
			'3d' => __( '3D', 'wpmovielibrary' ),
			'sd' => __( 'SD', 'wpmovielibrary' ),
			'hd' => __( 'HD', 'wpmovielibrary' ),
		),
		'default'  => ''
	)
);

$wpmoly_movie_meta = array(
	'tmdb_id' => array(
		'title' => __( 'TMDb ID', 'wpmovielibrary' ),
		'type' => 'hidden',
		'filter' => 'intval',
		'filter_args' => null,
		'size' => 'hidden',
		'group' => 'meta'
	),
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

$wpmoly_metabox_panels = array(

	'preview' => array(
		'title'    => __( 'Preview', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-video',
		'callback' => 'WPMOLY_Edit_Movies::render_preview_panel'
	),

	'meta' => array(
		'title'    => __( 'Metadata', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-meta',
		'callback' => 'WPMOLY_Edit_Movies::render_meta_panel'
	),

	'details' => array(
		'title'    => __( 'Details', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-details',
		'callback' => 'WPMOLY_Edit_Movies::render_details_panel'
	),

	'images' => array(
		'title'    => __( 'Images', 'wpmovielibrary' ),
		'icon'     => 'wpmolicon icon-images-alt',
		'callback' => 'WPMOLY_Edit_Movies::render_images_panel'
	)

);