<?php
/**
 * WPMovieLibrary Config Settings definition
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

$wpmoly_config = array(

	// 'wpmoly' General settings section
	array(
		'icon'    => 'wpmolicon icon-cogs',
		'title'   => __( 'General', 'wpmovielibrary' ),
		'heading' => __( 'General options', 'wpmovielibrary' ),
		'fields'  => array(
		)
	),

	// 'wpmoly-movies' Movies settings subsection
	array(
		'icon'    => 'wpmolicon icon-movie',
		'title'   => __( 'Movies', 'wpmovielibrary' ),
		'desc'    => __( 'WPMovieLibrary handles movies as regular WordPress posts, but you can define some specific behaviours movies only should have.', 'wpmovielibrary'),
		'subsection' => true,
		'fields'  => array(

			// Add movies to the main loop
			'frontpage' => array(
				'id'       => 'wpmoly-frontpage',
				'type'     => 'switch',
				'title'    => __( 'Show Movies in Home Page', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, movies will appear among other Posts in the Home Page.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Add movies to the main loop
			'search' => array(
				'id'       => 'wpmoly-search',
				'type'     => 'switch',
				'title'    => __( 'Movies in search results', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, the standard WordPress Search will return every movie matching the search in addition to regular posts. Search will include all available meta fields. Examples: a search with keywork <code>Sean Penn</code> will add the movies <em>Into The Wild</em> and <em>The Secret Life of Walter Mitty</em> to the search results; a search with keywork <code>Taiwan</code> will add the movie <em>Life of Pi</em>.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
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
			)
		)
	),

	// 'wpmoly-meta' Meta settings subsection
	array(
		'icon'    => 'wpmolicon icon-meta',
		'title'   => __( 'Metadata', 'wpmovielibrary' ),
		'heading' => __( 'Metadata settings', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

			'meta-start' => array(
				'id'       => 'meta-start',
				'type'     => 'section',
				'title'    => __( 'Movies metadata', 'wpmovielibrary'),
				'subtitle' => __( 'Metadata give you useful information about your movies: director, release date, runtime, prouction, actors, languages…', 'wpmovielibrary'),
				'indent'   => true
			),

			// Show movie meta in posts
			'show-meta' => array(
				'id'       => 'wpmoly-show-meta',
				'type'     => 'select',
				'title'    => __( 'Show metadata', 'wpmovielibrary' ),
				'desc'     => __( 'Add metadata to posts&rsquo; content: director, genres, runtime…', 'wpmovielibrary' ),
				'options'  => array(
					'everywhere' => __( 'Everywhere', 'wpmovielibrary' ),
					'posts_only' => __( 'Only In Post Read', 'wpmovielibrary' ),
					'nowhere'    => __( 'Don&rsquo;t Show', 'wpmovielibrary' ),
				),
				'default'  => 'posts_only'
			),

			'meta-links' => array(
				'id'       => 'wpmoly-meta-links',
				'type'     => 'select',
				'title'    => __( 'Add links to meta', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, metadata will appear as links to meta pages.', 'wpmovielibrary' ),
				'options'  => array(
					'everywhere' => __( 'Everywhere', 'wpmovielibrary' ),
					'posts_only' => __( 'Only In Post Read', 'wpmovielibrary' ),
					'nowhere'    => __( 'Don&rsquo;t Show', 'wpmovielibrary' ),
				),
				'default'  => 'posts_only',
				'required' => array( 'wpmoly-show-meta', "!=", 'nowhere' ),
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
						'release_date'         => __( 'Local release date', 'wpmovielibrary' ),
						'photography'          => __( 'Director of Photography', 'wpmovielibrary' ),
						'composer'             => __( 'Original Music Composer', 'wpmovielibrary' ),
						'author'               => __( 'Author', 'wpmovielibrary' ),
						'writer'               => __( 'Writer', 'wpmovielibrary' ),
						'cast'                 => __( 'Actors', 'wpmovielibrary' ),
						'certification'        => __( 'Certification', 'wpmovielibrary' ),
						'budget'               => __( 'Budget', 'wpmovielibrary' ),
						'revenue'              => __( 'Revenue', 'wpmovielibrary' ),
						'tagline'              => __( 'Tagline', 'wpmovielibrary' ),
						'imdb_id'              => __( 'IMDb Id', 'wpmovielibrary' ),
						'adult'                => __( 'Adult', 'wpmovielibrary' ),
						'homepage'             => __( 'Homepage', 'wpmovielibrary' )
					)
				),
				'required' => array( 'wpmoly-show-meta', "!=", 'nowhere' ),
				'indent'   => true
			),

			'meta-end' => array(
				'id'     => 'meta-end',
				'type'   => 'section',
				'indent' => false,
			)
		)
	),

	// 'wpmoly-details' Details settings subsection
	array(
		'icon'    => 'wpmolicon icon-details',
		'title'   => __( 'Details', 'wpmovielibrary' ),
		'heading' => __( 'Details settings', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

			'details-start' => array(
				'id'       => 'details-start',
				'type'     => 'section',
				'title'    => __( 'Movie details', 'wpmovielibrary'),
				'subtitle' => __( 'Details are a different way to manage your movies. You can specify and filter movies by rating, media, status, language, subtitles…', 'wpmovielibrary'),
				'indent'   => true
			),

			// Show movie details in posts
			'show-details' => array(
				'id'       => 'wpmoly-show-details',
				'type'     => 'select',
				'title'    => __( 'Show details', 'wpmovielibrary' ),
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
				'title'    => __( 'Details as icons', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, movie details will appear in the form of icons rather than default colored labels.', 'wpmovielibrary' ),
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
						'media'  => __( 'Media', 'wpmovielibrary' ),
						'status' => __( 'Status', 'wpmovielibrary' ),
						'rating' => __( 'Rating', 'wpmovielibrary' )
					),
					'available'  => array(
						'language'  => __( 'Language', 'wpmovielibrary' ),
						'subtitles' => __( 'Subtitles', 'wpmovielibrary' ),
						'format'    => __( 'Format', 'wpmovielibrary' )
					)
				),
				'required' => array( 'wpmoly-show-details', "!=", 'nowhere' ),
				'indent'   => true
			),

			'details-end' => array(
				'id'     => 'details-end',
				'type'   => 'section',
				'indent' => false,
			),
		)
	),

	// 'wpmoly-format' Formatting settings subsection
	array(
		'icon'    => 'wpmolicon icon-format',
		'title'   => __( 'Formatting', 'wpmovielibrary' ),
		'heading' => __( 'Formatting settings', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

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

			// Release date formatting
			'format-rating' => array(
				'id'       => 'wpmoly-format-rating',
				'type'     => 'select',
				'title'    => __( 'Rating format', 'wpmovielibrary' ),
				'desc'     => __( 'Should ratings be displayed using 5 or 10 stars.', 'wpmovielibrary' ),
				'options'  => array(
					'5'  => __( '5 stars', 'wpmovielibrary' ),
					'10' => __( '10 stars', 'wpmovielibrary' )
				),
				'default'  => '5'
			),
		),
	),

	// 'wpmoly-converting' Formatting settings subsection
	array(
		'icon'    => 'wpmolicon icon-import',
		'title'   => __( 'Converting', 'wpmovielibrary' ),
		'heading' => __( 'Converting settings', 'wpmovielibrary' ),
		'desc'    => __( 'This section allows you to configure the post types convertor tool. This can be usefull to convert regular posts, pages and possibly other custom post types into movies to avoid duplicate contents or having to manually recreate already existing contents. Note that this will most likely affect your SEO as it will change Posts’ URLs.', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

			// Notice
			array(
                            'id'     => 'wpmoly-convert-notice',
                            'type'   => 'info',
                            'notice' => true,
                            'style'  => 'critical',
                            'icon'   => 'wpmolicon icon-warning',
                            'title'  => __( 'Experimental', 'wpmovielibrary' ),
                            'desc'   => __( 'Posts to Movies conversion is still experimental. Do not activate this unless you are sure of what you are doing, and make sure you have backups of your database before using this feature.', 'wpmovielibrary' )
                        ),

			// Post type convert enable
			'convert-enable' => array(
				'id'       => 'wpmoly-convert-enable',
				'type'     => 'switch',
				'title'    => __( 'Convert Post Types', 'wpmovielibrary' ),
				'desc'     => __( 'Enable post types conversion tools.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),

			// Post type to convert
			'convert-post-types' => array(
				'id'       => 'wpmoly-convert-post-types',
				'type'     => 'select',
				'title'    => __( 'Post Types available to convert', 'wpmovielibrary' ),
				'desc'     => __( 'Select which post types should be convertible to movie.', 'wpmovielibrary' ),
				'data'     => 'post_types',
				'multi'    => true,
				'required' => array( 'wpmoly-convert-enable', "=", '1' ),
				'default'  => array( 'post', 'page', 'review' )
			),
		),
	),

	// 'wpmoly-images' Images and Posters section
	array(
		'icon'    => 'wpmolicon icon-image',
		'title'   => __( 'Images', 'wpmovielibrary' ),
		'heading' => __( 'Images and Posters options', 'wpmovielibrary' ),
		'fields'  => array(
		)
	),

	// 'wpmoly-posters' Posters settings subsection
	array(
		'icon'    => 'wpmolicon icon-poster',
		'title'   => __( 'Posters', 'wpmovielibrary' ),
		'heading' => __( 'Posters settings', 'wpmovielibrary' ),
		'subsection' => true,
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
				'default'  => 'original'
			),

			// Delete posters when deleting movies
			'posters-delete' => array(
				'id'       => 'wpmoly-posters-delete',
				'type'     => 'switch',
				'title'    => __( 'Delete posters with movies', 'wpmovielibrary' ),
				'desc'     => __( 'Enable this if you want to delete posters along with movies.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),

			// Poster attachment title
			'poster-title' => array(
				'id'       => 'wpmoly-poster-title',
				'type'     => 'text',
				'title'    => __( 'Posters image title', 'wpmovielibrary' ),
				'desc'     => __( 'Title for the imported posters images.', 'wpmovielibrary' ),
				'validate' => 'no_html',
				'default'  => sprintf( '%s "{title}"', __( 'Poster for the movie', 'wpmovielibrary' ) )
			),

			// Poster attachment description
			'poster-description' => array(
				'id'       => 'wpmoly-poster-description',
				'type'     => 'text',
				'title'    => __( 'Posters image description', 'wpmovielibrary' ),
				'desc'     => __( 'Description text for the imported posters images.', 'wpmovielibrary' ),
				'validate' => 'no_html',
				'default'  => sprintf( '© {year} {production} − %s', __( 'All right reserved.', 'wpmovielibrary' ) )
			)
		)
	),

	// 'wpmoly-images' Images settings subsection
	array(
		'icon'    => 'wpmolicon icon-images',
		'title'   => __( 'Images', 'wpmovielibrary' ),
		'heading' => __( 'Images settings', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

			// Images size
			'images-size' => array(
				'id'       => 'wpmoly-images-size',
				'type'     => 'select',
				'title'    => __( 'Images Default Size', 'wpmovielibrary' ),
				'desc'     => __( 'Movie Image size. Default is TMDb&rsquo;s original size.', 'wpmovielibrary' ),
				'options'  => array(
					'small'    => __( 'Small (~300px)', 'wpmovielibrary' ),
					'medium'   => __( 'Medium (~780px)', 'wpmovielibrary' ),
					'full'     => __( 'Full (~1280px) ', 'wpmovielibrary' ),
					'original' => __( 'Original', 'wpmovielibrary' )
				),
				'default'  =>'original'
			),

			// Maximum number of image to show
			'images-delete' => array(
				'id'       => 'wpmoly-images-delete',
				'type'     => 'switch',
				'title'    => __( 'Delete images with movies', 'wpmovielibrary' ),
				'desc'     => __( 'Enable this if you want to delete all imported images along with movies. Handy if you have a great number of movies to delete and possibly dozens of images attached.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),

			// Image attachment title
			'image-title' => array(
				'id'       => 'wpmoly-image-title',
				'type'     => 'text',
				'title'    => __( 'Images title', 'wpmovielibrary' ),
				'desc'     => __( 'Title for the imported movie images.', 'wpmovielibrary' ),
				'validate' => 'no_html',
				'default'  => sprintf( '%s "{title}"', __( 'Image from the movie', 'wpmovielibrary' ) )
			),

			// Image attachment description
			'image-description' => array(
				'id'       => 'wpmoly-image-description',
				'type'     => 'text',
				'title'    => __( 'Images description', 'wpmovielibrary' ),
				'desc'     => __( 'Description text for the imported movie images.', 'wpmovielibrary' ),
				'validate' => 'no_html',
				'default'  => sprintf( '© {year} {production} − %s', __( 'All right reserved.', 'wpmovielibrary' ) )
			)
		),
	),

	// 'wpmoly-taxonomies' Taxonomies section
	array(
		'icon'    => 'wpmolicon icon-tags',
		'title'   => __( 'Taxonomies', 'wpmovielibrary' ),
		'heading' => __( 'Built-in Taxonomies configuration', 'wpmovielibrary' ),
		'fields'  => array(
		)
	),

	// 'wpmoly-taxonomies' general settings subsection
	array(
		'icon'    => 'wpmolicon icon-folder',
		'title'   => __( 'General', 'wpmovielibrary' ),
		'heading' => __( 'General settings', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

			// Notice
			array(
                            'id'     => 'wpmoly-taxonomies-notice',
                            'type'   => 'info',
                            'notice' => true,
                            'style'  => 'critical',
                            'icon'   => 'wpmolicon icon-warning',
                            'title'  => __( 'Experimental', 'wpmovielibrary' ),
                            'desc'   => __( 'Enabling Categories and Post tags for movies will result in your movies appearing in Categories and Post Tags archive pages, among regular WordPress Posts. This could also interfer with other plugins/themes dealing with Categories/Post Tags. Use it carefully.', 'wpmovielibrary' )
                        ),

			// Enable Categories
			'enable-categories' => array(
				'id'       => 'wpmoly-enable-categories',
				'type'     => 'switch',
				'title'    => __( 'Enable Categories', 'wpmovielibrary' ),
				'description' => __( 'Allow movies to use regular WordPress Categories.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),

			// Enable Post Tags
			'enable-tags' => array(
				'id'       => 'wpmoly-enable-tags',
				'type'     => 'switch',
				'title'    => __( 'Enable Post Tags', 'wpmovielibrary' ),
				'description' => __( 'Allow movies to use regular WordPress Post Tags.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			)
		)
	),

	// 'wpmoly-collections' collections settings subsection
	array(
		'icon'    => 'wpmolicon icon-collection',
		'title'   => __( 'Collections', 'wpmovielibrary' ),
		'heading' => __( 'Collections settings', 'wpmovielibrary' ),
		'subsection' => true,
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

			// Enable Collections for regular WordPress Posts
			'collection-posts' => array(
				'id'       => 'wpmoly-collection-posts',
				'type'     => 'switch',
				'title'    => __( 'Posts Collections support', 'wpmovielibrary' ),
				'desc'     => __( '<strong>Experimental</strong>: if enabled, allow regular WordPress Posts to use collection taxonomy.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0,
				'required' => array( 'wpmoly-enable-collection', "=", 1 ),
				'indent'   => true
			),

			// Collections Post Types
			'collection-post-types' => array(
				'id'       => 'wpmoly-collection-post-types',
				'type'     => 'select',
				'title'    => __( 'Collections Post Types support', 'wpmovielibrary' ),
				'desc'     => __( 'Select which post types should support collections.', 'wpmovielibrary' ),
				'data'     => 'post_types',
				'multi'    => true,
				'required' => array( 'wpmoly-collection-posts', "=", '1' ),
				'default'  => array( 'post', 'page' ),
				'indent'   => true
			)
		)
	),

	// 'wpmoly-genres' Genres settings subsection
	array(
		'icon'    => 'wpmolicon icon-tag',
		'title'   => __( 'Genres', 'wpmovielibrary' ),
		'heading' => __( 'Genres settings', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

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

			// Enable Genres for regular WordPress Posts
			'genre-posts' => array(
				'id'       => 'wpmoly-genre-posts',
				'type'     => 'switch',
				'title'    => __( 'Posts Genres support', 'wpmovielibrary' ),
				'desc'     => __( '<strong>Experimental</strong>: if enabled, allow regular WordPress Posts to use genre taxonomy.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0,
				'required' => array( 'wpmoly-enable-genre', "=", 1 ),
				'indent'   => true
			),

			// Genres Post Types
			'genre-post-types' => array(
				'id'       => 'wpmoly-genre-post-types',
				'type'     => 'select',
				'title'    => __( 'Genres Post Types support', 'wpmovielibrary' ),
				'desc'     => __( 'Select which post types should support genres.', 'wpmovielibrary' ),
				'data'     => 'post_types',
				'multi'    => true,
				'required' => array( 'wpmoly-genre-posts', "=", '1' ),
				'default'  => array( 'post', 'page' ),
				'indent'   => true
			)
		)
	),

	// 'wpmoly-actors' Actors settings subsection
	array(
		'icon'    => 'wpmolicon icon-actor',
		'title'   => __( 'Actors', 'wpmovielibrary' ),
		'heading' => __( 'Actors settings', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

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

			// Enable Actors for regular WordPress Posts
			'actor-posts' => array(
				'id'       => 'wpmoly-actor-posts',
				'type'     => 'switch',
				'title'    => __( 'Posts Actors support', 'wpmovielibrary' ),
				'desc'     => __( '<strong>Experimental</strong>: if enabled, allow regular WordPress Posts to use actor taxonomy.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0,
				'required' => array( 'wpmoly-enable-actor', "=", 1 ),
				'indent'   => true
			),

			// Actors Post Types
			'actor-post-types' => array(
				'id'       => 'wpmoly-actor-post-types',
				'type'     => 'select',
				'title'    => __( 'Actors Post Types support', 'wpmovielibrary' ),
				'desc'     => __( 'Select which post types should support actors.', 'wpmovielibrary' ),
				'data'     => 'post_types',
				'multi'    => true,
				'required' => array( 'wpmoly-actor-posts', "=", '1' ),
				'default'  => array( 'post', 'page' ),
				'indent'   => true
			)
		),
	),

	// 'wpmoly-archives' Archives Pages section
	array(
		'icon'    => 'wpmolicon icon-archive',
		'title'   => __( 'Archives', 'wpmovielibrary' ),
		'heading' => __( 'Archives Pages Settings', 'wpmovielibrary' ),
		'fields'  => array(
		)
	),

	// 'wpmoly-movie-archives' Movie Archives settings subsection
	array(
		'icon'    => 'wpmolicon icon-movie',
		'title'   => __( 'Movie Archives', 'wpmovielibrary' ),
		'heading' => __( 'Movie Archives page settings', 'wpmovielibrary' ),
		'desc'    => __( 'This section allow you to define a custom page to use for movie archives.', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

			// Notice
			/*array(
                            'id'     => 'wpmoly-archives-notice',
                            'type'   => 'info',
                            'notice' => true,
                            'style'  => 'info',
                            'icon'   => 'wpmolicon icon-info',
                            'title'  => __( 'Permalinks Required', 'wpmovielibrary' ),
                            'desc'   => __( 'Custom Archives Pages require Permalinks to be activated; using the default permalink structure will prevent archives to work properly. Ignore this notice if you are already using custom permalinks.', 'wpmovielibrary' )
                        ),*/

			// Movie archives page
			'movie-archives' => array(
				'id'       => 'wpmoly-movie-archives',
				'type'     => 'select',
				'title'    => __( 'Movie Archives Page', 'wpmovielibrary' ),
				'desc'     => __( 'Choose a page to use to display movie archives.', 'wpmovielibrary' ),
				'data'     => 'pages',
				'validate_callback' => 'WPMOLY_Utils::permalinks_changed',
				'default'  => ''
			),

			// Archives Position
			'movie-archives-position' => array(
				'id'       => 'wpmoly-movie-archives-position',
				'type'     => 'select',
				'title'    => __( 'Archives Position', 'wpmovielibrary' ),
				'desc'     => __( 'Where to display the Archives on the page.', 'wpmovielibrary' ),
				'options'  => array(
					'top'    => __( 'Top', 'wpmovielibrary' ),
					'bottom' => __( 'Bottom', 'wpmovielibrary' )
				),
				'default'  => 'top',
			),

			// Movie archives page title rewrite
			'movie-archives-title-rewrite' => array(
				'id'       => 'wpmoly-movie-archives-title-rewrite',
				'type'     => 'switch',
				'title'    => __( 'Rewrite Movie Archives Page Titles', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, movie archives page’s title and post title will be rewritten to feature currently browsed metas, details, and letters.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Movie archives page menu
			'menu' => array(
				'id'       => 'wpmoly-movie-archives-menu',
				'type'     => 'switch',
				'title'    => __( 'Archives Menu', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, add an alphabetical menu and sorting options before the movies list.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Movie archives page grid columns
			'grid-columns' => array(
				'id'       => 'wpmoly-movie-archives-grid-columns',
				'type'     => 'slider',
				'title'    => __( 'Grid Columns', 'wpmovielibrary' ),
				'desc'     => __( 'How many columns should the movie grid have.', 'wpmovielibrary' ),
				'min'      => 1,
				'step'     => 1,
				'max'      => 8,
				'display_value' => 'text',
				'default'  => 4
			),

			// Movie archives page grid rows
			'grid-rows' => array(
				'id'       => 'wpmoly-movie-archives-grid-rows',
				'type'     => 'slider',
				'title'    => __( 'Grid Rows', 'wpmovielibrary' ),
				'desc'     => __( 'How many rows should the movie grid have.', 'wpmovielibrary' ),
				'min'      => 1,
				'step'     => 1,
				'max'      => 12,
				'display_value' => 'text',
				'default'  => 6
			),

			// Movie archives page grid default sorting order
			'movies-order' => array(
				'id'       => 'wpmoly-movie-archives-movies-order',
				'type'     => 'button_set',
				'title'    => __( 'Movies order', 'wpmovielibrary' ),
				'desc'     => __( 'How movies should be ordered by default.', 'wpmovielibrary' ),
				'options'  => array(
					'ASC'  => __( 'Ascending' ),
					'DESC' => __( 'Descending' ),
				),
				'default'  => 'ASC'
			),

			// Movie archives page grid default sorting order
			'movies-orderby' => array(
				'id'       => 'wpmoly-movie-archives-movies-orderby',
				'type'     => 'button_set',
				'title'    => __( 'Movies Sorting', 'wpmovielibrary' ),
				'desc'     => __( 'Default movies sorting.', 'wpmovielibrary' ),
				'options'  => array(
					'title'     => __( 'Title', 'wpmovielibrary' ),
					'date'      => __( 'Release Date', 'wpmovielibrary' ),
					'localdate' => __( 'Local Release Date', 'wpmovielibrary' ),
					'rating'    => __( 'Rating', 'wpmovielibrary' ),
				),
				'default'  => 'title'
			),

			// Movie archives page max number of movies per page
			'movies-limit' => array(
				'id'       => 'wpmoly-movie-archives-movies-limit',
				'type'     => 'text',
				'title'    => __( 'Movies per page limit', 'wpmovielibrary' ),
				'desc'     => __( 'Limit the number of movies per page to be listed. Can be useful if your dealing with massive numbers of movies.', 'wpmovielibrary' ),
				'validate' => 'numeric',
				'default'  => 99
			),

			'movies-meta' => array(
				'id'       => 'wpmoly-movie-archives-movies-meta',
				'type'     => 'sorter',
				'title'    => __( 'Grid Movies Meta', 'wpmovielibrary' ),
				'desc'     => __( 'You can show some metadata along with posters in the grid.', 'wpmovielibrary' ),
				'compiler' => 'true',
				'options'  => array(
					'used' => array(),
					'available' => array(
						'title'  => __( 'Title', 'wpmovielibrary' ),
						'year'   => __( 'Year', 'wpmovielibrary' ),
						'rating' => __( 'Rating', 'wpmovielibrary' )
					)
				)
                        ),

			// Movie archives page frontend edit inputs
			'frontend-edit' => array(
				'id'       => 'wpmoly-movie-archives-frontend-edit',
				'type'     => 'switch',
				'title'    => __( 'Editable movies-per-page value', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, allows movies-per-page value to be modified on frontend. The sorting menu will show an input where visitors can change the movies-per-page value to display more or less movies. It is recommended to set a limit above if this feature is to be activated.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),

			// Movie archives page frontend advanced edit settings
			'frontend-advanced-edit' => array(
				'id'       => 'wpmoly-movie-archives-frontend-advanced-edit',
				'type'     => 'switch',
				'title'    => __( 'Advanced Editable grid settings', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, allows the grid sorting to be modified on frontend. The sorting menu will show a list of possible sortings (rating, release date, title, …) that visitors can select to sort the grid. <strong>This feature is experimental!</strong> Be sure to test it thoroughly before enabling it publicly.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),
		)
	),

	// 'wpmoly-movie-archives' Movie Archives settings subsection
	array(
		'icon'    => 'wpmolicon icon-tags',
		'title'   => __( 'Taxonomy Archives', 'wpmovielibrary' ),
		'heading' => __( 'Taxonomy Archives page settings', 'wpmovielibrary' ),
		'desc'    => __( 'This section allow you to define a custom page to use for taxonomy archives.', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

			// Collection archives page
			'collection-archives' => array(
				'id'       => 'wpmoly-collection-archives',
				'type'     => 'select',
				'title'    => __( 'Collection Archives Page', 'wpmovielibrary' ),
				'desc'     => __( 'Choose a page to use to display collection archives.', 'wpmovielibrary' ),
				'data'     => 'pages',
				'validate_callback' => 'WPMOLY_Utils::permalinks_changed',
				'default'  => '',
				'required' => array( 'wpmoly-enable-collection', "=", 1 )
			),

			// Genre archives page
			'genre-archives' => array(
				'id'       => 'wpmoly-genre-archives',
				'type'     => 'select',
				'title'    => __( 'Genre Archives Page', 'wpmovielibrary' ),
				'desc'     => __( 'Choose a page to use to display genre archives.', 'wpmovielibrary' ),
				'data'     => 'pages',
				'validate_callback' => 'WPMOLY_Utils::permalinks_changed',
				'default'  => '',
				'required' => array( 'wpmoly-enable-genre', "=", 1 )
			),

			// Actor archives page
			'actor-archives' => array(
				'id'       => 'wpmoly-actor-archives',
				'type'     => 'select',
				'title'    => __( 'Actor Archives Page', 'wpmovielibrary' ),
				'desc'     => __( 'Choose a page to use to display actor archives.', 'wpmovielibrary' ),
				'data'     => 'pages',
				'validate_callback' => 'WPMOLY_Utils::permalinks_changed',
				'default'  => '',
				'required' => array( 'wpmoly-enable-actor', "=", 1 )
			),

			// Archives Position
			'archives-position' => array(
				'id'       => 'wpmoly-tax-archives-position',
				'type'     => 'select',
				'title'    => __( 'Archives Position', 'wpmovielibrary' ),
				'desc'     => __( 'Where to display the Archives on the page.', 'wpmovielibrary' ),
				'options'  => array(
					'top'    => __( 'Top', 'wpmovielibrary' ),
					'bottom' => __( 'Bottom', 'wpmovielibrary' )
				),
				'default'  => 'top',
			),

			// Movie archives page title rewrite
			'archives-title-rewrite' => array(
				'id'       => 'wpmoly-tax-archives-title-rewrite',
				'type'     => 'switch',
				'title'    => __( 'Rewrite Archives Page Titles', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, taxonomy archives page’s title and post title will be rewritten to feature currently browsed letter.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Taxonomy archives page menu
			'archives-menu' => array(
				'id'       => 'wpmoly-tax-archives-menu',
				'type'     => 'switch',
				'title'    => __( 'Archives Menu', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, add an alphabetical menu and sorting options before the terms list.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Taxonomy archives page don't show empty terms
			'hide-empty' => array(
				'id'       => 'wpmoly-tax-archives-hide-empty',
				'type'     => 'switch',
				'title'    => __( 'Hide empty terms', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, terms related to no movie will be excluded from the list.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Taxonomy archives page default term order
			'terms-orderby' => array(
				'id'       => 'wpmoly-tax-archives-terms-orderby',
				'type'     => 'button_set',
				'title'    => __( 'Terms sort', 'wpmovielibrary' ),
				'desc'     => __( 'How terms should be sorted by default.', 'wpmovielibrary' ),
				'options'  => array(
					'count' => __( 'Movie count', 'wpmovielibrary' ),
					'title' => __( 'Title', 'wpmovielibrary' ),
				),
				'default'  => 'title'
			),

			// Taxonomy archives page sorting order
			'terms-order' => array(
				'id'       => 'wpmoly-tax-archives-terms-order',
				'type'     => 'button_set',
				'title'    => __( 'Terms order', 'wpmovielibrary' ),
				'desc'     => __( 'How terms should be ordered by default.', 'wpmovielibrary' ),
				'options'  => array(
					'ASC'  => __( 'Ascending' ),
					'DESC' => __( 'Descending' ),
				),
				'default'  => 'ASC'
			),

			// Taxonomy archives page number of terms per page
			'terms-per-page' => array(
				'id'       => 'wpmoly-tax-archives-terms-per-page',
				'type'     => 'text',
				'title'    => __( 'Terms per page', 'wpmovielibrary' ),
				'desc'     => __( 'How many terms should be listed per archive page.', 'wpmovielibrary' ),
				'validate' => 'numeric',
				'default'  => 50
			),

			// Taxonomy archives page max number of terms per page
			'terms-limit' => array(
				'id'       => 'wpmoly-tax-archives-terms-limit',
				'type'     => 'text',
				'title'    => __( 'Terms per page limit', 'wpmovielibrary' ),
				'desc'     => __( 'Limit the number of terms per page to be listed. Can be useful if your dealing with massive numbers of terms.', 'wpmovielibrary' ),
				'validate' => 'numeric',
				'default'  => 999
			),

			// Taxonomy archives page frontend inputs
			'frontend-edit' => array(
				'id'       => 'wpmoly-tax-archives-frontend-edit',
				'type'     => 'switch',
				'title'    => __( 'Editable terms-per-page value', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, allows terms-per-page value to be modified on frontend. The sorting menu will show an input where visitors can change the terms-per-page value to display more or less terms. It is recommended to set a limit above if this feature is to be activated.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),
		)
	),

	// 'wpmoly-translate' Languages
	array(
		'icon'    => 'wpmolicon icon-language',
		'title'   => __( 'Languages', 'wpmovielibrary' ),
		'heading' => __( 'Languages Support', 'wpmovielibrary' ),
		'fields'  => array(
		)
	),

	// 'wpmoly-translate' Translation settings subsection
	array(
		'icon'    => 'wpmolicon icon-flag',
		'title'   => __( 'Translation', 'wpmovielibrary' ),
		'heading' => __( 'Translation settings', 'wpmovielibrary' ),
		'subsection' => true,
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

			'countries-format' => array(
				'id'       => 'wpmoly-countries-format',
				'type'     => 'select',
				'multi'    => true,
				'sortable' => true,
				'title'    => __( 'Country names format', 'wpmovielibrary' ),
				'desc'     => sprintf( __( 'How production countries should be appear in your movies. Default is <code>Flag + translation</code> showing something like <code>%s</code>.', 'wpmovielibrary' ), sprintf( '<span class="flag flag-ir"></span> %s', __( 'Ireland', 'wpmovielibrary-iso' ) ) ),
				'options'  => array(
					'flag'        => __( 'Flag', 'wpmovielibrary' ),
					'original'    => __( 'Original', 'wpmovielibrary' ),
					'translated'  => __( 'Translation', 'wpmovielibrary' ),
					'ptranslated' => sprintf( '(%s)', __( 'Translation', 'wpmovielibrary' ) ),
					'poriginal'   => sprintf( '(%s)', __( 'Original', 'wpmovielibrary' ) )
				),
				'default'  => array(
					'flag',
					'translated'
				),
				'required' => array( 'wpmoly-translate-countries', "=", 1 ),
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

			'languages-format' => array(
				'id'       => 'wpmoly-languages-format',
				'type'     => 'select',
				'multi'    => true,
				'sortable' => true,
				'title'    => __( 'Languages names format', 'wpmovielibrary' ),
				'desc'     => __( 'How spoken languages should be appear in your movies. Default is translated.', 'wpmovielibrary' ),
				'options'  => array(
					'original'    => __( 'Original', 'wpmovielibrary' ),
					'translated'  => __( 'Translation', 'wpmovielibrary' ),
					'ptranslated' => sprintf( '(%s)', __( 'Translation', 'wpmovielibrary' ) ),
					'poriginal'   => sprintf( '(%s)', __( 'Original', 'wpmovielibrary' ) ),
					'atranslated' => __( 'Abbreviated translation', 'wpmovielibrary' ),
					'aoriginal'   => __( 'Abbreviated original', 'wpmovielibrary' )
				),
				'default'  => array(
					'translated'
				),
				'required' => array( 'wpmoly-translate-languages', "=", 1 ),
			),
		)
	),

	// 'wpmoly-rewrite' Permalinks settings subsection
	array(
		'icon'    => 'wpmolicon icon-link',
		'title'   => __( 'Permalinks', 'wpmovielibrary' ),
		'heading' => __( 'Rewrite rules & Permalinks', 'wpmovielibrary' ),
		'desc' => __( 'You can adapt the plugin’s permalinks to your local language.', 'wpmovielibrary'),
		'subsection' => true,
		'fields'  => array(

			// Movie URL Rewrite Rule
			'rewrite-enable' => array(
				'id'       => 'wpmoly-rewrite-enable',
				'type'     => 'switch',
				'title'    => __( 'Translate permalinks', 'wpmovielibrary' ),
				'desc'     => __( 'Although it can be very tempting to customize your URLs, <strong>beware</strong>: you probably shouldn\'t modify this more than once if your site relies on search engines; changing URLs too often will could badly affect your site’s referencing.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1,
				'indent'   => true
			),

			// Movie URL Rewrite Rule
			'rewrite-movie' => array(
				'id'       => 'wpmoly-rewrite-movie',
				'type'     => 'text',
				'title'    => __( 'Movies URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'URL Rewrite Rule to apply on movies. Default is <code>movies</code>, resulting in URL like <code>http://yourblog/movies/fight-club</code>. You can use this field to translate URLs to your language.', 'wpmovielibrary' ),
				'validate_callback' => 'WPMOLY_Utils::permalinks_changed',
				'default'  => 'movies',
				'required' => array( 'wpmoly-rewrite-enable', "=", 1 ),
				'indent'   => true
			),

			// Collections URL Rewrite Rule
			'rewrite-collection' => array(
				'id'       => 'wpmoly-rewrite-collection',
				'type'     => 'text',
				'title'    => __( 'Collections URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'URL Rewrite Rule to apply on collections. Default is <code>collection</code>, resulting in URL like <code>http://yourblog/collection/david-fincher</code>. You can use this field to translate URLs to your language.', 'wpmovielibrary' ),
				'validate_callback' => 'WPMOLY_Utils::permalinks_changed',
				'default'  => 'collection',
				'required' => array(
					array( 'wpmoly-rewrite-enable', "=", 1 ),
					array( 'wpmoly-enable-collection', "=", 1 )
				),
				'indent'   => true
			),

			// Genres URL Rewrite Rule
			'rewrite-genre' => array(
				'id'       => 'wpmoly-rewrite-genre',
				'type'     => 'text',
				'title'    => __( 'Genres URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'URL Rewrite Rule to apply on genres. Default is <code>genre</code>, resulting in URL like <code>http://yourblog/genre/thriller</code>. You can use this field to translate URLs to your language.', 'wpmovielibrary' ),
				'validate_callback' => 'WPMOLY_Utils::permalinks_changed',
				'default'  => 'genre',
				'required' => array(
					array( 'wpmoly-rewrite-enable', "=", 1 ),
					array( 'wpmoly-enable-genre', "=", 1 )
				),
				'indent'   => true
			),

			// Actors URL Rewrite Rule
			'rewrite-actor' => array(
				'id'       => 'wpmoly-rewrite-actor',
				'type'     => 'text',
				'title'    => __( 'Actors URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'URL Rewrite Rule to apply on actors. Default is <code>actor</code>, resulting in URL like <code>http://yourblog/actor/brad-pitt</code>. You can use this field to translate URLs to your language.', 'wpmovielibrary' ),
				'validate_callback' => 'WPMOLY_Utils::permalinks_changed',
				'default'  => 'actor',
				'required' => array(
					array( 'wpmoly-rewrite-enable', "=", 1 ),
					array( 'wpmoly-enable-actor', "=", 1 )
				),
				'indent'   => true
			),

			// Movie URL Rewrite Rule
			/*'rewrite-details' => array(
				'id'       => 'wpmoly-rewrite-details',
				'type'     => 'switch',
				'title'    => __( 'Movie Details URL Rewrite', 'wpmovielibrary' ),
				'desc'     => __( 'Use localized URLs for Movie Details. Enable this to have URLs like <code>http://yourblog/films/disponible</code> for French rather than the default <code>http://yourblog/movies/available</code>.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'validate_callback' => 'WPMOLY_Utils::permalinks_changed',
				'default'  => 0,
				'required' => array( 'wpmoly-rewrite-enable', "=", 1 ),
				'indent'   => true
			),*/

		)

	),

	// 'wpmoly-translate' Languages
	array(
		'icon'    => 'wpmolicon icon-style',
		'title'   => __( 'Appearance', 'wpmovielibrary' ),
		'heading' => __( 'Styling and customization', 'wpmovielibrary' ),
		'fields'  => array(
		)
	),

	// 'wpmoly-translate' Translation settings subsection
	array(
		'icon'    => 'wpmolicon icon-video-format',
		'title'   => __( 'Headbox', 'wpmovielibrary' ),
		'heading' => __( 'Styling the movie headbox', 'wpmovielibrary' ),
		'subsection' => true,
		'fields'  => array(

			// Replace excerpt by overview
			'headbox-enable' => array(
				'id'       => 'wpmoly-headbox-enable',
				'type'     => 'switch',
				'title'    => __( 'Enable Headbox', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, movies will use the Headbox introduced with version 2. Disable to show old metadata display from WPMovieLibrary 1.x instead.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1
			),

			// Headbox Position
			'headbox-position' => array(
				'id'       => 'wpmoly-headbox-position',
				'type'     => 'select',
				'title'    => __( 'Headbox Position', 'wpmovielibrary' ),
				'desc'     => __( 'Where to display the Headbox on posts.', 'wpmovielibrary' ),
				'options'  => array(
					'top'    => __( 'Top', 'wpmovielibrary' ),
					'bottom' => __( 'Bottom', 'wpmovielibrary' )
				),
				'default'  => 'top',
			),

			// Notice
			array(
                            'id'     => 'wpmoly-headbox-notice',
                            'type'   => 'info',
                            'notice' => true,
                            'style'  => 'critical',
                            'icon'   => 'wpmolicon icon-warning',
                            'title'  => __( 'Experimental', 'wpmovielibrary' ),
                            'desc'   => __( 'The Headbox Themes are still experimental: although they should work correctly they are not fully customisable and have spots for some unimplemented yet features such as actors images. Just so you know!', 'wpmovielibrary' )
                        ),

			// Headbox theme
			'headbox-theme' => array(
				'id'       => 'wpmoly-headbox-theme',
				'type'     => 'select',
				'title'    => __( 'Headbox Theme', 'wpmovielibrary' ),
				'desc'     => __( 'Select a Theme to use for your Headbox.', 'wpmovielibrary' ),
				'options'  => array(
					'wpmoly'   => __( 'WPMovieLibrary', 'wpmovielibrary' ),
					'imdb'     => __( 'IMDb', 'wpmovielibrary' ),
					'allocine' => __( 'Allociné', 'wpmovielibrary' ),
				),
				'default'  => 'wpmoly'
			),

			// Default Headbox Tabs
			'headbox-tabs' => array(
				'id'       => 'wpmoly-headbox-tabs',
				'type'     => 'select',
				'title'    => __( 'Headbox Tabs', 'wpmovielibrary' ),
				'desc'     => __( 'Which tabs should appear in the headbox and in which order.', 'wpmovielibrary' ) . '<br /><br /><img class="wpmoly-helper" src="' . WPMOLY_URL . '/assets/img/headbox_tabs.jpg" alt="" />',
				'multi'    => true,
				'sortable' => true,
				'options'  => array(
					'overview' => __( 'Overview', 'wpmovielibrary' ),
					'meta'     => __( 'Metadata', 'wpmovielibrary' ),
					'details'  => __( 'Details', 'wpmovielibrary' ),
					'actors'   => __( 'Actors', 'wpmovielibrary' ),
					'images'   => __( 'Images', 'wpmovielibrary' )
				),
				'default' => array( 'overview', 'meta', 'details', 'images', 'actors' ),
				'required' => array( 'wpmoly-headbox-theme', "=", 'wpmoly' )
			),

			// Title Content
			'headbox-title' => array(
				'id'       => 'wpmoly-headbox-title',
				'type'     => 'select',
				'title'    => __( 'Headbox Title', 'wpmovielibrary' ),
				'desc'     => __( 'Content of the Headbox Title line.', 'wpmovielibrary' ) . '<br /><br /><img class="wpmoly-helper" src="' . WPMOLY_URL . '/assets/img/headbox_title.jpg" alt="" />',
				'multi'    => true,
				'sortable' => true,
				'options'  => array(),
				'default'  => array( 'title' ),
				'required' => array( 'wpmoly-headbox-theme', "=", 'wpmoly' )
			),

			// Subtitle Content
			'headbox-subtitle' => array(
				'id'       => 'wpmoly-headbox-subtitle',
				'type'     => 'select',
				'title'    => __( 'Headbox Subtitle', 'wpmovielibrary' ),
				'desc'     => __( 'Content of the Headbox Subtitle line.', 'wpmovielibrary' ) . '<br /><br /><img class="wpmoly-helper" src="' . WPMOLY_URL . '/assets/img/headbox_subtitle.jpg" alt="" />',
				'multi'    => true,
				'sortable' => true,
				'options'  => array(),
				'default'  => array( 'tagline' ),
				'required' => array( 'wpmoly-headbox-theme', "=", 'wpmoly' )
			),

			//  Content
			'headbox-details-1' => array(
				'id'       => 'wpmoly-headbox-details-1',
				'type'     => 'select',
				'title'    => __( 'Headbox Details 1', 'wpmovielibrary' ),
				'desc'     => __( 'Content of the Headbox first details line.', 'wpmovielibrary' ) . '<br /><br /><img class="wpmoly-helper" src="' . WPMOLY_URL . '/assets/img/headbox_details_1.jpg" alt="" />',
				'multi'    => true,
				'sortable' => true,
				'options'  => array(),
				'default'  => array( 'status', 'media' ),
				'required' => array( 'wpmoly-headbox-theme', "=", 'wpmoly' )
			),

			//  Content
			'headbox-details-2' => array(
				'id'       => 'wpmoly-headbox-details-2',
				'type'     => 'select',
				'title'    => __( 'Headbox Details 2', 'wpmovielibrary' ),
				'desc'     => __( 'Content of the Headbox second details line.', 'wpmovielibrary' ) . '<br /><br /><img class="wpmoly-helper" src="' . WPMOLY_URL . '/assets/img/headbox_details_2.jpg" alt="" />',
				'multi'    => true,
				'sortable' => true,
				'options'  => array(),
				'default'  => array( 'release_date', 'runtime' ),
				'required' => array( 'wpmoly-headbox-theme', "=", 'wpmoly' )
			),

			//  Content
			'headbox-details-3' => array(
				'id'       => 'wpmoly-headbox-details-3',
				'type'     => 'select',
				'title'    => __( 'Headbox Details 3', 'wpmovielibrary' ),
				'desc'     => __( 'Content of the Headbox third details line.', 'wpmovielibrary' ) . '<br /><br /><img class="wpmoly-helper" src="' . WPMOLY_URL . '/assets/img/headbox_details_3.jpg" alt="" />',
				'multi'    => true,
				'sortable' => true,
				'options'  => array(),
				'default'  => array( 'rating' ),
				'required' => array( 'wpmoly-headbox-theme', "=", 'wpmoly' )
			)
		)
	),

	// 'wpmoly-cache' Caching
	array(
		'icon'    => 'wpmolicon icon-cache',
		'title'   => __( 'Cache', 'wpmovielibrary' ),
		'heading' => __( 'Caching', 'wpmovielibrary' ),
		'fields'  => array(

			// Results caching
			'enable' => array(
				'id'       => 'wpmoly-enable-cache',
				'type'     => 'switch',
				'title'    => __( 'Enable Caching', 'wpmovielibrary' ),
				'desc'     => __( 'If enabled, WPMovieLibrary will cache movie related data to prevent too frequent queries to the database. <strong>This feature is experimental!</strong> Enabling this could generate <strong>huge</strong> amounts of entries in your database. It is recommended to use this feature sparingly, ideally not in production. <a href="http://wpmovielibrary.com/documentation/performance">Learn more about caching</a>.', 'wpmovielibrary' ),
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

	// 'wpmoly-legacy' Legacy
	array(
		'icon'    => 'wpmolicon icon-legacy',
		'title'   => __( 'Legacy', 'wpmovielibrary' ),
		'heading' => __( 'Compatibility settings for WPMovieLibrary 1.x', 'wpmovielibrary' ),
		'fields'  => array(

			// Results caching
			'legacy-mode' => array(
				'id'       => 'wpmoly-legacy-mode',
				'type'     => 'switch',
				'title'    => __( 'Enable Legacy mode', 'wpmovielibrary' ),
				'subtitle' => __( 'WPMovieLibrary 1.x compatibility mode', 'wpmovielibrary' ),
				'description' => __( 'If enabled, WPMovieLibrary will automatically update all movies to the new metadata format introduced by version 1.3. Each time a metadata is access, the plugin will look for obsolete metadata and will update it if needed. Once all movies are updated the plugin will stop looking, but you should deactivate this anyway. <a href="http://wpmovielibrary.com/development/release-notes/#version-1.3">Learn more about this change</a>.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
			),

			// Delete deprecated safety
			'legacy-safety' => array(
				'id'       => 'wpmoly-legacy-safety',
				'type'     => 'switch',
				'title'    => __( 'Enable Legacy Safety mode', 'wpmovielibrary' ),
				'subtitle' => __( 'WPMovieLibrary 1.x compatibility safety mode', 'wpmovielibrary' ),
				'description' => __( 'If enabled, WPMovieLibrary will update deprecated metadata to the new format but will <em>not</em> delete the deprecated metadata for safety.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 1,
				'required' => array( 'wpmoly-legacy-mode', '=', 1 )
			)
		)
	),

	// 'wpmoly-api' API Settings
	array(
		'icon'    => 'wpmolicon icon-api',
		'title'   => __( 'API', 'wpmovielibrary' ),
		'heading' => __( 'TheMovieDB API settings', 'wpmovielibrary' ),
		'fields'  => array(

			// API internal mode
                        'personnal' => array(
				'id'       => 'wpmoly-api-internal',
				'type'     => 'switch',
				'title'    => __( 'Personnal API Key', 'wpmovielibrary' ),
				'subtitle' => __( 'Optional: use your own TMDb API key', 'wpmovielibrary' ),
				'desc'     => __( 'A valid TMDb API key is required to fetch informations on the movies you add to WPMovieLibrary. Leave deactivated if you do not have a personnal API key. <a href="http://tmdb.caercam.org/">Learn more</a> about the API key or <a href="https://www.themoviedb.org/">get your own</a>.', 'wpmovielibrary' ) . ' ' . __( 'If you do not have an API key or do not want to use yours right now, WPMovieLibrary will use just its own.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' )
                        ),

			// API Key
			'api_key' => array(
				'id'       => 'wpmoly-api-key',
				'type'     => 'text',
				'title'    => __( 'API Key', 'wpmovielibrary' ),
				'subtitle' => __( 'Set up your own API key', 'wpmovielibrary' ),
				'desc'     => __( 'Using your own API key is a more privacy-safe choice as it will avoid WPMovieLibrary to filter queries sent to the API through its own relay server at tmdb.caercam.org. You will also be able to access statistics on your API usage in your TMDb user account.', 'wpmovielibrary' ), 
				'validate' => 'no_special_chars',
				'validate_callback' => array( 'WPMOLY_TMDb', 'check_api_key' ),
				'default'  => null,
				'required' => array( 'wpmoly-api-internal', "=", 1 ),
				'indent'   => true
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
				'default'  => 'https'
			),

			// API Language
			'language' => array(
				'id'       => 'wpmoly-api-language',
				'type'     => 'select',
				'title'    => __( 'API Language', 'wpmovielibrary' ),
				'desc'     => __( 'Default language to use when fetching informations from TMDb. Default is english. You can always change this manually when add a new movie.', 'wpmovielibrary' ),
				'options'  => $wpmoly_supported_languages,
				'default'  => 'en'
			),

			// API Country
			'country' => array(
				'id'       => 'wpmoly-api-country',
				'type'     => 'select',
				'title'    => __( 'API Country', 'wpmovielibrary' ),
				'desc'     => __( 'Default country to use when fetching release informations from TMDb. Default is United States. This is mostly used to get movie certifications corresponding to your country.', 'wpmovielibrary' ),
				'options'  => $wpmoly_supported_countries,
				'default'  => 'US'
			),

			// API Alternative Country
			'country-alt' => array(
				'id'       => 'wpmoly-api-country-alt',
				'type'     => 'select',
				'title'    => __( 'API Alternative Country', 'wpmovielibrary' ),
				'desc'     => __( 'You can select an alternative country to use when fetching release informations from TMDb. If primary country leaves empty results, the alternative country will be used to fill the blank.', 'wpmovielibrary' ),
				'options'  => $wpmoly_supported_countries,
				'default'  => 'US'
			),
		)
	),

	// 'wpmoly-advanced' Advanced Settings
	array(
		'icon'    => 'wpmolicon icon-advanced',
		'title'   => __( 'Advanced Settings', 'wpmovielibrary' ),
		'heading' => __( 'Advanced Plugin Settings & Tools', 'wpmovielibrary' ),
		'fields'  => array(

			// API internal mode
                        'personnal' => array(
				'id'       => 'wpmoly-debug-mode',
				'type'     => 'switch',
				'title'    => __( 'Debug Mode', 'wpmovielibrary' ),
				'desc'     => __( 'Log specific information for debugging purpose.', 'wpmovielibrary' ),
				'on'       => __( 'Enabled', 'wpmovielibrary' ),
				'off'      => __( 'Disabled', 'wpmovielibrary' ),
				'default'  => 0
                        ),
		)
	),

	// Divider
	array(
		'type' => 'divide',
	),

	// 'wpmoly-deactivate' What to do on deactivation
	array(
		'icon'    => 'wpmolicon icon-deactivate',
		'title'   => __( 'Deactivate', 'wpmovielibrary' ),
		'heading' => __( 'Deactivation options', 'wpmovielibrary' ),
		'fields'  => array(

			'movies' => array(
				'id'       => 'wpmoly-deactivate-movies',
				'type'     => 'select',
				'title'    => __( 'Movie Post Type', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Movies when WPMovieLibrary is deactivated.', 'wpmovielibrary' ),
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
				'desc'     => __( 'How to handle Collections Taxonomy when WPMovieLibrary is deactivated.', 'wpmovielibrary' ),
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
				'desc'     => __( 'How to handle Genres Taxonomy when WPMovieLibrary is deactivated.', 'wpmovielibrary' ),
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
				'desc'     => __( 'How to handle Actors Taxonomy when WPMovieLibrary is deactivated.', 'wpmovielibrary' ),
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
				'desc'     => __( 'How to handle Cached data when WPMovieLibrary is deactivated.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'empty'    => __( 'Empty (recommended)', 'wpmovielibrary' ),
				),
				'default'  => 'empty'
			)
		)
	),

	// 'wpmoly-uninstall' What to do on uninstallation
	array(
		'icon'    => 'wpmolicon icon-no',
		'title'   => __( 'Uninstall', 'wpmovielibrary' ),
		'heading' => __( 'Uninstallation options', 'wpmovielibrary' ),
		'fields'  => array(

			'movies' => array(
				'id'       => 'wpmoly-uninstall-movies',
				'type'     => 'select',
				'title'    => __( 'Movie Post Type', 'wpmovielibrary' ),
				'desc'     => __( 'How to handle Movies when WPMovieLibrary is uninstalled.', 'wpmovielibrary' ),
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
				'desc'     => __( 'How to handle Collections Taxonomy when WPMovieLibrary is uninstalled.', 'wpmovielibrary' ),
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
				'desc'     => __( 'How to handle Genres Taxonomy when WPMovieLibrary is uninstalled.', 'wpmovielibrary' ),
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
				'desc'     => __( 'How to handle Actors Taxonomy when WPMovieLibrary is uninstalled.', 'wpmovielibrary' ),
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
				'desc'     => __( 'How to handle Cached data when WPMovieLibrary is uninstalled.', 'wpmovielibrary' ),
				'options'  => array(
					'conserve' => __( 'Conserve', 'wpmovielibrary' ),
					'empty'    => __( 'Empty (recommended)', 'wpmovielibrary' ),
				),
				'default'  => 'empty'
			)
		)
	),

	// 'wpmoly-import-export' Import/Export
	array(
		'icon'    => 'wpmolicon icon-update',
		'title'   => __( 'Import / Export', 'wpmovielibrary' ),
		'heading' => __( 'Import and Export your settings.', 'wpmovielibrary' ),
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

	// Divider
	/*array(
		'type' => 'divide',
	),*/

	// 'wpmoly-about' About Plugin
	/*array(
		'icon'   => 'wpmolicon icon-info',
		'title'  => __( 'Information', 'wpmovielibrary' ),
		'desc'   => __( '<p class="description">This is the Description. Again HTML is allowed</p>', 'wpmovielibrary' ),
		'fields' => array(
			array(
				'id'      => 'wpmoly-raw-info',
				'type'    => 'raw',
				'content' => '',
			)
		),
	)*/
);

$legacy_config = array(
	'tmdb' => array(
		'apikey'       => 'wpmoly-api-key',
		'internal_api' => 'wpmoly-api-internal',
		'lang'         => 'wpmoly-api-language',
		'scheme'       => 'wpmoly-api-scheme'
	),
	'wpml' => array(
		'show_in_home'          => 'wpmoly-frontpage',
		'movie_rewrite'         => 'wpmoly-rewrite-movie',
		'meta_in_posts'         => 'wpmoly-show-meta',
		'details_in_posts'      => 'wpmoly-show-details',
		'details_as_icons'      => 'wpmoly-details-icons',
		'date_format'           => 'wpmoly-format-date',
		'time_format'           => 'wpmoly-format-time'
	),
	'images' => array(
		'poster_featured' => 'wpmoly-poster-featured',
		'poster_size'     => 'wpmoly-poster-size',
		'images_size'     => 'wpmoly-images-size',
		'delete_images'   => 'wpmoly-images-delete',
		'delete_posters'  => 'wpmoly-posters-delete'
	),
	'taxonomies' => array(
		'enable_collection'       => 'wpmoly-enable-collection',
		'collection_rewrite'      => 'wpmoly-rewrite-collection',
		'collection_autocomplete' => 'wpmoly-collection-autocomplete',
		'enable_genre'            => 'wpmoly-enable-genre',
		'genre_rewrite'           => 'wpmoly-rewrite-genre',
		'genre_autocomplete'      => 'wpmoly-genre-autocomplete',
		'enable_actor'            => 'wpmoly-enable-actor',
		'actor_rewrite'           => 'wpmoly-rewrite-actor',
		'actor_autocomplete'      => 'wpmoly-actor-autocomplete',
		'actor_limit'             => 'wpmoly-actor-limit'
	),
	'deactivate' => array(
		'movies'      => 'wpmoly-deactivate-movies',
		'collections' => 'wpmoly-deactivate-collections',
		'genres'      => 'wpmoly-deactivate-genres',
		'actors'      => 'wpmoly-deactivate-actors',
		'cache'       => 'wpmoly-deactivate-cache'
	),
	'uninstall' => array(
		'movies'      => 'wpmoly-uninstall-movies',
		'collections' => 'wpmoly-uninstall-collections',
		'genres'      => 'wpmoly-uninstall-genres',
		'actors'      => 'wpmoly-uninstall-actors',
		'cache'       => 'wpmoly-uninstall-cache'
	),
	'cache' => array(
		'caching'      => 'wpmoly-enable-cache',
		'user_caching' => 'wpmoly-user-cache',
		'caching_time' => 'wpmoly-cache-expire'
	)
);