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

			global $_wpml_settings_;
			$this->sections = array_merge(
				$this->sections,
				$_wpml_settings_
			);

			// ACTUAL DECLARATION OF SECTIONS
			$this->sections[] = array(
				'title'  => __( 'Home Settings', 'wpmovielibrary' ),
				'desc'   => __( 'Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at: <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>', 'wpmovielibrary' ),
				'icon'   => 'el-icon-home',
				// 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
				'fields' => array(

					array(
						'id'       => 'opt-web-fonts',
						'type'     => 'media',
						'title'    => __( 'Web Fonts', 'wpmovielibrary' ),
						'compiler' => 'true',
						'mode'     => false,
						// Can be set to false to allow any media type, or can also be set to any mime type.
						'desc'     => __( 'Basic media uploader with disabled URL input field.', 'wpmovielibrary' ),
						'subtitle' => __( 'Upload any media using the WordPress native uploader', 'wpmovielibrary' ),
						'hint'     => array(
							//'title'     => '',
							'content' => 'This is a <b>hint</b> tool-tip for the webFonts field.<br/><br/>Add any HTML based text you like here.',
						)
					),
                        array(
                            'id'       => 'section-media-checkbox',
                            'type'     => 'switch',
                            'title'    => __( 'Section Show', 'wpmovielibrary' ),
                            'subtitle' => __( 'With the "section" field you can create indent option sections.', 'wpmovielibrary' ),

                        ),
                        array(
                            'id'       => 'section-media-start',
                            'type'     => 'section',
                            'title'    => __( 'Media Options', 'wpmovielibrary' ),
                            'subtitle' => __( 'With the "section" field you can create indent option sections.', 'wpmovielibrary' ),
                            'indent'   => true, // Indent all options below until the next 'section' option is set.
                            'required' => array( 'section-media-checkbox', "=", 1 ),
                        ),
                        array(
                            'id'       => 'opt-media',
                            'type'     => 'media',
                            'url'      => true,
                            'title'    => __( 'Media w/ URL', 'wpmovielibrary' ),
                            'compiler' => 'true',
                            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                            'desc'     => __( 'Basic media uploader with disabled URL input field.', 'wpmovielibrary' ),
                            'subtitle' => __( 'Upload any media using the WordPress native uploader', 'wpmovielibrary' ),
                            'default'  => array( 'url' => 'http://s.wordpress.org/style/images/codeispoetry.png' ),
                            //'hint'      => array(
                            //    'title'     => 'Hint Title',
                            //    'content'   => 'This is a <b>hint</b> for the media field with a Title.',
                            //)
                        ),
                        array(
                            'id'       => 'section-media-end',
                            'type'     => 'section',
                            'indent'   => false, // Indent all options below until the next 'section' option is set.
                            'required' => array( 'section-media-checkbox', "=", 1 ),
                        ),
                        array(
                            'id'       => 'media-no-url',
                            'type'     => 'media',
                            'title'    => __( 'Media w/o URL', 'wpmovielibrary' ),
                            'desc'     => __( 'This represents the minimalistic view. It does not have the preview box or the display URL in an input box. ', 'wpmovielibrary' ),
                            'subtitle' => __( 'Upload any media using the WordPress native uploader', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'media-no-preview',
                            'type'     => 'media',
                            'preview'  => false,
                            'title'    => __( 'Media No Preview', 'wpmovielibrary' ),
                            'desc'     => __( 'This represents the minimalistic view. It does not have the preview box or the display URL in an input box. ', 'wpmovielibrary' ),
                            'subtitle' => __( 'Upload any media using the WordPress native uploader', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-gallery',
                            'type'     => 'gallery',
                            'title'    => __( 'Add/Edit Gallery', 'wpmovielibrary' ),
                            'subtitle' => __( 'Create a new Gallery by selecting existing or uploading new images using the WordPress native uploader', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'            => 'opt-slider-label',
                            'type'          => 'slider',
                            'title'         => __( 'Slider Example 1', 'wpmovielibrary' ),
                            'subtitle'      => __( 'This slider displays the value as a label.', 'wpmovielibrary' ),
                            'desc'          => __( 'Slider description. Min: 1, max: 500, step: 1, default value: 250', 'wpmovielibrary' ),
                            'default'       => 250,
                            'min'           => 1,
                            'step'          => 1,
                            'max'           => 500,
                            'display_value' => 'label'
                        ),
                        array(
                            'id'            => 'opt-slider-text',
                            'type'          => 'slider',
                            'title'         => __( 'Slider Example 2 with Steps (5)', 'wpmovielibrary' ),
                            'subtitle'      => __( 'This example displays the value in a text box', 'wpmovielibrary' ),
                            'desc'          => __( 'Slider description. Min: 0, max: 300, step: 5, default value: 75', 'wpmovielibrary' ),
                            'default'       => 75,
                            'min'           => 0,
                            'step'          => 5,
                            'max'           => 300,
                            'display_value' => 'text'
                        ),
                        array(
                            'id'            => 'opt-slider-select',
                            'type'          => 'slider',
                            'title'         => __( 'Slider Example 3 with two sliders', 'wpmovielibrary' ),
                            'subtitle'      => __( 'This example displays the values in select boxes', 'wpmovielibrary' ),
                            'desc'          => __( 'Slider description. Min: 0, max: 500, step: 5, slider 1 default value: 100, slider 2 default value: 300', 'wpmovielibrary' ),
                            'default'       => array(
                                1 => 100,
                                2 => 300,
                            ),
                            'min'           => 0,
                            'step'          => 5,
                            'max'           => '500',
                            'display_value' => 'select',
                            'handles'       => 2,
                        ),
                        array(
                            'id'            => 'opt-slider-float',
                            'type'          => 'slider',
                            'title'         => __( 'Slider Example 4 with float values', 'wpmovielibrary' ),
                            'subtitle'      => __( 'This example displays float values', 'wpmovielibrary' ),
                            'desc'          => __( 'Slider description. Min: 0, max: 1, step: .1, default value: .5', 'wpmovielibrary' ),
                            'default'       => .5,
                            'min'           => 0,
                            'step'          => .1,
                            'max'           => 1,
                            'resolution'    => 0.1,
                            'display_value' => 'text'
                        ),
                        array(
                            'id'      => 'opt-spinner',
                            'type'    => 'spinner',
                            'title'   => __( 'JQuery UI Spinner Example 1', 'wpmovielibrary' ),
                            'desc'    => __( 'JQuery UI spinner description. Min:20, max: 100, step:20, default value: 40', 'wpmovielibrary' ),
                            'default' => '40',
                            'min'     => '20',
                            'step'    => '20',
                            'max'     => '100',
                        ),
                        array(
                            'id'       => 'switch-on',
                            'type'     => 'switch',
                            'title'    => __( 'Switch On', 'wpmovielibrary' ),
                            'subtitle' => __( 'Look, it\'s on!', 'wpmovielibrary' ),
                            'default'  => true,
                        ),
                        array(
                            'id'       => 'switch-off',
                            'type'     => 'switch',
                            'title'    => __( 'Switch Off', 'wpmovielibrary' ),
                            'subtitle' => __( 'Look, it\'s on!', 'wpmovielibrary' ),
                            //'options' => array('on', 'off'),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'switch-parent',
                            'type'     => 'switch',
                            'title'    => __( 'Switch - Nested Children, Enable to show', 'wpmovielibrary' ),
                            'subtitle' => __( 'Look, it\'s on! Also hidden child elements!', 'wpmovielibrary' ),
                            'default'  => 0,
                            'on'       => 'Enabled',
                            'off'      => 'Disabled',
                        ),
                        array(
                            'id'       => 'switch-child1',
                            'type'     => 'switch',
                            'required' => array( 'switch-parent', '=', '1' ),
                            'title'    => __( 'Switch - This and the next switch required for patterns to show', 'wpmovielibrary' ),
                            'subtitle' => __( 'Also called a "fold" parent.', 'wpmovielibrary' ),
                            'desc'     => __( 'Items set with a fold to this ID will hide unless this is set to the appropriate value.', 'wpmovielibrary' ),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'switch-child2',
                            'type'     => 'switch',
                            'required' => array( 'switch-parent', '=', '1' ),
                            'title'    => __( 'Switch2 - Enable the above switch and this one for patterns to show', 'wpmovielibrary' ),
                            'subtitle' => __( 'Also called a "fold" parent.', 'wpmovielibrary' ),
                            'desc'     => __( 'Items set with a fold to this ID will hide unless this is set to the appropriate value.', 'wpmovielibrary' ),
                            'default'  => false,
                        ),
                        array(
                            'id'       => 'opt-patterns',
                            'type'     => 'image_select',
                            'tiles'    => true,
                            'required' => array(
                                array( 'switch-child1', 'equals', 1 ),
                                array( 'switch-child2', 'equals', 1 ),
                            ),
                            'title'    => __( 'Images Option (with pattern=>true)', 'wpmovielibrary' ),
                            'subtitle' => __( 'Select a background pattern.', 'wpmovielibrary' ),
                            'default'  => 0,
                            'options'  => $sample_patterns
                            ,
                        ),
                        array(
                            'id'       => 'opt-homepage-layout',
                            'type'     => 'sorter',
                            'title'    => 'Layout Manager Advanced',
                            'subtitle' => 'You can add multiple drop areas or columns.',
                            'compiler' => 'true',
                            'options'  => array(
                                'enabled'  => array(
                                    'highlights' => 'Highlights',
                                    'slider'     => 'Slider',
                                    'staticpage' => 'Static Page',
                                    'services'   => 'Services'
                                ),
                                'disabled' => array(),
                                'backup'   => array(),
                            ),
                            'limits'   => array(
                                'disabled' => 1,
                                'backup'   => 2,
                            ),
                        ),
                        array(
                            'id'       => 'opt-homepage-layout-2',
                            'type'     => 'sorter',
                            'title'    => 'Homepage Layout Manager',
                            'desc'     => 'Organize how you want the layout to appear on the homepage',
                            'compiler' => 'true',
                            'options'  => array(
                                'disabled' => array(
                                    'highlights' => 'Highlights',
                                    'slider'     => 'Slider',
                                ),
                                'enabled'  => array(
                                    'staticpage' => 'Static Page',
                                    'services'   => 'Services'
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'opt-slides',
                            'type'        => 'slides',
                            'title'       => __( 'Slides Options', 'wpmovielibrary' ),
                            'subtitle'    => __( 'Unlimited slides with drag and drop sortings.', 'wpmovielibrary' ),
                            'desc'        => __( 'This field will store all slides values into a multidimensional array to use into a foreach loop.', 'wpmovielibrary' ),
                            'placeholder' => array(
                                'title'       => __( 'This is a title', 'wpmovielibrary' ),
                                'description' => __( 'Description Here', 'wpmovielibrary' ),
                                'url'         => __( 'Give us a link!', 'wpmovielibrary' ),
                            ),
                        ),
                        array(
                            'id'       => 'opt-presets',
                            'type'     => 'image_select',
                            'presets'  => true,
                            'title'    => __( 'Preset', 'wpmovielibrary' ),
                            'subtitle' => __( 'This allows you to set a json string or array to override multiple preferences in your theme.', 'wpmovielibrary' ),
                            'default'  => 0,
                            'desc'     => __( 'This allows you to set a json string or array to override multiple preferences in your theme.', 'wpmovielibrary' ),
                            'options'  => array(
                                '1' => array(
                                    'alt'     => 'Preset 1',
                                    'img'     => ReduxFramework::$_url . '../sample/presets/preset1.png',
                                    'presets' => array(
                                        'switch-on'     => 1,
                                        'switch-off'    => 1,
                                        'switch-parent' => 1
                                    )
                                ),
                                '2' => array(
                                    'alt'     => 'Preset 2',
                                    'img'     => ReduxFramework::$_url . '../sample/presets/preset2.png',
                                    'presets' => '{"opt-slider-label":"1", "opt-slider-text":"10"}'
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'opt-typography',
                            'type'        => 'typography',
                            'title'       => __( 'Typography', 'wpmovielibrary' ),
                            //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                            'google'      => true,
                            // Disable google fonts. Won't work if you haven't defined your google api key
                            'font-backup' => true,
                            // Select a backup non-google font in addition to a google font
                            //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                            //'subsets'       => false, // Only appears if google is true and subsets not set to false
                            //'font-size'     => false,
                            //'line-height'   => false,
                            //'word-spacing'  => true,  // Defaults to false
                            //'letter-spacing'=> true,  // Defaults to false
                            //'color'         => false,
                            //'preview'       => false, // Disable the previewer
                            'all_styles'  => true,
                            // Enable all Google Font style/weight variations to be added to the page
                            'output'      => array( 'h2.site-description, .entry-title' ),
                            // An array of CSS selectors to apply this font style to dynamically
                            'compiler'    => array( 'h2.site-description-compiler' ),
                            // An array of CSS selectors to apply this font style to dynamically
                            'units'       => 'px',
                            // Defaults to px
                            'subtitle'    => __( 'Typography option with each property can be called individually.', 'wpmovielibrary' ),
                            'default'     => array(
                                'color'       => '#333',
                                'font-style'  => '700',
                                'font-family' => 'Abel',
                                'google'      => true,
                                'font-size'   => '33px',
                                'line-height' => '40px'
                            ),
                        ),
                    ),
                );

                $this->sections[] = array(
                    'type' => 'divide',
                );

                $this->sections[] = array(
                    'icon'   => 'el-icon-cogs',
                    'title'  => __( 'General Settings', 'wpmovielibrary' ),
                    'fields' => array(
                        array(
                            'id'       => 'opt-layout',
                            'type'     => 'image_select',
                            'compiler' => true,
                            'title'    => __( 'Main Layout', 'wpmovielibrary' ),
                            'subtitle' => __( 'Select main content and sidebar alignment. Choose between 1, 2 or 3 column layout.', 'wpmovielibrary' ),
                            'options'  => array(
                                '1' => array(
                                    'alt' => '1 Column',
                                    'img' => ReduxFramework::$_url . 'assets/img/1col.png'
                                ),
                                '2' => array(
                                    'alt' => '2 Column Left',
                                    'img' => ReduxFramework::$_url . 'assets/img/2cl.png'
                                ),
                                '3' => array(
                                    'alt' => '2 Column Right',
                                    'img' => ReduxFramework::$_url . 'assets/img/2cr.png'
                                ),
                                '4' => array(
                                    'alt' => '3 Column Middle',
                                    'img' => ReduxFramework::$_url . 'assets/img/3cm.png'
                                ),
                                '5' => array(
                                    'alt' => '3 Column Left',
                                    'img' => ReduxFramework::$_url . 'assets/img/3cl.png'
                                ),
                                '6' => array(
                                    'alt' => '3 Column Right',
                                    'img' => ReduxFramework::$_url . 'assets/img/3cr.png'
                                )
                            ),
                            'default'  => '2'
                        ),
                        array(
                            'id'       => 'opt-textarea',
                            'type'     => 'textarea',
                            'required' => array( 'layout', 'equals', '1' ),
                            'title'    => __( 'Tracking Code', 'wpmovielibrary' ),
                            'subtitle' => __( 'Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.', 'wpmovielibrary' ),
                            'validate' => 'js',
                            'desc'     => 'Validate that it\'s javascript!',
                        ),
                        array(
                            'id'       => 'opt-ace-editor-css',
                            'type'     => 'ace_editor',
                            'title'    => __( 'CSS Code', 'wpmovielibrary' ),
                            'subtitle' => __( 'Paste your CSS code here.', 'wpmovielibrary' ),
                            'mode'     => 'css',
                            'theme'    => 'monokai',
                            'desc'     => 'Possible modes can be found at <a href="http://ace.c9.io" target="_blank">http://ace.c9.io/</a>.',
                            'default'  => "#header{\nmargin: 0 auto;\n}"
                        ),
                        /*
                    array(
                        'id'        => 'opt-ace-editor-js',
                        'type'      => 'ace_editor',
                        'title'     => __('JS Code', 'wpmovielibrary'),
                        'subtitle'  => __('Paste your JS code here.', 'wpmovielibrary'),
                        'mode'      => 'javascript',
                        'theme'     => 'chrome',
                        'desc'      => 'Possible modes can be found at <a href="http://ace.c9.io" target="_blank">http://ace.c9.io/</a>.',
                        'default'   => "jQuery(document).ready(function(){\n\n});"
                    ),
                    array(
                        'id'        => 'opt-ace-editor-php',
                        'type'      => 'ace_editor',
                        'title'     => __('PHP Code', 'wpmovielibrary'),
                        'subtitle'  => __('Paste your PHP code here.', 'wpmovielibrary'),
                        'mode'      => 'php',
                        'theme'     => 'chrome',
                        'desc'      => 'Possible modes can be found at <a href="http://ace.c9.io" target="_blank">http://ace.c9.io/</a>.',
                        'default'   => '<?php\nisset ( $redux ) ? true : false;\n?>'
                    ),
                    */
                        array(
                            'id'       => 'opt-editor',
                            'type'     => 'editor',
                            'title'    => __( 'Footer Text', 'wpmovielibrary' ),
                            'subtitle' => __( 'You can use the following shortcodes in your footer text: [wp-url] [site-url] [theme-url] [login-url] [logout-url] [site-title] [site-tagline] [current-year]', 'wpmovielibrary' ),
                            'default'  => 'Powered by Redux Framework.',
                        ),
                        array(
                            'id'       => 'password',
                            'type'     => 'password',
                            'username' => true,
                            'title'    => 'SMTP Account',
                            //'placeholder' => array('username' => 'Enter your Username')
                        )
                    )
                );

                $this->sections[] = array(
                    'icon'       => 'el-icon-website',
                    'title'      => __( 'Styling Options', 'wpmovielibrary' ),
                    'subsection' => true,
                    'fields'     => array(
                        array(
                            'id'       => 'opt-select-stylesheet',
                            'type'     => 'select',
                            'title'    => __( 'Theme Stylesheet', 'wpmovielibrary' ),
                            'subtitle' => __( 'Select your themes alternative color scheme.', 'wpmovielibrary' ),
                            'options'  => array( 'default.css' => 'default.css', 'color1.css' => 'color1.css' ),
                            'default'  => 'default.css',
                        ),
                        array(
                            'id'       => 'opt-color-background',
                            'type'     => 'color',
                            'output'   => array( '.site-title' ),
                            'title'    => __( 'Body Background Color', 'wpmovielibrary' ),
                            'subtitle' => __( 'Pick a background color for the theme (default: #fff).', 'wpmovielibrary' ),
                            'default'  => '#FFFFFF',
                            'validate' => 'color',
                        ),
                        array(
                            'id'       => 'opt-background',
                            'type'     => 'background',
                            'output'   => array( 'body' ),
                            'title'    => __( 'Body Background', 'wpmovielibrary' ),
                            'subtitle' => __( 'Body background with image, color, etc.', 'wpmovielibrary' ),
                            //'default'   => '#FFFFFF',
                        ),
                        array(
                            'id'       => 'opt-color-footer',
                            'type'     => 'color',
                            'title'    => __( 'Footer Background Color', 'wpmovielibrary' ),
                            'subtitle' => __( 'Pick a background color for the footer (default: #dd9933).', 'wpmovielibrary' ),
                            'default'  => '#dd9933',
                            'validate' => 'color',
                        ),
                        array(
                            'id'       => 'opt-color-rgba',
                            'type'     => 'color_rgba',
                            'title'    => __( 'Color RGBA - BETA', 'wpmovielibrary' ),
                            'subtitle' => __( 'Gives you the RGBA color. Still quite experimental. Use at your own risk.', 'wpmovielibrary' ),
                            'default'  => array( 'color' => '#dd9933', 'alpha' => '1.0' ),
                            'output'   => array( 'body' ),
                            'mode'     => 'background',
                            'validate' => 'colorrgba',
                        ),
                        array(
                            'id'       => 'opt-color-header',
                            'type'     => 'color_gradient',
                            'title'    => __( 'Header Gradient Color Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'Only color validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'default'  => array(
                                'from' => '#1e73be',
                                'to'   => '#00897e'
                            )
                        ),
                        array(
                            'id'       => 'opt-link-color',
                            'type'     => 'link_color',
                            'title'    => __( 'Links Color Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'Only color validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            //'regular'   => false, // Disable Regular Color
                            //'hover'     => false, // Disable Hover Color
                            //'active'    => false, // Disable Active Color
                            //'visited'   => true,  // Enable Visited Color
                            'default'  => array(
                                'regular' => '#aaa',
                                'hover'   => '#bbb',
                                'active'  => '#ccc',
                            )
                        ),
                        array(
                            'id'       => 'opt-header-border',
                            'type'     => 'border',
                            'title'    => __( 'Header Border Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'Only color validation can be done on this field type', 'wpmovielibrary' ),
                            'output'   => array( '.site-header' ),
                            // An array of CSS selectors to apply this font style to
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'default'  => array(
                                'border-color'  => '#1e73be',
                                'border-style'  => 'solid',
                                'border-top'    => '3px',
                                'border-right'  => '3px',
                                'border-bottom' => '3px',
                                'border-left'   => '3px'
                            )
                        ),
                        array(
                            'id'       => 'opt-spacing',
                            'type'     => 'spacing',
                            'output'   => array( '.site-header' ),
                            // An array of CSS selectors to apply this font style to
                            'mode'     => 'margin',
                            // absolute, padding, margin, defaults to padding
                            'all'      => true,
                            // Have one field that applies to all
                            //'top'           => false,     // Disable the top
                            //'right'         => false,     // Disable the right
                            //'bottom'        => false,     // Disable the bottom
                            //'left'          => false,     // Disable the left
                            //'units'         => 'em',      // You can specify a unit value. Possible: px, em, %
                            //'units_extended'=> 'true',    // Allow users to select any type of unit
                            //'display_units' => 'false',   // Set to false to hide the units if the units are specified
                            'title'    => __( 'Padding/Margin Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'Allow your users to choose the spacing or margin they want.', 'wpmovielibrary' ),
                            'desc'     => __( 'You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'wpmovielibrary' ),
                            'default'  => array(
                                'margin-top'    => '1px',
                                'margin-right'  => '2px',
                                'margin-bottom' => '3px',
                                'margin-left'   => '4px'
                            )
                        ),
                        array(
                            'id'             => 'opt-dimensions',
                            'type'           => 'dimensions',
                            'units'          => 'em',    // You can specify a unit value. Possible: px, em, %
                            'units_extended' => 'true',  // Allow users to select any type of unit
                            'title'          => __( 'Dimensions (Width/Height) Option', 'wpmovielibrary' ),
                            'subtitle'       => __( 'Allow your users to choose width, height, and/or unit.', 'wpmovielibrary' ),
                            'desc'           => __( 'You can enable or disable any piece of this field. Width, Height, or Units.', 'wpmovielibrary' ),
                            'default'        => array(
                                'width'  => 200,
                                'height' => 100,
                            )
                        ),
                        array(
                            'id'       => 'opt-typography-body',
                            'type'     => 'typography',
                            'title'    => __( 'Body Font', 'wpmovielibrary' ),
                            'subtitle' => __( 'Specify the body font properties.', 'wpmovielibrary' ),
                            'google'   => true,
                            'default'  => array(
                                'color'       => '#dd9933',
                                'font-size'   => '30px',
                                'font-family' => 'Arial,Helvetica,sans-serif',
                                'font-weight' => 'Normal',
                            ),
                        ),
                        array(
                            'id'       => 'opt-custom-css',
                            'type'     => 'textarea',
                            'title'    => __( 'Custom CSS', 'wpmovielibrary' ),
                            'subtitle' => __( 'Quickly add some CSS to your theme by adding it to this block.', 'wpmovielibrary' ),
                            'desc'     => __( 'This field is even CSS validated!', 'wpmovielibrary' ),
                            'validate' => 'css',
                        ),
                        array(
                            'id'       => 'opt-custom-html',
                            'type'     => 'textarea',
                            'title'    => __( 'Custom HTML', 'wpmovielibrary' ),
                            'subtitle' => __( 'Just like a text box widget.', 'wpmovielibrary' ),
                            'desc'     => __( 'This field is even HTML validated!', 'wpmovielibrary' ),
                            'validate' => 'html',
                        ),
                    )
                );

                /**
                 *  Note here I used a 'heading' in the sections array construct
                 *  This allows you to use a different title on your options page
                 * instead of reusing the 'title' value.  This can be done on any
                 * section - kp
                 */
                $this->sections[] = array(
                    'icon'    => 'el-icon-bullhorn',
                    'title'   => __( 'Field Validation', 'wpmovielibrary' ),
                    'heading' => __( 'Validate ALL fields within Redux.', 'wpmovielibrary' ),
                    'desc'    => __( '<p class="description">This is the Description. Again HTML is allowed2</p>', 'wpmovielibrary' ),
                    'fields'  => array(
                        array(
                            'id'       => 'opt-text-email',
                            'type'     => 'text',
                            'title'    => __( 'Text Option - Email Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'This is a little space under the Field Title in the Options table, additional info is good in here.', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate' => 'email',
                            'msg'      => 'custom error message',
                            'default'  => 'test@test.com',
                            //                        'text_hint' => array(
                            //                            'title'     => 'Valid Email Required!',
                            //                            'content'   => 'This field required a valid email address.'
                            //                        )
                        ),
                        array(
                            'id'       => 'opt-text-post-type',
                            'type'     => 'text',
                            'title'    => __( 'Text Option with Data Attributes', 'wpmovielibrary' ),
                            'subtitle' => __( 'You can also pass an options array if you want. Set the default to whatever you like.', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'data'     => 'post_type',
                        ),
                        array(
                            'id'       => 'opt-multi-text',
                            'type'     => 'multi_text',
                            'title'    => __( 'Multi Text Option - Color Validated', 'wpmovielibrary' ),
                            'validate' => 'color',
                            'subtitle' => __( 'If you enter an invalid color it will be removed. Try using the text "blue" as a color.  ;)', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'       => 'opt-text-url',
                            'type'     => 'text',
                            'title'    => __( 'Text Option - URL Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'This must be a URL.', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate' => 'url',
                            'default'  => 'http://reduxframework.com',
                            //                        'text_hint' => array(
                            //                            'title'     => '',
                            //                            'content'   => 'Please enter a valid <strong>URL</strong> in this field.'
                            //                        )
                        ),
                        array(
                            'id'       => 'opt-text-numeric',
                            'type'     => 'text',
                            'title'    => __( 'Text Option - Numeric Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'This must be numeric.', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate' => 'numeric',
                            'default'  => '0',
                        ),
                        array(
                            'id'       => 'opt-text-comma-numeric',
                            'type'     => 'text',
                            'title'    => __( 'Text Option - Comma Numeric Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'This must be a comma separated string of numerical values.', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate' => 'comma_numeric',
                            'default'  => '0',
                        ),
                        array(
                            'id'       => 'opt-text-no-special-chars',
                            'type'     => 'text',
                            'title'    => __( 'Text Option - No Special Chars Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'This must be a alpha numeric only.', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate' => 'no_special_chars',
                            'default'  => '0'
                        ),
                        array(
                            'id'       => 'opt-text-str_replace',
                            'type'     => 'text',
                            'title'    => __( 'Text Option - Str Replace Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'You decide.', 'wpmovielibrary' ),
                            'desc'     => __( 'This field\'s default value was changed by a filter hook!', 'wpmovielibrary' ),
                            'validate' => 'str_replace',
                            'str'      => array(
                                'search'      => ' ',
                                'replacement' => 'thisisaspace'
                            ),
                            'default'  => 'This is the default.'
                        ),
                        array(
                            'id'       => 'opt-text-preg_replace',
                            'type'     => 'text',
                            'title'    => __( 'Text Option - Preg Replace Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'You decide.', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate' => 'preg_replace',
                            'preg'     => array(
                                'pattern'     => '/[^a-zA-Z_ -]/s',
                                'replacement' => 'no numbers'
                            ),
                            'default'  => '0'
                        ),
                        array(
                            'id'                => 'opt-text-custom_validate',
                            'type'              => 'text',
                            'title'             => __( 'Text Option - Custom Callback Validated', 'wpmovielibrary' ),
                            'subtitle'          => __( 'You decide.', 'wpmovielibrary' ),
                            'desc'              => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate_callback' => 'redux_validate_callback_function',
                            'default'           => '0'
                        ),
                        array(
                            'id'                => 'opt-text-custom_validate-class',
                            'type'              => 'text',
                            'title'             => __( 'Text Option - Custom Callback Validated - Class', 'wpmovielibrary' ),
                            'subtitle'          => __( 'You decide.', 'wpmovielibrary' ),
                            'desc'              => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate_callback' => array( $this, 'validate_callback_function' ),
                            // You can pass the current class
                            // Or pass the class name and method
                            //'validate_callback' => array(
                            //    'WPML_Redux_Framework_config',
                            //    'validate_callback_function'
                            //),
                            'default'           => '0'
                        ),
                        array(
                            'id'       => 'opt-textarea-no-html',
                            'type'     => 'textarea',
                            'title'    => __( 'Textarea Option - No HTML Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'All HTML will be stripped', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate' => 'no_html',
                            'default'  => 'No HTML is allowed in here.'
                        ),
                        array(
                            'id'       => 'opt-textarea-html',
                            'type'     => 'textarea',
                            'title'    => __( 'Textarea Option - HTML Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'HTML Allowed (wp_kses)', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
                            'default'  => 'HTML is allowed in here.'
                        ),
                        array(
                            'id'           => 'opt-textarea-some-html',
                            'type'         => 'textarea',
                            'title'        => __( 'Textarea Option - HTML Validated Custom', 'wpmovielibrary' ),
                            'subtitle'     => __( 'Custom HTML Allowed (wp_kses)', 'wpmovielibrary' ),
                            'desc'         => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate'     => 'html_custom',
                            'default'      => '<p>Some HTML is allowed in here.</p>',
                            'allowed_html' => array( '' ) //see http://codex.wordpress.org/Function_Reference/wp_kses
                        ),
                        array(
                            'id'       => 'opt-textarea-js',
                            'type'     => 'textarea',
                            'title'    => __( 'Textarea Option - JS Validated', 'wpmovielibrary' ),
                            'subtitle' => __( 'JS will be escaped', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'validate' => 'js'
                        ),
                    )
                );

                $this->sections[] = array(
                    'icon'   => 'el-icon-check',
                    'title'  => __( 'Radio/Checkbox Fields', 'wpmovielibrary' ),
                    'desc'   => __( '<p class="description">This is the Description. Again HTML is allowed</p>', 'wpmovielibrary' ),
                    'fields' => array(
                        array(
                            'id'       => 'opt-checkbox',
                            'type'     => 'checkbox',
                            'title'    => __( 'Checkbox Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'default'  => '1'// 1 = on | 0 = off
                        ),
                        array(
                            'id'       => 'opt-multi-check',
                            'type'     => 'checkbox',
                            'title'    => __( 'Multi Checkbox Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            //Must provide key => value pairs for multi checkbox options
                            'options'  => array(
                                '1' => 'Opt 1',
                                '2' => 'Opt 2',
                                '3' => 'Opt 3'
                            ),
                            //See how std has changed? you also don't need to specify opts that are 0.
                            'default'  => array(
                                '1' => '1',
                                '2' => '0',
                                '3' => '0'
                            )
                        ),
                        array(
                            'id'       => 'opt-checkbox-data',
                            'type'     => 'checkbox',
                            'title'    => __( 'Multi Checkbox Option (with menu data)', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'data'     => 'menu'
                        ),
                        array(
                            'id'       => 'opt-checkbox-sidebar',
                            'type'     => 'checkbox',
                            'title'    => __( 'Multi Checkbox Option (with sidebar data)', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'data'     => 'sidebars'
                        ),
                        array(
                            'id'       => 'opt-radio',
                            'type'     => 'radio',
                            'title'    => __( 'Radio Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            //Must provide key => value pairs for radio options
                            'options'  => array(
                                '1' => 'Opt 1',
                                '2' => 'Opt 2',
                                '3' => 'Opt 3'
                            ),
                            'default'  => '2'
                        ),
                        array(
                            'id'       => 'opt-radio-data',
                            'type'     => 'radio',
                            'title'    => __( 'Multi Checkbox Option (with menu data)', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'data'     => 'menu'
                        ),
                        array(
                            'id'       => 'opt-image-select',
                            'type'     => 'image_select',
                            'title'    => __( 'Images Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            //Must provide key => value(array:title|img) pairs for radio options
                            'options'  => array(
                                '1' => array( 'title' => 'Opt 1', 'img' => 'images/align-none.png' ),
                                '2' => array( 'title' => 'Opt 2', 'img' => 'images/align-left.png' ),
                                '3' => array( 'title' => 'Opt 3', 'img' => 'images/align-center.png' ),
                                '4' => array( 'title' => 'Opt 4', 'img' => 'images/align-right.png' )
                            ),
                            'default'  => '2'
                        ),
                        array(
                            'id'       => 'opt-image-select-layout',
                            'type'     => 'image_select',
                            'title'    => __( 'Images Option for Layout', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This uses some of the built in images, you can use them for layout options.', 'wpmovielibrary' ),
                            //Must provide key => value(array:title|img) pairs for radio options
                            'options'  => array(
                                '1' => array(
                                    'alt' => '1 Column',
                                    'img' => ReduxFramework::$_url . 'assets/img/1col.png'
                                ),
                                '2' => array(
                                    'alt' => '2 Column Left',
                                    'img' => ReduxFramework::$_url . 'assets/img/2cl.png'
                                ),
                                '3' => array(
                                    'alt' => '2 Column Right',
                                    'img' => ReduxFramework::$_url . 'assets/img/2cr.png'
                                ),
                                '4' => array(
                                    'alt' => '3 Column Middle',
                                    'img' => ReduxFramework::$_url . 'assets/img/3cm.png'
                                ),
                                '5' => array(
                                    'alt' => '3 Column Left',
                                    'img' => ReduxFramework::$_url . 'assets/img/3cl.png'
                                ),
                                '6' => array(
                                    'alt' => '3 Column Right',
                                    'img' => ReduxFramework::$_url . 'assets/img/3cr.png'
                                )
                            ),
                            'default'  => '2'
                        ),
                        array(
                            'id'       => 'opt-sortable',
                            'type'     => 'sortable',
                            'title'    => __( 'Sortable Text Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'Define and reorder these however you want.', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'options'  => array(
                                'si1' => 'Item 1',
                                'si2' => 'Item 2',
                                'si3' => 'Item 3',
                            )
                        ),
                        array(
                            'id'       => 'opt-check-sortable',
                            'type'     => 'sortable',
                            'mode'     => 'checkbox', // checkbox or text
                            'title'    => __( 'Sortable Text Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'Define and reorder these however you want.', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'options'  => array(
                                'si1' => false,
                                'si2' => true,
                                'si3' => false,
                            )
                        ),
                    )
                );

                $this->sections[] = array(
                    'icon'   => 'el-icon-list-alt',
                    'title'  => __( 'Select Fields', 'wpmovielibrary' ),
                    'desc'   => __( '<p class="description">This is the Description. Again HTML is allowed</p>', 'wpmovielibrary' ),
                    'fields' => array(
                        array(
                            'id'       => 'opt-select',
                            'type'     => 'select',
                            'title'    => __( 'Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            //Must provide key => value pairs for select options
                            'options'  => array(
                                '1' => 'Opt 1',
                                '2' => 'Opt 2',
                                '3' => 'Opt 3'
                            ),
                            'default'  => '2'
                        ),
                        array(
                            'id'       => 'opt-multi-select',
                            'type'     => 'select',
                            'multi'    => true,
                            'title'    => __( 'Multi Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            //Must provide key => value pairs for radio options
                            'options'  => array(
                                '1' => 'Opt 1',
                                '2' => 'Opt 2',
                                '3' => 'Opt 3'
                            ),
                            'required' => array( 'select', 'equals', array( '1', '3' ) ),
                            'default'  => array( '2', '3' )
                        ),
                        array(
                            'id'       => 'opt-select-image',
                            'type'     => 'select_image',
                            'title'    => __( 'Select Image', 'wpmovielibrary' ),
                            'subtitle' => __( 'A preview of the selected image will appear underneath the select box.', 'wpmovielibrary' ),
                            'options'  => $sample_patterns,
                            // Alternatively
                            //'options'   => Array(
                            //                'img_name' => 'img_path'
                            //             )
                            'default'  => 'tree_bark.png',
                        ),
                        array(
                            'id'   => 'opt-info',
                            'type' => 'info',
                            'desc' => __( 'You can easily add a variety of data from WordPress.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-categories',
                            'type'     => 'select',
                            'data'     => 'categories',
                            'title'    => __( 'Categories Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-categories-multi',
                            'type'     => 'select',
                            'data'     => 'categories',
                            'multi'    => true,
                            'title'    => __( 'Categories Multi Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-pages',
                            'type'     => 'select',
                            'data'     => 'pages',
                            'title'    => __( 'Pages Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-multi-select-pages',
                            'type'     => 'select',
                            'data'     => 'pages',
                            'multi'    => true,
                            'title'    => __( 'Pages Multi Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-tags',
                            'type'     => 'select',
                            'data'     => 'tags',
                            'title'    => __( 'Tags Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-multi-select-tags',
                            'type'     => 'select',
                            'data'     => 'tags',
                            'multi'    => true,
                            'title'    => __( 'Tags Multi Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-menus',
                            'type'     => 'select',
                            'data'     => 'menus',
                            'title'    => __( 'Menus Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-multi-select-menus',
                            'type'     => 'select',
                            'data'     => 'menu',
                            'multi'    => true,
                            'title'    => __( 'Menus Multi Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-post-type',
                            'type'     => 'select',
                            'data'     => 'post_type',
                            'title'    => __( 'Post Type Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-multi-select-post-type',
                            'type'     => 'select',
                            'data'     => 'post_type',
                            'multi'    => true,
                            'title'    => __( 'Post Type Multi Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-multi-select-sortable',
                            'type'     => 'select',
                            'data'     => 'post_type',
                            'multi'    => true,
                            'sortable' => true,
                            'title'    => __( 'Post Type Multi Select Option + Sortable', 'wpmovielibrary' ),
                            'subtitle' => __( 'This field also has sortable enabled!', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-posts',
                            'type'     => 'select',
                            'data'     => 'post',
                            'title'    => __( 'Posts Select Option2', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-multi-select-posts',
                            'type'     => 'select',
                            'data'     => 'post',
                            'multi'    => true,
                            'title'    => __( 'Posts Multi Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-roles',
                            'type'     => 'select',
                            'data'     => 'roles',
                            'title'    => __( 'User Role Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-capabilities',
                            'type'     => 'select',
                            'data'     => 'capabilities',
                            'multi'    => true,
                            'title'    => __( 'Capabilities Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                        ),
                        array(
                            'id'       => 'opt-select-elusive',
                            'type'     => 'select',
                            'data'     => 'elusive-icons',
                            'title'    => __( 'Elusive Icons Select Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'Here\'s a list of all the elusive icons by name and icon.', 'wpmovielibrary' ),
                        ),
                    )
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

			// You can append a new section at any time.
			$this->sections[] = array(
                    'icon'   => 'el-icon-eye-open',
                    'title'  => __( 'Additional Fields', 'wpmovielibrary' ),
                    'desc'   => __( '<p class="description">This is the Description. Again HTML is allowed</p>', 'wpmovielibrary' ),
                    'fields' => array(
                        array(
                            'id'       => 'opt-datepicker',
                            'type'     => 'date',
                            'title'    => __( 'Date Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'   => 'opt-divide',
                            'type' => 'divide'
                        ),
                        array(
                            'id'       => 'opt-button-set',
                            'type'     => 'button_set',
                            'title'    => __( 'Button Set Option', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            //Must provide key => value pairs for radio options
                            'options'  => array(
                                '1' => 'Opt 1',
                                '2' => 'Opt 2',
                                '3' => 'Opt 3'
                            ),
                            'default'  => '2'
                        ),
                        array(
                            'id'       => 'opt-button-set-multi',
                            'type'     => 'button_set',
                            'title'    => __( 'Button Set, Multi Select', 'wpmovielibrary' ),
                            'subtitle' => __( 'No validation can be done on this field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is the description field, again good for additional info.', 'wpmovielibrary' ),
                            'multi'    => true,
                            //Must provide key => value pairs for radio options
                            'options'  => array(
                                '1' => 'Opt 1',
                                '2' => 'Opt 2',
                                '3' => 'Opt 3'
                            ),
                            'default'  => array( '2', '3' )
                        ),
                        array(
                            'id'   => 'opt-info-field',
                            'type' => 'info',
                            'desc' => __( 'This is the info field, if you want to break sections up.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'    => 'opt-info-warning',
                            'type'  => 'info',
                            'style' => 'warning',
                            'title' => __( 'This is a title.', 'wpmovielibrary' ),
                            'desc'  => __( 'This is an info field with the warning style applied and a header.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'    => 'opt-info-success',
                            'type'  => 'info',
                            'style' => 'success',
                            'icon'  => 'el-icon-info-sign',
                            'title' => __( 'This is a title.', 'wpmovielibrary' ),
                            'desc'  => __( 'This is an info field with the success style applied, a header and an icon.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'    => 'opt-info-critical',
                            'type'  => 'info',
                            'style' => 'critical',
                            'icon'  => 'el-icon-info-sign',
                            'title' => __( 'This is a title.', 'wpmovielibrary' ),
                            'desc'  => __( 'This is an info field with the critical style applied, a header and an icon.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'       => 'opt-raw_info',
                            'type'     => 'info',
                            'required' => array( '18', 'equals', array( '1', '2' ) ),
                            'raw_html' => true,
                            'desc'     => $sampleHTML,
                        ),
                        array(
                            'id'     => 'opt-info-normal',
                            'type'   => 'info',
                            'notice' => true,
                            'title'  => __( 'This is a title.', 'wpmovielibrary' ),
                            'desc'   => __( 'This is an info notice field with the normal style applied, a header and an icon.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'     => 'opt-notice-info',
                            'type'   => 'info',
                            'notice' => true,
                            'style'  => 'info',
                            'title'  => __( 'This is a title.', 'wpmovielibrary' ),
                            'desc'   => __( 'This is an info notice field with the info style applied, a header and an icon.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'     => 'opt-notice-warning',
                            'type'   => 'info',
                            'notice' => true,
                            'style'  => 'warning',
                            'icon'   => 'el-icon-info-sign',
                            'title'  => __( 'This is a title.', 'wpmovielibrary' ),
                            'desc'   => __( 'This is an info notice field with the warning style applied, a header and an icon.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'     => 'opt-notice-success',
                            'type'   => 'info',
                            'notice' => true,
                            'style'  => 'success',
                            'icon'   => 'el-icon-info-sign',
                            'title'  => __( 'This is a title.', 'wpmovielibrary' ),
                            'desc'   => __( 'This is an info notice field with the success style applied, a header and an icon.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'     => 'opt-notice-critical',
                            'type'   => 'info',
                            'notice' => true,
                            'style'  => 'critical',
                            'icon'   => 'el-icon-info-sign',
                            'title'  => __( 'This is a title.', 'wpmovielibrary' ),
                            'desc'   => __( 'This is an notice field with the critical style applied, a header and an icon.', 'wpmovielibrary' )
                        ),
                        array(
                            'id'       => 'opt-custom-callback',
                            'type'     => 'callback',
                            'title'    => __( 'Custom Field Callback', 'wpmovielibrary' ),
                            'subtitle' => __( 'This is a completely unique field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is created with a callback function, so anything goes in this field. Make sure to define the function though.', 'wpmovielibrary' ),
                            'callback' => 'redux_my_custom_field'
                        ),
                        array(
                            'id'       => 'opt-custom-callback-class',
                            'type'     => 'callback',
                            'title'    => __( 'Custom Field Callback - Class', 'wpmovielibrary' ),
                            'subtitle' => __( 'This is a completely unique field type', 'wpmovielibrary' ),
                            'desc'     => __( 'This is created with a callback function, so anything goes in this field. Make sure to define the function though.', 'wpmovielibrary' ),
                            //'callback'  => array( $this, 'class_field_callback' ) // Can use the current class object
                            'callback' => array( 'WPML_Redux_Framework_config', 'class_field_callback' )
                            // Can use just class name
                        ),
                        array(
                            'id'              => 'opt-customizer-only-in-section',
                            'type'            => 'select',
                            'title'           => __( 'Customizer Only Option', 'wpmovielibrary' ),
                            'subtitle'        => __( 'The subtitle is NOT visible in customizer', 'wpmovielibrary' ),
                            'desc'            => __( 'The field desc is NOT visible in customizer.', 'wpmovielibrary' ),
                            'customizer_only' => true,
                            //Must provide key => value pairs for select options
                            'options'         => array(
                                '1' => 'Opt 1',
                                '2' => 'Opt 2',
                                '3' => 'Opt 3'
                            ),
                            'default'         => '2'
                        ),
                    )
                );

                $this->sections[] = array(
                    'icon'            => 'el-icon-list-alt',
                    'title'           => __( 'Customizer Only', 'wpmovielibrary' ),
                    'desc'            => __( '<p class="description">This Section should be visible only in Customizer</p>', 'wpmovielibrary' ),
                    'customizer_only' => true,
                    'fields'          => array(
                        array(
                            'id'              => 'opt-customizer-only',
                            'type'            => 'select',
                            'title'           => __( 'Customizer Only Option', 'wpmovielibrary' ),
                            'subtitle'        => __( 'The subtitle is NOT visible in customizer', 'wpmovielibrary' ),
                            'desc'            => __( 'The field desc is NOT visible in customizer.', 'wpmovielibrary' ),
                            'customizer_only' => true,
                            //Must provide key => value pairs for select options
                            'options'         => array(
                                '1' => 'Opt 1',
                                '2' => 'Opt 2',
                                '3' => 'Opt 3'
                            ),
                            'default'         => '2'
                        ),
                    )
                );

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
			/*$this->args['share_icons'][] = array(
				'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
				'title' => 'Visit us on GitHub',
				'icon'  => 'el-icon-github'
			    //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
			);
			$this->args['share_icons'][] = array(
				'url'   => 'https://www.facebook.com/pages/Redux-Framework/243141545850368',
				'title' => 'Like us on Facebook',
				'icon'  => 'el-icon-facebook'
			);
			$this->args['share_icons'][] = array(
				'url'   => 'http://twitter.com/reduxframework',
				'title' => 'Follow us on Twitter',
				'icon'  => 'el-icon-twitter'
			);
			$this->args['share_icons'][] = array(
				'url'   => 'http://www.linkedin.com/company/redux-framework',
				'title' => 'Find us on LinkedIn',
				'icon'  => 'el-icon-linkedin'
			);*/

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
