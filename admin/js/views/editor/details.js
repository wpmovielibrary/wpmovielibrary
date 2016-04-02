
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	Details: wpmoly.view.Meta.extend({

		template: wp.template( 'wpmoly-editor-details' ),

		events: {
			'change [data-meta-type="detail"]': 'update'
		},

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.model = this.controller.details;

			this.listenTo( this.model, 'change', this.change );

			this.listenTo( this.model, 'autosave:start', this.saving );
			this.listenTo( this.model, 'autosave:done',  this.saved );
			this.listenTo( this.model, 'autosave:fail',  this.notsaved );
		},

		/**
		 * Render the view.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			var data = this.model.toJSON();

			this.$el.html( this.template( data ) );

			var $select = this.$( '.select2 select' );
			_.each( $select, function( select ) {
				var $select = this.$( select ),
				placeholder = $select.attr( 'placeholder' ) || '';

				$select.select2({
					placeholder: placeholder
				});
			}, this );

			return this;
		},

	})

} );
