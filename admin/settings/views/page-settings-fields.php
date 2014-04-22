<?php 

if ( 'input' == $_type ) : ?>

	<input type="text" id="<?php echo $_id ?>" name="<?php echo $_name ?>" value="<?php echo $_value ?>" size="40" maxlength="32" />
<?php if ( 'apikey' == $field['id'] ) : ?>
	<input id="APIKey_check" type="button" name="APIKey_check" class="button button-secondary" value="<?php _e( 'Check API Key', 'wpml' ); ?>" />
<?php endif; ?>
	<p class="description"><?php echo $field['description'] ?></p>

<?php elseif ( 'toggle' == $_type ) : ?>

	<label><input type="radio" id="<?php echo $_id ?>" name="<?php echo $_name ?>" value="1"<?php checked( $_value, 1 ); ?>/> <?php echo $_title ?></label>
	<label><input type="radio" id="<?php echo $_id ?>" name="<?php echo $_name ?>" value="0"<?php checked( $_value, 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpml' ); ?></label>
	<p class="description"><?php echo $field['description'] ?></p>

<?php elseif ( 'select' == $_type || 'multiple' == $_type ) : ?>

	<select id="<?php echo $_id ?>" name="<?php echo $_name ?>[]"<?php if ( 'multiple' == $_type ) echo ' multiple="multiple"' ?>>
<?php foreach ( $field['values'] as $slug => $option ) : ?>
		<option value="<?php echo $slug ?>"<?php echo ( is_array( $_value ) ? ( in_array( $slug, $_value ) ? ' selected="selected"' : '' ) : selected( $_value, $slug, true ) ); ?>><?php echo $option ?></option>
<?php endforeach; ?>
	</select>
	<p class="description"><?php echo $field['description'] ?></p>

<?php elseif ( 'sorted' == $_type ) : ?>

								<p class="description">
									<?php _e( 'Which metadata to display in posts: director, genres, runtime, rating…', 'wpml' ); ?>
									<span class="hide-if-js"><?php _e( 'Javascript seems to be deactivated; please active it to customize your Movie metadata order.', 'wpml' ); ?></span>
								</p>

								<div class="default_movie_meta_sortable hide-if-no-js">
									<ul id="draggable" class="droptrue"><?php echo $draggable ?></ul>
									<ul id="droppable" class="dropfalse"><?php echo $droppable ?></ul>
									<!--<input type="hidden" id="default_movie_meta_sorted" name="tmdb_data[wpml][default_movie_meta_sorted]" value="" />-->
								</div>

								<select id="<?php echo $_id ?>" name="<?php echo $_name ?>[]" class="hide-if-js" style="min-height:<?php echo count( $items ) ?>em;min-width:16em;" multiple>
									<?php echo $options ?>
								</select>

<?php endif; ?>
