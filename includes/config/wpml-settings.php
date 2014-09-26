<?php
/**
 * WPMovieLibrary Config Settings definition
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

$wpmoly_config = array(

	'wpmoly' => array(
		'icon'    => 'el-icon-film',
		'title'   => __( 'General', 'wpmovielibrary' ),
		'heading' => __( 'General options', 'wpmovielibrary' ),
		'fields'  => array(

			// Add movies to the main loop
			'frontpage' => array(
				'id'       => 'wpmoly-frontpage',
				'type'     => 'switch',
				'title'    => __( 'Show Movies in Home Page', 'wpmovielibrary' ),
				'desc'     => __( 'If enable, movies will appear among other Posts in the Home Page.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Replace excerpt by overview
			'excerpt' => array(
				'id'       => 'wpmoly-excerpt',
				'type'     => 'switch',
				'title'    => __( 'Replace excerpt by overview', 'wpmovielibrary' ),
				'desc'     => __( 'Replace movie excerpts by the movie overview if available. <a href="http://codex.wordpress.org/Excerpt">Learn more about Excerpt</a>.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Replace excerpt by overview
			'excerpt-length' => array(
				'id'       => 'wpmoly-excerpt-length',
				'type'     => 'text',
				'title'    => __( 'Excerpt overview length', 'wpmovielibrary' ),
				'desc'     => __( 'Excerpt overview default number of words. This will override WordPress and Themes or Plugins default values for movies only.', 'wpmovielibrary' ),
				'default'  => '75',
				'required' => array( 'wpmoly-excerpt', "=", 1 ),
				'indent'   => true
			),

			// Show movie meta in posts
			'show-meta' => array(
				'id'       => 'wpmoly-show-meta',
				'type'     => 'select',
				'title'    => __( 'Show basic movie metadata', 'wpmovielibrary' ),
				'desc'     => __( 'Add metadata to posts&rsquo; content: director, genres, runtime…', 'wpmovielibrary' ),
				'options'  => array(
					'everywhere' => __( 'Everywhere', 'wpmovielibrary' ),
					'posts_only' => __( 'Only In Post Read', 'wpmovielibrary' ),
					'nowhere'    => __( 'Don&rsquo;t Show', 'wpmovielibrary' ),
				),
				'default'  => 'posts_only'
			),

			// Default movie meta to show
			'sort-meta' => array(
				'id'       => 'wpmoly-sort-meta',
				'type'     => 'sorter',
				'title'    => __( 'Movie metadata', 'wpmovielibrary' ),
				'desc'     => __( 'Which metadata to display in posts: director, genres, runtime, rating…', 'wpmovielibrary' ),
				//'callback' => 'sorted_markup_fields',
				'compiler' => 'true',
				'options'  => array(
					'used' => array(
						'director'     => __( 'Director', 'wpmovielibrary' ),
						'runtime'      => __( 'Runtime', 'wpmovielibrary' ),
						'release_date' => __( 'Release date', 'wpmovielibrary' ),
						'genres'       => __( 'Genres', 'wpmovielibrary' ),
						'overview'     => __( 'Overview', 'wpmovielibrary' )
					),
					'available' => array(
						'title'                => __( 'Title', 'wpmovielibrary' ),
						'original_title'       => __( 'Original Title', 'wpmovielibrary' ),
						'production_companies' => __( 'Production', 'wpmovielibrary' ),
						'production_countries' => __( 'Country', 'wpmovielibrary' ),
						'spoken_languages'     => __( 'Languages', 'wpmovielibrary' ),
						'producer'             => __( 'Producer', 'wpmovielibrary' ),
						'photography'          => __( 'Director of Photography', 'wpmovielibrary' ),
						'composer'             => __( 'Original Music Composer', 'wpmovielibrary' ),
						'author'               => __( 'Author', 'wpmovielibrary' ),
						'writer'               => __( 'Writer', 'wpmovielibrary' ),
						'cast'                 => __( 'Actors', 'wpmovielibrary' )
					)
				),
				'required' => array( 'wpmoly-show-meta', "!=", 'nowhere' ),
				'indent'   => true
			),

			// Show movie details in posts
			'show-details' => array(
				'id'       => 'wpmoly-show-details',
				'type'     => 'select',
				'title'    => __( 'Show movie details', 'wpmovielibrary' ),
				'desc'     => __( 'Add details to posts&rsquo; content: movie status, media…', 'wpmovielibrary' ),
				'options'  => array(
					'everywhere'  => __( 'Everywhere', 'wpmovielibrary' ),
					'posts_only'  => __( 'Only In Post Read', 'wpmovielibrary' ),
					'nowhere'     => __( 'Don&rsquo;t Show', 'wpmovielibrary' )
				),
				'default' => 'posts_only'
			),

			// Show movie details as icons
			'details-icons' => array(
				'id'       => 'wpmoly-details-icons',
				'type'     => 'switch',
				'title'    => __( 'Show details as icons', 'wpmovielibrary' ),
				'desc'     => __( 'If enable, movie details will appear in the form of icons rather than default colored labels.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1,
				'required' => array( 'wpmoly-show-details', "!=", 'nowhere' ),
				'indent'   => true
			),

			// Default movie detail to show
			'sort-details' => array(
				'id'       => 'wpmoly-sort-details',
				'type'     => 'sorter',
				'title'    => __( 'Movie details', 'wpmovielibrary' ),
				'desc'     => __( 'Which detail to display in posts: movie status, media…', 'wpmovielibrary' ),
				'compiler' => 'true',
				'options'  => array(
					'used' => array(
						'movie_media'  => __( 'Media', 'wpmovielibrary' ),
						'movie_status' => __( 'Status', 'wpmovielibrary' ),
						'movie_rating' => __( 'Rating', 'wpmovielibrary' )
					),
					'available'  => array(
						'movie_language' => __( 'Language', 'wpmovielibrary' ),
						'movie_subtitle' => __( 'Subtitles', 'wpmovielibrary' )
					)
				),
				'required' => array( 'wpmoly-show-details', "!=", 'nowhere' ),
				'indent'   => true
			),

			// Release date formatting
			'format-date' => array(
				'id'       => 'wpmoly-format-date',
				'type'     => 'text',
				'title'    => __( 'Release date format', 'wpmovielibrary' ),
				'desc'     => __( 'Apply a custom date format to movies\' release dates. Leave empty to use the default API format. Check the <a href="http://codex.wordpress.org/Formatting_Date_and_Time">documentation on date and time formatting</a>.', 'wpmovielibrary' ),
				'default'  => 'j F Y'
			),

			// Release date formatting
			'format-time' => array(
				'id'       => 'wpmoly-format-time',
				'type'     => 'text',
				'title'    => __( 'Runtime format', 'wpmovielibrary' ),
				'desc'     => __( 'Apply a custom time format to movies\' runtimes. Leave empty to use the default API format.', 'wpmovielibrary' ),
				'default'  => 'G \h i \m\i\n'
			),
		),
	),

	
	// Taxonomies
	'wpmoly-images' => array(
		'icon'    => 'el-icon-picture',
		'title'   => __( 'Images', 'wpmovielibrary' ),
		'heading' => __( 'Images and Posters options', 'wpmovielibrary' ),
		'fields'  => array(

			// Use posters as featured images
			'poster-featured' => array(
				'id'       => 'wpmoly-poster-featured',
				'type'     => 'switch',
				'title'    => __( 'Posters as Thumbnails', 'wpmovielibrary' ),
				'desc'     => __( 'Using posters as movies thumbnails will automatically import new movies&rsquo; poster and set them as post featured image. This setting doesn’t affect movie import by list where posters are automatically saved and set as featured image.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default' => 1
			),

			// Movie posters size
			'poster-size' => array(
				'id'       => 'wpmoly-poster-size',
				'type'     => 'select',
				'title'    => __( 'Posters Default Size', 'wpmovielibrary' ),
				'desc'     => __( 'Movie Poster size. Default is TMDb&rsquo;s original size.', 'wpmovielibrary' ),
				'options'  => array(
					'xx-small' => __( 'Invisible (~100px)', 'wpmovielibrary' ),
					'x-small'  => __( 'Tiny (~150px)', 'wpmovielibrary' ),
					'small'    => __( 'Small (~200px)', 'wpmovielibrary' ),
					'medium'   => __( 'Medium (~350px)', 'wpmovielibrary' ),
					'large'    => __( 'Large (~500px)', 'wpmovielibrary' ),
					'full'     => __( 'Full (~800px) ', 'wpmovielibrary' ),
					'original' => __( 'Original', 'wpmovielibrary' )
				),
				'default' => 'original'
			),

			// Images size
			'images-size' => array(
				'id'       => 'wpmoly-images-size',
				'type'     => 'select',
				'title'    => __( 'Images Default Size', 'wpmovielibrary' ),
				'desc'     => __( 'Movie Image size. Default is TMDb&rsquo;s original size.', 'wpmovielibrary' ),
				'options'  => array(
					'small'    => __( 'Small (~200px)', 'wpmovielibrary' ),
					'medium'   => __( 'Medium (~350px)', 'wpmovielibrary' ),
					'full'     => __( 'Full (~800px) ', 'wpmovielibrary' ),
					'original' => __( 'Original', 'wpmovielibrary' )
				),
				'default' =>'original'
			),

			// Maximum number of image to show
			'images-delete' => array(
				'id'       => 'wpmoly-images-delete',
				'type'     => 'switch',
				'title'    => __( 'Delete images with movies', 'wpmovielibrary' ),
				'desc'     => __( 'Enable this if you want to delete all imported images along with movies. Handy if you have a great number of movies to delete and possibly dozens of images attached.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default' => 0
			),

			// Maximum number of image to show
			'posters-delete' => array(
				'id'       => 'wpmoly-posters-delete',
				'type'     => 'switch',
				'title'    => __( 'Delete posters with movies', 'wpmovielibrary' ),
				'desc'     => __( 'Enable this if you want to delete posters along with movies.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default' => 0
			),
		),
	),

	// Taxonomies
	'wpmoly-taxonomies' => array(
		'icon'    => 'el-icon-tags',
		'title'   => __( 'Taxonomies', 'wpmovielibrary' ),
		'heading' => __( 'Built-in Taxonomies configuration', 'wpmovielibrary' ),
		'fields'  => array(

			// Enable Collections Taxonomy
			'enable-collection' => array(
				'id'       => 'wpmoly-enable-collection',
				'type'     => 'switch',
				'title'    => __( 'Enable Collections', 'wpmovielibrary' ),
				'description' => __( 'Enable Collections Custom Taxonomy. Collections work for movies as Categories work for Posts: a hierarchical taxonomy to group your movies coherently. The default behavior is to use Collections to group movies by director, but you can use them differently at your will.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Enable Collections Autocomplete
			'collection-autocomplete' => array(
				'id'       => 'wpmoly-collection-autocomplete',
				'type'     => 'switch',
				'title'    => __( 'Add Collections automatically', 'wpmovielibrary' ),
				'desc'     => __( 'Automatically add custom taxonomies when adding/importing movies. If enabled, each added/imported movie will be automatically added to the collection corresponding to its director(s).', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1,
				'required' => array( 'wpmoly-enable-collection', "=", 1 ),
				'indent'   => true
			),

			// Enable Genres Taxonomy
			'enable-genre' => array(
				'id'       => 'wpmoly-enable-genre',
				'type'     => 'switch',
				'title'    => __( 'Enable Genres', 'wpmovielibrary' ),
				'desc'     => __( 'Enable Genres Custom Taxonomy. Genres work for movies as Tags work for Posts: a non-hierarchical taxonomy to improve movies management.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Enable Genres Autocomplete
			'genre-autocomplete' => array(
				'id'       => 'wpmoly-genre-autocomplete',
				'type'     => 'switch',
				'title'    => __( 'Add Genres automatically', 'wpmovielibrary' ),
				'desc'     => __( 'Automatically add Genres when adding/importing movies.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1,
				'required' => array( 'wpmoly-enable-genre', "=", 1 ),
				'indent'   => true
			),

			// Enable Actors Taxonomy
			'enable-actor' => array(
				'id'       => 'wpmoly-enable-actor',
				'type'     => 'switch',
				'title'    => __( 'Enable Actors', 'wpmovielibrary' ),
				'desc'     => __( 'Enable Actors Custom Taxonomy. Actors work for movies as Tags work for Posts: a non-hierarchical taxonomy to improve movies management. WPMovieLibrary stores Actors in a custom order, the most important actors appearing in the top of the list, then the supporting roles, and so on.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Enable Actors Autocomplete
			'actor-autocomplete' => array(
				'id'       => 'wpmoly-actor-autocomplete',
				'type'     => 'switch',
				'title'    => __( 'Add Actors automatically', 'wpmovielibrary' ),
				'desc'     => __( 'Automatically add Actors when adding/importing movies.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1,
				'required' => array( 'wpmoly-enable-actor', "=", 1 ),
				'indent'   => true
			),

			// Enable Actors Autocomplete
			'actor-limit' => array(
				'id'       => 'wpmoly-actor-limit',
				'type'     => 'text',
				'title'    => __( 'Actors limit', 'wpmovielibrary' ),
				'desc'     => __( 'Limit the number of actors per movie. This is useful if you\'re dealing with big libraries and don\'t want to have massive lists of actors created. Limiting the Actors will result in keeping only the most famous/important actors as taxonomies, while the complete list of actors will remained stored as a regular metadata. Set to 0 to disable.', 'wpmovielibrary' ),
				'default'  => 0,
				'validate' => 'numeric',
				'required' => array( 'wpmoly-enable-actor', "=", 1 ),
				'indent'   => true
			),
		),
	),

	// Languages
	'wpmoly-translate' => array(
		'icon'    => 'el-icon-flag',
		'title'   => __( 'Languages', 'wpmovielibrary' ),
		'heading' => __( 'Languages Support', 'wpmovielibrary' ),
		'fields'  => array(

			'translate-countries' => array(
				'id'       => 'wpmoly-translate-countries',
				'type'     => 'switch',
				'title'    => __( 'Translate Countries', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, countries names will be translated to the current WordPress language.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			'translate-languages' => array(
				'id'       => 'wpmoly-translate-languages',
				'type'     => 'switch',
				'title'    => __( 'Translate Languages', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, languages names will be translated to the current WordPress language.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Movie URL Rewrite Rule
			'rewrite-movie' => array(
				'id'       => 'wpmoly-rewrite-movie',
				'type'     => 'text',
				'title'    => __( 'Movies URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'URL Rewrite Rule to apply on movies. Default is <code>movies</code>, resulting in URL like <code>http://yourblog/movies/fight-club</code>. You can use this field to translate URLs to your language. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				//'validate' => 'no_special_chars',
				'validate_callback' => 'WPML_Settings::notify_permalinks_change',
				'default'  => 'movies'
			),

			// Movie URL Rewrite Rule
			'rewrite-details' => array(
				'id'       => 'wpmoly-rewrite-details',
				'type'     => 'switch',
				'title'    => __( 'Movie Details URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'Use localized URLs for Movie Details. Enable this to have URLs like <code>http://yourblog/films/disponible</code> for French rather than the default <code>http://yourblog/movies/available</code>. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),

			// Collections URL Rewrite Rule
			'rewrite-collection' => array(
				'id'       => 'wpmoly-rewrite-collection',
				'type'     => 'text',
				'title'    => __( 'Collections URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'URL Rewrite Rule to apply on collections. Default is <code>collection</code>, resulting in URL like <code>http://yourblog/collection/david-fincher</code>. You can use this field to translate URLs to your language. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				'validate' => 'no_special_chars',
				'default'  => 'collection',
				'required' => array( 'wpmoly-enable-collection', "=", 1 )
			),

			// Genres URL Rewrite Rule
			'rewrite-genre' => array(
				'id'       => 'wpmoly-rewrite-genre',
				'type'     => 'text',
				'title'    => __( 'Genres URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'URL Rewrite Rule to apply on genres. Default is <code>genre</code>, resulting in URL like <code>http://yourblog/genre/thriller</code>. You can use this field to translate URLs to your language. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				'validate' => 'no_special_chars',
				'default'  => 'genre',
				'required' => array( 'wpmoly-enable-genre', "=", 1 )
			),

			// Actors URL Rewrite Rule
			'rewrite-actor' => array(
				'id'       => 'wpmoly-rewrite-actor',
				'type'     => 'text',
				'title'    => __( 'Actors URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'URL Rewrite Rule to apply on actors. Default is <code>actor</code>, resulting in URL like <code>http://yourblog/actor/brad-pitt</code>. You can use this field to translate URLs to your language. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				'validate' => 'no_special_chars',
				'default'  => 'actor',
				'required' => array( 'wpmoly-enable-actor', "=", 1 )
			),

		)

	),

	// Caching
	'wpmoly-cache' => array(
		'icon'    => 'el-icon-hdd',
		'title'   => __( 'Cache', 'wpmovielibrary' ),
		'heading' => __( 'Caching', 'wpmovielibrary' ),
		'fields'  => array(

			// Results caching
			'enable' => array(
				'id'       => 'wpmoly-enable-cache',
				'type'     => 'switch',
				'title'    => __( 'Enable Caching', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, WPML will cache movie related data to prevent too frequent queries to the database. <strong>This feature is experimental!</strong> Enabling this could generate <strong>huge</strong> amounts of entries in your database. It is recommended to use this feature sparingly, ideally not in production. <a href="http://wpmovielibrary.com/documentation/performance">Learn more about caching</a>.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),

			// Results caching
			'user' => array(
				'id'       => 'wpmoly-user-cache',
				'type'     => 'switch',
				'title'    => __( 'User Caching', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, caching will be activated for logged in users as well as guests.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0,
				'required' => array( 'wpmoly-enable-cache', "=", 1 ),
				'indent'   => true
			),

			// Caching delay
			'expire' => array(
				'id'       => 'wpmoly-cache-expire',
				'type'     => 'text',
				'title'    => __( 'Caching Time', 'wpmovielibrary' ),
				'desc'     => __( 'Time of validity for cached data, in seconds. Default is 3600 (one hour)', 'wpmovielibrary' ),
				'validate' => 'numeric',
				'default'  => 3600,
				'required' => array( 'wpmoly-enable-cache', "=", 1 ),
				'indent'   => true
			)
		)
	),

	// Legacy
	'wpmoly-legacy' => array(
		'icon'    => 'el-icon-leaf',
		'title'   => __( 'Legacy', 'wpmovielibrary' ),
		'heading' => __( 'Compatibility settings for WPMovieLibrary 1.x', 'wpmovielibrary' ),
		'fields'  => array(

			// Results caching
			'legacy-mode' => array(
				'id'       => 'wpmoly-legacy-mode',
				'type'     => 'switch',
				'title'    => __( 'Enable Legacy mode', 'wpmovielibrary' ),
				'subtitle' => __( 'WPMovieLibrary 1.x compatibility mode', 'wpmovielibrary' ),
				'description' => __( 'If enabled, WPML will automatically update all movies to the new metadata format introduced by version 1.3. Each time a metadata is access, the plugin will look for obsolete metadata and will update it if needed. Once all movies are updated the plugin will stop looking, but you should deactivate this anyway. <a href="http://wpmovielibrary/development/release-notes/#version-1.3">Learn more about this change</a>.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			)
		)
	),

	'wpmoly-api' => array(
		'icon'    => 'el-icon-cogs',
		'title'   => __( 'API', 'wpmovielibrary' ),
		'heading' => __( 'TheMovieDB API settings', 'wpmovielibrary' ),
		'fields'  => array(

                        'personnal' => array(
				'id'       => 'wpmoly-api-internal',
				'type'     => 'switch',
				'title'    => __( 'Personnal API Key', 'wpmovielibrary' ),
				'subtitle' => __( 'Optional: use your own TMDb API key', 'wpmovielibrary' ),
				'desc'     => __( 'A valid TMDb API key is required to fetch informations on the movies you add to WPMovieLibrary. Leave deactivated if you do not have a personnal API key. <a href="http://tmdb.caercam.org/">Learn more</a> about the API key or <a href="https://www.themoviedb.org/">get your own</a>.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'hint'     => array( 'content' => __( 'If you do not have an API key or do not want to use yours right now, WPMovieLibrary will use just its own.', 'wpmovielibrary' ) ),
                        ),

			'api_key' => array(
				'id'       => 'wpmoly-api-key',
				'type'     => 'text',
				'title'    => __( 'API Key', 'wpmovielibrary' ),
				'subtitle' => __( 'Set up your own API key', 'wpmovielibrary' ),
				'desc'     => __( 'Using your own API key is a more privacy-safe choice as it will avoid WPMovieLibrary to filter queries sent to the API through its own relay server at tmdb.caercam.org. You will also be able to access statistics on your API usage in your TMDb user account.', 'wpmovielibrary' ), 
				'validate' => 'no_special_chars',
				'validate_callback' => array( 'WPML_TMDb', 'check_api_key' ),
				'default'  => null,
				'required' => array( 'wpmoly-api-internal', "=", 1 ),
				'indent'   => true
			),

			'language' => array(
				'id'       => 'wpmoly-api-language',
				'type'     => 'select',
				'title'    => __( 'API Language', 'wpmovielibrary' ),
				'desc'     => __( 'Default language to use when fetching informations from TMDb. Default is english. You can always change this manually when add a new movie.', 'wpmovielibrary' ),
				'options'  => array(
					"bg" => "български език",
					"cs" => "Český",
					"da" => "Dansk",
					"de" => "Deutsch",
					"el" => "ελληνικά",
					"en" => "English",
					"es" => "Español",
					"fi" => "Suomi",
					"fr" => "Français",
					"he" => "עִבְרִית",
					"hu" => "Magyar",
					"it" => "Italiano",
					"ko" => "한국어/조선말",
					"nb" => "Bokmål",
					"nl" => "Nederlands",
					"no" => "Norsk",
					"pl" => "Polski",
					"pt" => "Português",
					"ru" => "Pусский",
					"sk" => "Slovenčina",
					"sv" => "Svenska",
					"th" => "ภาษาไทย",
					"tr" => "Türkçe",
					"uk" => "Український",
					"zh" => "中国"
				),
				'default' => 'en'
			),

			// API Scheme
			'scheme' => array(
				'id'       => 'wpmoly-api-scheme',
				'type'     => 'select',
				'title'    => __( 'API Scheme', 'wpmovielibrary' ),
				'desc'     => __( 'Default scheme used to contact TMDb API. Default is HTTPS.', 'wpmovielibrary' ),
				'options'  => array(
					'http'  => __( 'HTTP', 'wpmovielibrary' ),
					'https' => __( 'HTTPS', 'wpmovielibrary' )
				),
				'default' => 'https'
			),
		)
	),

	array(
		'type' => 'divide',
	),

	// What to do on deactivation
	'wpmoly-deactivate' => array(
		'icon'    => 'el-icon-off',
		'title'   => __( 'Deactivate', 'wpmovielibrary' ),
		'heading' => __( 'Deactivation options', 'wpmovielibrary' ),
		'fields'  => array(

			'movies' => array(
				'id'       => 'wpmoly-deactivate-movies',
				'type'     => 'select',
				'title'    => __( 'Movie Post Type', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Movies when WPML is deactivated.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve (recommended)', 'wpmovielibrary' ),
					'convert'  => __( 'Convert to Posts', 'wpmovielibrary' ),
					'remove'   => __( 'Delete (irreversible)', 'wpmovielibrary' ),
					'delete'   => __( 'Delete Completely (irreversible)', 'wpmovielibrary' ),
				),
				'default' => 'conserve'
			),

			'collections' => array(
				'id'       => 'wpmoly-deactivate-collections',
				'type'     => 'select',
				'title'    => __( 'Collections Taxonomy', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Collections Taxonomy when WPML is deactivated.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve (recommended)', 'wpmovielibrary' ),
					'convert'  => __( 'Convert to Categories', 'wpmovielibrary' ),
					'delete'   => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default'  => 'conserve'
			),

			'genres' => array(
				'id'       => 'wpmoly-deactivate-genres',
				'type'     => 'select',
				'title'    => __( 'Genres Taxonomy', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Genres Taxonomy when WPML is deactivated.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve (recommended)', 'wpmovielibrary' ),
					'convert'  => __( 'Convert to Tags', 'wpmovielibrary' ),
					'delete'   => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default'  => 'conserve'
			),

			'actors' => array(
				'id'       => 'wpmoly-deactivate-actors',
				'type'     => 'select',
				'title'    => __( 'Actors Taxonomy', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Actors Taxonomy when WPML is deactivated.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve (recommended)', 'wpmovielibrary' ),
					'convert'  => __( 'Convert to Tags', 'wpmovielibrary' ),
					'delete'   => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default'  => 'conserve'
			),

			'cache'       => array(
				'id'       => 'wpmoly-deactivate-cache',
				'type'     => 'select',
				'title'    => __( 'Cache', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Cached data when WPML is deactivated.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'empty'    => __( 'Empty (recommended)', 'wpmovielibrary' ),
				),
				'default'  => 'empty'
			)
		)
	),

	// What to do on uninstallation
	'wpmoly-uninstall' => array(
		'icon'    => 'el-icon-remove-sign',
		'title'   => __( 'Uninstall', 'wpmovielibrary' ),
		'heading' => __( 'Uninstallation options', 'wpmovielibrary' ),
		'fields'  => array(

			'movies' => array(
				'id'       => 'wpmoly-uninstall-movies',
				'type'     => 'select',
				'title'    => __( 'Movie Post Type', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Movies when WPML is uninstalled.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'convert'  => __( 'Convert to Posts (recommended)', 'wpmovielibrary' ),
					'delete'   => __( 'Delete Completely (irreversible)', 'wpmovielibrary' ),
				),
				'default'  => 'convert'
			),

			'collections' => array(
				'id'       => 'wpmoly-uninstall-collections',
				'type'     => 'select',
				'title'    => __( 'Collections Taxonomy', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Collections Taxonomy when WPML is uninstalled.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'convert'  => __( 'Convert to Categories (recommended)', 'wpmovielibrary' ),
					'delete'   => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default'  => 'convert'
			),

			'genres' => array(
				'id'       => 'wpmoly-uninstall-genres',
				'type'     => 'select',
				'title'    => __( 'Genres Taxonomy', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Genres Taxonomy when WPML is uninstalled.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'convert'  => __( 'Convert to Tags (recommended)', 'wpmovielibrary' ),
					'delete'   => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default'  => 'convert'
			),

			'actors' => array(
				'id'       => 'wpmoly-uninstall-actors',
				'type'     => 'select',
				'title'    => __( 'Actors Taxonomy', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Actors Taxonomy when WPML is uninstalled.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'convert'  => __( 'Convert to Tags (recommended)', 'wpmovielibrary' ),
					'delete'   => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default'  => 'convert'
			),

			'cache' => array(
				'id'       => 'wpmoly-uninstall-cache',
				'type'     => 'select',
				'title'    => __( 'Cache', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Cached data when WPML is uninstalled.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'empty'    => __( 'Empty (recommended)', 'wpmovielibrary' ),
				),
				'default'  => 'empty'
			)
		)
	),

	'wpmoly-import-export' => array(
		'icon'    => 'el-icon-refresh',
		'title'   => __( 'Import / Export', 'wpmovielibrary' ),
		'heading' => __( 'Import and Export your settings and movies from file, text or URL.', 'wpmovielibrary' ),
		'fields'  => array(

			'import-export' => array(
				'id'         => 'wpmoly-import-export',
				'type'       => 'import_export',
				'title'      => 'Import Export',
				'subtitle'   => 'Save and restore your settings',
				'full_width' => false,
			)

		),
	),

	'divide-1' => array(
		'type' => 'divide',
	),

	'wpmoly-about' => array(
		'icon'   => 'el-icon-info-sign',
		'title'  => __( 'Information', 'wpmovielibrary' ),
		'desc'   => __( '<p class="description">This is the Description. Again HTML is allowed</p>', 'wpmovielibrary' ),
		'fields' => array(
			array(
				'id'      => 'wpmoly-raw-info',
				'type'    => 'raw',
				'content' => '',
			)
		),
	)
);