<?php
/**
 * Define the formatting helper class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/helpers
 */

namespace wpmoly\Helpers;

/**
 * Add a set of formatting helpers for various data.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/helpers
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Formatting {

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
	public static function actors( $actors ) {

		return self::cast( $actors );
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
	public static function adult( $adult ) {

		if ( empty( $adult ) ) {
			$status = '';
		} elseif ( _is_bool( $adult ) ) {
			$status = __( 'Yes', 'wpmovielibrary' );
		} else {
			$status = __( 'No', 'wpmovielibrary' );
		}

		if ( empty( $status ) ) {
			return self::filter_empty( $status );
		}

		/**
		 * Filter final adult restriction.
		 * 
		 * @since    3.0
		 * 
		 * @param    string     $status Filtered adult restriction.
		 * @param    boolean    $is_adult Adult restriction?
		 */
		return apply_filters( 'wpmoly/filter/meta/adult', $status, _is_bool( $adult ) );
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
	public static function author( $author ) {

		if ( empty( $author ) ) {
			return self::filter_empty( $author );
		}

		$authors = explode( ',', $author );
		foreach ( $authors as $key => $author ) {

			/**
			 * Filter single author meta value.
			 * 
			 * This is used to generate permalinks for authors and can be extended to
			 * post-formatting modifications.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $author Filtered author.
			 */
			$authors[ $key ] = apply_filters( 'wpmoly/filter/meta/author/single', trim( $author ) );
		}

		/**
		 * Filter final authors lists.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $authors Filtered authors list.
		 */
		return apply_filters( 'wpmoly/filter/meta/author', implode( ', ', $authors ) );
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
	public static function budget( $budget ) {

		return self::money( $budget );
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
	public static function cast( $actors ) {

		$actors = self::terms_list( $actors,  'actor' );

		return self::filter_empty( $actors );
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
	public static function certification( $certification ) {

		if ( empty( $certification ) ) {
			return $certification;
		}

		/**
		 * Filter final certification.
		 * 
		 * @since    3.0
		 * 
		 * @param    string     $certification Filtered certification.
		 */
		return apply_filters( 'wpmoly/filter/meta/certification', $certification );
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
	public static function composer( $composer ) {

		if ( empty( $composer ) ) {
			return self::filter_empty( $composer );
		}

		$composers = explode( ',', $composer );
		foreach ( $composers as $key => $composer ) {

			/**
			 * Filter single composer meta value.
			 * 
			 * This is used to generate permalinks for producers and can be extended to
			 * post-formatting modifications.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $composer Filtered composer.
			 */
			$composers[ $key ] = apply_filters( 'wpmoly/filter/meta/composer/single', trim( $composer ) );
		}

		/**
		 * Filter final production producers lists.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $composers Filtered producers list.
		 */
		return apply_filters( 'wpmoly/filter/meta/composer', implode( ', ', $composers ) );
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
	public static function countries( $countries, $icon = false ) {

		return self::production_countries( $countries );
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
	public static function date( $date, $format = null ) {

		return self::release_date( $date, $format );
	}

	/**
	 * Format Movies details.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $detail details slug
	 * @param    array      $data detail value
	 * @param    boolean    $text Show text?
	 * @param    string     $icon Show icon?
	 * 
	 * @return   string    Formatted value
	 */
	public static function detail( $detail, $data, $text = true, $icon = '' ) {

		$data = (array) $data;
		if ( empty( $data ) ) {
			return self::filter_empty( $data );
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

				if ( _is_bool( $icon ) ) {
					$icon = '<span class="wpmolicon icon-' . $slug . '"></span>&nbsp;';
				} else {
					$icon = false;
				}

				/**
				 * Filter single detail value.
				 * 
				 * This is used to generate permalinks for details and can be extended to
				 * post-formatting modifications.
				 * 
				 * @since    3.0
				 * 
				 * @param    string     $value Filtered detail value.
				 * @param    string     $slug Detail slug value.
				 * @param    boolean    $text Show text?
				 * @param    boolean    $icon Show icon?
				 */
				$value = apply_filters( "wpmoly/filter/detail/{$detail}/single", $value, $slug, $text, $icon );

				$data[ $key ] = $icon . $value;
			}
		}

		/**
		 * Filter final detail value.
		 * 
		 * This is used to generate permalinks for details and can be extended to
		 * post-formatting modifications.
		 * 
		 * @since    3.0
		 * 
		 * @param    string     $value Filtered detail value.
		 * @param    string     $slug Detail slug value.
		 * @param    boolean    $text Show text?
		 * @param    boolean    $icon Show icon?
		 */
		return apply_filters( "wpmoly/filter/detail/{$detail}", implode( ', ', $data ), $data, $text, $icon );
	}

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
	public static function director( $director ) {

		$director = self::terms_list( $director, 'collection' );

		return self::filter_empty( $director );
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
	public static function format( $format, $text = true, $icon = false ) {

		return self::detail( 'format', $format, $text, $icon );
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
	public static function genres( $genres ) {

		$genres = self::terms_list( $genres, 'genre' );

		return self::filter_empty( $genres );
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
	public static function homepage( $homepage ) {

		if ( empty( $homepage ) ) {
			return self::filter_empty( $homepage );
		}

		$homepage = sprintf( '<a href="%1$s" title="%2$s">%1$s</a>', esc_url( $homepage ), __( 'Official Website', 'wpmovielibrary' ) );

		return self::filter_empty( $homepage );
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
	public static function language( $languages, $text = true, $icon = false ) {

		return self::spoken_languages( $languages, $text, $icon, $variant = 'my_' );
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
	public static function languages( $languages, $text = true, $icon = false, $variant = '' ) {

		return self::spoken_languages( $languages, $text, $icon, $variant );
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
	public static function local_release_date( $date, $format = null ) {

		return self::release_date( $date, $format, $variant = 'local_' );
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
	public static function media( $media, $text = true, $icon = false ) {

		return self::detail( 'media', $media, $text, $icon );
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
	public static function money( $money ) {

		$money = intval( $money );
		if ( ! $money ) {
			return self::filter_empty( $money );
		}

		$money = '$' . number_format_i18n( $money );

		return self::filter_empty( $money );
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
	public static function photography( $photography ) {

		if ( empty( $photography ) ) {
			return $photography;
		}

		$photography = explode( ',', $photography );
		foreach ( $photography as $key => $photographer ) {

			/**
			 * Filter single DOP meta value.
			 * 
			 * This is used to generate permalinks for DOPs and can be extended to
			 * post-formatting modifications.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $producer Filtered producer.
			 */
			$photography[ $key ] = apply_filters( 'wpmoly/filter/meta/photography/single', trim( $photographer ) );
		}

		/**
		 * Filter final production directors of photography lists.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $producers Filtered directors of photography list.
		 */
		return apply_filters( 'wpmoly/filter/meta/photography', implode( ', ', $photography ) );
	}

	/**
	 * Format Movies producers.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $producers Movie producers
	 * 
	 * @return   string    Formatted value
	 */
	public static function producer( $producers ) {

		if ( empty( $producers ) ) {
			return self::filter_empty( $producers );
		}

		$producers = explode( ',', $producers );
		foreach ( $producers as $key => $producer ) {

			/**
			 * Filter single producer meta value.
			 * 
			 * This is used to generate permalinks for producers and can be extended to
			 * post-formatting modifications.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $producer Filtered producer.
			 */
			$producers[ $key ] = apply_filters( 'wpmoly/filter/meta/producer/single', trim( $producer ) );
		}

		/**
		 * Filter final production producers lists.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $producers Filtered producers list.
		 */
		return apply_filters( 'wpmoly/filter/meta/producer', implode( ', ', $producers ) );
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
	public static function production( $companies ) {

		return self::production_companies( $companies );
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
	public static function production_companies( $companies ) {

		if ( empty( $companies ) ) {
			return self::filter_empty( $companies );
		}

		$companies = explode( ',', $companies );
		foreach ( $companies as $key => $company ) {

			/**
			 * Filter single country meta value.
			 * 
			 * This is used to generate permalinks for countries and can be extended to
			 * post-formatting modifications.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $company Filtered company.
			 */
			$companies[ $key ] = apply_filters( 'wpmoly/filter/meta/production/single', trim( $company ) );
		}

		/**
		 * Filter final production companies lists.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $companies Filtered companies list.
		 */
		return apply_filters( 'wpmoly/filter/meta/production_companies', implode( ', ', $companies ) );
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
	public static function production_countries( $countries, $icon = false ) {

		if ( empty( $countries ) ) {
			return self::filter_empty( $countries );
		}

		if ( '1' == wpmoly_o( 'translate-countries' ) ) {
			$formats = wpmoly_o( 'countries-format', array() );
		} elseif ( false === $icon ) {
			$formats = array( 'flag', 'original' );
		} else {
			$formats = array( 'original' );
		}

		$countries_data = array();

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

				/**
				 * Filter single country meta value.
				 * 
				 * This is used to generate permalinks for countries and can be extended to
				 * post-formatting modifications.
				 * 
				 * @since    3.0
				 * 
				 * @param    string    $country Filtered country.
				 * @param    array     $country_data Country instance.
				 * @param    object    $format Country format.
				 */
				$items[] = apply_filters( 'wpmoly/filter/meta/country/single', $item, $country, $format );
			}

			$countries_data[ $key ] = $items;
			$countries[ $key ] = implode( '&nbsp;', $items );
		}

		if ( empty( $countries ) ) {
			return self::filter_empty( $countries );
		}

		/**
		 * Filter final countries lists.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $countries Filtered countries list.
		 * @param    array     $countries_data Countries data array.
		 * @param    array     $formats Countries format.
		 */
		return apply_filters( 'wpmoly/filter/meta/production_countries', implode( ', ', $countries ), $countries_data, $formats );
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
	public static function rating( $rating, $text = true, $icon = false ) {

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
		$details = wpmoly_o( 'default_details' );
		if ( isset( $details['rating']['options'][ $value ] ) ) {
			$title = $details['rating']['options'][ $value ];
		} else {
			$title = '';
		}

		$id = preg_replace( '/([0-5])(\.|_)(0|5)/i', '$1-$3', $value );
		$class = "wpmoly-movie-rating wpmoly-movie-rating-$id";

		$label = '';
		if ( _is_bool( $text ) ) {
			$label = $title;
		}

		$html = '';
		if ( _is_bool( $icon ) ) {

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

			$filled = floor( $value );
			$half   = ceil( $value - floor( $value ) );
			$empty  = ceil( 5.0 - ( $filled + $half ) );

			if ( 0.0 == $value ) {
				if ( true === $include_empty ) {
					$html = str_repeat( $stars['empty'], 10 );
				} else {
					$class = 'not-rated';
					$html  = sprintf( '<small><em>%s</em></small>', __( 'Not rated yet!', 'wpmovielibrary' ) );
				}
			} else if ( 10 == $base ) {
				$_filled = $value * 2;
				$_empty  = 10 - $_filled;
				$title   = "{$_filled}/10 − {$title}";

				$html = str_repeat( $stars['filled'], $_filled ) . str_repeat( $stars['empty'], $_empty );
			} else {
				$title = "{$value}/5 − {$title}";
				$html  = str_repeat( $stars['filled'], $_filled ) . str_repeat( $stars['half'], $_half ) . str_repeat( $stars['empty'], $_empty );
			}

			$html = '<span class="' . $class . '" title="' . $title . '">' . $html . '</span> ';

			/**
			 * Filter generated HTML markup.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $html Stars HTML markup
			 * @param    float     $value Rating value
			 * @param    string    $title Rating title
			 * @param    string    $class CSS classes
			 */
			$html = apply_filters( 'wpmoly/filter/html/rating/stars', $html, $value, $title, $class );
		}

		/**
		 * Filter final rating stars.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $rating Rating Stars HTML markup
		 * @param    string    $html Rating Stars HTML block
		 * @param    string    $label Rating label
		 * @param    float     $value Rating value
		 * @param    string    $title Rating title
		 */
		return apply_filters( 'wpmoly/filter/detail/rating', $html . $label, $html, $label, $value, $title );
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
	public static function revenue( $revenue ) {

		return self::money( $revenue );
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
	 * @param    string    $variant Local release date variant
	 * 
	 * @return   string    Formatted value
	 */
	public static function release_date( $date, $date_format = null, $variant = '' ) {

		if ( empty( $date ) ) {
			return self::filter_empty( $date );
		}

		$variant = (string) $variant;
		if ( 'local_' != $variant ) {
			$variant = '';
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

		/**
		 * Filter release date meta final value.
		 * 
		 * This is used to generate permalinks for dates and can be extended to
		 * post-formatting modifications.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $date Filtered date.
		 * @param    array     $raw_date Unfiltered date
		 * @param    array     $date_parts Date parts, if need be
		 * @param    string    $date_format Date format
		 * @param    int       $timestamp Date UNIX Timestamp
		 * @param    string    $variant Local release date variant
		 */
		return apply_filters( "wpmoly/filter/meta/{$variant}release_date", self::filter_empty( $date ), $date, $date_parts, $date_format, $timestamp, $variant );
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
	public static function runtime( $runtime, $time_format = null ) {

		$runtime = intval( $runtime );
		if ( ! $runtime ) {
			return self::filter_empty( __( 'Duration unknown', 'wpmovielibrary' ) );
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

		return self::filter_empty( $runtime );
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
	public static function spoken_languages( $languages, $text = true, $icon = '', $variant = '' ) {

		if ( empty( $languages ) ) {
			return self::filter_empty( $languages );
		}

		if ( is_string( $languages ) ) {
			$languages = explode( ',', $languages );
		}

		$variant = (string) $variant;
		if ( 'my_' != $variant ) {
			$variant = '';
		}

		$languages_data = array();

		foreach ( $languages as $key => $language ) {

			$language = get_language( $language );
			$languages_data[ $key ] = $language;

			if ( true !== $text ) {
				$name = '';
			} elseif ( '1' == wpmoly_o( 'translate-languages' ) ) {
				$name = $language->localized_name;
			} else {
				$name = $language->standard_name;
			}

			if ( _is_bool( $icon ) ) {
				$icon = '<span class="wpmoly language iso icon" title="' . esc_attr( $language->localized_name ) . ' (' . esc_attr( $language->standard_name ) . ')">' . esc_attr( $language->code ) . '</span>&nbsp;';
			} else {
				$icon = false;
			}

			/**
			 * Filter single language meta value.
			 * 
			 * This is used to generate permalinks for languages and can be extended to
			 * post-formatting modifications.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $language Filtered language.
			 * @param    array     $language_data Language instance.
			 * @param    string    $icon Language icon string.
			 */
			$languages[ $key ] = apply_filters( 'wpmoly/filter/meta/language/single', $name, $language, $icon, $variant );
		}

		if ( empty( $languages ) ) {
			return self::filter_empty( $languages );
		}

		/**
		 * Filter final languages lists.
		 * 
		 * This is used to generate permalinks for languages and can be extended to
		 * post-formatting modifications.
		 * 
		 * @since    3.0
		 * 
		 * @param    string     $languages Filtered languages list.
		 * @param    array      $languages_data Languages data array.
		 * @param    boolean    $text
		 * @param    mixed      $icon
		 */
		return apply_filters( 'wpmoly/filter/meta/spoken_languages', implode( ', ', $languages ), $languages_data, $text, $icon );
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
	public static function status( $statuses, $text = true, $icon = false ) {

		return self::detail( 'status', $statuses, $text, $icon );
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
	public static function subtitles( $subtitles, $text = true, $icon = false, $variant = '' ) {

		return self::spoken_languages( $subtitles, $text, $icon, $variant );
	}

	/**
	 * Format Movies writers.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $writer Movie writers
	 * 
	 * @return   string    Formatted value
	 */
	public static function writer( $writer ) {

		if ( empty( $writer ) ) {
			return self::filter_empty( $writer );
		}

		$writers = explode( ',', $writer );
		foreach ( $writers as $key => $writer ) {

			/**
			 * Filter single writer meta value.
			 * 
			 * This is used to generate permalinks for writers and can be extended to
			 * post-formatting modifications.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $writer Filtered writer.
			 */
			$writers[ $key ] = apply_filters( 'wpmoly/filter/meta/writer/single', trim( $writer ) );
		}

		/**
		 * Filter final writers lists.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $writers Filtered writers list.
		 */
		return apply_filters( 'wpmoly/filter/meta/writer', implode( ', ', $writers ) );
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
	public static function year( $date ) {

		return self::release_date( $date, $format = 'Y' );
	}

	/**
	 * Internal helpers
	 */

	/**
	 * Format Movies empty fields.
	 * 
	 * This is used by almost every other formatting public static function to filter and replace
	 * empty values.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $value field value
	 * 
	 * @return   string    Formatted value
	 */
	public static function filter_empty( $value ) {

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
	public static function terms_list( $terms, $taxonomy ) {

		if ( empty( $terms ) ) {
			return self::filter_empty( $terms );
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

}