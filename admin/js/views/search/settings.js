
wpmoly = window.wpmoly || {};

var Search = wpmoly.view.Search || {};

_.extend( Search, {

	Settings: wp.Backbone.View.extend({

		className: 'wpmoly-search-settings-container',

		template: wp.template( 'wpmoly-search-settings' ),

		events: {
			/*'click [data-setting="api-adult"]'               : 'switchSetting',
			'click [data-setting="api-paginate"]'            : 'switchSetting',
			'change [data-setting="search-year"]'            : 'setSetting',
			'change [data-setting="search-pyear"]'           : 'setSetting',
			'click [data-setting="api-language"]'            : 'setSetting',
			'click [data-setting="collection-autocomplete"]' : 'switchSetting',
			'click [data-setting="actor-autocomplete"]'      : 'switchSetting',
			'click [data-setting="genre-autocomplete"]'      : 'switchSetting',
			'change [data-setting="actor-limit"]'            : 'setSetting',
			'click [data-setting="hide-existing-backdrops"]' : 'switchSetting',
			'click [data-setting="hide-existing-posters"]'   : 'switchSetting'*/
			'click button[data-set-setting]'      : 'setSetting',
			'change input[data-set-setting]'      : 'setSetting',
			'input [data-switch-setting]'         : 'switchSetting',
			'click [data-action="save-settings"]' : 'saveSettings'
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			var options = options || {};

			this.model      = options.model;
			this.controller = options.controller;

			wpmoly.on( 'settings:toggle', this.toggle, this );

			this.listenTo( this.model, 'change', this.render );

			this.render();
		},

		/**
		 * Toggle the setting panel.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		toggle: function() {

			if ( this.opened ) {
				this.close();
			} else {
				this.open();
			}
		},

		/**
		 * Open the setting panel.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		open: function() {

			this.opened = true;
			this.$el.slideDown( 250 );

			wpmoly.trigger( 'settings:open' );
		},

		/**
		 * Close the setting panel.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		close: function() {

			this.opened = false;
			this.$el.slideUp( 150 );

			wpmoly.trigger( 'settings:close' );
		},

		/**
		 * Toggle a boolean setting.
		 *
		 * @since    3.0
		 *
		 * @param    object   JS 'click' event
		 *
		 * @return   void
		 */
		switchSetting: function( event ) {

			var $elem = this.$( event.currentTarget ),
			  setting = $elem.attr( 'data-switch-setting' ),
			    value = $elem.attr( 'data-value' );

			if ( 'true' === value ) {
				value = false;
			} else {
				value = true;
			}

			this.model.set( s.underscored( setting ), value );
		},

		/**
		 * Set a regular setting with its new value.
		 *
		 * @since    3.0
		 *
		 * @param    object   JS 'click' event
		 *
		 * @return   void
		 */
		setSetting: function( event ) {

			var elem = event.currentTarget
			   $elem = this.$( elem ),
			 setting = $elem.attr( 'data-set-setting' ),
			   value = 'INPUT' === elem.nodeName ? $elem.val() : $elem.attr( 'data-value' );

			this.model.set( s.underscored( setting ), value );
		},

		/**
		 * Save the current set of settings.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		saveSettings: function() {

			wpmoly.trigger( 'settings:save' );
		},

		/**
		 * Render the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		render: function() {

			this.$el.html( this.template( this.model.toJSON() ) );

			return this;
		},
	}),

});