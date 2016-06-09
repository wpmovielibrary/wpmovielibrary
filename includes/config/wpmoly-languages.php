<?php
/**
 * WPMovieLibrary Config Languages definition
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	wp_die();

// Builtin restricted list for API support
$supported_languages = array(

	'bg' => __( 'Bulgarian', 'wpmovielibrary-iso' ),
	'cs' => __( 'Czech', 'wpmovielibrary-iso' ),
	'cn' => __( 'Cantonese', 'wpmovielibrary-iso' ),
	'da' => __( 'Danish', 'wpmovielibrary-iso' ),
	'de' => __( 'German', 'wpmovielibrary-iso' ),
	'el' => __( 'Greek', 'wpmovielibrary-iso' ),
	'en' => __( 'English', 'wpmovielibrary-iso' ),
	'es' => __( 'Spanish', 'wpmovielibrary-iso' ),
	'fa' => __( 'Farsi', 'wpmovielibrary-iso' ),
	'fi' => __( 'Finnish', 'wpmovielibrary-iso' ),
	'fr' => __( 'French', 'wpmovielibrary-iso' ),
	'he' => __( 'Hebrew', 'wpmovielibrary-iso' ),
	'hi' => __( 'Hindi', 'wpmovielibrary-iso' ),
	'hu' => __( 'Hungarian', 'wpmovielibrary-iso' ),
	'it' => __( 'Italian', 'wpmovielibrary-iso' ),
	'ja' => __( 'Japanese', 'wpmovielibrary-iso' ),
	'ko' => __( 'Korean', 'wpmovielibrary-iso' ),
	'nl' => __( 'Dutch', 'wpmovielibrary-iso' ),
	'no' => __( 'Norwegian', 'wpmovielibrary-iso' ),
	'pl' => __( 'Polish', 'wpmovielibrary-iso' ),
	'pt' => __( 'Portuguese', 'wpmovielibrary-iso' ),
	'ru' => __( 'Russian', 'wpmovielibrary-iso' ),
	'sv' => __( 'Swedish', 'wpmovielibrary-iso' ),
	'tr' => __( 'Turkish', 'wpmovielibrary-iso' ),
	'uk' => __( 'Ukrainian', 'wpmovielibrary-iso' ),
	'zh' => __( 'Chinese', 'wpmovielibrary-iso' )
);

// Builtin, non-exhaustive iso_639_1 matching array for translation
$languages = array(
	'native' => array(
		'af' => 'Afrikaans',
		'ar' => 'العربية',
		'bg' => 'български език',
		'cn' => '广州话 / 廣州話',
		'cs' => 'Český',
		'da' => 'Dansk',
		'de' => 'Deutsch',
		'el' => 'ελληνικά',
		'en' => 'English',
		'es' => 'Español',
		'fa' => 'فارسی',
		'fi' => 'Suomi',
		'fr' => 'Français',
		'he' => 'עִבְרִית',
		'hi' => 'हिन्दी',
		'hu' => 'Magyar',
		'it' => 'Italiano',
		'ja' => '日本語',
		'ko' => '한국어/조선말',
		'la' => 'Latin',
		'nb' => 'Bokmål',
		'nl' => 'Nederlands',
		'no' => 'Norsk',
		'ny' => 'chiCheŵa, chinyanja',
		'pl' => 'Polski',
		'pt' => 'Português',
		'ru' => 'Pусский',
		'sk' => 'Slovenčina',
		'st' => 'Sesotho',
		'sv' => 'Svenska',
		'ta' => 'தமிழ்',
		'th' => 'ภาษาไทย',
		'tr' => 'Türkçe',
		'uk' => 'Український',
		'zh' => '中国',
		'xh' => 'isiXhosa',
		'zu' => 'isiZulu',
	),
	'standard' => array(
		'af' => 'Afrikaans',
		'ar' => 'Arabic',
		'bg' => 'Bulgarian',
		'cn' => 'Cantonese',
		'cs' => 'Czech',
		'da' => 'Danish',
		'de' => 'German',
		'el' => 'Greek',
		'en' => 'English',
		'es' => 'Spanish',
		'fa' => 'Farsi',
		'fi' => 'Finnish',
		'fr' => 'French',
		'he' => 'Hebrew',
		'hi' => 'Hindi',
		'hu' => 'Hungarian',
		'it' => 'Italian',
		'ja' => 'Japanese',
		'ko' => 'Korean',
		'la' => 'Latin',
		'nb' => 'Norwegian BokmÃ¥l',
		'nl' => 'Dutch',
		'no' => 'Norwegian',
		'ny' => 'Chichewa',
		'pl' => 'Polish',
		'pt' => 'Portuguese',
		'ru' => 'Russian',
		'sk' => 'Slovak',
		'st' => 'Southern Sotho',
		'sv' => 'Swedish',
		'ta' => 'Tamil',
		'th' => 'Thai',
		'tr' => 'Turkish',
		'uk' => 'Ukrainian',
		'zh' => 'Chinese',
		'xh' => 'Xhosa',
		'zu' => 'Zulu'
	)
);
