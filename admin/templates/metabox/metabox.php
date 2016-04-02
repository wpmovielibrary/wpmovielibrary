
<?php do_action( 'wpmoly/before/metabox/content', $metabox ); ?>

		<div id="wpmoly-meta" class="wpmoly-meta wpmoly-tabbed-metabox css-powered<?php echo $empty ? '' : ' hidden'; ?>">

<?php do_action( 'wpmoly/before/metabox/panels', $metabox ); ?>

<?php foreach ( $panels as $id => $panel ) : ?>

			<div id="wpmoly-meta-<?php echo $id ?>-panel" class="panel<?php echo $panel['default'] ?> hide-if-js"><?php echo $panel['content'] ?></div>
<?php endforeach; ?>

<?php do_action( 'wpmoly/after/metabox/panels', $metabox ); ?>

<?php do_action( 'wpmoly/before/metabox/menu', $metabox ); ?>

			<ul id="wpmoly-meta-menu" class="wpmoly-meta-menu css-powered">

<?php foreach ( $tabs as $id => $tab ) : ?>

				<li id="wpmoly-meta-<?php echo $id ?>" class="tab<?php echo $tab['default'] ?>"><a class="navigate" href="#wpmoly-meta-<?php echo $id ?>-panel"><span class="<?php echo $tab['icon'] ?>"></span>&nbsp; <span class="text"><?php _e( $tab['title'], 'wpmovielibrary' ) ?></span><span class="label hide-if-js" title=""><span></a></li>
<?php endforeach; ?>
			</ul>

<?php do_action( 'wpmoly/after/metabox/menu', $metabox ); ?>

			<div style="clear:both"></div>

		</div>

<?php do_action( 'wpmoly/after/metabox/content', $metabox ); ?>
