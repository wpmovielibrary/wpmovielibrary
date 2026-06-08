<?php
/**
 * Inline SVG icons for the standard TMDb movie genres.
 *
 * Used by the dashboard "Most Popular Genres" panel. Keys are genre term
 * slugs; the special 'default' key is the fallback for unknown genres.
 * Icons are monochrome (currentColor), aria-hidden, drawn on a 48×48 grid.
 *
 * @link https://wplibraries.com
 * @package WPMovieLibrary
 */

return [
	'action'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M27 4 11 27h9l-3 17 20-25H27z"/></svg>',
	'adventure'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><circle cx="24" cy="24" r="19" fill="none" stroke="currentColor" stroke-width="3"/><path d="M32 16l-5.5 10.5L16 32l5.5-10.5z"/></svg>',
	'animation'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M33.5 5.5 42.5 14.5 16 41 5 43 7 32z"/></svg>',
	'comedy'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><circle cx="24" cy="24" r="19" fill="none" stroke="currentColor" stroke-width="3"/><circle cx="17" cy="19" r="2.5"/><circle cx="31" cy="19" r="2.5"/><path d="M15 28c2.5 4 5.5 6 9 6s6.5-2 9-6" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>',
	'crime'           => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M10 26c0-8 6-14 14-14s14 6 14 14v8"/><path d="M17 28c0-4.5 3-7.5 7-7.5s7 3 7 7.5v8"/><path d="M24 28v12"/></svg>',
	'documentary'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><rect x="5" y="14" width="26" height="20" rx="3"/><path d="M33 21l10-6v18l-10-6z"/></svg>',
	'drama'           => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M10 7c4.5 2 9.5 3 14 3s9.5-1 14-3v15c0 11-6 19-14 19s-14-8-14-19z" fill="none" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/><circle cx="18" cy="20" r="2.5"/><circle cx="30" cy="20" r="2.5"/><path d="M18 32c1.8-2.7 3.8-4 6-4s4.2 1.3 6 4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>',
	'family'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><circle cx="17" cy="13" r="6.5"/><path d="M6 39c0-7.5 4.8-12 11-12s11 4.5 11 12z"/><circle cx="34" cy="19" r="4.5"/><path d="M27.5 39c0-5.5 2.9-8.5 6.5-8.5s6.5 3 6.5 8.5z"/></svg>',
	'fantasy'         => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M29 15 33 19 11 41 7 37z"/><path d="M36 4l2.2 4.6 5 .7-3.6 3.6.9 5.1-4.5-2.4-4.5 2.4.9-5.1L29 9.3l5-.7z"/><circle cx="14" cy="16" r="2"/><circle cx="24" cy="8" r="2"/></svg>',
	'history'         => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 6h24M12 42h24"/><path d="M15 6v4c0 6 4 9 9 14-5 5-9 8-9 14v4M33 6v4c0 6-4 9-9 14 5 5 9 8 9 14v4"/></svg>',
	'horror'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M24 5c-8 0-14 6-14 15v22l5-4 5 4 4-4 4 4 5-4 5 4V20c0-9-6-15-14-15z" fill="none" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/><circle cx="19" cy="20" r="2.5"/><circle cx="29" cy="20" r="2.5"/></svg>',
	'music'           => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M19 37V11l18-5v26" fill="none" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/><ellipse cx="14" cy="37" rx="5" ry="4.5"/><ellipse cx="32" cy="32" rx="5" ry="4.5"/></svg>',
	'mystery'         => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><circle cx="20" cy="20" r="12" fill="none" stroke="currentColor" stroke-width="3.5"/><path d="M29 29l13 13" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>',
	'romance'         => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M24 42S6 30 6 17C6 11 10.5 6 16.5 6 20 6 23 8 24 11c1-3 4-5 7.5-5C37.5 6 42 11 42 17c0 13-18 25-18 25z"/></svg>',
	'science-fiction' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path fill-rule="evenodd" d="M24 4c6 4 9 10 9 17 0 4.6-1.4 9-4 12.5H19c-2.6-3.5-4-7.9-4-12.5 0-7 3-13 9-17zm0 9.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"/><path d="M16 31l-6 9 8-3zM32 31l6 9-8-3zM21 36h6l-3 8z"/></svg>',
	'thriller'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M24 11C14 11 6.5 18 3.5 24 6.5 30 14 37 24 37s17.5-7 20.5-13C41.5 18 34 11 24 11z" fill="none" stroke="currentColor" stroke-width="3"/><circle cx="24" cy="24" r="5.5"/></svg>',
	'war'             => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M8 31c0-10 7-18 16-18s16 8 16 18v3H8z"/><rect x="4" y="36" width="40" height="5" rx="2.5"/></svg>',
	'western'         => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M15.5 25c0-9 3-17 8.5-17s8.5 8 8.5 17z"/><path d="M4 27c6.5 4.5 13 6.5 20 6.5S37.5 31.5 44 27c0 7-9 13-20 13S4 34 4 27z"/></svg>',
	'default'         => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true" fill="currentColor"><path d="M24 4l6.2 12.6L44 18.6l-10 9.7L36.4 42 24 35.5 11.6 42 14 28.3 4 18.6l13.8-2z"/></svg>',
];
