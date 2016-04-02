
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	StatusMessage: Backbone.Model.extend({

		defaults: {
			type    : 'status',
			icon    : 'icon-api',
			effect  : 'rotate',
			message : wpmolyL10n.ready,
			date    : 0
		},

		/**
		 * Initialize the Model.
		 *
		 * If no date is passed through the attributes parameter, set
		 * date to now.
		 *
		 * @since    3.0
		 *
		 * @param    object    attributes
		 * @param    object    options
		 *
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			var attributes = attributes || {},
			       options = options || {};

			if ( ! attributes.date ) {
				this.set({ date: new Date() });
			}
		}
	}),

	Status: Backbone.Collection.extend({

		model: wpmoly.model.StatusMessage,

		/**
		 * Initialize the Collection.
		 *
		 * @since    3.0
		 *
		 * @param    object    models
		 * @param    object    options
		 *
		 * @return   void
		 */
		initialize: function( models, options ) {

			var options = options || {};
			this.controller = options.controller;

			if ( _.isEmpty( models ) ) {
				var model = new wpmoly.model.StatusMessage;
				this.add( model );
			}

			this.post_id = options.post_id || '';

			// Bind events
			this.bindEvents();
		},

		/**
		 * Bind events.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		bindEvents: function() {

			wpmoly.on( 'status:start', this.start, this );
			wpmoly.on( 'status:stop',  this.stop,  this );
		},

		/**
		 * Add a new message to the top of the pile.
		 *
		 * @since    3.0
		 *
		 * @param    object    model
		 * @param    object    options
		 *
		 * @return   void
		 */
		stack: function( model, options ) {

			var status = new wpmoly.model.StatusMessage( model );

			this.add( status, { at: 0 } );
		},

		/**
		 * Add a new message to the top of the pile and start animating.
		 *
		 * @since    3.0
		 *
		 * @param    object    model
		 * @param    object    options
		 *
		 * @return   void
		 */
		start: function( model, options ) {

			this.stack( model, options );

			wpmoly.trigger( 'status:started' );
		},

		/**
		 * Add a new message to the top of the pilepile and stop animating.
		 *
		 * @since    3.0
		 *
		 * @param    object    model
		 * @param    object    options
		 *
		 * @return   void
		 */
		stop: function( model, options ) {

			this.stack( model, options );

			wpmoly.trigger( 'status:stopped' );
		},
	})

} );
