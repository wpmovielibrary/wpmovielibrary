<?php
/**
 * Permalink Settings view Template
 *
 * Add a metabox for custom permalinks in the permalink settings page.
 *
 * @since 3.0.0
 *
 * @uses $settings
 * @uses $permalinks
 * @uses $enabled
 */
?>

		<p><?php _e( 'These settings control the permalinks used specifically by the movie library.', 'wpmovielibrary' ); ?></p>

		<div id="wpmoly-permalinks-notice" class="wpmoly info<?php if ( true === $enabled ) : ?> hide-if-js<?php endif; ?>">
			<div class="notice-content"><p><?php _e( 'Custom permalink settings are not available because WordPress Permalinks are disabled. To enable Permalinks please go to the top of the current page and select anything other than "Plain" in the "Common Settings" section.', 'wpmovielibrary' ); ?></p></div>
		</div>

		<script type="text/javascript">var _wpmolyPermalinks = <?php echo json_encode( $permalinks ); ?>;</script>
		<div id="wpmoly-permalinks" class="wpmoly-metabox wpmoly-tabbed-metabox clearfix<?php if ( false === $enabled ) : ?> hide-if-js<?php endif; ?>">

			<div id="wpmoly-permalinks-menu" class="wpmoly-metabox-menu">
				<ul>
<?php
$active = true;
foreach ( $settings as $id => $setting ) :
?>

					<li class="tab<?php if ( $active ) { ?> active<?php } ?>"><a class="navigate" href="#wpmoly-<?php echo esc_attr( $id ); ?>"><span class="<?php echo esc_attr( $setting['icon'] ); ?>"></span><span class="title"><?php echo esc_attr( $setting['title'] ); ?></span></a></li>
<?php
$active = false;
endforeach;
?>

				</ul>
			</div>
			<div id="wpmoly-permalinks-content" class="wpmoly-metabox-content">
<?php
$active = true;
foreach ( $settings as $id => $setting ) :
?>

				<div id="wpmoly-<?php echo esc_attr( $id ); ?>" class="panel<?php if ( $active ) { ?> active<?php } ?>">
<?php
foreach ( $setting['fields'] as $slug => $field ) :
	$is_disabled = isset( $field['disabled'] ) && true === $field['disabled'];
?>
					<h4><?php echo esc_attr( $field['title'] ); ?></h4>
					<p><?php echo wp_kses( $field['description'], wp_kses_allowed_html( 'post' ) ); ?></p>
					<table class="form-table wpmoly-permalink-structure<?php echo $is_disabled ? ' disabled' : ''; ?>">
						<tbody>
<?php if ( 'radio' == $field['type'] ) : ?>

<?php
if ( empty( $permalinks[ $slug ] ) ) :
	$permalinks[ $slug ] = $field['choices'][ $field['default'] ]['value'];
endif;

$choices = array();
foreach ( $field['choices'] as $name => $choice ) :
	$choices[] = $choice['value'];
?>
							<tr>
								<th>
									<label><input id="<?php echo esc_attr( $slug . '_' . $name ); ?>" name="wpmoly_permalinks[<?php echo esc_attr( $slug ); ?>]" type="radio" data-name="<?php echo esc_attr( $slug ); ?>" value="<?php echo esc_attr( $choice['value'] ); ?>" class="" <?php checked( $choice['value'], $permalinks[ $slug ] ); ?><?php disabled( $is_disabled, true ); ?>/> <?php echo esc_attr( $choice['label'] ); ?></label>
								</th>
								<td>
									<code><?php echo esc_html( $choice['description'] ) ?></code>
								</td>
							</tr>
<?php endforeach; ?>

<?php if ( true === $field['custom'] ) : ?>

							<tr>
								<th>
									<label><input id="custom_<?php echo esc_attr( $slug ); ?>" name="wpmoly_permalinks[<?php echo esc_attr( $slug ); ?>]" type="radio" value="custom" <?php checked( in_array( $permalinks[ $slug ], $choices ), false ); ?><?php disabled( $is_disabled, true ); ?>/> <?php _e( 'Custom Structure' ); ?></label>
								</th>
								<td>
									<code><?php echo esc_url( untrailingslashit( home_url() ) ) ?></code> <input id="custom_<?php echo esc_attr( $slug ); ?>_value" name="wpmoly_permalinks[custom_<?php echo esc_attr( $slug ); ?>]" type="text" value="<?php echo ! in_array( $permalinks[ $slug ], $choices ) ? esc_attr( $permalinks[ $slug ] ) : ''; ?>" class="regular-text code custom-value" <?php disabled( $is_disabled, true ); ?>/>
									<p><em><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.<br />Note: <code>%postname%</code> and similar name tags (<code>%movie%</code>) aren’t needed; WordPress will automatically add the movie\'s name at the end of the URL.', 'wpmovielibrary' ); ?></em></p>
								</td>
							</tr>
<?php endif; ?>

<?php elseif ( 'text' == $field['type'] ) : ?>

							<tr>
								<th><label><?php echo esc_attr( $field['title'] ); ?></label></th>
								<td>
									<input id="<?php echo esc_attr( $slug ); ?>" name="wpmoly_permalinks[<?php echo esc_attr( $slug ); ?>]" type="text" value="<?php echo esc_attr( $permalinks[ $slug ] ); ?>" class="regular-text code" <?php disabled( $is_disabled, true ); ?>/>
								</td>
							</tr>
<?php endif; ?>

						</tbody>
					</table>
<?php endforeach; ?>

				</div>
<?php
$active = false;
endforeach;
?>

			</div>
		</div>
