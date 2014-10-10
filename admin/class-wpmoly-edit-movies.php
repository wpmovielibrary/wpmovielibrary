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
 * @copyright 2014 CaerCam.org
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

			add_action( 'admin_footer', array( $this, 'edit_details_inline' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 9 );

			add_filter( 'manage_movie_posts_columns', __CLASS__ . '::movies_columns_head' );
			add_action( 'manage_movie_posts_custom_column', __CLASS__ . '::movies_columns_content', 10, 2 );
			add_action( 'quick_edit_custom_box', __CLASS__ . '::quick_edit_movies', 10, 2 );
			add_action( 'bulk_edit_custom_box', __CLASS__ . '::bulk_edit_movies', 10, 2 );
			add_filter( 'post_row_actions', __CLASS__ . '::expand_quick_edit_link', 10, 2 );

			add_action( 'the_posts', __CLASS__ . '::the_posts_hijack', 10, 2 );
			add_action( 'ajax_query_attachments_args', __CLASS__ . '::load_images_dummy_query_args', 10, 1 );

			add_action( 'add_meta_boxes_movie', __CLASS__ . '::add_meta_boxes', 10 );
			add_action( 'save_post_movie', __CLASS__ . '::save_movie', 10, 4 );

			add_action( 'wp_ajax_wpmoly_set_detail', __CLASS__ . '::set_detail_callback' );
			add_action( 'wp_ajax_wpmoly_save_details', __CLASS__ . '::save_details_callback' );
			add_action( 'wp_ajax_wpmoly_empty_meta', __CLASS__ . '::empty_meta_callback' );
		}

		/**
		 * Enqueue required media scripts and styles
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $hook_suffix The current admin page.
		 */
		public function admin_enqueue_scripts( $hook ) {

			if ( ( 'post.php' != $hook && 'post-new.php' != $hook ) || 'movie' != get_post_type() )
				return false;

			wp_enqueue_media();
			wp_enqueue_script( 'media-grid' );
			wp_enqueue_script( 'media' );
			wp_localize_script( 'media-grid', '_wpMediaGridSettings', array(
				'adminUrl' => parse_url( self_admin_url(), PHP_URL_PATH ),
			) );

			wp_register_script( 'select2-sortable-js', ReduxFramework::$_url . 'assets/js/vendor/select2.sortable.min.js', array( 'jquery' ), WPMOLY_VERSION, true );
			wp_register_script( 'select2-js', ReduxFramework::$_url . 'assets/js/vendor/select2/select2.min.js', array( 'jquery', 'select2-sortable-js' ), WPMOLY_VERSION, true );
			wp_enqueue_script( 'field-select-js', ReduxFramework::$_url . 'inc/fields/select/field_select.min.js', array( 'jquery', 'select2-js' ), WPMOLY_VERSION, true );
			wp_enqueue_style( 'select2-css', ReduxFramework::$_url . 'assets/js/vendor/select2/select2.css', array(), WPMOLY_VERSION, 'all' );
			wp_enqueue_style( 'redux-field-select-css', ReduxFramework::$_url . 'inc/fields/select/field_select.css', WPMOLY_VERSION, true );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                      "All Movies" WP List Table
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		public function edit_details_inline() {

			if ( 'edit-movie' != get_current_screen()->id )
				return false;

			$attributes = array(
				'default_movie_media' => WPMOLY_Settings::get_available_movie_media(),
				'default_movie_status' => WPMOLY_Settings::get_available_movie_status(),
				'default_movie_rating' => WPMOLY_Settings::get_available_movie_rating()
			);

			echo self::render_admin_template( 'edit-movies/edit-details-inline.php', $attributes );
		}

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert a simple 'Poster' column to Movies list table to display
		 * movies' poster set as featured image if available.
		 * 
		 * @since     1.0.0
		 * 
		 * @param     array    $defaults Default WP_List_Table header columns
		 * 
		 * @return    array    Default columns with new poster column
		 */
		public static function movies_columns_head( $defaults ) {

			$title = array_search( 'title', array_keys( $defaults ) );
			$comments = array_search( 'comments', array_keys( $defaults ) ) - 1;

			$defaults = array_merge(
				array_slice( $defaults, 0, $title, true ),
				array( 'poster' => __( 'Poster', 'wpmovielibrary' ) ),
				array_slice( $defaults, $title, $comments, true ),
				array( 'movie_release_date' => __( 'Year', 'wpmovielibrary' ) ),
				array( 'movie_status' => __( 'Status', 'wpmovielibrary' ) ),
				array( 'movie_media' => __( 'Media', 'wpmovielibrary' ) ),
				array( 'movie_rating' => __( 'Rating', 'wpmovielibrary' ) ),
				array_slice( $defaults, $comments, count( $defaults ), true )
			);

			unset( $defaults['author'] );
			return $defaults;
		}

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert movies' poster set as featured image if available.
		 * 
		 * TODO: use wpmoly_get_movie_meta()
		 * 
		 * @since     1.0.0
		 * 
		 * @param     string   $column_name The column name
		 * @param     int      $post_id current movie's post ID
		 */
		public static function movies_columns_content( $column_name, $post_id ) {

			switch ( $column_name ) {
				case 'poster':
					$html = get_the_post_thumbnail( $post_id, 'thumbnail' );
					break;
				case 'movie_release_date':
					$meta = wpmoly_get_movie_meta( $post_id, 'release_date' );
					$html = apply_filters( 'wpmoly_format_movie_release_date', $meta, 'Y' );
					break;
				case 'movie_status':
				case 'movie_media':
					$meta = call_user_func( "wpmoly_get_{$column_name}", $post_id );
					$_details = WPMOLY_Settings::get_supported_movie_details();
					if ( isset( $_details[ $column_name ]['options'][ $meta ] ) ) {
						$html = $_details[ $column_name ]['options'][ $meta ];
						$html = '<span class="' . $column_name . '_title">' . __( $html, 'wpmovielibrary' ) . '</span>';
					}
					else
						$html = '<span class="' . $column_name . '_title"><em>' . __( 'None', 'wpmovielibrary' ) . '</em></span>';
					$html .= '<a href="#" class="wpmoly-inline-edit-toggle hide-if-no-js" onclick="wpmoly_edit_details.inline_editor( \'' . str_replace( 'movie_', '', $column_name ) . '\', this ); return false;"><span class="wpmolicon icon-cog"></span></a>';
					break;
				case 'movie_rating':
					$meta = wpmoly_get_movie_rating( $post_id );
					$html = apply_filters( 'wpmoly_editable_rating_stars', $meta, $post_id );
					$html .= '<a href="#" class="wpmoly-inline-edit-toggle hide-if-no-js" onclick="$(this).prev(\'.wpmoly-movie-rating\').toggleClass(\'wpmoly-movie-editable-rating\'); return false;"><span class="wpmolicon icon-cog"></span></a>';
					break;
				default:
					$html = '';
					break;
			}

			echo $html;
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

			if ( 'movie' != $post_type || 'poster' != $column_name || 1 !== did_action( 'quick_edit_custom_box' ) )
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

			if ( 'movie' != $post_type || 'poster' != $column_name || 1 !== did_action( 'bulk_edit_custom_box' ) )
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

			$attributes = array(
				'default_movie_media' => WPMOLY_Settings::get_available_movie_media(),
				'default_movie_status' => WPMOLY_Settings::get_available_movie_status(),
				'check' => 'is_' . $type . 'edit'
			);

			echo self::render_admin_template( 'edit-movies/quick-edit.php', $attributes );
		}

		/**
		 * Alter the Quick Edit link in Movies Lists to update the Movie Details
		 * current values.
		 * 
		 * TODO: group Details in a single, cached query.
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

			$nonce = wpmoly_create_nonce( 'set-quickedit-movie-details' );

			$details = '{';
			$details .= 'movie_id: ' . $post->ID . ',';
			$details .= 'movie_media: \'' . get_post_meta( $post->ID, '_wpmoly_movie_media', TRUE ) . '\',';
			$details .= 'movie_status: \'' . get_post_meta( $post->ID, '_wpmoly_movie_status', TRUE ) . '\',';
			$details .= 'movie_rating: \'' . get_post_meta( $post->ID, '_wpmoly_movie_rating', TRUE ) . '\'';
			$details .= '}';

			$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
			$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
			$actions['inline hide-if-no-js'] .= " onclick=\"wpmoly_edit_movies.quick_edit({$details}, '{$nonce}')\">"; 
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
		 * @param    array    $_query concerned WP_Query instance
		 * 
		 * @return   array    Posts return by the query if we're not looking for movie images
		 */
		public static function the_posts_hijack( $posts, $_query ) {

			if ( ! is_null( $_query ) && isset( $_query->query['tmdb_id'] ) && isset( $_query->query['tmdb_type'] ) ) {

				$tmdb_id = esc_attr( $_query->query['tmdb_id'] );
				$tmdb_type = esc_attr( $_query->query['tmdb_type'] );
				$paged = intval( $_query->query['paged'] );
				$per_page = intval( $_query->query['posts_per_page'] );

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
		 */
		public static function load_movie_posters( $tmdb_id, $post ) {

			$posters = WPMOLY_TMDb::get_movie_posters( $tmdb_id );
			$posters = apply_filters( 'wpmoly_jsonify_movie_images', $posters, $post, 'poster' );

			return $posters;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Callbacks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * "All Movies" WP List Table inline edit shortcut for details.
		 *
		 * @since    1.0
		 */
		public static function set_detail_callback() {

			$detail = ( isset( $_POST['type'] ) && in_array( $_POST['type'], array( 'status', 'media', 'rating' ) ) ? esc_attr( $_POST['type'] ) : null );
			if ( is_null( $detail ) )
				return new WP_Error( 'invalid', __( 'Invalid detail: should be status, media or rating.', 'wpmovielibrary' ) );

			wpmoly_check_ajax_referer( $detail . '-inline-edit' );

			$post_id = ( isset( $_POST['post_id'] ) && '' != $_POST['post_id'] ? intval( $_POST['post_id'] ) : null );
			$value = ( isset( $_POST['data'] ) && '' != $_POST['data'] ? esc_attr( $_POST['data'] ) : null );

			if ( is_null( $post_id ) || is_null( $detail ) || is_null( $value ) )
				return new WP_Error( 'invalid', __( 'Empty or invalid Post ID or Movie Details', 'wpmovielibrary' ) );

			$response = self::set_movie_detail( $post_id, $detail, $value );

			wpmoly_ajax_response( $response, array(), wpmoly_create_nonce( $detail . '-inline-edit' ) );
		}

		/**
		 * Save movie details: media, status, rating.
		 * 
		 * Although values are submitted as array each value is stored in a
		 * dedicated post meta.
		 *
		 * @since    1.0
		 */
		public static function save_details_callback() {

			$post_id = ( isset( $_POST['post_id'] )      && '' != $_POST['post_id']      ? intval( $_POST['post_id'] ) : null );
			$details = ( isset( $_POST['wpmoly_details'] ) && '' != $_POST['wpmoly_details'] ? $_POST['wpmoly_details'] : null );

			if ( is_null( $post_id ) || is_null( $details ) )
				return new WP_Error( 'invalid', __( 'Empty or invalid Post ID or Movie Details', 'wpmovielibrary' ) );

			wpmoly_check_ajax_referer( 'save-movie-details' );

			$response = self::save_movie_details( $post_id, $details );

			wpmoly_ajax_response( $response, array(), wpmoly_create_nonce( 'save-movie-details' ) );
		}

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


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Meta Boxes
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Register WPMOLY Metaboxes
		 * 
		 * Alter $wp_meta_boxes to display the Details Metabox right below
		 * WordPress standard Submit Metabox.
		 * 
		 * @since    1.0
		 */
		public static function add_meta_boxes() {

			$metaboxes = WPMOLY_Settings::get_metaboxes();

			foreach ( $metaboxes as $i => $metabox ) {

				extract( $metabox, EXTR_OVERWRITE );

				if ( 'side' != $context )
					$id = "{$i}_{$id}";

				add_meta_box( $id, $title, $callback, $screen, $context, $priority,  $callback_args );
			}

			add_meta_box( 'redux', 'WordPress Movie Library', __CLASS__ . '::redux_meta', 'movie', 'normal', 'high', null );

			global $wp_meta_boxes;

			$details = $wp_meta_boxes['movie']['side']['core']['wpmoly_details'];
			$core = $wp_meta_boxes['movie']['side']['core'];
			$submit = $wp_meta_boxes['movie']['side']['core']['submitdiv'];

			unset( $core['wpmoly_details'], $core['submitdiv'] );

			$wp_meta_boxes['movie']['side']['core'] = array_merge(
				array( 'submitdiv' => $submit ),
				array( 'wpmoly_details' => $details ),
				$core
			);

		}

		public static function redux_meta( $post, $metabox ) {

			$metadata = wpmoly_get_movie_meta( $post->ID );
			$metadata = wpmoly_filter_empty_array( $metadata );
			$preview  = array();
			$empty    = (bool) ( isset( $metadata['_empty'] ) && 1 == $metadata['_empty'] );

			$_meta = WPMOLY_Settings::get_supported_movie_meta();
			$select = null;
			$status = '';
			$rating = wpmoly_get_movie_rating( $post->ID );

			// TODO: cleanup
			if ( isset( $_GET['wpmoly_search_movie'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'search-movies' ) && ( empty( $metadata ) || isset( $metadata['_empty'] ) ) ) {

				$search_by = ( isset( $_GET['search_by'] ) && in_array( $_GET['search_by'], array( 'title', 'id' ) ) ? $_GET['search_by'] : null );
				$search_query = ( isset( $_GET['search_query'] ) && '' != $_GET['search_query'] ? $_GET['search_query'] : null );

				if ( ! is_null( $search_by ) && ! is_null( $search_query ) )
					$metadata = call_user_func_array( array( 'WPMOLY_TMDb', "_get_movie_by_$search_by" ), array( $search_query, wpmoly_o( 'api-language' ) ) );

				if ( isset( $metadata['result'] ) ) {

					if ( 'movie' == $metadata['result'] )
						$metadata = $metadata['movies'][ 0 ];
					else if ( 'movies' == $metadata['result'] )
						$select = $metadata['movies'];
				}
			}

			if ( $empty )
				$metadata = array(
					'title'          => '<span class="lipsum">Lorem ipsum dolor</span>',
					'original_title' => '<span class="lipsum">Lorem ipsum dolor sit amet</span>',
					'genres'         => '<span class="lipsum">Lorem, ipsum, dolor, sit, amet</span>',
					'release_date'   => '<span class="lipsum">2014</span>',
					'rating'         => '<span class="lipsum">0-0</span>',
					'overview'       => '<span class="lipsum">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mattis fermentum eros, et rhoncus enim cursus vitae. Nullam interdum mi feugiat, tempor turpis ac, viverra lorem. Nunc placerat sapien ut vehicula iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lacinia augue pharetra orci porta, nec posuere lectus accumsan. Mauris porttitor posuere lacus, sit amet auctor nibh congue eu.</span>',
					'director'       => '<span class="lipsum">Lorem ipsum</span>',
					'cast'           => '<span class="lipsum">Lorem, ipsum, dolor, sit, amet, consectetur, adipiscing, elit, mattis, fermentum, eros, rhoncus, cursus, vitae</span>',
					
				);
			else
				foreach ( $metadata as $slug => $meta )
					$preview[ $slug ] = call_user_func( 'apply_filters', "wpmoly_format_movie_{$slug}", $meta );

			$attributes = array(
				'languages' => WPMOLY_Settings::get_available_languages(),
				'metas' => $_meta,
				'metadata' => $metadata,
				'preview' => $preview,
				'status' => $status,
				'rating' => $rating,
				'select' => $select,
				'thumbnail' => get_the_post_thumbnail( $post->ID, 'medium' ),
				'empty' => $empty
			);

			$attributes['preview'] = self::render_admin_template( 'metaboxes/movie-meta-preview.php', $attributes );
			$attributes['meta']    = self::render_admin_template( 'metaboxes/movie-meta-meta.php', $attributes );
			$attributes['details'] = self::render_admin_template( 'metaboxes/movie-meta-details.php', array( 'details' => self::render_details_metabox() ) );
			$attributes['images']  = self::render_admin_template( 'metaboxes/movie-meta-images.php', array() );

			echo self::render_admin_template( 'metaboxes/movie-meta2.php', $attributes );
		}

		private static function render_details_metabox() {

			global $wpmoly_movie_details;

			$class  = new ReduxFramework();

			foreach ( $wpmoly_movie_details as $slug => $detail ) {

				$field_name = $detail['type'];
				$class_name = "ReduxFramework_{$field_name}";
				$value      = '';
				if ( function_exists( "wpmoly_get_{$slug}" ) )
					$value = call_user_func( "wpmoly_get_{$slug}" );

				if ( ! class_exists( $class_name ) )
					require_once WPMOLY_PATH . "includes/framework/redux/ReduxCore/inc/fields/{$field_name}/field_{$field_name}.php";

				$field = new $class_name( $detail, $value, $class );

				ob_start();
				$field->render();
				$html = ob_get_contents();
				ob_end_clean();

				$wpmoly_movie_details[ $slug ]['html'] = $html;
			}

			return $wpmoly_movie_details;
		}

		/**
		 * Movie Images Metabox.
		 * 
		 * Display a large Metabox below post editor to fetch and edit movie
		 * informations using the TMDb API.
		 * 
		 * @since    1.0
		 * 
		 * @param    object    Current Post object
		 * @param    null      $metabox null
		 */
		public static function metabox_images( $post, $metabox ) {

			global $wp_version;
			$attributes = array(
				'nonce' => wpmoly_nonce_field( 'upload-movie-image', $referer = false ),
				'images' => WPMOLY_Media::get_movie_imported_images(),
				'version' => ( version_compare( $wp_version, '4.0', '>=' ) ? 4 : 0 )
			);

			echo self::render_admin_template( 'metaboxes/movie-images.php', $attributes );
		}

		/**
		 * Set specific movie detail.
		 * 
		 * @since     1.0.0
		 * 
		 * @param    int       $post_id ID of the current Post
		 * @param    string    $detail Movie detail: media, status or rating
		 * @param    string    $value Detail value
		 * 
		 * @return   int|object    WP_Error object is anything went
		 *                                  wrong, true else
		 */
		public static function set_movie_detail( $post_id, $detail, $value ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', sprintf( __( 'Error: invalid post, post #%s is not a movie.', 'wpmovielibrary' ), $post_id ) );

			if ( in_array( $detail, array( 'status', 'media', 'language', 'subtitles', 'format' ) ) ) {

				$allowed = call_user_func( "WPMOLY_Settings::get_available_movie_{$detail}" );
				if ( ! in_array( $value, array_keys( $allowed ) ) )
					return new WP_Error( 'invalid_value', sprintf( __( 'Error: invalid value, allowed values for \'%s\' are %s.', 'wpmovielibrary' ), $detail, implode( ', ', array_keys( $allowed ) ) ) );

				update_post_meta( $post_id, '_wpmoly_movie_' . $detail, $value );
				$updated = get_post_meta( $post_id, '_wpmoly_movie_' . $detail, true );
				if ( '' == $updated || $value != $updated )
					return new WP_Error( 'update_error', __( 'Error: couldn\'t update movie detail.', 'wpmovielibrary' ) );
			}
			else if ( 'rating' == $detail ) {
				$value = number_format( $value, 1 );
				if ( 0 > floor( $value ) || 5 < ceil( $value ) )
					return new WP_Error( 'invalid_value', sprintf( __( 'Error: invalid value, allowed values for \'rating\' are floats between 0.0 and 5.0.', 'wpmovielibrary' ) ) );

				update_post_meta( $post_id, '_wpmoly_movie_' . $detail, $value );
				$updated = get_post_meta( $post_id, '_wpmoly_movie_' . $detail, true );
				if ( '' == $updated || 0 != abs( $value - number_format( $updated, 1 ) ) )
					return new WP_Error( 'update_error', __( 'Error: couldn\'t update movie detail.', 'wpmovielibrary' ) );
			}

			WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return $post_id;
		}

		/**
		 * Save movie details.
		 * 
		 * TODO: Use some iteration
		 * 
		 * @since     1.0.0
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

			if ( ! is_array( $details )
			  || ! isset( $details['movie_media'] ) || ! isset( $details['movie_status'] ) || ! isset( $details['movie_rating'] )
			  || ! isset( $details['movie_language'] ) || ! isset( $details['movie_subtitles'] ) || ! isset( $details['movie_format'] ) )
				return new WP_Error( 'invalid_details', __( 'Error: the submitted movie details are invalid.', 'wpmovielibrary' ) );

			update_post_meta( $post_id, '_wpmoly_movie_media', $details['movie_media'] );
			update_post_meta( $post_id, '_wpmoly_movie_status', $details['movie_status'] );
			update_post_meta( $post_id, '_wpmoly_movie_rating', number_format( $details['movie_rating'], 1 ) );
			update_post_meta( $post_id, '_wpmoly_movie_language', $details['movie_language'] );
			update_post_meta( $post_id, '_wpmoly_movie_subtitles', $details['movie_subtitles'] );
			update_post_meta( $post_id, '_wpmoly_movie_format', $details['movie_format'] );

			WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return $post_id;
		}

		/**
		 * Save movie metadata.
		 * 
		 * @since     1.3
		 * 
		 * @param    int      $post_id ID of the current Post
		 * @param    array    $details Movie details: media, status, rating
		 * 
		 * @return   int|object    WP_Error object is anything went
		 *                                  wrong, true else
		 */
		public static function save_movie_meta( $post_id, $movie_meta, $clean = true ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', __( 'Error: submitted post is not a movie.', 'wpmovielibrary' ) );

			$movie_meta = self::validate_meta_data( $movie_meta );
			unset( $movie_meta['post_id'] );

			foreach ( $movie_meta as $slug => $meta )
				update_post_meta( $post_id, "_wpmoly_movie_{$slug}", $meta );

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
		 * @since    1.0.0
		 * 
		 * @param    array    $data The Movie Metadata to filter
		 * 
		 * @return   array    The filtered Metadata
		 */
		private static function validate_meta_data( $data ) {

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
		 * Remove movie meta and taxonomies.
		 * 
		 * @since     1.2
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
		 * @since     1.0.0
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
				self::save_movie_details( $post_ID, $wpmoly_details );
			}

			WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return ( ! empty( $errors->errors ) ? $errors : $post_ID );
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