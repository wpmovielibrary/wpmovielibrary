
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Settings: Backbone.Model.extend({

		defaults: {
			collection_autocomplete : true,
			genre_autocomplete      : true,
			actor_autocomplete      : true,
			actor_limit             : false,
			api_language            : 'en',
			api_paginate            : true,
			api_adult               : false,

			posters_featured        : true,
			posters_autoimport      : false,
			posters_limit           : 10,
			posters_size            : 'original',

			backdrops_autoimport    : false,
			backdrops_limit         : 10,
			backdrops_size          : 'original',
		},

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 */
		initialize: function( attributes, options ) {

			var options = options || {};
			this.controller = options.controller;

			this.post_id = options.post_id || '';
		},

		/**
		 * Save the Settings.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    options
		 * 
		 * @return   deferred
		 */
		save: function( options ) {

			return this.sync( 'save', this, options );
		},

		/**
		 * Backbone.sync() override to allow custom queries.
		 * 
		 * @since    3.0
		 *
		 * @param    string    method Are we saving or is it a regular sync?
		 * @param    object    model Current model
		 * @param    object    options Query options
		 * 
		 * @return   mixed
		 */
		sync: function( method, model, options ) {

			if ( 'save' == method ) {

				var settings = {};
				_.each( model.toJSON(), function( value, key ) {
					settings[ key.replace( '_', '-' ) ] = value;
				}, this );

				this.controller.status.start({
					icon    : 'icon-settings',
					message : wpmolyL10n.savingSettings
				});

				var self = this;
				return wp.ajax.send( 'wpmoly_save_settings', {
					data: {
						settings: settings,
						//nonce:   ''
					},
					success: function( response ) {
						wpmoly.info( response );
						self.controller.status.stop({ message: wpmolyL10n.settingsSaved });
					},
					error: function( response ) {
						wpmoly.error( response );
						self.controller.status.stop({ message: wpmolyL10n.settingsError });
					}
				} );

			} else {
				return Backbone.prototype.sync.apply( this, arguments );
			}
		}
	})

} );
