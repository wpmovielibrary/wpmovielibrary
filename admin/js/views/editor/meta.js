
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	Meta: Backbone.View.extend({

		template: wp.template( 'wpmoly-editor-meta' ),

		events: {
			'change [data-meta-type="meta"]': 'update'
		},

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.model = this.controller.meta;

			this.listenTo( this.model, 'change', this.change );

			wpmoly.on( 'editor:meta:autosave:start',  this.saving,   this );
			wpmoly.on( 'editor:meta:autosave:done',   this.saved,    this );
			wpmoly.on( 'editor:meta:autosave:failed', this.notsaved, this );
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

			return this;
		},

		/**
		 * Update the Model with new values.
		 * 
		 * @since    3.0
		 * 
		 * @param    Event     JS 'change' event.
		 */
		update: function( event ) {

			var $elem = this.$( event.currentTarget ),
			      key = $elem.attr( 'data-meta-key' ),
			    value = $elem.val();

			this.model.set( key, value );
		},

		/**
		 * Update the View to match Model changes.
		 * 
		 * @since    3.0
		 * 
		 * @param    Model     Model changed
		 * @param    object    Changes options
		 */
		change: function( model, options ) {

			_.each( model.changed, function( value, key ) {
				var $field = this.$( '[data-meta-key="' + key + '"]' );
				if ( $field.length ) {
					$field.val( value );
				}
			}, this );
		},

		/**
		 * Animate the field saving process.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    fields Changed fFields, usually one single field
		 * @param    string    status Saving or saved?
		 */
		saving: function( fields, status ) {

			_.each( fields, function( value, key ) {
				var $field = this.$( '[data-meta-key="' + key + '"]' ),
				     $elem = $field.parent( '.wpmoly-meta-value' );
				if ( $elem.length ) {
					if ( 'saving' === status ) {
						$elem.addClass( 'saving' );
					} else if ( 'saved' === status ) {
						$elem.removeClass( 'saving' );
						$elem.append( '<div class="saved-notice"><span class="wpmolicon icon-yes"></span></div>' );
						window.setTimeout( function() {
							wpmoly.$( '.saved-notice' ).fadeOut( 750 );
						}, 750 );
					} else if ( 'failed' === status ) {
						$elem.removeClass( 'saving' );
						$elem.append( '<div class="saved-notice"><span class="wpmolicon icon-no"></span></div>' );
						window.setTimeout( function() {
							wpmoly.$( '.saved-notice' ).fadeOut( 1500 );
						}, 1500 );
					} 
				}
			}, this );
		},

		/**
		 * Animate the field saving process.
		 * 
		 * Shortcut function for saving( field, 'saved' ).
		 * 
		 * @since    3.0
		 * 
		 * @param    object    fields Changed fFields
		 */
		saved: function( fields ) {

			this.saving( fields, 'saved' );
		},

		/**
		 * Animate the field saving process.
		 * 
		 * Shortcut function for saving( field, 'failed' ).
		 * 
		 * @since    3.0
		 * 
		 * @param    object    fields Changed fFields
		 */
		notsaved: function( fields ) {

			this.saving( fields, 'failed' );
		}
	})

} );
