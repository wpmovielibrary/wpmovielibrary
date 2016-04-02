
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	metabox = wpmoly.metabox = {

		$el      : $( '.wpmoly-tabbed-metabox' ),

		$search  : $( '.wpmoly-movie-search' ),

		$open    : $( '[data-action="open-editor"]' ),

		$close   : $( '[data-action="close-editor"]' ),

		toggle: function( toggle ) {

			var toggle = toggle || false;

			metabox.$el.toggleClass( 'hidden', ! toggle );
			metabox.$search.toggleClass( 'hidden', ! toggle );
			metabox.$search.toggleClass( 'no-handle', toggle );

			metabox.$open.toggleClass( 'hidden', toggle );
			metabox.$close.toggleClass( 'hidden', ! toggle );
		},

		run: function() {

			metabox.$open.on( 'click', function( event ) {

				event.preventDefault();

				metabox.toggle( true );

				if ( ! wpmoly.search.runned ) {
					wpmoly.search.run();
				}
				if ( ! wpmoly.editor.runned ) {
					wpmoly.editor.run();
				}
			} );

			metabox.$close.on( 'click', function( event ) {

				event.preventDefault();

				metabox.toggle( false );
			} );

			if ( metabox.$el.length ) {
				metabox.$el.removeClass( 'css-powered' ).addClass( 'js-powered' );
				metabox.view = new wpmoly.view.Metabox({ el: metabox.$el });
			}
		}
	};

})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.metabox );