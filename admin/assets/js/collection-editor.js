wpmoly = window.wpmoly || {};

wpmoly.editor = wpmoly.editor || {};

(function( $, _, Backbone ) {

	var Dashboard = wpmoly.dashboard;

	/**
	 * Create a new Collection Editor instance.
	 *
	 * Set snapshot, node and term models, editor controller and editor view. Load
	 * term first, then snapshot, then node. Editor view is rendered when term is
	 * fetched, editor regions are set when node is fetched.
	 *
	 * @since 3.0.0
	 *
	 * @param {Element} editor Collection Editor DOM element.
	 *
	 * @return {object} Collection instance.
	 */
	var Editor = function( editor ) {

		var term_id = parseInt( wpmoly.$( '#object_ID' ).val() ),
		    $parent = wpmoly.$( '#wpmoly-editor' );

		// Set editor models.
		var term = new wp.api.models.Collections( { id : term_id } ),
		    node = new wpmoly.api.models.Collection( { id : term_id } );

		var controller = new CollectionEditor.controller.Editor( [], {
			term : term,
			node : node,
		} );

		// Set editor view.
		var view = new CollectionEditor.view.Editor({
			el         : editor,
			controller : controller,
		});

		view.$el.addClass( 'term-editor collection-editor' );

		// Render editor view.
		term.once( 'sync', function() {
			// Hide loading animation.
			$parent.removeClass( 'loading' );
			view.render();
		} );

		// Load collection.
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

	var CollectionEditor = wpmoly.editor.collection = _.extend( TermEditor, {

		controller : _.extend( TermEditor.controller, {

			/**
			 * CollectionEditor 'Submit' Block Controller.
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

					return CollectionEditor.editor.controller.save();
				},
			}),

			/**
			 * CollectionEditor Editor controller.
			 *
			 * @since 3.0.0
			 */
			Editor : TermEditor.controller.Editor.extend({

				taxonomy : 'collection',

			}),

		} ),

		view : _.extend( TermEditor.view, {

			/**
			 * CollectionEditor Editor View.
			 *
			 * @since 3.0.0
			 */
			Editor : TermEditor.view.Editor.extend({

				template : wp.template( 'wpmoly-collection-editor' ),

			}),

		} ),

	} );

	/**
	 * Create collection editor instance.
	 *
	 * @since 3.0.0
	 */
	CollectionEditor.loadEditor = function() {

		var editor = document.querySelector( '#wpmoly-collection-editor' );
		if ( editor ) {
			CollectionEditor.editor = new Editor( editor );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 3.0.0
	 */
	CollectionEditor.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			CollectionEditor.loadEditor();
			TermEditor.loadSidebar();
		} );

		return CollectionEditor;
	};

})( jQuery, _, Backbone );

wpmoly.runners['collection'] = wpmoly.editor.collection;
