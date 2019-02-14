<?php
/**
 * Person Headbox view Template
 *
 * Showing a person's extended headbox.
 *
 * @since 3.0.0
 *
 * @uses $person
 */

?>
	<div data-headbox="<?php echo $headbox->id; ?>" data-theme="extended" class="wpmoly headbox post-headbox person-headbox theme-extended">
		<div class="headbox-header">
			<div class="headbox-titles">
				<div class="person-name"><a href="<?php the_permalink( $person->id ); ?>"><?php $person->the_name(); ?></a></div>
			</div>
		</div>
		<div class="headbox-content"></div>
		<div class="headbox-footer"></div>
	</div>
