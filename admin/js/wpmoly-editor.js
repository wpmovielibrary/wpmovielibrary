
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	editor = wpmoly.editor = {

		runned: false,

		run: function() {

			var $editor = $( '#wpmoly-meta' ),
			    post_id = $( '#post_ID' ).val();

			editor.controller = new wpmoly.controller.Editor( {}, {
				post_id: post_id
			} );

			if ( $editor.length /*&& ! $editor.hasClass( 'hidden' )*/ ) {

				wpmoly.editor = new wpmoly.view.Editor({
					el         : $editor,
					controller : editor.controller
				});

				wpmoly.editor.tagbox = {
					collection : new wpmoly.view.CollectionsBox,
					genre      : new wpmoly.view.GenresBox,
					actor      : new wpmoly.view.ActorsBox
				};

				wpmoly.editor.runned = true;
			}
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.editor );
