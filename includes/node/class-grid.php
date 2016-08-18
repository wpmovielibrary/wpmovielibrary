<?php
/**
 * Define the grid class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * Handle grids.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 * 
 * @property    int       $id Grid ID.
 * @property    string    $type Grid type: movie, actor, genre…
 * @property    string    $mode Grid mode: grid, list or archive
 * @property    string    $preset Grid content preset.
 * @property    string    $order_by Grid content order by.
 * @property    string    $order Grid content order.
 * @property    int       $columns Number of columns to use.
 * @property    int       $rows Number of rows to use.
 * @property    int       $total Number of Nodes to use.
 * @property    int       $show_menu Show the Grid menu to users.
 * @property    int       $mode_control Allow users to control the Grid mode.
 * @property    int       $content_control Allow users to control the Grid content.
 * @property    int       $display_control Allow users to control the Grid display.
 * @property    int       $order_control Allow users to control the Grid content ordering.
 * @property    int       $show_pagination Show the Grid pagination to users.
 */
class Grid extends Node {

	/**
	 * Node Collection.
	 * 
	 * @var    Collection
	 */
	public $items;

	/**
	 * Node JSON.
	 * 
	 * @var    object
	 */
	public $json;

	/**
	 * Supported Grid types.
	 * 
	 * @var    array
	 */
	private $supported_types = array();

	/**
	 * Supported Grid modes.
	 * 
	 * @var    array
	 */
	private $supported_modes = array();

	/**
	 * Supported Grid themes.
	 * 
	 * @var    array
	 */
	private $supported_themes = array();

	/**
	 * Initialize the Grid.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function init() {

		$this->suffix = '_wpmoly_grid_';
		$this->items = new Collection;

		$default_settings = array( 'type', 'mode', 'theme', 'preset', 'columns', 'rows', 'column_width', 'row_height', 'show_menu', 'mode_control', 'content_control', 'display_control', 'order_control', 'show_pagination' );

		/**
		 * Filter the default grid settings list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_settings
		 */
		$this->default_settings = apply_filters( 'wpmoly/filter/default/' . $this->type . '/grid/settings', $default_settings );

		$grid_types = array(
			'movie' => array(
				'label' => __( 'Movie', 'wpmovielibrary' ),
				'icon'  => 'wpmolicon icon-video',
				'modes' => array(
					'grid' => array(
						'label'  => __( 'Grid', 'wpmovielibrary' ),
						'icon'   => 'wpmolicon icon-th',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							),
							'variant-1' => array(
								'label' => __( 'Variant #1' ),
								'icon'  => 'wpmolicon icon-style'
							),
							'variant-2' => array(
								'label' => __( 'Variant #2' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'archive' => array(
						'label' => __( 'Archive', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							),
							'variant-1' => array(
								'label' => __( 'Variant #1' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					)
				)
			),
			'actor' => array(
				'label' => __( 'Actor', 'wpmovielibrary' ),
				'icon'  => 'wpmolicon icon-actor-alt',
				'modes' => array(
					'grid' => array(
						'label' => __( 'Grid', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'archive' => array(
						'label' => __( 'Archive', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					)
				)
			),
			'genre' => array(
				'label' => __( 'Genre', 'wpmovielibrary' ),
				'icon'  => 'wpmolicon icon-tag',
				'modes' => array(
					'grid' => array(
						'label' => __( 'Grid', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'archive' => array(
						'label' => __( 'Archive', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					)
				)
			)
		);

		/**
		 * Filter the supported Grid types.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_types
		 */
		$this->supported_types = apply_filters( 'wpmoly/filter/grid/supported/types', $grid_types );

		foreach ( $this->supported_types as $type_id => $type ) {

			/**
			 * Filter the supported Grid modes.
			 * 
			 * @since    3.0
			 * 
			 * @param    array    $default_modes
			 */
			$this->supported_modes[ $type_id ] = apply_filters( 'wpmoly/filter/grid/supported/' . $type_id . '/modes', $type['modes'] );

			foreach ( $this->supported_modes[ $type_id ] as $mode_id => $mode ) {

				/**
				 * Filter the supported Grid themes.
				 * 
				 * @since    3.0
				 * 
				 * @param    array    $default_themes
				 */
				$this->supported_themes[ $type_id ][ $mode_id ] = apply_filters( 'wpmoly/filter/grid/supported/' . $type_id . '/' . $mode_id . '/themes', $mode['themes'] );
			}
		}

		$this->build();
	}

	/**
	 * Build the Grid.
	 * 
	 * Load items depending on presets or custom settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function build() {

		if ( 'custom' != $this->preset ) {
			$query = $this->get_query_callback();
			if ( is_callable( $query ) ) {
				$items = call_user_func( $query );
				foreach ( (array) $items as $item ) {
					$this->items->add( $item );
				}
				return $this->items;
			}
		}

		return $this->build_query();
	}

	/**
	 * Determine the callback to use based on the Grid type.
	 * 
	 * This should return a valid array( $class, $method ) callback.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function get_query_callback() {

		$classes = array(
			'movie' => '\wpmoly\Query\Movies',
			'actor' => '\wpmoly\Query\Actors',
			'genre' => '\wpmoly\Query\Genres'
		);

		if ( isset( $classes[ $this->type ] ) ) {
			$class = $classes[ $this->type ];
			$method = str_replace( '-', '_', $this->preset );
			if ( method_exists( $class, $method ) ) {
				return array( $class, $method );
			}
		}

		return array();
	}

	/**
	 * Perform a custom query.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function build_query() {

		return array();
	}

	/**
	 * Simple accessor for supported types.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_supported_types() {

		return $this->supported_types;
	}

	/**
	 * Simple accessor for supported modes.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_supported_modes( $type = '' ) {

		return ! empty( $type ) && ! empty( $this->supported_modes[ $type ] ) ? $this->supported_modes[ $type ] : $this->supported_modes;
	}

	/**
	 * Simple accessor for supported themes.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_supported_themes( $type = '', $mode = '' ) {

		return ! empty( $type ) && ! empty( $mode ) && ! empty( $this->supported_themes[ $type ][ $mode ] ) ? $this->supported_themes[ $type ][ $mode ] : $this->supported_themes;
	}

	/**
	 * Return a valid number of rows.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $rows Number of rows.
	 * 
	 * @return   int
	 */
	public function validate_rows( $rows ) {

		/**
		 * Filter the minimum number of rows.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $min Default minimum number of rows.
		 * @param    Grid    $grid Grid instance.
		 */
		$min = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/rows/min', 1, $this );

		/**
		 * Filter the maximum number of rows.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $max Default maximum number of rows.
		 * @param    Grid    $grid Grid instance.
		 */
		$max = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/rows/max', 10, $this );

		/**
		 * Filter the default number of rows.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $default Default number of rows.
		 * @param    Grid    $grid Grid instance.
		 */
		$default = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/rows/default', 4, $this );

		return ! empty( $rows ) ? max( $min, min( $rows, $max ) ) : $default;
	}

	/**
	 * Return a valid number of columns.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $rows Number of columns.
	 * 
	 * @return   int
	 */
	public function validate_columns( $columns ) {

		/**
		 * Filter the minimum number of columns.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $min Default minimum number of columns.
		 * @param    Grid    $grid Grid instance.
		 */
		$min = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/columns/min', 1, $this );

		/**
		 * Filter the maximum number of columns.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $max Default maximum number of columns.
		 * @param    Grid    $grid Grid instance.
		 */
		$max = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/columns/max', 12, $this );

		/**
		 * Filter the default number of columns.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $default Default number of columns.
		 * @param    Grid    $grid Grid instance.
		 */
		$default = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/columns/default', 5, $this );

		return ! empty( $columns ) ? max( $min, min( $columns, $max ) ) : $default;
	}

	/**
	 * Return a valid ideal column width.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $column_width Ideal column width.
	 * 
	 * @return   int
	 */
	public function validate_column_width( $column_width ) {

		/**
		 * Filter the default ideal column width.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $ideal_width Default ideal column width.
		 * @param    Grid    $grid Grid instance.
		 */
		$ideal_width = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/columns/ideal_width', 160, $this );

		return ! empty( $column_width ) ? intval( $column_width ) : $ideal_width;
	}

	/**
	 * Return a valid ideal row height.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $row_width Ideal row height.
	 * 
	 * @return   int
	 */
	public function validate_row_height( $row_height ) {

		/**
		 * Filter the default ideal row height.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $ideal_width Default ideal row height.
		 * @param    Grid    $grid Grid instance.
		 */
		$ideal_height = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/rows/ideal_height', 240, $this );

		return ! empty( $row_height ) ? intval( $row_height ) : $ideal_height;
	}

	/**
	 * Make sure a Grid preset is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $preset Grid preset to validate.
	 * 
	 * @return   string
	 */
	public function validate_preset( $preset ) {

		if ( empty( $preset ) ) {
			$preset = 'default_preset';
		}

		return $preset;
	}

	/**
	 * Make sure a Grid theme is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $theme Grid theme to validate.
	 * 
	 * @return   string
	 */
	public function validate_theme( $theme ) {

		return isset( $this->supported_themes[ $this->type ][ $theme ] ) ? $theme : 'default';
	}

	/**
	 * Make sure a Grid mode is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $mode Grid mode to validate.
	 * 
	 * @return   string
	 */
	public function validate_mode( $mode ) {

		return isset( $this->supported_modes[ $this->type ][ $mode ] ) ? $mode : 'grid';
	}

	/**
	 * Make sure a Grid type is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Grid type to validate.
	 * 
	 * @return   string
	 */
	public function validate_type( $type ) {

		return isset( $this->supported_types[ $type ] ) ? $type : 'movie';
	}

	/**
	 * Save grid settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function save() {

		foreach ( $this->default_settings as $setting ) {
			if ( isset( $this->$setting ) ) {
				update_post_meta( $this->id, $this->suffix . $setting, $this->$setting );
			}
		}
	}

	/**
	 * JSONify the Grid instance.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function toJSON() {

		$json = array();

		$json['types'] = $this->supported_types;
		$json['modes'] = $this->supported_modes;
		$json['themes'] = $this->supported_themes;

		$json['settings'] = array();
		foreach ( $this->default_settings as $setting ) {
			$json['settings'][ $setting ] = $this->$setting;
		}

		return $this->json = json_encode( $json );
	}
}
