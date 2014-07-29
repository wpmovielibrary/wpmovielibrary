<?php
/**
 * WPMovieLibrary POT Generator
 *
 * Contains the methods for generating the wpmovielibrary.pot and wpmovielibrary-admin.pot files.
 * Code is based on: http://i18n.trac.wordpress.org/browser/tools/trunk/makepot.php
 *
 * @class WPML_Makepot
 * @since 2.0
 * @package WPMovieLibrary
 * @author Geert De Deckere
 */
class WPML_Makepot {

	/**
	 * @var string Filesystem directory path for the WPMovieLibrary plugin (with trailing slash)
	 */
	public $wpmovielibrary_path;

	/**
	 * @var array All available projects with their settings
	 */
	public $projects;

	/**
	 * @var object StringExtractor
	 */
	public $extractor;

	/**
	 * @var array Rules for StringExtractor
	 */
	public $rules = array(
		'_'               => array( 'string' ),
		'__'              => array( 'string' ),
		'_e'              => array( 'string' ),
		'_c'              => array( 'string' ),
		'_n'              => array( 'singular', 'plural' ),
		'_n_noop'         => array( 'singular', 'plural' ),
		'_nc'             => array( 'singular', 'plural' ),
		'__ngettext'      => array( 'singular', 'plural' ),
		'__ngettext_noop' => array( 'singular', 'plural' ),
		'_x'              => array( 'string', 'context' ),
		'_ex'             => array( 'string', 'context' ),
		'_nx'             => array( 'singular', 'plural', null, 'context' ),
		'_nx_noop'        => array( 'singular', 'plural', 'context' ),
		'_n_js'           => array( 'singular', 'plural' ),
		'_nx_js'          => array( 'singular', 'plural', 'context' ),
		'esc_attr__'      => array( 'string' ),
		'esc_html__'      => array( 'string' ),
		'esc_attr_e'      => array( 'string' ),
		'esc_html_e'      => array( 'string' ),
		'esc_attr_x'      => array( 'string', 'context' ),
		'esc_html_x'      => array( 'string', 'context' ),
	);

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// Default path
		$this->set_wpmovielibrary_path( dirname(__FILE__) . '/../..' );

		// All available projects with their settings
		$this->projects = array(
			'wpmovielibrary' => array(
				'title'    => 'Front-end',
				'file'     => $this->wpmovielibrary_path . 'languages/wpmovielibrary.pot',
				'excludes' => array( '/admin/(.*?)', '(.*?)admin(.*?)' ),
				'includes' => array(),
			),
			'wpmovielibrary' => array(
				'title'    => 'Admin',
				'file'     => $this->wpmovielibrary_path . 'languages/wpmovielibrary-admin.pot',
				'excludes' => array(),
				'includes' => array( '/admin/(.*?)', '(.*?)admin(.*?)' ),
			),
		);

		// Ignore some strict standards notices caused by extract/extract.php
		error_reporting(E_ALL);

		// Load required files and objects
		require_once 'not-gettexted.php';
		require_once 'pot-ext-meta.php';
		require_once 'extract/extract.php';
		$this->extractor = new StringExtractor( $this->rules );
	}

	/**
	 * Sets the WooCommerce filesystem directory path
	 *
	 * @param string $path
	 * @return void
	 */
	public function set_wpmovielibrary_path( $path ) {
		$this->wpmovielibrary_path = realpath( $path ) . '/';
	}

	/**
	 * POT generator
	 *
	 * @param string $project "wpmovielibrary" or "wpmovielibrary-admin"
	 * @return bool true on success, false on error
	 */
	public function generate_pot( $project = 'wpmovielibrary' ) {
		// Unknown project
		if ( empty( $this->projects[ $project ] ) )
			return false;

		// Project config
		$config = $this->projects[ $project ];

		// Extract translatable strings from the WooCommerce plugin
		$originals = $this->extractor->extract_from_directory( $this->wpmovielibrary_path, $config['excludes'], $config['includes'] );
		/*var_dump( $config, $project );
		print_r( $originals ); die();*/

		// Build POT file
		$pot = new PO;
		$pot->entries = $originals->entries;
		$pot->set_header( 'Project-Id-Version', 'WPMovieLibrary ' . $this->wpmovielibrary_version() . ' ' . $config['title'] );
		$pot->set_header( 'Report-Msgid-Bugs-To', 'https://github.com/Askelon/wpmovielibrary/issues' );
		$pot->set_header( 'POT-Creation-Date', gmdate( 'Y-m-d H:i:s+00:00' ) );
		$pot->set_header( 'MIME-Version', '1.0' );
		$pot->set_header( 'Content-Type', 'text/plain; charset=UTF-8' );
		$pot->set_header( 'Content-Transfer-Encoding', '8bit' );
		$pot->set_header( 'PO-Revision-Date', gmdate( 'Y' ) . '-MO-DA HO:MI+ZONE' );
		$pot->set_header( 'Last-Translator', 'Charlie MERLAND <charlie@caercam.org>' );
		$pot->set_header( 'Language-Team', 'WPMovieLibrary <support@wpmovielibrary.com>' );

		// Write POT file
		$result = $pot->export_to_file( $config['file'] );

		// Add plugin header
		if ( $project == 'wpmovielibrary' ) {
			$potextmeta = new PotExtMeta;
			$potextmeta->append( $this->wpmovielibrary_path . 'wpmovielibrary.php', $config['file'] );
		}

		return $result;
	}

	/**
	 * Retrieves the WooCommerce version from the wpmovielibrary.php file.
	 *
	 * @access public
	 * @return string|false WooCommerce version number, false if not found
	 */
	public function wpmovielibrary_version() {
		// Only run this method once
		static $version;
		if ( null !== $version )
			return $version;

		// File that contains the WooCommerce version number
		$file = $this->wpmovielibrary_path . 'wpmovielibrary.php';

 		if ( is_readable( $file ) && preg_match( '/\bVersion:\s*+(\S+)/i', file_get_contents( $file ), $matches ) )
			$version = $matches[1];
		else
			$version = false;

		return $version;
	}

	/**
	 * get_first_lines function.
	 *
	 * @access public
	 * @param mixed $filename
	 * @param int $lines (default: 30)
	 * @return string|bool
	 */
	public static function get_first_lines($filename, $lines = 30) {
		$extf = fopen($filename, 'r');
		if (!$extf) return false;
		$first_lines = '';
		foreach(range(1, $lines) as $x) {
			$line = fgets($extf);
			if (feof($extf)) break;
			if (false === $line) {
				return false;
			}
			$first_lines .= $line;
		}
		return $first_lines;
	}

	/**
	 * get_addon_header function.
	 *
	 * @access public
	 * @param mixed $header
	 * @param mixed &$source
	 * @return string|bool
	 */
	public static function get_addon_header($header, &$source) {
		if (preg_match('|'.$header.':(.*)$|mi', $source, $matches))
			return trim($matches[1]);
		else
			return false;
	}
}
