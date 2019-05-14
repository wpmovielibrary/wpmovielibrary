<?php
/**
 * Define the localization functionality.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\core;

use wpmoly\nodes;
use wpmoly\utils;

/**
 * Loads and defines the localization files for this plugin.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class L10n {

	/**
	 * The single instance of the class.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var L10n
	 */
	private static $_instance = null;

	/**
	 * Supported languages
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @var array
	 */
	public static $supported_languages;

	/**
	 * Standard languages
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @var array
	 */
	public static $standard_languages;

	/**
	 * Native languages
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @var array
	 */
	public static $native_languages;

	/**
	 * Supported countries
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @var array
	 */
	public static $supported_countries;

	/**
	 * Standard countries
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @var array
	 */
	public static $standard_countries;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return L10n
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new static;
			self::$_instance->init();
		}

		return self::$_instance;
	}

	/**
	 * Initialize.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function init() {

		$this->set_countries();
		$this->set_languages();
	}

	/**
	 * Set default countries for localization.
	 *
	 * @since 3.0.0
	 *
	 * @access public
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
			'VN' => __( 'Viet Nam', 'wpmovielibrary-iso' ),
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
			'ZW' => 'Zimbabwe',
		);

		/**
		 * Filter the default supported countries list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $supported
		 */
		self::$supported_countries = apply_filters( 'wpmoly/filter/l10n/countries/supported', $supported_countries );

		/**
		 * Filter the default standard countries list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $supported
		 */
		self::$standard_countries = apply_filters( 'wpmoly/filter/l10n/countries/standard', $standard_countries );
	}

	/**
	 * Set default languages for localization.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function set_languages() {

		$supported_languages = array(
			'ar' => __( 'Arabic', 'wpmovielibrary-iso' ),
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
			'zh' => __( 'Mandarin', 'wpmovielibrary-iso' ),
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
			'ro' => 'Romanian',
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
			'zu' => 'Zulu',
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
			'zh' => '普通话',
			'xh' => 'isiXhosa',
			'zu' => 'isiZulu',
		);

		/**
		 * Filter the default supported languages list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $supported_languages
		 */
		self::$supported_languages = apply_filters( 'wpmoly/filter/l10n/languages/supported', $supported_languages );

		/**
		 * Filter the default standard languages list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $supported_languages
		 */
		self::$standard_languages = apply_filters( 'wpmoly/filter/l10n/languages/standard', $standard_languages );

		/**
		 * Filter the default native languages list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $supported_languages
		 */
		self::$native_languages = apply_filters( 'wpmoly/filter/l10n/languages/native', $native_languages );
	}

	/**
	 * Localize the plugin's custom post types permalinks.
	 *
	 * @since 3.0.0
	 *
	 * @param array $post_types  Post Types list.
	 *
	 * @return array
	 */
	public function localize_post_types( $post_types = array() ) {

		global $pagenow;

		// Don't do this while flushing rewrite rules.
		if ( 'options-permalink.php' === $pagenow ) {
			return $post_types;
		}

		if ( ! empty( $post_types['movie'] ) && utils\movie\has_archives_page() ) {
			$post_types['movie']['has_archive'] = utils\movie\get_archive_link( 'relative' );
		}

		if ( ! empty( $post_types['person'] ) && utils\person\has_archives_page() ) {
			$post_types['person']['has_archive'] = utils\person\get_archive_link( 'relative' );
		}

		return $post_types;
	}

	/**
	 * Localize the plugin's custom taxonomies permalinks.
	 *
	 * @since 3.0.0
	 *
	 * @param array $taxonomies  Taxonomies list.
	 *
	 * @return array
	 */
	public function localize_taxonomies( $taxonomies = array() ) {

		foreach ( $taxonomies as $slug => $taxonomy ) {
			if ( utils\has_archives_page( $slug ) ) {
				$taxonomies[ $slug ]['rewrite']['slug'] = utils\get_taxonomy_archive_link( $slug, 'relative' );
			}
		}

		return $taxonomies;
	}

	/**
	 * Localize JS scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function localize_scripts() {

		$this->localize_rest_scripts();

		if ( is_admin() ) {
			$this->localize_admin_scripts();
		} else {
			$this->localize_public_scripts();
		}
	}

	/**
	 * Localize REST JS scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function localize_rest_scripts() {

		$localized = array(
			'root'          => esc_url_raw( get_rest_url() ),
			'nonce'         => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ),
			'versionString' => 'wp/v2/',
		);

		wp_localize_script( 'wp-api', 'wpApiSettings', $localized );

		$localized = array(
			'root'              => esc_url_raw( get_rest_url() ),
			'nonce'             => wp_create_nonce( 'wpmoly_rest' ),
			'verbose'           => ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ),
			'versionString'     => 'wpmoly/v1/',
			'actor_prefix'      => utils\actor\prefix( '' ),
			'collection_prefix' => utils\collection\prefix( '' ),
			'genre_prefix'      => utils\genre\prefix( '' ),
			'grid_prefix'       => utils\grid\prefix( '' ),
			'movie_prefix'      => utils\movie\prefix( '' ),
			'option_prefix'     => utils\settings\prefix( '' ),
			'page_prefix'       => utils\page\prefix( '' ),
			'person_prefix'     => utils\person\prefix( '' ),
		);

		wp_localize_script( 'wpmoly-api', 'wpmolyApiSettings', $localized );

		$localized = array(
			'settings' => array(
				'loading_error' => esc_html__( 'Error while loading settings.', 'wpmovielibrary' ),
				'schema_error'  => esc_html__( 'Error while loading settings schema.', 'wpmovielibrary' ),
				'saving_error'  => esc_html__( 'Error while saving settings.', 'wpmovielibrary' ),
				'saving'        => esc_html__( 'Saving settings...', 'wpmovielibrary' ),
				'saved'         => esc_html__( 'Settings saved!', 'wpmovielibrary' ),
			),
		);

		wp_localize_script( 'wpmoly-api', 'wpmolyApiL10n', $localized );
	}

	/**
	 * Localize Admin-side JS scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function localize_admin_scripts() {

		wp_localize_script( 'wpmoly-tmdb', 'tmdbApiSettings', array(
			'api_key'             => utils\o( 'api-key', '' ),
			'language'            => utils\o( 'api-language', 'en-US' ),
			'country'             => utils\o( 'api-country', 'US' ),
			'alternative_country' => utils\o( 'api-alternative-country', 'US' ),
			'adult'               => utils\o( 'api-adult', true ),
			'mapping' => array(
				'Movie'       => 'Movie',
				'SearchMovie' => 'Movies',
			),
		) );

		$localized = array(
			'api' => array(
				'missing' => esc_html__( 'Couldn’t find WordPress Rest API Backbone client.', 'wpmovielibrary' ),
				'missing_collection' => esc_html__( 'Couldn’t find WordPress Rest API Backbone client collection object.', 'wpmovielibrary' ),
			),
 			'n_movie_found' => array(
				esc_html__( '%s Movie', 'wpmovielibrary' ),
				esc_html__( '%s Movies', 'wpmovielibrary' ),
			),
 			'd_separator' => _x( '.', 'number decimal separator', 'wpmovielibrary' ),
 			'o_separator' => _x( ',', 'number order separator', 'wpmovielibrary' ),
			'no' => esc_html__( 'No' ),
			'yes' => esc_html__( 'Yes' ),
			'dismiss' => esc_html__( 'Dismiss' ),
			'i_know' => esc_html__( 'I know what I\'m doing.', 'wpmovielibrary' ),
			'page' => esc_html__( 'Page' ),
			'of' => esc_html__( 'of' ),
			'next_page' => esc_html__( 'Next page' ),
			'previous_page' => esc_html__( 'Previous page' ),
		);

		wp_localize_script( 'wpmoly-core', 'wpmolyL10n', $localized );

		$post_id   = get_the_ID();
		$post_type = get_post_type( $post_id );
		if ( in_array( $post_type, array( 'grid', 'movie', 'person' ), true ) ) {
			$localized = array(
				'edit_with_wpmoly' => esc_html__( 'Edit with wpMovieLibrary' ),
				'edit_link' => esc_url_raw( admin_url( 'admin.php?page=wpmovielibrary-' . $post_type . 's&id=' . $post_id . '&action=edit' ) ),
			);
			wp_localize_script( 'wpmoly-gutenberg', 'wpmolyBlockEditorL10n', $localized );
		}

		$localized = array(
			'open_search'  => esc_html__( 'Open search menu', 'wpmovielibrary' ),
			'close_search' => esc_html__( 'Close the search', 'wpmovielibrary' ),
			'n_published'  => array(
				esc_html__( '%s published', 'wpmovielibrary' ),
				esc_html__( '%s published', 'wpmovielibrary' ),
			),
			'n_future'     => array(
				esc_html__( '%s future', 'wpmovielibrary' ),
				esc_html__( '%s future', 'wpmovielibrary' ),
			),
			'n_draft'      => array(
				esc_html__( '%s draft', 'wpmovielibrary' ),
				esc_html__( '%s drafts', 'wpmovielibrary' ),
			),
			'n_pending'    => array(
				esc_html__( '%s pending', 'wpmovielibrary' ),
				esc_html__( '%s pending', 'wpmovielibrary' ),
			),
			'n_private'    => array(
				esc_html__( '%s private', 'wpmovielibrary' ),
				esc_html__( '%s private', 'wpmovielibrary' ),
			),
			'n_trashed'    => array(
				esc_html__( '%s trashed', 'wpmovielibrary' ),
				esc_html__( '%s trashed', 'wpmovielibrary' ),
			),
			'n_autodraft'  => array(
				esc_html__( '%s auto-draft', 'wpmovielibrary' ),
				esc_html__( '%s auto-draft(s)', 'wpmovielibrary' ),
			),
			'empty_trash' => esc_html__( 'Empty Trash' ),
			'start_editing' => esc_html__( 'Start editing?', 'wpmovielibrary' ),
			'trash_drafts' => esc_html__( 'Trash Drafts', 'wpmovielibrary' ),
			'delete_permanently' => esc_html__( 'Delete permanently?', 'wpmovielibrary' ),
			'about_trash_draft' => esc_html__( 'Your are about to move all drafts the trash. You can always restore them later.', 'wpmovielibrary' ),
			'standard_countries' => self::$standard_countries,
			'standard_languages' => self::$standard_languages,
			'native_languages'   => self::$native_languages,
		);

		$localized_grid = $localized + array(
			'n_total_post' => array(
				__( 'You have a total of <strong>%s</strong> grid.', 'wpmovielibrary' ),
				__( 'You have a total of <strong>%s</strong> grids.', 'wpmovielibrary' ),
			),
			'n_draft_post' => array(
				esc_html__( '%s grid draft found.', 'wpmovielibrary' ),
				esc_html__( '%s grid drafts found.', 'wpmovielibrary' ),
			),
			'n_post_in_trash' => array(
				esc_html__( '%s grid found in trash.', 'wpmovielibrary' ),
				esc_html__( '%s grids found in trash.', 'wpmovielibrary' ),
			),
			'trash_post_warning' => esc_html__( 'Be careful: deleted grids can not be restored!', 'wpmovielibrary' ),
			'no_post_in_trash' => esc_html__( 'No grid found in trash.', 'wpmovielibrary' ),
			'about_delete_posts' => esc_html__( 'Your are about to completely delete all grids in the trash. This can not be undone!', 'wpmovielibrary' ),
			'move_post_to_trash' => esc_html__( 'Move grid to trash', 'wpmovielibrary' ),
			'publish_post'     => esc_html__( 'Publish grid', 'wpmovielibrary' ),
			'restore_post'     => esc_html__( 'Restore grid', 'wpmovielibrary' ),
			'delete_post'      => esc_html__( 'Delete grid', 'wpmovielibrary' ),
			'existing_post'    => esc_html__( 'A grid already exists with this title.', 'wpmovielibrary' ),
			'post_udpated'     => esc_html__( 'Grid updated!', 'wpmovielibrary' ),
			'post_trashed'     => esc_html__( 'Grid trashed. Redirecting...', 'wpmovielibrary' ),
			'edit_link'        => esc_url_raw( admin_url( 'admin.php?page=wpmovielibrary-grids' ) ),
			'old_edit_link'    => esc_url_raw( admin_url( 'edit.php?post_type=grid' ) ),
			'back_to_edit'     => esc_html__( 'Back the grid list', 'wpmovielibrary' ),
			'edit_label'       => esc_html__( 'Grid Browser', 'wpmovielibrary' ),
			'old_edit_label'   => esc_html__( 'Classic Browser', 'wpmovielibrary' ),
			'old_editor_label' => esc_html__( 'Classic Editor', 'wpmovielibrary' ),
			'search_posts'     => esc_html__( 'Search grids', 'wpmovielibrary' ),
			'post_title'       => esc_html__( 'Title' ),
			'about_new_post'   => esc_html__( 'Create a new content grid. You can customize it later.', 'wpmovielibrary' ),
			'create_post'      => esc_html__( 'Create Grid', 'wpmovielibrary' ),
			'new_title'        => esc_html__( 'New Title', 'wpmovielibrary' ),
			'update_title'     => esc_html__( 'Update Title', 'wpmovielibrary' ),
			'about_new_title'  => esc_html__( 'Change the current grid title.', 'wpmovielibrary' ),
		);

		wp_localize_script( 'wpmoly-grid-browser', 'wpmolyEditorL10n', $localized_grid );
		wp_localize_script( 'wpmoly-grid-editor',  'wpmolyEditorL10n', $localized_grid );

		$localized_movie = $localized + array(
			'n_total_post' => array(
				__( 'You have a total of <strong>%s</strong> movie.', 'wpmovielibrary' ),
				__( 'You have a total of <strong>%s</strong> movies.', 'wpmovielibrary' ),
			),
			'n_draft_post' => array(
				esc_html__( '%s movie draft found.', 'wpmovielibrary' ),
				esc_html__( '%s movie drafts found.', 'wpmovielibrary' ),
			),
			'n_post_in_trash' => array(
				esc_html__( '%s movie found in trash.', 'wpmovielibrary' ),
				esc_html__( '%s movies found in trash.', 'wpmovielibrary' ),
			),
			'n_days_ago' => array(
				__( '<strong>%s</strong> day ago', 'wpmovielibrary' ),
				__( '<strong>%s</strong> days ago', 'wpmovielibrary' ),
			),
			'moments_ago'        => esc_html__( 'Moments ago', 'wpmovielibrary' ),
			'trash_post_warning' => esc_html__( 'Be careful: deleted movies can not be restored!', 'wpmovielibrary' ),
			'no_post_in_trash'   => esc_html__( 'No movie found in trash.', 'wpmovielibrary' ),
			'about_delete_posts' => esc_html__( 'Your are about to completely delete all movies in the trash. This can not be undone!', 'wpmovielibrary' ),
			'move_post_to_trash' => esc_html__( 'Move movie to trash', 'wpmovielibrary' ),
			'publish_post'     => esc_html__( 'Publish movie', 'wpmovielibrary' ),
			'delete_post'      => esc_html__( 'Delete movie', 'wpmovielibrary' ),
			'existing_post'    => esc_html__( 'A movie already exists with this title.', 'wpmovielibrary' ),
			'post_udpated'     => esc_html__( 'Movie updated!', 'wpmovielibrary' ),
			'post_trashed'     => esc_html__( 'Movie trashed. Redirecting...', 'wpmovielibrary' ),
			'edit_link'        => esc_url_raw( admin_url( 'admin.php?page=wpmovielibrary-movies' ) ),
			'old_edit_link'    => esc_url_raw( admin_url( 'edit.php?post_type=movie' ) ),
			'back_to_edit'     => esc_html__( 'Back the movie list', 'wpmovielibrary' ),
			'edit_label'       => esc_html__( 'Movie Browser', 'wpmovielibrary' ),
			'old_edit_label'   => esc_html__( 'Classic Browser', 'wpmovielibrary' ),
			'preview_label'    => esc_html__( 'Preview', 'wpmovielibrary' ),
			'download_label'   => esc_html__( 'Import Metadata', 'wpmovielibrary' ),
			'snapshot_label'   => esc_html__( 'Refresh Snapshot', 'wpmovielibrary' ),
			'old_editor_label' => esc_html__( 'Classic Editor', 'wpmovielibrary' ),
			'search_posts'     => esc_html__( 'Search movies', 'wpmovielibrary' ),
			'post_title'       => esc_html__( 'Title' ),
			'about_new_post'   => esc_html__( 'Create a new movie. You can customize it later.', 'wpmovielibrary' ),
			'create_post'      => esc_html__( 'Create Movie', 'wpmovielibrary' ),
			'post_created'     => esc_html__( 'New movie added!', 'wpmovielibrary' ),
			'new_title'        => esc_html__( 'New Title', 'wpmovielibrary' ),
			'update_title'     => esc_html__( 'Update Title', 'wpmovielibrary' ),
			'about_new_title'  => esc_html__( 'Change the current movie title. This only affects the post\'s title.', 'wpmovielibrary' ),
			'no_actor_found'   => esc_html__( 'No actor found. Maybe add some?', 'wpmovielibrary' ),
			'no_category_found'   => esc_html__( 'No category found. Maybe add some?', 'wpmovielibrary' ),
			'no_collection_found' => esc_html__( 'No collection found. Maybe add some?', 'wpmovielibrary' ),
			'no_company_found'    => esc_html__( 'No company found. Maybe add some?', 'wpmovielibrary' ),
			'no_country_found'    => esc_html__( 'No country found. Maybe add some?', 'wpmovielibrary' ),
			'no_genre_found'      => esc_html__( 'No genre found. Maybe add some?', 'wpmovielibrary' ),
			'no_language_found'   => esc_html__( 'No language found. Maybe add some?', 'wpmovielibrary' ),
			'no_tag_found'        => esc_html__( 'No tag found. Maybe add some?', 'wpmovielibrary' ),
			'snapshot_updated'    => esc_html__( 'Snapshot updated!', 'wpmovielibrary' ),
			'synchronize_actors'      => esc_html__( 'Synchronize with cast', 'wpmovielibrary' ),
			'synchronize_collections' => esc_html__( 'Synchronize with directors', 'wpmovielibrary' ),
			'synchronize_companies'   => esc_html__( 'Synchronize with meta', 'wpmovielibrary' ),
			'synchronize_countries'   => esc_html__( 'Synchronize with meta', 'wpmovielibrary' ),
			'synchronize_genres'      => esc_html__( 'Synchronize with meta', 'wpmovielibrary' ),
			'synchronize_languages'   => esc_html__( 'Synchronize with meta', 'wpmovielibrary' ),
			'premiere_release'           => esc_html__( 'Premiere release', 'wpmovielibrary' ),
	    'theatrical_limited_release' => esc_html__( 'Theatrical (limited) release', 'wpmovielibrary' ),
	    'theatrical_release'         => esc_html__( 'Theatrical release', 'wpmovielibrary' ),
	    'digital_release'            => esc_html__( 'Digital release', 'wpmovielibrary' ),
	    'physical_release'           => esc_html__( 'Physical release', 'wpmovielibrary' ),
	    'tv_release'                 => esc_html__( 'TV release', 'wpmovielibrary' ),
			'select_release'             => esc_html__( 'Use this release for local release date and certification.', 'wpmovielibrary' ),
			'custom_posters'             => esc_html__( 'Custom posters', 'wpmovielibrary' ),
			'custom_backdrops'           => esc_html__( 'Custom backdrops', 'wpmovielibrary' ),
			'use_as_custom_posters'      => esc_html__( 'Use as custom poster(s)', 'wpmovielibrary' ),
			'use_as_custom_backdrops'    => esc_html__( 'Use as custom backdrop(s)', 'wpmovielibrary' ),
			'setting_as_poster'          => esc_html__( 'Setting selected image as poster...', 'wpmovielibrary' ),
			'setting_as_backdrop'        => esc_html__( 'Setting selected image as backdrop...', 'wpmovielibrary' ),
			'poster_updated'             => esc_html__( 'Poster updated!', 'wpmovielibrary' ),
			'backdrop_updated'           => esc_html__( 'Backdrop updated!', 'wpmovielibrary' ),
			'removing_poster'            => esc_html__( 'Removing selected poster...', 'wpmovielibrary' ),
			'removing_backdrop'          => esc_html__( 'Removing selected backdrop...', 'wpmovielibrary' ),
			'poster_removed'             => esc_html__( 'Poster has been removed from the collection. Media remains in the library.', 'wpmovielibrary' ),
			'backdrop_removed'           => esc_html__( 'Backdrop has been removed from the collection. Media remains in the library.', 'wpmovielibrary' ),
			'view_attachment'            => esc_html__( 'View Attachment', 'wpmovielibrary' ),
			'upload_fail'                => esc_html__( 'Upload failed.', 'wpmovielibrary' ),
			'upload_success'             => __( 'Media uploaded successfully. <a href="%s" target="_blank">View Media</a>.', 'wpmovielibrary' ),
			'actor_does_not_exist'       => esc_html__( 'Actor "%s" is not (yet) part of your library.', 'wpmovielibrary' ),
			'collection_does_not_exist'  => esc_html__( 'You don’t have any collection for "%s" yet.', 'wpmovielibrary' ),
			'category_does_not_exist'    => esc_html__( 'You don’t have any category name "%s" yet.', 'wpmovielibrary' ),
			'genre_does_not_exist'       => esc_html__( 'No "%s" genre is part of your library (yet).', 'wpmovielibrary' ),
			'tag_does_not_exist'         => esc_html__( 'You don’t have any "%s" tag yet.', 'wpmovielibrary' ),
			'new_actor'                  => esc_html__( 'New actor.' ),
			'new_category'               => esc_html__( 'New category.' ),
			'new_collection'             => esc_html__( 'New collection.' ),
			'new_genre'                  => esc_html__( 'New genre.' ),
			'new_tag'                    => esc_html__( 'New tag.' ),
		);

		wp_localize_script( 'wpmoly-movie-browser', 'wpmolyEditorL10n', $localized_movie );
		wp_localize_script( 'wpmoly-movie-editor',  'wpmolyEditorL10n', $localized_movie );

		$localized_person = $localized + array(
			'n_total_post' => array(
				__( 'You have a total of <strong>%s</strong> person.', 'wpmovielibrary' ),
				__( 'You have a total of <strong>%s</strong> persons.', 'wpmovielibrary' ),
			),
			'n_draft_post' => array(
				esc_html__( '%s person draft found.', 'wpmovielibrary' ),
				esc_html__( '%s person drafts found.', 'wpmovielibrary' ),
			),
			'n_post_in_trash' => array(
				esc_html__( '%s person found in trash.', 'wpmovielibrary' ),
				esc_html__( '%s persons found in trash.', 'wpmovielibrary' ),
			),
			'n_days_ago' => array(
				__( '<strong>%s</strong> day ago', 'wpmovielibrary' ),
				__( '<strong>%s</strong> days ago', 'wpmovielibrary' ),
			),
			'moments_ago'        => esc_html__( 'Moments ago', 'wpmovielibrary' ),
			'trash_post_warning' => esc_html__( 'Be careful: deleted movies can not be restored!', 'wpmovielibrary' ),
			'no_post_in_trash'   => esc_html__( 'No person found in trash.', 'wpmovielibrary' ),
			'about_delete_posts' => esc_html__( 'Your are about to completely delete all movies in the trash. This can not be undone!', 'wpmovielibrary' ),
			'move_post_to_trash' => esc_html__( 'Move person to trash', 'wpmovielibrary' ),
			'publish_post'     => esc_html__( 'Publish person', 'wpmovielibrary' ),
			'delete_post'      => esc_html__( 'Delete person', 'wpmovielibrary' ),
			'existing_post'    => esc_html__( 'A person already exists with this title.', 'wpmovielibrary' ),
			'post_udpated'     => esc_html__( 'Person updated!', 'wpmovielibrary' ),
			'post_trashed'     => esc_html__( 'Person trashed. Redirecting...', 'wpmovielibrary' ),
			'edit_link'        => esc_url_raw( admin_url( 'admin.php?page=wpmovielibrary-movies' ) ),
			'old_edit_link'    => esc_url_raw( admin_url( 'edit.php?post_type=person' ) ),
			'back_to_edit'     => esc_html__( 'Back the person list', 'wpmovielibrary' ),
			'edit_label'       => esc_html__( 'Person Browser', 'wpmovielibrary' ),
			'old_edit_label'   => esc_html__( 'Classic Browser', 'wpmovielibrary' ),
			'preview_label'    => esc_html__( 'Preview', 'wpmovielibrary' ),
			'download_label'   => esc_html__( 'Import Metadata', 'wpmovielibrary' ),
			'snapshot_label'   => esc_html__( 'Refresh Snapshot', 'wpmovielibrary' ),
			'old_editor_label' => esc_html__( 'Classic Editor', 'wpmovielibrary' ),
			'search_posts'     => esc_html__( 'Search movies', 'wpmovielibrary' ),
			'post_title'       => esc_html__( 'Title' ),
			'about_new_post'   => esc_html__( 'Create a new person. You can customize it later.', 'wpmovielibrary' ),
			'create_post'      => esc_html__( 'Create Person', 'wpmovielibrary' ),
			'post_created'     => esc_html__( 'New person added!', 'wpmovielibrary' ),
			'new_title'        => esc_html__( 'New Title', 'wpmovielibrary' ),
			'update_title'     => esc_html__( 'Update Title', 'wpmovielibrary' ),
			'about_new_title'  => esc_html__( 'Change the current person title. This only affects the post\'s title.', 'wpmovielibrary' ),
			'snapshot_updated'    => esc_html__( 'Snapshot updated!', 'wpmovielibrary' ),

			'custom_pictures'         => esc_html__( 'Custom pictures', 'wpmovielibrary' ),
			'custom_backdrops'        => esc_html__( 'Custom backdrops', 'wpmovielibrary' ),
			'use_as_custom_pictures'  => esc_html__( 'Use as custom picture(s)', 'wpmovielibrary' ),
			'use_as_custom_backdrops' => esc_html__( 'Use as custom backdrop(s)', 'wpmovielibrary' ),
			'setting_as_picture'      => esc_html__( 'Setting selected image as picture...', 'wpmovielibrary' ),
			'setting_as_backdrop'     => esc_html__( 'Setting selected image as backdrop...', 'wpmovielibrary' ),
			'picture_updated'         => esc_html__( 'Picture updated!', 'wpmovielibrary' ),
			'backdrop_updated'        => esc_html__( 'Backdrop updated!', 'wpmovielibrary' ),
			'removing_picture'        => esc_html__( 'Removing selected picture...', 'wpmovielibrary' ),
			'removing_backdrop'       => esc_html__( 'Removing selected backdrop...', 'wpmovielibrary' ),
			'picture_removed'         => esc_html__( 'Picture has been removed from the collection. Media remains in the library.', 'wpmovielibrary' ),
			'backdrop_removed'        => esc_html__( 'Backdrop has been removed from the collection. Media remains in the library.', 'wpmovielibrary' ),
			'view_attachment'         => esc_html__( 'View Attachment', 'wpmovielibrary' ),
			'upload_fail'             => esc_html__( 'Upload failed.', 'wpmovielibrary' ),
			'upload_success'          => __( 'Media uploaded successfully. <a href="%s" target="_blank">View Media</a>.', 'wpmovielibrary' ),
		);

		wp_localize_script( 'wpmoly-person-browser', 'wpmolyEditorL10n', $localized_person );
		wp_localize_script( 'wpmoly-person-editor',  'wpmolyEditorL10n', $localized_person );

		$localized = array(
			'open_search'  => esc_html__( 'Open search menu', 'wpmovielibrary' ),
			'close_search' => esc_html__( 'Close the search', 'wpmovielibrary' ),
		);

		$localized_actor = $localized + array(
			'n_total_post' => array(
				__( '<strong>%s</strong> movie.', 'wpmovielibrary' ),
				__( '<strong>%s</strong> movies.', 'wpmovielibrary' ),
			),
			'n_total_terms' => array(
				__( 'You have a total of <strong>%s</strong> actor.', 'wpmovielibrary' ),
				__( 'You have a total of <strong>%s</strong> actors.', 'wpmovielibrary' ),
			),
			'snapshot_updated'      => esc_html__( 'Snapshot updated!', 'wpmovielibrary' ),
			'add_new_term'          => esc_html__( 'Create a new actor. You can customize it later.', 'wpmovielibrary' ),
			'about_tmdb_id'         => esc_html__( 'You can set a TMDb ID to actors to download available pictures.', 'wpmovielibrary' ),
			'about_related_persons' => esc_html__( 'Select a person to link to this actor. This will be used to complete biography, pictures and other details.', 'wpmovielibrary' ),
			'tmdb_id'               => esc_html__( 'TMDb ID' ),
			'name'                  => esc_html__( 'Name' ),
			'create_term'           => esc_html__( 'Create Actor', 'wpmovielibrary' ),
			'delete_term'           => esc_html__( 'Delete actor?', 'wpmovielibrary' ),
			'edit_link'             => esc_url_raw( admin_url( 'admin.php?page=wpmovielibrary-actors' ) ),
			'old_edit_link'         => esc_url_raw( admin_url( 'edit-tags.php?taxonomy=actor' ) ),
			'edit_label'            => esc_html__( 'Actor browser', 'wpmovielibrary' ),
			'old_edit_label'        => esc_html__( 'Classic actor browser', 'wpmovielibrary' ),
			'search_terms'          => esc_html__( 'Search actors', 'wpmovielibrary' ),
			'term_name'             => esc_html__( 'Actor name', 'wpmovielibrary' ),
			'new_name'              => esc_html__( 'New Name', 'wpmovielibrary' ),
			'update_name'           => esc_html__( 'Update Name', 'wpmovielibrary' ),
			'about_new_name'        => esc_html__( 'Change the current actor name.', 'wpmovielibrary' ),
			'select_thumbnail'      => esc_html__( 'Select Thumbnail', 'wpmovielibrary' ),
			'use_as_thumbnail'      => esc_html__( 'Use as actor thumbnail', 'wpmovielibrary' ),
			'no_description'        => esc_html__( 'No description set for actor "%s".', 'wpmovielibrary' ),
			'fetch_person'          => esc_html__( 'Fetch person data', 'wpmovielibrary' ),
			'save_person'           => esc_html__( 'Save related person', 'wpmovielibrary' ),
		);

		wp_localize_script( 'wpmoly-actor-browser', 'wpmolyEditorL10n', $localized_actor );
		wp_localize_script( 'wpmoly-actor-editor', 'wpmolyEditorL10n', $localized_actor );

		$localized_collection = $localized + array(
			'n_total_post' => array(
				__( '<strong>%s</strong> movie.', 'wpmovielibrary' ),
				__( '<strong>%s</strong> movies.', 'wpmovielibrary' ),
			),
			'n_total_terms'  => array(
				__( 'You have a total of <strong>%s</strong> collection.', 'wpmovielibrary' ),
				__( 'You have a total of <strong>%s</strong> collections.', 'wpmovielibrary' ),
			),
			'add_new_term'     => esc_html__( 'Create a new collection. You can customize it later.', 'wpmovielibrary' ),
			'name'             => esc_html__( 'Name' ),
			'create_term'      => esc_html__( 'Create Collection', 'wpmovielibrary' ),
			'delete_term'      => esc_html__( 'Delete collection?', 'wpmovielibrary' ),
			'edit_link'        => esc_url_raw( admin_url( 'admin.php?page=wpmovielibrary-collections' ) ),
			'old_edit_link'    => esc_url_raw( admin_url( 'edit-tags.php?taxonomy=collection' ) ),
			'edit_label'       => esc_html__( 'Collection browser', 'wpmovielibrary' ),
			'old_edit_label'   => esc_html__( 'Classic collection browser', 'wpmovielibrary' ),
			'search_terms'     => esc_html__( 'Search collections', 'wpmovielibrary' ),
			'term_name'        => esc_html__( 'Collection name', 'wpmovielibrary' ),
			'new_name'         => esc_html__( 'New Name', 'wpmovielibrary' ),
			'update_name'      => esc_html__( 'Update Name', 'wpmovielibrary' ),
			'about_new_name'   => esc_html__( 'Change the current collection name.', 'wpmovielibrary' ),
			'select_thumbnail' => esc_html__( 'Select Thumbnail', 'wpmovielibrary' ),
			'use_as_thumbnail' => esc_html__( 'Use as colection thumbnail', 'wpmovielibrary' ),
			'no_description'   => esc_html__( 'No description set for colection "%s".', 'wpmovielibrary' ),
		);

		wp_localize_script( 'wpmoly-collection-browser', 'wpmolyEditorL10n', $localized_collection );
		wp_localize_script( 'wpmoly-collection-editor', 'wpmolyEditorL10n', $localized_collection );

		$localized_genre = $localized + array(
			'n_total_post' => array(
				__( '<strong>%s</strong> movie.', 'wpmovielibrary' ),
				__( '<strong>%s</strong> movies.', 'wpmovielibrary' ),
			),
			'n_total_terms'  => array(
				__( 'You have a total of <strong>%s</strong> genre.', 'wpmovielibrary' ),
				__( 'You have a total of <strong>%s</strong> genres.', 'wpmovielibrary' ),
			),
			'add_new_term'     => esc_html__( 'Create a new genre. You can customize it later.', 'wpmovielibrary' ),
			'name'             => esc_html__( 'Name' ),
			'create_term'      => esc_html__( 'Create Genre', 'wpmovielibrary' ),
			'delete_term'      => esc_html__( 'Delete genre?', 'wpmovielibrary' ),
			'edit_link'        => esc_url_raw( admin_url( 'admin.php?page=wpmovielibrary-genres' ) ),
			'old_edit_link'    => esc_url_raw( admin_url( 'edit-tags.php?taxonomy=genre' ) ),
			'edit_label'       => esc_html__( 'Genre browser', 'wpmovielibrary' ),
			'old_edit_label'   => esc_html__( 'Classic genre browser', 'wpmovielibrary' ),
			'search_terms'     => esc_html__( 'Search genres', 'wpmovielibrary' ),
			'term_name'        => esc_html__( 'Genre name', 'wpmovielibrary' ),
			'new_name'         => esc_html__( 'New Name', 'wpmovielibrary' ),
			'update_name'      => esc_html__( 'Update Name', 'wpmovielibrary' ),
			'about_new_name'   => esc_html__( 'Change the current genre name.', 'wpmovielibrary' ),
			'select_thumbnail' => esc_html__( 'Select Thumbnail', 'wpmovielibrary' ),
			'use_as_thumbnail' => esc_html__( 'Use as genre thumbnail', 'wpmovielibrary' ),
			'no_description'   => esc_html__( 'No description set for genre "%s".', 'wpmovielibrary' ),
		);

		wp_localize_script( 'wpmoly-genre-browser', 'wpmolyEditorL10n', $localized_genre );
		wp_localize_script( 'wpmoly-genre-editor', 'wpmolyEditorL10n', $localized_genre );
	}

	/**
	 * Localize Public-side JS scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function localize_public_scripts() {

		$localized = array(
			'run' => __( 'Run Forrest, run!', 'wpmovielibrary' ),
			'api' => array(
				'missing' => __( 'Couldn’t find WordPress Rest API Backbone client.', 'wpmovielibrary' ),
				'missing_collection' => __( 'Couldn’t find WordPress Rest API Backbone client collection object.', 'wpmovielibrary' ),
			),
 			'min' => _x( 'min', 'movie runtime in minutes', 'wpmovielibrary' ),
 			'n_movie_found' => array(
				__( '%s Movie', 'wpmovielibrary' ),
				__( '%s Movies', 'wpmovielibrary' ),
			),
			'restAPIError' => __( 'An unknown error occurred while loading the content from the REST API.', 'wpmovielibrary' ),
			'restAPIErrorFootnote' => __( 'If the problem persists, contact the administrator.', 'wpmovielibrary' ),
		);

		wp_localize_script( 'wpmoly-core', 'wpmolyL10n', $localized );
	}

}
