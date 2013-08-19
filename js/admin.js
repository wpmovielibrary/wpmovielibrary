jQuery(document).ready(function($) {

	cloudfront = 'http://d3gtl9l2a4fn1j.cloudfront.net/';

	if ( $('#tmdb-settings').length > 0 )
		$('#tmdb-settings').accordion();

	if ( $('#tmdb-tabs-settings').length > 0 )
		$('#tmdb-tabs-settings').tabs();

	$('input#tmdb_empty').click(function(e) {
		e.preventDefault();
		$('.list-table input[type=text], .list-table input[type=hidden], .list-table textarea').val('');
		$('#tmdb_save_images, #progressbar').hide();
		$('.tmdb_select_movie, .tmdb_movie_images').remove();
		$('#tmdb_data').empty();
		tmdb_clear_status();
	});

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

	$('input#tmdb_search').click(function(e) {
		e.preventDefault();
		$('#tmdb_data > *, .tmdb_select_movie, .tmdb_movie_images').remove();

		type = jQuery('#tmdb_search_type > :selected').val();
		data = jQuery('#tmdb_query').val();
		lang = jQuery('#tmdb_search_lang').val();

		if ( type == 'title' )
			tmdb_status(ajax_object.search_movie_title+' "'+data+'"');
		else if ( type == 'id' )
			tmdb_status(ajax_object.search_movie+' #'+data);

		$.ajax({
			type: 'GET',
			url: ajax_object.ajax_url,
			data: {
				action: 'tmdb_search',
				type: type,
				data: data,
				lang: lang
			},
			success: function(response, status, xhr) {
				ct = xhr.getResponseHeader("content-type") || "";
				r  = response;
				if ( ct.indexOf('json') > -1 ) {
					populate_movie(r);
					set_featured(r.poster_path);
				}
				else if ( ct.indexOf('html') > -1 ) {
					$('#tmdb_data').append(r).show();
					$('.tmdb_select_movie a').click(function(e) {
						e.preventDefault();
						id = this.id.replace('tmdb_','');
						search_movie(id);
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
		$('.star').removeClass('s');
		$(this).addClass('s');
		$(this).prevAll().addClass('s');
		$(this).nextAll().removeClass('s');
	});

	$('input#wpml_save').click(function() {
		save_wpml_details();
	});

	populate_movie = function(data) {
		m = data;
		$('.list-table input[type=text], .list-table input[type=hidden], .list-table textarea').each(function() {
			$this = $(this);
			$(this).val('');
			$type = $(this).prop('type');
			
			_id = this.id.replace('tmdb_data_','');
			if ( typeof m[_id] == "object" ) {
				if ( Array.isArray( m[_id] ) ) {
					if ( _id == 'images' ) {
						populate_movie_images(m.images);
					}
					else {
						_v = [];
						$.each(m[_id], function() {
							_v.push( $this.val() + this.name );
						});
						$(this).val(_v.join(', '));
					}
				}
				else {
					console.log(m[_id]);
					$.each(m[_id], function() {
					});
				}
			}
			else {
				_v = ( m[_id] != null ? m[_id] : '' );
				$(this).val(_v);
			}
			$('.list-table, .button-empty').show();
		});
	}

	populate_movie_images = function(images) {

		$('#tmdb_data_images').val('');

		_v = [];
		$.each(m.images, function() {
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
				_v.push(cloudfront+'t/p/w'+j.width+j.file_path);
			});
			$('#tmdb_data_images').val(_v.join(','));
		});

		$('#tmdb_save_images').click(function(e) {
			e.preventDefault();
			save_images();
		});
		
		$('#tmdb_save_images').show();
	}

	save_images = function() {
	    
		img   = $('#tmdb_data_images').val().split(',');
		title = $('#tmdb_data_title').val();
		total = img.length;

		$('#progressbar').progressbar({
			value: false
		}).show();

		d = 1;
		$.each(img, function(i) {
			i = i+1;
			tmdb_status(ajax_object.save_image+' #'+i);
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
	}

	save_wpml_details = function() {
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
	}

	search_movie = function(id) {
		$.ajax({
			type: 'GET',
			url: ajax_object.ajax_url,
			data: {
				action: 'tmdb_search',
				type: 'id',
				data: id
			},
			success: function(_r) {
				$('#tmdb_data > *').not('p').remove();
				$('#tmdb_data').hide();
				populate_movie(_r);
				set_featured(_r.poster_path);
			},
			beforeSend: function() {
				$('input#tmdb_search').addClass('button-loading');
			},
			complete: function() {
				$('input#tmdb_search').removeClass('button-loading');
			},
		});
	}

	set_featured = function(image) {

		if ( wp.media.featuredImage.get() > 0 )
			return false;

		tmdb_status(ajax_object.set_featured);
		title = $('#tmdb_data_title').val();

		$.ajax({
			type: 'GET',
			url: ajax_object.ajax_url,
			data: {
				action: 'tmdb_set_featured',
				image: image,
				post_id: $('#post_ID').val(),
				title: title+' − '+ajax_object.poster
			},
			success: function(r) {
				if ( r ) {
					wp.media.featuredImage.set(r);
					tmdb_status(ajax_object.done);
				}
				else {
					tmdb_status(ajax_object.oops);
				}
			}
		});
	}

	tmdb_status = function(message) {
		$('#tmdb_status').text(message);
	}

	tmdb_clear_status = function() {
		$('#tmdb_status').empty();
	}
});