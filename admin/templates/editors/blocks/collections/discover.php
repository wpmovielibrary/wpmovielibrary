<?php
/**
 * Movie Editor Discover Block Template
 *
 * @since 3.0.0
 *
 * @uses $id
 * @uses $title
 */
?>

					<div id="<?php echo esc_attr( $id ); ?>-block" data-controller="<?php echo esc_attr( $controller ); ?>" class="<?php echo esc_attr( $class ); ?>">
						<button type="button" class="button arrow" data-action="collapse"><span class="wpmolicon icon-up-chevron"></span></button>
						<h3 class="block-title"><?php echo esc_html( $title ); ?></h3>
						<div class="block-content">
							<p><?php printf( _n( 'You have a total of <a href="%s">%s collection</a>.', 'You have a total of <a href="%s">%s collections</a>.', $total, 'wpmovielibrary' ), esc_url( $edit ), '<strong>' . $total . '</strong>' ); ?></p>
						</div>
					</div>
