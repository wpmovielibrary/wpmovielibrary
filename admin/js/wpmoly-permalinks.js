
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	permalinks = wpmoly.permalinks = {

		run: function() {

			wpmoly.permalinks = new wpmoly.view.Permalinks;
		}
	};

})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.permalinks );