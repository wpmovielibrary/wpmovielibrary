<?php

class WPML_TMDb extends WPMovieLibrary {

	/**
	 * TMDb API Settings
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $TMDb_settings = '';

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

		$this->tmdb = new TMDb( $this->APIKey, $this->lang, false, $this->scheme );
	}

	private function wpml_scheme_check() {
		if ( ! in_array( $this->scheme, array( 'http', 'https' ) ) )
			return 'https://';
		else
			return $this->scheme . '://';
	}

	private function wpml_api_key_check( $key ) {
		$_tmdb = new TMDb( $key, $this->lang, false, $this->scheme );
		$data = $_tmdb->getConfiguration();
		return $data;
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

	public function wpml_save_tmdb_image( $image, $post_id ) {
		//$upload = media_sideload_image( $image, $post_id );
		//return ( is_object( $upload ) ? false : true );
		return 'true';
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
			echo '<span>'.__( 'Invalid API key - You must be granted a valid key', 'wpml' ).'</span>';
		else
			echo '<span>'.__( 'Valid API key - Save your settings and have fun!', 'wpml' ).'</span>';

		die();
	}

	public function wpml_tmdb_save_image_callback() {

		$image   = ( isset( $_GET['image'] )   && '' != $_GET['image']   ? $_GET['image']   : '' );
		$post_id = ( isset( $_GET['post_id'] ) && '' != $_GET['post_id'] ? $_GET['post_id'] : '' );

		if ( '' == $image || '' == $post_id )
			return false;

		echo $this->wpml_save_tmdb_image( $image, $post_id );
		die();
	}

	public function wpml_tmdb_search_callback() {

		$type = ( isset( $_GET['type'] ) && '' != $_GET['type'] ? $_GET['type'] : '' );
		$data = ( isset( $_GET['data'] ) && '' != $_GET['data'] ? $_GET['data'] : '' );

		if ( '' == $data || '' == $type )
			return false;

		if ( 'title' == $type )
			$this->wpml_get_movie_by_title( $data );
		else if ( 'id' == $type )
			$this->wpml_get_movie_by_id( $data );

		die();
	}

	private function wpml_get_movie_by_title( $title ) {

		$data = $this->tmdb->searchMovie( $title );

		if ( 1 == $data['total_results'] ) {
			$this->wpml_get_movie_by_id( $data['results'][0]['id'] );
		}
		else if ( $data['total_results'] > 1 ) {

			$ret  = '<p><strong>';
			$ret .= __( 'Your request showed multiple results. Select your movie in the list or try another search:', 'wpml' );
			$ret .= '</strong></p>';

			foreach ( $data['results'] as $movie ) {

				$ret .= '<div class="tmdb_select_movie">';
				$ret .= '<a id="tmdb_'.$movie['id'].'" href="#">';

				if ( $movie['poster_path'] != null )
					$ret .= '<img src="http://d3gtl9l2a4fn1j.cloudfront.net/t/p/w150/'.$movie['poster_path'].'" alt="'.$movie['title'].'" />';
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

	private function wpml_get_movie_by_id( $id ) {

		$movie  = $this->tmdb->getMovie( $id );
		$casts  = $this->tmdb->getMovieCast( $id );
		$images = $this->tmdb->getMovieImages( $id, '' );
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
}