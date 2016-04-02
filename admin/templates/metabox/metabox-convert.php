
		<p><?php printf( __( 'You can convert this %s to Movie to access WPMovieLibrary features without duplicating your content.', 'wpmovielibrary' ), $post_type ); ?></p>
		<p id="wpmoly-convert-button">
			<a href="<?php echo wpmoly_nonce_url( admin_url( "post.php?post={$post_id}&action=edit&wpmoly_convert_post_type=1" ), 'convert-post-type' ) ?>" class="button button-primary button-large"><?php printf( __( 'Convert %s to Movie', 'wpmovielibrary' ), $post_type ); ?></a>
		</p>
