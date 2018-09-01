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

		var view = new GridBrowser.view.Browser({
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
			 * GridBrowser Menu View.
			 *
			 * @since 1.0.0
			 */
			BrowserMenu : PostBrowser.view.BrowserMenu.extend({

				className : 'post-browser-menu grid-browser-menu',

			}),

			/**
			 * GridBrowser Pagination Menu View.
			 *
			 * @since 1.0.0
			 */
			BrowserPagination : PostBrowser.view.BrowserPagination.extend({

				className : 'post-browser-menu post-browser-pagination grid-browser-menu grid-browser-pagination',

			}),

			/**
			 * GridBrowser Item View.
			 *
			 * @since 1.0.0
			 */
			BrowserItem : PostBrowser.view.BrowserItem.extend({

				className : 'post grid',

				template : wp.template( 'wpmoly-grid-browser-item' ),

			}),

			/**
			 * GridBrowser Content View.
			 *
			 * @since 1.0.0
			 */
			BrowserContent : PostBrowser.view.BrowserContent.extend({

				className : 'post-browser-content grid-browser-content',

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
