<?php
/**
 * The file that defines the plugin formatting functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\helpers;

/**
 * Format movie adult status.
 *
 * @since 3.0.0
 *
 * @param string $adult   Field value.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_adult( $adult, $options = array() ) {

	if ( empty( $adult ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/adult/value', format_empty_value( $adult ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$is_adult = _is_bool( $adult );
	if ( $is_adult ) {
		$adult = __( 'Yes', 'wpmovielibrary' );
	} else {
		$adult = __( 'No', 'wpmovielibrary' );
	}

	if ( $options['is_link'] ) {

		/**
		 * Filter final adult restriction.
		 *
		 * @since 3.0.0
		 *
		 * @param string $adult   Filtered adult restriction.
		 * @param array  $options Formatting options.
		 */
		return apply_filters( 'wpmoly/filter/meta/adult/url', $adult, compact( 'is_adult' ) );
	}

	/**
	 * Filter final adult restriction.
	 *
	 * @since 3.0.0
	 *
	 * @param string  $adult    Filtered adult restriction.
	 * @param boolean $is_adult Adult restriction?
	 */
	return apply_filters( 'wpmoly/filter/meta/adult', $adult, $is_adult );
}

/**
 * Format movie author.
 *
 * @since 3.0.0
 *
 * @param string $author  Movie author.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_author( $author, $options = array() ) {

	if ( empty( $author ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/author/value', format_empty_value( $author ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$authors = explode( ',', $author );
	foreach ( $authors as $key => $value ) {

		/**
		 * Filter single author.
		 *
		 * @since 3.0.0
		 *
		 * @param string $author  Single author.
		 * @param array  $options Formatting options.
		 */
		$value = apply_filters( 'wpmoly/filter/meta/author/single', trim( $value ), $options );

		if ( $options['is_link'] ) {

			/**
			 * Filter single author URL.
			 *
			 * @since 3.0.0
			 *
			 * @param string $author  Filtered single author.
			 * @param array  $options Formatting options.
			 */
			$value = apply_filters( 'wpmoly/filter/meta/author/url', $value, $options );
		}

		$authors[ $key ] = $value;
	}

	$authors = implode( ', ', $authors );

	/**
	 * Filter final author list.
	 *
	 * @since 3.0.0
	 *
	 * @param string $authors Filtered author list.
	 * @param array  $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/author', $authors, $options );
}

/**
 * Format movies budget.
 *
 * @since 3.0.0
 *
 * @param string $budget  Movie budget.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_budget( $budget, $options = array() ) {

	if ( empty( $budget ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/budget/value', format_empty_value( $budget ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link'       => true,
		'currency'      => '$',
		'sign_position' => 'before',
	) );

	if ( $options['is_link'] && has_filter( 'wpmoly/filter/meta/budget/url' ) ) {

		/**
		 * Filter final budget restriction.
		 *
		 * @since 3.0.0
		 *
		 * @param string $adult   Filtered budget restriction.
		 * @param array  $options Formatting options.
		 */
		return apply_filters( 'wpmoly/filter/meta/budget/url', $budget, array(
			'content' => format_money( $budget, $options ),
		) );
	}

	$budget = format_money( $budget, $options );

	/**
	 * Filter final budget value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $budget  Filtered budget value.
	 * @param array  $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/budget', $budget, $options );
}

/**
 * Format movies certification.
 *
 * @since 3.0.0
 *
 * @param string $certification Movie certification.
 * @param array  $options       Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_certification( $certification, $options = array() ) {

	if ( empty( $certification ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/certification/value', format_empty_value( $certification ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	if ( $options['is_link'] ) {

		/**
		 * Filter certification URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $certification Filtered certification.
		 * @param array  $options       Formatting options.
		 */
		$certification = apply_filters( 'wpmoly/filter/meta/certification/url', $certification, $options );
	}

	/**
	 * Filter final certification value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $certification Filtered certification value.
	 * @param array  $options       Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/certification', $certification, $options );
}

/**
 * Format movie composer.
 *
 * @since 3.0.0
 *
 * @param string $composer Movie composer.
 * @param array  $options  Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_composer( $composer, $options = array() ) {

	if ( empty( $composer ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/composer/value', format_empty_value( $composer ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$composers = explode( ',', $composer );
	foreach ( $composers as $key => $value ) {

		/**
		 * Filter single composer.
		 *
		 * @since 3.0.0
		 *
		 * @param string $composer Single composer.
		 * @param array  $options  Formatting options.
		 */
		$value = apply_filters( 'wpmoly/filter/meta/composer/single', trim( $value ), $options );

		if ( $options['is_link'] ) {

			/**
			 * Filter single composer URL.
			 *
			 * @since 3.0.0
			 *
			 * @param string $composer Filtered single composer .
			 * @param array  $options  Formatting options.
			 */
			$value = apply_filters( 'wpmoly/filter/meta/composer/url', $value, $options );
		}

		$composers[ $key ] = $value;
	}

	$composers = implode( ', ', $composers );

	/**
	 * Filter final composer list.
	 *
	 * @since 3.0.0
	 *
	 * @param string $composers Filtered composer list.
	 * @param array  $options   Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/composer', $composers, $options );
}

/**
 * Format movie actors.
 *
 * Match each actor against the actor taxonomy to detect missing
 * terms. If term actor exists, provide a link, raw text value
 * if no matching term could be found.
 *
 * @since 3.0.0
 *
 * @param string $cast    Actor list.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_cast( $cast, $options = array() ) {

	if ( empty( $cast ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/cast/value', format_empty_value( $cast ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$cast = format_terms_list( $cast, 'actor', $options );

	return $cast;
}

/**
 * Format movie production countries.
 *
 * @since 3.0.0
 *
 * @param string $production_countries Movie countries list.
 * @param array  $options              Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_production_countries( $production_countries, $options = array() ) {

	if ( empty( $production_countries ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/production_countries/value', format_empty_value( $production_countries ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'show_flag' => true,
		'is_link'   => true,
	) );

	$formatted_countries = array();
	$production_countries = explode( ',', $production_countries );
	foreach ( $production_countries as $key => $country ) {

		$country = get_country( $country );

		$formatted_country = $country->localized_name;

		if ( $options['is_link'] ) {

			/**
			 * Filter single country url.
			 *
			 * @since 3.0.0
			 *
			 * @param string $country Country object.
			 * @param array  $options Formatting options.
			 */
			$formatted_country = apply_filters( 'wpmoly/filter/meta/production_countries/url', $country, $options );
		}

		if ( $formatted_country instanceof \wpmoly\helpers\Country ) {
			$formatted_country = $formatted_country->localized_name;
		}

		if ( $options['show_flag'] ) {
			$formatted_country = $country->flag() . $formatted_country;
		}

		$formatted_countries[] = $formatted_country;
	}

	/**
	 * Filter final countries lists.
	 *
	 * @since 3.0.0
	 *
	 * @param string $countries           Filtered countries list.
	 * @param array  $formatted_countries Countries format.
	 * @param array  $countries_data      Countries data array.
	 */
	return apply_filters( 'wpmoly/filter/meta/production_countries', implode( ', ', $formatted_countries ), $formatted_countries, $production_countries );
}

/**
 * Format movie directors.
 *
 * Match each director against the director taxonomy to detect missing
 * terms. If term director exists, provide a link, raw text value
 * if no matching term could be found.
 *
 * @since 3.0.0
 *
 * @param string $director Director list.
 * @param array  $options  Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_director( $director, $options = array() ) {

	if ( empty( $director ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/director/value', format_empty_value( $director ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$director = format_terms_list( $director, 'collection', $options );

	return $director;
}

/**
 * Format movie formats.
 *
 * @since 3.0.0
 *
 * @param string $format  Movie format.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_format( $format, $options = array() ) {

	if ( empty( $format ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/format/value', format_empty_value( $format ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'show_text'  => true,
		'show_icon'  => true,
		'is_link'    => true,
		'attr_title' => '',
	) );

	$format = format_detail( 'format', $format, $options );

	return $format;
}

/**
 * Format movie genres.
 *
 * Match each genre against the genre taxonomy to detect missing
 * terms. If term genre exists, provide a link, raw text value
 * if no matching term could be found.
 *
 * @since 3.0.0
 *
 * @param string $genres  Field value.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_genres( $genres, $options = array() ) {

	if ( empty( $genres ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/genres/value', format_empty_value( $genres ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$genres = format_terms_list( $genres, 'genre', $options );

	return $genres;
}

/**
 * Format movie homepage link.
 *
 * @since 3.0.0
 *
 * @param string $homepage Homepage value.
 * @param array  $options  Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_homepage( $homepage, $options = array() ) {

	if ( empty( $homepage ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/homepage/value', format_empty_value( $homepage ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	if ( $options['is_link'] ) {

		/**
		 * Filter homepage url.
		 *
		 * @since 3.0.0
		 *
		 * @param string  $homepage Homepage value.
		 * @param boolean $is_adult Adult restriction?
		 */
		return apply_filters( 'wpmoly/filter/meta/homepage/url', $homepage );
	}

	return $homepage;
}

/**
 * Format movie IMDb ID.
 *
 * @since 3.0.0
 *
 * @param string $writer  Movie IMDb ID.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_imdb_id( $imdb_id, $options = array() ) {

	if ( empty( $imdb_id ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/imdb_id/value', format_empty_value( $imdb_id ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	if ( $options['is_link'] ) {

		/**
		 * Filter IMDb ID URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $writer  Filtered IMDb ID.
		 * @param array  $options Formatting options.
		 */
		$imdb_id = apply_filters( 'wpmoly/filter/meta/imdb_id/url', $imdb_id, $options );
	}

	/**
	 * Filter final IMDb ID.
	 *
	 * @since 3.0.0
	 *
	 * @param string $imdb_id Filtered IMDb ID.
	 * @param array  $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/imdb_id', $imdb_id, $options );
}

/**
 * Format movies languages.
 *
 * These are personal languages, ie. detail.
 *
 * @since 3.0.0
 *
 * @param string $languages Movie languages.
 * @param array  $options   Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_language( $languages, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = 'language';

	return format_language( $languages, $options );
}

/**
 * Format movies local release dates.
 *
 * @since 3.0.0
 *
 * @param string $local_release_date Movie local release date.
 * @param array  $options            Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_local_release_date( $local_release_date, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = 'local_';

	return format_date( $local_release_date, $options );
}

/**
 * Format movie medias.
 *
 * @since 3.0.0
 *
 * @param string $media   Movie media.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_media( $media, $options = array() ) {

	if ( empty( $media ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/media/value', format_empty_value( $media ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'show_text'  => true,
		'show_icon'  => true,
		'is_link'    => true,
		'attr_title' => '',
	) );

	$media = format_detail( 'media', $media, $options );

	return $media;
}

/**
 * Format movie director of photography.
 *
 * @since 3.0.0
 *
 * @param string $photography Movie director of photography.
 * @param array  $options     Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_photography( $photography, $options = array() ) {

	if ( empty( $photography ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/photography/value', format_empty_value( $photography ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$photographers = explode( ',', $photography );
	foreach ( $photographers as $key => $value ) {

		/**
		 * Filter single director of photography.
		 *
		 * @since 3.0.0
		 *
		 * @param string $photography Single director of photography.
		 * @param array  $options     Formatting options.
		 */
		$value = apply_filters( 'wpmoly/filter/meta/photography/single', trim( $value ), $options );

		if ( $options['is_link'] ) {

			/**
			 * Filter single director of photography URL.
			 *
			 * @since 3.0.0
			 *
			 * @param string $photography Filtered single director of photography.
			 * @param array  $options     Formatting options.
			 */
			$value = apply_filters( 'wpmoly/filter/meta/photography/url', $value, $options );
		}

		$photographers[ $key ] = $value;
	}

	$photographers = implode( ', ', $photographers );

	/**
	 * Filter final director of photography list.
	 *
	 * @since 3.0.0
	 *
	 * @param string $photographers Filtered director of photography list.
	 * @param array  $options       Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/photography', $photographers, $options );
}

/**
 * Format movie production companies.
 *
 * @since 3.0.0
 *
 * @param string $production_companies Movie production companies.
 * @param array  $options              Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_production_companies( $production_companies, $options = array() ) {

	if ( empty( $production_companies ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/production_companies/value', format_empty_value( $production_companies ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$production_companies = explode( ',', $production_companies );
	foreach ( $production_companies as $key => $value ) {

		/**
		 * Filter single production company.
		 *
		 * @since 3.0.0
		 *
		 * @param string $production_company Single production company.
		 * @param array  $options            Formatting options.
		 */
		$value = apply_filters( 'wpmoly/filter/meta/production_companies/single', trim( $value ), $options );

		if ( $options['is_link'] ) {

			/**
			 * Filter single production company URL.
			 *
			 * @since 3.0.0
			 *
			 * @param string $production_company Filtered single production company.
			 * @param array  $options            Formatting options.
			 */
			$value = apply_filters( 'wpmoly/filter/meta/production_companies/url', $value, $options );
		}

		$production_companies[ $key ] = $value;
	}

	$production_companies = implode( ', ', $production_companies );

	/**
	 * Filter final production companies list.
	 *
	 * @since 3.0.0
	 *
	 * @param string $production_companies Filtered production companies list.
	 * @param array  $options              Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/production_companies', $production_companies, $options );
}

/**
 * Format movie producers.
 *
 * @since 3.0.0
 *
 * @param string $producer Movie producers.
 * @param array  $options  Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_producer( $producer, $options = array() ) {

	if ( empty( $producer ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/producer/value', format_empty_value( $producer ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$producers = explode( ',', $producer );
	foreach ( $producers as $key => $value ) {

		/**
		 * Filter single producer.
		 *
		 * @since 3.0.0
		 *
		 * @param string $producer Single producer.
		 * @param array  $options  Formatting options.
		 */
		$value = apply_filters( 'wpmoly/filter/meta/producer/single', trim( $value ), $options );

		if ( $options['is_link'] ) {

			/**
			 * Filter single producer URL.
			 *
			 * @since 3.0.0
			 *
			 * @param string $producer Filtered single producer.
			 * @param array  $options  Formatting options.
			 */
			$value = apply_filters( 'wpmoly/filter/meta/producer/url', $value, $options );
		}

		$producers[ $key ] = $value;
	}

	$producers = implode( ', ', $producers );

	/**
	 * Filter final producer list.
	 *
	 * @since 3.0.0
	 *
	 * @param string $producer Filtered producer list.
	 * @param array  $options  Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/producer', $producers, $options );
}

/**
 * Format movie ratings.
 *
 * @since 3.0.0
 *
 * @param string $rating  Movie rating.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_rating( $rating, $options = array() ) {

	if ( empty( $rating ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/rating/value', format_empty_value( $rating ) );
	}

	// Parse formatting options
	$options = wp_parse_args( (array) $options, array(
		'show_icon' => true,
		'show_text' => false,
		'is_link'   => true,
		'include_empty' => true,
	) );

	$text = '';
	$html = '';
	$title = '';

	$show_text = _is_bool( $options['show_text'] );
	$show_icon = _is_bool( $options['show_icon'] );

	$base = (int) wpmoly_o( 'format-rating' );
	if ( 10 != $base ) {
		$base = 5;
	}

	$value = floatval( $rating );
	if ( 0 > $value ) {
		$value = 0.0;
	}
	if ( 5.0 < $value ) {
		$value = 5.0;
	}

	$value = number_format( $value, 1 );
	$metadata = get_registered_movie_meta( 'rating' );
	if ( ! empty( $metadata['show_in_rest']['enum'][ $value ] ) ) {
		$label = $metadata['show_in_rest']['enum'][ $value ];
	} else {
		$label = '';
	}

	$id = preg_replace( '/([0-5])(\.|_)(0|5)/i', '$1-$3', $value );
	$class = "wpmoly-movie-rating wpmoly-movie-rating-$id";

	if ( $show_text ) {
		$text = $label;
	}

	if ( $show_icon ) {

		$stars = array();

		/**
		 * Filter filled stars icon HTML block.
		 *
		 * @since 3.0.0
		 *
		 * @param string $html Filled star icon default HTML block
		 */
		$stars['filled'] = apply_filters( 'wpmoly/filter/html/filled/star', '<span class="wpmolicon icon-star"></span>' );

		/**
		 * Filter half-filled stars icon HTML block.
		 *
		 * @since 3.0.0
		 *
		 * @param string $html Half-filled star icon default HTML block
		 */
		$stars['half'] = apply_filters( 'wpmoly/filter/html/half/star', '<span class="wpmolicon icon-star-half"></span>' );

		/**
		 * Filter empty stars icon HTML block.
		 *
		 * @since 3.0.0
		 *
		 * @param string $html Empty star icon default HTML block
		 */
		$stars['empty'] = apply_filters( 'wpmoly/filter/html/empty/star', '<span class="wpmolicon icon-star-empty"></span>' );

		$filled = floor( $value );
		$half   = ceil( $value - floor( $value ) );
		$empty  = ceil( 5.0 - ( $filled + $half ) );

		if ( 0.0 == $value ) {
			if ( _is_bool( $options['include_empty'] ) ) {
				$html = str_repeat( $stars['empty'], $base );
			} else {
				$class = 'not-rated';
				$html  = sprintf( '<small><em>%s</em></small>', __( 'Not rated yet!', 'wpmovielibrary' ) );
			}
		} else if ( 10 == $base ) {
			$_filled = $value * 2;
			$_empty  = 10 - $_filled;
			$title   = "{$_filled}/10 − {$label}";

			$html = str_repeat( $stars['filled'], $_filled ) . str_repeat( $stars['empty'], $_empty );
		} else {
			/*$_filled = floor( $value );
			$_empty  = 5 - ceil( $value );*/

			$title = "{$value}/5 − {$label}";
			$html  = str_repeat( $stars['filled'], $filled ) . str_repeat( $stars['half'], $half ) . str_repeat( $stars['empty'], $empty );
		}

		$html = '<span class="' . $class . '" title="' . $title . '">' . $html . '</span> ';

		/**
		 * Filter generated HTML markup.
		 *
		 * @since 3.0.0
		 *
		 * @param string $html  Stars HTML markup
		 * @param float  $value Rating value
		 * @param string $title Rating title
		 * @param string $class CSS classes
		 */
		$html = apply_filters( 'wpmoly/filter/html/rating/stars', $html, $value, $title, $class );
	} else {
		$html = $label;
	} // End if().

	if ( _is_bool( $options['is_link'] ) ) {

		/**
		 * Filter meta permalink.
		 *
		 * @since 3.0.0
		 *
		 * @param string $value   Rating value.
		 * @param array  $options Permalink options.
		 */
		$html = apply_filters( 'wpmoly/filter/detail/rating/url', $value, array(
			'content' => $html,
			'title'   => sprintf( __( 'Movies rated %s', 'wpmovielibrary' ), $label ),
		) );
	}

	$filtered_rating = $html;
	if ( $show_text && $show_icon ) {
		$filtered_rating = $html . $text;
	}

	/**
	 * Filter final rating stars.
	 *
	 * @since 3.0.0
	 *
	 * @param string $filtered_rating Filtered rating HTML output.
	 * @param string $html            HTML part.
	 * @param string $text            Text part.
	 * @param string $value           Rating value.
	 * @param string $label           Rating label.
	 * @param array  $options         Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/detail/rating', $filtered_rating, $html, $text, $value, $label, $options );
}

/**
 * Format movies release dates.
 *
 * @since 3.0.0
 *
 * @param string $release_date Movie release date.
 * @param array  $options      Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_release_date( $release_date, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = '';

	return format_date( $release_date, $options );
}

/**
 * Format movies revenue.
 *
 * @since 3.0.0
 *
 * @param string $revenue Movie revenue.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_revenue( $revenue, $options = array() ) {

	if ( empty( $revenue ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/revenue/value', format_empty_value( $revenue ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link'       => true,
		'currency'      => '$',
		'sign_position' => 'before',
	) );

	if ( $options['is_link'] && has_filter( 'wpmoly/filter/meta/revenue/url' ) ) {

		/**
		 * Filter final revenue restriction.
		 *
		 * @since 3.0.0
		 *
		 * @param string $adult   Filtered revenue restriction.
		 * @param array  $options Formatting options.
		 */
		return apply_filters( 'wpmoly/filter/meta/revenue/url', $revenue, array(
			'content' => format_money( $revenue, $options ),
		) );
	}

	$revenue = format_money( $revenue, $options );

	/**
	 * Filter final revenue value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $revenue Filtered revenue value.
	 * @param array  $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/revenue', $revenue, $options );
}

/**
 * Format movie runtime.
 *
 * If no format is provided, use the format defined in settings. If no such
 * settings can be found, fallback to a standard 'X h Y min' format.
 *
 * @since 3.0.0
 *
 * @param string $runtime Movie runtime.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_runtime( $runtime, $options = array() ) {

	if ( empty( $runtime ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/runtime/value', format_empty_value( $runtime ) );
	}

	// Parse formatting options
	$options = wp_parse_args( (array) $options, array(
		'format' => '',
	) );

	$runtime = absint( $runtime );

	$options['format'] = (string) $options['format'];
	if ( empty( $options['format'] ) ) {
		$options['format'] = get_option( 'time_format' );
		if ( empty( $options['format'] ) ) {
			$options['format'] = 'G \h i \m\i\n';
		}
	}

	$runtime = date_i18n( $options['format'], mktime( 0, $runtime ) );
	if ( false !== stripos( $runtime, 'am' ) || false !== stripos( $runtime, 'pm' ) ) {
		$runtime = date_i18n( 'G:i', mktime( 0, $runtime ) );
	}

	/**
	 * Filter final runtime value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $runtime Filtered runtime value.
	 * @param array  $options Formatting options.
	 */
	return $runtime;
}

/**
 * Format movie spoken languages.
 *
 * @since 3.0.0
 *
 * @param string $runtime Movie runtime.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_spoken_languages( $languages, $options = array() ) {

	$options = (array) $options;

	return format_language( $languages, $options );
}

/**
 * Format movie statuses.
 *
 * @since 3.0.0
 *
 * @param string $status Movie status.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_status( $status, $options = array() ) {

	if ( empty( $status ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/status/value', format_empty_value( $status ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'show_text'  => true,
		'show_icon'  => true,
		'is_link'    => true,
		'attr_title' => '',
	) );

	$status = format_detail( 'status', $status, $options );

	return $status;
}

/**
 * Format movies subtitles.
 *
 * @since 3.0.0
 *
 * @param string $subtitles Movie subtitles.
 * @param array  $options   Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_subtitles( $subtitles, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = 'subtitles';

	return format_language( $subtitles, $options );
}

/**
 * Format movie TMDb ID.
 *
 * @since 3.0.0
 *
 * @param string $writer  Movie TMDb ID.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_tmdb_id( $tmdb_id, $options = array() ) {

	if ( empty( $tmdb_id ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/tmdb_id/value', format_empty_value( $tmdb_id ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	if ( $options['is_link'] ) {

		/**
		 * Filter TMDb ID URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $writer  Filtered TMDb ID.
		 * @param array  $options Formatting options.
		 */
		$tmdb_id = apply_filters( 'wpmoly/filter/meta/tmdb_id/url', $tmdb_id, $options );
	}

	/**
	 * Filter final TMDb ID.
	 *
	 * @since 3.0.0
	 *
	 * @param string $tmdb_id Filtered TMDb ID.
	 * @param array  $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/tmdb_id', $tmdb_id, $options );
}

/**
 * Format movie writers.
 *
 * @since 3.0.0
 *
 * @param string $writer  Movie writers.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_writer( $writer, $options = array() ) {

	if ( empty( $writer ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/writer/value', format_empty_value( $writer ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	$writers = explode( ',', $writer );
	foreach ( $writers as $key => $value ) {

		/**
		 * Filter single writer.
		 *
		 * @since 3.0.0
		 *
		 * @param string $writer  Single writer.
		 * @param array  $options Formatting options.
		 */
		$value = apply_filters( 'wpmoly/filter/meta/writer/single', trim( $value ), $options );

		if ( $options['is_link'] ) {

			/**
			 * Filter single writer URL.
			 *
			 * @since 3.0.0
			 *
			 * @param string $writer  Filtered single writer .
			 * @param array  $options Formatting options.
			 */
			$value = apply_filters( 'wpmoly/filter/meta/writer/url', $value, $options );
		}

		$writers[ $key ] = $value;
	}

	$writers = implode( ', ', $writers );

	/**
	 * Filter final writer list.
	 *
	 * @since 3.0.0
	 *
	 * @param string $writer  Filtered writer list.
	 * @param array  $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/writer', $writers, $options );
}

/**
 * Format movie years.
 *
 * @since 3.0.0
 *
 * @param string $year    Movie release year.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_movie_year( $year, $options = array() ) {

	if ( ! preg_match( '/^\d{4}$/i', $year ) ) {
		$year = '';
	}

	if ( empty( $year ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( 'wpmoly/filter/meta/empty/year/value', format_empty_value( $year ) );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	if ( $options['is_link'] ) {

		/**
		 * Filter year URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $year    Filtered year.
		 * @param array  $options Formatting options.
		 */
		$year = apply_filters( 'wpmoly/filter/meta/year/url', $year, $options );
	}

	/**
	 * Filter final year.
	 *
	 * @since 3.0.0
	 *
	 * @param string $year    Filtered year.
	 * @param array  $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/meta/year', $year, $options );
}

/*
 * Generic formatting functions.
 */

/**
 * Format a money value.
 *
 * @since 3.0.0
 *
 * @param string $money   Money value.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_money( $money, $options = array() ) {

	$money = intval( $money );
	if ( ! $money ) {
		return format_empty_value( $money );
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'currency'      => '$',
		'sign_position' => 'before',
	) );

	$money = number_format_i18n( $money );
	if ( 'after' == $options['sign_position'] ) {
		$money = $money . $options['currency'];
	} else {
		$money = $options['currency'] . $money;
	}

	return $money;
}

/**
 * Format a date value.
 *
 * If no format is provided, use the format defined in settings. If no such
 * settings can be found, fallback to a standard 'j F Y' format.
 *
 * @since 3.0.0
 *
 * @param string $date    Date value.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_date( $date, $options = array() ) {

	// Parse formatting options
	$options = wp_parse_args( (array) $options, array(
		'format'  => '',
		'variant' => '',
		'is_link' => true,
	) );

	if ( empty( $date ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( "wpmoly/filter/meta/empty/{$options['variant']}release_date/value", format_empty_value( $date ) );
	}

	$timestamp  = strtotime( $date );

	$options['format'] = (string) $options['format'];
	if ( empty( $options['format'] ) ) {
		$options['format'] = get_option( 'date_format' );
		if ( empty( $options['format'] ) ) {
			$options['format'] = 'j F Y';
		}
	}

	$formatted_date = date_i18n( $options['format'], $timestamp );

	if ( $options['is_link'] ) {

		$options['content'] = $formatted_date;

		/**
		 * Filter date URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $date    Date.
		 * @param array  $options Formatting options.
		 */
		$formatted_date = apply_filters( "wpmoly/filter/meta/{$options['variant']}release_date/url", $date, $options );
	}

	/**
	 * Filter final date value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $formatted_date Filtered date.
	 * @param array  $options        Formatting options.
	 */
	return apply_filters( "wpmoly/filter/meta/{$options['variant']}release_date", $formatted_date, $options );
}

/**
 * Format details.
 *
 * @since 3.0.0
 *
 * @param string $detail  Detail slug.
 * @param array  $value   Detail value.
 * @param array  $options Formatting options.
 *
 * @return string Formatted value
 */
function format_detail( $detail, $value, $options = array() ) {

	$metadata = get_registered_movie_meta( $detail );
	if ( empty( $metadata['show_in_rest']['enum'] ) ) {
		return '';
	}

	// Formatting options.
	$options = wp_parse_args( (array) $options, array(
		'show_text'  => true,
		'show_icon'  => true,
		'is_link'    => true,
		'attr_title' => '',
	) );

	if ( ! is_array( $value ) ) {
		$value = (array) $value;
	}

	$enum = $metadata['show_in_rest']['enum'];
	foreach ( $value as $key => $slug ) {
		if ( ! empty( $enum[ $slug ] ) ) {
			$filtered_value = '';
			if ( $options['show_text'] ) {
				$filtered_value = __( $enum[ $slug ], 'wpmovielibrary' );
			}

			if ( $options['show_icon'] ) {
				$icon = '<span class="wpmolicon icon-' . $slug . '"></span>&nbsp;';
			} else {
				$icon = false;
			}

			if ( $options['is_link'] ) {

				$options['content'] = __( $enum[ $slug ], 'wpmovielibrary' );
				$options['title']   = sprintf( __( 'Movies filed as “%s”', 'wpmovielibrary' ), __( $enum[ $slug ], 'wpmovielibrary' ) );

				/**
				 * Filter meta permalink.
				 *
				 * @since 3.0.0
				 *
				 * @param string $link    HTML link.
				 * @param string $url     Permalink URL.
				 * @param string $title   Permalink title attribute.
				 * @param string $content Permalink content.
				 */
				$filtered_value = apply_filters( "wpmoly/filter/detail/{$detail}/url", $slug, $options );

			} else {

				/**
				 * Filter single detail value.
				 *
				 * This is used to generate permalinks for details and can be extended to
				 * post-formatting modifications.
				 *
				 * @since 3.0.0
				 *
				 * @param string $filtered_value Filtered detail value.
				 * @param string $slug           Detail slug value.
				 * @param array  $options        Formatting options.
				 */
				$filtered_value = apply_filters( "wpmoly/filter/detail/{$detail}/single", $filtered_value, $slug, $options );
			}

			$value[ $key ] = $icon . $filtered_value;
		} // End if().
	} // End foreach().

	/**
	 * Filter final detail value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $filtered_value Filtered detail value.
	 * @param string $value          Detail slug value.
	 * @param array  $options        Formatting options.
	 */
	return apply_filters( "wpmoly/filter/detail/{$detail}", implode( ', ', $value ), $value, $options );
}

/**
 * Format movie empty fields.
 *
 * This is used by almost every other formatting function to filter and replace
 * empty values.
 *
 * @since 3.0.0
 *
 * @param string $value field value
 *
 * @return string Formatted value
 */
function format_empty_value( $value ) {

	if ( ! empty( $value ) ) {
		return $value;
	}

	/**
	 * Filter empty meta value.
	 *
	 * Use a long dash for replacer.
	 *
	 * @param string $value Empty value replacer
	 */
	return apply_filters( 'wpmoly/filter/meta/empty/value', '&mdash;' );
}

/**
 * Format languages.
 *
 * Support variants for spoken languages (meta), language (detail) and subtitles
 * (also detail). Default is 'spoken_languages'.
 *
 * @since 3.0.0
 *
 * @param string $languages Movie languages.
 * @param array  $options   Formatting options.
 *
 * @return string Formatted value
 */
function format_language( $languages, $options = array() ) {

	// Parse formatting options
	$options = wp_parse_args( (array) $options, array(
		'show_icon' => true,
		'show_text' => true,
		'is_link'   => true,
		'variant'   => 'spoken_languages',
	) );

	if ( empty( $languages ) ) {

		/**
		 * Filter empty meta value.
		 *
		 * @param string $value Replaced empty value.
		 */
		return apply_filters( "wpmoly/filter/meta/empty/{$options['variant']}/value", format_empty_value( $languages ) );
	}

	$show_text = _is_bool( $options['show_text'] );
	$show_icon = _is_bool( $options['show_icon'] );

	if ( is_string( $languages ) ) {
		$languages = explode( ',', $languages );
	}

	$languages_data = array();

	foreach ( $languages as $key => $language ) {

		$language = get_language( $language );
		$languages_data[ $key ] = $language;

		if ( ! $show_text ) {
			$name = '';
		} elseif ( '1' == wpmoly_o( 'translate-languages' ) ) {
			$name = $language->localized_name;
		} else {
			$name = $language->standard_name;
		}

		if ( $show_icon ) {
			$icon = '<span class="wpmoly language iso icon" title="' . esc_attr( $language->localized_name ) . ' (' . esc_attr( $language->standard_name ) . ')">' . esc_attr( $language->code ) . '</span>&nbsp;';
		} else {
			$icon = '';
		}

		if ( $options['is_link'] ) {

			$options['language'] = $language;

			if ( 'language' == $options['variant'] ) {
				$options['variant'] = 'language';
				$options['title'] = sprintf( __( '%s-dubbed movies', 'wpmovielibrary' ), $language->localized_name );

				/**
				 * Filter language URL.
				 *
				 * @since 3.0.0
				 *
				 * @param string $language Language value.
				 * @param array  $options  Formatting options.
				 */
				$name = apply_filters( 'wpmoly/filter/detail/language/url', $name, $options );

			} elseif ( 'subtitles' == $options['variant'] ) {
				$options['variant'] = 'subtitles';
				$options['title'] = sprintf( __( '%s-subtitled movies', 'wpmovielibrary' ), $language->localized_name );

				/**
				 * Filter subtitles URL.
				 *
				 * @since 3.0.0
				 *
				 * @param string $subtitles Language value.
				 * @param array  $options   Formatting options.
				 */
				$name = apply_filters( 'wpmoly/filter/detail/subtitles/url', $name, $options );

			} else {
				$options['variant'] = 'spoken_languages';
				$options['title'] = sprintf( __( '%s-speaking movies', 'wpmovielibrary' ), $language->localized_name );

				/**
				 * Filter spoken language URL.
				 *
				 * @since 3.0.0
				 *
				 * @param string $language Language value.
				 * @param array  $options  Formatting options.
				 */
				$name = apply_filters( 'wpmoly/filter/meta/spoken_languages/url', $name, $options );
			} // End if().
		} else {
			/**
			 * Filter single language meta value.
			 *
			 * @since 3.0.0
			 *
			 * @param string $language      Filtered language.
			 * @param array  $language_data Language instance.
			 * @param string $icon          Language icon string.
			 * @param array  $options       Formatting options.
			 */
			$name = apply_filters( "wpmoly/filter/meta/{$options['variant']}/single", $name, $language, $icon, $options );
		} // End if().

		$languages[ $key ] = $icon . $name;
	} // End foreach().

	/**
	 * Filter final languages lists.
	 *
	 * This is used to generate permalinks for languages and can be extended to
	 * post-formatting modifications.
	 *
	 * @since 3.0.0
	 *
	 * @param string $languages      Filtered languages list.
	 * @param array  $languages_data Languages data array.
	 * @param array  $options        Formatting options.
	 */
	return apply_filters( "wpmoly/filter/meta/{$options['variant']}", implode( ', ', $languages ), $languages_data, $options );
}

/**
 * Format movie misc actors/genres list depending on
 * existing terms.
 *
 * This is used to provide links for actors and genres lists
 * by using the metadata lists instead of taxonomies. But since
 * actors and genres can be added to the metadata and not terms,
 * we rely on metadata to show a correct list.
 *
 * @since 3.0.0
 *
 * @param string $terms    Field value.
 * @param string $taxonomy Taxonomy we're dealing with.
 * @param array  $options  Formatting options.
 *
 * @return string Formatted value
 */
function format_terms_list( $terms, $taxonomy, $options = array() ) {

	if ( empty( $terms ) ) {
		return format_empty_value( $terms );
	}

	$options = wp_parse_args( (array) $options, array(
		'is_link' => true,
	) );

	if ( is_string( $terms ) ) {
		$terms = explode( ',', $terms );
	}

	foreach ( $terms as $key => $term ) {

		$term = trim( str_replace( array( '&#039;', '’' ), "'", $term ) );

		if ( ! taxonomy_exists( $taxonomy ) ) {
			$t = $term;
		} else {
			$t = get_term_by( 'name', $term, $taxonomy );
			if ( ! $t ) {
				$t = get_term_by( 'slug', sanitize_title( $term ), $taxonomy );
			}
		}

		if ( ! $t ) {
			$t = $term;
		}

		if ( is_object( $t ) && '' != $t->name ) {
			if ( true === $options['is_link'] ) {
				$link = get_term_link( $t, $taxonomy );
				if ( ! is_wp_error( $link ) ) {
					$t = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $link, sprintf( __( 'More movies from %s', 'wpmovielibrary' ), $t->name ), $t->name );
				} else {
					$t = $t->name;
				}
			} else {
				$t = $t->name;
			}
		}

		$terms[ $key ] = $t;
	}

	if ( empty( $terms ) ) {
		return '';
	}

	return implode( ', ', $terms );
}
