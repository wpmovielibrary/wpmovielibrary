
$ = jQuery;

wpml = wpml || {};

wpml.importer = {

	init: function() {

		var timer;
		var delay = 500;

		// Nav and sort links click
		$('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
			e.preventDefault();
			var query = this.search.substring( 1 );
			var data = {
				paged: wpml.importer.__query( query, 'paged' ) || '1',
				order: wpml.importer.__query( query, 'order' ) || 'asc',
				orderby: wpml.importer.__query( query, 'orderby' ) || 'title'
			};
			wpml.importer.load( data );
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
				wpml.importer.load( data );
			}, delay);
		});

		// Import a list of titles
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
				wpml.importer.load({
					paged: 1,
					order: 'asc',
					orderby: 'title'
				});
			}
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
				wpml.importer.init();
			}
		});
	},

	__query: function( query, variable ) {

		var vars = query.split("&");
		for ( var i = 0; i <vars.length; i++ ) {
			var pair = vars[ i ].split("=");
			if ( pair[0] == variable )
				return pair[1];
		}
		return false;
	}
};