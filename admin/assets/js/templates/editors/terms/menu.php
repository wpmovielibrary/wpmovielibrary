<?php
/**
 * Term Browser Menu Template.
 *
 * @since 3.0.0
 */
?>

		<button class="button grid active" type="button" title="{{ wpmolyEditorL10n.edit_label }}"><span class="wpmolicon icon-grid"></span></button>
		<button class="button search" type="button" data-action="open-search" title="{{ wpmolyEditorL10n.open_search }}"><span class="wpmolicon icon-search"></span></button>
		<div class="terms-search">
			<button class="button close" type="button" data-action="close-search" title="{{ wpmolyEditorL10n.close_search }}"><span class="wpmolicon icon-no"></span></button>
			<button class="button search" type="button" data-action="start-search" title="{{ wpmolyEditorL10n.search_terms }}"><span class="wpmolicon icon-search"></span></button>
			<input class="search-input" type="text" data-value="search-query" placeholder="{{ wpmolyEditorL10n.term_name }}" />
		</div>
