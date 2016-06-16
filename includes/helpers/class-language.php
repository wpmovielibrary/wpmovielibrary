<?php

namespace wpmoly\Helpers;

class Language {

	/**
	 * Language ISO 639-1 Code.
	 * 
	 * @var    string
	 */
	public $code = '';

	/**
	 * Language native name.
	 * 
	 * @var    string
	 */
	public $native_name = '';

	/**
	 * Language standard name.
	 * 
	 * @var    string
	 */
	public $standard_name = '';

	/**
	 * Language translated name.
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
	 * ISO 639-1 table of native languages names.
	 * 
	 * @var    array
	 */
	public static $native = array(
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
	 * ISO 639-1 table of standard languages names.
	 * 
	 * @var    array
	 */
	public static $standard = array(
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
		}

		/**
		 * Filter the default supported languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported
		 */
		self::$supported = apply_filters( 'wpmoly/filter/languages/supported', self::$supported );

		/**
		 * Filter the default native languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $native
		 */
		self::$native = apply_filters( 'wpmoly/filter/languages/native', self::$native );

		/**
		 * Filter the default standard languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $standard
		 */
		self::$standard = apply_filters( 'wpmoly/filter/languages/standard', self::$standard );

	}

	/**
	 * Match a language by its name or code.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $data
	 * 
	 * @return   void
	 */
	protected function match( $data ) {

		$data = (string) $data;

		// Find language ISO code
		if ( isset( self::$standard[ $data ] ) ) {
			$this->code = $data;
			$this->native_name   = self::$native[ $data ];
			$this->standard_name = self::$standard[ $data ];
			$this->localize();

			return $this;
		}

		$code = array_search( $data, self::$native );
		if ( false !== $code ) {
			$this->code = $code;
			$this->native_name   = $data;
			$this->standard_name = self::$standard[ $code ];
			$this->localize();

			return $this;
		}

		$code = array_search( $data, self::$standard );
		if ( false !== $code ) {
			$this->code = $code;
			$this->native_name   = self::$standard[ $code ];
			$this->standard_name = $data;
			$this->localize();
		}

		return $this;
	}

	/**
	 * Set the translated name of the language.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function localize() {

		if ( empty( $this->code ) ) {
			return false;
		}

		if ( ! isset( self::$standard[ $this->code ] ) ) {
			return false;
		}

		$this->localized_name = __( self::$standard[ $this->code ], 'wpmovielibrary-iso' );
	}

	/**
	 * Get a language.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $language
	 * 
	 * @return   Language
	 */
	public static function get( $language ) {

		$language = trim( (string) $language );

		$instance = new static;
		$instance->match( $language );

		return $instance;
	}
}