<?php
/**
 * Statistics Widget default template.
 *
 * @since 3.0.0
 *
 * @uses $widget
 * @uses $data
 */

?>

		<section id="<?php echo esc_attr( $widget->id ); ?>" class="<?php echo esc_attr( $widget->classname ); ?>">
<?php if ( ! empty( $data['title'] ) ) : ?>
			<?php echo $data['title']; ?>
<?php endif; ?>
<?php if ( ! empty( $data['description'] ) ) : ?>
			<p><?php echo $data['description']; ?></p>
<?php endif; ?>

			<?php echo $data['content']; ?>
		</section>
