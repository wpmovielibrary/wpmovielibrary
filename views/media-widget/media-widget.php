
	<div class="wpml-widget-description"><?php echo $description ?></div>

	<ul>
<?php foreach ( $items as $item ) : ?>
		<li><a href="<?php echo $item['link'] ?>" title="<?php echo $item['attr_title'] ?>"><?php echo $item['title'] ?></a></li>

<?php endforeach; ?>
	</ul>