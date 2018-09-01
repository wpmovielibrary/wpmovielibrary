<?php
/**
 * Statistics Widget default template.
 *
 * @since 3.0.0
 *
 * @uses $widget WP_Widget instance.
 * @uses $data Widget data.
 */

?>

		<section id="<?php echo esc_attr( $widget->id ); ?>" class="<?php echo esc_attr( $widget->classname ); ?>">
<?php if ( ! empty( $data['title'] ) ) : ?>
			<?php echo $data['title']; ?>
<?php endif; ?>
<?php if ( ! empty( $data['description'] ) ) : ?>
			<p><?php echo $data['description']; ?></p>
<?php endif; ?>

			<ul class="wpmoly details list">
<?php foreach ( $data['details'] as $slug => $detail ) : ?>
				<li><?php echo $detail; ?></li>
<?php endforeach; ?>
			</ul>

		</section>
