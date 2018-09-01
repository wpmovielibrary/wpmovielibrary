wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new Metabox instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} metabox Metabox DOM element.
	 *
	 * @return {object} Metabox instance.
	 */
	Metabox = function( metabox ) {

		var metabox = new Metaboxes.view.Metabox( { el : metabox } );

		metabox.$el.removeClass( 'css-powered' ).addClass( 'js-powered' );

		return this;
	};

	/**
	 * Metaboxes wrapper.
	 *
	 * @since 1.0.0
	 */
	Metaboxes = wpmoly.metaboxes = {

		/**
		 * List of grid instances.
		 *
		 * This should not be used directly. Use Grids.get()
		 * instead.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		metaboxes : [],

		/**
		 * List of editor views.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		view : {},

		/**
		 * Add a Metabox instance.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} metabox Metabox DOM Element.
		 * @param {object} options Metabox options.
		 *
		 * @return {Metabox} Metabox instance.
		 */
		add : function( metabox, options ) {

			var metabox = new Metabox( metabox, options );

			this.metaboxes.push( metabox );

			return metabox;
		},
	};

	/**
	 * Metabox View.
	 *
	 * @since 1.0.0
	 */
	Metaboxes.view.Metabox = Backbone.View.extend({

		events : {
			'click .navigate': 'browse',
		},

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 */
		initialize : function() {

			this.$( '.wpmoly-meta-menu.css-powered' ).removeClass( 'css-powered' );
			this.$( '.tab.default, .panel.default' ).removeClass( 'default' ).addClass( 'active' );
		},

		/**
		 * Switch tabs.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} JS 'click' event.
		 */
		browse : function( event ) {

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
		},

	});

	/**
	 * Run Forrest, run!
	 *
	 * @since 1.0.0
	 */
	Metaboxes.run = function() {

		var metaboxes = document.querySelectorAll( '.wpmoly-tabbed-metabox' );
		if ( metaboxes ) {
			_.each( metaboxes, function( metabox ) {
				Metaboxes.add( metabox );
			} );
		}
	};

})( jQuery, _, Backbone );

wpmoly.runners['metaboxes'] = wpmoly.metaboxes;
