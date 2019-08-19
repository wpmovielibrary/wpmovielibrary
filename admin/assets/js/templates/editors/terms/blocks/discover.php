<?php
/**
 * Term Browser 'Discover' Block Template.
 *
 * @since 3.0.0
 */
?>

							<p>{{{ data.text }}}</p>
							<div class="terms-search">
								<input class="search-input" type="text" data-value="search-query" placeholder="{{ wpmolyEditorL10n.search_terms }}" />
								<button type="button" class="button search empty" data-action="start-search" title="{{ wpmolyEditorL10n.search_terms }}"><span class="wpmolicon icon-search"></span></button>
								<button type="button" class="button close empty" data-action="close-search" title="{{ wpmolyEditorL10n.close_search }}"><span class="wpmolicon icon-no"></span></button>
							</div>
							<p class="classic-terms-browser">{{{ wpmolyEditorL10n.old_browser.replace( '%s', wpmolyEditorL10n.old_edit_link ) }}}</p>
