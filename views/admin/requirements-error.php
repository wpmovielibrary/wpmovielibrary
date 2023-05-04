<div class="error">
	<p><?php _e( 'WPMovieLibrary error: your environment does not meet all of the system requirements listed below.', 'wpmovielibrary' ); ?></p>

	<ul class="ul-disc">
		<li>
			<strong>PHP <?php echo WPMOLY_REQUIRED_PHP_VERSION; ?>+</strong>
			<em><?php printf( __( '(You\'re running version %s)', 'wpmovielibrary' ), PHP_VERSION ); ?></em>
		</li>
		<li>
			<strong>WordPress <?php echo WPMOLY_REQUIRED_WP_VERSION; ?>+</strong>
			<em><?php printf( __( '(You\'re running version %s)', 'wpmovielibrary' ), esc_html( $wp_version ) ); ?></em>
		</li>
	</ul>

	<p><?php _e( 'If you need to upgrade your version of PHP you can ask your hosting company for assistance, and if you need help upgrading WordPress you can refer to <a href="http://codex.wordpress.org/Upgrading_WordPress">the Codex</a>.', 'wpmovielibrary' ); ?></p>
</div>