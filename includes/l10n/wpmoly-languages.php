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
$wpmoly_supported_languages = array(

	'bg' => __( 'Bulgarian', 'wpmovielibrary-iso' ),
	'cs' => __( 'Czech', 'wpmovielibrary-iso' ),
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

// Builtin restricted list for API support
$wpmoly_supported_countries = array(
	'AR' => __( 'Argentina', 'wpmovielibrary-iso' ),
	'AM' => __( 'Armenia', 'wpmovielibrary-iso' ),
	'AU' => __( 'Australia', 'wpmovielibrary-iso' ),
	'AT' => __( 'Austria', 'wpmovielibrary-iso' ),
	'AZ' => __( 'Azerbaijan', 'wpmovielibrary-iso' ),
	'BH' => __( 'Bahrain', 'wpmovielibrary-iso' ),
	'BY' => __( 'Belarus', 'wpmovielibrary-iso' ),
	'BE' => __( 'Belgium', 'wpmovielibrary-iso' ),
	'BO' => __( 'Bolivia', 'wpmovielibrary-iso' ),
	'BR' => __( 'Brazil', 'wpmovielibrary-iso' ),
	'BG' => __( 'Bulgaria', 'wpmovielibrary-iso' ),
	'CL' => __( 'Chile', 'wpmovielibrary-iso' ),
	'CO' => __( 'Colombia', 'wpmovielibrary-iso' ),
	'HR' => __( 'Croatia', 'wpmovielibrary-iso' ),
	'CZ' => __( 'Czech Republic', 'wpmovielibrary-iso' ),
	'DK' => __( 'Denmark', 'wpmovielibrary-iso' ),
	'DO' => __( 'Dominican Republic', 'wpmovielibrary-iso' ),
	'EC' => __( 'Ecuador', 'wpmovielibrary-iso' ),
	'EE' => __( 'Estonia', 'wpmovielibrary-iso' ),
	'FJ' => __( 'Fiji', 'wpmovielibrary-iso' ),
	'FI' => __( 'Finland', 'wpmovielibrary-iso' ),
	'FR' => __( 'France', 'wpmovielibrary-iso' ),
	'DE' => __( 'Germany', 'wpmovielibrary-iso' ),
	'GR' => __( 'Greece', 'wpmovielibrary-iso' ),
	'HK' => __( 'Hong Kong', 'wpmovielibrary-iso' ),
	'HU' => __( 'Hungary', 'wpmovielibrary-iso' ),
	'IS' => __( 'Iceland', 'wpmovielibrary-iso' ),
	'IN' => __( 'India', 'wpmovielibrary-iso' ),
	'ID' => __( 'Indonesia', 'wpmovielibrary-iso' ),
	'IQ' => __( 'Iraq', 'wpmovielibrary-iso' ),
	'IE' => __( 'Ireland', 'wpmovielibrary-iso' ),
	'IL' => __( 'Israel', 'wpmovielibrary-iso' ),
	'IT' => __( 'Italy', 'wpmovielibrary-iso' ),
	'JP' => __( 'Japan', 'wpmovielibrary-iso' ),
	'JO' => __( 'Jordan', 'wpmovielibrary-iso' ),
	'KZ' => __( 'Kazakhstan', 'wpmovielibrary-iso' ),
	'KR' => __( 'South Korea', 'wpmovielibrary-iso' ),
	'KW' => __( 'Kuwait', 'wpmovielibrary-iso' ),
	'LV' => __( 'Latvia', 'wpmovielibrary-iso' ),
	'LB' => __( 'Lebanon', 'wpmovielibrary-iso' ),
	'LT' => __( 'Lithuania', 'wpmovielibrary-iso' ),
	'MY' => __( 'Malaysia', 'wpmovielibrary-iso' ),
	'MX' => __( 'Mexico', 'wpmovielibrary-iso' ),
	'NL' => __( 'Netherlands', 'wpmovielibrary-iso' ),
	'NZ' => __( 'New Zealand', 'wpmovielibrary-iso' ),
	'NO' => __( 'Norway', 'wpmovielibrary-iso' ),
	'OM' => __( 'Oman', 'wpmovielibrary-iso' ),
	'PG' => __( 'Papua New Guinea', 'wpmovielibrary-iso' ),
	'PE' => __( 'Peru', 'wpmovielibrary-iso' ),
	'PH' => __( 'Philippines', 'wpmovielibrary-iso' ),
	'PL' => __( 'Poland', 'wpmovielibrary-iso' ),
	'PT' => __( 'Portugal', 'wpmovielibrary-iso' ),
	'PR' => __( 'Puerto Rico', 'wpmovielibrary-iso' ),
	'QA' => __( 'Qatar', 'wpmovielibrary-iso' ),
	'RO' => __( 'Romania', 'wpmovielibrary-iso' ),
	'RU' => __( 'Russia', 'wpmovielibrary-iso' ),
	'SG' => __( 'Singapore', 'wpmovielibrary-iso' ),
	'SK' => __( 'Slovakia', 'wpmovielibrary-iso' ),
	'SI' => __( 'Slovenia', 'wpmovielibrary-iso' ),
	'ZA' => __( 'South Africa', 'wpmovielibrary-iso' ),
	'ES' => __( 'Spain', 'wpmovielibrary-iso' ),
	'SE' => __( 'Sweden', 'wpmovielibrary-iso' ),
	'TW' => __( 'Taiwan', 'wpmovielibrary-iso' ),
	'TH' => __( 'Thailand', 'wpmovielibrary-iso' ),
	'TR' => __( 'Turkey', 'wpmovielibrary-iso' ),
	'UA' => __( 'Ukraine', 'wpmovielibrary-iso' ),
	'AE' => __( 'United Arab Emirates', 'wpmovielibrary-iso' ),
	'GB' => __( 'United Kingdom', 'wpmovielibrary-iso' ),
	'US' => __( 'United States', 'wpmovielibrary-iso' ),
	'UY' => __( 'Uruguay', 'wpmovielibrary-iso' ),
	'VE' => __( 'Venezuela', 'wpmovielibrary-iso' ),
	'VN' => __( 'Viet Nam', 'wpmovielibrary-iso' )
);

// Builtin, non-exhaustive iso_639_1 matching array for translation
$wpmoly_languages = array(

	'af' => array(
		'native'   => 'Afrikaans',
		'name'     => __( 'Afrikaans', 'wpmovielibrary-iso' ),
		'standard' => 'Afrikaans'
	),
	'ar' => array(
		'native'   => 'العربية',
		'name'     => __( 'Arabic', 'wpmovielibrary-iso' ),
		'standard' => 'Arabic'
	),
	'bg' => array(
		'native'   => 'български език',
		'name'     => __( 'Bulgarian', 'wpmovielibrary-iso' ),
		'standard' => 'Bulgarian'
	),
	'cs' => array(
		'native'   => 'Český',
		'name'     => __( 'Czech', 'wpmovielibrary-iso' ),
		'standard' => 'Czech'
	),
	'da' => array(
		'native'   => 'Dansk',
		'name'     => __( 'Danish', 'wpmovielibrary-iso' ),
		'standard' => 'Danish'
	),
	'de' => array(
		'native'   => 'Deutsch',
		'name'     => __( 'German', 'wpmovielibrary-iso' ),
		'standard' => 'German'
	),
	'el' => array(
		'native'   => 'ελληνικά',
		'name'     => __( 'Greek', 'wpmovielibrary-iso' ),
		'standard' => 'Greek'
	),
	'en' => array(
		'native'   => 'English',
		'name'     => __( 'English', 'wpmovielibrary-iso' ),
		'standard' => 'English'
	),
	'es' => array(
		'native'   => 'Español',
		'name'     => __( 'Spanish', 'wpmovielibrary-iso' ),
		'standard' => 'Spanish'
	),
	'fa' => array(
		'native'   => 'فارسی',
		'name'     => __( 'Farsi', 'wpmovielibrary-iso' ),
		'standard' => 'Farsi'
	),
	'fi' => array(
		'native'   => 'Suomi',
		'name'     => __( 'Finnish', 'wpmovielibrary-iso' ),
		'standard' => 'Finnish'
	),
	'fr' => array(
		'native'   => 'Français',
		'name'     => __( 'French', 'wpmovielibrary-iso' ),
		'standard' => 'French'
	),
	'he' => array(
		'native'   => 'עִבְרִית',
		'name'     => __( 'Hebrew', 'wpmovielibrary-iso' ),
		'standard' => 'Hebrew'
	),
	'hi' => array(
		'native'   => 'हिन्दी',
		'name'     => __( 'Hindi', 'wpmovielibrary-iso' ),
		'standard' => 'Hindi'
	),
	'hu' => array(
		'native'   => 'Magyar',
		'name'     => __( 'Hungarian', 'wpmovielibrary-iso' ),
		'standard' => 'Hungarian'
	),
	'it' => array(
		'native'   => 'Italiano',
		'name'     => __( 'Italian', 'wpmovielibrary-iso' ),
		'standard' => 'Italian'
	),
	'ja' => array(
		'native'   => '日本語',
		'name'     => __( 'Japanese', 'wpmovielibrary-iso' ),
		'standard' => 'Japanese'
	),
	'ko' => array(
		'native'   => '한국어/조선어',
		'name'     => __( 'Korean', 'wpmovielibrary-iso' ),
		'standard' => 'Korean'
	),
	'nb' => array(
		'native'   => 'Bokmål',
		'name'     => __( 'Norwegian Bokmål', 'wpmovielibrary-iso' ),
		'standard' => 'Norwegian Bokmål'
	),
	'nl' => array(
		'native'   => 'Nederlands',
		'name'     => __( 'Dutch', 'wpmovielibrary-iso' ),
		'standard' => 'Dutch'
	),
	'no' => array(
		'native'   => 'Norsk',
		'name'     => __( 'Norwegian', 'wpmovielibrary-iso' ),
		'standard' => 'Norwegian'
	),
	'ny' => array(
		'native'   => 'chiCheŵa, chinyanja',
		'name'     => __( 'Chichewa', 'wpmovielibrary-iso' ),
		'standard' => 'Chichewa'
	),
	'pl' => array(
		'native'   => 'Polski',
		'name'     => __( 'Polish', 'wpmovielibrary-iso' ),
		'standard' => 'Polish'
	),
	'pt' => array(
		'native'   => 'Português',
		'name'     => __( 'Portuguese', 'wpmovielibrary-iso' ),
		'standard' => 'Portuguese'
	),
	'ru' => array(
		'native'   => 'Pусский',
		'name'     => __( 'Russian', 'wpmovielibrary-iso' ),
		'standard' => 'Russian'
	),
	'sk' => array(
		'native'   => 'Slovenčina',
		'name'     => __( 'Slovak', 'wpmovielibrary-iso' ),
		'standard' => 'Slovak'
	),
	'st' => array(
		'native'   => 'Sesotho',
		'name'     => __( 'Southern Sotho', 'wpmovielibrary-iso' ),
		'standard' => 'Southern Sotho'
	),
	'sv' => array(
		'native'   => 'Svenska',
		'name'     => __( 'Swedish', 'wpmovielibrary-iso' ),
		'standard' => 'Swedish'
	),
	'ta' => array(
		'native'   => 'தமிழ்',
		'name'     => __( 'Tamil', 'wpmovielibrary-iso' ),
		'standard' => 'Tamil'
	),
	'th' => array(
		'native'   => 'ภาษาไทย',
		'name'     => __( 'Thai', 'wpmovielibrary-iso' ),
		'standard' => 'Thai'
	),
	'tr' => array(
		'native'   => 'Türkçe',
		'name'     => __( 'Turkish', 'wpmovielibrary-iso' ),
		'standard' => 'Turkish'
	),
	'uk' => array(
		'native'   => 'Український',
		'name'     => __( 'Ukrainian', 'wpmovielibrary-iso' ),
		'standard' => 'Ukrainian'
	),
	'zh' => array(
		'native'   => '中国',
		'name'     => __( 'Chinese', 'wpmovielibrary-iso' ),
		'standard' => 'Chinese'
	),
	'xh' => array(
		'native'   => 'isiXhosa',
		'name'     => __( 'Xhosa', 'wpmovielibrary-iso' ),
		'standard' => 'Xhosa'
	),
	'zu' => array(
		'native'   => 'isiZulu',
		'name'     => __( 'Zulu', 'wpmovielibrary-iso' ),
		'standard' => 'Zulu'
	)
);

// Builtin iso_3166_1 matching array for translation
$wpmoly_countries = array(
	'AF' => array(
		'native' => 'Afghanistan',
		'name'   => __( 'Afghanistan', 'wpmovielibrary-iso' )
	),
	'AX' => array(
		'native' => 'Åland Islands',
		'name'   => __( 'Åland Islands', 'wpmovielibrary-iso' )
	),
	'AL' => array(
		'native' => 'Albania',
		'name'   => __( 'Albania', 'wpmovielibrary-iso' )
	),
	'DZ' => array(
		'native' => 'Algeria',
		'name'   => __( 'Algeria', 'wpmovielibrary-iso' )
	),
	'AS' => array(
		'native' => 'American Samoa',
		'name'   => __( 'American Samoa', 'wpmovielibrary-iso' )
	),
	'AD' => array(
		'native' => 'Andorra',
		'name'   => __( 'Andorra', 'wpmovielibrary-iso' )
	),
	'AO' => array(
		'native' => 'Angola',
		'name'   => __( 'Angola', 'wpmovielibrary-iso' )
	),
	'AI' => array(
		'native' => 'Anguilla',
		'name'   => __( 'Anguilla', 'wpmovielibrary-iso' )
	),
	'AQ' => array(
		'native' => 'Antarctica',
		'name'   => __( 'Antarctica', 'wpmovielibrary-iso' )
	),
	'AG' => array(
		'native' => 'Antigua and Barbuda',
		'name'   => __( 'Antigua and Barbuda', 'wpmovielibrary-iso' )
	),
	'AR' => array(
		'native' => 'Argentina',
		'name'   => __( 'Argentina', 'wpmovielibrary-iso' )
	),
	'AM' => array(
		'native' => 'Armenia',
		'name'   => __( 'Armenia', 'wpmovielibrary-iso' )
	),
	'AW' => array(
		'native' => 'Aruba',
		'name'   => __( 'Aruba', 'wpmovielibrary-iso' )
	),
	'AU' => array(
		'native' => 'Australia',
		'name'   => __( 'Australia', 'wpmovielibrary-iso' )
	),
	'AT' => array(
		'native' => 'Austria',
		'name'   => __( 'Austria', 'wpmovielibrary-iso' )
	),
	'AZ' => array(
		'native' => 'Azerbaijan',
		'name'   => __( 'Azerbaijan', 'wpmovielibrary-iso' )
	),
	'BS' => array(
		'native' => 'Bahamas',
		'name'   => __( 'Bahamas', 'wpmovielibrary-iso' )
	),
	'BH' => array(
		'native' => 'Bahrain',
		'name'   => __( 'Bahrain', 'wpmovielibrary-iso' )
	),
	'BD' => array(
		'native' => 'Bangladesh',
		'name'   => __( 'Bangladesh', 'wpmovielibrary-iso' )
	),
	'BB' => array(
		'native' => 'Barbados',
		'name'   => __( 'Barbados', 'wpmovielibrary-iso' )
	),
	'BY' => array(
		'native' => 'Belarus',
		'name'   => __( 'Belarus', 'wpmovielibrary-iso' )
	),
	'BE' => array(
		'native' => 'Belgium',
		'name'   => __( 'Belgium', 'wpmovielibrary-iso' )
	),
	'BZ' => array(
		'native' => 'Belize',
		'name'   => __( 'Belize', 'wpmovielibrary-iso' )
	),
	'BJ' => array(
		'native' => 'Benin',
		'name'   => __( 'Benin', 'wpmovielibrary-iso' )
	),
	'BM' => array(
		'native' => 'Bermuda',
		'name'   => __( 'Bermuda', 'wpmovielibrary-iso' )
	),
	'BT' => array(
		'native' => 'Bhutan',
		'name'   => __( 'Bhutan', 'wpmovielibrary-iso' )
	),
	'BO' => array(
		'native' => 'Bolivia, Plurinational State of',
		'name'   => __( 'Bolivia, Plurinational State of', 'wpmovielibrary-iso' )
	),
	'BQ' => array(
		'native' => 'Bonaire, Sint Eustatius and Saba',
		'name'   => __( 'Bonaire, Sint Eustatius and Saba', 'wpmovielibrary-iso' )
	),
	'BA' => array(
		'native' => 'Bosnia and Herzegovina',
		'name'   => __( 'Bosnia and Herzegovina', 'wpmovielibrary-iso' )
	),
	'BW' => array(
		'native' => 'Botswana',
		'name'   => __( 'Botswana', 'wpmovielibrary-iso' )
	),
	'BV' => array(
		'native' => 'Bouvet Island',
		'name'   => __( 'Bouvet Island', 'wpmovielibrary-iso' )
	),
	'BR' => array(
		'native' => 'Brazil',
		'name'   => __( 'Brazil', 'wpmovielibrary-iso' )
	),
	'IO' => array(
		'native' => 'British Indian Ocean Territory',
		'name'   => __( 'British Indian Ocean Territory', 'wpmovielibrary-iso' )
	),
	'BN' => array(
		'native' => 'Brunei Darussalam',
		'name'   => __( 'Brunei Darussalam', 'wpmovielibrary-iso' )
	),
	'BG' => array(
		'native' => 'Bulgaria',
		'name'   => __( 'Bulgaria', 'wpmovielibrary-iso' )
	),
	'BF' => array(
		'native' => 'Burkina Faso',
		'name'   => __( 'Burkina Faso', 'wpmovielibrary-iso' )
	),
	'BI' => array(
		'native' => 'Burundi',
		'name'   => __( 'Burundi', 'wpmovielibrary-iso' )
	),
	'KH' => array(
		'native' => 'Cambodia',
		'name'   => __( 'Cambodia', 'wpmovielibrary-iso' )
	),
	'CM' => array(
		'native' => 'Cameroon',
		'name'   => __( 'Cameroon', 'wpmovielibrary-iso' )
	),
	'CA' => array(
		'native' => 'Canada',
		'name'   => __( 'Canada', 'wpmovielibrary-iso' )
	),
	'CV' => array(
		'native' => 'Cape Verde',
		'name'   => __( 'Cape Verde', 'wpmovielibrary-iso' )
	),
	'KY' => array(
		'native' => 'Cayman Islands',
		'name'   => __( 'Cayman Islands', 'wpmovielibrary-iso' )
	),
	'CF' => array(
		'native' => 'Central African Republic',
		'name'   => __( 'Central African Republic', 'wpmovielibrary-iso' )
	),
	'TD' => array(
		'native' => 'Chad',
		'name'   => __( 'Chad', 'wpmovielibrary-iso' )
	),
	'CL' => array(
		'native' => 'Chile',
		'name'   => __( 'Chile', 'wpmovielibrary-iso' )
	),
	'CN' => array(
		'native' => 'China',
		'name'   => __( 'China', 'wpmovielibrary-iso' )
	),
	'CX' => array(
		'native' => 'Christmas Island',
		'name'   => __( 'Christmas Island', 'wpmovielibrary-iso' )
	),
	'CC' => array(
		'native' => 'Cocos Islands',
		'name'   => __( 'Cocos Islands', 'wpmovielibrary-iso' )
	),
	'CO' => array(
		'native' => 'Colombia',
		'name'   => __( 'Colombia', 'wpmovielibrary-iso' )
	),
	'KM' => array(
		'native' => 'Comoros',
		'name'   => __( 'Comoros', 'wpmovielibrary-iso' )
	),
	'CG' => array(
		'native' => 'Congo',
		'name'   => __( 'Congo', 'wpmovielibrary-iso' )
	),
	'CD' => array(
		'native' => 'Democratic Republic of Congo',
		'name'   => __( 'Democratic Republic of Congo', 'wpmovielibrary-iso' )
	),
	'CK' => array(
		'native' => 'Cook Islands',
		'name'   => __( 'Cook Islands', 'wpmovielibrary-iso' )
	),
	'CR' => array(
		'native' => 'Costa Rica',
		'name'   => __( 'Costa Rica', 'wpmovielibrary-iso' )
	),
	'CI' => array(
		'native' => 'Côte d’Ivoire',
		'name'   => __( 'Côte d’Ivoire', 'wpmovielibrary-iso' )
	),
	'HR' => array(
		'native' => 'Croatia',
		'name'   => __( 'Croatia', 'wpmovielibrary-iso' )
	),
	'CU' => array(
		'native' => 'Cuba',
		'name'   => __( 'Cuba', 'wpmovielibrary-iso' )
	),
	'CW' => array(
		'native' => 'Curaçao',
		'name'   => __( 'Curaçao', 'wpmovielibrary-iso' )
	),
	'CY' => array(
		'native' => 'Cyprus',
		'name'   => __( 'Cyprus', 'wpmovielibrary-iso' )
	),
	'CZ' => array(
		'native' => 'Czech Republic',
		'name'   => __( 'Czech Republic', 'wpmovielibrary-iso' )
	),
	'DK' => array(
		'native' => 'Denmark',
		'name'   => __( 'Denmark', 'wpmovielibrary-iso' )
	),
	'DJ' => array(
		'native' => 'Djibouti',
		'name'   => __( 'Djibouti', 'wpmovielibrary-iso' )
	),
	'DM' => array(
		'native' => 'Dominica',
		'name'   => __( 'Dominica', 'wpmovielibrary-iso' )
	),
	'DO' => array(
		'native' => 'Dominican Republic',
		'name'   => __( 'Dominican Republic', 'wpmovielibrary-iso' )
	),
	'EC' => array(
		'native' => 'Ecuador',
		'name'   => __( 'Ecuador', 'wpmovielibrary-iso' )
	),
	'EG' => array(
		'native' => 'Egypt',
		'name'   => __( 'Egypt', 'wpmovielibrary-iso' )
	),
	'SV' => array(
		'native' => 'El Salvador',
		'name'   => __( 'El Salvador', 'wpmovielibrary-iso' )
	),
	'GQ' => array(
		'native' => 'Equatorial Guinea',
		'name'   => __( 'Equatorial Guinea', 'wpmovielibrary-iso' )
	),
	'ER' => array(
		'native' => 'Eritrea',
		'name'   => __( 'Eritrea', 'wpmovielibrary-iso' )
	),
	'EE' => array(
		'native' => 'Estonia',
		'name'   => __( 'Estonia', 'wpmovielibrary-iso' )
	),
	'ET' => array(
		'native' => 'Ethiopia',
		'name'   => __( 'Ethiopia', 'wpmovielibrary-iso' )
	),
	'FK' => array(
		'native' => 'Falkland Islands',
		'name'   => __( 'Falkland Islands', 'wpmovielibrary-iso' )
	),
	'FO' => array(
		'native' => 'Faroe Islands',
		'name'   => __( 'Faroe Islands', 'wpmovielibrary-iso' )
	),
	'FJ' => array(
		'native' => 'Fiji',
		'name'   => __( 'Fiji', 'wpmovielibrary-iso' )
	),
	'FI' => array(
		'native' => 'Finland',
		'name'   => __( 'Finland', 'wpmovielibrary-iso' )
	),
	'FR' => array(
		'native' => 'France',
		'name'   => __( 'France', 'wpmovielibrary-iso' )
	),
	'GF' => array(
		'native' => 'French Guiana',
		'name'   => __( 'French Guiana', 'wpmovielibrary-iso' )
	),
	'PF' => array(
		'native' => 'French Polynesia',
		'name'   => __( 'French Polynesia', 'wpmovielibrary-iso' )
	),
	'TF' => array(
		'native' => 'French Southern Territories',
		'name'   => __( 'French Southern Territories', 'wpmovielibrary-iso' )
	),
	'GA' => array(
		'native' => 'Gabon',
		'name'   => __( 'Gabon', 'wpmovielibrary-iso' )
	),
	'GM' => array(
		'native' => 'Gambia',
		'name'   => __( 'Gambia', 'wpmovielibrary-iso' )
	),
	'GE' => array(
		'native' => 'Georgia',
		'name'   => __( 'Georgia', 'wpmovielibrary-iso' )
	),
	'DE' => array(
		'native' => 'Germany',
		'name'   => __( 'Germany', 'wpmovielibrary-iso' )
	),
	'GH' => array(
		'native' => 'Ghana',
		'name'   => __( 'Ghana', 'wpmovielibrary-iso' )
	),
	'GI' => array(
		'native' => 'Gibraltar',
		'name'   => __( 'Gibraltar', 'wpmovielibrary-iso' )
	),
	'GR' => array(
		'native' => 'Greece',
		'name'   => __( 'Greece', 'wpmovielibrary-iso' )
	),
	'GL' => array(
		'native' => 'Greenland',
		'name'   => __( 'Greenland', 'wpmovielibrary-iso' )
	),
	'GD' => array(
		'native' => 'Grenada',
		'name'   => __( 'Grenada', 'wpmovielibrary-iso' )
	),
	'GP' => array(
		'native' => 'Guadeloupe',
		'name'   => __( 'Guadeloupe', 'wpmovielibrary-iso' )
	),
	'GU' => array(
		'native' => 'Guam',
		'name'   => __( 'Guam', 'wpmovielibrary-iso' )
	),
	'GT' => array(
		'native' => 'Guatemala',
		'name'   => __( 'Guatemala', 'wpmovielibrary-iso' )
	),
	'GG' => array(
		'native' => 'Guernsey',
		'name'   => __( 'Guernsey', 'wpmovielibrary-iso' )
	),
	'GN' => array(
		'native' => 'Guinea',
		'name'   => __( 'Guinea', 'wpmovielibrary-iso' )
	),
	'GW' => array(
		'native' => 'Guinea-Bissau',
		'name'   => __( 'Guinea-Bissau', 'wpmovielibrary-iso' )
	),
	'GY' => array(
		'native' => 'Guyana',
		'name'   => __( 'Guyana', 'wpmovielibrary-iso' )
	),
	'HT' => array(
		'native' => 'Haiti',
		'name'   => __( 'Haiti', 'wpmovielibrary-iso' )
	),
	'HM' => array(
		'native' => 'Heard Island and McDonald Islands',
		'name'   => __( 'Heard Island and McDonald Islands', 'wpmovielibrary-iso' )
	),
	'VA' => array(
		'native' => 'Vatican',
		'name'   => __( 'Vatican', 'wpmovielibrary-iso' )
	),
	'HN' => array(
		'native' => 'Honduras',
		'name'   => __( 'Honduras', 'wpmovielibrary-iso' )
	),
	'HK' => array(
		'native' => 'Hong Kong',
		'name'   => __( 'Hong Kong', 'wpmovielibrary-iso' )
	),
	'HU' => array(
		'native' => 'Hungary',
		'name'   => __( 'Hungary', 'wpmovielibrary-iso' )
	),
	'IS' => array(
		'native' => 'Iceland',
		'name'   => __( 'Iceland', 'wpmovielibrary-iso' )
	),
	'IN' => array(
		'native' => 'India',
		'name'   => __( 'India', 'wpmovielibrary-iso' )
	),
	'ID' => array(
		'native' => 'Indonesia',
		'name'   => __( 'Indonesia', 'wpmovielibrary-iso' )
	),
	'IR' => array(
		'native' => 'Iran',
		'name'   => __( 'Iran', 'wpmovielibrary-iso' )
	),
	'IQ' => array(
		'native' => 'Iraq',
		'name'   => __( 'Iraq', 'wpmovielibrary-iso' )
	),
	'IE' => array(
		'native' => 'Ireland',
		'name'   => __( 'Ireland', 'wpmovielibrary-iso' )
	),
	'IM' => array(
		'native' => 'Isle of Man',
		'name'   => __( 'Isle of Man', 'wpmovielibrary-iso' )
	),
	'IL' => array(
		'native' => 'Israel',
		'name'   => __( 'Israel', 'wpmovielibrary-iso' )
	),
	'IT' => array(
		'native' => 'Italy',
		'name'   => __( 'Italy', 'wpmovielibrary-iso' )
	),
	'JM' => array(
		'native' => 'Jamaica',
		'name'   => __( 'Jamaica', 'wpmovielibrary-iso' )
	),
	'JP' => array(
		'native' => 'Japan',
		'name'   => __( 'Japan', 'wpmovielibrary-iso' )
	),
	'JE' => array(
		'native' => 'Jersey',
		'name'   => __( 'Jersey', 'wpmovielibrary-iso' )
	),
	'JO' => array(
		'native' => 'Jordan',
		'name'   => __( 'Jordan', 'wpmovielibrary-iso' )
	),
	'KZ' => array(
		'native' => 'Kazakhstan',
		'name'   => __( 'Kazakhstan', 'wpmovielibrary-iso' )
	),
	'KE' => array(
		'native' => 'Kenya',
		'name'   => __( 'Kenya', 'wpmovielibrary-iso' )
	),
	'KI' => array(
		'native' => 'Kiribati',
		'name'   => __( 'Kiribati', 'wpmovielibrary-iso' )
	),
	'KP' => array(
		'native' => 'North Korea',
		'name'   => __( 'North Korea', 'wpmovielibrary-iso' )
	),
	'KR' => array(
		'native' => 'South Korea',
		'name'   => __( 'South Korea', 'wpmovielibrary-iso' )
	),
	'KW' => array(
		'native' => 'Kuwait',
		'name'   => __( 'Kuwait', 'wpmovielibrary-iso' )
	),
	'KG' => array(
		'native' => 'Kyrgyzstan',
		'name'   => __( 'Kyrgyzstan', 'wpmovielibrary-iso' )
	),
	'LA' => array(
		'native' => 'Lao',
		'name'   => __( 'Lao', 'wpmovielibrary-iso' )
	),
	'LV' => array(
		'native' => 'Latvia',
		'name'   => __( 'Latvia', 'wpmovielibrary-iso' )
	),
	'LB' => array(
		'native' => 'Lebanon',
		'name'   => __( 'Lebanon', 'wpmovielibrary-iso' )
	),
	'LS' => array(
		'native' => 'Lesotho',
		'name'   => __( 'Lesotho', 'wpmovielibrary-iso' )
	),
	'LR' => array(
		'native' => 'Liberia',
		'name'   => __( 'Liberia', 'wpmovielibrary-iso' )
	),
	'LY' => array(
		'native' => 'Libya',
		'name'   => __( 'Libya', 'wpmovielibrary-iso' )
	),
	'LI' => array(
		'native' => 'Liechtenstein',
		'name'   => __( 'Liechtenstein', 'wpmovielibrary-iso' )
	),
	'LT' => array(
		'native' => 'Lithuania',
		'name'   => __( 'Lithuania', 'wpmovielibrary-iso' )
	),
	'LU' => array(
		'native' => 'Luxembourg',
		'name'   => __( 'Luxembourg', 'wpmovielibrary-iso' )
	),
	'MO' => array(
		'native' => 'Macao',
		'name'   => __( 'Macao', 'wpmovielibrary-iso' )
	),
	'MK' => array(
		'native' => 'Macedonia',
		'name'   => __( 'Macedonia', 'wpmovielibrary-iso' )
	),
	'MG' => array(
		'native' => 'Madagascar',
		'name'   => __( 'Madagascar', 'wpmovielibrary-iso' )
	),
	'MW' => array(
		'native' => 'Malawi',
		'name'   => __( 'Malawi', 'wpmovielibrary-iso' )
	),
	'MY' => array(
		'native' => 'Malaysia',
		'name'   => __( 'Malaysia', 'wpmovielibrary-iso' )
	),
	'MV' => array(
		'native' => 'Maldives',
		'name'   => __( 'Maldives', 'wpmovielibrary-iso' )
	),
	'ML' => array(
		'native' => 'Mali',
		'name'   => __( 'Mali', 'wpmovielibrary-iso' )
	),
	'MT' => array(
		'native' => 'Malta',
		'name'   => __( 'Malta', 'wpmovielibrary-iso' )
	),
	'MH' => array(
		'native' => 'Marshall Islands',
		'name'   => __( 'Marshall Islands', 'wpmovielibrary-iso' )
	),
	'MQ' => array(
		'native' => 'Martinique',
		'name'   => __( 'Martinique', 'wpmovielibrary-iso' )
	),
	'MR' => array(
		'native' => 'Mauritania',
		'name'   => __( 'Mauritania', 'wpmovielibrary-iso' )
	),
	'MU' => array(
		'native' => 'Mauritius',
		'name'   => __( 'Mauritius', 'wpmovielibrary-iso' )
	),
	'YT' => array(
		'native' => 'Mayotte',
		'name'   => __( 'Mayotte', 'wpmovielibrary-iso' )
	),
	'MX' => array(
		'native' => 'Mexico',
		'name'   => __( 'Mexico', 'wpmovielibrary-iso' )
	),
	'FM' => array(
		'native' => 'Micronesia',
		'name'   => __( 'Micronesia', 'wpmovielibrary-iso' )
	),
	'MD' => array(
		'native' => 'Moldova',
		'name'   => __( 'Moldova', 'wpmovielibrary-iso' )
	),
	'MC' => array(
		'native' => 'Monaco',
		'name'   => __( 'Monaco', 'wpmovielibrary-iso' )
	),
	'MN' => array(
		'native' => 'Mongolia',
		'name'   => __( 'Mongolia', 'wpmovielibrary-iso' )
	),
	'ME' => array(
		'native' => 'Montenegro',
		'name'   => __( 'Montenegro', 'wpmovielibrary-iso' )
	),
	'MS' => array(
		'native' => 'Montserrat',
		'name'   => __( 'Montserrat', 'wpmovielibrary-iso' )
	),
	'MA' => array(
		'native' => 'Morocco',
		'name'   => __( 'Morocco', 'wpmovielibrary-iso' )
	),
	'MZ' => array(
		'native' => 'Mozambique',
		'name'   => __( 'Mozambique', 'wpmovielibrary-iso' )
	),
	'MM' => array(
		'native' => 'Myanmar',
		'name'   => __( 'Myanmar', 'wpmovielibrary-iso' )
	),
	'NA' => array(
		'native' => 'Namibia',
		'name'   => __( 'Namibia', 'wpmovielibrary-iso' )
	),
	'NR' => array(
		'native' => 'Nauru',
		'name'   => __( 'Nauru', 'wpmovielibrary-iso' )
	),
	'NP' => array(
		'native' => 'Nepal',
		'name'   => __( 'Nepal', 'wpmovielibrary-iso' )
	),
	'NL' => array(
		'native' => 'Netherlands',
		'name'   => __( 'Netherlands', 'wpmovielibrary-iso' )
	),
	'NC' => array(
		'native' => 'New Caledonia',
		'name'   => __( 'New Caledonia', 'wpmovielibrary-iso' )
	),
	'NZ' => array(
		'native' => 'New Zealand',
		'name'   => __( 'New Zealand', 'wpmovielibrary-iso' )
	),
	'NI' => array(
		'native' => 'Nicaragua',
		'name'   => __( 'Nicaragua', 'wpmovielibrary-iso' )
	),
	'NE' => array(
		'native' => 'Niger',
		'name'   => __( 'Niger', 'wpmovielibrary-iso' )
	),
	'NG' => array(
		'native' => 'Nigeria',
		'name'   => __( 'Nigeria', 'wpmovielibrary-iso' )
	),
	'NU' => array(
		'native' => 'Niue',
		'name'   => __( 'Niue', 'wpmovielibrary-iso' )
	),
	'NF' => array(
		'native' => 'Norfolk Island',
		'name'   => __( 'Norfolk Island', 'wpmovielibrary-iso' )
	),
	'MP' => array(
		'native' => 'Northern Mariana Islands',
		'name'   => __( 'Northern Mariana Islands', 'wpmovielibrary-iso' )
	),
	'NO' => array(
		'native' => 'Norway',
		'name'   => __( 'Norway', 'wpmovielibrary-iso' )
	),
	'OM' => array(
		'native' => 'Oman',
		'name'   => __( 'Oman', 'wpmovielibrary-iso' )
	),
	'PK' => array(
		'native' => 'Pakistan',
		'name'   => __( 'Pakistan', 'wpmovielibrary-iso' )
	),
	'PW' => array(
		'native' => 'Palau',
		'name'   => __( 'Palau', 'wpmovielibrary-iso' )
	),
	'PS' => array(
		'native' => 'Palestine',
		'name'   => __( 'Palestine', 'wpmovielibrary-iso' )
	),
	'PA' => array(
		'native' => 'Panama',
		'name'   => __( 'Panama', 'wpmovielibrary-iso' )
	),
	'PG' => array(
		'native' => 'Papua New Guinea',
		'name'   => __( 'Papua New Guinea', 'wpmovielibrary-iso' )
	),
	'PY' => array(
		'native' => 'Paraguay',
		'name'   => __( 'Paraguay', 'wpmovielibrary-iso' )
	),
	'PE' => array(
		'native' => 'Peru',
		'name'   => __( 'Peru', 'wpmovielibrary-iso' )
	),
	'PH' => array(
		'native' => 'Philippines',
		'name'   => __( 'Philippines', 'wpmovielibrary-iso' )
	),
	'PN' => array(
		'native' => 'Pitcairn',
		'name'   => __( 'Pitcairn', 'wpmovielibrary-iso' )
	),
	'PL' => array(
		'native' => 'Poland',
		'name'   => __( 'Poland', 'wpmovielibrary-iso' )
	),
	'PT' => array(
		'native' => 'Portugal',
		'name'   => __( 'Portugal', 'wpmovielibrary-iso' )
	),
	'PR' => array(
		'native' => 'Puerto Rico',
		'name'   => __( 'Puerto Rico', 'wpmovielibrary-iso' )
	),
	'QA' => array(
		'native' => 'Qatar',
		'name'   => __( 'Qatar', 'wpmovielibrary-iso' )
	),
	'RE' => array(
		'native' => 'Réunion',
		'name'   => __( 'Réunion', 'wpmovielibrary-iso' )
	),
	'RO' => array(
		'native' => 'Romania',
		'name'   => __( 'Romania', 'wpmovielibrary-iso' )
	),
	'RU' => array(
		'native' => 'Russia',
		'name'   => __( 'Russia', 'wpmovielibrary-iso' )
	),
	'RW' => array(
		'native' => 'Rwanda',
		'name'   => __( 'Rwanda', 'wpmovielibrary-iso' )
	),
	'BL' => array(
		'native' => 'Saint Barthélemy',
		'name'   => __( 'Saint Barthélemy', 'wpmovielibrary-iso' )
	),
	'SH' => array(
		'native' => 'Saint Helena, Ascension and Tristan da Cunha',
		'name'   => __( 'Saint Helena, Ascension and Tristan da Cunha', 'wpmovielibrary-iso' )
	),
	'KN' => array(
		'native' => 'Saint Kitts and Nevis',
		'name'   => __( 'Saint Kitts and Nevis', 'wpmovielibrary-iso' )
	),
	'LC' => array(
		'native' => 'Saint Lucia',
		'name'   => __( 'Saint Lucia', 'wpmovielibrary-iso' )
	),
	'MF' => array(
		'native' => 'Saint Martin',
		'name'   => __( 'Saint Martin', 'wpmovielibrary-iso' )
	),
	'PM' => array(
		'native' => 'Saint Pierre and Miquelon',
		'name'   => __( 'Saint Pierre and Miquelon', 'wpmovielibrary-iso' )
	),
	'VC' => array(
		'native' => 'Saint Vincent and the Grenadines',
		'name'   => __( 'Saint Vincent and the Grenadines', 'wpmovielibrary-iso' )
	),
	'WS' => array(
		'native' => 'Samoa',
		'name'   => __( 'Samoa', 'wpmovielibrary-iso' )
	),
	'SM' => array(
		'native' => 'San Marino',
		'name'   => __( 'San Marino', 'wpmovielibrary-iso' )
	),
	'ST' => array(
		'native' => 'Sao Tome and Principe',
		'name'   => __( 'Sao Tome and Principe', 'wpmovielibrary-iso' )
	),
	'SA' => array(
		'native' => 'Saudi Arabia',
		'name'   => __( 'Saudi Arabia', 'wpmovielibrary-iso' )
	),
	'SN' => array(
		'native' => 'Senegal',
		'name'   => __( 'Senegal', 'wpmovielibrary-iso' )
	),
	'RS' => array(
		'native' => 'Serbia',
		'name'   => __( 'Serbia', 'wpmovielibrary-iso' )
	),
	'SC' => array(
		'native' => 'Seychelles',
		'name'   => __( 'Seychelles', 'wpmovielibrary-iso' )
	),
	'SL' => array(
		'native' => 'Sierra Leone',
		'name'   => __( 'Sierra Leone', 'wpmovielibrary-iso' )
	),
	'SG' => array(
		'native' => 'Singapore',
		'name'   => __( 'Singapore', 'wpmovielibrary-iso' )
	),
	'SX' => array(
		'native' => 'Sint Maarten',
		'name'   => __( 'Sint Maarten', 'wpmovielibrary-iso' )
	),
	'SK' => array(
		'native' => 'Slovakia',
		'name'   => __( 'Slovakia', 'wpmovielibrary-iso' )
	),
	'SI' => array(
		'native' => 'Slovenia',
		'name'   => __( 'Slovenia', 'wpmovielibrary-iso' )
	),
	'SB' => array(
		'native' => 'Solomon Islands',
		'name'   => __( 'Solomon Islands', 'wpmovielibrary-iso' )
	),
	'SO' => array(
		'native' => 'Somalia',
		'name'   => __( 'Somalia', 'wpmovielibrary-iso' )
	),
	'ZA' => array(
		'native' => 'South Africa',
		'name'   => __( 'South Africa', 'wpmovielibrary-iso' )
	),
	'GS' => array(
		'native' => 'South Georgia and the South Sandwich Islands',
		'name'   => __( 'South Georgia and the South Sandwich Islands', 'wpmovielibrary-iso' )
	),
	'SS' => array(
		'native' => 'South Sudan',
		'name'   => __( 'South Sudan', 'wpmovielibrary-iso' )
	),
	'ES' => array(
		'native' => 'Spain',
		'name'   => __( 'Spain', 'wpmovielibrary-iso' )
	),
	'LK' => array(
		'native' => 'Sri Lanka',
		'name'   => __( 'Sri Lanka', 'wpmovielibrary-iso' )
	),
	'SD' => array(
		'native' => 'Sudan',
		'name'   => __( 'Sudan', 'wpmovielibrary-iso' )
	),
	'SR' => array(
		'native' => 'Suriname',
		'name'   => __( 'Suriname', 'wpmovielibrary-iso' )
	),
	'SJ' => array(
		'native' => 'Svalbard and Jan Mayen',
		'name'   => __( 'Svalbard and Jan Mayen', 'wpmovielibrary-iso' )
	),
	'SZ' => array(
		'native' => 'Swaziland',
		'name'   => __( 'Swaziland', 'wpmovielibrary-iso' )
	),
	'SE' => array(
		'native' => 'Sweden',
		'name'   => __( 'Sweden', 'wpmovielibrary-iso' )
	),
	'CH' => array(
		'native' => 'Switzerland',
		'name'   => __( 'Switzerland', 'wpmovielibrary-iso' )
	),
	'SY' => array(
		'native' => 'Syria',
		'name'   => __( 'Syria', 'wpmovielibrary-iso' )
	),
	'TW' => array(
		'native' => 'Taiwan',
		'name'   => __( 'Taiwan', 'wpmovielibrary-iso' )
	),
	'TJ' => array(
		'native' => 'Tajikistan',
		'name'   => __( 'Tajikistan', 'wpmovielibrary-iso' )
	),
	'TZ' => array(
		'native' => 'Tanzania',
		'name'   => __( 'Tanzania', 'wpmovielibrary-iso' )
	),
	'TH' => array(
		'native' => 'Thailand',
		'name'   => __( 'Thailand', 'wpmovielibrary-iso' )
	),
	'TL' => array(
		'native' => 'Timor-Leste',
		'name'   => __( 'Timor-Leste', 'wpmovielibrary-iso' )
	),
	'TG' => array(
		'native' => 'Togo',
		'name'   => __( 'Togo', 'wpmovielibrary-iso' )
	),
	'TK' => array(
		'native' => 'Tokelau',
		'name'   => __( 'Tokelau', 'wpmovielibrary-iso' )
	),
	'TO' => array(
		'native' => 'Tonga',
		'name'   => __( 'Tonga', 'wpmovielibrary-iso' )
	),
	'TT' => array(
		'native' => 'Trinidad and Tobago',
		'name'   => __( 'Trinidad and Tobago', 'wpmovielibrary-iso' )
	),
	'TN' => array(
		'native' => 'Tunisia',
		'name'   => __( 'Tunisia', 'wpmovielibrary-iso' )
	),
	'TR' => array(
		'native' => 'Turkey',
		'name'   => __( 'Turkey', 'wpmovielibrary-iso' )
	),
	'TM' => array(
		'native' => 'Turkmenistan',
		'name'   => __( 'Turkmenistan', 'wpmovielibrary-iso' )
	),
	'TC' => array(
		'native' => 'Turks and Caicos Islands',
		'name'   => __( 'Turks and Caicos Islands', 'wpmovielibrary-iso' )
	),
	'TV' => array(
		'native' => 'Tuvalu',
		'name'   => __( 'Tuvalu', 'wpmovielibrary-iso' )
	),
	'UG' => array(
		'native' => 'Uganda',
		'name'   => __( 'Uganda', 'wpmovielibrary-iso' )
	),
	'UA' => array(
		'native' => 'Ukraine',
		'name'   => __( 'Ukraine', 'wpmovielibrary-iso' )
	),
	'AE' => array(
		'native' => 'United Arab Emirates',
		'name'   => __( 'United Arab Emirates', 'wpmovielibrary-iso' )
	),
	'GB' => array(
		'native' => 'United Kingdom',
		'name'   => __( 'United Kingdom', 'wpmovielibrary-iso' )
	),
	'US' => array(
		'native' => 'United States of America',
		'name'   => __( 'United States', 'wpmovielibrary-iso' )
	),
	'UM' => array(
		'native' => 'United States Minor Outlying Islands',
		'name'   => __( 'United States Minor Outlying Islands', 'wpmovielibrary-iso' )
	),
	'UY' => array(
		'native' => 'Uruguay',
		'name'   => __( 'Uruguay', 'wpmovielibrary-iso' )
	),
	'UZ' => array(
		'native' => 'Uzbekistan',
		'name'   => __( 'Uzbekistan', 'wpmovielibrary-iso' )
	),
	'VU' => array(
		'native' => 'Vanuatu',
		'name'   => __( 'Vanuatu', 'wpmovielibrary-iso' )
	),
	'VE' => array(
		'native' => 'Venezuela',
		'name'   => __( 'Venezuela', 'wpmovielibrary-iso' )
	),
	'VN' => array(
		'native' => 'Viet Nam',
		'name'   => __( 'Viet Nam', 'wpmovielibrary-iso' )
	),
	'VG' => array(
		'native' => 'British Virgin Islands',
		'name'   => __( 'British Virgin Islands', 'wpmovielibrary-iso' )
	),
	'VI' => array(
		'native' => 'U.S Virgin Islands.',
		'name'   => __( 'U.S Virgin Islands.', 'wpmovielibrary-iso' )
	),
	'WF' => array(
		'native' => 'Wallis and Futuna',
		'name'   => __( 'Wallis and Futuna', 'wpmovielibrary-iso' )
	),
	'EH' => array(
		'native' => 'Western Sahara',
		'name'   => __( 'Western Sahara', 'wpmovielibrary-iso' )
	),
	'YE' => array(
		'native' => 'Yemen',
		'name'   => __( 'Yemen', 'wpmovielibrary-iso' )
	),
	'ZM' => array(
		'native' => 'Zambia',
		'name'   => __( 'Zambia', 'wpmovielibrary-iso' )
	),
	'ZW' => array(
		'native' => 'Zimbabwe',
		'name'   => __( 'Zimbabwe', 'wpmovielibrary-iso' )
	),
);

