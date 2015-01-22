<?php
/**
 * Movie Headbox Tabs Template view
 * 
 * Showing a movie's headbox tabs, Allociné style.
 * 
 * @since    2.0
 * 
 * @uses    $id
 * @uses    $tabs
 */
?>
			<div class="wpmoly headbox allocine movie content">
<?php
$hide = '';
foreach ( $tabs as $slug => $tab ) :
?>
				<div id="movie-headbox-<?php echo $slug ?>-<?php echo $id ?>" class="wpmoly headbox allocine movie content tab <?php echo $slug . $hide ?>">

<?php echo $tab['content'] ?>

				</div>

<?php
$hide = ' hide-if-js';
endforeach; ?>
			</div>