
<?php if ( ! is_null( $post_type ) ) : ?>

		<p><?php printf( __( 'You can convert this %s to Movie to access WPMovieLibrary features without duplicating your content.', 'wpmovielibrary' ), $post_type ); ?></p>
		<p id="wpmoly-convert-button">
			<a href="<?php echo wpmoly_nonce_url( admin_url( "post.php?post={$post_id}&action=edit&wpmoly_convert_post_type=1" ), 'convert-post-type' ) ?>" class="button button-primary button-large"><?php printf( __( 'Convert %s to Movie', 'wpmovielibrary' ), $post_type ); ?></a>
		</p>
<?php else : ?>
		<?php do_action( 'wpmoly_before_metabox_content' ); ?>
		<input type="hidden" id="wpmoly-autocomplete-collection" value="<?php echo wpmoly_o( 'collection-autocomplete' ); ?>" />
		<input type="hidden" id="wpmoly-autocomplete-genre" value="<?php echo wpmoly_o( 'genre-autocomplete' ); ?>" />
		<input type="hidden" id="wpmoly-autocomplete-actor" value="<?php echo wpmoly_o( 'actor-autocomplete' ); ?>" />

		<div id="wpmoly-meta" class="wpmoly-meta">

			<div id="wpmoly-meta-menu-bg"></div>
			<ul id="wpmoly-meta-menu" class="hide-if-no-js">

<?php foreach ( $tabs as $id => $tab ) : ?>

				<li id="wpmoly-meta-<?php echo $id ?>" class="tab<?php echo $tab['active'] ?>"><a href="#" onclick="wpmoly_meta_panel.navigate( '<?php echo $id ?>' ) ; return false;"><span class="<?php echo $tab['icon'] ?>"></span>&nbsp; <span class="text"><?php echo $tab['title'] ?></span></a></li>
<?php endforeach; ?>
				<li class="tab off"><a href="#" onclick="wpmoly_meta_panel.resize() ; return false;"><span class="wpmolicon icon-collapse"></span>&nbsp; <span class="text"><?php _e( 'Collapse', 'wpmovielibrary' ) ?></span></a></li>
			</ul>

			<div id="wpmoly-meta-panels">

				<?php do_action( 'wpmoly_before_metabox_panels' ); ?>

<?php foreach ( $panels as $id => $panel ) : ?>

				<div id="wpmoly-meta-<?php echo $id ?>-panel" class="panel<?php echo $panel['active'] ?> hide-if-js"><?php echo $panel['content'] ?></div>
<?php endforeach; ?>

				<?php do_action( 'wpmoly_after_metabox_panels' ); ?>
			</div>
			<div style="clear:both"></div>

		</div>

		<?php do_action( 'wpmoly_after_metabox_content' ); ?>

<?php endif; ?>