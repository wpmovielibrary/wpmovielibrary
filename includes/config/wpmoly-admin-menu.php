<?php
/**
 * WPMovieLibrary Config Admin menu definition
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 Charlie MERLAND
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	wp_die();

$wpmoly_admin_menu = array(

	'page' => array(
		'page_title' => WPMOLY_NAME,
		'menu_title' => __( 'Movies', 'wpmovielibrary' ),
		'capability' => 'edit_posts',
		'menu_slug'  => 'wpmovielibrary',
		'function'   => null,
		'icon_url'   => 'none',
		'position'   => 6
	),

	'subpages' => array(

		'dashboard' => array(
			'page_title'  => __( 'Your library', 'wpmovielibrary' ),
			'menu_title'  => __( 'Your library', 'wpmovielibrary' ),
			'capability'  => 'read',
			'menu_slug'   => 'wpmovielibrary',
			'function'    => 'WPMOLY_Dashboard::dashboard',
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(
				'load-{screen_hook}' => 'WPMOLY_Dashboard::add_tabs'
			),
			'scripts'     => array(
				'dashboard' =>array(
					'file'    => sprintf( '%s/assets/js/admin/wpmoly-dashboard.js', WPMOLY_URL ),
					'require' => array( WPMOLY_SLUG . '-admin', 'jquery', 'jquery-ui-sortable' ),
					'footer'  => true
				)
			),
			'styles'      => array(
				'dashboard' => array(
					'file'    => sprintf( '%s/assets/css/admin/wpmoly-dashboard.css', WPMOLY_URL ),
					'require' => array(),
					'global'  => false
				)
			)
		),

		'all_movies' => array(
			'page_title'  => __( 'All Movies', 'wpmovielibrary' ),
			'menu_title'  => __( 'All Movies', 'wpmovielibrary' ),
			'capability'  => 'edit_posts',
			'menu_slug'   => 'edit.php?post_type=movie',
			'function'    => null,
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'new_movie' => array(
			'page_title'  => __( 'Add New', 'wpmovielibrary' ),
			'menu_title'  => __( 'Add New', 'wpmovielibrary' ),
			'capability'  => 'publish_posts',
			'menu_slug'   => 'post-new.php?post_type=movie',
			'function'    => null,
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'collections' => array(
			'page_title'  => __( 'Collections', 'wpmovielibrary' ),
			'menu_title'  => __( 'Collections', 'wpmovielibrary' ),
			'capability'  => 'manage_categories',
			'menu_slug'   => 'edit-tags.php?taxonomy=collection&post_type=movie',
			'function'    => null,
			'condition'   => create_function('', 'return wpmoly_o( "enable-collection" );'),
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'genres' => array(
			'page_title'  => __( 'Genres', 'wpmovielibrary' ),
			'menu_title'  => __( 'Genres', 'wpmovielibrary' ),
			'capability'  => 'manage_categories',
			'menu_slug'   => 'edit-tags.php?taxonomy=genre&post_type=movie',
			'function'    => null,
			'condition'   => create_function('', 'return wpmoly_o( "enable-genre" );'),
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'actors' => array(
			'page_title'  => __( 'Actors', 'wpmovielibrary' ),
			'menu_title'  => __( 'Actors', 'wpmovielibrary' ),
			'capability'  => 'manage_categories',
			'menu_slug'   => 'edit-tags.php?taxonomy=actor&post_type=movie',
			'function'    => null,
			'condition'   => create_function('', 'return wpmoly_o( "enable-actor" );'),
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'importer' => array(
			'page_title'  => __( 'Import Movies', 'wpmovielibrary' ),
			'menu_title'  => __( 'Import Movies', 'wpmovielibrary' ),
			'capability'  => 'publish_posts',
			'menu_slug'   => 'wpmovielibrary-import',
			'function'    => 'WPMOLY_Import::import_page',
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(
				'load-{screen_hook}' => 'WPMOLY_Import::import_movie_list_add_options'
			),
			'scripts'     => array(
				
			),
			'styles'      => array(
				'importer' => array(
					'file'    => sprintf( '%s/assets/css/admin/wpmoly-importer.css', WPMOLY_URL ),
					'require' => array()
				)
			)
		),

		'update_movies' => array(
			'page_title'  => __( 'Update movies to version 1.3', 'wpmovielibrary' ),
			'menu_title'  => __( 'Update movies', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'wpmovielibrary-update-movies',
			'function'    => 'WPMOLY_Legacy::update_movies_page',
			'condition'   => null,
			'hide'        => true,
			'actions'     => array(),
			'scripts'     => array(
				'jquery-ajax-queue' => array(
					'file'    => sprintf( '%s/assets/js/vendor/jquery.ajaxQueue.js', WPMOLY_URL ),
					'require' => array( 'jquery' ),
					'footer'  => true
				),
				'updates' => array(
					'file'    => sprintf( '%s/assets/js/admin/wpmoly-updates.js', WPMOLY_URL ),
					'require' => array( WPMOLY_SLUG . '-admin', 'jquery' ),
					'footer'  => true
				)
			),
			'styles'      => array(
				'roboto-font' => array(
					'file'    => '//fonts.googleapis.com/css?family=Roboto:100',
					'require' => array()
				),
				'legacy' => array(
					'file'    => sprintf( '%s/assets/css/admin/wpmoly-legacy.css', WPMOLY_URL ),
					'require' => array()
				)
			)
		),

		'add_custom_pages' => array(
			'page_title'  => __( 'Add custom pages', 'wpmovielibrary' ),
			'menu_title'  => __( 'Add custom pages', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'wpmovielibrary-add-custom-pages',
			'function'    => 'WPMOLY_Archives::create_pages',
			'condition'   => null,
			'hide'        => true,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		)

	)

);

		