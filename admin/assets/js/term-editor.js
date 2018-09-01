wpmoly = window.wpmoly || {};

wpmoly.editor = wpmoly.editor || {};

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
		  && _.has( TermEditor.controller, block.dataset['controller'] )
		  && _.has( TermEditor.view, block.dataset['controller'] ) ) {
			controller = new TermEditor.controller[ block.dataset['controller'] ]({
				term_id : parseInt( wpmoly.$( '#object_ID' ).val() ),
			});
			view = new TermEditor.view[ block.dataset['controller'] ]({
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

		var view = new TermEditor.view.Sidebar( { el : sidebar } );

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
	 * TermEditor wrapper.
	 *
	 * @since 1.0.0
	 */
	var TermEditor = wpmoly.editor.term = {

		/**
		 * List of term editor models.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		model : {},

		/**
		 * List of term editor controllers.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		controller : {},

		/**
		 * List of term editor views.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		view : {},
	};

	/**
	 * TermEditor 'Submit' Block Controller.
	 *
	 * @since 1.0.0
	 */
	TermEditor.controller.SubmitBlock = Backbone.Model.extend({

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

			this.term = TermEditor.editor.controller.term;
		},

		/**
		 * Update the node.
		 *
		 * @since 1.0.0
		 *
		 * @return xhr
		 */
		save : function() {

			var term = this.term;

			return term.save( [], {
				beforeSend : function( xhr, options ) {
					term.trigger( 'saving', xhr, options );
				},
				success : function( model, response, options ) {
					term.trigger( 'saved', model, response, options );
				},
				error : function( model, response, options ) {
					term.trigger( 'notsaved', model, response, options );
				},
			} );
		},

		/**
		 * Trash the term.
		 *
		 * @since 1.0.0
		 *
		 * @return xhr
		 */
		destroy : function() {

			var term = this.term;

			// Don't delete permanently.
			term.requireForceForDelete = false;
			term.trigger( 'trashing' );

			return term.destroy({
				wait : true,
				success : function( model, response, options ) {
					term.trigger( 'trashed', model, response, options );
				},
				error : function( model, response, options ) {
					term.trigger( 'nottrashed', model, response, options );
				},
			});
		},

	});

	/**
	 * TermEditor 'Rename' Block Controller.
	 *
	 * @since 1.0.0
	 */
	TermEditor.controller.RenameBlock = Backbone.Model.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			this.term = TermEditor.editor.controller.term;
		},

		/**
		 * Save new grid name.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} name New name.
		 *
		 * @return Returns itself to allow chaining.
		 */
		updateName : function( name ) {

			this.term.save( { name : name }, { patch : true } );

			return this;
		},

	});

	/**
	 * TermEditor Editor controller.
	 *
	 * @since 3.0.0
	 */
	TermEditor.controller.Editor = Backbone.Model.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.node = options.node;
			this.term = options.term;

			this.listenTo( this.term, 'error',   this.error );
			this.listenTo( this.term, 'saved',   this.saved );
			this.listenTo( this.term, 'trashed', this.quit );
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

			console.log( wpmolyApiSettings[ this.taxonomy + '_prefix' ] + key );
			this.term.setMeta( wpmolyApiSettings[ this.taxonomy + '_prefix' ] + key, value );
		},

		/**
		 * Save genre node and term data.
		 *
		 * @since 3.0.0
		 *
		 * @return xhr
		 */
		save : function() {

			var atts = {},
					term = this.term;

			atts.meta = term.getMetas();

			var options = {
				patch : true,
				wait  : true,
				beforeSend : function( xhr, options ) {
					term.trigger( 'saving', xhr, options );
				},
				success : function( model, response, options ) {
					term.trigger( 'saved', model, response, options );
				},
				error : function( model, response, options ) {
					term.trigger( 'notsaved', model, response, options );
				},
			};

			return term.save( atts, options );
		},

		/**
		 * Notify successful saving.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		saved : function() {

			wpmoly.success( wpmolyEditorL10n.term_udpated );

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
		 * Term trashed: quit.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		quit : function() {

			wpmoly.success( wpmolyEditorL10n.term_trashed );

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

			window.location.href = origin + pathname + '?page=wpmovielibrary-' + this.taxonomy + 's';

			return this;
		},

		/**
		 * Select Collection thumbnail.
		 *
		 * @since 3.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		selectThumbnail : function() {

			if ( this.frame ) {
				return this.frame.open();
			}

			this.frame = wp.media({
				title   : wpmolyEditorL10n.select_thumbnail,
				library : {
					type  : 'image',
				},
				button : {
					text : wpmolyEditorL10n.use_as_thumbnail,
				},
				multiple : false,
			});

			this.frame.on( 'select', _.bind( this.setThumbnail, this ) );

			this.frame.open();

			return this;
		},

		/**
		 * Select Genre thumbnail.
		 *
		 * @since 3.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setThumbnail : function() {

			// Grab the selected attachment.
			var attachment = this.frame.state().get( 'selection' ).first();

			this.setMeta( 'custom_thumbnail', attachment.id );

			this.term.set( { thumbnail : attachment.get( 'sizes' ).medium.url } );

			// Close frame.
			this.frame.close();

			return this;
		},

		/**
		 * Remove Genre thumbnail.
		 *
		 * @since 3.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeThumbnail : function() {

			this.setMeta( 'custom_thumbnail', 0 );

			this.term.set( { thumbnail : null } );

			return this;
		},

	});

	/**
	 * TermEditor Editor View.
	 *
	 * @since 3.0.0
	 */
	TermEditor.view.Editor = wpmoly.Backbone.View.extend({

		events : function() {
			return {
				'click [data-action="change-thumbnail"]' : 'selectThumbnail',
				'click [data-action="remove-thumbnail"]' : 'removeThumbnail',
			};
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

			this.listenTo( this.controller.term, 'change',  this.render );
		},

		/**
		 * Prepare rendering options.
		 *
		 * @since 3.0.0
		 *
		 * @return {object}
		 */
		prepare : function() {

			var options = this.controller.term.toJSON() || {};

			return options;
		},

		/**
		 * Change term thumbnail.
		 *
		 * @since 3.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		selectThumbnail : function() {

			this.controller.selectThumbnail();

			return this;
		},

		/**
		 * Remove term thumbnail.
	 	 *
		 * @since 3.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeThumbnail : function() {

			this.controller.removeThumbnail();

			return this;
		},

	});

	/**
	 * TermEditor Block View.
	 *
	 * @since 1.0.0
	 */
	TermEditor.view.Block = Dashboard.view.Block.extend({

		events : function() {
			return _.extend( Dashboard.view.Block.prototype.events.call( this, arguments ) || {}, {
				'click [data-action="edit"]' : 'edit',
			} );
		},

		/**
		 * Toggle edit/preview block modes.
		 *
		 * @since 1.0.0
		 *
		 * @return Return itself to allow chaining.
		 */
		edit : function() {

			this.$el.toggleClass( 'mode-edit mode-preview' );

			return this;
		},

	});

	/**
	 * TermEditor 'Submit' Block View.
	 *
	 * @since 1.0.0
	 */
	TermEditor.view.SubmitBlock = TermEditor.view.Block.extend({

		events : function() {
			return _.extend( TermEditor.view.Block.prototype.events.call( this, arguments ) || {}, {
				'click [data-action="save"]'          : 'save',
				'click [data-action="trash"]'         : 'trash',
				'click [data-action="dismiss"]'       : 'dismiss',
				'click [data-action="confirm-trash"]' : 'confirmTrash',
			} );
		},

		template : wp.template( 'wpmoly-term-editor-submit' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.listenTo( this.controller.term, 'saving',             this.saving );
			this.listenTo( this.controller.term, 'saved',              this.saved );
			this.listenTo( this.controller.term, 'notsaved',           this.saved );
			this.listenTo( this.controller.term, 'trashing',           this.trashing );
			this.listenTo( this.controller.term, 'trashed nottrashed', this.trashed );
			this.listenTo( this.controller.term, 'sync',               this.render );

			this.render();
		},

		/**
		 * Save the term.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		save : function() {

			this.saving();
			this.controller.save();

			return this;
		},

		/**
		 * Dismiss confirmation request.
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
		 * Delete the term.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		confirmTrash : function() {

			this.controller.destroy();

			this.$el.removeClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Delete the term.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trash : function() {

			this.$el.addClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Show loading animation.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		saving : function() {

			this.$el.addClass( 'loading' );

			return this;
		},

		/**
		 * Show temporary checked icon.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		saved : function() {

			this.$el.removeClass( 'loading' );

			var $icon = this.$( '.button.save .wpmolicon' );

			$icon.removeClass( 'icon-save' ).addClass( 'icon-yes-alt' );

			_.delay( function() {
				$icon.removeClass( 'icon-yes-alt' ).addClass( 'icon-save' );
			}, 2500 );

			return this;
		},

		/**
		 * Show temporary error icon.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		notsaved : function() {

			this.$el.removeClass( 'loading' );

			var $icon = this.$( '.button.save .wpmolicon' );

			$icon.removeClass( 'icon-save' ).addClass( 'icon-warning' );

			_.delay( function() {
				$icon.removeClass( 'icon-warning' ).addClass( 'icon-save' );
			}, 2500 );

			return this;
		},

		/**
		 * Show trashing animation.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashing : function() {

			this.$el.addClass( 'trashing' );

			return this;
		},

		/**
		 * Hide trashing animation.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashed : function() {

			this.$el.removeClass( 'trashing' );

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

			var options = _.pick( this.controller.term.toJSON(), [ 'old_edit_link' ] );

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

	});

	/**
	 * TermEditor 'Rename' Block View.
	 *
	 * @since 1.0.0
	 */
	TermEditor.view.RenameBlock = TermEditor.view.Block.extend({

		events : function() {
			return _.extend( TermEditor.view.Block.prototype.events.call( this, arguments ) || {}, {
				'input [data-value="new-name"]'     : 'change',
				'click [data-action="update-name"]' : 'update',
			} );
		},

		template : wp.template( 'wpmoly-term-editor-rename' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.listenTo( this.controller.term, 'change',  this.render );
		},

		/**
		 * Enable/disable submit button based on name input length.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		change : function() {

			var name = this.$( '[data-value="new-name"]' ).val().trim();

			this.$( '#update-name' ).prop( 'disabled', ( 2 >= name.length ) );

			return this;
		},

		/**
		 * Update name, if name input length reach the minimum.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		update : function() {

			var name = this.$( '[data-value="new-name"]' ).val().trim();

			if ( 3 > name.length ) {
				return this;
			}

			this.$( '#update-name' ).prop( 'disabled', true );
			this.$( '[data-value="new-name"]' ).val( '' );

			this.controller.updateName( name );

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

			var options = _.pick( this.controller.term.toJSON(), 'name' );

			return options;
		},

	});

	/**
	 * TermEditor Sidebar View.
	 *
	 * @since 1.0.0
	 */
	TermEditor.view.Sidebar = wp.Backbone.View;

	/**
	 * Create sidebar instance.
	 *
	 * @since 1.0.0
	 */
	TermEditor.loadSidebar = function() {

		var sidebar = document.querySelector( '#wpmoly-editor-sidebar' );
		if ( sidebar ) {
			TermEditor.sidebar = new Sidebar( sidebar );
		}
	};

})( jQuery, _, Backbone );
