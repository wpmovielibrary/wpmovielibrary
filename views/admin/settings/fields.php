<?php 

if ( 'input' == $_type ) : ?>

	<input type="text" id="<?php echo $_id ?>" name="<?php echo $_name ?>" value="<?php echo $_value ?>" size="40" maxlength="32" />
<?php if ( 'apikey' == $field['id'] ) : ?>
	<?php wpmoly_nonce_field( 'check-api-key' ) ?>
	<input id="APIKey_check" type="button" name="APIKey_check" class="button button-secondary" value="<?php _e( 'Check API Key', 'wpmovielibrary' ); ?>" />
	<span class="spinner"></span>
<?php endif; ?>
	<p class="description"><?php _e( $field['description'], 'wpmovielibrary' ) ?></p>

<?php elseif ( 'toggle' == $_type ) : ?>

	<div class="label_onoff hide-if-no-js"><span class="label_on<?php if ( 1 == $_value ) echo ' active'; ?>"><?php _e( 'Enable', 'wpmovielibrary' ); ?></span><span class="label_off<?php if ( 0 == $_value ) echo ' active'; ?>"><?php _e( 'Don&rsquo;t', 'wpmovielibrary' ); ?></span></div>
	<div class="label_onoff_radio hide-if-js">
		<label><input class="enable" type="radio" id="<?php echo $_id ?>_on" name="<?php echo $_name ?>" value="1"<?php checked( $_value, 1 ); ?>/> <?php _e( $_title, 'wpmovielibrary' ) ?></label>
		<label><input class="disable" type="radio" id="<?php echo $_id ?>_off" name="<?php echo $_name ?>" value="0"<?php checked( $_value, 0 ); ?>/> <?php _e( 'Don&rsquo;t', 'wpmovielibrary' ); ?></label>
	</div>
	<p class="description"><?php _e( $field['description'], 'wpmovielibrary' ) ?></p>

<?php elseif ( 'select' == $_type || 'multiple' == $_type ) : ?>

<?php if ( 'default_movie_details' == $field['id'] ) : ?>
	<div class="default_movie_details hide-if-no-js">
		<ul class="droptrue">
<?php foreach ( $field['values'] as $slug => $option ) : ?>
			<li data-movie-detail="<?php echo $slug ?>" class="default_movie_detail<?php echo ( ( is_array( $_value ) && in_array( $slug, $_value ) ) || $_value == $slug ? ' selected' : '' ); ?>"><?php _e( $option, 'wpmovielibrary' ); ?><span class="dashicons dashicons-yes"></span></li>
<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

	<select id="<?php echo $_id ?>" name="<?php echo $_name ?>[]"<?php if ( 'multiple' == $_type ) echo ' multiple="multiple"' ?> class="<?php if ( 'default_movie_details' == $field['id'] ) echo 'hide-if-js' ?>">
<?php foreach ( $field['values'] as $slug => $option ) : ?>
		<option value="<?php echo $slug ?>"<?php if ( is_array( $_value ) && in_array( $slug, $_value ) ) echo ' selected="selected"'; else selected( $_value, $slug, true ); ?>><?php _e( $option, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>
	</select>
	<p class="description"><?php _e( $field['description'], 'wpmovielibrary' ) ?></p>

<?php elseif ( 'sorted' == $_type ) : ?>

	<p class="description">
		<?php _e( 'Which metadata to display in posts: director, genres, runtime, rating…', 'wpmovielibrary' ); ?>
		<span class="hide-if-js"><?php _e( 'Javascript seems to be deactivated; please active it to customize your Movie metadata order.', 'wpmovielibrary' ); ?></span>
	</p>

	<div class="default_movie_meta_sortable hide-if-no-js">
		<ul id="draggable" class="droptrue"><?php echo $draggable ?></ul>
		<ul id="droppable" class="dropfalse"><?php echo $droppable ?></ul>
		<input type="hidden" id="default_movie_meta_sorted" name="wpmoly_settings[wpmoly][default_movie_meta_sorted]" value="<?php echo implode( ',', $selected ) ?>" />
	</div>

	<select id="<?php echo $_id ?>" name="<?php echo $_name ?>[]" class="hide-if-js" style="min-height:<?php echo count( $items ) ?>em;min-width:16em;" multiple>
		<?php echo $options ?>
	</select>

<?php endif; ?>
