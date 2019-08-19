wpmoly = window.wpmoly || {};

wpmoly.browser = wpmoly.browser || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new ActorBrowser Browser instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} browser ActorBrowser browser DOM Element.
	 *
	 * @return {object} Browser instance.
	 */
	var Browser = function( browser ) {

		var terms = new wp.api.collections.Actors;

		var controller = new ActorBrowser.controller.Browser( [], {
			terms : terms,
		} );

		var view = new ActorBrowser.view.Browser({
			el         : browser,
			controller : controller,
		});

		view.$el.addClass( 'term-browser actor-browser' );

		// Hide loading animation.
		terms.once( 'sync error', function() {
			wpmoly.$( '.wpmoly-container' ).removeClass( 'loading' );
		} );

		// Load actors.
		terms.fetch({
			data : {
				context  : 'edit',
				orderby  : 'name',
				per_page : 20,
			}
		});

		/**
		 * ActorBrowser browser instance.
		 *
		 * Provide a set of useful functions to interact with the actors
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

	var ActorBrowser = wpmoly.browser.actors = TermBrowser;

	/**
	 * TermBrowser 'Discover' Block Controller.
	 *
	 * @since 1.0.0
	 */
	ActorBrowser.controller.BrowserBlock = TermBrowser.controller.BrowserBlock.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			var nodes = new wpmoly.api.collections.Actors;

			this.counts = nodes.counts;

			this.bindEvents();
		},

	});

	/**
	 * ActorBrowser 'Add New' Block View.
	 *
	 * @since 1.0.0
	 */
	ActorBrowser.controller.AddNewBlock = TermBrowser.controller.AddNewBlock.extend({

		model : wp.api.models.Actors,

		collection : wp.api.collections.Actors,

	});

	/**
	 * ActorBrowser Pagination Menu View.
	 *
	 * @since 1.0.0
	 */
	ActorBrowser.view.BrowserPagination = TermBrowser.view.BrowserPagination.extend({

		className : 'term-browser-menu term-browser-pagination actor-browser-menu actor-browser-pagination',

	});

	/**
	 * ActorBrowser Item View.
	 *
	 * @since 1.0.0
	 */
	ActorBrowser.view.BrowserItem = TermBrowser.view.BrowserItem.extend({

		className : 'term actor',

	});

	/**
	 * ActorBrowser Content View.
	 *
	 * @since 1.0.0
	 */
	ActorBrowser.view.BrowserContent = TermBrowser.view.BrowserContent.extend({

		className : 'term-browser-content actor-browser-content',

	});

	/**
	 * Create genres browser instance.
	 *
	 * @since 1.0.0
	 */
	ActorBrowser.loadBrowser = function() {

		var browser = document.querySelector( '#wpmoly-actor-browser' );
		if ( browser ) {
			ActorBrowser.browser = new Browser( browser );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 1.0.0
	 */
	ActorBrowser.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			ActorBrowser.loadBrowser();
			TermBrowser.loadSidebar();
		} );

		return ActorBrowser;
	};

})( jQuery, _, Backbone );

wpmoly.runners['actorbrowser'] = wpmoly.browser.actors;
