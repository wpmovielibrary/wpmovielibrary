
			<button type="button" <# if ( data.settings_control ) { #>data-action="toggle-settings"<# } else { #>disabled="disabled"<# } #> class="button left"><span class="wpmolicon icon-dots"></span></button>
			<button type="button" <# if ( data.custom_control ) { #>data-action="toggle-customs"<# } else { #>disabled="disabled"<# } #> class="button right"><span class="wpmolicon icon-settings"></span></button>
