
$ = $ || jQuery;

wpmoly = wpmoly || {};

	wpmoly.importer = {};
	
		wpmoly.importer.movies = wpmoly_import_movies = {

			list: '#wpmoly_import_list',
			button: '#wpmoly_importer',
		};

			/**
			 * Delete one or more movie import drafts
			 * 
			 * @since    1.0
			 * 
			 * @param    array    Movies to delete
			 */
			wpmoly.importer.movies.delete = function( movies ) {

				if ( ! $.isArray( movies ) )
					var movies = [ movies ];

				$.each( movies, function() {
					$( 'tr#p_' + this ).next( '.wpmoly-import-movie-select' ).remove();
				} );

				wpmoly._get({
					data: {
						action: 'wpmoly_delete_movies',
						nonce: wpmoly.get_nonce( 'delete-movies' ),
						movies: movies
					},
					error: function( response ) {
						wpmoly_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpmoly_state.set( this, 'error' );
						});
					},
					success: function( response ) {

						$(response.data).each( function() {
							$( '#post_' + this ).parents( 'tr, li' ).fadeToggle().remove();
						});

						var message = ( response.data.length > 1 ? wpmoly_lang.deleted_movies : wpmoly_lang.deleted_movie );
						wpmoly_state.clear();
						wpmoly_state.set( message.replace( '%s', response.data.length ), 'error');

						if ( ! $( wpmoly_import_meta.selected + ':checked' ).length ) {
							wpmoly_import_view.reload( {}, 'queued' );
							wpmoly_import_view.reload( {} );
						}
						$( '.spinner' ).hide();
					},
					complete: function( r ) {
						wpmoly.update_nonce( 'delete-movies', r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Import a list of movies by title.
			 * 
			 * Call WPMOLY_Import::import_movies_callback() to create movie draft
			 * for each title submitted and update the table
			 * 
			 * @since    1.0
			 */
			wpmoly.importer.movies.import = function() {

				if ( undefined == $( wpmoly_import_movies.list ) || '' == $( wpmoly_import_movies.list ).val() )
					return false;

				wpmoly._post({
					dataType: 'json',
					data: {
						action: 'wpmoly_import_movies',
						nonce: wpmoly.get_nonce( 'import-movies-list' ),
						movies: $( wpmoly_import_movies.list ).val(),
					},
					beforeSend: function() {
						$( wpmoly_import_movies.button ).prev( '.spinner' ).addClass( 'spinning' );
					},
					error: function( response ) {
						wpmoly_state.clear();
						if ( undefined != response.responseJSON.errors ) {
							$.each( response.responseJSON.errors, function() {
								if ( $.isArray( this ) ) {
									$.each( this, function() {
										wpmoly_state.set( this, 'error' );
									});
								}
								else {
									wpmoly_state.set( this, 'error' );
								}
							});
						}
						else
							wpmoly_state.set( wpmoly_lang.oops, 'error' );

						$( wpmoly_import_movies.list ).val( '' );
						wpmoly_import_view.reload({});
					},
					success: function( response ) {

						if ( undefined != response.errors ) {
							wpmoly_state.clear();
							if ( undefined != response.errors ) {
								$.each( response.errors, function() {
									if ( $.isArray( this ) ) {
										$.each( this, function() {
											wpmoly_state.set( this, 'error' );
										});
									}
									else {
										wpmoly_state.set( this, 'error' );
									}
								});
							}
							else
								wpmoly_state.set( wpmoly_lang.oops, 'error' );

							$( wpmoly_import_movies.list ).val( '' );
							wpmoly_import_view.reload({});
						} else {
							var message = ( response.data.length > 1 ? wpmoly_lang.imported_movies : wpmoly_lang.imported_movie );
							$( wpmoly_import_movies.list ).val('');
							wpmoly_state.clear();
							wpmoly_state.set( message.replace( '%s', response.data.length ), 'updated' );
							$( '#_wpmoly_imported' ).trigger( 'click' );
							wpmoly_import_view.reload( {} );
						}
					},
					complete: function( r ) {
						wpmoly.update_nonce( 'import-movies-list', r.responseJSON.nonce );
						$( wpmoly_import_movies.button ).prev( '.spinner' ).removeClass( 'spinning' );
					}
				});
			};