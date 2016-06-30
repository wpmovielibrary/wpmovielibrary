<?php

namespace wpmoly\Helpers;

use wpmoly\Permalink;

class Permalinks {

	/**
	 * Build a permalink for adult restrictions.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for adult
	 * restrictions.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $text Movie adult restriction text.
	 * @param    boolean    $is_adult Adult restriction?
	 * 
	 * @return   string
	 */
	public static function adult( $text, $is_adult = false ) {

		if ( empty( $text ) ) {
			return $text;
		}

		if ( true === $is_adult ) {
			$content = 'yes';
			$attr_title = __( 'Adults-only movies', 'wpmovielibrary' );
		} else {
			$content = 'no';
			$attr_title = __( 'All-audience movies', 'wpmovielibrary' );
		};

		$permalink = new Permalink;
		$permalink->setID( 'adult' );
		$permalink->setContent( $content );
		$permalink->setTitle( $text );
		$permalink->setTitleAttr( $attr_title );

		/**
		 * Filter single adult restriction permalink.
		 * 
		 * @since    3.0
		 * 
		 * @param    string     $permalink Permalink HTML output.
		 * @param    object     $permalink_object Permalink instance.
		 * @param    string     $text Default text.
		 * @param    boolean    $is_adult Adult restriction?
		 */
		return apply_filters( 'wpmoly/filter/permalink/adult', $permalink->toHTML(), $permalink, $text, $is_adult );
	}

	/**
	 * Build a permalink for authors.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for authors.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $author Movie author.
	 * 
	 * @return   string
	 */
	public static function author( $author ) {

		if ( empty( $author ) ) {
			return $author;
		}

		$permalink = new Permalink;
		$permalink->setID( 'author' );
		$permalink->setContent( sanitize_title_with_dashes( $author ) );
		$permalink->setTitle( $author );
		$permalink->setTitleAttr( sprintf( __( 'Movies from author %s', 'wpmovielibrary' ), $author ) );

		/**
		 * Filter single author permalink.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $permalink Permalink HTML output.
		 * @param    object    $permalink_object Permalink instance.
		 * @param    string    $author Default text.
		 */
		return apply_filters( 'wpmoly/filter/permalink/author', $permalink->toHTML(), $permalink, $author );
	}

	/**
	 * Build a permalink for certifications.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for certifications.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $certification Movie certification.
	 * 
	 * @return   string
	 */
	public static function certification( $certification ) {

		if ( empty( $certification ) ) {
			return $certification;
		}

		$permalink = new Permalink;
		$permalink->setID( 'certification' );
		$permalink->setContent( sanitize_title_with_dashes( $certification ) );
		$permalink->setTitle( $certification );
		$permalink->setTitleAttr( sprintf( __( '%s rated movies', 'wpmovielibrary' ), $certification ) );

		/**
		 * Filter single certification permalink.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $permalink Permalink HTML output.
		 * @param    object    $permalink_object Permalink instance.
		 * @param    string    $certification Default text.
		 */
		return apply_filters( 'wpmoly/filter/permalink/certification', $permalink->toHTML(), $permalink, $certification );
	}

	/**
	 * Build a permalink for composers.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for composers.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $composer Movie composer.
	 * 
	 * @return   string
	 */
	public static function composer( $composer ) {

		if ( empty( $composer ) ) {
			return $composer;
		}

		$permalink = new Permalink;
		$permalink->setID( 'composer' );
		$permalink->setContent( sanitize_title_with_dashes( $composer ) );
		$permalink->setTitle( $composer );
		$permalink->setTitleAttr( sprintf( __( 'Movies from composer %s', 'wpmovielibrary' ), $composer ) );

		/**
		 * Filter single composer permalink.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $permalink Permalink HTML output.
		 * @param    object    $permalink_object Permalink instance.
		 * @param    string    $composer Default text.
		 */
		return apply_filters( 'wpmoly/filter/permalink/composer', $permalink->toHTML(), $permalink, $composer );
	}

	/**
	 * Build a permalink for dates.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for release dates and local
	 * release dates.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $date Filtered date.
	 * @param    array     $raw_date Unfiltered date
	 * @param    array     $date_parts Date parts, if need be
	 * @param    string    $date_format Date format
	 * @param    int       $timestamp Date UNIX Timestamp
	 * @param    string    $variant Local release date variant
	 * 
	 * @return   string
	 */
	public static function release_date( $date, $raw_date = array(), $date_parts = array(), $date_format = '', $timestamp = '', $variant = '' ) {

		if ( empty( $raw_date ) ) {
			return $date;
		}

		$variant = (string) $variant;
		if ( 'local_' != $variant ) {
			$variant = '';
		}

		if ( ! empty( $variant ) ) {
			$id = 'local-release-date';
			$attr_title = __( 'Movies localy released in %s', 'wpmovielibrary' );
		} else {
			$id = 'release-date';
			$attr_title = __( 'Movies released in %s', 'wpmovielibrary' );
		}

		$permalink = new Permalink;
		switch ( $date_format ) {
			case 'Y':
				$permalink->setID( $id );
				$permalink->setContent( date_i18n( $date_format, $timestamp ) );
				$permalink->setTitle( $date );
				$permalink->setTitleAttr( sprintf( $attr_title, date_i18n( $date_format, $timestamp ) ) );
				break;
			case 'j F':
				$permalink->setID( $id );
				$permalink->setContent( date_i18n( 'Y-m', $timestamp ) );
				$permalink->setTitle( $date );
				$permalink->setTitleAttr( sprintf( $attr_title, date_i18n( $date_format, $timestamp ) ) );
				break;
			case 'j F Y':
				$month = self::release_date( $date_parts[0], $raw_date, $date_parts, $date_format = 'j F', $timestamp, $variant );
				$year  = self::release_date( $date_parts[1], $raw_date, $date_parts, $date_format = 'Y',   $timestamp, $variant );
				$permalink = $month . ' ' . $year;
				break;
			default:
				break;
		}

		if ( ! $permalink instanceof Permalink ) {
			return $permalink;
		}

		/**
		 * Filter date permalink.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $permalink Permalink HTML output.
		 * @param    object    $permalink_object Permalink instance.
		 * @param    string    $date Default text.
		 */
		return apply_filters( 'wpmoly/filter/permalink/date', $permalink->toHTML(), $permalink, $date );
	}

	/**
	 * Build a permalink for languages.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for languages.
	 * 
	 * @since    3.0
	 * 
	 * @param    string          $language Formatted language.
	 * @param    array|object    $language_data Language instance.
	 * @param    string          $icon Language icon.
	 * @param    string          $variant Details language variant.
	 * 
	 * @return   string
	 */
	public static function language( $language, $language_data, $icon, $variant = 'my_' ) {

		if ( empty( $language ) ) {
			return $language;
		}

		$variant = (string) $variant;
		if ( 'my_' != $variant ) {
			$variant = '';
		}

		if ( ! empty( $variant ) ) {
			$id = 'my-language';
			$attr_title = __( 'My %s-speaking movies', 'wpmovielibrary' );
		} else {
			$id = 'language';
			$attr_title = __( '%s-speaking movies', 'wpmovielibrary' );
		}

		if ( $language_data instanceof \wpmoly\Helpers\Language ) {

			$permalink = new Permalink;
			$permalink->setID( $id );
			$permalink->setContent( $language_data->code );
			$permalink->setTitle( $language );
			$permalink->setTitleAttr( sprintf( $attr_title, $language_data->localized_name ) );

			/**
			 * Filter single language permalink.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $permalink Permalink HTML output.
			 * @param    object    $permalink_object Permalink instance.
			 * @param    string    $language Default text.
			 */
			$language = apply_filters( "wpmoly/filter/permalink/{$variant}language", $permalink->toHTML(), $permalink, $language );
		}

		if ( ! empty( $icon ) ) {
			$language = $icon . $language;
		}

		return $language;
	}

	/**
	 * Build a permalink for countries.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for countries.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $country Formatted country.
	 * @param    object    $country_data Country instance.
	 * @param    object    $format Country format.
	 * 
	 * @return   string
	 */
	public static function country( $country, $country_data, $format ) {

		if ( 'flag' == $format || empty( $country ) ) {
			return $country;
		}

		if ( $country_data instanceof \wpmoly\Helpers\Country ) {

			$permalink = new Permalink;
			$permalink->setID( 'country' );
			$permalink->setContent( $country_data->code );
			$permalink->setTitle( $country );
			$permalink->setTitleAttr( sprintf( __( 'Movies produced in %s', 'wpmovielibrary' ), $country_data->localized_name ) );

			/**
			 * Filter single country permalink.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $permalink Permalink HTML output.
			 * @param    object    $permalink_object Permalink instance.
			 * @param    string    $country Default text.
			 */
			return apply_filters( 'wpmoly/filter/permalink/country', $permalink->toHTML(), $permalink, $country );
		}

		return $country;
	}

	/**
	 * Build a permalink for directors of photography.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for directors of
	 * photography.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $company Movie director of photography.
	 * 
	 * @return   string
	 */
	public static function photographer( $photographer ) {

		if ( empty( $photographer ) ) {
			return $photographer;
		}

		$permalink = new Permalink;
		$permalink->setID( 'photography' );
		$permalink->setContent( sanitize_title_with_dashes( $photographer ) );
		$permalink->setTitle( $photographer );
		$permalink->setTitleAttr( sprintf( __( 'Movies from Director of Photography %s', 'wpmovielibrary' ), $photographer ) );

		/**
		 * Filter single photographer permalink.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $permalink Permalink HTML output.
		 * @param    object    $permalink_object Permalink instance.
		 * @param    string    $photographer Default text.
		 */
		return apply_filters( 'wpmoly/filter/permalink/photographer', $permalink->toHTML(), $permalink, $photographer );
	}

	/**
	 * Build a permalink for production companies.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for production companies.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $company Movie production company
	 * 
	 * @return   string
	 */
	public static function production( $company ) {

		if ( empty( $company ) ) {
			return $company;
		}

		$permalink = new Permalink;
		$permalink->setID( 'production' );
		$permalink->setContent( sanitize_title_with_dashes( $company ) );
		$permalink->setTitle( $company );
		$permalink->setTitleAttr( sprintf( __( 'Movies produced by %s', 'wpmovielibrary' ), $company ) );

		/**
		 * Filter single production permalink.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $permalink Permalink HTML output.
		 * @param    object    $permalink_object Permalink instance.
		 * @param    string    $company Default text.
		 */
		return apply_filters( 'wpmoly/filter/permalink/production', $permalink->toHTML(), $permalink, $company );
	}

	/**
	 * Build a permalink for producers.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for producer.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $producer Movie producer
	 * 
	 * @return   string
	 */
	public static function producer( $producer ) {

		if ( empty( $producer ) ) {
			return $producer;
		}

		$permalink = new Permalink;
		$permalink->setID( 'producer' );
		$permalink->setContent( sanitize_title_with_dashes( $producer ) );
		$permalink->setTitle( $producer );
		$permalink->setTitleAttr( sprintf( __( 'Movies produced by %s', 'wpmovielibrary' ), $producer ) );

		/**
		 * Filter single producer permalink.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $permalink Permalink HTML output.
		 * @param    object    $permalink_object Permalink instance.
		 * @param    string    $producer Default text.
		 */
		return apply_filters( 'wpmoly/filter/permalink/producer', $permalink->toHTML(), $permalink, $producer );
	}

	/**
	 * Build a permalink for writers.
	 * 
	 * Uses \wpmoly\Permalink() to generate custom URLs for writers.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $writer Movie writer.
	 * 
	 * @return   string
	 */
	public static function writer( $writer ) {

		if ( empty( $writer ) ) {
			return $writer;
		}

		$permalink = new Permalink;
		$permalink->setID( 'writer' );
		$permalink->setContent( sanitize_title_with_dashes( $writer ) );
		$permalink->setTitle( $writer );
		$permalink->setTitleAttr( sprintf( __( 'Movies from writer %s', 'wpmovielibrary' ), $writer ) );

		/**
		 * Filter single writer permalink.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $permalink Permalink HTML output.
		 * @param    object    $permalink_object Permalink instance.
		 * @param    string    $writer Default text.
		 */
		return apply_filters( 'wpmoly/filter/permalink/writer', $permalink->toHTML(), $permalink, $writer );
	}

}