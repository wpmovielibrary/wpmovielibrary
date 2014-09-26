<?php
/**
 * WPMovieLibrary Config Admin menu definition
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

$wpml_admin_menu = array(
	'page' => array(
		'page_title' => WPML_NAME,
		'menu_title' => __( 'Movies', 'wpmovielibrary' ),
		'capability' => 'manage_options',
		'menu_slug'  => 'wpmovielibrary',
		'function'   => null,
		'icon_url'   => WPML_URL . '/assets/img/logo-18x18.png',
		'position'   => 6
	),
	'subpages' => array(
		'dashboard' => array(
			'page_title'  => __( 'Your library', 'wpmovielibrary' ),
			'menu_title'  => __( 'Your library', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'wpmovielibrary',
			'function'    => 'WPML_Dashboard::dashboard',
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(
				'load-{screen_hook}' => 'WPML_Dashboard::add_tabs'
			)
		),
		'all_movies' => array(
			'page_title'  => __( 'All Movies', 'wpmovielibrary' ),
			'menu_title'  => __( 'All Movies', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'edit.php?post_type=movie',
			'function'    => null,
			'condition'   => null,
			'hide'        => false,
			'actions'     => array()
		),
		'new_movie' => array(
			'page_title'  => __( 'Add New', 'wpmovielibrary' ),
			'menu_title'  => __( 'Add New', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'post-new.php?post_type=movie',
			'function'    => null,
			'condition'   => null,
			'hide'        => false,
			'actions'     => array()
		),
		'collections' => array(
			'page_title'  => __( 'Collections', 'wpmovielibrary' ),
			'menu_title'  => __( 'Collections', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'edit-tags.php?taxonomy=collection&post_type=movie',
			'function'    => null,
			'condition'   => function() { return wpmoly_o( 'enable-collection' ); },
			'hide'        => false,
			'actions'     => array()
		),
		'genres' => array(
			'page_title'  => __( 'Genres', 'wpmovielibrary' ),
			'menu_title'  => __( 'Genres', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'edit-tags.php?taxonomy=genre&post_type=movie',
			'function'    => null,
			'condition'   => function() { return wpmoly_o( 'enable-genre' ); },
			'hide'        => false,
			'actions'     => array()
		),
		'actors' => array(
			'page_title'  => __( 'Actors', 'wpmovielibrary' ),
			'menu_title'  => __( 'Actors', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'edit-tags.php?taxonomy=actor&post_type=movie',
			'function'    => null,
			'condition'   => function() { return wpmoly_o( 'enable-actor' ); },
			'hide'        => false,
			'actions'     => array()
		),
		'import' => array(
			'page_title'  => __( 'Import Movies', 'wpmovielibrary' ),
			'menu_title'  => __( 'Import Movies', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'wpml_import',
			'function'    => 'WPML_Import::import_page',
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(
				'load-{screen_hook}' => 'WPML_Import::import_movie_list_add_options'
			),
		),
		'update-movies' => array(
			'page_title'  => __( 'Update movies to version 1.3', 'wpmovielibrary' ),
			'menu_title'  => __( 'Update movies', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'wpml-update-movies',
			'function'    => 'WPML_Deprecated_Meta::update_movies_page',
			'condition'   => function() { return ( ! empty( $_GET["page"] ) && "wpml-update-movies" == $_GET["page"] ); },
			'hide'        => true,
			'actions'     => array()
		)
	)
);

		