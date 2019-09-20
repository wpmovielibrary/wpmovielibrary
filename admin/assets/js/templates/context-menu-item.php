<?php
/**
 * Dashboard Context Menu Item Template
 *
 * @since 3.0.0
 */
?>

						<div class="context-menu-icon"><span class="<# if ( _.isTrue( data.selectable ) && _.isTrue( data.selected ) ) { #>wpmolicon icon-yes-alt2<# } else { #>{{ data.icon }}<# } #>"></span></div>
						<div class="context-menu-text">{{ data.title }}</div>
						<# if ( data.groups.length ) { #>
						<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
						<div class="context-menu-sub-menu">
							<div class="context-menu-content context-sub-menu-content"></div>
						</div>
						<# } #>
