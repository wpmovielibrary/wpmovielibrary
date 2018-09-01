<?php
/**
 * Dashboard Setting Field Template
 *
 * @since 3.0.0
 */
?>

									<# if ( '' !== data.description ) { #>
									<div class="setting-info"><button class="button info" type="button" data-action="show-description"><span class="wpmolicon icon-info-circled-o"></span></button></div>
									<# } if ( 'boolean' === data.type ) { #>
									<div class="setting-input"><input id="{{ data.name }}-toggle" type="checkbox" data-action="toggle" data-setting="{{ data.name }}" value="1" <# if ( true === data.value ) { #> checked="checked"<# } #>/><label for="{{ data.name }}-toggle"></label></div>
									<# } #>
									<div class="setting-content">
										<div class="setting-title">{{ data.title }}</div>
										<div class="setting-label">{{ data.label }}</div>
										<# if ( '' !== data.description ) { #>
										<div class="setting-description">{{{ data.description }}}</div>
										<# } if ( 'string' === data.type && data.options ) { #>
										<select data-setting="{{ data.name }}">
											<option value=""></option>
										<# _.each( data.options, function( value, key ) { #>
											<option value="{{ key }}"<# if ( key === data.value ) { #> selected="selected"<# } #>>{{ value }}</option>
										<# } ); #>
										</select>
										<# } else if ( 'string' === data.type ) { #>
										<input type="text" data-setting="{{ data.name }}" value="{{ data.value }}" />
										<# } else if ( 'integer' === data.type || 'number' === data.type ) { #>
										<input type="number" data-setting="{{ data.name }}" value="{{ data.value }}" />
										<# } #>
									</div>
