<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


/**
 * Movie Details Widget.
 * 
 * Display a list of the Movies Details: Status, Media and Rating. This
 * replace the previous Status, Media and Most Rated Movies Widgets.
 * 
 * @since    1.2
 */
class WPML_Details_Widget extends WPML_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget. No
	 * stylesheets or JavaScript needed, localization loaded in public class.
	 */
	public function __construct() {

		$this->widget_name        = __( 'WPMovieLibrary Details', 'wpmovielibrary' );
		$this->widget_description = __( 'Display a list of the available details: status, media and rating.', 'wpmovielibrary' );
		$this->widget_css         = 'wpmovielibrary wpml-widget wpml-details-widget';
		$this->widget_id          = 'wpmovielibrary_details_widget';
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

		$this->details = array(
			'status' => array(
				'default'  => __( 'Select a status', 'wpmovielibrary' ),
				'empty'    => __( 'No status to display.', 'wpmovielibrary' )
			),
			'media' => array(
				'default'  => __( 'Select a media', 'wpmovielibrary' ),
				'empty'    => __( 'No media to display.', 'wpmovielibrary' )
			),
			'rating' => array(
				'default'  => __( 'Select a rating', 'wpmovielibrary' ),
				'empty'    => __( 'No rating to display.', 'wpmovielibrary' )
			),
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
		$name = apply_filters( 'wpml_cache_name', 'details_widget', $args );
		// Naughty PHP 5.3 fix
		$widget = &$this;
		$content = WPML_Cache::output( $name, function() use ( $widget, $args, $instance ) {

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

		if ( ! in_array( $instance['detail'], array( 'status', 'media', 'rating' ) ) )
			return false;

		extract( $args, EXTR_SKIP );
		extract( $instance );

		$title = apply_filters( 'widget_title', $title );

		$details = call_user_func( "WPML_Settings::get_available_movie_{$detail}" );
		$rewrite = call_user_func( "WPML_Settings::wpml__details_rewrite" );
		$movies = WPML_Settings::wpml__movie_rewrite();

		if ( ! empty( $details ) ) {

			$this->widget_css .= " wpml-{$detail}-widget";

			if ( $css )
				$this->widget_css .= ' wpml-list custom';

			$items = array();
			foreach ( $details as $slug => $_title ) {

				$_slug = ( $rewrite ? __( $slug, 'wpmovielibrary' ) : $slug );

				$item = array(
					'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpmovielibrary' ), __( $_title, 'wpmovielibrary' ) ),
					'link'        => home_url( "/{$movies}/{$_slug}/" )
				);

				if ( 'rating' != $detail )
					$item['title'] = __( $_title, 'wpmovielibrary' );
				else if ( 'rating' == $detail && $list )
					$item['title'] = esc_attr__( $_title, 'wpmovielibrary' ) . ' (' . $slug . '&#9733;)';
				else
					$item['title'] = '<div class="movie_rating_display stars_' . str_replace( '.', '_', $slug ) . '"><div class="stars_labels"><span class="stars_label stars_label_' . str_replace( '.', '_', $slug ) . '">' . esc_attr__( $_title, 'wpmovielibrary' ) . '</span></div></div>';

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
