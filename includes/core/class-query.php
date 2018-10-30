<?php
/**
 * Define the Query class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\core;

/**
 *
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Query {

	/**
	 * Custom query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $vars;

	/**
	 * Custom rewrite tags.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $tags;

	/**
	 * The single instance of the class.
	 *
	 * @since      3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Library
	 */
	private static $_instance = null;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return \wpmoly\Library
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new static;
			self::$_instance->init();
		}

		return self::$_instance;
	}

	/**
	 * Initialize core.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function init() {

		/**
		 * Filters the default available custom query vars.
		 *
		 * @since 3.0.0
		 *
		 * @param array $vars Defaults query vars.
		 */
		$this->vars = apply_filters( 'wpmoly/filter/query/default/vars', array(
			'actor',
			'adult',
			'author',
			'budget',
			'certification',
			'company',
			'composer',
			'country',
			'director',
			'my_format',
			'genre',
			'grid',
			'imdb_id',
			'my_language',
			'languages',
			'local_release',
			'my_media',
			'photography',
			'preset',
			'producer',
			'my_rating',
			'release',
			'revenue',
			'runtime',
			'my_status',
			'my_subtitles',
			'tmdb_id',
			'writer',
		) );

		/**
		 * Filters the default available custom tags for URL rewriting.
		 *
		 * @since 3.0
		 *
		 * @param array $tags Defaults rewrite tags.
		 */
		$this->tags = apply_filters( 'wpmoly/filter/rewrite/default/tags', array(
			'%imdb_id%'          => '(tt[0-9]+)',
			'%tmdb_id%'          => '([0-9]+)',
			'%year%'             => '([0-9]{4})',
			'%monthnum%'         => '([0-9]{1,2})',
			'%day%'              => '([0-9]{1,2})',
			'%release_year%'     => '([0-9]{4})',
			'%release_monthnum%' => '([0-9]{1,2})',
			'%release_day%'      => '([0-9]{1,2})',
		) );

		foreach ( array_keys( $this->tags ) as $tag ) {
			$this->vars[] = str_replace( '%', '', $tag );
		}

	}

	/**
	 * Register query vars for custom rewrite tags.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars
	 *
	 * @return array
	 */
	public function add_query_vars( $query_vars ) {

		$query_vars = array_merge( $query_vars, $this->vars );

		return $query_vars;
	}

	/**
	 * Register custom rewrite tags.
	 *
	 * Add a set of new movie-related rewrite tags.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function add_rewrite_tags() {

		global $wp_rewrite;

		foreach ( $this->tags as $tag => $regex ) {
			$wp_rewrite->add_rewrite_tag( $tag, $regex, str_replace( array( '%', '_' ), array( '', '-' ), $tag ) . '=' );
		}
	}

	/**
	 * Replace custom rewrite tags in post links.
	 *
	 * WordPress automatically appends the post's name at the end of the
	 * permalink, meaning we have to check for the presence of a %postname%
	 * or %movie% tag in the permalink that could be present if we're dealing
	 * with custom permalink structures and remove it. This may result in
	 * duplicate slashes that we need to clean while we're at it.
	 *
	 * This markers should be stripped automatically when saving permalink
	 * structures, but we're still better off checking to avoid malformed
	 * URLs.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string  $permalink
	 * @param object  $post      WP_Post instance
	 * @param boolean $leavename
	 * @param boolean $sample
	 *
	 * @return string
	 */
	public function replace_movie_link_tags( $permalink, $post, $leavename, $sample ) {

		if ( $sample || 'movie' != $post->post_type ) {
			return $permalink;
		}

		// Check for duplicate post name and clean duplicate slashes
		$permalink = preg_replace( '/' . $post->post_name . '\/?$/', '', $permalink );
		$permalink = preg_replace( '/([^:])(\/{2,})/', '$1/', $permalink );

		return $this->replace_tags( $permalink, $post );
	}

	/**
	 * Replace custom rewrite tags in permalinks.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $permalink
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	private function replace_tags( $permalink, $post ) {

		$search  = array();
		$replace = array();

		foreach ( array_keys( $this->tags ) as $tag ) {
			if ( false !== strpos( $permalink, $tag ) ) {
				$search[]  = $tag;
				$replace[] = $this->get_replacement( $tag, $post );
			} else {
				unset( $search[ $tag ] );
			}
		}

		$search[]  = '%postname%';
		$replace[] = $post->post_name;

		$permalink = str_replace( $search, $replace, $permalink );

		return trailingslashit( $permalink );
	}

	/**
	 * Get replacement value for custom rewrite tags in permalinks.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $tag
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	private function get_replacement( $tag, $post ) {

		$value = '';

		switch ( $tag ) {
			case '%imdb_id%':
			case '%tmdb_id%':
				$value = get_movie_meta( $post->ID, str_replace( '%', '', $tag ), true );
				break;
			case '%release_year%':
				$value = get_movie_meta( $post->ID, 'release_date', true );
				$value = date( 'Y', strtotime( $value ) );
				break;
			case '%release_monthnum%':
				$value = get_movie_meta( $post->ID, 'release_date', true );
				$value = date( 'm', strtotime( $value ) );
				break;
			case '%year%':
				$value = date( 'Y', strtotime( $post->post_date ) );
				break;
			case '%monthnum%':
				$value = date( 'm', strtotime( $post->post_date ) );
				break;
			default:
				break;
		}

		return $value;
	}

	/**
	 * Filter 'author' meta query parameter.
	 *
	 * There is a confusion between post authors and movie authors, the former
	 * being a WordPress user and the latter a movie meta value. Post authors
	 * should be queried by their user ID while movie authors should be queried
	 * by their full name.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array           $args    Key value array of query var to query value.
	 * @param string          $key     Meta key.
	 * @param mixed           $param   Meta value.
	 * @param WP_REST_Request $request The request used.
	 *
	 * @return array
	 */
	public function filter_meta_author_query_param( $args, $key, $param, $request ) {

		if ( empty( $key ) || empty( $param ) ) {
			return $args;
		}

		// Clean up data.
		$author = array_filter( $request[ $param ] );
		if ( empty( $author ) ) {
			return $args;
		}

		// Don't bother with extra data.
		$author = array_shift( $author );

		if ( preg_match( '/^[\d]+$/', $author ) ) {
			// WordPress author (ie. user ID)?
			$request['author'] = array( (int) $author );
		} else {
			// Movie author.
			$args['author__in'] = array();

			// Meta key.
			$key = prefix_movie_meta_key( $key );

			/**
			 * Filter meta value.
			 *
			 * @since 3.0.0
			 *
			 * @param string $author
			 */
			$value = apply_filters( 'wpmoly/filter/query/movies/author/value', $author );

			/**
			 * Filter meta comparison operator.
			 *
			 * @since 3.0.0
			 *
			 * @param string $compare
			 */
			$compare = apply_filters( 'wpmoly/filter/query/movies/author/compare', 'LIKE' );

			$args['meta_query'][] = compact( 'key', 'value', 'compare' );
		}

		return $args;
	}

	/**
	 * Add custom parameters to query movies of a specific meta interval.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array           $args    Key value array of query var to query value.
	 * @param string          $key     Meta key.
	 * @param mixed           $param   Meta value.
	 * @param WP_REST_Request $request The request used.
	 *
	 * @return array
	 */
	public function filter_meta_interval_query_param( $args, $key, $param, $request ) {

		if ( empty( $key ) || empty( $param ) ) {
			return $args;
		}

		// Meta key.
		$key = prefix_movie_meta_key( $key );

		/**
		 * Filter meta value.
		 *
		 * @since 3.0.0
		 *
		 * @param string $value
		 */
		$value = apply_filters( "wpmoly/filter/query/movies/{$param}/value", $request[ $param ] );

		if ( ! is_array( $value ) ) {

			/**
			 * Filter meta comparison operator.
			 *
			 * @since 3.0.0
			 *
			 * @param string $compare
			 */
			$compare = apply_filters( "wpmoly/filter/query/movies/{$param}/compare", 'LIKE' );

			$args['meta_query'][] = compact( 'key', 'value', 'compare' );

		} else {

			/**
			 * Filter meta casting type.
			 *
			 * @since 3.0.0
			 *
			 * @param string $type Casting type.
			 */
			$type = apply_filters( "wpmoly/filter/query/movies/{$param}/type", 'NUMERIC' );

			$args['meta_query'] = array(
				array(
					'key'     => $key,
					'value'   => $value[0],
					'type'    => $type,
					'compare' => '>=',
				),
				array(
					'key'     => $key,
					'value'   => $value[1],
					'type'    => $type,
					'compare' => '<=',
				),
				'relation' => 'AND',
			);
		} // End if().

		return $args;
	}

	/**
	 * Add custom parameters to query movies of a specific meta.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array           $args    Key value array of query var to query value.
	 * @param string          $key     Meta key.
	 * @param mixed           $param   Meta value.
	 * @param WP_REST_Request $request The request used.
	 *
	 * @return array
	 */
	public function filter_meta_query_param( $args, $key, $param, $request ) {

		if ( empty( $key ) || empty( $param ) ) {
			return $args;
		}

		// Meta key.
		$key = prefix_movie_meta_key( $key );

		/**
		 * Filter meta value.
		 *
		 * @since 3.0.0
		 *
		 * @param string $value
		 */
		$value = apply_filters( "wpmoly/filter/query/movies/{$param}/value", $request[ $param ] );

		/**
		 * Filter meta comparison operator.
		 *
		 * @since 3.0.0
		 *
		 * @param string $compare
		 */
		$compare = apply_filters( "wpmoly/filter/query/movies/{$param}/compare", 'LIKE' );

		$args['meta_query'][] = compact( 'key', 'value', 'compare' );

		return $args;
	}

	/**
	 * 'alphabetical' actors query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_alphabetical_actors_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'orderby' => 'name',
			'order'   => 'asc',
		) );

		return $query_vars;
	}

	/**
	 * 'unalphabetical' actors query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_unalphabetical_actors_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'orderby' => 'name',
			'order'   => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'alphabetical' collections query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_alphabetical_collections_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'orderby' => 'name',
			'order'   => 'asc',
		) );

		return $query_vars;
	}

	/**
	 * 'unalphabetical' collections query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_unalphabetical_collections_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'orderby' => 'name',
			'order'   => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'alphabetical' genres query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_alphabetical_genres_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'orderby' => 'name',
			'order'   => 'asc',
		) );

		return $query_vars;
	}

	/**
	 * 'unalphabetical' genres query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_unalphabetical_genres_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'orderby' => 'name',
			'order'   => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'alphabetical' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_alphabetical_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'meta_key' => prefix_movie_meta_key( 'title' ),
			'orderby'  => 'meta_value',
			'order'    => 'asc',
		) );

		return $query_vars;
	}

	/**
	 * 'unalphabetical' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_unalphabetical_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'meta_key' => prefix_movie_meta_key( 'title' ),
			'orderby'  => 'meta_value',
			'order'    => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'current-year' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_current_year_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'meta_key'   => prefix_movie_meta_key( 'release_date' ),
			'meta_type'  => 'date',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => prefix_movie_meta_key( 'release_date' ),
					'type'    => 'date',
					'value'   => sprintf( '%d-01-01', date( 'Y' ) ),
					'compare' => '>=',
				),
				array(
					'key'     => prefix_movie_meta_key( 'release_date' ),
					'type'    => 'date',
					'value'   => sprintf( '%d-12-31', date( 'Y' ) ),
					'compare' => '<=',
				),
			),
			'orderby' => 'meta_value',
			'order'   => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'last-year' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_last_year_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'meta_key'   => prefix_movie_meta_key( 'release_date' ),
			'meta_type'  => 'date',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => prefix_movie_meta_key( 'release_date' ),
					'type'    => 'date',
					'value'   => sprintf( '%d-01-01', date( 'Y' ) - 1 ),
					'compare' => '>=',
				),
				array(
					'key'     => prefix_movie_meta_key( 'release_date' ),
					'type'    => 'date',
					'value'   => sprintf( '%d-12-31', date( 'Y' ) - 1 ),
					'compare' => '<=',
				),
			),
			'orderby' => 'meta_value',
			'order'   => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'last-added' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_last_added_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'orderby' => 'date',
			'order'   => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'first-added' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_first_added_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'orderby' => 'date',
			'order'   => 'asc',
		) );

		return $query_vars;
	}

	/**
	 * 'last-released' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_last_released_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'meta_key'  => prefix_movie_meta_key( 'release_date' ),
			'meta_type' => 'date',
			'orderby'   => 'meta_value',
			'order'     => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'first-released' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_first_released_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'meta_key'  => prefix_movie_meta_key( 'release_date' ),
			'meta_type' => 'date',
			'orderby'   => 'meta_value',
			'order'     => 'asc',
		) );

		return $query_vars;
	}

	/**
	 * 'incoming' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_incoming_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'meta_key'     => prefix_movie_meta_key( 'release_date' ),
			'meta_type'    => 'date',
			'meta_value'   => sprintf( '%d-01-01', date( 'Y' ) + 1 ),
			'meta_compare' => '>=',
			'orderby'      => 'meta_value',
			'order'        => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'most-rated' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_most_rated_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'meta_key'     => prefix_movie_meta_key( 'rating' ),
			//'meta_value'   => 0.0
			//'meta_compare' => '>',
			'orderby'      => 'meta_value_num',
			'order'        => 'desc',
		) );

		return $query_vars;
	}

	/**
	 * 'least-rated' movies query preset query vars.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function filter_least_rated_movies_preset_param( $query_vars = array() ) {

		$query_vars = array_merge( (array) $query_vars, array(
			'meta_key'     => prefix_movie_meta_key( 'rating' ),
			//'meta_value'   => 0.0
			//'meta_compare' => '>',
			'orderby'      => 'meta_value_num',
			'order'        => 'asc',
		) );

		return $query_vars;
	}

	/**
	 * Filter 'rating' query type.
	 *
	 * Movies can be filtered by ratings interval but require values to be
	 * casted as DECIMAL instead of NUMERIC.
	 *
	 * @see \wpmoly\rest\API::add_meta_interval_query_param()
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $type Query type.
	 *
	 * @return string
	 */
	public function filter_rating_query_type( $type ) {

		if ( 'DECIMAL' !== $type ) {
			$type = 'DECIMAL';
		}

		return $type;
	}

	/**
	 * Filter 'actor' query var value. Replace value by matching term
	 * name, if any.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_actor_query_var( $query_var ) {

		$term = get_term_by( 'slug', $query_var, 'actor' );
		if ( ! empty( $term->name ) ) {
			return $term->name;
		}

		return $this->filter_name_query_var( $query_var, 'actor' );
	}

	/**
	 * Filter 'adult' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_adult_query_var( $query_var ) {

		$query_var = _is_bool( $query_var ) ? 'true' : 'false';

		return $query_var;
	}

	/**
	 * Filter 'author' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_author_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'author' );
	}

	/**
	 * Filter 'certification' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_certification_query_var( $query_var ) {

		$query_var = strtoupper( $query_var );

		return $query_var;
	}

	/**
	 * Filter 'company' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_company_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'company' );

		return $query_var;
	}

	/**
	 * Filter 'composer' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_composer_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'composer' );
	}

	/**
	 * Convert a sanitized country code, standard name or localized name to
	 * its clean standard name.
	 *
	 * This is used by movie queries to match URL query vars to the real movie
	 * metadata.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Sanitized value to "unsanitize".
	 *
	 * @return string
	 */
	public function filter_country_query_var( $query_var ) {

		$query_var = get_country( $query_var );
		$query_var = $query_var->standard_name;

		return $query_var;
	}

	/**
	 * Filter 'director' query var value. Replace value by matching term
	 * name, if any.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_director_query_var( $query_var ) {

		$term = get_term_by( 'slug', $query_var, 'collection' );
		if ( ! $term ) {
			return $query_var;
		}

		if ( ! empty( $term->name ) ) {
			$query_var = $term->name;
		}

		return $query_var;
	}

	/**
	 * Filter 'format' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_format_query_var( $query_var ) {

		$query_var = strtolower( $query_var );

		return $query_var;
	}

	/**
	 * Filter 'genre' query var value. Replace value by matching term
	 * name, if any.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_genre_query_var( $query_var ) {

		$term = get_term_by( 'slug', $query_var, 'genre' );
		if ( ! empty( $term->name ) ) {
			return $term->name;
		}

		return $this->filter_name_query_var( $query_var, 'genre' );
	}

	/**
	 * Filter 'languages' query var value. Convert a sanitized language code,
	 * standard name or localized name to its clean native name.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_languages_query_var( $query_var ) {

		$query_var = get_language( $query_var );
		$query_var = $query_var->native_name;

		return $query_var;
	}

	/**
	 * Filter 'language' query var value. Convert a sanitized language
	 * standard name, native name or localized name to its ISO code.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_language_query_var( $query_var ) {

		$query_var = get_language( $query_var );
		$query_var = $query_var->code;

		return $query_var;
	}

	/**
	 * Filter 'media' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_media_query_var( $query_var ) {

		$query_var = strtolower( $query_var );

		return $query_var;
	}

	/**
	 * Filter 'revenue' and 'budget' query var values.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_money_query_var( $query_var ) {

		$interval = explode( '-', (string) $query_var );

		sort( $interval );

		if ( ! empty( $interval[0] ) && ! empty( $interval[1] ) ) {
			// Custom interval.
			$interval = array_map( 'intval', $interval );
		} elseif ( empty( $interval[0] ) && ! empty( $interval[1] ) ) {
			// Default interval.
			$min = 0;
			$max = 0;
			if ( $interval[1] < 100000 ) {
				// Less than 100 thousands.
				$min = 0;
				$max = 100000;
			} elseif ( $interval[1] < 500000 ) {
				// 100 thousands to half million.
				$min = floor( $interval[1] / 100000 ) * 100000;
				$max = $min + 100000;
			} elseif ( $interval[1] < 1000000 ) {
				// Half million to 1 million.
				$min = floor( $interval[1] / 500000 ) * 500000;
				$max = $min + 500000;
			} elseif ( $interval[1] < 10000000 ) {
				// 1 millions to 10 millions.
				$min = floor( $interval[1] / 1000000 ) * 1000000;
				$max = $min + 1000000;
			} elseif ( $interval[1] < 100000000 ) {
				// 10 millions to 100 millions.
				$min = floor( $interval[1] / 10000000 ) * 10000000;
				$max = $min + 10000000;
			} elseif ( $interval[1] < 1000000000 ) {
				// 100 millions 1 billion.
				$min = floor( $interval[1] / 100000000 ) * 100000000;
				$max = $min + 100000000;
			} elseif ( $interval[1] >= 1000000000 ) {
				// More than 1 billion.
				$interval = 1000000000;
			}

			if ( $min && $max ) {
				$interval = array( $min, $max );
			} else {
				$interval = $interval[1];
			}
		} else {
			// No interval.
			$interval = array_shift( $interval );
		} // End if().

		return $interval;
	}

	/**
	 * Filter 'photography' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_photography_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'photography' );
	}

	/**
	 * Filter 'producer' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_producer_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'producer' );
	}

	/**
	 * Convert a sanitized rating to a float-formatted value.
	 *
	 * This is used by movie queries to match URL query vars to the real movie
	 * metadata.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Sanitized value to "unsanitize".
	 *
	 * @return string
	 */
	public function filter_rating_query_var( $query_var ) {

		if ( ! preg_match( '/^[\d](\.([\d])?)?|[\d](\.([\d])?)?-[\d](\.([\d])?)?$/', $query_var ) ) {
			return $query_var;
		}

		$interval = explode( '-', $query_var );

		if ( 2 === count( $interval ) ) {
			$interval = array_map( function( $n ) {
				return number_format( max( 0.0, min( $n, 5.0 ) ), 1 );
			}, $interval );
			sort( $interval );
		} elseif ( 1 === count( $interval ) ) {
			$interval = array_shift( $interval );
		} else {
			$interval = $query_var;
		}

		return $interval;
	}

	/**
	 * Convert a sanitized date to a standard YYYY-MM-DD date format.
	 *
	 * This is used by movie queries to match URL query vars to the real movie
	 * metadata.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Sanitized value to "unsanitize".
	 *
	 * @return string
	 */
	public function filter_release_query_var( $query_var ) {

		$start = '';
		$end = '';

		// Year interval.
		if ( preg_match( '/^([0-9]{4})-([0-9]{4})$/', $query_var, $match ) ) {
			$start = "{$match[1]}-1-1";
			$end   = "{$match[2]}-12-31";
		} elseif ( preg_match( '/^([0-9]{4}-[0-9]{1,2})-([0-9]{4}-[0-9]{1,2})$/', $query_var, $match ) ) {
			// Month interval.
			$start = "{$match[1]}-1}";
			$end   = "{$match[2]}-31}";
		} elseif ( preg_match( '/^([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})-([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})$/', $query_var, $match ) ) {
			// Day interval.
			$start = $match[1];
			$end   = $match[2];
		} elseif ( preg_match( '/^([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})$/', $query_var, $match ) ) {
			// Day.
			$start = $match[1];
		} elseif ( preg_match( '/^([0-9]{4}-[0-9]{2})$/', $query_var, $match ) ) {
			// Month.
			$start = $match[1];
		} elseif ( preg_match( '/^([0-9]{4})$/', $query_var, $match ) ) {
			// Year.
			$start = $match[1];
		}

		if ( ! empty( $start ) && ! empty( $end ) ) {
			$interval = array( $start, $end );
		} else {
			$interval = $start;
		}

		return $interval;
	}

	/**
	 * Filter 'runtime' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return array
	 */
	public function filter_runtime_query_var( $query_var ) {

		$interval = explode( '-', (string) $query_var );

		sort( $interval );

		if ( ! empty( $interval[0] ) && ! empty( $interval[1] ) ) {
			// Custom interval.
			$interval = array_map( 'intval', $interval );
		} elseif ( empty( $interval[0] ) && ! empty( $interval[1] ) ) {
			// Default interval.
			$min = (int) floor( $interval[1] / 10 ) * 10;
			$max = (int) ceil( $interval[1] / 10 ) * 10;
			$interval = array( $min, $max );
		} else {
			// No interval.
			$interval = array_shift( $interval );
		}

		return $interval;
	}

	/**
	 * Filter 'status' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_status_query_var( $query_var ) {

		$query_var = strtolower( $query_var );

		return $query_var;
	}

	/**
	 * Filter 'subtitles' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_subtitles_query_var( $query_var ) {

		$query_var = get_language( $query_var );
		$query_var = $query_var->code;

		return $query_var;
	}

	/**
	 * Filter 'writer' query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_writer_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'writer' );
	}

	/**
	 * Filter people-based query var value.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $query_var Query var value.
	 *
	 * @return string
	 */
	public function filter_name_query_var( $query_var, $type ) {

		if ( ! is_string( $type ) ) {
			return $query_var;
		}

		$cache = $this->get_cached_names( $type );
		if ( ! empty( $cache[ $query_var ] ) ) {
			return $cache[ $query_var ];
		}

		return $query_var;
	}

	/**
	 * Retrieve a cached list of people and company names.
	 *
	 * Meta value passed through URL are sanitized and can not be accurately
	 * matched with original value due to ambiguities around hyphens and
	 * accenttuated letters. Matching table are stored as options to retrieve
	 * original value from slug.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $type Meta type.
	 *
	 * @return array
	 */
	private function get_cached_names( $type ) {

		global $wpdb;

		$cached_names = array();
		$supported = array(
			'actor'       => 'cast',
			'author'      => 'author',
			'company'     => 'production_companies',
			'composer'    => 'composer',
			'director'    => 'director',
			'photography' => 'photography',
			'producer'    => 'producer',
			'writer'      => 'writer',
		);

		if ( ! isset( $supported[ $type ] ) ) {
			return $cached_names;
		}

		// Meta key.
		$meta_key = prefix_movie_meta_key( $supported[ $type ] );

		/**
		 * Filter cache option name.
		 *
		 * @since 3.0.0
		 *
		 * @param string $option_name Option name.
		 */
		$option_name = apply_filters( 'wpmoly/filter/people/cache/option/name', "_wpmoly_{$type}_cache_table" );

		/**
		 * Filter cache option autoload.
		 *
		 * @since 3.0.0
		 *
		 * @param string $option_autoload Option autoload.
		 */
		$option_autoload = apply_filters( 'wpmoly/filter/people/cache/option/autoload', 'no' );

		$cached_names = get_option( $option_name, $cached_names );
		if ( ! empty( $cached_names ) ) {
			return $cached_names;
		}

		$people = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s", $meta_key ) );
		foreach ( $people as $list ) {
			$list = explode( ',', $list->meta_value );
			foreach ( $list as $item ) {
				$item = trim( $item );
				$slug = sanitize_title_with_dashes( $item );
				$cached_names[ $slug ] = $item;
			}
		}

		update_option( $option_name, $cached_names, $option_autoload );

		return $cached_names;
	}

	/**
	 * Filter movies by letter.
	 *
	 * Add a new WHERE clause to the current query to limit selection to the
	 * movies with a title starting with a specific letter.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $where
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public function filter_movies_by_letter( $where, $query ) {

		global $wpdb;

		$letter = $query->get( 'letter' );
		if ( ! empty( $letter ) ) {
			$where .= " AND {$wpdb->posts}.post_title LIKE '" . $wpdb->esc_like( strtoupper( $letter ) ) . "%'";
		}

		return $where;
	}

	/**
	 * Filter movies using presets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $where
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public function filter_movies_by_preset( $wp_query ) {

		if ( 'movie' !== $wp_query->get( 'post_type' ) || '' === $wp_query->get( 'preset' ) ) {
			return false;
		}

		$preset = str_replace( '-movies', '', $wp_query->get( 'preset' ) );

		/**
		 * Filter query vars.
		 *
		 * @since 3.0.0
		 *
		 * @param array $query_vars
		 */
		$query_vars = apply_filters( "wpmoly/filter/query/movies/{$preset}/preset/param", $wp_query->query_vars );

		foreach ( $query_vars as $name => $var ) {
			$wp_query->set( $name, $var );
		}

		unset( $wp_query->query['preset'] );
	}

}
