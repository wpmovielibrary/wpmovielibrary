
wpmoly = window.wpmoly || {};

var Search = wpmoly.view.Search || {};

_.extend( Search, {

	HistoryItem: wp.Backbone.View.extend({

		className: 'history-item',

		template: wp.template( 'wpmoly-search-history-item' ),

		/**
		 * Render the View.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		render: function() {

			var data = this.model.toJSON()
			    data.date = data.date.toAPITimeString();

			this.$el.html( this.template( data ) );

			return this;
		}

	}),

	History: wp.Backbone.View.extend({

		className: 'wpmoly-search-history-container',

		template: wp.template( 'wpmoly-search-history' ),

		events: {
			'click [data-action="clean-history"]' : 'clean'
		},

		_viewsByCid: [],

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			var options = options || {};

			this.controller = options.controller;
			this.collection = options.controller.status;

			wpmoly.on( 'history:toggle', this.toggle, this );

			this.listenTo( this.collection, 'add',    this.createSubviews );
			this.listenTo( this.collection, 'remove', this.removeSubviews );
			this.listenTo( this.collection, 'reset',  this.render );
		},

		/**
		 * Render the View.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		render: function() {

			this.$el.html( this.template() );

			return this;
		},

		/**
		 * Create subviews for models added to the collection.
		 *
		 * @param    object    model
		 * @param    object    collection
		 * @param    object    options
		 *
		 * @return   void
		 */
		createSubviews: function( model, collection, options ) {

			var view = new wpmoly.view.Search.HistoryItem({ model: model });
			this._viewsByCid[ model.cid ] = view;

			this.views.add( '.history-items', view );

			this.scroll();
		},

		/**
		 * Remove subviews when models are removed from the collection.
		 *
		 * @param    object    model
		 * @param    object    collection
		 * @param    object    options
		 *
		 * @return   void
		 */
		removeSubviews: function( model, collection, options ) {

			var view = this._viewsByCid[ model.cid ];
			delete this._viewsByCid[ model.cid ];

			if ( view ) {
				view.remove();
			}

			this.scroll();
		},

		/**
		 * Create subviews and scroll to bottom when ready.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		ready: function() {

			if ( this.collection.length && _.isEmpty( this.views.all() ) ) {
				this.collection.map( function( model ) {
					this.createSubviews( model );
				}, this );
			}

			this.scroll();

			return this;
		},

		/**
		 * Scroll parent element to bottom.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		scroll: function() {

			var parent = this.$el.parent()[0];
			    parent.scrollTop = parent.scrollHeight;
		},

		/**
		 * Empty the history.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		clean: function() {

			wpmoly.trigger( 'history:clean' );
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

			wpmoly.trigger( 'history:open' );
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

			wpmoly.trigger( 'history:close' );
		},
	}),

});