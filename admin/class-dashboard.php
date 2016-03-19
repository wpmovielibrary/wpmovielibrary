<?php
/**
 * WPMovieLibrary Dashboard Class extension.
 * 
 * Implement a simple custom Dashboard
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Dashboard' ) ) :

	class WPMOLY_Dashboard extends WPMOLY_Module {

		/**
		 * Dashboard Widgets.
		 * 
		 * @since    1.0
		 * 
		 * @var      array
		 */
		protected $widgets = array();

		/**
		 * Dashboard allowed screen options.
		 * 
		 * @since    1.2
		 * 
		 * @var      array
		 */
		protected $allowed_options = array();

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			$this->init();
			$this->register_hook_callbacks();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {

			global $wpmoly_dashboard_widgets;

			$this->widgets = array();
			$this->allowed_options = array( 'welcome_panel' );

			foreach ( $wpmoly_dashboard_widgets as $slug => $widget ) {

				$class = $this->filter_widget_classname( $widget['class'] );
				$this->widgets[ $class ] = $class::get_instance();
				$this->allowed_options[] = $slug;		
			}

		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_filter( 'set-screen-option', array( $this, 'set_option' ), 10, 3 );
			add_filter( 'screen_settings', array( $this, 'screen_options' ), 10, 2 );
			add_filter( 'wpmoly_filter_widget_classname', array( $this, 'filter_widget_classname' ), 10, 1 );

			add_action( 'wp_ajax_wpmoly_save_screen_option', array( $this, 'wpmoly_save_screen_option_callback' ) );
			add_action( 'wp_ajax_wpmoly_save_dashboard_widget_settings', __CLASS__ . '::wpmoly_save_dashboard_widget_settings_callback' );
			add_action( 'wp_ajax_wpmoly_load_more_movies', __CLASS__ . '::wpmoly_load_more_movies_callback' );
		}

		/**
		 * AJAX Callback to update the plugin screen options.
		 * 
		 * 
		 * 
		 * @since    1.0
		 */
		public function wpmoly_save_screen_option_callback() {

			check_ajax_referer( 'screen-options-nonce', 'screenoptionnonce' );

			$screen_id = ( isset( $_POST['screenid'] ) && '' != $_POST['screenid'] ? $_POST['screenid'] : null );
			$visible = ( isset( $_POST['visible'] ) && in_array( $_POST['visible'], array( '0', '1' ) ) ? $_POST['visible'] : '0' );
			$option = ( isset( $_POST['option'] ) && '' != $_POST['option'] ? $_POST['option'] : null );

			if ( is_null( $screen_id ) || is_null( $option ) || ! in_array( $option, $this->allowed_options ) )
				wp_die( 0 );

			$update = self::save_screen_option( $option, $visible, $screen_id );

			wp_die( $update );
		}

		/**
		 * AJAX Callback to update the plugin Widgets settings.
		 * 
		 * 
		 * 
		 * @since    1.0
		 */
		public static function wpmoly_save_dashboard_widget_settings_callback() {

			$widget = ( isset( $_POST['widget'] ) && '' != $_POST['widget'] ? $_POST['widget'] : null );
			$setting = ( isset( $_POST['setting'] ) && '' != $_POST['setting'] ? $_POST['setting'] : null );
			$value = ( isset( $_POST['value'] ) && '' != $_POST['value'] ? $_POST['value'] : null );

			if ( is_null( $widget ) || is_null( $setting ) || is_null( $value ) || ! class_exists( $widget ) )
				wp_die( 0 );

			wpmoly_check_ajax_referer( 'save-' . strtolower( $widget ) );

			$class = $widget::get_instance();
			$update = self::save_widget_setting( $class->widget_id, $setting, $value );

			WPMOLY_Utils::ajax_response( $update, array(), WPMOLY_Utils::create_nonce( 'save-' . strtolower( $widget ) ) );
		}

		/**
		 * AJAX Callback to load more movies to the Widget.
		 * 
		 * @since    1.0
		 */
		public static function wpmoly_load_more_movies_callback() {

			wpmoly_check_ajax_referer( 'load-more-widget-movies' );

			$widget = ( isset( $_GET['widget'] ) && '' != $_GET['widget'] ? $_GET['widget'] : null );
			$offset = ( isset( $_GET['offset'] ) && '' != $_GET['offset'] ? $_GET['offset'] : 0 );
			$limit  = ( isset( $_GET['limit'] ) && '' != $_GET['limit'] ? $_GET['limit'] : null );

			if ( is_null( $widget ) || ! class_exists( $widget ) )
				wp_die( 0 );

			$class = $widget::get_instance();
			$class->get_widget_content( $limit, $offset );
			wp_die();
		}

		/**
		 * Save plugin Welcome Panel screen option.
		 *
		 * @since    1.0
		 * 
		 * @param    bool|int    $status Screen option value. Default false to skip.
		 * @param    string      $option The option name.
		 * @param    int         $value The number of rows to use.
		 * 
		 * @return   bool|string
		 */
		public function set_option( $status, $option, $value ) {

			if ( in_array( $option, $this->allowed_options ) )
				return $value;
		}

		/**
		 * Show plugin Welcome panel screen option form.
		 *
		 * @since    1.0
		 * 
		 * @param    string    $status Screen settings markup.
		 * @param    object    WP_Screen object.
		 * 
		 * @return   string    Updated screen settings
		 */
		public function screen_options( $status, $args ) {

			if ( $args->base != 'toplevel_page_wpmovielibrary' )
				return $status;

			$user_id = get_current_user_id();
			$hidden = get_user_option( 'metaboxhidden_' . $args->base );

			if ( ! is_array( $hidden ) )
				update_user_option( $user_id, 'metaboxhidden_' . $args->base, array(), true );

			$return = array( '<h5>' . __( 'Show on screen', 'wpmovielibrary' ) . '</h5>' );
			$return[] = $this->set_screen_option( 'welcome_panel', __( 'Welcome', 'wpmovielibrary' ), $status );

			global $wpmoly_dashboard_widgets;
			foreach ( $wpmoly_dashboard_widgets as $slug => $widget )
				$return[] = $this->set_screen_option( $slug, $widget['title'], $status );

			$return[] = get_submit_button( __( 'Apply', 'wpmovielibrary' ), 'button hide-if-js', 'screen-options-apply', false );

			$return = implode( '', $return );

			return $return;
		}

		/**
		 * Generate and render screen option.
		 *
		 * @since    1.0
		 * 
		 * @param    string    $option Screen option ID.
		 * @param    string    $title Screen option title.
		 * @param    string    $status Screen setting markup.
		 * 
		 * @return   string    Updated screen settings
		 */
		private function set_screen_option( $option, $title, $status ) {

			if ( ! in_array( $option, $this->allowed_options ) )
				return $status;

			$hidden = get_user_option( 'metaboxhidden_' . get_current_screen()->id );
			$visible = ( in_array( 'wpmoly_dashboard_' . $option . '_widget', $hidden ) ? '0' : '1' );

			$return = $status . '<label for="show_wpmoly_' . $option . '"><input id="show_wpmoly_' . $option . '" type="checkbox"' . checked( $visible, '1', false ) . ' />' . __( $title, 'wpmovielibrary' ) . '</label>';
			
			return $return;
		}

		/**
		 * Save Widgets screen options. This is used to init the screen
		 * options if they don't exist yet.
		 *
		 * @since    1.0
		 * 
		 * @return   array    List of hidden Widgets ID
		 */
		private static function save_screen_options() {

			$edited  = false;;
			$user_id = get_current_user_id();
			$screen  = get_current_screen();
			$hidden  = get_user_option( 'metaboxhidden_' . $screen->id );

			return $hidden;
		}

		/**
		 * Save a single Widget screen options.  This is used to save
		 * the options through AJAX.
		 *
		 * @since    1.0
		 * 
		 * @param    string    $option Screen setting ID.
		 * @param    string    $value Screen setting value.
		 * @param    string    $value Screen ID.
		 * 
		 * @return   int       Update status for JSON: 1 on success, 0 on failure.
		 */
		private static function save_screen_option( $option, $value, $screen_id ) {

			$user_id = get_current_user_id();
			$hidden = get_user_option( 'metaboxhidden_' . $screen_id );
			$hidden = ( is_array( $hidden ) ? $hidden : array() );
			$option = 'wpmoly_dashboard_' . $option . '_widget';

			$_option = array_search( $option, $hidden );

			if ( '0' == $value && ! $_option )
				$hidden[] = $option;
			else if ( '0' == $value && ! isset( $hidden[ $_option ] ) )
				$hidden[] = $option;
			else if ( '1' == $value && isset( $hidden[ $_option ] ) )
				unset( $hidden[ $_option ] );

			$hidden = array_unique( $hidden );

			$update = update_user_option( $user_id, 'metaboxhidden_' . $screen_id, $hidden, true );
			$update = ( true === $update ? 1 : 0 );

			return $update;
		}

		/**
		 * Save a plugin Dashboard Widget setting.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $widget_id Widget ID
		 * @param    string    $setting Setting name
		 * @param    string    $value Setting value
		 * 
		 * @return   boolean   Update status, success or failure
		 */
		private static function save_widget_setting( $widget_id, $setting, $value ) {

			$settings = get_user_option( $widget_id . '_settings' );

			if ( ! $settings ) {
				update_user_option( get_current_user_id(), $widget_id . '_settings', array() );
				$settings = $defaults;
			}

			$settings[ $setting ] = esc_attr( $value );
			$update = update_user_option( get_current_user_id(), $widget_id . '_settings', $settings );

			return $update;
		}

		/**
		 * Render WPMOLY Dashboard Page.
		 * 
		 * Create a nice landing page for the plugin, displaying recent
		 * movies and other stuff like a simple shortcut menu.
		 * 
		 * @since    1.0
		 */
		public static function dashboard() {

			$hidden = self::save_screen_options();

			if ( isset( $_GET['hide_wpmoly_api_key_notice'] ) && ( isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'hide-wpmoly-api-key-notice' ) ) )
				WPMovieLibrary_Admin::show_api_key_notice();

			if ( isset( $_GET['wpmoly_set_archive_page'] ) && ( isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'wpmoly-set-archive-page' ) ) )
				WPMOLY_Utils::set_archive_page();

			global $wpmoly_dashboard_widgets;
			foreach ( $wpmoly_dashboard_widgets as $widget )
				self::add_dashboard_widget( $widget );

			echo self::render_admin_template( '/dashboard/dashboard.php', array( 'screen' => get_current_screen(), 'hidden' => $hidden ) );
			echo self::render_admin_template( '/dashboard/movie-modal.php' );
		}
 
		/**
		 * Adds a new widget to the Plugin's Dashboard.
		 * 
		 * @since    1.0
		 * 
		 * @param    array     $widget Widget data
		 */
		public static function add_dashboard_widget( $widget ) {

			global $wp_dashboard_control_callbacks;

			extract( $widget );

			$class = apply_filters( 'wpmoly_filter_widget_classname', $class );
			$widget_id = strtolower( $class );
			$widget_name = __( $name, 'wpmovielibrary' );
			$location = ( 'side' == $location ? 'side' : 'normal' );
			$instance = new $class;
			$callback = array( $instance, 'dashboard_widget' );
			$callback_args = array( 'id' => $widget_id );
			$control_callback = ( 'side' == $location ? null : array( $instance, 'dashboard_widget_handle' ) );

			if ( ! is_null( $control_callback ) && current_user_can( 'edit_dashboard' ) && is_callable( $control_callback ) ) {

				$wp_dashboard_control_callbacks[ $widget_id ] = $control_callback;
				$widget_name = __( $widget_name, 'wpmovielibrary' );

				if ( isset( $_GET['edit'] ) && $widget_id == $_GET['edit'] ) {
					list( $url ) = explode( '#', add_query_arg( 'edit', false ), 2 );
					$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '" class="edit-box close-box"><span class="hide-if-js">' . __( 'Cancel' ) . '</span><span class="hide-if-no-js">' . __( 'Close' ) . '</span></a></span>';
					$callback = $control_callback;
				}
				else {
					list( $url ) = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
					$widget_name .= ' <span class="postbox-title-action"><a href="' . wp_nonce_url( "$url#$widget_id", "edit_$widget_id" ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>';
					$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '" class="edit-box close-box hide-if-no-js hide-if-js"><span class="hide-if-js">' . __( 'Cancel' ) . '</span><span class="hide-if-no-js">' . __( 'Close' ) . '</span></a></span>';
				}
			}

			$screen = get_current_screen();
			$priority = 'core';

			add_meta_box( $widget_id, $widget_name, $callback, $screen, $location, $priority, $callback_args );

		}

		/**
		 * Prepare Widgets Class Names
		 *
		 * @since    1.2
		 *
		 * @param    string    Default classname
		 *
		 * @param    string    Filter classname
		 */
		public function filter_widget_classname( $name ) {

			return "WPMOLY_Dashboard_{$name}_Widget";
		}

		/**
		 * Prepare sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Roll back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {}

	}

endif;
