
$ = $ || jQuery;

wpmoly = wpmoly || {};

	wpmoly.editor = {

		_movie_id: $('#post_ID').val(),
		_movie_title: $('#meta_data_title').val(),
		_movie_tmdb_id: $('#meta_data_tmdb_id').val(),

	};

		/**
		 * Movies Post Editor page's Metadata part
		 * 
		 * @since    1.0
		 */
		wpmoly.editor.meta = wpmoly_edit_meta = {

			// Search option
			type: $( '#tmdb_search_type option:selected' ).val(),
			lang: $( '#tmdb_search_lang option:selected' ).val(),
			title: $( '#tmdb_query' ).val(),
			post_id: parseInt( $( '#post_ID' ).val() ),
			tmdb_id: undefined,
			poster_featured: $( '#wpmoly_poster_featured' ).val(),

			// Events
			_search: {
				element: '#tmdb_search',
				event: 'click'
			},
			_title: {
				element: '#title',
				event: 'input'
			},
			_empty: {
				element: '#tmdb_empty',
				event: 'click'
			},
			_query: {
				element: '#tmdb_query',
				event: 'input'
			},

			fields: '#meta_data',
		};

			/**
			 * Init Events
			 */
			wpmoly.editor.meta.init = function() {

				$( wpmoly_edit_meta._search.element ).on( wpmoly_edit_meta._search.event, function( e ) {
					e.preventDefault();
					wpmoly_edit_meta.search();
				});

				$( wpmoly_edit_meta._title.element ).on( wpmoly_edit_meta._title.event, function( e ) {
					wpmoly_edit_meta.prefill_title( $(this).val() );
				});
			
				$( wpmoly_edit_meta._empty.element ).on( wpmoly_edit_meta._empty.event, function( e ) {
					e.preventDefault();
					wpmoly_edit_meta.empty_results();
				});

				$( wpmoly_edit_meta._query.element ).on( wpmoly_edit_meta._query.event, function() {
					wpmoly_edit_meta.title = $(this).val();
				});

				wpmoly_edit_meta.poster_featured = ( undefined != wpmoly_edit_meta.poster_featured && '1' == wpmoly_edit_meta.poster_featured );
			};

			/**
			 * Search a movie by its title.
			 * 
			 * If a single match if found, set it; if multiple matches
			 * are found, display a selection menu.
			 * 
			 * @since    1.0
			 */
			wpmoly.editor.meta.search = function() {

				if ( '' == wpmoly_edit_meta.title )
					wpmoly_edit_meta.title = $( '#tmdb_query' ).val();

				$( wpmoly_edit_meta._search.element ).next('.spinner' ).css({display: 'inline-block'});
				$( wpmoly_edit_meta.fields ).empty().hide();

				wpmoly_state.clear();
				if ( wpmoly_edit_meta.type == 'title' )
					wpmoly_state.set( wpmoly_ajax.lang.search_movie_title + ' "' + wpmoly_edit_meta.title + '"', 'warning' );
				else if ( wpmoly_edit_meta.type == 'id' )
					wpmoly_state.set( wpmoly_ajax.lang.search_movie + ' #' + wpmoly_edit_meta.tmdb_id, 'success' );

				wpmoly._get({
					data: {
						action: 'wpmoly_search_movie',
						nonce: wpmoly.get_nonce( 'search-movies' ),
						type: wpmoly_edit_meta.type,
						data: ( wpmoly_edit_meta.type == 'title' ? wpmoly_edit_meta.title : wpmoly_edit_meta.tmdb_id ),
						lang: wpmoly_edit_meta.lang
					},
					error: function( response ) {
						wpmoly_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpmoly_state.set( this, 'error' );
						});
					},
					success: function( response ) {
						if ( 'movie' == response.data.result ) {
							wpmoly_edit_meta.set( response.data.movies[ 0 ] );
							if ( wpmoly_edit_meta.poster_featured )
								wpmoly_posters.set_featured( response.data.movies[ 0 ].poster_path );
						}
						else if ( 'movies' == response.data.result ) {
							wpmoly_edit_meta.select( response.data.movies, response.data.message );
						}
						else if ( 'empty' == response.data.result ) {
							wpmoly_state.set( response.data.message, 'error' );
						}
					},
					complete: function( r ) {
						$( wpmoly_edit_meta._search.element ).next('.spinner' ).hide();
						wpmoly.update_nonce( 'search-movies', r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Display a list of movies matching the search.
			 * 
			 * @since    1.0
			 * 
			 * @param    object    Movies to add to the selection list
			 * @param    string    Notice message to display
			 */
			wpmoly.editor.meta.select = function( movies, message ) {

				var html = '', message = message || '';

				if ( '' != message )
					$( wpmoly_edit_meta.fields ).addClass('update success' ).append('<p>' + message + '</p>' ).show();

				$.each( movies, function() {
					var $movie = $( '<div class="tmdb_select_movie"><a id="tmdb_' + this.id + '" href="#" onclick="wpmoly_edit_meta.get( ' + this.id + ' ); return false;"><img src="' + this.poster + '" alt="' + this.title + '" /><em>' + this.title + '</em></a><input type=\'hidden\' value=\'' + this.json + '\' /></div>' );
					$( wpmoly_edit_meta.fields ).append( $movie );
				});

			};
			
			/**
			 * Get a movie by its ID.
			 * 
			 * @since    1.0
			 * 
			 * @param    int       Movie TMDb ID
			 */
			wpmoly.editor.meta.get = function( tmdb_id ) {
				wpmoly._get({
					data: {
						action: 'wpmoly_search_movie',
						nonce: wpmoly.get_nonce( 'search-movies' ),
						type: 'id',
						data: tmdb_id,
						lang: wpmoly_edit_meta.lang
					},
					beforeSend: function() {
						$( wpmoly_edit_meta._search.element ).next( '.spinner' ).css( { display: 'inline-block' } );
					},
					error: function( response ) {
						wpmoly_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpmoly_state.set( this, 'error' );
						});
					},
					success: function( response ) {
						$( wpmoly_edit_meta.fields ).empty().hide();
						wpmoly_edit_meta.tmdb_id = response.data._tmdb_id;
						wpmoly_edit_meta.set( response.data );
						if ( wpmoly_edit_meta.poster_featured )
							wpmoly_posters.set_featured( response.data.poster_path );
					},
					complete: function( r ) {
						$( wpmoly_edit_meta._search.element ).next( '.spinner' ).hide();
						wpmoly.update_nonce( 'search-movies', r.responseJSON.nonce );
					}
				});
			};
			
			/**
			 * Fill in the meta fields.
			 * 
			 * @since    1.0
			 * 
			 * @param    object    Movie metadata
			 */
			wpmoly.editor.meta.set = function( data ) {

				wpmoly.editor._movie_tmdb_id = data._tmdb_id;
				wpmoly.editor._movie_title   = data.meta.title;

				$( '#meta_data_tmdb_id' ).val( data._tmdb_id );
				$( '.meta_data_field' ).each( function() {

					var field = this, value = '', type = field.type,
					    slug = this.id.replace( 'meta_data_', '' ),
					     sub = wpmoly.switch_data( slug ),
					   _data = data;
					field.value = '';

					if ( 'meta' == sub )
						_data = data.meta;
					else if ( 'crew' == sub )
						_data = data.crew;

					if ( 'object' == typeof _data[ slug ] ) {
						if ( Array.isArray( _data[ slug ] ) ) {
							_v = [];
							$.each(_data[ slug ], function() {
								_v.push( field.value + this );
							});
							value = _v.join( ', ' );
						}
					}
					else
						_v = ( _data[ slug ] != null ? _data[ slug ] : '' );
						value = _v;

					$( field ).val( _v );
				});

				if ( data.taxonomy.actors.length ) {
					var limit = parseInt( $( '#wpmoly_actor_limit' ).val() ) || 0,
					    actors = ( limit ? data.taxonomy.actors.splice( 0, limit ) : data.taxonomy.actors );

					$.each( actors, function(i) {
						$( '#tagsdiv-actor .tagchecklist' ).append( '<span><a id="actor-check-num-' + i + '" class="ntdelbutton">X</a>&nbsp;' + this + '</span>' );
						tagBox.flushTags( $( '#actor.tagsdiv' ), $( '<span>' + this + '</span>' ) );
					});
				}

				if ( data.taxonomy.genres.length ) {
					$.each( data.taxonomy.genres, function(i) {
						$( '#tagsdiv-genre .tagchecklist' ).append( '<span><a id="genre-check-num-' + i + '" class="ntdelbutton">X</a>&nbsp;' + this + '</span>' );
						tagBox.flushTags( $( '#genre.tagsdiv' ), $( '<span>' + this + '</span>' ) );
					});
				}

				if ( data.crew.director.length ) {
					
					$.each( data.crew.director, function( i, val ) {
						$( '#newcollection' ).delay( 1000 ).queue( function( next ) {
							$( this ).prop( 'value', val );
							$( '#collection-add-submit' ).click();
							next();
						});
					});
				}

				$( '#tmdb_query' ).focus();
				wpmoly_state.clear();
				wpmoly_state.set( wpmoly_ajax.lang.done, 'success' );
			};

			/**
			 * Prefill the Movie Meta Metabox search input with the
			 * page title.
			 * 
			 * @since    1.0
			 * 
			 * @param    string    Movie Title
			 */
			wpmoly.editor.meta.prefill_title = function( title ) {
				if ( '' != title )
					wpmoly_edit_meta.title = title;
					$( '#tmdb_query' ).val( title );
			};

			/**
			* Empty all Movie search result fields, reset all taxonomies 
			* and remove the featured image.
			 * 
			 * @since    1.0
			*/
			wpmoly.editor.meta.empty_results = function() {

				$( '.meta_data_field' ).val( '' );
				$( '#meta_data' ).empty().hide();
				$( '#remove-post-thumbnail' ).trigger( 'click' );

				wpmoly._post({
					data: {
						action: 'wpmoly_empty_meta',
						nonce: wpmoly.get_nonce( 'empty-movie-meta' ),
						post_id: wpmoly_edit_meta.post_id
					},
					beforeSend: function() {
						$( wpmoly_edit_meta._empty.element ).prev( '.spinner' ).css( { display: 'inline-block' } );
					},
					error: function( response ) {
						wpmoly_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpmoly_state.set( this, 'error' );
						});
					},
					success: function( response ) {
						$( '.categorydiv input[type=checkbox]' ).prop( 'checked', false );
						$( '.tagchecklist' ).empty();
					},
					complete: function( r ) {
						$( wpmoly_edit_meta._search.element ).prev( '.spinner' ).hide();
						wpmoly.update_nonce( 'empty-meta', r.responseJSON.nonce );
					}
				});

				wpmoly_state.clear();
			};

		wpmoly_edit_meta.init();