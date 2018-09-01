<?php
/**
 * Collection Headbox default template.
 *
 * @since 3.0.0
 *
 * @uses $headbox
 * @uses $collection
 */

?>

	<div data-headbox="<?php echo $headbox->id; ?>" data-theme="default" class="wpmoly term-headbox collection-headbox theme-<?php echo $headbox->get_theme(); ?>">
		<button class="button maximize" data-action="maximize" title="<?php esc_html_e( 'Maximize Headbox', 'wpmovielibrary' ); ?>">{{ 'svg:icon:maximize' }}</button>
		<button class="button minimize" data-action="minimize" title="<?php esc_html_e( 'Minimize Headbox', 'wpmovielibrary' ); ?>">{{ 'svg:icon:minimize' }}</button>
		<div class="headbox-header">
			<div class="headbox-thumbnail">
				<img src="<?php echo $collection->get_thumbnail(); ?>" alt="" />
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-titles">
				<div class="headbox-title">
					<div class="term-title collection-title"><a href="<?php echo get_term_link( $collection->term, 'collection' ); ?>"><?php $collection->the_name(); ?></a></div>
				</div>
				<div class="headbox-subtitle">
					<div class="term-count collection-count"><?php printf( _n( '%d Movie', '%d Movies', $collection->term->count, 'wpmovielibrary' ), $collection->term->count ); ?></div>
				</div>
			</div>
			<div class="headbox-metadata">
				<div class="headbox-description">
					<div class="term-description collection-description"><?php $collection->the_description(); ?></div>
				</div>
			</div>
		</div>
	</div>
