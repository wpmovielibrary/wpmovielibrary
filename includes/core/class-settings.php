<?php
/**
 * Define the settings class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\core;

use wpmoly\utils;

/**
 *
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Settings {

	/**
	 * The single instance of the plugin.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Settings
	 */
	private static $_instance = null;

	/**
	 * Settings fields.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $setting_fields = array();

	/**
	 * Settings.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Builtin restricted list for API support
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $supported_languages = array();

	/**
	 * Builtin restricted list for API support
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $supported_countries = array();

	/**
	 * Builtin, non-exhaustive iso_639_1 matching array for translation
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $languages = array();

	/**
	 * Builtin iso_3166_1 matching array for translation
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $countries = array();

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Settings
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new static;
			self::$_instance->init();
		}

		return self::$_instance;
	}

	/**
	 * Initialize core.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function init() {

		$this->set_defaults( array(
			'countries'           => L10n::$standard_countries,
			'supported_countries' => L10n::$supported_countries,
			'languages'           => L10n::$standard_languages,
			'supported_languages' => L10n::$supported_languages,
		) );

		$setting_fields = array(
			'general' => array(
				'type'        => 'page',
				'title'       => __( 'General', 'wpmovielibrary' ),
				'description' => __( 'General plugin settings.', 'wpmovielibrary' ),
			),
			/*'license' => array(
				'type'        => 'group',
				'title'       => __( 'General', 'wpmovielibrary' ),
				'description' => __( 'General plugin settings.', 'wpmovielibrary' ),
				'page'        => 'general',
			),
			'license_key' => array(
				'type'              => 'string',
				'title'             => __( 'License Key', 'wpmovielibrary' ),
				'label'             => __( 'A valid license key for the plugin.', 'wpmovielibrary' ),
				'description'       => __( 'Your License Key is a unique identifier delivered when you sign up for the plugin, helping us keep track of your customer record.', 'wpmovielibrary' ),
				'default'           => '',
				'group'             => 'license',
				'sanitize_callback' => '\wpmoly\rest\sanitize_license_key',
				'validate_callback' => '\wpmoly\rest\validate_license_key',
			),*/

			'integration' => array(
				'type'        => 'group',
				'title'       => __( 'Integration', 'wpmovielibrary' ),
				'description' => __( 'Integration settings.', 'wpmovielibrary' ),
				'page'        => 'general',
			),

			'add_to_frontpage' => array(
				'type'              => 'boolean',
				'title'             => __( 'Home Page', 'wpmovielibrary' ),
				'label'             => __( 'Show contents on the front page.', 'wpmovielibrary' ),
				'description'       => __( 'The plugin will try to make contents appear in your front page along with regular posts. This can be broken in premium themes and free themes that use their own frontpage process and templates.', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'integration',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			'add_movies_to_frontpage' => array(
				'type'              => 'boolean',
				'title'             => __( 'Movies', 'wpmovielibrary' ),
				'label'             => __( 'Show movies on the front page.', 'wpmovielibrary' ),
				'default'           => true,
				'parent'            => 'add_to_frontpage',
				'group'             => 'integration',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			/*'add_persons_to_frontpage' => array(
				'type'              => 'boolean',
				'title'             => __( 'Persons', 'wpmovielibrary' ),
				'label'             => __( 'Show persons on the front page.', 'wpmovielibrary' ),
				'default'           => false,
				'parent'            => 'add_to_frontpage',
				'group'             => 'integration',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),*/

			'add_to_search' => array(
				'type'              => 'boolean',
				'title'             => __( 'Search Results', 'wpmovielibrary' ),
				'label'             => __( 'Include contents to search results.', 'wpmovielibrary' ),
				'description'       => __( 'The plugin will try to add contents to the standard WordPress search results in addition to regular posts. This can be broken by themes or plugins that extend or replace the default search.', 'wpmovielibrary' ),
				'default'           => false,
				'group'             => 'integration',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			'add_movies_to_search' => array(
				'type'              => 'boolean',
				'title'             => __( 'Movies', 'wpmovielibrary' ),
				'label'             => __( 'Include movies to search results.', 'wpmovielibrary' ),
				'default'           => false,
				'parent'            => 'add_to_search',
				'group'             => 'integration',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			/*'add_persons_to_search' => array(
				'type'              => 'boolean',
				'title'             => __( 'Persons', 'wpmovielibrary' ),
				'label'             => __( 'Include persons to search results.', 'wpmovielibrary' ),
				'default'           => false,
				'parent'            => 'add_to_search',
				'group'             => 'integration',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),*/

			'replace_excerpt' => array(
				'type'              => 'boolean',
				'title'             => __( 'Overview as excerpt', 'wpmovielibrary' ),
				'label'             => __( 'Replace contents excerpts with overviews.', 'wpmovielibrary' ),
				'description'       => __( 'Use the contents overview as excerpt wherever possible. This can be broken by themes not using the standard WordPress excerpts and plugins extending the excerpts for their own usage.', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'integration',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			'replace_movies_excerpt' => array(
				'type'              => 'boolean',
				'title'             => __( 'Movies', 'wpmovielibrary' ),
				'label'             => __( 'Replace movies excerpts with overviews.', 'wpmovielibrary' ),
				'default'           => true,
				'parent'            => 'replace_excerpt',
				'group'             => 'integration',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			/*'replace_persons_excerpt' => array(
				'type'              => 'boolean',
				'title'             => __( 'Persons', 'wpmovielibrary' ),
				'label'             => __( 'Replace persons excerpts with overviews.', 'wpmovielibrary' ),
				'default'           => true,
				'parent'            => 'replace_excerpt',
				'group'             => 'integration',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),*/

			'formatting' => array(
				'type'        => 'group',
				'title'       => __( 'Formatting', 'wpmovielibrary' ),
				'description' => __( 'Formatting settings.', 'wpmovielibrary' ),
				'page'        => 'general',
			),
			'date_format' => array(
				'type'              => 'string',
				'title'             => __( 'Date Format', 'wpmovielibrary' ),
				'label'             => __( 'Date format used on airing dates.', 'wpmovielibrary' ),
				'description'       => __( 'Apply a custom date format to airing dates. Leave empty to use the default WordPress format. Check the <a href="http://codex.wordpress.org/Formatting_Date_and_Time">documentation on date and time formatting</a>. Default is <code>j F Y</code>.', 'wpmovielibrary' ),
				'default'           => 'j F Y',
				'group'             => 'formatting',
				'sanitize_callback' => '\wpmoly\rest\sanitize_date_format',
				'validate_callback' => '\wpmoly\rest\expect_string_value',
				'show_in_rest'      => true,
			),
			'time_format' => array(
				'type'              => 'string',
				'title'             => __( 'Time Format', 'wpmovielibrary' ),
				'label'             => __( 'Time format used on content runtime.', 'wpmovielibrary' ),
				'description'       => __( 'Apply a custom time format to movie runtimes. Leave empty to use the default WordPress format. Check the <a href="http://codex.wordpress.org/Formatting_Date_and_Time">documentation on date and time formatting</a>. Default is <code>G \h i \m\i\n</code>.', 'wpmovielibrary' ),
				'default'           => 'G \h i \m\i\n',
				'group'             => 'formatting',
				'sanitize_callback' => '\wpmoly\rest\sanitize_time_format',
				'validate_callback' => '\wpmoly\rest\expect_string_value',
				'show_in_rest'      => true,
			),

			'content' => array(
				'type'        => 'page',
				'title'       => __( 'Content', 'wpmovielibrary' ),
				'description' => __( 'Content settings.', 'wpmovielibrary' ),
			),

			/*'persons' => array(
				'type'        => 'group',
				'title'       => __( 'Persons', 'wpmovielibrary' ),
				'description' => __( 'Persons settings.', 'wpmovielibrary' ),
				'page'        => 'content',
			),
			'auto_import_person_movies' => array(
				'type'              => 'boolean',
				'title'             => __( 'Import movies', 'wpmovielibrary' ),
				'label'             => __( 'Automatically import movies with new persons.', 'wpmovielibrary' ),
				'description'       => __( 'When adding a new person, the plugin will automatically create and import the corresponding movies that are not already part of the library. <strong>This can result in <em>super</em> large amounts of data, be <em>double-extra</em> careful when activating this option!</strong>. Default is <code>false</code>.', 'wpmovielibrary' ),
				'default'           => false,
				'group'             => 'persons',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),*/

			'actors' => array(
				'type'        => 'group',
				'title'       => __( 'Actors', 'wpmovielibrary' ),
				'description' => __( 'Actors settings.', 'wpmovielibrary' ),
				'page'        => 'content',
			),
			'auto_import_actors' => array(
				'type'              => 'boolean',
				'title'             => __( 'Import actors', 'wpmovielibrary' ),
				'label'             => __( 'Automatically import actors with new movies.', 'wpmovielibrary' ),
				'description'       => __( 'When adding a new movies, the plugin will automatically create the available actors if it does not already exist and relate it to the movies. Default is <code>true</code>.', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'actors',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			/*'auto_import_persons' => array(
				'type'              => 'boolean',
				'title'             => __( 'Import persons', 'wpmovielibrary' ),
				'label'             => __( 'Automatically import persons with new actors.', 'wpmovielibrary' ),
				'description'       => __( 'When adding a new actor, the plugin will automatically create and import the corresponding person if it does not already exist. Default is <code>false</code>.', 'wpmovielibrary' ),
				'default'           => false,
				'group'             => 'actors',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),*/

			'collections' => array(
				'type'        => 'group',
				'title'       => __( 'Collections', 'wpmovielibrary' ),
				'description' => __( 'Collections settings.', 'wpmovielibrary' ),
				'page'        => 'content',
			),
			'auto_import_collections' => array(
				'type'              => 'boolean',
				'title'             => __( 'Use directors as collections', 'wpmovielibrary' ),
				'label'             => __( 'Automatically import directors as collections with new movies.', 'wpmovielibrary' ),
				'description'       => __( 'When adding a new movie, the plugin will automatically create collections for each director if it does not already exist and relate it to the movies. Default is <code>true</code>.', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'collections',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),

			'genres' => array(
				'type'        => 'group',
				'title'       => __( 'Genres', 'wpmovielibrary' ),
				'description' => __( 'Genres settings.', 'wpmovielibrary' ),
				'page'        => 'content',
			),
			'auto_import_genres' => array(
				'type'              => 'boolean',
				'title'             => __( 'Import genres', 'wpmovielibrary' ),
				'label'             => __( 'Automatically import genres with new movies.', 'wpmovielibrary' ),
				'description'       => __( 'When adding a new movie, the plugin will automatically create the available genres if it does not already exist and relate it to the movies. Default is <code>true</code>.', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'genres',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),

			'categories' => array(
				'type'        => 'group',
				'title'       => __( 'Categories', 'wpmovielibrary' ),
				'description' => __( 'Categories settings.', 'wpmovielibrary' ),
				'page'        => 'content',
			),
			'enable_categories' => array(
				'type'              => 'boolean',
				'title'             => __( 'Enable Categories', 'wpmovielibrary' ),
				'label'             => __( 'Make WordPress standard categories available for movies.', 'wpmovielibrary' ),
				'description'       => __( '.', 'wpmovielibrary' ),
				'default'           => false,
				'group'             => 'categories',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),

			'tags' => array(
				'type'        => 'group',
				'title'       => __( 'Post Tags', 'wpmovielibrary' ),
				'description' => __( 'Post Tags settings.', 'wpmovielibrary' ),
				'page'        => 'content',
			),
			'enable_tags' => array(
				'type'              => 'boolean',
				'title'             => __( 'Enable Post Tags', 'wpmovielibrary' ),
				'label'             => __( 'Make WordPress standard post tags available for movies.', 'wpmovielibrary' ),
				'description'       => __( '.', 'wpmovielibrary' ),
				'default'           => false,
				'group'             => 'tags',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),

			'images' => array(
				'type'        => 'page',
				'title'       => __( 'Images', 'wpmovielibrary' ),
				'description' => __( 'Images settings.', 'wpmovielibrary' ),
			),

			'posters' => array(
				'type'        => 'group',
				'title'       => __( 'Posters', 'wpmovielibrary' ),
				'description' => __( 'Posters settings.', 'wpmovielibrary' ),
				'page'        => 'images',
			),
			'auto_import_movie_posters' => array(
				'type'              => 'boolean',
				'title'             => __( 'Movie posters', 'wpmovielibrary' ),
				'label'             => __( 'Automatically import posters with new movies.', 'wpmovielibrary' ),
				'description'       => __( 'When adding a new movies, the plugin will automatically download the available poster and set it as featured image. Default is <code>true</code>.', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'posters',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			'movie_poster_size' => array(
				'type'              => 'string',
				'title'             => __( 'Movie posters size', 'wpmovielibrary' ),
				'label'             => __( 'Default movie poster size.', 'wpmovielibrary' ),
				'default'           => 'original',
				'options'           => array(
					'small'    => __( 'Small', 'wpmovielibrary' ),
					'medium'   => __( 'Medium', 'wpmovielibrary' ),
					'large'    => __( 'Large', 'wpmovielibrary' ),
					'full'     => __( 'Full ', 'wpmovielibrary' ),
					'original' => __( 'Original', 'wpmovielibrary' ),
				),
				'parent'            => 'auto_import_movie_posters',
				'group'             => 'posters',
				'sanitize_callback' => '\wpmoly\rest\sanitize_movie_poster_size',
				'validate_callback' => '\wpmoly\rest\validate_movie_poster_size',
				'show_in_rest'      => true,
			),
			'movie_poster_title' => array(
				'type'              => 'string',
				'title'             => __( 'Movie posters title', 'wpmovielibrary' ),
				'label'             => __( 'Default movie poster title.', 'wpmovielibrary' ),
				'description'       => __( 'Title set for every imported movie poster.', 'wpmovielibrary' ),
				'default'           => sprintf( '%s "{title}"', __( 'Image from', 'wpmovielibrary' ) ),
				'parent'            => 'auto_import_movie_posters',
				'group'             => 'posters',
				'sanitize_callback' => '\wpmoly\rest\sanitize_movie_poster_title',
				'validate_callback' => '\wpmoly\rest\expect_string_value',
				'show_in_rest'      => true,
			),
			'movie_poster_description' => array(
				'type'              => 'string',
				'title'             => __( 'Movie posters description', 'wpmovielibrary' ),
				'label'             => __( 'Default movie poster description.', 'wpmovielibrary' ),
				'description'       => __( 'Description set for every imported movie poster.', 'wpmovielibrary' ),
				'default'           => sprintf( '© {year} {production} − %s', __( 'All right reserved.', 'wpmovielibrary' ) ),
				'parent'            => 'auto_import_movie_posters',
				'group'             => 'posters',
				'sanitize_callback' => '\wpmoly\rest\sanitize_movie_poster_description',
				'validate_callback' => '\wpmoly\rest\expect_string_value',
				'show_in_rest'      => true,
			),

			'backdrops' => array(
				'type'        => 'group',
				'title'       => __( 'Backdrops', 'wpmovielibrary' ),
				'description' => __( 'Backdrops settings.', 'wpmovielibrary' ),
				'page'        => 'images',
			),
			'auto_import_movie_backdrops' => array(
				'type'              => 'boolean',
				'title'             => __( 'Movie backdrops', 'wpmovielibrary' ),
				'label'             => __( 'Automatically import backdrops with new movie.', 'wpmovielibrary' ),
				'description'       => __( 'When adding a new movie, the plugin will automatically download the available backdrops. Default is <code>true</code>.', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'backdrops',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			'movie_backdrop_size' => array(
				'type'              => 'string',
				'title'             => __( 'Movie backdrops size', 'wpmovielibrary' ),
				'label'             => __( 'Default movie backdrop size.', 'wpmovielibrary' ),
				'default'           => 'original',
				'options'           => array(
					'medium'   => __( 'Medium', 'wpmovielibrary' ),
					'large'    => __( 'Large', 'wpmovielibrary' ),
					'full'     => __( 'Full ', 'wpmovielibrary' ),
					'original' => __( 'Original', 'wpmovielibrary' ),
				),
				'parent'            => 'auto_import_movie_backdrops',
				'group'             => 'backdrops',
				'sanitize_callback' => '\wpmoly\rest\sanitize_movie_backdrop_size',
				'validate_callback' => '\wpmoly\rest\validate_movie_backdrop_size',
				'show_in_rest'      => true,
			),
			'movie_backdrop_title' => array(
				'type'              => 'string',
				'title'             => __( 'Movie backdrops title', 'wpmovielibrary' ),
				'label'             => __( 'Default movie backdrop title.', 'wpmovielibrary' ),
				'description'       => __( 'Title set for every imported movie backdrop.', 'wpmovielibrary' ),
				'default'           => sprintf( '%s "{title}"', __( 'Image from', 'wpmovielibrary' ) ),
				'parent'            => 'auto_import_movie_backdrops',
				'group'             => 'backdrops',
				'sanitize_callback' => '\wpmoly\rest\sanitize_movie_backdrop_title',
				'validate_callback' => '\wpmoly\rest\expect_string_value',
				'show_in_rest'      => true,
			),
			'movie_backdrop_description' => array(
				'type'              => 'string',
				'title'             => __( 'Movie backdrops description', 'wpmovielibrary' ),
				'label'             => __( 'Default movie backdrop description.', 'wpmovielibrary' ),
				'description'       => __( 'Description set for every imported movie backdrop.', 'wpmovielibrary' ),
				'default'           => sprintf( '© {year} {production} − %s', __( 'All right reserved.', 'wpmovielibrary' ) ),
				'parent'            => 'auto_import_movie_backdrops',
				'group'             => 'backdrops',
				'sanitize_callback' => '\wpmoly\rest\sanitize_movie_backdrop_description',
				'validate_callback' => '\wpmoly\rest\expect_string_value',
				'show_in_rest'      => true,
			),

			/*'pictures' => array(
				'type'        => 'group',
				'title'       => __( 'Pictures', 'wpmovielibrary' ),
				'description' => __( 'Pictures settings.', 'wpmovielibrary' ),
				'page'        => 'images',
			),
			'auto_import_pictures' => array(
				'type'              => 'boolean',
				'title'             => __( 'Import pictures', 'wpmovielibrary' ),
				'label'             => __( 'Automatically import pictures with new persons.', 'wpmovielibrary' ),
				'description'       => __( 'When adding a new person, the plugin will automatically download the available picture and set it as featured image. Default is <code>true</code>.', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'pictures',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			'picture_size' => array(
				'type'              => 'string',
				'title'             => __( 'Pictures size', 'wpmovielibrary' ),
				'label'             => __( 'Default pictures size.', 'wpmovielibrary' ),
				'default'           => 'original',
				'options'           => array(
					'small'    => __( 'Small', 'wpmovielibrary' ),
					'large'    => __( 'Large', 'wpmovielibrary' ),
					'original' => __( 'Original', 'wpmovielibrary' ),
				),
				'parent'            => 'auto_import_pictures',
				'group'             => 'pictures',
				'sanitize_callback' => '\wpmoly\rest\sanitize_picture_size',
				'validate_callback' => '\wpmoly\rest\validate_picture_size',
				'show_in_rest'      => true,
			),
			'picture_title' => array(
				'type'              => 'string',
				'title'             => __( 'Pictures title', 'wpmovielibrary' ),
				'label'             => __( 'Default pictures title.', 'wpmovielibrary' ),
				'description'       => __( 'Title set for every imported picture.', 'wpmovielibrary' ),
				'default'           => sprintf( '%s "{title}"', __( 'Image of', 'wpmovielibrary' ) ),
				'parent'            => 'auto_import_pictures',
				'group'             => 'pictures',
				'sanitize_callback' => '\wpmoly\rest\sanitize_picture_title',
				'validate_callback' => '\wpmoly\rest\expect_string_value',
				'show_in_rest'      => true,
			),
			'picture_description' => array(
				'type'              => 'string',
				'title'             => __( 'Pictures description', 'wpmovielibrary' ),
				'label'             => __( 'Default pictures description.', 'wpmovielibrary' ),
				'description'       => __( 'Description set for every imported picture.', 'wpmovielibrary' ),
				'default'           => sprintf( '© {year} {production} − %s', __( 'All right reserved.', 'wpmovielibrary' ) ),
				'parent'            => 'auto_import_pictures',
				'group'             => 'pictures',
				'sanitize_callback' => '\wpmoly\rest\sanitize_picture_description',
				'validate_callback' => '\wpmoly\rest\expect_string_value',
				'show_in_rest'      => true,
			),*/

			'appearance' => array(
				'type'        => 'page',
				'title'       => __( 'Appearance', 'wpmovielibrary' ),
				'description' => __( 'Appearance settings.', 'wpmovielibrary' ),
			),

			'headbox' => array(
				'type'        => 'group',
				'title'       => __( 'Headbox', 'wpmovielibrary' ),
				'description' => __( 'Headbox settings.', 'wpmovielibrary' ),
				'page'        => 'appearance',
			),
			'enable_headbox' => array(
				'type'              => 'boolean',
				'title'             => __( 'Enable Headboxes', 'wpmovielibrary' ),
				'label'             => __( 'Enable content headboxes.', 'wpmovielibrary' ),
				'description'       => __( 'Headboxes are information boxes highlighting contents data: title, year, runtime, poster, overview...', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'headbox',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			'enable_movie_headbox' => array(
				'type'              => 'boolean',
				'title'             => __( 'Movies', 'wpmovielibrary' ),
				'label'             => __( 'Enable movie headboxes.', 'wpmovielibrary' ),
				'default'           => true,
				'parent'            => 'enable_headbox',
				'group'             => 'headbox',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			/*'enable_persons_headbox' => array(
				'type'              => 'boolean',
				'title'             => __( 'Persons', 'wpmovielibrary' ),
				'label'             => __( 'Enable persons headboxes.', 'wpmovielibrary' ),
				'default'           => true,
				'parent'            => 'enable_headbox',
				'group'             => 'headbox',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),*/
			'enable_actors_headbox' => array(
				'type'              => 'boolean',
				'title'             => __( 'Actors', 'wpmovielibrary' ),
				'label'             => __( 'Enable actors headboxes.', 'wpmovielibrary' ),
				'default'           => true,
				'parent'            => 'enable_headbox',
				'group'             => 'headbox',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			'enable_genres_headbox' => array(
				'type'              => 'boolean',
				'title'             => __( 'Genres', 'wpmovielibrary' ),
				'label'             => __( 'Enable genres headboxes.', 'wpmovielibrary' ),
				'default'           => true,
				'parent'            => 'enable_headbox',
				'group'             => 'headbox',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),

			'api' => array(
				'type'        => 'page',
				'title'       => __( 'API', 'wpmovielibrary' ),
				'description' => __( 'API settings.', 'wpmovielibrary' ),
			),
			'api_settings' => array(
				'type'        => 'group',
				'title'       => __( 'API settings', 'wpmovielibrary' ),
				'description' => __( 'API settings.', 'wpmovielibrary' ),
				'page'        => 'api',
			),
			'personal_api_key' => array(
				'type'              => 'boolean',
				'title'             => __( 'Personal API Key', 'wpmovielibrary' ),
				'label'             => __( 'Use your own TMDb API Key.', 'wpmovielibrary' ),
				'description'       => __( 'A valid TMDb API key is required to fetch movies metadata and images; Leave this disabled if you do not have a personal API key and want the plugin to use its own mirror API. Using your own API key is highly recommended for safety, privacy and reliability.', 'wpmovielibrary' ),
				'default'           => false,
				'group'             => 'api_settings',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),
			'api_key' => array(
				'type'              => 'string',
				'title'             => __( 'API Key', 'wpmovielibrary' ),
				'label'             => __( 'Personal Key for the TMDb.org API.', 'wpmovielibrary' ),
				'description'       => __( 'Using your own API key is a more reliable and privacy-safe choice as it will avoid your queries to run by our server before reaching the API. You will also be able to access statistics on your API usage in your TMDb user account.', 'wpmovielibrary' ),
				'default'           => '',
				'parent'            => 'personal_api_key',
				'group'             => 'api_settings',
				'sanitize_callback' => '\wpmoly\rest\sanitize_api_key',
				'validate_callback' => '\wpmoly\rest\validate_api_key',
				'show_in_rest'      => true,
			),
			'api_presets' => array(
				'type'        => 'group',
				'title'       => __( 'API Presets', 'wpmovielibrary' ),
				'description' => __( 'API Presets.', 'wpmovielibrary' ),
				'page'        => 'api',
			),
			'api_language' => array(
				'type'              => 'string',
				'title'             => __( 'API Default Language', 'wpmovielibrary' ),
				'label'             => __( 'The default language for the TMDb API.', 'wpmovielibrary' ),
				'description'       => __( 'Default language to use when fetching informations from TMDb. You can always change this manually when add a new movie. Default is English.', 'wpmovielibrary' ),
				'default'           => 'en',
				'options'           => $this->supported_languages,
				'group'             => 'api_presets',
				'sanitize_callback' => '\wpmoly\rest\sanitize_api_language',
				'validate_callback' => '\wpmoly\rest\validate_api_language',
				'show_in_rest'      => true,
			),
			'api_country' => array(
				'type'              => 'string',
				'title'             => __( 'API Default Country', 'wpmovielibrary' ),
				'label'             => __( 'The default country for the TMDb API.', 'wpmovielibrary' ),
				'description'       => __( 'Default country to use when fetching release informations from TMDb. This is mostly used to get certifications corresponding to your country. Default is United States.', 'wpmovielibrary' ),
				'default'           => 'US',
				'options'           => $this->supported_countries,
				'group'             => 'api_presets',
				'sanitize_callback' => '\wpmoly\rest\sanitize_api_country',
				'validate_callback' => '\wpmoly\rest\validate_api_country',
				'show_in_rest'      => true,
			),
			'api_alternative_country' => array(
				'type'              => 'string',
				'title'             => __( 'API Alternative Country', 'wpmovielibrary' ),
				'label'             => __( 'An alternative country for the TMDb API.', 'wpmovielibrary' ),
				'description'       => __( 'You can select an alternative country to use when fetching release informations from TMDb. If primary country leaves empty results, the alternative country will be used in an attempt to fill the blank. A good idea is to select English if you\'re not using that language as primary API Language.', 'wpmovielibrary' ),
				'default'           => 'US',
				'options'           => array( '' => '' ) + $this->supported_countries,
				'group'             => 'api_presets',
				'sanitize_callback' => '\wpmoly\rest\sanitize_api_alternative_country',
				'validate_callback' => '\wpmoly\rest\validate_api_alternative_country',
				'show_in_rest'      => true,
			),
			'api_include_adult' => array(
				'type'              => 'boolean',
				'title'             => __( 'Adult Movies', 'wpmovielibrary' ),
				'label'             => __( 'Include adult movies to search results.', 'wpmovielibrary' ),
				'default'           => true,
				'group'             => 'api_presets',
				'sanitize_callback' => '\wpmoly\rest\sanitize_boolean_value',
				'validate_callback' => '\wpmoly\rest\expect_boolean_value',
				'show_in_rest'      => true,
			),

			/*'advanced' => array(
				'type'        => 'page',
				'title'       => __( 'Advanced', 'wpmovielibrary' ),
				'description' => __( 'Advanced settings.', 'wpmovielibrary' ),
			),*/
		);

		foreach ( $setting_fields as $setting => $field ) {

			$field = wp_parse_args( $field, array(
				'type'        => '',
				'title'       => '',
				'description' => '',
			) );

			if ( 'group' === $field['type'] ) {
				$args = wp_parse_args( $field, array(
					'page' => '',
				) );
			} else {
				$args = wp_parse_args( $field, array(
					'label'             => '',
					'default'           => null,
					'sanitize_callback' => '\wpmoly\rest\sanitize_settings',
					'validate_callback' => '\wpmoly\rest\validate_settings',
					'parent'            => null,
					'group'             => null,
				) );
			}

			$this->setting_fields[ $setting ] = $args;
		}

		$additional_settings = array(
			'permalinks' => array(
				'type'              => 'object',
				'description'       =>  esc_html__( 'Custom permalink structures for movies, genres, actors...', 'wpmovielibrary' ),
				'default'           => array(),
				'sanitize_callback' => '\wpmoly\rest\sanitize_permalinks',
				'validate_callback' => '\wpmoly\rest\validate_permalinks',
			),
			'archive_pages' => array(
				'type'              => 'object',
				'description'       => esc_html__( 'Custom archive page IDs for movies, genres, actors...', 'wpmovielibrary' ),
				'default'           => array(),
				'sanitize_callback' => '\wpmoly\rest\sanitize_archive_pages',
				'validate_callback' => '\wpmoly\rest\validate_archive_pages',
				'show_in_rest'      => array(
					'type'              => 'object',
					'schema' =>array(
						'properties' => array(
							'actor'      => array(
								'description' => __( 'Actors custom archive page ID.', 'wpmovielibrary' ),
								'type'        => 'integer',
								'context'     => array( 'edit' ),
							),
							'genre'      => array(
								'description' => __( 'Genres custom archive page ID.', 'wpmovielibrary' ),
								'type'        => 'integer',
								'context'     => array( 'edit' ),
							),
							'movie'      => array(
								'description' => __( 'Movies custom archive page ID.', 'wpmovielibrary' ),
								'type'        => 'integer',
								'context'     => array( 'edit' ),
							),
							'person'      => array(
								'description' => __( 'Persons custom archive page ID.', 'wpmovielibrary' ),
								'type'        => 'integer',
								'context'     => array( 'edit' ),
							),
						),
					),
				),
			),
		);

		foreach ( $additional_settings as $setting => $args ) {

			$args = wp_parse_args( $args, array(
				'type'              => '',
				'description'       => '',
				'sanitize_callback' => '\wpmoly\rest\sanitize_settings',
				'validate_callback' => '\wpmoly\rest\validate_settings',
				'default'           => '',
			) );

			$this->additional_settings[ $setting ] = $args;
		}
	}

	/**
	 * Register plugin's settings.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register() {

		/**
		 * Filter default plugin's settings fields.
		 *
		 * @since 3.0.0
		 *
		 * @param array $setting_fields Default settings fields.
		 */
		$setting_fields = apply_filters( 'wpmoly/filter/settings/fields', $this->setting_fields );

		foreach ( $setting_fields as $name => $args ) {

			$args = wp_array_slice_assoc( $args, array(
				'type',
				'description',
				'sanitize_callback',
				'validate_callback',
				'default',
				'show_in_rest',
			) );

			register_setting( 'wpmoly', utils\settings\prefix( $name ), $args );
		}

		/**
		 * Filter default plugin's additional settings.
		 *
		 * @since 3.0.0
		 *
		 * @param array $setting_fields Default settings fields.
		 */
		$additional_settings = apply_filters( 'wpmoly/filter/additional/settings', $this->additional_settings );

		foreach ( $additional_settings as $name => $args ) {

			$args = wp_array_slice_assoc( $args, array(
				'type',
				'description',
				'sanitize_callback',
				'validate_callback',
				'default',
				'show_in_rest',
			) );

			register_setting( 'wpmoly', utils\settings\prefix( $name ), $args );
		}
	}

	/**
	 * Set a number of important, plugin-wide default data. Apply a filter
	 * on each default set of data.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param array $defaults Default data
	 */
	private function set_defaults( $defaults ) {

		$defaults = wp_parse_args( $defaults, array(
			'countries'           => array(),
			'supported_countries' => array(),
			'languages'           => array(),
			'supported_languages' => array(),
		) );

		/**
		 * Filter the default countries list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $countries
		 */
		$this->countries = apply_filters( 'wpmoly/filter/settings/countries/stantard', $defaults['countries'] );

		/**
		 * Filter the default supported countries list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $supported_countries
		 */
		$this->supported_countries = apply_filters( 'wpmoly/filter/settings/countries/supported', $defaults['supported_countries'] );

		/**
		 * Filter the default languages list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $languages
		 */
		$this->languages = apply_filters( 'wpmoly/filter/settings/languages/stantard', $defaults['languages'] );

		/**
		 * Filter the default supported languages list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $supported_languages
		 */
		$this->supported_languages = apply_filters( 'wpmoly/filter/settings/languages/supported', $defaults['supported_languages'] );
	}

	/**
	 * Retrieve all settings fields.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_setting_fields() {

		return $this->setting_fields;
	}

	/**
	 * Retrieve a specific setting field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Option name
	 *
	 * @return mixed
	 */
	public function get_setting_field( $name ) {

		$setting_fields = $this->get_setting_fields();
		if ( ! empty( $setting_fields[ $name ] ) ) {
			return $setting_fields[ $name ];
		}

		return false;
	}

	/**
	 * Retrieve all additional settings.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_additional_settings() {

		return $this->additional_settings;
	}

	/**
	 * Retrieve all additional settings.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function get_additional_setting() {

		$additional_setting = $this->get_additional_settings();
		if ( ! empty( $additional_settings[ $name ] ) ) {
			return $additional_settings[ $name ];
		}

		return false;
	}

	/**
	 * Retrieve a specific option.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Option name
	 * @param mixed $default Option default value to return if needed
	 *
	 * @return mixed
	 */
	public function get( $name, $default = null ) {

		if ( in_array( $name, array( 'countries', 'supported_countries', 'languages', 'supported_languages' ) ) ) {
			return $this->$name;
		}

		$key = sanitize_key( $name );
		if ( isset( $this->settings[ $key ] ) ) {
			return $this->settings[ $key ];
		}

		$name = utils\settings\prefix( $key );
		$value = get_option( $name, $default );

		$option = $this->get_setting_field( $key );
		if ( ! empty( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ) {
			$value = call_user_func_array( $option['sanitize_callback'], array( $value ) );
		}

		return $value;
	}

	/**
	 * Set a new value for a specific option.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Option name
	 * @param mixed $value Option value
	 */
	public function set( $name, $value ) {


	}

	/**
	 * Check if a specific option exists.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Option name
	 *
	 * @return boolean
	 */
	public function __isset( $name ) {

		if ( in_array( $name, array( 'languages', 'supported_languages' ) ) ) {
			return true;
		}


	}

	/**
	 * Checks if current user is allowed to modify settings.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function auth_callback() {

		return current_user_can( 'manage_options' );
	}

}
