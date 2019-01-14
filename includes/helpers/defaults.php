<?php
/**
 * The file that defines the plugin default functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\helpers;

use \wpmoly\core\L10n;

/**
 * Retrieve supported meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_meta( $object_type = null ) {

	$meta = array();
	if ( ! is_null( $object_type ) && function_exists( "\wpmoly\helpers\get_registered_{$object_type}_meta" ) ) {
		$meta = call_user_func( "\wpmoly\helpers\get_registered_{$object_type}_meta" );
	}

	return $meta;
}

/**
 * Define supported post meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_post_meta( $meta_name = '' ) {

	$post_meta  = (array) get_default_posts_meta();
	$grid_meta  = (array) get_registered_grid_meta();
	$page_meta  = (array) get_registered_page_meta();
	$movie_meta = (array) get_registered_movie_meta();
	$attachment_meta = (array) get_registered_attachment_meta();

	$registered_meta = $post_meta + $grid_meta + $page_meta + $movie_meta + $attachment_meta;

	/**
	 * Filter default meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$registered_meta = apply_filters( 'wpmoly/filter/registered/post/meta', (array) $registered_meta );

	if ( empty( $meta_name ) ) {
		return $registered_meta;
	}

	if ( empty( $registered_meta[ $meta_name ] ) ) {
		return array();
	}

	return $registered_meta[ $meta_name ];
}

/**
 * Define common posts meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_default_posts_meta() {

	/**
	 * Filter default post meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$post_meta = apply_filters( 'wpmoly/filter/default/post/meta', array() );

	return $post_meta;
}

/**
 * Define supported post meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_term_meta( $meta_name = '' ) {

	$term_meta       = (array) get_default_terms_meta();
	$actor_meta      = (array) get_registered_actor_meta();
	$collection_meta = (array) get_registered_collection_meta();
	$genre_meta      = (array) get_registered_genre_meta();

	$registered_meta = $term_meta + $actor_meta + $collection_meta + $genre_meta;

	/**
	 * Filter default meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$registered_meta = apply_filters( 'wpmoly/filter/registered/term/meta', (array) $registered_meta );

	if ( empty( $meta_name ) ) {
		return $registered_meta;
	}

	if ( empty( $registered_meta[ $meta_name ] ) ) {
		return array();
	}

	return $registered_meta[ $meta_name ];
}

/**
 * Define common terms meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_default_terms_meta() {

	/**
	 * Filter default post meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$term_meta = apply_filters( 'wpmoly/filter/default/term/meta', array(
		'custom_thumbnail' => array(
			'type'         => 'integer',
			'taxonomy'     => array( 'actor', 'collection', 'genre' ),
			'description'  => __( 'Term custom thumbnail attachment ID.', 'wpmovielibrary' ),
			'show_in_rest' => true,
		),
		'thumbnail' => array(
			'type'         => 'string',
			'taxonomy'     => array( 'actor', 'collection', 'genre' ),
			'description'  => __( 'Term default thumbnail.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default'      => '',
		),
		'tmdb_id' => array(
			'type'         => 'integer',
			'taxonomy'     => array( 'actor', 'collection', 'genre' ),
			'description'  => __( 'Term related TMDb ID.', 'wpmovielibrary' ),
			'show_in_rest' => true,
		),
	) );

	return $term_meta;
}

/**
 * Define supported actor term meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_actor_meta( $meta_name = '' ) {

	$default_meta = get_default_terms_meta();

	$registered_meta = array(
		'snapshot' => array(
			'type'         => 'string',
			'taxonomy'     => array( 'actor', 'collection' ),
			'description'  => __( 'Person Snapshot.', 'wpmovielibrary' ),
			'protected'    => true,
			'show_in_rest' => array(
				'context'    => array( 'edit' ),
			),
		),
	);

	/**
	 * Filter default actor meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$registered_meta = apply_filters( 'wpmoly/filter/registered/actor/meta', $default_meta + $registered_meta );

	if ( empty( $meta_name ) ) {
		return $registered_meta;
	}

	if ( empty( $registered_meta[ $meta_name ] ) ) {
		return array();
	}

	return $registered_meta[ $meta_name ];
}

/**
 * Define supported collection term meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_collection_meta( $meta_name = '' ) {

	$default_meta = get_default_terms_meta();

	$registered_meta = array(
		'snapshot' => array(
			'type'         => 'string',
			'taxonomy'     => array( 'actor', 'collection' ),
			'description'  => __( 'Person Snapshot.', 'wpmovielibrary' ),
			'protected'    => true,
			'show_in_rest' => array(
				'context'    => array( 'edit' ),
			),
		),
	);

	/**
	 * Filter default collection meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$registered_meta = apply_filters( 'wpmoly/filter/registered/collection/meta', $default_meta + $registered_meta );

	if ( empty( $meta_name ) ) {
		return $registered_meta;
	}

	if ( empty( $registered_meta[ $meta_name ] ) ) {
		return array();
	}

	return $registered_meta[ $meta_name ];
}

/**
 * Define supported genre term meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_genre_meta( $meta_name = '' ) {

	$default_meta = get_default_terms_meta();

	$registered_meta = array();

	/**
	 * Filter default genre meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$registered_meta = apply_filters( 'wpmoly/filter/registered/genre/meta', $default_meta + $registered_meta );

	if ( empty( $meta_name ) ) {
		return $registered_meta;
	}

	if ( empty( $registered_meta[ $meta_name ] ) ) {
		return array();
	}

	return $registered_meta[ $meta_name ];
}

/**
 * Define supported grid post meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_grid_meta( $meta_name = '' ) {

	$default_meta = get_default_posts_meta();

	$registered_meta = array(
		'type' => array(
			'type'         => 'string',
			'post_type'    => array( 'grid' ),
			'description'  => __( 'Grid type', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 'movie',
		),
		'mode' => array(
			'type'         => 'string',
			'post_type'    => array( 'grid' ),
			'description'  => __( 'Grid mode', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 'grid',
		),
		'theme' => array(
			'type'         => 'string',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Grid theme', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 'default',
		),
		'preset' => array(
			'type'         => 'string',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Select a preset to apply to the grid. Presets override any filters and ordering settings you might define, be sure to select "Custom" for those settings to be used.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 'custom',
		),
		'columns' => array(
			'type'         => 'integer',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 5,
		),
		'rows' => array(
			'type'         => 'integer',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 4,
		),
		'list_columns' => array(
			'type'         => 'integer',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Number of columns for the grid in list mode. Default is 3.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 3,
		),
		'list_rows' => array(
			'type'         => 'integer',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Number of rows for the grid in list mode. Default is 8.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 8,
		),
		'enable_pagination' => array(
			'type'         => 'boolean',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Allow visitors to browse through the grid. Is Ajax browsing is enabled, pagination will use Ajax to dynamically load new pages instead of reloading. Default is enabled.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 1,
		),
		'settings_control' => array(
			'type'         => 'boolean',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Visitors will be able to change some settings to browse the grid differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 1,
		),
		'custom_letter' => array(
			'type'         => 'boolean',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Allow visitors to filter the grid by letters. Default is enabled.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 1,
		),
		'custom_order' => array(
			'type'         => 'boolean',
			'post_type'    => array( 'grid' ),
			'description'  => esc_html__( 'Allow visitors to change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
			'show_in_rest' => true,
			'default' => 1,
		),
	);

	/**
	 * Filter default grid meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$registered_meta = apply_filters( 'wpmoly/filter/registered/grid/meta', $default_meta + $registered_meta );

	if ( empty( $meta_name ) ) {
		return $registered_meta;
	}

	if ( empty( $registered_meta[ $meta_name ] ) ) {
		return array();
	}

	return $registered_meta[ $meta_name ];
}

/**
 * Define supported page post meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_page_meta( $meta_name = '' ) {

	$default_meta = get_default_posts_meta();

	$registered_meta = array(
		'grid_id' => array(
			'type'         => 'integer',
			'post_type'    => array( 'page' ),
			'description'  => __( 'Archive Page Grid ID', 'wpmovielibrary' ),
			'show_in_rest' => true,
		),
	);

	/**
	 * Filter default  meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$registered_meta = apply_filters( 'wpmoly/filter/registered/page/meta', $default_meta + $registered_meta );

	if ( empty( $meta_name ) ) {
		return $registered_meta;
	}

	if ( empty( $registered_meta[ $meta_name ] ) ) {
		return array();
	}

	return $registered_meta[ $meta_name ];
}

/**
 * Define supported movie post meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_movie_meta( $meta_name = '' ) {

	$default_meta = get_default_posts_meta();

	$registered_meta = array(
		// Meta.
		'adult' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Adult', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_adult',
			),
		),
		'author' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Author', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_author',
			),
		),
		'budget' => array(
			'type'          => 'integer',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Budget', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_budget',
			),
		),
		'cast' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Actors', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_cast',
			),
		),
		'certification' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Certification', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_certification',
			),
		),
		'composer' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Original Music Composer.', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_composer',
			),
		),
		'director' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Director', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_director',
			),
		),
		'genres' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Genres', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_genres',
			),
		),
		'homepage' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Homepage', 'wpmovielibrary' ),
			'format'       => 'uri',
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_homepage',
			),
		),
		'imdb_id' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'IMDb Id', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_imdb_id',
			),
		),
		'local_release_date' => array(
			'type'         => 'string',
			'format'       => 'data-time',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Local Release Date', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_local_release_date',
			),
		),
		'original_title' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Original Title', 'wpmovielibrary' ),
			'show_in_rest' => true,
		),
		'overview' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Overview', 'wpmovielibrary' ),
			'show_in_rest' => true,
		),
		'photography' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Director of Photography', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_photography',
			),
		),
		'producer' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Producer', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_producer',
			),
		),
		'production_companies' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Production Companies', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_production_companies',
			),
		),
		'production_countries' => array(
			'type'          => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Production Countries', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_production_countries',
			),
		),
		'release_date' => array(
			'type'         => 'string',
			'format'       => 'data-time',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Release Date', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_release_date',
			),
		),
		'revenue' => array(
			'type'         => 'integer',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Revenue', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_revenue',
			),
		),
		'runtime' => array(
			'type'         => 'integer',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Runtime.', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_runtime',
			),
		),
		'spoken_languages' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Spoken Languages.', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_spoken_languages',
			),
			'default'      => array(),
		),
		'tagline' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Tagline', 'wpmovielibrary' ),
			'show_in_rest' => true,
		),
		'title' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Title', 'wpmovielibrary' ),
			'show_in_rest' => true,
		),
		'tmdb_id' => array(
			'type'         => 'integer',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'TMDb Id', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_tmdb_id',
			),
		),
		'writer' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Writer', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_writer',
			),
		),
		// Details.
		'format' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'A list of format the movie is available in.', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_format',
				'enum'  => array(
					'3d' => __( '3D', 'wpmovielibrary' ),
					'sd' => __( 'SD', 'wpmovielibrary' ),
					'hd' => __( 'HD', 'wpmovielibrary' ),
				),
				/*'items'   => 'string',
				'default' => array(),*/
				'default' => '',
			),
		),
		'language' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'A list of language this movie is available in.', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_language',
				'enum'  => L10n::$supported_languages,
				/*'items'   => 'string',
				'default' => array(),*/
				'default' => '',
			),
		),
		'media' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'A list of media this movie is available in.', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_media',
				'enum'  => array(
					'dvd'     => __( 'DVD', 'wpmovielibrary' ),
					'bluray'  => __( 'Blu-ray', 'wpmovielibrary' ),
					'vod'     => __( 'VoD', 'wpmovielibrary' ),
					'divx'    => __( 'DivX', 'wpmovielibrary' ),
					'vhs'     => __( 'VHS', 'wpmovielibrary' ),
					'cinema'  => __( 'Cinema', 'wpmovielibrary' ),
					'other'   => __( 'Other', 'wpmovielibrary' ),
				),
				/*'items'   => 'string',
				'default' => array(),*/
				'default' => '',
			),
		),
		'rating' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Owner rating for this movie.', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_rating',
				'enum'  => array(
					'0.0' => __( 'Not rated', 'wpmovielibrary' ),
					'0.5' => __( 'Junk', 'wpmovielibrary' ),
					'1.0' => __( 'Very bad', 'wpmovielibrary' ),
					'1.5' => __( 'Bad', 'wpmovielibrary' ),
					'2.0' => __( 'Not that bad', 'wpmovielibrary' ),
					'2.5' => __( 'Average', 'wpmovielibrary' ),
					'3.0' => __( 'Not bad', 'wpmovielibrary' ),
					'3.5' => __( 'Good', 'wpmovielibrary' ),
					'4.0' => __( 'Very good', 'wpmovielibrary' ),
					'4.5' => __( 'Excellent', 'wpmovielibrary' ),
					'5.0' => __( 'Masterpiece', 'wpmovielibrary' ),
				),
				'default'  => '0.0',
			),
		),
		'status' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Status of the movie within the library.', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_status',
				'enum' => array(
					'available'   => __( 'Available', 'wpmovielibrary' ),
					'loaned'      => __( 'Loaned', 'wpmovielibrary' ),
					'scheduled'   => __( 'Scheduled', 'wpmovielibrary' ),
					'unavailable' => __( 'Unvailable', 'wpmovielibrary' ),
				),
				/*'default'  => array(),*/
				'default' => '',
			),
		),
		'subtitles' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'A list of subtitles this movie is available in.', 'wpmovielibrary' ),
			'show_in_rest' => array(
				'prepare_callback' => '\wpmoly\rest\prepare_movie_subtitles',
				'enum'  => array_merge( array(
					'none' => __( 'None', 'wpmovielibrary' ),
				), L10n::$supported_languages ),
				/*'items'   => 'string',
				'default' => array(),*/
				'default' => '',
			),
		),
		'backdrop_id' => array(
			'type'         => 'integer',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Movie Backdrop Attachment ID.', 'wpmovielibrary' ),
			'protected'    => true,
			'show_in_rest' => array(
				'context'    => array( 'edit' ),
			),
		),
		'poster_id' => array(
			'type'         => 'integer',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Movie Poster Attachment ID.', 'wpmovielibrary' ),
			'protected'    => true,
			'show_in_rest' => array(
				'context'    => array( 'edit' ),
			),
		),
		'snapshot' => array(
			'type'         => 'string',
			'post_type'    => array( 'movie' ),
			'description'  => __( 'Movie Snapshot.', 'wpmovielibrary' ),
			'protected'    => true,
			'show_in_rest' => array(
				'context'    => array( 'edit' ),
			),
		),
	);

	foreach ( $registered_meta as $name => $args ) {
		$registered_meta[ $name ]['post_type'] = array( 'movie' );
	}

	/**
	 * Filter default movie meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$registered_meta = apply_filters( 'wpmoly/filter/registered/movie/meta', $default_meta + $registered_meta );

	if ( empty( $meta_name ) ) {
		return $registered_meta;
	}

	if ( empty( $registered_meta[ $meta_name ] ) ) {
		return array();
	}

	return $registered_meta[ $meta_name ];
}

/**
 * Define supported attachment post meta.
 *
 * @since 3.0.0
 *
 * @return array
 */
function get_registered_attachment_meta( $meta_name = '' ) {

	$default_meta = get_default_posts_meta();

	$registered_meta = array(
		'backdrop_related_tmdb_id' => array(
			'type'         => 'integer',
			'post_type'    => array( 'attachment' ),
			'description'  => __( 'Backdrop related TMDb ID', 'wpmovielibrary' ),
			'protected'    => true,
			'show_in_rest' => true,
		),
		'image_related_tmdb_id' => array(
			'type'         => 'integer',
			'post_type'    => array( 'attachment' ),
			'description'  => __( 'Backdrop related TMDb ID (Legacy)', 'wpmovielibrary' ),
			'protected'    => true,
			'show_in_rest' => true,
		),
		'poster_related_tmdb_id' => array(
			'type'         => 'integer',
			'post_type'    => array( 'attachment' ),
			'description'  => __( 'Poster related TMDb ID', 'wpmovielibrary' ),
			'protected'    => true,
			'show_in_rest' => true,
		),
	);

	/**
	 * Filter default  meta.
	 *
	 * @since 3.0.0
	 *
	 * @param array $registered_meta Registered  Meta.
	 */
	$registered_meta = apply_filters( 'wpmoly/filter/registered/attachment/meta', $default_meta + $registered_meta );

	if ( empty( $meta_name ) ) {
		return $registered_meta;
	}

	if ( empty( $registered_meta[ $meta_name ] ) ) {
		return array();
	}

	return $registered_meta[ $meta_name ];
}
