wpmoly = window.wpmoly || {};

wpmoly.browser = wpmoly.browser || {};

(function( $, _, Backbone ) {

	var Dashboard = wpmoly.dashboard;

	/**
	 * Create a new Block instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} block Block DOM element.
	 *
	 * @return {object} Block instance.
	 */
	var Block = function( block ) {

		var controller, view;
		if ( _.has( block.dataset, 'controller' )
		  && _.has( PostBrowser.controller, block.dataset['controller'] )
		  && _.has( PostBrowser.view, block.dataset['controller'] ) ) {
			controller = new PostBrowser.controller[ block.dataset['controller'] ]({
				post_id : parseInt( wpmoly.$( '#object_ID' ).val() ),
			});
			view = new PostBrowser.view[ block.dataset['controller'] ]({
				el         : block,
				controller : controller,
			});
		} else {
			view = new Dashboard.view.Block( { el : block } );
		}

		/**
		 * Block instance.
		 *
		 * Provide a set of useful functions to interact with the block
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var block = {

			id : block.dataset['controller'] || wp.api.utils.camelCaseDashes( block.id.replace( 'editor-', '' ) ),

			view : view,

			controller : controller,

		};

		return block;
	};

	/**
	 * Create a new Sidebar instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} sidebar Sidebar DOM element.
	 *
	 * @return {object} Sidebar instance.
	 */
	var Sidebar = function( sidebar ) {

		var view = new PostBrowser.view.Sidebar( { el : sidebar } );

		var blocks = {};
		_.each( document.querySelectorAll( '.editor-block' ), function( block ) {
			// Create Block instance.
			var block = new Block( block );
			// Add Block view to sidebar.
			view.views.add( block.view, { silent : true } );
			// Keep track of Blocks.
			blocks[ block.id ] = block;
		}, this );

		/**
		 * Sidebar instance.
		 *
		 * Provide a set of useful functions to interact with the sidebar
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var sidebar = {

			blocks : blocks,

		};

		return sidebar;
	};

	/**
	 * PostBrowser wrapper.
	 *
	 * @since 1.0.0
	 */
	var PostBrowser = wpmoly.browser.posts = {

		/**
		 * List of posts browser models.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		model : {},

		/**
		 * List of posts browser controllers.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		controller : {},

		/**
		 * List of posts browser views.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		view : {},
	};

	PostBrowser.controller.Browser = Backbone.Model.extend({

		defaults : {
			status : 'publish',
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

			this.posts = options.posts;

			this.on( 'change:status', this.filter, this );

			this.listenTo( this.posts, 'error',  this.error );
		},

		/**
		 * Search posts.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} query Search query.
		 *
		 * @return Returns itself to allow chaining.
		 */
		searchPosts : function( query ) {

			if ( _.isString( query ) && 2 < query.length ) {
				this.posts.fetch({
					data : {
						_fields  : 'title,id,type,meta,poster,edit_link',
						search   : query,
						context  : 'edit',
						per_page : 20,
					},
				});
			}

			return this;
		},

		/**
		 * Notify collection errors.
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
		 * Fetch posts based on post status.
		 *
		 * @since 1.0.0
		 *
		 * @param {int} id Post ID.
		 *
		 * @return Returns itself to allow chaining.
		 */
		filter : function() {

			var data = _.extend( this.posts.state.data, { status : this.get( 'status' ) } );

			this.posts.fetch( { data : data } );

			return this;
		},

		/**
		 * Check if a specific page number matches the current page number.
		 *
		 * @since 1.0.0
		 *
		 * @param {int} page Page number.
		 *
		 * @return {boolean}
		 */
		isCurrentPage : function( page ) {

			var page = parseInt( page );

			return page === this.getCurrentPage();
		},

		/**
		 * Check if a specific page number is reachable.
		 *
		 * @since 1.0.0
		 *
		 * @param {int} page Page number.
		 *
		 * @return {boolean}
		 */
		isBrowsable : function( page ) {

			var page = parseInt( page );

			return 1 <= page && page <= this.getTotalPages() && ! this.isCurrentPage( page );
		},

		/**
		 * Jump to the specific page number after making sure that number is
		 * reachable.
		 *
		 * @since 1.0.0
		 *
		 * @param {int} page Page number.
		 *
		 * @return {Promise}
		 */
		setCurrentPage : function( page ) {

			var page = parseInt( page );
			if ( ! this.isBrowsable( page ) ) {
				return 0;
			}

			var data = _.extend( this.posts.state.data, { page : page } );

			return this.posts.fetch( { data : data } );
		},

		/**
		 * Retrieve the current page number.
		 *
		 * @since 1.0.0
		 *
		 * @return {int}
		 */
		getCurrentPage : function() {

			return parseInt( this.posts.state.currentPage ) || 1;
		},

		/**
		 * Retrieve the total number of pages.
		 *
		 * @since 1.0.0
		 *
		 * @return {int}
		 */
		getTotalPages : function() {

			return parseInt( this.posts.state.totalPages ) || 1;
		},

		/**
		 * Jump to the previous page, if any.
		 *
		 * @since 1.0.0
		 *
		 * @return {int}
		 */
		previousPage : function() {

			return this.setCurrentPage( this.getCurrentPage() - 1 );
		},

		/**
		 * Jump to the next page, if any.
		 *
		 * @since 1.0.0
		 *
		 * @return {int}
		 */
		nextPage : function() {

			return this.setCurrentPage( this.getCurrentPage() + 1 );
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

			var post = this.posts.get( id );

			post.save( { status : 'draft' } ).done( _.bind( this.filter, this ) );

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

			var post = this.posts.get( id )
			  status = 'publish';

			if ( 'trash' === post.get( 'status' ) ) {
				status = 'draft';
			}

			post.save( { status : status } ).done( _.bind( this.filter, this ) );

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

			var post = this.posts.get( id );

			post.requireForceForDelete = false;
			post.destroy( { wait : true } );

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

			var post = this.posts.get( id );

			post.destroy( { wait : true } );

			return this;
		},

		/**
		 * Reset content.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		reset : function() {

			this.posts.fetch({
				data : {
					_fields  : 'title,id,type,meta,poster,edit_link',
					context  : 'edit',
					per_page : 20,
				},
			});

			return this;
		},

	});

	/**
	 * PostBrowser 'Discover' Block Controller.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.controller.BrowserBlock = Backbone.Model.extend({

    /**
     * Initialize the Controller.
     *
     * @since 1.0.0
     *
     * @param {object} attributes Controller attributes.
     * @param {object} options    Controller options.
     */
    initialize : function( attributes, options ) {

      this.counts = PostBrowser.browser.posts.counts;

			this.bindEvents();
		},

   	/**
     * Bind controller events.
     *
     * @since 1.0.0
     *
     * @return Returns itself to allow chaining.
     */
    bindEvents : function() {

			this.listenTo( PostBrowser.browser.posts, 'destroy', this.refresh );
			this.listenTo( PostBrowser.browser.posts, 'sync',    this.refresh );

			return this;
    },

		/**
		 * Browse the posts based on post status.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Browsing options.
		 *
		 * @return xhr
		 */
		filter : function( status ) {

			return PostBrowser.browser.controller.set( { status : status } );
		},

		/**
		 * Refresh the post list.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		refresh : function() {

			// Not delaying sometime results in outdated counts.
			_.delay( _.bind( function() {
				this.counts.fetch( { data : { context  : 'edit' } } );
			}, this ), 100 );

			return this;
		},

	});

	/**
	 * PostBrowser 'Add New' Block View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.controller.AddNewBlock = Backbone.Model.extend({

		/**
		 * Check if a post already exists with specified title.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} title Post title.
		 *
		 * @return xhr
		 */
		postExists : function( title ) {

			this.posts = new this.collection;

			return this.posts.fetch({
				data : {
					search  : title,
					status  : [ 'publish', 'draft', 'auto-draft', 'future', 'pending', 'private' ],
				},
			});
		},

		/**
		 * Create a new post.
		 *
		 * If successful, add the new post to the browser.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		create : function( title ) {

			var postModel = this.model;

			this.postExists( title ).done( function( response, status, xhr ) {
				var matches = _.where( _.pluck( response, 'title' ), { rendered : title } );
				if ( matches.length ) {
					wpmoly.warning( wpmolyEditorL10n.existing_post );
				} else {
					var model = new postModel,
					    posts = PostBrowser.browser.posts;
					model.save({
						title  : title,
						status : 'publish',
					}, {
						success : function( model, xhr, options ) {
							var message = wpmolyEditorL10n.post_created + ' ' + s.sprintf( '<a href="%s">%s</a>', model.get( 'edit_link' ), wpmolyEditorL10n.start_editing );
							wpmoly.success( message );
						},
						error : function( model, xhr, options ) {
							wpmoly.error( xhr, { destroy : false } );
						}
					});
					PostBrowser.browser.controller.reset();
				}
			} ).fail( function( xhr ) {
				wpmoly.error( xhr, { destroy : false } );
			} );
		},

	});

	/**
	 * PostBrowser 'Drafts' Block View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.controller.DraftsBlock = Backbone.Model.extend({

		/**
		 * Bind controller events.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		bindEvents : function() {

			this.posts = new this.collection;

			this.listenTo( this.posts, 'remove', this.update );

			this.listenTo( PostBrowser.browser.posts, 'update', this.refresh );

			return this;
		},

		/**
		 * Update browser to reflect changes.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		update : function() {

			PostBrowser.browser.controller.filter();

			return this;
		},

		/**
		 * Restore post from draft.
		 *
		 * Change the post status to 'publish' and remove the model from
		 * the collection.
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

			var post = this.posts.get( id );

			post.save( { status : 'publish' }, { patch : true } ).done( _.bind( function( model ) {
				this.posts.remove( model.id );
			}, this ) );

			return this;
		},

		/**
		 * Move draft to the trash.
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

			var post = this.posts.get( id );

			// Don't delete permanently.
			post.requireForceForDelete = false;
			post.destroy( { wait : true } );

			return this;
		},

		/**
		 * Move all drafts to the trash.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashPosts : function() {

			_.each( _.clone( this.posts.models ), function( model ) {
				model.requireForceForDelete = false;
				model.destroy( { wait : true } );
			} );

			return this;
		},

		/**
		 * Refresh the post list.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		refresh : function() {

			this.posts.fetch({
				data : {
					context : 'edit',
					status  : [ 'draft', 'auto-draft' ],
				},
			});

			return this;
		},

	});

	/**
	 * PostBrowser 'Trash' Block View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.controller.TrashBlock = Backbone.Model.extend({

		/**
		 * Bind controller events.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		bindEvents : function() {

			this.posts = new this.collection;

			this.listenTo( this.posts, 'remove', this.update );

			this.listenTo( PostBrowser.browser.posts, 'update', this.refresh );

			return this;
		},

		/**
		 * Update browser to reflect changes.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		update : function() {

			PostBrowser.browser.controller.filter();

			return this;
		},

		/**
		 * Restore post from draft.
		 *
		 * Change the post status to 'draft' and remove the model from
		 * the collection.
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

			var post = this.posts.get( id );

			post.save( { status : 'draft' }, { patch : true } ).done( _.bind( function( model ) {
				this.posts.remove( model.id );
			}, this ) );

			return this;
		},

		/**
		 * Move draft to the trash.
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

			var post = this.posts.get( id );

			// Don't delete permanently.
			post.requireForceForDelete = true;
			post.destroy( { wait : true } );

			return this;
		},

		/**
		 * Move all drafts to the trash.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashPosts : function() {

			_.each( _.clone( this.posts.models ), function( model ) {
				model.requireForceForDelete = true;
				model.destroy( { wait : true } );
			} );

			return this;
		},

		/**
		 * Refresh the post list.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		refresh : function() {

			this.posts.fetch({
				data : {
					context : 'edit',
					status  : 'trash',
				},
			});

			return this;
		},

	});

	/**
	 * PostBrowser 'Discover' Block View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.BrowserBlock = Dashboard.view.Block.extend({

		events : function() {
			return _.extend( Dashboard.view.Block.prototype.events || {}, {
				'click [data-action="start-search"]'   : 'startSearch',
				'click [data-action="close-search"]'   : 'closeSearch',
				'keypress [data-value="search-query"]' : 'startSearch',
				'click [data-action="filter"]'         : 'filterPosts',
			} );
		},

		template : wp.template( 'wpmoly-post-editor-discover' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.listenTo( PostBrowser.browser.controller, 'change:status', this.render );
			this.listenTo( this.controller.counts,         'change',        this.render );
		},

		/**
		 * Start search.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		startSearch : function( event ) {

			if ( 'keypress' === event.type && ( 13 !== ( event.which || event.charCode || event.keyCode ) ) ) {
				return this;
			} else {
				event.preventDefault();
			}

			var query = this.$( '[data-value="search-query"]' ).val();

			this.$el.addClass( 'searching' );

			PostBrowser.browser.controller.searchPosts( query );

			return this;
		},

		/**
		 * Reset search.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		closeSearch : function( event ) {

			event.preventDefault();

			this.$el.removeClass( 'searching' );
			this.$( '[data-value="search-query"]' ).val( '' );

			PostBrowser.browser.controller.reset();

			return this;
		},

		/**
		 * Switch post status.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} JS 'click' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		filterPosts : function( event ) {

			event.preventDefault();

			var $target = this.$( event.currentTarget ),
			     status = $target.data( 'value' ) || 'publish';

			this.controller.filter( status );

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

			var options = this.controller.counts.toJSON();

			options.total = _.sum( options );

			_.each( options, function( value, key ) {
				if ( value ) {
					options[ key ] = s.numberFormat( value, 0, wpmolyL10n.d_separator, wpmolyL10n.o_separator );
				}
			} );

			options.current = PostBrowser.browser.controller.get( 'status' ) || 'publish';

			var list = [],
			    text = wpmoly._n( wpmolyEditorL10n.n_total_post, s.numberFormat( parseInt( options.total ), 0, wpmolyL10n.d_separator, wpmolyL10n.o_separator ) );

			if ( options.publish ) {
				list.push( '<a href="#" data-action="filter" data-value="publish">' + wpmoly._n( wpmolyEditorL10n.n_published, options.publish ) + '</a></li>' );
			}

			if ( options.draft && options.draft > 0 ) {
				list.push( '<a href="#" data-action="filter" data-value="draft">' + wpmoly._n( wpmolyEditorL10n.n_draft, options.draft ) + '</a></li>' );
			} else {
				if ( options.publish ) {
					list.push( wpmolyEditorL10n.no_draft.toLowerCase() );
				} else {
					list.push( wpmolyEditorL10n.no_draft );
				}
			}

			if ( options.trash && options.trash > 0 ) {
				list.push( '<a href="#" data-action="filter" data-value="trash">' + wpmoly._n( wpmolyEditorL10n.n_trashed, options.trash ) + '</a></li>' );
			} else {
				if ( options.publish || options.draft ) {
					list.push( wpmolyEditorL10n.no_trash.toLowerCase() );
				} else {
					list.push( wpmolyEditorL10n.no_trash );
				}
			}

			if ( options.future ) {
				list.push( '<a href="#" data-action="filter" data-value="future">' + wpmoly._n( wpmolyEditorL10n.n_future, options.future ) + '</a></li>' );
			}

			if ( options.pending ) {
				list.push( '<a href="#" data-action="filter" data-value="pending">' + wpmoly._n( wpmolyEditorL10n.n_pending, options.pending ) + '</a></li>' );
			}

			if ( options.private ) {
				list.push( '<a href="#" data-action="filter" data-value="private">' + wpmoly._n( wpmolyEditorL10n.n_private, options.private ) + '</a></li>' );
			}

			if ( options.autodraft ) {
				list.push( '<a href="#" data-action="filter" data-value="auto-draft">' + wpmoly._n( wpmolyEditorL10n.n_autodraft, options.autodraft ) + '</a></li>' );
			}

			options.text = text + ' ' + list.join( ', ' ) + '.';

			return options;
		},

	});

	/**
	 * PostBrowser 'Add New' Block View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.AddNewBlock = Dashboard.view.Block.extend({

		events : function() {
			return _.extend( Dashboard.view.Block.prototype.events || {}, {
				'input [data-value="new-post-title"]' : 'change',
				'click [data-action="add-new-post"]'  : 'create',
				'keypress [data-value="new-post-title"]' : 'create',
			} );
		},

		template : wp.template( 'wpmoly-post-editor-add-new' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.render();
		},

		/**
		 * Enable/disable submit button based on title input length.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		change : function() {

			var title = this.$( '[data-value="new-post-title"]' ).val().trim();

			this.$( '#add-new-post' ).prop( 'disabled', ( 1 >= title.length ) );

			return this;
		},

		/**
		 * Create a new post, if title input length reach the minimum.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} JS 'keypress' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		create : function( event ) {

			if ( 'keypress' === event.type && ( 13 !== ( event.which || event.charCode || event.keyCode ) ) ) {
				return this;
			} else {
				event.preventDefault();
			}

			var title = this.$( '[data-value="new-post-title"]' ).val().trim();

			this.$( '#add-new-post' ).prop( 'disabled', true );
			this.$( '[data-value="new-post-title"]' ).val( '' );

			this.controller.create( title );

			return this;
		},

	});

	/**
	 * PostBrowser 'Drafts' Block Item View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.DraftsBlockItem = wpmoly.Backbone.View.extend({

		tagName : 'li',

		className : 'list-item',

		events : {
			'click [data-action="restore-post"]' : 'restorePost',
			'click [data-action="trash-post"]'   : 'trashPost',
		},

		template : wp.template( 'wpmoly-post-editor-drafts-item' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.on( 'rendered', this.setID, this );
		},

		/**
		 * Switch draft status to 'publish'.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		restorePost : function( event ) {

			var id = this.$( event.currentTarget ).data( 'item-id' );

			this.controller.restorePost( id );

			return this;
		},

		/**
		 * Move draft to the trash.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashPost : function( event ) {

			var id = this.$( event.currentTarget ).data( 'item-id' );

			this.controller.trashPost( id );

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

			var options = this.model.toJSON();

			return options;
		},

		/**
		 * Set View ID.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setID : function() {

			this.el.id = 'list-item-' + this.model.get( 'id' );

			return this;
		},

	});

	/**
	 * PostBrowser 'Drafts' Block View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.DraftsBlock = Dashboard.view.Block.extend({

		events : function() {
			return _.extend( Dashboard.view.Block.prototype.events || {}, {
				'click [data-action="dismiss"]'             : 'dismiss',
				'click [data-action="trash-posts"]'         : 'trashPosts',
				'click [data-action="confirm-trash-posts"]' : 'confirmTrashPosts',
				'change #trash-drafts-confirmed'            : 'checkConfirmation',
			} );
		},

		template : wp.template( 'wpmoly-post-editor-drafts' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.posts = this.controller.posts;

			this.on( 'rendered', this.addItems, this );

			this.listenTo( this.posts, 'request', this.loading );
			this.listenTo( this.posts, 'sync',    this.render );
		},

		/**
		 * Dismiss the confirmation request.
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
		 * Move all post drafts to the trash.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		confirmTrashPosts : function() {

			this.controller.trashPosts();

			this.$( '.posts-list' ).scrollTop( '0px' );
			this.$( '[data-action="confirm-trash-posts"]' ).prop( 'disabled', true );

			this.$el.addClass( 'emptying' );
			this.$el.removeClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Move all post drafts to the trash.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashPosts : function() {

			this.$el.addClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Check trash post drafts user confirmation.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		checkConfirmation : function() {

			var checked = this.$( '#trash-drafts-confirmed' ).is( ':checked' );

			this.$( '[data-action="confirm-trash-posts"]' ).prop( 'disabled', ! checked );

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
		 * Prepare rendering options.
		 *
		 * @since 1.0.0
		 *
		 * @return {object}
		 */
		prepare : function() {

			var options = {
				posts : this.posts.toJSON(),
			};

			return options;
		},

		/**
		 * Add items tp the View.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		addItems : function() {

			this.$el.removeClass( 'loading emptying' );

			if ( this.posts.length ) {
				this.posts.each( function( model ) {
					this.views.add( '.posts-list', new PostBrowser.view.DraftsBlockItem({
						controller : this.controller,
						model      : model,
					}) );
				}, this );
			}

			return this;
		},
	});

	/**
	 * PostBrowser 'Trash' Block Item View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.TrashBlockItem = wpmoly.Backbone.View.extend({

		tagName : 'li',

		className : 'list-item',

		events : {
			'click [data-action="dismiss"]'            : 'dismiss',
			'click [data-action="restore-post"]'       : 'restorePost',
			'click [data-action="trash-post"]'         : 'trashPost',
			'click [data-action="confirm-trash-post"]' : 'confirmTrashPost',
		},

		template : wp.template( 'wpmoly-post-editor-trash-item' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.on( 'rendered', this.setID, this );
		},

		/**
		 * Dismiss the confirmation request.
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
		 * Switch draft status to 'publish'.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		restorePost : function( event ) {

			var id = this.model.get( 'id' );

			this.controller.restorePost( id );

			return this;
		},

		/**
		 * Move draft to the trash.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashPost : function( event ) {

			this.$el.addClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Empty the trash.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		confirmTrashPost : function() {

			var id = this.model.get( 'id' );

			this.controller.trashPost( id );

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

			var options = this.model.toJSON();

			return options;
		},

		/**
		 * Set View ID.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setID : function() {

			this.el.id = 'list-item-' + this.model.get( 'id' );

			return this;
		},

	});

	/**
	 * PostBrowser 'Trash' Block View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.TrashBlock = Dashboard.view.Block.extend({

		events : function() {
			return _.extend( Dashboard.view.Block.prototype.events || {}, {
				'click [data-action="dismiss"]'             : 'dismiss',
				'click [data-action="trash-posts"]'         : 'trashPosts',
				'click [data-action="confirm-trash-posts"]' : 'confirmTrashPosts',
				'change #trash-posts-confirmed'             : 'checkConfirmation',
			} );
		},

		template : wp.template( 'wpmoly-post-editor-trash' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.posts = this.controller.posts;

			this.on( 'rendered', this.addItems, this );

			this.listenTo( this.posts, 'request', this.loading );
			this.listenTo( this.posts, 'sync',    this.render );
		},

		/**
		 * Dismiss the confirmation request.
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
		 * Empty the trash.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		confirmTrashPosts : function() {

			this.controller.trashPosts();

			this.$( '.posts-list' ).scrollTop( '0px' );
			this.$( '[data-action="confirm-trash-posts"]' ).prop( 'disabled', true );

			this.$el.addClass( 'emptying' );
			this.$el.removeClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Move all drafts to the trash.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashPosts : function() {

			this.$el.addClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Check empty trash user confirmation.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		checkConfirmation : function() {

			var checked = this.$( '#trash-posts-confirmed' ).is( ':checked' );

			this.$( '[data-action="confirm-trash-posts"]' ).prop( 'disabled', ! checked );

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
		 * Prepare rendering options.
		 *
		 * @since 1.0.0
		 *
		 * @return {object}
		 */
		prepare : function() {

			var options = {
				posts : this.posts.toJSON(),
			};

			return options;
		},

		/**
		 * Add items to the View.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		addItems : function() {

			this.$el.removeClass( 'loading emptying' );

			if ( this.posts.length ) {
				this.posts.each( function( model ) {
					this.views.add( '.posts-list', new PostBrowser.view.TrashBlockItem({
						controller : this.controller,
						model      : model,
					}) );
				}, this );
			}

			return this;
		},

	});

	/**
	 * PostBrowser Sidebar View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.Sidebar = wp.Backbone.View;

	/**
	 * PostBrowser Pagination Menu View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.BrowserPagination = wpmoly.Backbone.View.extend({

		className : 'post-browser-menu post-browser-pagination',

		template : wp.template( 'wpmoly-post-browser-pagination' ),

		events : {
			'click [data-action="previous-page"]' : 'previousPage',
			'click [data-action="next-page"]'     : 'nextPage',
			'change [data-action="jump-to"]'      : 'jumpTo',
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

			this.posts = this.controller.posts;

			this.listenTo( this.posts, 'update', this.render );
		},

		/**
		 * Go to the previous page.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		previousPage : function() {

			this.controller.previousPage();

			return this;
		},

		/**
		 * Go to the next page.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		nextPage : function() {

			this.controller.nextPage();

			return this;
		},

		/**
		 * Jump to a specific page.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		jumpTo : function() {

			var page = this.$( '[data-action="jump-to"]' ).val();

			this.controller.setCurrentPage( page );

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

			var options = {
				current : this.controller.getCurrentPage(),
				total   : this.controller.getTotalPages(),
			};

			return options;
		},

	});

	/**
	 * PostBrowser Item View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.BrowserItem = wpmoly.Backbone.View.extend({

		className : 'post',

		template : wp.template( 'wpmoly-post-browser-item' ),

		events : {
			'click [data-action="dismiss"]'             : 'dismiss',
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

			this.on( 'rendered', this.adjust, this );

			this.controller = options.controller;
		},

		/**
		 * Dismiss the confirmation request.
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
		 * Convert post to draft.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		draftPost : function() {

			var id = this.model.get( 'id' );

			this.controller.draftPost( id );

			return this;
		},

		/**
		 * Restore post.
		 *
		 * Trashed posts are permanently deleted whereas drafts are set
		 * to 'publish'.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		restorePost : function() {

			var id = this.model.get( 'id' );

			this.controller.restorePost( id );

			return this;
		},

		/**
		 * Wrapper function for trashing posts: ask for user confirmation
		 * before doing anything.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashPost : function() {

			this.$el.addClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Wrapper function for deleting posts: ask for user confirmation
		 * before doing anything.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		deletePost : function() {

			this.$el.addClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Move post to the trash.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		confirmTrashPost : function() {

			var id = this.model.get( 'id' );

			this.controller.trashPost( id );

			this.$el.removeClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Permanently delete the post.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		confirmDeletePost : function() {

			var id = this.model.get( 'id' );

			this.controller.deletePost( id );

			this.$el.removeClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Adjust thumbnail height to keep ratio.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		resize : function() {

			if ( ! this.height ) {
				this.height = this.$el.width();
			}

			this.$( '.post-thumbnail' ).css( { height : this.height } );

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

			var options = this.model.toJSON();

			return options;
		},

		/**
		 * Adjust View.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		adjust : function() {

			_.delay( _.bind( this.resize, this ), 50 );

			this.$el.addClass( 'type-empty' );
			this.$el.addClass( 'status-' + this.model.get( 'status' ) );

			return this;
		},

	});

	/**
	 * PostBrowser Content View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.BrowserContent = wpmoly.Backbone.View.extend({

		className : 'post-browser-content',

		template : wp.template( 'wpmoly-post-browser' ),

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
			this.posts      = this.controller.posts;

			this.listenTo( this.posts, 'request', this.loading );
			this.listenTo( this.posts, 'update',  this.update );
			this.listenTo( this.posts, 'sync',    this.loaded );
			this.listenTo( this.posts, 'error',   this.loaded );
			this.listenTo( this.posts, 'destroy', this.loaded );
		},

		/**
		 * Update post views.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		update : function() {

			this.views.remove();

			_.each( this.posts.where( { status : this.controller.get( 'status' ) } ), this.addItem, this );

			return this;
		},

		/**
		 * Add new post item view.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model Post model.
		 *
		 * @return Returns itself to allow chaining.
		 */
		addItem : function( model ) {

			this.views.add( new PostBrowser.view.BrowserItem({
				controller : this.controller,
				model      : model,
			}) );

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

	});

	/**
	 * PostBrowser Browser View.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.view.Browser = wpmoly.Backbone.View.extend({

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			var options = options || {};

			this._views = {};

			this.controller = options.controller;
			this.posts      = this.controller.posts;

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
				controller : this.controller,
			};

			if ( ! this.content ) {
				this.content = new PostBrowser.view.BrowserContent( options );
			}

			if ( ! this.pagination ) {
				this.pagination = new PostBrowser.view.BrowserPagination( options );
			}

			this.views.add( this.content );
			this.views.add( this.pagination );

			return this;
		},

	});

	/**
	 * Create sidebar instance.
	 *
	 * @since 1.0.0
	 */
	PostBrowser.loadSidebar = function() {

		var sidebar = document.querySelector( '#wpmoly-browser-sidebar' );
		if ( sidebar ) {
			PostBrowser.sidebar = new Sidebar( sidebar );
		}
	};

})( jQuery, _, Backbone );
