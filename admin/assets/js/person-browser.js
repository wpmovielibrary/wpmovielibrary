wpmoly = window.wpmoly || {};

wpmoly.browser = wpmoly.browser || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new PersonBrowser Browser instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} browser PersonBrowser browser DOM Element.
	 *
	 * @return {object} Browser instance.
	 */
	var Browser = function( browser ) {

		var posts = new wp.api.collections.Persons;

		var controller = new PersonBrowser.controller.Browser( [], {
			posts : posts,
		});

		var view = new PersonBrowser.view.Browser({
			el         : browser,
			controller : controller,
		});

		view.$el.addClass( 'post-browser person-browser' );

		// Hide loading animation.
		posts.once( 'sync error', function() {
			wpmoly.$( '.wpmoly-container' ).removeClass( 'loading' );
		} );

		// Load persons.
		posts.fetch({
			data : {
				_fields  : 'title,id,type,meta,picture,edit_link',
				context  : 'edit',
				per_page : 20,
			},
		});

		/**
		 * PersonBrowser browser instance.
		 *
		 * Provide a set of useful functions to interact with the persons
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

	var PostBrowser = wpmoly.browser.posts;

	var PersonBrowser = wpmoly.browser.persons = _.extend( PostBrowser, {

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

					var nodes = new wpmoly.api.collections.Persons;

					this.counts = nodes.counts;

					this.bindEvents();
				},
			}),

			Browser : PostBrowser.controller.Browser.extend({

				/**
				 * Open person modal.
				 *
				 * @since 1.0.0
				 *
				 * @param {int} id Post ID.
				 *
				 * @return Returns itself to allow chaining.
				 */
				openModal : function( id ) {

					PersonBrowser.modal.load( id );

					PersonBrowser.modal.open();
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
					   model = new wp.api.models.Persons( { id : id } );

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
					   model = new wp.api.models.Persons( { id : id } );

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
					   model = new wp.api.models.Persons( { id : id } );

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
					   model = new wp.api.models.Persons( { id : id } );

					model.destroy( { wait : true } ).done( _.bind( self.filter, self ) );

					return this;
				},

			}),

			/**
			 * PersonBrowser 'Add New' Block View.
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

					this.model      = wp.api.models.Persons;
					this.collection = wp.api.collections.Persons;

					if ( this.bindEvents ) {
						this.bindEvents();
					}
				},

			}),

			/**
			 * PersonBrowser 'Drafts' Block View.
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

					this.collection = wp.api.collections.Persons;

					if ( this.bindEvents ) {
						this.bindEvents();
					}
				},
			}),

			/**
			 * PersonBrowser 'Trash' Block View.
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

					this.collection = wp.api.collections.Persons;

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

			/**
			 * PersonBrowser Browser Menu View.
			 *
			 * @since 1.0.0
			 */
			BrowserMenu : PostBrowser.view.BrowserMenu.extend({

				className : 'post-browser-menu person-browser-menu',

			}),

			/**
			 * PersonBrowser Pagination Menu View.
			 *
			 * @since 1.0.0
			 */
			BrowserPagination : PostBrowser.view.BrowserPagination.extend({

				className : 'post-browser-menu post-browser-pagination person-browser-menu person-browser-pagination',

			}),

			/**
			 * PersonBrowser Browser Item View.
			 *
			 * @since 1.0.0
			 */
			BrowserItem : PostBrowser.view.BrowserItem.extend({

				className : 'post person',

				template : wp.template( 'wpmoly-person-browser-item' ),

				events : {
					'click [data-action="dismiss"]'             : 'dismiss',
					'click [data-action="preview-person"]'      : 'previewPerson',
					'click [data-action="draft-post"]'          : 'draftPost',
					'click [data-action="restore-post"]'        : 'restorePost',
					'click [data-action="trash-post"]'          : 'trashPost',
					'click [data-action="delete-post"]'         : 'deletePost',
					'click [data-action="confirm-trash-post"]'  : 'confirmTrashPost',
					'click [data-action="confirm-delete-post"]' : 'confirmDeletePost',
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

					this.on( 'render',   this.dismiss, this );
					this.on( 'rendered', this.selectize, this );

					this.listenTo( this.model, 'change', this.render );
				},

				/**
				 * .
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				previewPerson : function() {

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
				dismiss : function() {

					this.$el.removeClass( 'confirmation-asked' );

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

					if ( _.has( options.title, 'rendered' ) ) {
						options.title = options.title.rendered;
					} else if ( _.isEmpty( options.title ) ) {
						options.title = '<em>Untitled</em>';
					}

					return options;
				},

			}),

			/**
			 * PersonBrowser Content View.
			 *
			 * @since 1.0.0
			 */
			BrowserContent : PostBrowser.view.BrowserContent.extend({

				className : 'post-browser-content person-browser-content',

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

					$( window ).off( 'resize.person-browser-content' ).on( 'resize.person-browser-content', _.debounce( this.adjust, 50 ) );
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

					this.views.add( new PersonBrowser.view.BrowserItem({
						controller : this.controller,
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
					     height = Math.floor( width * 1.25 );

					this.$( '.person' ).css({
						width : width,
					});

					this.$( '.person-picture' ).css({
						width  : width,
						height : height,
					});

					return this;
				},

			}),
		} ),

		/**
		 * Create persons browser instance.
		 *
		 * @since 1.0.0
		 */
		loadBrowser : function() {

			var browser = document.querySelector( '#wpmoly-person-browser' );
			if ( browser ) {
				PersonBrowser.browser = new Browser( browser );
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
				PersonBrowser.loadBrowser();
				PostBrowser.loadSidebar();
			} );

			return PersonBrowser;
		},

	} );

})( jQuery, _, Backbone );

wpmoly.runners['personbrowser'] = wpmoly.browser.persons;
