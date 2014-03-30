<div class="anh_message <?php esc_attr_e( $class ); ?>">
	<?php foreach ( $this->notices[ $type ] as $notice ) : ?>
		<p><?php echo wp_kses( $notice, wp_kses_allowed_html( 'post' ) ); ?></p>
	<?php endforeach; ?>
</div>