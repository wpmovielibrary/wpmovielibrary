<?php

namespace wpmoly\Formatting;

/**
 * Format a Movie's director for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function director( $data ) {

	$output = terms_list( $data, 'collection' );
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's genres for display
 * 
 * Match each genre against the genre taxonomy to detect missing
 * terms. If term genre exists, provide a link, raw text value
 * if no matching term could be found.
 * 
 * @since    3.0
 * 
 * @param    string    $genres field value
 * 
 * @return   string    Formatted output
 */
function genres( $genres ) {

	$genres = terms_list( $genres, 'genre' );

	return filter_empty( $genres );
}

/**
 * Format a Movie's casting for display
 * 
 * Match each actor against the actor taxonomy to detect missing
 * terms. If term actor exists, provide a link, raw text value
 * if no matching term could be found.
 * 
 * @since    3.0
 * 
 * @param    string    $actors field value
 * 
 * @return   string    Formatted output
 */
function cast( $actors ) {

	$actors = terms_list( $actors,  'actor' );

	return filter_empty( $actors );
}

/**
 * Format a Movie's release date for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function release_date( $date, $date_format = null ) {

	if ( empty( $date ) ) {
		return filter_empty( $date );
	}

	$timestamp  = strtotime( $date );
	$date_parts = array();

	if ( ! is_null( $date_format ) ) {
		$format = (string) $date_format;
	} else {
		$format = wpmoly_o( 'format-date' );
	}

	if ( empty( $format ) ) {
		$format = 'j F Y';
	}

	if ( 'j F Y' == $date_format ) {
		$date_href = date( 'Y-m', $timestamp );
		$date_parts[] = date_i18n( 'j F', $timestamp );
		$date_parts[] = date_i18n( 'Y', $timestamp );
		$output = implode( '&nbsp;', $date_parts );
	} else {
		$output = date_i18n( $format, $timestamp );
	}

	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's runtime for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function runtime( $data, $format = null ) {

	$data = intval( $data );
	if ( ! $data ) {
		return filter_empty( __( 'Duration unknown', 'wpmovielibrary' ) );
	}

	if ( is_null( $format ) ) {
		$format = wpmoly_o( 'format-time' );
	}

	if ( '' == $format ) {
		$format = 'G \h i \m\i\n';
	}

	$output = date_i18n( $format, mktime( 0, $data ) );
	if ( false !== stripos( $output, 'am' ) || false !== stripos( $output, 'pm' ) ) {
		$output = date_i18n( 'G:i', mktime( 0, $data ) );
	}

	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's languages for display
 * 
 * $text and $icon parameters are essentially used by language and subtitles
 * details.
 * 
 * @since    3.0
 * 
 * @param    string     $languages Languages
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted output
 */
function spoken_languages( $languages, $text = true, $icon = false ) {

	if ( empty( $languages ) ) {
		return $languages;
	}

	if ( is_string( $languages ) ) {
		$languages = explode( ',', $languages );
	}

	$output = array();
	foreach ( $languages as $language ) {

		$language = get_language( $language );
		if ( true !== $text ) {
			$name = '';
		} elseif ( '1' == wpmoly_o( 'translate-languages' ) ) {
			$name = $language->localized_name;
		} else {
			$name = $language->standard_name;
		}

		if ( true === $icon ) {
			$name = '<span class="wpmoly language iso icon" title="' . esc_attr( $language->standard_name ) . '">' . esc_attr( $language->code ) . '</span>&nbsp;' . $name;
		}

		/*$url = apply_filters( 'wpmoly_movie_meta_link', array(
			'key'   => 'spoken_languages',
			'value' => $language,
			'type'  => 'meta',
			'text'  => $title,
			'title' => sprintf( __( 'More movies in %s', 'wpmovielibrary' ), $title )
		) );*/
		$output[] = $name;
	}

	$output = implode( ', ', $output );
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's countries for display.
 * 
 * @since    3.0
 * 
 * @param    string     $countries Countries list
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted output
 */
function production_countries( $countries, $icon = false ) {

	if ( empty( $countries ) ) {
		return $countries;
	}

	if ( '1' == wpmoly_o( 'translate-countries' ) ) {
		$formats = wpmoly_o( 'countries-format', array() );
	} elseif ( false === $icon ) {
		$formats = array( 'flag', 'original' );
	} else {
		$formats = array( 'original' );
	}

	$output = array();
	$countries = explode( ',', $countries );
	foreach ( $countries as $country ) {

		$country = get_country( $country );

		$items = array();
		foreach ( $formats as $format ) {

			switch ( $format ) {
				case 'flag':
					$item = $country->flag();
					break;
				case 'original':
					$item = $country->standard_name;
					break;
				case 'translated':
					$item = $country->localized_name;
					break;
				case 'ptranslated':
					$item = sprintf( '(%s)', $country->localized_name );
					break;
				case 'poriginal':
					$item = sprintf( '(%s)', $country->standard_name );
					break;
				default:
					$item = '';
					break;
			}

			//if ( 'flag' != $format && ! empty( $item ) ) {
				/*$item = apply_filters( 'wpmoly_movie_meta_link', array(
					'key'   => 'production_countries',
					'value' => $country->standard_name,
					'type'  => 'meta',
					'text'  => $item,
					'title' => sprintf( __( 'More movies from country %s', 'wpmovielibrary' ), $item )
				) );*/
			//}

			$items[] = $item;
		}

		$output[] = implode( '&nbsp;', $items );
	}

	$output = implode( ',&nbsp; ', $output );
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's production companies for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function production_companies( $data ) {

	$output = array();

	$data = explode( ',', $data );
	$data = array_map( 'trim', $data );

	foreach ( $data as $d ) {
		$output[] = $d;/*apply_filters( 'wpmoly_movie_meta_link', array(
			'key'   => 'production_companies',
			'value' => $d,
			'type'  => 'meta',
			'text'  => $d,
			'title' => sprintf( __( 'More movies produced by %s', 'wpmovielibrary' ), $d )
		) );*/
	}

	if ( ! empty( $output ) )
		$output = implode( ', ', $output );

	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's producer for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function producer( $data ) {

	$output = array();

	$data = explode( ',', $data );
	$data = array_map( 'trim', $data );

	foreach ( $data as $d ) {
		$output[] = $d;/*apply_filters( 'wpmoly_movie_meta_link', array(
			'key'   => 'producer',
			'value' => $d,
			'type'  => 'meta',
			'text'  => $d,
			'title' => sprintf( __( 'More movies produced by %s', 'wpmovielibrary' ), $d )
		) );*/
	}

	if ( ! empty( $output ) )
		$output = implode( ', ', $output );

	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's composer for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function composer( $data ) {

	$output = $data;/*apply_filters( 'wpmoly_movie_meta_link', array(
		'key'   => 'composer',
		'value' => $data,
		'type'  => 'meta',
		'text'  => $data,
		'title' => sprintf( __( 'More movies from composer %s', 'wpmovielibrary' ), $data )
	) );*/
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's editor for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function editor( $data ) {

	$output = $data;/*apply_filters( 'wpmoly_movie_meta_link', array(
		'key'   => 'editor',
		'value' => $data,
		'type'  => 'meta',
		'text'  => $data,
		'title' => sprintf( __( 'More movies edited by %s', 'wpmovielibrary' ), $data )
	) );*/
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's author for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function author( $data ) {

	$output = $data;/*apply_filters( 'wpmoly_movie_meta_link', array(
		'key'   => 'author',
		'value' => $data,
		'type'  => 'meta',
		'text'  => $data,
		'title' => sprintf( __( 'More movies from author %s', 'wpmovielibrary' ), $data )
	) );*/
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's director of photography for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function photography( $data ) {

	$output = $data;/*apply_filters( 'wpmoly_movie_meta_link', array(
		'key'   => 'photography',
		'value' => $data,
		'type'  => 'meta',
		'text'  => $data,
		'title' => sprintf( __( 'More movies for director of photography %s', 'wpmovielibrary' ), $data )
	) );*/
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's certification for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function certification( $data ) {

	$output = $data;/*apply_filters( 'wpmoly_movie_meta_link', array(
		'key'   => 'certification',
		'value' => $data,
		'type'  => 'meta',
		'text'  => $data,
		'title' => sprintf( __( 'More movies certified %s', 'wpmovielibrary' ), $data )
	) );*/
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a money value for display
 * 
 * @since    3.0
 * 
 * @param    string    $money field value
 * 
 * @return   string    Formatted output
 */
function money( $money ) {

	$money = intval( $money );
	if ( ! $money ) {
		return '';
	}

	$money = '$' . number_format_i18n( $money );

	return filter_empty( $money );
}

/**
 * Format a Movie's adult status for display
 * 
 * @since    3.0
 * 
 * @param    string    $adult field value
 * 
 * @return   string    Formatted output
 */
function adult( $adult ) {

	if ( empty( $adult ) ) {
		$adult = '';
	} elseif ( _is_bool( $adult ) ) {
		$adult = __( 'Yes', 'wpmovielibrary' );
	} else {
		$adult = __( 'No', 'wpmovielibrary' );
	}

	return filter_empty( $adult );
}

/**
 * Format movie homepage link.
 * 
 * @since    3.0
 * 
 * @param    string    $homepage Homepage link
 * 
 * @return   string    Formatted output
 */
function homepage( $homepage ) {

	if ( ! empty( $homepage ) ) {
		$homepage = sprintf( '<a href="%1$s" title="%2$s">%1$s</a>', esc_url( $homepage ), __( 'Official Website', 'wpmovielibrary' ) );
	}

	return filter_empty( $homepage );
}

/**
 * Format a Movie's misc field for display
 * 
 * @since    3.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function filter_empty( $data ) {

	if ( ! empty( $data ) ) {
		return $data;
	}

	// Long dash
	$data = '&mdash;';

	/**
	 * Filter empty meta value.
	 * 
	 * @param    string    $data Empty value replacer
	 */
	return apply_filters( 'wpmoly/filter/meta/empty/value', $data );
}

/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *                             Aliases
 * 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

function actors( $actors ) {
	return cast( $actors );
}

function date( $date, $format = null ) {
	return release_date( $date, $format );
}

function local_release_date( $date, $format = null ) {
	return release_date( $date, $format );
}

function year( $date ) {
	return release_date( $date, $format = 'Y' );
}

function languages( $languages, $text = true, $icon = false ) {
	return spoken_languages( $languages, $text, $icon );
}

function countries( $countries, $icon = false ) {
	return production_countries( $countries );
}

function production( $companies ) {
	return production_companies( $companies );
}

function budget( $budget ) {
	return money( $budget );
}

function revenue( $revenue ) {
	return money( $revenue );
}

/**
 * Format movies media.
 * 
 * @since    3.0
 * 
 * @param    string     $medias Movie media
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted output
 */
function media( $media, $text = true, $icon = false ) {

	return detail( 'media', $media, $text, $icon );
}

/**
 * Format movies status.
 * 
 * @since    3.0
 * 
 * @param    array      $data Movie statuses
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted output
 */
function status( $statuses, $text = true, $icon = false ) {

	return detail( 'status', $statuses, $text, $icon );
}

/**
 * Format movies rating.
 * 
 * @since    3.0
 * 
 * @param    string     $rating Movie rating
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted output
 */
function rating( $rating, $text = true, $icon = false ) {

	/*$format = ( 'raw' == $format ? 'raw' : 'html' );

	if ( '' == $data )
		return $data;

	if ( 'html' == $format ) {
		$data = apply_filters( 'wpmoly_movie_rating_stars', $data );
		$data = WPMovieLibrary::render_template( 'shortcodes/rating.php', array( 'data' => $data ), $require = 'always' );
	}*/

	return $rating;
}

/**
 * Format movies language.
 * 
 * Alias for spoken_languages().
 * 
 * @since    3.0
 * 
 * @param    array      $languages Movie languages
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted output
 */
function language( $languages, $text = true, $icon = false ) {

	return spoken_languages( $languages, $text, $icon );
}

/**
 * Format movies subtitles.
 * 
 * Alias for spoken_languages() since subtitles are languages names.
 * 
 * @since    3.0
 * 
 * @param    array      $subtitles Movie subtitles
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted output
 */
function subtitles( $subtitles, $text = true, $icon = false ) {

	return spoken_languages( $subtitles, $text, $icon );
}

/**
 * Format movies format.
 * 
 * @since    3.0
 * 
 * @param    array      $format Movie formats
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted output
 */
function format( $format, $text = true, $icon = false ) {

	return detail( 'format', $format, $text, $icon );
}

/**
 * Format movies details.
 * 
 * @since    3.0
 * 
 * @param    string     $detail details slug
 * @param    array      $data detail value
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted output
 */
function detail( $detail, $data, $text = true, $icon = false ) {

	$data = (array) $data;
	if ( empty( $data ) ) {
		return $data;
	}

	$details = wpmoly_o( 'default_details' );
	if ( ! isset( $details[ $detail ]['options'] ) ) {
		return '';
	}

	$details = $details[ $detail ]['options'];
	foreach ( $data as $key => $slug ) {
		if ( isset( $details[ $slug ] ) ) {
			$value = '';
			if ( true === $text ) {
				$value = __( $details[ $slug ], 'wpmovielibrary' );
			}

			if ( true === $icon ) {
				$value = '<span class="wpmolicon icon-' . $slug . '"></span>&nbsp;' . $value;
			}

			$data[ $key ] = $value;
		}
	}

	return implode( ', ', $data );
}

/**
 * Generate rating stars block.
 * 
 * @since    3.0
 * 
 * @param    float      $rating movie to turn into stars
 * @param    int        $post_id movie's post ID
 * @param    int        $base 5-stars or 10-stars?
 * 
 * @return   string    Formatted output
 */
function get_rating_stars( $rating, $post_id = null, $base = null, $include_empty = false ) {

	/*$defaults = WPMOLY_Settings::get_supported_movie_details();

	if ( is_null( $post_id ) || ! intval( $post_id ) )
		$post_id = get_the_ID();

	if ( is_null( $base ) )
		$base = wpmoly_o( 'format-rating' );
	if ( 10 != $base )
		$base = 5;

	if ( 0 > $rating )
		$rating = 0.0;
	if ( 5.0 < $rating )
		$rating = 5.0;

	$title = '';
	if ( isset( $defaults['rating']['options'][ $rating ] ) )
		$title = $defaults['rating']['options'][ $rating ];

	$_rating = preg_replace( '/([0-5])(\.|_)(0|5)/i', '$1-$3', $rating );

	$class   = "wpmoly-movie-rating wpmoly-movie-rating-{$_rating}";

	$filled  = '<span class="wpmolicon icon-star-filled"></span>';
	$half    = '<span class="wpmolicon icon-star-half"></span>';
	$empty   = '<span class="wpmolicon icon-star-empty"></span>';

	$_filled = floor( $rating );
	$_half   = ceil( $rating - floor( $rating ) );
	$_empty  = ceil( 5.0 - ( $_filled + $_half ) );

	if ( 0.0 == $rating ) {
		if ( true === $include_empty ) {
			$stars  = '<div id="wpmoly-movie-rating-' . $post_id . '" class="' . $class . '" title="' . $title . '">';
			$stars .= str_repeat( $empty, 10 );
			$stars .= '</div>';
		} else {
			$stars  = '<div id="wpmoly-movie-rating-' . $post_id . '" class="not-rated" title="' . $title . '">';
			$stars .= sprintf( '<small><em>%s</em></small>', __( 'Not rated yet!', 'wpmovielibrary' ) );
			$stars .= '</div>';
		}
	}
	else if ( 10 == $base ) {
		$_filled = $rating * 2;
		$_empty  = 10 - $_filled;
		$title   = "{$_filled}/10 − {$title}";

		$stars  = '<div id="wpmoly-movie-rating-' . $post_id . '" class="' . $class . '" title="' . $title . '">';
		$stars .= str_repeat( $filled, $_filled );
		$stars .= str_repeat( $empty, $_empty );
		$stars .= '</div>';
	}
	else {
		$title   = "{$rating}/5 − {$title}";
		$stars  = '<div id="wpmoly-movie-rating-' . $post_id . '" class="' . $class . '" title="' . $title . '">';
		$stars .= str_repeat( $filled, $_filled );
		$stars .= str_repeat( $half, $_half );
		$stars .= str_repeat( $empty, $_empty );
		$stars .= '</div>';
	}*/

	/**
	 * Filter generated HTML markup.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    Stars HTML markup
	 * @param    float     Rating value
	 */
	/*$stars = apply_filters( 'wpmoly_movie_rating_stars_html', $stars, $rating );

	return $stars;*/

	return $rating;
}

/**
 * Format a Movie's misc actors/genres list depending on
 * existing terms.
 * 
 * This is used to provide links for actors and genres lists
 * by using the metadata lists instead of taxonomies. But since
 * actors and genres can be added to the metadata and not terms,
 * we rely on metadata to show a correct list.
 * 
 * @since    3.0
 * 
 * @param    string    $terms field value
 * @param    string    $taxonomy taxonomy we're dealing with
 * 
 * @return   string    Formatted output
 */
function terms_list( $terms, $taxonomy ) {

	$has_taxonomy = (boolean) wpmoly_o( "enable-{$taxonomy}" );

	if ( is_string( $terms ) ) {
		$terms = explode( ',', $terms );
	}

	foreach ( $terms as $key => $term ) {
		
		$term = trim( str_replace( array( '&#039;', "’" ), "'", $term ) );

		if ( ! $has_taxonomy ) {
			$t = $term;
		}
		else {
			$t = get_term_by( 'name', $term, $taxonomy );
			if ( ! $t ) {
				$t = get_term_by( 'slug', sanitize_title( $term ), $taxonomy );
			}
		}

		if ( ! $t ) {
			$t = $term;
		}

		if ( is_object( $t ) && '' != $t->name ) {
			$link = get_term_link( $t, $taxonomy );
			if ( ! is_wp_error( $link ) ) {
				$t = sprintf( '<a href="%s" title="%s">%s</a>', $link, sprintf( __( 'More movies from %s', 'wpmovielibrary' ), $t->name ), $t->name );
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
