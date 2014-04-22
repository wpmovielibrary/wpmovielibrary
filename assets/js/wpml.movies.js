
wpml = wpml || {};

wpml.movies = wpml_movies = {

	lang: '',
	data: {},
	type: '',
	movies: {},

	init: function() {},
	search: function() {},
	get_movie: function() {}
};

	wpml.movies.search = function() {

		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: {
				action: 'wpml_search_movie',
				wpml_check: wpml_ajax.utils.wpml_check,
				type: wpml_movies.type,
				data: wpml_movies.data,
				lang: wpml_movies.lang
			},
			success: function( response ) {
				wpml_movies.movies = response;
			}
		});
	};

	wpml.movies.get_movie = function( id ) {

		wpml._get({
				action: 'wpml_search_movie',
				wpml_check: wpml_ajax.utils.wpml_check,
				type: 'id',
				data: id,
				lang: wpml_movies.lang
			},
			function( response ) {
				wpml_movies.movies = response;
			}
		);
	};