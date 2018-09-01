wpmoly = window.wpmoly || {};

wpmoly.editor = wpmoly.editor || {};

(function( $, _, Backbone ) {

	var Dashboard = wpmoly.dashboard;

	/**
	 * Create a new Genre Editor instance.
	 *
	 * Set snapshot, node and term models, editor controller and editor view. Load
	 * term first, then snapshot, then node. Editor view is rendered when term is
	 * fetched, editor regions are set when node is fetched.
	 *
	 * @since 3.0.0
	 *
	 * @param {Element} editor Genre Editor DOM element.
	 *
	 * @return {object} Genre instance.
	 */
	var Editor = function( editor ) {

		var term_id = parseInt( wpmoly.$( '#object_ID' ).val() ),
		    $parent = wpmoly.$( '#wpmoly-editor' );

		// Set editor models.
		var term = new wp.api.models.Genres( { id : term_id } ),
		    node = new wpmoly.api.models.Genre( { id : term_id } );

		var controller = new GenreEditor.controller.Editor( [], {
			term : term,
			node : node,
		} );

		// Set editor view.
		var view = new GenreEditor.view.Editor({
			el         : editor,
			controller : controller,
		});

		view.$el.addClass( 'term-editor genre-editor' );

		// Render editor view.
		term.once( 'sync', function() {
			// Hide loading animation.
			$parent.removeClass( 'loading' );
			view.render();
		} );

		// Load genre.
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

	var GenreEditor = wpmoly.editor.genre = _.extend( TermEditor, {

		controller : _.extend( TermEditor.controller, {

			/**
			 * GenreEditor 'Submit' Block Controller.
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

					return GenreEditor.editor.controller.save();
				},
			}),

			/**
			 * GenreEditor Editor controller.
			 *
			 * @since 3.0.0
			 */
			Editor : TermEditor.controller.Editor.extend({

				taxonomy : 'genre',

			}),

		} ),

		view : _.extend( TermEditor.view, {

			/**
			 * GenreEditor Editor View.
			 *
			 * @since 3.0.0
			 */
			Editor : TermEditor.view.Editor.extend({

				template : wp.template( 'wpmoly-genre-editor' ),

			}),

		} ),

	} );

	/**
	 * Create genre editor instance.
	 *
	 * @since 3.0.0
	 */
	GenreEditor.loadEditor = function() {

		var editor = document.querySelector( '#wpmoly-genre-editor' );
		if ( editor ) {
			GenreEditor.editor = new Editor( editor );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 3.0.0
	 */
	GenreEditor.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			GenreEditor.loadEditor();
			TermEditor.loadSidebar();
		} );

		return GenreEditor;
	};

})( jQuery, _, Backbone );

wpmoly.runners['genre'] = wpmoly.editor.genre;
