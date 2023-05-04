 <?php
/**
 * Taxonomies Archive Pagination Template
 * 
 * @since    1.2
 * 
 * @uses    $links
 */

?>

	<ul class="wpmoly page-numbers">
<?php
foreach ( $links as $link ) : 
	if ( is_null( $link['url'] ) ) :
?>

		<li><span class="<?php echo $link['class']; ?>"><?php echo $link['title']; ?></span></li>
<?php
	else :
?>

		<li><a href="<?php echo $link['url']; ?>" class="<?php echo $link['class']; ?>"><?php echo $link['title']; ?></a></li>
<?php
	endif;
endforeach;
?>

	</ul>
