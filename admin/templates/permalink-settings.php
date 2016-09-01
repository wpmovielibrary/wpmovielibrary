<?php
/**
 * Permalink Settings view Template
 * 
 * Add a metabox for custom permalinks in the permalink settings page.
 * 
 * @since    3.0
 * 
 * @uses    $settings
 */
?>

		<p><?php _e( 'These settings control the permalinks used specifically by the movie library.', 'wpmovielibrary' ); ?></p>

		<div id="wpmoly-permalinks" class="wpmoly-metabox wpmoly-tabbed-metabox">

			<div id="wpmoly-permalinks-menu" class="wpmoly-metabox-menu">
				<ul>
<?php
		$active = true;
		foreach ( $settings as $id => $setting ) {
?>
					<li class="tab<?php if ( $active ) { ?> active<?php } ?>">
					<a class="navigate" href="#wpmoly-<?php echo esc_attr( $id ); ?>"><span class="<?php echo esc_attr( $setting['icon'] ); ?>"></span><span class="title"><?php echo esc_attr( $setting['title'] ); ?></span></a></li>
<?php
			$active = false;
		}
?>
				</ul>
			</div>
			<div id="wpmoly-permalinks-content" class="wpmoly-metabox-content">
<?php
		$active = true;
		foreach ( $settings as $id => $setting ) {
?>
				<div id="wpmoly-<?php echo esc_attr( $id ); ?>" class="panel<?php if ( $active ) { ?> active<?php } ?>">
<?php
			foreach ( $setting['fields'] as $slug => $field ) {
?>
					<h4><?php echo esc_attr( $field['title'] ); ?></h4>
					<p><?php echo wp_kses( $field['description'], wp_kses_allowed_html( 'post' ) ); ?></p>
					<table class="form-table wpmoly-permalink-structure">
						<tbody>
<?php
				if ( 'radio' == $field['type'] ) {
					foreach ( $field['choices'] as $name => $choice ) {
?>
							<tr>
								<th>
									<label><input name="<?php echo esc_attr( $name ); ?>" type="radio" value="<?php echo esc_attr( $choice['value'] ); ?>" class="wctog" <?php checked( $field['default'], $name ); ?>/> <?php echo esc_attr( $choice['label'] ); ?></label>
								</th>
								<td>
<?php
						if ( 'custom' == $name ) {
?>
									<code><?php echo esc_url( home_url() . '/' ) ?></code> <input name="custom_<?php echo esc_attr( $name ); ?>" type="text" value="" class="regular-text code" />
									<p><em><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'wpmovielibrary' ); ?></em></p>
<?php
						} else {
?>
									<code><?php echo esc_html( $choice['description'] ) ?></code>
<?php
						}
?>
								</td>
							</tr>
<?php
					}
				} elseif ( 'text' == $field['type'] ) {
?>
							<tr>
								<th></th>
								<td>
									<code><?php echo esc_url( home_url() . '/' ) ?></code> <input name="<?php echo esc_attr( $slug ); ?>" type="text" value="<?php echo esc_attr( $field['default'] ); ?>" class="regular-text code" />
								</td>
							</tr>
<?php
				}
?>
						</tbody>
					</table>
<?php
			}
?>
				</div>
<?php
			$active = false;
		}
?>
			</div>
		</div>
<?php