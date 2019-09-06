wpmoly = window.wpmoly || {};

wpmoly.browser = wpmoly.browser || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new GridBrowser Browser instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} browser GridBrowser browser DOM Element.
	 *
	 * @return {object} Browser instance.
	 */
	var Browser = function( browser ) {

		var posts = new wp.api.collections.Grids;

		var controller = new GridBrowser.controller.Browser( [], {
			posts : posts,
		} );

		var view = new GridBrowser.view.GridBrowser({
			el         : browser,
			controller : controller,
		});

		view.$el.addClass( 'post-browser grid-browser' );

		// Hide loading animation.
		posts.once( 'sync error', function() {
			wpmoly.$( '.wpmoly-container' ).removeClass( 'loading' );
		} );

		// Load grids.
		posts.fetch( { data : { context : 'edit' } } );

		/**
		 * GridBrowser browser instance.
		 *
		 * Provide a set of useful functions to interact with the grids
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var browser = {

			posts : posts,

			controller : controller,

			view : view,

		};

		return browser;
	};

	var PostBrowser = wpmoly.browser.posts;

	var GridBrowser = wpmoly.browser.grids = _.extend( PostBrowser, {

		/**
		 * Extend PostBrowser controllers.
		 *
		 * @since 1.0.0
		 */
		controller : _.extend( PostBrowser.controller, {

			/**
			 * GridBrowser 'Discover' Block Controller.
			 *
			 * @since 1.0.0
			 */
			BrowserBlock : PostBrowser.controller.BrowserBlock.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					var nodes = new wpmoly.api.collections.Grids;

					this.counts = nodes.counts;

					this.bindEvents();
				},
			}),

			/**
			 * GridBrowser 'Add New' Block View.
			 *
			 * @since 1.0.0
			 */
			AddNewBlock : PostBrowser.controller.AddNewBlock.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.model      = wp.api.models.Grids;
					this.collection = wp.api.collections.Grids;

					if ( this.bindEvents ) {
						this.bindEvents();
					}
				},

			}),

			/**
			 * GridBrowser 'Drafts' Block View.
			 *
			 * @since 1.0.0
			 */
			DraftsBlock : PostBrowser.controller.DraftsBlock.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.collection = wp.api.collections.Grids;

					if ( this.bindEvents ) {
						this.bindEvents();
					}
				},
			}),

			/**
			 * GridBrowser 'Trash' Block View.
			 *
			 * @since 1.0.0
			 */
			TrashBlock : PostBrowser.controller.TrashBlock.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.collection = wp.api.collections.Grids;

					if ( this.bindEvents ) {
						this.bindEvents();
					}
				},
			}),
		} ),

		/**
		 * Extend PostBrowser views.
		 *
		 * @since 1.0.0
		 */
		view : _.extend( PostBrowser.view, {

			/**
			 * GridBrowser Pagination Menu View.
			 *
			 * @since 1.0.0
			 */
			GridBrowserPagination : PostBrowser.view.BrowserPagination.extend({

				className : 'post-browser-menu post-browser-pagination grid-browser-menu grid-browser-pagination',

			}),

			/**
			 * GridBrowser Item View.
			 *
			 * @since 1.0.0
			 */
			GridBrowserItem : PostBrowser.view.BrowserItem.extend({

				className : 'post grid',

				template : wp.template( 'wpmoly-grid-browser-item' ),

				/**
				 * Render the View.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				render : function() {

					PostBrowser.view.BrowserItem.prototype.render.apply( this, arguments );

					var type = this.model.getMeta( wpmolyApiSettings.grid_prefix + 'type' ) || 'empty';

					this.$el.addClass( 'type-' + type  );

					return this;
				},

			}),

			/**
			 * GridBrowser Content View.
			 *
			 * @since 1.0.0
			 */
			GridBrowserContent : PostBrowser.view.BrowserContent.extend({

				className : 'post-browser-content grid-browser-content',

				/**
				 * Add new post item view.
				 *
				 * @since 1.0.0
				 *
				 * @param {object} model Post model.
				 *
				 * @return Returns itself to allow chaining.
				 */
				addItem : function( model ) {

					this.views.add( new PostBrowser.view.GridBrowserItem({
						controller : this.controller,
						parent     : this,
						model      : model,
					}) );

					return this;
				},

			}),

			/**
			 * GridBrowser View.
			 *
			 * @since 1.0.0
			 */
			GridBrowser : PostBrowser.view.Browser.extend({

				/**
				 * Set subviews.
				 *
				 * @since 1.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				setRegions : function() {

					var options = {
						controller : this.controller,
					};

					if ( ! this.content ) {
						this.content = new PostBrowser.view.GridBrowserContent( options );
					}

					if ( ! this.pagination ) {
						this.pagination = new PostBrowser.view.GridBrowserPagination( options );
					}

					this.views.add( this.content );
					this.views.add( this.pagination );

					return this;
				},

			}),

		} ),

		/**
		* Create grids browser instance.
		*
		* @since 1.0.0
		*/
		loadBrowser : function() {

			var browser = document.querySelector( '#wpmoly-grid-browser' );
			if ( browser ) {
				GridBrowser.browser = new Browser( browser );
			}
		},

		/**
		* Run Forrest, run!
		*
		* @since 1.0.0
		*/
		run : function() {

			if ( ! wp.api ) {
				return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
			}

			wp.api.loadPromise.done( function() {
				GridBrowser.loadBrowser();
				PostBrowser.loadSidebar();
			} );

			return GridBrowser;
		},

	} );

})( jQuery, _, Backbone );

wpmoly.runners['gridbrowser'] = wpmoly.browser.grids;
