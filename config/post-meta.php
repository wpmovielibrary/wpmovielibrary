<?php

return [
	'movie' => [
		// budget, homepage, imdb_id, id (renamed tmdb_id), original_title,
		// overview, release_date, revenue, runtime, tagline, title
		// certification, local_release_date, release_year → imported from TMDb Movies Release Dates endpoint
		// cast → JSON array imported from TMDb Movies Credits endpoint to preserve casting order
		//         format: [{ "id": int, "name": string, "character": string, "order": int, "profile_path": string }]
		'director' => [
			'type'         => 'string',
			'description'  => 'Movie director(s)',
			'single'       => true,
			'show_in_rest' => [
				'schema' => [
					'type'    => 'string',
					'context' => [ 'view', 'edit' ],
				],
			],
		],
	],
];