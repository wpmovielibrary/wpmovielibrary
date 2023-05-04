
wpmoly = wpmoly || {};

_.extend( wpmoly, { widgets: {} } );

_.extend( wpmoly.widgets, {

	_widgets: [],

	Model: {
		Movie: Backbone.Model.extend({

			defaults: {
				select:        'date',
				select_status: 'all',
				select_media:  'all',
				select_rating: 'all',
				select_meta:   'all'
			},

			
		})
	},

	View: {
		Movie: Backbone.View.extend({

			events: {
				'change [data-select="select"]':      'switch_select',
				'change [data-select-meta="select"]': 'switch_meta_select',
			},

			initialize: function() {

				this.hide_selects();

				_.each( this.model.attributes, function( value, key ) {
					value = this.$( '.wpmoly-movies-widget-' + key.replace( '_', '-' ) ).val() || '';
					this.model.attributes[ key ] = value;
				}, this );

				this.model.on( 'change:select',      this.toggle_select, this );
				this.model.on( 'change:select_meta', this.toggle_select_meta, this );
			},

			hide_selects: function() {

				this.$( '.wpmoly-movies-widget-select' ).not( '.wpmoly-movies-widget-select-select' ).not( '.selected' ).hide();
				this.$( '.wpmoly-movies-widget-meta-select' ).not( '.selected' ).hide();
			},

			switch_select: function( event ) {

				var $elem = this.$( event.currentTarget ),
				   select = $elem.val();

				this.model.set({ select: select });
			},

			switch_meta_select: function( event ) {

				var $elem = this.$( event.currentTarget ),
				   select = $elem.val();

				this.model.set( 'select_meta', select );
			},

			toggle_select: function( model, value ) {

				this.hide_selects();
				this.$( '.wpmoly-movies-widget-select.selected' ).removeClass( 'selected' ).hide();
				this.$( '.wpmoly-movies-widget-select-' + value ).addClass( 'selected' ).show();
			},

			toggle_select_meta: function( model, value ) {

				console.log( value );
				this.$( '.wpmoly-movies-widget-meta-select.selected' ).removeClass( 'selected' ).hide();
				this.$( '.wpmoly-movies-widget-select-' + value ).addClass( 'selected' ).show();
			}
		})
	},
},
{
	init: function() {

		var $widgets = Backbone.$( '[data-wpmoly=movies-widget]' );

		_.each( $widgets, function( widget ) {
			var  _widget = new wpmoly.widgets.Model.Movie;
			_widget.view = new wpmoly.widgets.View.Movie({
				el:    widget,
				model: _widget,
			});
			this._widgets.push( _widget );
		}, this );
	}

} );

jQuery( document ).ready( function() {
	wpmoly.widgets.init();
});
