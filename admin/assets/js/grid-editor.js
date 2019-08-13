wpmoly = window.wpmoly || {};

wpmoly.grids = wpmoly.grids || {};

wpmoly.editor = wpmoly.editor || {};

(function( $, _, Backbone ) {

	var Dashboard = wpmoly.dashboard;

	/**
	 * Create a new Grid Preview instance.
	 *
	 * @since 1.0.0
	 *
	 * @return {object} Preview instance.
	 */
	var Preview = function() {

		var grid = document.querySelector( '[data-preview-grid]' );
		if ( ! grid ) {
			return;
		}

		grid.setAttribute( 'data-grid', grid.getAttribute( 'data-preview-grid' ) );
		grid.removeAttribute( 'data-preview-grid' );

		var grid = wpmoly.grids.add( grid, { context : 'edit' } );

		return grid;
	};

	/**
	 * Create a new GridEditor Editor instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} editor GridEditor Editor DOM element.
	 *
	 * @return {object} Editor instance.
	 */
	var Editor = function( editor ) {

		var post_id = parseInt( wpmoly.$( '#object_ID' ).val() ),
		    $parent = wpmoly.$( '#wpmoly-editor' );

		// Set editor models.
		var post = new wp.api.models.Grids( { id : post_id } ),
		    node = new wpmoly.api.models.Grid( { id : post_id } );

		// Set editor controller.
		var controller = new GridEditor.controller.Editor( [], {
			post : post,
			node : node,
		} );

		// Set editor view.
		var view = new GridEditor.view.Editor({
			el         : editor,
			controller : controller,
		});

		view.$el.addClass( 'post-editor grid-editor' );

		// Set grid preview.
		view.on( 'ready', function() {
			controller.preview = new Preview();
		} );

		// Render editor view.
		post.once( 'sync', function() {
			view.render();
		} );

		// Set editor view's regions.
		node.once( 'sync', function() {
			// Hide loading animation.
			$parent.removeClass( 'loading' );
			// Render the editor.
			view.setRegions();
		} );

		// Load grid.
		post.fetch( { data : { context : 'edit' } } );
		node.fetch( { data : { context : 'edit' } } );

		/**
		 * Editor instance.
		 *
		 * Provide a set of useful functions to interact with the editor
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var editor = {

			post : post,

			node : node,

			controller : controller,

			view : view,

		};

		return editor;
	};

	var PostEditor = wpmoly.editor.post;

	var GridEditor = wpmoly.editor.grid = _.extend( PostEditor, {

		controller : _.extend( PostEditor.controller, {

			/**
			 * GridEditor 'Submit' Block Controller.
			 *
			 * @since 3.0.0
			 */
			SubmitBlock : PostEditor.controller.SubmitBlock.extend({

				/**
				 * Update the node.
				 *
				 * @since 3.0.0
				 *
				 * @return xhr
				 */
				save : function() {

					return GridEditor.editor.controller.save();
				},
			}),

			Editor : Backbone.Model.extend({

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

					this.node = options.node;
					this.post = options.post;

					this.listenTo( this.post, 'error',   this.error );
					this.listenTo( this.post, 'saved',   this.saved );
					this.listenTo( this.post, 'trashed', this.quit );
					this.listenTo( this.post, 'change:meta', this.updatePreview );
				},

				/**
				 * Update grid preview.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} model   Model object.
				 * @param {object} value   Changed value(s).
				 * @param {object} options Options.
				 *
				 * @return Returns itself to allow chaining.
				 */
				updatePreview : function( model, value, options ) {

					if ( _.isUndefined( this.preview ) ) {
						return this;
					}

					var atts = {};
					_.each( model.getMetas() || {}, function( value, key ) {
						var key = key.replace( wpmolyApiSettings.grid_prefix, '' );
						atts[ key ] = value;
					} );

					this.preview.set( atts );
					this.preview.controller.reset();

					return this;
				},

				/**
				 * Set meta values.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} meta    Meta values.
				 * @param {object} options Options.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setMetas : function( meta, options ) {

					if ( ! _.isObject( meta ) || _.isArray( meta ) ) {
						return false;
					}

					_.each( meta, function( value, key ) {
						this.setMeta( key, value, options );
					}, this );

					return this;
				},

				/**
				 * Set single meta value.
				 *
				 * @since 3.0.0
				 *
				 * @param {string} key     Meta key.
				 * @param {mixed}  value   Meta value.
				 * @param {object} options Options.
				 *
				 * @return {boolean}
				 */
				setMeta : function( key, value, options ) {

					if ( ! _.isString( key ) ) {
						return false;
					}

					if ( _.isNull( value ) ) {
						if ( _.has( this.node.defaults, key ) && _.isNumber( this.node.defaults[ key ] ) ) {
							value = parseInt( value );
						}
						if ( _.isNull( value ) ) {
							value = '';
						}
					}

					this.node.set( key, value, options );

					this.post.setMeta( wpmolyApiSettings.grid_prefix + key, value );
				},

				/**
				 * Save movie node and post data.
				 *
				 * @since 3.0.0
				 *
				 * @return xhr
				 */
				save : function() {

					var atts = {},
					    post = this.post;

					if ( 'publish' !== post.get( 'status' ) ) {
						atts.status = 'publish';
					}

					atts.meta = post.getMetas();

					var options = {
						patch : true,
						wait  : true,
						beforeSend : function( xhr, options ) {
							post.trigger( 'saving', xhr, options );
						},
						success : function( model, response, options ) {
							post.trigger( 'saved', model, response, options );
						},
						error : function( model, response, options ) {
							post.trigger( 'notsaved', model, response, options );
						},
					};

					return post.save( atts, options );
				},

				/**
				 * Notify successful saving.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				saved : function() {

					wpmoly.success( wpmolyEditorL10n.post_udpated );

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
				 * Post trashed: quit.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				quit : function() {

					wpmoly.success( wpmolyEditorL10n.post_trashed );

					_.delay( _.bind( this.redirect, this ), 2500 );

					return this;
				},

				/**
				 * Redirect to grid browser.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				redirect : function() {

					var origin = window.location.origin,
					  pathname = window.location.pathname;

					window.location.href = origin + pathname + '?page=wpmovielibrary-grids';

					return this;
				},

			}),

			/**
			 * GridEditor 'Parameters' Block View.
			 *
			 * @since 1.0.0
			 */
			ParametersBlock : Backbone.Model.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.post     = GridEditor.editor.controller.post;
					this.node     = GridEditor.editor.controller.node;
					this.defaults = GridEditor.editor.controller.node.defaults;
				},

				/**
				 * Update paramaters from the grid model.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} params
				 *
				 * @return Returns itself to allow chaining.
				 */
				updateParameters : function( params ) {

					GridEditor.editor.controller.setMetas( params || {} );

					return this;
				},

				/**
				 * Set grid type.
				 *
				 * @since 1.0.0
				 *
				 * @param {string} type Grid type.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setType : function( type ) {

					var defaults = this.defaults();

					this.updateParameters({
						type  : type || defaults.type,
						mode  : defaults.mode,
						theme : defaults.theme,
					});

					return this;
				},

				/**
				 * Set grid mode.
				 *
				 * @since 1.0.0
				 *
				 * @param {string} mode Grid mode.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setMode : function( mode ) {

					var defaults = this.defaults();

					this.updateParameters({
						mode  : mode || defaults.mode,
						theme : defaults.theme,
					});

					return this;
				},

				/**
				 * Set grid theme.
				 *
				 * @since 1.0.0
				 *
				 * @param {string} theme Grid theme.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setTheme : function( theme ) {

					var defaults = this.defaults();

					this.updateParameters({
						theme : theme || defaults.theme,
					});

					return this;
				},

			}),

			/**
			 * GridEditor 'Archives' Block View.
			 *
			 * @since 1.0.0
			 */
			ArchivesBlock : Backbone.Model.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.node = GridEditor.editor.controller.node;
					this.post = GridEditor.editor.controller.post;

					this.settings = new wp.api.models.Settings;
					this.page     = new wp.api.collections.Pages;
					this.pages    = new wp.api.collections.Pages;

					this.loadSettings();
					this.loadArchivePage();
					this.loadArchivePages();

					this.listenTo( this.post, 'change:meta', this.setCurrentArchiveType );

					this.listenTo( this.page, 'update', this.setCurrentArchivePage );
				},

				/**
				 * Update settings.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loadSettings : function() {

					this.settings.fetch({
						data : {
							context : 'edit',
						},
					});

					return this;
				},

				/**
				 * Update current archive page.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loadArchivePage : function() {

					this.page.fetch({
						data : {
							grid_id : this.get( 'post_id' ),
							context : 'edit',
						},
					});

					return this;
				},

				/**
				 * Update current archive pages.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loadArchivePages : function() {

					this.pages.fetch({
						data : {
							per_page : 100,
						},
					});

					return this;
				},

				/**
				 * Update current archive type.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} model   Grid meta object.
				 * @param {mixed}  changed Changed values.
				 * @param {object} options Options.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setCurrentArchiveType : function( model, changed, options ) {

					if ( ! _.has( changed || {}, 'type' ) ) {
						this.set( { archive_type : this.post.getMeta( wpmolyApiSettings.grid_prefix + 'type' ) || this.node.defaults.type } );
					} else {
						this.set( { archive_type : changed.type } );
					}

					return this;
				},

				/**
				 * Update current archive page.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} collection Archive Pages collection.
				 * @param {object} options    Options.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setCurrentArchivePage : function( collection, options ) {

					if ( 1 !== collection.length ) {
						return this;
					}

					var page = collection.first();

					this.set( { archive_page : page.id } );

					return this;
				},

				/**
				 * Update archive pages.
				 *
				 * @since 1.0.0
				 *
				 * @param {int} page_id Archive Page ID.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setArchivePage : function( type, page_id ) {

					if ( ! _.isString( type ) || ! _.isNumber( page_id ) ) {
						return this;
					}

					var archive_pages = this.settings.get( wpmolyApiSettings.option_prefix + 'archive_pages' ) || {};
					if ( _.isArray( archive_pages ) ) {
						archive_pages = _.object( archive_pages );
					}

					archive_pages[ type ] = page_id;

					var page = new wp.api.models.Page( { id : page_id } ),
					settings = new wp.api.models.Settings;

					var atts = {
						meta : {},
					};

					atts.meta[ wpmolyApiSettings.page_prefix + 'grid_id' ] = this.get( 'post_id' );

					page.save( atts, { patch : true } );

					atts = {};
					atts[ wpmolyApiSettings.option_prefix + 'archive_pages' ] = archive_pages;

					settings.save( atts, { patch : true } );
					settings.destroy();

					return this;
				},
			}),

		} ),

		view : _.extend( PostEditor.view, {

			/**
			 * GridEditor 'Parameters' Block View.
			 *
			 * @since 1.0.0
			 */
			ParametersBlock : Dashboard.view.Block.extend({

				events : function() {
					return _.extend( Dashboard.view.Block.prototype.events || {}, {
						'click [data-action="set-type"]'  : 'setType',
						'click [data-action="set-mode"]'  : 'setMode',
						'click [data-action="set-theme"]' : 'setTheme',
					} );
				},

				template : wp.template( 'wpmoly-grid-editor-parameters' ),

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					this.controller = options.controller;

					this.listenTo( this.controller.post, 'change:meta', this.render );

					this.render();
				},

				/**
				 * Set grid type.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} event JS 'click' Event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setType : function( event ) {

					event.preventDefault();

					var value = this.$( event.currentTarget ).data( 'value' );

					this.controller.setType( value );

					return this;
				},

				/**
				 * Set grid mode.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} event JS 'click' Event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setMode : function( event ) {

					event.preventDefault();

					var value = this.$( event.currentTarget ).data( 'value' );

					this.controller.setMode( value );

					return this;
				},

				/**
				 * Set grid theme.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} event JS 'click' Event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				setTheme : function( event ) {

					event.preventDefault();

					var value = this.$( event.currentTarget ).data( 'value' );

					this.controller.setTheme( value );

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

					var grid = _.pick( this.controller.post.getMetas() || {}, wpmolyApiSettings.grid_prefix + 'type', wpmolyApiSettings.grid_prefix + 'mode', wpmolyApiSettings.grid_prefix + 'theme' ),
					defaults = _.pick( this.controller.node.defaults || {}, 'type', 'mode', 'theme' ),
					options = {
						type     : grid[ wpmolyApiSettings.grid_prefix + 'type' ]  || defaults.type,
						mode     : grid[ wpmolyApiSettings.grid_prefix + 'mode' ]  || defaults.mode,
						theme    : grid[ wpmolyApiSettings.grid_prefix + 'theme' ] || defaults.theme,
						defaults : defaults,
					};

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

					var options = this.prepare();

					this.$el.html( this.template( options ) );

					return this;
				},
			}),

			/**
			 * GridEditor 'Archives' Block View.
			 *
			 * @since 1.0.0
			 */
			ArchivesBlock : Dashboard.view.Block.extend({

				events : function() {
					return _.extend( Dashboard.view.Block.prototype.events || {}, {
						'change [data-value="archive-type"]'   : 'change',
						'change [data-value="archive-page"]'   : 'change',
						'click [data-action="update-setting"]' : 'update',
					} );
				},

				template : wp.template( 'wpmoly-grid-editor-archives' ),

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					this.controller = options.controller;

					this.listenTo( this.controller.pages, 'update', this.render );
					this.listenTo( this.controller, 'change:archive_page', this.render );
					this.listenTo( this.controller, 'change:archive_type', this.render );
				},

				/**
				 * Enable/disable submit button based on title input length.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				change : function() {

					var type = this.$( '[data-value="archive-type"]' ).val(),
					 page_id = this.$( '[data-value="archive-page"]' ).val();

					this.$( '#update-setting' ).prop( 'disabled', ( _.isEmpty( type ) || _.isEmpty( page_id ) ) );

					return this;
				},

				/**
				 * Update title, if title input length reach the minimum.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				update : function() {

					var type = this.$( '[data-value="archive-type"]' ).val().trim(),
					 page_id = this.$( '[data-value="archive-page"]' ).val().trim();

					if ( ! page_id.length || ! type.length ) {
						return this;
					}

					this.$( '#update-setting' ).prop( 'disabled', true );
					this.$( '[data-value="archive-page"]' ).val( '' );
					this.$( '[data-value="archive-type"]' ).val( '' );

					this.controller.setArchivePage( type, parseInt( page_id ) );

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
						current_page : this.controller.get( 'archive_page' ),
						current_type : this.controller.get( 'archive_type' ),
						pages        : this.controller.pages.toJSON(),
						archives     : {},
					};

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

					Dashboard.view.Block.prototype.render.apply( this, arguments );

					var selects = this.$( '[data-selectize="1"]' );
					_.each( selects, function( select ) {
						var $select = this.$( select ),
						plugins = $select.attr( 'data-selectize-plugins' ) || '',
						create = $select.attr( 'data-selectize-create' );

						$select.selectize( _.extend({
							closeAfterSelect : true,
						}, {
							plugins : _.filter( plugins.split( ',' ) ),
							create  : true === create || false,
						}) );
					}, this );

					return this;
				},

			}),

			/**
			 * Editor Section View.
			 *
			 * @since 1.0.0
			 */
			EditorSection : wp.Backbone.View.extend({

				/**
				 * Render the View.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				render : function() {

					wp.Backbone.View.prototype.render.apply( this, arguments );

					var selects = this.$( '[data-selectize="1"]' );
					_.each( selects, function( select ) {
						var $select = this.$( select ),
						plugins = $select.attr( 'data-selectize-plugins' ) || '',
						create = $select.attr( 'data-selectize-create' );

						$select.selectize( _.extend({
							closeAfterSelect : true,
						}, {
							plugins : _.filter( plugins.split( ',' ) ),
							create  : true === create || false,
						}) );
					}, this );

					return this;
				},

			}),

			/**
			 * Editor View.
			 *
			 * @since 1.0.0
			 */
			Editor : wp.Backbone.View.extend({

				className : 'grid-editor',

				template : wp.template( 'wpmoly-grid-editor' ),

				events : {
					'click .preview-section a' : 'preventDefault',
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
				 * Disable click on preview links.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} event JS 'click' Event.
				 */
				preventDefault : function( event ) {

					event.preventDefault();
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

					if ( ! this.meta ) {
						this.meta = new GridEditor.view.MetaEditor( options );
					}

					this.views.set( '#wpmoly-grid-meta', this.meta );

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
						post_id : this.controller.post.get( 'id' ),
						title   : this.controller.post.get( 'title' ) || {},
					};

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

					this.trigger( 'ready' );

					return this;
				},

			} ),
		}),
	});

	_.extend( GridEditor.view, {

		/**
		 * Editor Meta Section View.
		 *
		 * @since 1.0.0
		 */
		MetaEditor : GridEditor.view.EditorSection.extend({

			className : 'wpmoly-metabox wpmoly-tabbed-metabox',

			template : wp.template( 'wpmoly-grid-meta-editor' ),

			events : function() {
				return _.extend( GridEditor.view.EditorSection.prototype.events || {}, {
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

				this.controller = options.controller;

				this.listenTo( this.controller.post, 'sync', this.render );
			},

			/**
			 * Update grid model.
			 *
			 * @since 1.0.0
			 *
			 * @param {object} event JS 'change' Event.
			 *
			 * @return Returns itself to allow chaining.
			 */
			change : function( event ) {

				var $target = this.$( event.currentTarget ),
				      field = $target.data( 'field' ),
				      value = $target.val();

				this.controller.setMeta( field, value );

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

				var atts = {},
				    meta = this.controller.post.getMetas(),
				defaults = this.controller.node.defaults;

				_.each( meta || {}, function( value, key ) {
						var key = key.replace( wpmolyApiSettings.grid_prefix, '' );
						atts[ key ] = value;
					} );

				var options = _.extend( defaults, atts );

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

				GridEditor.view.EditorSection.prototype.render.apply( this, arguments );

				wpmoly.metaboxes.add( this.el );

				return this;
			},

		}),

	} );

	/**
	 * Create grids editor instance.
	 *
	 * @since 1.0.0
	 */
	GridEditor.loadEditor = function() {

		var editor = document.querySelector( '#wpmoly-grid-editor' );
		if ( editor ) {
			GridEditor.editor = new Editor( editor );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 1.0.0
	 */
	GridEditor.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			GridEditor.loadEditor();
			PostEditor.loadSidebar();
		} );

		return GridEditor;
	};

})( jQuery, _, Backbone );

wpmoly.runners['grid'] = wpmoly.editor.grid;
