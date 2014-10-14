
$ = $ || jQuery;

wpmoly = wpmoly || {};

	/**
	* WPMOLY Movie editor
	*/
	wpmoly.editor = {};

		/**
		 * WPMOLY Movie Editor: Movie Details
		 */
		wpmoly.editor.details = wpmoly_edit_details = {};

			/**
			 * Inline editor
			 * 
			 * @since    1.0
			 * 
			 * @param    string    Detail type: status, media, rating
			 * @param    object    Caller link DOM Element
			 */
			wpmoly.editor.details.inline_editor = function( type, link ) {

				if ( 'status' != type && 'media' != type && 'rating' != type )
					return false;

				var $editor = $( '.wpmoly-inline-edit-' + type ),
				      $link = $( link ),
				    $parent = $link.parents( 'td' ),
				    display = ( 'none' == $editor.css( 'display' ) ),
				     active = ( $parent.find( '.wpmoly-inline-edit-' + type ).length ),
				       prev = ( 'rating' == type ? '.movie-rating-display' : '.movie_' + type + '_title' ),
				      $prev = $link.prev( prev ),
				      links = '.column-wpmoly-' + type + '.inline_editing .wpmoly-inline-edit-toggle';

				if ( display || ! active ) {
					if ( ! active ) {
						$( links + '.active' ).removeClass( 'active' );
						$(links).find( '.wpmolicon' ).removeClass().addClass( 'wpmolicon icon-cog' );
					}
					$editor.show();
					$parent.addClass( 'inline_editing' );
					$link.addClass( 'active' );
					$link.find( '.wpmolicon' ).removeClass().addClass( 'wpmolicon icon-no-alt' );
					$editor.appendTo( $parent ).show();
				}
				else {
					$link.removeClass( 'active' );
					$parent.removeClass( 'inline_editing' );
					$link.find( '.wpmolicon' ).removeClass().addClass( 'wpmolicon icon-cog' );
					$editor.hide();
				}
			};

			/**
			 * Inline edit value
			 * 
			 * @since    1.0
			 * 
			 * @param    string    Detail type: status, media, rating
			 * @param    object    Caller link DOM Element
			 */
			wpmoly.editor.details.inline_edit = function( type, link ) {

				if ( 'status' != type && 'media' != type && 'rating' != type )
					return false;

				var $parent = $( link ).parents( 'tr' ),
				    post_id = $parent.prop( 'id' ).replace( 'post-','' ),
				      _link = $parent.find( '.column-wpmoly-' + type + ' > a' ),
				      _span = $parent.find( '.movie_' + type + '_title' ),
				     value = $( link ).attr( 'data-' + type + '' ),
				     title = $( link ).attr( 'data-' + type + '-title' ),
				     nonce = wpmoly.get_nonce( type + '-inline-edit' );

				if ( 'rating' == type ) {
					wpmoly_rating.update( $( '#wpmoly-movie-rating-' + post_id ), value );
				} else {
					_span.text( title );
					wpmoly_edit_details.inline_editor( type, _link );
				}

				wpmoly._post({
					data: {
						action: 'wpmoly_set_detail',
						nonce: nonce,
						type: type,
						data: value,
						post_id: post_id
					},
					error: function( response ) {
						wpmoly_state.clear();
						$.each( response.responseJSON.errors, function() {
							wpmoly_state.set( this, 'error' );
						});
					},
					success: function( response ) {

						_span.after( '<div id="wpmoly_temp_status"><em>' + wpmoly_ajax.lang.done + '</em></div>' );
						timer = window.setTimeout( function() {
							$( '#wpmoly_temp_status' ).fadeOut( 1000, function() { $(this ).remove() });
						}, 1000 );
					},
					complete: function( r ) {
						wpmoly.update_nonce( type + '-inline-edit', r.responseJSON.nonce );
					}
				});
			};

			/**
			 * Edit Movie Rating
			 * 
			 * @since    1.0
			 */
			wpmoly.editor.details.rating = wpmoly_rating = {};

				/**
				 * Update the rating
				 * 
				 * @since    2.0
				 */
				wpmoly.editor.details.rating.update = function( $container, rating ) {

					if ( ! $container.hasClass( 'wpmoly-movie-editable-rating' ) && ! $container.hasClass( 'wpmoly-movie-modal-rating' ) )
						return false;

					var $stars = $container.find( '.wpmolicon' ),
					   is_half = ( Math.floor( rating ) < rating && rating < Math.ceil( rating ) ),
					    filled = Math.floor( rating ),
					      half = is_half ? filled + 1 : -1;

					$stars.removeClass().addClass( 'wpmolicon icon-star-empty' );
					$.each( $stars, function( i ) {
						if ( i < filled )
							$( this ).removeClass( 'icon-star-empty' ).addClass( 'icon-star-filled' );
						else if ( 0 <= half && i < half )
							$( this ).removeClass( 'icon-star-empty' ).addClass( 'icon-star-half' );
					} );
				};

				/**
				 * Show the stars
				 * 
				 * @since    1.0
				 */
				wpmoly.editor.details.rating.change_in = function( e, post_id ) {

					var $container = $( '#wpmoly-movie-rating-' + post_id );

					if ( ! $container.hasClass( 'wpmoly-movie-editable-rating' ) )
						return false;

					var current_class = 'wpmolicon icon-star-filled',
					           median = e.target.offsetLeft + ( e.target.offsetWidth / 2 ),
					         $current = $( e.target, $container ),
					            $prev = $current.prevAll(),
					            $next = $current.nextAll();

					if ( ! e.layerX )
						current_class = 'wpmolicon icon-star-empty';
					else if ( e.layerX <= median )
						current_class = 'wpmolicon icon-star-half';

					$current.removeClass().addClass( current_class );
					   $prev.removeClass().addClass( 'wpmolicon icon-star-filled' );
					   $next.removeClass().addClass( 'wpmolicon icon-star-empty' );

					var rating = $( '.icon-star-filled', $container ).length + ( $( '.icon-star-half', $container ).length / 2 );
					$container.attr( 'data-rated', rating );
				};

				/**
				 * Revert the stars to default/previous
				 * 
				 * @since    1.0
				 */
				wpmoly.editor.details.rating.change_out = function( post_id ) {

					var $container = $( '#wpmoly-movie-rating-' + post_id ),
					        rating = $container.attr( 'data-rating' );

					if ( ! $container.hasClass( 'wpmoly-movie-editable-rating' ) )
						return false;

					wpmoly_rating.update( $container, rating );
				};

				/**
				 * Update the rating and fix the stars
				 * 
				 * @since    1.0
				 */
				wpmoly.editor.details.rating.rate = function( post_id ) {

					var $container = $( '#wpmoly-movie-rating-' + post_id ),
					        rating = $container.attr( 'data-rated' );

					console.log( $container );
					if ( ! $container.hasClass( 'wpmoly-movie-editable-rating' ) )
						return false;

					$container.attr( 'data-rating', rating );
					wpmoly_edit_details.inline_edit( 'rating', $container );

				};

			wpmoly.editor.details.rating.init = function() {
				$( '.wpmoly-movie-editable-rating' ).removeClass( 'wpmoly-movie-editable-rating' );
			};

		wpmoly.editor.details.rating.init();
