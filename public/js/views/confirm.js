
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	Confirm: wp.Backbone.View.extend({

		id: _.uniqueId( 'confirm-modal-' ),

		tagName: 'div',

		className: 'wpmoly-confirm-modal-container',

		template: wp.template( 'wpmoly-confirm-modal' ),

		events: {
			'click .backdrop, [data-action="cancel"]' : 'cancel',
			'click [data-action="confirm"]'           : 'confirm'
		},

		model: Backbone.Model,

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		initialize: function( options ) {

			var options = options || {};

			this.model    = options.model    || new this.model;
			this.result   = options.result   || false;
			this.callback = options.callback || function( ret ) { return ret };

			this.$container = wpmoly.$( document.body );

			this.render();

			wpmoly.$( window ).one( 'keydown', _.bind( this.keydown, this ) );

			return this;
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			var data = this.model.toJSON();

			this.$container.append( this.$el.html( this.template( data ) ) );

			this.$el.id = this.id;

			return this;
		},

		/**
		 * User cancelation. Trigger the 'cancel' event and execute
		 * the callback with false as a parameter.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		cancel: function( event ) {

			event.preventDefault();

			this.trigger( 'cancel' );

			this.callback.call( false );

			return this.close();
		},

		/**
		 * User confirmation. Trigger the 'confirm' event and execute
		 * the callback with true as a parameter.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		confirm: function( event ) {

			event.preventDefault();

			this.trigger( 'confirm' );

			this.callback.call( true );

			return this.close();
		},

		/**
		 * User hit the Enter key: confirm.
		 * User hit the Esc key: cancel.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'keydown' Event
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		keydown: function( event ) {

			var keyCode = event.keyCode ? event.keyCode : event.charCode;

			if ( 13 === keyCode ) {
				this.confirm( event );
			} else if ( 27 === keyCode ) {
				this.cancel( event );
			}
		},

		/**
		 * Open the confirmation modal.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		open: function() {

			this.$container.addClass( 'modal-open' );
			this.$el.show();

			return this;
		},

		/**
		 * Close the confirmation modal.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		close: function() {

			this.$container.removeClass( 'modal-open' );
			this.remove();

			return this;
		}
	})

});