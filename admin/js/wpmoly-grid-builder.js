
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	gridbuilder = wpmoly.gridbuilder = {

		runned: false,

		run: function() {

			var $builder = $( '#wpmoly-grid-builder' ),
			     post_id = $( '#post_ID' ).val();

			gridbuilder.controller = new wpmoly.controller.GridBuilder( {}, {
				post_id: post_id
			} );

			if ( $builder.length ) {

				wpmoly.gridbuilder = new wpmoly.view.Grid.Builder({
					el         : $builder,
					model      : gridbuilder.controller.builder,
					controller : gridbuilder.controller
				});

				wpmoly.gridbuilder.runned = true;
			}
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.gridbuilder );
