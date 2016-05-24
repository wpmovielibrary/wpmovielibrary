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

$wpmoly_admin_bar_menu = array(

	'menu' => array(
		'id'    => 'wpmovielibrary',
		'title' => '<span class="wpmolicon icon-wpmoly"></span>&nbsp;' . __( 'Movie Library', 'wpmovielibrary' ),
		'href'  => admin_url( 'admin.php?page=wpmovielibrary' )
	),

	'submenu' => array(
	
		array(
			'parent'     => 'wpmovielibrary',
			'id'         => 'wpmoly-library',
			'title'      => __( 'Your movie library', 'wpmovielibrary' ),
			'href'       => admin_url( 'admin.php?page=wpmovielibrary' ),
			'capability' => 'read'
		),

		array(
			'parent'     => 'wpmoly-movies',
			'id'         => 'wpmoly-all-movies',
			'title'      => __( 'View all movies', 'wpmovielibrary' ),
			'href'       => admin_url( 'edit.php?post_type=movie' ),
			'capability' => 'edit_posts'
		),

		array(
			'parent'     => 'wpmoly-movies',
			'id'         => 'wpmoly-new-movie',
			'title'      => __( 'Add new movie', 'wpmovielibrary' ),
			'href'       => admin_url( 'post-new.php?post_type=movie' ),
			'capability' => 'publish_posts'
		),

		array(
			'parent'     => 'wpmoly-movies',
			'id'         => 'wpmoly-import-movies',
			'title'      => __( 'Import movies', 'wpmovielibrary' ),
			'href'       => admin_url( 'admin.php?page=wpmovielibrary-import' ),
			'capability' => 'publish_posts'
		),

		array(
			'parent'     => 'wpmoly-utils',
			'id'         => 'wpmoly-settings',
			'title'      => __( 'Library Settings', 'wpmovielibrary' ),
			'href'       => admin_url( 'admin.php?page=wpmovielibrary-settings' ),
			'capability' => 'manage_options'
		),

		array(
			'parent'     => 'wpmoly-utils',
			'id'         => 'wpmoly-about',
			'title'      => __( 'About  WPMovieLibrary', 'wpmovielibrary' ),
			'href'       => admin_url( 'index.php?page=wpmovielibrary-about' ),
			'capability' => 'read'
		),

		array(
			'parent'     => 'wpmoly-utils',
			'id'         => 'wpmoly-movie-update',
			'title'      => __( 'Update movies', 'wpmovielibrary' ),
			'href'       => admin_url( 'admin.php?page=wpmovielibrary-update-movies' ),
			'capability' => 'manage_options',
			'meta'       => array(
				'class' => 'active',
			),
			'condition'  => wpmoly_has_deprecated_meta()
		),

	),

	'group' => array(
		array(
			'parent' => 'wpmovielibrary',
			'id'     => 'wpmoly-movies',
			'meta'   => array(
				'class' => 'ab-sub-secondary',
			),
		),

		array(
			'parent' => 'wpmovielibrary',
			'id'     => 'wpmoly-utils',
			'meta'   => array(
				'class' => 'ab-sub-third',
			),
		),
	)
);

?>