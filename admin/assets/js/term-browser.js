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
		  && _.has( TermBrowser.controller, block.dataset['controller'] )
		  && _.has( TermBrowser.view, block.dataset['controller'] ) ) {
			controller = new TermBrowser.controller[ block.dataset['controller'] ]({
				post_id : parseInt( wpmoly.$( '#object_ID' ).val() ),
			});
			view = new TermBrowser.view[ block.dataset['controller'] ]({
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

		var view = new TermBrowser.view.Sidebar( { el : sidebar } );

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
	 * TermBrowser wrapper.
	 *
	 * @since 1.0.0
	 */
	var TermBrowser = wpmoly.browser.terms = {

		/**
		 * List of terms browser models.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		model : {},

		/**
		 * List of terms browser controllers.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		controller : {},

		/**
		 * List of terms browser views.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		view : {},
	};

	TermBrowser.controller.Browser = Backbone.Model.extend({

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

			this.terms = options.terms;

			this.listenTo( this.terms, 'error',  this.error );
		},

		/**
		 * Search terms.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} query Search query.
		 *
		 * @return Returns itself to allow chaining.
		 */
		searchTerms : function( query ) {

			if ( _.isString( query ) && 2 < query.length ) {
				this.terms.fetch({
					data : {
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
		 * @param {object} collection Term collection.
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
		 * Permanently delete a specific term.
		 *
		 * @since 1.0.0
		 *
		 * @param {int} id Term ID.
		 *
		 * @return Returns itself to allow chaining.
		 */
		deleteTerm : function( id ) {

			if ( ! this.terms.has( id ) ) {
				return this;
			}

			var term = this.terms.get( id );

			term.destroy( { wait : true } );

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
		 * @return {int}
		 */
		setCurrentPage : function( page ) {

			var page = parseInt( page );
			if ( ! this.isBrowsable( page ) ) {
				return 0;
			}

			var data = _.extend( this.terms.state.data, { page : page } );

			this.terms.fetch( { data : data } );

			return page;
		},

		/**
		 * Retrieve the current page number.
		 *
		 * @since 1.0.0
		 *
		 * @return {int}
		 */
		getCurrentPage : function() {

			return parseInt( this.terms.state.currentPage ) || 1;
		},

		/**
		 * Retrieve the total number of pages.
		 *
		 * @since 1.0.0
		 *
		 * @return {int}
		 */
		getTotalPages : function() {

			return parseInt( this.terms.state.totalPages ) || 1;
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
		 * Reset content.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		reset : function() {

			this.terms.fetch({
				data : {
					context  : 'edit',
					orderby  : 'name',
					per_page : 20,
				},
			});

			return this;
		},

	});

	/**
	 * TermBrowser 'Discover' Block Controller.
	 *
	 * @since 1.0.0
	 */
	TermBrowser.controller.BrowserBlock = Backbone.Model.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

      this.counts = TermBrowser.browser.terms.counts;

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

			this.listenTo( TermBrowser.browser.terms, 'destroy', this.refresh );
			this.listenTo( TermBrowser.browser.terms, 'sync',    this.refresh );

			return this;
    },

		/**
		 * Browse the terms based on post status.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Browsing options.
		 *
		 * @return xhr
		 */
		filter : function( status ) {

			return TermBrowser.browser.controller.set( { status : status } );
		},

		/**
		 * Refresh the term list.
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
	 * TermBrowser 'Add New' Block View.
	 *
	 * @since 1.0.0
	 */
	TermBrowser.controller.AddNewBlock = Backbone.Model.extend({

		/**
		 * Check if an term already exists with specified name.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} name Term name.
		 *
		 * @return xhr
		 */
		termExists : function( name ) {

			this.terms = new this.collection;

			return this.terms.fetch( { data : { search : name } } );
		},

		/**
		 * Create a new term.
		 *
		 * If successful, add the new term to the browser.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} name Term name.
		 *
		 * @return Returns itself to allow chaining.
		 */
		create : function( name ) {

			var termModel = this.model;

			this.termExists( name ).done( function( response, status, xhr ) {
				var matches = _.where( _.pluck( response, 'name' ), { rendered : name } );
				if ( matches.length ) {
					wpmoly.warning( wpmolyEditorL10n.existing_term );
				} else {
					var model = new termModel;
					model.save({
						name : name,
					}, {
						success : function( model, xhr, options ) {
							TermBrowser.browser.controller.searchTerms( name );
						},
					});
				}
			} ).fail( function( xhr ) {
				wpmoly.error( xhr, { destroy : false } );
			} );
		},

	});

	/**
	 * TermBrowser 'Discover' Block View.
	 *
	 * @since 1.0.0
	 */
	TermBrowser.view.BrowserBlock = Dashboard.view.Block.extend({

		events : function() {
			return _.extend( Dashboard.view.Block.prototype.events || {}, {
				'click [data-action="start-search"]'   : 'startSearch',
				'click [data-action="close-search"]'   : 'closeSearch',
				'keypress [data-value="search-query"]' : 'startSearch',
				'click [data-action="filter"]'         : 'filterTerms',
			} );
		},

		template : wp.template( 'wpmoly-term-editor-discover' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.listenTo( this.controller.counts, 'change', this.render );
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

			TermBrowser.browser.controller.searchTerms( query );

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

			TermBrowser.browser.controller.reset();

			return this;
		},

		/**
		 * Switch term status.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} JS 'click' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		filterTerms : function( event ) {

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

			options.text = wpmoly._n( wpmolyEditorL10n.n_total_terms, s.numberFormat( options.total, 0, wpmolyL10n.d_separator, wpmolyL10n.o_separator ) );

			return options;
		},

	});

	/**
	 * TermBrowser 'Add New' Block View.
	 *
	 * @since 1.0.0
	 */
	TermBrowser.view.AddNewBlock = Dashboard.view.Block.extend({

		events : function() {
			return _.extend( Dashboard.view.Block.prototype.events || {}, {
				'input [data-value="new-term-name"]' : 'change',
				'click [data-action="add-new-term"]' : 'create',
			} );
		},

		template : wp.template( 'wpmoly-term-editor-add-new' ),

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
		 * Enable/disable submit button based on name input length.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		change : function() {

			var name = this.$( '[data-value="new-term-name"]' ).val().trim();

			this.$( '#add-new-term' ).prop( 'disabled', ( 2 >= name.length ) );

			return this;
		},

		/**
		 * Create a new term, if name input length reach the minimum.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		create : function() {

			var name = this.$( '[data-value="new-term-name"]' ).val().trim();

			if ( 3 > name.length ) {
				return this;
			}

			this.$( '#add-new-term' ).prop( 'disabled', true );
			this.$( '[data-value="new-term-name"]' ).val( '' );

			this.controller.create( name );

			return this;
		},

	});

	/**
	 * TermBrowser Sidebar View.
	 *
	 * @since 1.0.0
	 */
	TermBrowser.view.Sidebar = wp.Backbone.View;

	/**
	 * TermBrowser Pagination Menu View.
	 *
	 * @since 1.0.0
	 */
	TermBrowser.view.BrowserPagination = wp.Backbone.View.extend({

		className : 'term-browser-menu term-browser-pagination',

		template : wp.template( 'wpmoly-term-browser-pagination' ),

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

			this.terms = this.controller.terms;

			this.listenTo( this.terms, 'update', this.render );
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
	 * TermBrowser Item View.
	 *
	 * @since 1.0.0
	 */
	TermBrowser.view.BrowserItem = wp.Backbone.View.extend({

		className : 'term',

		template : wp.template( 'wpmoly-term-browser-item' ),

		events : {
			'click [data-action="dismiss"]'              : 'dismiss',
			'click [data-action="delete-term"]'         : 'deleteTerm',
			'click [data-action="confirm-delete-term"]' : 'confirmDeleteTerm',
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
		 * Wrapper function for deleting terms: ask for user confirmation
		 * before doing anything.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		deleteTerm : function() {

			this.$el.addClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Permanently delete the term.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		confirmDeleteTerm : function() {

			var id = this.model.get( 'id' );

			this.controller.deleteTerm( id );

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

			this.$( '.term-thumbnail' ).css( { height : this.height } );

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
		 * Render the View.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		render : function() {

			wp.Backbone.View.prototype.render.apply( this, arguments );

			_.delay( _.bind( this.resize, this ), 50 );

			return this;
		},

	});

	/**
	 * TermBrowser Content View.
	 *
	 * @since 1.0.0
	 */
	TermBrowser.view.BrowserContent = wp.Backbone.View.extend({

		className : 'term-browser',

		template : wp.template( 'wpmoly-term-browser' ),

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
			this.terms   = this.controller.terms;

			this.listenTo( this.terms, 'request', this.loading );
			this.listenTo( this.terms, 'update',  this.update );
			this.listenTo( this.terms, 'sync',    this.loaded );
			this.listenTo( this.terms, 'error',   this.loaded );
			this.listenTo( this.terms, 'destroy', this.loaded );
		},

		/**
		 * Update term views.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		update : function() {

			this.views.remove();

			_.each( this.terms.models, this.addItem, this );

			return this;
		},

		/**
		 * Add new term item view.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model Term model.
		 *
		 * @return Returns itself to allow chaining.
		 */
		addItem : function( model ) {

			this.views.add( new TermBrowser.view.BrowserItem({
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
	 * TermBrowser Browser View.
	 *
	 * @since 1.0.0
	 */
	TermBrowser.view.Browser = wp.Backbone.View.extend({

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
			this.terms      = this.controller.terms;

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
				this.content = new TermBrowser.view.BrowserContent( options );
			}

			if ( ! this.pagination ) {
				this.pagination = new TermBrowser.view.BrowserPagination( options );
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
	TermBrowser.loadSidebar = function() {

		var sidebar = document.querySelector( '#wpmoly-browser-sidebar' );
		if ( sidebar ) {
			TermBrowser.sidebar = new Sidebar( sidebar );
		}
	};

})( jQuery, _, Backbone );
