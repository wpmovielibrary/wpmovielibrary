/**
 * TheMovieDB.org API Client.
 *
 * @since 3.0.0
 */
var TMDb = window.tmdb = {

	models : {},

	collections : {},

	root : 'https://api.themoviedb.org',

	version : 3,

	/**
	 * API Settings.
	 *
	 * @since 3.0.0
	 */
	settings : {},

	/**
	 * Initialize the API client.
	 *
	 * @since 3.0.0
	 */
	init : function( options ) {

		var options = options || {},
		   settings = _.pick( window.tmdbApiSettings || {}, 'adult', 'api_key', 'api_key', 'country', 'language' );

		TMDb.settings = _.defaults( window.tmdbApiSettings || {}, _.extend( {
			adult               : true,
			alternative_country : '',
			api_key             : '',
			country             : 'US',
			language            : 'en',
		}, options || {} ) );

		if ( _.isEmpty( TMDb.settings.api_key ) ) {
			TMDb.root = 'https://api.wpmovielibrary.com';
		}
	},

};

TMDb.init();

(function( _, Backbone, wp ) {

	'use strict';

	var Base = {

		parameters : {
			api_key : {
				type     : 'string',
				default  : TMDb.settings.api_key,
				required : ! _.isEmpty( TMDb.settings.api_key ),
				pattern  : /[a-z0-9]{32}/i,
			},
			append_to_response : {
				type     : 'string',
				default  : '',
				required : false,
				pattern  : /[^,(?! )]+/g,
			},
			include_adult : {
				type     : 'boolean',
				default  : TMDb.settings.adult,
				required : false,
			},
			include_image_language : {
				type     : 'string',
				default  : 'null',
				required : false,
				pattern  : /[^,(?! )]+/g,
			},
			language : {
				type     : 'string',
				default  : TMDb.settings.language,
				required : false,
				pattern  : /^[a-z]{2}$|^[a-z]{2}-[A-Z]{2}$/i,
			},
			page : {
				type     : 'string',
				default  : '1',
				required : false,
				pattern  : /^[0-9]+$/g,
			},
			primary_release_year : {
				type     : 'string',
				default  : 'null',
				required : false,
				pattern  : /^[1-2][0-9]{3}$/g,
			},
			query : {
				type     : 'string',
				default  : '',
				required : true,
				pattern  : /\w+/,
			},
			year : {
				type     : 'string',
				default  : 'null',
				required : false,
				pattern  : /^[1-2][0-9]{3}$/g,
			},
		},

		/**
		 * Iterate through supported parameters to build a proper API query using
		 * only required and non-empty optional parameters.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} data Submitted parameters.
		 *
		 * @return {object}
		 */
		prepareParameters : function( data ) {

			var filtered = {};

			_.each( BaseModel.prototype.parameters, function( parameter, name ) {

				// Only handle the parameters supported by the Model/Collection.
				if ( ! _.contains( this.parameters, name ) ) {
					return;
				}

				// Don't miss required parameters.
				if ( parameter.required && _.isEmpty( data[ name ] || parameter.default ) ) {
					throw new Error( 'Missing required query parameter \'' + name + '\'.' );
				}

				// Validate optional parameters.
				if ( ! _.isEmpty( data[ name ] ) && ! parameter.pattern.test( data[ name ] ) ) {
					throw new Error( 'Malformed query parameter \'' + name + '\'.' );
				}

				// Only include required parameters and optional parameters if not empty.
				if ( parameter.required ) {
					filtered[ name ] = data[ name ] || parameter.default;
				} else if ( ! _.isEmpty( data[ name ] ) ) {
					filtered[ name ] = data[ name ];
				}

			}, this );

			return filtered;

		},

		/**
		 * Generate a constructed url.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		url : function() {

			return TMDb.root + '/' + TMDb.version + '/' + ( _.isFunction( this.base ) ? this.base() : this.base );
		},

	};

	/**
	 * API Base Model Object.
	 *
	 * @since 3.0.0
	 */
	var BaseModel = wp.api.WPApiBaseModel.extend({

		/**
		 * Models shouldn't be allowed to destroy themselves.
		 *
		 * @since 3.0.0
		 *
		 * @return bool
		 */
		destroy : function() {

			return false;
		},

		/**
		 * Models shouldn't be saved.
		 *
		 * @since 3.0.0
		 *
		 * @return bool
		 */
		save : function() {

			return false;
		},

		/**
		 * Sync.
		 *
		 * @param {string}         method.
		 * @param {Backbone.Model} model.
		 * @param {*}              options.
		 *
		 * @returns {*}.
		 */
		sync : function( method, model, options ) {

			// Filter paramater list.
			_.extend( options || {}, {
				data : this.prepareParameters( options.data || {} ),
			} );

			return wp.api.WPApiBaseModel.prototype.sync.apply( this, arguments );
		},

	});

	/**
	 * API Base Collection Object.
	 *
	 * @since 3.0.0
	 */
	var BaseCollection = wp.api.WPApiBaseCollection.extend({

		/**
		 * Parse xhr response.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		parse : function( response, options ) {

			var models = [];

			if ( ! _.isUndefined( response.results ) ) {

				this.state.totalPages   = parseInt( response.total_pages, 10 ) || 1;
				this.state.totalObjects = parseInt( response.total_results, 10 ) || response.results.length;
				this.state.currentPage  = parseInt( response.page, 10 ) || 1;

				models = response.results;
			}

			return models;
		},

		/**
		 * Reset.
		 *
		 * @param {array} models.
		 * @param {*}     options.
		 *
		 * @returns {array}.
		 */
		reset : function( models, options ) {

			this.state.currentPage  = 1;
			this.state.totalObjects = 0;
			this.state.totalPages   = 1;

			return wp.api.WPApiBaseCollection.prototype.reset.apply( this, arguments );
		},

		/**
		 * Sync.
		 *
		 * @param {string}         method.
		 * @param {Backbone.Model} model.
		 * @param {*}              options.
		 *
		 * @returns {*}.
		 */
		sync : function( method, model, options ) {

			// Filter paramater list.
			_.extend( options || {}, {
				data : this.prepareParameters( options.data || {} ),
			} );

			return wp.api.WPApiBaseCollection.prototype.sync.apply( this, arguments );
		},

	});

	/**
	 * Extend BaseModel and BaseCollection prototypes to support custom URL method
	 * and automatic parameters handling.
	 */
	_.extend( BaseModel.prototype, Base );
	_.extend( BaseCollection.prototype, Base );

	/**
	 * Movie Alternative Titles Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.collections.MovieAlternativeTitles = BaseCollection.extend({

		parameters : [ 'api_key', 'country' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.parent = options.parent;

			BaseCollection.prototype.initialize.call( this, attributes, options );
		},

		/**
		 * Parse xhr response.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		parse : function( response, options ) {

			var models = [];
			if ( ! _.isUndefined( response.titles ) ) {
				_.each( response.titles, function( title ) {
					models.push( new Backbone.Model( title ) );
				} );
			}

			return models;
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'movie/' + this.parent.get( 'id' ) + '/alternative_titles';
		},

	});

	/**
	 * Movie Credits Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.collections.MovieCredits = BaseCollection.extend({

		parameters : [ 'api_key' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.parent = options.parent;

			this.cast = new Backbone.Collection;
			this.crew = new Backbone.Collection;

			this.on( 'update', this.update, this );

			BaseCollection.prototype.initialize.call( this, attributes, options );
		},

		/**
		 * Update collections.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		update : function() {

			this.cast.set( this.filter( function( person ) {
				return person.has( 'character' );
			} ), { merge : false } );

			this.crew.set( this.filter( function( person ) {
				return person.has( 'job' );
			} ), { merge : false } );
		},

		/**
		 * Convert attributes to Model.
		 *
		 * Disambiguate IDs. People can have multiple jobs/characters and using the
		 * same ID causes unwanted merges.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} atts
		 *
		 * @return Backbone.Model
		 */
		modelize : function( atts ) {

			if ( ! _.isObject( atts ) ) {
				return atts;
			}

			if ( _.has( atts, 'id' ) ) {
				atts.tmdb_id = atts.id;
				delete atts.id;
			}

			return new Backbone.Model( atts );
		},

		/**
		 * Parse xhr response.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		parse : function( response, options ) {

			var models = [];

			if ( _.isArray( response ) ) {
					_.each( response, function( credit ) {
						models.push( this.modelize( credit ) );
					}, this );
			} else {
				if ( ! _.isUndefined( response.cast ) ) {
					_.each( response.cast, function( actor ) {
						models.push( this.modelize( actor ) );
					}, this );
				}

				if ( ! _.isUndefined( response.crew ) ) {
					_.each( response.crew, function( person ) {
						models.push( this.modelize( person ) );
					}, this );
				}
			}

			return models;
		},

		/**
		 * Return cast and crew separately.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} options
		 *
		 * @return {object}
		 */
		toJSON : function( options ) {

			return {
				cast : this.cast.toJSON( options ),
				crew : this.crew.toJSON( options ),
			};
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'movie/' + this.parent.get( 'id' ) + '/credits';
		},

	});

	/**
	 * Movie External IDs Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.models.MovieExternalIDs = BaseModel.extend({

		parameters : [ 'api_key' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.parent = options.parent;

			BaseCollection.prototype.initialize.call( this, attributes, options );
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'movie/' + this.parent.get( 'id' ) + '/external_ids';
		},

	});

	/**
	 * Movie Images Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.collections.MovieImages = BaseCollection.extend({

		parameters : [ 'api_key', 'language', 'include_image_language' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.parent = options.parent;

			this.backdrops = new Backbone.Collection;
			this.posters   = new Backbone.Collection;

			this.on( 'update', this.update, this );

			BaseCollection.prototype.initialize.call( this, attributes, options );
		},

		/**
		 * Update collections.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		update : function() {

			this.backdrops.set( this.where( { type : 'backdrop' } ) );

			this.posters.set( this.where( { type : 'poster' } ) );
		},

		/**
		 * Parse xhr response.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		parse : function( response, options ) {

			var models = [];

			if ( ! _.isEmpty( response.backdrops ) ) {
				_.each( response.backdrops, function( backdrop ) {
					models.push( new Backbone.Model( _.extend( backdrop, { type : 'backdrop' } ) ) );
				} );
			}

			if ( ! _.isEmpty( response.posters ) ) {
				_.each( response.posters, function( poster ) {
					models.push( new Backbone.Model( _.extend( poster, { type : 'poster' } ) ) );
				} );
			}

			return models;
		},

		/**
		 * Return backdrops and posters separately.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} options
		 *
		 * @return {object}
		 */
		toJSON : function( options ) {

			return {
				backdrops : this.backdrops.toJSON( options ),
				posters   : this.posters.toJSON( options ),
			};
		},

		/**
		 * Fetch complete movie images.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} options
		 *
		 * @return {xhr}
		 */
		fetchAll : function( options ) {

			var parent = this.parent,
			   options = options || {},
			    before = options.before,
			   success = options.success,
			  complete = options.complete,
			     error = options.error;

			return this.fetch({
				data : options.data,
				beforeSend : function( xhr, options ) {
					parent.trigger( 'fetch:images:start', xhr, options );
					if ( before ) {
						before.apply( this, arguments );
					}
				},
				complete : function( xhr, status ) {
					parent.trigger( 'fetch:images:complete', xhr, status );
					if ( complete ) {
						complete.apply( this, arguments );
					}
				},
				success : function( model, response, options ) {
					parent.trigger( 'fetch:images:success', model, response, xhr );
					if ( success ) {
						success.apply( this, arguments );
					}
				},
				error : function( xhr, status, response ) {
					parent.trigger( 'fetch:images:error', xhr, status, response );
					if ( error ) {
						error.apply( this, arguments );
					}
				},
			});
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'movie/' + this.parent.get( 'id' ) + '/images';
		},

	});

	/**
	 * Movie Release Dates Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.collections.MovieReleaseDates = BaseCollection.extend({

		parameters : [ 'api_key' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.parent = options.parent;

			//this.certifications = new Backbone.Collection;
			//this.releases       = new Backbone.Collection;
			//this.dates          = new Backbone.Collection;

			//this.on( 'update', this.update );

			BaseCollection.prototype.initialize.call( this, attributes, options );
		},

		/**
		 * Update collections.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		//update : function() {},

		/**
		 * Parse xhr response.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		parse : function( response, options ) {

			var models = [];

			if ( ! _.isEmpty( response.results ) ) {
				_.each( response.results, function( release_date ) {
					models.push( new Backbone.Model( release_date ) );
				} );
			}

			return models;
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'movie/' + this.parent.get( 'id' ) + '/release_dates';
		},

	});

	/**
	 * Movie Videos Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.collections.MovieVideos = BaseCollection.extend({

		parameters : [ 'api_key', 'language' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.parent = options.parent;

			BaseCollection.prototype.initialize.call( this, attributes, options );
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'movie/' + this.parent.get( 'id' ) + '/videos';
		},

	});

	/**
	 * 'Movie' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.Movie = TMDb.models.Movie = BaseModel.extend({

		parameters : [ 'api_key', 'append_to_response', 'language', 'include_image_language' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			BaseModel.prototype.initialize.call( this, attributes, options );

			this.ids      = new TMDb.models.MovieExternalIDs( [], { parent : this } );
			this.titles   = new TMDb.collections.MovieAlternativeTitles( [], { parent : this } );
			this.credits  = new TMDb.collections.MovieCredits( [], { parent : this } );
			this.images   = new TMDb.collections.MovieImages( [], { parent : this } );
			this.releases = new TMDb.collections.MovieReleaseDates( [], { parent : this } );
			this.videos   = new TMDb.collections.MovieVideos( [], { parent : this } );
		},

		/**
		 * Override standard Backbone.Model.toJSON() to support additional
		 * collections.
		 *
		 * @since 3.0.0
		 *
		 * @return {object}
		 */
		toJSON : function() {

			return _.extend( _.clone( this.attributes ), {
				external_ids       : this.ids.toJSON() || {},
				alternative_titles : this.titles.toJSON() || {},
				credits            : this.credits.toJSON() || {},
				images             : this.images.toJSON() || {},
				release_dates      : this.releases.toJSON() || {},
				videos             : this.videos.toJSON() || {},
			} );
		},

		/**
		 * Fetch complete movie data.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} options
		 *
		 * @return {xhr}
		 */
		fetchAll : function( options ) {

			var self = this,
			 options = options || {};

			options.data = _.extend( options.data || {}, {
				append_to_response : 'external_ids,alternative_titles,credits,images,release_dates,videos',
			} );

			var before = options.before,
			   success = options.success,
			  complete = options.complete,
			     error = options.error;

			return this.fetch({
				data : options.data,
				beforeSend : function( xhr, options ) {
					self.trigger( 'fetch:start', xhr, options );
					if ( before ) {
						before.apply( this, arguments );
					}
				},
				complete : function( xhr, status ) {
					self.trigger( 'fetch:complete', xhr, status );
					if ( complete ) {
						complete.apply( this, arguments );
					}
				},
				success : function( model, response, options ) {

					if ( ! _.isUndefined( model.get( 'external_ids' ) ) ) {
						self.ids.set( model.get( 'external_ids' ) || [] );
						self.unset( 'external_ids' );
					}

					if ( ! _.isUndefined( model.get( 'alternative_titles' ) ) ) {
						self.titles.set( model.get( 'alternative_titles' ).titles || [] );
						self.unset( 'alternative_titles' );
					}

					if ( ! _.isUndefined( model.get( 'credits' ) ) ) {
						self.credits.set( _.union( model.get( 'credits' ).cast || [], model.get( 'credits' ).crew || [] ), { parse : true } );
						self.unset( 'credits' );
					}

					if ( ! _.isUndefined( model.get( 'release_dates' ) ) ) {
						self.releases.set( model.get( 'release_dates' ).results || [] );
						self.unset( 'release_dates' );
					}

					if ( ! _.isUndefined( model.get( 'videos' ) ) ) {
						self.videos.set( model.get( 'videos' ).results || [] );
						self.unset( 'videos' );
					}

					/**
					 * Images have to be queried separately due to append_to_response
					 * returning paginated results instead of the full images list.
					 */
					self.images.fetchAll();

					self.trigger( 'fetch:success', model, response, options );

					if ( success ) {
						success.apply( this, arguments );
					}
				},
				error : function( xhr, status, response ) {
					self.trigger( 'fetch:error', xhr, status, response );
					if ( error ) {
						error.apply( this, arguments );
					}
				},
			});
		},

		/**
		* Generate endpoing URL.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'movie/' + this.get( 'id' );
		},

	});

	/**
	 * 'Movie' API Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.Movies = TMDb.collections.Movies = BaseCollection.extend({

		base : 'search/movie',

		parameters : [ 'api_key', 'include_adult', 'language', 'page', 'primary_release_year', 'query', 'year' ],

	});

	/**
	 * Person Credits Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.collections.PersonCredits = BaseCollection.extend({

		parameters : [ 'api_key' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.parent = options.parent;

			this.cast = new Backbone.Collection;
			this.crew = new Backbone.Collection;

			this.on( 'update', this.update, this );

			BaseCollection.prototype.initialize.call( this, attributes, options );
		},

		/**
		 * Update collections.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		update : function() {

			this.cast.set( this.filter( function( person ) {
				return person.has( 'character' );
			} ), { merge : false } );

			this.crew.set( this.filter( function( person ) {
				return person.has( 'job' );
			} ), { merge : false } );
		},

		/**
		 * Convert attributes to Model.
		 *
		 * Disambiguate IDs. People can have multiple jobs/characters and using the
		 * same ID causes unwanted merges.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} atts
		 *
		 * @return Backbone.Model
		 */
		modelize : function( atts ) {

			if ( ! _.isObject( atts ) ) {
				return atts;
			}

			if ( _.has( atts, 'id' ) ) {
				atts.tmdb_id = atts.id;
				delete atts.id;
			}

			return new Backbone.Model( atts );
		},

		/**
		 * Parse xhr response.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		parse : function( response, options ) {

			var models = [];

			if ( _.isArray( response ) ) {
					_.each( response, function( credit ) {
						models.push( this.modelize( credit ) );
					}, this );
			} else {
				if ( ! _.isUndefined( response.cast ) ) {
					_.each( response.cast, function( actor ) {
						models.push( this.modelize( actor ) );
					}, this );
				}

				if ( ! _.isUndefined( response.crew ) ) {
					_.each( response.crew, function( person ) {
						models.push( this.modelize( person ) );
					}, this );
				}
			}

			return models;
		},

		/**
		 * Return cast and crew separately.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} options
		 *
		 * @return {object}
		 */
		toJSON : function( options ) {

			return {
				cast : this.cast.toJSON( options ),
				crew : this.crew.toJSON( options ),
			};
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'person/' + this.parent.get( 'id' ) + '/credits';
		},

	});

	/**
	 * Person External IDs Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.models.PersonExternalIDs = BaseModel.extend({

		parameters : [ 'api_key', 'language' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.parent = options.parent;

			BaseCollection.prototype.initialize.call( this, attributes, options );
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'person/' + this.parent.get( 'id' ) + '/external_ids';
		},

	});

	/**
	 * Person Images Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.collections.PersonImages = BaseCollection.extend({

		parameters : [ 'api_key', 'language' ],

		/**
		 * Parse xhr response.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		parse : function( response, options ) {

			var models = [];

			if ( ! _.isEmpty( response.profiles ) ) {
				_.each( response.profiles, function( image ) {
					models.push( new Backbone.Model( image ) );
				} );
			}

			return models;
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'person/' + this.parent.get( 'id' ) + '/images';
		},

	});

	/**
	 * Person Tagged Images Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.collections.PersonTaggedImages = BaseCollection.extend({

		parameters : [ 'api_key', 'language', 'page' ],

		/**
		 * Parse xhr response.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} response
		 * @param {object} options
		 *
		 * @return {array}
		 */
		parse : function( response, options ) {

			var models = [];

			if ( ! _.isEmpty( response.results ) ) {
				_.each( response.results, function( image ) {
					models.push( _.extend( new Backbone.Model( _.omit( image, 'media' ) ), {
						media : new Backbone.Model( image.media ),
					} ) );
				} );
			}

			return models;
		},

		/**
		* Generate a constructed url.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'person/' + this.parent.get( 'id' ) + '/tagged_images';
		},

	});

	/**
	 * 'Person' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.Person = TMDb.models.Person = BaseModel.extend({

		parameters : [ 'api_key', 'append_to_response', 'language' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			BaseModel.prototype.initialize.call( this, attributes, options );

			this.ids          = new TMDb.models.PersonExternalIDs( [], { parent : this } );
			this.credits      = new TMDb.collections.PersonCredits( [], { parent : this } );
			this.images       = new TMDb.collections.PersonImages( [], { parent : this } );
			this.taggedimages = new TMDb.collections.PersonTaggedImages( [], { parent : this } );
		},

		/**
		 * Override standard Backbone.Model.toJSON() to support additional
		 * collections.
		 *
		 * @since 3.0.0
		 *
		 * @return {object}
		 */
		toJSON : function() {

			return _.extend( _.clone( this.attributes ), {
				external_ids : this.ids.toJSON() || {},
				credits      : this.credits.toJSON() || {},
				images       : this.images.toJSON() || {},
				taggedimages : this.taggedimages.toJSON() || {},
			} );
		},

		/**
		 * Fetch complete movie data.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} options
		 *
		 * @return {xhr}
		 */
		fetchAll : function( options ) {

			var self = this,
			 options = options || {};

			options.data = _.extend( options.data || {}, {
				append_to_response : 'combined_credits,external_ids',
			} );

			var before = options.before,
			   success = options.success,
			  complete = options.complete,
			     error = options.error;

			return this.fetch({
				data : options.data,
				beforeSend : function( xhr, options ) {
					self.trigger( 'fetch:start', xhr, options );
					if ( before ) {
						before.apply( this, arguments );
					}
				},
				complete : function( xhr, status ) {
					self.trigger( 'fetch:complete', xhr, status );
					if ( complete ) {
						complete.apply( this, arguments );
					}
				},
				success : function( model, response, options ) {

					if ( ! _.isUndefined( model.get( 'external_ids' ) ) ) {
						self.ids.set( model.get( 'external_ids' ) || [] );
						self.unset( 'external_ids' );
					}

					if ( ! _.isUndefined( model.get( 'combined_credits' ) ) ) {
						self.credits.set( _.union( model.get( 'combined_credits' ).cast || [], model.get( 'combined_credits' ).crew || [] ), { parse : true } );
						self.unset( 'combined_credits' );
					}

					/**
					 * Images (and tagged images) have to be queried separately due to
					 * append_to_response returning paginated results instead of the full
					 * images list.
					 */
					self.images.fetch().done( function( model, status, xhr ) {
						self.trigger( 'fetch:images:success', model, status, xhr );
						if ( ! _.isUndefined( model.images ) && _.has( model.images, 'profiles' ) ) {
							self.images.add( model.images.profiles );
						}
					} ).fail( function( xhr, status, error ) {
						self.trigger( 'fetch:images:error', xhr, status, error );
					} ).always( function( model, status, xhr ) {
						self.trigger( 'fetch:images:stop', model, status, xhr );
					} );

					self.taggedimages.fetch().done( function( model, status, xhr ) {
						self.trigger( 'fetch:taggedimages:success', model, status, xhr );
						if ( ! _.isUndefined( model.tagged_images ) && _.has( model.tagged_images, 'results' ) ) {
							self.taggedimages.add( model.tagged_images.results );
						}
					} ).fail( function( xhr, status, error ) {
						self.trigger( 'fetch:taggedimages:error', xhr, status, error );
					} ).always( function( model, status, xhr ) {
						self.trigger( 'fetch:taggedimages:stop', model, status, xhr );
					} );

					self.trigger( 'fetch:success', model, response, options );

					if ( success ) {
						success.apply( this, arguments );
					}
				},
				error : function( xhr, status, response ) {

					console.log( response, status, xhr );
					self.trigger( 'fetch:error', xhr, status, response );
					if ( error ) {
						error.apply( this, arguments );
					}
				},
			});
		},

		/**
		* Generate endpoing URL.
		*
		* @since 3.0.0
		*
		* @return string
		*/
		base : function() {

			return 'person/' + this.get( 'id' );
		},

	});

	/**
	 * 'Persons' API Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	TMDb.Persons = TMDb.collections.Persons = BaseCollection.extend({

		base : 'search/person',

		parameters : [ 'api_key', 'include_adult', 'language', 'page', 'query' ],

	});

} )( _, Backbone, wp );
