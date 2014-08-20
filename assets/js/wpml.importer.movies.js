
$ = $ || jQuery;

wpml = wpml || {};

	wpml.importer = {};
	
		wpml.importer.movies = wpml_import_movies = {

			list: '#wpml_import_list',
		};

			/**
			 * Delete one or more movie import drafts
			 * 
			 * @since    1.0
			 * 
			 * @param    array    Movies to delete
			 */
			wpml.importer.movies.delete = function( movies ) {

				if ( ! $.isArray( movies ) )
					var movies = [ movies ];

				wpml._get({
					data: {
						action: 'wpml_delete_movies',
						nonce: wpml.get_nonce( 'delete-movies' ),
						movies: movies
					},
					error: function( response ) {
						wpml_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpml_state.set( this, 'error' );
						});
					},
					success: function( response ) {

						$(response.data).each( function() {
							$( '#post_' + this ).parents( 'tr, li' ).fadeToggle().remove();
						});

						var message = ( response.data.length > 1 ? wpml_ajax.lang.deleted_movies : wpml_ajax.lang.deleted_movie );
						wpml_state.clear();
						wpml_state.set( message.replace( '%s', response.data.length ), 'error');

						if ( ! $( wpml_import_meta.selected + ':checked' ).length ) {
							wpml_import_view.reload( {}, 'queued' );
							wpml_import_view.reload( {} );
						}
						$( '.spinner' ).hide();
					},
					complete: function( r ) {
						wpml.update_nonce( 'delete-movies', r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Import a list of movies by title.
			 * 
			 * Call WPML_Import::import_movies_callback() to create movie draft
			 * for each title submitted and update the table
			 * 
			 * @since    1.0
			 */
			wpml.importer.movies.import = function() {

				if ( undefined == $( wpml_import_movies.list ) || '' == $( wpml_import_movies.list ).val() )
					return false;

				wpml._post({
					data: {
						action: 'wpml_import_movies',
						nonce: wpml.get_nonce( 'import-movies-list' ),
						movies: $( wpml_import_movies.list ).val(),
					},
					error: function( response ) {
						wpml_state.clear();
						$.each( response.responseJSON.errors, function() {
							if ( $.isArray( this ) ) {
								$.each( this, function() {
									wpml_state.set( this, 'error' );
								});
							}
							else {
								wpml_state.set( this, 'error' );
							}
						});
						$( wpml_import_movies.list ).val( '' );
						wpml_import_view.reload({});
					},
					success: function( response ) {

						var message = ( response.data.length > 1 ? wpml_ajax.lang.imported_movies : wpml_ajax.lang.imported_movie );
						$( wpml_import_movies.list ).val('');
						wpml_state.clear();
						wpml_state.set( message.replace( '%s', response.data.length ), 'updated' );
						$( '#_wpml_imported' ).trigger( 'click' );
						wpml_import_view.reload( {} );
					},
					complete: function( r ) {
						wpml.update_nonce( 'import-movies-list', r.responseJSON.nonce );
					}
				});
			};