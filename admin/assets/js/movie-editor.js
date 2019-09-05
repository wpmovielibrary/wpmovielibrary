wpmoly = window.wpmoly || {};

wpmoly.editor = wpmoly.editor || {};

(function( $, _, Backbone ) {

	var Dashboard = wpmoly.dashboard;

	/**
	 * Create a new Movie Editor instance.
	 *
	 * Set snapshot, node and post models, editor controller and editor view. Load
	 * post first, then snapshot, then node. Editor view is rendered when post is
	 * fetched, editor regions are set when node is fetched.
	 *
	 * @since 3.0.0
	 *
	 * @param {Element} editor Movie Editor DOM element.
	 *
	 * @return {object} Movie instance.
	 */
	var Editor = function( editor ) {

		var post_id = parseInt( wpmoly.$( '#object_ID' ).val() ),
		    $parent = wpmoly.$( '#wpmoly-editor' );

		// Show loading animation.
		$parent.addClass( 'loading' );

		// Set editor models.
		var post = new wp.api.models.Movies( { id : post_id } ),
		    node = new wpmoly.api.models.Movie( { id : post_id } ),
		settings = new wp.api.models.Settings,
		 persons = new wp.api.collections.Persons;

		settings.fetch();

		// Snapshot and Meta shortcut.
		var meta = new MovieEditor.model.Meta( [], {
			defaults : node.defaults,
			model    : post,
		} ),
		snapshot = node.snapshot = new MovieEditor.model.Snapshot( [], { model : post } );

		// Set editor controllers.
		var search = new MovieEditor.controller.Search,
		   posters = new MovieEditor.controller.PostersEditor( [], { post : post, meta : meta, node : node } ),
		 backdrops = new MovieEditor.controller.BackdropsEditor( [], { post : post, meta : meta, node : node } );

		// Set editor controller.
		var controller = new MovieEditor.controller.Editor( [], {
			settings  : settings,
			search    : search,
			snapshot  : snapshot,
			persons   : persons,
			meta      : meta,
			post      : post,
			node      : node,
			posters   : posters,
			backdrops : backdrops,
		} );

		posters.controller   = controller;
		backdrops.controller = controller;

		// Set editor view.
		var view = new MovieEditor.view.Editor({
			el         : editor,
			controller : controller,
		});

		view.$el.addClass( 'post-editor movie-editor' );

		// Redirect after trash.
		post.on( 'trashed', function() {
			window.location.replace( window.location.search.replace( /(&id=[\d]+)(&action=edit)/i, '' ) );
		} );

		// Set editor regions.
		node.once( 'sync', function( response ) {
			// Hide loading animation.
			$parent.removeClass( 'loading' );
			// Set snapshot.
			snapshot.set( post.get( 'snapshot' ) || {} );
			// Load media.
			controller.backdrops.load();
			controller.posters.load();
			// Render the editor.
			view.render();
		} );

		// Load node and persons.
		post.on( 'sync', function() {

			node.fetch( { data : { context : 'embed' } } );

			var related_persons = post.get( 'persons' );
			if ( ! _.isEmpty( related_persons ) ) {
				persons.fetch( { data : { include : related_persons, context : 'edit' } } );
			}
		} );

		// Switch to search mode if no snapshot is found.
		post.once( 'sync', function() {
			if ( _.isEmpty( post.get( 'snapshot' ) ) ) {
				var tmdb_id = post.getMeta( wpmolyApiSettings.movie_prefix + 'tmdb_id' );
				if ( ! tmdb_id ) {
					controller.set( { mode : 'download' } );
					search.set( { query : ( post.get( 'title' ) || {} ).raw || '' } );
				} else {
					snapshot.set( { id : tmdb_id } );
					wpmoly.warning( wpmolyEditorL10n.missing_snapshot.replace( '%s', 'javascript:document.querySelector(\'[data-mode=snapshot]\').click()' ), { destroy : false } );
				}
			}
		} );

		// Load movie.
		post.fetch( { data : { context : 'edit' } } );

		/**
		 * Editor instance.
		 *
		 * Provide a set of useful functions to interact with the editor
		 * without directly calling controllers and views.
		 *
		 * @since 3.0.0
		 */
		var editor = {

			search : search,

			snapshot : snapshot,

			post : post,

			node : node,

			posters : posters,

			backdrops : backdrops,

			controller : controller,

			view : view,

		};

		// Debug.
		_.map( editor, function( model, name ) {
			wpmoly.observe( model, { name : name } );
		} );

		return editor;
	};

	var PostEditor = wpmoly.editor.post;

	var MovieEditor = wpmoly.editor.movie = _.extend( PostEditor, {

		model : _.extend( PostEditor.model, {

			Meta : Backbone.Model.extend({

				/**
				 * Initialize the Model.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.model    = options.model;
					this.defaults = options.defaults;
				},

				/**
				 * Save Meta.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				save : function( meta, options ) {

					if ( _.isEmpty( meta ) ) {
						var meta = this.toJSON();
					}

					var attributes = {
						meta : {},
					};

					_.each( meta, function( value, key ) {

						var details = [ 'format', 'language', 'media', 'related_persons', 'subtitles' ];
						if ( ! _.contains( details, key ) ) {
							value = _.isArray( value ) ? value.join( ', ' ) : value;
						}

						// Apply defaults, if any.
						if ( _.has( this.defaults, key ) && ( ( _.isNumber( value ) && ! value ) || ( ! _.isNumber( value ) && _.isEmpty( value ) ) ) ) {
							value = this.defaults[ key ];
						}

						attributes.meta[ wpmolyApiSettings.movie_prefix + key ] = value;
					}, this );

					return this.model.save( attributes, _.extend( { patch : true }, options || {} ) );
				},

			}),

			Snapshot : Backbone.Model.extend({

				/**
				 * Initialize the Model.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.model = options.model;
				},

				/**
				 * Save Snapshot.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				save : function( snapshot, options ) {

					if ( _.isUndefined( snapshot ) || _.isUndefined( snapshot.id ) ) {
						var snapshot = this.toJSON();
					}

					if ( _.isUndefined( snapshot.id ) ) {
						return false;
					}

					snapshot._snapshot_date = ( new Date ).toISOString().substr( 0, 19 ) + '+00:00';

					this.set( snapshot );

					var attributes = {
						meta : {},
					};

					attributes.meta[ wpmolyApiSettings.movie_prefix + 'snapshot' ] = JSON.stringify( snapshot );

					return this.model.save( attributes, _.extend( { patch : true }, options || {} ) );
				},

			}),

		} ),

		controller : _.extend( PostEditor.controller, {

			/**
			 * MovieEditor 'Actors' Block Controller.
			 *
			 * @since 3.0.0
			 */
			ActorsBlock : PostEditor.controller.TaxonomyBlock.extend({

				taxonomy : 'actors',

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.meta     = MovieEditor.editor.controller.meta;
					this.post     = MovieEditor.editor.controller.post;
					this.node     = MovieEditor.editor.controller.node;
					this.snapshot = MovieEditor.editor.controller.snapshot;
					this.settings = MovieEditor.editor.controller.settings;

					this.term  = wp.api.models.Actors;
					this.terms = new wp.api.collections.Actors;

					PostEditor.controller.TaxonomyBlock.prototype.initialize.apply( this, arguments );

					this.listenTo( this.post, 'saved',  this.saved );
					this.listenTo( this.meta, 'change', this.importTerms );
				},

				/**
				 * Automatically import terms if needed.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				importTerms : function() {

					if ( true !== this.settings.get( wpmolyApiSettings.option_prefix + 'auto_import_actors' ) ) {
						return false;
					}

					if ( _.isEmpty( this.post.get( 'actors' ) ) ) {
						return this.synchronize();
					}

					return this;
				},

				/**
				 * Handle terms list manual changes.
				 *
				 * @since 3.0.0
				 *
				 * @param {array} names List of term names.
				 *
				 * @return Return itself to allow chaining.
				 */
				updateTerms : function( names ) {

					var actors = [];

					_.each( names || [], function( name ) {
						var terms = this.terms.where( { name : name } );
						if ( terms.length ) {
							actors = _.union( actors, terms );
						} else {
							var term = { name : name },
							 credits = this.snapshot.get( 'credits' ) || {},
							   actor = _.where( credits.cast || [], { name : name } );
							if ( actor.length ) {
								term.tmdb_id = _.first( actor ).tmdb_id || '';
							}
							actors.push( term );
						}
					}, this );

					this.terms.set( actors );

					return this;
				},

				/**
				 * Synchronize taxonomy with meta.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				synchronize : function() {

					var actors = this.node.get( 'cast' ) || [],
					   credits = this.snapshot.get( 'credits' ) || {};

					if ( _.isEmpty( actors ) && _.has( credits, 'cast' ) ) {
						actors = credits.cast;
					}

					if ( _.isString( actors ) ) {
						var trim = s.trim,
						  actors = _.invoke( actors.split( ',' ), 'trim' );
					}

					var models = [];
					_.each( actors, function( actor ) {
						if ( _.has( actor, 'name' ) && ( _.has( actor, 'id' ) || _.has( actor, 'tmdb_id' ) ) ) {
							var model = _.pick( actor, 'name', 'id', 'tmdb_id' );
						} else if ( _.isString( actor ) ) {
							var model = { name : actor };
						}

						var actor = _.find( credits.cast || {}, { name : model.name } );
						if ( actor && ! model.tmdb_id ) {
							model.tmdb_id = actor.tmdb_id;
						}

						models.push( model );
					}, this );

					this.terms.set( models );

					return this;
				},

				/**
				 * Create a new term.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} model Term data.
				 *
				 * @return {jQuery.promise}
				 */
				createTerm : function( term ) {

					var self = this,
					   model = new self.term,
					attributes = {
						name : term.get( 'name' ),
						meta : {},
					};

					var credits = this.snapshot.get( 'credits' ) || {},
					      actor = _.find( credits.cast || {}, { name : term.get( 'name' ) } );

					if ( ! _.isUndefined( actor ) ) {
						var thumbnail = 'neutral';
						if ( '1' == actor.gender ) {
							thumbnail = 'female';
						} else if ( '2' == actor.gender ) {
							thumbnail = 'male';
						}
						attributes.meta[ wpmolyApiSettings.actor_prefix + 'thumbnail' ] = thumbnail;
						attributes.meta[ wpmolyApiSettings.actor_prefix + 'tmdb_id' ]   = actor.tmdb_id;
						attributes.meta[ wpmolyApiSettings.actor_prefix + 'gender' ]    = actor.gender;
					}

					var options = {
						error : function( model, response, options ) {
							// If term already exists, update metadata.
							if ( _.has( response.responseJSON, 'code' ) && 'term_exists' === response.responseJSON.code ) {
								var m = new self.term( { id : response.responseJSON.data.term_id } );
								// Safety: don't overwrite name though.
								m.save( { meta : model.getMetas() || {} } , { patch : true } );
							}
						},
					};

					return model.save( attributes, _.extend( options || {}, { patch : true } ) );
				},

			}),

			/**
			 * MovieEditor 'Certifications' Block Controller.
			 *
			 * @since 3.0.0
			 */
			CertificationsBlock : Backbone.Model.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.meta     = MovieEditor.editor.controller.meta;
					this.post     = MovieEditor.editor.controller.post;
					this.snapshot = MovieEditor.editor.controller.snapshot;
				},

			}),


			/**
			 * MovieEditor 'Collections' Block Controller.
			 *
			 * @since 3.0.0
			 */
			CollectionsBlock : PostEditor.controller.TaxonomyBlock.extend({

				taxonomy : 'collections',

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.meta     = MovieEditor.editor.controller.meta;
					this.post     = MovieEditor.editor.controller.post;
					this.node     = MovieEditor.editor.controller.node;
					this.snapshot = MovieEditor.editor.controller.snapshot;
					this.settings = MovieEditor.editor.controller.settings;

					this.term  = wp.api.models.Collections;
					this.terms = new wp.api.collections.Collections;

					PostEditor.controller.TaxonomyBlock.prototype.initialize.apply( this, arguments );

					this.listenTo( this.post, 'saved',  this.save );
					this.listenTo( this.meta, 'change', this.importTerms );
				},

				/**
				 * Automatically import terms if needed.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				importTerms : function() {

					if ( true !== this.settings.get( wpmolyApiSettings.option_prefix + 'auto_import_collections' ) ) {
						return false;
					}

					if ( _.isEmpty( this.post.get( 'collections' ) ) ) {
						return this.synchronize();
					}

					return this;
				},

				/**
				 * Synchronize taxonomy with meta.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				synchronize : function() {

					var director = this.node.get( 'director' ) || [],
					     credits = this.snapshot.get( 'credits' ) || {};

					if ( _.isEmpty( director ) && _.has( credits, 'crew' ) ) {
						director = _.where( credits.crew, { job : 'Director' } );
					}

					if ( _.isString( director ) ) {
						var trim = s.trim,
						director = _.invoke( director.split( ',' ), 'trim' );
					}

					var models = [];
					_.each( director, function( d ) {
						if ( _.has( d, 'name' ) && ( _.has( d, 'id' ) || _.has( d, 'tmdb_id' ) ) ) {
							var model = _.pick( d, 'name', 'id', 'tmdb_id' );
						} else if ( _.isString( d ) ) {
							var model = { name : d };
						}

						var d = _.find( credits.crew || {}, { name : model.name } );
						if ( d && ! model.tmdb_id ) {
							model.tmdb_id = d.tmdb_id;
						}

						models.push( model );
					}, this );

					this.terms.set( models );

					return this;
				},

				/**
				 * Create a new term.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} model Term data.
				 *
				 * @return {jQuery.promise}
				 */
				createTerm : function( term ) {

					var self = this,
					   model = new self.term,
					attributes = {
						name : term.get( 'name' ),
						meta : {},
					};

					var credits = this.snapshot.get( 'credits' ) || {},
					   director = _.find( credits.crew || {}, { job : 'Director', name : term.get( 'name' ) } );

					if ( ! _.isUndefined( director ) ) {
						attributes.meta[ wpmolyApiSettings.genre_prefix + 'tmdb_id' ] = director.tmdb_id;
						attributes.meta[ wpmolyApiSettings.genre_prefix + 'gender' ]  = director.gender;
					}

					var options = {
						error : function( model, response, options ) {
							// If term already exists, update metadata.
							if ( _.has( response.responseJSON, 'code' ) && 'term_exists' === response.responseJSON.code ) {
								var m = new self.term( { id : response.responseJSON.data.term_id } );
								// Safety: don't overwrite name though.
								m.save( { meta : model.getMetas() || {} } , { patch : true } );
							}
						},
					};

					return model.save( attributes, _.extend( options || {}, { patch : true } ) );
				},

			}),

			/**
			 * MovieEditor 'Companies' Block Controller.
			 *
			 * @since 3.0.0
			 */
			CompaniesBlock : Backbone.Model.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.meta     = MovieEditor.editor.controller.meta;
					this.snapshot = MovieEditor.editor.controller.snapshot;
				},

			}),

			/**
			 * MovieEditor 'Countries' Block Controller.
			 *
			 * @since 3.0.0
			 */
			CountriesBlock : Backbone.Model.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.meta     = MovieEditor.editor.controller.meta;
					this.snapshot = MovieEditor.editor.controller.snapshot;
				},

			}),

			/**
			 * MovieEditor 'Details' Block Controller.
			 *
			 * @since 3.0.0
			 */
			DetailsBlock : Backbone.Model.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.editor = MovieEditor.editor.controller;
				},

			}),

			/**
			 * MovieEditor 'Genres' Block Controller.
			 *
			 * @since 3.0.0
			 */
			GenresBlock : PostEditor.controller.TaxonomyBlock.extend({

				taxonomy : 'genres',

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.meta     = MovieEditor.editor.controller.meta;
					this.post     = MovieEditor.editor.controller.post;
					this.node     = MovieEditor.editor.controller.node;
					this.snapshot = MovieEditor.editor.controller.snapshot;
					this.settings = MovieEditor.editor.controller.settings;

					this.term  = wp.api.models.Genres;
					this.terms = new wp.api.collections.Genres;

					PostEditor.controller.TaxonomyBlock.prototype.initialize.apply( this, arguments );

					this.listenTo( this.post, 'saved', this.save );
					this.listenTo( this.meta, 'change', this.importTerms );
				},

				/**
				 * Automatically import terms if needed.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				importTerms : function() {

					if ( true !== this.settings.get( wpmolyApiSettings.option_prefix + 'auto_import_genres' ) ) {
						return false;
					}

					if ( _.isEmpty( this.post.get( 'genres' ) ) ) {
						return this.synchronize();
					}

					return this;
				},

				/**
				 * Synchronize taxonomy with meta.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				synchronize : function() {

					var genres = this.node.get( 'genres' ) || [];
					if ( _.isEmpty( genres ) ) {
						genres = this.snapshot.get( 'genres' ) || [];
					}

					if ( _.isString( genres ) ) {
						var trim = s.trim,
						  genres = _.invoke( genres.split( ',' ), 'trim' );
					}

					var models = [];
					_.each( genres, function( genre ) {
						if ( _.has( genre, 'name' ) || _.has( genre, 'id' ) ) {
							var model = _.pick( genre, 'name', 'id' );
						} else if ( _.isString( genre ) ) {
							var model = { name : genre };
						}

						var genre = _.find( this.snapshot.get( 'genres' ) || [], { name : model.name } );
						if ( genre && ! model.tmdb_id ) {
							delete model.id;
							model.tmdb_id = genre.id;
						}

						models.push( model );
					}, this );

					this.terms.set( models );

					return this;
				},

				/**
				 * Create a new term.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} model Term data.
				 *
				 * @return {jQuery.promise}
				 */
				createTerm : function( term ) {

					var self = this,
					   model = new self.term,
					attributes = {
						name : term.get( 'name' ),
						meta : {},
					};

					var genres = this.snapshot.get( 'genres' ) || {},
					     genre = _.find( genres, { name : term.get( 'name' ) } );

					if ( ! _.isUndefined( genre ) ) {
						attributes.meta[ wpmolyApiSettings.genre_prefix + 'tmdb_id' ] = genre.id;
					}

					var options = {
						error : function( model, response, options ) {
							// If term already exists, update metadata.
							if ( _.has( response.responseJSON, 'code' ) && 'term_exists' === response.responseJSON.code ) {
								var m = new self.term( { id : response.responseJSON.data.term_id } );
								// Safety: don't overwrite name though.
								m.save( { meta : model.getMetas() || {} } , { patch : true } );
							}
						},
					};

					return model.save( attributes, _.extend( options || {}, { patch : true } ) );
				},

			}),

			/**
			 * MovieEditor 'Languages' Block Controller.
			 *
			 * @since 3.0.0
			 */
			LanguagesBlock : Backbone.Model.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.meta     = MovieEditor.editor.controller.meta
					this.snapshot = MovieEditor.editor.controller.snapshot;
				},

			}),

			/**
			 * MovieEditor 'Submit' Block Controller.
			 *
			 * @since 3.0.0
			 */
			MenuBlock : PostEditor.controller.SubmitBlock.extend({

				/**
				 * Change editor mode.
				 *
				 * @since 3.0.0
				 *
				 * @param {string} mode Editor mode.
				 *
				 * @return xhr
				 */
				setMode : function( mode ) {

					if ( 'editor' === mode ) {
						window.location.href = MovieEditor.editor.controller.post.get( 'old_edit_link' );
					} else if ( 'view' === mode ) {
						window.location.href = MovieEditor.editor.controller.post.get( 'link' );
					} else {
						MovieEditor.editor.controller.set( { mode : mode } );
					}

					return this;
				},

				/**
				 * Get editor mode.
				 *
				 * @since 3.0.0
				 *
				 * @return xhr
				 */
				getMode : function() {

					return MovieEditor.editor.controller.get( 'mode' );
				},

				/**
				 * Update the node.
				 *
				 * @since 3.0.0
				 *
				 * @return xhr
				 */
				save : function() {

					return MovieEditor.editor.controller.save();
				},
			}),

			/**
			 * Search controller.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} attributes
			 * @param {object} options
			 */
			Search : Backbone.Model.extend({

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					this.result = new Backbone.Model;

					this.settings = new Backbone.Model( {
						year         : '',
						primary_year : '',
						language     : TMDb.settings.language,
						adult        : '',
					} );
				},

				/**
				 * Mirror results collection events.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				bindEvents : function() {

					var self = this;

					this.listenTo( this.results, 'request', function( collection, xhr, options ) {
						self.trigger( 'search:start', collection, xhr, options );
					} );

					this.listenTo( this.results, 'reset', function( collection, options ) {
						self.trigger( 'search:reset', collection, options );
					} );

					this.listenTo( this.results, 'remove', function( model, collection, options ) {
						self.trigger( 'search:remove', model, collection, options );
					} );

					this.listenTo( this.results, 'add', function( model, collection, options ) {
						self.trigger( 'search:add', model, collection, options );
					} );

					this.listenTo( this.results, 'update', function( collection, options ) {
						self.trigger( 'search:update', collection, options );
					} );

					this.listenTo( this.results, 'sync', function( collection, response, options ) {
						self.trigger( 'search:stop', collection, response, options );
					} );

					this.listenTo( this.results, 'error', function( collection, response, options ) {
						self.trigger( 'search:failed', collection, response, options );
					} );

					return this;
				},

				/**
				 * Start search process.
				 *
				 * @since 3.0.0
				 *
				 * @param {int} movie_id Movie ID.
				 *
				 * @return boolean|object Returns false if invalid ID, itself otherwise.
				 */
				import : function( movie_id ) {

					var self = this,
					  result = this.results.get( movie_id ),
					   movie = new TMDb.Movie( { id : movie_id } );

					this.result.set( result.toJSON() );

					movie.on( 'fetch:start', function( xhr, options ) {
						self.trigger( 'import:start', xhr, options );
					}, this );

					movie.on( 'fetch:complete', function( xhr, status ) {
						self.trigger( 'import:stop', xhr, status );
					}, this );

					movie.on( 'fetch:success', function( response, status, xhr ) {
						self.trigger( 'import:done', movie.toJSON(), status, xhr );
					}, this );

					movie.on( 'fetch:images:success', function( model, status, xhr ) {
						self.trigger( 'import:images:done', model, status, xhr );
					}, this );

					movie.on( 'fetch:error', function( xhr, status, response ) {
						self.trigger( 'import:failed', xhr, status, response );
					}, this );

					movie.fetchAll( { data : this._prepareQueryData() } );

					return this;
				},

				/**
				 * Start search process.
				 *
				 * @since 3.0.0
				 *
				 * @param {string} query Search query.
				 *
				 * @return Returns itself to allow chaining.
				 */
				search : function( query ) {

					if ( _.isEmpty( query) ) {
						return this;
					}

					this.set( { query : query }, { silent : true } );

					if ( /^(tt)?(\d+)$/i.test( query ) || /^(id|imdb|tmdb):(.*)$/i.test( query ) ) {
						this.set( 'query', query.replace( /^(id|imdb|tmdb):/, '' ) );
						return this._searchById();
					} else if ( /^(title):(.*)$/i.test( query ) ) {
						this.set( 'query', query.replace( 'title:', '' ) );
						return this._searchByTitle();
					} else if ( /^(actor|director):(.*)$/i.test( query ) ) {
						return this._searchByPerson();
					} else {
						return this._searchByTitle();
					}

					return this;
				},

				/**
				 * Search movies based on title.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				_searchByTitle : function() {

					var self = this;
					if ( _.isUndefined( self.results ) ) {
						self.results = new TMDb.Movies;
						self.bindEvents();
					}

					var collection = self.results;

					collection.fetch({
						data : self._prepareQueryData(),
						beforeSend : function( xhr, options ) {
							collection.reset();
							self.trigger( 'search:start', xhr, options );
						},
						complete : function( xhr, status ) {
							self.trigger( 'search:stop', xhr, status );
						},
						success : function( response, status, xhr ) {
							self.trigger( 'search:done', response, status, xhr );
						},
						error : function( xhr, status, response ) {
							self.trigger( 'search:failed', xhr, status, response );
						},
					});

					return this;
				},

				/**
				 * Search movies based on TMDb or IMDb ID.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				_searchById : function() {

					return this.import( this.get( 'query' ) );
				},

				/**
				 * Search movies based on person.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				_searchByPerson : function() {

					var query = /(actor|director):(.*)/i.exec( this.get( 'query' ) ),
					      job = query[1],
					   person = query[2];

					return this;
				},

				/**
				 * Prepare query data parameters to include settings.
				 *
				 * @since 3.0.0
				 *
				 * @return array
				 */
				_prepareQueryData : function() {

					var data = {},
					settings = this.settings.toJSON();

					if ( ! _.isEmpty( this.get( 'query' ) ) ) {
						data.query = this.get( 'query' );
					}

					if ( ! _.isEmpty( settings.language ) ) {
						data.language = settings.language;
					}

					if ( ! _.isEmpty( settings.year ) ) {
						data.year = settings.year;
					}

					if ( ! _.isEmpty( settings.primary_year ) ) {
						data.primary_release_year = settings.primary_year;
					}

					if ( ! _.isEmpty( settings.adult ) ) {
						data.include_adult = settings.adult;
					}

					return data;
				},

				/**
				 * Jump to a specific results page.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				browse : function( page ) {

					this.results.more( { data : { page : parseInt( page ) } } );

					return this;
				},

				/**
				 * First results page.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				first : function() {

					this.results.more( { data : { page : 1 } } );

					return this;
				},

				/**
				 * Last results page.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				last : function() {

					this.results.more( { data : { page : this.results.state.totalPages } } );

					return this;
				},

				/**
				 * Next results page.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				next : function() {

					this.results.more( { data : { page : this.results.state.currentPage + 1 } } );

					return this;
				},

				/**
				 * Previous results page.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				previous : function() {

					this.results.more( { data : { page : this.results.state.currentPage - 1 } } );

					return this;
				},

				/**
				 * Reset search form and results.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				reset : function() {

					this.set( { query : '' } );

					if ( this.results ) {
						this.results.reset();
					}

					return this;
				},

			}),

			/**
			 * Editor controller.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} attributes
			 * @param {object} options
			 */
			Editor : Backbone.Model.extend({

				defaults : {
					mode : 'preview',
				},

				/**
				 * Initialize the Controller.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes Controller attributes.
				 * @param {object} options    Controller options.
				 */
				initialize : function( attributes, options ) {

					var options = options || {};

					this.settings  = options.settings;
					this.search    = options.search;
					this.snapshot  = options.snapshot;
					this.persons   = options.persons;
					this.meta      = options.meta;
					this.post      = options.post;
					this.node      = options.node;
					this.posters   = options.posters;
					this.backdrops = options.backdrops;

					this.listenTo( this.post,     'error', this.error );
					this.listenTo( this.node,     'error', this.error );
					this.listenTo( this.persons,  'error', this.error );
					this.listenTo( this.snapshot, 'error', this.error );
					this.listenTo( this.search,   'error', this.error );
					this.listenTo( this.search,   'import:failed', this.error );

					this.listenTo( this.search, 'import:start', function() {
						this.search.reset();
					} );

					this.listenTo( this.search, 'import:done',        this.saveSnapshot );
					this.listenTo( this.search, 'import:images:done', this.importImages );

					this.listenTo( this.snapshot, 'change',      this.updateMeta );
					this.listenTo( this.post,     'change:meta', this.updateMeta );
				},

				/**
				 * Save snapshot.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} attributes
				 *
				 * @return Returns itself to allow chaining.
				 */
				saveSnapshot : function( attributes ) {

					this.snapshot.save( attributes || [] );

					this.set( { mode : 'preview' } );

					return this;
				},

				/**
				 * Update snapshot by querying fresh data from the API.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				updateSnapshot : function() {

					var movie = new TMDb.Movie( { id : this.snapshot.get( 'id' ) } ),
					 snapshot = this.snapshot;

					movie.on( 'fetch:success', function() {
						snapshot.save( movie.toJSON() );
						wpmoly.success( wpmolyEditorL10n.snapshot_updated );
					}, this );

					movie.on( 'fetch:images:success', function() {
						snapshot.save( movie.toJSON() );
					}, this );

					movie.on( 'fetch:error', function( xhr, status, response ) {
						wpmoly.error( xhr, { destroy : false } );
					}, this );

					movie.fetchAll();

					return this;
				},

				/**
				 * Update meta.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				updateMeta : function() {

					var meta = {},
					snapshot = this.snapshot;

					// Avoid confusion with 'status' detail.
					_.each( _.omit( this.post.getMetas(), 'status' ) || [], function( value, key ) {

						var key = key.replace( wpmolyApiSettings.movie_prefix, '' );

						value = value || snapshot.get( key ) || null;

						if ( _.isArray( value ) ) {
							var names = _.filter( _.pluck( value, 'name' ) );
							if ( ! _.isEmpty( names ) ) {
								value = names;
							}
						}

						meta[ key ] = value || '';
					}, this );

					// Update TMDb ID.
					if ( snapshot.has( 'id' ) ) {
						meta.tmdb_id = snapshot.get( 'id' );
					}

					// Update credits.
					if ( _.has( snapshot.get( 'credits' ) || {}, 'crew' ) ) {
						meta.author      = ! _.isEmpty( meta.author ) ?      meta.author :      _.pluck( _.where( snapshot.get( 'credits' ).crew, { job : 'Author' } ), 'name' );
						meta.composer    = ! _.isEmpty( meta.composer ) ?    meta.composer :    _.pluck( _.where( snapshot.get( 'credits' ).crew, { job : 'Original Music Composer' } ), 'name' );
						meta.director    = ! _.isEmpty( meta.director ) ?    meta.director :    _.pluck( _.where( snapshot.get( 'credits' ).crew, { job : 'Director' } ), 'name' );
						meta.photography = ! _.isEmpty( meta.photography ) ? meta.photography : _.pluck( _.where( snapshot.get( 'credits' ).crew, { job : 'Director of Photography' } ), 'name' );
						meta.producer    = ! _.isEmpty( meta.producer ) ?    meta.producer :    _.pluck( _.where( snapshot.get( 'credits' ).crew, { job : 'Producer' } ), 'name' );
						meta.writer      = ! _.isEmpty( meta.writer ) ?      meta.writer :      _.pluck( _.where( snapshot.get( 'credits' ).crew, { job : 'Novel' } ), 'name' );
					}

					// Update cast.
					if ( _.has( snapshot.get( 'credits' ) || {}, 'cast' ) ) {
						meta.cast = ! _.isEmpty( meta.cast ) ? meta.cast : _.pluck( snapshot.get( 'credits' ).cast, 'name' );
					}

					/**
					 * Loop through release types. Check for theatrical, limited theatrical,
					 * digital, physical, TV and premiere release. Use the corresponding
					 * release date, but use the first available certification value.
					 */
					var language = this.settings.get( wpmolyApiSettings.option_prefix + 'api_country' ),
					    releases = _.find( snapshot.get( 'release_dates' ), { iso_3166_1 : language } );
					if ( ! _.isUndefined( releases ) ) {
						var release_date = '',
						   certification = '';
						_.every( [ 3, 2, 4 ,5, 6, 1 ], function( type ) {
							var release = _.find( releases.release_dates, { type : type } );
							if ( _.isUndefined( release ) ) {
								// Continue.
								return true;
							}
							// Use first available certification.
							if ( _.isEmpty( certification ) && ! _.isEmpty( release.certification ) ) {
								meta.certification = release.certification;
							}
							// Set local release date.
							if ( ! _.isEmpty( release.release_date ) ) {
								meta.local_release_date = new Date( release.release_date ).toISOString().slice( 0, 10 );
								// Break.
								return false;
							}
						} );
					}

					this.meta.set( meta );

					return this;
				},

				/**
				 * Import Movie Backdrops and Posters.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} model
				 *
				 * @return Returns itself to allow chaining.
				 */
				importImages : function( model ) {

					this.snapshot.save( this.snapshot.toJSON() || {} );

					if ( true === this.settings.get( wpmolyApiSettings.option_prefix + 'auto_import_movie_backdrops' ) ) {
						this.backdrops.download( model.backdrops.first() || {} );
					}

					if ( true === this.settings.get( wpmolyApiSettings.option_prefix + 'auto_import_movie_posters' ) ) {
						this.posters.download( model.posters.first() || {} );
					}

					return this;
				},

				/**
				 * Import Person.
				 *
				 * @since 3.0.0
				 *
				 * @param {int} person_id
				 *
				 * @return Returns itself to allow chaining.
				 */
				importPerson : function( person_id ) {

					var self = this,
					   model = new wp.api.models.Persons,
					  person = new TMDb.Person( { id : person_id } );

					person.on( 'fetch:start', function( xhr, options ) {
						self.trigger( 'person:import:start', xhr, options );
					}, this );

					person.on( 'fetch:complete', function( xhr, status ) {
						self.trigger( 'person:import:stop', xhr, status );
					}, this );

					person.on( 'fetch:success', function( response, status, xhr ) {
						self.trigger( 'person:import:done', person.toJSON(), status, xhr );
					}, this );

					person.on( 'fetch:images:success', function( model, status, xhr ) {
						self.trigger( 'person:import:images:done', model, status, xhr );
					}, this );

					person.on( 'fetch:error', function( xhr, status, response ) {
						self.trigger( 'person:import:failed', xhr, status, response );
					}, this );

					var data = {};

					person.fetchAll( { data : data } );

					return this;
				},

				/**
				 * Set post featured image.
				 *
				 * @since 3.0.0
				 *
				 * @param {int} id
				 *
				 * @return Returns itself to allow chaining.
				 */
				setFeaturedPoster : function( id ) {

					this.post.save( { featured_media : id }, { patch : true } );

					return this;
				},

				/**
				 * Save movie node and post data.
				 *
				 * @since 3.0.0
				 *
				 * @return xhr
				 */
				save : function() {

					var atts = {},
					    meta = this.meta,
					    post = this.post;

					if ( 'publish' !== post.get( 'status' ) ) {
						post.save( { status : 'publish' }, { patch : true } );
					}

					var options = {
						patch : true,
						wait  : true,
						beforeSend : function( xhr, options ) {
							post.trigger( 'saving', xhr, options );
							wpmoly.info( wpmolyEditorL10n.saving_changes );
						},
						success : function( model, response, options ) {
							post.trigger( 'saved', model, response, options );
							wpmoly.success( wpmolyEditorL10n.post_udpated );
						},
						error : function( model, response, options ) {
							post.trigger( 'notsaved', model, response, options );
						},
					};

					return meta.save( [], options );
				},

				/**
				 * Notify collection errors.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} collection Post collection.
				 * @param {object} xhr        XHR response.
				 * @param {object} options    Options.
				 *
				 * @return Returns itself to allow chaining.
				 */
				error : function( collection, xhr, options ) {

					wpmoly.error( xhr, { destroy : false } );

					return this;
				},

			}),

		} ),

		view : _.extend( PostEditor.view, {

			/**
			 * MovieEditor 'Actors' Block View.
			 *
			 * @since 3.0.0
			 */
			ActorsBlock : PostEditor.view.TaxonomyBlock.extend({

				events : function() {
					return _.extend( {}, _.result( PostEditor.view.TaxonomyBlock.prototype, 'events' ), {
						'click [data-action="synchronize"]' : 'synchronize',
						'change [data-field]' : 'change',
					} );
				},

				template : wp.template( 'wpmoly-movie-editor-actors' ),

				/**
				 * Synchronize taxonomy with meta.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				synchronize : function() {

					this.controller.synchronize();

					return this;
				},

				/**
				 * Update terms.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				change : function() {

					var names = this.$( '[data-field]' ).val();

					this.controller.updateTerms( names );

					return this;
				},

			}),

			/**
			 * MovieEditor 'Certifications' Block View.
			 *
			 * @since 3.0.0
			 */
			CertificationsBlock : PostEditor.view.TaxonomyBlock.extend({

				template : wp.template( 'wpmoly-movie-editor-certifications' ),

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;

					this.on( 'rendered', this.selectize, this );
					this.once( 'rendered', function() {
						this.$el.addClass( 'mode-preview' );
					}, this );

					this.listenTo( this.controller.meta,     'change', this.render );
					this.listenTo( this.controller.snapshot, 'change', this.render );

					this.render();
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var meta = this.controller.meta.toJSON() || {},
					snapshot = this.controller.snapshot.toJSON() || {},
					 options = {
						release_date       : meta.release_date  || '',
						certification      : meta.certification || '',
						local_release_date : meta.local_release_date || '',
						release_dates      : snapshot.release_dates || [],
					};

					return options;
				},

				/**
				 * Selectize select elements.
				 *
				 * Generate a custom country selectize dropdown.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				selectize : function() {

					var $options = this.$( '[data-release-date]' );
					if ( ! $options.length ) {
						return this;
					}

					var self = this,
					   types = [
						'',
						wpmolyEditorL10n.premiere_release,
						wpmolyEditorL10n.theatrical_limited_release,
						wpmolyEditorL10n.theatrical_release,
						wpmolyEditorL10n.digital_release,
						wpmolyEditorL10n.physical_release,
						wpmolyEditorL10n.tv_release,
					],
					options = {
						onChange : function( value ) {
							var $item = this.getItem( value ),
							     meta = {
								certification      : $item.attr( 'data-certification' ),
								local_release_date : $item.attr( 'data-release-date' ),
							};
							self.controller.meta.set( meta );
						},
						closeAfterSelect : true,
						options : [],
						render  : {},
					};

					_.each( $options, function( option ) {
						var $option = this.$( option );
						options.options.push({
							date : $option.attr( 'data-release-date' ),
							type : $option.attr( 'data-release-type' ),
							country : $option.attr( 'data-country' ),
							certification : $option.attr( 'data-certification' ),
						});
					}, this );


					options.render.option = options.render.item = function( item, escape ) {

						var date = ( new Date( item.date ) ).toISOString().substring( 0, 10 ),
						   $item = '';

						if ( ! _.isEmpty( item.certification ) ) {
							$item += '<span class="certification">' + escape( item.certification ) + '</span>';
						}

						if ( ! _.isEmpty( item.country ) ) {
							$item += '<span class="flag flag-' + escape( item.country ) + '" title="' + escape( wpmolyEditorL10n.standard_countries[ item.country ] || '' ) + '"></span>';
						}

						if ( ! _.isEmpty( item.date ) ) {
							$item += '<span class="release-date">' + escape( ( new Date( item.date ) ).toLocaleDateString() ) + '</span>';
						}

						if ( ! _.isEmpty( types[ item.type ] ) ) {
							$item += '<span class="release-type">' + escape( types[ item.type ] ) + '</span>';
						}

						return '<div data-certification="' + escape( item.certification ) + '" data-release-date="' + escape( date ) + '">' + $item + '</div>';
					};

					this.$( '[data-selectize="1"]' ).selectize( _.extend( options, {
						plugins : [ 'remove_button' ],
					} ) );

					return this;
				},

			}),

			/**
			 * MovieEditor 'Collections' Block View.
			 *
			 * @since 3.0.0
			 */
			CollectionsBlock : PostEditor.view.TaxonomyBlock.extend({

				events : function() {
					return _.extend( {}, _.result( PostEditor.view.TaxonomyBlock.prototype, 'events' ), {
						'click [data-action="synchronize"]' : 'synchronize',
						'change [data-field]' : 'change',
					} );
				},

				template : wp.template( 'wpmoly-movie-editor-collections' ),

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					PostEditor.view.TaxonomyBlock.prototype.initialize.apply( this, arguments );

					this.listenTo( this.controller.terms, 'update', this.render );
				},

				/**
				 * Synchronize taxonomy with meta.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				synchronize : function() {

					this.controller.synchronize();

					return this;
				},

				/**
				 * Update terms.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				change : function() {

					var terms = this.$( '[data-field]' ).val();
					_.each( this.controller.terms.models, function( term ) {
						if ( ! _.contains( terms, term.get( 'name' ) ) ) {
							this.controller.terms.remove( term, { silent : true } );
						}
					}, this );

					return this;
				},

			}),

			/**
			 * MovieEditor 'Companies' Block View.
			 *
			 * @since 3.0.0
			 */
			CompaniesBlock : PostEditor.view.TaxonomyBlock.extend({

				events : function() {
					return _.extend( {}, _.result( PostEditor.view.TaxonomyBlock.prototype, 'events' ), {
						'change [data-field]' : 'change',
					} );
				},

				template : wp.template( 'wpmoly-movie-editor-companies' ),

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;

					this.on( 'rendered', this.selectize, this );
					this.once( 'rendered', function() {
						this.$el.addClass( 'mode-preview' );
					}, this );

					this.listenTo( this.controller.meta,     'change', this.render );
					this.listenTo( this.controller.snapshot, 'change', this.render );

					this.render();
				},

				/**
				 * Update terms.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				change : function() {

					var value = this.$( '[data-field]' ).val() || [];

					this.controller.meta.set( 'production_companies', value.join( ', ' ) );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var meta = this.controller.meta.toJSON() || {},
					snapshot = this.controller.snapshot.toJSON() || {},
					options = {},
						terms = [];

					var companies = meta.production_companies || snapshot.production_companies || '';
					if ( _.isString( companies ) ) {
						companies = _.filter( companies.split( ', ' ) );
					}

					_.each( companies, function( company ) {
						if ( _.isString( company ) && _.has( snapshot, 'production_companies' ) ) {
							company = _.find( snapshot.production_companies, { name : s.trim( company ) } );
						}
						terms.push( company );
					}, this );

					options.terms = _.filter( terms );

					return options;
				},

			}),

			/**
			 * MovieEditor 'Countries' Block View.
			 *
			 * @since 3.0.0
			 */
			CountriesBlock : PostEditor.view.TaxonomyBlock.extend({

				events : function() {
					return _.extend( {}, _.result( PostEditor.view.TaxonomyBlock.prototype, 'events' ), {
						'change [data-field]' : 'change',
					} );
				},

				template : wp.template( 'wpmoly-movie-editor-countries' ),

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;

					this.on( 'rendered', this.selectize, this );
					this.once( 'rendered', function() {
						this.$el.addClass( 'mode-preview' );
					}, this );

					this.listenTo( this.controller.meta,     'change', this.render );
					this.listenTo( this.controller.snapshot, 'change', this.render );

					this.render();
				},

				/**
				 * Update terms.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				change : function() {

					var value = this.$( '[data-field]' ).val() || [],
					countries = [];

					_.each( value, function( country ) {
						if ( ! _.isUndefined( wpmolyEditorL10n.standard_countries[ country ] ) ) {
							countries.push( wpmolyEditorL10n.standard_countries[ country ] );
						} else {
							var country = s.capitalize( country ),
							code = _.invert( wpmolyEditorL10n.standard_countries )[ country ];
							if ( ! _.isUndefined( code ) ) {
								countries.push( wpmolyEditorL10n.standard_countries[ code ] );
							}
						}
					}, this );

					this.controller.meta.set( 'production_countries', countries.join( ', ' ) );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var meta = this.controller.meta.toJSON() || {},
					snapshot = this.controller.snapshot.toJSON() || {},
					 options = {
						terms : [],
					};

					var countries = meta.production_countries || snapshot.production_countries || '';
					if ( _.isString( countries ) ) {
						countries = _.filter( countries.split( ', ' ) );
					}

					_.each( countries, function( country ) {
						if ( _.isString( country ) && _.has( snapshot, 'production_countries' ) ) {
							country = _.find( snapshot.production_countries, { name : s.trim( country ) } );
						}
						options.terms.push( country );
					}, this );

					return options;
				},

			}),

			/**
			 * MovieEditor 'Details' Block View.
			 *
			 * @since 3.0.0
			 */
			DetailsBlock : Dashboard.view.Block.extend({

				events : function() {
					return _.extend( {}, _.result( Dashboard.view.Block.prototype, 'events' ), {
						'change [data-field]'                  : 'change',
						'click [data-action="update-details"]' : 'update',
					} );
				},

				template : wp.template( 'wpmoly-movie-editor-details' ),

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;
					this.editor = this.controller.editor;

					this.on( 'rendered', this.selectize, this );
					this.once( 'rendered', function() {
						this.$el.addClass( 'mode-preview' );
					}, this );

					this.listenTo( this.editor.meta, 'change', this.render );

					this.render();
				},

				/**
				 * Enable/disable submit button based on title input length.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				change : function() {

					var fields = this.$( '[data-field]' ),
					  disabled = true,
					    values = {};

					_.each( fields, function( field ) {
						var $field = this.$( field ),
						     field = $field.attr( 'data-field' ),
						     value = $field.val();

						values[ field ] = value;

						if ( disabled && ! _.isEqual( value, this.editor.post.getMeta( wpmolyApiSettings.movie_prefix + field ) ) ) {
							disabled = false;
						}
					}, this );

					this.$( '#update-details' ).prop( 'disabled', disabled );

					if ( ! disabled ) {
						this.editor.meta.set( values, { silent : true } );
					}

					return this;
				},

				/**
				 * Update title, if title input length reach the minimum.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				update : function() {

					var data = {},
					  fields = this.$( '[data-field]' );
					_.each( fields, function( field ) {
						var $field = this.$( field ),
						     field = $field.attr( 'data-field' ),
						     value = $field.val();
						if ( ! _.isNull( value ) ) {
							data[ field ] = value;
						}
					}, this );

					if ( ! _.isEmpty( data ) ) {
						this.editor.meta.save( data, { patch : true, silent : true } );
					}

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var meta = this.editor.meta.toJSON() || {},
					 options = {
						format    : meta.format,
						language  : meta.language,
						media     : meta.media,
						rating    : meta.rating,
						status    : meta.status,
						subtitles : meta.subtitles,
					};

					return options;
				},

			}),

			/**
			 * MovieEditor 'Genres' Block View.
			 *
			 * @since 3.0.0
			 */
			GenresBlock : PostEditor.view.TaxonomyBlock.extend({

				events : function() {
					return _.extend( {}, _.result( PostEditor.view.TaxonomyBlock.prototype, 'events' ), {
						'click [data-action="synchronize"]' : 'synchronize',
						'change [data-field]' : 'change',
					} );
				},

				template : wp.template( 'wpmoly-movie-editor-genres' ),

				/**
				 * Synchronize taxonomy with meta.
				 *
				 * @since 3.0.0
				 *
				 * @return Return itself to allow chaining.
				 */
				synchronize : function() {

					this.controller.synchronize();

					return this;
				},

				/**
				 * Update terms.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				change : function() {

					var terms = this.$( '[data-field]' ).val();
					_.each( this.controller.terms.models, function( term ) {
						if ( ! _.contains( terms, term.get( 'name' ) ) ) {
							this.controller.terms.remove( term, { silent : true } );
						}
					}, this );

					return this;
				},

			}),

			/**
			 * MovieEditor 'Languages' Block View.
			 *
			 * @since 3.0.0
			 */
			LanguagesBlock : PostEditor.view.TaxonomyBlock.extend({

				events : function() {
					return _.extend( {}, _.result( PostEditor.view.TaxonomyBlock.prototype, 'events' ), {
						'change [data-field]' : 'change',
					} );
				},

				template : wp.template( 'wpmoly-movie-editor-languages' ),

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;

					this.on( 'rendered', this.selectize, this );
					this.once( 'rendered', function() {
						this.$el.addClass( 'mode-preview' );
					}, this );

					this.listenTo( this.controller.meta,     'change', this.render );
					this.listenTo( this.controller.snapshot, 'change', this.render );

					this.render();
				},

				/**
				 * Update terms.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				change : function() {

					var value = this.$( '[data-field]' ).val() || [],
					languages = [];

					_.each( value, function( lang ) {
						if ( ! _.isUndefined( wpmolyEditorL10n.native_languages[ lang ] ) ) {
							languages.push( wpmolyEditorL10n.native_languages[ lang ] );
						} else {
							var lang = s.capitalize( lang ),
							language = _.invert( wpmolyEditorL10n.native_languages )[ lang ] || _.invert( wpmolyEditorL10n.standard_languages )[ lang ];
							if ( ! _.isUndefined( language ) ) {
								languages.push( wpmolyEditorL10n.native_languages[ language ] );
							}
						}
					}, this );

					this.controller.meta.set( wpmolyApiSettings.movie_prefix + 'spoken_languages', languages.join( ', ' ) );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var meta = this.controller.meta.toJSON() || {},
					snapshot = this.controller.snapshot.toJSON() || {},
					options = {
						terms : [],
					};


					var languages = meta.spoken_languages || snapshot.spoken_languages || '';
					if ( _.isString( languages ) ) {
						languages = _.filter( languages.split( ', ' ) );
					}

					_.each( languages, function( language ) {
						if ( _.isString( language ) && _.has( snapshot, 'spoken_languages' ) ) {
							language = _.find( snapshot.spoken_languages, { name : s.trim( language ) } );
						}
						options.terms.push( language );
					}, this );

					return options;
				},

			}),

			/**
			 * MovieEditor 'Submit' Block View.
			 *
			 * @since 1.0.0
			 */
			MenuBlock : PostEditor.view.SubmitBlock.extend({

				template : wp.template( 'wpmoly-movie-editor-submit' ),

				events : function() {
					return _.extend( {}, _.result( PostEditor.view.SubmitBlock.prototype, 'events' ), {
						'click [data-mode]'          : 'changeMode',
						'click [data-action="menu"]' : 'toggleMenu',
					} );
				},

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					PostEditor.view.SubmitBlock.prototype.initialize.apply( this, arguments );

					this.listenTo( PostEditor.editor.controller, 'change:mode', this.render );
				},

				/**
				 * Change Editor mode.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} JS 'click' event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				changeMode : function( event ) {

					var $target = this.$( event.currentTarget ),
					       mode = $target.attr( 'data-mode' );

					this.controller.setMode( mode );
					this.closeMenu();

					return this;
				},

				/**
				 * Toggle block menu.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				toggleMenu : function() {

					if ( ! this.$( '.dropdown-menu' ).hasClass( 'active' ) ) {
						this.openMenu();
					} else {
						this.closeMenu();
					}

					return this;
				},

				/**
				 * Open block menu.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				openMenu : function() {

					this.$( '.dropdown-menu' ).addClass( 'active' );

					return this;
				},

				/**
				 * Close block menu.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				closeMenu : function() {

					this.$( '.dropdown-menu' ).removeClass( 'active' );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var options = _.pick( this.controller.post.toJSON(), [ 'link', 'old_edit_link' ] );

					_.extend( options, {
						mode : this.controller.getMode(),
					} );

					return options;
				},

			}),

			/**
			 * MovieEditor 'SearchResults' Block View.
			 *
			 * @since 3.0.0
			 */
			SearchResults : wpmoly.Backbone.View.extend({

				className : 'search-results',

				template : wp.template( 'wpmoly-movie-editor-search-results' ),

				events : function() {
					return _.extend( {}, _.result( wpmoly.Backbone.View.prototype, 'events' ), {
						'click [data-action="jump-to"]'  : 'browse',
						'click [data-action="first"]'    : 'first',
						'click [data-action="last"]'     : 'last',
						'click [data-action="next"]'     : 'next',
						'click [data-action="previous"]' : 'previous',
						'click [data-action="import"]'   : 'import',
					} );
				},

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;

					this.listenTo( this.controller, 'search:start',  this.loading );
					this.listenTo( this.controller, 'search:stop',   this.loaded );
					this.listenTo( this.controller, 'search:update', this.loaded );
					this.listenTo( this.controller, 'search:done',   this.render );
					this.listenTo( this.controller, 'search:reset',  this.render );
					this.listenTo( this.controller, 'search:update', this.render );
				},

				/**
				 * Jump to a specific results page.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} event JS 'click' Event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				browse : function( event ) {

					var $target = this.$( event.currentTarget ),
					       page = $target.attr( 'data-value' );

					this.controller.browse( page );

					return this;
				},

				/**
				 * First results page.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				first : function() {

					this.controller.first();

					return this;
				},

				/**
				 * Last results page.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				last : function() {

					this.controller.last();

					return this;
				},

				/**
				 * Next results page.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				next : function() {

					this.controller.next();

					return this;
				},

				/**
				 * Previous results page.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				previous : function() {

					this.controller.previous();

					return this;
				},

				/**
				 * Import selected movie.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} event JS 'click' Event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				import : function( event ) {

					var $target = this.$( event.currentTarget ),
					   movie_id = $target.attr( 'data-movie-id' );

					this.controller.import( movie_id );

					return this;
				},

				/**
				 * Show loading animation.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loading : function() {

					this.$el.addClass( 'loading' );

					return this;
				},

				/**
				 * Hide loading animation.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loaded : function() {

					this.$el.removeClass( 'loading' );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var options = _.extend( this.controller.settings.toJSON() || {}, {
						results : [],
						state   : {},
					} );

					if ( ! _.isUndefined( this.controller.results ) ) {
						options.results = this.controller.results.toJSON() || {};
						options.state   = _.pick( this.controller.results.state, 'currentPage', 'totalPages', 'totalObjects' );
					}

					return options;
				},

			}),

			SearchLoading : wpmoly.Backbone.View.extend({

				className : 'search-loading',

				template : wp.template( 'wpmoly-movie-editor-search-loading' ),

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;

					this.listenTo( this.controller.result, 'change', this.render );
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function( movie_id ) {

					var options = {
						backdrop : this.controller.result.get( 'backdrop_path' ),
					};

					return options;
				},

			}),

			/**
			 * MovieEditor 'SearchForm' Block View.
			 *
			 * @since 3.0.0
			 */
			SearchForm : wpmoly.Backbone.View.extend({

				className : 'search-form',

				template : wp.template( 'wpmoly-movie-editor-search-form' ),

				events : function() {
					return _.extend( {}, _.result( wpmoly.Backbone.View.prototype, 'events' ), {
						'keypress [data-value="search-query"]'  : 'toggleSearch',
						'keypress [data-value="search-query"]'  : 'toggleSearch',
						'click [data-action="advanced-search"]' : 'advancedSearch',
						'click [data-action="search"]'          : 'startSearch',
						'click [data-action="reset"]'           : 'resetSearch',
						'change [data-setting]'                 : 'changeSetting',
					} );
				},

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;

					this.on( 'rendered', this.selectize, this );

					this.listenTo( this.controller, 'change',       this.render );
					this.listenTo( this.controller, 'search:reset', this.render );
				},

				/**
				 * Start search.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} event JS 'click' event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				toggleSearch : function( event ) {

					if ( 'keypress' === event.type ) {
						if ( 13 === ( event.which || event.charCode || event.keyCode ) ) {
							this.startSearch();
						} else if ( 27 === ( event.which || event.charCode || event.keyCode ) ) {
							this.resetSearch();
						}
					} else {
						event.preventDefault();
					}

					return this;
				},

				/**
				 * Start search.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} event JS 'click' event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				startSearch : function() {

					var query = this.$( '[data-value="search-query"]' ).val();

					this.controller.search( query );

					return this;
				},

				/**
				 * Show/hide advanced search settings.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				advancedSearch : function() {

					this.$el.toggleClass( 'advanced-search' );

					return this;
				},

				/**
				 * Reset search.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				resetSearch : function() {

					this.controller.reset();

					return this;
				},

				/**
				 * Change search settings.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} event JS 'change' event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				changeSetting : function( event ) {

					var target = event.currentTarget,
					$target = this.$( target ),
					setting = $target.attr( 'data-setting' );

					if ( 'checkbox' === target.type ) {
						value = $target.is( ':checked' );
					} else {
						value = $target.val();
					}

					this.controller.settings.set( setting, value );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var options = _.extend( this.controller.settings.toJSON() || {} ,{
						query : this.controller.get( 'query' ),
					} );

					return options;
				},

			}),

			/**
			 * MovieEditor 'Search' Block View.
			 *
			 * @since 3.0.0
			 */
			Search : wpmoly.Backbone.View.extend({

				className : 'search-section-inner',

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;

					this.listenTo( this.controller, 'import:start', this.loading );
					this.listenTo( this.controller, 'import:done',  this.loaded );

					this.setRegions();
				},

				/**
				 * Set subviews.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				setRegions : function() {

					var options = {
						controller : this.controller,
					};

					if ( ! this.downloading ) {
						this.downloading = new MovieEditor.view.SearchLoading( options );
					}

					if ( ! this.form ) {
						this.form = new MovieEditor.view.SearchForm( options );
					}

					if ( ! this.results ) {
						this.results = new MovieEditor.view.SearchResults( options );
					}

					this.views.add( this.downloading );
					this.views.add( this.form );
					this.views.add( this.results );

					return this;
				},

				/**
				 * Show loading animation.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loading : function() {

					this.$el.addClass( 'loading' );

					return this;
				},

				/**
				 * Hide loading animation.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loaded : function() {

					this.$el.removeClass( 'loading' );

					return this;
				},

			}),

			/**
			 * MovieEditor 'Snapshot' Block View.
			 *
			 * @since 3.0.0
			 */
			Snapshot : wpmoly.Backbone.View.extend({

				className : 'snapshot-section-inner mode-summary',

				template : wp.template( 'wpmoly-movie-editor-snapshot' ),

				events : function() {
					return _.extend( {}, _.result( wpmoly.Backbone.View.prototype, 'events' ), {
						'click [data-action="update-snapshot"]' : 'update',
						'click [data-tab]'                      : 'changeTab',
					} );
				},

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					var options = options || {};

					this.controller = options.controller;
					this.post  = this.controller.post;
					this.model = this.controller.snapshot;

					this.listenTo( this.post,  'sync error', this.loaded );
					this.listenTo( this.model, 'change',     this.render );

					this.on( 'rendered', this.renderJSON, this );
				},

				/**
				 * Update snapshot.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				update : function() {

					this.controller.updateSnapshot();
					this.loading();

					return this;
				},

				/**
				 * Switch between tabs.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} event JS 'click' Event.
				 *
				 * @return Returns itself to allow chaining.
				 */
				changeTab : function( event ) {

					var $target = this.$( event.currentTarget ),
					tab = $target.attr( 'data-tab' );

					this.$el.removeClass( 'mode-summary mode-formatted mode-raw' );
					this.$el.addClass( 'mode-' + tab );

					return this;
				},

				/**
				 * Show loading animation.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loading : function() {

					this.$( '[data-action="update-snapshot"]' ).addClass( 'loading' );

					return this;
				},

				/**
				 * Hide loading animation.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				loaded : function() {

					this.$( '[data-action="update-snapshot"]' ).removeClass( 'loading' );

					return this;
				},

				/**
				 * Prepare rendering options.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				prepare : function() {

					var date = new Date( this.controller.snapshot.get( '_snapshot_date' ) )
					days = Date.now() - date.getTime(),
					options = {
						snapshot : this.controller.snapshot.toJSON(),
					};

					options.size = JSON.size( options.snapshot );
					options.date = date.toLocaleDateString();
					options.days = Math.floor( days / 86400000 );

					return options;
				},

				/**
				 * Render JSON to be collapsable.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				renderJSON : function() {

					var json = this.controller.snapshot.toJSON() || {},
					    html = JSON.render( json, {
						level : 1,
					} );

					this.$( '.snapshot-details-panel.formatted-panel' ).html( html );

					return this;
				},

			}),

			/**
			 * MovieEditor Generic 'Editor' Block View.
			 *
			 * @since 3.0.0
			 */
			EditorSection : wpmoly.Backbone.View.extend({

				events : function() {
					return _.extend( {}, _.result( wpmoly.Backbone.View.prototype, 'events' ), {
						'click [data-action="edit"]'   : 'edit',
						'click [data-action="toggle"]' : 'toggle',
						'click [data-action="reload"]' : 'reload',
					} );
				},

				/**
				 * Initialize the View.
				 *
				 * @since 3.0.0
				 *
				 * @param {object} options Options.
				 */
				initialize : function( options ) {

					this.on( 'rendered', this.selectize, this );
				},

				/**
				 * Toggle edit mode.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				edit : function() {

					this.$el.toggleClass( 'mode-preview mode-edit' );

					return this;
				},

				/**
				 * Show/hide editor section.
				 *
				 * @since 3.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				toggle : function() {

					var $icon = this.$( '[data-action="toggle"] .wpmolicon' ),
					 $content = this.$( '.editor-content' );

					if ( $content.hasClass( 'active' ) ) {
						$content.slideUp();
						$content.removeClass( 'active' );
						$icon.removeClass( 'icon-up-open' ).addClass( 'icon-down-open' );
					} else {
						$content.slideDown();
						$content.addClass( 'active' );
						$icon.removeClass( 'icon-down-open' ).addClass( 'icon-up-open' );
					}

					return this;
				},

				/**
				 * Refresh meta from API.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				reload : function() {

					//this.controller.reload();

					return this;
				},

			}),

		} ),

	} );

	_.extend( MovieEditor.controller, {

		BackdropsUploader : MovieEditor.controller.ImagesUploader.extend({

			/**
			 * Set uploader parameters.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setUploaderParameters : function() {

				var defaults = this.defaultUploadParameters();

				this.uploadParameters = _.extend( defaults, {
					post_data : {
						meta_input : {
							_wpmoly_backdrop_related_tmdb_id : this.controller.meta.get( 'tmdb_id' ),
						},
					},
				} );

				return this.uploadParameters;
			},

		}),

		PostersUploader : MovieEditor.controller.ImagesUploader.extend({

			/**
			 * Set uploader parameters.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setUploaderParameters : function() {

				var defaults = this.defaultUploadParameters();

				this.uploadParameters = _.extend( defaults, {
					post_data : {
						meta_input : {
							_wpmoly_poster_related_tmdb_id : this.controller.meta.get( 'tmdb_id' ),
						},
					},
				} );

				return this.uploadParameters;
			},

		}),

		MovieImagesEditor : MovieEditor.controller.ImagesEditor.extend({

			/**
			 * Initialize the Controller.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} attributes Controller attributes.
			 * @param {object} options    Controller options.
			 */
			initialize : function( attributes, options ) {

				MovieEditor.controller.ImagesEditor.prototype.initialize.apply( this, arguments );

				var options = options || {};

				this.node = options.node;
				this.meta = options.meta;
			},

			/**
				* Load attachments.
				*
				* @since 3.0.0
				*
				* @return Returns itself to allow chaining.
				*/
			load : function( images ) {

				var images = _.pluck( this.node.get( this.types ), 'id' );

				return this.loadAttachments( images || [] );
			},

			/**
			 * Import default image.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} model Backdrop Model.
			 *
			 * @return Returns itself to allow chaining.
			 */
			download : function( model ) {

				this.downloadImage( model );

				return this;
			},

			/**
				* Add new attachment to the collection.
				*
				* @since 3.0.0
				*
				* @param {object} attachment
				*
				* @return Returns itself to allow chaining.
				*/
			addAttachment : function( attachment ) {

				var attachment = new wp.api.models.Media( { id : attachment.id } ),
						attributes = {
					post : this.post.get( 'id' ),
					meta : {},
				};

				var tmdb_id = this.meta.get( 'tmdb_id' ) || this.controller.snapshot.get( 'id' );

				attributes.meta[ this.type + '_related_tmdb_id' ] = tmdb_id;

				if ( _.has( this.controller, 'settings' ) ) {
					var meta = this.meta,
					 replace = function( s ) {
						// replace year.
						s = s.replace( '{year}', meta.has( 'release_date' ) ? ( new Date( meta.get( 'release_date' ) ).getFullYear() ) || '' : '' );
						// Sorcery. Replace {property} with node.get( property ), if any.
						return s.replace( /{([a-z_]+)}/gi, function( m, p, d ) { return meta.has( p ) ? meta.get( p ) || m : m; } );
					};

					attributes.title       = replace( this.controller.settings.get( wpmolyApiSettings.option_prefix + 'movie_' + this.type + '_title' ) || '' );
					attributes.caption     = replace( this.controller.settings.get( wpmolyApiSettings.option_prefix + 'movie_' + this.type + '_description' ) || '' );
					attributes.alt_text    = replace( this.controller.settings.get( wpmolyApiSettings.option_prefix + 'movie_' + this.type + '_title' ) || '' );
					attributes.description = replace( this.controller.settings.get( wpmolyApiSettings.option_prefix + 'movie_' + this.type + '_description' ) || '' );
				}

				var self = this;

				// Save related TMDb ID.
				attachment.save( attributes, {
					patch : true,
					wait  : true,
				});

				var images = this.node.get( this.types ) || [];

				images.push({
					id    : attachment.get( 'id' ),
					sizes : attachment.get( 'sizes' ) || {},
				});

				var list = {};
				list[ this.types ] = images;

				this.node.set( list );

				this.loadAttachments();

				return this;
			},

			/**
				* Remove Image.
				*
				* @since 3.0.0
				*
				* @param {object} attachment Image attachment.
				*
				* @return Returns itself to allow chaining.
				*/
			removeImage : function( attachment ) {

				var self = this,
						data = {
					post : null,
					meta : {},
				},
				 options = {
					patch : true,
					beforeSend : function() {
						wpmoly.warning( wpmolyEditorL10n[ 'removing_' + self.type ] );
					},
					success : function() {

						wpmoly.success( wpmolyEditorL10n[ self.type + '_removed' ] );

						var images = _.reject( self.node.get( self.types ) || [], function( image ) {
							return image.id === attachment.get( 'id' );
						}, self );

						var list = {};
						list[ self.types ] = images;

						self.node.set( list );

						self.loadAttachments();
					},
					error : function( model, xhr, options ) {
						wpmoly.error( xhr, { destroy : false } );
					},
				};

				data.meta[ this.type + '_related_tmdb_id' ] = null;

				attachment.save( data, options );

				return this;
			},

			/**
				* Set Image As...
				*
				* @since 3.0.0
				*
				* @param {object} model Image model.
				*
				* @return Returns itself to allow chaining.
				*/
			setAsImage : function( model ) {

				var data = {},
						self = this,
				 options = {
					patch : true,
					beforeSend : function() {
						wpmoly.info( wpmolyEditorL10n[ 'setting_as_' + self.type ] );
					},
					success : function() {
						wpmoly.success( wpmolyEditorL10n[ self.type + '_updated' ] );
					},
					error : function( model, xhr, options ) {
						wpmoly.error( xhr, { destroy : false } );
					},
				};

				data[ this.type + '_id' ] = model.get( 'id' );

				this.controller.meta.save( data, options );

				return this;
			},

		}),

	} );

	_.extend( MovieEditor.controller, {

		BackdropsEditor : MovieEditor.controller.MovieImagesEditor.extend({

			type : 'backdrop',

			types : 'backdrops',

			/**
				* Initialize the Controller.
				*
				* @since 3.0.0
				*
				* @param {object} attributes Controller attributes.
				* @param {object} options    Controller options.
				*/
			initialize : function( attributes, options ) {

				MovieEditor.controller.MovieImagesEditor.prototype.initialize.apply( this, arguments );

				this.uploader = new PostEditor.controller.BackdropsUploader( [], { controller : this } );

				this.mirrorEvents();
			},

		}),

		PostersEditor : MovieEditor.controller.MovieImagesEditor.extend({

			type : 'poster',

			types : 'posters',

			/**
				* Initialize the Controller.
				*
				* @since 3.0.0
				*
				* @param {object} attributes Controller attributes.
				* @param {object} options    Controller options.
				*/
			initialize : function( attributes, options ) {

				MovieEditor.controller.MovieImagesEditor.prototype.initialize.apply( this, arguments );

				this.uploader = new PostEditor.controller.PostersUploader( [], { controller : this } );

				this.mirrorEvents();
			},

			/**
			 * Import default poster.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} model Poster Model.
			 *
			 * @return Returns itself to allow chaining.
			 */
			 download : function( model ) {

				this.uploader.once( 'upload:stop', this.setFeaturedPoster, this );

 				this.downloadImage( model );

 				return this;
 			},

			/**
			 * Set poster as featured image.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} uploader
			 * @param {object} file
			 *
			 * @return Returns itself to allow chaining.
			 */
			setFeaturedPoster : function( uploader, file ) {

				this.controller.setFeaturedPoster( file.attachment.get( 'id' ) );

				return this;
			},

		}),

	} );

	_.extend( MovieEditor.view, {

		Image : wpmoly.Backbone.View.extend({

			events : function() {
				return _.extend( {}, _.result( wpmoly.Backbone.View.prototype, 'events' ), {
					'click [data-action="download"]' : 'downloadImage',
					'click [data-action="remove"]'   : 'removeImage',
					'click [data-action="set-as"]'   : 'setAsImage',
					'click [data-action="open"]'     : 'openImage',
				} );
			},

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				var options = options || {};

				this.controller = options.controller;

				this.snapshot    = this.controller.snapshot;
				this.images      = this.controller[ this.types ];
				this.uploader    = this.images.uploader;
				this.attachments = this.images.attachments;

				this.listenTo( this.attachments, 'add',    this.addAttachment );
				this.listenTo( this.attachments, 'remove', this.removeAttachment );
				this.listenTo( this.attachments, 'update', this.update );

				this.listenTo( this.uploader, 'download:start',    this.uploading );
				this.listenTo( this.uploader, 'upload:stop',       this.uploaded );
				this.listenTo( this.uploader, 'upload:failed',     this.uploaded );
				this.listenTo( this.uploader, 'upload:progress',   this.showUploadProgress );
				this.listenTo( this.uploader, 'download:progress', this.showDownloadProgress );

				this.listenTo( this.controller, this.types + ':filter', this.filter );

				this.listenTo( this.snapshot, 'change', this.render );

				this.attachment = false;
				this.uploading = false;

				this.setAttachment();
			},

			/**
			 * Set related attachment, if any.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setAttachment : function() {

				if ( this.model.has( 'id' ) ) {
					this.attachment = this.model;
				} else if ( this.model.has( 'file_path' ) ) {
					var images = this.snapshot.get( 'images' ) || {},
					attachments = this.attachments;

					var filename = /\/([^/.]+)\.[^.]*$/.exec( this.model.get( 'file_path' ) || '' ) || [];
					if ( 1 < filename.length ) {
						// Find corresponding attachment, if any.
						var attachment = attachments.find( function( att ) {
							return 0 < att.get( 'source_url' ).indexOf( filename[1] );
						} );
						// Ship attachment, if any.
						if ( ! _.isUndefined( attachment ) ) {
							this.attachment = attachment;
						}
					}
				}

				return this;
			},

			/**
			 * Filter Image.
			 *
			 * @since 3.0.0
			 *
			 * @param {string} filter Filter name.
			 * @param {string} value  Filter value.
			 *
			 * @return Returns itself to allow chaining.
			 */
			filter : function( filter, value ) {

				var check = this.$el.attr( 'data-' + filter || '' );
				if ( _.isEmpty( value ) ) {
					this.$el.show();
				} else {
					if ( ! _.isEmpty( check ) && value === check ) {
						this.$el.show();
					} else {
						this.$el.hide();
					}
				}

				return this;
			},

			/**
			 * Download Image.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			downloadImage : function() {

				this.images.downloadImage( this.model );

				return this;
			},

			/**
			 * Remove Image from collection.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			removeImage : function() {

				this.images.removeImage( this.attachment );

				return this;
			},

			/**
			 * Set Image.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setAsImage : function() {

				this.images.setAsImage( this.attachment );

				return this;
			},

			/**
			 * Open Image.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			openImage : function() {

				if ( this.attachment ) {
					this.images.editImage( this.attachment );
				}

				return this;
			},

			/**
			 * Show upload progress.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} uploader
			 * @param {object} file
			 *
			 * @return Returns itself to allow chaining.
			 */
			showUploadProgress : function( uploader, file ) {

				if ( this.uploading && this.model.get( 'file_path' ) === '/' + file.name ) {
					this.$( '.upload-progress .progress-bar' ).width( file.percent + '%' );
				}

				return this;
			},

			/**
			 * Show upload progress.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} model
			 * @param {int}    percent
			 *
			 * @return Returns itself to allow chaining.
			 */
			showDownloadProgress : function( model, percent ) {

				if ( this.uploading && model.get( 'file_path' ) === this.model.get( 'file_path' ) ) {
					this.$( '.download-progress .progress-bar' ).width( percent + '%' );
				}

				return this;
			},

			/**
			 * Show uploading animation.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			uploading : function( model ) {

				if ( model.get( 'file_path' ) === this.model.get( 'file_path' ) ) {
					this.uploading = true;
					this.$el.addClass( 'uploading' );
				}

				return this;
			},

			/**
			 * Hide uploading animation.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			uploaded : function( uploader, file ) {

				if ( this.uploading && this.model.get( 'file_path' ) === '/' + file.name ) {
					this.$el.removeClass( 'uploading' );
				}

				return this;
			},

			/**
			 * Add freshly uploaded attachment.
			 *
			 * Whenever a new attachment is added to the collection, check for a match
			 * with current image and render the view if needed.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} model
			 * @param {object} collection
			 * @param {object} options
			 *
			 * @return Returns itself to allow chaining.
			 */
			addAttachment : function( model, collection, options ) {

				if ( ! this._matchAttachment( model ) ) {
					return this;
				}

				if ( ! this.attachment ) {
					this.attachment = model;
				}

				this.render();
				this.$el.addClass( 'has-attachment' );
				this.$el.removeClass( 'uploading' );

				return this;
			},

			/**
			 * Remove attachment.
			 *
			 * Whenever an existing attachment is removed from the collection, check
			 * for a match with current image and render the view if needed.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} model
			 * @param {object} collection
			 * @param {object} options
			 *
			 * @return Returns itself to allow chaining.
			 */
			removeAttachment : function( model, collection, options ) {

				if ( ! this._matchAttachment( model ) ) {
					return this;
				}

				if ( this.attachment ) {
					this.attachment = false;
				}

				this.render();
				this.$el.removeClass( 'has-attachment uploading' );

				return this;
			},

			/**
			 * Match an attachment to the current image model.
			 *
			 * Compare the current image's filename with the attachment's slug. Matching
			 * attachments should have a slug corresponding to the model's lowercased
			 * filename striped from extension.
			 *
			 * @param {object} attachment
			 *
			 * @return boolean
			 */
			_matchAttachment : function( attachment ) {

				var slug = attachment.get( 'slug' ),
				filename = /\/([^/.]+)\.[^.]*$/.exec( this.model.get( 'file_path' ) || '' ) || [];
				if ( 0 <= slug.indexOf( ( filename[1] || '' ).toLowerCase() || false ) ) {
					return true;
				}

				return false;
			},

			/**
			 * Render the View.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			render : function() {

				wpmoly.Backbone.View.prototype.render.apply( this, arguments );

				var options = this.prepare();
				if ( options.attachment || 'attachment' === options.type ) {
					this.$el.addClass( 'has-attachment' );
				}

				if ( this.uploading ) {
					this.$el.addClass( 'uploading' );
				}

				this.$el.attr( 'data-language', options.lang );
				this.$el.attr( 'data-size', options.size );
				this.$el.attr( 'data-ratio', ( options.ratio > 1 ? 'landscape' : 'portrait' ) );

				return this;
			},

		}),

		ImagesEditorMenu : wpmoly.Backbone.View.extend({

			events : function() {
				return _.extend( {}, _.result( wpmoly.Backbone.View.prototype, 'events' ), {
					'change [data-filter="language"]' : 'filterByLanguage',
					'change [data-filter="ratio"]'    : 'filterByRatio',
					'change [data-filter="size"]'     : 'filterBySize',
				} );
			},

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				var options = options || {};

				this.controller  = options.controller;
				this.images      = this.controller[ this.types ];
				this.snapshot    = this.controller.snapshot
				this.attachments = this.images.attachments;

				this.listenTo( this.images,      'update', this.render );
				this.listenTo( this.snapshot,    'change', this.render );
				this.listenTo( this.attachments, 'update', this.render );

				this.on( 'rendered', this.selectize, this );
			},

			/**
			 * Filter images by language.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			filterByLanguage : function() {

				var language = this.$( '[data-filter="language"]' ).val() || '';

				this.controller.trigger( this.types + ':filter', 'language', language );

				return this;
			},

			/**
			 * Filter images by ratio.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			filterByRatio : function() {

				var ratio = this.$( '[data-filter="ratio"]' ).val() || '';

				this.controller.trigger( this.types + ':filter', 'ratio', ratio );

				return this;
			},

			/**
			 * Filter images by size.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			filterBySize : function() {

				var size = this.$( '[data-filter="size"]' ).val() || '';

				this.controller.trigger( this.types + ':filter', 'size', size );

				return this;
			},

			/**
			 * Prepare rendering options.
			 *
			 * @since 3.0.0
			 *
			 * @return {object}
			 */
			prepare : function() {

				var images = this.controller.snapshot.get( this.types ) || {},
				   options = {
					languages : _.uniq( _.pluck( images.images, 'iso_639_1' ) ),
				};

				return options;
			},

		}),

		ImagesEditorContent : wpmoly.Backbone.View.extend({

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				var options = options || {};

				this.controller = options.controller;

				this.listenTo( this.controller.snapshot, 'change:images', this.render );
			},

			/**
			 * .
			 *
			 * @since 3.0.0
			 *
			 * @param {object} collection
			 * @param {object} options
			 *
			 * @return Returns itself to allow chaining.
			 */
			addItems : function( collection, options ) {

				var images = this.controller.snapshot.get( 'images' ) || {};
				_.each( images[ this.types ] || [], function( image ) {
					var view = new this.imageView({
						model      : new Backbone.Model( image ),
						controller : this.controller,
					});
					this.views.add( view );
				}, this );

				return this;
			},

			/**
			 * Render the View.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			render : function() {

				wpmoly.Backbone.View.prototype.render.apply( this, arguments );

				this.addItems();

				return this;
			},

		}),

		ImagesEditorUploader : MovieEditor.view.EditorSection.extend({

			events : function() {
				return _.extend( {}, _.result( MovieEditor.view.EditorSection.prototype, 'events' ), {
					'click [data-action="select-image"]' : 'select',
				} );
			},

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				MovieEditor.view.EditorSection.prototype.initialize.call( this, arguments );

				var options = options || {};

				this.controller  = options.controller;
				this.images      = this.controller[ this.types ];
				this.uploader    = this.images.uploader;
				this.attachments = this.images.attachments;

				this.listenTo( this.images.attachments, 'update', this.render );

				this.on( 'ready', this.addItems, this );
				this.on( 'ready', this.setUploader, this );

				this.render();
			},

			/**
			 * Set wp.Uploader instance.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setUploader : function() {

				var $dropzone = this.$( '.uploader-dropzone' ),
				   $container = this.$( '.uploader-container' );
				if ( $dropzone.length && $container.length ) {
					this.uploader.setUploader({
						container : $container,
						dropzone  : $dropzone,
					});
				}

				return this;
			},

			/**
			 * Select images from the library to use as images.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			select : function() {

				if ( this.frame ) {
					return this.frame.open();
				}

				this.frame = wp.media({
					title   : wpmolyEditorL10n[ 'custom_' + this.types ],
					library : {
						type  : 'image',
					},
					button : {
						text : wpmolyEditorL10n[ 'use_as_custom_' + this.types ],
					},
					multiple : true,
				});

				this.frame.on( 'select', _.bind( this.addAttachments, this ) );

				this.frame.open();

				return this;
			},

			/**
			 * Add selected images to the attachments collection.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			addAttachments : function() {

				// Grab the selected attachment.
				var attachments = this.frame.state().get( 'selection' );

				// Add to collection.
				attachments.map( this.images.addAttachment, this.images );

				// Close frame.
				this.frame.close();

				return this;
			},

			/**
			 * .
			 *
			 * @since 3.0.0
			 *
			 * @param {object} collection
			 * @param {object} options
			 *
			 * @return Returns itself to allow chaining.
			 */
			addItems : function( collection, options ) {

				this.views.remove();

				var images = this.controller.snapshot.get( this.types ) || {},
				    images = images.images || [],
				attachments = _.clone;

				this.attachments.each( function( attachment, index ) {
					var image = _.find( images, function( image ) {
						return 0 < attachment.get( 'source_url' ).indexOf( image.file_path );
					} );
					if ( _.isUndefined( image ) ) {

						var view = new this.imageView({
							model      : attachment,
							controller : this.controller,
						});
						this.views.add( '.uploader-container', view );
					}
				}, this );

				return this;
			},

			/**
			 * Render the View.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			render : function() {

				wpmoly.Backbone.View.prototype.render.apply( this, arguments );

				this.trigger( 'ready' );

				return this;
			},

		}),

		/**
		 * MovieEditor 'Images Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		ImagesEditor : MovieEditor.view.EditorSection.extend({

			events : function() {
				return _.extend( {}, _.result( MovieEditor.view.EditorSection.prototype, 'events' ), {
					'click [data-action="download"]' : 'switchTab',
					'click [data-action="upload"]'   : 'switchTab',
				} );
			},

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				MovieEditor.view.EditorSection.prototype.initialize.apply( this, arguments );

				var options = options || {};

				this.controller = options.controller;

				this.setRegions();
			},

			/**
			 * Switch content tabs.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} JS 'click' event.
			 *
			 * @return Returns itself to allow chaining.
			 */
			switchTab : function( event ) {

				var $target = this.$( event.currentTarget ),
				        tab = $target.attr( 'data-action' );

				this.$el.removeClass( function ( i, c ) {
					return ( c.match(/(^|\s)mode-\S+/g) || [] ).join( ' ' );
				} ).addClass( 'mode-' + tab );

				return this;
			},

		}),

	} );

	_.extend( MovieEditor.view, {

		/**
		 * Backdrop Editor single backdrop View.
		 *
		 * @since 3.0.0
		 */
		Backdrop : MovieEditor.view.Image.extend({

			className : 'image backdrop',

			template : wp.template( 'wpmoly-movie-backdrops-editor-item' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'backdrop';
				this.types     = 'backdrops';

				MovieEditor.view.Image.prototype.initialize.apply( this, arguments );
			},

			/**
			 * Prepare rendering options.
			 *
			 * @since 3.0.0
			 *
			 * @return {object}
			 */
			prepare : function() {

				var options = {};
				if ( this.attachment ) {
					options = {
						type   : 'attachment',
						url    : this.attachment.get( 'media_details' ).sizes.medium.source_url,
						width  : this.attachment.get( 'media_details' ).width,
						height : this.attachment.get( 'media_details' ).height,
						ratio  : this.attachment.get( 'media_details' ).width / this.attachment.get( 'media_details' ).height,
					};
				} else {
					options = {
						url    : 'https://image.tmdb.org/t/p/w300' + this.model.get( 'file_path' ),
						width  : this.model.get( 'width' ),
						height : this.model.get( 'height' ),
						ratio  : this.model.get( 'ratio' ),
						lang   : this.model.get( 'iso_639_1' ),
					};
				}

				options.size = '';

				if ( options.width ) {
					if ( 1500 <= options.width ) {
						options.size = 'huge';
					} else if ( 1000 <= options.width ) {
						options.size = 'large';
					} else if ( 500 <= options.width ) {
						options.size = 'medium';
					} else if ( 250 <= options.width ) {
						options.size = 'small';
					}
				}

				return options;

			},

		}),

		/**
		 * Backdrop Editor Menu View.
		 *
		 * @since 3.0.0
		 */
		BackdropsEditorMenu : MovieEditor.view.ImagesEditorMenu.extend({

			className : 'images-editor-menu backdrops-editor-menu',

			template : wp.template( 'wpmoly-movie-backdrops-editor-menu' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'backdrop';
				this.types     = 'backdrops';

				MovieEditor.view.ImagesEditorMenu.prototype.initialize.apply( this, arguments );
			},

		}),

		/**
		 * Backdrop Editor Content View.
		 *
		 * @since 3.0.0
		 */
		BackdropsEditorContent : MovieEditor.view.ImagesEditorContent.extend({

			className : 'images-editor-content backdrops-editor-content',

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'backdrop';
				this.types     = 'backdrops';
				this.imageView = MovieEditor.view.Backdrop;

				MovieEditor.view.ImagesEditorContent.prototype.initialize.apply( this, arguments );
			},

		}),

		/**
		 * Backdrop Editor Uploader View.
		 *
		 * @since 3.0.0
		 */
		BackdropsEditorUploader : MovieEditor.view.ImagesEditorUploader.extend({

			className : 'images-uploader backdrops-uploader',

			template : wp.template( 'wpmoly-movie-backdrops-editor-uploader' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'backdrop';
				this.types     = 'backdrops';
				this.imageView = MovieEditor.view.Backdrop;

				MovieEditor.view.ImagesEditorUploader.prototype.initialize.apply( this, arguments );
			},

		}),

		/**
		 * Poster Editor single poster View.
		 *
		 * @since 3.0.0
		 */
		Poster : MovieEditor.view.Image.extend({

			className : 'image poster',

			template : wp.template( 'wpmoly-movie-posters-editor-item' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'poster';
				this.types     = 'posters';

				MovieEditor.view.Image.prototype.initialize.apply( this, arguments );
			},

			/**
			 * Prepare rendering options.
			 *
			 * @since 3.0.0
			 *
			 * @return {object}
			 */
			prepare : function() {

				var options = {};
				if ( this.attachment ) {
					options = {
						type   : 'attachment',
						url    : this.attachment.get( 'media_details' ).sizes.medium.source_url,
						width  : this.attachment.get( 'media_details' ).width,
						height : this.attachment.get( 'media_details' ).height,
						ratio  : this.attachment.get( 'media_details' ).width / this.attachment.get( 'media_details' ).height,
						lang   : this.model.get( 'iso_639_1' ),
					};
				} else {
					options = {
						url    : 'https://image.tmdb.org/t/p/w185' + this.model.get( 'file_path' ),
						width  : this.model.get( 'width' ),
						height : this.model.get( 'height' ),
						ratio  : this.model.get( 'ratio' ),
						lang   : this.model.get( 'iso_639_1' ),
					};
				}

				options.size = '';

				if ( options.height ) {
					if ( 1500 <= options.height ) {
						options.size = 'huge';
					} else if ( 1000 <= options.height ) {
						options.size = 'large';
					} else if ( 500 <= options.height ) {
						options.size = 'medium';
					} else if ( 250 <= options.height ) {
						options.size = 'small';
					}
				}

				return options;
			},

		}),

		/**
		 * Poster Editor Menu View.
		 *
		 * @since 3.0.0
		 */
		PostersEditorMenu : MovieEditor.view.ImagesEditorMenu.extend({

			className : 'images-editor-menu posters-editor-menu',

			template : wp.template( 'wpmoly-movie-posters-editor-menu' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'poster';
				this.types     = 'posters';

				MovieEditor.view.ImagesEditorMenu.prototype.initialize.apply( this, arguments );
			},

		}),

		/**
		 * Poster Editor Content View.
		 *
		 * @since 3.0.0
		 */
		PostersEditorContent : MovieEditor.view.ImagesEditorContent.extend({

			className : 'images-editor-content posters-editor-content',

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'poster';
				this.types     = 'posters';
				this.imageView = MovieEditor.view.Poster;

				MovieEditor.view.ImagesEditorContent.prototype.initialize.apply( this, arguments );
			},

		}),

		/**
		 * Poster Editor Uploader View.
		 *
		 * @since 3.0.0
		 */
		PostersEditorUploader : MovieEditor.view.ImagesEditorUploader.extend({

			className : 'images-uploader posters-uploader',

			template : wp.template( 'wpmoly-movie-posters-editor-uploader' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'poster';
				this.types     = 'posters';
				this.imageView = MovieEditor.view.Poster;

				MovieEditor.view.ImagesEditorUploader.prototype.initialize.apply( this, arguments );
			},

		}),

	} );

	_.extend( MovieEditor.view, {

		/**
		 * MovieEditor 'Meta Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		MetaEditor : MovieEditor.view.EditorSection.extend({

			className : 'wpmoly movie-headbox mode-preview',

			template : wp.template( 'wpmoly-movie-meta-editor' ),

			events : function() {
				return _.extend( {}, _.result( MovieEditor.view.EditorSection.prototype, 'events' ), {
					'change [data-field]' : 'change',
				} );
			},

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				MovieEditor.view.EditorSection.prototype.initialize.apply( this, arguments );

				var options = options || {};

				this.controller = options.controller;

				this.listenTo( this.controller.posters,   'download:start', this.loadingPoster );
				this.listenTo( this.controller.posters,   'upload:stop',    this.loadedPoster );
				this.listenTo( this.controller.backdrops, 'download:start', this.loadingBackdrop );
				this.listenTo( this.controller.backdrops, 'upload:stop',    this.loadedBackdrop );

				this.listenTo( this.controller.node,     'sync',   this.render );
				this.listenTo( this.controller.meta,     'change', this.render );
				this.listenTo( this.controller.snapshot, 'change', this.render );
			},

			/**
			 * Show poster loading animation.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			loadingPoster : function() {

				this.$( '.headbox-poster' ).addClass( 'loading' );

				return this;
			},

			/**
			 * Hide poster loading animation.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			loadedPoster : function() {

				this.$( '.headbox-poster' ).removeClass( 'loading' );

				return this;
			},

			/**
			 * Show backdrop loading animation.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			loadingBackdrop: function() {

				this.$( '.headbox-backdrop' ).addClass( 'loading' );

				return this;
			},

			/**
			 * Hide backdrop loading animation.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			loadedBackdrop : function() {

				this.$( '.headbox-backdrop' ).removeClass( 'loading' );

				return this;
			},


			/**
			 * Update node meta.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} event JS 'change' Event.
			 *
			 * @return Returns itself to allow chaining.
			 */
			change : function( event ) {

				var $target = this.$( event.currentTarget ),
				      field = $target.attr( 'data-field' ),
				      value = $target.val();

				this.controller.meta.set( field, value );

				return this;
			},

			/**
			 * Prepare rendering options.
			 *
			 * @since 3.0.0
			 *
			 * @return {object}
			 */
			prepare : function() {

				var options = {},
				   defaults = this.controller.node.defaults || {},
				   snapshot = this.controller.snapshot.toJSON() || {},
					     meta = this.controller.meta.toJSON() || {},
					     node = this.controller.node.toJSON() || {};

				_.each( defaults, function( value, key ) {

					var option = {
						meta     : _.has( meta, key ) ? meta[ key ] : null,
						node     : _.has( node, key ) ? node[ key ] : null,
						snapshot : _.has( snapshot, key ) ? snapshot[ key ] : null,
						default  : value,
						status   : null,
					};

					if ( _.isNull( option.meta ) && ! _.isNull( option.snapshot ) ) {
						option.status = 'snapshot';
					} else if ( ! _.isNull( option.meta ) && option.meta !== this.controller.post.getMeta( wpmolyApiSettings.movie_prefix + key ) ) {
						option.status = 'changed';
						option.node = option.meta;
					} else if ( ! _.isNull( option.meta ) && option.meta !== this.controller.post.getMeta( wpmolyApiSettings.movie_prefix + key ) ) {
						option.status = 'saved';
					}

					options[ key ] = option;
				}, this );

				if ( ! _.isNull( meta.release_date ) ) {
					options.year = new Date( meta.release_date ).getFullYear();
				} else if ( ! _.isNull( snapshot.release_date ) ) {
					options.year = new Date( snapshot.release_date ).getFullYear();
				} else {
					options.year = null;
				}

				if ( _.has( node.poster || {}, 'id' ) && _.isNumber( node.poster.id ) ) {
					options.poster = node.poster.sizes.medium.url;
				} else if ( _.has( snapshot.images || {}, 'posters' ) ) {
					var poster = _.first( snapshot.images.posters );
					options.poster = ! _.isUndefined( poster ) ? 'https://image.tmdb.org/t/p/w185' + poster.file_path : ! _.isUndefined( options.poster ) ? options.poster.sizes.medium.url : '';
				}

				if ( _.has( node.backdrop || {}, 'id' ) && _.isNumber( node.backdrop.id ) ) {
					options.backdrop = node.backdrop.sizes.large.url;
				} else if ( _.has( snapshot.images || {}, 'backdrops' ) ) {
					var backdrop = _.first( snapshot.images.backdrops );
					options.backdrop = ! _.isUndefined( backdrop ) ? 'https://image.tmdb.org/t/p/original' + backdrop.file_path : ! _.isUndefined( options.backdrop ) ? options.backdrop.sizes.large.url : '';
				}

				return options;
			},

		}),

		/**
		 * MovieEditor 'Credits Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		CreditsEditor : MovieEditor.view.EditorSection.extend({

			className : 'wpmoly movie-credits mode-preview',

			template : wp.template( 'wpmoly-movie-credits-editor' ),

			events : function() {
				return _.extend( {}, _.result( MovieEditor.view.EditorSection.prototype, 'events' ), {
					'change [data-field]'          : 'change',
					'click [data-action="import"]' : 'import',
				} );
			},

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				MovieEditor.view.EditorSection.prototype.initialize.apply( this, arguments );

				var options = options || {};

				this.controller = options.controller;

				this.listenTo( this.controller.node,     'sync',   this.render );
				this.listenTo( this.controller.meta,     'change', this.render );
				this.listenTo( this.controller.persons,  'change', this.render );
				this.listenTo( this.controller.snapshot, 'change', this.render );
			},

			/**
			 * Update node meta.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} event JS 'change' Event.
			 *
			 * @return Returns itself to allow chaining.
			 */
			change : function( event ) {

				var $target = this.$( event.currentTarget ),
				      field = $target.attr( 'data-field' ),
				      value = $target.val();

				if ( _.isArray( value ) ) {
					value = value.join( ', ' );
				}

				this.controller.meta.set( field, value );

				return this;
			},

			/**
			 * Import person.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} event JS 'click' Event.
			 *
			 * @return Returns itself to allow chaining.
			 */
			import : function( event ) {

				var $target = this.$( event.currentTarget ),
				  person_id = $target.attr( 'data-person-id' );

				this.controller.importPerson( person_id );

				return this;
			},

			/**
			 * Prepare rendering options.
			 *
			 * @since 3.0.0
			 *
			 * @return {object}
			 */
			prepare : function() {

				var credits = this.controller.snapshot.get( 'credits' ) || {},
				    persons = this.controller.persons,
				    options = {
					actors      : [],
					authors     : [],
					composers   : [],
					directors   : [],
					photography : [],
					producers   : [],
					writers     : [],
					persons     : [],
				};

				if ( credits.crew ) {
					options.authors     = _.where( credits.crew, { job : 'Author' } );
					options.composers   = _.where( credits.crew, { job : 'Original Music Composer' } );
					options.directors   = _.where( credits.crew, { job : 'Director' } );
					options.photography = _.where( credits.crew, { job : 'Director of Photography' } );
					options.producers   = _.where( credits.crew, { job : 'Producer' } );
					options.writers     = _.where( credits.crew, { job : 'Novel' } );
				}

				if ( credits.cast ) {
					options.actors = credits.cast;
				}

				persons.each( function( person ) {
					options.persons.push({
						id        : person.get( 'id' ),
						edit_link : person.get( 'edit_link' ),
						tmdb_id   : person.getMeta( wpmolyApiSettings.person_prefix + 'tmdb_id' ),
						name      : person.getMeta( wpmolyApiSettings.person_prefix + 'name' ),
					});
				} );

				return options;
			},

		}),

		/**
		 * MovieEditor 'Posters Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		PostersEditor : MovieEditor.view.ImagesEditor.extend({

			className : 'wpmoly movie-images movie-posters mode-download',

			template : wp.template( 'wpmoly-movie-posters-editor' ),

			/**
			 * Set subviews.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setRegions : function() {

				var options = {
					controller : this.controller,
				};

				if ( ! this.menu ) {
					this.menu = new MovieEditor.view.PostersEditorMenu( options );
				}

				if ( ! this.content ) {
					this.content = new MovieEditor.view.PostersEditorContent( options );
				}

				if ( ! this.uploader ) {
					this.uploader = new MovieEditor.view.PostersEditorUploader( options );
				}

				this.views.set( '.editor-content-download .panel.left',  this.menu );
				this.views.set( '.editor-content-download .panel.right', this.content );
				this.views.add( '.editor-content-upload .panel.right',   this.uploader );

				return this;
			},

		}),

		/**
		 * MovieEditor 'Backdrops Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		BackdropsEditor : MovieEditor.view.ImagesEditor.extend({

			className : 'wpmoly movie-images movie-backdrops mode-download',

			template : wp.template( 'wpmoly-movie-backdrops-editor' ),

			/**
			 * Set subviews.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setRegions : function() {

				var options = {
					controller : this.controller,
				};

				if ( ! this.menu ) {
					this.menu = new MovieEditor.view.BackdropsEditorMenu( options );
				}

				if ( ! this.content ) {
					this.content = new MovieEditor.view.BackdropsEditorContent( options );
				}

				if ( ! this.uploader ) {
					this.uploader = new MovieEditor.view.BackdropsEditorUploader( options );
				}

				this.views.set( '.editor-content-download .panel.left',  this.menu );
				this.views.set( '.editor-content-download .panel.right', this.content );
				this.views.add( '.editor-content-upload .panel.right',   this.uploader );

				return this;
			},

		}),

		/**
		 * MovieEditor 'Preview' Block View.
		 *
		 * @since 3.0.0
		 */
		Preview : wpmoly.Backbone.View.extend({

			className : 'editor-preview',

			template : wp.template( 'wpmoly-movie-editor-preview' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				var options = options || {};

				this.controller = options.controller;

				this.setRegions();
			},

			/**
			 * Set subviews.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setRegions : function() {

				var options = {
					controller : this.controller,
				};

				if ( ! this.meta ) {
					this.meta = new MovieEditor.view.MetaEditor( options );
				}

				if ( ! this.credits ) {
					this.credits = new MovieEditor.view.CreditsEditor( options );
				}

				if ( ! this.backdrops ) {
					this.backdrops = new MovieEditor.view.BackdropsEditor( options );
				}

				if ( ! this.posters ) {
					this.posters = new MovieEditor.view.PostersEditor( options );
				}

				this.views.set( '#wpmoly-movie-meta',      this.meta );
				this.views.set( '#wpmoly-movie-credits',   this.credits );
				this.views.set( '#wpmoly-movie-backdrops', this.backdrops );
				this.views.set( '#wpmoly-movie-posters',   this.posters );

				return this;
			},

			/**
			 * Render the View.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			render : function() {

				wpmoly.Backbone.View.prototype.render.apply( this, arguments );

				this.$el.addClass( this.className );

				return this;
			},

		}),

		/**
		 * MovieEditor 'Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		Editor : wpmoly.Backbone.View.extend({

			className : '',

			template : wp.template( 'wpmoly-movie-editor' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				var options = options || {};

				this.controller = options.controller;

				this.listenTo( this.controller, 'change:mode', this.setMode );

				this.setRegions();
			},

			/**
			 * Change editor mode.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setMode : function() {

				this.$el.removeClass( 'mode-' + this.controller.previous( 'mode' ) );
				this.$el.addClass( 'mode-' + this.controller.get( 'mode' ) );

				return this;
			},

			/**
			 * Set subviews.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setRegions : function() {

				var options = {
					controller : this.controller,
				};

				if ( ! this.preview ) {
					this.preview = new MovieEditor.view.Preview( options );
				}

				if ( ! this.search ) {
					this.search = new MovieEditor.view.Search( { controller : this.controller.search } );
				}

				if ( ! this.snapshot ) {
					this.snapshot = new MovieEditor.view.Snapshot( options );
				}

				this.views.set( '#wpmoly-movie-preview',  this.preview );
				this.views.set( '#wpmoly-movie-search',   this.search );
				this.views.set( '#wpmoly-movie-snapshot', this.snapshot );

				return this;
			},

			/**
			 * Render the View.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			render : function() {

				wpmoly.Backbone.View.prototype.render.apply( this, arguments );

				this.setMode();

				return this;
			},

		}),

	} );

	/**
	 * Create movie editor instance.
	 *
	 * @since 3.0.0
	 */
	MovieEditor.loadEditor = function() {

		var editor = document.querySelector( '#wpmoly-movie-editor' );
		if ( editor ) {
			MovieEditor.editor = new Editor( editor );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 3.0.0
	 */
	MovieEditor.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			MovieEditor.loadEditor();
			PostEditor.loadSidebar();
		} );

		return MovieEditor;
	};

})( jQuery, _, Backbone );

wpmoly.runners['movie'] = wpmoly.editor.movie;
