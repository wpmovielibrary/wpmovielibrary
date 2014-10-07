<?php
/**
 * WPMovieLibrary Config Admin menu definition
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	wp_die();

$wpmoly_admin_menu = array(

	'page' => array(
		'page_title' => WPMOLY_NAME,
		'menu_title' => __( 'Movies', 'wpmovielibrary' ),
		'capability' => 'manage_options',
		'menu_slug'  => 'wpmovielibrary',
		'function'   => null,
		'icon_url'   => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhLS0gQ3JlYXRlZCB3aXRoIElua3NjYXBlIChodHRwOi8vd3d3Lmlua3NjYXBlLm9yZy8pIC0tPgoKPHN2ZwogICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgIHhtbG5zOmNjPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyMiCiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIKICAgeG1sbnM6c3ZnPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIgogICB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIKICAgdmVyc2lvbj0iMS4xIgogICB3aWR0aD0iOTYwIgogICBoZWlnaHQ9Ijk2MCIKICAgaWQ9InN2ZzIiPgogIDxtZXRhZGF0YQogICAgIGlkPSJtZXRhZGF0YTgiPgogICAgPHJkZjpSREY+CiAgICAgIDxjYzpXb3JrCiAgICAgICAgIHJkZjphYm91dD0iIj4KICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3N2Zyt4bWw8L2RjOmZvcm1hdD4KICAgICAgICA8ZGM6dHlwZQogICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL3B1cmwub3JnL2RjL2RjbWl0eXBlL1N0aWxsSW1hZ2UiIC8+CiAgICAgICAgPGRjOnRpdGxlPjwvZGM6dGl0bGU+CiAgICAgIDwvY2M6V29yaz4KICAgIDwvcmRmOlJERj4KICA8L21ldGFkYXRhPgogIDxkZWZzCiAgICAgaWQ9ImRlZnM2IiAvPgogIDxpbWFnZQogICAgIHhsaW5rOmhyZWY9ImZpbGU6Ly8vaG9tZS9jaGFybGllL0ltYWdlcy9EZXNpZ24vV1BNTC9XUE1PTFkvbG9nb19uYl9mbGF0LnBuZyIKICAgICB4PSIwIgogICAgIHk9IjAiCiAgICAgd2lkdGg9Ijk2MCIKICAgICBoZWlnaHQ9Ijk2MCIKICAgICBpZD0iaW1hZ2UxMCIgLz4KICA8cGF0aAogICAgIGQ9Ik0gMCw0ODAgMCwwIGwgNDgwLDAgNDgwLDAgMCw0ODAgMCw0ODAgLTQ4MCwwIC00ODAsMCAwLC00ODAgeiBtIDg5Ny4xNDE1NSwyNjEuMTIzMzIgYyAzLjAwMDA2LC0xLjAzMjE3IDcuNDUzNzcsLTIuODk2NTQgOS44OTcxMywtNC4xNDMwNiAzLjk3MDA4LC0yLjAyNTM4IDQuOTA0LC0yLjE0Mjk0IDguNzgyNjMsLTEuMTA1NTcgMi4zODcwOCwwLjYzODQ1IDUuOTk5MTUsMi40OTQwMiA4LjAyNjgxLDQuMTIzNSAzLjM1ODA0LDIuNjk4NTkgNC4xNTI0LDIuOTA3NTIgOC45MTE2OCwyLjM0Mzg1IDIuODczNzYsLTAuMzQwMzUgNS42NzkwNiwtMS4xNjU5IDYuMjMzOTksLTEuODM0NTYgMC42NzU1MywtMC44MTM5NiAwLjkyNTg5LC0xMTkuOTg3NDEgMC43NTc1OSwtMzYwLjYxMTYgTCA5MzkuNSwyMC41IGwgLTQ1NywwIC00NTcsMCAwLDI5NSAwLDI5NSA4Ljc1OTQwNSwwLjI4OTA3IGMgOC43MTg5MDksMC4yODc3MyA4Ljc5MjMsMC4yNjk1MyAxNS44NzQ1MTMsLTMuOTM3OTQgNi42NTI2NCwtMy45NTIyNiA3LjgxNzAwMSwtNC4yOTA1NCAxNy45MTM3NzcsLTUuMjA0NDEgOS41NTQyNCwtMC44NjQ3NyAxMS4wOTk0MTYsLTAuNzgwMzUgMTMuNDA4NDI5LDAuNzMyNTcgMi40OTIxMiwxLjYzMjkgMi43MzkxOTYsMS42MTc4MiA1LjQ4MTE4OSwtMC4zMzQ2NSBDIDg4LjUxNjU5OCw2MDAuOTIwMDkgOTAuMTI1NDY4LDYwMCA5MC41MTI1NzgsNjAwIGMgMC4zODcxMSwwIDMuNjE0MjkzLDIuMzE1OTIgNy4xNzE1MTcsNS4xNDY0OSBDIDEwNi43MjM2NSw2MTIuMzM5NDggMTEwLjM3MTcxLDYxNCAxMTcuMTM0NzIsNjE0IGMgNC41NDc4MSwwIDYuMjg3ODksLTAuNDgzMDkgOC41Mzg1NSwtMi4zNzA1NCAxLjU1NDcsLTEuMzAzOCA0LjQwMTczLC0yLjcwMzg4IDYuMzI2NzMsLTMuMTExMjkgNC42MDcwNywtMC45NzUwNiA2LjQ3MzMxLC0yLjc1MDA0IDEwLjE1NTg3LC05LjY1OTIzIDQuNzczODksLTguOTU2NzQgNS4zMTA1NCwtOS4yNTE3IDE0LjIxODMsLTcuODE0NzIgNC4xNDA3LDAuNjY3OTcgOC41MTc3OSwwLjk1NTc5IDkuNzI2ODcsMC42Mzk2MSAyLjcwNDQxLC0wLjcwNzIyIDYuOTU0MTMsMy41MTgxMyAxMC40MjI2LDEwLjM2MjgyIDEuOTE4NzUsMy43ODY0NyAzLjQxNzAxLDUuMTc4ODcgNy45NjkwMyw3LjQwNTk2IGwgNS42MDYwMiwyLjc0Mjc3IDYuMzY5NjQsLTEuNzI0MTggYyA3LjMxODQxLC0xLjk4MSAxMS4wNDQ3OSwtMS41NjEwMiAxOC4xNTQ2MiwyLjA0NjA5IDUuMjE0MjUsMi42NDU0MiA3Ljg4Mjk3LDMuMDkxMTYgOC44NzcwNSwxLjQ4MjcxIDEuNjMyMywtMi42NDExMSA1LjMzNTU1LC0wLjc4NDEyIDEwLjgyMjQyLDUuNDI2OSAzLjEyMjY3LDMuNTM0OCA1LjY3NzU4LDYuOTA1ODIgNS42Nzc1OCw3LjQ5MTE3IDAsMC41ODUzNCAzLjI3MzM1LDQuNDQ1NSA3LjI3NDEsOC41NzgxMiA4LjQ5OTU1LDguNzc5NjggMTAuMzIyODQsOS41MDM4MSAyMy45Mjk3NCw5LjUwMzgxIGwgOS43MTcwNCwwIDUuMjUxOTIsOC41OTQ0NyBjIDguMjkzMDUsMTMuNTcxMDggMTAuNzk1NjcsMTQuODA4MzQgMjIuMjA1NjMsMTAuOTc4MTQgOC40NzU4MiwtMi44NDUyNCAxMC44Njg5MSwtMy4wNzk1NSAxNS4zODI0MSwtMS41MDYxNCAzLjExMTE1LDEuMDg0NTUgOC4yNjUyMSw3LjM1NjM4IDguMjA3MzYsOS45ODcyOCAtMC4wMTc1LDAuNzk1NDQgLTAuNjkyNSwyLjMxOTc2IC0xLjUsMy4zODczOCAtMi4xMjExNywyLjgwNDQzIC0xLjgxNTY0LDQuNDQzNDQgMS4wNzM2OCw1Ljc1OTkxIDIuMzcxODMsMS4wODA2NyAyLjk2OTE2LDAuNzM0NjggOC45MjgyOSwtNS4xNzE0OCBsIDYuMzg2NDEsLTYuMzI5NjQgNC44MjE3MSwxLjMwNTcgYyAyLjY1MTk0LDAuNzE4MTQgNi4wMzM2NywxLjkwOSA3LjUxNDk2LDIuNjQ2MzcgMS40ODEyOSwwLjczNzM3IDIuOTIyNTIsMC45Njk3MSAzLjIwMjc0LDAuNTE2MzEgMC4yODAyMiwtMC40NTM0MSAtMC43OTk0OSwtNS4xODQyMiAtMi4zOTkzNSwtMTAuNTEyOTIgLTMuMjQ2NDYsLTEwLjgxMzEgLTcuMjMyODgsLTI5LjUxNDAzIC05LjE5OTI4LC00My4xNTUzOCAtMi4wMjgxLC0xNC4wNjkyOSAtMS43MTA0OCwtNTkuMTI5MTQgMC41MTgwOCwtNzMuNSA0Ljg4ODQxLC0zMS41MjI3OCAxMy4xODc5NiwtNTguMTc3NjMgMjYuNjIyMjQsLTg1LjUgMTMuOTYzNzQsLTI4LjM5OTE3IDI5LjU5NDEyLC01MC4zODQxNCA1MS44MjA2MSwtNzIuODg4NDkgMjIuMjQwMiwtMjIuNTE4MjEgNDUuMzIxMDcsLTM5LjA4MjAxIDc0Ljc0MTcxLC01My42Mzc3NyAyNy4wODU4MywtMTMuNDAwNjMgNTIuNTk3NzUsLTIxLjQ0NDUyIDgzLC0yNi4xNjk4NSAxOC44MTA5NSwtMi45MjM3MyA1OC42OTQ4OSwtMy4xNjkyMiA3NywtMC40NzM5NSA3NC41ODM4NSwxMC45ODE4NSAxMzguNjgzODksNDcuODY3MzEgMTgzLjYzNDc5LDEwNS42NzAwNiAzMC4xMzkzOCwzOC43NTY0OCA1MC4wOTUyLDg2Ljk5MzQ3IDU2LjQ3MDU1LDEzNi41IDIuMjUwNzUsMTcuNDc3NzEgMi4yNTI5Nyw1MC41MjU2NSAwLjAwNSw2Ny45MDUwOCAtNC45MTQzMiwzNy45ODYxNSAtMTguMjE4NjUsNzcuNjA0NTkgLTM1LjkzODg2LDEwNy4wMjA5OCAtMS45MzA5MywzLjIwNTQyIC0zLjI5NTc4LDYuMzg4MjkgLTMuMDMzMDIsNy4wNzMwNCAwLjY1NjEsMS43MDk3OCA4LjIwMTkyLDYuOTc0NTEgMTEuODYxOTcsOC4yNzYxMiA0Ljc0NzU1LDEuNjg4MzYgMTMuNzAzNDQsMS4zOTExMSAxOS42NDE1NSwtMC42NTE5IHogTSA2NTEuNDcwMTIsNjc3LjAwNjcxIGMgMzMuOTg4NDIsLTkuMTY2NzIgNTkuNTMyMjUsLTM1LjY1OTUyIDY4LjIzNDQzLC03MC43Njk0IDIuNDk2MTIsLTEwLjA3MDg2IDIuMjI5NywtMzIuMjg4NzQgLTAuNTA4NTgsLTQyLjQxMjc0IC05LjIwNzk1LC0zNC4wNDM3MyAtMzIuOTI3MzUsLTU4LjY2MjkzIC02NS45MDc2NSwtNjguNDA3ODcgLTEyLjU1MTEsLTMuNzA4NTcgLTMxLjA1MzM4LC00LjM5NjkzIC00NC4yOTgzNSwtMS42NDgwOCAtMzQuMzI0ODksNy4xMjM3NSAtNjEuMjI4NTUsMzEuODEyMzIgLTcxLjYyODg5LDY1LjczMTM4IC0yLjU2NDgxLDguMzY0NzQgLTIuNzU5NiwxMC4yMzUyMyAtMi43NTk2LDI2LjUgMCwxNi4xOTM3NyAwLjIwMjE2LDE4LjE1NjU5IDIuNzA4MzgsMjYuMjk2NTkgMTAuNTA0OCwzNC4xMTg2NyAzNi42OTEyMiw1OC4xNjYzNiA3MS42OTAxNCw2NS44MzQ5OCAxMS4zOTkyMSwyLjQ5NzY4IDMwLjk1OTMzLDEuOTc5NjEgNDIuNDcwMTIsLTEuMTI0ODYgeiBtIC0zNS40NDU4OCwtNDguNjA5OCBDIDYwNC4xMDE2LDYyNS4xOTMgNTkzLjkzMDAxLDYxNi42ODQwNiA1ODguMjI1ODEsNjA1LjE0MjQgNTg0LjU3NDgxLDU5Ny43NTUwOSA1ODQuNSw1OTcuMzcxNzggNTg0LjUsNTg2LjA1MTg2IGMgMCwtMTAuOTIyODggMC4xNzQzMSwtMTEuOTE5ODUgMy4yMDE0NSwtMTguMzEwMzkgMTEuODU4MzcsLTI1LjAzNCA0MS45MTk5NiwtMzMuMzgzNjMgNjUuMDEwODEsLTE4LjA1NjggMTYuNjA2MTcsMTEuMDIyNTUgMjMuNDc1MTQsMzMuNzk5MTEgMTUuODUwNjksNTIuNTU4NjggLTguMzgyMDMsMjAuNjIzNTMgLTMxLjA3NjgsMzEuOTIwOSAtNTIuNTM4NzEsMjYuMTUzNTYgeiBNIDc1OC40NzMyMSw1MjUuOTIyNjYgYyA2Ny4xNjIyOCwtMTcuNzk4NDIgOTMuODExNjEsLTI0Ljk4MDY1IDk0LjY3OTI4LC0yNS41MTY5IDEuMjc5MDEsLTAuNzkwNDcgMS4wNDAzLC0zLjMzMjAzIC0xLjA5ODMsLTExLjY5Mzg4IC03LjY5NDIzLC0zMC4wODQxNCAtMzEuMDI4NywtNjMuODUwNzkgLTYwLjg5ODM1LC04OC4xMjQyOCAtNy4xMjc0NCwtNS43OTIxIC03LjMxMjU3LC01Ljg3MzQ5IC05LjA5Nzk1LC00IEMgNzgwLjAxMDg0LDM5OC43MzU2NiA3MDQsNTExLjczODA4IDcwNCw1MTIuNjMzMjkgYyAwLDAuMzE1MDcgMi4wODM3NSwyLjg4ODk3IDQuNjMwNTUsNS43MTk3OCAyLjU0NjgsMi44MzA4MSA2LjAxODM4LDcuOTU5NDMgNy43MTQ2MywxMS4zOTY5MyAxLjY5NjI0LDMuNDM3NSAzLjMxMjk0LDYuMjUgMy41OTI2Niw2LjI1IDAuMjc5NzIsMCAxNy42MjA2MywtNC41MzQ4IDM4LjUzNTM3LC0xMC4wNzczNCB6IgogICAgIGlkPSJwYXRoMjk5MyIKICAgICBzdHlsZT0iZmlsbDojMDEwMTAxIiAvPgo8L3N2Zz4K',
		'position'   => 6
	),

	'subpages' => array(

		'dashboard' => array(
			'page_title'  => __( 'Your library', 'wpmovielibrary' ),
			'menu_title'  => __( 'Your library', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'wpmovielibrary',
			'function'    => 'WPMOLY_Dashboard::dashboard',
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(
				'load-{screen_hook}' => 'WPMOLY_Dashboard::add_tabs'
			),
			'scripts'     => array(
				'dashboard' =>array(
					'file'    => sprintf( '%s/assets/js/admin/wpmoly-dashboard.js', WPMOLY_URL ),
					'require' => array( WPMOLY_SLUG . '-admin', 'jquery', 'jquery-ui-sortable' ),
					'footer'  => true
				)
			),
			'styles'      => array(
				'dashboard' => array(
					'file'    => sprintf( '%s/assets/css/admin/wpmoly-dashboard.css', WPMOLY_URL ),
					'require' => array(),
					'global'  => false
				)
			)
		),

		'all_movies' => array(
			'page_title'  => __( 'All Movies', 'wpmovielibrary' ),
			'menu_title'  => __( 'All Movies', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'edit.php?post_type=movie',
			'function'    => null,
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'new_movie' => array(
			'page_title'  => __( 'Add New', 'wpmovielibrary' ),
			'menu_title'  => __( 'Add New', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'post-new.php?post_type=movie',
			'function'    => null,
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'collections' => array(
			'page_title'  => __( 'Collections', 'wpmovielibrary' ),
			'menu_title'  => __( 'Collections', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'edit-tags.php?taxonomy=collection&post_type=movie',
			'function'    => null,
			'condition'   => create_function('', 'return wpmoly_o( "enable-collection" );'),
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'genres' => array(
			'page_title'  => __( 'Genres', 'wpmovielibrary' ),
			'menu_title'  => __( 'Genres', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'edit-tags.php?taxonomy=genre&post_type=movie',
			'function'    => null,
			'condition'   => create_function('', 'return wpmoly_o( "enable-genre" );'),
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'actors' => array(
			'page_title'  => __( 'Actors', 'wpmovielibrary' ),
			'menu_title'  => __( 'Actors', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'edit-tags.php?taxonomy=actor&post_type=movie',
			'function'    => null,
			'condition'   => create_function('', 'return wpmoly_o( "enable-actor" );'),
			'hide'        => false,
			'actions'     => array(),
			'scripts'     => array(),
			'styles'      => array()
		),

		'importer' => array(
			'page_title'  => __( 'Import Movies', 'wpmovielibrary' ),
			'menu_title'  => __( 'Import Movies', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'wpmovielibrary-import',
			'function'    => 'WPMOLY_Import::import_page',
			'condition'   => null,
			'hide'        => false,
			'actions'     => array(
				'load-{screen_hook}' => 'WPMOLY_Import::import_movie_list_add_options'
			),
			'scripts'     => array(
				
			),
			'styles'      => array(
				'importer' => array(
					'file'    => sprintf( '%s/assets/css/admin/wpmoly-importer.css', WPMOLY_URL ),
					'require' => array()
				)
			)
		),

		'update_movies' => array(
			'page_title'  => __( 'Update movies to version 1.3', 'wpmovielibrary' ),
			'menu_title'  => __( 'Update movies', 'wpmovielibrary' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'wpmovielibrary-update-movies',
			'function'    => 'WPMOLY_Legacy::update_movies_page',
			'condition'   => null,
			'hide'        => true,
			'actions'     => array(),
			'scripts'     => array(
				'jquery-ajax-queue' => array(
					'file'    => sprintf( '%s/assets/js/vendor/jquery.ajaxQueue.js', WPMOLY_URL ),
					'require' => array( 'jquery' ),
					'footer'  => true
				),
				'updates' => array(
					'file'    => sprintf( '%s/assets/js/admin/wpmoly-updates.js', WPMOLY_URL ),
					'require' => array( WPMOLY_SLUG . '-admin', 'jquery' ),
					'footer'  => true
				)
			),
			'styles'      => array(
				'roboto-font' => array(
					'file'    => '//fonts.googleapis.com/css?family=Roboto:100',
					'require' => array()
				),
				'legacy' => array(
					'file'    => sprintf( '%s/assets/css/admin/wpmoly-legacy.css', WPMOLY_URL ),
					'require' => array()
				)
			)
		)

	)

);

		