wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new Headbox instance.
	 *
	 * @since    1.0.0
	 *
	 * @param    {Element}    headbox Headbox DOM element.
	 *
	 * @return   {object}     Headbox instance.
	 */
	Headbox = wpmoly.Headbox = function( headbox, options ) {

		var options = options || {};

		// Set a unique headbox ID to the headbox element.
		headbox.id  = _.uniqueId( 'wpmoly-headbox-' );

		var post_id = headbox.getAttribute( 'data-headbox' ),
		      theme = headbox.getAttribute( 'data-theme' );

		var views = {
			'default'    : Headboxes.view.DefaultHeadbox,
			'extended'   : Headboxes.view.ExtendedHeadbox,
			'vintage'    : Headboxes.view.VintageHeadbox,
			'allocine'   : Headboxes.view.AllocineHeadbox,
			'allocine-2' : Headboxes.view.Allocine2Headbox,
			'imdb-2'     : Headboxes.view.IMDb2Headbox,
		};

		if ( ! _.isEmpty( theme ) ) {
			if ( ! _.isUndefined( views[ theme ] ) ) {
				var view = new views[ theme ]({
					el : headbox
				});
			}
		}

		var headbox = {

			/**
			 * Grid ID.
			 *
			 * @since    1.0.0
			 *
			 * @var      int
			 */
			headbox_id : parseInt( post_id ),

			/**
			 * Grid selector.
			 *
			 * @since    1.0.0
			 *
			 * @var      string
			 */
			selector : headbox.id,
		};

		return headbox;
	};

	/**
	 * Headboxes Wrapper.
	 *
	 * Store controllers, views and headboxes objects.
	 *
	 * @since    1.0.0
	 */
	Headboxes = wpmoly.Headboxes = wpmoly.headboxes = {

		/**
		 * List of headbox instances.
		 *
		 * This should not be used directly. Use Headboxes.get()
		 * instead.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		headboxes : [],

		/**
		 * List of headbox views.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		view : {},

		/**
		 * Retrieve Headbox instances.
		 *
		 * Headboxes can have multiple instances. Use Headbox.find() to retrieve
		 * a specific instance.
		 *
		 * @since    1.0.0
		 *
		 * @param    {int}       headbox_id Headbox ID.
		 *
		 * @return   {array}     List of Headbox instances.
		 */
		get : function( headbox_id ) {

			return _.where( this.headboxes, { headbox_id : headbox_id } );
		},

		/**
		 * Retrieve a Headbox instance.
		 *
		 * Headboxes can have multiple instances. Use Headbox.get() to retrieve
		 * a list of all instances for a specific Headbox.
		 *
		 * @since    1.0.0
		 *
		 * @param    {string}       selector Headbox unique identifier.
		 *
		 * @return   {Headbox}      Headbox instance.
		 */
		find : function( selector ) {

			return _.find( this.headboxes, { selector : selector } );
		},

		/**
		 * Add a Headbox instance.
		 *
		 * @since    1.0.0
		 *
		 * @param    {string}    headbox Headbox unique identifier.
		 * @param    {object}    options Headbox options.
		 *
		 * @return   {Headbox}      Headbox instance.
		 */
		add : function( headbox, options ) {

			var headbox = new Headbox( headbox, options );

			this.headboxes.push( headbox );

			return headbox;
		},
	};

	/**
	 * 'Extended' Headbox view.
	 *
	 * @since    1.0.0
	 */
	Headboxes.view.ExtendedHeadbox = wp.Backbone.View;

	/**
	 * 'Default' Headbox view.
	 *
	 * @since    1.0.0
	 *
	 * @param    {object}    options Headbox options.
	 */
	Headboxes.view.DefaultHeadbox = wp.Backbone.View.extend({

		events : {
			'click [data-action="expand"]'   : 'expand',
			'click [data-action="collapse"]' : 'collapse',
			'click [data-action="minimize"]' : 'minimize',
			'click [data-action="maximize"]' : 'maximize',
		},

		/**
		 * Initialize the View.
		 *
		 * @since    1.0.0
		 */
		initialize : function( options ) {

			this.prepare();
		},

		/**
		 * Show the Headbox full size.
		 *
		 * @since    1.0.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		expand : function() {

			this.$el.removeClass( 'collapsed' );

			return this;
		},

		/**
		 * Reduce the Headbox height.
		 *
		 * @since    1.0.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		collapse : function() {

			this.$el.addClass( 'collapsed' );

			return this;
		},

		minimize : function() {

			this.$el.addClass( 'theme-default' );
			this.$el.removeClass( 'theme-extended' );

			return this;
		},

		maximize : function() {
			this.$el.addClass( 'theme-extended' );
			this.$el.removeClass( 'theme-default' );

			return this;
		},

		/**
		 * Prepare the Headbox.
		 *
		 * Determine if the Headbox is collapsable, and collapse it.
		 *
		 * @since    1.0.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		prepare : function() {

			var $content = this.$( '.headbox-content' ),
			    collapse = $content.outerHeight() > 260;

			if ( this.$el.hasClass( 'series-headbox theme-default' ) ) {

				if ( collapse ) {
					this.$el.addClass( 'collapse' );
				}

				if ( collapse && ! this.$el.hasClass( 'collapsed' ) ) {
					this.$el.addClass( 'collapsed' );
				}
			}
		},

	});

	/**
	 * 'Vintage' Headbox view.
	 *
	 * @since    1.0.0
	 */
	Headboxes.view.VintageHeadbox = wp.Backbone.View.extend({

		events : {
			'click .headbox-tab a' : 'switchTab',
		},

		/**
		 * Switch headbox panels.
		 *
		 * @since    1.0.0
		 *
		 * @param    {object}    event JS 'click' Event.
		 *
		 * @return   Returns itself to allow chaining.
		 */
		switchTab : function( event ) {

			event.preventDefault();

			var $target = this.$( event.currentTarget );
			     target = $target.data( 'tab' );

			this.$( '.headbox-tab' ).removeClass( 'active' );
			this.$( '.headbox-panel' ).removeClass( 'active' );

			$target.parent( 'li.headbox-tab' ).addClass( 'active' );
			this.$( '[data-panel="' + target + '"]' ).addClass( 'active' );

			return this;
		},
	});

	/**
	 * 'Allocine' Headbox view.
	 *
	 * @since    1.0.0
	 */
	Headboxes.view.AllocineHeadbox = Headboxes.view.Allocine2Headbox = wp.Backbone.View.extend({

		events : {
			'click .headbox-tab a'         : 'switchTab',
			'click [data-action="expand"]' : 'showDetails',
		},

		/**
		 * Switch headbox panels.
		 *
		 * @since    1.0.0
		 *
		 * @param    {object}    event JS 'click' Event.
		 *
		 * @return   Returns itself to allow chaining.
		 */
		switchTab : function( event ) {

			event.preventDefault();

			var $target = this.$( event.currentTarget );
			     target = $target.data( 'tab' );

			this.$( '.headbox-tab' ).removeClass( 'active' );
			this.$( '.headbox-panel' ).removeClass( 'active' );

			$target.parent( 'li.headbox-tab' ).addClass( 'active' );
			this.$( '[data-panel="' + target + '"]' ).addClass( 'active' );

			return this;
		},

		/**
		 * Show details block.
		 *
		 * @since    1.0.0
		 *
		 * @param    {object}    event JS 'click' Event.
		 *
		 * @return   Returns itself to allow chaining.
		 */
		showDetails : function( event ) {

			event.preventDefault();

			this.$( '.headbox-more' ).slideToggle();
			this.$( '.series-meta' ).slideToggle();

			return this;
		},

	});

	/**
	 * 'IMDb v2' Headbox view.
	 *
	 * @since    1.0.0
	 */
	Headboxes.view.IMDb2Headbox = wp.Backbone.View.extend({

		/**
		 * Initialize the View.
		 *
		 * @since    1.0.0
		 */
		initialize : function() {

			_.bindAll( this, 'resize' );

			var event = 'resize.' + this.el.className;
			  $window = wpmoly.$( window );

			$window.off( event ).on( event, _.debounce( this.resize, 100 ) );

			this.resize();
		},

		/**
		 * Resize header backdrop.
		 *
		 * @since    1.0.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		resize : function() {

			var $poster = this.$( '.headbox-poster img' ),
			  $backdrop = this.$( '.headbox-backdrop' );

			$backdrop.css( { height : $poster.innerHeight() } );

			return this;
		},

	});

	/**
	 * Run Forrest, run!
	 *
	 * @since    1.0.0
	 */
	Headboxes.run = function() {

		return _.map(
			document.querySelectorAll( '[data-headbox]' ),
			Headboxes.add,
			Headboxes
		);
	};

})( jQuery, _, Backbone );

wpmoly.runners['headboxes'] = wpmoly.headboxes;
