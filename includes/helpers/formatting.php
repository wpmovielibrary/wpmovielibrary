<?php

namespace wpmoly\Formatting;

/**
 * Format a Movie's director for display
 * 
 * @since    1.1
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
 * @since    1.1
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function genres( $data ) {

	$output = terms_list( $data, 'genre' );
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's casting for display
 * 
 * Match each actor against the actor taxonomy to detect missing
 * terms. If term actor exists, provide a link, raw text value
 * if no matching term could be found.
 * 
 * @since    1.1
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function cast( $data ) {

	$output = terms_list( $data,  'actor' );
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's release date for display
 * 
 * @since    1.1
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function release_date( $data, $format = null ) {

	if ( is_null( $data ) || '' == $data )
		return $data;

	if ( is_null( $format ) ) {
		$format = wpmoly_o( 'format-date' );
	}

	if ( '' == $format ) {
		$format = 'j F Y';
	}

	$args = array(
		'key'  => 'release_date',
		'type' => 'meta'
	);

	if ( 'j F Y' == $format ) {

		$date  = date_i18n( 'j F', strtotime( $data ) );
		$_date = date( 'Y-m', strtotime( $data ) );
		$year = date_i18n( 'Y', strtotime( $data ) );

		/*$year = apply_filters( 'wpmoly_movie_meta_link', array(
			'key'   => 'release_date',
			'value' => $year,
			'type'  => 'meta',
			'text'  => $year,
			'title' => sprintf( __( 'More movies released on %s', 'wpmovielibrary' ), $year ),
		) );
		$date = apply_filters( 'wpmoly_movie_meta_link', array(
			'key'   => 'release_date',
			'value' => $_date,
			'type'  => 'meta',
			'text'  => $date,
			'title' => sprintf( __( 'More movies released on %s', 'wpmovielibrary' ), date_i18n( 'F Y', strtotime( $data ) ) ),
		) );*/

		$output = "$date&nbsp;$year";

	} else {
		$output = date_i18n( $format, strtotime( $data ) );
		/*$output = apply_filters( 'wpmoly_movie_meta_link', array(
			'key'   => 'release_date',
			'value' => $output,
			'type'  => 'meta',
			'text'  => $output,
			'title' => sprintf( __( 'More movies released on %s', 'wpmovielibrary' ), $output ),
		) );*/
	}

	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's runtime for display
 * 
 * @since    1.1
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
 * @since    2.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function spoken_languages( $languages ) {

	if ( empty( $languages ) ) {
		return $languages;
	}

	$output = array();
	$languages = explode( ',', $languages );
	foreach ( $languages as $language ) {

		if ( '1' == wpmoly_o( 'translate-languages' ) ) {
			$language = get_language( $language );
			$language = $language->localized_name;
		}

		/*$url = apply_filters( 'wpmoly_movie_meta_link', array(
			'key'   => 'spoken_languages',
			'value' => $language,
			'type'  => 'meta',
			'text'  => $title,
			'title' => sprintf( __( 'More movies in %s', 'wpmovielibrary' ), $title )
		) );*/
		$output[] = $language;
	}

	$output = implode( ', ', $output );
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's countries for display.
 * 
 * @since    2.0
 * 
 * @param    string    $countries Countries list
 * 
 * @return   string    Formatted output
 */
function production_countries( $countries ) {

	if ( empty( $countries ) ) {
		return $countries;
	}

	if ( '1' == wpmoly_o( 'translate-countries' ) ) {
		$formats = wpmoly_o( 'countries-format', array() );
	} else {
		$formats = array( 'flag', 'original' );
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
 * @since    2.0
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
 * @since    2.0
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
 * @since    2.0
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
 * @since    2.0
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
 * @since    2.0
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
 * @since    2.0
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
 * @since    2.0
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
 * Format a Movie's budget for display
 * 
 * @since    2.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function budget( $data, $format = 'html' ) {

	$output = intval( $data );
	if ( ! $output ) {
		return $output;
	}

	if ( 'html' != $format ) {
		$format = 'raw';
	}

	if ( 'html' == $format ) {
		$output = '$' . number_format_i18n( $output );
	}

	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's revenue for display
 * 
 * @since    2.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function revenue( $data, $format = 'html' ) {

	$output = intval( $data );
	if ( ! $output ) {
		return $output;
	}

	if ( 'html' != $format ) {
		$format = 'raw';
	}

	if ( 'html' == $format ) {
		$output = '$' . number_format_i18n( $output );
	}

	$output = filter_empty( $output );

	return $output;
}

/**
 * Format a Movie's adult status for display
 * 
 * @since    2.0
 * 
 * @param    string    $data field value
 * 
 * @return   string    Formatted output
 */
function adult( $data ) {

	$status = array( 'true', 'false' );
	$output = array( __( 'Yes', 'wpmovielibrary' ), __( 'No', 'wpmovielibrary' ) );
	$output = str_replace( $status, $output, $data );

	$output_ = array( 1, 0 );
	$output_ = str_replace( $status, $output_, $data );

	$output = $data;/*apply_filters( 'wpmoly_movie_meta_link', array(
		'key' => 'adult',
		'value' => $output,
		'type'  => 'meta',
		'text'  => $output,
		'title' => _n( 'More movies for adults only', 'More movies for all audiences', $output_, 'wpmovielibrary' )
	) );*/
	$output = filter_empty( $output );

	return $output;
}

/**
 * Format movie homepage link.
 * 
 * @since    2.0.3
 * 
 * @param    string    $data Homepage link
 * 
 * @return   string    Formatted output
 */
function homepage( $data ) {

	if ( ! empty( $data ) ) {
		$data = sprintf( '<a href="%1$s" title="%2$s">%1$s</a>', $data, __( 'Official Website', 'wpmovielibrary' ) );
	}

	$output = filter_empty( $data );

	return $output;
}

/**
 * Format a Movie's misc field for display
 * 
 * @since    1.1
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
	return apply_filters( 'wpmoly/filter/meta/value/empty', $data );
}

/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *                             Aliases
 * 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

function actors( $data ) {
	return cast( $data );
}

function date( $data, $format = null ) {
	return release_date( $data, $format );
}

function local_release_date( $data, $format = null ) {
	return release_date( $data, $format );
}

function year( $data ) {
	return release_date( $data, $format = 'Y' );
}

function languages( $data ) {
	return spoken_languages( $data );
}

function countries( $data ) {
	return production_countries( $data );
}

function production( $data ) {
	return production_companies( $data );
}

/**
 * Format a Movie's media. If format is HTML, will return a
 * HTML formatted string; will return the value without change
 * if raw is asked.
 * 
 * @since    1.1
 * 
 * @param    string     $data detail value
 * @param    string     $format data format, raw or HTML
 * @param    boolean    $icon Show as icon or text
 * 
 * @return   string    Formatted output
 */
function media( $data, $format = 'html', $icon = false ) {

	return detail( 'media', $data, $format, $icon );
}

/**
 * Format a Movie's status. If format is HTML, will return a
 * HTML formatted string; will return the value without change
 * if raw is asked.
 * 
 * @since    1.1
 * 
 * @param    string     $data detail value
 * @param    string     $format data format, raw or HTML
 * @param    boolean    $icon Show as icon or text
 * 
 * @return   string    Formatted output
 */
function status( $data, $format = 'html', $icon = false ) {

	return detail( 'status', $data, $format, $icon );
}

/**
 * Format a Movie's rating. If format is HTML, will return a
 * HTML formatted string; will return the value without change
 * if raw is asked.
 * 
 * @since    1.1
 * 
 * @param    string     $data detail value
 * @param    string     $format data format, raw or HTML
 * 
 * @return   string    Formatted output
 */
function rating( $data, $format = 'html' ) {

	$format = ( 'raw' == $format ? 'raw' : 'html' );

	if ( '' == $data )
		return $data;

	if ( 'html' == $format ) {
		$data = apply_filters( 'wpmoly_movie_rating_stars', $data );
		$data = WPMovieLibrary::render_template( 'shortcodes/rating.php', array( 'data' => $data ), $require = 'always' );
	}

	return $data;
}

/**
 * Format a Movie's language. If format is HTML, will return a
 * HTML formatted string; will return the value without change
 * if raw is asked.
 * 
 * @since    2.0
 * 
 * @param    string     $data detail value
 * @param    string     $format data format, raw or HTML
 * 
 * @return   string    Formatted output
 */
function language( $data, $format = 'html' ) {

	$format = ( 'raw' == $format ? 'raw' : 'html' );

	if ( '' == $data )
		return $data;

	if ( wpmoly_o( 'details-icons' ) && 'html' == $format  ) {
		$view = 'shortcodes/detail-icon-title.php';
	} else if ( 'html' == $format ) {
		$view = 'shortcodes/detail.php';
	}

	$title = array();
	$lang  = WPMOLY_Settings::get_available_movie_language();

	if ( ! is_array( $data ) )
		$data = array( $data );

	foreach ( $data as $d )
		if ( isset( $lang[ $d ] ) )
			$title[] = __( $lang[ $d ], 'wpmovielibrary' );

	$data = WPMovieLibrary::render_template( $view, array( 'detail' => 'lang', 'data' => 'lang', 'title' => implode( ', ', $title ) ), $require = 'always' );

	return $data;
}

/**
 * Format a Movie's . If format is HTML, will return a
 * HTML formatted string; will return the value without change
 * if raw is asked.
 * 
 * @since    2.0
 * 
 * @param    string     $data detail value
 * @param    string     $format data format, raw or HTML
 * 
 * @return   string    Formatted output
 */
function subtitles( $data, $format = 'html' ) {

	$format = ( 'raw' == $format ? 'raw' : 'html' );

	if ( '' == $data )
		return $data;

	if ( wpmoly_o( 'details-icons' ) && 'html' == $format  ) {
		$view = 'shortcodes/detail-icon-title.php';
	} else if ( 'html' == $format ) {
		$view = 'shortcodes/detail.php';
	}

	$title = array();
	$lang  = WPMOLY_Settings::get_available_movie_language();

	if ( ! is_array( $data ) )
		$data = array( $data );

	foreach ( $data as $d ) {
		if ( 'none' == $d ) {
			$title = array( __( 'None', 'wpmovielibrary' ) );
			break;
		} elseif ( isset( $lang[ $d ] ) ) {
			$title[] = __( $lang[ $d ], 'wpmovielibrary' );
		}
	}

	$data = WPMovieLibrary::render_template( $view, array( 'detail' => 'subtitle', 'data' => 'subtitles', 'title' => implode( ', ', $title ) ), $require = 'always' );

	return $data;
}

/**
 * Format a Movie's . If format is HTML, will return a
 * HTML formatted string; will return the value without change
 * if raw is asked.
 * 
 * @since    2.0
 * 
 * @param    string     $data detail value
 * @param    string     $format data format, raw or HTML
 * @param    boolean    $icon Show as icon or text
 * 
 * @return   string    Formatted output
 */
function format( $data, $format = 'html', $icon = false ) {

	return detail( 'format', $data, $format, $icon );
}

/**
 * Format a Movie detail. If format is HTML, will return a
 * HTML formatted string; will return the value without change
 * if raw is asked.
 * 
 * @since    2.0
 * 
 * @param    string     $detail details slug
 * @param    string     $data detail value
 * @param    string     $format data format, raw or HTML
 * @param    boolean    $icon Show as icon or text
 * 
 * @return   string    Formatted output
 */
function detail( $detail, $data, $format = 'html', $icon = false ) {

	$format = ( 'raw' == $format ? 'raw' : 'html' );

	if ( '' == $data )
		return $data;

	if ( true === $icon || ( wpmoly_o( 'details-icons' ) && 'html' == $format ) ) {
		$view = 'shortcodes/detail-icon.php';
	} else {
		$view = 'shortcodes/detail.php';
	}

	$title = '';
	$default_fields = call_user_func( "WPMOLY_Settings::get_available_movie_{$detail}" );

	if ( ! is_array( $data ) )
		$data = array( $data );

	$_data = '';
	foreach ( $data as $d ) {
		if ( isset( $default_fields[ $d ] ) ) {
			$title = __( $default_fields[ $d ], 'wpmovielibrary' );
			$_data .= WPMovieLibrary::render_template( $view, array( 'detail' => $detail, 'data' => $d, 'title' => $title ), $require = 'always' );
		}
	}

	return $_data;
}

/**
 * Generate rating stars block.
 * 
 * @since    2.0
 * 
 * @param    float      $rating movie to turn into stars
 * @param    int        $post_id movie's post ID
 * @param    int        $base 5-stars or 10-stars?
 * 
 * @return   string    Formatted output
 */
function get_rating_stars( $rating, $post_id = null, $base = null, $include_empty = false ) {

	$defaults = WPMOLY_Settings::get_supported_movie_details();

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
	}

	/**
	 * Filter generated HTML markup.
	 * 
	 * @since    2.0
	 * 
	 * @param    string    Stars HTML markup
	 * @param    float     Rating value
	 */
	$stars = apply_filters( 'wpmoly_movie_rating_stars_html', $stars, $rating );

	return $stars;
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
 * @since    1.1
 * 
 * @param    string    $data field value
 * @param    string    $taxonomy taxonomy we're dealing with
 * 
 * @return   string    Formatted output
 */
function terms_list( $data, $taxonomy ) {

	$has_taxonomy = wpmoly_o( "enable-{$taxonomy}" );
	$_data = explode( ',', $data );

	foreach ( $_data as $key => $term ) {
		
		$term = trim( str_replace( array( '&#039;', "’" ), "'", $term ) );

		if ( $has_taxonomy ) {
			$_term = get_term_by( 'name', $term, $taxonomy );
			if ( ! $_term )
				$_term = get_term_by( 'slug', sanitize_title( $term ), $taxonomy );
		}
		else {
			$_term = $term;
		}

		if ( ! $_term )
			$_term = $term;

		if ( is_object( $_term ) && '' != $_term->name ) {
			$link = get_term_link( $_term, $taxonomy );
			$_term = ( is_wp_error( $link ) ? $_term->name : sprintf( '<a href="%s" title="%s">%s</a>', $link, sprintf( __( 'More movies from %s', 'wpmovielibrary' ), $_term->name ), $_term->name ) );
		}
		$_data[ $key ] = $_term;
	}

	$_data = ( ! empty( $_data ) ? implode( ', ', $_data ) : '&mdash;' );

	return $_data;
}