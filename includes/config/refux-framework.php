<?php
/**
 * ReduxFramework Sample Config File
 * For full documentation, please visit: https://docs.reduxframework.com
 **/

if ( ! class_exists( 'WPML_Redux_Framework_config' ) ) {

	class WPML_Redux_Framework_config {

		public $args = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;

		public function __construct() {

			if ( ! class_exists( 'ReduxFramework' ) )
				return;

			// This is needed. Bah WordPress bugs.  ;)
			if ( true == Redux_Helpers::isTheme( __FILE__ ) )
				$this->initSettings();
			else
				add_action( 'plugins_loaded', array( $this, 'initSettings' ), 10 );

		}

		public function initSettings() {

			// Just for demo purposes. Not needed per say.
			$this->theme = wp_get_theme();

			// Set the default arguments
			$this->setArguments();

			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();

			if ( ! isset( $this->args['opt_name'] ) )
				return;

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
			add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
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
			global $_wpml_settings_;
			$this->sections = array_merge(
				$this->sections,
				$_wpml_settings_
			);


			$theme_info = '<div class="redux-framework-section-desc">';
			$theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . __( '<strong>Theme URL:</strong> ', 'wpmovielibrary' ) . '<a href="' . $this->theme->get( 'ThemeURI' ) . '" target="_blank">' . $this->theme->get( 'ThemeURI' ) . '</a></p>';
			$theme_info .= '<p class="redux-framework-theme-data description theme-author">' . __( '<strong>Author:</strong> ', 'wpmovielibrary' ) . $this->theme->get( 'Author' ) . '</p>';
			$theme_info .= '<p class="redux-framework-theme-data description theme-version">' . __( '<strong>Version:</strong> ', 'wpmovielibrary' ) . $this->theme->get( 'Version' ) . '</p>';
			$theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get( 'Description' ) . '</p>';
			$tabs = $this->theme->get( 'Tags' );
			if ( ! empty( $tabs ) ) {
				$theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . __( '<strong>Tags:</strong> ', 'wpmovielibrary' ) . implode( ', ', $tabs ) . '</p>';
			}
			$theme_info .= '</div>';

			if ( file_exists( dirname( __FILE__ ) . '/../README.md' ) ) {
				$this->sections['theme_docs'] = array(
					'icon'   => 'el-icon-list-alt',
					'title'  => __( 'Documentation', 'wpmovielibrary' ),
					'fields' => array(
						array(
							'id'       => '17',
							'type'     => 'raw',
							'markdown' => true,
							'content'  => file_get_contents( dirname( __FILE__ ) . '/../README.md' )
						),
					),
				);
			}

			$this->sections[] = array(
				'title'  => __( 'Import / Export', 'wpmovielibrary' ),
				'desc'   => __( 'Import and Export your Redux Framework settings from file, text or URL.', 'wpmovielibrary' ),
				'icon'   => 'el-icon-refresh',
				'fields' => array(
					array(
						'id'         => 'opt-import-export',
						'type'       => 'import_export',
						'title'      => 'Import Export',
						'subtitle'   => 'Save and restore your Redux options',
						'full_width' => false,
					),
				),
			);

			$this->sections[] = array(
				'type' => 'divide',
			);

			$this->sections[] = array(
				'icon'   => 'el-icon-info-sign',
				'title'  => __( 'Theme Information', 'wpmovielibrary' ),
				'desc'   => __( '<p class="description">This is the Description. Again HTML is allowed</p>', 'wpmovielibrary' ),
				'fields' => array(
					array(
						'id'      => 'opt-raw-info',
						'type'    => 'raw',
						'content' => $item_info,
					)
				),
			);

			if ( file_exists( trailingslashit( dirname( __FILE__ ) ) . 'README.html' ) ) {
				$tabs['docs'] = array(
					'icon'    => 'el-icon-book',
					'title'   => __( 'Documentation', 'wpmovielibrary' ),
					'content' => nl2br( file_get_contents( trailingslashit( dirname( __FILE__ ) ) . 'README.html' ) )
				);
			}
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

		/**
		* All the possible arguments for Redux.
		* For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
		* */
		public function setArguments() {

			$plugin = get_plugin_data( WPML_PATH . 'wpmovielibrary.php' ); // For use with some settings. Not necessary.

			$this->args = array(
				'opt_name'             => '_wpml_settings',
				'display_name'         => $plugin['Name'],
				'display_version'      => $plugin['Version'],
				'menu_type'            => 'submenu',
				'allow_sub_menu'       => false,
				'menu_title'           => __( 'Settings', 'wpmovielibrary' ),
				'page_title'           => __( 'Settings', 'wpmovielibrary' ),
				'admin_bar'            => false,
				'dev_mode'             => true,
				'update_notice'        => true,
				'customizer'           => false,
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
				'system_info'          => false,
				// REMOVE

				// HINTS
				'hints' => array(
					'icon'          => 'icon-question-sign',
					'icon_position' => 'right',
					'icon_color'    => 'lightgray',
					'icon_size'     => 'normal',
					'tip_style'     => array(
						'color'   => 'dark',
						'shadow'  => false,
						'rounded' => false,
						'style'   => '',
					),
					'tip_position'  => array(
						'my' => 'top center',
						'at' => 'bottom center',
					),
					'tip_effect'    => array(
						'show' => array(
							'effect'   => '',
							'duration' => '0',
							'event'    => 'mouseover',
						),
						'hide' => array(
							'effect'   => '',
							'duration' => '0',
							'event'    => 'click mouseleave',
						),
					),
				)
			);

			// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
			$this->args['share_icons'][] = array(
				'url'   => 'https://github.com/wpmovielibrary/wpmovielibrary',
				'title' => 'Visit us on GitHub',
				'icon'  => 'el-icon-github'
			);
			$this->args['share_icons'][] = array(
				'url'   => 'https://www.facebook.com/wpmovielibrary',
				'title' => 'Like us on Facebook',
				'icon'  => 'el-icon-facebook'
			);
			$this->args['share_icons'][] = array(
				'url'   => 'http://twitter.com/WPMovieLibrary',
				'title' => 'Follow us on Twitter',
				'icon'  => 'el-icon-twitter'
			);
			$this->args['share_icons'][] = array(
				'url'   => 'http://wpmovielibrary.com',
				'title' => 'Find us on WPMovieLibrary.com',
				'icon'  => 'el-icon-globe-alt'
			);

			// Panel Intro text -> before the form
			if ( ! isset( $this->args['global_variable'] ) || $this->args['global_variable'] !== false ) {
				if ( ! empty( $this->args['global_variable'] ) ) {
					$v = $this->args['global_variable'];
				}
				else {
					$v = str_replace( '-', '_', $this->args['opt_name'] );
				}
				$this->args['intro_text'] = sprintf( __( '<p>Did you know that Redux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', 'wpmovielibrary' ), $v );
			}
			else {
				$this->args['intro_text'] = __( '<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', 'wpmovielibrary' );
			}

			// Add content after the form.
			$this->args['footer_text'] = __( '<p>This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.</p>', 'wpmovielibrary' );
		}

		public function validate_callback_function( $field, $value, $existing_value ) {

			$error = true;
			$value = 'just testing';

			/*
			do your validation

			if(something) {
			    $value = $value;
			} elseif(something else) {
			    $error = true;
			    $value = $existing_value;
			    
			}
			*/

			$return['value'] = $value;
			$field['msg']    = 'your custom error message';
			if ( $error == true )
				$return['error'] = $field;

			return $return;
		}

		public function class_field_callback( $field, $value ) {
			print_r( $field );
			echo '<br/>CLASS CALLBACK';
			print_r( $value );
		}

        }

        global $reduxConfig;
        $reduxConfig = new WPML_Redux_Framework_config();
}
else {
	echo "The class named WPML_Redux_Framework_config has already been called. <strong>Developers, you need to prefix this class with your company name or you'll run into problems!</strong>";
}

/**
 * Custom function for the callback referenced above
 */
if ( ! function_exists( 'redux_my_custom_field' ) ):
	function redux_my_custom_field( $field, $value ) {
		print_r( $field );
		echo '<br/>';
		print_r( $value );
	}
endif;

/**
 * Custom function for the callback validation referenced above
 **/
if ( ! function_exists( 'redux_validate_callback_function' ) ):
	function redux_validate_callback_function( $field, $value, $existing_value ) {

		$error = true;
		$value = 'just testing';

		/*
		do your validation

		if(something) {
			$value = $value;
		} elseif(something else) {
			$error = true;
			$value = $existing_value;
		}
		*/

		$return['value'] = $value;
		$field['msg']    = 'your custom error message';
		if ( $error == true )
			$return['error'] = $field;

		return $return;
	}
endif;
