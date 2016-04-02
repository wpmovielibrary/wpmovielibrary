
		<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

		<div id="wpmoly-details" class="wpmoly-details">
			<p><em><?php _e( 'Details are personal data related to your experience of the movie: which media do you own? In which language(s)/subtitle(s)? What is the current status of your copy? How much did you enjoy it? These data are not automatically fetched like metadata, they are here to make it easier for you to manage your library.', 'wpmovielibrary' ) ?></em></p>
<?php foreach ( $fields as $slug => $field ) : ?>
			<div id="wpmoly-details-<?php echo $slug ?>" class="wpmoly-details-item wpmoly-details-<?php echo $slug ?> <?php echo $field['size'] ?> select2">
				<?php echo $field['html'] ?>
			</div>

<?php endforeach; ?>
		</div>
