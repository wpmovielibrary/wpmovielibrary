
		<div id="wpmoly-meta" class="wpmoly-meta">

			<div id="wpmoly-meta-menu-bg"></div>
			<ul id="wpmoly-meta-menu" class="hide-if-no-js">
				<li id="wpmoly-meta-preview" class="tab<?php //if ( ! $empty ) echo ' active' ?>"><a href="#" onclick="wpmoly_meta_panel.navigate( 'preview' ) ; return false;"><span class="wpmolicon icon-video"></span>&nbsp; <span class="text"><?php _e( 'Preview', 'wpmovielibrary' ) ?></span></a></li>
				<li id="wpmoly-meta-meta" class="tab<?php //if ( $empty ) echo ' active' ?>"><a href="#" onclick="wpmoly_meta_panel.navigate( 'meta' ) ; return false;"><span class="wpmolicon icon-meta"></span>&nbsp; <span class="text"><?php _e( 'Metadata', 'wpmovielibrary' ) ?></span></a></li>
				<li id="wpmoly-meta-details" class="tab active"><a href="#" onclick="wpmoly_meta_panel.navigate( 'details' ) ; return false;"><span class="wpmolicon icon-details"></span>&nbsp; <span class="text"><?php _e( 'Details', 'wpmovielibrary' ) ?></span></a></li>
				<li id="wpmoly-meta-images" class="tab"><a href="#" onclick="wpmoly_meta_panel.navigate( 'images' ) ; return false;"><span class="wpmolicon icon-images-alt"></span>&nbsp; <span class="text"><?php _e( 'Images', 'wpmovielibrary' ) ?></span></a></li>
				<li class="tab off"><a href="#" onclick="wpmoly_meta_panel.resize() ; return false;"><span class="wpmolicon icon-collapse"></span>&nbsp; <span class="text"><?php _e( 'Collapse', 'wpmovielibrary' ) ?></span></a></li>
			</ul>

			<div id="wpmoly-meta-panels">
				<div id="wpmoly-meta-preview-panel" class="panel<?php //if ( ! $empty ) echo ' active' ?> hide-if-js"><?php echo $preview ?></div>
				<div id="wpmoly-meta-meta-panel" class="panel<?php //if ( $empty ) echo ' active' ?> hide-if-js"><?php echo $meta ?></div>
				<div id="wpmoly-meta-details-panel" class="panel active hide-if-js"><?php echo $details ?></div>
				<div id="wpmoly-meta-images-panel" class="panel hide-if-js"><?php echo $images ?></div>
			</div>
			<div style="clear:both"></div>

		</div>