<?php
/**
 * Define the settings admin page class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary\Admin;

use WPMovieLibrary\Settings as SettingsManager;
use function WPMovieLibrary\Support\Helpers\config;

/**
 * Render the plugin settings page.
 *
 * Single-action class: instantiated on demand by Backstage and invoked
 * directly. Reads the stored settings, builds the language and country
 * select data and renders the matching template.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Settings {

	use Renderable;

	/**
	 * Primary country associated with each supported language, used to
	 * derive a flag emoji for language options on the settings page.
	 *
	 * Language codes are ISO 639-1, country codes are ISO 3166-1 alpha-2.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private const LANGUAGE_COUNTRIES = [
		'ar' => 'SA',
		'bg' => 'BG',
		'cn' => 'HK',
		'cs' => 'CZ',
		'da' => 'DK',
		'de' => 'DE',
		'el' => 'GR',
		'en' => 'GB',
		'es' => 'ES',
		'fa' => 'IR',
		'fi' => 'FI',
		'fr' => 'FR',
		'he' => 'IL',
		'hi' => 'IN',
		'hu' => 'HU',
		'it' => 'IT',
		'ja' => 'JP',
		'ko' => 'KR',
		'nl' => 'NL',
		'no' => 'NO',
		'pl' => 'PL',
		'pt' => 'PT',
		'ru' => 'RU',
		'sv' => 'SE',
		'tr' => 'TR',
		'uk' => 'UA',
		'zh' => 'CN',
	];

	/**
	 * Render the settings page.
	 *
	 * @since 6.0.0
	 *
	 * @access public
	 */
	public function __invoke() {

		$settings = SettingsManager::get_instance();

		$tmdb_language = (string) $settings->get( 'tmdb.language', 'en' );
		$tmdb_country  = (string) $settings->get( 'tmdb.country', 'US' );

		// l10n config is normally reserved for future L10n classes, but is
		// explicitly authorized here to populate the settings selects.
		$l10n = (array) config( 'l10n', [] );

		$languages = [];
		foreach ( (array) ( $l10n['languages']['supported'] ?? [] ) as $code => $name ) {
			$languages[ $code ] = [
				'name' => $name,
				'flag' => $this->flag_emoji( self::LANGUAGE_COUNTRIES[ $code ] ?? '' ),
			];
		}

		$countries = [];
		foreach ( (array) ( $l10n['countries']['supported'] ?? [] ) as $code => $name ) {
			$countries[ $code ] = [
				'name' => $name,
				'flag' => $this->flag_emoji( $code ),
			];
		}

		// Preserve previously saved values that fall outside the supported
		// lists, so re-saving the form doesn't silently change them.
		if ( '' !== $tmdb_language && ! isset( $languages[ $tmdb_language ] ) ) {
			$languages[ $tmdb_language ] = [
				'name' => $tmdb_language,
				'flag' => '',
			];
		}
		if ( '' !== $tmdb_country && ! isset( $countries[ $tmdb_country ] ) ) {
			$countries[ $tmdb_country ] = [
				'name' => $tmdb_country,
				'flag' => $this->flag_emoji( $tmdb_country ),
			];
		}

		$this->render( 'settings', [
			'plugin_page' => 'wpmovielibrary-settings',
			'hook_suffix' => get_current_screen()->id,
			'post_type'   => get_current_screen()->post_type ?? '',
			'tmdb_api_key'  => (string) $settings->get( 'tmdb.api_key', '' ),
			'tmdb_language' => $tmdb_language,
			'tmdb_country'  => $tmdb_country,
			'languages'     => $languages,
			'countries'     => $countries,
			'saved'         => ! empty( $_GET['updated'] ),
		] );
	}

	/**
	 * Convert an ISO 3166-1 alpha-2 country code to its flag emoji.
	 *
	 * Flag emojis are sequences of Regional Indicator Symbols: each letter
	 * of the country code maps to a code point in the U+1F1E6–U+1F1FF range.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @param string $country ISO 3166-1 alpha-2 country code, e.g. 'FR'.
	 *
	 * @return string Flag emoji, or an empty string for invalid codes.
	 */
	private function flag_emoji( string $country ) {

		$country = strtoupper( $country );
		if ( ! preg_match( '/^[A-Z]{2}$/', $country ) ) {
			return '';
		}

		$flag = '';
		foreach ( str_split( $country ) as $letter ) {
			$flag .= html_entity_decode( sprintf( '&#x%X;', 0x1F1E6 + ord( $letter ) - ord( 'A' ) ), ENT_NOQUOTES, 'UTF-8' );
		}

		return $flag;
	}
}
