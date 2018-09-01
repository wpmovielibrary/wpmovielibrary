<?php
/**
 * Post Browser Pagination Menu Template
 *
 * @since 3.0.0
 */
?>

<# if ( 1 < data.current ) { #>
			<button type="button" data-action="previous-page" class="button left" title="{{ wpmolyL10n.previous_page }}"><span class="wpmolicon icon-left-chevron"></span></button>
<# } else { #>
			<button type="button" disabled="disabled" class="button left"><span class="wpmolicon icon-left-chevron"></span></button>
<# } #>
			<div class="pagination-menu"><span class="page-label">{{ wpmolyL10n.page }}</span><span class="current-page"><# if ( 1 < data.total ) { #><input type="text" size="1" data-action="jump-to" value="{{ data.current }}" <# if ( data.preview ) { #>readonly="true" <# } #>/><# } else { #>{{ data.current }}<# } #></span><span class="of-label">{{ wpmolyL10n.of }}</span><span class="total-pages">{{ data.total }}</span></div>
<# if ( data.current < data.total ) { #>
			<button type="button" data-action="next-page" class="button right" title="{{ wpmolyL10n.next_page }}"><span class="wpmolicon icon-right-chevron"></span></button>
<# } else { #>
			<button type="button" disabled="disabled" class="button right"><span class="wpmolicon icon-right-chevron"></span></button>
<# } #>
