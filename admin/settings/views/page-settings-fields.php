<?php 

if ( 'input' == $_type ) : ?>

	<input type="text" id="<?php echo $_id ?>" name="<?php echo $_name ?>" value="<?php echo $_value ?>" size="40" maxlength="32" />
<?php if ( 'apikey' == $field['id'] ) : ?>
	<input id="APIKey_check" type="button" name="APIKey_check" class="button button-secondary" value="<?php _e( 'Check API Key', WPML_SLUG ); ?>" />
<?php endif; ?>
	<p class="description"><?php _e( $field['description'], WPML_SLUG ) ?></p>

<?php elseif ( 'toggle' == $_type ) : ?>

	<label><input type="radio" id="<?php echo $_id ?>" name="<?php echo $_name ?>" value="1"<?php checked( $_value, 1 ); ?>/> <?php _e( $_title, WPML_SLUG ) ?></label>
	<label><input type="radio" id="<?php echo $_id ?>" name="<?php echo $_name ?>" value="0"<?php checked( $_value, 0 ); ?>/> <?php _e( 'Don&rsquo;t', WPML_SLUG ); ?></label>
	<p class="description"><?php _e( $field['description'], WPML_SLUG ) ?></p>

<?php elseif ( 'select' == $_type || 'multiple' == $_type ) : ?>

	<select id="<?php echo $_id ?>" name="<?php echo $_name ?>[]"<?php if ( 'multiple' == $_type ) echo ' multiple="multiple"' ?>>
<?php foreach ( $field['values'] as $slug => $option ) : ?>
		<option value="<?php echo $slug ?>"<?php echo ( is_array( $_value ) ? ( in_array( $slug, $_value ) ? ' selected="selected"' : '' ) : selected( $_value, $slug, true ) ); ?>><?php _e( $option, WPML_SLUG ); ?></option>
<?php endforeach; ?>
	</select>
	<p class="description"><?php _e( $field['description'], WPML_SLUG ) ?></p>

<?php elseif ( 'sorted' == $_type ) : ?>

								<p class="description">
									<?php _e( 'Which metadata to display in posts: director, genres, runtime, rating…', WPML_SLUG ); ?>
									<span class="hide-if-js"><?php _e( 'Javascript seems to be deactivated; please active it to customize your Movie metadata order.', WPML_SLUG ); ?></span>
								</p>

								<div class="default_movie_meta_sortable hide-if-no-js">
									<ul id="draggable" class="droptrue"><?php echo $draggable ?></ul>
									<ul id="droppable" class="dropfalse"><?php echo $droppable ?></ul>
									<input type="hidden" id="default_movie_meta_sorted" name="wpml_settings[wpml][default_movie_meta_sorted]" value="<?php echo implode( ',', $selected ) ?>" />
								</div>

								<select id="<?php echo $_id ?>" name="<?php echo $_name ?>[]" class="hide-if-js" style="min-height:<?php echo count( $items ) ?>em;min-width:16em;" multiple>
									<?php echo $options ?>
								</select>

<?php endif; ?>
