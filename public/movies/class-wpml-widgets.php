<?php
/**
 * WP_Widget Class extension.
 * 
 * WPML provides specific Widgets: Recent Movies, Most Rated Movies,
 * Collections, Genres, Actors…
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

require_once( plugin_dir_path( __FILE__ ) . 'actors-widget.class.php' );
require_once( plugin_dir_path( __FILE__ ) . 'collections-widget.class.php' );
require_once( plugin_dir_path( __FILE__ ) . 'genres-widget.class.php' );
require_once( plugin_dir_path( __FILE__ ) . 'media-widget.class.php' );
require_once( plugin_dir_path( __FILE__ ) . 'most-rated-movies-widget.class.php' );
require_once( plugin_dir_path( __FILE__ ) . 'recent-movies-widget.class.php' );
require_once( plugin_dir_path( __FILE__ ) . 'status-widget.class.php' );

add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Recent_Movies_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Most_Rated_Movies_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Collections_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Genres_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Actors_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Media_Widget");' ) );
add_action( 'widgets_init', create_function( '', 'register_widget("WPML_Status_Widget");' ) );
