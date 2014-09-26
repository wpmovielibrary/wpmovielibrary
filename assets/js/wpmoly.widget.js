
$ = $ || jQuery;

wpml = wpml || {};

wpml.widget = wpml_widget = {

	select: '.wpml-movies-widget-select > option',
	select_status: '.wpml-movies-widget-select-status',
	select_media: '.wpml-movies-widget-select-media',
	select_rating: '.wpml-movies-widget-select-rating',
};

	/*wpml.widget = function() {
		
	};*/

	wpml.widget.init = function() {

		wpml_widget.hide();

		$( wpml_widget.select ).unbind( 'click' ).on( 'click', function() {
			wpml_widget.toggle( this.value );
		});
	};

	wpml.widget.toggle = function( select ) {

		$( '.selected' ).removeClass( 'selected' );

		switch ( select ) {
			case 'status':
			case 'media':
			case 'rating':
				var selector = '.wpml-movies-widget-select-' + select;
				break;
			default:
				var selector = undefined;
				break;
		}

		if ( undefined != selector )
			$( selector ).addClass( 'selected' ).show();

		wpml_widget.hide();

	};

	wpml.widget.hide = function() {
		$( wpml_widget.select_status ).not( '.selected' ).hide();
		$( wpml_widget.select_media ).not( '.selected' ).hide();
		$( wpml_widget.select_rating ).not( '.selected' ).hide();
	}

wpml_widget.init();
