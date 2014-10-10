
$ = $ || jQuery;

wpmoly = wpmoly || {};

	/**
	* WPMOLY Movie editor
	*/
	wpmoly.editor = {};

		/**
		 * WPMOLY Movie Editor: Movie Details
		 */
		wpmoly.editor.details = wpmoly_edit_details = {

			details_status: '#wpmoly-details-status',
			save_details: '#wpmoly_save'
		};

			/**
			 * Init Movie Details
			 */
			wpmoly.editor.details.init = function() {};

			/**
			 * Save Movie Details
			 * 
			 * @since    1.0
			 */
			wpmoly.editor.details.save = function() {

				wpmoly._post({
					data: {
						action: 'wpmoly_save_details',
						nonce: wpmoly.get_nonce( 'save-movie-details' ),
						post_id: $( '#post_ID' ).val(),
						wpmoly_details: {
							movie_media: $( '#movie-media' ).val(),
							movie_status: $( '#movie-status' ).val(),
							movie_rating: $( '#movie-rating' ).val()
						}
					},
					beforeSend: function() {
						$( wpmoly_edit_details.save_details ).prev( '.spinner' ).css( { display: 'inline-block' } );
					},
					error: function( response ) {
						$( wpmoly_edit_details.details_status ).show().html( '<p>' + wpmoly_ajax.lang.oops + '</p>' );
					},
					success: function( response ) {
						$( wpmoly_edit_details.details_status ).show().html( '<p>' + wpmoly_ajax.lang.done + '</p>' );
						timer = window.setTimeout( function() {
							$( wpmoly_edit_details.details_status ).fadeOut( 1500, function() { $( this ).empty() } );
						}, 2000 );
					},
					complete: function( r ) {
						$( wpmoly_edit_details.save_details ).prev( '.spinner' ).hide();
						wpmoly.update_nonce( 'save-movie-details', r.responseJSON.nonce );
					}
				});
			};

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
				      links = '.column-movie_' + type + '.inline_editing .wpmoly-inline-edit-toggle';

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
				      _link = $parent.find( '.column-movie_' + type + ' > a' ),
				      _span = $parent.find( '.movie_' + type + '_title' ),
				     value = $( link ).attr( 'data-' + type + '' ),
				     title = $( link ).attr( 'data-' + type + '-title' ),
				     nonce = wpmoly.get_nonce( type + '-inline-edit' );

				if ( 'rating' == type )
					_span.removeClass().addClass( 'movie-rating-title stars stars-' + value.replace( '.', '-' ) );
				else
					_span.text( title );

				wpmoly_edit_details.inline_editor( type, _link );

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
			 * Edit Movie Status
			 * 
			 * @since    1.0
			 */
			wpmoly.editor.details.status = wpmoly_status = {

				select: '#movie-status-select',
				hidden: '#hidden-movie-status',
				display: '#movie-status-display',

				edit: '#edit-movie-status',
				save: '#save-movie-status',
				cancel: '#cancel-movie-status',

				show: function() {},
				update: function() {},
				revert: function() {}
			};

				/**
				 * Show the editor
				 * 
				 * @since    1.0
				 */
				wpmoly.editor.details.status.show = function() {
					if ( $( wpmoly_status.select ).is(":hidden") ) {
						$( wpmoly_status.select ).slideDown( 'fast' );
						$( wpmoly_status.edit ).hide();
					}
				};

				/**
				 * Update status
				 * 
				 * @since    1.0
				 */
				wpmoly.editor.details.status.update = function() {
					var option = '#movie-status > option:selected';
					$( wpmoly_status.select ).slideUp( 'fast' );
					$( wpmoly_status.edit ).show();
					$( wpmoly_status.display ).text( $( option ).text() );
					$( wpmoly_status.hidden ).val( $( option ).prop( 'id' ) );
				};

				/**
				 * Hide the editor (cancel edit )
				 * 
				 * @since    1.0
				 */
				wpmoly.editor.details.status.revert = function() {
					var option = '#movie-status #'+$( wpmoly_status.hidden ).val();
					$( wpmoly_status.select ).slideUp( 'fast' );
					$( option ).prop( 'selected', true );
					$( wpmoly_status.display ).text( $( option ).text() );
					$( wpmoly_status.edit ).show();
				};

			/**
			 * Edit Movie Media
			 * 
			 * @since    1.0
			 */
			wpmoly.editor.details.media = wpmoly_media = {

				select: '#movie-media-select',
				hidden: '#hidden-movie-media',
				display: '#movie-media-display',

				edit: '#edit-movie-media',
				save: '#save-movie-media',
				cancel: '#cancel-movie-media',

				show: function() {},
				update: function() {},
				revert: function() {}
			};

				/**
				 * Show the editor
				 * 
				 * @since    1.0
				 */
				wpmoly.editor.details.media.show = function() {
					if ( $( wpmoly_media.select ).is( ':hidden' ) ) {
						$( wpmoly_media.select ).slideDown( 'fast' );
						$( wpmoly_media.edit ).hide();
					}
				};

				/**
				 * Update media
				 * 
				 * @since    1.0
				 */
				wpmoly.editor.details.media.update = function() {
					var option = '#movie-media > option:selected';
					$( wpmoly_media.select ).slideUp( 'fast' );
					$( wpmoly_media.edit ).show();
					$( wpmoly_media.display ).text( $( option ).text() );
					$( wpmoly_media.hidden ).val( $( option ).prop( 'id' ) );
				};

				/**
				 * Hide the editor (cancel edit )
				 */
				wpmoly.editor.details.media.revert = function() {
					var option = '#movie-media #'+$( wpmoly_media.hidden ).val();
					$( wpmoly_media.select ).slideUp( 'fast' );
					$( option ).prop( 'selected', true );
					$( wpmoly_media.display ).text( $( option ).text() );
					$( wpmoly_media.edit ).show();
				};

			/**
			 * Edit Movie Rating
			 * 
			 * @since    1.0
			 */
			wpmoly.editor.details.rating = wpmoly_rating = {

				/*select: '#movie-rating-select',
				hidden: '#hidden-movie-rating',
				display: '#movie-rating-display',

				stars: '#stars, #bulk_stars',
				edit: '#edit-movie-rating',
				save: '#save-movie-rating',
				cancel: '#cancel-movie-rating',

				init: function() {},
				show: function() {},
				update: function() {},
				change_in: function() {},
				change_out: function() {},
				revert: function() {}*/
			};
			

				/**
				 * Init Events
				 * 
				 * @since    1.0
				 */
				wpmoly.editor.details.rating.init = function() {

					console.log( '!' );

					/*$( wpmoly_rating.stars ).on( 'click', function( e ) {
						e.preventDefault();
						wpmoly_rating.rate();
					});

					$( wpmoly_rating.stars ).on( 'mousemove', function( e ) {
						wpmoly_rating.change_in( e );
					});

					$( wpmoly_rating.stars ).on( 'mouseleave', function( e ) {
						wpmoly_rating.change_out( e );
					});*/


				};

				/**
				 * Update the rating
				 * 
				 * @since    2.0
				 */
				wpmoly.editor.details.rating.update = function( $container, rating ) {

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

					var $container = $( '#wpmoly-movie-rating-' + post_id ),
					 current_class = 'wpmolicon icon-star-filled',
					        median = e.target.offsetLeft + ( e.target.offsetWidth / 2 ),
					      $current = $( e.target ),
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

					wpmoly_rating.update( $container, rating );

					/*var _rate = $( wpmoly_rating.stars ).attr( 'data-rating' );

					if ( function() {} == _rate )
						return false;

					_rate = _rate.replace( 'stars-','' );
					_rate = _rate.replace( '-','.' );

					$( '#movie-rating, #bulk-movie-rating' ).val(_rate );
					$( wpmoly_rating.stars ).attr( 'data-rating', _rate );
					$( wpmoly_rating.stars ).attr( 'data-rated', true );

					if ( $( wpmoly_rating.stars ).parent( 'a.wpmoly-inline-edit-rating-update' ).length )
						$( wpmoly_rating.stars ).parent( 'a.wpmoly-inline-edit-rating-update' ).attr( 'data-rating', _rate );*/
				};

	wpmoly.editor.details.init();
	wpmoly.editor.details.rating.init();
	