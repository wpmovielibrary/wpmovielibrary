<?php
/**
 * Define the Rest API extension class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\rest;

use WP_Error;
use WP_Taxonomy;
use WP_Post_Type;
use wpmoly\utils;

/**
 * Handle the custom WordPress Rest API endpoints.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 */
class API {

	/**
	 * Current instance.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @var API
	 */
	public static $instance;

	/**
	 * Define the API class.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		self::$instance = $this;

		$supported_parameters = apply_filters( 'wpmoly/filter/query/default/parameters', array(
			'actor'         => 'cast',
			'adult'         => 'adult',
			'author'        => 'author',
			'budget'        => 'budget',
			'certification' => 'certification',
			'company'       => 'production_companies',
			'composer'      => 'composer',
			'country'       => 'production_countries',
			'director'      => 'director',
			'my_format'     => 'format',
			'genre'         => 'genres',
			'my_language'   => 'language',
			'languages'     => 'spoken_languages',
			'local_release' => 'local_release_date',
			'my_media'      => 'media',
			'photography'   => 'photography',
			'preset'        => 'preset',
			'producer'      => 'producer',
			'my_rating'     => 'rating',
			'release'       => 'release_date',
			'revenue'       => 'revenue',
			'runtime'       => 'runtime',
			'my_status'     => 'status',
			'my_subtitles'  => 'subtitles',
			'writer'        => 'writer',
		) );

		$this->supported_parameters = $supported_parameters;
	}

	/**
	 * Singleton.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register additional routes for the REST API.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_routes() {

		$endpoints = array(
			'wpmoly\rest\endpoints\Actors',
			'wpmoly\rest\endpoints\Collections',
			'wpmoly\rest\endpoints\Genres',
			'wpmoly\rest\endpoints\Grids',
			'wpmoly\rest\endpoints\Movies',
			'wpmoly\rest\endpoints\Pages',
			'wpmoly\rest\endpoints\Settings',
			'wpmoly\rest\endpoints\TMDb',
		);

		foreach ( $endpoints as $class ) {
			if ( class_exists( $class ) ) {
				$controller = new $class;
				$controller->register_routes();
			}
		}
	}

	/**
	 * Register additional fields for the REST API data response objects.
	 *
	 * Add posters to movies, thumbnails to actors and thumbnails
	 * to collections and genres.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_fields() {

		// Movies thumbnails
		register_rest_field( array( 'movie' ), 'poster', array(
			'get_callback'    => array( $this, 'get_post_thumbnail' ),
			'update_callback' => null,
			'schema'          => array(
				'description' => __( 'The object featured poster.' ),
				'type'        => 'object',
				'context'     => array( 'view', 'edit' ),
			),
		) );

		// Terms thumbnails
		register_rest_field( array( 'actor', 'collection', 'genre' ), 'thumbnail', array(
			'get_callback'    => array( $this, 'get_term_thumbnail' ),
			'update_callback' => null,
			'schema'          => array(
				'description' => __( 'The object featured thumbnail.' ),
				'type'        => 'object',
				'context'     => array( 'view', 'edit' ),
			),
		) );
	}

	/**
	 * Add custom REST API page query params.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array           $args    Key value array of query var to query value.
	 * @param WP_REST_Request $request The request used.
	 *
	 * @return array
	 */
	public function add_page_query_params( $args, $request ) {

		if ( isset( $request['grid_id'] ) && 'edit' == $request['context'] ) {
			$args['meta_key']   = utils\grid\prefix( 'id' );
			$args['meta_value'] = absint( $request['grid_id'] );
		}

		return $args;
	}

	/**
	 * Add custom REST API post query params.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array           $args    Key value array of query var to query value.
	 * @param WP_REST_Request $request The request used.
	 *
	 * @return array
	 */
	public function add_post_query_params( $args, $request ) {

		// Preset should be overriden by custom order request.
		// We have to use $_REQUEST instead of $request to ignore defaults.
		if ( ! empty( $request['preset'] ) && ! ( empty( $_REQUEST['order'] ) || empty( $_REQUEST['orderby'] ) ) ) {
			unset( $request['preset'] );
		}

		if ( ! empty( $request['preset'] ) ) {

			$preset = str_replace( '-movies', '', $request['preset'] );

			/** This filter is documented in includes/core/class-query.php */
			$args = apply_filters( "wpmoly/filter/query/movies/{$preset}/preset/param", $args );
		}

		if ( ! empty( $request['letter'] ) ) {
			$args['letter'] = $request['letter'];
		}

		if ( ! isset( $args['meta_query'] ) ) {
			$args['meta_query'] = array();
		}

		foreach ( $this->supported_parameters as $param => $key ) {
			if ( ! empty( $request[ $param ] ) ) {

				/**
				 * Filter query parameters.
				 *
				 * @since 3.0.0
				 *
				 * @param array           $args    Query parameters.
				 * @param string          $key     Meta key.
				 * @param string          $param   Parameter slug.
				 * @param WP_REST_Request $request The request used.
				 */
				$args = apply_filters( "wpmoly/filter/query/movies/{$param}/param", $args, $key, $param, $request );
			}
		}

		return $args;
	}

	/**
	 * Add custom REST API term query params.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array           $args    Key value array of query var to query value.
	 * @param WP_REST_Request $request The request used.
	 *
	 * @return array
	 */
	public function add_term_query_params( $args, $request ) {

		// Preset should be overriden by custom order request.
		// We have to use $_REQUEST instead of $request to ignore defaults.
		if ( ! empty( $request['preset'] ) && ( ! empty( $_REQUEST['order'] ) || ! empty( $_REQUEST['orderby'] ) ) ) {
			unset( $request['preset'] );
		}

		$taxonomy = str_replace( array( 'rest_', '_query' ), '', current_filter() );
		if ( ! in_array( $taxonomy, array( 'actor', 'collection', 'genre' ) ) ) {
			return $args;
		}

		$taxonomy = get_taxonomy( $taxonomy );
		if ( ! $taxonomy ) {
			return $args;
		}

		if ( ! empty( $request['preset'] ) ) {

			$preset = str_replace( "-{$taxonomy->rest_base}", '', $request['preset'] );

			/** This filter is documented in includes/core/class-query.php */
			$args = apply_filters( "wpmoly/filter/query/{$taxonomy->rest_base}/{$preset}/preset/param", $args );
		}

		return $args;
	}

	/**
	 * Save attachment meta.
	 *
	 * @TODO cheap stuff. Make ot cleaner.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_Post         $attachment Inserted or updated attachment
	 *                                    object.
	 * @param WP_REST_Request $request    The request sent to the API.
	 * @param bool            $creating   True when creating an attachment, false when updating.
	 */
	public function update_attachment_meta( $attachment, $request, $creating ) {

		/** The 'rest_insert_attachment' hook fires twice, first in post controller
		 * then in attachment controller. No need to run this each time.
		 */
		if ( 1 < did_action( 'rest_insert_attachment' ) ) {
			return false;
		}

		if ( is_object( $attachment ) ) {
			$attachment = get_object_vars( $attachment );
		}

		if ( ! empty( $request['meta']['backdrop_related_tmdb_id'] ) ) {
			update_post_meta( $attachment['ID'], '_wpmoly_backdrop_related_tmdb_id', $request['meta']['backdrop_related_tmdb_id'] );
		}

		if ( ! empty( $request['meta']['image_related_tmdb_id'] ) ) {
			update_post_meta( $attachment['ID'], '_wpmoly_image_related_tmdb_id', $request['meta']['image_related_tmdb_id'] );
		}

		if ( ! empty( $request['meta']['poster_related_tmdb_id'] ) ) {
			update_post_meta( $attachment['ID'], '_wpmoly_poster_related_tmdb_id', $request['meta']['poster_related_tmdb_id'] );
		}
	}

	/**
	 * Update actor's thumbnail based on gender.
	 *
	 * Limit this to terms creation, not update.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_Term         $term     Inserted or updated term object.
	 * @param WP_REST_Request $request  Request object.
	 * @param bool            $creating True when creating a term, false when updating.
	 */
	public function update_actor_thumbnail( $term, $request, $creating ) {

		if ( true !== $creating || ! isset( $request['meta']['gender'] ) ) {
			return false;
		}

		$genders = array(
			'neutral',
			'female',
			'male',
		);

		$gender = $request['meta']['gender'];
		if ( ! empty( $genders[ $gender ] ) ) {
			update_term_meta( $term->term_id, utils\actor\prefix( 'thumbnail' ), $genders[ $gender ] );
		}
	}

	/**
	 * Update collection's thumbnail based on name.
	 *
	 * Limit this to terms creation, not update.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_Term         $term     Inserted or updated term object.
	 * @param WP_REST_Request $request  Request object.
	 * @param bool            $creating True when creating a term, false when updating.
	 */
	public function update_collection_thumbnail( $term, $request, $creating ) {

		if ( true !== $creating || ! isset( $term->name ) ) {
			return false;
		}

		update_term_meta( $term->term_id, utils\collection\prefix( 'thumbnail' ), substr( strtoupper( $term->name ), 0, 1 ) );
	}

	/**
	 * Update genre's thumbnail based on name.
	 *
	 * Limit this to terms creation, not update.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_Term         $term     Inserted or updated term object.
	 * @param WP_REST_Request $request  Request object.
	 * @param bool            $creating True when creating a term, false when updating.
	 */
	public function update_genre_thumbnail( $term, $request, $creating ) {

		if ( true !== $creating || ! isset( $request['meta']['tmdb_id'] ) ) {
			return false;
		}

		$default_genres = array(
			'28'    => 'action',
			'12'    => 'adventure',
			'16'    => 'animation',
			'35'    => 'comedy',
			'80'    => 'crime',
			'99'    => 'documentary',
			'18'    => 'drama',
			'10751' => 'family',
			'14'    => 'fantasy',
			'10769' => 'foreign',
			'36'    => 'history',
			'27'    => 'horror',
			'10402' => 'music',
			'9648'  => 'mystery',
			'10749' => 'romance',
			'878'   => 'science-fiction',
			'53'    => 'thriller',
			'10770' => 'tv-movie',
			'10752' => 'war',
			'37'    => 'western',
		);

		$tmdb_id = $request['meta']['tmdb_id'];
		if ( ! empty( $default_genres[ $tmdb_id ] ) ) {
			update_term_meta( $term->term_id, utils\genre\prefix( 'thumbnail' ), $default_genres[ $tmdb_id ] );
		}
	}

	/**
	 * Register custom REST API collection params.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_params JSON Schema-formatted collection parameters.
	 * @param mixed $object       WP_Post_Type or WP_Taxonomy object.
	 *
	 * @return array
	 */
	public function register_collection_params( $query_params, $object ) {

		if ( $object instanceof WP_Post_Type ) {
			return $this->register_post_collection_params( $query_params, $object );
		} elseif ( $object instanceof WP_Taxonomy ) {
			return $this->register_term_collection_params( $query_params, $object );
		}

		return $query_params;
	}

	/**
	 * Register custom REST API post collection params.
	 *
	 * Add support for letter and meta filtering, presets, fields selection.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param array        $query_params JSON Schema-formatted collection parameters.
	 * @param WP_Post_Type $post_type    Post Type object.
	 *
	 * @return array
	 */
	private function register_post_collection_params( $query_params, $post_type ) {

		if ( 'movie' === $post_type->name ) {

			$supported = $this->supported_parameters;

			// Support grid presets.
			$query_params['preset'] = array(
				'description' => __( 'Limit result set using presets.', 'wpmovielibrary' ),
				'type'        => 'string',
				'default'     => 'custom',
				//'sanitize_callback' => '',
			);

			// Filter movies by first letter.
			$query_params['letter'] = array(
				'description' => __( 'Filter movies by letter.', 'wpmovielibrary' ),
				'type'        => 'string',
				'default'     => '',
				'enum'        => array( '' ) + str_split( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ),
			);

			// Avoid loading all available meta.
			$query_params['fields'] = array(
				'description' => __( 'Limit result meta set to specific fields.', 'wpmovielibrary' ),
				'type'        => 'array',
				'default'     => array( 'title', 'genres', 'director', 'rating', 'release_date', 'runtime', 'year' ),
				'items'       => array(
					'type' => 'string',
				),
				//'sanitize_callback' => '',
			);

			// Authors are WordPress users; we want to be able to use
			// that option to match movies authors.
			if ( ! empty( $query_params['author']['items']['type'] ) ) {

				unset( $supported['author'] );

				$query_params['author']['description'] = __( 'Limit result set to posts assigned to specific authors. Use integers to match WordPress users, strings to match movies authors.', 'wpmovielibrary' );
				$query_params['author']['items']['type'] = 'string';
			}

			$metadata = get_registered_meta_keys( 'post', 'movie' );

			foreach ( $supported as $param => $key ) {

				$meta_key = utils\movie\prefix( $key );

				if ( ! empty( $metadata[ $meta_key ] ) ) {

					$meta = $metadata[ $meta_key ];
					if ( ! empty( $meta['show_in_rest']['schema'] ) ) {
						$meta = $meta['show_in_rest']['schema'];
					}

					$args = array(
						'description' => $meta['description'],
						'type'        => $meta['type'],
					);

					if ( ! empty( $meta['enum'] ) ) {
						$args['enum'] = $meta['enum'];
					}

					if ( ! empty( $meta['default'] ) ) {
						$args['default'] = $meta['default'];
					}

					$query_params[ $param ] = $args;
				}
			}

		} elseif ( 'page' === $post_type->name ) {

			$query_params['grid_id'] = array(
				'description' => __( 'Limit result set to page related to a specific grid.', 'wpmovielibrary' ),
				'type'        => 'integer',
			);

		} // End if().

		return $query_params;
	}

	/**
	 * Register custom REST API term collection params.
	 *
	 * Add support for presets.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param array       $query_params JSON Schema-formatted collection parameters.
	 * @param WP_Taxonomy $taxonomy     Taxonomy object.
	 *
	 * @return array
	 */
	private function register_term_collection_params( $query_params, $taxonomy ) {

		if ( in_array( $taxonomy->name, array( 'actor', 'collection', 'genre' ) ) ) {

			// Support grid presets.
			$query_params['preset'] = array(
				'description' => __( 'Limit result set using presets.', 'wpmovielibrary' ),
				'type'        => 'string',
				'default'     => 'custom',
				//'sanitize_callback' => '',
			);

			// Support term_order orderby parameter.
			if ( ! empty( $query_params['orderby']['enum'] ) && ! in_array( 'term_order', $query_params['orderby']['enum'], true ) ) {
				$query_params['orderby']['enum'][] = 'term_order';
				$query_params['orderby']['default'] = 'term_order';
			}
		}

		return $query_params;
	}

	/**
	 * Filter the actor data for REST API response.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $term     Term object.
	 * @param WP_REST_Request  $request  Request object.
	 *
	 * @return array
	 */
	public function prepare_actor_for_response( $response, $term, $request ) {

		$response = $this->prepare_term_for_response( $response, $term, $request );

		$meta = utils\get_registered_actor_meta();

		// Some meta should not be visible, although they may be editable.
		$protected = wp_filter_object_list( $meta, array( 'protected' => true ) );
		foreach ( $protected as $key => $value ) {
			$meta_key = utils\actor\prefix( $key );
			if ( isset( $response->data['meta'][ $meta_key ] ) ) {
				unset( $response->data['meta'][ $meta_key ] );
			}
		}

		if ( 'edit' === $request['context'] ) {
			$snapshot = utils\actor\get_meta( $term->term_id, 'snapshot' );
			if ( ! empty( $snapshot ) ) {
				$response->data['snapshot'] = json_decode( $snapshot );
			}
		} elseif ( isset( $response->data['meta'] ) ) {
			foreach ( $response->data['meta'] as $key => $value ) {
				unset( $response->data['meta'][ $key ] );
				$response->data['meta'][ utils\actor\unprefix( $key, false ) ] = $value;
			}
		}

		return $response;
	}

	/**
	 * Filter the collection data for REST API response.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $term     Term object.
	 * @param WP_REST_Request  $request  Request object.
	 *
	 * @return array
	 */
	public function prepare_collection_for_response( $response, $term, $request ) {

		$response = $this->prepare_term_for_response( $response, $term, $request );

		if ( 'edit' !== $request['context'] ) {
			foreach ( $response->data['meta'] as $key => $value ) {
				unset( $response->data['meta'][ $key ] );
				$response->data['meta'][ utils\collection\unprefix( $key, false ) ] = $value;
			}
		}

		return $response;
	}

	/**
	 * Filter the genre data for REST API response.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $term     Term object.
	 * @param WP_REST_Request  $request  Request object.
	 *
	 * @return array
	 */
	public function prepare_genre_for_response( $response, $term, $request ) {

		$response = $this->prepare_term_for_response( $response, $term, $request );

		if ( 'edit' !== $request['context'] ) {
			foreach ( $response->data['meta'] as $key => $value ) {
				unset( $response->data['meta'][ $key ] );
				$response->data['meta'][ utils\genre\unprefix( $key, false ) ] = $value;
			}
		}

		return $response;
	}

	/**
	 * Filter the grid post data for REST API response.
	 *
	 * @TODO Allow rendered content when specifically requested.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $post     Post object.
	 * @param WP_REST_Request  $request  Request object.
	 *
	 * @return array
	 */
	public function prepare_grid_for_response( $response, $post, $request ) {

		$response = $this->prepare_post_for_response( $response, $post, $request );

		if ( 'edit' !== $request['context'] ) {
			foreach ( $response->data['meta'] as $key => $value ) {
				unset( $response->data['meta'][ $key ] );
				$response->data['meta'][ utils\grid\unprefix( $key, false ) ] = $value;
			}
		}

		return $response;
	}

	/**
	 * Filter the movie post data for REST API response.
	 *
	 * @TODO Allow rendered content when specifically requested.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $post     Post object.
	 * @param WP_REST_Request  $request  Request object.
	 *
	 * @return array
	 */
	public function prepare_movie_for_response( $response, $post, $request ) {

		$response = $this->prepare_post_for_response( $response, $post, $request );

		$meta = utils\get_registered_movie_meta();

		// Some meta should not be visible, although they may be editable.
		$protected = wp_filter_object_list( $meta, array( 'protected' => true ) );
		foreach ( $protected as $key => $value ) {
			$meta_key = utils\movie\prefix( $key );
			if ( isset( $response->data['meta'][ $meta_key ] ) ) {
				unset( $response->data['meta'][ $meta_key ] );
			}
		}

		if ( 'edit' === $request['context'] ) {
			$snapshot = utils\movie\get_meta( $post->ID, 'snapshot' );
			if ( ! empty( $snapshot ) ) {
				$response->data['snapshot'] = json_decode( $snapshot );
			}
		} elseif ( isset( $response->data['meta'] ) ) {
			foreach ( $response->data['meta'] as $key => $value ) {
				unset( $response->data['meta'][ $key ] );
				$response->data['meta'][ utils\movie\unprefix( $key, false ) ] = $value;
			}
		}

		return $response;
	}

	/**
	 * Filter the post data for REST API response.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Post          $post     Term object.
	 * @param WP_REST_Request  $request  Request object.
	 *
	 * @return array
	 */
	public function prepare_post_for_response( $response, $post, $request ) {

		// Content/excerpt are overkill.
		$response->data['excerpt']['rendered'] = '';
		$response->data['content']['rendered'] = '';

		if ( 'edit' === $request['context'] ) {
			$response->data['edit_link'] = admin_url( 'admin.php?page=wpmovielibrary-' . $post->post_type . 's&id=' . $post->ID . '&action=edit' );
			$response->data['old_edit_link'] = get_edit_post_link( $post->ID, false );
		}

		return $response;
	}

	/**
	 * Filter the term data for REST API response.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_REST_Response $response The response object.
	 * @param WP_Term          $term     Term object.
	 * @param WP_REST_Request  $request  Request object.
	 *
	 * @return array
	 */
	public function prepare_term_for_response( $response, $term, $request ) {

		if ( 'edit' === $request['context'] ) {
			if ( in_array( $term->taxonomy, array( 'actor', 'collection', 'genre' ), true ) ) {
				$response->data['edit_link'] = admin_url( 'admin.php?page=wpmovielibrary-' . $term->taxonomy . 's&id=' . $term->term_id. '&action=edit' );
				$response->data['old_edit_link'] = get_edit_term_link( $term->term_id, $term->taxonomy );
			} else {
				$response->data['edit_link'] = get_edit_term_link( $term->term_id, $term->taxonomy );
			}
		}

		return $response;
	}

	/**
	 * Add post thumbnail to the data response.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array           $object     Post object.
	 * @param string          $field_name Field name.
	 * @param WP_REST_Request $request    Current REST Request.
	 *
	 * @return Image
	 */
	public function get_post_thumbnail( $object, $field_name, $request ) {

		if ( isset( $object['type'] ) && 'movie' === $object['type'] ) {

			$movie = utils\movie\get( $object['id'] );

			return $movie->get_poster();
		}

		return null;
	}

	/**
	 * Add term thumbnail to the data response.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array           $object     Post object.
	 * @param string          $field_name Field name.
	 * @param WP_REST_Request $request    Current REST Request.
	 *
	 * @return Image
	 */
	public function get_term_thumbnail( $object, $field_name, $request ) {

		if ( isset( $object['taxonomy'] ) && 'actor' === $object['taxonomy'] ) {

			$term = utils\actor\get( $object['id'] );

			return $term->get_thumbnail();

		} elseif ( isset( $object['taxonomy'] ) && 'collection' === $object['taxonomy'] ) {

			$term = utils\collection\get( $object['id'] );

			return $term->get_thumbnail();

		} elseif ( isset( $object['taxonomy'] ) && 'genre' === $object['taxonomy'] ) {

			$term = utils\genre\get( $object['id'] );

			return $term->get_thumbnail();

		}

		return null;
	}
}
