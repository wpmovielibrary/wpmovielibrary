<?php
/**
 * The file that defines the plugin permalinks functions.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\helpers;

/**
 * Generate basic movie meta permalink.
 *
 * @since 3.0.0
 *
 * @param string $meta Meta name.
 * @param string $value Meta value.
 *
 * @return string
 */
function generate_movie_meta_url( $meta, $value ) {

	$meta = sanitize_key( $meta );
	$meta = str_replace( '_', '-', $meta );

	$value = sanitize_title_with_dashes( $value );

	$url = get_movie_archive_link() . $meta . '/' . $value;

	/**
	 * Filter a movie meta URL.
	 *
	 * @since 3.0.0
	 *
	 * @param string $url Meta URL.
	 * @param string $meta Meta key.
	 * @param string $value Meta value.
	 */
	return apply_filters( "wpmoly/filter/permalink/{$meta}/{$value}/url", trailingslashit( $url ), $meta, $value );
}

/**
 * Build a permalink for adult restrictions.
 *
 * @since 3.0.0
 *
 * @param string $content Movie adult restriction text.
 * @param boolean $is_adult Adult restriction?
 *
 * @return string
 */
function get_movie_adult_url( $adult, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content'  => '',
		'title'    => '',
		'is_adult' => false,
	) );

	$adult = (string) $adult;

	if ( true === $options['is_adult'] ) {
		$value = 'yes';
		if ( empty( $options['content'] ) ) {
			$options['content'] = __( 'Yes', 'wpmovielibrary' );
		}
		if ( empty( $options['title'] ) ) {
			$options['title'] = __( 'Adults-only movies', 'wpmovielibrary' );
		}
	} else {
		$value = 'no';
		if ( empty( $options['content'] ) ) {
			$options['content'] = __( 'No', 'wpmovielibrary' );
		}
		if ( empty( $options['title'] ) ) {
			$options['title'] = __( 'All-audience movies', 'wpmovielibrary' );
		}
	}

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/adult/slug', _x( 'adult', 'adult permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $value );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter adult restriction permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $content Default text.
	 * @param boolean $is_adult Adult restriction?
	 */
	return apply_filters( 'wpmoly/filter/permalink/adult', $permalink, $options['content'], $options['is_adult'] );
}

/**
 * Build a permalink for author.
 *
 * @since 3.0.0
 *
 * @param string $author Movie author.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_author_url( $author, $options = array() ) {

	$author = (string) $author;
	$options = wp_parse_args( (array) $options, array(
		'content' => $author,
		'title'   => sprintf( __( 'Movies from author %s', 'wpmovielibrary' ), $author ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/author/slug', _x( 'author', 'author permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $author );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter author permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $author Movie author.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/author', $permalink, $author, $options );
}

/**
 * Build a permalink for budget.
 *
 * @since 3.0.0
 *
 * @param string $budget Movie budget.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_budget_url( $budget, $options = array() ) {

	$budget = (string) $budget;
	$options = wp_parse_args( (array) $options, array(
		'content' => $budget,
		'title'   => sprintf( __( 'Movies of %s budget', 'wpmovielibrary' ), $budget ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/budget/slug', _x( 'budget', 'budget permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $budget );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter budget permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $budget Movie budget.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/budget', $permalink, $budget, $options );
}

/**
 * Build a permalink for certifications.
 *
 * @since 3.0.0
 *
 * @param string $certification Movie certification.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_certification_url( $certification, $options = array() ) {

	$certification = (string) $certification;
	$options = wp_parse_args( (array) $options, array(
		'content' => $certification,
		'title'   => sprintf( __( '“%s” rated movies', 'wpmovielibrary' ), $certification ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/certification/slug', _x( 'certification', 'certification permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $certification );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter certification permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param int $certification Movie certification.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/certification', $permalink, $certification, $options );
}

/**
 * Build a permalink for original music composers.
 *
 * @since 3.0.0
 *
 * @param string $composer Movie original music composer.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_composer_url( $composer, $options = array() ) {

	$composer = (string) $composer;
	$options = wp_parse_args( (array) $options, array(
		'content' => $composer,
		'title'   => sprintf( __( 'Movies from original music composer %s', 'wpmovielibrary' ), $composer ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/composer/slug', _x( 'composer', 'composer permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $composer );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter composer permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $composer Movie composer.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/composer', $permalink, $composer, $options );
}

/**
 * Build a permalink for format.
 *
 * @since 3.0.0
 *
 * @param string $format Movie format.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_format_url( $format, $options = array() ) {

	$format = (string) $format;
	$options = wp_parse_args( (array) $options, array(
		'content' => $format,
		'title'   => sprintf( _x( '%s movies', 'movie format', 'wpmovielibrary' ), $format ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/format/slug', _x( 'format', 'format permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $format );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter  permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $format Movie format.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/format', $permalink, $format, $options );
}

/**
 * Build a permalink for movie homepages.
 *
 * @since 3.0.0
 *
 * @param string $homepage Movie homepage.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_homepage_url( $homepage, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => '',
	) );

	$homepage = (string) $homepage;

	if ( empty( $options['content'] ) ) {
		$options['content'] = str_replace( array( 'http://', 'https://' ), '', untrailingslashit( $homepage ) );
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = __( 'Official movie website', 'wpmovielibrary' );
	}

	$permalink = '<a href="' . esc_url( $homepage ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter movie homepage permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param int $homepage Movie homepage.
	 * @param arrat $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/homepage', $permalink, $homepage, $options );
}

/**
 * Build an external link for IMDb IDs.
 *
 * @since 3.0.0
 *
 * @param string $content Movie IMDb ID.
 * @param boolean $options Permalink options.
 *
 * @return string
 */
function get_movie_imdb_id_url( $imdb_id, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => '',
	) );

	$imdb_id = (string) $imdb_id;

	if ( empty( $options['target'] ) ) {
		$options['target'] = '_blank';
	}

	if ( empty( $options['content'] ) ) {
		$options['content'] = $imdb_id;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = __( 'Movie page on IMDb.com', 'wpmovielibrary' );
	}

	$url = sprintf( 'https://www.imdb.com/title/%s/', $imdb_id );

	$permalink = '<a href="' . esc_url( $url ) . '" target="' . $options['target'] . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter movie IMDb ID permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param int $imdb_id Movie IMDb ID.
	 * @param arrat $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/imdb_id', $permalink, $imdb_id, $options );
}

/**
 * Build a permalink for languages.
 *
 * @since 3.0.0
 *
 * @param string $language Movie language.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_language_url( $language, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content'  => '',
		'title'    => '',
		'language' => '',
		'variant'  => 'spoken_languages',
	) );

	if ( ! $options['language'] instanceof \wpmoly\helpers\Language ) {
		return $language;
	}

	$language_object = $options['language'];

	if ( empty( $options['content'] ) ) {
		$options['content'] = $language;
	}

	if ( 'language' == $options['variant'] ) {
		$slug = _x( 'language', 'language permalink slug', 'wpmovielibrary' );
		if ( empty( $options['title'] ) ) {
			$options['title'] = sprintf( __( '%s-dubbed movies', 'wpmovielibrary' ), $language_object->localized_name );
		}
	} elseif ( 'subtitles' == $options['variant'] ) {
		$slug = _x( 'subtitles', 'subtitles permalink slug', 'wpmovielibrary' );
		if ( empty( $options['title'] ) ) {
			$options['title'] = sprintf( __( '%s-subtitled movies', 'wpmovielibrary' ), $language_object->localized_name );
		}
	} else {
		$slug = _x( 'languages', 'spoken languages permalink slug', 'wpmovielibrary' );
		if ( empty( $options['title'] ) ) {
			$options['title'] = sprintf( __( '%s-speaking movies', 'wpmovielibrary' ), $language_object->localized_name );
		}
	}

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/language/slug', $slug );

	$url = generate_movie_meta_url( $slug, $language_object->localized_name );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter single language permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $language Movie language object.
	 * @param array $options Formatting options.
	 */
	return apply_filters( "wpmoly/filter/permalink/{$options['variant']}", $permalink, $language_object, $options );
}

/**
 * Build a permalink for local release dates.
 *
 * Alias for get_movie_date_url().
 *
 * @since 3.0.0
 *
 * @param string $local_release_date Movie release date.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_local_release_date_url( $local_release_date, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = 'local_';

	return get_movie_date_url( $local_release_date, $options );
}

/**
 * Build a permalink for medias.
 *
 * @since 3.0.0
 *
 * @param string $media Movie media.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_media_url( $media, $options = array() ) {

	$media = (string) $media;
	$options = wp_parse_args( (array) $options, array(
		'content' => $media,
		'title'   => sprintf( _x( '%s movies', 'movie media', 'wpmovielibrary' ), $media ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/media/slug', _x( 'media', 'media permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $media );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter media permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $media Movie media.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/media', $permalink, $media, $options );
}

/**
 * Build a permalink for release dates.
 *
 * Alias for get_movie_date_url().
 *
 * @since 3.0.0
 *
 * @param string $release_date Movie release date.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_release_date_url( $release_date, $options = array() ) {

	return get_movie_date_url( $release_date, $options );
}

/**
 * Build a permalink for dates.
 *
 * A bunch of different, basic formats are supported. US/UK -formatted dates will
 * link to monthly archives while French-formatted dates will be splited to link
 * to monthly and yearly archives.
 *
 * Support a 'local_' variant for local release dates.
 *
 * @since 3.0.0
 *
 * @param string $release_date Movie release date.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_date_url( $date, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'format'  => '',
		'variant' => '',
	) );

	$date = (string) $date;
	$timestamp = strtotime( $date );

	if ( empty( $options['content'] ) ) {
		$options['content'] = $date;
	}

	if ( 'local_' == $options['variant'] ) {
		$slug = _x( 'local-release', ' permalink slug', 'wpmovielibrary' );
	} else {
		$slug = _x( 'release', ' permalink slug', 'wpmovielibrary' );
	}

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/date/slug', $slug );

	switch ( $options['format'] ) {
		case 'Y':
			if ( 'local_' == $options['variant'] ) {
				$options['title'] = sprintf( __( 'Movies locally released in %s', 'wpmovielibrary' ), date_i18n( 'Y', $timestamp ) );
				$url = generate_movie_meta_url( $slug, date( 'Y', $timestamp ) );
			} else {
				$options['title'] = sprintf( __( 'Movies released in %s', 'wpmovielibrary' ), date_i18n( 'Y', $timestamp ) );
				$url = generate_movie_meta_url( $slug, date( 'Y', $timestamp ) );
			}

			$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';
			break;
		case 'j F Y':
			$permalink = array();

			$month = date_i18n( 'j F', $timestamp );
			if ( 'local_' == $options['variant'] ) {
				$options['title'] = sprintf( __( 'Movies locally released in %s', 'wpmovielibrary' ), $month );
				$url = generate_movie_meta_url( $slug, date( 'Y-m', $timestamp ) );
			} else {
				$options['title'] = sprintf( __( 'Movies released in %s', 'wpmovielibrary' ), $month );
				$url = generate_movie_meta_url( $slug, date( 'Y-m', $timestamp ) );
			}
			$permalink[] = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $month ) . '</a>';

			$year = date_i18n( 'Y', $timestamp );
			if ( 'local_' == $options['variant'] ) {
				$options['title'] = sprintf( __( 'Movies locally released in %s', 'wpmovielibrary' ), $year );
				$url = generate_movie_meta_url( $slug, date( 'Y', $timestamp ) );
			} else {
				$options['title'] = sprintf( __( 'Movies released in %s', 'wpmovielibrary' ), $year );
				$url = generate_movie_meta_url( $slug, date( 'Y', $timestamp ) );
			}
			$permalink[] = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $year ) . '</a>';

			$permalink = implode( '&nbsp;', $permalink );
			break;
		case 'Y-m-d':
		case 'm/d/Y':
		case 'd/m/Y':
		default:
			if ( 'local_' == $options['variant'] ) {
				$options['title'] = sprintf( __( 'Movies locally released in %s', 'wpmovielibrary' ), date_i18n( 'F Y', $timestamp ) );
				$url = generate_movie_meta_url( $slug, date( 'Y-m-d', $timestamp ) );
			} else {
				$options['title'] = sprintf( __( 'Movies released in %s', 'wpmovielibrary' ), date_i18n( 'F Y', $timestamp ) );
				$url = generate_movie_meta_url( $slug, date( 'Y-m-d', $timestamp ) );
			}

			$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';
			break;
	} // End switch().

	/**
	 * Filter date permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $date Movie date.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/date', $permalink, $date, $options );
}

/**
 * Build a permalink for directors of photography.
 *
 * @since 3.0.0
 *
 * @param string $photographer Movie director of photography.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_photography_url( $photographer, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => '',
	) );

	$photographer = (string) $photographer;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $photographer;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( __( 'Movies from director of photography %s', 'wpmovielibrary' ), $photographer );
	}

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/photography/slug', _x( 'photography', 'photography permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $photographer );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter  permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $photographer Movie director of photography.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/photography', $permalink, $photographer, $options );
}

/**
 * Build a permalink for producers.
 *
 * @since 3.0.0
 *
 * @param string $producer Movie producer.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_producer_url( $producer, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => '',
	) );

	$producer = (string) $producer;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $producer;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( _x( 'Movies produced by %s', 'producer', 'wpmovielibrary' ), $producer );
	}

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/producer/slug', _x( 'producer', 'producer permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $producer );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter  permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $producer Movie producer.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/producer', $permalink, $producer, $options );
}

/**
 * Build a permalink for production companies.
 *
 * @since 3.0.0
 *
 * @param string $production_company Movie production company.
 * @param array  $options            Permalink options.
 *
 * @return string
 */
function get_movie_production_companies_url( $production_company, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => '',
	) );

	$production_company = (string) $production_company;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $production_company;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( _x( 'Movies produced by %s', 'production company', 'wpmovielibrary' ), $production_company );
	}

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/production_companies/slug', _x( 'company', 'production companies permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $production_company );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter  permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink          Permalink HTML output.
	 * @param string $production_company Movie production company.
	 * @param array  $options            Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/production_company', $permalink, $production_company, $options );
}

/**
 * Build a permalink for production countries.
 *
 * @since 3.0.0
 *
 * @param string $production_country Production country.
 * @param array  $options            Permalink options.
 *
 * @return string
 */
function get_movie_production_countries_url( $production_country, $options = array() ) {

	if ( ! $production_country instanceof \wpmoly\helpers\Country ) {
		return $production_country;
	}

	$options = wp_parse_args( (array) $options, array(
		'content' => $production_country->localized_name,
		'title'   => sprintf( __( 'Movies produced in: %1$s (%2$s)', 'wpmovielibrary' ), $production_country->localized_name, $production_country->standard_name ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/production_countries/slug', _x( 'country', 'production-countries permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $production_country->localized_name );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter single country permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink          Permalink HTML output.
	 * @param string $production_country Movie production country object.
	 * @param array  $options            Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/production_country', $permalink, $production_country, $options );
}

/**
 * Build a permalink for ratings.
 *
 * @since 3.0.0
 *
 * @param string $rating Movie rating.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_rating_url( $rating, $options = array() ) {

	$rating = (string) $rating;
	$options = wp_parse_args( (array) $options, array(
		'content' => $rating,
		'title'   => sprintf( _x( 'Movies rated %s', 'movie rating', 'wpmovielibrary' ), $rating ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/rating/slug', _x( 'rating', 'rating permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, floatval( $rating ) );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . $options['content'] . '</a>';

	/**
	 * Filter  permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $rating Movie rating.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/rating', $permalink, $rating, $options );
}

/**
 * Build a permalink for revenues.
 *
 * @since 3.0.0
 *
 * @param string $revenue Movie revenue.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_revenue_url( $revenue, $options = array() ) {

	$revenue = (string) $revenue;
	$options = wp_parse_args( (array) $options, array(
		'content' => $revenue,
		'title'   => sprintf( __( 'Movies of %s revenue', 'wpmovielibrary' ), $revenue ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/revenue/slug', _x( 'revenue', 'revenue permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $revenue );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter revenue permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $revenue Movie revenue.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/revenue', $permalink, $revenue, $options );
}

/**
 * Build a permalink for spoken languages.
 *
 * @since 3.0.0
 *
 * @param string $spoken_languages Movie spoken languages.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_spoken_languages_url( $spoken_languages, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = 'spoken_languages';

	return get_movie_language_url( $spoken_languages, $options );
}

/**
 * Build a permalink for statuses.
 *
 * @since 3.0.0
 *
 * @param string $status Movie status.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_status_url( $status, $options = array() ) {

	$status = (string) $status;
	$options = wp_parse_args( (array) $options, array(
		'content' => $status,
		'title'   => sprintf( _x( '%s movies', 'movie status', 'wpmovielibrary' ), $status ),
	) );

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/status/slug', _x( 'status', 'status permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $status );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter  permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $status Movie status.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/status', $permalink, $status, $options );
}

/**
 * Build a permalink for subtitles.
 *
 * @since 3.0.0
 *
 * @param string $subtitles Movie subtitles.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_subtitles_url( $subtitles, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = 'subtitles';

	return get_movie_language_url( $subtitles, $options );
}

/**
 * Build an external link for TMDb IDs.
 *
 * @since 3.0.0
 *
 * @param string $content Movie TMDb ID.
 * @param boolean $options Permalink options.
 *
 * @return string
 */
function get_movie_tmdb_id_url( $tmdb_id, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => '',
	) );

	$tmdb_id = absint( $tmdb_id );

	if ( empty( $options['target'] ) ) {
		$options['target'] = '_blank';
	}

	if ( empty( $options['content'] ) ) {
		$options['content'] = $tmdb_id;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = __( 'Movie page on TheMovieDB.org', 'wpmovielibrary' );
	}

	$url = sprintf( 'https://www.themoviedb.org/movie/%d', $tmdb_id );

	$permalink = '<a href="' . esc_url( $url ) . '" target="' . $options['target'] . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter movie TMDb ID permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param int $tmdb_id Movie TMDb ID.
	 * @param arrat $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/tmdb_id', $permalink, $tmdb_id, $options );
}

/**
 * Build a permalink for writers.
 *
 * @since 3.0.0
 *
 * @param string $writer Movie writer.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_writer_url( $writer, $options = array() ) {

	$options = wp_parse_args( (array) $options, array(
		'content' => '',
		'title'   => '',
	) );

	$writer = (string) $writer;

	if ( empty( $options['content'] ) ) {
		$options['content'] = $writer;
	}

	if ( empty( $options['title'] ) ) {
		$options['title'] = sprintf( __( 'Movies from writer %s', 'wpmovielibrary' ), $writer );
	}

	/**
	 * Filter permalink slug.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug Default slug
	 */
	$slug = apply_filters( 'wpmoly/filter/permalink/writer/slug', _x( 'writer', 'writer permalink slug', 'wpmovielibrary' ) );

	$url = generate_movie_meta_url( $slug, $writer );

	$permalink = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $options['title'] ) . '">' . esc_html( $options['content'] ) . '</a>';

	/**
	 * Filter writer permalink.
	 *
	 * @since 3.0.0
	 *
	 * @param string $permalink Permalink HTML output.
	 * @param string $writer Movie writer.
	 * @param array $options Formatting options.
	 */
	return apply_filters( 'wpmoly/filter/permalink/writer', $permalink, $writer, $options );
}

/**
 * Build a permalink for years.
 *
 * @since 3.0.0
 *
 * @param string $writer Movie writer.
 * @param array $options Permalink options.
 *
 * @return string
 */
function get_movie_year_url( $year, $options = array() ) {

	$options = (array) $options;
	$options['variant'] = '';
	$options['format']  = 'Y';
	$options['content'] = $year;

	return get_movie_date_url( "$year-01-01", $options );
}
