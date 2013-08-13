jQuery(document).ready(function($) {
	$("input#tmdb_search").click(function(e) {
		e.preventDefault();
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
					console.log(r);
					populate_movie(r);
				}
				else if ( ct.indexOf('html') > -1 ) {
					$('#tmdb_data').html(r);
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
								$('#tmdb_data').empty();
								populate_movie(_r);
							}
						});
					});
				}
			}
		});
	});

	populate_movie = function(data) {
		//m = $.parseJSON(data);
		m = data;
		jQuery('.list-table input').each(function() {
			$this = $(this);
			_id = this.id.replace('tmdb_data_','');
			if ( Array.isArray( m[_id] ) ) {
				_v = [];
				$.each(m[_id], function() {
					_v.push( $this.val() + this.name );
				});
				_v = _v.join(', ');
			}
			else {
				_v  = ( m[_id] != null ? m[_id] : '' );
			}
			$(this).val(_v);
		});
	}
});