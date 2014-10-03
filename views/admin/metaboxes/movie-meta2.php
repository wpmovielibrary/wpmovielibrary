
		<div id="wpmoly-meta" class="wpmoly-meta">

			<div id="wpmoly-meta-menu-bg"></div>
			<ul id="wpmoly-meta-menu" class="hide-if-no-js">
				<li id="wpmoly-meta-preview" class="tab active"><a href="#" onclick="wpmoly_edit_meta.panel( 'preview' ) ; return false;"><span class="dashicons dashicons-editor-video"></span>&nbsp; Aperçu</a></li>
				<li id="wpmoly-meta-meta" class="tab"><a href="#" onclick="wpmoly_edit_meta.panel( 'meta' ) ; return false;"><span class="dashicons dashicons-clipboard"></span>&nbsp; Métadonnées</a></li>
				<li id="wpmoly-meta-details" class="tab"><a href="#" onclick="wpmoly_edit_meta.panel( 'details' ) ; return false;"><span class="dashicons dashicons-awards"></span>&nbsp; Détails</a></li>
				<li id="wpmoly-meta-images" class="tab"><a href="#" onclick="wpmoly_edit_meta.panel( 'images' ) ; return false;"><span class="dashicons dashicons-images-alt"></span>&nbsp; Images</a></li>
			</ul>

			<div id="wpmoly-meta-panels">
				<div id="wpmoly-meta-preview-panel" class="panel active hide-if-js"><?php echo $preview ?></div>
				<div id="wpmoly-meta-meta-panel" class="panel hide-if-js"><?php echo $meta ?></div>
				<div id="wpmoly-meta-details-panel" class="panel hide-if-js"><?php echo $details ?></div>
				<div id="wpmoly-meta-images-panel" class="panel hide-if-js"><?php echo $images ?></div>
			</div>
			<div style="clear:both"></div>

		</div>