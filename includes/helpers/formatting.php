<?php

namespace wpmoly\Helpers\Formatting;

/**
 * Format Movies director.
 * 
 * Match each name against the collection taxonomy to detect missing
 * terms. If term collection exists, provide a link, raw text value
 * if no matching term could be found.
 * 
 * @since    3.0
 * 
 * @param    string    $director field value
 * 
 * @return   string    Formatted value
 */
function director( $director ) {

	$director = terms_list( $director, 'collection' );

	return filter_empty( $director );
}

/**
 * Format Movies genres.
 * 
 * Match each genre against the genre taxonomy to detect missing
 * terms. If term genre exists, provide a link, raw text value
 * if no matching term could be found.
 * 
 * @since    3.0
 * 
 * @param    string    $genres field value
 * 
 * @return   string    Formatted value
 */
function genres( $genres ) {

	$genres = terms_list( $genres, 'genre' );

	return filter_empty( $genres );
}

/**
 * Format Movies casting.
 * 
 * Match each actor against the actor taxonomy to detect missing
 * terms. If term actor exists, provide a link, raw text value
 * if no matching term could be found.
 * 
 * @since    3.0
 * 
 * @param    string    $actors Movie actors list
 * 
 * @return   string    Formatted value
 */
function cast( $actors ) {

	$actors = terms_list( $actors,  'actor' );

	return filter_empty( $actors );
}

/**
 * Format Movies release date.
 * 
 * If no format is provided, use the format defined in settings. If no such
 * settings can be found, fallback to a standard 'day Month Year' format.
 * 
 * @since    3.0
 * 
 * @param    string    $date Movie release date
 * @param    string    $date_format Date format for output
 * 
 * @return   string    Formatted value
 */
function release_date( $date, $date_format = null ) {

	if ( empty( $date ) ) {
		return filter_empty( $date );
	}

	$timestamp  = strtotime( $date );
	$date_parts = array();

	$date_format = (string) $date_format;
	if ( empty( $date_format ) ) {
		$date_format = wpmoly_o( 'format-date' );
		if ( empty( $date_format ) ) {
			$date_format = 'j F Y';
		}
	}

	if ( 'j F Y' == $date_format ) {
		$date_parts[] = date_i18n( 'j F', $timestamp );
		$date_parts[] = date_i18n( 'Y', $timestamp );
		$date = implode( '&nbsp;', $date_parts );
	} else {
		$date = date_i18n( $date_format, $timestamp );
	}

	return apply_filters( 'wpmoly/filter/meta/release_date/url', filter_empty( $date ), $date, $date_parts, $date_format, $timestamp );
}

/**
 * Format Movies runtime.
 * 
 * If no format is provided, use the format defined in settings. If no such
 * settings can be found, fallback to a standard 'X h Y min' format.
 * 
 * @since    3.0
 * 
 * @param    string    $runtime field value
 * 
 * @return   string    Formatted value
 */
function runtime( $runtime, $time_format = null ) {

	$runtime = intval( $runtime );
	if ( ! $runtime ) {
		return filter_empty( __( 'Duration unknown', 'wpmovielibrary' ) );
	}

	$time_format = (string) $time_format;
	if ( empty( $time_format ) ) {
		$time_format = wpmoly_o( 'format-time' );
		if ( empty( $time_format ) ) {
			$time_format = 'G \h i \m\i\n';
		}
	}

	$runtime = date_i18n( $time_format, mktime( 0, $runtime ) );
	if ( false !== stripos( $runtime, 'am' ) || false !== stripos( $runtime, 'pm' ) ) {
		$runtime = date_i18n( 'G:i', mktime( 0, $runtime ) );
	}

	return filter_empty( $runtime );
}

/**
 * Format Movies languages.
 * 
 * $text and $icon parameters are essentially used by language and subtitles
 * details.
 * 
 * @since    3.0
 * 
 * @param    array      $languages Languages
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function spoken_languages( $languages, $text = true, $icon = false ) {

	if ( empty( $languages ) ) {
		return filter_empty( $languages );
	}

	if ( is_string( $languages ) ) {
		$languages = explode( ',', $languages );
	}

	foreach ( $languages as $key => $language ) {

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

		$languages[ $key ] = $name;
	}

	$languages = implode( ',&nbsp;', $languages );

	return filter_empty( $languages );
}

/**
 * Format Movies countries.
 * 
 * @since    3.0
 * 
 * @param    string     $countries Countries list
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function production_countries( $countries, $icon = false ) {

	if ( empty( $countries ) ) {
		return filter_empty( $countries );
	}

	if ( '1' == wpmoly_o( 'translate-countries' ) ) {
		$formats = wpmoly_o( 'countries-format', array() );
	} elseif ( false === $icon ) {
		$formats = array( 'flag', 'original' );
	} else {
		$formats = array( 'original' );
	}

	$countries = explode( ',', $countries );
	foreach ( $countries as $key => $country ) {

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

			$items[] = $item;
		}

		$countries[ $key ] = implode( '&nbsp;', $items );
	}

	$countries = implode( ',&nbsp; ', $countries );

	return filter_empty( $countries );
}

/**
 * Format Movies production companies.
 * 
 * @since    3.0
 * 
 * @param    string    $companies Movie production companies
 * 
 * @return   string    Formatted value
 */
function production_companies( $companies ) {

	return filter_empty( $companies );
}

/**
 * Format Movies producer.
 * 
 * @since    3.0
 * 
 * @param    string    $producer Movie producer
 * 
 * @return   string    Formatted value
 */
function producer( $producer ) {

	return filter_empty( $producer );
}

/**
 * Format Movies composer.
 * 
 * @since    3.0
 * 
 * @param    string    $composer Movie original music composer
 * 
 * @return   string    Formatted value
 */
function composer( $composer ) {

	return filter_empty( $composer );
}

/**
 * Format Movies editor.
 * 
 * @since    3.0
 * 
 * @param    string    $editor Movie editor
 * 
 * @return   string    Formatted value
 */
function editor( $editor ) {

	return filter_empty( $editor );
}

/**
 * Format Movies author.
 * 
 * @since    3.0
 * 
 * @param    string    $author Movie author
 * 
 * @return   string    Formatted value
 */
function author( $author ) {

	return filter_empty( $author );
}

/**
 * Format Movies director of photography.
 * 
 * @since    3.0
 * 
 * @param    string    $photography Movie director of photography
 * 
 * @return   string    Formatted value
 */
function photography( $photography ) {

	return filter_empty( $photography );
}

/**
 * Format Movies certification.
 * 
 * @since    3.0
 * 
 * @param    string    $certification Movie certification
 * 
 * @return   string    Formatted value
 */
function certification( $certification ) {

	return filter_empty( $certification );
}

/**
 * Format a money value.
 * 
 * @since    3.0
 * 
 * @param    string    $money field value
 * 
 * @return   string    Formatted value
 */
function money( $money ) {

	$money = intval( $money );
	if ( ! $money ) {
		return filter_empty( $money );
	}

	$money = '$' . number_format_i18n( $money );

	return filter_empty( $money );
}

/**
 * Format Movies adult status.
 * 
 * @since    3.0
 * 
 * @param    string    $adult field value
 * 
 * @return   string    Formatted value
 */
function adult( $adult ) {

	if ( empty( $adult ) ) {
		$adult = '';
	} elseif ( _is_bool( $adult ) ) {
		$adult = __( 'Yes', 'wpmovielibrary' );
	} else {
		$adult = __( 'No', 'wpmovielibrary' );
	}

	if ( empty( $adult ) ) {
		return filter_empty( $adult );
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
 * @return   string    Formatted value
 */
function homepage( $homepage ) {

	if ( empty( $homepage ) ) {
		return filter_empty( $homepage );
	}

	$homepage = sprintf( '<a href="%1$s" title="%2$s">%1$s</a>', esc_url( $homepage ), __( 'Official Website', 'wpmovielibrary' ) );

	return filter_empty( $homepage );
}

/**
 * Format Movies casting.
 * 
 * Alias for \wpmoly\Helpers\Formatting\cast()
 * 
 * @since    3.0
 * 
 * @param    string     $actors Movie actors list
 * 
 * @return   string    Formatted value
 */
function actors( $actors ) {
	return cast( $actors );
}

/**
 * Format Movies release date.
 * 
 * Alias for \wpmoly\Helpers\Formatting\release_date()
 * 
 * @since    3.0
 * 
 * @param    string    $date Movie release date
 * @param    string    $date_format Date format for output
 * 
 * @return   string    Formatted value
 */
function date( $date, $format = null ) {
	return release_date( $date, $format );
}

/**
 * Format Movies local release date.
 * 
 * Alias for \wpmoly\Helpers\Formatting\release_date()
 * 
 * @since    3.0
 * 
 * @param    string    $date Movie local release date
 * @param    string    $date_format Date format for output
 * 
 * @return   string    Formatted value
 */
function local_release_date( $date, $format = null ) {
	return release_date( $date, $format );
}

/**
 * Format Movies release date.
 * 
 * Alias for \wpmoly\Helpers\Formatting\release_date()
 * 
 * @since    3.0
 * 
 * @param    string    $date Movie release date
 * @param    string    $date_format Date format for output
 * 
 * @return   string    Formatted value
 */
function year( $date ) {
	return release_date( $date, $format = 'Y' );
}

/**
 * Format Movies languages.
 * 
 * Alias for \wpmoly\Helpers\Formatting\spoken_languages()
 * 
 * @since    3.0
 * 
 * @param    array      $languages Languages
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function languages( $languages, $text = true, $icon = false ) {
	return spoken_languages( $languages, $text, $icon );
}

/**
 * Format Movies countries.
 * 
 * Alias for \wpmoly\Helpers\Formatting\production_countries()
 * 
 * @since    3.0
 * 
 * @param    string     $countries Countries list
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function countries( $countries, $icon = false ) {
	return production_countries( $countries );
}

/**
 * Format Movies production companies.
 * 
 * Alias for \wpmoly\Helpers\Formatting\production_companies()
 * 
 * @since    3.0
 * 
 * @param    string    $companies Movie production companies
 * 
 * @return   string    Formatted value
 */
function production( $companies ) {
	return production_companies( $companies );
}

/**
 * Format Movies budget.
 * 
 * Alias for \wpmoly\Helpers\Formatting\money()
 * 
 * @since    3.0
 * 
 * @param    string     $budget Movie budget
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function budget( $budget ) {
	return money( $budget );
}

/**
 * Format Movies revenue.
 * 
 * Alias for \wpmoly\Helpers\Formatting\money()
 * 
 * @since    3.0
 * 
 * @param    string     $medias Movie revenue
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function revenue( $revenue ) {
	return money( $revenue );
}

/**
 * Format Movies media.
 * 
 * @since    3.0
 * 
 * @param    string     $medias Movie media
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function media( $media, $text = true, $icon = false ) {

	return detail( 'media', $media, $text, $icon );
}

/**
 * Format Movies status.
 * 
 * @since    3.0
 * 
 * @param    array      $data Movie statuses
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function status( $statuses, $text = true, $icon = false ) {

	return detail( 'status', $statuses, $text, $icon );
}

/**
 * Format Movies language.
 * 
 * Alias for \wpmoly\Helpers\Formatting\spoken_languages()
 * 
 * @since    3.0
 * 
 * @param    array      $languages Movie languages
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function language( $languages, $text = true, $icon = false ) {

	return spoken_languages( $languages, $text, $icon );
}

/**
 * Format Movies subtitles.
 * 
 * Alias for \wpmoly\Helpers\Formatting\spoken_languages() since subtitles are languages
 * names.
 * 
 * @since    3.0
 * 
 * @param    array      $subtitles Movie subtitles
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function subtitles( $subtitles, $text = true, $icon = false ) {

	return spoken_languages( $subtitles, $text, $icon );
}

/**
 * Format Movies format.
 * 
 * @since    3.0
 * 
 * @param    array      $format Movie formats
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function format( $format, $text = true, $icon = false ) {

	return detail( 'format', $format, $text, $icon );
}

/**
 * Format Movies rating.
 * 
 * @since    3.0
 * 
 * @param    string     $rating Movie rating
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function rating( $rating, $text = true, $icon = false ) {

	$base = (int) wpmoly_o( 'format-rating' );
	if ( 10 != $base ) {
		$base = 5;
	}

	$rating = floatval( $rating );
	if ( 0 > $rating ) {
		$rating = 0.0;
	}
	if ( 5.0 < $rating ) {
		$rating = 5.0;
	}

	$details = wpmoly_o( 'default_details' );
	if ( isset( $details['rating']['options'][ (string) $rating ] ) ) {
		$label = $details['rating']['options'][ (string) $rating ];
	} else {
		$label = '';
	}

	$id = preg_replace( '/([0-5])(\.|_)(0|5)/i', '$1-$3', $rating );
	$class = "wpmoly-movie-rating wpmoly-movie-rating-$id";

	if ( true === $icon ) {

		$stars = array();

		/**
		 * Filter filled stars icon HTML block.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $html Filled star icon default HTML block
		 */
		$stars['filled'] = apply_filters( 'wpmoly/filter/html/filled/star', '<span class="wpmolicon icon-star-filled"></span>' );

		/**
		 * Filter half-filled stars icon HTML block.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $html Half-filled star icon default HTML block
		 */
		$stars['half']= apply_filters( 'wpmoly/filter/html/half/star', '<span class="wpmolicon icon-star-half"></span>' );

		/**
		 * Filter empty stars icon HTML block.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $html Empty star icon default HTML block
		 */
		$stars['empty'] = apply_filters( 'wpmoly/filter/html/empty/star', '<span class="wpmolicon icon-star-empty"></span>' );

		$filled = floor( $rating );
		$half   = ceil( $rating - floor( $rating ) );
		$empty  = ceil( 5.0 - ( $filled + $half ) );

		$html = '';
		if ( 0.0 == $rating ) {
			if ( true === $include_empty ) {
				$html = str_repeat( $stars['empty'], 10 );
			} else {
				$class = 'not-rated';
				$html  = sprintf( '<small><em>%s</em></small>', __( 'Not rated yet!', 'wpmovielibrary' ) );
			}
		}
		else if ( 10 == $base ) {
			$_filled = $rating * 2;
			$_empty  = 10 - $_filled;
			$title   = "{$_filled}/10 − {$label}";

			$html = str_repeat( $stars['filled'], $_filled ) . str_repeat( $stars['empty'], $_empty );
		}
		else {
			$title = "{$rating}/5 − {$label}";
			$html  = str_repeat( $stars['filled'], $_filled ) . str_repeat( $stars['half'], $_half ) . str_repeat( $stars['empty'], $_empty );
		}

		$html = '<span class="' . $class . '" title="' . $title . '">' . $html . '</span>';

		/**
		 * Filter generated HTML markup.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $html Stars HTML markup
		 * @param    float     $rating Rating value
		 * @param    string    $label Rating title
		 */
		$html = apply_filters( 'wpmoly/filter/html/rating/stars', $html, $rating, $label );

		return $html . '&nbsp;' . $label;
	}

	return $label;
}

/**
 * Format Movies details.
 * 
 * @since    3.0
 * 
 * @param    string     $detail details slug
 * @param    array      $data detail value
 * @param    boolean    $text Show text?
 * @param    boolean    $icon Show icon?
 * 
 * @return   string    Formatted value
 */
function detail( $detail, $data, $text = true, $icon = false ) {

	$data = (array) $data;
	if ( empty( $data ) ) {
		return filter_empty( $data );
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
 * Format Movies empty fields.
 * 
 * This is used by almost every other formatting function to filter and replace
 * empty values.
 * 
 * @since    3.0
 * 
 * @param    string    $value field value
 * 
 * @return   string    Formatted value
 */
function filter_empty( $value ) {

	if ( ! empty( $value ) ) {
		return $value;
	}

	/**
	 * Filter empty meta value.
	 * 
	 * Use a long dash for replacer.
	 * 
	 * @param    string    $value Empty value replacer
	 */
	return apply_filters( 'wpmoly/filter/meta/empty/value', '&mdash;' );
}

/**
 * Format Movies misc actors/genres list depending on
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
 * @return   string    Formatted value
 */
function terms_list( $terms, $taxonomy ) {

	if ( empty( $terms ) ) {
		return filter_empty( $terms );
	}

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
