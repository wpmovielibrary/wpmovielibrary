<?php
/**
 * WPMovieLibrary Edit_Movies Class extension.
 * 
 * Edit Movies related methods. Handles Post List Tables, Quick and Bulk Edit
 * Forms, Meta and Details Metaboxes, Images and Posters WP Media Modals.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Edit_Movies' ) ) :

	class WPMOLY_Edit_Movies extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			if ( ! is_admin() )
				return false;

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 9 );

			// Bulk/quick edit
			add_filter( 'bulk_post_updated_messages', __CLASS__ . '::movie_bulk_updated_messages', 10, 2 );

			add_action( 'quick_edit_custom_box', __CLASS__ . '::quick_edit_movies', 10, 2 );
			add_action( 'bulk_edit_custom_box', __CLASS__ . '::bulk_edit_movies', 10, 2 );
			add_filter( 'post_row_actions', __CLASS__ . '::expand_quick_edit_link', 10, 2 );

			// Post List Table
			add_filter( 'manage_movie_posts_columns', __CLASS__ . '::movies_columns_head' );
			add_action( 'manage_movie_posts_custom_column', __CLASS__ . '::movies_columns_content', 10, 2 );
			add_filter( 'manage_edit-movie_sortable_columns', __CLASS__ . '::movies_sortable_columns', 10, 1 );
			add_action( 'pre_get_posts', __CLASS__ . '::movies_sortable_columns_order', 10, 1 );

			// Post Convert
			if ( 1 == wpmoly_o( 'convert-enable' ) ) {
				add_action( 'admin_footer-edit.php', 'WPMOLY_Edit_Movies::bulk_admin_footer', 10 );
				add_action( 'load-post.php', 'WPMOLY_Edit_Movies::convert_post_type', 10 );
				add_action( 'load-edit.php', 'WPMOLY_Edit_Movies::bulk_convert_post_type', 10 );
			}

			// Media
			add_action( 'the_posts', __CLASS__ . '::the_posts_hijack', 10, 2 );
			add_action( 'ajax_query_attachments_args', __CLASS__ . '::load_images_dummy_query_args', 10, 1 );

			// Post edit
			add_filter( 'post_updated_messages', __CLASS__ . '::movie_updated_messages', 10, 1 );
			add_action( 'save_post_movie', __CLASS__ . '::save_movie', 10, 4 );
			add_action( 'wp_insert_post_empty_content', __CLASS__ . '::filter_empty_content', 10, 2 );
			add_action( 'wp_insert_post_data', __CLASS__ . '::filter_empty_title', 10, 2 );

			// Callbacks
			add_action( 'wp_ajax_wpmoly_save_meta', __CLASS__ . '::save_meta_callback' );
			add_action( 'wp_ajax_wpmoly_empty_meta', __CLASS__ . '::empty_meta_callback' );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                        Scripts & Styles
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Enqueue required media scripts and styles
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $hook_suffix The current admin page.
		 */
		public function admin_enqueue_scripts( $hook_suffix ) {

			if ( ( 'post.php' != $hook_suffix && 'post-new.php' != $hook_suffix ) || 'movie' != get_post_type() )
				return false;

			wp_enqueue_media();
			wp_enqueue_script( 'media' );

			wp_register_script( 'select2-sortable-js', ReduxFramework::$_url . 'assets/js/vendor/select2.sortable.min.js', array( 'jquery' ), WPMOLY_VERSION, true );
			wp_register_script( 'select2-js', ReduxFramework::$_url . 'assets/js/vendor/select2/select2.min.js', array( 'jquery', 'select2-sortable-js' ), WPMOLY_VERSION, true );
			wp_enqueue_script( 'field-select-js', ReduxFramework::$_url . 'inc/fields/select/field_select.min.js', array( 'jquery', 'select2-js' ), WPMOLY_VERSION, true );
			wp_enqueue_style( 'select2-css', ReduxFramework::$_url . 'assets/js/vendor/select2/select2.css', array(), WPMOLY_VERSION, 'all' );
			wp_enqueue_style( 'redux-field-select-css', ReduxFramework::$_url . 'inc/fields/select/field_select.css', WPMOLY_VERSION, true );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Callbacks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Remove all currently edited post' metadata and taxonomies.
		 *
		 * @since    1.2
		 */
		public static function empty_meta_callback() {

			$post_id = ( isset( $_POST['post_id'] ) && '' != $_POST['post_id'] ? intval( $_POST['post_id'] ) : null );

			if ( is_null( $post_id ) )
				return new WP_Error( 'invalid', __( 'Empty or invalid Post ID or Movie Details', 'wpmovielibrary' ) );

			wpmoly_check_ajax_referer( 'empty-movie-meta' );

			$response = self::empty_movie_meta( $post_id );

			wpmoly_ajax_response( $response, array(), wpmoly_create_nonce( 'empty-movie-meta' ) );
		}

		/**
		 * Save metadata once they're collected.
		 *
		 * @since    2.0.3
		 */
		public static function save_meta_callback() {

			$post_id  = ( isset( $_POST['post_id'] ) && '' != $_POST['post_id'] ? intval( $_POST['post_id'] ) : null );
			$data     = ( isset( $_POST['data'] ) && '' != $_POST['data'] ? $_POST['data'] : null );

			if ( is_null( $post_id ) )
				return new WP_Error( 'invalid', __( 'Empty or invalid Post ID or Movie Details', 'wpmovielibrary' ) );

			wpmoly_check_ajax_referer( 'save-movie-meta' );

			$response = self::save_movie_meta( $post_id, $data );

			wpmoly_ajax_response( $response, array(), wpmoly_create_nonce( 'save-movie-meta' ) );
		}

		/**
		 * Add message support for movies in Post Editor.
		 * 
		 * @since    2.1.4
		 * 
		 * @param    array    $messages Default Post update messages
		 * 
		 * @return   array    Updated Post update messages
		 */
		public static function movie_updated_messages( $messages ) {

			global $post;
			$post_ID = $post->ID;

			$new_messages = array(
				'movie' => array(
					1  => sprintf( __( 'Movie updated. <a href="%s">View movie</a>', 'wpmovielibrary' ), esc_url( get_permalink( $post_ID ) ) ),
					2  => __( 'Custom field updated.', 'wpmovielibrary' ) ,
					3  => __( 'Custom field deleted.', 'wpmovielibrary' ),
					4  => __( 'Movie updated.', 'wpmovielibrary' ),
					5  => isset( $_GET['revision'] ) ? sprintf( __( 'Movie restored to revision from %s', 'wpmovielibrary' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					6  => sprintf( __( 'Movie published. <a href="%s">View movie</a>', 'wpmovielibrary' ), esc_url( get_permalink( $post_ID ) ) ),
					7  => __( 'Movie saved.' ),
					8  => sprintf( __( 'Movie submitted. <a target="_blank" href="%s">Preview movie</a>', 'wpmovielibrary' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
					9  => sprintf( __( 'Movie scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview movie</a>', 'wpmovielibrary' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
					10 => sprintf( __( 'Movie draft updated. <a target="_blank" href="%s">Preview movie</a>', 'wpmovielibrary' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
					11 => __( 'Successfully converted to movie.', 'wpmovielibrary' )
				)
			);

			$messages = array_merge( $messages, $new_messages );

			return $messages;
		}

		/**
		 * Add message support for movies in Post Editor bulk edit.
		 * 
		 * @since    2.1.4
		 * 
		 * @param    array    $messages Default Post bulk edit messages
		 * 
		 * @return   array    Updated Post bulk edit messages
		 */
		public static function movie_bulk_updated_messages( $bulk_messages, $bulk_counts ) {

			$new_messages = array(
				'movie' => array(
					'updated'   => _n( '%s movie updated.', '%s movies updated.', $bulk_counts['updated'], 'wpmovielibrary' ),
					'locked'    => _n( '%s movie not updated, somebody is editing it.', '%s movies not updated, somebody is editing them.', $bulk_counts['locked'], 'wpmovielibrary' ),
					'deleted'   => _n( '%s movie permanently deleted.', '%s movies permanently deleted.', $bulk_counts['deleted'], 'wpmovielibrary' ),
					'trashed'   => _n( '%s movie moved to the Trash.', '%s movies moved to the Trash.', $bulk_counts['trashed'], 'wpmovielibrary' ),
					'untrashed' => _n( '%s movie restored from the Trash.', '%s movies restored from the Trash.', $bulk_counts['untrashed'], 'wpmovielibrary' ),
				)
			);

			$messages = array_merge( $bulk_messages, $new_messages );

			return $messages;
		}

		/**
		 * Add custom bulk action to edit.php
		 * 
		 * This is needed as WordPress does not provide a way to add new
		 * actions via filters, only remove existing actions.
		 * 
		 * @since    2.1.4
		 */
		public static function bulk_admin_footer() {

			global $post_type;
 
			$post_types = wpmoly_o( 'convert-post-types' );
			if ( ! in_array( $post_type, $post_types ) )
				return false;
?>
	<script type="text/javascript">
		(function( $ ) {
			$( '<option>' ).val( 'bulk_convert_post_type' ).text( '<?php _e( 'Convert to movie', 'wpmovielibrary' )?>' ).appendTo( "select[name='action']" );
			$( '<option>' ).val( 'bulk_convert_post_type' ).text( '<?php _e( 'Convert to movie', 'wpmovielibrary' )?>' ).appendTo( "select[name='action2']" );
		})(jQuery);
	</script>

<?php
		}

		/**
		 * Convert a Post (or Page) to Movie.
		 * 
		 * This is used to convert regular posts and pages to movies to
		 * avoid having to duplicate content of manually create/delete
		 * contents to use WPMOLY features.
		 * 
		 * @since    2.1.4
		 */
		public static function convert_post_type() {

			if ( ! isset( $_REQUEST['wpmoly_convert_post_type'] ) || '1' != $_REQUEST['wpmoly_convert_post_type'] )
				return false;

			wpmoly_check_admin_referer( 'convert-post-type' );

			$post_id = intval( esc_attr( $_REQUEST['post'] ) );
			$post_types = wpmoly_o( 'convert-post-types' );
			if ( is_null( get_post( $post_id ) ) || ! in_array( get_post_type( $post_id ), $post_types ) )
				return false;

			$update = self::bulk_convert_posts( $post_id );
			if ( ! is_wp_error( $update ) ) {
				$update = "&message=11";
			} else {
				$update = '';
			}

			wp_safe_redirect( admin_url( "post.php?post={$post_id}&action=edit{$update}" ) );
		}

		/**
		 * Bulk Convert Posts (or Pages) to Movie.
		 * 
		 * @since    2.1.4
		 * 
		 * @param    array    List of Post IDs to update
		 * 
		 * @return   WP_Error|bool    True on success, WP_Error if any post was not updated
		 */
		public static function bulk_convert_posts( $post_ids ) {

			$error = new WP_Error();

			if ( ! is_array( $post_ids ) )
				$post_ids = array( $post_ids );

			foreach ( $post_ids as $post_id ) {
				$update = set_post_type( $post_id, $post_type = 'movie' );

				if ( ! $update )
					$error->add( $post_id, sprintf( __( 'Error update Post #%d post_type.', 'wpmovielibrary' ), $post_id ) );
			}

			if ( ! empty( $error->errors ) )
				return $error;

			return true;
		}

		/**
		 * Handle Bulk Convert action.
		 * 
		 * Intercept a custom 'bulk_convert_post_type' action in edit.php,
		 * convert the concerned posts and redirects.
		 * 
		 * @since    2.1.4
		 */
		public static function bulk_convert_post_type() {

			global $typenow;

			$post_types = wpmoly_o( 'convert-post-types' );

			if ( ! in_array( $typenow, $post_types ) )
				return false;

			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action = $wp_list_table->current_action();

			if ( 'bulk_convert_post_type' != $action )
				return false;

			check_admin_referer( 'bulk-posts' );

			if ( isset( $_REQUEST['post'] ) )
				$post_ids = array_map( 'intval', $_REQUEST['post'] );
			
			if ( empty( $post_ids ) )
				return false;
			
			// this is based on wp-admin/edit.php
			$sendback = remove_query_arg( array( 'exported', 'untrashed', 'deleted', 'ids' ), wp_get_referer() );
			if ( ! $sendback )
				$sendback = admin_url( "edit.php?post_type=$typenow" );
			
			$pagenum  = $wp_list_table->get_pagenum();
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );

			$result = self::bulk_convert_posts( $post_ids );
			if ( ! is_wp_error( $result ) ) {
				$updated = count( $post_ids );
			} else {
				$updated = null;
			}

			$ids = implode( ',', $post_ids );

			$sendback = add_query_arg( compact( 'updated', 'ids' ), $sendback );
			$sendback = remove_query_arg( array( 'action', 'paged', 'mode', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view' ), $sendback );
			
			wp_safe_redirect( esc_url( $sendback ) );
			exit();
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                     "All Movies" WP List Table
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert a simple 'Poster' column to Movies list table to display
		 * movies' poster set as featured image if available.
		 * 
		 * @since    1.0
		 * 
		 * @param    array    $defaults Default WP_List_Table header columns
		 * 
		 * @return   array    Default columns with new poster column
		 */
		public static function movies_columns_head( $defaults ) {

			$title = array_search( 'title', array_keys( $defaults ) );
			$comments = array_search( 'comments', array_keys( $defaults ) ) - 1;

			$defaults = array_merge(
				array_slice( $defaults, 0, $title, true ),
				array( 'wpmoly-poster' => __( 'Poster', 'wpmovielibrary' ) ),
				array_slice( $defaults, $title, $comments, true ),
				array( 'wpmoly-release_date' => sprintf( '<span class="wpmolicon icon-date" title="%s"></span>', __( 'Year', 'wpmovielibrary' ) ) ),
				array( 'wpmoly-status'       => sprintf( '<span class="wpmolicon icon-status" title="%s"></span>', __( 'Status', 'wpmovielibrary' ) ) ),
				array( 'wpmoly-media'        => sprintf( '<span class="wpmolicon icon-video" title="%s"></span>', __( 'Media', 'wpmovielibrary' ) ) ),
				array( 'wpmoly-rating'       => __( 'Rating', 'wpmovielibrary' ) ),
				array_slice( $defaults, $comments, count( $defaults ), true )
			);

			unset( $defaults['author'] );
			return $defaults;
		}

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert movies' poster set as featured image if available.
		 * 
		 * @since    1.0
		 * 
		 * @param    string   $column_name The column name
		 * @param    int      $post_id current movie's post ID
		 */
		public static function movies_columns_content( $column_name, $post_id ) {

			$_column_name = str_replace( 'wpmoly-', '', $column_name );
			switch ( $column_name ) {
				case 'wpmoly-poster':
					$html = get_the_post_thumbnail( $post_id, 'thumbnail' );
					break;
				case 'wpmoly-release_date':
					$meta = wpmoly_get_movie_meta( $post_id, 'release_date' );
					$html = apply_filters( 'wpmoly_format_movie_release_date', $meta, 'Y' );
					break;
				case 'wpmoly-status':
					$meta = call_user_func_array( 'wpmoly_get_movie_meta', array( 'post_id' => $post_id, 'meta' => $_column_name ) );
					$html = apply_filters( 'wpmoly_format_movie_status', $meta, $format = 'html', $icon = true );
					break;
				case 'wpmoly-media':
					$meta = call_user_func_array( 'wpmoly_get_movie_meta', array( 'post_id' => $post_id, 'meta' => $_column_name ) );
					$html = apply_filters( 'wpmoly_format_movie_media', $meta, $format = 'html', $icon = true );
					break;
				case 'wpmoly-rating':
					$meta = wpmoly_get_movie_rating( $post_id );
					$html = apply_filters( 'wpmoly_movie_rating_stars', $meta, $post_id, $base = 5 );
					break;
				default:
					$html = '';
					break;
			}

			echo $html;
		}

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert movies' poster set as featured image if available.
		 * 
		 * @since    2.0
		 * 
		 * @param    array    $column_name The column name
		 * 
		 * @return   array    $columns Updated the column name
		 */
		public static function movies_sortable_columns( $columns ) {

			$columns['wpmoly-release_date'] = 'wpmoly-release_date';
			$columns['wpmoly-status']       = 'wpmoly-status';
			$columns['wpmoly-media']        = 'wpmoly-media';
			$columns['wpmoly-rating']       = 'wpmoly-rating';

			return $columns;
		}

		/**
		 * 
		 * 
		 * @since    2.0
		 * 
		 * @param    object    $wp_query Current WP_Query instance
		 */
		public static function movies_sortable_columns_order( $wp_query ) {

			if ( ! is_admin() )
			    return false;

			$orderby = $wp_query->get( 'orderby' );
			$allowed = array( 'wpmoly-release_date', 'wpmoly-release_date', 'wpmoly-status', 'wpmoly-media', 'wpmoly-rating' );
			if ( in_array( $orderby, $allowed ) ) {
				$key = str_replace( 'wpmoly-', '_wpmoly_movie_', $orderby );
				$wp_query->set( 'meta_key', $key );
				$wp_query->set( 'orderby', 'meta_value_num' );
			}
		}

		/**
		 * Add new fields to Movies' Quick Edit form in Movies Lists to edit
		 * Movie Details directly from the list.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $column_name WP List Table Column name
		 * @param    string    $post_type Post type
		 */
		public static function quick_edit_movies( $column_name, $post_type ) {

			if ( 'movie' != $post_type || 'wpmoly-poster' != $column_name || 1 !== did_action( 'quick_edit_custom_box' ) )
				return false;

			self::quickbulk_edit( 'quick' );
		}

		/**
		 * Add new fields to Movies' Bulk Edit form in Movies Lists.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $column_name WP List Table Column name
		 * @param    string    $post_type Post type
		 */
		public static function bulk_edit_movies( $column_name, $post_type ) {

			if ( 'movie' != $post_type || 'wpmoly-poster' != $column_name || 1 !== did_action( 'bulk_edit_custom_box' ) )
				return false;

			self::quickbulk_edit( 'bulk' );
		}

		/**
		 * Generic function to show WPMOLY Quick/Bulk Edit form.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $type Form type, 'quick' or 'bulk'.
		 */
		private static function quickbulk_edit( $type ) {

			if ( ! in_array( $type, array( 'quick', 'bulk' ) ) )
				return false;

			$fields = array();
			$attributes = array(
				'check' => 'is_' . $type . 'edit'
			);

			$details = WPMOLY_Settings::get_supported_movie_details();
			foreach ( $details as $slug => $detail ) {
				$fields[ $slug ] = array(
					'title'   => $detail['title'],
					'icon'    => $detail['icon'],
					'multi'   => $detail['multi'],
					'options' => $detail['options']
				);
			}

			$attributes['fields'] = $fields;

			echo self::render_admin_template( 'edit-movies/quick-edit.php', $attributes, $require = 'always' );
		}

		/**
		 * Alter the Quick Edit link in Movies Lists to update the Movie Details
		 * current values.
		 * 
		 * @since    1.0
		 * 
		 * @param    array     $actions List of current actions
		 * @param    object    $post Current Post object
		 * 
		 * @return   string    Edited Post Actions
		 */
		public static function expand_quick_edit_link( $actions, $post ) {

			global $current_screen;

			if ( isset( $current_screen ) && ( ( $current_screen->id != 'edit-movie' ) || ( $current_screen->post_type != 'movie' ) ) )
				return $actions;

			$nonce    = wpmoly_create_nonce( 'set-quickedit-movie-details' );
			$details  = WPMOLY_Settings::get_supported_movie_details();
			$_details = array_keys( $details );

			foreach ( $_details as $i => $detail ) {
				$data = call_user_func_array( 'wpmoly_get_movie_meta', array( 'post_id' => $post->ID, 'meta' => $detail ) );
				if ( is_array( $data ) && isset( $details[ $detail ]['multi'] ) && true == $details[ $detail ]['multi'] )
					$data = '[' . implode( ',', array_map( create_function( '$d', 'return "\'" . $d . "\'";' ), $data ) ) . ']';
				else
					$data = "'{$data}'";
				$_details[ $i ] = sprintf( "{$detail}: %s" , $data );
			}

			$_details = '{' . implode( ', ', $_details ) . '}';

			$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
			$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
			$actions['inline hide-if-no-js'] .= " onclick=\"wpmoly_edit_movies.quick_edit({$_details}, '{$nonce}')\">"; 
			$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
			$actions['inline hide-if-no-js'] .= '</a>';

			return $actions;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                      Movie images Media Modal
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Handle dummy arguments for AJAX Query. Movie Edit page adds
		 * an extended WP Media Modal to allow user to pick up which
		 * images he wants to download from the current movie. Media Modals
		 * are filled with the latest uploaded media, in order to avoid
		 * this with restrict the selection in the Javascript part by 
		 * querying only the post's attachment. We also need the movie
		 * TMDb ID, so we pass it as a search query.
		 * 
		 * This method cleans up the options and add the TMDb ID to the
		 * WP Query to bypass wp_ajax_query_attachments() filtering of
		 * the query options.
		 * 
		 * @since    1.0
		 * 
		 * @param    array    $query List of options for the query
		 * 
		 * @return   array    Filtered list of query options
		 */
		public static function load_images_dummy_query_args( $query ) {

			if ( isset( $query['s'] ) && 1 == preg_match_all( '/^TMDb_ID=([0-9]+),type=(image|poster)$/i', $query['s'], $m ) ) {

				unset( $query['post__not_in'] );
				unset( $query['post_mime_type'] );
				unset( $query['post_status'] );
				unset( $query['s'] );

				$query['post_type'] = 'movie';
				$query['tmdb_id'] = $m[1][0];
				$query['tmdb_type'] = $m[2][0];

			}

			return $query;
		}

		/**
		 * This is the hijack trick. Use the 'the_posts' filter hook to
		 * determine whether we're looking for movie images or regular
		 * posts. If the query has a 'tmdb_id' field, images wanted, we
		 * load them. If not, it's just a regular Post query, return the
		 * Posts.
		 * 
		 * @since    1.0
		 * 
		 * @param    array    $posts Posts concerned by the hijack, should be only one
		 * @param    array    $wp_query concerned WP_Query instance
		 * 
		 * @return   array    Posts return by the query if we're not looking for movie images
		 */
		public static function the_posts_hijack( $posts, $wp_query ) {

			if ( ! is_null( $wp_query ) && isset( $wp_query->query['tmdb_id'] ) && isset( $wp_query->query['tmdb_type'] ) ) {

				$tmdb_id   = esc_attr( $wp_query->query['tmdb_id'] );
				$tmdb_type = esc_attr( $wp_query->query['tmdb_type'] );
				$paged     = intval( $wp_query->query['paged'] );
				$per_page  = intval( $wp_query->query['posts_per_page'] );

				if ( 'image' == $tmdb_type )
					$images = self::load_movie_images( $tmdb_id, $posts[0] );
				else if ( 'poster' == $tmdb_type )
					$images = self::load_movie_posters( $tmdb_id, $posts[0] );

				$images = array_slice( $images, ( ( $paged - 1 ) * $per_page ), $per_page );

				wp_send_json_success( $images );
			}

			return $posts;
		}

		/**
		 * Load the Movie Images and display a jsonified result.s
		 * 
		 * @since    1.0
		 * 
		 * @param    int      $tmdb_id Movie TMDb ID to fetch images
		 * @param    array    $post Related Movie Post
		 * 
		 * @return   array    Movie images
		 */
		public static function load_movie_images( $tmdb_id, $post ) {

			$images = WPMOLY_TMDb::get_movie_images( $tmdb_id );
			$images = apply_filters( 'wpmoly_jsonify_movie_images', $images, $post, 'image' );

			return $images;
		}

		/**
		 * Load the Movie Images and display a jsonified result.s
		 * 
		 * @since    1.0
		 * 
		 * @param    int      $tmdb_id Movie TMDb ID to fetch images
		 * @param    array    $post Related Movie Post
		 * 
		 * @return   array    Movie posters
		 */
		public static function load_movie_posters( $tmdb_id, $post ) {

			$posters = WPMOLY_TMDb::get_movie_posters( $tmdb_id );
			$posters = apply_filters( 'wpmoly_jsonify_movie_images', $posters, $post, 'poster' );

			return $posters;
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Metabox
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Posts Metabox content callback.
		 * 
		 * @since    2.1.4
		 * 
		 * @param    object    Current Post object
		 */
		public static function metabox( $post, $args = array() ) {

			if ( 'movie' == $post->post_type ) {
				self::movie_metabox( $post, $args );
			} else {
				self::standard_metabox( $post, $args );
			}
		}

		/**
		 * Movie Metabox content callback.
		 * 
		 * @since    2.1.4
		 * 
		 * @param    object    $post Current Post object
		 * @param    array     $args Metabox parameters
		 */
		public static function standard_metabox( $post, $args = array() ) {

			global $wp_post_types;

			$post_type = $post->post_type;
			$post_id   = $post->ID;

			$post_types = wpmoly_o( 'convert-post-types' );

			if ( in_array( $post_type, $post_types ) ) {
				if ( isset( $wp_post_types[ $post_type ]->labels->singular_name ) ) {
					$post_type = $wp_post_types[ $post_type ]->labels->singular_name;
				}
			} else {
				$post_type = null;
			}

			$attributes = compact( 'post_type', 'post_id' );

			echo self::render_admin_template( 'metabox/metabox.php', $attributes );
		}

		/**
		 * Movie Metabox content callback.
		 * 
		 * @since    2.0
		 * 
		 * @param    object    $post Current Post object
		 * @param    array     $args Metabox parameters
		 */
		public static function movie_metabox( $post, $args = array() ) {

			$defaults = array(
				'panels' => array()
			);
			$args = wp_parse_args( $args['args'], $defaults );

			/**
			 * Filter the Metabox Panels to add/remove tabs.
			 *
			 * This should be used through Plugins to create additionnal
			 * Metabox panels.
			 *
			 * @since    2.0
			 *
			 * @param    array    $wpmoly_metabox_panels Existing Panels
			 */
			$args['panels'] = apply_filters( 'wpmoly_filter_metabox_panels', $args['panels'] );

			$tabs   = array();
			$panels = array();

			foreach ( $args['panels'] as $id => $panel ) {

				if ( ! is_callable( $panel['callback'] ) )
					continue;

				$is_active = ( true === $panel['default'] );
				$tabs[ $id ] = array(
					'title'  => $panel['title'],
					'icon'   => $panel['icon'],
					'active' => $is_active ? ' active' : ''
				);
				$panels[ $id ] = array( 
					'active'  => $is_active ? ' active' : '',
					'content' => call_user_func_array( $panel['callback'], array( $post->ID ) )
				);
			}

			$attributes = array(
				'tabs'      => $tabs,
				'panels'    => $panels,
				'post_type' => ( 'movie' != get_post_type() ? get_post_type : null )
			);

			echo self::render_admin_template( 'metabox/metabox.php', $attributes );
		}

		/**
		 * Movie Metabox Preview Panel.
		 * 
		 * Display a Metabox panel to preview metadata.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_preview_panel( $post_id ) {

			$rating   = wpmoly_get_movie_rating( $post_id );
			$metadata = wpmoly_get_movie_meta( $post_id );
			$metadata = wpmoly_filter_empty_array( $metadata );

			$preview  = array();
			$empty    = (bool) ( isset( $metadata['_empty'] ) && 1 == $metadata['_empty'] );

			if ( $empty )
				$preview = array(
					'title'          => '<span class="lipsum">Lorem ipsum dolor</span>',
					'original_title' => '<span class="lipsum">Lorem ipsum dolor sit amet</span>',
					'genres'         => '<span class="lipsum">Lorem, ipsum, dolor, sit, amet</span>',
					'release_date'   => '<span class="lipsum">2016</span>',
					'rating'         => '<span class="lipsum">0-0</span>',
					'overview'       => '<span class="lipsum">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mattis fermentum eros, et rhoncus enim cursus vitae. Nullam interdum mi feugiat, tempor turpis ac, viverra lorem. Nunc placerat sapien ut vehicula iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lacinia augue pharetra orci porta, nec posuere lectus accumsan. Mauris porttitor posuere lacus, sit amet auctor nibh congue eu.</span>',
					'director'       => '<span class="lipsum">Lorem ipsum</span>',
					'cast'           => '<span class="lipsum">Lorem, ipsum, dolor, sit, amet, consectetur, adipiscing, elit, mattis, fermentum, eros, rhoncus, cursus, vitae</span>',
					
				);
			else
				foreach ( $metadata as $slug => $meta )
					$preview[ $slug ] = call_user_func( 'apply_filters', "wpmoly_format_movie_{$slug}", $meta );

			$attributes = array(
				'empty'     => $empty,
				'thumbnail' => get_the_post_thumbnail( $post_id, 'medium' ),
				'rating'    => apply_filters( 'wpmoly_movie_rating_stars', $rating, $post_id, $base = 5 ),
				'preview'   => $preview
			);

			$panel = self::render_admin_template( 'metabox/panels/panel-preview.php', $attributes );

			return $panel;
		}

		/**
		 * Movie Metabox Meta Panel.
		 * 
		 * Display a Metabox panel to download movie metadata.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_meta_panel( $post_id ) {

			$metas     = WPMOLY_Settings::get_supported_movie_meta();
			$languages = WPMOLY_Settings::get_supported_languages();
			$metadata  = wpmoly_get_movie_meta( $post_id );
			$metadata  = wpmoly_filter_empty_array( $metadata );

			$attributes = array(
				'languages' => $languages,
				'metas'     => $metas,
				'metadata'  => $metadata
			);

			$panel = self::render_admin_template( 'metabox/panels/panel-meta.php', $attributes );

			return $panel;
		}

		/**
		 * Movie Metabox Details Panel.
		 * 
		 * Display a Metabox panel to edit movie details.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_details_panel( $post_id ) {

			$details = WPMOLY_Settings::get_supported_movie_details();
			$class   = new ReduxFramework();

			foreach ( $details as $slug => $detail ) {

				if ( isset( $detail['panel'] ) && 'custom' == $detail['panel'] ) {
					unset( $details[ $slug ] );
					continue;
				}

				$field_name = $detail['type'];
				$class_name = "ReduxFramework_{$field_name}";
				$value      = call_user_func_array( 'wpmoly_get_movie_meta', array( 'post_id' => $post_id, 'meta' => $slug ) );

				if ( ! class_exists( $class_name ) )
					require_once WPMOLY_PATH . "includes/framework/redux/ReduxCore/inc/fields/{$field_name}/field_{$field_name}.php";

				$field = new $class_name( $detail, $value, $class );

				ob_start();
				$field->render();
				$html = ob_get_contents();
				ob_end_clean();

				$details[ $slug ]['html'] = $html;
			}

			$attributes = array( 'details' => $details );

			$panel = self::render_admin_template( 'metabox/panels/panel-details.php', $attributes );

			return $panel;
		}

		/**
		 * Movie Images Metabox Panel.
		 * 
		 * Display a Metabox panel to download movie images.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_images_panel( $post_id ) {

			global $wp_version;

			$attributes = array(
				'nonce'   => wpmoly_nonce_field( 'upload-movie-image', $referer = false ),
				'images'  => WPMOLY_Media::get_movie_imported_images(),
				'version' => ( version_compare( $wp_version, '4.0', '>=' ) ? 4 : 0 )
			);

			$panel = self::render_admin_template( 'metabox/panels/panel-images.php', $attributes  );

			return $panel;
		}

		/**
		 * Movie Posters Metabox Panel.
		 * 
		 * Display a Metabox panel to download movie posters.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_posters_panel( $post_id ) {

			global $wp_version;

			$attributes = array(
				'posters' => WPMOLY_Media::get_movie_imported_posters(),
				'version' => ( version_compare( $wp_version, '4.0', '>=' ) ? 4 : 0 )
			);

			$panel = self::render_admin_template( 'metabox/panels/panel-posters.php', $attributes  );

			return $panel;
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Save data
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Save movie details.
		 * 
		 * @since    1.0
		 * 
		 * @param    int      $post_id ID of the current Post
		 * @param    array    $details Movie details: media, status, rating
		 * 
		 * @return   int|object    WP_Error object is anything went
		 *                                  wrong, true else
		 */
		public static function save_movie_details( $post_id, $details ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', __( 'Error: submitted post is not a movie.', 'wpmovielibrary' ) );

			$details    = self::validate_details( $details );
			$supported  = WPMOLY_Settings::get_supported_movie_details();

			if ( ! is_array( $details ) )
				return new WP_Error( 'invalid_details', __( 'Error: the submitted movie details are invalid.', 'wpmovielibrary' ) );

			foreach ( $details as $slug => $detail )
				update_post_meta( $post_id, "_wpmoly_movie_{$slug}", $detail );

			WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return $post_id;
		}

		/**
		 * Save movie metadata.
		 * 
		 * @since    1.3
		 * 
		 * @param    int      $post_id ID of the current Post
		 * @param    array    $details Movie details: media, status, rating
		 * 
		 * @return   int|object    WP_Error object is anything went wrong, true else
		 */
		public static function save_movie_meta( $post_id, $movie_meta, $clean = true ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', __( 'Error: submitted post is not a movie.', 'wpmovielibrary' ) );

			$movie_meta = self::validate_meta( $movie_meta );
			unset( $movie_meta['post_id'] );

			foreach ( $movie_meta as $slug => $meta )
				$update = update_post_meta( $post_id, "_wpmoly_movie_{$slug}", $meta );

			if ( false !== $clean )
				WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return $post_id;
		}

		/**
		 * Filter the Movie Metadata submitted when saving a post to
		 * avoid storing unexpected data to the database.
		 * 
		 * The Metabox array makes a distinction between pure metadata
		 * and crew data, so we filter them separately. If the data slug
		 * is valid, the value is escaped and added to the return array.
		 * 
		 * @since    1.0
		 * 
		 * @param    array    $data The Movie Metadata to filter
		 * 
		 * @return   array    The filtered Metadata
		 */
		private static function validate_meta( $data ) {

			if ( ! is_array( $data ) || empty( $data ) || ! isset( $data['tmdb_id'] ) )
				return $data;

			$data = wpmoly_filter_empty_array( $data );
			$data = wpmoly_filter_undimension_array( $data );

			$supported = WPMOLY_Settings::get_supported_movie_meta();
			$keys = array_keys( $supported );
			$movie_tmdb_id = esc_attr( $data['tmdb_id'] );
			$movie_post_id = ( isset( $data['post_id'] ) && '' != $data['post_id'] ? esc_attr( $data['post_id'] ) : null );
			$movie_poster = ( isset( $data['poster'] ) && '' != $data['poster'] ? esc_attr( $data['poster'] ) : null );
			$movie_meta = array();

			foreach ( $data as $slug => $_meta ) {
				if ( in_array( $slug, $keys ) ) {
					$filter = ( isset( $supported[ $slug ]['filter'] ) && function_exists( $supported[ $slug ]['filter'] ) ? $supported[ $slug ]['filter'] : 'esc_html' );
					$args   = ( isset( $supported[ $slug ]['filter_args'] ) && ! is_null( $supported[ $slug ]['filter_args'] ) ? $supported[ $slug ]['filter_args'] : null );
					$movie_meta[ $slug ] = call_user_func( $filter, $_meta, $args );
				}
			}

			$_data = array_merge(
				array(
					'tmdb_id' => $movie_tmdb_id,
					'post_id' => $movie_post_id,
					'poster'  => $movie_poster
				),
				$movie_meta
			);

			return $_data;
		}

		/**
		 * Filter the Movie Details submitted when saving a post to
		 * avoid storing unexpected data to the database.
		 * 
		 * @since    2.1
		 * 
		 * @param    array    $data The Movie Details to filter
		 * 
		 * @return   array    The filtered Details
		 */
		private static function validate_details( $data ) {

			if ( ! is_array( $data ) || empty( $data ) )
				return $data;

			$data = wpmoly_filter_empty_array( $data );

			$supported = WPMOLY_Settings::get_supported_movie_details();
			$movie_details = array();

			foreach ( $supported as $slug => $detail ) {

				if ( isset( $data[ $slug ] ) ) {

					$_detail = $data[ $slug ];
					if ( is_array( $_detail ) && 1 == $detail['multi'] ) {

						$_d = array();
						foreach ( $_detail as $d )
							if ( in_array( $d, array_keys( $detail['options'] ) ) )
								$_d[] = $d;

						$movie_details[ $slug ] = $_d;
					} else if ( 'select' == $detail['type'] && in_array( $_detail, array_keys( $detail['options'] ) ) ) {
						$movie_details[ $slug ] = $_detail;
					} else if ( 'text' == $detail['type'] ) {
						$movie_details[ $slug ] = $_detail;
					}
				}
				else {
					$movie_details[ $slug ] = null;
				}
			}

			return $movie_details;
		}

		/**
		 * Remove movie meta and taxonomies.
		 * 
		 * @since    1.2
		 * 
		 * @param    int      $post_id ID of the current Post
		 * 
		 * @return   boolean  Always return true
		 */
		public static function empty_movie_meta( $post_id ) {

			wp_delete_object_term_relationships( $post_id, array( 'collection', 'genre', 'actor' ) );
			delete_post_meta( $post_id, '_wpmoly_movie_data' );

			return true;
		}

		/**
		 * Save TMDb fetched data.
		 * 
		 * Uses the 'save_post_movie' action hook to save the movie metadata
		 * as a postmeta. This method is used in regular post creation as
		 * well as in movie import. If no $movie_meta is passed, we're 
		 * most likely creating a new movie, use $_REQUEST to get the data.
		 * 
		 * Saves the movie details as well.
		 *
		 * @since    1.0
		 * 
		 * @param    int        $post_ID ID of the current Post
		 * @param    object     $post Post Object of the current Post
		 * @param    boolean    $queue Queued movie?
		 * @param    array      $movie_meta Movie Metadata to save with the post
		 * 
		 * @return   int|WP_Error
		 */
		public static function save_movie( $post_ID, $post, $queue = false, $movie_meta = null ) {

			if ( ! current_user_can( 'edit_post', $post_ID ) )
				return new WP_Error( __( 'You are not allowed to edit posts.', 'wpmovielibrary' ) );

			if ( ! $post = get_post( $post_ID ) || 'movie' != get_post_type( $post ) )
				return new WP_Error( sprintf( __( 'Posts with #%s is invalid or is not a movie.', 'wpmovielibrary' ), $post_ID ) );

			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return $post_ID;

			$errors = new WP_Error();

			if ( ! is_null( $movie_meta ) && count( $movie_meta ) ) {

				// Save TMDb data
				self::save_movie_meta( $post_ID, $movie_meta );

				// Set poster as featured image
				if ( wpmoly_o( 'poster-featured' ) && ! $queue ) {
					$upload = WPMOLY_Media::set_image_as_featured( $movie_meta['poster'], $post_ID, $movie_meta['tmdb_id'], $movie_meta['title'] );
					if ( is_wp_error( $upload ) )
						$errors->add( $upload->get_error_code(), $upload->get_error_message() );
					else
						update_post_meta( $post_ID, '_thumbnail_id', $upload );
				}

				// Switch status from import draft to published
				if ( 'import-draft' == get_post_status( $post_ID ) && ! $queue ) {
					$update = wp_update_post(
						array(
							'ID' => $post_ID,
							'post_name'   => sanitize_title_with_dashes( $movie_meta['title'] ),
							'post_status' => 'publish',
							'post_title'  => $movie_meta['title'],
							'post_date'   => current_time( 'mysql' )
						),
						$wp_error = true
					);
					if ( is_wp_error( $update ) )
						$errors->add( $update->get_error_code(), $update->get_error_message() );
				}

				// Autofilling Actors
				if ( wpmoly_o( 'enable-actor' ) && wpmoly_o( 'actor-autocomplete' ) ) {
					$limit = intval( wpmoly_o( 'actor-limit' ) );
					$actors = explode( ',', $movie_meta['cast'] );
					if ( $limit )
						$actors = array_slice( $actors, 0, $limit );
					$actors = wp_set_object_terms( $post_ID, $actors, 'actor', false );
				}

				// Autofilling Genres
				if ( wpmoly_o( 'enable-genre' ) && wpmoly_o( 'genre-autocomplete' ) ) {
					$genres = explode( ',', $movie_meta['genres'] );
					$genres = wp_set_object_terms( $post_ID, $genres, 'genre', false );
				}

				// Autofilling Collections
				if ( wpmoly_o( 'enable-collection' ) && wpmoly_o( 'collection-autocomplete' ) ) {
					$collections = explode( ',', $movie_meta['director'] );
					$collections = wp_set_object_terms( $post_ID, $collections, 'collection', false );
				}
			}
			else if ( isset( $_REQUEST['meta'] ) && '' != $_REQUEST['meta'] ) {

				self::save_movie_meta( $post_ID, $_POST['meta'] );
			}

			if ( isset( $_REQUEST['wpmoly_details'] ) && ! is_null( $_REQUEST['wpmoly_details'] ) ) {

				if ( isset( $_REQUEST['is_quickedit'] ) || isset( $_REQUEST['is_bulkedit'] ) )
					wpmoly_check_admin_referer( 'quickedit-movie-details' );

				$wpmoly_details = $_REQUEST['wpmoly_details'];
				if ( true === $_REQUEST['is_bulkedit'] ) {
					foreach ( $_REQUEST['post'] as $post_id ) {
						self::save_movie_details( $post_id, $wpmoly_details );
					}
				} else {
					self::save_movie_details( $post_ID, $wpmoly_details );
				}
			}

			WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return ( ! empty( $errors->errors ) ? $errors : $post_ID );
		}

		/**
		 * If a movie's post is considered "empty" and post_title is
		 * empty, bypass WordPress empty content safety to avoid losing
		 * imported metadata. 'wp_insert_post_data' filter will later
		 * update the post_title to the correct movie title.
		 * 
		 * @since    2.0
		 * 
		 * @param    bool     $maybe_empty Whether the post should be considered "empty".
		 * @param    array    $postarr     Array of post data.
		 * 
		 * @return   boolean
		 */
		public static function filter_empty_content( $maybe_empty, $postarr ) {

			if ( ! isset( $postarr['post_type'] ) || 'movie' != $postarr['post_type'] )
				return $maybe_empty;

			if ( '' == trim( $postarr['post_title'] ) )
				return false;
		}

		/**
		 * Filter slashed post data just before it is inserted into the
		 * database. If an empty movie title is detected, and metadata
		 * contains a title, use it for post_title; if no movie title
		 * can be found, just use (no title) for post_title.
		 * 
		 * @since    2.0
		 * 
		 * @param    array    $data    An array of slashed post data.
		 * @param    array    $postarr An array of sanitized, but otherwise unmodified post data.
		 * 
		 * @return   array    Updated $data
		 */
		public static function filter_empty_title( $data, $postarr ) {

			if ( '' != $data['post_title'] || ! isset( $data['post_type'] ) || 'movie' != $data['post_type'] || in_array( $data['post_status'], array( 'import-queued', 'import-draft' ) ) )
				return $data;

			$no_title   = __( '(no title)' );
			$post_title = $no_title;
			if ( isset( $postarr['meta']['title'] ) && trim( $postarr['meta']['title'] ) )
				$post_title = $postarr['meta']['title'];

			if ( $post_title != $no_title && ! in_array( $data['post_status'], array( 'draft', 'pending', 'auto-draft' ) ) )
				$data['post_name'] = sanitize_title( $post_title );

			$data['post_title'] = $post_title;

			return $data;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}
		
	}
	
endif;