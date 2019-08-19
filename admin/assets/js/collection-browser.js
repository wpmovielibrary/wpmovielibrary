wpmoly = window.wpmoly || {};

wpmoly.browser = wpmoly.browser || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new CollectionBrowser Browser instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} browser CollectionBrowser browser DOM Element.
	 *
	 * @return {object} Browser instance.
	 */
	var Browser = function( browser ) {

		var terms = new wp.api.collections.Collections;

		var controller = new CollectionBrowser.controller.Browser( [], {
			terms : terms,
		} );

		var view = new CollectionBrowser.view.Browser({
			el         : browser,
			controller : controller,
		});

		view.$el.addClass( 'term-browser collection-browser' );

		// Hide loading animation.
		terms.once( 'sync error', function() {
			wpmoly.$( '.wpmoly-container' ).removeClass( 'loading' );
		} );

		// Load collections.
		terms.fetch({
			data : {
				context  : 'edit',
				orderby  : 'name',
				per_page : 20,
			}
		});

		/**
		 * CollectionBrowser browser instance.
		 *
		 * Provide a set of useful functions to interact with the collections
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var browser = {

			terms : terms,

			controller : controller,

			view : view,

		};

		return browser;
	};

	var TermBrowser = wpmoly.browser.terms;

	var CollectionBrowser = wpmoly.browser.collections = TermBrowser;

	/**
	 * TermBrowser 'Discover' Block Controller.
	 *
	 * @since 1.0.0
	 */
	CollectionBrowser.controller.BrowserBlock = TermBrowser.controller.BrowserBlock.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			var nodes = new wpmoly.api.collections.Collections;

			this.counts = nodes.counts;

			this.bindEvents();
		},

	});

	/**
	 * CollectionBrowser 'Add New' Block View.
	 *
	 * @since 1.0.0
	 */
	CollectionBrowser.controller.AddNewBlock = TermBrowser.controller.AddNewBlock.extend({

		model : wp.api.models.Collections,

		collection : wp.api.collections.Collections,

	});

	/**
	 * CollectionBrowser Pagination Menu View.
	 *
	 * @since 1.0.0
	 */
	CollectionBrowser.view.BrowserPagination = TermBrowser.view.BrowserPagination.extend({

		className : 'term-browser-menu term-browser-pagination collection-browser-menu collection-browser-pagination',

	});

	/**
	 * CollectionBrowser Item View.
	 *
	 * @since 1.0.0
	 */
	CollectionBrowser.view.BrowserItem = TermBrowser.view.BrowserItem.extend({

		className : 'term collection',

	});

	/**
	 * CollectionBrowser Content View.
	 *
	 * @since 1.0.0
	 */
	CollectionBrowser.view.BrowserContent = TermBrowser.view.BrowserContent.extend({

		className : 'term-browser-content collection-browser-content',

	});

	/**
	 * Create collections browser instance.
	 *
	 * @since 1.0.0
	 */
	CollectionBrowser.loadBrowser = function() {

		var browser = document.querySelector( '#wpmoly-collection-browser' );
		if ( browser ) {
			CollectionBrowser.browser = new Browser( browser );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 1.0.0
	 */
	CollectionBrowser.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			CollectionBrowser.loadBrowser();
			TermBrowser.loadSidebar();
		} );

		return CollectionBrowser;
	};

})( jQuery, _, Backbone );

wpmoly.runners['collectionbrowser'] = wpmoly.browser.collections;
