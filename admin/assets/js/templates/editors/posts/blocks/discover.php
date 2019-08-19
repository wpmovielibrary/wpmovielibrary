<?php
/**
 * Post Browser 'Discover' Block Template
 *
 * @since 3.0.0
 */
?>

							<p>{{{ data.text }}}</p>
							<div class="posts-search">
								<input class="search-input" type="text" data-value="search-query" placeholder="{{ wpmolyEditorL10n.search_posts }}" />
								<button type="button" class="button search empty" data-action="start-search" title="{{ wpmolyEditorL10n.search_posts }}"><span class="wpmolicon icon-search"></span></button>
								<button type="button" class="button close empty" data-action="close-search" title="{{ wpmolyEditorL10n.search_posts }}"><span class="wpmolicon icon-no"></span></button>
							</div>
							<p class="classic-posts-browser">Feeling lost? Use the <a href="{{ wpmolyEditorL10n.old_edit_link }}">old posts browser</a>.</p>
