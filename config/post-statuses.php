<?php

return [
	'import-draft' => [
		'label'                     => _x( 'Imported Draft', 'post import-draft status label', 'wpmovielibrary' ),
		'public'                    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => false,
		'label_count'               => _n_noop( 'Imported Draft <span class="count">(%s)</span>', 'Imported Draft <span class="count">(%s)</span>' ),
	],
	'import-queued' => [
		'label'                     => _x( 'Queued Movie', 'post import-queued status label', 'wpmovielibrary' ),
		'public'                    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => false,
		'label_count'               => _n_noop( 'Queued Movie <span class="count">(%s)</span>', 'Queued Movies <span class="count">(%s)</span>' ),
	],
];