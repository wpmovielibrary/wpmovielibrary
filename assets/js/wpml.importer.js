
$ = jQuery;

wpml = wpml || {};

wpml.import = {

	target: {},

	init: function() {

		$('#wpml-import #wpml_empty').click(function() {
			$('.wpml-import-movie-select').remove();
		});

		$('#wpml-import input#doaction, #wpml-import input#doaction2').click(function(e) {

			e.preventDefault();
			n = this.id.replace('do','');
			action = $(this).prev('select[name='+n+']');
			$(this).addClass('loader');

			if ( 'tmdb_data' == action.val() ) {
				wpml.import.get_movies();
			}
			else if ( 'delete' == action.val() ) {
				$('.movies > tbody input[type=checkbox]:checked').each(function() {
					var id = this.id.replace('post_','');
					wpml.movie.delete(id);
				});
			}
			else {
				return false;
			}
		});

		$('#wpml-import .fetch_movie').click(function(e) {

			e.preventDefault();

			post_id = this.id.replace('fetch_','');
			tr      = $(this).parents('tr');
			tr.prop('id', 'p_'+post_id);

			title = tr.find('.movietitle span.movie_title').text();

			if ( ! post_id.length )
				return false;

			wpml.import.search_movie( title );
		});
	},

		add: function() {

			if ( undefined == $('#wpml_import_list') || '' == $('#wpml_import_list').val() )
				return false;

			var movies = $('#wpml_import_list').val();

			$.ajax({
				type: 'POST',
				url: ajax_object.ajax_url,
				data: {
					action: 'wpml_import_movies',
					wpml_import_list: movies,
					wpml_ajax_movie_import: $('#wpml_ajax_movie_import').val()
				},
				success: function(response) {
					if ( 0 == response )
						return false;

					$('.updated').remove();
					$('#wpml_import_list').val('');
					$('#wpml-tabs').before('<div class="updated"><p>'+response+'</p></div>');
					$('#_wpml_imported').trigger('click');
					wpml.import.table.load({
						paged: 1,
						order: 'asc',
						orderby: 'title'
					});
				}
			});
		},

	get_movie: function( id ) {

		$.ajax({
			type: 'GET',
			url: ajax_object.ajax_url,
			data: {
				action: 'tmdb_search',
				wpml_check: ajax_object.wpml_check,
				type: 'id',
				data: id,
				_id: post_id
			},
			success: function(response) {
					wpml.import.populate(response);
			},
			beforeSend: function() {
				$('input.loader').addClass('button-loading');
				$('#p_'+post_id).find('.poster').addClass('loading');
			},
			complete: function() {
				$('input.loader').removeClass('button-loading');
				$('.poster.loading').removeClass('loading');
			},
		});

	},

	get_movies: function() {

		$('.movies > tbody input[type=checkbox]:checked').each(function(i) {

			post_id = this.value;	// Set post_id for search_movie
			tr      = $(this).parents('tr');
			tr.prop('id', 'p_'+post_id);

			title = tr.find('.movietitle span.movie_title').text();

			if ( ! post_id.length )
				return false;

			tr.find('.poster').addClass('loading');
			wpml.import.search_movie( title );
		});

	},

	populate: function(data) {

		var tr     = $('#p_'+data._id);
		var fields = $('#p_'+data._id+'_tmdb_data input');
		console.log(data);

		data.images = [];

		fields.each(function(i, field) {

			f_name = field.id.replace('p_'+data._id+'_tmdb_data_','');

			var sub = wpml.switch_data( f_name );

			if ( 'meta' == sub )
				var _data = data.meta;
			else if ( 'crew' == sub )
				var _data = data.crew;
			else
				var _data = data;

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

		$('#p_'+data._id+'_tmdb_data_tmdb_id').val(data._tmdb_id);
		$('#p_'+data._id+'_tmdb_data_post_id').val(data._id);
		$('#p_'+data._id+'_tmdb_data_poster').val(data.poster_path);

		tr.find('.poster').html('<img src="'+ajax_object.base_url_xxsmall+data.poster_path+'" alt="'+data.meta.title+'" />');
		tr.find('.movie_title').text(data.meta.title);
		tr.find('.movie_director').text($('#p_'+data._id+'_tmdb_data_director').val());
		tr.find('.movie_tmdb_id').text(data._tmdb_id);

		$('#p_'+data._id+'_tmdb_data').appendTo('#tmdb_data');
	},

	populate_select_list: function(data) {

		var html = '';

		var _next = wpml.import.target.next();
		var _tmdb_id = wpml.import.target.find('.movie_tmdb_id');

		if ( ( undefined != _next && _next.hasClass('wpml-import-movie-select') ) || ( undefined != _tmdb_id && '' != _tmdb_id.text() ) )
			return false;

		$.each(data.movies, function() {
			html += '<div class="tmdb_select_movie">';
			html += '	<a id="tmdb_'+this.id+'" href="#'+data._id+'">';
			html += '		<img src="'+this.poster+'" alt="'+this.title+'" />';
			html += '		<em>'+this.title+'</em>';
			html += '	</a>';
			html += '	<input type=\'hidden\' value=\''+this.json+'\' />';
			html += '</div>';
		});

		html = '<tr class="wpml-import-movie-select"><td colspan="7"><div class="tmdb_select_movies">'+html+'</div></td></tr>';

		wpml.import.target.after(html);
	},

	search_movie: function( title ) {

		$.ajax({
			type: 'GET',
			url: ajax_object.ajax_url,
			data: {
				action: 'tmdb_search',
				wpml_check: ajax_object.wpml_check,
				type: 'title',
				data: title,
				lang: '',
				_id: post_id
			},
			success: function(response) {

				wpml.import.target = $('#p_'+response._id); // Update the target for populates

				if ( response._result == 'movie' ) {
					wpml.import.populate(response);
				}
				else if ( response._result == 'movies' ) {
					wpml.import.populate_select_list(response);

					$('.tmdb_select_movie a').unbind('click').bind('click', function(e) {
						e.preventDefault();

						post_id = this.hash.replace('#',''); // Update post_id so that get_movie will find the target TR
						tmdb_id = this.id.replace('tmdb_','');
						wpml.import.get_movie(tmdb_id);
						$(this).parents('.wpml-import-movie-select').remove();
					});
				}
				else if ( response._result == 'error' || response._result == 'empty' ) {
					$('#import-intro').after('<div id="import-error" class="success settings-error">'+response.p+'</div>').show();
				}
			},
			beforeSend: function() {
				$('input.loader').addClass('button-loading');
			},
			complete: function() {
				$('input.loader').removeClass('button-loading loader');
				$('.poster.loading').removeClass('loading');
			},
		});

	},

	set_target: function(wot) {
		this.target = wot;
	},

	table: {
	
		init: function() {

			var timer;
			var delay = 500;

			// Nav and sort links click
			$('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
				e.preventDefault();
				var query = this.search.substring( 1 );
				var data = {
					paged: wpml.http_query_var( query, 'paged' ) || '1',
					order: wpml.http_query_var( query, 'order' ) || 'asc',
					orderby: wpml.http_query_var( query, 'orderby' ) || 'title'
				};
				wpml.import.table.load( data );
			});

			// Update when manually changing page number in input field
			$('input[name=paged]').on('keyup', function(e) {

				if ( 13 == e.which )
					e.preventDefault();

				var data = {
					paged: parseInt( $('input[name=paged]').val() ) || '1',
					order: $('input[name=order]').val() || 'asc',
					orderby: $('input[name=orderby]').val() || 'title'
				};

				window.clearTimeout( timer );
				timer = window.setTimeout(function() {
					wpml.import.table.load( data );
				}, delay);
			});

			// Import a list of titles
			$('#wpml_importer').click(function(e) {
				e.preventDefault();
				wpml.import.add();
			});
		},

		load: function( data ) {

			$.ajax({
				url: ajaxurl,
				type: 'GET',
				data: $.extend(
					{
						action: 'wpml_fetch_imported_movies',
						wpml_fetch_imported_movies_nonce: $('#wpml_fetch_imported_movies_nonce').val(),
					},
					data
				),
				success: function( response ) {

					var response = $.parseJSON( response );
					if ( response.rows.length )
						$('#the-list').html( response.rows );
					if ( response.column_headers.length )
						$('thead tr, tfoot tr').html( response.column_headers );
					if ( response.pagination.bottom.length )
						$('.tablenav.top .tablenav-pages').html( $(response.pagination.top).html() );
					if ( response.pagination.top.length )
						$('.tablenav.bottom .tablenav-pages').html( $(response.pagination.bottom).html() );
					wpml.import.table.init();
				}
			});
		}

	}
};