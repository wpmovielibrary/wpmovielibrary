<?php if ( ! $_ajax ) : ?>
					<ul id="wpmoly-queued-list" class="wp-list-table">

<?php endif; if ( empty( $movies ) ) : ?>
						<li><div class="movietitle column-movietitle"><?php _e( 'No queued movie, dude.', 'wpmovielibrary' ) ?></div></li>
<?php else : foreach ( $movies as $movie ) : ?>
						<li id="p_<?php echo $movie['ID'] ?>">
							<div scope="row" class="check-column"><input type="checkbox" id="post_<?php echo $movie['ID'] ?>" name="movie[]" value="<?php echo $movie['ID'] ?>" onclick="wpmoly_queue_utils.toggle_button();" /></div>
							<div class="movietitle column-movietitle"><span class="movie_title"><?php echo $movie['title'] ?></span></div>
							<div class="director column-director"><span class="movie_director"><?php echo $movie['director'] ?></span></div>
							<div class="actions column-actions">
								<div class="row-actions visible">
									<span class="dequeue"><a class="dequeue_movie" id="dequeue_<?php echo $movie['ID'] ?>" href="#" title="<?php _e( 'Dequeue', 'wpmovielibrary' ) ?>" onclick="wpmoly_movies_queue.remove([<?php echo $movie['ID'] ?>]); return false;"><span class="wpmolicon icon-no"></span></a> | </span>
									<span class="delete"><a class="delete_movie" id="delete_<?php echo $movie['ID'] ?>" href="#" title="<?php _e( 'Delete', 'wpmovielibrary' ) ?>" onclick="wpmoly_import_movies.delete([<?php echo $movie['ID'] ?>]); return false;"><span class="wpmolicon icon-trash"></span></a></span>
								</div>
							</div>
							<div class="status column-status"><span class="movie_status"><?php _e( 'Queued', 'wpmovielibrary' ) ?></span></div>
						</li>
<?php endforeach; endif; if ( ! $_ajax ) : ?>
					</ul>
<?php endif; ?>
