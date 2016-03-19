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

$wpmoly_dashboard_widgets = array(
	'statistics' => array(
		'class'    => 'Stats',
		'title'    => __( 'Statistics', 'wpmovielibrary' ),
		'name'     => __( 'Your library', 'wpmovielibrary' ),
		'location' => 'side'
	),
	'quickaction' => array(
		'class'    => 'Quickaction',
		'title'    => __( 'Quick Actions', 'wpmovielibrary' ),
		'name'     => __( 'Quick Actions', 'wpmovielibrary' ),
		'location' => 'side'
	),
	'helper' => array(
		'class'    => 'Helper',
		'title'    => __( 'Help', 'wpmovielibrary' ),
		'name'     => __( 'Help', 'wpmovielibrary' ),
		'location' => 'side'
	),
	'vendor' => array(
		'class'    => 'Vendor',
		'title'    => __( 'Rate me!', 'wpmovielibrary' ),
		'name'     => __( 'Rate me!', 'wpmovielibrary' ),
		'location' => 'side'
	),
	'latest_movies' => array(
		'class' => 'Latest_Movies',
		'title' => __( 'Latest Movies', 'wpmovielibrary' ),
		'name'  => __( 'Movies you recently added', 'wpmovielibrary' ),
		'location' => 'normal'
	),
	'most_rated_movies' => array(
		'class' => 'Most_Rated_Movies',
		'title' => __( 'Most Rated Movies', 'wpmovielibrary' ),
		'name'  => __( 'Your most rated movies', 'wpmovielibrary' ),
		'location' => 'normal'
	),
);
