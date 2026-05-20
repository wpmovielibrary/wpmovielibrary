<?php
/**
 * Define the template engine class.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

namespace WPMovieLibrary;

/**
 * Template engine class inspired by Blade.
 *
 * @since 6.0.0
 * @author Charlie Merland <charlie@caercam.org>
 */
class Template {

	/**
	 * Template directory.
	 *
	 * @since 6.0.0
	 * 
	 * @access private
	 *
	 * @var string
	 */
	private string $template_dir;

	/**
	 * Cache directory for compiled templates.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private string $cache_dir;

	/**
	 * Whether to enable caching of compiled templates.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var bool
	 */
	private bool $cache;

	/**
	 * Collected Sections during the execution of a child template.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private array $sections = [];

	/**
	 * Section currently being captured.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var string|null
	 */
	private ?string $current_section = null;

	/**
	 * Layout declared via @extends.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @var string|null
	 */
	private ?string $layout = null;

	/**
	* Constructor.
	*
	* @since 6.0.0
	*
	* @access public
	*
	* @param string $template_dir Directory where templates are located.
	* @param string $cache_dir	Directory where compiled templates will be stored.
	* @param bool   $cache		Whether to enable caching of compiled templates.
	*/
	public function __construct( string $template_dir, string $cache_dir, bool $cache = true ) {

		$this->template_dir = rtrim( $template_dir, '/' );
		$this->cache_dir	= rtrim( $cache_dir, '/' );
		$this->cache		= $cache;

		if ( ! is_dir( $this->cache_dir ) ) {
			mkdir( $this->cache_dir, 0755, true );
		}
	}

	/**
	 * Compile and execute a template, returning the produced HTML.
	 * 
	 * Note: $data is passed to the template as local variables.
	 * For example, ['foo' => 'bar'] will make $foo available in the
	 * template with the value 'bar'.
	 * 
	 * @since 6.0.0
	 * 
	 * @access public
	 *
	 * @param string $name  Relative path without extension, e.g., 'admin/dashboard'
	 * @param array  $data  Variables injected into the template
	 * 
	 * @return string Rendered HTML output
	 */
	public function render( string $name, array $data = [] ) {

		// Reset state for each root render
		$this->sections		= [];
		$this->current_section = null;
		$this->layout		  = null;

		$compiled = $this->compile( $name );
		$output   = $this->execute( $compiled, $data );

		// If the template declares @extends, render the layout
		if ( null !== $this->layout ) {
			$layout		  = $this->layout;
			$this->layout	= null; // avoid infinite loop if layout also has @extends
			$layout_compiled = $this->compile( $layout );
			$output		  = $this->execute( $layout_compiled, $data );
		}

		return $output;
	}

	/**
	 * Returns the path to the compiled file (from cache or recompiled if necessary).
	 * 
	 * @since 6.0.0
	 * 
	 * @access private
	 * 
	 * @param string $name Relative path of the template to compile, e.g., 'admin/dashboard'
	 * 
	 * @return array Path to the compiled PHP file ready for execution
	 */
	private function compile( string $name ) {

		$source_path = $this->resolve_path( $name );

		if ( $this->cache ) {
			$cache_path = $this->cache_path( $name );
			if ( ! file_exists( $cache_path ) || filemtime( $source_path ) > filemtime( $cache_path ) ) {
				file_put_contents( $cache_path, $this->apply_directives( file_get_contents( $source_path ) ) );
			}
			return [ 'path' => $cache_path ];
		}

		return [ 'source' => $this->apply_directives( file_get_contents( $source_path ) ) ];
	}

	/**
	 * Apply Blade-like directives to the template source code, transforming it into pure PHP.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @param string $source The source code of the template to compile.
	 *
	 * @return string Compiled code ready for execution.
	 */
	private function apply_directives( string $source ) {

		// 1. Remove Blade comments {{-- ... --}}
		$source = preg_replace('/\{\{--.*?--\}\}/s', '', $source );

		// 2. Layout directives
		$source = preg_replace(
			'/@extends\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
			'<?php $this->set_layout("$1" ); ?>',
			$source
		);
		$source = preg_replace(
			'/@section\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
			'<?php $this->start_section("$1" ); ?>',
			$source
		);
		$source = preg_replace(
			'/@endsection/',
			'<?php $this->end_section(); ?>',
			$source
		);

		// @yield with default value : @yield('title', 'Default Title')
		$source = preg_replace(
			'/@yield\s*\(\s*[\'"](.+?)[\'"]\s*,\s*(.+?)\s*\)/',
			'<?php echo $this->yield_section("$1", $2); ?>',
			$source
		);
		// @yield without default value : @yield('title')
		$source = preg_replace(
			'/@yield\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
			'<?php echo $this->yield_section("$1"); ?>',
			$source
		);

		// 3. @include — pass all local variables via get_defined_vars()
		// @include with additional data : @include('partial', ['foo' => 'bar'])
		$source = preg_replace(
			'/@include\s*\(\s*[\'"](.+?)[\'"]\s*,\s*(\[.+?\])\s*\)/',
			'<?php echo $this->render_include("$1", array_merge(get_defined_vars(), $2)); ?>',
			$source
		);
		// @include simple : @include('partial')
		$source = preg_replace(
			'/@include\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
			'<?php echo $this->render_include("$1", get_defined_vars()); ?>',
			$source
		);
		// @props for defining variables with defaults in included templates
		$source = preg_replace_callback(
			'/@props\s*\(\s*(\[.+?\])\s*\)/s',
			fn(array $m): string => '<?php extract(array_merge(' . $m[1] . ', get_defined_vars()), EXTR_SKIP); ?>',
			$source
		);

		// 4. Conditions
		$source = preg_replace( '/@if\s*\((.+?)\)/s',	 '<?php if ( $1): ?>',	 $source );
		$source = preg_replace( '/@elseif\s*\((.+?)\)/s', '<?php elseif ( $1): ?>', $source );
		$source = preg_replace( '/@else/',				'<?php else: ?>',		 $source );
		$source = preg_replace( '/@endif/',			   '<?php endif; ?>',		$source );

		// 5. Loops
		$source = preg_replace( '/@foreach\s*\((.+?)\)/s', '<?php foreach ( $1): ?>', $source );
		$source = preg_replace( '/@endforeach/',		   '<?php endforeach; ?> ',   $source );
		$source = preg_replace( '/@for\s*\((.+?)\)/s',	 '<?php for ( $1): ?>',	 $source );
		$source = preg_replace( '/@endfor/',			   '<?php endfor; ?>',		$source );
		$source = preg_replace( '/@while\s*\((.+?)\)/s',   '<?php while ( $1): ?>',   $source );
		$source = preg_replace( '/@endwhile/',			 '<?php endwhile; ?>',	  $source );

		// 6. Display — order is important: {!! before {{ to avoid conflicts
		$source = preg_replace(
			'/\{!!\s*(.+?)\s*!!\}/s',
			'<?php echo $1; ?>',
			$source
		);
		$source = preg_replace(
			'/\{\{\s*(.+?)\s*\}\}/s',
			'<?php echo htmlspecialchars((string)( $1), ENT_QUOTES, \'UTF-8\' ); ?>',
			$source
		);


		return $source;
	}

	/**
	 * Executes a compiled template and returns the output.
	 * Uses include when cache is active (compiled to disk), eval() otherwise
	 * to avoid writing potentially broken templates to disk in development.
	 *
	 * @since 6.0.0
	 *
	 * @access private
	 *
	 * @param array|string $__compiled Compiled template — either ['path' => ...] or ['source' => ...]
	 * @param array $__data	 Variables to extract into the template's scope.
	 *
	 * @return string The output generated by the template.
	 */
	private function execute( array|string $__compiled, array $__data ) {

		extract( $__data, EXTR_SKIP );

		ob_start();
		if ( isset( $__compiled['path'] ) ) {
			include $__compiled['path'];
		} else {
			eval( '?>' . $__compiled['source'] );
		}

		return ob_get_clean();
	}

	/**
	 * Variant of execute() for @include: minimal scope to prevent
	 * internal variables of execute() from leaking into the included template.
	 * 
	 * @since 6.0.0
	 * 
	 * @access private
	 * 
	 * @param string $__name Template name to include, e.g., 'admin/partial'
	 * @param array $__data Variables to extract into the included template's scope.
	 * 
	 * @return string The content generated by the included template.
	 */
	private function render_include( string $__name, array $__data ) {

		// Filter out any variables that could interfere with the template execution
		$blacklist = [ 'compiled', 'data', 'blacklist', 'this' ];
		foreach ( $blacklist as $key ) {
			unset( $__data[ $key ] );
		}

		$compiled = $this->compile( $__name );

		return $this->execute( $compiled, $__data );
	}

	/**
	 * Called by @extends in the compiled template
	 * 
	 * @since 6.0.0
	 * 
	 * @access public
	 * 
	 * @param string $name Layout template name, e.g., 'admin/layout'
	 */
	public function set_layout( string $name ) {

		$this->layout = $name;
	}

	/**
	 * Called by @section — starts capturing content
	 * 
	 * @since 6.0.0
	 * 
	 * @access public
	 * 
	 * @param string $name Section name, e.g., 'content'
	 */
	public function start_section( string $name ) {

		$this->current_section = $name;
		ob_start();
	}

	/**
	 * Called by @endsection — stops capturing and stores the section
	 * 
	 * @since 6.0.0
	 * 
	 * @access public
	 */
	public function end_section() {

		if ( null === $this->current_section ) {
			throw new \RuntimeException( 'Template: @endsection without matching @section.' );
		}

		$this->sections[ $this->current_section ] = ob_get_clean();
		$this->current_section = null;
	}

	/**
	 * Called by @yield in the layout — returns the section or the default value
	 * 
	 * @since 6.0.0
	 * 
	 * @access public
	 * 
	 * @param string $name Section name, e.g., 'content'
	 * @param string|null $default Default value if the section is not defined
	 * 
	 * @return string The content of the section or the default value
	 */
	public function yield_section( string $name, ?string $default = null ) {

		return $this->sections[ $name ] ?? $default ?? '';
	}

	/**
	 * Resolves the full path to a template file.
	 * 
	 * @since 6.0.0
	 * 
	 * @access private
	 * 
	 * @param string $name Template name, e.g., 'admin.dashboard'
	 * 
	 * @return string The full path to the template file
	 */
	private function resolve_path( string $name ) {

		// Convert dot notation to directory separators, e.g., 'admin.dashboard' => 'admin/dashboard'
		$name = str_replace( '.', '/', $name );
		$path = realpath( $this->template_dir . '/' . $name . '.php' );

		if ( false === $path || ! str_starts_with( $path, $this->template_dir ) ) {
			throw new \RuntimeException( "Template: invalid path — {$name}" );
		}

		return $path;
	}

	/**
	 * Returns the path to the compiled file in the cache directory.
	 * 
	 * @since 6.0.0
	 * 
	 * @access private
	 * 
	 * @param string $name Template name, e.g., 'admin.dashboard'
	 * 
	 * @return string The full path to the compiled template file in the cache
	 */
	private function cache_path( string $name ) {

		// Convert dot and slash notation to underscores, e.g., 'admin.dashboard' => 'admin_dashboard'
		$name = str_replace( [ '.', '/' ], '_', $name );

		return realpath( $this->cache_dir . '/' . $name . '.php' ) ?: $this->cache_dir . '/' . $name . '.php';
	}
}