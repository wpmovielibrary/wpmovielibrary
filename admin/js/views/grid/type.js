
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

_.extend( Grid, {

	Type: wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-grid-builder-type-metabox' ),

		events: {
			'click [data-action="grid-type"]'  : 'setType',
			'click [data-action="grid-mode"]'  : 'setMode',
			'click [data-action="grid-theme"]' : 'setTheme'
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.model = this.controller.builder;

			this.bindEvents();
		},

		bindEvents: function() {

			this.listenTo( this.model, 'change:type',  this.render );
			this.listenTo( this.model, 'change:mode',  this.render );
			this.listenTo( this.model, 'change:theme', this.render );
		},

		setType: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			this.controller.setType( value );
		},

		setMode: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			this.controller.setMode( value );
		},

		setTheme: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			this.controller.setTheme( value );
		},

		render: function() {

			this.$el.html( this.template(
				this.model.toJSON()
			) );
		}
	})

});
