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
		    node = new wpmoly.api.models.Actor( { id : term_id } );

		var controller = new ActorEditor.controller.Editor( [], {
			term : term,
			node : node,
		} );

		// Set editor view.
		var view = new ActorEditor.view.Editor({
			el         : editor,
			controller : controller,
		});

		view.$el.addClass( 'term-editor actor-editor' );

		// Render editor view.
		term.once( 'sync', function() {
			// Hide loading animation.
			$parent.removeClass( 'loading' );
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

		return editor;
	};

	var TermEditor = wpmoly.editor.term;

	var ActorEditor = wpmoly.editor.actor = _.extend( TermEditor, {

		controller : _.extend( TermEditor.controller, {

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
			 * ActorEditor Editor controller.
			 *
			 * @since 3.0.0
			 */
			Editor : TermEditor.controller.Editor.extend({

				taxonomy : 'actor',

			}),

		} ),

		view : _.extend( TermEditor.view, {

			/**
			 * ActorEditor Editor View.
			 *
			 * @since 3.0.0
			 */
			Editor : TermEditor.view.Editor.extend({

				template : wp.template( 'wpmoly-actor-editor' ),

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
