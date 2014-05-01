<?php
/**
 * WPMovieLibrary Ajax Class.
 * 
 * This class format the data return through AJAX calls.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPML_Ajax' ) ) :

	class WPML_Ajax {

		/**
		 * Callback status
		 * 
		 * @var    array
		 */
		public $success = false;

		/**
		 * Error storage
		 * 
		 * @var    array
		 */
		public $errors = array();

		/**
		 * Data storage for AJAX
		 * 
		 * @var    array
		 */
		public $data = array();

		/**
		 * i18n elements storage
		 * 
		 * @var    array
		 */
		public $i18n = array();

	}

endif;