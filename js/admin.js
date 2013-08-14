jQuery(document).ready(function($) {

	cloudfront = 'http://d3gtl9l2a4fn1j.cloudfront.net/';

	if ( $('#tmdb-settings').length > 0 )
		$('#tmdb-settings').accordion();

	if ( $('#tmdb-tabs-settings').length > 0 )
		$('#tmdb-tabs-settings').tabs();

	$('input#tmdb_empty').click(function(e) {
		e.preventDefault();
		$('.list-table input[type=text], .list-table input[type=hidden]').val('');
		$('#tmdb_save_images').hide();
		$('#tmdb_data > *, .tmdb_select_movie, .tmdb_movie_images').remove();
	});

	$('input#APIKey_check').click(function(e) {
		e.preventDefault();
		$.ajax({
			type: 'GET',
			url: ajax_object.ajax_url,
			data: {
				action: 'tmdb_api_key_check',
				key: $('input#APIKey').val()
			},
			success: function(response) {
				
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
		$('#tmdb_data > *, .tmdb_select_movie').remove();
		$.ajax({
			type: 'GET',
			url: ajax_object.ajax_url,
			data: {
				action: 'tmdb_search',
				type: jQuery('#tmdb_search_type > :selected').val(),
				data: jQuery('#tmdb_query').val()
			},
			success: function(response, status, xhr) {
				ct = xhr.getResponseHeader("content-type") || "";
				r  = response;
				if ( ct.indexOf('json') > -1 ) {
					populate_movie(r);
				}
				else if ( ct.indexOf('html') > -1 ) {
					$('#tmdb_data').append(r).show();
					$('.tmdb_select_movie a').click(function(e) {
						e.preventDefault();
						id = this.id.replace('tmdb_','');
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
							}
						});
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

	populate_movie = function(data) {
		//m = $.parseJSON(data);
		m = data;
		$('.list-table input[type=text], .list-table input[type=hidden]').each(function() {
			$this = $(this);
			$(this).val('');
			_id = this.id.replace('tmdb_data_','');
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
				_v = ( m[_id] != null ? m[_id] : '' );
				$(this).val(_v);
			}
			$('.list-table, .button-empty').show();
		});
	}

	populate_movie_images = function(images) {

		$('#progressbar').remove();
		$('#tmdb_data_images').val('');
		$('#tmdb_data_images').after('<div id="tmdb_data_images_preview" />');

		_v = [];
		$.each(m.images, function() {
			url   = cloudfront+'t/p/w'+this.width+this.file_path;
			url_  = cloudfront+'t/p/w150'+this.file_path;
			html  = '<div class="tmbd_movie_images">';
			html += '<a href="#" class="tmbd_movie_image_remove"></a>';
			html += '<img src=\''+url_+'\' data-tmdb=\''+JSON.stringify(this)+'\' alt=\'\' />';
			html += '</div>';
			$('#tmdb_data_images_preview').append(html);
			_v.push(url);
		});
		$('#tmdb_data_images').val(_v.join(','));

		$('.tmbd_movie_image_remove').click(function(e) {
			e.preventDefault();
			$(this).parent('.tmbd_movie_images').remove();
			_v = [];
			$('.tmbd_movie_images').each(function() {
				j = $.parseJSON($(this).find('img').attr('data-tmdb'));
				_v.push(cloudfront+'t/p/w'+j.width+j.file_path);
			});
			$('#tmdb_data_images').val(_v.join(','));
		});

		$('#tmdb_save_images').click(function(e) {
			e.preventDefault();
			img = $('#tmdb_data_images').val().split(',');
			total = img.length;
			$('#tmdb_data_images_preview').html('<div id="progressbar"><div class="progress-label">...</div></div>');
			$('#progressbar').progressbar();
			$.each(img, function(i) {
				i = i+1;
				$.ajax({
					type: 'GET',
					url: ajax_object.ajax_url,
					data: {
						action: 'tmdb_save_image',
						image: this,
						post_id: $('#post_ID').val()
					},
					success: function(_r) {
						v = $('#tmdb_data_images').val();
						$('#tmdb_data_images').val(v.replace(img,''));
					},
					complete: function() {
						$('#progressbar').progressbar({
							value: ( ( 100 / total ) * i )
						});
						$('.progress-label').text(i+' / '+total);
					}
				});
			});
			$('.progress-label').empty();
		});
		
		$('#tmdb_save_images').show();
	}
});