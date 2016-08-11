<?php
/**
 * Define the localization functionality.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

use wpmoly\Node;

/**
 * Loads and defines the localization files for this plugin.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class l10n {

	/**
	 * Singleton.
	 *
	 * @var    l10n
	 */
	private static $instance = null;

	/**
	 * Supported languages
	 * 
	 * @var    array
	 */
	public static $supported_languages;

	/**
	 * Standard languages
	 * 
	 * @var    array
	 */
	public static $standard_languages;

	/**
	 * Native languages
	 * 
	 * @var    array
	 */
	public static $native_languages;

	/**
	 * Supported countries
	 * 
	 * @var    array
	 */
	public static $supported_countries;

	/**
	 * Standard countries
	 * 
	 * @var    array
	 */
	public static $standard_countries;

	/**
	 * Initialize the class.
	 * 
	 * @since    3.0
	 */
	public function __construct() {

		$this->set_countries();
		$this->set_languages();
	}

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Options
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Set default countries for localization.
	 * 
	 * @since    3.0
	 */
	public function set_countries() {

		$supported_countries = array(
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

		$standard_countries = array(
			'AF' => 'Afghanistan',
			'AX' => 'Åland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BQ' => 'Bonaire, Sint Eustatius and Saba',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo',
			'CD' => 'Democratic Republic of Congo',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'Côte d’Ivoire',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CW' => 'Curaçao',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island and McDonald Islands',
			'VA' => 'Vatican',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KP' => 'North Korea',
			'KR' => 'South Korea',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Lao',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestine',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Réunion',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthélemy',
			'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SX' => 'Sint Maarten',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'SS' => 'South Sudan',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syria',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States of America',
			'UM' => 'United States Minor Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela',
			'VN' => 'Viet Nam',
			'VG' => 'British Virgin Islands',
			'VI' => 'U.S Virgin Islands',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe'
		);

		/**
		 * Filter the default supported countries list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported
		 */
		self::$supported_countries = apply_filters( 'wpmoly/filter/l10n/countries/supported', $supported_countries );

		/**
		 * Filter the default standard countries list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported
		 */
		self::$standard_countries = apply_filters( 'wpmoly/filter/l10n/countries/standard', $standard_countries );
	}

	/**
	 * Set default languages for localization.
	 * 
	 * @since    3.0
	 */
	public function set_languages() {

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

		$standard_languages = array(
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
		);

		$native_languages = array(
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
			'zu' => 'isiZulu'
		);

		/**
		 * Filter the default supported languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported_languages
		 */
		self::$supported_languages = apply_filters( 'wpmoly/filter/l10n/languages/supported', $supported_languages );

		/**
		 * Filter the default standard languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported_languages
		 */
		self::$standard_languages = apply_filters( 'wpmoly/filter/l10n/languages/standard', $standard_languages );

		/**
		 * Filter the default native languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported_languages
		 */
		self::$native_languages = apply_filters( 'wpmoly/filter/l10n/languages/native', $native_languages );
	}

	/**
	 * Localize JS scripts.
	 *
	 * @since    3.0
	 */
	public function localize_scripts() {

		$localized = array(
			'_locale'            => get_locale(),
			'_language'          => wpmoly_o( 'api-language' ),
			'_country'           => wpmoly_o( 'api-country' ),
			'_country_alt'       => wpmoly_o( 'api-country-alt' ),

			'actors'             => __( 'actors', 'wpmovielibrary' ),
			'autocomplete'       => __( 'add automatically', 'wpmovielibrary' ),
			'availableBackdrops' => __( 'Available Backdrops', 'wpmovielibrary' ),
			'availablePosters'   => __( 'Available Posters', 'wpmovielibrary' ),
			'backdrop'           => array(
				__( 'Backdrop', 'wpmovielibrary' ),
				__( 'Backdrops', 'wpmovielibrary' ),
			),
			'backdropAlt'        => wpmoly_o( 'backdrops-title', sprintf( '%s "{title}"', __( 'Image from the movie', 'wpmovielibrary' ) ) ),
			'backdropCaption'    => wpmoly_o( 'backdrops-description', sprintf( '© {release_date} {production_companies} − %s', __( 'All right reserved.', 'wpmovielibrary' ) ) ),
			'collections'        => __( 'collections', 'wpmovielibrary' ),
			'featuredImageSet'   => __( 'Featured image set!', 'wpmovielibrary' ),
			'genres'             => __( 'genres', 'wpmovielibrary' ),
			'importImages'       => __( 'Import selected images', 'wpmovielibrary' ),
			'importingMovie'     => __( 'Importing movie %s…', 'wpmovielibrary' ),
			'importingPoster'    => __( 'Importing poster…', 'wpmovielibrary' ),
			'nMoviesFound'       => array(
				__( 'No movies found!', 'wpmovielibrary' ),
				__( 'One movie found!', 'wpmovielibrary' ),
				__( '%d movies found!', 'wpmovielibrary' )
			),
			'metaSaved'          => __( 'Metadata saved!', 'wpmovielibrary' ),
			'modalTabTitle'      => __( 'Backdrops and Posters', 'wpmovielibrary' ),
			'movieImported'      => __( 'Movie %s imported successfully!', 'wpmovielibrary' ),
			'termsAutocomplete'  => __( 'or %s using the %s list.', 'wpmovielibrary' ),
			'poster'             => array(
				__( 'Poster', 'wpmovielibrary' ),
				__( 'Posters', 'wpmovielibrary' ),
			),
			'posterAlt'          => wpmoly_o( 'posters-title', sprintf( '%s "{title}"', __( 'Poster from the movie', 'wpmovielibrary' ) ) ),
			'posterCaption'      => wpmoly_o( 'posters-description', sprintf( '© {release_date} {production_companies} − %s', __( 'All right reserved.', 'wpmovielibrary' ) ) ),
			'posterImported'     => __( 'Poster successfully imported!', 'wpmovielibrary' ),
			'ready'              => __( 'Ready!', 'wpmovielibrary' ),
			'reload'             => __( 'Reload', 'wpmovielibrary' ),
			'replaceSettings'    => __( 'You’re about to save the current settings. This will erase the existing settings and apply to every search, including multiple imports.<br /><br />Are you sure you want to do that?', 'wpmovielibrary' ),
			'run'                => __( 'Run Forrest, run!', 'wpmovielibrary' ),
			'savingSettings'     => __( 'Saving settings…', 'wpmovielibrary' ),
			'searchingMovie'     => __( 'Searching movie %s…', 'wpmovielibrary' ),
			'selectedImages'     => array(
				__( 'one selected image', 'wpmovielibrary' ),
				__( '%d selected images', 'wpmovielibrary' )
			),
			'setImagesAs'        => __( 'You’re about to set %s as %s for the movie %s.<br /><br />Are you sure you want to do that?', 'wpmovielibrary' ),
			'settingFeatured'    => __( 'Setting featured image…', 'wpmovielibrary' ),
			'settingsError'      => __( 'Error: settings were not saved.', 'wpmovielibrary' ),
			'settingsSaved'      => __( 'Settings saved!', 'wpmovielibrary' ),
		);

		wp_localize_script( 'wpmoly', 'wpmolyL10n', $localized );

		$poster   = Node\DefaultPoster::get_instance();
		$backdrop = Node\DefaultBackdrop::get_instance();
		$localized = array(
			'poster'   => $poster->get_sizes(),
			'backdrop' => $backdrop->get_sizes()
		);

		wp_localize_script( 'wpmoly', 'wpmolyDefaultImages', $localized );
	}

}
