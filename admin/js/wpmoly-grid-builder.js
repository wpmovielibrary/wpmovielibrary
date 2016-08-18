
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	gridbuilder = wpmoly.gridbuilder = {

		runned: false,

		run: function() {

			var $builder = $( '#wpmoly-grid-builder' ),
			       nonce = $( '#wpmoly_save_grid_setting_nonce' ).val(),
			     post_id = $( '#post_ID' ).val();

			gridbuilder.controller = new wpmoly.controller.GridBuilder( {
				post_id : post_id,
				nonce   : nonce
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
