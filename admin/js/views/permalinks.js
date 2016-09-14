
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	Permalinks: Backbone.View.extend({

		el: '#wpmoly-permalinks',

		events: {
			//'change #wpmoly_permalinks_movie_base' : 'updateMovieBase'
			'focus .custom-value' : 'selectCustomPermalink'
		},

		initialize: function() {

			this.bindEvents();
		},

		/**
		 * Hide notice when user selects a permalink structure.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		bindEvents: function() {

			var $selection = wpmoly.$( 'input[name="selection"]' );
			$selection.on( 'change', function( event ) {
				var $elem = wpmoly.$( event.currentTarget ),
				  enabled = '' !== $elem.val();

				wpmoly.$( '#wpmoly-permalinks-notice' ).toggle( ! enabled );
				wpmoly.$( '#wpmoly-permalinks' ).toggle( enabled );
			} );

			return this;
		},

		/**
		 * Replace %movie_base% in examples.
		 * 
		 * TODO: make it work?
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'change' event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		updateMovieBase: function( event ) {

			var $elem = this.$( event.currentTarget ),
			 $samples = this.$( '[name="wpmoly_permalinks[movie_permalink]"]' );

			_.each( $samples, function( sample ) {
				var $sample = this.$( sample ).parents( 'tr' ).find( 'code' ),
				       base = _wpmolyPermalinks.movie_base,
				       text = $sample.text().replace( '/' + base + '/', '/' + $elem.val() + '/' );
				    $sample.text( text );
			}, this );

			return this;
		},

		/**
		 * Select matching RADIO input when a custom text field is
		 * focused.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'focus' event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		selectCustomPermalink: function( event ) {

			var id = event.currentTarget.id;
			this.$( '#' + id.replace( '_value', '' ) ).prop( 'checked', true );

			return this;
		}
	})
} );
