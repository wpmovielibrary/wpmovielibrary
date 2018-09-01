<?php
/**
 * Post Browser 'Discover' Block Template
 *
 * @since 3.0.0
 */
?>

							<p>{{{ wpmoly._n( wpmolyEditorL10n.n_total_post, data.total ) }}}</p>
							<ul class="posts-list">
<# if ( data.publish ) { #>
								<li class="list-item<# if ( 'publish' === data.current ) { #> active<# } #>"><span class="wpmolicon icon-right-open"></span> <a href="#" data-action="filter" data-value="publish">{{ wpmoly._n( wpmolyEditorL10n.n_published, data.publish ) }}</a></li>
<# } if ( data.future ) { #>
								<li class="list-item<# if ( 'future' === data.current ) { #> active<# } #>"><span class="wpmolicon icon-right-open"></span> <a href="#" data-action="filter" data-value="future">{{ wpmoly._n( wpmolyEditorL10n.n_future, data.future ) }}</a></li>
<# } if ( data.draft ) { #>
								<li class="list-item<# if ( 'draft' === data.current ) { #> active<# } #>"><span class="wpmolicon icon-right-open"></span> <a href="#" data-action="filter" data-value="draft">{{ wpmoly._n( wpmolyEditorL10n.n_draft, data.draft ) }}</a></li>
<# } if ( data.pending ) { #>
								<li class="list-item<# if ( 'pending' === data.current ) { #> active<# } #>"><span class="wpmolicon icon-right-open"></span> <a href="#" data-action="filter" data-value="pending">{{ wpmoly._n( wpmolyEditorL10n.n_pending, data.pending ) }}</a></li>
<# } if ( data.private ) { #>
								<li class="list-item<# if ( 'private' === data.current ) { #> active<# } #>"><span class="wpmolicon icon-right-open"></span> <a href="#" data-action="filter" data-value="private">{{ wpmoly._n( wpmolyEditorL10n.n_private, data.private ) }}</a></li>
<# } if ( data.trash ) { #>
								<li class="list-item<# if ( 'trash' === data.current ) { #> active<# } #>"><span class="wpmolicon icon-right-open"></span> <a href="#" data-action="filter" data-value="trash">{{ wpmoly._n( wpmolyEditorL10n.n_trashed, data.trash ) }}</a></li>
<# } if ( data.autodraft ) { #>
								<li class="list-item<# if ( 'autodraft' === data.current ) { #> active<# } #>"><span class="wpmolicon icon-right-open"></span> <a href="#" data-action="filter" data-value="auto-draft">{{ wpmoly._n( wpmolyEditorL10n.n_autodraft, data.autodraft ) }}</a></li>
<# } #>
							</ul>
							<ul class="links-list">
								<li class="list-item"><span class="wpmolicon icon-right-open"></span> <a href="{{ wpmolyEditorL10n.old_edit_link }}">{{ wpmolyEditorL10n.old_edit_label }}</a></li>
							</ul>
