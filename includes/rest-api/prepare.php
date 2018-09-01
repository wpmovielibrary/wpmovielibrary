<?php

namespace wpmoly\rest;

/**
 * Prepare movie adult for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $adult   Movie adult.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_adult( $adult, $context = 'edit' ) {

	return prepare_movie_meta_value( 'adult', $adult, $context );
}

/**
 * Prepare movie author list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $author  List of movie authors.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_author( $author, $context = 'edit' ) {

	return prepare_movie_meta_values( 'author', $author, $context );
}

/**
 * Prepare movie budget for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $budget  Movie budget.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_budget( $budget, $context = 'edit' ) {

	return prepare_movie_meta_value( 'budget', $budget, $context );
}

/**
 * Prepare movie cast list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $cast    List of movie actors.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_cast( $cast, $context = 'edit' ) {

	return prepare_movie_meta_values( 'cast', $cast, $context );
}

/**
 * Prepare movie certification for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $certification Movie certification.
 * @param  string $context       REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_certification( $certification, $context = 'edit' ) {

	return prepare_movie_meta_value( 'certification', $certification, $context );
}

/**
 * Prepare movie composer list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $composer List of movie composers.
 * @param  string $context  REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_composer( $composer, $context = 'edit' ) {

	return prepare_movie_meta_values( 'composer', $composer, $context );
}

/**
 * Prepare movie director list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $director List of movie directors.
 * @param  string $context  REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_director( $director, $context = 'edit' ) {

	return prepare_movie_meta_values( 'director', $director, $context );
}

/**
 * Prepare movie format list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $format List of movie formats.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_format( $format, $context = 'edit' ) {

	$options = array();
	if ( 'embed' === $context ) {
		$options['show_icon'] = false;
	}

	return prepare_movie_meta_values( 'format', $format, $context, $options );
}

/**
 * Prepare movie genres list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $genres List of movie genres.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_genres( $genres, $context = 'edit' ) {

	return prepare_movie_meta_values( 'genres', $genres, $context );
}

/**
 * Prepare movie homepage for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $homepage Movie homepage.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_homepage( $homepage, $context = 'edit' ) {

	return prepare_movie_meta_value( 'homepage', $homepage, $context );
}

/**
 * Prepare movie IMDb ID for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $imdb_id Movie IMDb ID.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_imdb_id( $imdb_id, $context = 'edit' ) {

	return prepare_movie_meta_value( 'imdb_id', $imdb_id, $context );
}

/**
 * Prepare movie language list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $language List of movie languages.
 * @param  string $context  REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_language( $language, $context = 'edit' ) {

	$options = array();
	if ( 'embed' === $context ) {
		$options['show_icon'] = false;
	}

	return prepare_movie_meta_values( 'language', $language, $context, $options );
}

/**
 * Prepare movie local release date for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $local_release_date Movie local release date.
 * @param  string $context            REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_local_release_date( $local_release_date, $context = 'edit' ) {

		return prepare_movie_meta_value( 'local_release_date', $local_release_date, $context );
}

/**
 * Prepare movie media list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $media List of movie media.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_media( $media, $context = 'edit' ) {

	$options = array();
	if ( 'embed' === $context ) {
		$options['show_icon'] = false;
	}

	return prepare_movie_meta_values( 'media', $media, $context, $options );
}

/**
 * Prepare movie director of photography list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $photography List of movie director of photography.
 * @param  string $context     REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_photography( $photography, $context = 'edit' ) {

	return prepare_movie_meta_values( 'photography', $photography, $context );
}

/**
 * Prepare movie producer list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $producer List of movie producers.
 * @param  string $context  REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_producer( $producer, $context = 'edit' ) {

	return prepare_movie_meta_values( 'producer', $producer, $context );
}

/**
 * Prepare movie production companies list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $production_companies List of movie production companies.
 * @param  string $context  REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_production_companies( $production_companies, $context = 'edit' ) {

	return prepare_movie_meta_values( 'production_companies', $production_companies, $context );
}

/**
 * Prepare movie production countries list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $production_countries List of movie production countries.
 * @param  string $context              REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_production_countries( $production_countries, $context = 'edit' ) {

	$options = array();
	if ( 'embed' === $context ) {
		$options['show_flag'] = false;
	}

	return prepare_movie_meta_values( 'production_countries', $production_countries, $context, $options );
}

/**
 * Prepare movie rating for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $rating  Movie rating.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_rating( $rating, $context = 'edit' ) {

	$options = array();
	if ( 'embed' === $context ) {
		$options['show_icon'] = false;
	}

	return prepare_movie_meta_value( 'rating', $rating, $context, $options );
}

/**
 * Prepare movie release date for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $release_date Movie release date.
 * @param  string $context      REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_release_date( $release_date, $context = 'edit' ) {

	return prepare_movie_meta_value( 'release_date', $release_date, $context );
}

/**
 * Prepare movie revenue for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $revenue Movie revenue.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_revenue( $revenue, $context = 'edit' ) {

	return prepare_movie_meta_value( 'revenue', $revenue, $context );
}

/**
 * Prepare movie runtime for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $runtime Movie runtime.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_runtime( $runtime, $context = 'edit' ) {

	return prepare_movie_meta_value( 'runtime', $runtime, $context );
}

/**
 * Prepare movie spoken languages list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $spoken_languages List of movie spoken_languages.
 * @param  string $context          REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_spoken_languages( $spoken_languages, $context = 'edit' ) {

	$options = array();
	if ( 'embed' === $context ) {
		$options['show_icon'] = false;
	}

	return prepare_movie_meta_values( 'spoken_languages', $spoken_languages, $context, $options );
}

/**
 * Prepare movie status list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $status  List of movie status.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_status( $status, $context = 'edit' ) {

	return prepare_movie_meta_values( 'status', $status, $context );
}

/**
 * Prepare movie subtitles list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $subtitles List of movie subtitles.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_subtitles( $subtitles, $context = 'edit' ) {

	$options = array();
	if ( 'embed' === $context ) {
		$options['show_icon'] = false;
	}

	return prepare_movie_meta_values( 'subtitles', $subtitles, $context, $options );
}

/**
 * Prepare movie TMDb ID for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $tmdb_id Movie TMDb ID.
 * @param  string $context REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_tmdb_id( $tmdb_id, $context = 'edit' ) {

	return prepare_movie_meta_value( 'tmdb_id', $tmdb_id, $context );
}

/**
 * Prepare movie writer list for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $writer   List of movie writers.
 * @param  string $context  REST API Request context, 'view' or 'edit'. Default is 'edit'.
 *
 * @return mixed
 */
function prepare_movie_writer( $writer, $context = 'edit' ) {

	return prepare_movie_meta_values( 'writer', $writer, $context );
}

/**
 * Prepare a specific item for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $type    Item type.
 * @param  string $item    Item.
 * @param  string $context REST API Request context.
 *
 * @return mixed
 */
function prepare_movie_meta_value( $type, $item, $context = 'edit', $options = array() ) {

	if ( 'view' === $context ) {
		/** This filter is documented in includes/nodes/posts/class-movie.php */
		$item = apply_filters( "wpmoly/filter/the/movie/{$type}", $item );
	} elseif ( 'embed' === $context ) {

		add_filter( "wpmoly/filter/meta/empty/{$type}/value", '__return_null' );

		$options = wp_parse_args( $options, array(
			'is_link' => false,
		) );

		/** This filter is documented in includes/nodes/posts/class-movie.php */
		$item = apply_filters( "wpmoly/filter/the/movie/{$type}", $item, $options );

		remove_filter( "wpmoly/filter/meta/empty/{$type}/value", '__return_null' );
	}

	return $item;
}

/**
 * Prepare a list of items for REST API Response.
 *
 * @since 3.0.0
 *
 * @param  string $type    Item type.
 * @param  string $items   List of items.
 * @param  string $context REST API Request context.
 *
 * @return mixed
 */
function prepare_movie_meta_values( $type, $items, $context = 'edit', $options = array() ) {

	if ( 'view' === $context ) {
		/** This filter is documented in includes/nodes/posts/class-movie.php */
		$items = apply_filters( "wpmoly/filter/the/movie/{$type}", $items, $options );
	} elseif ( 'embed' === $context ) {

		add_filter( "wpmoly/filter/meta/empty/{$type}/value", '__return_null' );

		$options = wp_parse_args( $options, array(
			'is_link' => false,
		) );

		/** This filter is documented in includes/nodes/posts/class-movie.php */
		$items = apply_filters( "wpmoly/filter/the/movie/{$type}", $items, $options );

		remove_filter( "wpmoly/filter/meta/empty/{$type}/value", '__return_null' );
	} elseif ( 'edit' === $context ) {
		if ( is_string( $items ) ) {
			$items = explode( ',', $items );
		}
		$items = array_map( 'trim', (array) $items );
		$items = array_filter( $items );
	}

	return $items;
}
