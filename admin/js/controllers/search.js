
wpmoly = window.wpmoly || {};

_.extend( wpmoly.controller, {

	Search: Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			this.post_id = options.post_id || '';

			var $title = wpmoly.$( '#title' );
			$title.on( 'input', _.bind( this.setTitle, this ) );

			// API block
			this.api       = wpmoly.api;
			this.settings  = new wpmoly.model.Settings( _wpmoly_search_settings || {}, { controller: this } );
			this.search    = new wpmoly.model.Search( { query: $title.val() }, { controller: this } );
			this.status    = new wpmoly.model.Status( null, { controller: this } );
			this.results   = new wpmoly.model.Results( null, { controller  : this } );

			// Bind events
			this.bindEvents();
		},

		/**
		 * Bind controller events.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		bindEvents: function() {

			this.listenTo( this.api, 'movie:search:start',   this.searchProcess );
			this.listenTo( this.api, 'movie:search:success', this.searchResults );
			this.listenTo( this.api, 'movie:fetch:start',    this.fetchProcess );
			this.listenTo( this.api, 'movie:fetch:success',  this.fetchResults );

			wpmoly.on( 'search:reset',  this.searchReset,  this );

			wpmoly.on( 'settings:save', this.saveSettings, this );
			wpmoly.on( 'history:clean', this.cleanHistory, this );
			wpmoly.on( 'api:search',    this.searchMovie,  this );
			wpmoly.on( 'api:fetch',     this.fetchMovie,   this );

			wpmoly.on( 'editor:meta:reload', this.fetchMovie, this );
		},

		/**
		 * Update the search query on post title changes.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		setTitle: function() {

			this.search.set({ query: wpmoly.$( '#title' ).val() });
		},

		/**
		 * Save settings after asking for confirmation.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		saveSettings: function() {

			var confirm = wpmoly.confirm( wpmolyL10n.replaceSettings );

			confirm.open();
			confirm.on( 'confirm', function() {
				this.settings.save();
			}, this );
		},

		/**
		 * Empty status history.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		cleanHistory: function() {

			this.status.reset();
			this.status.add( new wpmoly.model.StatusMessage );
		},

		/**
		 * Search movie.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * 
		 * @return   xhr
		 */
		searchMovie: function( model ) {

			wpmoly.trigger( 'search:reset' );

			if ( this.api.locked ) {
				return;
			}

			var model = model || this.search,
			    query = model.get( 'query' ) || false, regexp, params, result;
			if ( ! query ) {
				return;
			}

			regexp = new RegExp(/^(id:(tt\d{5,7}))|(id:(\d{1,6}))$/i);
			if ( regexp.test( query ) ) {
				return this.api.movie.fetch( query.replace( 'id:', '' ) );
			}

			params = {
				page                 : this.settings.get( 'search_page' )  || 1,
				language             : this.settings.get( 'api_language' ) || '',
				include_adult        : this.settings.get( 'api_adult' )    || '',
				year                 : this.settings.get( 'search_year' )  || '',
				primary_release_year : this.settings.get( 'search_pyear' ) || ''
			};

			return this.api.movie.search( query, params );
		},

		/**
		 * Search is processing, update status.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    data
		 * 
		 * @return   void
		 */
		searchProcess: function( data ) {

			wpmoly.trigger( 'status:start', {
				icon    : 'icon-search',
				message : s.sprintf( wpmolyL10n.searchingMovie, s.aquote( data.query ) )
			} );
		},

		/**
		 * Search succeed, update status and handle results.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    response
		 * 
		 * @return   void
		 */
		searchResults: function( response ) {

			wpmoly.trigger( 'status:stop', {
				icon    : 'icon-search',
				message : wpmoly.l10n._n( wpmolyL10n.nMoviesFound, response.results.length )
			} );

			this.results.page        = response.page;
			this.results.total_pages = response.total_pages;
			this.results.add( response.results );

			wpmoly.trigger( 'search:open' );
		},

		/**
		 * Reset search results.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		searchReset: function() {

			wpmoly.trigger( 'status:stop', {
				icon    : 'icon-api',
				message : wpmolyL10n.ready
			} );

			if ( this.results.length ) {
				this.results.reset();
			}

			wpmoly.trigger( 'search:close' );
		},

		/**
		 * Fetch a movie.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * 
		 * @return   void
		 */
		fetchMovie: function( model ) {

			if ( this.api.locked ) {
				return;
			}

			var model = model || this.search,
			    query = model.query || model.get( 'query' ) || false,
			   params, result;
			if ( ! query ) {
				return;
			}

			params = {
				page                 : this.settings.get( 'search_page' )  || 1,
				language             : this.settings.get( 'api_language' ) || '',
				include_adult        : this.settings.get( 'api_adult' )    || '',
				year                 : this.settings.get( 'search_year' )  || '',
				primary_release_year : this.settings.get( 'search_pyear' ) || ''
			};

			this.api.movie.fetch( query, params );
		},

		/**
		 * Import is processing, update status.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    data
		 * 
		 * @return   void
		 */
		fetchProcess: function( data ) {

			wpmoly.trigger( 'status:start', {
				icon    : 'icon-import',
				effect  : 'bounce',
				message : s.sprintf( wpmolyL10n.importingMovie, s.quote( data.query ) )
			} );
		},

		/**
		 * Import succeed, update status and handle results.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    response
		 * 
		 * @return   void
		 */
		fetchResults: function( response ) {

			wpmoly.trigger( 'search:close' );

			wpmoly.trigger( 'status:stop', {
				icon    : 'icon-import',
				effect  : 'bounce',
				message : s.sprintf( wpmolyL10n.movieImported, s.quote( response.title ) || '' )
			} );

			wpmoly.trigger( 'editor:meta:update', response, { autosave: false } );

			if ( this.settings.get( 'posters_featured' ) ) {
				var posters = response.images.posters || [],
				     poster = posters.slice( 0, 1 );

				wpmoly.once( 'editor:poster:import:start', function( uploader, file ) {
					wpmoly.trigger( 'status:start', {
						icon    : 'icon-poster',
						effect  : 'bounce',
						message : wpmolyL10n.importingPoster
					} );
				}, this );

				wpmoly.once( 'editor:poster:import:done', function( uploader, file, response ) {
					wpmoly.trigger( 'status:stop', {
						icon    : 'icon-poster',
						effect  : 'bounce',
						message : wpmolyL10n.posterImported
					} );

					wpmoly.trigger( 'editor:image:featured', file.attachment );
				}, this );

				wpmoly.trigger( 'editor:poster:import', poster.map( function( image ) {
					return new wpmoly.model.Poster( image );
				} ) );
			}

			/*if ( this.settings.get( 'backdrops_autoimport' ) ) {
				var backdrops = response.images.backdrops || [],
				    backdrops = backdrops.slice( 0, this.settings.get( 'backdrops_limit' ) );

				wpmoly.trigger( 'editor:backdrop:import', backdrops.map( function( image ) {
					return new wpmoly.model.Backdrop( image );
				} ) );
			}*/
		},
	})
} );
