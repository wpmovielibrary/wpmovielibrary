<?php

namespace wpmoly\Helpers;

class Country {

	/**
	 * Country ISO 3166-1 Code.
	 * 
	 * @var    string
	 */
	public $code = '';

	/**
	 * Country standard name.
	 * 
	 * @var    string
	 */
	public $standard_name = '';

	/**
	 * Country translated name.
	 * 
	 * @var    string
	 */
	public $localized_name = '';

	/**
	 * Restricted list for API support
	 * 
	 * @var    array
	 */
	public static $supported = array();

	/**
	 * ISO 3166-1 table.
	 * 
	 * @var    array
	 */
	public static $available = array(
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
	 * Initialize the instance.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function __construct() {

		if ( empty( self::$supported ) ) {
			self::$supported = array(
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
		}

		/**
		 * Filter the default supported countries list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported
		 */
		self::$supported = apply_filters( 'wpmoly/filter/countries/supported', self::$supported );

		/**
		 * Filter the default available countries list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $countries
		 */
		self::$available = apply_filters( 'wpmoly/filter/countries/available', self::$available );

	}

	/**
	 * Match a country by its name or code.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $data
	 * 
	 * @return   void
	 */
	protected function match( $data ) {

		$data = (string) $data;

		if ( isset( self::$available[ $data ] ) ) {
			$this->code = $data;
			$this->standard_name = self::$available[ $data ];
			$this->localize();

			return $this;
		}

		$code = array_search( $data, self::$available );
		if ( false !== $code ) {
			$this->code = $code;
			$this->standard_name = $data;
			$this->localize();
		}

		return $this;
	}

	/**
	 * Set the translated name of the country.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function localize() {

		if ( empty( $this->code ) ) {
			return false;
		}

		if ( ! isset( self::$available[ $this->code ] ) ) {
			return false;
		}

		$this->localized_name = __( self::$available[ $this->code ], 'wpmovielibrary-iso' );
	}

	/**
	 * Generate country's flag.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function flag() {

		$flag = sprintf( '<span class="flag flag-%s" title="%s"></span>', strtolower( $this->code ), $this->standard_name );

		/**
		* Apply filter to the rendered country flag
		* 
		* @since    2.0
		* 
		* @param    string    $flag HTML markup
		* @param    string    $country Country
		*/
		return apply_filters( 'wpmoly/filter/country/flag/html', $flag, $this );
	}

	/**
	 * Get a country.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $country
	 * 
	 * @return   Country
	 */
	public static function get( $country ) {

		$country = trim( (string) $country );

		$instance = new static;
		$instance->match( $country );

		return $instance;
	}
}