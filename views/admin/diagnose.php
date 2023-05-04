<?php

$icons = array(
	'success' => 'dashicons-yes',
	'warning' => 'dashicons-no-alt',
	'error'   => 'dashicons-no',
	'null'    => 'dashicons-minus'
);

?>

<?php if ( $diagnose->is_sent() ) : ?>
	<div id="message" class="updated notice notice-success is-dismissible">
		<p><?php _e( 'Diagnose results sent!', 'wpmovielibrary' ); ?></p>
		<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php _e( 'Dismiss' ); ?></span></button>
	</div>
<?php elseif ( $diagnose->is_saved() ) : ?>
	<div id="message" class="updated notice notice-success is-dismissible">
		<p><?php _e( 'Diagnose results saved!', 'wpmovielibrary' ); ?></p>
		<button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php _e( 'Dismiss' ); ?></span></button>
	</div>
<?php endif; ?>

	<div class="wrap">
		<h1 class="hidden"><?php _e( 'WordPress Movie Library Diagnose Tool', 'wpmovielibrary' ); ?></h1>
	</div>

	<div class="diagnose-wrap">

		<div class="diagnose-box">

			<div class="diagnose-header">
				<div class="diagnose-info">
					<div class="diagnose-version">v<strong><?php echo $diagnose->get_version(); ?></strong></div>
					<div class="diagnose-last-saved"><?php _e( 'Last saved:', 'wpmovielibrary' ); ?> <strong><?php echo is_int( $diagnose->get_last_saved() ) ? date( 'Y/n/j', $diagnose->get_last_saved() ) : $diagnose->get_last_saved(); ?></strong></div>
				</div>
				<div class="diagnose-logo"><img src="<?php echo esc_url( WPMOLY_URL . '/assets/img/diagnose.png' ); ?>" alt="" /></div>
				<div class="diagnose-title"><h1><?php _e( 'Diagnose Tool', 'wpmovielibrary' ); ?></h1></div>
				<div class="diagnose-subtitle"><?php _e( 'Welcome to the WordPress Movie Library Diagnose Tool.', 'wpmovielibrary' ); ?></div>
			</div>

			<div class="diagnose-description">
				<p><?php printf( __( 'This page lets you check on the compatibility of the plugin with your current installation and the incoming version 3.0. Learn more about version 3.0 and the diagnose tool on the plugin’s official website <a href="%s">here</a>.', 'wpmovielibrary' ), esc_url( 'http://wpmovielibrary.com' ) ); ?></p>
<?php if ( ! is_int( $diagnose->get_last_saved() ) ) : ?>
				<p></p>
				<p><?php _e( 'It seems you never runned the diagnose before.', 'wpmovielibrary' ); ?> <?php printf( '<a href="%s">%s</a>', esc_url( admin_url( 'index.php?page=wpmovielibrary-diagnose&amp;diagnose=run' ) ), __( 'Do it now?', 'wpmovielibrary' ) ) ?></p>
<?php endif; ?>
			</div>

			<div class="diagnose-survey">
<?php
if ( ! $diagnose->is_dismissed() ) :
	if ( $diagnose->is_sent() ) : ?>
				<h4><?php _e( 'Thank you!', 'wpmovielibrary' ); ?></h4>
				<div class="survey-info">
					<p><?php printf( __( 'Your data have been sent to our server for processing. Your secret key is <strong>%s</strong>. This key is personal and generated from the current home URL of your website and we have no way to use it to trace it back to your site. You can check on your data anytime at %s.', 'wpmovielibrary' ), md5( home_url( '/' ) ), sprintf( '<a href="%s">%s</a>', esc_url( 'https://www.wpmovielibrary.com/survey/?skey=' . md5( home_url( '/' ) ) ), 'wpmovielibrary.com/survey/?skey=your-secret-key' ) ); ?></p>
					<p><?php printf( __( 'If you think the data you sent is outdated, you can update it in one single click: %s. We don’t provide a direct link to delete your data to ensure a minimum of consistency in the data, but you can request a deletion by droping us a mail at %s with your secret key and we’ll notify you once it’s done.', 'wpmovielibrary' ), sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'index.php?page=wpmovielibrary-diagnose&amp;survey=update' ) ), __( 'update results', 'wpmovielibrary' ) ), '<a href="' . esc_url( 'mailto:support@wpmovielibrary.com?subject=WPMovieLibrary Survey Removal Request&body=User "' . md5( home_url( '/' ) ) . '" requested its data to be removed from survey database.' ) . '">support@wpmovielibrary.com</a>' ); ?></p>
				</div>
<?php
	else :
?>
				<h2><?php _e( 'Want to help? Share this with us!', 'wpmovielibrary' ); ?></h2>
				<div class="survey-info">
					<p><big><?php _e( 'The data below could help us improve the plugin by learning how users are really using the plugin, under what environment(s) and in what condition(s).', 'wpmovielibrary' ); ?></big></p>
					<p><?php _e( 'Informations that aren’t of much importance to you, such as the number of movies, additional post types or availables themes, can be very valuable informations for us to improve the plugin: they let us know how intensively is the plugin used, what’s surrounding it and the possible conflicts it could encounter, or even cause.', 'wpmovielibrary' ); ?></p>
					<p><?php _e( 'Sharing this data is <strong>completely private</strong> and we will <strong>never</strong>, <strong>ever</strong> publish anything transmitted to us that way. Each information is listed with a description of what it actually is, and how it will benefit us to know it; data will be stored anonymously in out database for statistics and study purpose only. Once they’re transmitted <strong>we will have no way to link these data back to your site</strong>. You will be provided a secret key that will allow you to check on your data to make sure we didn’t logged something we shouldn’t have.', 'wpmovielibrary' ); ?></p>
					<p><?php _e( 'Ready to participate? Click below!', 'wpmovielibrary' ); ?></p>
					<div class="survey-submit">
						<a class="button" href="<?php echo esc_url( admin_url( 'index.php?page=wpmovielibrary-diagnose&amp;survey=answer' ) ); ?>"><?php _e( 'I want to help', 'wpmovielibrary' ); ?></a>
						<div class="survey-dismiss"><?php printf( __( 'No thanks, %s', 'wpmovielibrary' ), sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'index.php?page=wpmovielibrary-diagnose&amp;survey=dismiss' ) ), __( 'don’t ask me again.', 'wpmovielibrary' ) ) ); ?></div>
					</div>
				</div>
<?php
	endif;
else :
?>
				<div class="survey-dismissed"><?php printf( __( 'Survey dismissed. %s', 'wpmovielibrary' ), sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'index.php?page=wpmovielibrary-diagnose&amp;survey=undismiss' ) ), __( 'Changed your mind?', 'wpmovielibrary' ) ) ); ?></div>
<?php endif; ?>
			</div>

			<div class="diagnose-content">

				<div class="diagnose-separator">
					<h2><?php _e( 'Requirements', 'wpmovielibrary' ); ?></h2>
					<p><?php _e( 'What the plugin expects to work correctly.', 'wpmovielibrary' ); ?></p>
				</div>

				<div class="diagnose-left">
					<div class="diagnose-top-block"><?php _e( 'Current Version', 'wpmovielibrary' ); ?></div>
<?php foreach ( $diagnose->get_requirements( 'v2' ) as $requirement ) : ?>
					<div class="diagnose-block">
						<div class="block-title"><?php echo $requirement['title']; ?></div>
						<div class="block-content">
<?php
	foreach ( $requirement['items'] as $id => $item ) :
		$result = $diagnose->get_results( 'v2', $id );
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
<?php foreach ( $diagnose->get_requirements( 'v3' ) as $requirement ) : ?>
					<div class="diagnose-block">
						<div class="block-title"><?php echo $requirement['title']; ?></div>
						<div class="block-content">
<?php
	foreach ( $requirement['items'] as $id => $item ) :
		$result = $diagnose->get_results( 'v3', $id );
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
<?php foreach ( $diagnose->get_analysis( 'data' ) as $analysis ) : ?>
					<div class="diagnose-block">
						<div class="block-title"><?php echo $analysis['title']; ?></div>
						<div class="block-content">
<?php
	foreach ( $analysis['items'] as $id => $item ) :
		$result = $diagnose->get_contents( 'data', $id );
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
<?php foreach ( $diagnose->get_analysis( 'misc' ) as $analysis ) : ?>
					<div class="diagnose-block">
						<div class="block-title"><?php echo $block['title']; ?></div>
						<div class="block-content">
<?php
	foreach ( $analysis['items'] as $id => $item ) :
		$result = $diagnose->get_contents( 'misc', $id );
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

	</div>
