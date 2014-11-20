<?php //do_action( 'wpmoly_dashboard_setup' ); ?>

	<div id="wpmoly-home" class="wrap">

		<h2><?php echo WPMOLY_NAME; ?><small>v<?php echo WPMOLY_VERSION; ?></small> <a href="<?php echo admin_url( 'index.php?page=wpmovielibrary-about' ) ?>"><span class="wpmolicon icon-info-o"></span></a></h2>

		<?php echo self::render_admin_template( 'dashboard/welcome.php', array( 'hidden' => $hidden ) ); ?>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( $screen->id, 'side', '' ); ?>
				</div>
			</div>
		</div>

	</div>
