jQuery(document).ready(function($) {

	// Settings
	if ( $('#wpml-tabs').length > 0 )
		$('#wpml-tabs').tabs();

	$('input#APIKey_check').click(function(e) {
		e.preventDefault();
		$('#api_status').remove();
		$.ajax({
			type: 'GET',
			url: ajax_object.ajax_url,
			data: {
				action: 'tmdb_api_key_check',
				key: $('input#APIKey').val()
			},
			success: function(response) {
				$('input#APIKey_check').after(response);
			},
			beforeSend: function() {
				$('input#APIKey_check').addClass('button-loading');
			},
			complete: function() {
				$('input#APIKey_check').removeClass('button-loading');
			},
		});
	});

	// TMDb data -- New movie
	$('input#tmdb_empty').click(function(e) {
		e.preventDefault();
		
		a = document.getElementsByClassName('tmdb_data_field');
		for ( i = 0; i < a.length; ++i ) a.item(i).value = '';
		document.getElementById('tmdb_save_images').style.display = 'none';
		document.getElementById('progressbar').style.display = 'none';
		a = document.getElementsByClassName('tmdb_select_movie');
		while( a.item(0) ) a.item(0).remove();
		a = document.getElementsByClassName('tmdb_movie_images');
		while( a.item(0) ) a.item(0).remove();
		document.getElementById('tmdb_data').innerHTML = '';
		wpml.status.clear();
	});

	$('input#tmdb_search').click(function(e) {
		e.preventDefault();
		wpml.movie.type = $('#tmdb_search_type > :selected').val();
		wpml.movie.data = $('#tmdb_query').val();
		wpml.movie.lang = $('#tmdb_search_lang').val();
		wpml.movie.search_movie();
	});

	// Status

	$('#movie-status-select').siblings('a.edit-movie-status').click(function() {
		if ( $('#movie-status-select').is(":hidden") ) {
			$('#movie-status-select').slideDown('fast');
			$(this).hide();
		}
		return false;
	});

	$('.save-movie-status', '#movie-status-select').click(function() {
		$('#movie-status-select').slideUp('fast');
		$('#movie-status-select').siblings('a.edit-movie-status').show();
		$('#movie-status-display').text($('#movie_status > option:selected').text());
		return false;
	});

	$('.cancel-movie-status', '#movie-status-select').click(function() {
		$('#movie-status-select').slideUp('fast');
		$('#movie_status').val($('#hidden_movie_status').val());
		$('#movie-status-display').text($('#hidden_movie_status').val());
		$('#movie-status-select').siblings('a.edit-movie-status').show();
		
		return false;
	});

	// Media

	$('#movie-media-select').siblings('a.edit-movie-media').click(function() {
		if ( $('#movie-media-select').is(":hidden") ) {
			$('#movie-media-select').slideDown('fast');
			$(this).hide();
		}
		return false;
	});

	$('.save-movie-media', '#movie-media-select').click(function() {
		$('#movie-media-select').slideUp('fast');
		$('#movie-media-select').siblings('a.edit-movie-media').show();
		$('#movie-media-display').text($('#movie_media > option:selected').text());
		return false;
	});

	$('.cancel-movie-media', '#movie-media-select').click(function() {
		$('#movie-media-select').slideUp('fast');
		$('#movie_media').val($('#hidden_movie_media').val());
		$('#movie-media-display').text($('#hidden_movie_media').val());
		$('#movie-media-select').siblings('a.edit-movie-media').show();
		
		return false;
	});

	// Rating

	$('#movie-rating-select').siblings('a.edit-movie-rating').click(function() {
		if ( $('#movie-rating-select').is(":hidden") ) {
			$('#movie_rating_display').hide();
			$('#movie-rating-select').slideDown('fast');
			$(this).hide();
		}
		return false;
	});

	$('.save-movie-rating', '#movie-rating-select').click(function() {
		var n = $('.star.s').last().prop('id').replace('star-','');
		$('#movie-rating-select').slideUp('fast');
		$('#movie-rating-select').siblings('a.edit-movie-rating').show();
		$('#movie_rating_display').removeClass().addClass('stars-'+n).show();
		$('#movie_rating, #hidden_movie_rating').val(n);
		return false;
	});

	$('.cancel-movie-rating', '#movie-rating-select').click(function() {
		$('#movie-rating-select').slideUp('fast');
		$('#movie_media').val($('#hidden_movie_media').val());
		$('#movie-rating-display').text($('#hidden_movie_media').val());
		$('#movie-rating-select').siblings('a.edit-movie-rating').show();
		$('#movie_rating_display').show();
		return false;
	});

	$('.star').not('.s').hover(
		function() {
			$(this).addClass('on');
			$(this).prevAll().addClass('on');
			$(this).nextAll().removeClass('on');
		},
		function() {
			$(this).removeClass('on');
			$(this).nextAll().removeClass('on');
		}
	);

	$('.star').click(function() {
		$('.star').removeClass('s on');
		$(this).addClass('s');
		$(this).prevAll().addClass('s');
		$(this).nextAll().removeClass('s on');
	});

	$('input#wpml_save').click(function() {
		wpml.movie.save_details();
	});

	
	// Movie import
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

	$('#wpml-import #wpml_empty').click(function() {
		$('.wpml-import-movie-select').remove();
	});

	$('.delete_movie').click(function(e) {
		e.preventDefault();
		var id = this.id.replace('delete_','');
		wpml.movie.delete(id);
	});

});

$ = jQuery;

wpml = {

	movie: {

		lang: '',

		data: {},

		type: '',

		delete: function(id) {

			$.ajax({
				type: 'GET',
				url: ajax_object.ajax_url,
				data: {
					action: 'wpml_delete_movie',
					post_id: id
				},
				success: function(response) {
					$('#post_'+id).parents('tr').remove();
				},
				beforeSend: function() {
					$('input.loader').addClass('button-loading');
				},
				complete: function() {
					$('input.loader').removeClass('button-loading loader');
				},
			});

		},

		get_movie: function(id) {

			$.ajax({
				type: 'GET',
				url: ajax_object.ajax_url,
				data: {
					action: 'tmdb_search',
					type: 'id',
					data: id,
					lang: wpml.movie.lang
				},
				success: function(response) {
					tmdb_data = document.getElementById('tmdb_data');
					while (tmdb_data.lastChild) tmdb_data.removeChild(tmdb_data.lastChild);
					tmdb_data.style.display = 'none';
					wpml.movie.populate(response);
					wpml.movie.images.set_featured(response.poster_path, null, response.title);
				},
				beforeSend: function() {
					$('input#tmdb_search').addClass('button-loading');
				},
				complete: function() {
					$('input#tmdb_search').removeClass('button-loading');
				},
			});

		},

		populate: function(data) {

			$('.tmdb_data_field').each(function() {

				var field = this;
				var type = field.type;
				var _id = this.id.replace('tmdb_data_','');
				var _v = '';

				field.value = '';

				if ( typeof data[_id] == "object" ) {
					if ( Array.isArray( data[_id] ) ) {
						if ( _id == 'images' ) {
							wpml.movie.images.populate(data.images);
						}
						else {
							_v = [];
							$.each(data[_id], function() {
								_v.push( field.value + this.name );
							});
							value = _v.join(', ');
						}
					}
				}
				else {
					_v = ( data[_id] != null ? data[_id] : '' );
					value = _v;
				}

				if ( type == 'input' )
					field.value = _v;
				else if ( type == 'textarea' )
					field.innerHTML = _v;

				$('.list-table, .button-empty').show();
			});

			if ( data.taxonomy.actors.length ) {
				$.each(data.taxonomy.actors, function(i) {
					$('#tagsdiv-actor .tagchecklist').append('<span><a id="actor-check-num-'+i+'" class="ntdelbutton">X</a>&nbsp;'+this+'</span>');
				});
			}

			if ( data.taxonomy.genres.length ) {
				$.each(data.taxonomy.genres, function(i) {
					$('#tagsdiv-genre .tagchecklist').append('<span><a id="genre-check-num-'+i+'" class="ntdelbutton">X</a>&nbsp;'+this+'</span>');
				});
			}
		},

		populate_select_list: function(data) {

			$('#tmdb_data').append(data.p).show();

			var html = '';

			$.each(data.movies, function() {
				html += '<div class="tmdb_select_movie">';
				html += '	<a id="tmdb_'+this.id+'" href="#">';
				html += '		<img src="'+this.poster+'" alt="'+this.title+'" />';
				html += '		<em>'+this.title+'</em>';
				html += '	</a>';
				html += '	<input type=\'hidden\' value=\''+this.json+'\' />';
				html += '</div>';
			});

			$('#tmdb_data').append(html);
		},

		save_details: function() {

			$.ajax({
				type: 'POST',
				url: ajax_object.ajax_url,
				data: {
					action: 'wpml_save_details',
					post_id: $('#post_ID').val(),
					wpml_details: {
						media: $('#movie_media').val(),
						status: $('#movie_status').val(),
						rating: $('#movie_rating').val()
					}
				},
				beforeSend: function() {
					$('input#wpml_save').addClass('button-loading');
				},
				complete: function() {
					$('input#wpml_save').removeClass('button-loading');
				},
			});

		},

		search_movie: function() {

			$('#tmdb_data > *, .tmdb_select_movie, .tmdb_movie_images').remove();

			if (  wpml.movie.type == 'title' )
				wpml.status.set(ajax_object.search_movie_title+' "'+ wpml.movie.data+'"');
			else if (  wpml.movie.type == 'id' )
				wpml.status.set(ajax_object.search_movie+' #'+ wpml.movie.data);

			$.ajax({
				type: 'GET',
				url: ajax_object.ajax_url,
				data: {
					action: 'tmdb_search',
					type:  wpml.movie.type,
					data:  wpml.movie.data,
					lang: wpml.movie.lang
				},
				success: function(response) {
					if ( response.result == 'movie' ) {
						wpml.movie.populate(response);
						wpml.movie.images.set_featured(response.poster_path, null, response.title);
					}
					else if ( response.result == 'movies' ) {
						wpml.movie.populate_select_list(response);

						$('.tmdb_select_movie a').click(function(e) {
							e.preventDefault();
							id = this.id.replace('tmdb_','');
							wpml.movie.get_movie(id);
						});
					}
				},
				beforeSend: function() {
					$('input#tmdb_search').addClass('button-loading');
				},
				complete: function() {
					$('input#tmdb_search').removeClass('button-loading');
				},
			});
		},

		images: {

			populate: function(images) {

				$('#tmdb_data_images').val('');

				_v = [];
				$.each(images, function() {
					html  = '<div class="tmdb_movie_images">';
					html += '<a href="#" class="tmdb_movie_image_remove"></a>';
					html += '<img src=\''+ajax_object.base_url_small+this.file_path+'\' data-tmdb=\''+JSON.stringify(this)+'\' alt=\'\' />';
					html += '</div>';
					$('#tmdb_images_preview').append(html);
					_v.push(ajax_object.base_url_original+this.file_path);
				});
				$('#tmdb_data_images').val(_v.join(','));

				$('.tmdb_movie_image_remove').click(function(e) {
					e.preventDefault();
					$(this).parent('.tmdb_movie_images').remove();
					_v = [];
					$('.tmdb_movie_images').each(function() {
						j = $.parseJSON($(this).find('img').attr('data-tmdb'));
						_v.push(ajax_object.base_url_original+j.file_path);
					});
					$('#tmdb_data_images').val(_v.join(','));
				});

				$('#tmdb_save_images').click(function(e) {
					e.preventDefault();
					wpml.movie.images.save();
				});
				
				$('#tmdb_save_images').show();

			},

			save: function() {

				img   = $('#tmdb_data_images').val().split(',');
				title = $('#tmdb_data_title').val();
				total = img.length;

				$('#progressbar').progressbar({
					value: false
				}).show();

				$.each(img, function(i) {
					i = i+1;
					wpml.status.set(ajax_object.save_image+' #'+i);
					$.ajax({
						type: 'GET',
						url: ajax_object.ajax_url,
						data: {
							action: 'tmdb_save_image',
							image: this,
							post_id: $('#post_ID').val(),
							title: title+' − Photo '+i
						},
						success: function(_r) {
							v = $('#tmdb_data_images').val();
							$('#tmdb_data_images').val(v.replace(img,''));
							
						},
						complete: function() {
							$('#progressbar').progressbar({
								value: ( $('#progressbar').progressbar('value') + ( 100 / total ) )
							});
							$('.progress-label').text($('#progressbar').progressbar('value') + '%');
						}
					});
				});
				$('.tmdb_movie_images').remove();

			},

			set_featured: function(image, id, title) {

				if ( ! $('#wpml-tmdb') || wp.media.featuredImage.get() > 0 )
					return false;

				wpml.status.set(ajax_object.set_featured);
				var title   = title || $('#tmdb_data_title').val();
				var post_id = id || $('#post_ID').val();

				$.ajax({
					type: 'GET',
					url: ajax_object.ajax_url,
					data: {
						action: 'tmdb_set_featured',
						image: image,
						post_id: post_id,
						title: title+' − '+ajax_object.poster
					},
					success: function(r) {
						if (r) {
							wp.media.featuredImage.set(r);
							wpml.status.set(ajax_object.done);
						}
						else {
							wpml.status.set(ajax_object.oops);
						}
					}
				});

			},
		},
	},

	import: {

		target: {},

		get_movie: function(id) {

			$.ajax({
				type: 'GET',
				url: ajax_object.ajax_url,
				data: {
					action: 'tmdb_search',
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

				if ( ! post_id.length ) {
					console.log('!post_id');
					return false;
				}

				wpml.import.search_movie(title);
			});
			
		},
		
		populate: function(data) {

			var tr     = $('#p_'+data._id);
			var fields = $('#p_'+data._id+'_tmdb_data input')

			data.images = [];

			fields.each(function(i, field) {

				//field  = this;
				f_name = field.id.replace('p_'+data._id+'_tmdb_data_','');
				
				if ( Array.isArray( data[f_name] ) && data[f_name].length ) {
					var _v = [];
					$.each(data[f_name], function() {
						_v.push( field.value + this.name );
					});
					field.value = _v.join(', ');
				}
				else {
					var _v = ( data[f_name] != null ? data[f_name] : '' );
					field.value = _v;
				}
			});

			$('#p_'+data._id+'_tmdb_data_tmdb_id').val(data.id);
			$('#p_'+data._id+'_tmdb_data_post_id').val(data._id);
			$('#p_'+data._id+'_tmdb_data_poster').val(data.poster_path);

			tr.find('.poster').html('<img src="'+ajax_object.base_url_xxsmall+data.poster_path+'" alt="'+data.title+'" />');
			tr.find('.movie_title').text(data.title);
			tr.find('.movie_director').text($('#p_'+data._id+'_tmdb_data_director').val());
			tr.find('.movie_tmdb_id').text(data.id);
		},
		
		populate_select_list: function(data) {

			var html = '';

			$.each(data.movies, function() {
				html += '<div class="tmdb_select_movie">';
				html += '	<a id="tmdb_'+this.id+'" href="#'+data._id+'">';
				html += '		<img src="'+this.poster+'" alt="'+this.title+'" />';
				html += '		<em>'+this.title+'</em>';
				html += '	</a>';
				html += '	<input type=\'hidden\' value=\''+this.json+'\' />';
				html += '</div>';
			});

			html = '<tr class="wpml-import-movie-select"><td colspan="6"><div class="tmdb_select_movies">'+html+'</div></td></tr>'

			wpml.import.target.after(html);
		},

		search_movie: function(title) {

			$.ajax({
				type: 'GET',
				url: ajax_object.ajax_url,
				data: {
					action: 'tmdb_search',
					type: 'title',
					data: title,
					lang: '',
					_id: post_id
				},
				success: function(response) {

					wpml.import.target = $('#p_'+response._id); // Update the target for populates

					if ( response.result == 'movie' ) {
						wpml.import.populate(response);
					}
					else if ( response.result == 'movies' ) {
						wpml.import.populate_select_list(response);

						$('.tmdb_select_movie a').unbind('click').bind('click', function(e) {
							e.preventDefault();
							
							post_id = this.hash.replace('#',''); // Update post_id so that get_movie will find the target TR
							tmdb_id = this.id.replace('tmdb_','');
							wpml.import.get_movie(tmdb_id);
							$(this).parents('.wpml-import-movie-select').remove();
						});
					}
				},
				beforeSend: function() {
					$('input.loader').addClass('button-loading');
				},
				complete: function() {
					$('input.loader').removeClass('button-loading loader');
				},
			});

		},
		
		set_target: function(wot) {
			this.target = wot;
		},
	},

	status: {

		set: function(message) {
			$('#tmdb_status').text(message);
		},

		clear: function() {
			$('#tmdb_status').empty();
		}
		
	},

};