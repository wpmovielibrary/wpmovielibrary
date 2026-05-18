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
class Template
{
    /**
     * Template directory.
     *
     * @since 6.0.0
     * 
     * @access private
     *
     * @var string
     */
    private string $templateDir;

    /**
     * Cache directory for compiled templates.
     *
     * @since 6.0.0
     *
     * @access private
     *
     * @var string
     */
    private string $cacheDir;

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
    private ?string $currentSection = null;

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
    * @param string $templateDir Directory where templates are located.
    * @param string $cacheDir    Directory where compiled templates will be stored.
    * @param bool   $cache       Whether to enable caching of compiled templates.
    */
    public function __construct( string $templateDir, string $cacheDir, bool $cache = true ) {

        $this->templateDir = rtrim( $templateDir, '/' );
        $this->cacheDir    = rtrim( $cacheDir, '/' );
        $this->cache       = $cache;

        if ( ! is_dir( $this->cacheDir ) ) {
            mkdir( $this->cacheDir, 0755, true );
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
        $this->sections       = [];
        $this->currentSection = null;
        $this->layout         = null;

        $compiled = $this->compile( $name );
        $output   = $this->execute( $compiled, $data );

        // If the template declares @extends, render the layout
        if ( null !== $this->layout ) {
            $layout          = $this->layout;
            $this->layout    = null; // avoid infinite loop if layout also has @extends
            $layoutCompiled  = $this->compile( $layout );
            $output          = $this->execute( $layoutCompiled, $data );
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
     * @return string Path to the compiled PHP file ready for execution
     */
    private function compile( string $name ) {

        $sourcePath = $this->resolvePath($name);
        $cachePath  = $this->cachePath($name);

        if ($this->cache) {
            if (!file_exists($cachePath) || filemtime($sourcePath) > filemtime($cachePath)) {
                $compiled = $this->applyDirectives(file_get_contents($sourcePath));
                file_put_contents($cachePath, $compiled);
            }
            return file_get_contents($cachePath);
        }

        return $this->applyDirectives(file_get_contents($sourcePath));
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
    private function applyDirectives( string $source ) {

        // 1. Remove Blade comments {{-- ... --}}
        $source = preg_replace('/\{\{--.*?--\}\}/s', '', $source );

        // 2. Layout directives
        $source = preg_replace(
            '/@extends\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
            '<?php $this->setLayout("$1" ); ?>',
            $source
        );
        $source = preg_replace(
            '/@section\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
            '<?php $this->startSection("$1" ); ?>',
            $source
        );
        $source = preg_replace(
            '/@endsection/',
            '<?php $this->endSection(); ?>',
            $source
        );

        // @yield with default value : @yield('title', 'Default Title')
        $source = preg_replace(
            '/@yield\s*\(\s*[\'"](.+?)[\'"]\s*,\s*(.+?)\s*\)/',
            '<?php echo $this->yieldSection("$1", $2); ?>',
            $source
        );
        // @yield without default value : @yield('title')
        $source = preg_replace(
            '/@yield\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
            '<?php echo $this->yieldSection("$1"); ?>',
            $source
        );

        // 3. @include — pass all local variables via get_defined_vars()
        // @include with additional data : @include('partial', ['foo' => 'bar'])
        $source = preg_replace(
            '/@include\s*\(\s*[\'"](.+?)[\'"]\s*,\s*(\[.+?\])\s*\)/',
            '<?php echo $this->renderInclude("$1", array_merge(get_defined_vars(), $2)); ?>',
            $source
        );
        // @include simple : @include('partial')
        $source = preg_replace(
            '/@include\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
            '<?php echo $this->renderInclude("$1", get_defined_vars()); ?>',
            $source
        );
        // @props for defining variables with defaults in included templates
        $source = preg_replace_callback(
            '/@props\s*\(\s*(\[.+?\])\s*\)/s',
            function (array $matches): string {
                return '<?php extract(array_merge(' . $matches[1] . ', get_defined_vars()), EXTR_SKIP); ?>';
            },
            $source
        );

        // 4. Conditions
        $source = preg_replace( '/@if\s*\((.+?)\)/s',     '<?php if ( $1): ?>',     $source );
        $source = preg_replace( '/@elseif\s*\((.+?)\)/s', '<?php elseif ( $1): ?>', $source );
        $source = preg_replace( '/@else/',                '<?php else: ?>',         $source );
        $source = preg_replace( '/@endif/',               '<?php endif; ?>',        $source );

        // 5. Loops
        $source = preg_replace( '/@foreach\s*\((.+?)\)/s', '<?php foreach ( $1): ?>', $source );
        $source = preg_replace( '/@endforeach/',           '<?php endforeach; ?> ',   $source );
        $source = preg_replace( '/@for\s*\((.+?)\)/s',     '<?php for ( $1): ?>',     $source );
        $source = preg_replace( '/@endfor/',               '<?php endfor; ?>',        $source );
        $source = preg_replace( '/@while\s*\((.+?)\)/s',   '<?php while ( $1): ?>',   $source );
        $source = preg_replace( '/@endwhile/',             '<?php endwhile; ?>',      $source );

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
     * Executes a compiled file with the provided variables and returns the output.
     * The include is in a class method so that $this is available
     * in the templates (setLayout, startSection, etc.).
     * 
     * @since 6.0.0
     * 
     * @access private
     * 
     * @param string $__compiled The compiled PHP code to execute.
     * @param array $__data Variables to extract into the template's scope.
     * 
     * @return string The output generated by the template.
     */
    private function execute( string $__compiled, array $__data ): string
    {
        extract( $__data, EXTR_SKIP );

        ob_start();
        eval( '?>' . $__compiled );

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
    private function renderInclude( string $__name, array $__data ) {

        // Filter out any variables that could interfere with the template execution
        $blacklist = [ 'compiledPath', 'data', 'blacklist', 'this' ];
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
    public function setLayout( string $name ) {

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
    public function startSection( string $name ) {

        $this->currentSection = $name;
        ob_start();
    }

    /**
     * Called by @endsection — stops capturing and stores the section
     * 
     * @since 6.0.0
     * 
     * @access public
     */
    public function endSection() {

        if ( null === $this->currentSection ) {
            throw new \RuntimeException( 'Template: @endsection sans @section correspondant.' );
        }

        $this->sections[ $this->currentSection ] = ob_get_clean();
        $this->currentSection = null;
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
    public function yieldSection( string $name, ?string $default = null ) {

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
    private function resolvePath( string $name ) {

        // Convert dot notation to directory separators, e.g., 'admin.dashboard' => 'admin/dashboard'
        $name = str_replace( '.', '/', $name );
        $path = realpath( $this->templateDir . '/' . $name . '.php' );

        if ( false === $path || ! str_starts_with( $path, $this->templateDir ) ) {
            throw new \RuntimeException("Template: chemin invalide — {$name}");
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
    private function cachePath( string $name ) {

        // Convert dot and slash notation to underscores, e.g., 'admin.dashboard' => 'admin_dashboard'
        $name = str_replace( [ '.', '/' ], '_', $name );

        return realpath( $this->cacheDir . '/' . $name . '.php' ) ?: $this->cacheDir . '/' . $name . '.php';
    }
}