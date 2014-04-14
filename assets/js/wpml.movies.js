
wpml = wpml || {};

wpml.movies = wpml_movies = {

	lang: '',
	data: {},
	type: '',

	init: function() {},
	search: function() {},
	get_movie: function() {},
	populate: function() {},
	populate_quick_edit: function() {},
	populate_select_list: function() {}
};

	wpml.movies.search = function() {

		$('#tmdb_data > *, .tmdb_select_movie').remove();
		$('.tmdb_movie_images').not('.tmdb_movie_imported_image').remove();

		if (  wpml_movies.type == 'title' )
			wpml.status.set(wpml_ajax.lang.search_movie_title + ' "' +  wpml_movies.data + '"', 'warning');
		else if (  wpml_movies.type == 'id' )
			wpml.status.set(wpml_ajax.lang.search_movie + ' #' +  wpml_movies.data, 'success');

		wpml.get({
				action: 'wpml_search_movie',
				wpml_check: wpml_ajax.utils.wpml_check,
				type: wpml_movies.type,
				data: wpml_movies.data,
				lang: wpml_movies.lang
			},
			function( response ) {
				if ( 'movie' == response._result ) {
					wpml_movies.populate( response );
					wpml.media.posters.set_featured( response.poster_path/*, null, response.title, response._tmdb_id*/ );
				}
				else if ( 'movies' == response._result ) {
					wpml_movies.populate_select_list( response );

					$('.tmdb_select_movie a').click(function(e) {
						e.preventDefault();
						id = this.id.replace('tmdb_','');
						wpml_movies.get_movie(id);
					});
				}
				else if ( 'error' == response._result || 'empty' == response._result ) {
					$('#tmdb_data').html( response.p ).show();
					$('#tmdb_status').empty();
				}
			}
		);
	};

	wpml.movies.get_movie = function( id ) {

		wpml.get({
				action: 'wpml_search_movie',
				wpml_check: wpml_ajax.utils.wpml_check,
				type: 'id',
				data: id,
				lang: wpml.movies.lang
			},
			function(response) {
				var tmdb_data = document.getElementById('tmdb_data');
				while ( tmdb_data.lastChild )
					tmdb_data.removeChild( tmdb_data.lastChild );
				tmdb_data.style.display = 'none';
				wpml_movies.populate( response );
				wpml.media.posters.set_featured( response.poster_path );
			}
		);
	};

	wpml.movies.populate = function(data) {

		$('#tmdb_data_tmdb_id').val(data._tmdb_id);

		$('.tmdb_data_field').each(function() {

			var field = this;
			var type = field.type;
			var _id = this.id.replace('tmdb_data_','');
			var value = '';

			field.value = '';

			var sub = wpml.switch_data( _id );

			if ( 'meta' == sub )
				var _data = data.meta;
			else if ( 'crew' == sub )
				var _data = data.crew;
			else
				var _data = data;

			if ( typeof _data[_id] == "object" ) {
				if ( Array.isArray( _data[_id] ) ) {
					_v = [];
					$.each(_data[_id], function() {
						_v.push( field.value + this );
					});
					value = _v.join(', ');
				}
			}
			else {
				_v = ( _data[_id] != null ? _data[_id] : '' );
				value = _v;
			}

			$(field).val(_v);

			$('.list-table, .button-empty').show();
		});

		if ( data.taxonomy.actors.length ) {
			$.each( data.taxonomy.actors, function(i) {
				$('#tagsdiv-actor .tagchecklist').append('<span><a id="actor-check-num-' + i + '" class="ntdelbutton">X</a>&nbsp;' + this + '</span>');
				tagBox.flushTags( $('#actor.tagsdiv'), $('<span>' + this + '</span>') );
			});
		}

		if ( data.taxonomy.genres.length ) {
			$.each( data.taxonomy.genres, function(i) {
				$('#tagsdiv-genre .tagchecklist').append('<span><a id="genre-check-num-' + i + '" class="ntdelbutton">X</a>&nbsp;' + this + '</span>');
				tagBox.flushTags( $('#genre.tagsdiv'), $('<span>' + this + '</span>') );
			});
		}

		if ( data.crew.director.length ) {
			$.each( data.crew.director, function(i) {
				$('#newcollection').prop('value', this);
				$('#collection-add-submit').click();
			});
		}

		$('#tmdb_query').focus();
		wpml.status.set(wpml_ajax.lang.done, 'success');
	};

	wpml.movies.populate_quick_edit = function( movie_details, nonce ) {

		var $wp_inline_edit = inlineEditPost.edit;

		inlineEditPost.edit = function( id ) {

			$wp_inline_edit.apply( this, arguments );

			var $post_id = 0;

			if ( typeof( id ) == 'object' )
				$post_id = parseInt( this.getId( id ) );

			if ( $post_id > 0 ) {

				var $edit_row = $( '#edit-' + $post_id );

				var nonceInput = $('#wpml_movie_details_nonce');
				nonceInput.val( nonce );

				var movie_media = $edit_row.find('select.movie_media');
				var movie_status = $edit_row.find('select.movie_status');
				var movie_rating = $edit_row.find('#movie_rating');
				var hidden_movie_rating = $edit_row.find('#hidden_movie_rating');
				var stars = $edit_row.find('#stars');

				movie_media.children('option').each(function() {
					if ( $(this).val() == movie_details.movie_media )
						$(this).prop("selected", "selected");
					else
						$(this).prop("selected", "");
				});

				movie_status.children('option').each(function() {
					if ( $(this).val() == movie_details.movie_status )
						$(this).prop("selected", true);
					else
						$(this).prop("selected", false);
				});

				if ( '' != movie_details.movie_rating && ' ' != movie_details.movie_rating ) {
					movie_rating.val( movie_details.movie_rating );
					hidden_movie_rating.val( movie_details.movie_rating );
					stars.removeClass('stars_', 'stars_0_0', 'stars_0_5', 'stars_1_0', 'stars_1_5', 'stars_2_0', 'stars_2_5', 'stars_3_0', 'stars_3_5', 'stars_4_0', 'stars_4_5', 'stars_5_0');
					stars.addClass( 'stars_' + movie_details.movie_rating.replace('.','_') );
					stars.attr('data-rated', true);
					stars.attr('data-default-rating', movie_details.movie_rating);
					stars.attr('data-rating', movie_details.movie_rating);
				}

			}

		};
	};

	wpml.movies.populate_select_list = function( data ) {

		$('#tmdb_data').append( data.p ).show();

		var html = '';

		$.each( data.movies, function() {
			html += '<div class="tmdb_select_movie">';
			html += '	<a id="tmdb_' + this.id + '" href="#">';
			html += '		<img src="' + this.poster + '" alt="' + this.title + '" />';
			html += '		<em>' + this.title + '</em>';
			html += '	</a>';
			html += '	<input type=\'hidden\' value=\'' + this.json + '\' />';
			html += '</div>';
		});

		$('#tmdb_data').append(html);

	};