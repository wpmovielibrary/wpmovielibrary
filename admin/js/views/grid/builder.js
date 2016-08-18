
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid = {};

_.extend( Grid, {

	Builder: wp.Backbone.View.extend({

		events: {
			'change .butterbean-control input'    : '_onchange',
			'change .butterbean-control select'   : '_onchange',
			'change .butterbean-control textarea' : '_onchange'
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

			this.set_regions();
			this.bindEvents();
		},

		/**
		 * Set Regions (subviews).
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		set_regions: function() {

			this.type = new wpmoly.view.Grid.Type({ controller: this.controller });

			this.views.set( '#wpmoly-grid-builder-type-metabox', this.type );

			this.togglePostbox( this.model, this.model.get( 'type' ) );
		},

		/**
		 * Bind events.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		bindEvents: function() {

			this.listenTo( this.model, 'change:type',  this.togglePostbox );
			this.listenTo( this.model, 'change:mode',  this.togglePostbox );
			this.listenTo( this.model, 'change:theme', this.togglePostbox );
		},

		/**
		 * Show/Hide ButterBean Metaboxes depending on grid type.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		togglePostbox: function( model, value, options ) {

			var $postbox = this.$( '#butterbean-ui-' + value + '-grid-settings' ),
			  $postboxes = this.$( '.butterbean-ui.postbox' );
			if ( ! $postbox.length ) {
				return;
			}

			$postboxes.removeClass( 'active' );
			$postbox.addClass( 'active' );
		},

		/**
		 * Handle setting change events.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'change' Event
		 * 
		 * @return   void
		 */
		_onchange : function( e ) {

			var value,
			 $control = this.$( e.target ).parents( '.butterbean-control' );

			switch ( e.target.type ) {
				case 'text':
				case 'textarea':
				case 'radio':
					value = e.target.value;
					break;
				case 'checkbox':
					value = e.target.checked ? '1' : '0';
					break;
				case 'checkboxes':
					var value = [],
					   $elems = this.$( e.target ).find( 'input:checked' );
					_.each( $elems, function( elem ) {
						value.push( elem.value );
					} );
					break;
				case 'select-one':
				case 'select-multiple':
					var value = [],
					   $elems = this.$( e.target ).find( 'option:selected' );
					_.each( $elems, function( elem ) {
						value.push( elem.value );
					} );
					break;
				default:
					break;
			}

			if ( _.isEmpty( value ) ) {
				return;
			}

			var name = $control.prop( 'id' ).replace( 'butterbean-control-_wpmoly_grid_', '' );

			this.model.set( name, value );
		}
	})

} );
