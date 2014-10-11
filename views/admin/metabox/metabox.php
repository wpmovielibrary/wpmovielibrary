
		<?php do_action( 'wpmoly_before_metabox_content' ); ?>

		<div id="wpmoly-meta" class="wpmoly-meta">

			<div id="wpmoly-meta-menu-bg"></div>
			<ul id="wpmoly-meta-menu" class="hide-if-no-js">

<?php foreach ( $tabs as $id => $tab ) : ?>

				<li id="wpmoly-meta-<?php echo $id ?>" class="tab<?php echo $tab['active'] ?>"><a href="#" onclick="wpmoly_meta_panel.navigate( '<?php echo $id ?>' ) ; return false;"><span class="<?php echo $tab['icon'] ?>"></span>&nbsp; <span class="text"><?php echo $tab['title'] ?></span></a></li>
<?php endforeach; ?>
				<li class="tab off"><a href="#" onclick="wpmoly_meta_panel.resize() ; return false;"><span class="wpmolicon icon-collapse"></span>&nbsp; <span class="text"><?php _e( 'Collapse', 'wpmovielibrary' ) ?></span></a></li>
			</ul>

			<div id="wpmoly-meta-panels">

				<?php do_action( 'wpmoly_before_metabox_panels' ); ?>

<?php foreach ( $panels as $id => $panel ) : ?>

				<div id="wpmoly-meta-<?php echo $id ?>-panel" class="panel<?php echo $panel['active'] ?> hide-if-js"><?php echo $panel['content'] ?></div>
<?php endforeach; ?>

				<?php do_action( 'wpmoly_after_metabox_panels' ); ?>
			</div>
			<div style="clear:both"></div>

		</div>

		<?php do_action( 'wpmoly_after_metabox_content' ); ?>
