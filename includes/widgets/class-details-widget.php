<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */


/**
 * Movie Details Widget.
 * 
 * Display a list of the Movies Details: Status, Media and Rating. This
 * replace the previous Status, Media and Most Rated Movies Widgets.
 * 
 * @since    1.2
 */
class WPMOLY_Details_Widget extends WPMOLY_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget. No
	 * stylesheets or JavaScript needed, localization loaded in public class.
	 */
	public function __construct() {

		$this->widget_name        = __( 'WPMovieLibrary Details', 'wpmovielibrary' );
		$this->widget_description = __( 'Display a list of the available details: status, media and rating.', 'wpmovielibrary' );
		$this->widget_css         = 'wpmoly details';
		$this->widget_id          = 'wpmovielibrary-details-widget';
		$this->widget_form        = 'details-widget/details-admin.php';

		$this->widget_params      = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Movie Details', 'wpmovielibrary' )
			),
			'description' => array(
				'type'  => 'text',
				'std' => ''
			),
			'detail' => array(
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

		$this->details = array();

		$supported = WPMOLY_Settings::get_supported_movie_details();
		foreach ( $supported as $slug => $detail )
			$this->details[ $slug ] = array(
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
		$name = apply_filters( 'wpmoly_cache_name', 'details_widget', $args );
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

		$defaults = array(
			'title'       => __( 'Movie Details', 'wpmovielibrary' ),
			'description' => '',
			'detail'      => '',
			'list'        => 0,
			'css'         => 0
		);
		$args = wp_parse_args( $args, $defaults );

		extract( $args, EXTR_SKIP );
		extract( $instance );

		$title = apply_filters( 'widget_title', $title );

		$details = call_user_func( "WPMOLY_Settings::get_available_movie_{$detail}" );
		$rewrite = wpmoly_o( 'rewrite-details' );
		$movies  = wpmoly_o( 'rewrite-movie' );

		if ( ! empty( $details ) ) {

			$baseurl = trailingslashit( get_post_type_archive_link( 'movie' ) );

			$this->widget_css .= " wpmoly {$detail}";

			if ( $css )
				$this->widget_css .= ' list custom';

			$items = array();
			foreach ( $details as $slug => $_title ) {

				$item = array(
					'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpmovielibrary' ), __( $_title, 'wpmovielibrary' ) ),
					'link'        => WPMOLY_Utils::get_meta_permalink(
						array(
							'key'     => $detail,
							'value'   => $slug,
							'type'    => 'detail',
							'format'  => 'raw',
							'baseurl' => $baseurl
						)
					)
				);

				if ( 'rating' != $detail )
					$item['title'] = __( $_title, 'wpmovielibrary' );
				else if ( 'rating' == $detail && $list )
					$item['title'] = esc_attr__( $_title, 'wpmovielibrary' ) . ' (' . $slug . '&#9733;)';
				else
					$item['title'] = '<div class="movie-rating-display">' . apply_filters( 'wpmoly_movie_rating_stars', $slug, null, null, true ) . '<span class="rating-label">' . esc_attr__( $_title, 'wpmovielibrary' ) . '</span></div>';

				$items[] = $item;
			}

			$attributes = array( 'items' => $items, 'description' => $description, 'default_option' => $this->details[ $detail ]['default'], 'style' => $this->widget_css );

			if ( $list )
				$html = WPMovieLibrary::render_template( 'details-widget/details-dropdown-list.php', $attributes, $require = 'always' );
			else
				$html = WPMovieLibrary::render_template( 'details-widget/details-list.php', $attributes, $require = 'always' );
		}
		else {
			$html = WPMovieLibrary::render_template( 'empty.php', array( 'message' => __( 'No detail no show', 'wpmovielibrary' ) ), $require = 'always' );
		}

		return $before_widget . $before_title . $title . $after_title . $html . $after_widget;
	}

}
