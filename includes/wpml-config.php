<?php
/**
 * WPMovieLibrary Default Config
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
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
			'title'    => __( 'API Settings', WPML_SLUG ),
		),
		'settings' => array(

			// TMDb API Key
			'apikey' => array(
				'title' => __( 'API Key', WPML_SLUG ),
				'description' => __( 'You need a valid TMDb API key in order to fetch informations on the movies you add to WPMovieLibrary. You can get an individual API key by registering on <a href="https://www.themoviedb.org/">TheMovieDB</a>. If you don&rsquo;t want to get your own API Key, WPMovieLibrary will use a dummy, more restricted API. <a href="http://tmdb.caercam.org/">Learn more about the dummy API</a>.', WPML_SLUG ),
				'type' => 'input',
				'default' => ''
			),

			// API Dummy mode
			'dummy' => array(
				'title' => __( 'Use dummy API', WPML_SLUG ),
				'description' => __( '', WPML_SLUG ),
				'type'=> 'toggle',
				'default' => 1
			),

			// API Lang
			'lang' => array(
				'title' => __( 'API Language', WPML_SLUG ),
				'description' => __( 'Default language to use when fetching informations from TMDb. Default is english. You can always change this manually when add a new movie.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'en' => __( 'English', WPML_SLUG ),
					'fr' => __( 'French', WPML_SLUG )
				),
				'default' => 'en'
			),

			// API Scheme
			'scheme' => array(
				'title' => __( 'API Scheme', WPML_SLUG ),
				'description' => __( 'Default scheme used to contact TMDb API. Default is HTTPS.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'http' => __( 'HTTP', WPML_SLUG ),
					'https' => __( 'HTTPS', WPML_SLUG )
				),
				'default' => 'https'
			),

			// Movie posters size
			'poster_size' => array(
				'title' => __( 'Posters Default Size', WPML_SLUG ),
				'description' => __( 'Movie Poster size. Default is TMDb&rsquo;s original size.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'xx-small'  => __( 'Invisible (~100px)', WPML_SLUG ),
					'x-small'  => __( 'Tiny (~150px)', WPML_SLUG ),
					'small'  => __( 'Small (~200px)', WPML_SLUG ),
					'medium'  => __( 'Medium (~350px)', WPML_SLUG ),
					'large'  => __( 'Large (~500px)', WPML_SLUG ),
					'full' => __( 'Full (~800px) ', WPML_SLUG ),
					'original' => __( 'Original', WPML_SLUG )
				),
				'default' => 'original'
			),

			// Use posters as featured images
			'poster_featured' => array(
				'title' => __( 'Add Posters As Thumbnails', WPML_SLUG ),
				'description' => __( 'Using posters as movies thumbnails will automatically import new movies&rsquo; poster and set them as post featured image. This setting doesn’t affect movie import by list where posters are automatically saved and set as featured image.', WPML_SLUG ),
				'type' => 'toggle',
				'default' => 1
			),

			// Images size
			'images_size' => array(
				'title' => __( 'Images Default Size', WPML_SLUG ),
				'description' => __( 'Movie Image size. Default is TMDb&rsquo;s original size.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'small'  => __( 'Small (~200px)', WPML_SLUG ),
					'medium'  => __( 'Medium (~350px)', WPML_SLUG ),
					'full' => __( 'Full (~800px) ', WPML_SLUG ),
					'original' => __( 'Original', WPML_SLUG )
				),
				'default' =>'original'
			),

			// Maximum number of image to show
			'images_max' => array(
				'title' => __( 'Maximum Images To Fetch', WPML_SLUG ),
				'description' => __( 'Maximum amount of images to fetch. Especially useful if you activated automatic images import. Default is12, set at 0 to fetch all images.', WPML_SLUG ),
				'type' => 'input',
				'default' => 12
			),

			// Results caching
			'caching' => array(
				'title' => __( 'Enable Caching', WPML_SLUG ),
				'description' => __( 'When enabled, WPML will store for a variable time the data fetched from TMDb. This prevents WPML from generating excessive, useless duplicate queries to the API. This is especially useful if you’re using the dummy API. <a href="http://www.caercam.org/wpmovielibrary/">Learn more ~ WPML Caching</a>', WPML_SLUG ),
				'type' => 'toggle',
				'default' => 1
			),

			// Caching delay
			'caching_time' => array(
				'title' => __( 'Caching Time', WPML_SLUG ),
				'description' => __( 'Time of validity for Cached data, in days.', WPML_SLUG ),
				'type' => 'input',
				'default' => 15
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
				'title'    => __( 'Show Movies in Home Page', WPML_SLUG ),
				'description' => __( 'If enable, movies will appear among other Posts in the Home Page.', WPML_SLUG ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Show movie meta in posts
			'meta_in_posts' => array(
				'title' => __( 'Show basic movie metadata', WPML_SLUG ),
				'description' => __( 'Add metadata to posts&rsquo; content: director, genres, runtime…', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'everywhere' => __( 'Everywhere', WPML_SLUG ),
					'posts_only' => __( 'Only In Post Read', WPML_SLUG ),
					'nowhere'    => __( 'Don&rsquo;t Show', WPML_SLUG ),
				),
				'default' => 'posts_only'
			),

			// Show movie details in posts
			'details_in_posts' => array(
				'title' => __( 'Show movie details', WPML_SLUG ),
				'description' => __( 'Add details to posts&rsquo; content: movie status, media…', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'everywhere'  => __( 'Everywhere', WPML_SLUG ),
					'posts_only'  => __( 'Only In Post Read', WPML_SLUG ),
					'nowhere'     => __( 'Don&rsquo;t Show', WPML_SLUG )
				),
				'default' => 'posts_only'
			),

			// Show movie details as icons
			'details_as_icons' => array(
				'title' => __( 'Show details as icons', WPML_SLUG ),
				'description' => __( 'If enable, movie details will appear in the form of icons rather than default colored labels.', WPML_SLUG ),
				'type' => 'toggle',
				'default' => 1
			),

			// Default movie meta to show
			'default_movie_meta' => array(
				'title' => __( 'Movie metadata', WPML_SLUG ),
				'description' => __( 'Which metadata to display in posts: director, genres, runtime, rating…', WPML_SLUG ),
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

			// Default movie detail to show
			'default_movie_details' => array(
				'title' => __( 'Movie details', WPML_SLUG ),
				'description' => __( 'Which detail to display in posts: movie status, media…', WPML_SLUG ),
				'type' => 'multiple',
				'values' => array(
					'movie_media'  => __( 'Media', WPML_SLUG ),
					'movie_status' => __( 'Status', WPML_SLUG ),
					'movie_rating' => __( 'Rating', WPML_SLUG )
				),
				'default' => array(
					'movie_media',
					'movie_status'
				)
			),

			// Enable Collections Taxonomy
			'enable_collection' => array(
				'title'    => __( 'Enable Collections', WPML_SLUG ),
				'description' => __( 'Enable Custom Taxonomies to group movies. If enabled, three new Taxonomie will be active to help sort your movies: Collections, Actors and Genres. To learn more about WPML Taxonomies please refer to the documentation.', WPML_SLUG ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Enable Genres Taxonomy
			'enable_genre' => array(
				'title'    => __( 'Enable Genres', WPML_SLUG ),
				'description' => __( '', WPML_SLUG ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Enable Actors Taxonomy
			'enable_actor' => array(
				'title'    => __( 'Enable Actors', WPML_SLUG ),
				'description' => __( '', WPML_SLUG ),
				'type'     => 'toggle',
				'default' => 1
			),

			// Enable automatic adding of taxonomies
			'taxonomy_autocomplete' => array(
				'title'    => __( 'Add Taxonomy on import', WPML_SLUG ),
				'description' => __( 'Automatically add custom taxonomies when adding/importing movies. If enabled, each added/imported movie will be automatically added to the collection corresponding to the director. Actors and Genres tags will be filled automatically as well. Unexisting Taxonomies will be created. Ex: adding the movie <em>Fight Club</em> will add the movie to a "David Fincher" collection and the movie will be tagged with tags like "Edward Norton", "Brad Pitt", "Drama"...', WPML_SLUG ),
				'type'     => 'toggle',
				'default' => 1
			),
		),
	),

	// What to do on deactivation
	'deactivate' => array(
		'section' => array(
			'id'       => 'deactivate',
			'title'    => __( 'Deactivate', WPML_SLUG ),
		),
		'settings' => array(
			'movies' => array(
				'title' => __( 'Movie Post Type', WPML_SLUG ),
				'description' => __( 'How to handle Movies when WPML is deactivated.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve (recommended)', WPML_SLUG ),
					'convert' => __( 'Convert to Posts', WPML_SLUG ),
					'remove' => __( 'Delete (irreversible)', WPML_SLUG ),
					'delete' => __( 'Delete Completely (irreversible)', WPML_SLUG ),
				),
				'default' => 'conserve'
			),

			'collections' => array(
				'title' => __( 'Collections Taxonomy', WPML_SLUG ),
				'description' => __( 'How to handle Collections Taxonomy when WPML is deactivated.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve (recommended)', WPML_SLUG ),
					'convert' => __( 'Convert to Categories', WPML_SLUG ),
					'delete' => __( 'Delete (irreversible)', WPML_SLUG ),
				),
				'default' => 'conserve'
			),

			'genres'      => array(
				'title' => __( 'Genres Taxonomy', WPML_SLUG ),
				'description' => __( 'How to handle Genres Taxonomy when WPML is deactivated.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve (recommended)', WPML_SLUG ),
					'convert' => __( 'Convert to Tags', WPML_SLUG ),
					'delete' => __( 'Delete (irreversible)', WPML_SLUG ),
				),
				'default' => 'conserve'
			),

			'actors'      => array(
				'title' => __( 'Actors Taxonomy', WPML_SLUG ),
				'description' => __( 'How to handle Actors Taxonomy when WPML is deactivated.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve (recommended)', WPML_SLUG ),
					'convert' => __( 'Convert to Tags', WPML_SLUG ),
					'delete' => __( 'Delete (irreversible)', WPML_SLUG ),
				),
				'default' => 'conserve'
			),

			'cache'       => array(
				'title' => __( 'Cache', WPML_SLUG ),
				'description' => __( 'How to handle Cached data when WPML is deactivated.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', WPML_SLUG ),
					'empty' => __( 'Empty (recommended)', WPML_SLUG ),
				),
				'default' => 'empty'
			)
		)
	),

	// What to do on uninstallation
	'uninstall' => array(
		'section' => array(
			'id'       => 'uninstall',
			'title'    => __( 'Uninstall', WPML_SLUG ),
		),
		'settings' => array(
			'movies'      => array(
				'title' => __( 'Movie Post Type', WPML_SLUG ),
				'description' => __( 'How to handle Movies when WPML is uninstalled.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', WPML_SLUG ),
					'convert' => __( 'Convert to Posts (recommended)', WPML_SLUG ),
					'delete' => __( 'Delete Completely (irreversible)', WPML_SLUG ),
				),
				'default' => 'convert'
			),

			'collections' => array(
				'title' => __( 'Collections Taxonomy', WPML_SLUG ),
				'description' => __( 'How to handle Collections Taxonomy when WPML is uninstalled.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', WPML_SLUG ),
					'convert' => __( 'Convert to Categories (recommended)', WPML_SLUG ),
					'delete' => __( 'Delete (irreversible)', WPML_SLUG ),
				),
				'default' => 'convert'
			),

			'genres'      => array(
				'title' => __( 'Genres Taxonomy', WPML_SLUG ),
				'description' => __( 'How to handle Genres Taxonomy when WPML is uninstalled.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', WPML_SLUG ),
					'convert' => __( 'Convert to Tags (recommended)', WPML_SLUG ),
					'delete' => __( 'Delete (irreversible)', WPML_SLUG ),
				),
				'default' => 'convert'
			),

			'actors'      => array(
				'title' => __( 'Actors Taxonomy', WPML_SLUG ),
				'description' => __( 'How to handle Actors Taxonomy when WPML is uninstalled.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', WPML_SLUG ),
					'convert' => __( 'Convert to Tags (recommended)', WPML_SLUG ),
					'delete' => __( 'Delete (irreversible)', WPML_SLUG ),
				),
				'default' => 'convert'
			),

			'cache'       => array(
				'title' => __( 'Cache', WPML_SLUG ),
				'description' => __( 'How to handle Cached data when WPML is uninstalled.', WPML_SLUG ),
				'type' => 'select',
				'values' => array(
					'conserve' => __( 'Conserve', WPML_SLUG ),
					'empty' => __( 'Empty (recommended)', WPML_SLUG ),
				),
				'default' => 'empty'
			)
		)
	)
);

$wpml_movie_details = array(
	'movie_media'   => array(
		'title' => __( 'Media', WPML_SLUG ),
		'options' => array(
			'dvd'     => __( 'DVD', WPML_SLUG ),
			'bluray'  => __( 'BluRay', WPML_SLUG ),
			'vod'     => __( 'VOD', WPML_SLUG ),
			'vhs'     => __( 'VHS', WPML_SLUG ),
			'cinema'  => __( 'Cinema', WPML_SLUG ),
			'other'   => __( 'Other', WPML_SLUG ),
		),
		'default' => array(
			'dvd'   => __( 'DVD', WPML_SLUG ),
		),
	),
	'movie_status'  => array(
		'title' => __( 'Status', WPML_SLUG ),
		'options' => array(
			'available' => __( 'Available', WPML_SLUG ),
			'loaned'    => __( 'Loaned', WPML_SLUG ),
			'scheduled' => __( 'Scheduled', WPML_SLUG ),
		),
		'default' => array(
			'available' => __( 'Available', WPML_SLUG ),
		)
	),
	'movie_rating'  => array(
		'title' => __( 'Rating', WPML_SLUG ),
		'options' => array(
			'0.5' => __( 'Junk', WPML_SLUG ),
			'1.0' => __( 'Very bad', WPML_SLUG ),
			'1.5' => __( 'Bad', WPML_SLUG ),
			'2.0' => __( 'Not that bad', WPML_SLUG ),
			'2.5' => __( 'Average', WPML_SLUG ),
			'3.0' => __( 'Not bad', WPML_SLUG ),
			'3.5' => __( 'Good', WPML_SLUG ),
			'4.0' => __( 'Very good', WPML_SLUG ),
			'4.5' => __( 'Excellent', WPML_SLUG ),
			'5.0' => __( 'Masterpiece', WPML_SLUG )
		),
		'default' => array(
			'0.0' => '',
		)
	)
);

$wpml_movie_meta = array(
	'meta' => array(
		'type' => __( 'Type', WPML_SLUG ),
		'value' => __( 'Value', WPML_SLUG ),
		'data' => array(
			'title' => array(
				'title' => __( 'Title', WPML_SLUG ),
				'type' => 'text'
			),
			'original_title' => array(
				'title' => __( 'Original Title', WPML_SLUG ),
				'type' => 'text'
			),
			'overview' => array(
				'title' => __( 'Overview', WPML_SLUG ),
				'type' => 'textarea'
			),
			'production_companies' => array(
				'title' => __( 'Production', WPML_SLUG ),
				'type' => 'text'
			),
			'production_countries' => array(
				'title' => __( 'Country', WPML_SLUG ),
				'type' => 'text'
			),
			'spoken_languages' => array(
				'title' => __( 'Languages', WPML_SLUG ),
				'type' => 'text'
			),
			'runtime' => array(
				'title' => __( 'Runtime', WPML_SLUG ),
				'type' => 'text'
			),
			'genres' => array(
				'title' => __( 'Genres', WPML_SLUG ),
				'type' => 'text'
			),
			'release_date' => array(
				'title' => __( 'Release Date', WPML_SLUG ),
				'type' => 'text'
			)
		)
	),
	'crew' => array(
		'type' => __( 'Job', WPML_SLUG ),
		'value' => __( 'Name(s)', WPML_SLUG ),
		'data' => array(
			'director' => array(
				'title' => __( 'Director', WPML_SLUG ),
				'type' => 'text'
			),
			'producer' => array(
				'title' => __( 'Producer', WPML_SLUG ),
				'type' => 'text'
			),
			'photography' => array(
				'title' => __( 'Director of Photography', WPML_SLUG ),
				'type' => 'text'
			),
			'composer' => array(
				'title' => __( 'Original Music Composer', WPML_SLUG ),
				'type' => 'text'
			),
			'author' => array(
				'title' => __( 'Author', WPML_SLUG ),
				'type' => 'text'
			),
			'writer' => array(
				'title' => __( 'Writer', WPML_SLUG ),
				'type' => 'text'
			),
			'cast' => array(
				'title' => __( 'Actors', WPML_SLUG ),
				'type' => 'textarea'
			)
		)
	)
);