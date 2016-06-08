<?php

$default_filters = array(
	array(
		'hookname' => 'wpmoly/shortcode/format/runtime/value',
		'callback' => 'wpmoly\Formatting\runtime'
	),
	array(
		'hookname' => 'wpmoly/shortcode/format/production_countries/value',
		'callback' => 'wpmoly\Formatting\production_countries'
	),
	array(
		'hookname' => 'wpmoly/shortcode/format/local_release_date/value',
		'callback' => 'wpmoly\Formatting\local_release_date'
	),
	array(
		'hookname' => 'wpmoly/shortcode/format/release_date/value',
		'callback' => 'wpmoly\Formatting\release_date'
	)
);

foreach ( $default_filters as $filter ) {
	add_filter( $filter['hookname'], $filter['callback'] );
}
