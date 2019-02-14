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
	<div data-headbox="<?php echo $headbox->id; ?>" data-theme="default" class="wpmoly headbox post-headbox person-headbox theme-default">
		<div class="headbox-header">
			<div class="headbox-titles">
				<div class="person-name"><a href="<?php the_permalink( $person->id ); ?>"><?php $person->the_name(); ?></a></div>
			</div>
		</div>
		<div class="headbox-content"></div>
		<div class="headbox-footer"></div>
	</div>
