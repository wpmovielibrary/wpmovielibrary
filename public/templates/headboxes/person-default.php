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
				<div class="person-department"><?php $person->the_department(); ?></div>
			</div>
			<div class="person-bio">
				<div class="person-birthday"><?php printf( __( 'Born %s', 'wpmovielibrary' ), date_i18n( get_option( 'date_format', 'j, F Y' ), strtotime( $person->get_birthday() ) ) ); ?></div>
				<div class="person-place-of-birth"><?php printf( __( 'In %s', 'wpmovielibrary' ), $person->get_the_place_of_birth() ); ?></div>
			</div>
		</div>
		<div class="headbox-footer"></div>
	</div>
