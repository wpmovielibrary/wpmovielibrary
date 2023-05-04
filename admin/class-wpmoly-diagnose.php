<?php
/**
 * WPMovieLibrary Diagnose Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Diagnose' ) ) :

	class WPMOLY_Diagnose extends WPMOLY_Module {

		/**
		 * Diagnose tool version.
		 * 
		 * @var    string
		 */
		private $version = '1.0.8';

		/**
		 * Last time diagnose results were saved.
		 * 
		 * @var    string
		 */
		private $last_saved = '';

		/**
		 * Diagnose steps.
		 * 
		 * @var    array
		 */
		private $items = array();

		/**
		 * Diagnose.
		 * 
		 * @var    array
		 */
		private $diagnose = array();

		/**
		 * Diagnose saving status.
		 * 
		 * @var    boolean
		 */
		private $saved;

		/**
		 * Survey send status.
		 * 
		 * @var    boolean
		 */
		private $sent;

		/**
		 * Survey dismiss status.
		 * 
		 * @var    boolean
		 */
		private $dismissed;

		/**
		 * Class Constructor.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   void
		 */
		protected function __construct() {

			if ( ! is_admin() ) {
				return false;
			}

			$this->items = array(
				'requirements' => array(
					'v2' => array(
						'system' => array(
							'title'       => __( 'System', 'wpmovielibrary' ),
							'description' => __( '', 'wpmovielibrary' ),
							'items' => array(
								'php-version' => array(
									'title'       => __( 'PHP Version', 'wpmovielibrary' ),
									'description' => __( 'The version of PHP your server is running. Version 5.3 at least is required, version 5.6 or 7 is recommended.', 'wpmovielibrary' )
								)
							)
						),
						'environment' => array(
							'title'       => __( 'Environment', 'wpmovielibrary' ),
							'description' => __( '', 'wpmovielibrary' ),
							'items' => array(
								'wordpress-version' => array(
									'title'       => __( 'WordPress Version', 'wpmovielibrary' ),
									'description' => __( 'The version of PHP your site is running. Version 4.2 at least is required, version 4.7 is recommended.', 'wpmovielibrary' )
								),
								'curl-version' => array(
									'title'       => __( 'PHP cURL', 'wpmovielibrary' ),
									'description' => __( 'WordPress rely first on PHP cURL to get remote data. Having it installed cannot hurt.', 'wpmovielibrary' )
								),
							)
						)
					),
					'v3' => array(
						'system' => array(
							'title'       => __( 'System', 'wpmovielibrary' ),
							'description' => __( '', 'wpmovielibrary' ),
							'items' => array(
								'php-version' => array(
									'title'       => __( 'PHP Version', 'wpmovielibrary' ),
									'description' => __( 'The version of PHP your server is running. Version 5.5 at least is required, version 7 is recommended.', 'wpmovielibrary' )
								)
							)
						),
						'environment' => array(
							'title'       => __( 'Environment', 'wpmovielibrary' ),
							'description' => __( '', 'wpmovielibrary' ),
							'items' => array(
								'wordpress-version' => array(
									'title'       => __( 'WordPress Version', 'wpmovielibrary' ),
									'description' => __( 'The version of PHP your site is running. Version 4.7 is required.', 'wpmovielibrary' )
								),
								'curl-version' => array(
									'title'       => __( 'PHP cURL', 'wpmovielibrary' ),
									'description' => __( 'WordPress rely first on PHP cURL to get remote data. Having it installed cannot hurt.', 'wpmovielibrary' )
								),
							)
						)
					)
				),
				'analysis' => array(
					'data' => array(
						'content' => array(
							'title'       => __( 'Content', 'wpmovielibrary' ),
							'description' => __( '', 'wpmovielibrary' ),
							'items' => array(
								'movies' => array(
									'title'       => __( 'Number of movies', 'wpmovielibrary' ),
									'description' => __( 'The number of movies in your library. This helps us estimate the average size of libraries handled by the plugin.', 'wpmovielibrary' ),
								),
								'actors' => array(
									'title'       => __( 'Number of actors', 'wpmovielibrary' ),
									'description' => __( 'The number of actors in your library. This helps us estimate the average number of terms added by the plugin.', 'wpmovielibrary' ),
								),
								'collections' => array(
									'title'       => __( 'Number of collections', 'wpmovielibrary' ),
									'description' => __( 'The number of collections in your library. This helps us estimate the average number of terms added by the plugin.', 'wpmovielibrary' ),
								),
								'genres' => array(
									'title'       => __( 'Number of genres', 'wpmovielibrary' ),
									'description' => __( 'The number of genres in your library. This helps us estimate the average number of terms added by the plugin.', 'wpmovielibrary' ),
								)
							)
						),
						'settings' => array(
							'title'       => __( 'Settings', 'wpmovielibrary' ),
							'description' => __( '', 'wpmovielibrary' ),
							'items' => array(
								'api-internal' => array(
									'title'       => __( 'Personnal API Key', 'wpmovielibrary' ),
									'description' => __( 'Are you using your personal API key? This helps us improve the relay server provided for user who don’t have an API key.', 'wpmovielibrary' )
								),
								'api-language' => array(
									'title'       => __( 'API Language', 'wpmovielibrary' ),
									'description' => __( 'Which Language does the API use. This helps us generate a list of most commonly used languages.', 'wpmovielibrary' )
								),
								'api-country' => array(
									'title'       => __( 'API Country', 'wpmovielibrary' ),
									'description' => __( 'Which Country does the API use. This helps us grasp what countries are mostly represented among users.', 'wpmovielibrary' )
								),
								'api-country-alt' => array(
									'title'       => __( 'API Alternative Country', 'wpmovielibrary' ),
									'description' => __( 'Which Alternative Country does the API use. This helps us grasp what countries are mostly represented among users.', 'wpmovielibrary' )
								),
								'poster-size' => array(
									'title'       => __( 'Posters Default Size', 'wpmovielibrary' ),
									'description' => __( 'Poster image size. This helps us estimate the diskspace usage of the plugin.', 'wpmovielibrary' )
								),
								'images-size' => array(
									'title'       => __( 'Images Default Size', 'wpmovielibrary' ),
									'description' => __( 'Movie Images size. This helps us estimate the diskspace usage of the plugin.', 'wpmovielibrary' )
								),
								'headbox-theme' => array(
									'title'       => __( 'Headbox Theme', 'wpmovielibrary' ),
									'description' => __( 'Headbox prefered theme. This helps us improving the theme choice for the Headbox feature.', 'wpmovielibrary' )
								),
							)
						),
					),
					'misc' => array(
						'content' => array(
							'title'       => __( 'Content', 'wpmovielibrary' ),
							'description' => __( '', 'wpmovielibrary' ),
							'items' => array(
								'active-theme' => array(
									'title'       => __( 'Active Theme', 'wpmovielibrary' ),
									'description' => __( 'Currently used theme on your site. This helps us improving compatibility with themes.', 'wpmovielibrary' )
								),
								'installed-themes' => array(
									'title'       => __( 'Installed Themes', 'wpmovielibrary' ),
									'description' => __( 'Currently available themes on your site. This helps us improving compatibility with themes.', 'wpmovielibrary' )
								),
								'active-plugins' => array(
									'title'       => __( 'Active Plugins', 'wpmovielibrary' ),
									'description' => __( 'Plugins currently active on your site. This helps us improving compatibility with other plugins.', 'wpmovielibrary' )
								),
								'installed-plugins' => array(
									'title'       => __( 'Installed Plugins', 'wpmovielibrary' ),
									'description' => __( 'Plugins currently available on your site. This helps us improving compatibility with other plugins.', 'wpmovielibrary' )
								),
							)
						),
						'settings' => array(
							'title'       => __( 'Settings', 'wpmovielibrary' ),
							'description' => __( '', 'wpmovielibrary' ),
							'items' => array(
								'multisite' => array(
									'title'       => __( 'Multisite enabled', 'wpmovielibrary' ),
									'description' => __( 'Are running a network or a single website? This helps us improve the plugin internal behaviour.', 'wpmovielibrary' )
								),
								'site-language' => array(
									'title'       => __( 'Site Language', 'wpmovielibrary' ),
									'description' => __( 'Default language for your site. This helps us generate a list of most commonly used languages.', 'wpmovielibrary' )
								),
								'date-format' => array(
									'title'       => __( 'Date Format', 'wpmovielibrary' ),
									'description' => __( 'Default date format used on your site. This helps us improve the plugin’s default settings.', 'wpmovielibrary' )
								),
								'time-format' => array(
									'title'       => __( 'Time Format', 'wpmovielibrary' ),
									'description' => __( 'Default time format used on your site. This helps us improve the plugin’s default settings.', 'wpmovielibrary' )
								),
								'permalink-structure' => array(
									'title'       => __( 'Permalink Structure', 'wpmovielibrary' ),
									'description' => __( 'Default used on your site. This helps us improve the plugin permalink management.', 'wpmovielibrary' )
								),
								'additional-post-types' => array(
									'title'       => __( 'Additional Post Types', 'wpmovielibrary' ),
									'description' => __( 'Are you using other custom post types? This helps us improve the plugin .', 'wpmovielibrary' )
								),
								'additional-taxonomies' => array(
									'title'       => __( 'Additional Taxonomies', 'wpmovielibrary' ),
									'description' => __( 'Are you using other custom taxonomies? This helps us improve the plugin .', 'wpmovielibrary' )
								),
							)
						)
					)
				)
			);

			$this->init();
		}

		/**
		 * Initializes variables
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   void
		 */
		public function init() {

			$defaults = array(
				'version' => '',
				'date'    => '',
				'results' => array(
					'v2' => array(
						'php-version'       => array( 'type' => '', 'message' => '' ),
						'wordpress-version' => array( 'type' => '', 'message' => '' ),
						'curl-version'      => array( 'type' => '', 'message' => '' )
					),
					'v3' => array(
						'php-version'       => array( 'type' => '', 'message' => '' ),
						'wordpress-version' => array( 'type' => '', 'message' => '' ),
						'curl-version'      => array( 'type' => '', 'message' => '' )
					)
				),
				'content' => array(
					'data' => array(
						'movies'                => '',
						'actors'                => '',
						'collections'           => '',
						'genres'                => '',
						'api-internal'          => '',
						'api-language'          => '',
						'api-country'           => '',
						'api-country-alt'       => '',
						'poster-size'           => '',
						'images-size'           => '',
						'headbox-theme'         => '',
					),
					'misc' => array(
						'active-theme'          => '',
						'installed-themes'      => '',
						'active-plugins'        => '',
						'installed-plugins'     => '',
						'multisite'             => '',
						'site-language'         => '',
						'date-format'           => '',
						'time-format'           => '',
						'permalink-structure'   => '',
						'additional-post-types' => '',
						'additional-taxonomies' => ''
					)
				)
			);

			$this->diagnose = get_option( '_wpmoly_diagnose', $defaults );

			$this->dismissed = get_option( '_wpmoly_dismiss_survey' );

			if ( empty( $this->diagnose['version'] ) || empty( $this->diagnose['date'] ) ) {
				$this->load();
			} elseif ( $this->diagnose['date'] < time() - WEEK_IN_SECONDS ) {
				$this->load();
				$this->save();
			}
		}

		/**
		 * Run Diagnose.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   void
		 */
		public function run() {

			if ( isset( $_GET['survey'] ) && 'dismiss' === $_GET['survey'] ) {
				$this->dismiss_survey( $dismiss = true );
			} elseif ( isset( $_GET['survey'] ) && 'undismiss' === $_GET['survey'] ) {
				$this->dismiss_survey( $dismiss = false );
			} elseif ( isset( $_GET['survey'] ) && ( 'answer' === $_GET['survey'] || 'update' === $_GET['survey'] ) ) {
				$this->load();
				$this->save();
				$this->send_survey();
			} elseif ( isset( $_GET['diagnose'] ) && 'run' === $_GET['diagnose'] ) {
				$this->load();
				$this->save();
			}
		}

		/**
		 * Actually perform Diagnose.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   void
		 */
		private function load() {

			$this->diagnose['version'] = $this->version;

			$this->check_system( 'v2' );
			$this->check_system( 'v3' );

			$this->check_environment( 'v2' );
			$this->check_environment( 'v3' );

			$this->check_data();
			$this->check_misc();

		}

		/**
		 * Saved diagnose results?
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   boolean
		 */
		public function is_saved() {

			return $this->saved;
		}

		/**
		 * Sent survey?
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   boolean
		 */
		public function is_sent() {

			return $this->sent;
		}

		/**
		 * Dismissed survey?
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   boolean
		 */
		public function is_dismissed() {

			return $this->dismissed;
		}

		/**
		 * Get Diagnose requirements.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @param    string    $version
		 * 
		 * @return   array
		 */
		public function get_requirements( $version = 'v2' ) {

			return $this->items['requirements'][ $version ];
		}

		/**
		 * Get Diagnose analysis.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @param    string    $section
		 * 
		 * @return   array
		 */
		public function get_analysis( $section = null ) {

			if ( ! is_null( $section ) ) {
				return $this->items['analysis'][ $section ];
			}

			return $this->items['analysis'];
		}

		/**
		 * Get Diagnose results.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @param    string    $version
		 * @param    string    $result
		 * 
		 * @return   array
		 */
		public function get_results( $version = 'v2', $result = null ) {

			if ( ! is_null( $result ) ) {
				return $this->diagnose['results'][ $version ][ $result ];
			}

			return $this->diagnose['results'][ $version ];
		}

		/**
		 * Get Diagnose analysis content.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @param    string    $section
		 * @param    string    $content
		 * 
		 * @return   array
		 */
		public function get_contents( $section = null, $content = null ) {

			if ( ! is_null( $section ) ) {
				if ( ! is_null( $content ) ) {
					return $this->diagnose['content'][ $section ][ $content ];
				} else {
					return $this->diagnose['content'][ $section ];
				}
			}

			return $this->diagnose['content'];
		}

		/**
		 * Get Diagnose Tool version.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   string
		 */
		public function get_version() {

			return $this->diagnose['version'];
		}

		/**
		 * Get last time a diagnose was performed.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   string
		 */
		public function get_last_saved() {

			return empty( $this->diagnose['date'] ) ? __( 'Never', 'wpmovielibrary' ) : intval( $this->diagnose['date'] );
		}

		/**
		 * Check System requirements.
		 * 
		 * Compare PHP version with requirements.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @param    string    $version
		 * 
		 * @return   void
		 */
		private function check_system( $version = 'v2' ) {

			if ( ! function_exists( 'phpversion' ) ) {
				return $this->diagnose['results'][ $version ]['php-version'] = array( 'type' => 'error', 'message' => __( 'Error: phpversion() is missing.', 'wpmovielibrary' ) );
			}

			$php_version = phpversion();
			if ( 'v2' === $version ) {
				if ( version_compare( $php_version, '7.0', '>=' ) ) {
					$this->diagnose['results'][ $version ]['php-version'] = array( 'type' => 'success', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $php_version ) );
				} elseif ( version_compare( $php_version, '5.3', '>=' ) ) {
					$this->diagnose['results'][ $version ]['php-version'] = array( 'type' => 'warning', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $php_version ) );
				} else {
					$this->diagnose['results'][ $version ]['php-version'] = array( 'type' => 'error', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $php_version ) );
				}
			} elseif ( 'v3' === $version ) {
				if ( version_compare( $php_version, '7.0', '>=' ) ) {
					$this->diagnose['results'][ $version ]['php-version'] = array( 'type' => 'success', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $php_version ) );
				} elseif ( version_compare( $php_version, '5.5', '>=' ) ) {
					$this->diagnose['results'][ $version ]['php-version'] = array( 'type' => 'warning', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $php_version ) );
				} else {
					$this->diagnose['results'][ $version ]['php-version'] = array( 'type' => 'error', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $php_version ) );
				}
			}
		}

		/**
		 * Check Environment requirements.
		 * 
		 * Compare WordPress and cURL with requirements.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @param    string    $version
		 * 
		 * @return   void
		 */
		private function check_environment( $version = 'v2' ) {

			global $wp_version;
			if ( 'v2' === $version ) {
				if ( version_compare( $wp_version, '4.2', '>=' ) ) {
					$this->diagnose['results'][ $version ]['wordpress-version'] = array( 'type' => 'success', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $wp_version ) );
				} else {
					$this->diagnose['results'][ $version ]['wordpress-version'] = array( 'type' => 'error', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $wp_version ) );
				}
			} elseif ( 'v3' === $version ) {
				if ( version_compare( $wp_version, '4.7', '>=' ) ) {
					$this->diagnose['results'][ $version ]['wordpress-version'] = array( 'type' => 'success', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $wp_version ) );
				} else {
					$this->diagnose['results'][ $version ]['wordpress-version'] = array( 'type' => 'error', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $wp_version ) );
				}
			}

			if ( 'v2' === $version || 'v3' === $version ) {
				if ( ! function_exists( 'curl_version' ) ) {
					$this->diagnose['results'][ $version ]['curl-version'] = array( 'type' => 'error', 'message' => __( 'Error: curl_version() is missing.', 'wpmovielibrary' ) );
				} else {
					$curl_version = curl_version();
					$this->diagnose['results'][ $version ]['curl-version'] = array( 'type' => 'success', 'message' => sprintf( __( 'Version %s.', 'wpmovielibrary' ), $curl_version['version'] ) );
				}
			}
		}

		/**
		 * Collect plugin-related data: number of movies, actors, genres,
		 * collections, ...
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   void
		 */
		private function check_data() {

			$movies      = wp_count_posts( 'movie' );
			$actors      = wp_count_terms( 'actor' );
			$collections = wp_count_terms( 'collection' );
			$genres      = wp_count_terms( 'genre' );

			$this->diagnose['content']['data']['movies']          = isset( $movies->publish ) ? $movies->publish : '';
			$this->diagnose['content']['data']['actors']          = is_numeric( $actors ) ? $actors : '';
			$this->diagnose['content']['data']['collections']     = is_numeric( $collections ) ? $collections : '';
			$this->diagnose['content']['data']['genres']          = is_numeric( $genres ) ? $genres : '';
			$this->diagnose['content']['data']['api-internal']    = wpmoly_o( 'api-internal' );
			$this->diagnose['content']['data']['api-language']    = wpmoly_o( 'api-language' );
			$this->diagnose['content']['data']['api-country']     = wpmoly_o( 'api-country' );
			$this->diagnose['content']['data']['api-country-alt'] = wpmoly_o( 'api-country-alt' );
			$this->diagnose['content']['data']['poster-size']     = wpmoly_o( 'poster-size' );
			$this->diagnose['content']['data']['images-size']     = wpmoly_o( 'images-size' );
			$this->diagnose['content']['data']['headbox-theme']   = wpmoly_o( 'headbox-theme' );
		}

		/**
		 * Collect site-wide informations: active theme, available themes,
		 * plugins, permalinks, date and time formats, ...
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   void
		 */
		private function check_misc() {

			$theme = wp_get_theme();
			$this->diagnose['content']['misc']['active-theme'] = $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' );

			$themes = wp_get_themes();
			foreach ( $themes as $id => $theme ) {
				$themes[ $id ] = $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' );
			}

			$this->diagnose['content']['misc']['installed-themes'] = implode( ', ', $themes );

			// Load plugin file
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			$active_plugins = array();
			$installed_plugins = array();

			$plugins = get_plugins();
			$actives = get_option( 'active_plugins', array() );
			foreach ( $plugins as $id => $plugin ) {
				if ( false === strpos( $id, 'wpmovielibrary' ) ) {
					if ( in_array( $id, $actives ) ) {
						$active_plugins[ $id ] = $plugin['Name'] . ' ' . $plugin['Version'];
					} else {
						$installed_plugins[ $id ] = $plugin['Name'] . ' ' . $plugin['Version'];
					}
				}
			}

			$this->diagnose['content']['misc']['active-plugins']    = implode( ', ', $active_plugins );
			$this->diagnose['content']['misc']['installed-plugins'] = implode( ', ', $installed_plugins );

			$this->diagnose['content']['misc']['multisite'] = is_multisite() ? 'Yes' : 'No';
			$this->diagnose['content']['misc']['site-language'] = get_locale();
			$this->diagnose['content']['misc']['date-format'] = get_option( 'date_format' );
			$this->diagnose['content']['misc']['time-format'] = get_option( 'time_format' );
			$this->diagnose['content']['misc']['permalink-structure'] = get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default';

			$post_types = get_post_types( array( 'exclude' => array( 'movie' ), '_builtin' => false ) );
			$this->diagnose['content']['misc']['additional-post-types'] = implode( ', ', $post_types );

			$taxonomies = get_taxonomies( array( 'exclude' => array( 'collection', 'genre', 'actor' ), '_builtin' => false ) );
			$this->diagnose['content']['misc']['additional-taxonomies'] = implode( ', ', $taxonomies );
		}

		/**
		 * Dismiss/undismiss survey info.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @param    boolean    $dismiss
		 * 
		 * @return   boolean
		 */
		public function dismiss_survey( $dismiss = true ) {

			if ( false === $dismiss ) {
				delete_option( '_wpmoly_dismiss_survey' );
				return $this->dismissed = false;
			}

			update_option( '_wpmoly_dismiss_survey', '1', $autoload = false );

			return $this->dismissed = true;
		}

		/**
		 * Send survey data.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   boolean
		 */
		public function send_survey() {

			$this->sent = true;
		}

		/**
		 * Save diagnose results.
		 * 
		 * @since    2.1.4.4
		 * 
		 * @return   boolean
		 */
		public function save() {

			$this->diagnose['date'] = time();

			update_option( '_wpmoly_diagnose', $this->diagnose, $autoload = false );

			return $this->saved = true;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation.
		 * 
		 * @since    2.1.4.4
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin.
		 * 
		 * @since    2.1.4.4
		 */
		public function deactivate() {}

		/**
		 * Register callbacks for actions and filters.
		 * 
		 * @since    2.1.4.4
		 */
		public function register_hook_callbacks() {}

	}

endif;
