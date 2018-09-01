window.wpmoly = window.wpmoly || {};

( function( $, _, Backbone ) {

	/**
	 * Create a new Grid instance.
	 *
	 * @since    1.0.0
	 *
	 * @param    {Element}    grid Grid DOM element.
	 *
	 * @return   {object}     Grid instance.
	 */
	var Grid = wpmoly.Grid = function( grid, options ) {

		var options = options || {};

		// Set a unique grid ID to the grid element.
		grid.id  = _.uniqueId( 'wpmoly-grid-' );

		var post_id = grid.getAttribute( 'data-grid' ),
		     widget = grid.getAttribute( 'data-widget' ),
		     preset = grid.getAttribute( 'data-preset' ) || {};

		// Handle preset, if any.
		if ( _.isObject( preset ) ) {
			var presetName = grid.getAttribute( 'data-preset-name' ) || '',
			   presetValue = grid.getAttribute( 'data-preset-value' ) || '';
			if ( ! _.isEmpty( presetName ) && ! _.isEmpty( presetValue ) ) {
				preset[ presetName ] = presetValue;
			}
		}

		// Set grid model.
		var model = new wpmoly.api.models.Grid( { id : parseInt( post_id ) } );

		// Set grid items collection.
		var items = new Backbone.Collection,
		    query = new Grids.model.Query;

		// Set grid controller.
		var controller = controller = new Grids.controller.Grid({
			context : options.context || 'view',
			post_id : parseInt( post_id ),
			widget  : widget,
			preset  : preset,
		}, {
			grid  : model,
			items : items,
			query : query,
		});

		// Set grid view.
		var view = new Grids.view.Grid({
			el         : grid,
			controller : controller,
		});

		// Render the grid when items are ready.
		items.on( 'sync', view.render, view );

		// Load items when grid is ready.
		//model.on( 'sync', controller.fetch, controller );

		// Load grid.
		model.fetch({
			data : {
				context : options.context || 'view',
			}
		});

		/**
		 * Grid instance.
		 *
		 * Provide a set of useful functions to interact with the Grid
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var grid = {

			/**
			 * Grid ID.
			 *
			 * @since 1.0.0
			 *
			 * @var int
			 */
			id : parseInt( post_id ),

			/**
			 * Grid selector.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			selector : grid.id,

			/**
			 * Retrieve grid settings.
			 *
			 * If no specific property is passed, return the full
			 * settings array.
			 *
			 * @since 1.0.0
			 *
			 * @param {string} property Grid setting name.
			 *
			 * @return {mixed}
			 */
			get : function( attribute ) {

				if ( ! attribute ) {
					return model.toJSON();
				}

				return model.get( attribute );
			},

			/**
			 * Set grid settings.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} attributes Settings list.
			 *
			 * @return {mixed}
			 */
			set : function( attributes ) {

				return model.set( attributes );
			},

		};

		// This comes handy when editing the grid.
		if ( 'edit' === options.context ) {
			_.extend( grid, {
				controller : controller,
				model      : model,
				view       : view,
			} );
		}

		return grid;
	};

	/**
	 * Grids Wrapper.
	 *
	 * Store controllers, views and grids objects.
	 *
	 * @since 1.0.0
	 */
	var Grids = wpmoly.grids = {

		/**
		 * List of grid instances.
		 *
		 * This should not be used directly. Use Grids.get()
		 * instead.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		grids : [],

		/**
		 * List of grid models.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		model : {},

		/**
		 * List of grid controllers.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		controller : {},

		/**
		 * List of grid views.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		view : {},

		/**
		 * Retrieve Grid instances.
		 *
		 * Grids can have multiple instances.
		 *
		 * @since 1.0.0
		 *
		 * @param {int} selector Grid ID or selector.
		 *
		 * @return {array} List of Grid instances.
		 */
		get : function( selector ) {

			var grid = [];
			if ( _.isNumber( selector ) ) {
				grid = _.where( this.grids, { id : selector } ) || [];
			} else {
				grid = _.find( this.grids, { selector : selector } ) || [];
			}

			if ( 1 === grid.length ) {
				grid = _.first( grid );
			}

			return grid;
		},

		/**
		 * Add a Grid instance.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} grid Grid DOM Element.
		 * @param {object} options Grid options.
		 *
		 * @return {Grid} Grid instance.
		 */
		add : function( grid, options ) {

			var grid = new Grid( grid, options );

			this.grids.push( grid );

			return grid;
		},
	};

	Grids.model.Query = Backbone.Model.extend({

		defaults : {
			fields : [ 'genres', 'poster', 'rating', 'runtime', 'title', 'year' ],
		},

		/**
		 * Initialize the Model.
		 *
		 * @since    1.0.0
		 *
		 * @param    {object}    attributes
		 * @param    {object}    options
		 */
		initialize : function( attributes, options ) {

			this.on( 'change', this.prepareQuery, this );
		},

		/**
		 * Prepare query parameters.
		 *
		 * Remove the preset parameter when needed, ie. when other
		 * parameters are available.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		prepareQuery : function() {

			this.set( { fields : this.defaults.fields }, { silent : true } );

			if ( _.has( this.changed, 'preset' ) ) {
				return this;
			}

			if ( _.has( this.changed, 'order' ) ) {
				this.set( { orderby : 'name' }, { silent : true } );
			}

			this.set( { preset : '' }, { silent : true } );

			return this;
		},
	});

	Grids.controller.Grid = Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 *
		 * @since    1.0.0
		 *
		 * @param    {object}    attributes
		 * @param    {object}    options
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.grid  = options.grid;
			this.items = options.items;
			this.query = options.query;

			this.setDefaults();

			this.listenTo( this.grid, 'change:type', this.reset );
		},

		/**
		 * Set defaults attributes depending on grid type.
		 *
		 * Posts and Taxonomies don't support the same 'orderby' value
		 * and shouldn't be ordered the same way.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setDefaults : function() {

			var atts = {},
			  preset = this.get( 'preset' );

			if ( _.isString( preset ) ) {
				atts = { preset : preset };
			} else if ( _.isObject( preset ) ) {
				var key = _.first( _.keys( preset ) );
				atts[ key ] = preset[ key ];
			} else if ( this.isPost() ) {
				atts = {
					order   : 'desc',
					orderby : 'date',
				};
			} else if ( this.isTaxonomy() ) {
				atts = {
					order   : 'asc',
					orderby : 'name',
				};
			} else {
				return;
			}

			this.query.set( atts );

			return this;
		},

		/**
		 * Set item collection.
		 *
		 * Different collections can be used, depending on the grid type.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setCollection : function() {

			var type = this.grid.get( 'type' ),
			collections = {
				movie      : wpmoly.api.collections.Movies,
				actor      : wp.api.collections.Actors,
				collection : wp.api.collections.Collections,
				genre      : wp.api.collections.Genres,
			};

			if ( ! _.has( collections, type ) || _.isUndefined( collections[ type ] ) ) {
				return wpmoly.error( 'missing-api-collection', wpmolyL10n.api.missing_collection );
			}

			this.collection = new collections[ type ];

			this.mirrorCollection();

			return this;
		},

		/**
		 * Mirror collection events and changes.
		 *
		 * The item collection must remain unchanged to avoid breaking
		 * events so we use a concealed collection to fetch items and
		 * populate the public collection.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		mirrorCollection : function() {

			this.stopListening( this.collection );

			this.listenTo( this.collection, 'request', function( collection, xhr, options ) {
				this.items.trigger( 'request', collection, xhr, options );
			} );

			this.listenTo( this.collection, 'reset', function( collection, xhr, options ) {
				this.items.trigger( 'reset', collection, xhr, options );
			} );

			this.listenTo( this.collection, 'add', function( model, collection, options ) {
				this.items.trigger( 'add', model, collection, options );
			} );

			this.listenTo( this.collection, 'remove', function( model, collection, options ) {
				this.items.trigger( 'remove', model, collection, options );
			} );

			this.listenTo( this.collection, 'update', function( collection, options ) {
				this.items.trigger( 'update', collection, options );
			} );

			this.listenTo( this.collection, 'error', function( collection, response, options ) {
				this.items.trigger( 'error', collection, response, options );
			} );

			this.listenTo( this.collection, 'sync', function( collection, response, options ) {
				this.items.trigger( 'sync', collection, response, options );
			} );

			return this;
		},

		/**
		 * Load grid items.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		fetch : function() {

			if ( _.isUndefined( this.collection ) ) {
				this.setCollection();
			}

			var data = this.query.toJSON() || {},
			    rows = 'list' === this.grid.get( 'list' ) ? this.grid.get( 'list_rows' ) : this.grid.get( 'rows' ),
			 columns = 'list' === this.grid.get( 'list' ) ? this.grid.get( 'list_columns' ) : this.grid.get( 'columns' );
			if ( ! data.per_page ) {
				data.per_page = Math.round( rows * columns ) || 20;
			}

			this.collection.fetch( { data : data } );

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

			var data = _.extend( this.query.toJSON(), {
				page : page,
			} );

			this.collection.fetch( { data : data } );

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

			return parseInt( this.collection.state.currentPage ) || 1;
		},

		/**
		 * Retrieve the total number of pages.
		 *
		 * @since 1.0.0
		 *
		 * @return {int}
		 */
		getTotalPages : function() {

			return parseInt( this.collection.state.totalPages ) || 1;
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
		 * Are we dealing with taxonomies?
		 *
		 * @since    1.0.0
		 *
		 * @return   {boolean}
		 */
		isTaxonomy : function() {

			return _.contains( [ 'actor', 'genre' ], this.grid.get( 'type' ) );
		},

		/**
		 * Are we dealing with posts?
		 *
		 * @since    1.0.0
		 *
		 * @return   {boolean}
		 */
		isPost : function() {

			return _.contains( [ 'movie' ], this.grid.get( 'type' ) );
		},

		reset : function() {

			this.setCollection();
			this.setDefaults();

			this.fetch();
		},

	});

	Grids.view.Settings = wp.Backbone.View.extend({

		className : 'grid-settings-inner',

		template : wp.template( 'wpmoly-grid-settings' ),

		events : {
			'change [data-setting-type]'  : 'change',
			'click [data-action="apply"]' : 'apply',
		},

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.listenTo( this.controller.grid,  'change', this.render );
			this.listenTo( this.controller.query, 'change', this.render );
		},

		/**
		 * Apply changes to query model.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		change : function( event ) {

			var $target = this.$( event.currentTarget ),
			    setting = $target.attr( 'data-setting-type' ),
			      value = $target.val();

			this.controller.query.set( setting, value );

			return this;
		},

		/**
		 * Apply changed settings.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		apply : function() {

			this.controller.fetch();

			this.views.parent.$el.removeClass( 'edit-settings' );

			return this;
		},

		/**
		 * Prepare template data.
		 *
		 * @since 1.0.0
		 *
		 * @return {object}    options
		 */
		prepare : function() {

			var query = this.controller.query.toJSON(),
			  grid_id = this.controller.get( 'post_id' ),
			  options = {
				grid_id : _.uniqueId( 'wpmoly-grid-' + grid_id ),
				query   : query,
			};

			_.extend( options, this.controller.grid.toJSON() || {} );

			return options;
		},

	});

	Grids.view.Menu = wp.Backbone.View.extend({

		className : 'grid-menu-inner',

		template : wp.template( 'wpmoly-grid-menu' ),

		events : {
			'click [data-action="toggle-settings"]' : 'toggleSettings',
			'click [data-action="toggle-customs"]'  : 'toggleCustoms',
		},

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.listenTo( this.controller.grid, 'change:settings_control', this.render );
			this.listenTo( this.controller.grid, 'change:custom_control',   this.render );
		},

		/**
		 * .
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		toggleSettings : function() {

			var $parent = this.views.parent.$el;

			$parent.removeClass( 'edit-customs' );
			$parent.toggleClass( 'edit-settings' );

			return this;
		},

		/**
		 * .
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		toggleCustoms : function() {

			var $parent = this.views.parent.$el;

			$parent.removeClass( 'edit-settings' );
			$parent.toggleClass( 'edit-customs' );

			return this;
		},

		/**
		 * Prepare template data.
		 *
		 * @since    1.0.0
		 *
		 * @return   {object}    options
		 */
		prepare : function() {

			var options = {
				settings_control : this.controller.grid.get( 'settings_control' ),
				custom_control   : this.controller.grid.get( 'custom_control' ),
			};

			return options;
		},

	});

	Grids.view.Pagination = wp.Backbone.View.extend({

		className : 'grid-menu-inner',

		template : wp.template( 'wpmoly-grid-pagination' ),

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
		 * @param {object} options
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.listenTo( this.controller.items, 'update', this.render );
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
		 * Prepare template data.
		 *
		 * @since 1.0.0
		 *
		 * @return {object} options
		 */
		prepare : function() {

			var options = {
				current : this.controller.getCurrentPage(),
				total   : this.controller.getTotalPages()
			};

			return options;
		},

	});

	Grids.view.Item = wp.Backbone.View.extend({

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options
		 */
		initialize : function( options ) {

			this.model      = options.model;
			this.controller = options.controller;

			this.template = this.setTemplate();

			this.listenTo( this.controller.grid, 'change:mode change:theme', this.render );

			this.on( 'prepare', this.setTemplate );
			this.on( 'prepare', this.setClassName );
		},

		/**
		 * Set the View template based on settings.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setTemplate : function() {

			var type = this.controller.grid.get( 'type' ),
			    mode = this.controller.grid.get( 'mode' ),
			   theme = this.controller.grid.get( 'theme' ),
			template = 'wpmoly-grid-' + type + '-' + mode;

			if ( theme && 'default' !== theme ) {
				template += '-' + theme;
			}

			this.template = wp.template( template );

			return this;
		},

		/**
		 * Set $el class names depending on settings.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setClassName : function() {

			var type = this.controller.grid.get( 'type' ),
			className = [ 'item' ];

			if ( 'series' === type ) {
				className.push( 'post-item' );
			} else if ( _.contains( [ 'actor', 'genre' ], type ) ) {
				className.push( 'term-item' );
			}

			className.push( type );

			this.className = className.join( ' ' );

			this.$el.addClass( this.className );

			return this;
		},

		/**
		 * Prepare template data.
		 *
		 * @since 1.0.0
		 *
		 * @return {object} options
		 */
		prepare : function() {

			var options = _.extend( this.model.toJSON() || {}, {
				uid  : _.uniqueId( this.model.get( 'id' ) ),
				grid : this.controller.grid.toJSON() || {},
			} );

			return options;
		},

	});

	Grids.view.Content = wp.Backbone.View.extend({

		className : 'grid-content-inner',

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options
		 */
		initialize : function( options ) {

			this.on( 'ready', this.adjust );

			this.controller = options.controller;
			this.grid  = this.controller.grid;
			this.items = this.controller.items;

			this.listenTo( this.grid, 'change:columns',      _.debounce( _.bind( this.adjust, this ), 50 ) );
			this.listenTo( this.grid, 'change:list_columns', _.debounce( _.bind( this.adjust, this ), 50 ) );
			this.listenTo( this.grid, 'change:theme',        _.debounce( _.bind( this.adjust, this ), 50 ) );

			this.listenTo( this.items, 'request', this.loading );
			this.listenTo( this.items, 'sync',    this.loaded );
			this.listenTo( this.items, 'error',   this.loaded );
			this.listenTo( this.items, 'sync',    this.addItems );
		},

		/**
		 * .
		 *
		 * @since 1.0.0
		 *
		 * @param {object} collection
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		addItems : function( collection, options ) {

			_.each( collection.models, function( model ) {
				var view = new Grids.view.Item({
					model      : model,
					controller : this.controller,
				});
				this.views.add( view );
			}, this );

			return this;
		},

		/**
		 * .
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		loading : function() {

			_.invoke( this.views.all(), 'remove' );

			this.$el.addClass( 'loading' );

			return this;
		},

		/**
		 * .
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
		 * Adjust items size.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		adjust : function() {

			var  ratio = this.controller.isPost() ? 1.5 : 1.25;
			innerWidth = this.$el.innerWidth();

			if ( 'list' === this.controller.grid.get( 'mode' ) ) {
				var columns = this.controller.grid.get( 'list_columns' );
			} else {
				var columns = this.controller.grid.get( 'columns' );
			}

			var width = Math.floor( ( innerWidth / columns ) - 8 );
			if ( 200 < width ) {
				columns = Math.ceil( innerWidth / 200 );
				width = Math.floor( ( innerWidth / columns ) - 8 );
			}

			var height = Math.floor( width * ratio );

			this.$el.attr( 'data-columns', columns );

			this.$( '.item' ).css({
				width : width,
			});

			this.$( '.item-thumbnail' ).css({
				width  : width,
				height : height,
			});

			return this;
		},

	});

	Grids.view.Grid = wp.Backbone.View.extend({

		template : wp.template( 'wpmoly-grid' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options
		 */
		initialize : function( options ) {

			this.controller = options.controller;
			this.grid       = this.controller.grid

			// Set subviews.
			this.setRegions();

			// Change Theme.
			this.listenTo( this.grid, 'change:theme', this.changeTheme );

			// Prepare $el.
			this.on( 'prepare', this.setClassName );
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

			this.settings   = new Grids.view.Settings( options );
			this.pagination = new Grids.view.Pagination( options );
			this.menu       = new Grids.view.Menu( options );
			this.content    = new Grids.view.Content( options );

			this.views.set( '.grid-settings', this.settings );
			this.views.set( '.grid-menu.settings-menu', this.menu );
			this.views.set( '.grid-menu.pagination-menu', this.pagination );
			this.views.set( '.grid-content', this.content );

			return this;
		},

		/**
		 * Update the grid classes on theme change.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		changeTheme : function( model, options ) {

			this.$el.removeClass( 'theme-' + model.previous( 'theme' ) );

			this.$el.addClass( 'theme-' + model.get( 'theme' ) );

			return this;
		},

		/**
		 * Set $el class names depending on grid settings.
		 *
		 * @since    1.0.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		setClassName : function() {

			var grid = this.controller.grid,
			className = [ 'wpmoly' ];

			className.push( grid.get( 'type' ) );
			className.push( grid.get( 'mode' ) );

			if ( ! _.isEmpty( grid.get( 'theme' ) ) ) {
				className.push( 'theme-' + grid.get( 'theme' ) );
			}

			this.className = className.join( ' ' );

			this.$el.addClass( this.className );

			return this;
		},

	});

	/**
	 * Run Forrest, run!
	 *
	 * Load the REST API Backbone client before loading all Grids.
	 *
	 * @see wp.api.loadPromise.done()
	 *
	 * @since    1.0.0
	 */
	Grids.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			return _.map(
				document.querySelectorAll( '[data-grid]' ),
				Grids.add,
				Grids
			);
		} );

		return Grids;
	};

} )( jQuery, _, Backbone );

wpmoly.runners['grids'] = wpmoly.grids;
