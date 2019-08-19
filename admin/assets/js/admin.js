/**
 * WPSeriesLibrary instance runner.
 *
 * @since 3.0.0
 *
 * @package WPSeriesLibrary
 */

(function( $ ) {

	'use strict';

	if ( 'edit-php' === adminpage && ( 'movie' === typenow || 'edit-movie' === pagenow || 'person' === typenow || 'edit-person' === pagenow ) ) {
		$( '.wp-header-end' ).before( '<a href="' + wpmolyL10n.edit_link.replace( '%s', typenow ) + '" class="page-title-action">' + wpmolyL10n.open_library + '</a>' );
	}

})( jQuery );
