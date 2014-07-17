
	<div class="widget-movies">

<?php foreach ( $items as $item ) : ?>
		<a href="<?php echo $item['link'] ?>" title="<?php echo $item['attr_title'] ?>">
			<figure class="widget-movie">
				<?php echo get_the_post_thumbnail( $item['ID'], 'thumbnail' ) ?>
			</figure>
		</a>

<?php endforeach; ?>
	</div>