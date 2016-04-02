
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	Metabox: Backbone.View.extend({

		events: {
			'click .navigate': 'browse'
		},

		initialize: function() {

			this.$( '.wpmoly-meta-menu.css-powered' ).removeClass( 'css-powered' );
			this.$( '.tab.default, .panel.default' ).removeClass( 'default' ).addClass( 'active' );
		},

		browse: function( event ) {

			event.preventDefault();

			var $elem = this.$( event.currentTarget ),
			     $tab = $elem.parent( 'li.tab' ),
			   $panel = this.$( event.currentTarget.hash ),
			    $_tab = this.$( '.tab.active' ),
			  $_panel = this.$( '.panel.active' );

			this.trigger( 'close:panel', $_panel, $_tab );

			$_tab.removeClass( 'active' );
			$_panel.removeClass( 'active' );

			this.trigger( 'open:panel', $panel, $tab );

			$tab.addClass( 'active' );
			$panel.addClass( 'active' );
		}
	})
} );
