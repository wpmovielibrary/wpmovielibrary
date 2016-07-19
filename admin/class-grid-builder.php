<?php
/**
 * Define the Grid Builder class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Admin;

use wpmoly\Core\Loader;

/**
 * Provide a tool to create, build, and save grids.
 * 
 * Currently supports movies, actors and genres.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class GridBuilder {

	/**
	 * Grid Post Type metaboxes.
	 * 
	 * @var    array
	 */
	private $metaboxes = array();

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	public function __construct() {

		$metaboxes = array(
			'type' => array(
				'id'            => 'wpmoly-grid-type',
				'title'         => __( 'Type', 'wpmovielibrary' ),
				'callback'      => array( $this, 'type_metabox' ),
				'screen'        => 'grid',
				'context'       => 'side',
				'priority'      => 'high',
				'callback_args' => null
			),
			'configure' => array(
				'id'            => 'wpmoly-grid-configure',
				'title'         => __( 'Configure', 'wpmovielibrary' ),
				'callback'      => array( $this, 'configure_metabox' ),
				'screen'        => 'grid',
				'context'       => 'side',
				'priority'      => 'high',
				'callback_args' => null
			)
		);

		/**
		 * Filter metaboxes for the grid builder.
		 * 
		 * @since    3.0
		 * 
		 * @param    array     $metaboxes Default metaboxes.
		 * @param    object    GridBuilder instance.
		 */
		$this->metaboxes = apply_filters( 'wpmoly/filter/grid/metaboxes', $metaboxes, $this );

		$settings = array(
			'movie-grid-settings' => array(
				'label'     => esc_html__( 'Réglages', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'normal',
				'priority'  => 'high',
				'sections'  => array(
					'grid-filters' => array(
						'label'    => esc_html__( 'Filters', 'wpmovielibrary' ),
						'icon'     => 'dashicons-filter',
						'settings' => array(
							'text' => array(
								'type'     => 'text',
								'section'  => 'grid-filters',
								'label'    => esc_html__( 'Text input', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'widefat' ),
								'sanitize' => 'wp_filter_nohtml_kses'
							)
						)
					),
					'grid-ordering' => array(
						'label' => esc_html__( 'Ordering', 'wpmovielibrary' ),
						'icon'  => 'dashicons-randomize',
						'settings' => array(
							'text1' => array(
								'type'     => 'text',
								'section'  => 'grid-ordering',
								'label'    => esc_html__( 'Text input', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'widefat' ),
								'sanitize' => 'wp_filter_nohtml_kses'
							)
						)
					),
					'grid-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'text2' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Text input', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'widefat' ),
								'sanitize' => 'wp_filter_nohtml_kses'
							)
						)
					),
					'grid-controls' => array(
						'label' => esc_html__( 'Controls', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-tools',
						'settings' => array(
							'text3' => array(
								'type'     => 'text',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Text input', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'widefat' ),
								'sanitize' => 'wp_filter_nohtml_kses'
							)
						)
					)
				)
			)
		);

		/**
		 * Filter grid settings for the grid builder.
		 * 
		 * @since    3.0
		 * 
		 * @param    array     $settings Default settings.
		 * @param    object    GridBuilder instance.
		 */
		$this->settings = apply_filters( 'wpmoly/filter/grid/settings', $settings, $this );
	}

	/**
	 * Register metaboxes.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function add_metaboxes() {

		/**
		 * Fires before starting to register metaboxes.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    GridBuilder instance.
		 */
		do_action( 'wpmoly/action/grid/before/add_metaboxes', $this );

		foreach ( $this->metaboxes as $metabox ) {
			$metabox = (object) $metabox;
			foreach ( (array) $metabox->screen as $screen ) {
				add_action( "add_meta_boxes_{$screen}", function() use ( $metabox ) {
					add_meta_box( $metabox->id . '-metabox', $metabox->title, $metabox->callback, $metabox->screen, $metabox->context, $metabox->priority, $metabox->callback_args );
				} );
			}
		}

		/**
		 * Fires when all metaboxes have been registered.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    GridBuilder instance.
		 */
		do_action( 'wpmoly/action/grid/after/add_metaboxes', $this );
	}

	/**
	 * Grid Type Metabox callback.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function type_metabox( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}

?>
		<button type="button" data-action="grid-type" data-value="movies" class="active"><span class="wpmolicon icon-video"></span></button>
		<button type="button" data-action="grid-type" data-value="actors"><span class="wpmolicon icon-actor-alt"></span></button>
		<button type="button" data-action="grid-type" data-value="genres"><span class="wpmolicon icon-tag"></span></button>
		<div class="clear"></div>
<?php
	}

	/**
	 * Grid Configure Metabox callback.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function configure_metabox( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}

		echo '!';
	}

	/**
	 * Grid Builder container opening.
	 * 
	 * Open the grid builder container and show a couple of useful snippets.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function header( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}
?>
		<div id="grid-builder-container">

			<div id="wpmoly-grid-builder-shortcuts">
				<div id="wpmoly-grid-builder-id">Id: <code><?php the_ID(); ?></code></div>
				<div id="wpmoly-grid-builder-shortcode">ShortCode: <code>[movies id=<?php the_ID(); ?>]</code></div>
			</div>
<?php
	}

	/**
	 * Grid Preview editor toolbox.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function preview( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}
?>
		<div id="wpmoly-grid-builder" class="wpmoly">
			<div class="grid-builder-separator">
				<button type="button" data-action="toggle-preview" class="button separator-label"><?php _e( 'Preview' ); ?></button>
			</div>
			<div id="wpmoly-grid-builder-preview">
				<div class="wpmoly grid">
					<div class="wpmoly grid menu clearfix">
						<button type="button" data-action="grid-menu" class="button left"><span class="wpmolicon icon-order"></span></button>
						<button type="button" data-action="grid-settings" class="button right"><span class="wpmolicon icon-settings"></span></button>
					</div>
					<div class="wpmoly grid content clearfix">
						<div class="wpmoly grid movie">
							<div class="wpmoly grid movie poster"></div>
							<div class="wpmoly grid movie title">Loren ispum dolor sit amet</div>
							<div class="wpmoly grid movie rating">
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-half"></span>
								<span class="wpmolicon icon-star-empty"></span>
							</div>
						</div>
						<div class="wpmoly grid movie">
							<div class="wpmoly grid movie poster"></div>
							<div class="wpmoly grid movie title">Loren ispum dolor sit amet</div>
							<div class="wpmoly grid movie rating">
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-half"></span>
								<span class="wpmolicon icon-star-empty"></span>
							</div>
						</div>
						<div class="wpmoly grid movie">
							<div class="wpmoly grid movie poster"></div>
							<div class="wpmoly grid movie title">Loren ispum dolor sit amet</div>
							<div class="wpmoly grid movie rating">
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-half"></span>
								<span class="wpmolicon icon-star-empty"></span>
							</div>
						</div>
						<div class="wpmoly grid movie">
							<div class="wpmoly grid movie poster"></div>
							<div class="wpmoly grid movie title">Loren ispum dolor sit amet</div>
							<div class="wpmoly grid movie rating">
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-half"></span>
								<span class="wpmolicon icon-star-empty"></span>
							</div>
						</div>
						<div class="wpmoly grid movie">
							<div class="wpmoly grid movie poster"></div>
							<div class="wpmoly grid movie title">Loren ispum dolor sit amet</div>
							<div class="wpmoly grid movie rating">
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-half"></span>
								<span class="wpmolicon icon-star-empty"></span>
							</div>
						</div>
						<div class="wpmoly grid movie">
							<div class="wpmoly grid movie poster"></div>
							<div class="wpmoly grid movie title">Loren ispum dolor sit amet</div>
							<div class="wpmoly grid movie rating">
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-half"></span>
								<span class="wpmolicon icon-star-empty"></span>
							</div>
						</div>
						<div class="wpmoly grid movie">
							<div class="wpmoly grid movie poster"></div>
							<div class="wpmoly grid movie title">Loren ispum dolor sit amet</div>
							<div class="wpmoly grid movie rating">
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-half"></span>
								<span class="wpmolicon icon-star-empty"></span>
							</div>
						</div>
						<div class="wpmoly grid movie">
							<div class="wpmoly grid movie poster"></div>
							<div class="wpmoly grid movie title">Loren ispum dolor sit amet</div>
							<div class="wpmoly grid movie rating">
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-filled"></span>
								<span class="wpmolicon icon-star-half"></span>
								<span class="wpmolicon icon-star-empty"></span>
							</div>
						</div>
					</div>
					<div class="wpmoly grid pagination clearfix">
						<button type="button" data-action="grid-paginate" data-value="prev" class="button left"><span class="wpmolicon icon-arrow-left"></span></button>
						<div class="pagination menu">Page <span class="current-page"><input type="text" size="1" data-action="grid-paginate" value="1" /></span> of <span class="total-pages">123</span></div>
						<button type="button" data-action="grid-paginate" data-value="next" class="button right"><span class="wpmolicon icon-arrow-right"></span></button>
					</div>
				</div>
			</div>
			<div class="grid-builder-separator"><button type="button" class="button separator-label"><?php _e( 'Settings' ); ?></button></div>
		</div>
<?php
	}

	public function load() {

		// Bail if not our post type.
		if ( 'grid' !== get_current_screen()->post_type ) {
			return;
		}

		require_once WPMOLY_PATH . 'vendor/butterbean/butterbean.php';
	}

	public function register_butterbean( $butterbean, $post_type ) {

		foreach ( $this->settings as $id => $setting ) {

			$setting = (object) $setting;
			$butterbean->register_manager(
				$id,
				array(
					'label'     => $setting->label,
					'post_type' => $setting->post_type,
					'context'   => $setting->context,
					'priority'  => $setting->priority
				)
			);

			$manager = $butterbean->get_manager( $id );

			foreach ( $setting->sections as $section_id => $section ) {

				$section = (object) $section;
				$manager->register_section(
					$section_id,
					array(
						'label' => $section->label,
						'icon'  => $section->icon
					)
				);

				foreach ( $section->settings as $control_id => $control ) {

					$control = (object) $control;
					$manager->register_control(
						$control_id,
						array(
							'type'    => $control->type,
							'section' => $section_id,
							'label'   => $control->label,
							'attr'    => $control->attr
						)
					);

					$manager->register_setting(
						$control_id,
						array(
							'sanitize_callback' => $control->sanitize
						)
					);
				}
			}
		}

		/*$butterbean->register_manager(
			'movie-grid-settings',
			array(
				'label'     => esc_html__( 'Réglages', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'normal',
				'priority'  => 'high'
			)
		);

		$manager = $butterbean->get_manager( 'movie-grid-settings' );

		


		$manager->register_section(
			'grid-ordering',
			array(
				'label' => esc_html__( 'Ordering', 'wpmovielibrary' ),
				'icon'  => 'dashicons-randomize'
			)
		);

		$manager->register_section(
			'grid-appearance',
			array(
				'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
				'icon'  => 'dashicons-admin-appearance'
			)
		);

		$butterbean->register_manager(
			'actor-grid-settings',
			array(
				'label'     => esc_html__( 'Réglages', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'normal',
				'priority'  => 'high'
			)
		);

		$manager = $butterbean->get_manager( 'actor-grid-settings' );

		$manager->register_section(
			'grid-filters',
			array(
				'label' => esc_html__( 'Filters', 'wpmovielibrary' ),
				'icon'  => 'dashicons-filter'
			)
		);

		$manager->register_control(
			'text',
			array(
				'type'    => 'text',
				'section' => 'grid-filters',
				'label'   => esc_html__( 'Text input', 'wpmovielibrary' ),
				'attr'    => array( 'class' => 'widefat' )
			)
		);

		$manager->register_setting(
			'text',
			array(
				'sanitize_callback' => 'wp_filter_nohtml_kses'
			)
		);

		$manager->register_section(
			'grid-ordering',
			array(
				'label' => esc_html__( 'Ordering', 'wpmovielibrary' ),
				'icon'  => 'dashicons-randomize'
			)
		);

		/*$manager->register_control(
			'textarea', // Same as setting name.
			array(
				'type'    => 'textarea',
				'section' => 'grid-filters',
				'label'   => esc_html__( 'Text area', 'wpmovielibrary' ),
				'attr'    => array( 'class' => 'widefat' )
			)
		);

		$manager->register_setting(
			'textarea', // Same as control name.
			array(
				'sanitize_callback' => 'wp_filter_nohtml_kses'
			)
		);

		$manager->register_section(
			'section_2',
			array(
				'label' => esc_html__( 'Section 2', 'wpmovielibrary' ),
				'icon'  => 'dashicons-admin-generic'
			)
		);

		$manager->register_control(
			'select', // Same as setting name.
			array(
				'type'    => 'select',
				'section' => 'section_2',
				'label'   => esc_html__( 'Select', 'wpmovielibrary' ),
				'attr'    => array( 'class' => 'widefat' ),
				'choices' => array(
					'select_a' => 'Select A',
					'select_b' => 'Select B',
					'select_c' => 'Select C',
					'select_d' => 'Select D',
				)
			)
		);

		$manager->register_setting(
			'select', // Same as control name.
			array(
				'sanitize_callback' => 'esc_attr'
			)
		);

		$manager->register_control(
			'select_multiple', // Same as setting name.
			array(
				'type'    => 'select',
				'section' => 'section_2',
				'label'   => esc_html__( 'Select multiple', 'wpmovielibrary' ),
				'attr'    => array( 'multiple' => 'true', 'class' => 'widefat' ),
				'choices' => array(
					'select_a' => 'Select A',
					'select_b' => 'Select B',
					'select_c' => 'Select C',
					'select_d' => 'Select D',
				)
			)
		);

		$manager->register_setting(
			'select_multiple', // Same as control name.
			array(
				'sanitize_callback' => ''
			)
		);

		$manager->register_section(
			'section_3',
			array(
				'label' => esc_html__( 'Section 3', 'wpmovielibrary' ),
				'icon'  => 'dashicons-admin-generic'
			)
		);

		$manager->register_control(
			'radio', // Same as setting name.
			array(
				'type'    => 'radio',
				'section' => 'section_3',
				'label'   => esc_html__( 'Radio', 'wpmovielibrary' ),
				'attr'    => array( 'class' => 'widefat' ),
				'choices' => array(
					'select_a' => 'Select A',
					'select_b' => 'Select B',
					'select_c' => 'Select C',
					'select_d' => 'Select D',
				)
			)
		);

		$manager->register_setting(
			'radio', // Same as control name.
			array(
				'sanitize_callback' => 'esc_attr'
			)
		);

		$manager->register_control(
			'checkbox', // Same as setting name.
			array(
				'type'    => 'checkbox',
				'section' => 'section_3',
				'label'   => esc_html__( 'Checkbox', 'wpmovielibrary' ),
				'attr'    => array( 'class' => 'widefat' )
			)
		);

		$manager->register_setting(
			'checkbox', // Same as control name.
			array(
				'sanitize_callback' => 'esc_attr'
			)
		);

		$manager->register_control(
			'checkboxes', // Same as setting name.
			array(
				'type'    => 'checkboxes',
				'section' => 'section_3',
				'label'   => esc_html__( 'Checkboxes', 'wpmovielibrary' ),
				'attr'    => array( 'multiple' => 'true', 'class' => 'widefat' ),
				'choices' => array(
					'select_a' => 'Select A',
					'select_b' => 'Select B',
					'select_c' => 'Select C',
					'select_d' => 'Select D',
				)
			)
		);

		$manager->register_setting(
			'checkboxes', // Same as control name.
			array(
				'sanitize_callback' => 'esc_attr'
			)
		);*/
	}

	/**
	 * Grid Builder container closing.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function footer( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}
?>
		</div><!-- /#grid-builder-container -->
<?php
	}

}
