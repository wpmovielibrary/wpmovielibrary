
$ = jQuery;

wpml = wpml || {};

wpml.importer = {

	init: function() {

		$('a[data-nav=true]').on('click', function(e) {
			e.preventDefault();
			wpml.importer.load( $(this).attr('data-nav-paged') );
		});
		$('a[data-nav=false]').on('click', function(e) {
			e.preventDefault();
		});

		$('#wpml_importer').click(function(e) {
			e.preventDefault();
			wpml.importer.add();
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
				wpml.importer.load( 1 );
			}
		});
	},

	load: function( paged ) {

		$.ajax({
			url: ajaxurl,
			type: 'GET',
			data: {
				action: 'wpml_fetch_imported_movies',
				wpml_fetch_imported_movies_nonce: $('#wpml_fetch_imported_movies_nonce').val(),
				paged: paged
			},
			success: function(response) {
				response = $.parseJSON( response );

				if ( response.rows.length )
					$('#the-list').html( response.rows );
				if ( response.pagination.bottom.length )
					$('.tablenav.top .tablenav-pages').html( $(response.pagination.top).html() );
				if ( response.pagination.top.length )
					$('.tablenav.bottom .tablenav-pages').html( $(response.pagination.bottom).html() );

				wpml.importer.init();
			}
		});
	},
};