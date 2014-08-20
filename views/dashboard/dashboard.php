<?php //do_action( 'wpml_dashboard_setup' ); ?>

	<div id="wpml-home" class="wrap">

		<h2><?php echo WPML_NAME; ?></h2>

<?php include( WPML_PATH . 'views/dashboard/welcome.php' ); ?>

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
