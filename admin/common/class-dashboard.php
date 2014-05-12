<?php
/**
 * WPMovieLibrary Dashboard Class extension.
 * 
 * Implement a simple custom Dashboard
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Dashboard' ) ) :

	class WPML_Dashboard extends WPML_Module {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 */
		public function __construct() {

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'wpml_dashboard_setup', array( $this, 'prefix_add_dashboard_widget' ) );
		}
 
public function prefix_add_dashboard_widget() {

	$widget_id = 'my_dashboard_widget';
	$widget_name = 'Featured Dashboard Page';
	$callback = array( $this, 'prefix_dashboard_widget' );
	$control_callback = array( $this, 'prefix_dashboard_widget_handle' );
	$callback_args = null;

	$screen = get_current_screen();
	global $wp_dashboard_control_callbacks, $admin_page_hooks;

	if ( $control_callback && current_user_can( 'edit_dashboard' ) && is_callable( $control_callback ) ) {
		$wp_dashboard_control_callbacks[$widget_id] = $control_callback;
		if ( isset( $_GET['edit'] ) && $widget_id == $_GET['edit'] ) {
			list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '">' . __( 'Cancel' ) . '</a></span>';
			$callback = '_wp_dashboard_control_callback';
		} else {
			list($url) = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( "$url#$widget_id" ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>';
		}
	}

	$side_widgets = array( 'dashboard_quick_press', 'dashboard_primary' );

	$location = 'normal';
	if ( in_array($widget_id, $side_widgets) )
		$location = 'side';

	$priority = 'core';
	if ( 'dashboard_browser_nag' === $widget_id )
		$priority = 'high';

	add_meta_box( $widget_id, $widget_name, $callback, $screen, $location, $priority, $callback_args );

}

public function prefix_dashboard_widget() {
    # get saved data
    if( !$widget_options = get_option( 'my_dashboard_widget_options' ) )
        $widget_options = array( );

    # default output
    $output = sprintf(
        '<h2 style="text-align:right">%s</h2>',
        __( 'Please, configure the widget ☝' )
    );
    
    # check if saved data contains content
    $saved_feature_post = isset( $widget_options['feature_post'] ) 
        ? $widget_options['feature_post'] : false;

    # custom content saved by control callback, modify output
    if( $saved_feature_post ) {
        $post = get_post( $saved_feature_post );
        if( $post ) {
            $content = do_shortcode( html_entity_decode( $post->post_content ) );
            $output = "<h2>{$post->post_title}</h2><p>{$content}</p>";
        }
    }
    echo "<div class='feature_post_class_wrap'>
        <label style='background:#ccc;'>$output</label>
    </div>
    ";
}

public function prefix_dashboard_widget_handle()
{
    # get saved data
    if( !$widget_options = get_option( 'my_dashboard_widget_options' ) )
        $widget_options = array( );

    # process update
    if( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['my_dashboard_widget_options'] ) ) {
        # minor validation
        $widget_options['feature_post'] = absint( $_POST['my_dashboard_widget_options']['feature_post'] );
        # save update
        update_option( 'my_dashboard_widget_options', $widget_options );
    }

    # set defaults  
    if( !isset( $widget_options['feature_post'] ) )
        $widget_options['feature_post'] = '';


    echo "<p><strong>Available Pages</strong></p>
    <div class='feature_post_class_wrap'>
        <label>Title</label>";
    wp_dropdown_pages( array(
        'post_type'        => 'page',
        'selected'         => $widget_options['feature_post'],
        'name'             => 'my_dashboard_widget_options[feature_post]',
        'id'               => 'feature_post',
        'show_option_none' => '- Select -'
    ) );
    echo "</div>";
}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {}

	}

endif;
