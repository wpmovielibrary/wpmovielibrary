<?php
/**
 * Person Headbox view Template
 *
 * Showing a person's default headbox.
 *
 * @since 3.0.0
 *
 * @uses $person
 * @uses $headbox
 */

?>

	<div data-headbox="<?php echo $headbox->id; ?>" data-theme="default" class="wpmoly headbox post-headbox person-headbox theme-<?php echo $headbox->get_theme(); ?>">
		<div class="headbox-header">
			<div class="headbox-picture">
				<img src="<?php echo $person->get_picture(); ?>" alt="" />
			</div>
		</div>
		<div class="headbox-content">
			<div class="headbox-titles">
				<div class="person-name"><a href="<?php the_permalink( $person->id ); ?>"><?php $person->the_name(); ?></a></div>
				<?php $person->the_departement(); ?>
			</div>
		</div>
		<div class="headbox-footer"></div>
	</div>
