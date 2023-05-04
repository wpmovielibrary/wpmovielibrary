<?php
/**
 * ReduxFramework Sample Config File
 * For full documentation, please visit: https://docs.reduxframework.com
 **/

if ( ! class_exists( 'WPMOLY_Redux_Framework_config' ) ) {

	class WPMOLY_Redux_Framework_config {

		public $args = array();
		public $sections = array();
		public $ReduxFramework;

		public function __construct() {

			if ( ! class_exists( 'ReduxFramework' ) )
				return;

			add_action( 'plugins_loaded', array( $this, 'initSettings' ), 10 );

		}

		public function initSettings() {

			// Set the default arguments
			$this->setArguments();

			// Set a few help tabs so you can see how it's done
			//$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();

			if ( ! isset( $this->args['opt_name'] ) )
				return;

			add_filter( 'redux/options/' . $this->args['opt_name'] . '/field/wpmoly-headbox-title/register', array( $this, 'available_movie_tags' ), 10, 1 );
			add_filter( 'redux/options/' . $this->args['opt_name'] . '/field/wpmoly-headbox-subtitle/register', array( $this, 'available_movie_tags' ), 10, 1 );
			add_filter( 'redux/options/' . $this->args['opt_name'] . '/field/wpmoly-headbox-details-1/register', array( $this, 'available_movie_tags' ), 10, 1 );
			add_filter( 'redux/options/' . $this->args['opt_name'] . '/field/wpmoly-headbox-details-2/register', array( $this, 'available_movie_tags' ), 10, 1 );
			add_filter( 'redux/options/' . $this->args['opt_name'] . '/field/wpmoly-headbox-details-3/register', array( $this, 'available_movie_tags' ), 10, 1 );
			add_filter( 'redux/options/' . $this->args['opt_name'] . '/data/post_types/after', array( $this, 'filter_select_data_post_types' ), 10, 1 );

			// If Redux is running as a plugin, this will remove the demo notice and links
			//add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

			// Function to test the compiler hook and demo CSS output.
			// Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
			//add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);

			// Change the arguments after they've been declared, but before the panel is created
			//add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );

			// Change the default value of a field after it's been set, but before it's been useds
			//add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );

			// Dynamically add a section. Can be also used to modify sections/fields
			//add_filter( 'redux/options/' . $this->args['opt_name'] . '/sections', array( $this, 'dynamic_section' ) );

			// Replace Framework Font with FontAwesome
			add_filter( 'redux/page/' . $this->args['opt_name'] . '/enqueue', array( $this, 'font_awesome_font' ) );

			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
		}

		function font_awesome_font() {

			wp_register_style( 'redux-font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', array(), '4.2.0', 'all' );
			wp_enqueue_style( 'redux-font-awesome' );
		}

		/**
		 * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
		 * Simply include this function in the child themes functions.php file.
		 * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
		 * so you must use get_template_directory_uri() if you want to use any of the built in icons
		 **/
		function dynamic_section( $sections ) {
			//$sections = array();
			$sections[] = array(
				'title'  => __( 'Section via hook', 'wpmovielibrary' ),
				'desc'   => __( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'wpmovielibrary' ),
				'icon'   => 'el-icon-paper-clip',
				// Leave this as a blank section, no options just some intro text set above.
				'fields' => array()
			);

			return $sections;
		}

		/**
		 * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
		 **/
		function change_arguments( $args ) {
			//$args['dev_mode'] = true;

			return $args;
		}

		/**
		 * Filter hook for filtering the default value of any given field. Very useful in development mode.
		 **/
		function change_defaults( $defaults ) {
			$defaults['str_replace'] = 'Testing filter hook!';

			return $defaults;
		}

		public function setSections() {

			/**
			 * Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
			 **/

			$sampleHTML = '';
			if ( file_exists( dirname( __FILE__ ) . '/info-html.html' ) ) {
				Redux_Functions::initWpFilesystem();

				global $wp_filesystem;

				$sampleHTML = $wp_filesystem->get_contents( dirname( __FILE__ ) . '/info-html.html' );
			}

			// ACTUAL DECLARATION OF SECTIONS
			$this->sections = array_merge(
				$this->sections,
				WPMOLY_Settings::get_default_settings()
			);
		}

		public function setHelpTabs() {

			// Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
			$this->args['help_tabs'][] = array(
				'id'      => 'redux-help-tab-1',
				'title'   => __( 'Theme Information 1', 'wpmovielibrary' ),
				'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'wpmovielibrary' )
			);

			$this->args['help_tabs'][] = array(
				'id'      => 'redux-help-tab-2',
				'title'   => __( 'Theme Information 2', 'wpmovielibrary' ),
				'content' => __( '<p>This is the tab content, HTML is allowed.</p>', 'wpmovielibrary' )
			);

			// Set the help sidebar
			$this->args['help_sidebar'] = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'wpmovielibrary' );
		}

		public function available_movie_tags( $field ) {

			$field['options'] = WPMOLY_Settings::get_available_movie_tags();
			return $field;
		}

		public function filter_select_data_post_types( $data ) {

			unset( $data['attachment'], $data['movie'] );

			return $data;
		}

		/**
		* All the possible arguments for Redux.
		* For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
		* */
		public function setArguments() {

			$this->args = array(
				'opt_name'             => 'wpmoly_settings',
				'display_name'         => WPMOLY_NAME,
				'display_version'      => WPMOLY_VERSION,
				'menu_type'            => 'submenu',
				'allow_sub_menu'       => true,
				'menu_title'           => __( 'Settings', 'wpmovielibrary' ),
				'page_title'           => __( 'Settings', 'wpmovielibrary' ),
				'admin_bar'            => false,
				'dev_mode'             => false,
				'update_notice'        => false,
				'customizer'           => true,
				'page_parent'          => 'wpmovielibrary',
				'page_permissions'     => 'manage_options',
				'menu_icon'            => '',
				'last_tab'             => '',
				'page_icon'            => 'icon-themes',
				'page_slug'            => 'wpmovielibrary-settings',
				'save_defaults'        => true,
				'default_show'         => true,
				'default_mark'         => '<sup> (<abbr title="' . __( 'Currently using default value.', 'wpmovielibrary' ) . '">default</abbr>)</sup>',
				'show_import_export'   => true,
				
				// CAREFUL -> These options are for advanced use only
				'transient_time'       => 60 * MINUTE_IN_SECONDS,
				'output'               => true,
				'output_tag'           => true,
				'database'             => '',
				'system_info'          => true,
				'system_info_icon_class' => 'fa fa-wrench',
				// REMOVE
			);

			// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
			$this->args['share_icons'][] = array(
				'url'   => 'https://github.com/wpmovielibrary/wpmovielibrary',
				'title' => 'Visit us on GitHub',
				'icon'  => 'wpmolicon icon-github'
			);
			$this->args['share_icons'][] = array(
				'url'   => 'https://www.facebook.com/wpmovielibrary',
				'title' => 'Like us on Facebook',
				'icon'  => 'wpmolicon icon-facebook'
			);
			$this->args['share_icons'][] = array(
				'url'   => 'http://twitter.com/WPMovieLibrary',
				'title' => 'Follow us on Twitter',
				'icon'  => 'wpmolicon icon-twitter'
			);
			$this->args['share_icons'][] = array(
				'url'   => 'https://plus.google.com/+Wpmovielibraryplugin',
				'title' => 'Join our circle on Google+',
				'icon'  => 'wpmolicon icon-googleplus'
			);
			$this->args['share_icons'][] = array(
				'url'   => 'http://wpmovielibrary.com',
				'title' => 'Find us on WPMovieLibrary.com',
				'icon'  => 'wpmolicon icon-wordpress'
			);
		}

	}

	global $wpmoly_redux_config;
	$wpmoly_redux_config = new WPMOLY_Redux_Framework_config();
}
else {
	echo "The class named WPMOLY_Redux_Framework_config has already been called. <strong>Developers, you need to prefix this class with your company name or you'll run into problems!</strong>";
}
