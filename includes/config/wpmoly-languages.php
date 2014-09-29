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

$wpmoly_languages = array(

// 	'' => array(
// 		'native' => '',
// 		'name'   => __( '', 'wpmovielibrary-iso' )
// 	),

	'af' => array(
		'native' => 'Afrikaans',
		'name'   => __( 'Afrikaans', 'wpmovielibrary-iso' )
	),
	'ar' => array(
		'native' => 'العربية',
		'name'   => __( 'Arabic', 'wpmovielibrary-iso' )
	),
	'bg' => array(
		'native' => 'български език',
		'name'   => __( 'Bulgarian', 'wpmovielibrary-iso' )
	),
	'cs' => array(
		'native' => 'Český',
		'name'   => __( 'Czech', 'wpmovielibrary-iso' )
	),
	'da' => array(
		'native' => 'Dansk',
		'name'   => __( 'Danish', 'wpmovielibrary-iso' )
	),
	'de' => array(
		'native' => 'Deutsch',
		'name'   => __( 'German', 'wpmovielibrary-iso' )
	),
	'el' => array(
		'native' => 'ελληνικά',
		'name'   => __( 'Greek', 'wpmovielibrary-iso' )
	),
	'en' => array(
		'native' => 'English',
		'name'   => __( 'English', 'wpmovielibrary-iso' )
	),
	'es' => array(
		'native' => 'Español',
		'name'   => __( 'Spanish', 'wpmovielibrary-iso' )
	),
	'fa' => array(
		'native' => 'فارسی',
		'name'   => __( 'Farsi', 'wpmovielibrary-iso' )
	),
	'fi' => array(
		'native' => 'Suomi',
		'name'   => __( 'Finnish', 'wpmovielibrary-iso' )
	),
	'fr' => array(
		'native' => 'Français',
		'name'   => __( 'French', 'wpmovielibrary-iso' )
	),
	'he' => array(
		'native' => 'עִבְרִית',
		'name'   => __( 'Hebrew', 'wpmovielibrary-iso' )
	),
	'hi' => array(
		'native' => 'हिन्दी',
		'name'   => __( 'Hindi', 'wpmovielibrary-iso' )
	),
	'hu' => array(
		'native' => 'Magyar',
		'name'   => __( 'Hungarian', 'wpmovielibrary-iso' )
	),
	'it' => array(
		'native' => 'Italiano',
		'name'   => __( 'Italian', 'wpmovielibrary-iso' )
	),
	'ja' => array(
		'native' => '日本語',
		'name'   => __( 'Japanese', 'wpmovielibrary-iso' )
	),
	'ko' => array(
		'native' => '한국어/조선어',
		'name'   => __( 'Korean', 'wpmovielibrary-iso' )
	),
	'nb' => array(
		'native' => 'Bokmål',
		'name'   => __( 'Norwegian Bokmål', 'wpmovielibrary-iso' )
	),
	'nl' => array(
		'native' => 'Nederlands',
		'name'   => __( 'Dutch', 'wpmovielibrary-iso' )
	),
	'no' => array(
		'native' => 'Norsk',
		'name'   => __( 'Norwegian', 'wpmovielibrary-iso' )
	),
	'ny' => array(
		'native' => 'chiCheŵa, chinyanja',
		'name'   => __( 'Chichewa', 'wpmovielibrary-iso' )
	),
	'pl' => array(
		'native' => 'Polski',
		'name'   => __( 'Polish', 'wpmovielibrary-iso' )
	),
	'pt' => array(
		'native' => 'Português',
		'name'   => __( 'Portuguese', 'wpmovielibrary-iso' )
	),
	'ru' => array(
		'native' => 'Pусский',
		'name'   => __( 'Russian', 'wpmovielibrary-iso' )
	),
	'sk' => array(
		'native' => 'Slovenčina',
		'name'   => __( 'Slovak', 'wpmovielibrary-iso' )
	),
	'st' => array(
		'native' => 'Sesotho',
		'name'   => __( 'Southern Sotho', 'wpmovielibrary-iso' )
	),
	'sv' => array(
		'native' => 'Svenska',
		'name'   => __( 'Swedish', 'wpmovielibrary-iso' )
	),
	'ta' => array(
		'native' => 'தமிழ்',
		'name'   => __( 'Tamil', 'wpmovielibrary-iso' )
	),
	'th' => array(
		'native' => 'ภาษาไทย',
		'name'   => __( 'Thai', 'wpmovielibrary-iso' )
	),
	'tr' => array(
		'native' => 'Türkçe',
		'name'   => __( 'Turkish', 'wpmovielibrary-iso' )
	),
	'uk' => array(
		'native' => 'Український',
		'name'   => __( 'Ukrainian', 'wpmovielibrary-iso' )
	),
	'zh' => array(
		'native' => '中国',
		'name'   => __( 'Chinese', 'wpmovielibrary-iso' )
	),
	'xh' => array(
		'native' => 'isiXhosa',
		'name'   => __( 'Xhosa', 'wpmovielibrary-iso' )
	),
	'zu' => array(
		'native' => 'isiZulu',
		'name'   => __( 'Zulu', 'wpmovielibrary-iso' )
	)
);

$wpmoly_countries = array(
	'AF' => __( 'Afghanistan', 'wpmovielibrary-iso' ),
	'AX' => __( 'Åland Islands', 'wpmovielibrary-iso' ),
	'AL' => __( 'Albania', 'wpmovielibrary-iso' ),
	'DZ' => __( 'Algeria', 'wpmovielibrary-iso' ),
	'AS' => __( 'American Samoa', 'wpmovielibrary-iso' ),
	'AD' => __( 'Andorra', 'wpmovielibrary-iso' ),
	'AO' => __( 'Angola', 'wpmovielibrary-iso' ),
	'AI' => __( 'Anguilla', 'wpmovielibrary-iso' ),
	'AQ' => __( 'Antarctica', 'wpmovielibrary-iso' ),
	'AG' => __( 'Antigua and Barbuda', 'wpmovielibrary-iso' ),
	'AR' => __( 'Argentina', 'wpmovielibrary-iso' ),
	'AM' => __( 'Armenia', 'wpmovielibrary-iso' ),
	'AW' => __( 'Aruba', 'wpmovielibrary-iso' ),
	'AU' => __( 'Australia', 'wpmovielibrary-iso' ),
	'AT' => __( 'Austria', 'wpmovielibrary-iso' ),
	'AZ' => __( 'Azerbaijan', 'wpmovielibrary-iso' ),
	'BS' => __( 'Bahamas', 'wpmovielibrary-iso' ),
	'BH' => __( 'Bahrain', 'wpmovielibrary-iso' ),
	'BD' => __( 'Bangladesh', 'wpmovielibrary-iso' ),
	'BB' => __( 'Barbados', 'wpmovielibrary-iso' ),
	'BY' => __( 'Belarus', 'wpmovielibrary-iso' ),
	'BE' => __( 'Belgium', 'wpmovielibrary-iso' ),
	'BZ' => __( 'Belize', 'wpmovielibrary-iso' ),
	'BJ' => __( 'Benin', 'wpmovielibrary-iso' ),
	'BM' => __( 'Bermuda', 'wpmovielibrary-iso' ),
	'BT' => __( 'Bhutan', 'wpmovielibrary-iso' ),
	'BO' => __( 'Bolivia, Plurinational State of', 'wpmovielibrary-iso' ),
	'BQ' => __( 'Bonaire, Sint Eustatius and Saba', 'wpmovielibrary-iso' ),
	'BA' => __( 'Bosnia and Herzegovina', 'wpmovielibrary-iso' ),
	'BW' => __( 'Botswana', 'wpmovielibrary-iso' ),
	'BV' => __( 'Bouvet Island', 'wpmovielibrary-iso' ),
	'BR' => __( 'Brazil', 'wpmovielibrary-iso' ),
	'IO' => __( 'British Indian Ocean Territory', 'wpmovielibrary-iso' ),
	'BN' => __( 'Brunei Darussalam', 'wpmovielibrary-iso' ),
	'BG' => __( 'Bulgaria', 'wpmovielibrary-iso' ),
	'BF' => __( 'Burkina Faso', 'wpmovielibrary-iso' ),
	'BI' => __( 'Burundi', 'wpmovielibrary-iso' ),
	'KH' => __( 'Cambodia', 'wpmovielibrary-iso' ),
	'CM' => __( 'Cameroon', 'wpmovielibrary-iso' ),
	'CA' => __( 'Canada', 'wpmovielibrary-iso' ),
	'CV' => __( 'Cape Verde', 'wpmovielibrary-iso' ),
	'KY' => __( 'Cayman Islands', 'wpmovielibrary-iso' ),
	'CF' => __( 'Central African Republic', 'wpmovielibrary-iso' ),
	'TD' => __( 'Chad', 'wpmovielibrary-iso' ),
	'CL' => __( 'Chile', 'wpmovielibrary-iso' ),
	'CN' => __( 'China', 'wpmovielibrary-iso' ),
	'CX' => __( 'Christmas Island', 'wpmovielibrary-iso' ),
	'CC' => __( 'Cocos Islands', 'wpmovielibrary-iso' ),
	'CO' => __( 'Colombia', 'wpmovielibrary-iso' ),
	'KM' => __( 'Comoros', 'wpmovielibrary-iso' ),
	'CG' => __( 'Congo', 'wpmovielibrary-iso' ),
	'CD' => __( 'Democratic Republic of Congo', 'wpmovielibrary-iso' ),
	'CK' => __( 'Cook Islands', 'wpmovielibrary-iso' ),
	'CR' => __( 'Costa Rica', 'wpmovielibrary-iso' ),
	'CI' => __( 'Côte d’Ivoire', 'wpmovielibrary-iso' ),
	'HR' => __( 'Croatia', 'wpmovielibrary-iso' ),
	'CU' => __( 'Cuba', 'wpmovielibrary-iso' ),
	'CW' => __( 'Curaçao', 'wpmovielibrary-iso' ),
	'CY' => __( 'Cyprus', 'wpmovielibrary-iso' ),
	'CZ' => __( 'Czech Republic', 'wpmovielibrary-iso' ),
	'DK' => __( 'Denmark', 'wpmovielibrary-iso' ),
	'DJ' => __( 'Djibouti', 'wpmovielibrary-iso' ),
	'DM' => __( 'Dominica', 'wpmovielibrary-iso' ),
	'DO' => __( 'Dominican Republic', 'wpmovielibrary-iso' ),
	'EC' => __( 'Ecuador', 'wpmovielibrary-iso' ),
	'EG' => __( 'Egypt', 'wpmovielibrary-iso' ),
	'SV' => __( 'El Salvador', 'wpmovielibrary-iso' ),
	'GQ' => __( 'Equatorial Guinea', 'wpmovielibrary-iso' ),
	'ER' => __( 'Eritrea', 'wpmovielibrary-iso' ),
	'EE' => __( 'Estonia', 'wpmovielibrary-iso' ),
	'ET' => __( 'Ethiopia', 'wpmovielibrary-iso' ),
	'FK' => __( 'Falkland Islands', 'wpmovielibrary-iso' ),
	'FO' => __( 'Faroe Islands', 'wpmovielibrary-iso' ),
	'FJ' => __( 'Fiji', 'wpmovielibrary-iso' ),
	'FI' => __( 'Finland', 'wpmovielibrary-iso' ),
	'FR' => __( 'France', 'wpmovielibrary-iso' ),
	'GF' => __( 'French Guiana', 'wpmovielibrary-iso' ),
	'PF' => __( 'French Polynesia', 'wpmovielibrary-iso' ),
	'TF' => __( 'French Southern Territories', 'wpmovielibrary-iso' ),
	'GA' => __( 'Gabon', 'wpmovielibrary-iso' ),
	'GM' => __( 'Gambia', 'wpmovielibrary-iso' ),
	'GE' => __( 'Georgia', 'wpmovielibrary-iso' ),
	'DE' => __( 'Germany', 'wpmovielibrary-iso' ),
	'GH' => __( 'Ghana', 'wpmovielibrary-iso' ),
	'GI' => __( 'Gibraltar', 'wpmovielibrary-iso' ),
	'GR' => __( 'Greece', 'wpmovielibrary-iso' ),
	'GL' => __( 'Greenland', 'wpmovielibrary-iso' ),
	'GD' => __( 'Grenada', 'wpmovielibrary-iso' ),
	'GP' => __( 'Guadeloupe', 'wpmovielibrary-iso' ),
	'GU' => __( 'Guam', 'wpmovielibrary-iso' ),
	'GT' => __( 'Guatemala', 'wpmovielibrary-iso' ),
	'GG' => __( 'Guernsey', 'wpmovielibrary-iso' ),
	'GN' => __( 'Guinea', 'wpmovielibrary-iso' ),
	'GW' => __( 'Guinea-Bissau', 'wpmovielibrary-iso' ),
	'GY' => __( 'Guyana', 'wpmovielibrary-iso' ),
	'HT' => __( 'Haiti', 'wpmovielibrary-iso' ),
	'HM' => __( 'Heard Island and McDonald Islands', 'wpmovielibrary-iso' ),
	'VA' => __( 'Vatican', 'wpmovielibrary-iso' ),
	'HN' => __( 'Honduras', 'wpmovielibrary-iso' ),
	'HK' => __( 'Hong Kong', 'wpmovielibrary-iso' ),
	'HU' => __( 'Hungary', 'wpmovielibrary-iso' ),
	'IS' => __( 'Iceland', 'wpmovielibrary-iso' ),
	'IN' => __( 'India', 'wpmovielibrary-iso' ),
	'ID' => __( 'Indonesia', 'wpmovielibrary-iso' ),
	'IR' => __( 'Iran', 'wpmovielibrary-iso' ),
	'IQ' => __( 'Iraq', 'wpmovielibrary-iso' ),
	'IE' => __( 'Ireland', 'wpmovielibrary-iso' ),
	'IM' => __( 'Isle of Man', 'wpmovielibrary-iso' ),
	'IL' => __( 'Israel', 'wpmovielibrary-iso' ),
	'IT' => __( 'Italy', 'wpmovielibrary-iso' ),
	'JM' => __( 'Jamaica', 'wpmovielibrary-iso' ),
	'JP' => __( 'Japan', 'wpmovielibrary-iso' ),
	'JE' => __( 'Jersey', 'wpmovielibrary-iso' ),
	'JO' => __( 'Jordan', 'wpmovielibrary-iso' ),
	'KZ' => __( 'Kazakhstan', 'wpmovielibrary-iso' ),
	'KE' => __( 'Kenya', 'wpmovielibrary-iso' ),
	'KI' => __( 'Kiribati', 'wpmovielibrary-iso' ),
	'KP' => __( 'North Korea', 'wpmovielibrary-iso' ),
	'KR' => __( 'South Korea', 'wpmovielibrary-iso' ),
	'KW' => __( 'Kuwait', 'wpmovielibrary-iso' ),
	'KG' => __( 'Kyrgyzstan', 'wpmovielibrary-iso' ),
	'LA' => __( 'Lao', 'wpmovielibrary-iso' ),
	'LV' => __( 'Latvia', 'wpmovielibrary-iso' ),
	'LB' => __( 'Lebanon', 'wpmovielibrary-iso' ),
	'LS' => __( 'Lesotho', 'wpmovielibrary-iso' ),
	'LR' => __( 'Liberia', 'wpmovielibrary-iso' ),
	'LY' => __( 'Libya', 'wpmovielibrary-iso' ),
	'LI' => __( 'Liechtenstein', 'wpmovielibrary-iso' ),
	'LT' => __( 'Lithuania', 'wpmovielibrary-iso' ),
	'LU' => __( 'Luxembourg', 'wpmovielibrary-iso' ),
	'MO' => __( 'Macao', 'wpmovielibrary-iso' ),
	'MK' => __( 'Macedonia', 'wpmovielibrary-iso' ),
	'MG' => __( 'Madagascar', 'wpmovielibrary-iso' ),
	'MW' => __( 'Malawi', 'wpmovielibrary-iso' ),
	'MY' => __( 'Malaysia', 'wpmovielibrary-iso' ),
	'MV' => __( 'Maldives', 'wpmovielibrary-iso' ),
	'ML' => __( 'Mali', 'wpmovielibrary-iso' ),
	'MT' => __( 'Malta', 'wpmovielibrary-iso' ),
	'MH' => __( 'Marshall Islands', 'wpmovielibrary-iso' ),
	'MQ' => __( 'Martinique', 'wpmovielibrary-iso' ),
	'MR' => __( 'Mauritania', 'wpmovielibrary-iso' ),
	'MU' => __( 'Mauritius', 'wpmovielibrary-iso' ),
	'YT' => __( 'Mayotte', 'wpmovielibrary-iso' ),
	'MX' => __( 'Mexico', 'wpmovielibrary-iso' ),
	'FM' => __( 'Micronesia', 'wpmovielibrary-iso' ),
	'MD' => __( 'Moldova', 'wpmovielibrary-iso' ),
	'MC' => __( 'Monaco', 'wpmovielibrary-iso' ),
	'MN' => __( 'Mongolia', 'wpmovielibrary-iso' ),
	'ME' => __( 'Montenegro', 'wpmovielibrary-iso' ),
	'MS' => __( 'Montserrat', 'wpmovielibrary-iso' ),
	'MA' => __( 'Morocco', 'wpmovielibrary-iso' ),
	'MZ' => __( 'Mozambique', 'wpmovielibrary-iso' ),
	'MM' => __( 'Myanmar', 'wpmovielibrary-iso' ),
	'NA' => __( 'Namibia', 'wpmovielibrary-iso' ),
	'NR' => __( 'Nauru', 'wpmovielibrary-iso' ),
	'NP' => __( 'Nepal', 'wpmovielibrary-iso' ),
	'NL' => __( 'Netherlands', 'wpmovielibrary-iso' ),
	'NC' => __( 'New Caledonia', 'wpmovielibrary-iso' ),
	'NZ' => __( 'New Zealand', 'wpmovielibrary-iso' ),
	'NI' => __( 'Nicaragua', 'wpmovielibrary-iso' ),
	'NE' => __( 'Niger', 'wpmovielibrary-iso' ),
	'NG' => __( 'Nigeria', 'wpmovielibrary-iso' ),
	'NU' => __( 'Niue', 'wpmovielibrary-iso' ),
	'NF' => __( 'Norfolk Island', 'wpmovielibrary-iso' ),
	'MP' => __( 'Northern Mariana Islands', 'wpmovielibrary-iso' ),
	'NO' => __( 'Norway', 'wpmovielibrary-iso' ),
	'OM' => __( 'Oman', 'wpmovielibrary-iso' ),
	'PK' => __( 'Pakistan', 'wpmovielibrary-iso' ),
	'PW' => __( 'Palau', 'wpmovielibrary-iso' ),
	'PS' => __( 'Palestine', 'wpmovielibrary-iso' ),
	'PA' => __( 'Panama', 'wpmovielibrary-iso' ),
	'PG' => __( 'Papua New Guinea', 'wpmovielibrary-iso' ),
	'PY' => __( 'Paraguay', 'wpmovielibrary-iso' ),
	'PE' => __( 'Peru', 'wpmovielibrary-iso' ),
	'PH' => __( 'Philippines', 'wpmovielibrary-iso' ),
	'PN' => __( 'Pitcairn', 'wpmovielibrary-iso' ),
	'PL' => __( 'Poland', 'wpmovielibrary-iso' ),
	'PT' => __( 'Portugal', 'wpmovielibrary-iso' ),
	'PR' => __( 'Puerto Rico', 'wpmovielibrary-iso' ),
	'QA' => __( 'Qatar', 'wpmovielibrary-iso' ),
	'RE' => __( 'Réunion', 'wpmovielibrary-iso' ),
	'RO' => __( 'Romania', 'wpmovielibrary-iso' ),
	'RU' => __( 'Russia', 'wpmovielibrary-iso' ),
	'RW' => __( 'Rwanda', 'wpmovielibrary-iso' ),
	'BL' => __( 'Saint Barthélemy', 'wpmovielibrary-iso' ),
	'SH' => __( 'Saint Helena, Ascension and Tristan da Cunha', 'wpmovielibrary-iso' ),
	'KN' => __( 'Saint Kitts and Nevis', 'wpmovielibrary-iso' ),
	'LC' => __( 'Saint Lucia', 'wpmovielibrary-iso' ),
	'MF' => __( 'Saint Martin', 'wpmovielibrary-iso' ),
	'PM' => __( 'Saint Pierre and Miquelon', 'wpmovielibrary-iso' ),
	'VC' => __( 'Saint Vincent and the Grenadines', 'wpmovielibrary-iso' ),
	'WS' => __( 'Samoa', 'wpmovielibrary-iso' ),
	'SM' => __( 'San Marino', 'wpmovielibrary-iso' ),
	'ST' => __( 'Sao Tome and Principe', 'wpmovielibrary-iso' ),
	'SA' => __( 'Saudi Arabia', 'wpmovielibrary-iso' ),
	'SN' => __( 'Senegal', 'wpmovielibrary-iso' ),
	'RS' => __( 'Serbia', 'wpmovielibrary-iso' ),
	'SC' => __( 'Seychelles', 'wpmovielibrary-iso' ),
	'SL' => __( 'Sierra Leone', 'wpmovielibrary-iso' ),
	'SG' => __( 'Singapore', 'wpmovielibrary-iso' ),
	'SX' => __( 'Sint Maarten', 'wpmovielibrary-iso' ),
	'SK' => __( 'Slovakia', 'wpmovielibrary-iso' ),
	'SI' => __( 'Slovenia', 'wpmovielibrary-iso' ),
	'SB' => __( 'Solomon Islands', 'wpmovielibrary-iso' ),
	'SO' => __( 'Somalia', 'wpmovielibrary-iso' ),
	'ZA' => __( 'South Africa', 'wpmovielibrary-iso' ),
	'GS' => __( 'South Georgia and the South Sandwich Islands', 'wpmovielibrary-iso' ),
	'SS' => __( 'South Sudan', 'wpmovielibrary-iso' ),
	'ES' => __( 'Spain', 'wpmovielibrary-iso' ),
	'LK' => __( 'Sri Lanka', 'wpmovielibrary-iso' ),
	'SD' => __( 'Sudan', 'wpmovielibrary-iso' ),
	'SR' => __( 'Suriname', 'wpmovielibrary-iso' ),
	'SJ' => __( 'Svalbard and Jan Mayen', 'wpmovielibrary-iso' ),
	'SZ' => __( 'Swaziland', 'wpmovielibrary-iso' ),
	'SE' => __( 'Sweden', 'wpmovielibrary-iso' ),
	'CH' => __( 'Switzerland', 'wpmovielibrary-iso' ),
	'SY' => __( 'Syria', 'wpmovielibrary-iso' ),
	'TW' => __( 'Taiwan', 'wpmovielibrary-iso' ),
	'TJ' => __( 'Tajikistan', 'wpmovielibrary-iso' ),
	'TZ' => __( 'Tanzania', 'wpmovielibrary-iso' ),
	'TH' => __( 'Thailand', 'wpmovielibrary-iso' ),
	'TL' => __( 'Timor-Leste', 'wpmovielibrary-iso' ),
	'TG' => __( 'Togo', 'wpmovielibrary-iso' ),
	'TK' => __( 'Tokelau', 'wpmovielibrary-iso' ),
	'TO' => __( 'Tonga', 'wpmovielibrary-iso' ),
	'TT' => __( 'Trinidad and Tobago', 'wpmovielibrary-iso' ),
	'TN' => __( 'Tunisia', 'wpmovielibrary-iso' ),
	'TR' => __( 'Turkey', 'wpmovielibrary-iso' ),
	'TM' => __( 'Turkmenistan', 'wpmovielibrary-iso' ),
	'TC' => __( 'Turks and Caicos Islands', 'wpmovielibrary-iso' ),
	'TV' => __( 'Tuvalu', 'wpmovielibrary-iso' ),
	'UG' => __( 'Uganda', 'wpmovielibrary-iso' ),
	'UA' => __( 'Ukraine', 'wpmovielibrary-iso' ),
	'AE' => __( 'United Arab Emirates', 'wpmovielibrary-iso' ),
	'GB' => __( 'United Kingdom', 'wpmovielibrary-iso' ),
	'US' => __( 'United States', 'wpmovielibrary-iso' ),
	'UM' => __( 'United States Minor Outlying Islands', 'wpmovielibrary-iso' ),
	'UY' => __( 'Uruguay', 'wpmovielibrary-iso' ),
	'UZ' => __( 'Uzbekistan', 'wpmovielibrary-iso' ),
	'VU' => __( 'Vanuatu', 'wpmovielibrary-iso' ),
	'VE' => __( 'Venezuela', 'wpmovielibrary-iso' ),
	'VN' => __( 'Viet Nam', 'wpmovielibrary-iso' ),
	'VG' => __( 'British Virgin Islands', 'wpmovielibrary-iso' ),
	'VI' => __( 'U.S Virgin Islands.', 'wpmovielibrary-iso' ),
	'WF' => __( 'Wallis and Futuna', 'wpmovielibrary-iso' ),
	'EH' => __( 'Western Sahara', 'wpmovielibrary-iso' ),
	'YE' => __( 'Yemen', 'wpmovielibrary-iso' ),
	'ZM' => __( 'Zambia', 'wpmovielibrary-iso' ),
	'ZW' => __( 'Zimbabwe', 'wpmovielibrary-iso' )
);