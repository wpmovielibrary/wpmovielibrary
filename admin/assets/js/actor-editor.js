wpmoly = window.wpmoly || {};

wpmoly.editor = wpmoly.editor || {};

(function( $, _, Backbone ) {

	var Dashboard = wpmoly.dashboard;

	/**
	 * Create a new Actor Editor instance.
	 *
	 * Set snapshot, node and term models, editor controller and editor view. Load
	 * term first, then snapshot, then node. Editor view is rendered when term is
	 * fetched, editor regions are set when node is fetched.
	 *
	 * @since 3.0.0
	 *
	 * @param {Element} editor Actor Editor DOM element.
	 *
	 * @return {object} Actor instance.
	 */
	var Editor = function( editor ) {

		var term_id = parseInt( wpmoly.$( '#object_ID' ).val() ),
		    $parent = wpmoly.$( '#wpmoly-editor' );

		// Set editor models.
		var term = new wp.api.models.Actors( { id : term_id } ),
		    node = new wpmoly.api.models.Actor( { id : term_id } ),
		snapshot = node.snapshot = new ActorEditor.model.Snapshot( [], { model : term } );

		var controller = new TermEditor.controller.Editor( [], {
			term : term,
			node : node,
			snapshot : snapshot,
		} );

		// Set editor view.
		var view = new TermEditor.view.Editor({
			el         : editor,
			controller : controller,
		});

		view.$el.addClass( 'term-editor actor-editor' );

		// Render editor view.
		term.once( 'sync', function() {
			// Hide loading animation.
			$parent.removeClass( 'loading' );
			// Set snapshot.
			snapshot.set( term.get( 'snapshot' ) || {} );
			// Render the editor.
			view.render();
		} );

		// Load actor.
		term.fetch( { data : { context : 'edit' } } );

		/**
		 * Editor instance.
		 *
		 * Provide a set of useful functions to interact with the editor
		 * without directly calling controllers and views.
		 *
		 * @since 3.0.0
		 */
		var editor = {

			term : term,

			node : node,

			controller : controller,

			view : view,

		};

		// Debug.
		_.map( editor, function( model, name ) {
			wpmoly.observe( model, { name : name } );
		} );

		return editor;
	};

	var TermEditor = wpmoly.editor.term;

	var ActorEditor = wpmoly.editor.actor = _.extend( TermEditor, {

		model : {

			Snapshot : Backbone.Model.extend({

				/**
				 * Initialize the Model.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.model = options.model;
				},

				/**
				 * Save Snapshot.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				save : function( snapshot, options ) {

					if ( _.isUndefined( snapshot ) || _.isUndefined( snapshot.id ) ) {
						var snapshot = this.toJSON();
					}

					if ( _.isUndefined( snapshot.id ) ) {
						return false;
					}

					var options = _.extend( options || {}, {
						silent : true,
					} );

					snapshot._snapshot_date = ( new Date ).toISOString().substr( 0, 19 ) + '+00:00';

					this.set( snapshot );

					var attributes = {
						meta : {},
					};

					attributes.meta[ wpmolyApiSettings.actor_prefix + 'snapshot' ] = JSON.stringify( snapshot );

					return this.model.save( attributes, _.extend( { patch : true }, options || {} ) );
				},

			}),

		},

		controller : _.extend( TermEditor.controller, {

			/**
			 * ActorEditor Editor controller.
			 *
			 * @since 3.0.0
			 */
			Editor : TermEditor.controller.Editor.extend({

				taxonomy : 'actor',

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
					this.snapshot = options.snapshot;

					this.listenTo( this.term, 'error',   this.error );
					this.listenTo( this.term, 'saved',   this.saved );
					this.listenTo( this.term, 'trashed', this.quit );
				},

			}),

			/**
			 * ActorEditor 'Submit' Block Controller.
			 *
			 * @since 3.0.0
			 */
			SubmitBlock : TermEditor.controller.SubmitBlock.extend({

				/**
				 * Update the node.
				 *
				 * @since 3.0.0
				 *
				 * @return xhr
				 */
				save : function() {

					return ActorEditor.editor.controller.save();
				},
			}),

			/**
			 * ActorEditor 'Related Person' Block Controller.
			 *
			 * @since 3.0.0
			 */
			RelatedPersonBlock : Backbone.Model.extend({

				defaults : {
					related_person_id : null,
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

					this.term = ActorEditor.editor.controller.term;
					this.node = ActorEditor.editor.controller.node;
					this.snapshot = this.node.snapshot;

					this.listenTo( this.term, 'change:meta', this.changePerson );

					this.on( 'change:related_person_id', this.updatePerson, this );
				},

				/**
				 * Set related person ID.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				changePerson : function() {

					this.set( { related_person_id : this.term.getMeta( wpmolyApiSettings.actor_prefix + 'related_person_id' ) } );

					return this;
				},

				/**
				 * Update related person ID.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				updatePerson : function() {

					return this.term.setMeta( wpmolyApiSettings.actor_prefix + 'related_person_id', this.get( 'related_person_id' ) );
				},

				/**
				 * Save related person ID.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				savePerson : function() {

					this.term.save({
						meta : this.term.getMetas(),
					}, {
						patch : true,
					});

					return this;
				},

			}),

		} ),

		view : _.extend( TermEditor.view, {

			/**
			 * ActorEditor 'Related Person' Block View.
			 *
			 * @since 1.0.0
			 */
			RelatedPersonBlock : TermEditor.view.Block.extend({

				events : function() {
					return _.extend( TermEditor.view.Block.prototype.events.call( this, arguments ) || {}, {
						'change [data-field="person"]'      : 'changePerson',
						'click [data-action="save-person"]' : 'savePerson',
					} );
				},

				template : wp.template( 'wpmoly-actor-related-person' ),

				/**
				 * Initialize the View.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					this.controller = options.controller;

					this.listenTo( this.controller, 'change:related_person_id', this.render );

					this.on( 'rendered', this.selectize, this );

					this.render();
				},

				/**
				 * Update related person ID.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
		 	 	 */
				changePerson : function() {

					var person_id = this.$( '[data-field="person"]' ).val();

					this.controller.set( { related_person_id : parseInt( person_id ) || null } );

					return this;
				},

				/**
				 * Save related person ID.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				savePerson : function() {

					this.controller.savePerson();

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
						person : this.controller.get( 'related_person_id' ) || 0,
					};

					return options;
				},

			}),

			/**
			 * ActorEditor Editor View.
			 *
			 * @since 3.0.0
			 */
			/*Editor : TermEditor.view.Editor.extend({

				template : wp.template( 'wpmoly-actor-editor' ),

			}),*/

			/**
			 * ActorEditor Thumbnail Editor Default Picture Picker View.
			 *
			 * @since 3.0.0
			 */
			ThumbnailPicker : TermEditor.view.ThumbnailPicker.extend({

				className : 'editor-content-inner',

				template : wp.template( 'wpmoly-actor-thumbnail-picker' ),

			}),

			/**
			 * ActorEditor Thumbnail Editor Picture Downloader View.
			 *
			 * @since 3.0.0
			 */
			ThumbnailDownloader : wpmoly.Backbone.View.extend({

				className : 'editor-content-inner',

				template : wp.template( 'wpmoly-actor-thumbnail-downloader' ),

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

					this.listenTo( this.controller.term,     'change:meta', this.render );
					this.listenTo( this.controller.snapshot, 'change:images', this.render );
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
						thumbnails : this.controller.snapshot.get( 'images' ) || {},
					};

					return options;
				},

			}),

		} ),

	} );

	/**
	 * Create actor editor instance.
	 *
	 * @since 3.0.0
	 */
	ActorEditor.loadEditor = function() {

		var editor = document.querySelector( '#wpmoly-actor-editor' );
		if ( editor ) {
			ActorEditor.editor = new Editor( editor );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 3.0.0
	 */
	ActorEditor.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			ActorEditor.loadEditor();
			TermEditor.loadSidebar();
		} );

		return ActorEditor;
	};

})( jQuery, _, Backbone );

wpmoly.runners['actor'] = wpmoly.editor.actor;
