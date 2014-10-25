
$ = $ || jQuery;

wpmoly = wpmoly || {};

wpmoly.widget = wpmoly_widget = {

	select: '.wpmoly-movies-widget-select-select > option',
	select_meta_select: '.wpmoly-movies-widget-meta-select',

	select_status: '.wpmoly-movies-widget-select-status',
	select_media: '.wpmoly-movies-widget-select-media',
	select_rating: '.wpmoly-movies-widget-select-rating',
	select_meta: '.wpmoly-movies-widget-select-meta',
	select_year: '.wpmoly-movies-widget-select-release_date',
	select_language: '.wpmoly-movies-widget-select-spoken_languages',
	select_production_countries: '.wpmoly-movies-widget-select-production_countries',
	select_production_companies: '.wpmoly-movies-widget-select-production_companies',
};

	/*wpmoly.widget = function() {
		
	};*/

	wpmoly.widget.init = function() {

		wpmoly_widget.hide();

		$( wpmoly_widget.select ).on( 'click', function( e ) {
			wpmoly_widget.toggle( e, this.value );
		});

		$( wpmoly_widget.select_meta + ' option' ).on( 'click', function( e ) {
			wpmoly_widget.toggle_meta( e, this.value );
		});

		$( wpmoly_widget.select_meta_select + ' select' ).on( 'change', function( e ) {
			$( wpmoly_widget.select_meta_select + ' option' ).prop( 'selected', false );
			if ( e.added.element.length )
				$( e.added.element[0] ).prop( 'selected', true );
		});

		/*$( wpmoly_widget.select_meta ).on( 'change', function( e ) {
			wpmoly_widget.toggle_meta( e );
		});*/
	};

	wpmoly.widget.toggle = function( event, select ) {
	    
		$( '.wpmoly-movies-widget-select.selected' ).removeClass( 'selected' );

		var $selector = $( '.wpmoly-movies-widget-select-' + select );
		if ( undefined != $selector )
			$selector.addClass( 'selected' ).show();

		wpmoly_widget.hide();
	};

	wpmoly.widget.toggle_meta = function( event, select ) {

		var $target = $( event.target ),
		    $widget = $target.parents( 'div.widget' ),
		    $select = $widget.find( '.wpmoly-movies-widget-select-' + select ),
		   $selects = $widget.find( '.wpmoly-movies-widget-select-meta .redux-container-select' );

		console.log( $select, $selects );
		$selects.removeClass( 'selected' ).hide();
		$select.addClass( 'selected' ).show();
		//wpmoly_widget.hide();

	};

	wpmoly.widget.hide = function() {
		$( wpmoly_widget.select_status ).not( '.selected' ).hide();
		$( wpmoly_widget.select_media ).not( '.selected' ).hide();
		$( wpmoly_widget.select_rating ).not( '.selected' ).hide();
		$( wpmoly_widget.select_meta_select ).not( '.selected' ).hide();
	}

wpmoly_widget.init();
