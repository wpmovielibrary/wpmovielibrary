
$ = $ || jQuery;

wpml = wpml || {};

	wpml.editor = {};

		/**
		 * Movies Post Editor page's Metadata part
		 * 
		 * @since    1.0.0
		 */
		wpml.editor.meta = wpml_edit_meta = {

			// Search option
			type: $( '#tmdb_search_type option:selected' ).val(),
			lang: $( '#tmdb_search_lang option:selected' ).val(),
			title: $( '#tmdb_query' ).val(),
			post_id: parseInt( $( '#post_ID' ).val() ),
			tmdb_id: undefined,

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

			fields: '#tmdb_data',
		};

			/**
			 * Init Events
			 */
			wpml.editor.meta.init = function() {

				$( wpml_edit_meta._search.element ).on( wpml_edit_meta._search.event, function( e ) {
					e.preventDefault();
					wpml_edit_meta.search();
				});

				$( wpml_edit_meta._title.element ).on( wpml_edit_meta._title.event, function( e ) {
					wpml_edit_meta.prefill_title( $(this).val() );
				});
			
				$( wpml_edit_meta._empty.element ).on( wpml_edit_meta._empty.event, function( e ) {
					e.preventDefault();
					wpml_edit_meta.empty_results();
				});

				$( wpml_edit_meta._query.element ).on( wpml_edit_meta._query.event, function() {
					wpml_edit_meta.title = $(this).val();
				});
			};

			/**
			 * Search a movie by its title.
			 * 
			 * If a single match if found, set it; if multiple matches
			 * are found, display a selection menu.
			 * 
			 * @since    1.0.0
			 */
			wpml.editor.meta.search = function() {

				if ( '' == wpml_edit_meta.title )
					wpml_edit_meta.title = $( '#tmdb_query' ).val();

				$( wpml_edit_meta._search.element ).next('.spinner' ).css({display: 'inline-block'});
				$( wpml_edit_meta.fields ).empty().hide();

				wpml_state.clear();
				if ( wpml_edit_meta.type == 'title' )
					wpml_state.set( wpml_ajax.lang.search_movie_title + ' "' + wpml_edit_meta.title + '"', 'warning' );
				else if ( wpml_edit_meta.type == 'id' )
					wpml_state.set( wpml_ajax.lang.search_movie + ' #' + wpml_edit_meta.tmdb_id, 'success' );

				wpml._get({
					data: {
						action: 'wpml_search_movie',
						nonce: wpml.get_nonce( 'search-movies' ),
						type: wpml_edit_meta.type,
						data: ( wpml_edit_meta.type == 'title' ? wpml_edit_meta.title : wpml_edit_meta.tmdb_id ),
						lang: wpml_edit_meta.lang
					},
					error: function( response ) {
						wpml_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpml_state.set( this, 'error' );
						});
					},
					success: function( response ) {
						if ( 'movie' == response.data.result ) {
							wpml_edit_meta.set( response.data.movies[ 0 ] );
							wpml_posters.set_featured( response.data.movies[ 0 ].poster_path );
						}
						else if ( 'movies' == response.data.result ) {
							wpml_edit_meta.select( response.data.movies, response.data.message );
						}
					},
					complete: function( r ) {
						$( wpml_edit_meta._search.element ).next('.spinner' ).hide();
						wpml.update_nonce( 'search-movies', r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Display a list of movies matching the search.
			 * 
			 * @since    1.0.0
			 * 
			 * @param    object    Movies to add to the selection list
			 * @param    string    Notice message to display
			 */
			wpml.editor.meta.select = function( movies, message ) {

				var html = '', message = message || '';

				if ( '' != message )
					$( wpml_edit_meta.fields ).addClass('update success' ).append('<p>' + message + '</p>' ).show();

				$.each( movies, function() {
					var $movie = $( '<div class="tmdb_select_movie"><a id="tmdb_' + this.id + '" href="#" onclick="wpml_edit_meta.get( ' + this.id + ' ); return false;"><img src="' + this.poster + '" alt="' + this.title + '" /><em>' + this.title + '</em></a><input type=\'hidden\' value=\'' + this.json + '\' /></div>' );
					$( wpml_edit_meta.fields ).append( $movie );
				});

			};
			
			/**
			 * Get a movie by its ID.
			 * 
			 * @since    1.0.0
			 * 
			 * @param    int       Movie TMDb ID
			 */
			wpml.editor.meta.get = function( tmdb_id ) {
				wpml._get({
					data: {
						action: 'wpml_search_movie',
						nonce: wpml.get_nonce( 'search-movies' ),
						type: 'id',
						data: tmdb_id,
						lang: wpml_edit_meta.lang
					},
					beforeSend: function() {
						$( wpml_edit_meta._search.element ).next( '.spinner' ).css( { display: 'inline-block' } );
					},
					error: function( response ) {
						wpml_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpml_state.set( this, 'error' );
						});
					},
					success: function( response ) {
						$( wpml_edit_meta.fields ).empty().hide();
						wpml_edit_meta.tmdb_id = response.data._tmdb_id;
						wpml_edit_meta.set( response.data );
						wpml_posters.set_featured( response.data.poster_path );
					},
					complete: function( r ) {
						$( wpml_edit_meta._search.element ).next( '.spinner' ).hide();
						wpml.update_nonce( 'search-movies', r.responseJSON.nonce );
					}
				});
			};
			
			/**
			 * Fill in the meta fields.
			 * 
			 * @since    1.0.0
			 * 
			 * @param    object    Movie metadata
			 */
			wpml.editor.meta.set = function( data ) {

				$( '#tmdb_data_tmdb_id' ).val( data._tmdb_id );
				$( '.tmdb_data_field' ).each( function() {

					var field = this, value = '', type = field.type,
					    slug = this.id.replace( 'tmdb_data_', '' ),
					     sub = wpml.switch_data( slug ),
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
					var limit = parseInt( $( '#wpml_actor_limit' ).val() ) || 0,
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
					$.each( data.crew.director, function(i) {
						$( '#newcollection' ).prop( 'value', this );
						$( '#collection-add-submit' ).click();
					});
				}

				$( '#tmdb_query' ).focus();
				wpml_state.clear();
				wpml_state.set( wpml_ajax.lang.done, 'success' );
			};

			/**
			 * Prefill the Movie Meta Metabox search input with the
			 * page title.
			 * 
			 * @since    1.0.0
			 * 
			 * @param    string    Movie Title
			 */
			wpml.editor.meta.prefill_title = function( title ) {
				if ( '' != title )
					wpml_edit_meta.title = title;
					$( '#tmdb_query' ).val( title );
			};

			/**
			* Empty all Movie search result fields, reset all taxonomies 
			* and remove the featured image.
			 * 
			 * @since    1.0.0
			*/
			wpml.editor.meta.empty_results = function() {

				$( '.tmdb_data_field' ).val( '' );
				$( '.categorydiv input[type=checkbox]' ).prop( 'checked', false );
				$( '.tagchecklist' ).empty();
				$( '#tmdb_data' ).empty().hide();
				$( '#remove-post-thumbnail' ).trigger( 'click' );

				wpml_state.clear();
			};

		wpml_edit_meta.init();