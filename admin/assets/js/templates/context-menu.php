<?php
/**
 * Dashboard Context Menu Template
 *
 * @since 3.0.0
 */
?>

			<div class="context-menu-content">
				<# if ( data.groups.length ) { _.each( data.groups, function( group ) { #>
				<ul id="group-{{ group.id }}" class="context-menu-group">
					<# _.each( group.items, function( item ) { #>
					<# if ( ! item.groups.length ) { #>
					<li class="context-menu-item {{ item.id }}-item" data-item="{{ item.id }}">
					<# } else { #>
					<li class="context-menu-item {{ item.id }}-item">
					<# } #>
						<div class="context-menu-icon"><span class="{{ item.icon }}"></span></div>
						<div class="context-menu-text">{{ item.title }}</div>
						<# if ( item.groups.length ) { #>
						<div class="context-menu-icon"><span class="wpmolicon icon-right-chevron"></span></div>
						<div class="context-menu-sub-menu">
							<div class="context-menu-content context-sub-menu-content">
							<# _.each( item.groups, function( subgroup ) { #>
								<ul id="group-{{ subgroup.id }}" class="context-menu-group">
									<# if ( subgroup.items.length ) { _.each( subgroup.items, function( item ) { #>
										<# if ( item.selectable ) { #>
										<li class="context-menu-item">
											<input type="<# if ( item.selectable.multiple ) { #>checkbox<# } else { #>radio<# } #>" class="hidden" id="{{ item.selectable.field }}-{{ item.selectable.value }}" data-field="{{ item.selectable.field }}" name="{{ item.selectable.field }}[]" value="{{ item.selectable.value }}"<# if ( item.selectable.selected ) { #> checked="checked"<# } #>>
											<label for="{{ item.selectable.field }}-{{ item.selectable.value }}">
												<div class="context-menu-icon"><span class="wpmolicon icon-yes-alt2"></span></div>
												<div class="context-menu-text">{{ item.title }}</div>
											</label>
										</li>
										<# } else { #>
										<li class="context-menu-item" data-item="{{ item.id }}">
											<div class="context-menu-icon">{{ item.icon }}</div>
											<div class="context-menu-text">{{ item.title }}</div>
										</li>
										<# } #>
									<# } ); } #>
								</ul>
							<# } ); #>
							</div>
						</div>
						<# } #>
					</li>
					<# } ); #>
				</ul>
				<# } ); } #>
			</div>
