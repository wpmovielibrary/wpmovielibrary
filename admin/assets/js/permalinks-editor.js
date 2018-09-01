wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new Permalinks Editor instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} editor PermalinksEditor DOM element.
	 *
	 * @return {object} PermalinksEditor instance.
	 */
	var PermalinksEditor = function( editor ) {

		var editor = new Permalinks.view.Editor( { el : editor } );

		return this;
	};

	/**
	 * Permalinks wrapper.
	 *
	 * @since 1.0.0
	 */
	var Permalinks = wpmoly.permalinks = {

		/**
		 * List of editor views.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		view : {},
	};

	/**
	 * Permalinks Editor View.
	 *
	 * @since 1.0.0
	 */
	Permalinks.view.Editor = Backbone.View.extend({

		events : {
			'click [data-name]'   : 'updateCustomPermalink',
			'focus .custom-value' : 'selectCustomPermalink',
		},

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 */
		initialize : function() {

			this.bindEvents();
		},

		/**
		 * Hide notice when user selects a permalink structure.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		bindEvents : function() {

			var $selection = $( 'input[name="selection"]' );
			$selection.on( 'change', function( event ) {
				var $elem = $( event.currentTarget ),
				  enabled = '' !== $elem.val();

				$( '#wpmoly-permalinks-notice' ).toggle( ! enabled );
				$( '#wpmoly-permalinks' ).toggle( enabled );
			} );

			return this;
		},

		/**
		 * Replace %series_base% in examples.
		 *
		 * @TODO make it work?
		 *
		 * @since 1.0.0
		 *
		 * @param {object} JS 'change' event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		updateSeriesBase : function( event ) {

			var $elem = this.$( event.currentTarget ),
			 $samples = this.$( '[name="wpmoly_permalinks[series_permalink]"]' );

			_.each( $samples, function( sample ) {
				var $sample = this.$( sample ).parents( 'tr' ).find( 'code' ),
				       base = _wpmolyPermalinks.series_base,
				       text = $sample.text().replace( '/' + base + '/', '/' + $elem.val() + '/' );
				    $sample.text( text );
			}, this );

			return this;
		},

		/**
		 * Update custom permalink value.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} JS 'change' event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		updateCustomPermalink : function( event ) {

			var $elem = this.$( event.currentTarget ),
			  $custom = this.$( '#custom_' + $elem.attr( 'data-name' ) + '_value' );

			$custom.val( $elem.val() );

			return this;
		},

		/**
		 * Select matching RADIO input when a custom text field is
		 * focused.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} JS 'focus' event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		selectCustomPermalink : function( event ) {

			var id = event.currentTarget.id;
			this.$( '#' + id.replace( '_value', '' ) ).prop( 'checked', true );

			return this;
		},

	});

	/**
	 * Run Forrest, run!
	 *
	 * @since 1.0.0
	 */
	Permalinks.run = function() {

		var editor = document.querySelector( '#wpmoly-permalinks' );
		if ( editor ) {
			Permalinks.editor = new PermalinksEditor( editor );
		}

		return Permalinks;
	};

})( jQuery, _, Backbone );

wpmoly.runners['permalinks'] = wpmoly.permalinks;
