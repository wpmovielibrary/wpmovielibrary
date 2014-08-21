<?php
/**
 * WPMovieLibrary Default Config
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	wp_die();
}

$wpml_settings = array(
	'settings_revision' => WPML_SETTINGS_REVISION,
	'tmdb' => array(
		'section' => array(
			'id'       => 'tmdb',
			'title'    => __( 'API Settings', 'wpmovielibrary' ),
		),
		'settings' => array(

			// Foo Bar test option
			/*'foobar' => array(
				'title' => __( 'Foo', 'wpmovielibrary' ),
				'description' => 'Bar',
				'type' => 'input',
				'default' => 'foobar'
			),*/

			// TMDb API Key
			'apikey' => array(
				'title' => __( 'API Key', 'wpmovielibrary' ),
				'description' => __( 'You need a valid TMDb API key in order to fetch informations on the movies you add to WPMovieLibrary. You can get an individual API key by registering on <a href="https://www.themoviedb.org/">TheMovieDB</a>.', 'wpmovielibrary' ),
				'type' => 'input',
				'default' => ''
			),

			// API internal mode
			'internal_api' => array(
				'title' => __( 'Use internal API Key', 'wpmovielibrary' ),
				'description' => __( 'If you enable this, WPML will send all queries to the API through a relay server at http://tmdb.caercam.org/; The main purpose of this is to avoid you the need to register a personal account on TMDb and request a personal API key to use the plugin. Note that the relay server provides no warranty and is most indicated for testing the plugin or a minimal usage of it; if you are planning to use WPMovieLibrary extensively, you should consider getting your own API key at <a href="https://www.themoviedb.org/">TheMovieDB</a>. <a href="http://tmdb.caercam.org/">Learn more about the internal API key</a>.', 'wpmovielibrary' ),
				'type'=> 'toggle',
				'default' => 1
			),

			// API Lang
			'lang' => array(
				'title' => __( 'API Language', 'wpmovielibrary' ),
				'description' => __( 'Default language to use when fetching informations from TMDb. Default is english. You can always change this manually when add a new movie.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
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
				'title' => __( 'API Scheme', 'wpmovielibrary' ),
				'description' => __( 'Default scheme used to contact TMDb API. Default is HTTPS.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'http' => __( 'HTTP', 'wpmovielibrary' ),
					'https' => __( 'HTTPS', 'wpmovielibrary' )
				),
				'default' => 'https'
			),
		)
	),
	'wpml' => array(
		'section' => array(
			'id'       => 'wpml',
			'title'    => WPML_NAME,
		),
		'settings' => array(

			// Add movies to the main loop
			'show_in_home' => array(
				'title'    => __( 'Show Movies in Home Page', 'wpmovielibrary' ),
				'description' => __( 'If enable, movies will appear among other Posts in the Home Page.', 'wpmovielibrary' ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Movie URL Rewrite Rule
			'movie_rewrite' => array(
				'title'    => __( 'Movies URL Rewrite', 'wpmovielibrary' ),
				'description' => __( 'URL Rewrite Rule to apply on movies. Default is <code>movies</code>, resulting in URL like <code>http://yourblog/movies/fight-club</code>. You can use this field to translate URLs to your language. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				'type'     => 'input',
				'default' => 'movies'
			),

			// Movie URL Rewrite Rule
			'details_rewrite' => array(
				'title'    => __( 'Movie Details URL Rewrite', 'wpmovielibrary' ),
				'description' => __( 'Use localized URLs for Movie Details. Enable this to have URLs like <code>http://yourblog/films/disponible</code> for French rather than the default <code>http://yourblog/movies/available</code>. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				'type'     => 'toggle',
				'default' => 0
			),

			// Show movie meta in posts
			'meta_in_posts' => array(
				'title' => __( 'Show basic movie metadata', 'wpmovielibrary' ),
				'description' => __( 'Add metadata to posts&rsquo; content: director, genres, runtime…', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'everywhere' => __( 'Everywhere', 'wpmovielibrary' ),
					'posts_only' => __( 'Only In Post Read', 'wpmovielibrary' ),
					'nowhere'    => __( 'Don&rsquo;t Show', 'wpmovielibrary' ),
				),
				'default' => 'posts_only'
			),

			// Default movie meta to show
			'default_movie_meta' => array(
				'title' => __( 'Movie metadata', 'wpmovielibrary' ),
				'description' => __( 'Which metadata to display in posts: director, genres, runtime, rating…', 'wpmovielibrary' ),
				'type' => 'select',
				'callback' => 'sorted_markup_fields',
				'default' => array(
					'director',
					'runtime',
					'release_date',
					'genres',
					'overview'
				)
			),

			// Show movie details in posts
			'details_in_posts' => array(
				'title' => __( 'Show movie details', 'wpmovielibrary' ),
				'description' => __( 'Add details to posts&rsquo; content: movie status, media…', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'everywhere'  => __( 'Everywhere', 'wpmovielibrary' ),
					'posts_only'  => __( 'Only In Post Read', 'wpmovielibrary' ),
					'nowhere'     => __( 'Don&rsquo;t Show', 'wpmovielibrary' )
				),
				'default' => 'posts_only'
			),

			// Show movie details as icons
			'details_as_icons' => array(
				'title' => __( 'Show details as icons', 'wpmovielibrary' ),
				'description' => __( 'If enable, movie details will appear in the form of icons rather than default colored labels.', 'wpmovielibrary' ),
				'type' => 'toggle',
				'default' => 1
			),

			// Default movie detail to show
			'default_movie_details' => array(
				'title' => __( 'Movie details', 'wpmovielibrary' ),
				'description' => __( 'Which detail to display in posts: movie status, media…', 'wpmovielibrary' ),
				'type' => 'multiple',
				'values' => array(
					'movie_media'  => __( 'Media', 'wpmovielibrary' ),
					'movie_status' => __( 'Status', 'wpmovielibrary' ),
					'movie_rating' => __( 'Rating', 'wpmovielibrary' )
				),
				'default' => array(
					'movie_media',
					'movie_status'
				)
			),

			// Release date formatting
			'date_format' => array(
				'title' => __( 'Release date format', 'wpmovielibrary' ),
				'description' => __( 'Apply a custom date format to movies\' release dates. Leave empty to use the default API format. Check the <a href="http://codex.wordpress.org/Formatting_Date_and_Time">documentation on date and time formatting</a>.', 'wpmovielibrary' ),
				'type' => 'input',
				'default' => 'j F Y'
			),

			// Release date formatting
			'time_format' => array(
				'title' => __( 'Runtime format', 'wpmovielibrary' ),
				'description' => __( 'Apply a custom time format to movies\' runtimes. Leave empty to use the default API format.', 'wpmovielibrary' ),
				'type' => 'input',
				'default' => 'G \h i \m\i\n'
			),
		),
	),

	// Taxonomies
	'images' => array(
		'section' => array(
			'id'       => 'images',
			'title'    => __( 'Images', 'wpmovielibrary' ),
		),
		'settings' => array(

			// Use posters as featured images
			'poster_featured' => array(
				'title' => __( 'Add Posters As Thumbnails', 'wpmovielibrary' ),
				'description' => __( 'Using posters as movies thumbnails will automatically import new movies&rsquo; poster and set them as post featured image. This setting doesn’t affect movie import by list where posters are automatically saved and set as featured image.', 'wpmovielibrary' ),
				'type' => 'toggle',
				'default' => 1
			),

			// Movie posters size
			'poster_size' => array(
				'title' => __( 'Posters Default Size', 'wpmovielibrary' ),
				'description' => __( 'Movie Poster size. Default is TMDb&rsquo;s original size.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'xx-small'  => __( 'Invisible (~100px)', 'wpmovielibrary' ),
					'x-small'  => __( 'Tiny (~150px)', 'wpmovielibrary' ),
					'small'  => __( 'Small (~200px)', 'wpmovielibrary' ),
					'medium'  => __( 'Medium (~350px)', 'wpmovielibrary' ),
					'large'  => __( 'Large (~500px)', 'wpmovielibrary' ),
					'full' => __( 'Full (~800px) ', 'wpmovielibrary' ),
					'original' => __( 'Original', 'wpmovielibrary' )
				),
				'default' => 'original'
			),

			// Images size
			'images_size' => array(
				'title' => __( 'Images Default Size', 'wpmovielibrary' ),
				'description' => __( 'Movie Image size. Default is TMDb&rsquo;s original size.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'small'  => __( 'Small (~200px)', 'wpmovielibrary' ),
					'medium'  => __( 'Medium (~350px)', 'wpmovielibrary' ),
					'full' => __( 'Full (~800px) ', 'wpmovielibrary' ),
					'original' => __( 'Original', 'wpmovielibrary' )
				),
				'default' =>'original'
			),

			// Maximum number of image to show
			'delete_images' => array(
				'title' => __( 'Delete images with movies', 'wpmovielibrary' ),
				'description' => __( 'Enable this if you want to delete all imported images along with movies. Handy if you have a great number of movies to delete and possibly dozens of images attached.', 'wpmovielibrary' ),
				'type' => 'toggle',
				'default' => 0
			),

			// Maximum number of image to show
			'delete_posters' => array(
				'title' => __( 'Delete posters with movies', 'wpmovielibrary' ),
				'description' => __( 'Enable this if you want to delete posters along with movies.', 'wpmovielibrary' ),
				'type' => 'toggle',
				'default' => 0
			),
		),
	),

	// Taxonomies
	'taxonomies' => array(
		'section' => array(
			'id'       => 'taxonomies',
			'title'    => __( 'Taxonomies', 'wpmovielibrary' ),
		),
		'settings' => array(

			// Enable Collections Taxonomy
			'enable_collection' => array(
				'title'    => __( 'Enable Collections', 'wpmovielibrary' ),
				'description' => __( 'Enable Collections Custom Taxonomy. Collections work for movies as Categories work for Posts: a hierarchical taxonomy to group your movies coherently. The default behavior is to use Collections to group movies by director, but you can use them differently at your will.', 'wpmovielibrary' ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Collections URL Rewrite Rule
			'collection_rewrite' => array(
				'title'    => __( 'Collections URL Rewrite', 'wpmovielibrary' ),
				'description' => __( 'URL Rewrite Rule to apply on collections. Default is <code>collection</code>, resulting in URL like <code>http://yourblog/collection/david-fincher</code>. You can use this field to translate URLs to your language. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				'type'     => 'input',
				'default' => 'collection'
			),

			// Enable Collections Autocomplete
			'collection_autocomplete' => array(
				'title'    => __( 'Add Collections automatically', 'wpmovielibrary' ),
				'description' => __( 'Automatically add custom taxonomies when adding/importing movies. If enabled, each added/imported movie will be automatically added to the collection corresponding to its director(s).', 'wpmovielibrary' ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Enable Genres Taxonomy
			'enable_genre' => array(
				'title'    => __( 'Enable Genres', 'wpmovielibrary' ),
				'description' => __( 'Enable Genres Custom Taxonomy. Genres work for movies as Tags work for Posts: a non-hierarchical taxonomy to improve movies management.', 'wpmovielibrary' ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Genres URL Rewrite Rule
			'genre_rewrite' => array(
				'title'    => __( 'Genres URL Rewrite', 'wpmovielibrary' ),
				'description' => __( 'URL Rewrite Rule to apply on genres. Default is <code>genre</code>, resulting in URL like <code>http://yourblog/genre/thriller</code>. You can use this field to translate URLs to your language. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				'type'     => 'input',
				'default' => 'genre'
			),

			// Enable Genres Autocomplete
			'genre_autocomplete' => array(
				'title'    => __( 'Add Genres automatically', 'wpmovielibrary' ),
				'description' => __( 'Automatically add Genres when adding/importing movies.', 'wpmovielibrary' ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Enable Actors Taxonomy
			'enable_actor' => array(
				'title'    => __( 'Enable Actors', 'wpmovielibrary' ),
				'description' => __( 'Enable Actors Custom Taxonomy. Actors work for movies as Tags work for Posts: a non-hierarchical taxonomy to improve movies management. WPMovieLibrary stores Actors in a custom order, the most important actors appearing in the top of the list, then the supporting roles, and so on.', 'wpmovielibrary' ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Actors URL Rewrite Rule
			'actor_rewrite' => array(
				'title'    => __( 'Actors URL Rewrite', 'wpmovielibrary' ),
				'description' => __( 'URL Rewrite Rule to apply on actors. Default is <code>actor</code>, resulting in URL like <code>http://yourblog/actor/brad-pitt</code>. You can use this field to translate URLs to your language. <strong>Beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will most likely affect with your site\'s visibility.', 'wpmovielibrary' ),
				'type'     => 'input',
				'default' => 'actor'
			),

			// Enable Actors Autocomplete
			'actor_autocomplete' => array(
				'title'    => __( 'Add Actors automatically', 'wpmovielibrary' ),
				'description' => __( 'Automatically add Actors when adding/importing movies.', 'wpmovielibrary' ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Enable Actors Autocomplete
			'actor_limit' => array(
				'title'    => __( 'Actors limit', 'wpmovielibrary' ),
				'description' => __( 'Limit the number of actors per movie. This is useful if you\'re dealing with big libraries and don\'t want to have massive lists of actors created. Limiting the Actors will result in keeping only the most famous/important actors as taxonomies, while the complete list of actors will remained stored as a regular metadata. Set to 0 to disable.', 'wpmovielibrary' ),
				'type'     => 'input',
				'default' => 0
			),
		),
	),

	// What to do on deactivation
	'deactivate' => array(
		'section' => array(
			'id'       => 'deactivate',
			'title'    => __( 'Deactivate', 'wpmovielibrary' ),
		),
		'settings' => array(
			'movies' => array(
				'title' => __( 'Movie Post Type', 'wpmovielibrary' ),
				'description' => __( 'How to handle Movies when WPML is deactivated.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve (recommended)', 'wpmovielibrary' ),
					'convert' => __( 'Convert to Posts', 'wpmovielibrary' ),
					'remove' => __( 'Delete (irreversible)', 'wpmovielibrary' ),
					'delete' => __( 'Delete Completely (irreversible)', 'wpmovielibrary' ),
				),
				'default' => 'conserve'
			),

			'collections' => array(
				'title' => __( 'Collections Taxonomy', 'wpmovielibrary' ),
				'description' => __( 'How to handle Collections Taxonomy when WPML is deactivated.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve (recommended)', 'wpmovielibrary' ),
					'convert' => __( 'Convert to Categories', 'wpmovielibrary' ),
					'delete' => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default' => 'conserve'
			),

			'genres'      => array(
				'title' => __( 'Genres Taxonomy', 'wpmovielibrary' ),
				'description' => __( 'How to handle Genres Taxonomy when WPML is deactivated.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve (recommended)', 'wpmovielibrary' ),
					'convert' => __( 'Convert to Tags', 'wpmovielibrary' ),
					'delete' => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default' => 'conserve'
			),

			'actors'      => array(
				'title' => __( 'Actors Taxonomy', 'wpmovielibrary' ),
				'description' => __( 'How to handle Actors Taxonomy when WPML is deactivated.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve (recommended)', 'wpmovielibrary' ),
					'convert' => __( 'Convert to Tags', 'wpmovielibrary' ),
					'delete' => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default' => 'conserve'
			),

			'cache'       => array(
				'title' => __( 'Cache', 'wpmovielibrary' ),
				'description' => __( 'How to handle Cached data when WPML is deactivated.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'empty' => __( 'Empty (recommended)', 'wpmovielibrary' ),
				),
				'default' => 'empty'
			)
		)
	),

	// What to do on uninstallation
	'uninstall' => array(
		'section' => array(
			'id'       => 'uninstall',
			'title'    => __( 'Uninstall', 'wpmovielibrary' ),
		),
		'settings' => array(
			'movies'      => array(
				'title' => __( 'Movie Post Type', 'wpmovielibrary' ),
				'description' => __( 'How to handle Movies when WPML is uninstalled.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'convert' => __( 'Convert to Posts (recommended)', 'wpmovielibrary' ),
					'delete' => __( 'Delete Completely (irreversible)', 'wpmovielibrary' ),
				),
				'default' => 'convert'
			),

			'collections' => array(
				'title' => __( 'Collections Taxonomy', 'wpmovielibrary' ),
				'description' => __( 'How to handle Collections Taxonomy when WPML is uninstalled.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'convert' => __( 'Convert to Categories (recommended)', 'wpmovielibrary' ),
					'delete' => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default' => 'convert'
			),

			'genres'      => array(
				'title' => __( 'Genres Taxonomy', 'wpmovielibrary' ),
				'description' => __( 'How to handle Genres Taxonomy when WPML is uninstalled.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'convert' => __( 'Convert to Tags (recommended)', 'wpmovielibrary' ),
					'delete' => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default' => 'convert'
			),

			'actors'      => array(
				'title' => __( 'Actors Taxonomy', 'wpmovielibrary' ),
				'description' => __( 'How to handle Actors Taxonomy when WPML is uninstalled.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'convert' => __( 'Convert to Tags (recommended)', 'wpmovielibrary' ),
					'delete' => __( 'Delete (irreversible)', 'wpmovielibrary' ),
				),
				'default' => 'convert'
			),

			'cache'       => array(
				'title' => __( 'Cache', 'wpmovielibrary' ),
				'description' => __( 'How to handle Cached data when WPML is uninstalled.', 'wpmovielibrary' ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'empty' => __( 'Empty (recommended)', 'wpmovielibrary' ),
				),
				'default' => 'empty'
			)
		)
	),

	// Caching
	'cache' => array(
		'section' => array(
			'id'       => 'cache',
			'title'    => __( 'Cache', 'wpmovielibrary' ),
		),
		'settings' => array(
			// Results caching
			'caching' => array(
				'title' => __( 'Enable Caching', 'wpmovielibrary' ),
				'description' => __( 'If enabled, WPML will cache movie related data to prevent too frequent queries to the database. <strong>This feature is experimental!</strong> Enabling this could generate <strong>huge</strong> amounts of entries in your database. It is recommended to use this feature sparingly, ideally not in production. <a href="http://wpmovielibrary.com/documentation/performance">Learn more about caching</a>.', 'wpmovielibrary' ),
				'type' => 'toggle',
				'default' => 0
			),

			// Results caching
			'user_caching' => array(
				'title' => __( 'User Caching', 'wpmovielibrary' ),
				'description' => __( 'If enabled, caching will be activated for logged in users as well as guests.', 'wpmovielibrary' ),
				'type' => 'toggle',
				'default' => 0
			),

			// Caching delay
			'caching_time' => array(
				'title' => __( 'Caching Time', 'wpmovielibrary' ),
				'description' => __( 'Time of validity for cached data, in seconds. Default is 3600 (one hour)', 'wpmovielibrary' ),
				'type' => 'input',
				'default' => 3600
			)
		)
	)
);

$wpml_movie_details = array(
	'movie_media'   => array(
		'title' => __( 'Media', 'wpmovielibrary' ),
		'options' => array(
			'dvd'     => __( 'DVD', 'wpmovielibrary' ),
			'bluray'  => __( 'Blu-ray', 'wpmovielibrary' ),
			'vod'     => __( 'VoD', 'wpmovielibrary' ),
			'divx'    => __( 'DivX', 'wpmovielibrary' ),
			'vhs'     => __( 'VHS', 'wpmovielibrary' ),
			'cinema'  => __( 'Cinema', 'wpmovielibrary' ),
			'other'   => __( 'Other', 'wpmovielibrary' ),
		),
		'default' => array(
			'dvd'   => __( 'DVD', 'wpmovielibrary' ),
		),
	),
	'movie_status'  => array(
		'title' => __( 'Status', 'wpmovielibrary' ),
		'options' => array(
			'available'   => __( 'Available', 'wpmovielibrary' ),
			'loaned'      => __( 'Loaned', 'wpmovielibrary' ),
			'scheduled'   => __( 'Scheduled', 'wpmovielibrary' ),
			'unavailable' => __( 'Unvailable', 'wpmovielibrary' ),
		),
		'default' => array(
			'available' => __( 'Available', 'wpmovielibrary' ),
		)
	),
	'movie_rating'  => array(
		'title' => __( 'Rating', 'wpmovielibrary' ),
		'options' => array(
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
		'default' => array(
			'0.0' => '',
		)
	)
);

$wpml_movie_meta = array(
	'meta' => array(
		'type' => __( 'Type', 'wpmovielibrary' ),
		'value' => __( 'Value', 'wpmovielibrary' ),
		'data' => array(
			'title' => array(
				'title' => __( 'Title', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'wp_kses',
				'filter_args' => array( 'b' => array(), 'i' => array(), 'em' => array(), 'strong' => array(), 'sup' => array(), 'sub' => array() )
			),
			'original_title' => array(
				'title' => __( 'Original Title', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'wp_kses',
				'filter_args' => array( 'b' => array(), 'i' => array(), 'em' => array(), 'strong' => array(), 'sup' => array(), 'sub' => array() )
			),
			'overview' => array(
				'title' => __( 'Overview', 'wpmovielibrary' ),
				'type' => 'textarea',
				'filter' => 'wp_kses',
				'filter_args' => array( 'b' => array(), 'i' => array(), 'em' => array(), 'strong' => array(), 'sup' => array(), 'sub' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(), 'br' => array(), 'span' => array() )
			),
			'production_companies' => array(
				'title' => __( 'Production', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'production_countries' => array(
				'title' => __( 'Country', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'spoken_languages' => array(
				'title' => __( 'Languages', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'runtime' => array(
				'title' => __( 'Runtime', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'genres' => array(
				'title' => __( 'Genres', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'release_date' => array(
				'title' => __( 'Release Date', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			)
		)
	),
	'crew' => array(
		'type' => __( 'Job', 'wpmovielibrary' ),
		'value' => __( 'Name(s)', 'wpmovielibrary' ),
		'data' => array(
			'director' => array(
				'title' => __( 'Director', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'producer' => array(
				'title' => __( 'Producer', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'photography' => array(
				'title' => __( 'Director of Photography', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'composer' => array(
				'title' => __( 'Original Music Composer', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'author' => array(
				'title' => __( 'Author', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'writer' => array(
				'title' => __( 'Writer', 'wpmovielibrary' ),
				'type' => 'text',
				'filter' => 'esc_html',
				'filter_args' => null
			),
			'cast' => array(
				'title' => __( 'Actors', 'wpmovielibrary' ),
				'type' => 'textarea',
				'filter' => 'esc_html',
				'filter_args' => null
			)
		)
	)
);

$wpml_movie_meta_aliases = array(

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

$wpml_metaboxes = array(

	array(
		'id'            => 'wpml_details',
		'title'         => __( 'WPMovieLibrary − Movie Details', 'wpmovielibrary' ),
		'callback'      => 'WPML_Edit_Movies::metabox_details',
		'screen'        => 'movie',
		'context'       => 'side',
		'priority'      => 'default',
		'callback_args' => null
	),
	array(
		'id'            => 'wpml_meta',
		'title'         => __( 'WPMovieLibrary − Movie Meta', 'wpmovielibrary' ),
		'callback'      => 'WPML_Edit_Movies::metabox_meta',
		'screen'        => 'movie',
		'context'       => 'normal',
		'priority'      => 'high',
		'callback_args' => null
	),
	array(
		'id'            => 'wpml_images',
		'title'         => __( 'WPMovieLibrary − Movie Images', 'wpmovielibrary' ),
		'callback'      => 'WPML_Edit_Movies::metabox_images',
		'screen'        => 'movie',
		'context'       => 'normal',
		'priority'      => 'high',
		'callback_args' => null
	),
);

$wpml_dashboard_widgets = array(
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

$wpml_shortcodes = array(

	'movies' => array(
		'atts' => array(
			'collection' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'genre' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'actor' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'order' => array(
				'default' => 'desc',
				'values'  => array( 'asc', 'desc' ),
				'filter'  => 'esc_attr'
			),
			'orderby' => array(
				'default' => 'date',
				'values'  => array( 'date', 'title', 'rating' ),
				'filter'  => 'esc_attr'
			),
			'count' => array(
				'default' => 4,
				'values'  => null,
				'filter'  => 'intval'
			),
			'poster' => array(
				'default' => 'medium',
				'values'  => array( 'none', 'thumb', 'thumbnail', 'medium', 'large', 'full' ),
				'filter'  => 'esc_attr'
			),
			'meta' => array(
				'default' => null,
				'values'  => array( 'director', 'runtime', 'release_date', 'genres', 'actors', 'overview', 'title', 'original_title', 'production', 'country', 'language', 'producer', 'photography', 'composer', 'author', 'writer' ),
				'filter'  => null
			),
			'details' => array(
				'default' => null,
				'values'  => array( 'media', 'status', 'rating' ),
				'filter'  => null
			)
		),
		'content'  => null,
		'callback' => 'movies_shortcode',
		'aliases'  => null
	),

	'movie' => array(
		'atts'     => array(
			'id' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'title' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'poster' => array(
				'default' => 'medium',
				'values'  => array( 'none', 'thumb', 'thumbnail', 'medium', 'large', 'full' ),
				'filter'  => 'esc_attr'
			),
			'meta' => array(
				'default' => array( 'director', 'runtime', 'release_date', 'genres', 'actors', 'overview' ),
				'values'  => array( 'director', 'runtime', 'release_date', 'genres', 'actors', 'overview', 'title', 'original_title', 'production', 'country', 'language', 'producer', 'photography', 'composer', 'author', 'writer' ),
				'filter'  => null
			),
			'details' => array(
				'default' => array( 'media', 'status', 'rating' ),
				'values'  => array( 'media', 'status', 'rating' ),
				'filter'  => null
			)
		),
		'content'  => null,
		'callback' => 'movie_shortcode',
		'aliases'  => null
	),

	'movie_meta' => array(
		'atts'     => array(
			'id' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'title' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'key' => array(
				'default' => null,
				'values'  => array( 'director', 'runtime', 'release_date', 'genres', 'actors', 'overview', 'title', 'original_title', 'production', 'country', 'language', 'producer', 'photography', 'composer', 'author', 'writer' ),
				'filter'  => 'esc_attr'
			),
			'label' => array(
				'default' => true,
				'values'  => 'boolean',
				'filter'  => 'esc_attr'
			)
		),
		'content'  => null,
		'callback' => 'movie_meta_shortcode',
		'aliases' => array(
			'movie_director',
			'movie_overview',
			'movie_title',
			'movie_original_title',
			'movie_production',
			'movie_country',
			'movie_language',
			'movie_lang',
			'movie_producer',
			'movie_photography',
			'movie_composer',
			'movie_author',
			'movie_writer'
		)
	),

	'movie_runtime' => array(
		'atts'     => array(
			'id' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'title' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'format' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'label' => array(
				'default' => true,
				'values'  => 'boolean',
				'filter'  => 'esc_attr'
			)
		),
		'content'  => null,
		'callback' => 'movie_runtime_shortcode',
		'aliases'  => null
	),

	'movie_release_date' => array(
		'atts'     => array(
			'id' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'title' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'format' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'label' => array(
				'default' => true,
				'values'  => 'boolean',
				'filter'  => 'esc_attr'
			)
		),
		'content'  => null,
		'callback' => 'movie_release_date_shortcode',
		'aliases'  => array( 'movie_date' )
	),

	'movie_actors' => array(
		'atts'     => array(
			'id' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'title' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'count' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'intval'
			),
			'label' => array(
				'default' => true,
				'values'  => 'boolean',
				'filter'  => 'esc_attr'
			)
		),
		'content'  => null,
		'callback' => 'movie_actors_shortcode',
		'aliases'  => array( 'movie_cast', 'movie_casting' )
	),

	'movie_genres' => array(
		'atts'     => array(
			'id' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'title' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'count' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'intval'
			),
			'label' => array(
				'default' => true,
				'values'  => 'boolean',
				'filter'  => 'esc_attr'
			)
		),
		'content'  => null,
		'callback' => 'movie_genres_shortcode',
		'aliases'  => null
	),

	'movie_poster' => array(
		'atts'     => array(
			'id' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'title' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'size' => array(
				'default' => 'medium',
				'values'  => array( 'none', 'thumb', 'thumbnail', 'medium', 'large', 'full' ),
				'filter'  => 'esc_attr'
			),
		),
		'content'  => null,
		'callback' => 'movie_poster_shortcode',
		'aliases'  => null
	),

	'movie_images' => array(
		'atts'     => array(
			'id' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'title' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'size' => array(
				'default' => 'thumbnail',
				'values'  => array( 'none', 'thumb', 'thumbnail', 'medium', 'large', 'full' ),
				'filter'  => 'esc_attr'
			),
			'count' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'intval'
			),
			'label' => array(
				'default' => true,
				'values'  => 'boolean',
				'filter'  => 'esc_attr'
			)
		),
		'content'  => null,
		'callback' => 'movie_images_shortcode',
		'aliases'  => array( 'movie_pictures', 'movie_photos' )
	),

	'movie_detail' => array(
		'atts'     => array(
			'id' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'title' => array(
				'default' => null,
				'values'  => null,
				'filter'  => 'esc_attr'
			),
			'key' => array(
				'default' => null,
				'values'  => array( 'media', 'status', 'rating' ),
				'filter'  => 'esc_attr'
			),
			'raw' => array(
				'default' => true,
				'values'  => 'boolean',
				'filter'  => 'esc_attr'
			),
		),
		'content'  => null,
		'callback' => 'movie_detail_shortcode',
		'aliases' => array(
			'movie_media',
			'movie_status',
			'movie_rating'
		)
	),
);
