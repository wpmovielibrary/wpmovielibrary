
$ = $ || jQuery;

wpml = wpml || {};

var wpml_meta, wpml_details, wpml_media, wpml_status, wpml_rating;

	/**
	* WPML Movie editor
	*/
	wpml.editor = {

		init: function() {
			wpml.editor.meta.init();
			wpml.editor.details.init();
			wpml.editor.movies.init();

			$('input#wpml_save').on( 'click', function() {
				wpml.editor.details.save();
			});

		},
		details: {},
		meta: {},
		movies: {}
	};

		/**
		 * WPML Movie Editor: Movie Details
		 */
		wpml.editor.details = wpml_details = {

			init: function() {},
			save: function() {},
			status: {},
			media: {},
			rating: {}
		};

			/**
			 * Init Movie Details
			 */
			wpml.editor.details.init = function() {
				wpml.editor.details.status.init();
				wpml.editor.details.media.init();
				wpml.editor.details.rating.init();
			};

			/**
			 * Save Movie Details
			 */
			wpml.editor.details.save = function() {
				$('#wpml_save').prev('.spinner').css({display: 'inline-block'});
				wpml._post({
						action: 'wpml_save_details',
						wpml_check: wpml_ajax.utils.wpml_check,
						post_id: $('#post_ID').val(),
						wpml_details: {
							media: $('#movie-media').val(),
							status: $('#movie-status').val(),
							rating: $('#movie-rating').val()
						}
					},
					function() {
						$('#wpml_save').prev('.spinner').hide();
					}
				);
			};

			/**
			 * Edit Movie Status
			 */
			wpml.editor.details.status = wpml_status = {

				select: '#movie-status-select',
				hidden: '#hidden-movie-status',
				display: '#movie-status-display',

				edit: '#edit-movie-status',
				save: '#save-movie-status',
				cancel: '#cancel-movie-status',

				init: function() {},
				show: function() {},
				update: function() {},
				revert: function() {}
			};

				/**
				 * Init Events
				 */
				wpml.editor.details.status.init = function() {

					$(wpml_status.edit).on( 'click', function( e ) {
						e.preventDefault();
						wpml_status.show();
					});

					$(wpml_status.save).on( 'click', function( e ) {
						e.preventDefault();
						wpml_status.update();
					});

					$(wpml_status.cancel).on( 'click', function( e ) {
						e.preventDefault();
						wpml_status.revert();
					});
				};

				/**
				 * Show the editor
				 */
				wpml.editor.details.status.show = function() {
					if ( $(wpml_status.select).is(":hidden") ) {
						$(wpml_status.select).slideDown('fast');
						$(wpml_status.edit).hide();
					}
				};

				/**
				 * Update status
				 */
				wpml.editor.details.status.update = function() {
					var option = '#movie-status > option:selected';
					$(wpml_status.select).slideUp('fast');
					$(wpml_status.edit).show();
					$(wpml_status.display).text( $(option).text() );
					$(wpml_status.hidden).val( $(option).prop('id') );
				};

				/**
				 * Hide the editor (cancel edit)
				 */
				wpml.editor.details.status.revert = function() {
					var option = '#movie-status #'+$(wpml_status.hidden).val();
					$(wpml_status.select).slideUp('fast');
					$(option).prop('selected', true);
					$(wpml_status.display).text( $(option).text() );
					$(wpml_status.edit).show();
				};

			/**
			 * Edit Movie Media
			 */
			wpml.editor.details.media = wpml_media = {

				select: '#movie-media-select',
				hidden: '#hidden-movie-media',
				display: '#movie-media-display',

				edit: '#edit-movie-media',
				save: '#save-movie-media',
				cancel: '#cancel-movie-media',

				init: function() {},
				show: function() {},
				update: function() {},
				revert: function() {}
			};

				/**
				 * Init Events
				 */
				wpml.editor.details.media.init = function() {

					$(wpml_media.edit).on( 'click', function( e ) {
						e.preventDefault();
						wpml_media.show();
					});

					$(wpml_media.save).on( 'click', function( e ) {
						e.preventDefault();
						wpml_media.update();
					});

					$(wpml_media.cancel).on( 'click', function( e ) {
						e.preventDefault();
						wpml_media.revert();
					});
				};

				/**
				 * Show the editor
				 */
				wpml.editor.details.media.show = function() {
					if ( $(wpml_media.select).is(":hidden") ) {
						$(wpml_media.select).slideDown('fast');
						$(wpml_media.edit).hide();
					}
				};

				/**
				 * Update media
				 */
				wpml.editor.details.media.update = function() {
					var option = '#movie-media > option:selected';
					$(wpml_media.select).slideUp('fast');
					$(wpml_media.edit).show();
					$(wpml_media.display).text( $(option).text() );
					$(wpml_media.hidden).val( $(option).prop('id') );
				};

				/**
				 * Hide the editor (cancel edit)
				 */
				wpml.editor.details.media.revert = function() {
					var option = '#movie-media #'+$(wpml_media.hidden).val();
					$(wpml_media.select).slideUp('fast');
					$(option).prop('selected', true);
					$(wpml_media.display).text( $(option).text() );
					$(wpml_media.edit).show();
				};

			/**
			 * Edit Movie Rating
			 */
			wpml.editor.details.rating = wpml_rating = {

				stars: '#stars, #bulk_stars',

				select: '#movie-rating-select',
				hidden: '#hidden-movie-rating',
				display: '#movie-rating-display',

				edit: '#edit-movie-rating',
				save: '#save-movie-rating',
				cancel: '#cancel-movie-rating',

				init: function() {},
				show: function() {},
				update: function() {},
				change_in: function() {},
				change_out: function() {},
				revert: function() {}
			};
			

				/**
				 * Init Events
				 */
				wpml.editor.details.rating.init = function() {

					$(wpml_rating.edit).on( 'click', function( e ) {
						e.preventDefault();
						wpml_rating.show();
					});

					$(wpml_rating.cancel).on( 'click', function( e ) {
						e.preventDefault();
						wpml_rating.revert();
					});

					$(wpml_rating.save).on( 'click', function( e ) {
						e.preventDefault();
						wpml_rating.update();
					});

					$(wpml_rating.stars).on( 'click', function( e ) {
						e.preventDefault();
						wpml_rating.rate();
					});

					$(wpml_rating.stars).on( 'mousemove', function( e ) {
						wpml_rating.change_in( e );
					});

					$(wpml_rating.stars).on( 'mouseleave', function( e ) {
						wpml_rating.change_out( e );
					});


				};

				/**
				 * Show the editor
				 */
				wpml.editor.details.rating.show = function() {
					if ( $(wpml_rating.select).is(":hidden") ) {
						$(wpml_rating.display).hide();
						$(wpml_rating.select).slideDown('fast');
						$(wpml_rating.edit).hide();
					}
				};

				/**
				 * Update the rating
				 */
				wpml.editor.details.rating.update = function() {
					var n = $('#movie-rating').val();
					$(wpml_rating.select).slideUp('fast');
					$(wpml_rating.edit).show();
					$(wpml_rating.display).removeClass().addClass('stars-'+n.replace('.','-')).show();
					$('#movie-rating, #hidden-movie-rating').val(n);
				};

				/**
				 * Hide the editor (cancel edit)
				 */
				wpml.editor.details.rating.revert = function() {
					$(wpml_rating.select).slideUp('fast');
					$(wpml_rating.edit).show();
					$(wpml_rating.display).show();
				};

				/**
				 * Show the stars
				 */
				wpml.editor.details.rating.change_in = function( e ) {

					var classes = 'stars-0 stars-0-0 stars-0-5 stars-1-0 stars-1-5 stars-2-0 stars-2-5 stars-3-0 stars-3-5 stars-4-0 stars-4-5 stars-5-0';

					var parentOffset = $(wpml_rating.stars).offset(); 
					var relX = e.pageX - parentOffset.left;

					if ( relX <= 0 ) var _rate = '0';
					if ( relX > 0 && relX < 8 ) var _rate = '0.5';
					if ( relX >= 8 && relX < 16 ) var _rate = '1.0';
					if ( relX >= 16 && relX < 24 ) var _rate = '1.5';
					if ( relX >= 24 && relX < 32 ) var _rate = '2.0';
					if ( relX >= 32 && relX < 40 ) var _rate = '2.5';
					if ( relX >= 40 && relX < 48 ) var _rate = '3.0';
					if ( relX >= 48 && relX < 56 ) var _rate = '3.5';
					if ( relX >= 56 && relX < 64 ) var _rate = '4.0';
					if ( relX >= 64 && relX < 80 ) var _rate = '4.5';
					if ( relX >= 80 ) var _rate = '5.0';

					var _class = 'stars-' + _rate.replace('.','-');
					var _label = _class.replace('stars-','stars-label-');

					$(wpml_rating.stars).removeClass( classes ).addClass( _class );
					$('.stars-label').removeClass('show');
					$('#'+_label).addClass('show');
					$(wpml_rating.stars).attr('data-rating', _rate);
				};

				/**
				 * Revert the stars to default/previous
				 */
				wpml.editor.details.rating.change_out = function( e ) {

					var classes = 'stars-0 stars-0-0 stars-0-5 stars-1-0 stars-1-5 stars-2-0 stars-2-5 stars-3-0 stars-3-5 stars-4-0 stars-4-5 stars-5-0';

					if ( 'true' == $(wpml_rating.stars).attr('data-rated') )
						return false;

					var _class = '';

					if ( $('#hidden-movie-rating, #bulk-hidden-movie-rating').length ) {
						_class = $('#hidden-movie-rating, #bulk-hidden-movie-rating').val();
						_class = 'stars-' + _class.replace('.','-');
					}

					$(wpml_rating.stars).removeClass( classes ).addClass( _class );
					$('.stars-label').removeClass('show');
				};

				/**
				 * Update the rating and fix the stars
				 */
				wpml.editor.details.rating.rate = function() {

					var _rate = $(wpml_rating.stars).attr('data-rating');

					if ( function() {} == _rate )
						return false;

					_rate = _rate.replace('stars-','');
					_rate = _rate.replace('-','.');

					$('#movie-rating, #bulk-movie-rating').val(_rate);
					$(wpml_rating.stars).attr('data-rating', _rate);
					$(wpml_rating.stars).attr('data-rated', true);
				};

		/**
		 * WPML Movie Editor: Movie Meta
		 */
		wpml.editor.meta = wpml_meta = {
			init: function() {},
			search_movie: function() {},
			prefill_title: function() {}
		}

			/**
			 * Init Events
			 */
			wpml.editor.meta.init = function() {

				$('#tmdb_search').click(function(e) {
					e.preventDefault();
					wpml_meta.search_movie();
				});

				$('input#title').on( 'input', function() {
					wpml_meta.prefill_title( $(this).val() );
				});
			};

			/**
			 * Search a movie by its title/ID with possible lang choice
			 */
			wpml.editor.meta.search_movie = function() {

				var type = $('#tmdb_search_type > :selected').val(),
				    data = $('#tmdb_query').val(),
				    lang = $('#tmdb_search_lang').val();

				$('#tmdb_search').next('.spinner').css({display: 'inline-block'});
				$('#tmdb_data > *, .tmdb_select_movie').remove();
				$('.tmdb_movie_images').not('.tmdb_movie_imported_image').remove();

				if (  type == 'title' )
					wpml.status.set( wpml_ajax.lang.search_movie_title + ' "' +  data + '"', 'warning' );
				else if (  type == 'id' )
					wpml.status.set( wpml_ajax.lang.search_movie + ' #' +  data, 'success' );

				wpml._get({
						action: 'wpml_search_movie',
						wpml_check: wpml_ajax.utils.wpml_check,
						type: type,
						data: data,
						lang: lang
					},
					function( movies ) {
						if ( 'movie' == movies._result ) {
							wpml_edit_movies.populate( movies );
							//wpml.media.posters.set_featured( movies.poster_path/*, null, movies.title, movies._tmdb_id*/ );
						}
						else if ( 'movies' == movies._result ) {
							wpml_edit_movies.populate_select_list( movies );

							$('.tmdb_select_movie a').on( 'click', function( e ) {
								e.preventDefault();
								id = this.id.replace('tmdb_','');

								wpml_edit_movies.get( id );
							});
						}
						else if ( 'error' == movies._result || 'empty' == movies._result ) {
							$('#tmdb_data').html( movies.p ).show();
							$('#tmdb_status').empty();
						}
						$('#tmdb_search').next('.spinner').hide();
					}
				);
			};

			/**
			 * Prefill the Movie Meta Metabox search input with the
			 * page title
			 */
			wpml.editor.meta.prefill_title = function( title ) {
				if ( '' != title )
					$('input#tmdb_query').val( title );
			};

		wpml.editor.movies = wpml_edit_movies = {

			actors: 'td.column-taxonomy-actor',
			visible: '.visible-actors',
			hidden: '.hidden-actors',
			more: '.more-actors',
			empty: '#tmdb_empty',

			init: function() {},
			excerpt_actors: function() {},
			populate: function() {},
			populate_quick_edit: function() {},
			populate_select_list: function() {},
			empty_results: function() {}
		};

			/**
			 * Init Events and generate Actors Excerpt list
			 */
			wpml.editor.movies.init = function() {

				wpml_edit_movies.actor_excerpt();

				$(wpml_edit_movies.more).on( 'click', function( e ) {
					e.preventDefault();
					wpml_edit_movies.toggle_actors( this );
				});
			
				$(wpml_edit_movies.empty).on( 'click', function( e ) {
					e.preventDefault();
					wpml_edit_movies.empty_results();
				});

			};

			/**
			 * Hide most of the actors in the All Movies view. Since
			 * movies can contain a large number of actors we limit
			 * the list length to the first five names and show a link
			 * to toggle the hidden rest on the list.
			 */
			wpml.editor.movies.actor_excerpt = function() {

				$(wpml_edit_movies.actors).each( function() {

					var visible = []; var hidden = [];
					var links = $(this).find('a');
					var _visible = links.slice( 0, 5 );
					var _hidden = links.slice( 5 );

					_visible.each(function() { visible.push( this.outerHTML ); });
					_hidden.each(function() { hidden.push( this.outerHTML ); });

					$(this).html('<span class="visible-actors"></span><span class="hidden-actors"></span>, <a class="more-actors" href="#">' + wpml_ajax.lang.see_more + '</a>');
					$(this).find(wpml_edit_movies.visible).html( visible.join(', ') );
					$(this).find(wpml_edit_movies.hidden).html( hidden.join(', ') );
				});
			};

			wpml.editor.movies.toggle_actors = function( link ) {

				$(link).prev(wpml_edit_movies.hidden).toggle();
				if ( 'none' != $(link).prev(wpml_edit_movies.hidden).css('display') )
					$(link).text( wpml_ajax.lang.see_less );
				else
					$(link).text( wpml_ajax.lang.see_more );
			};

			wpml.editor.movies.get = function( id ) {

				wpml._get({
						action: 'wpml_search_movie',
						wpml_check: wpml_ajax.utils.wpml_check,
						type: 'id',
						data: id,
						lang: wpml_movies.lang
					},
					function( response ) {
						var tmdb_data = document.getElementById('tmdb_data');
						while ( tmdb_data.lastChild )
							tmdb_data.removeChild( tmdb_data.lastChild );
						tmdb_data.style.display = 'none';
						wpml_edit_movies.populate( response );
						wpml_media.posters.set_featured( response.poster_path );
					}
				);
			};

			wpml.editor.movies.populate = function(data) {

				$('#tmdb_data_tmdb_id').val(data._tmdb_id);

				$('.tmdb_data_field').each(function() {

					var field = this;
					var type = field.type;
					var _id = this.id.replace('tmdb_data_','');
					var value = '';

					field.value = '';

					var sub = wpml.switch_data( _id );

					if ( 'meta' == sub )
						var _data = data.meta;
					else if ( 'crew' == sub )
						var _data = data.crew;
					else
						var _data = data;

					if ( typeof _data[_id] == "object" ) {
						if ( Array.isArray( _data[_id] ) ) {
							_v = [];
							$.each(_data[_id], function() {
								_v.push( field.value + this );
							});
							value = _v.join(', ');
						}
					}
					else {
						_v = ( _data[_id] != null ? _data[_id] : '' );
						value = _v;
					}

					$(field).val(_v);

					$('.list-table, .button-empty').show();
				});

				if ( data.taxonomy.actors.length ) {
					$.each( data.taxonomy.actors, function(i) {
						$('#tagsdiv-actor .tagchecklist').append('<span><a id="actor-check-num-' + i + '" class="ntdelbutton">X</a>&nbsp;' + this + '</span>');
						tagBox.flushTags( $('#actor.tagsdiv'), $('<span>' + this + '</span>') );
					});
				}

				if ( data.taxonomy.genres.length ) {
					$.each( data.taxonomy.genres, function(i) {
						$('#tagsdiv-genre .tagchecklist').append('<span><a id="genre-check-num-' + i + '" class="ntdelbutton">X</a>&nbsp;' + this + '</span>');
						tagBox.flushTags( $('#genre.tagsdiv'), $('<span>' + this + '</span>') );
					});
				}

				if ( data.crew.director.length ) {
					$.each( data.crew.director, function(i) {
						$('#newcollection').prop('value', this);
						$('#collection-add-submit').click();
					});
				}

				$('#tmdb_query').focus();
				wpml.status.set(wpml_ajax.lang.done, 'success');
			};

			wpml.editor.movies.populate_quick_edit = function( movie_details, nonce ) {

				var $wp_inline_edit = inlineEditPost.edit;

				inlineEditPost.edit = function( id ) {

					$wp_inline_edit.apply( this, arguments );

					var $post_id = 0;

					if ( typeof( id ) == 'object' )
						$post_id = parseInt( this.getId( id ) );

					if ( $post_id > 0 ) {

						var $edit_row = $( '#edit-' + $post_id );

						var nonceInput = $('#wpml_movie_details_nonce');
						nonceInput.val( nonce );

						var movie_media = $edit_row.find('select.movie_media');
						var movie_status = $edit_row.find('select.movie_status');
						var movie_rating = $edit_row.find('#movie_rating');
						var hidden_movie_rating = $edit_row.find('#hidden_movie_rating');
						var stars = $edit_row.find('#stars');

						movie_media.children('option').each(function() {
							if ( $(this).val() == movie_details.movie_media )
								$(this).prop("selected", "selected");
							else
								$(this).prop("selected", "");
						});

						movie_status.children('option').each(function() {
							if ( $(this).val() == movie_details.movie_status )
								$(this).prop("selected", true);
							else
								$(this).prop("selected", false);
						});

						if ( '' != movie_details.movie_rating && ' ' != movie_details.movie_rating ) {
							movie_rating.val( movie_details.movie_rating );
							hidden_movie_rating.val( movie_details.movie_rating );
							stars.removeClass('stars_', 'stars_0_0', 'stars_0_5', 'stars_1_0', 'stars_1_5', 'stars_2_0', 'stars_2_5', 'stars_3_0', 'stars_3_5', 'stars_4_0', 'stars_4_5', 'stars_5_0');
							stars.addClass( 'stars_' + movie_details.movie_rating.replace('.','_') );
							stars.attr('data-rated', true);
							stars.attr('data-default-rating', movie_details.movie_rating);
							stars.attr('data-rating', movie_details.movie_rating);
						}

					}

				};
			};

			wpml.editor.movies.populate_select_list = function( data ) {

				$('#tmdb_data').append( data.p ).show();

				var html = '';

				$.each( data.movies, function() {
					html += '<div class="tmdb_select_movie">';
					html += '	<a id="tmdb_' + this.id + '" href="#">';
					html += '		<img src="' + this.poster + '" alt="' + this.title + '" />';
					html += '		<em>' + this.title + '</em>';
					html += '	</a>';
					html += '	<input type=\'hidden\' value=\'' + this.json + '\' />';
					html += '</div>';
				});

				$('#tmdb_data').append(html);

			};

			/**
			* Empty all Movie search result fields, reset all taxonomies 
			* and remove the featured image.
			*/
			wpml.editor.movies.empty_results = function() {

				$('.tmdb_data_field').val('');
				$('.tmdb_select_movie, .wpml-import-movie-select').remove();
				$('.categorydiv input[type=checkbox]').prop('checked', false);
				$('#tmdb_data, .tagchecklist').empty();
				$('#remove-post-thumbnail').trigger('click');

				wpml.status.clear();
			};

	wpml.editor.init();