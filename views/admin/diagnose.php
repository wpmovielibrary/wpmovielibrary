<?php

$icons = array(
	'success' => 'dashicons-yes',
	'warning' => 'dashicons-no-alt',
	'error'   => 'dashicons-no',
	'null'    => 'dashicons-minus'
);

?>

	<div class="wrap diagnose-wrap">

		<div class="diagnose-header">
			<div class="diagnose-info">
				<div class="diagnose-version">v<strong><?php echo $version; ?></strong></div>
				<div class="diagnose-last-run"><?php _e( 'Last run:', 'wpmovielibrary' ); ?> <strong><?php echo is_int( $last_run ) ? date( 'Y/n/j', $last_run ) : $last_run; ?></strong></div>
			</div>
			<div class="diagnose-logo"><img src="<?php echo esc_url( WPMOLY_URL . '/assets/img/diagnose.png' ); ?>" alt="" /></div>
			<div class="diagnose-title"><h1><?php _e( 'Diagnose Tool', 'wpmovielibrary' ); ?></h1></div>
			<div class="diagnose-subtitle"><?php _e( 'Welcome to the WordPress Movie Library Diagnose Tool.', 'wpmovielibrary' ); ?></div>
		</div>

		<div class="diagnose-description">
			<p><?php printf( __( 'This page lets you check on the compatibility of the plugin with your current installation and the incoming version 3.0. Learn more about version 3.0 and the diagnose tool on the plugin’s official website <a href="%s">here</a>.', 'wpmovielibrary' ), esc_url( 'http://wpmovielibrary.com' ) ); ?></p>
		</div>

		<div class="diagnose-survey">
			<h2><?php _e( 'Want to help? Share this with us!', 'wpmovielibrary' ); ?></h2>
			<div class="survey-info">
				<p><big><?php _e( 'The data below could help us improve the plugin by learning how users are really using the plugin, under what environment(s) and in what condition(s).', 'wpmovielibrary' ); ?></big></p>
				<p><?php _e( 'Informations that aren’t of much importance to you, such as the number of movies, additional post types or availables themes, can be very valuable informations for us to improve the plugin: they let us know how intensively is the plugin used, what’s surrounding it and the possible conflicts it could encounter, or even cause.', 'wpmovielibrary' ); ?></p>
				<p><?php _e( 'Sharing this data is <strong>completely private</strong> and we will <strong>never</strong>, <strong>ever</strong> publish anything transmitted to us that way. Each information is listed with a description of what it actually is, and how it will benefit us to know it; data will be stored anonymously in out database for statistics and study purpose only. Once they’re transmitted <strong>we will have no way to link these data back to your site</strong>. You will be provided a secret key that will allow you to check on your data to make sure we didn’t logged something we shouldn’t have.', 'wpmovielibrary' ); ?></p>
				<p><?php _e( 'Ready to participate? Click below!', 'wpmovielibrary' ); ?></p>
				<div class="survey-submit">
					<a class="button" href="#"><?php _e( 'I want to help', 'wpmovielibrary' ); ?></a>
					<div class="survey-dismiss"><?php printf( __( 'No thanks, %s', 'wpmovielibrary' ), sprintf( '<a href="%s">%s</a>', esc_url( '#' ), __( 'don’t ask me again.', 'wpmovielibrary' ) ) ); ?></div>
				</div>
			</div>
		</div>

		<div class="diagnose-content">

			<div class="diagnose-separator">
				<h2><?php _e( 'Requirements', 'wpmovielibrary' ); ?></h2>
				<p><?php _e( 'What the plugin expects to work correctly.', 'wpmovielibrary' ); ?></p>
			</div>

			<div class="diagnose-left">
				<div class="diagnose-top-block"><?php _e( 'Current Version', 'wpmovielibrary' ); ?></div>
<?php foreach ( $items['requirements']['v2'] as $block ) : ?>
				<div class="diagnose-block">
					<div class="block-title"><?php echo $block['title']; ?></div>
					<div class="block-content">
<?php
	foreach ( $block['items'] as $id => $item ) :
		$result = $results['v2'][ $id ];
		$type = ! empty( $result['type'] && isset( $icons[ $result['type'] ] ) ) ? $result['type'] : 'null';
		$icon = $icons[ $type ];
?>
						<div class="diagnose-item item-<?php echo $type; ?>">
							<span class="dashicons <?php echo $icon; ?>"></span>
							<span class="item-title"><?php echo $item['title']; ?></span>
							<span class="item-description"><?php echo $item['description']; ?><?php if ( ! empty( $result['message'] ) ) : ?> <strong><?php echo $result['message']; ?></strong><?php endif; ?></span>
						</div>
<?php endforeach; ?>
					</div>
				</div>
<?php endforeach; ?>
			</div>

			<div class="diagnose-right">
				<div class="diagnose-top-block"><?php _e( 'Version 3.0', 'wpmovielibrary' ); ?></div>
<?php foreach ( $items['requirements']['v3'] as $block ) : ?>
				<div class="diagnose-block">
					<div class="block-title"><?php echo $block['title']; ?></div>
					<div class="block-content">
<?php
	foreach ( $block['items'] as $id => $item ) :
		$result = $results['v3'][ $id ];
		$type = ! empty( $result['type'] && isset( $icons[ $result['type'] ] ) ) ? $result['type'] : 'null';
		$icon = $icons[ $type ];
?>
						<div class="diagnose-item item-<?php echo $type; ?>">
							<span class="dashicons <?php echo $icon; ?>"></span>
							<span class="item-title"><?php echo $item['title']; ?></span>
							<span class="item-description"><?php echo $item['description']; ?><?php if ( ! empty( $result['message'] ) ) : ?> <strong><?php echo $result['message']; ?></strong><?php endif; ?></span>
						</div>
<?php endforeach; ?>
					</div>
				</div>
<?php endforeach; ?>
			</div>

			<div class="diagnose-separator">
				<h2><?php _e( 'Content Analysis', 'wpmovielibrary' ); ?></h2>
				<p><?php _e( 'Some insight on your library and site content.', 'wpmovielibrary' ); ?></p>
			</div>

			<div class="diagnose-left">
				<div class="diagnose-top-block"><?php _e( 'Plugin Data', 'wpmovielibrary' ); ?></div>
<?php foreach ( $items['analysis']['data'] as $block ) : ?>
				<div class="diagnose-block">
					<div class="block-title"><?php echo $block['title']; ?></div>
					<div class="block-content">
<?php
	foreach ( $block['items'] as $id => $item ) :
		$result = $analysis['data'][ $id ];
?>
						<div class="diagnose-item">
							<span class="item-title"><?php echo $item['title']; ?></span>
							<span class="item-value"><?php echo ! empty( $result ) ? $result : '−'; ?></span>
							<span class="item-description"><?php echo $item['description']; ?></span>
						</div>
<?php endforeach; ?>
					</div>
				</div>
<?php endforeach; ?>
			</div>

			<div class="diagnose-right">
				<div class="diagnose-top-block"><?php _e( 'General Data', 'wpmovielibrary' ); ?></div>
<?php foreach ( $items['analysis']['misc'] as $block ) : ?>
				<div class="diagnose-block">
					<div class="block-title"><?php echo $block['title']; ?></div>
					<div class="block-content">
<?php
	foreach ( $block['items'] as $id => $item ) :
		$result = $analysis['misc'][ $id ];
?>
						<div class="diagnose-item">
							<span class="item-title"><?php echo $item['title']; ?></span>
							<span class="item-value"><?php echo ! empty( $result ) ? $result : '−'; ?></span>
							<span class="item-description"><?php echo $item['description']; ?></span>
						</div>
<?php endforeach; ?>
					</div>
				</div>
<?php endforeach; ?>
			</div>
		</div>

		<div class="diagnose-footer">
		</div>

	</div>
