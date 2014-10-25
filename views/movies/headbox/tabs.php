<?php
/**
 * Movie Headbox Tabs Template view
 * 
 * Showing a movie's headbox tabs.
 * 
 * @since    2.0
 * 
 * @uses    $id
 * @uses    $tabs
 */
?>
		<div class="wpmoly headbox movie content">
<?php
$hide = '';
foreach ( $tabs as $slug => $tab ) :
?>
			<div id="movie-headbox-<?php echo $slug ?>-<?php echo $id ?>" class="wpmoly headbox movie content <?php echo $slug . $hide ?>">

				<h5 class="hide-if-js"><span class="wpmolicon icon-<?php echo $tab['icon'] ?>"></span> <span class="wpmoly headbox movie content tab title"><?php echo $tab['title'] ?></span></h5>

<?php echo $tab['content'] ?>

			</div>

<?php
$hide = ' hide-if-js';
endforeach; ?>
		</div>