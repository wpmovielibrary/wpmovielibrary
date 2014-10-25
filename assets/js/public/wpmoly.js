
$ = $ || jQuery;

window.wpmoly = window.wpmoly || {};

	wpmoly.init = function() {

		$( '.wpmoly-list' ).change(function() {
			if ( this.options[ this.selectedIndex ].value.length > 0 )
				location.href = this.options[ this.selectedIndex ].value;
		});

		$( '.wpmoly.headbox.movie.content > .content.hide-if-js' ).hide();
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