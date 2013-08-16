<?php

class WPML_TMDb extends WPMovieLibrary {

	/**
	 * TMDb API Key
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $APIKey = '';

	/**
	 * TMDb API Lang
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $lang = '';

	/**
	 * TMDb API Scheme
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $scheme = '';

	/**
	 * TMDb API Config
	 *
	 * @since   1.0.0
	 * @var     array
	 */
	protected $config = null;

	/**
	 * TMDb API
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $tmdb = '';

	public function __construct( $TMDb_settings ) {

		$this->APIKey = $TMDb_settings['APIKey'];
		$this->lang   = $TMDb_settings['lang'];
		$this->scheme = $TMDb_settings['scheme'];
		$this->scheme = $this->wpml_scheme_check();

		require_once plugin_dir_path( __FILE__ ) . 'class-tmdb.php';

		$this->tmdb   = new TMDb( $this->APIKey, $this->lang, false, $this->scheme );
		$this->config = $this->wpml_tmdb_config();
	}

	private function wpml_scheme_check() {
		if ( ! in_array( $this->scheme, array( 'http', 'https' ) ) )
			return 'https://';
		else
			return $this->scheme . '://';
	}

	private function wpml_tmdb_config() {
		$tmdb_config = $this->tmdb->getConfig();

		if ( is_null( $tmdb_config ) )
			return false;

		$base_url = ( 'https' == $this->scheme ? $tmdb_config['images']['secure_base_url'] : $tmdb_config['images']['base_url'] );

		$wpml_tmdb_config = array(
			'poster_url' => array(
				'xx-small' => $base_url . $tmdb_config['images']['poster_sizes'][0],
				'x-small'  => $base_url . $tmdb_config['images']['poster_sizes'][1],
				'small'    => $base_url . $tmdb_config['images']['poster_sizes'][2],
				'medium'   => $base_url . $tmdb_config['images']['poster_sizes'][3],
				'full'     => $base_url . $tmdb_config['images']['poster_sizes'][4],
				'original' => $base_url . $tmdb_config['images']['poster_sizes'][5]
			),
			'image_url' => array(
				'small'    => $base_url . $tmdb_config['images']['backdrop_sizes'][0],
				'medium'   => $base_url . $tmdb_config['images']['backdrop_sizes'][1],
				'full'     => $base_url . $tmdb_config['images']['backdrop_sizes'][2],
				'original' => $base_url . $tmdb_config['images']['backdrop_sizes'][3]
			),
		);

		return $wpml_tmdb_config;
	}

	private function wpml_api_key_check( $key ) {
		$_tmdb = new TMDb( $key, $this->lang, false, $this->scheme );
		$data = $_tmdb->getConfiguration();
		return $data;
	}

	public function wpml_tmdb_get_base_url( $type, $size ) {
		return $this->config[ $type . '_url' ][ $size ];
	}

	public function wpml_save_tmdb_data( $post_id ) {

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return false;

		if ( ! isset( $_POST['tmdb_data'] ) || '' == $_POST['tmdb_data'] )
			return false;

		$post = get_post( $post_id );
		if ( 'movie' != get_post_type( $post ) )
			return false;

		update_post_meta( $post_id, 'wpml_tmdb_data', $_POST['tmdb_data'] );
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                             Callbacks
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	public function wpml_tmdb_api_key_check_callback() {

		if ( ! isset( $_GET['key'] ) || '' == $_GET['key'] || 32 !== strlen( $_GET['key'] ) )
			die();

		$data = $this->wpml_api_key_check( esc_attr( $_GET['key'] ) );

		if ( isset( $data['status_code'] ) && 7 === $data['status_code'] )
			echo '<span id="api_status" class="invalid">'.__( 'Invalid API key - You must be granted a valid key', 'wpml' ).'</span>';
		else
			echo '<span id="api_status" class="valid">'.__( 'Valid API key - Save your settings and have fun!', 'wpml' ).'</span>';

		die();
	}

	public function wpml_tmdb_save_image_callback() {

		$image   = ( isset( $_GET['image'] )   && '' != $_GET['image']   ? $_GET['image']   : '' );
		$post_id = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : '' );
		$title   = ( isset( $_GET['title'] )   && '' != $_GET['title']   ? $_GET['title']   : '' );

		if ( '' == $image || '' == $post_id )
			return false;

		echo $this->wpml_image_upload( $image, $post_id, $title );
		die();
	}

	public function wpml_tmdb_search_callback() {

		$type = ( isset( $_GET['type'] ) && '' != $_GET['type'] ? $_GET['type'] : '' );
		$data = ( isset( $_GET['data'] ) && '' != $_GET['data'] ? $_GET['data'] : '' );
		$lang = ( isset( $_GET['lang'] ) && '' != $_GET['lang'] ? $_GET['lang'] : $this->wpml_o('tmdb-settings-lang') );

		if ( '' == $data || '' == $type )
			return false;

		if ( 'title' == $type )
			$this->wpml_get_movie_by_title( $data, $lang );
		else if ( 'id' == $type )
			$this->wpml_get_movie_by_id( $data, $lang );

		die();
	}

	public function wpml_tmdb_set_featured_callback() {

		$image   = ( isset( $_GET['image'] )   && '' != $_GET['image']   ? $_GET['image']   : '' );
		$post_id = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : '' );
		$title   = ( isset( $_GET['title'] )   && '' != $_GET['title']   ? $_GET['title']   : '' );

		if ( '' == $image || '' == $post_id || 1 != $this->wpml_o('tmdb-settings-poster_featured') )
			return false;

		echo $this->wpml_set_image_as_featured( $image, $post_id, $title );
		die();
	}


	/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 *
	 *                             Internal
	 * 
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	private function wpml_get_movie_by_title( $title, $lang ) {

		$data = $this->tmdb->searchMovie( $title, 1, $lang );

		if ( ! isset( $data['total_results'] ) ) {
			echo '<p><strong><em>'.__( 'I&rsquo;m Jack&rsquo;s empty result.', 'wpml' ).'</em></strong></p>';
			echo '<p>'.__( 'Sorry, your search returned no result. Try a more specific query?', 'wpml' ).'</p>';
		}
		else if ( 1 == $data['total_results'] ) {
			$this->wpml_get_movie_by_id( $data['results'][0]['id'], $lang );
		}
		else if ( $data['total_results'] > 1 ) {

			$ret  = '<p><strong>';
			$ret .= __( 'Your request showed multiple results. Select your movie in the list or try another search:', 'wpml' );
			$ret .= '</strong></p>';

			foreach ( $data['results'] as $movie ) {

				$ret .= '<div class="tmdb_select_movie">';
				$ret .= '<a id="tmdb_'.$movie['id'].'" href="#">';

				if ( $movie['poster_path'] != null )
					$ret .= '<img src="'.$this->config['poster_url'][$this->wpml_o('tmdb-settings-poster_size')].$movie['poster_path'].'" alt="'.$movie['title'].'" />';
				else
					$ret .= '<img src="'.$this->wpml_o('wpml-url').'/assets/no_poster.png" alt="'.$movie['title'].'" />';

				$ret .= '<em>'.$movie['title'].'</em>';
				$ret .= '<input type=\'hidden\' value=\''.json_encode( $movie ).'\' />';
				$ret .= '</div>';
			}

			echo $ret;
		}
		else {
			echo '<p><strong><em>'.__( 'I&rsquo;m Jack&rsquo;s empty result.', 'wpml' ).'</em></strong></p>';
			echo '<p>'.__( 'Sorry, your search returned no result. Try a more specific query?', 'wpml' ).'</p>';
		}
		
	}

	private function wpml_get_movie_by_id( $id, $lang ) {

		$movie  = $this->tmdb->getMovie( $id, $lang );
		$casts  = $this->tmdb->getMovieCast( $id );
		$images = $this->tmdb->getMovieImages( $id, '' );
		$poster = $images['posters'][0];
		$images = $images['backdrops'];

		$images_max = $this->wpml_o('tmdb-settings-images_max');
		if ( $images_max > 0 && count( $images ) > $images_max )
			$images = array_slice( $images, 0, $images_max );

		$images = array( 'images' => $images );

		foreach ( $casts['crew'] as $i => $c ) {
			switch ( $c['job'] ) {
				case 'Director':
					$movie['director'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				case 'Author':
					$movie['author'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				case 'Producer':
					$movie['production'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				case 'Director of Photography':
					$movie['photography'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				case 'Original Music Composer':
					$movie['soundtrack'][] = $c;
					unset( $casts['crew'][$i] );
					break;
				default:
					break;
			}
		}

		$movie = array_merge( $movie, $casts, $images );

		header('Content-type: application/json');
		echo json_encode( $movie );
	}

	/**
	 * Set the image as featured image.
	 * 
	 * @param int $image The ID of the image to set as featured
	 * @param int $post_id The post ID the image is to be associated with
	 * 
	 * @return string|WP_Error Populated HTML img tag on success
	 * 
	 * @since   1.0.0
	 */
	private function wpml_set_image_as_featured( $image, $post_id, $title ) {

		$size = $this->wpml_o('tmdb-settings-poster_size');
		$file = $this->config['poster_url'][ $size ] . $image;

		$image = $this->wpml_image_upload( $file, $post_id, $title );

		if ( is_object( $image ) )
			return false;
		else
			return $image;
	}

	/**
	 * Media Sideload Image revisited
	 * This is basically an override function for WP media_sideload_image
	 * modified to return the uploaded attachment ID instead of HTML img
	 * tag.
	 * 
	 * @see http://codex.wordpress.org/Function_Reference/media_sideload_image
	 * 
	 * @param string $file The URL of the image to download
	 * @param int $post_id The post ID the media is to be associated with
	 * @param string $title Optional. Title of the image
	 * 
	 * @return string|WP_Error Populated HTML img tag on success
	 * 
	 * @since   1.0.0
	 */
	private function wpml_image_upload( $file, $post_id, $title = null ) {

	        if ( empty( $file ) )
			return false;

		$tmp   = download_url( $file );

		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
		$file_array['name'] = basename( $matches[0] );
		$file_array['tmp_name'] = $tmp;

		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array['tmp_name'] );
			$file_array['tmp_name'] = '';
		}

		$id = media_handle_sideload( $file_array, $post_id, $title );
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return print_r( $id, true );
		}

		return $id;
	}


}