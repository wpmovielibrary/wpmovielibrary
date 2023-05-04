<?php
/**
 * WPMovieLibrary Movie Headbox Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Headbox_Allocine' ) ) :

	class WPMOLY_Headbox_Allocine extends WPMOLY_Headbox {

		/**
		 * Allocine Headbox Tabs.
		 *
		 * @since    2.1.4
		 * @var      array
		 */
		protected $tabs;

		/**
		 * Initialize Allocine Headbox.
		 *
		 * @since    2.1.4
		 */
		public function init() {

			$tabs = array(
				'main' =>  array(
					'title' => null,
					'icon'  => 'home',
					'content' => $this->render_main_tab()
				),
				'details' =>  array(
					'title' => __( 'Details', 'wpmovielibrary' ),
					'icon'  => 'meta',
					'content' => $this->render_details_tab()
				),
				'casting' =>  array(
					'title' => __( 'Casting', 'wpmovielibrary' ),
					'icon'  => 'actor',
					'content' => $this->render_casting_tab()
				),
				'photos' =>  array(
					'title' => __( 'Photos', 'wpmovielibrary' ),
					'icon'  => 'images',
					'content' => $this->render_images_tab()
				)
			);

			/**
			 * Filter the Headbox Default Tabs.
			 * 
			 * @since    2.1.4
			 * 
			 * @param    array    $tabs Default Headbox Tabs
			 */
			$this->tabs = apply_filters( 'wpmoly_filter_allocine_headbox_tabs', $tabs );

		}

		/**
		 * Render Allocine styled Headbox.
		 *
		 * @since    2.1.4
		 * 
		 * @param    string    $content The original post content
		 *
		 * @return   string    Filtered content
		 */
		public function render( $content = null ) {

			$this->init();

			$id = get_the_ID();

			$theme = wp_get_theme();
			if ( ! is_null( $theme->stylesheet ) ) {
				$theme = 'theme-' . $theme->stylesheet;
			} else {
				$theme = '';
			}

			if ( 'bottom' == wpmoly_o( 'headbox-position' ) ) {
				$theme .= ' position-bottom';
			} else {
				$theme .= ' position-top';
			}

			$title = apply_filters( 'wpmoly_format_movie_title', self::get_movie_meta( $id, 'title' ) );

			$menu = $this->render_menu();
			$tabs = $this->render_tabs();

			$attributes = compact( 'id', 'theme', 'title', 'menu', 'tabs' );

			$content = self::render_template( 'movies/movie-allocine-headbox.php', $attributes, $require = 'always' );

			return $content;
		}

		/**
		 * Render Allocine styled Headbox Menu.
		 *
		 * @since    2.1.4
		 * 
		 * @return   string    Headbox Tab HTML content
		 */
		private function render_menu() {

			$attributes = array(
				'id'    => get_the_ID(),
				'links' => $this->tabs
			);
			$content = self::render_template( 'movies/headbox-allocine/menu.php', $attributes, $require = 'always' );

			return $content;
		}

		/**
		 * Render Allocine styled Headbox Tabs.
		 *
		 * @since    2.1.4
		 * 
		 * @return   string    Headbox Tab HTML content
		 */
		private function render_tabs() {

			//print_r( $this->tabs );
			$attributes = array(
				'id'   => get_the_ID(),
				'tabs' => $this->tabs
			);
			$content = self::render_template( 'movies/headbox-allocine/tabs.php', $attributes, $require = 'always' );

			return $content;
		}

		/**
		 * Render Allocine styled Headbox 'Overview' Tab.
		 *
		 * @since    2.1.4
		 * 
		 * @return   string    Headbox Tab HTML content
		 */
		private function render_main_tab() {

			$id = get_the_ID();

			$poster = get_the_post_thumbnail( $id, 'medium' );
			$images = get_posts( array(
				'post_type'   => 'attachment',
				'orderby'     => 'title',
				'numberposts' => 5,
				'post_status' => null,
				'post_parent' => $id,
				'meta_key'    => '_wpmoly_image_related_tmdb_id',
				'exclude'     => get_post_thumbnail_id( $id )
			) );
			if ( $images ) {
				foreach ( $images as $i => $image ) {
					$images[ $i ] = array(
						'thumbnail' => wp_get_attachment_image_src( $image->ID, 'thumbnail' ),
						'full'      => wp_get_attachment_image_src( $image->ID, 'full' )
					);
				}
				$images = WPMovieLibrary::render_template( 'shortcodes/images.php', array( 'size' => 'thumbnail', 'movie_id' => $id, 'images' => $images ), $require = 'always' );
			}

			$collections = get_the_terms( $id, 'collection' );
			if ( $collections && ! is_wp_error( $collections ) ) {
				foreach ( $collections as $i => $c ) {
					$collections[ $i ] = $c->name;
				}
			}

			if ( is_array( $collections ) )
				$collections = implode( ',', $collections );

			$meta  = array();
			$_meta = array( 'title', 'director', 'composer', 'writer', 'local_release_date', 'runtime', 'genres', 'overview', 'production_countries', 'spoken_languages', 'budget', 'revenue' );
			foreach ( $_meta as $m )
				$meta[ $m ] = apply_filters( "wpmoly_format_movie_$m", self::get_movie_meta( $id, $m ) );

			$meta['collections'] = WPMOLY_Utils::format_movie_terms_list( $collections, 'collection' );

			$meta['release_date'] = self::get_movie_meta( $id, 'release_date' );
			$meta['year'] = apply_filters( 'wpmoly_format_movie_year', $meta['release_date'], 'Y' );
			$meta['release_date'] = apply_filters( 'wpmoly_format_movie_release_date', $meta['release_date'] );

			$meta = array_map( 'WPMOLY_Formatting_Meta::format_movie_field', $meta );

			$meta['cast'] = self::get_movie_meta( $id, 'cast' );
			$casting      = $meta['cast'];
			$meta['cast'] = array_slice( explode( ', ', $meta['cast'] ), 0, 3 );
			$meta['cast'] = implode( ', ', $meta['cast'] );
			$meta['cast'] = apply_filters( 'wpmoly_format_movie_cast', $meta['cast'] );

			$casting = apply_filters( 'wpmoly_format_movie_cast', $casting );
			$casting = array_slice( explode( ', ', $casting ), 0, 4 );

			$rating = apply_filters( 'wpmoly_movie_rating_stars', self::get_movie_meta( $id, 'rating' ), $id, $base = 10 );

			if ( empty( $images ) ) {
				$images = sprintf( __( 'No image to show for %s', 'wpmovielibrary' ), '<em>' . $meta['title'] . '</em>' );
			}

			$attributes = compact( 'id', 'meta', 'rating', 'casting', 'poster', 'images' );

			$content = self::render_template( 'movies/headbox-allocine/tabs/overview.php', $attributes, $require = 'always' );

			return $content;
		}

		/**
		 * Render Allocine styled Headbox 'Details' Tab.
		 *
		 * @since    2.1.4
		 * 
		 * @return   string    Headbox Tab HTML content
		 */
		private function render_details_tab() {

			$id = get_the_ID();

			$overview = self::get_movie_meta( $id, 'overview' );
			$tagline  = self::get_movie_meta( $id, 'tagline' );

			$details = wpmoly_get_movie_details();
			$default_fields = WPMOLY_Settings::get_supported_movie_details();

			foreach ( $details as $slug => $detail ) {

				if ( ! is_array( $detail ) )
					$detail = array( $detail );

				if ( isset( $default_fields[ $slug ]['panel'] ) && 'custom' == $default_fields[ $slug ]['panel'] ) {
					unset( $details[ $slug ] );
				} else {

					foreach ( $detail as $i => $d ) {

						if ( ! empty( $d ) ) {

							if ( isset( $default_fields[ $slug ]['options'] ) ) {
								$value = $default_fields[ $slug ]['options'][ $d ];
							} else {
								$value = $d;
							}

							if ( 'rating' == $slug ) {
								$d = apply_filters( "wpmoly_movie_meta_link", array(
									'key'   => 'rating',
									'value' => array_search( $value, $default_fields[ $slug ]['options'] ),
									'type'  => 'detail',
									'text'  => $value
								) );
							} else {
								$d = apply_filters( "wpmoly_movie_meta_link", array(
									'key'   => $slug,
									'value' => $value,
									'meta'  => 'detail',
									'text'  => $value
								) );
							}

							$detail[ $i ] = apply_filters( "wpmoly_format_movie_field", $d );
						}
					}

					$detail = implode( ', ', $detail );

					if ( empty( $detail ) )
						$detail = apply_filters( "wpmoly_format_movie_field", '' );

					$title = '';
					if ( isset( $default_fields[ $slug ] ) )
						$title = __( $default_fields[ $slug ]['title'], 'wpmovielibrary' );

					$details[ $slug ] = array( 'slug' => $slug, 'title' => $title, 'value' => $detail );
				}
			}

			$metas = wpmoly_get_movie_meta();
			$metas = wpmoly_filter_undimension_array( $metas );

			$default_fields = WPMOLY_Settings::get_supported_movie_meta();

			if ( ! empty( $metas ) ) {

				unset( $metas['title'], $metas['cast'], $metas['overview'], $metas['tagline'] );

				foreach ( $metas as $slug => $field ) {

					if ( isset( $default_fields[ $slug ] ) ) {
						// Custom filter if available
						if ( has_filter( "wpmoly_format_movie_{$slug}" ) )
							$field = apply_filters( "wpmoly_format_movie_{$slug}", $field );

						// Filter empty field
						$field = apply_filters( "wpmoly_format_movie_field", $field );

						$metas[ $slug ] = array( 'slug' => $slug, 'title' => __( $default_fields[ $slug ]['title'], 'wpmovielibrary' ), 'value' => $field );
					} else {
						unset( $metas[ $slug ] );
					}
				}
			}

			$attributes = compact( 'id', 'overview', 'tagline', 'details', 'metas' );

			$content = self::render_template( 'movies/headbox-allocine/tabs/details.php', $attributes, $require = 'always' );

			return $content;
		}

		/**
		 * Render Allocine styled Headbox 'Casting' Tab.
		 *
		 * @since    2.1.4
		 * 
		 * @return   string    Headbox Tab HTML content
		 */
		private function render_casting_tab() {

			$id = get_the_ID();

			$meta = self::get_movie_meta( $id, 'meta' );
			foreach ( $meta as $slug => $m ) {
				$meta[ $slug ] = apply_filters( "wpmoly_format_movie_$slug", $m );
				$meta[ $slug ] = explode( ', ', $meta[ $slug ] );
			}

			$attributes = compact( 'id', 'meta' );

			$content = self::render_template( 'movies/headbox-allocine/tabs/actors.php', $attributes, $require = 'always' );

			return $content;
		}

		/**
		 * Render Allocine styled Headbox 'Images' Tab.
		 *
		 * @since    2.1.4
		 * 
		 * @return   string    Headbox Tab HTML content
		 */
		private function render_images_tab() {

			$id = get_the_ID();

			$images = get_posts( array(
				'post_type'   => 'attachment',
				'orderby'     => 'post_date',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => $id,
				'meta_key'    => '_wpmoly_image_related_tmdb_id',
				'exclude'     => get_post_thumbnail_id( $id )
			) );

			if ( $images ) {

				foreach ( $images as $i => $image )
					$images[ $i ] = array(
						'thumbnail' => wp_get_attachment_image_src( $image->ID, 'thumbnail' ),
						'full'      => wp_get_attachment_image_src( $image->ID, 'full' )
					);

				$images = WPMovieLibrary::render_template( 'shortcodes/images.php', array( 'size' => 'thumbnail', 'movie_id' => get_the_ID(), 'images' => $images ), $require = 'always' );
			}

			$posters = get_posts( array(
				'post_type'   => 'attachment',
				'orderby'     => 'post_date',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => $id,
				'meta_key'    => '_wpmoly_poster_related_tmdb_id'
			) );

			if ( $posters ) {

				foreach ( $posters as $i => $poster )
					$posters[ $i ] = array(
						'thumbnail' => wp_get_attachment_image_src( $poster->ID, 'thumbnail' ),
						'full'      => wp_get_attachment_image_src( $poster->ID, 'full' )
					);

				$posters = WPMovieLibrary::render_template( 'shortcodes/images.php', array( 'size' => 'thumbnail', 'movie_id' => get_the_ID(), 'images' => $posters ), $require = 'always' );
			}

			if ( empty( $images ) ) {
				$images = __( 'No image to show.', 'wpmovielibrary' );
			}
			if ( empty( $posters ) ) {
				$posters = __( 'No poster to show.', 'wpmovielibrary' );
			}

			$attributes = array(
				'id'      => get_the_ID(),
				'images'  => $images,
				'posters' => $posters
			);

			$content = self::render_template( 'movies/headbox-allocine/tabs/images.php', $attributes, $require = 'always' );

			return $content;
		}

	}

endif;
