
$ = $ || jQuery;

wpmoly = wpmoly || {};

	wpmoly.editor = {}

		/**
		 * Handles 'All Movies' Table and Post Editor Movie Meta part
		 * 
		 * Add excerpt to actors lists, handles Quick/Bulk Edit in All
		 * Movies Table.
		 * 
		 * Populate Meta fields, taxonomies and featured images with the
		 * data fetched by wpmoly.editor.meta.search_movie().
		 */
		wpmoly.editor.movies = wpmoly_edit_movies = {

			actors: 'td.column-taxonomy-actor',
			visible: '.visible-actors',
			hidden: '.hidden-actors',

			_more: {
				element: '.more-actors',
				event: 'click'
			},

			init: function() {},
			short_actors: function() {},
			quick_edit: function() {}
		};

			/**
			 * Init Events and generate Actors Excerpt list
			 */
			wpmoly.editor.movies.init = function() {

				wpmoly_edit_movies.short_actors();

				$( wpmoly_edit_movies._more.element ).on( wpmoly_edit_movies._more.event, function( e ) {
					e.preventDefault();
					wpmoly_edit_movies.toggle_actors( this );
				});

			};

			/**
			 * Hide most of the actors in the All Movies view. Since
			 * movies can contain a large number of actors we limit
			 * the list length to the first five names and show a link
			 * to toggle the hidden rest on the list.
			 * 
			 * @since    1.0
			 */
			wpmoly.editor.movies.short_actors = function() {

				$( wpmoly_edit_movies.actors ).each( function() {

					var $links = $( this ).find('a');
					if ( $links.length ) {
						var visible = [],
						   _visible = $links.slice( 0, 5 ),
						     hidden = [],
						    _hidden = $links.slice( 5 );

						_visible.each( function() { visible.push( this.outerHTML ); } );
						_hidden.each( function() { hidden.push( this.outerHTML ); } );

						$( this ).html( '<span class="visible-actors"></span>, <span class="hidden-actors"></span> <a class="more-actors" href="#">' + wpmoly_lang.see_more + '</a>' );
						$( this ).find( wpmoly_edit_movies.visible ).html( visible.join( ', ' ) );
						$( this ).find( wpmoly_edit_movies.hidden ).html( hidden.join( ', ' ) );
					}
				});
			};

			/**
			 * Toggle the Show/Hide all actors link.
			 * 
			 * @since    1.0
			 * 
			 * @param    object    Link DOM object
			 */
			wpmoly.editor.movies.toggle_actors = function( link ) {

				$( link ).prev( wpmoly_edit_movies.hidden ).toggle();
				if ( 'none' != $( link ).prev( wpmoly_edit_movies.hidden ).css( 'display' ) )
					$( link ).text( wpmoly_lang.see_less );
				else
					$( link ).text( wpmoly_lang.see_more );
			};

			/**
			 * Fill the Quick Edit form with the correct Details.
			 * This can't be done in PHP so we have to get the data
			 * through AJAX and update the form manually.
			 * 
			 * @since    1.0
			 * 
			 * @param    object    Movie details: status, media, rating
			 * @param    string    Security Nonce
			 */
			wpmoly.editor.movies.quick_edit = function( details, nonce ) {

				var $wp_inline_edit = inlineEditPost.edit;

				inlineEditPost.edit = function( id ) {

					$wp_inline_edit.apply( this, arguments );

					var post_id = 0;

					if ( typeof( id ) == 'object' )
						post_id = parseInt( this.getId( id ) );

					if ( ! post_id )
						return false;

					wpmoly.update_nonce( 'set-quickedit-movie-details', nonce );

					$( '.inline-edit-movie-detail', '#edit-' + post_id ).each(function() {
						var detail = this.id.replace( 'movie-', '' );
						$( this ).children( 'option' ).each( function() {
							$option = $( this );
							if ( $.isArray( details[ detail ] ) ) {
								$.each( details[ detail ], function( i, _d ) {
									if ( _d == $option.val() )
										$option.prop( 'selected', true );
								});
							}
							else {
								$option.prop( 'selected', ( details[ detail ] == $option.val() ) );
							}
						});
					});
				};
			};

		wpmoly.editor.movies.init();