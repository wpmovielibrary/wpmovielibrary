
wpmoly = window.wpmoly || {};

var Search = wpmoly.view.Search || {};

_.extend( Search, {

	Result: wp.Backbone.View.extend({

		tagName: 'li',

		className: 'wpmoly-search-results-item',

		template: wp.template( 'wpmoly-search-result' ),

		events: {
			'click [data-action="fetch"]' : 'fetch'
		},

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		render: function() {

			var  config = wpmoly.api.configuration
			poster_path = this.model.get( 'poster_path' ),
			       data = {
				title    : this.model.get( 'title' ),
				overview : this.model.get( 'overview' ).split( ' ' ).slice( 0, 30 ).join( ' ' ) + '…',
				year     : new Date( this.model.get( 'release_date' ) ).getFullYear() || '',
				poster   : poster_path ? wpmoly.api.poster.getUrl( poster_path, 'small' ) : wpmolyDefaultImages.poster.small.url
			};

			this.$el.html( this.template( data ) );

			return this;
		},

		/**
		 * Fetch the movie corresponding to the current result.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		fetch: function( event ) {

			event.preventDefault();

			wpmoly.trigger( 'api:fetch', { query: this.model.get( 'id' ) } );
		}

	}),

	ResultsHeader: wp.Backbone.View.extend({

		className: 'wpmoly-search-results-header-container',

		template: wp.template( 'wpmoly-search-results-header' ),

		events: {
			'click [data-view="grid"]'    : 'modeGrid',
			'click [data-view="list"]'    : 'modeList',
			'click [data-action="reset"]' : 'reset',
		},

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.collection = options.collection || {};

			wpmoly.on( 'search:results:grid results:list', this.render, this );
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    mode
		 * 
		 * @return   Returns itself to allow chaining
		 */
		render: function( mode ) {

			var data = {
				page  : this.collection.page,
				total : this.collection.total_pages,
				view  : mode || 'grid'
			};

			this.$el.html( this.template( data ) );

			return this;
		},

		/**
		 * Display results as a grid.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		modeGrid: function() {

			wpmoly.trigger( 'search:results:grid', 'grid' );

			return this;
		},

		/**
		 * Display results as an extended grid.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		modeList: function() {

			wpmoly.trigger( 'search:results:list', 'list' );

			return this;
		},

		/**
		 * Reset the search results.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		reset: function() {

			wpmoly.trigger( 'search:reset' );

			return this;
		},

	}),

	ResultsMenu: wp.Backbone.View.extend({

		tagName: 'ul',

		template: wp.template( 'wpmoly-search-results-menu' ),

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.collection = options.collection || {};
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    mode
		 * 
		 * @return   Returns itself to allow chaining
		 */
		render: function( mode ) {

			var page = this.collection.page,
			   total = this.collection.total_pages;

			var data = {
				items: wpmoly.utils.paginate( page, total )
			};

			this.$el.html( this.template( data ) );

			return this;
		},

	}),

	ResultsList: wp.Backbone.View.extend({

		id: 'wpmoly-search-results-items',

		tagName: 'ul',

		className: 'wpmoly-search-results-items clearfix',

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			this.frame      = options.frame || {};
			this.controller = options.controller || {};
			this.collection = options.collection || {};
			this.columns    = options.columns    || 4;

			this.results = {};
			this.view    = 'grid';

			// Add new views for new movies
			this.listenTo( this.collection, 'add', function( model, collection, options ) {
				this.views.add( this.create_subview( model ) );
			} );

			// Remove views when models are removed
			this.listenTo( this.collection, 'remove', function( model, collection, options ) {
				var view = this.results[ model.cid ];
				delete this.results[ model.cid ];

				if ( view ) {
					view.remove();
				}
			} );

			// Re-render the view when collection is emptied
			this.listenTo( this.collection, 'reset', this.render );

			// Event handlers
			_.bindAll( this, 'set_grid' );

			// Reset columns on resize
			this.$window = wpmoly.$( window );
			this.$window.off( 'resize.' + this.id ).on( 'resize.' + this.id, _.debounce( this.set_grid, 50 ) );
		},

		/**
		 * Prepare the View.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		prepare: function() {

			if ( this.collection.length ) {
				this.views.set( this.collection.map( this.create_subview, this ) );
			}
		},

		/**
		 * Render the view.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function(  ) {

			wp.Backbone.View.prototype.render.apply( this, arguments );

			this.$el.attr( 'data-columns', this.columns );

			_.defer( this.set_grid, 50 );

			return this;
		},

		/**
		 * Calcul the best number of columns to use and resize thumbnails
		 * to fit correctly.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		set_grid: function() {

			if ( this.$el.hasClass( 'extended' ) ) {
				return false;
			}

			var prev = this.columns,
			   width = this.$el.actual( 'width' );

			if ( width ) {
				this.columns = Math.min( Math.round( width / 150 ), 12 ) || 1;
				if ( ! prev || prev !== this.columns ) {
					this.$el.closest( '.wpmoly-search-results-items' ).attr( 'data-columns', this.columns );
				}
			}

			this.set_columns( force = true );

			return this;
		},

		/**
		 * Fix thumbnails height to display properly in the grid.
		 *
		 * If the force parameter is set to true every movie in the
		 * grid will be resized; it set to false only movies not already
		 * resized will be considered.
		 * 
		 * @since    3.0
		 * 
		 * @param boolean force resize
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		set_columns: function( force ) {

			if ( ! this.collection.length ) {
				return;
			}

			if ( true === force ) {
				var $li = this.$( 'li' ),
				$items = $li.find( '.thumbnail' );
				$items.css({ width: '', height: '' });
				$li.css({ width: '' });
			} else {
				var $li = this.$( 'li' ).not( '.resized' ),
				$items = $li.find( '.thumbnail' );
			}

			var width = this.$( 'li:first' ).actual( 'width' ) - 8,
			   height = Math.round( width * 1.5 );

			this.thumbnail_width  = width;
			this.thumbnail_height = height;

			$li.addClass( 'resized' ).css({
				width: this.thumbnail_width,
				height: this.thumbnail_height
			});
			$items.css({
				width: this.thumbnail_width,
				height: this.thumbnail_height
			});

			return this;
		},

		/**
		 * Create a result subview.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		create_subview: function( model ) {

			var view = new wpmoly.view.Search.Result({
				model      : model,
				controller : this.controller,
				collection : this.collection,
				parent     : this
			});

			return this.results[ model.cid ] = view;
		}

	})

});

_.extend( Search, {

	Results: wp.Backbone.View.extend({

		className: 'wpmoly-search-results-container',

		template: wp.template( 'wpmoly-search-results' ),

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};

			wpmoly.on( 'search:open',  this.open,  this );
			wpmoly.on( 'search:close', this.close, this );

			wpmoly.on( 'search:results:grid', this.modeGrid, this );
			wpmoly.on( 'search:results:list', this.modeList, this );

			this.set_regions();

			this.render();
		},

		/**
		 * Set Regions (subviews).
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		set_regions: function() {

			this.header  = new wpmoly.view.Search.ResultsHeader({ controller: this.controller, collection: this.controller.results, frame: this });
			this.content = new wpmoly.view.Search.ResultsList({ controller: this.controller, collection: this.controller.results, frame: this });
			this.menu    = new wpmoly.view.Search.ResultsMenu({ controller: this.controller, collection: this.controller.results, frame: this });

			this.views.set( '#wpmoly-search-results-header',  this.header );
			this.views.set( '#wpmoly-search-results-content', this.content );
			this.views.set( '#wpmoly-search-results-menu',    this.menu );

			return this;
		},

		/**
		 * Show the panel.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		open: function() {

			this.$el.slideDown( 250 );

			return this;
		},

		/**
		 * Hide the panel.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		close: function() {

			this.$el.slideUp( 150 );

			return this;
		},

		/**
		 * Display results as a grid.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		modeGrid: function() {

			this.mode = 'grid';
			this.$el.removeClass( 'extended' );

			return this;
		},

		/**
		 * Display results as an extended grid.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		modeList: function() {

			this.mode = 'list';
			this.$el.addClass( 'extended' );

			return this;
		}
	})
} );
