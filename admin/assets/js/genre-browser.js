wpmoly = window.wpmoly || {};

wpmoly.browser = wpmoly.browser || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new GenreBrowser Browser instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} browser GenreBrowser browser DOM Element.
	 *
	 * @return {object} Browser instance.
	 */
	var Browser = function( browser ) {

		var terms = new wp.api.collections.Genres;

		var controller = new GenreBrowser.controller.Browser( [], {
			terms : terms,
		} );

		var view = new GenreBrowser.view.Browser({
			el         : browser,
			controller : controller,
		});

		view.$el.addClass( 'term-browser genre-browser' );

		// Hide loading animation.
		terms.once( 'sync error', function() {
			wpmoly.$( '.wpmoly-container' ).removeClass( 'loading' );
		} );

		// Load genres.
		terms.fetch({
			data : {
				context  : 'edit',
				orderby  : 'name',
				per_page : 20,
			}
		});

		/**
		 * GenreBrowser browser instance.
		 *
		 * Provide a set of useful functions to interact with the genres
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

	var GenreBrowser = wpmoly.browser.genres = TermBrowser;

	/**
	 * TermBrowser 'Discover' Block Controller.
	 *
	 * @since 1.0.0
	 */
	GenreBrowser.controller.BrowserBlock = TermBrowser.controller.BrowserBlock.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			var nodes = new wpmoly.api.collections.Genres;

			this.counts = nodes.counts;

			this.bindEvents();
		},

	});

	/**
	 * GenreBrowser 'Add New' Block View.
	 *
	 * @since 1.0.0
	 */
	GenreBrowser.controller.AddNewBlock = TermBrowser.controller.AddNewBlock.extend({

		model : wp.api.models.Genres,

		collection : wp.api.collections.Genres,

	});

	/**
	 * GenreBrowser Pagination Menu View.
	 *
	 * @since 1.0.0
	 */
	GenreBrowser.view.BrowserPagination = TermBrowser.view.BrowserPagination.extend({

		className : 'term-browser-menu term-browser-pagination genre-browser-menu genre-browser-pagination',

	});

	/**
	 * GenreBrowser Item View.
	 *
	 * @since 1.0.0
	 */
	GenreBrowser.view.BrowserItem = TermBrowser.view.BrowserItem.extend({

		className : 'genre term',

	});

	/**
	 * GenreBrowser Content View.
	 *
	 * @since 1.0.0
	 */
	GenreBrowser.view.BrowserContent = TermBrowser.view.BrowserContent.extend({

		className : 'term-browser-content genre-browser-content',

	});

	/**
	 * Create genres browser instance.
	 *
	 * @since 1.0.0
	 */
	GenreBrowser.loadBrowser = function() {

		var browser = document.querySelector( '#wpmoly-genre-browser' );
		if ( browser ) {
			GenreBrowser.browser = new Browser( browser );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 1.0.0
	 */
	GenreBrowser.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			GenreBrowser.loadBrowser();
			TermBrowser.loadSidebar();
		} );

		return GenreBrowser;
	};

})( jQuery, _, Backbone );

wpmoly.runners['genrebrowser'] = wpmoly.browser.genres;
