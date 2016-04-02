
wpmoly = window.wpmoly || {};

var media = wp.media,
     Post = media.view.MediaFrame.Post,
     l10n = media.view.l10n;

media.view.MediaFrame.Post = Post.extend({

	/**
	 * Initialize the Content View.
	 * 
	 * Replace wp.media.view.MediaFrame.Post to add a new tab in the left
	 * menu and custom buttons in the main Toolbar.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	initialize: function() {

		Post.prototype.initialize.apply( this, arguments );

		this.imagesController = new wpmoly.controller.Modal.Modal( {}, {
			frame:    this,
			autoload: true
		} );

		var options = {
			id      : 'movie-images',
			router  : 'movie-images-router',
			toolbar : 'movie-images-toolbar',
			menu    : 'default',
			title   : wpmolyL10n.modalTabTitle,
			tabs    : {
				backdrop: {
					text:          wpmolyL10n.availableBackdrops,
					defaultTab:    true,
					fetchOnRender: true
				},
				poster: {
					text: wpmolyL10n.availablePosters
				}
			},
			priority: 100 // places it above Insert From URL
		};

		for ( var tab in options.tabs ) {
			// Content
			this.on( 'content:render:' + tab, _.bind( this.imagesContentRender, this, options, tab ) );
			// Set the default tab
			if ( options.tabs[ tab ].defaultTab ) {
				options.content = tab;
			}
		}

		// Required for Frame to work as supposed
		this.states.add([
			new wpmoly.controller.Modal.State( options )
		]);

		// Custom views
		this.on( 'router:create:movie-images-router', this.createRouter, this );
		this.on( 'router:render:movie-images-router', _.bind( this.imagesRouterRender, this, options ) );
		this.on( 'toolbar:create:movie-images-toolbar', this.imagesToolbarCreate, this );

		this.on( 'toolbar:render:main-insert', function( toolbar ) {
			toolbar.views.add( new wpmoly.view.Modal.ImagesSelection({
				controller : this,
				toolbar    : toolbar,
				selection  : toolbar.selection
			}) );
		}, this );
	},

	/**
	 * Render the Router View.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    options
	 * @param    View      view
	 * 
	 * @return   void
	 */
	imagesRouterRender : function( options, view ) {

		var tabs = {};

		for ( var tab in options.tabs ) {
			tab_id = tab;
			tabs[tab_id] = {
				text: options.tabs[ tab ].text
			};
		}

		view.set( tabs );

	},

	/**
	 * Create the Toolbar View.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    toolbar
	 * 
	 * @return   void
	 */
	imagesToolbarCreate : function( toolbar ) {

		toolbar.view = new wpmoly.view.Modal.Toolbar({
			controller : this,
			selection  : this.imagesController.selection
		});

	},

	/**
	 * Render the Content View.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    options
	 * @param    View      view
	 * 
	 * @return   void
	 */
	imagesContentRender : function( options, tab ) {

		if ( this.imagesBrowser ) {
			this.imagesBrowser.remove();
		}

		this.imagesBrowser = new wpmoly.view.Modal.ImagesBrowser({
			frame      : this,
			controller : this.imagesController
		});

		this.content.set( this.imagesBrowser );

	},

});
