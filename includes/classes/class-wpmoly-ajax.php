<?php
/**
 * WPMovieLibrary Ajax Class.
 * 
 * This class format the data return through AJAX calls.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2016 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Ajax' ) ) :

	class WPMOLY_Ajax {

		/**
		 * Data result type
		 * 
		 * @var    string
		 */
		public $result = null;

		/**
		 * Data for AJAX
		 * 
		 * @var    array
		 */
		public $data = array();

		/**
		 * AJAX message
		 * 
		 * @var    array
		 */
		public $message = null;

		/**
		 * Optional Post ID
		 * 
		 * @var    array
		 */
		public $post_id = null;

		/**
		 * Optional TMDb ID
		 * 
		 * @var    array
		 */
		public $tmdb_id = null;

		/**
		 * i18n elements storage
		 * 
		 * @var    array
		 */
		public $i18n = array();

		public function __construct( $args = array() ) {

			$defaults = array(
				'result'	=> null,
				'data'		=> array(),
				'message'	=> null,
				'post_id'	=> null,
				'tmdb_id'	=> null,
				'i18n'		=> null,
				'nonce'		=> null
			);

			$args = wp_parse_args( $args, $defaults );
			extract( $args, EXTR_SKIP );

			$this->result	= $result;
			$this->data	= $data;
			$this->message	= $message;
			$this->post_id	= $post_id;
			$this->tmdb_id	= $tmdb_id;
			$this->i18n	= $i18n;
			$this->nonce	= $nonce;
		}

	}

endif;