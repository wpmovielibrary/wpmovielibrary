<?php
/**
 * Genre Headbox default template.
 *
 * @since 3.0.0
 *
 * @uses $headbox
 * @uses $genre
 */

?>
	<div id="genre-headbox-<?php echo $headbox->id; ?>" class="wpmoly term-headbox genre-headbox theme-<?php echo $headbox->get_theme(); ?>">
		<div class="headbox-header">
			<div class="headbox-thumbnail">
				<img src="<?php echo $genre->get_thumbnail(); ?>" alt="" />
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-titles">
				<div class="headbox-title">
					<div class="term-title genre-title"><a href="<?php echo get_term_link( $genre->term, 'genre' ); ?>"><?php $genre->the_name(); ?></a></div>
				</div>
				<div class="headbox-subtitle">
					<div class="term-count genre-count"><?php printf( _n( '%d Movie', '%d Movies', $genre->term->count, 'wpmovielibrary' ), $genre->term->count ); ?></div>
				</div>
			</div>
			<div class="headbox-metadata">
				<div class="headbox-description">
					<div class="term-description genre-description"><?php $genre->the_description(); ?></div>
				</div>
			</div>
		</div>
	</div>
