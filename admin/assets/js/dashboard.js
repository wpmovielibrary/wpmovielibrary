wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new Board instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} board Board DOM element.
	 *
	 * @return {object} Board instance.
	 */
	var Board = function( board ) {

		var view = new Dashboard.view.Dashboard( { el : board } );

		/**
		 * Board instance.
		 *
		 * Provide a set of useful functions to interact with the board
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var board = {

			view : view,

		};

		return board;
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

		var view = new Dashboard.view.Sidebar( { el : sidebar } );

		/**
		 * Sidebar instance.
		 *
		 * Provide a set of useful functions to interact with the sidebar
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var sidebar = {

			view : view,

		};

		return this;
	};

	/**
	 * Create a new Library instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} library Library DOM Element.
	 *
	 * @return {object} Library instance.
	 */
	var Library = function( library ) {

		var view = new Dashboard.view.Library;

		/**
		 * Library instance.
		 *
		 * Provide a set of useful functions to interact with the library
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var library = {

			view : view,

		};

		return library;
	};

	/**
	 * Create a new Grids Dashboard instance.
	 *
	 * @since 1.0.0
	 *
	 * @return {object} Grids instance.
	 */
	var Grids = function() {

		var view = new Dashboard.view.Grids;

		/**
		 * Grids instance.
		 *
		 * Provide a set of useful functions to interact with the grids
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var grids = {

			view : view,

		};

		return grids;
	};

	/**
	 * Create a new Actors Dashboard instance.
	 *
	 * @since 1.0.0
	 *
	 * @return {object} Actors instance.
	 */
	var Actors = function() {

		var view = new Dashboard.view.Actors;

		/**
		 * Actors instance.
		 *
		 * Provide a set of useful functions to interact with the actors
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var actors = {

			view : view,

		};

		return actors;
	};

	/**
	 * Create a new Genres Dashboard instance.
	 *
	 * @since 1.0.0
	 *
	 * @return {object} Genres instance.
	 */
	var Genres = function() {

		var view = new Dashboard.view.Genres;

		/**
		 * Genres instance.
		 *
		 * Provide a set of useful functions to interact with the genres
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var genres = {

			view : view,

		};

		return genres;
	};

	/**
	 * Create a new Settings Editor instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} editor Settings Editor DOM Element.
	 *
	 * @return {object} SettingsEditor instance.
	 */
	var SettingsEditor = function( editor ) {

		var settings = new wp.api.models.Settings,
			    schema = new wpmoly.api.collections.SettingsSchema;

		// Hide loading animation.
		settings.once( 'sync error', function() {
			wpmoly.$( '.wpmoly-container' ).removeClass( 'loading' );
		} );

		// Load settings.
		settings.fetch({
			success : function() {
				// If settings loaded correctly, load schema.
				schema.fetch({
					error : function( model, xhr, options ) {
						wpmoly.error( xhr, _.extend( options, {
							debug   : wpmolyApiL10n.settings.schema_error,
							destroy : false,
						} ) );
					},
				});
			},
			error : function( model, xhr, options ) {
				wpmoly.error( xhr, _.extend( options, {
					debug   : wpmolyApiL10n.settings.loading_error,
					destroy : false,
				} ) );
			},
		});

		var controller = new Dashboard.controller.SettingsEditor( [], {
			schema   : schema,
			settings : settings,
		});

		var view = new Dashboard.view.SettingsEditor({
			el         : editor,
			controller : controller,
		});

		var editor = {

			controller : controller,

			view : view,

		};

		return editor;
	};

	/**
	 * Dashboard wrapper.
	 *
	 * @since 1.0.0
	 */
	var Dashboard = wpmoly.dashboard = {

		/**
		 * List of dashboard models.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		model : {},

		/**
		 * List of dashboard controllers.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		controller : {},

		/**
		 * List of dashboard views.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		view : {},
	};

	Dashboard.controller.SettingsEditor = Backbone.Model.extend({

		defaults : {
			mode : 'browse',
			page : '',
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

			this.schema   = options.schema;
			this.settings = options.settings;

			this.on( 'change:page', this.updateHistory, this );

			this.listenTo( this.schema, 'update', this.updatePage );
		},

		/**
		 * Set default active page. If a page slug is passed in URL hash
		 * use it as default value.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		forceCurrentPage : function() {

			var hash = document.location.hash;
			if ( _.isEmpty( hash ) ) {
				return hash;
			}

			return hash.replace( /^\#(\w+)$/, '$1' );
		},

		/**
		 * Search settings matching a specific query.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} query Search query.
		 *
		 * @return Returns itself to allow chaining.
		 */
		searchSettings : function( query ) {

			this.trigger( 'search:close' );

			var query = query.toUpperCase(),
			  results = [];

			if ( _.isString( query ) && 2 < query.length ) {
				this.schema.each( function( field ) {

					var title = field.get( 'title' ) || '',
					description = field.get( 'description' ) || '',
					group, page;

					if ( -1 === title.toUpperCase().indexOf( query ) && -1 === description.toUpperCase().indexOf( query ) ) {
						return;
					}

					// Show setting groups too.
					if ( field.has( 'group' ) ) {
						group = this.schema.find({
							name : field.get( 'group' ),
							type : 'group',
						});
						results.push( group.cid );
					}

					// Also show setting groups and pages.
					if ( group || field.has( 'page' ) ) {
						page = this.schema.find({
							name : field.get( 'page' ) || group.get( 'page' ),
							type : 'page',
						});
						results.push( page.cid );
					}

					results.push( field.cid );

				}, this );
			}

			this.trigger( 'search:results', results );

			return this;
		},

		/**
		 * Update page history.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model Model object.
		 * @param {object} value Changed values.
		 * @param {object} options Options.
		 *
		 * @return Returns itself to allow chaining.
		 */
		updateHistory : function( model, value, options ) {

			window.history.replaceState( { page : 'settings' }, '', '#' + value );

			return this;
		},

		/**
		 * Update current group.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} collection Collection object.
		 * @param {object} options Options.
		 *
		 * @return Returns itself to allow chaining.
		 */
		updatePage : function( collection, options ) {

			if ( '' !== this.get( 'page' ) ) {
				return this;
			}

			this.set( { page : this.forceCurrentPage() || _.first( collection.where( { type : 'page' } ) ).get( 'name' ) } );

			return this;
		},

		/**
		 * Save a group of settings.
		 *
		 * Use a temporary settings model to save the settings from
		 * a specific group.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} group Settings group name.
		 *
		 * @return Returns itself to allow chaining.
		 */
		saveGroup : function( group ) {

			var fields = _.where( this.schema.toJSON(), { group : group } ),
			  settings = _.pick( this.settings.toJSON(), _.pluck( fields, 'name' ) )
			attributes = {}
			      self = this;

			_.each( settings, function( value, key ) {
				attributes[ wpmolyApiSettings.option_prefix + key ] = value;
			} );

			// Set temporary settings model.
			settings = new wp.api.models.Settings( attributes );

			// Save settings and destroy model.
			settings.save( [], {
				patch : true,
				beforeSend : function() {
					// Keep track of progress toast.
					wpmoly.info( wpmolyApiL10n.settings.saving );
				},
				success : function( model, xhr, options ) {
					// Notify success.
					wpmoly.success( wpmolyApiL10n.settings.saved );
					// Refresh settings.
					self.settings.fetch();
				},
				error : function( model, xhr, options ) {
					// Extend options.
					options.destroy = false;
					options.debug = wpmolyApiL10n.settings.saving_error;
					// Notify error.
					wpmoly.error( xhr, options );
				},
			} );
			settings.destroy();

			return this;
		},
	});

	/**
	 * Dashboard View.
	 *
	 * @since 1.0.0
	 */
	Dashboard.view.Dashboard = wpmoly.Backbone.View.extend({



	});

	/**
	 * Dashboard Sidebar View.
	 *
	 * @since 1.0.0
	 */
	Dashboard.view.Block = wpmoly.Backbone.View.extend({

		events : function() {
			return {
				'click [data-action="open"]'  : 'open',
				'click [data-action="close"]' : 'close',
			};
		},

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options View options.
		 */
		initialize : function( options ) {

			this.render();
		},

		/**
		 * Open the Block.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		open : function() {

			this.$el.removeClass( 'collapsed' );
			this.$( '.button' ).attr( 'data-action', 'close' );
			this.$( '.button .wpmolicon' ).addClass( 'icon-up-chevron' );
			this.$( '.button .wpmolicon' ).removeClass( 'icon-down-chevron' );

			return this;
		},

		/**
		 * Close the Block.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		close : function() {

			this.$el.addClass( 'collapsed' );
			this.$( '.button' ).attr( 'data-action', 'open' );
			this.$( '.button .wpmolicon' ).addClass( 'icon-down-chevron' );
			this.$( '.button .wpmolicon' ).removeClass( 'icon-up-chevron' );

			return this;
		},

		/**
		 * Render the View.
		 *
		 * @since 1.0.0
		 *
		 * @return {object}
		 */
		render : function() {

			if ( 'function' !== typeof this.template ) {
				return wpmoly.Backbone.View.prototype.render.apply( this, arguments );
			}

			this.trigger( 'render', options );

			var options = this.prepare(),
			   template = this.template( options );

			this.$( '.block-content' ).html( template );

			this.trigger( 'rendered', options );

			return this;
		},
	});

	/**
	 * Dashboard Sidebar View.
	 *
	 * @since 1.0.0
	 */
	Dashboard.view.Sidebar = wpmoly.Backbone.View.extend({

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.setBlocks();
		},

		/**
		 * Set the Blocks Views.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setBlocks : function() {

			var $blocks = this.$( '.dashboard-block' );
			_.each( $blocks, function( block ) {
				this.views.add( new Dashboard.view.Block( { el : block } ) );
			}, this );

			return this;
		},
	});

	/**
	 * Dashboard Grids View.
	 *
	 * @since 1.0.0
	 */
	Dashboard.view.Grids = wpmoly.Backbone.View.extend({

		id : 'wpmoly-grids',



	});

	/**
	 * Dashboard Actors View.
	 *
	 * @since 1.0.0
	 */
	Dashboard.view.Actors = wpmoly.Backbone.View.extend({

		id : 'wpmoly-actors',



	});

	/**
	 * Dashboard Genres View.
	 *
	 * @since 1.0.0
	 */
	Dashboard.view.Genres = wpmoly.Backbone.View.extend({

		id : 'wpmoly-genres',



	});

	/**
	 * Dashboard Settings Menu View.
	 *
	 * @since 1.0.0
	 */
	Dashboard.view.SettingsMenu = wpmoly.Backbone.View.extend({

		className : 'settings-menu',

		template : wp.template( 'wpmoly-settings-menu' ),

		events : {
			'click [data-action="browse"]'         : 'browse',
			'click [data-action="open-menu"]'      : 'openMenu',
			'click [data-action="open-search"]'    : 'openSearch',
			'click [data-action="start-search"]'   : 'startSearch',
			'click [data-action="close-search"]'   : 'closeSearch',
			'keypress [data-value="search-query"]' : 'startSearch',
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
			this.schema   = this.controller.schema;

			this.listenTo( this.schema,     'change',      this.render );
			this.listenTo( this.controller, 'change:page', this.render );
		},

		/**
		 * Open responsive menu.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		openMenu : function() {

			this.$el.toggleClass( 'opened' );

			return this;
		},

		/**
		 * Browse settings pages.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		browse : function( event ) {

			event.preventDefault();

			this.$el.removeClass( 'opened' );

			var target = this.$( event.currentTarget ),
			      page = target.data( 'page' );

			this.controller.set( { page : page } );

			return this;
		},

		/**
		 * Open the search menu.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		openSearch : function( event ) {

			event.preventDefault();

			this.$el.removeClass( 'opened' );
			this.$el.addClass( 'search-opened' );
			this.$( '.search-input' ).focus();

			return this;
		},

		/**
		 * Open the search menu.
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

			this.controller.searchSettings( query );

			return this;
		},

		/**
		 * Open the search menu.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		closeSearch : function( event ) {

			event.preventDefault();

			this.$el.removeClass( 'opened' );
			this.$el.removeClass( 'search-opened' );
			this.$( '[data-value="search-query"]' ).val( '' );

			this.controller.trigger( 'search:close' );

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

			var schema = this.schema.toJSON(),
			   options = {
				pages : _.where( schema, { type : 'page' } ),
				page  : this.controller.get( 'page' ),
			};

			return options;
		},

	});

	Dashboard.view.SettingsField = wpmoly.Backbone.View.extend({

		className : 'setting',

		template : wp.template( 'wpmoly-settings-field' ),

		events : {
			'change [data-setting]'                  : 'changeField',
			'click [data-action="toggle"]'           : 'toggleField',
			'click [data-action="show-description"]' : 'showDescription',
			'click [data-action="hide-description"]' : 'hideDescription',
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

			this.model      = options.model;
			this.controller = options.controller;

			this.schema   = this.controller.schema;
			this.settings = this.controller.settings;

			this.listenTo( this.controller, 'validate:' + this.model.get( 'name' ), this.validateField );
			this.listenTo( this.controller, 'search:results', this.showSearchResults );
		},

		/**
		 * Update settings on field change.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'change' event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		changeField : function( event ) {

			var target = event.currentTarget;
			if ( 'checkbox' === target.type ) {
				value = this.$( target ).is( ':checked' );
			} else {
				value = this.$( target ).val();
			}

			// Clean validation style.
			this.$( target ).removeClass( 'invalid valid' );

			// Retrieve setting value.
			var setting = this.$( target ).data( 'setting' );

			// Set new value.
			this.settings.set( setting, value );

			// Don't validate empty values.
			if ( _.isEmpty( value ) ) {
				return this;
			}

			return this;
		},

		/**
		 * Validate a changed settings value.
		 *
		 * @since 1.0.0
		 *
		 * @param {boolean} valid Value is valid?
		 *
		 * @return Returns itself to allow chaining.
		 */
		validateField : function( valid ) {

			var $target = this.$( '[data-setting="' + this.model.get( 'name' ) + '"]' );
			if ( false === valid ) {
				$target.addClass( 'invalid' );
			} else if ( true === valid ) {
				$target.addClass( 'valid' );
			}

			return this;
		},

		/**
		 * Toggle checkbox fields.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		toggleField : function() {

			this.$el.toggleClass( 'checked' );

			var settings = {},
			    children = this.schema.where( { parent : this.model.get( 'name' ) } );
			if ( children.length ) {
				var checked = this.$el.hasClass( 'checked' );
				this.$el.nextAll( '[data-parent="' + this.model.get( 'name' ) + '"]' ).toggleClass( 'checked', checked );
				this.$el.nextAll( '[data-parent="' + this.model.get( 'name' ) + '"]' ).find( 'input[type="checkbox"]' ).prop( 'checked', checked );
				_.each( children, function( field ) {
					settings[ wpmolyApiSettings.option_prefix + field.get( 'name' ) ] = checked;
				}, this );
			}

			this.settings.set( settings );

			return this;
		},

		/**
		 * Show the description box.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		showDescription : function() {

			this.$( '.setting-description' ).slideDown();
			this.$( '[data-action="show-description"]' ).addClass( 'active' );
			this.$( '[data-action="show-description"]' ).attr( 'data-action', 'hide-description' );
		},

		/**
		 * Hide the description box.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		hideDescription : function() {

			this.$( '.setting-description' ).slideUp();
			this.$( '[data-action="hide-description"]' ).removeClass( 'active' );
			this.$( '[data-action="hide-description"]' ).attr( 'data-action', 'show-description' );
		},

		/**
		 * Show settings search results.
		 *
		 * @since 1.0.0
		 *
		 * @param array results List of model IDs matching the search query.
		 *
		 * @return Returns itself to allow chaining.
		 */
		showSearchResults : function( results ) {

			if ( _.contains( results, this.model.cid ) ) {
				this.$el.addClass( 'search-result' );
			}

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

			var name = this.model.get( 'name' ),
			 options = this.model.toJSON()

			var key = wpmolyApiSettings.option_prefix + name;
			if ( this.settings.has( key ) && ! _.isNull( this.settings.get( key ) ) ) {
				options.value = this.settings.get( key );
			} else {
				this.model.get( 'default' );
			}

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

			wpmoly.Backbone.View.prototype.render.apply( this, arguments );

			this.$el.addClass( this.model.get( 'type' ) );
			this.$el.prop( 'id', this.model.get( 'name' ) );

			if ( this.schema.where( { parent : this.model.get( 'name' ) } ).length ) {
				this.$el.addClass( 'parent has-children' );
			}

			if ( this.model.has( 'parent' ) ) {
				this.$el.addClass( 'children has-parent' );
				this.$el.attr( 'data-parent', this.model.get( 'parent' ) );
			}

			if ( this.model.has( 'options' ) ) {
				this.$( 'select' ).selectize( { highlight : false } );
			}

			if ( 'boolean' === this.model.get( 'type' ) && true === this.settings.get( wpmolyApiSettings.option_prefix + this.model.get( 'name' ) ) ) {
				this.$el.addClass( 'checked' );
			}

			return this;
		},
	});

	Dashboard.view.SettingsGroup = wpmoly.Backbone.View.extend({

		className : 'settings-group',

		template : wp.template( 'wpmoly-settings-group' ),

		events : {
			'click [data-action="save"]'     : 'saveGroup',
			'click [data-action="collapse"]' : 'toggleContent',
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

			this.model      = options.model;
			this.controller = options.controller;

			this.schema   = this.controller.schema;

			this.listenTo( this.controller, 'search:results', this.showSearchResults );

			this.setFields();
		},

		/**
		 * Set settings groups.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setFields : function() {

			this.fields = this.schema.where( { group : this.model.get( 'name' ) } );

			_.each( this.fields, function( field ) {
				this.views.add( '.group-content', new Dashboard.view.SettingsField({
					controller : this.controller,
					model      : field,
				}) );
			}, this );

			return this;
		},

		toggleContent : function() {

			this.$el.toggleClass( 'collapsed' );
		},

		/**
		 * Save group settings.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		saveGroup : function() {

			this.controller.saveGroup( this.model.get( 'name' ) );

			return this;
		},

		/**
		 * Show settings search results.
		 *
		 * @since 1.0.0
		 *
		 * @param array results List of model IDs matching the search query.
		 *
		 * @return Returns itself to allow chaining.
		 */
		showSearchResults : function( results ) {

			if ( _.contains( results, this.model.cid ) ) {
				this.$el.addClass( 'search-result' );
			}

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

			return this.model.toJSON();
		},

	});

	Dashboard.view.SettingsPage = wpmoly.Backbone.View.extend({

		className : 'settings-page',

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			var options = options || {};

			this.model      = options.model;
			this.controller = options.controller;

			this.schema = this.controller.schema;
			this.settings = this.controller.settings;

			this.listenTo( this.controller, 'search:results', this.showSearchResults );

			this.setGroups();
		},

		/**
		 * Set settings groups.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setGroups : function() {

			this.groups = this.schema.where({
				page : this.model.get( 'name' ),
				type : 'group',
			});

			_.each( this.groups, function( group ) {
				this.views.add( new Dashboard.view.SettingsGroup({
					controller : this.controller,
					model      : group,
				}) );
			}, this );

			return this;
		},

		/**
		 * Show settings search results.
		 *
		 * @since 1.0.0
		 *
		 * @param array results List of model IDs matching the search query.
		 *
		 * @return Returns itself to allow chaining.
		 */
		showSearchResults : function( results ) {

			if ( _.contains( results, this.model.cid ) ) {
				this.$el.addClass( 'search-result' );
			}

			return this;
		},

		/**
		 * Render the View.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		render : function() {

			wpmoly.Backbone.View.prototype.render.apply( this, arguments );

			this.$el.addClass( this.model.get( 'name' ) + '-settings' );
			this.$el.attr( 'data-page', this.model.get( 'name' ) );

			return this;
		},

	});

	/**
	 * Dashboard Settings Editor View.
	 *
	 * @since 1.0.0
	 */
	Dashboard.view.SettingsEditor = wpmoly.Backbone.View.extend({

		id : 'wpmoly-settings',

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
			this.schema   = this.controller.schema;
			this.settings = this.controller.settings;

			this.setRegions();

			this.listenTo( this.schema, 'update', this.setPages );
			this.listenTo( this.controller, 'change:page',    this.updateCurrentPage );
			this.listenTo( this.controller, 'search:results', this.showSearchResults );
			this.listenTo( this.controller, 'search:close',   this.removeSearchResults );
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

			if ( ! this.menu ) {
				this.menu = new Dashboard.view.SettingsMenu( options );
			}

			this.views.add( this.menu );

			return this;
		},

		/**
		 * Set setting pages.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} collection Collection object.
		 * @param {object} options Options.
		 *
		 * @return Returns itself to allow chaining.
		 */
		setPages : function( collection, options ) {

			_.each( collection.where( { type : 'page' } ), function( page ) {
				this.views.add( new Dashboard.view.SettingsPage({
					controller : this.controller,
					model      : page,
				}) );
			}, this );

			this.updateCurrentPage();

			return this;
		},

		/**
		 * Hide all settings pages but the current one.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		updateCurrentPage : function() {

			this.$( '.settings-page' ).hide();
			this.$( '[data-page="' + this.controller.get( 'page' ) + '"]' ).show();

			return this;
		},

		/**
		 * Show settings search results.
		 *
		 * @since 1.0.0
		 *
		 * @param array results List of model IDs matching the search query.
		 *
		 * @return Returns itself to allow chaining.
		 */
		showSearchResults : function( results ) {

			this.$el.addClass( 'search-results' );

			return this;
		},

		/**
		 * Remove settings search results.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeSearchResults : function() {

			this.$el.removeClass( 'search-results' );

			this.$( '.search-result' ).removeClass( 'search-result' );

			return this;
		},

	});

	/**
	 * Create sidebar instance.
	 *
	 * @since 1.0.0
	 */
	Dashboard.loadSidebar = function() {

		var sidebar = document.querySelector( '#wpmoly-dashboard-sidebar' );
		if ( sidebar ) {
			Dashboard.sidebar = new Sidebar( sidebar );
		}
	};

	/**
	 * Create library instance.
	 *
	 * @since 1.0.0
	 */
	Dashboard.loadLibrary = function() {

		var library = document.querySelector( '#wpmoly-library' );
		if ( library ) {
			Dashboard.library = new Library( library );
		}
	};

	/**
	 * Create settings editor instance.
	 *
	 * @since 1.0.0
	 */
	Dashboard.loadSettings = function() {

		var editor = document.querySelector( '#wpmoly-settings' );
		if ( editor ) {
			Dashboard.settings = new SettingsEditor( editor );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 1.0.0
	 */
	Dashboard.run = function() {

		if ( '?page=wpmovielibrary' === window.location.search ) {
			window.location.href += '-movies';
		}

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			Dashboard.loadSidebar();
			Dashboard.loadLibrary();
			Dashboard.loadSettings();
		} );

		return Dashboard;
	};

})( jQuery, _, Backbone );

wpmoly.runners['dashboard'] = wpmoly.dashboard;
