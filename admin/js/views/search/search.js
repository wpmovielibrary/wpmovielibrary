
wpmoly = window.wpmoly || {};

var Search = wpmoly.view.Search = {};

_.extend( Search, {

	SearchForm: wp.Backbone.View.extend({

		className: 'wpmoly-search-form-container',

		template: wp.template( 'wpmoly-search-form' ),

		events: {
			'click [data-action="toggle-settings"]' : 'toggleSettings',
			'click [data-action="search"]'          : 'search',
			'click [data-action="update"]'          : 'update',
			'click [data-action="empty"]'           : 'empty',
			'change #wpmoly-search-query'           : 'change',
			'keypress #wpmoly-search-query'         : 'keypress'
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

			var $window = wpmoly.$( window ),
			      event = 'resize.' + this.className;

			this.on( 'ready', _.debounce( this.resize, 50 ), this );

			_.bindAll( this, 'resize' );
			this.bindEvents();

			$window.off( event ).on( event, _.debounce( this.resize, 50 ) );
		},

		/**
		 * Bind events. Mostly used to activate/deactivate the settings
		 * toggle button.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		bindEvents: function() {

			wpmoly.on( 'settings:open', function() {
				this.$el.addClass( 'settings-opened' );
			}, this );

			wpmoly.on( 'settings:close', function() {
				this.$el.removeClass( 'settings-opened' );
			}, this );
		},

		/**
		 * Open/close the settings panel.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		toggleSettings: function() {

			wpmoly.trigger( 'settings:toggle' );
		},

		/**
		 * Resize the query input to fit exactly the container's
		 * remaining width.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		resize: function() {

			var container = this.$el.width(),
			     settings = this.$( '.wpmoly-search-settings' ).width(),
			        tools = this.$( '.wpmoly-search-tools' ).width(),
			        width = container - settings - tools - 2;

			this.$( '.wpmoly-search-query' ).width( width );
		},

		/**
		 * Update model with query input value.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		change: function() {

			var query = this.$( '#wpmoly-search-query' ).val() || '';

			this.model.set({ query: query });
		},

		/**
		 * Trigger search.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		search: function() {

			wpmoly.trigger( 'api:search', this.model );
		},

		/**
		 * Update the meta.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		update: function() {

			this.model.set({ query: wpmoly.editor.controller.meta.get( 'tmdb_id' ) });

			wpmoly.trigger( 'editor:meta:reload' );
		},

		/**
		 * Empty the meta.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		empty: function() {

			wpmoly.trigger( 'editor:meta:empty' );
		},

		/**
		 * Trigger search when Enter is hit.
		 *
		 * @since    3.0
		 *
		 * @param    object    JS 'keypress'
		 *
		 * @return   void
		 */
		keypress: function( event ) {

			var code = event.keyCode ? event.keyCode : event.which;
			if ( 13 !== code ) {
				return;
			}

			event.preventDefault();

			this.change();
			this.search();
		}
	})

});

_.extend( Search, {

	Search: wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-search' ),

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};

			this.set_regions();

			this.render();
		},

		/**
		 * Set Regions (subviews).
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		set_regions: function() {

			this.status   = new wpmoly.view.Search.Status({ controller: this.controller });
			this.history  = new wpmoly.view.Search.History({ controller: this.controller });
			this.settings = new wpmoly.view.Search.Settings({ controller: this.controller, model: this.controller.settings });
			this.search   = new wpmoly.view.Search.SearchForm({ controller: this.controller, model: this.controller.search });
			this.results  = new wpmoly.view.Search.Results({ controller: this.controller });

			this.views.set( '#wpmoly-search-status',   this.status );
			this.views.set( '#wpmoly-search-history',  this.history );
			this.views.set( '#wpmoly-search-settings', this.settings );
			this.views.set( '#wpmoly-search-form',     this.search );
			this.views.set( '#wpmoly-search-results',  this.results );

			return this;
		}
	})
} );
