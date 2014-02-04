<?php
# th0th's Movie Collection plugin
# content filter for post type movie
?>
<div id="single-movie">
	<div class="poster">
		<?php echo get_post_meta($post->ID, 'html_poster', true); ?>
	</div>
	<div class="scrap">
		<?php foreach ( $fields_to_display as $field ) { ?>
		<div class="field" id="<?php echo $field; ?>">
			<span class="field-name"><?php echo strtoupper(str_replace('_', ' ', $field)); ?>: </span>
			<?php
			$meta = get_post_meta($post->ID, $field, true);

			# specific function for rating
			if ( $field == 'rating' ) {
				$stars = round($meta / 2);
				?>
				<div title="<?php echo $meta; ?> / 10" class="rating-stars stars<?php echo $stars; ?>"></div>
				<?php
			} else {
				# implode if meta data is an array
				if ( is_array($meta) ) {
					echo implode(', ', $meta);
				} else {
					echo $meta;
				}
			} ?>
		</div>
		<?php } ?>
	</div>
	<hr>
	<div class="review">
			<?php echo $content; ?>
	</div>
	<div class="clear"></div>
</div>