
		<div id="wpmoly-details" class="wpmoly-details">
			<p><em><?php _e( 'Details are personal data related to your experience of the movie: which media do you own? In which language(s)/subtitle(s)? What is the current status of your copy? How much did you enjoy it? These data are not automatically fetched like metadata, they are here to make it easier for you to manage your library.', 'wpmovielibrary' ) ?></em></p>

<?php foreach ( $details as $slug => $detail ) :
	$slug = str_replace( 'movie_', '', $slug );
?>
			<div id="wpmoly-details-<?php echo $slug ?>" class="wpmoly-details-item wpmoly-details-<?php echo $slug ?>">
				<h4 class="wpmoly-details-item-title"><span class="<?php echo $detail['icon'] ?>"></span>&nbsp; <?php echo $detail['title'] ?></h4>
				<div class="redux-field-init redux-field-container redux-field redux-container-select">
					<?php echo $detail['html'] ?>
				</div>
			</div>

<?php endforeach; ?>
		</div>
