wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	Gutenberg = wpmoly.gutenberg = {};

	Gutenberg.createBlockEditorSwitchButton = function() {

		var $toolbar = $( '.edit-post-header-toolbar' );
		if ( $toolbar.length ) {
			var $button = $( '<div id="wpmoly-editor-switch" class="wpmoly editor-switch"><a href="' + wpmolyBlockEditorL10n.edit_link + '" class="wpmoly editor-switch-button">' + wpmolyBlockEditorL10n.edit_with_wpmoly + '</a></div>' );
			$toolbar.append( $button );
		}
	};

	Gutenberg.init = function() {

		if ( ! _.isUndefined( typenow ) && ( 'grid' === typenow || 'movie' == typenow || 'person' == typenow ) ) {
			Gutenberg.createBlockEditorSwitchButton();
		}
	}

	/**
	 * Run Forrest, run!
	 *
	 * @since 1.0.0
	 */
	Gutenberg.run = function() {

		setTimeout( function() {
			Gutenberg.init();
		}, 25 );
	};

})( jQuery, _, Backbone );

wpmoly.runners['gutenberg'] = wpmoly.gutenberg;
