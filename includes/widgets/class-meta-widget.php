<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


/**
 * Movie Meta Widget.
 * 
 * Display a list of the Movies Meta: director, countries, runtime... 
 * 
 * @since    2.0
 */
class WPMOLY_Meta_Widget extends WPMOLY_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget. No
	 * stylesheets or JavaScript needed, localization loaded in public class.
	 */
	public function __construct() {

		$this->widget_name        = __( 'WPMovieLibrary Meta', 'wpmovielibrary' );
		$this->widget_description = __( 'Display a list of the available meta: director, countries, runtime...', 'wpmovielibrary' );
		$this->widget_css         = 'wpmovielibrary wpmoly-widget wpmoly-meta-widget';
		$this->widget_id          = 'wpmovielibrary_meta_widget';
		$this->widget_form        = 'meta-widget/meta-admin.php';

		$this->widget_params      = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Movie Meta', 'wpmovielibrary' )
			),
			'description' => array(
				'type'  => 'text',
				'std' => ''
			),
			'meta' => array(
				'type' => 'select',
				'std' => ''
			),
			'list' => array(
				'type' => 'checkbox',
				'std' => 0
			),
			'css' => array(
				'type' => 'checkbox',
				'std' => 0
			)
		);

		$this->meta = array();

		$supported = WPMOLY_Settings::get_supported_movie_meta();
		foreach ( $supported as $slug => $meta )
			$this->meta[ $slug ] = array(
				'default'  => sprintf( __( 'Select a %s', 'wpmovielibrary' ), $slug ),
				'empty'    => sprintf( __( 'No %s to display.', 'wpmovielibrary' ), $slug )
			);

		parent::__construct();
	}

	/**
	 * Output the Widget content.
	 * 
	 * @since    1.2
	 *
	 * @param    array    $args The array of form elements
	 * @param    array    $instance The current instance of the widget
	 * 
	 * @return   void
	 */
	public function widget( $args, $instance ) {

		// Caching
		$name = apply_filters( 'wpmoly_cache_name', 'meta_widget', $args );
		// Naughty PHP 5.3 fix
		$widget = &$this;
		$content = WPMOLY_Cache::output( $name, function() use ( $widget, $args, $instance ) {

			return $widget->widget_content( $args, $instance );
		});

		echo $content;
	}

	/**
	 * Generate the content of the widget.
	 * 
	 * @since    1.2
	 *
	 * @param    array    $args The array of form elements
	 * @param    array    $instance The current instance of the widget
	 * 
	 * @return   string   The Widget Content
	 */
	public function widget_content( $args, $instance ) {

		extract( $args, EXTR_SKIP );
		extract( $instance );

		$title = apply_filters( 'widget_title', $title );

		$metas = call_user_func( "WPMOLY_Settings::get_available_movie_{$meta}" );
		$movies  = wpmoly_o( 'rewrite-movie' );

		if ( ! empty( $metas ) ) {

			$this->widget_css .= " wpmoly-{$meta}-widget";

			if ( $css )
				$this->widget_css .= ' wpmoly-list custom';

			$items = array();
			foreach ( $metas as $slug => $_title ) {

				$_slug = ( $rewrite ? __( $slug, 'wpmovielibrary' ) : $slug );

				$item = array(
					'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpmovielibrary' ), __( $_title, 'wpmovielibrary' ) ),
					'link'        => WPMOLY_L10n::get_meta_permalink( $meta, $_slug, $type = 'detail', $format = 'raw' )
				);

				if ( 'rating' != $meta )
					$item['title'] = __( $_title, 'wpmovielibrary' );
				else if ( 'rating' == $meta && $list )
					$item['title'] = esc_attr__( $_title, 'wpmovielibrary' ) . ' (' . $slug . '&#9733;)';
				else
					$item['title'] = '<div class="movie-rating-display">' . apply_filters( 'wpmoly_movie_rating_stars', $slug ) . '<span class="rating-label">' . esc_attr__( $_title, 'wpmovielibrary' ) . '</span></div>';

				$items[] = $item;
			}

			$attributes = array( 'items' => $items, 'description' => $description, 'default_option' => $this->meta[ $meta ]['default'], 'style' => $this->widget_css );

			if ( $list )
				$html = WPMovieLibrary::render_template( 'meta-widget/meta-dropdown-list.php', $attributes, $require = 'always' );
			else
				$html = WPMovieLibrary::render_template( 'meta-widget/meta-list.php', $attributes, $require = 'always' );
		}
		else {
			$html = WPMovieLibrary::render_template( 'empty.php', array( 'message' => __( 'No meta no show', 'wpmovielibrary' ) ), $require = 'always' );
		}

		return $before_widget . $before_title . $title . $after_title . $html . $after_widget;
	}

}
