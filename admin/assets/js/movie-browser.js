wpmoly = window.wpmoly || {};

wpmoly.browser = wpmoly.browser || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new MovieBrowser Browser instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} browser MovieBrowser browser DOM Element.
	 *
	 * @return {object} Browser instance.
	 */
	var Browser = function( browser ) {

		var posts = new wp.api.collections.Movies;

		var controller = new MovieBrowser.controller.Browser( [], {
			posts : posts,
		});

		var view = new MovieBrowser.view.Browser({
			el         : browser,
			controller : controller,
		});

		view.$el.addClass( 'post-browser movie-browser' );

		// Hide loading animation.
		posts.once( 'sync error', function() {
			wpmoly.$( '.wpmoly-container' ).removeClass( 'loading' );
		} );

		// Load movies.
		posts.fetch({
			data : {
				_fields  : 'title,id,type,meta,poster,edit_link',
				context  : 'edit',
				per_page : 20,
			},
		});

		/**
		 * MovieBrowser browser instance.
		 *
		 * Provide a set of useful functions to interact with the movies
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var browser = {

			posts : posts,

			controller : controller,

			view : view,

		};

		return browser;
	};

	/**
	 * Create a new MovieBrowser Modal instance.
	 *
	 * @since 1.0.0
	 *
	 * @return {object} Modal instance.
	 */
	var Modal = function() {

		var post = new wp.api.models.Movies,
		    node = new wpmoly.api.models.Movie;

		var controller = new MovieBrowser.controller.MovieModal( [], {
			node : node,
			post : post,
		} );

		var view = new MovieBrowser.view.MovieModal({
			node       : node,
			post       : post,
			controller : controller,
		});

		$( 'body' ).append( view.render().el );

		/**
		 * MovieBrowser modal instance.
		 *
		 * Provide a set of useful functions to interact with the movie
		 * modal without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var modal = {

			node : node,

			post : post,

			controller : controller,

			view : view,

			/**
			 * Load a specific movie.
			 *
			 * @since 1.0.0
			 *
			 * @param int id Post ID.
			 *
			 * @return Returns itself to allow chaining.
			 */
			load : function( id ) {

				controller.set( { id : parseInt( id ) } );

				return modal;
			},

			/**
			 * Open Modal.
			 *
			 * @since 1.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			open : function() {

				controller.open();

				return modal;
			},

			/**
			 * Close Modal.
			 *
			 * @since 1.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			close : function() {

				controller.close();

				return modal;
			},

		};

		return modal;

	};

	var PostBrowser = wpmoly.browser.posts;

	var MovieBrowser = wpmoly.browser.movies = _.extend( PostBrowser, {

		/**
		 * Extend PostBrowser controllers.
		 *
		 * @since 1.0.0
		 */
		controller : _.extend( PostBrowser.controller, {

			/**
			 * GridBrowser 'Discover' Block Controller.
			 *
			 * @since 1.0.0
			 */
			BrowserBlock : PostBrowser.controller.BrowserBlock.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					var nodes = new wpmoly.api.collections.Movies;

					this.counts = nodes.counts;

					this.bindEvents();
				},
			}),

			MovieModal : Backbone.Model.extend({

				defaults : {
					id     : '',
					mode   : 'preview',
					status : 'close',
					index  : 0,
				},

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					var options = options || {};

					this.node  = options.node;
					this.post  = options.post;
					this.posts = MovieBrowser.browser.posts;

					this.on( 'change:id',     this.update, this );
					this.on( 'change:mode',   this.update, this );
					this.on( 'change:status', this.refresh, this );

					this.listenTo( this.node, 'error', this.error );
					this.listenTo( this.post, 'error', this.error );
				},

				/**
				 * Notify errors.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} collection Post collection.
				 * @param {object} xhr        XHR response.
				 * @param {object} options    Options.
				 *
				 * @return Returns itself to allow chaining.
				 */
				error : function( collection, xhr, options ) {

					wpmoly.error( xhr, { destroy : false } );

					return this;
				},

				/**
				 * Is there a previous page?
				 *
				 * @since 1.0.0
				 *
				 * @return {boolean}
				 */
				hasLess : function() {

					var page = this.posts.state.currentPage;

					return MovieBrowser.browser.controller.isBrowsable( page - 1 );
				},

				/**
				 * Is there a next page?
				 *
				 * @since 1.0.0
				 *
				 * @return {boolean}
				 */
				hasMore : function() {

					var page = this.posts.state.currentPage;

					return MovieBrowser.browser.controller.isBrowsable( page + 1 );
				},

				/**
				 * Is there a previous post?
				 *
				 * @since 1.0.0
				 *
				 * @return {boolean}
				 */
				hasPrevious : function() {

					return this.get( 'index' ) || ( ! this.get( 'index' ) && this.hasLess() );
				},

				/**
				 * Is there a next post?
				 *
				 * @since 1.0.0
				 *
				 * @return {boolean}
				 */
				hasNext : function() {

					return this.get( 'index' ) < this.posts.length - 1 || this.hasMore();
				},

				/**
				 * Refresh corresponding Model in the grid when
				 * closing.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				refresh : function() {

					if ( 'close' !== this.get( 'status' ) ) {
						return this;
					}

					/*var post = this.posts.get( this.post.id );
					if ( ! _.isUndefined( post ) ) {
						post.set( this.node.attributes );
					}*/

					return this;
				},

				/**
				 * Update modal content.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				update : function() {

					if ( 'edit' === this.get( 'mode' ) ) {
						this.updateEditor();
					} else if ( 'preview' === this.get( 'mode' ) ) {
						this.updatePreview();
					}

					return this;
				},

				/**
				 * Update collection index.
				 *
				 * @since 3.0.0
				 *
				 * @return {int}
				 */
				updateIndex : function() {

					var post = this.posts.get( this.get( 'id' ) ),
					   index = this.posts.indexOf( post );

					this.set( { index : index } );

					return index;
				},

				/**
				 * Update current node.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loadPost : function() {

					var post = this.posts.get( this.get( 'id' ) );

					this.post.set( post.toJSON() );

					return this;
				},

				/**
				 * Update current node.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loadNode : function() {

					var node = this.node;

					// Clean attributes.
					node.clear( { silent : true } );

					// Set ID.
					node.set( { id : this.get( 'id' ) }, { silent : true } );

					// Load node.
					node.fetch({
						data : {
							id      : this.get( 'id' ),
							context : 'embed',
						},
						beforeSend : function( xhr, options ) {
							node.trigger( 'fetch', node, xhr, options );
						},
						success : function( node, response, options ) {
							node.trigger( 'fetched', node, response, options  );
						},
						error : function( node, response, options ) {
							node.trigger( 'notfetched', node, response, options  );
						},
					});

					return this;
				},

				/**
				 * Update preview mode.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				updatePreview : function() {

					this.updateIndex();
					this.loadNode();

					return this;
				},

				/**
				 * Update editor mode.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				updateEditor : function() {

					this.updateIndex();
					this.loadPost();
					this.loadNode();

					return this;
				},

				/**
				 * Save Model changed.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Model attributes to save.
				 * @param {object} options    Saving options.
				 *
				 * @return xhr
				 */
				save : function( attributes, options ) {

					var atts = attributes,
					    post = this.post,
						 posts = this.posts,
				   options = _.extend( options || {}, {
						patch : true,
						wait  : true,
						success : function( post, response, options ) {
							post.trigger( 'saved', post, response, options );
							posts.get( post.get( 'id' ) ).set( post.toJSON() );
						},
						error : function( node, response, options ) {
							post.trigger( 'notsaved', post, response, options );
						},
					 } );

					var xhr = post.save( atts || post.changed, options );

					post.trigger( 'save', post, xhr, options );

					return xhr;
				},

				/**
				 * Show previous movie in the collection.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				previous : function() {

					var index = this.get( 'index' );
					if ( ! index ) {
						var self = this;
						if ( this.hasLess() ) {
							MovieBrowser.browser.controller.previousPage().done( function() {
								self.set( { id : self.posts.last().id } );
							} );
						}else {
							return this;
						}
					} else {
						var post = this.posts.at( index - 1 );
						this.set( { id : post.id } );
					}

					return this;
				},

				/**
				 * Show next movie in the collection.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				next : function() {

					var index = this.get( 'index' );
					if ( index === this.posts.length - 1 ) {
						var self = this;
						if ( this.hasMore() ) {
							MovieBrowser.browser.controller.nextPage().done( function() {
								self.set( { id : self.posts.first().id } );
							} );
						} else {
							return this;
						}
					} else {
						var post = this.posts.at( index + 1 );
						this.set( { id : post.id } );
					}

					return this;
				},

				/**
				 * Switch to 'preview' mode.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				preview : function() {

					this.set( { mode : 'preview' } );

					return this;
				},

				/**
				 * Switch to 'edit' mode.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				edit : function() {

					this.set( { mode : 'edit' } );

					return this;
				},

				/**
				 * Open Modal.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				open : function() {

					this.set( { status : 'open' } );

					return this;
				},

				/**
				 * Close Modal.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				close : function() {

					this.set( { status : 'close' } );

					return this;
				},

			}),

			Browser : PostBrowser.controller.Browser.extend({

				/**
				 * Open movie modal.
				 *
				 * @since 1.0.0
				 *
				 * @param {int} id Post ID.
				 *
				 * @return Returns itself to allow chaining.
				 */
				openModal : function( id ) {

					MovieBrowser.modal.load( id );

					MovieBrowser.modal.open();
				},

				/**
				 * Convert a specific post to draft.
				 *
				 * @since 1.0.0
				 *
				 * @param {int} id Post ID.
				 *
				 * @return Returns itself to allow chaining.
				 */
				draftPost : function( id ) {

					if ( ! this.posts.has( id ) ) {
						return this;
					}

					var self = this,
					   model = new wp.api.models.Movies( { id : id } );

					model.fetch().done( function( response, status ) {
						model.save( { status : 'draft' } ).done( _.bind( self.filter, self ) );
					} );

					return this;
				},

				/**
				 * Restore a specific post.
				 *
				 * Trashed posts are permanently deleted whereas drafts are set
				 * to 'publish'.
				 *
				 * @since 1.0.0
				 *
				 * @param {int} id Post ID.
				 *
				 * @return Returns itself to allow chaining.
				 */
				restorePost : function( id ) {

					if ( ! this.posts.has( id ) ) {
						return this;
					}

					var self = this,
					   model = new wp.api.models.Movies( { id : id } );

					// Fetch current post status to convert trashed posts to drafts.
					model.fetch().done( function( response, status ) {
						if ( 'success' === status ) {
							var status = ( 'trash' === model.get( 'status' ) ) ? 'draft' : 'publish';
							model.save( { status : status }, {
								patch : true,
								wait  : true,
							} ).done( _.bind( self.filter, self ) );
						}
					} );

					return this;
				},

				/**
				 * Move a specific post to the trash.
				 *
				 * @since 1.0.0
				 *
				 * @param {int} id Post ID.
				 *
				 * @return Returns itself to allow chaining.
				 */
				trashPost : function( id ) {

					if ( ! this.posts.has( id ) ) {
						return this;
					}

					var self = this,
					   model = new wp.api.models.Movies( { id : id } );

					model.requireForceForDelete = false;
					model.destroy( { wait : true } ).done( _.bind( self.filter, self ) );

					return this;
				},

				/**
				 * Permanently delete a specific post.
				 *
				 * @since 1.0.0
				 *
				 * @param {int} id Post ID.
				 *
				 * @return Returns itself to allow chaining.
				 */
				deletePost : function( id ) {

					if ( ! this.posts.has( id ) ) {
						return this;
					}

					var self = this,
					   model = new wp.api.models.Movies( { id : id } );

					model.destroy( { wait : true } ).done( _.bind( self.filter, self ) );

					return this;
				},

			}),

			/**
			 * MovieBrowser 'Add New' Block View.
			 *
			 * @since 1.0.0
			 */
			AddNewBlock : PostBrowser.controller.AddNewBlock.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.model      = wp.api.models.Movies;
					this.collection = wp.api.collections.Movies;

					if ( this.bindEvents ) {
						this.bindEvents();
					}
				},

			}),

			/**
			 * MovieBrowser 'Drafts' Block View.
			 *
			 * @since 1.0.0
			 */
			DraftsBlock: PostBrowser.controller.DraftsBlock.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.collection = wp.api.collections.Movies;

					if ( this.bindEvents ) {
						this.bindEvents();
					}
				},
			}),

			/**
			 * MovieBrowser 'Trash' Block View.
			 *
			 * @since 1.0.0
			 */
			TrashBlock : PostBrowser.controller.TrashBlock.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.collection = wp.api.collections.Movies;

					if ( this.bindEvents ) {
						this.bindEvents();
					}
				},
			}),
		} ),

		/**
		 * Extend PostBrowser views.
		 *
		 * @since 1.0.0
		 */
		view : _.extend( PostBrowser.view, {

			ModalEditorImages : wpmoly.Backbone.View.extend({

				className : 'movie-editor-images-inner',

				template : wp.template( 'wpmoly-movie-modal-editor-images' ),

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

						_.bindAll( this, 'adjust' );

					var options = options || {};

					this.on( 'rendered', this.adjust, this );

					this.node       = options.node;
					this.controller = options.controller;

					this.listenTo( this.node, 'change:posters',   this.render );
					this.listenTo( this.node, 'change:backdrops', this.render );

					$( window ).off( 'resize.movie-modal-editor' ).on( 'resize.movie-modal-editor', _.debounce( this.adjust, 50 ) );
				},

				/**
				 * Adjust images sizes.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				adjust : function() {

					this.adjustPosters();
					this.adjustBackdrops();

					return this;
				},

				/**
				 * Adjust posters sizes.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				adjustPosters : function() {

					var innerWidth = this.$( '.movie-posters' ).innerWidth() - 15,
					      $posters = this.$( '.movie-poster' );

					_.each( $posters, function( poster ) {

						var $poster = this.$( poster );

						if ( $poster.hasClass( 'first-poster' ) ) {
							width  = Math.floor( innerWidth * 0.57252 );
							height = Math.floor( width * 1.5 );
						} else if ( $poster.hasClass( 'second-poster' ) ) {
							width  = Math.floor( innerWidth * 0.42748 );
							height = width;
						} else {
							width  = Math.floor( innerWidth * 0.21374 );
							height = width;
						}

						if ( $poster.hasClass( 'more-poster' ) ) {
							$poster.find( 'a' ).css( { lineHeight : ( height - 2 ) + 'px' } );
						}

						$poster.css({
							height : height,
							width  : width,
						});

					}, this );

					if ( 4 <= $posters.length ) {
						var second = this.$( '.movie-poster.second-poster' ).height(),
						     third = this.$( '.movie-poster.third-poster' ).height();
						height = second + ( third * 2 );
					} else {
						height = this.$( '.movie-poster.first-poster' ).height();
					}

					this.$( '.movie-posters' ).height( height );

					return this;
				},

				/**
				 * Adjust backdrops sizes.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				adjustBackdrops : function() {

					var innerWidth = this.$( '.movie-backdrops' ).innerWidth() - 15,
					    $backdrops = this.$( '.movie-backdrop' );

					_.each( $backdrops, function( backdrop ) {

						var $backdrop = this.$( backdrop );

						if ( $backdrop.hasClass( 'first-backdrop' ) || $backdrop.hasClass( 'third-backdrop' ) ) {
							width  = Math.floor( innerWidth * 0.44 );
							height = Math.floor( width / 1.76 );
						} else if ( $backdrop.hasClass( 'second-backdrop' ) ) {
							width  = Math.floor( innerWidth * 0.56 );
							height = Math.floor( width / 1.76 );
						} else {
							width  = Math.floor( innerWidth * 0.186 );
							height = width;
						}

						if ( $backdrop.hasClass( 'more-backdrop' ) ) {
							$backdrop.find( 'a' ).css( { lineHeight : ( height - 2 ) + 'px' } );
						}

						$backdrop.css({
							height : height,
							width  : width,
						});

					}, this );

					if ( 3 <= $backdrops.length ) {
						var second = this.$( '.movie-backdrop.second-backdrop' ).height(),
						     third = this.$( '.movie-backdrop.third-backdrop' ).height();
						height = second + ( third * 2 );
						this.$( '.movie-backdrop.third-backdrop' ).css( { marginTop : 0 - ( second - third ) } );
					} else {
						height = this.$( '.movie-backdrop.first-backdrop' ).height();
					}

					this.$( '.movie-backdrops' ).height( height );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 1.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var node = this.node.toJSON(),
					 options = {
						backdrops : node.backdrops,
						posters   : node.posters,
					};

					return options;
				},

			}),

			ModalEditor : wpmoly.Backbone.View.extend({

				className : 'movie-modal-editor',

				template : wp.template( 'wpmoly-movie-modal-editor' ),

				events : function() {
					return _.extend( {}, _.result( wpmoly.Backbone.View.prototype, 'events' ), {
						'change [data-field]' : 'change',
					} );
				},

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.on( 'rendered', this.selectize, this );

					this.post       = options.post;
					this.controller = options.controller;

					this.listenTo( this.controller, 'change:id',   this.render );
					this.listenTo( this.controller, 'change:mode', this.render );

					this.setRegions();
				},

				/**
				 * Set subviews.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				setRegions : function() {

					var options = {
						node       : this.controller.node,
						controller : this.controller,
					};

					if ( ! this.images ) {
						this.images = new MovieBrowser.view.ModalEditorImages( options );
					}

					this.views.add( '.movie-editor-images', this.images, { silent : true } );

					return this;
				},

				/**
				 * Update movie meta/details.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} event JS 'change' event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				change : function( event ) {

					var $target = this.$( event.currentTarget ),
					      field = $target.attr( 'data-field' ),
					      value = $target.val();

					if ( _.isArray( value ) ) {
						value = value.join( ',' );
					}

					( attrs = {} )[ wpmolyApiSettings.movie_prefix + field ] = value;

					this.controller.save( { meta : attrs } );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 1.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var meta = this.post.getMetas(),
					 options = {
						has_previous : this.controller.hasPrevious(),
						has_next     : this.controller.hasNext(),
					};

					_.each( meta, function( value, key ) {
						var key = key.replace( wpmolyApiSettings.movie_prefix, '' );
						options[ key ] = value;
					} );

					return options;
				},

			}),

			ModalPreview : wpmoly.Backbone.View.extend({

				className : 'movie-modal-preview',

				template : wp.template( 'wpmoly-movie-modal-preview' ),

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.node      = options.node;
					this.controller = options.controller;

					this.listenTo( this.node, 'change', this.render );
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 1.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var node = this.node.toJSON(),
					  options = _.extend( node || {}, {
						poster       : ! _.isUndefined( node.poster )   ? node.poster.sizes.original.url   : '',
						backdrop     : ! _.isUndefined( node.backdrop ) ? node.backdrop.sizes.original.url : '',
						has_previous : this.controller.hasPrevious(),
						has_next     : this.controller.hasNext(),
					} );

					return options;
				},

				/**
				 * Render the View.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				render : function() {

					if ( 'preview' !== this.controller.get( 'mode' ) ) {
						return this;
					}

					wpmoly.Backbone.View.prototype.render.apply( this, arguments );

					return this;
				},

			}),

			MovieModal : wpmoly.Backbone.View.extend({

				className : 'wpmoly movie-modal closed',

				template : wp.template( 'wpmoly-movie-modal' ),

				events : function() {
					return _.extend( {}, _.result( wpmoly.Backbone.View.prototype, 'events' ), {
						'click .movie-modal-backdrop' : 'close',
						'click [data-action="browse-previous"]' : 'previousMovie',
						'click [data-action="browse-next"]'     : 'nextMovie',
						'click [data-action="close-modal"]'     : 'closeModal',
						'click [data-action="edit-movie"]'      : 'editMode',
						'click [data-action="preview-movie"]'   : 'previewMode',
					} );
				},

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.node      = options.node;
					this.controller = options.controller;

					this.on( 'rendered', this.rendered, this );

					this.listenTo( this.controller, 'change:mode',   this.changeMode );
					this.listenTo( this.controller, 'change:status', this.toggleStatus );

					this.listenTo( this.post, 'save',       this.saving );
					this.listenTo( this.post, 'saved',      this.saved );
					this.listenTo( this.post, 'notsaved',   this.saved );
					this.listenTo( this.node, 'fetch',      this.loading );
					this.listenTo( this.node, 'fetched',    this.loaded );
					this.listenTo( this.node, 'notfetched', this.loaded );

					this.setRegions();

					$( 'body' ).on( 'keydown.movie-modal', _.bind( this.keydown, this ) );
				},

				/**
				 * Set subviews.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				setRegions : function() {

					var options = {
						node       : this.controller.node,
						post       : this.controller.post,
						controller : this.controller,
					};

					if ( ! this.preview ) {
						this.preview = new MovieBrowser.view.ModalPreview( options );
					}

					if ( ! this.editor ) {
						this.editor = new MovieBrowser.view.ModalEditor( options );
					}

					this.views.add( '.movie-modal-content', this.preview, { silent : true } );
					this.views.add( '.movie-modal-content', this.editor, { silent : true } );

					return this;
				},

				/**
				 * Show/Hide Modal.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				toggleStatus : function() {

					var status = this.controller.get( 'status' );

					this.$el.toggleClass( 'closed', 'open' !== status );
					this.$el.toggleClass( 'opened', 'open' === status );

					$( 'body' ).toggleClass( 'modal-open', 'open' === status );

					return this;
				},

				/**
				 * Switch between 'edit' and 'preview' modes.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				changeMode : function() {

					this.$el.removeClass( 'preview-mode edit-mode' );
					this.$el.addClass( this.controller.get( 'mode' ) + '-mode' );

					return this;
				},

				/**
				 * Show previous movie in the collection.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				previousMovie : function() {

					this.controller.previous();

					return this;
				},

				/**
				 * Show next movie in the collection.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				nextMovie : function() {

					this.controller.next();

					return this;
				},

				/**
				 * Close Modal.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				closeModal : function() {

					this.controller.close();

					return this;
				},

				/**
				 * Switch to 'edit' mode.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				editMode : function() {

					this.controller.edit();

					return this;
				},

				/**
				 * Switch to 'preview' mode.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				previewMode : function() {

					this.controller.preview();

					return this;
				},

				/**
				 * A key has been pressed.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} JS 'keydown' event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				keydown : function( event ) {

					if ( _.contains( [ 'text', 'textarea' ], event.target.type ) ) {
						return this;
					}

					// Previous item when left or 'p' key is pressed.
					if ( 37 === event.which || 80 === event.which ) {
						this.previousMovie();
						event.stopImmediatePropagation();
					}

					// Next item when right or 'n' key is pressed.
					if ( 39 === event.which || 78 === event.which ) {
						this.nextMovie();
						event.stopImmediatePropagation();
					}

					// Edit mode when 'e' key is pressed.
					if ( 69 === event.which ) {
						this.editMode();
						event.stopImmediatePropagation();
					}

					// Preview mode when 'v' key is pressed.
					if ( 86 === event.which ) {
						this.previewMode();
						event.stopImmediatePropagation();
					}

					return this;
				},

				/**
				 * Show saving animation.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				saving : function() {

					this.$el.addClass( 'saving' );

					return this;
				},

				/**
				 * Hide saving animation.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				saved : function() {

					this.$el.removeClass( 'saving' );

					return this;
				},

				/**
				 * Show loading animation.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loading : function() {

					this.$el.addClass( 'loading' );

					return this;
				},

				/**
				 * Hide loading animation.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loaded : function() {

					this.$el.removeClass( 'loading' );

					return this;
				},

				/**
				 * Render the View.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				rendered : function() {

					this.$el.focus();
					this.changeMode();

					return this;
				},

			}),

			/**
			 * MovieBrowser Pagination Menu View.
			 *
			 * @since 1.0.0
			 */
			BrowserPagination : PostBrowser.view.BrowserPagination.extend({

				className : 'post-browser-menu post-browser-pagination movie-browser-menu movie-browser-pagination',

			}),

			/**
			 * MovieBrowser Browser Item View.
			 *
			 * @since 1.0.0
			 */
			Movie : PostBrowser.view.BrowserItem.extend({

				className : 'post movie',

				template : wp.template( 'wpmoly-movie-browser-item' ),

				events : function() {
					return _.extend( {}, _.result( PostBrowser.view.BrowserItem.prototype, 'events' ), {
						'contextmenu .post-thumbnail'               : 'openContextMenu',
						'click [data-action="preview-movie"]'       : 'previewMovie',
						'click [data-action="edit-status"]'         : 'editStatus',
						'click [data-action="edit-rating"]'         : 'editRating',
						'click [data-action="update-status"]'       : 'updateStatus',
						'click [data-action="update-rating"]'       : 'updateRating',
					} );
				},

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;
					this.parent     = options.parent;

					this.on( 'render',   this.dismiss, this );
					this.on( 'rendered', this.selectize, this );

					this.listenTo( this.model, 'change', this.render );
				},

				/**
				 * Open Context Menu.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} JS 'contextmenu' Event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				openContextMenu : function( event ) {

					// Get mouse position.
					var position = {
						x : event.pageX,
						y : event.pageY,
					};

					// Stop default and propagation.
					event.preventDefault();
					event.stopPropagation();

					// Set menu model.
					this.parent.menu.model.set( this.model.toJSON() );

					// Open menu.
					this.parent.menu.open();
					this.parent.menu.setPosition( position );

					return this;
				},

				/**
				 * .
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				previewMovie : function() {

					this.controller.openModal( this.model.get( 'id' ) );

					return this;
				},

				/**
				 * .
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				editStatus : function() {

					this.dismiss();
					this.$el.addClass( 'edit-status' );

					return this;
				},

				/**
				 * .
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				editRating : function() {

					this.dismiss();
					this.$el.addClass( 'edit-rating' );

					return this;
				},

				/**
				 * .
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				updateStatus : function() {

					var status = this.$( '[data-value="status"]' ).val();

					this.model.save( { status : status }, {
						patch : true,
						wait  : true,
					} );

					return this;
				},

				/**
				 * .
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				updateRating : function() {

					var rating = this.$( '[data-value="rating"]' ).val();

					this.model.save( { rating : rating }, {
						patch : true,
						wait  : true,
					} );

					return this;
				},

				/**
				 * .
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				dismiss : function() {

					this.$el.removeClass( 'edit-status edit-rating confirmation-asked' );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 1.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var options = _.extend( this.model.toJSON() || {}, {
						post_status : this.controller.get( 'status' ),
					} );

					if ( _.isArray( options.genres ) ) {
						options.genres = options.genres.join( ', ' );
					}

					if ( _.has( options.meta, wpmolyApiSettings.movie_prefix + 'release_date' ) ) {
						options.year = new Date( options.meta[ wpmolyApiSettings.movie_prefix + 'release_date' ] ).getFullYear()
					}

					if ( _.has( options.title, 'rendered' ) ) {
						options.title = options.title.rendered;
					} else if ( _.isEmpty( options.title ) ) {
						options.title = '<em>Untitled</em>';
					}

					return options;
				},

			}),

			/**
			 * MovieBrowser Context Menu View.
			 *
			 * @since 1.0.0
			 */
			BrowserContextMenu : wpmoly.Backbone.View.extend({

				className : 'wpmoly post-browser-context-menu movie-browser-context-menu',

				template : wp.template( 'wpmoly-movie-browser-context-menu' ),

				events : function() {
					return _.extend( {}, _.result( PostBrowser.view.BrowserItem.prototype, 'events' ), {
						'click'               : 'stopPropagation',
						'contextmenu'         : 'stopPropagation',
						'click [data-action]' : 'doStuff',
					} );
				},

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.model  = new Backbone.Model;
					this.parent = options.parent;
				},

				/**
				 * Stop event propagation to avoid impromptusly closing the menu.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} JS 'click' or 'contextmenu' Event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				stopPropagation : function( event ) {

					event.stopPropagation();

					return this;
				},

				/**
				 * Do stuff.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} JS 'click' Event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				doStuff : function( event ) {

					event.stopPropagation();

					console.log( event );

					return this;
				},

				/**
				 * Open Context Menu.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				open : function() {

					var self = this;

					// Add view to DOM.
					$( 'body' ).append( self.render().$el );

					// Bind closing events.
					$( 'body' ).on( 'click', _.bind( self.close, self ) );
					$( window ).on( 'resize', _.bind( self.close, self ) );

					return this;
				},

				/**
				 * Close Context Menu.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				close : function() {

					// Remove view.
					this.remove();

					// Clean Model.
					this.model.clear();

					// Unbind events.
					$( 'body' ).off( 'click', this.close );
					$( window ).off( 'resize', this.close );

					return this;
				},

				/**
				 * Position Context Menu from click event position.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} position Context Menu position.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setPosition : function( position ) {

					var position = position || {},
					   overflowX = ( window.innerWidth <= ( position.x + 400 ) ),
					   overflowY = ( window.innerHeight <= ( position.y + this.$el.height() ) );

					this.$el.css({
						left : position.x || 0,
						top  : ( overflowY ? ( position.y - this.$el.height() ) : position.y ) || 0,
					});

					this.$el.toggleClass( 'sub-menu-left', overflowX );
					this.$el.toggleClass( 'sub-menu-bottom', overflowY );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var options = this.model.toJSON();

					return options;
				},

			}),

			/**
			 * MovieBrowser Content View.
			 *
			 * @since 1.0.0
			 */
			BrowserContent : PostBrowser.view.BrowserContent.extend({

				className : 'post-browser-content movie-browser-content',

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					_.bindAll( this, 'adjust' );

					this.controller = options.controller;
					this.posts      = this.controller.posts;

					this.listenTo( this.posts, 'request', this.loading );
					this.listenTo( this.posts, 'update',  this.update );
					this.listenTo( this.posts, 'sync',    this.loaded );
					this.listenTo( this.posts, 'error',   this.loaded );
					this.listenTo( this.posts, 'destroy', this.loaded );

					this.listenTo( this.posts, 'change', _.debounce( this.adjust, 50 ) );
					this.listenTo( this.posts, 'sync',   _.debounce( this.adjust, 50 ) );

					$( window ).off( 'resize.movie-browser-content' ).on( 'resize.movie-browser-content', _.debounce( this.adjust, 50 ) );

					// Initialize Context Menu.
					this.menu = new PostBrowser.view.BrowserContextMenu( { parent : this } );
				},

				/**
				 * Update grid views.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				update : function() {

					this.views.remove();

					_.each( this.posts.models, this.addItem, this );

					_.delay( this.adjust, 50 );

					return this;
				},

				/**
				 * Add new grid item view.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} model Grid model.
				 *
				 * @return Returns itself to allow chaining.
				 */
				addItem : function( model ) {

					this.views.add( new MovieBrowser.view.Movie({
						controller : this.controller,
						parent     : this,
						model      : model,
					}) );

					return this;
				},

				/**
				 * Adjust items size.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				adjust : function() {

					var innerWidth = this.$el.innerWidth();
					if ( $( 'body' ).hasClass( 'modal-open' ) ) {
						innerWidth = innerWidth - 16;
					}

					var columns = Math.ceil( innerWidth / 180 ),
					      width = Math.floor( ( innerWidth / columns ) - 16 ),
					     height = Math.floor( width * 1.5 );

					this.$( '.movie' ).css({
						width : width,
					});

					this.$( '.movie-poster' ).css({
						width  : width,
						height : height,
					});

					return this;
				},

			}),
		} ),

		/**
		 * Create movie modal instance.
		 *
		 * @since 1.0.0
		 */
		loadModal : function() {

			MovieBrowser.modal = new Modal();
		},

		/**
		 * Create movies browser instance.
		 *
		 * @since 1.0.0
		 */
		loadBrowser : function() {

			var browser = document.querySelector( '#wpmoly-movie-browser' );
			if ( browser ) {
				MovieBrowser.browser = new Browser( browser );
			}
		},

		/**
		 * Run Forrest, run!
		 *
		 * @since 1.0.0
		 */
		run : function() {

			if ( ! wp.api ) {
				return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
			}

			wp.api.loadPromise.done( function() {
				MovieBrowser.loadBrowser();
				MovieBrowser.loadModal();
				PostBrowser.loadSidebar();
			} );

			return MovieBrowser;
		},

	} );

})( jQuery, _, Backbone );

wpmoly.runners['moviebrowser'] = wpmoly.browser.movies;
