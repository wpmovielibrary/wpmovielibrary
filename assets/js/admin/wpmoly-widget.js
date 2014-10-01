
$ = $ || jQuery;

wpmoly = wpmoly || {};

wpmoly.widget = wpmoly_widget = {

	select: '.wpmoly-movies-widget-select > option',
	select_status: '.wpmoly-movies-widget-select-status',
	select_media: '.wpmoly-movies-widget-select-media',
	select_rating: '.wpmoly-movies-widget-select-rating',
};

	/*wpmoly.widget = function() {
		
	};*/

	wpmoly.widget.init = function() {

		wpmoly_widget.hide();

		$( wpmoly_widget.select ).unbind( 'click' ).on( 'click', function() {
			wpmoly_widget.toggle( this.value );
		});
	};

	wpmoly.widget.toggle = function( select ) {

		$( '.selected' ).removeClass( 'selected' );

		switch ( select ) {
			case 'status':
			case 'media':
			case 'rating':
				var selector = '.wpmoly-movies-widget-select-' + select;
				break;
			default:
				var selector = undefined;
				break;
		}

		if ( undefined != selector )
			$( selector ).addClass( 'selected' ).show();

		wpmoly_widget.hide();

	};

	wpmoly.widget.hide = function() {
		$( wpmoly_widget.select_status ).not( '.selected' ).hide();
		$( wpmoly_widget.select_media ).not( '.selected' ).hide();
		$( wpmoly_widget.select_rating ).not( '.selected' ).hide();
	}

wpmoly_widget.init();
