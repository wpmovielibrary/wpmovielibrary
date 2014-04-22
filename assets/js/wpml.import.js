
wpml = wpml || {};

var wpml_import;

	wpml.import = wpml_import = {

		target: {},

		select: '.tmdb_select_movie a',
		select_list: '.wpml-import-movie-select',

		init: undefined,
		get_movie: undefined,
		populate: undefined,
		populate_select_list: undefined,
		search_movie: undefined
	}

		/**
		 * Init WPML Import
		 */
		wpml.import.init = function() {
			
			$(wpml_import.select).unbind('click').bind('click', function( e ) {
				e.preventDefault();
				wpml_import.select_movie( this );
			});
		};

		wpml.import.search_movie = function( post_id, title, loading ) {

			wpml._get({
					action: 'wpml_search_movie',
					wpml_check: wpml_ajax.utils.wpml_check,
					type: 'title',
					data: title,
					lang: '',
					_id: post_id
				},
				function( response ) {

					wpml_import.target = $('#p_' + response._id); // Update the target for populates

					if ( response._result == 'movie' ) {
						wpml.import.populate(response);
						wpml.queue.init();
					}
					else if ( response._result == 'movies' ) {
						wpml.import.populate_select_list(response);
						wpml.import.init();
					}
					else if ( response._result == 'error' || response._result == 'empty' ) {
						$('#p_' + post_id).find('.movie_title').after('<div class="import-error">' + response.p + '</div>');
						$('#p_' + post_id).find('.loading').removeClass('loading');
					}
				}
			);

		};

		/**
		 * Select a movie from the suggestion list.
		 * 
		 * @param    Object    Select link HTML object
		 */
		wpml.import.select_movie = function( link ) {
			var $link = $(link),
			    post_id = $link.attr('data-post-id'),
			    tmdb_id = $link.attr('data-tmdb-id');

			wpml.import.get_movie( post_id, tmdb_id );
			$link.parents(wpml_import.select_list).remove();
			wpml.queue.init();
		}

		/**
		 * Get movie by ID
		 * 
		 * Call WPML_TMDb::search_movie_callback() to find the movie
		 * matching the ID we got through wpml.import.search_movie()
		 * 
		 * @param    int    Movie TMDb ID
		 */
		wpml.import.get_movie = function( post_id, tmdb_id ) {
			wpml._get({
					action: 'wpml_search_movie',
					wpml_check: wpml_ajax.utils.wpml_check,
					type: 'id',
					data: tmdb_id,
					_id: post_id
				},
				function( response ) {
					wpml.import.populate( response );
				}
			);
		};

		/**
		 * Populate newly fetched movie fields
		 * 
		 * @param    object    Movie TMDb data object
		 */
		wpml.import.populate = function( data ) {

			var tr     = $('#p_' + data._id),
			    fields = $('#p_' + data._id + '_tmdb_data input');

			data.images = [];

			fields.each(function(i, field) {

				var f_name = field.id.replace('p_' + data._id + '_tmdb_data_',''),
				       sub = wpml.switch_data( f_name ),
				     _data = data;

				if ( 'meta' == sub )
					var _data = data.meta;
				else if ( 'crew' == sub )
					var _data = data.crew;

				if ( Array.isArray( _data[f_name] ) && _data[f_name].length ) {
					var _v = [];
					$.each(_data[f_name], function() {
						_v.push( field.value + this );
					});
					field.value = _v.join(', ');
				}
				else {
					var _v = ( _data[f_name] != null ? _data[f_name] : '' );
					field.value = _v;
				}
			});

			$('#p_' + data._id + '_tmdb_data_tmdb_id').val( data._tmdb_id );
			$('#p_' + data._id + '_tmdb_data_post_id').val( data._id );
			$('#p_' + data._id + '_tmdb_data_poster').val( data.poster_path );

			tr.find('.poster').html('<img src="' + wpml_ajax.utils.base_url.xxsmall + data.poster_path + '" alt="' + data.meta.title + '" />');
			tr.find('.movie_title').text(data.meta.title);
			tr.find('.movie_director').text($('#p_' + data._id + '_tmdb_data_director').val());
			tr.find('.movie_tmdb_id').html('<a href="http://www.themoviedb.org/movie/' + data._tmdb_id + '">' + data._tmdb_id + '</a>');

			$('#p_' + data._id + '_tmdb_data').appendTo('#tmdb_data');
			tr.find('.loading').removeClass('loading');
		};

		/**
		 * Populate the Movie Select list with possible choices of 
		 * movies matching the search.
		 * 
		 * @param    array    Movies TMDb data objects
		 */
		wpml.import.populate_select_list = function( data ) {

			var html = '';

			var _next = wpml_import.target.next();
			var _tmdb_id = wpml_import.target.find('.movie_tmdb_id');

			if ( ( undefined != _next && _next.hasClass('wpml-import-movie-select') ) || ( undefined != _tmdb_id && '' != _tmdb_id.text() ) )
				return false;

			$.each( data.movies, function() {
				html += '<div class="tmdb_select_movie">';
				html += '	<a href="#" data-post-id="' + data._id + '" data-tmdb-id="' + this.id + '">';
				html += '		<img src="' + this.poster + '" alt="' + this.title + '" />';
				html += '		<em>' + this.title + '</em>';
				html += '	</a>';
				html += '	<input type=\'hidden\' value=\'' + this.json + '\' />';
				html += '</div>';
			});

			html = '<tr class="wpml-import-movie-select"><td colspan="7"><div class="tmdb_select_movies">' + html + '</div></td></tr>';

			wpml_import.target.after( html );
		};

	
	wpml.import.init();
