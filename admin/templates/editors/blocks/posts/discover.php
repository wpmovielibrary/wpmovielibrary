<?php
/**
 * Post Editor 'Discover' Block Template
 *
 * @since 3.0.0
 *
 * @uses $id
 * @uses $title
 */
?>

					<div id="<?php echo esc_attr( $id ); ?>-block" data-controller="<?php echo esc_attr( $controller ); ?>" class="<?php echo esc_attr( $class ); ?>">
						<button type="button" class="button arrow" data-action="close"><span class="wpmolicon icon-up-chevron"></span></button>
						<h3 class="block-title"><?php esc_html_e( $title ); ?></h3>
						<div class="block-content">
							<p><?php printf( _n( 'You have a total of <a href="%s">%s post</a>.', 'You have a total of <a href="%s">%s posts</a>.', $total, 'wpmovielibrary' ), esc_url( $edit ), '<strong>' . $total . '</strong>' ); ?></p>
							<ul class="posts-list">
<?php if ( ! empty( $counts['publish'] ) ) { ?>
								<li class="list-item"><span class="wpmolicon icon-right-open"></span> <a href="<?php echo esc_url( admin_url( 'edit.php?post_status=publish&post_type=post' ) ); ?>" data-action="filter" data-value="publish"><?php printf( esc_html__( '%s published', '%s published', $counts['publish'], 'wpmovielibrary' ), $counts['publish'] ); ?></a></li>
<?php } if ( ! empty( $counts['future'] ) ) { ?>
								<li class="list-item"><span class="wpmolicon icon-right-open"></span> <a href="<?php echo esc_url( admin_url( 'edit.php?post_status=future&post_type=post' ) ); ?>" data-action="filter" data-value="future"><?php printf( esc_html__( '%s future', '%s future', $counts['future'], 'wpmovielibrary' ), $counts['future'] ); ?></a></li>
<?php } if ( ! empty( $counts['draft'] ) ) { ?>
								<li class="list-item"><span class="wpmolicon icon-right-open"></span> <a href="<?php echo esc_url( admin_url( 'edit.php?post_status=draft&post_type=post' ) ); ?>" data-action="filter" data-value="draft"><?php printf( esc_html__( '%s draft', '%s drafts', $counts['draft'], 'wpmovielibrary' ), $counts['draft'] ); ?></a></li>
<?php } if ( ! empty( $counts['pending'] ) ) { ?>
								<li class="list-item"><span class="wpmolicon icon-right-open"></span> <a href="<?php echo esc_url( admin_url( 'edit.php?post_status=pending&post_type=post' ) ); ?>" data-action="filter" data-value="pending"><?php printf( esc_html__( '%s pending', '%s pending', $counts['pending'], 'wpmovielibrary' ), $counts['pending'] ); ?></a></li>
<?php } if ( ! empty( $counts['private'] ) ) { ?>
								<li class="list-item"><span class="wpmolicon icon-right-open"></span> <a href="<?php echo esc_url( admin_url( 'edit.php?post_status=private&post_type=post' ) ); ?>" data-action="filter" data-value="private"><?php printf( esc_html__( '%s private', '%s private', $counts['private'], 'wpmovielibrary' ), $counts['private'] ); ?></a></li>
<?php } if ( ! empty( $counts['trash'] ) ) { ?>
								<li class="list-item"><span class="wpmolicon icon-right-open"></span> <a href="<?php echo esc_url( admin_url( 'edit.php?post_status=trash&post_type=post' ) ); ?>" data-action="filter" data-value="trash"><?php printf( esc_html__( '%s trashed', '%s trashed', $counts['trash'], 'wpmovielibrary' ), $counts['trash'] ); ?></a></li>
<?php } if ( ! empty( $counts['autodraft'] ) ) { ?>
								<li class="list-item"><span class="wpmolicon icon-right-open"></span> <a href="<?php echo esc_url( admin_url( 'edit.php?post_status=auto-draft&post_type=post' ) ); ?>" data-action="filter" data-value="auto-draft"><?php printf( esc_html__( '%s auto-draft', '%s auto-drafts', $counts['auto-draft'], 'wpmovielibrary' ), $counts['auto-draft'] ); ?></a></li>
<?php } ?>
							</ul>
							<ul class="links-list">
								<li class="list-item"><span class="wpmolicon icon-right-open"></span> <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=post' ) ); ?>"><?php esc_html_e( 'Classic post browser', 'wpmovielibrary' ); ?></a></li>
							</ul>
						</div>
					</div>
