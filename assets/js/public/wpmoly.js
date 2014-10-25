
$ = $ || jQuery;

window.wpmoly = window.wpmoly || {};

	wpmoly.init = function() {

		$( '.wpmoly-list' ).change(function() {
			if ( this.options[ this.selectedIndex ].value.length > 0 )
				location.href = this.options[ this.selectedIndex ].value;
		});

		$( '.hide-if-js' ).hide();
		$( '.hide-if-no-js' ).removeClass( 'hide-if-no-js' );
	};

	wpmoly.headbox = wpmoly_headbox = {};

		wpmoly.headbox.toggle = function( tab ) {

			var $tab = $( '#' + tab ),
			   $tabs = $( '.wpmoly.headbox.movie.content > .content' );

			console.log( $tab, $tabs );

			if ( undefined != $tab ) {
				$tabs.hide();
				$tab.show();
			}
		};

	wpmoly.init();