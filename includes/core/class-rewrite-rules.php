<?php
/**
 * Define the Rewrite Rules class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package wpMovieLibrary
 */

namespace wpmoly\core;

use wpmoly\utils;

/**
 * Handle the plugin's URL rewriting settings.
 *
 * @since 3.0.0
 * @package wpMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Rewrite_Rules {

	/**
	 * Register custom rewrite rules for custom post types and taxonomies.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param array The compiled array of rewrite rules.
	 */
	public function register( $rules ) {

		$movies     = $this->generate_movie_archives_rewrite_rules();
		$taxonomies = $this->generate_taxonomy_archives_rewrite_rules();

		$rules = array_merge( $movies, $taxonomies, $rules );

		return $rules;
	}

	/**
	 * Register admin notice.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_notice() {

		$check = get_transient( '_wpmoly_reset_permalinks_notice' );
		if ( ! $check ) {
			return false;
		}

		/**
		 * Filter the update permalinks notice message.
		 *
		 * @since 3.0
		 *
		 * @param string $message Update permalinks notice message content.
		 */
		$message = apply_filters( 'wpmoly/filter/permalinks/notice/message', __( 'Changes have been made to the archive pages, permalinks settings need to be update. Simply reload the %s, no saving required.', 'wpmovielibrary' ) );

		/**
		 * Filter the update permalinks notice CSS class names.
		 *
		 * @since 3.0
		 *
		 * @param string $message Update permalinks notice message content.
		 */
		$class = apply_filters( 'wpmoly/filter/permalinks/notice/style', array(
			'notice',
			'notice-wpmoly',
			'notice-info',
			'is-dismissible',
		) );

		$class = implode( ' ', $class );

		echo '<div class="' . esc_attr( $class ) . '"><p>' . sprintf( esc_html( $message ), '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '" target="_blank">' . __( 'Permalinks page', 'wpmovielibrary' ) . '</a>' ) . '</p></div>';
	}

	/**
	 * Create admin notice transient.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function set_notice() {

		return set_transient( '_wpmoly_reset_permalinks_notice', 1 );
	}

	/**
	 * Remove admin notice transient.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function delete_notice() {

		$notice = get_transient( '_wpmoly_reset_permalinks_notice' );
		if ( 1 !== $notice ) {
			delete_transient( '_wpmoly_reset_permalinks_notice' );
		}
	}

	/**
	 * Add custom rewrite rules for movies.
	 *
	 * Define a list of variants for movies archive to match meta/detail
	 * permalinks.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param array $rules Existing rewrite rules.
	 *
	 * @return array
	 */
	private function generate_movie_archives_rewrite_rules( $rules = array() ) {

		global $wp_rewrite;

		$new_rules = array();

		/**
		 * Filter default movie archives rewrite variants.
		 *
		 * Each variant must define a rule and a matching array of vars.
		 * Defaults variants support meta/detail name translation, used
		 * to set the grid preset to 'custom'.
		 *
		 * @since 3.0.0
		 *
		 * @param array $variants Default variants.
		 */
		$variants = apply_filters( 'wpmoly/filter/movie_archives/rewrite/variants', array(
			array(
				'rule' => '([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})',
				'vars' => array(
					'year'     => '',
					'monthnum' => '',
					'day'      => '',
				),
			),
			array(
				'rule' => '([0-9]{4})/([0-9]{1,2})',
				'vars' => array(
					'year'     => '',
					'monthnum' => '',
				),
			),
			array(
				'rule' => '([0-9]{4})',
				'vars' => array(
					'year' => '',
				),
			),
			array(
				'rule' => '(adult|' . _x( 'adult', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'adult',
					'adult'  => '',
				),
			),
			array(
				'rule' => '(author|' . _x( 'author', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'author',
					'author' => '',
				),
			),
			array(
				'rule' => '(budget|' . _x( 'budget', 'permalink', 'wpmovielibrary' ) . ')/([0-9]+|[0-9]+-|[0-9]+-[0-9]+)',
				'vars' => array(
					'preset' => 'budget',
					'budget' => '',
				),
			),
			array(
				'rule' => '(certification|' . _x( 'certification', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'        => 'certification',
					'certification' => '',
				),
			),
			array(
				'rule' => '(company|production-company|production-companies|' . _x( 'company', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'  => 'company',
					'company' => '',
				),
			),
			array(
				'rule' => '(composer|' . _x( 'composer', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'   => 'composer',
					'composer' => '',
				),
			),
			array(
				'rule' => '(country|production-country|production-countries|' . _x( 'country', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'  => 'country',
					'country' => '',
				),
			),
			array(
				'rule' => '(director|' . _x( 'director', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'   => 'director',
					'director' => '',
				),
			),
			array(
				'rule' => '(format|' . _x( 'format', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'my_format',
					'format' => '',
				),
			),
			array(
				'rule' => '(language|' . _x( 'language', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'   => 'my_language',
					'language' => '',
				),
			),
			array(
				'rule' => '(languages|spoken-languages|' . _x( 'spoken-languages', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'    => 'languages',
					'languages' => '',
				),
			),
			array(
				'rule' => '(local-release|local-release-date|' . _x( 'local-release-date', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'        => 'local_release',
					'local_release' => '',
				),
			),
			array(
				'rule' => '(media|' . _x( 'media', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'my_media',
					'media'  => '',
				),
			),
			array(
				'rule' => '(photography|' . _x( 'photography', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'      => 'photography',
					'photography' => '',
				),
			),
			array(
				'rule' => '(producer|' . _x( 'producer', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'   => 'producer',
					'producer' => '',
				),
			),
			array(
				'rule' => '(rating|' . _x( 'rating', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'my_rating',
					'my_rating' => '',
				),
			),
			array(
				'rule' => '(release|release-date|' . _x( 'release-date', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'  => 'release',
					'release' => '',
				),
			),
			array(
				'rule' => '(revenue|' . _x( 'revenue', 'permalink', 'wpmovielibrary' ) . ')/([0-9]+|[0-9]+-|[0-9]+-[0-9]+)',
				'vars' => array(
					'preset'  => 'revenue',
					'revenue' => '',
				),
			),
			array(
				'rule' => '(runtime|' . _x( 'runtime', 'permalink', 'wpmovielibrary' ) . ')/([0-9]+|[0-9]+-|[0-9]+-[0-9]+)',
				'vars' => array(
					'preset'  => 'runtime',
					'runtime' => '',
				),
			),
			array(
				'rule' => '(status|' . _x( 'status', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'my_status',
					'status' => '',
				),
			),
			array(
				'rule' => '(subtitles|' . _x( 'subtitles', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset'    => 'my_subtitles',
					'subtitles' => '',
				),
			),
			array(
				'rule' => '(writer|' . _x( 'writer', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array(
					'preset' => 'writer',
					'writer' => '',
				),
			),
		) );

		$movie = get_post_type_object( 'movie' );
		$movies = is_string( $movie->has_archive ) ? $movie->has_archive : 'movies';

		if ( ! utils\movie\has_archives_page() ) {
			// Default: no archive page set
			$query = 'index.php?post_type=movie';
			$rule  = trim( $movies, '/' );
			$index = 1;
		} else {
			// Existing archive page
			$archive_page = utils\movie\get_archives_page_id();

			$index = 2;
			$query = sprintf( 'index.php?page_id=%d', $archive_page );

			$rule1 = trim( str_replace( home_url(), '', get_permalink( $archive_page ) ), '/' );
			$rule2 = trim( $movies, '/' );
			if ( 'movies' !== $rule2 ) {
				$rule2 = "movies|$rule2";
			}

			$rule = "($rule1|$rule2)";

			// Remove default archive rules.
			foreach ( $rules as $r => $v ) {
				if ( false !== strpos( $r, $rule1 ) ) {
					unset( $rules[ $r ] );
				}
			}

			// Set new default archive rules.
			$new_rules[ $rule . '/?$' ]                               = $query;
			$new_rules[ $rule . '/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $query . '&feed=' . $wp_rewrite->preg_index( $index );
			$new_rules[ $rule . '/(feed|rdf|rss|rss2|atom)/?$' ]      = $query . '&feed=' . $wp_rewrite->preg_index( $index );
			$new_rules[ $rule . '/page/([0-9]{1,})/?$' ]              = $query . '&paged=' . $wp_rewrite->preg_index( $index );
		}

		// Loop through allowed variants
		foreach ( $variants as $variant ) {

			$_query = $query;
			$i = $index;

			// Use all vars to increment counter, but don't actually
			// put empty vars in the regex.
			foreach ( $variant['vars'] as $var => $value ) {
				if ( empty( $value ) ) {
					$_query = $_query . '&' . $var . '=' . $wp_rewrite->preg_index( $i );
				} else {
					$_query = $_query . '&' . $var . '=' . $value;
				}
				$i++;
			}

			$_rule = $rule . '/' . $variant['rule'];

			$new_rules[ $_rule . '/?$' ]                               = $_query;
			$new_rules[ $_rule . '/embed/?$' ]                         = $_query . '&embed=true';
			$new_rules[ $_rule . '/trackback/?$' ]                     = $_query . '&tb=1';
			$new_rules[ $_rule . '/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $_query . '&feed=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '/(feed|rdf|rss|rss2|atom)/?$' ]      = $_query . '&feed=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '/page/([0-9]{1,})/?$' ]              = $_query . '&paged=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '/comment-page-([0-9]{1,})/?$' ]      = $_query . '&cpage=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '(?:/([0-9]+))?/?$' ]                 = $_query . '&page=' . $wp_rewrite->preg_index( $i + 1 );
		}

		return $new_rules;
	}

	/**
	 * Add custom rewrite rules for movies.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param array $rules Existing rewrite rules.
	 *
	 * @return array
	 */
	private function generate_taxonomy_archives_rewrite_rules( $rules = array() ) {

		global $wp_rewrite;

		$new_rules = array();

		$taxonomies = array( 'actor', 'collection', 'genre' );
		foreach ( $taxonomies as $taxonomy ) {

			$taxonomy = get_taxonomy( $taxonomy );
			if ( ! $taxonomy ) {
				continue;
			}

			if ( ! utils\has_archives_page( $taxonomy->name ) ) {
				continue;
			}

			$archive_page = utils\get_archives_page_id( $taxonomy->name );

			$index = 2;
			$query = sprintf( 'index.php?page_id=%d', $archive_page );

			$rule1 = $taxonomy->rewrite['slug'];
			$rule2 = trim( utils\get_taxonomy_archive_link( $taxonomy->name, 'relative' ), '/' );
			$rule  = "($rule2|$rule1)";

			foreach ( $rules as $r => $v ) {
				if ( false !== strpos( $r, $rule1 ) ) {
					unset( $rules[ $r ] );
				}
			}

			$new_rules[ $rule . '/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index ) . '&feed=' . $wp_rewrite->preg_index( $index + 1 );
			$new_rules[ $rule . '/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' ]      = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index ) . '&feed=' . $wp_rewrite->preg_index( $index + 1 );
			$new_rules[ $rule . '/([^/]+)/page/([0-9]{1,})/?$' ]              = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index ) . '&paged=' . $wp_rewrite->preg_index( $index + 1 );
			$new_rules[ $rule . '/([^/]+)/embed/?$' ]                         = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index ) . '&embed=true';
			$new_rules[ $rule . '/([^/]+)/?$' ]                               = $query . '&' . $taxonomy->name . '=' . $wp_rewrite->preg_index( $index );
		}

		return $new_rules;
	}

}
