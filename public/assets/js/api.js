
wpmoly = window.wpmoly || {};

/**
 * API Wrapper.
 *
 * @since 1.0.0
 */
var API = wpmoly.api = {

	models : {},

	collections : {},

	root : wpmolyApiSettings.root,

	version : wpmolyApiSettings.versionString,

};


(function( _, Backbone, wp ) {

	'use strict';

	/**
	 * Set a group of meta key/values for a post.
	 *
	 * @param {object} meta The post meta to set, as key/value pairs.
	 */
	var setMetas = function( meta ) {
		var metas = _.clone( this.get( 'meta' ) || {} );
		_.extend( metas, meta );
		this.set( 'meta', metas );
	};

	/**
	 * Set a single meta value for a post, by key.
	 *
	 * @param {string} key   The meta key.
	 * @param {object} value The meta value.
	 */
	var setMeta = function( key, value ) {
		var metas = _.clone( this.get( 'meta' ) || {} );
		metas[ key ] = value;
		this.set( 'meta', metas );
	};

	wp.api.loadPromise.done( function() {
		wp.api.models.Movies.prototype.setMetas      = setMetas;
		wp.api.models.Movies.prototype.setMeta       = setMeta;
		wp.api.models.Persons.prototype.setMetas     = setMetas;
		wp.api.models.Persons.prototype.setMeta      = setMeta;
		wp.api.models.Grids.prototype.setMetas       = setMetas;
		wp.api.models.Grids.prototype.setMeta        = setMeta;
		wp.api.models.Actors.prototype.setMetas      = setMetas;
		wp.api.models.Actors.prototype.setMeta       = setMeta;
		wp.api.models.Collections.prototype.setMetas = setMetas;
		wp.api.models.Collections.prototype.setMeta  = setMeta;
		wp.api.models.Genres.prototype.setMetas      = setMetas;
		wp.api.models.Genres.prototype.setMeta       = setMeta;
	} );

	/**
	 * Base Model object for API Models to extend.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	var BaseModel = wp.api.WPApiBaseModel.extend({

		base : '',

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			wp.api.WPApiBaseModel.prototype.initialize.call( this, attributes, options );

			if ( ! _.isEmpty( options.mixins ) ) {
				_.each( options.mixins, function( mixin, slug ) {
					this[ slug ] = new mixing( [], { parent : this } );
				}, this );
			}
		},

		/**
		 * Retrieve nonce.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		nonce : function() {

			return wpApiSettings.nonce || '';
		},

		/**
		 * Generate a constructed url.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		url : function() {

			return API.root + API.version + this.base;
		},

	});

	/**
	 * Base Collection object for API Models to extend.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	var BaseCollection = wp.api.WPApiBaseCollection.extend({

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			wp.api.WPApiBaseCollection.prototype.initialize.call( this, attributes, options );

			if ( ! _.isEmpty( options.mixins ) ) {
				_.each( options.mixins, function( mixin, slug ) {
					this[ slug ] = new mixing( [], { parent : this } );
				}, this );
			}
		},

		/**
		 * Retrieve nonce.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		nonce : function() {

			return wpApiSettings.nonce || '';
		},

		/**
		 * Generate a constructed url.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		url : function() {

			return API.root + API.version + this.base;
		},

	});

	/**
	 * 'ActorsCount' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.models.ActorsCount = BaseModel.extend( { base : 'actors/count' } );

	/**
	 * 'CollectionsCount' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.models.CollectionsCount = BaseModel.extend( { base : 'collections/count' } );

	/**
	 * 'GenresCount' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.models.GenresCount = BaseModel.extend( { base : 'genres/count' } );

	/**
	 * 'GridsCount' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.models.GridsCount = BaseModel.extend( { base : 'grids/count' } );

	/**
	 * 'MoviesCount' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.models.MoviesCount = BaseModel.extend( { base : 'movies/count' } );

	/**
	 * 'PersonsCount' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.models.PersonsCount = BaseModel.extend( { base : 'persons/count' } );

	/**
	 * 'SettingsSchema' API Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.collections.SettingsSchema = BaseCollection.extend( { base : 'settings/schema' } );

	/**
	 * 'Actor' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.Actor = API.models.Actor = BaseModel.extend({

		base : 'actor',

		methods : [ 'GET', 'PUT', 'POST' ],

		/**
		* Generate a constructed url.
		*
		* @since 1.0.0
		*
		* @return string
		*/
		url : function() {

			return API.root + API.version + this.base + ( this.has( 'id' ) ? '/' + this.get( 'id' ) : '' );
		},
	});

	/**
	 * 'Collection' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
 	API.Collection = API.models.Collection = BaseModel.extend({

		base : 'collection',

		methods : [ 'GET', 'PUT', 'POST' ],

		/**
		* Generate a constructed url.
		*
		* @since 1.0.0
		*
		* @return string
		*/
		url : function() {

			return API.root + API.version + this.base + ( this.has( 'id' ) ? '/' + this.get( 'id' ) : '' );
		},

	});

	 /**
 	 * 'Genre' API Model.
 	 *
 	 * @since 3.0.0
 	 *
 	 * @param {object} attributes
 	 * @param {object} options
 	 */
 	API.Genre = API.models.Genre = BaseModel.extend({

		base : 'genre',

		methods : [ 'GET', 'PUT', 'POST' ],

		/**
		* Generate a constructed url.
		*
		* @since 1.0.0
		*
		* @return string
		*/
		url : function() {

			return API.root + API.version + this.base + ( this.has( 'id' ) ? '/' + this.get( 'id' ) : '' );
		},

	});

	/**
	 * 'Grid' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.Grid = API.models.Grid = BaseModel.extend({

		/**
		 * List of supported methods.
		 *
		 * @since 3.0.0
		 *
		 * @var {array}
		 */
		methods : [ 'GET', 'PUT', 'POST' ],

		defaults : function() {
			return {
				type              : '',
				mode              : 'grid',
				theme             : 'default',
				preset            : 'custom',
				columns           : 5,
				rows              : 4,
				list_columns      : 3,
				list_rows         : 8,
				enable_pagination : true,
				settings_control  : true,
				custom_letter     : true,
				custom_order      : true,
			};
		},

		/**
		* Generate a constructed url.
		*
		* @since 1.0.0
		*
		* @return string
		*/
		url : function() {

			return API.root + API.version + 'grid/' + this.get( 'id' );
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
	API.Movie = API.models.Movie = BaseModel.extend({

		defaults : {
			adult                : '',
			author               : '',
			budget               : 0,
			cast                 : '',
			certification        : '',
			composer             : '',
			director             : '',
			genres               : '',
			homepage             : '',
			imdb_id              : '',
			local_release_date   : '',
			original_title       : '',
			overview             : '',
			photography          : '',
			producer             : '',
			production_companies : '',
			production_countries : '',
			release_date         : '',
			revenue              : 0,
			runtime              : 0,
			spoken_languages     : '',
			tagline              : '',
			title                : '',
			tmdb_id              : 0,
			writer               : '',
			// Details.
			format               : '',
			language             : '',
			media                : '',
			rating               : '',
			status               : '',
			subtitles            : '',
		},

		/**
		 * List of supported methods.
		 *
		 * @since 3.0.0
		 *
		 * @var {array}
		 */
		methods : [ 'GET', 'POST', 'PUT', 'PATCH' ],

		/**
		* Generate a constructed url.
		*
		* @since 1.0.0
		*
		* @return string
		*/
		url : function() {

			return API.root + API.version + 'movie/' + this.get( 'id' );
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
	API.Person = API.models.Person = BaseModel.extend({

		defaults : {
			adult          : '',
			also_known_as  : '',
			biography      : '',
			birthday       : '',
			deathday       : '',
			homepage       : '',
			imdb_id        : '',
			name           : '',
			place_of_birth : '',
			tmdb_id        : 0,
		},

		/**
		 * List of supported methods.
		 *
		 * @since 3.0.0
		 *
		 * @var {array}
		 */
		methods : [ 'GET', 'POST', 'PUT', 'PATCH' ],

		/**
		* Generate a constructed url.
		*
		* @since 1.0.0
		*
		* @return string
		*/
		url : function() {

			return API.root + API.version + 'person/' + this.get( 'id' );
		},

	});

	/**
	 * 'Settings' API Model.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.Settings = API.models.Settings = BaseModel.extend({

		base : 'settings',

		/**
		 * List of supported methods.
		 *
		 * @since 3.0.0
		 *
		 * @var {array}
		 */
		methods : [ 'GET', 'POST', 'PUT', 'PATCH' ],

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.schema = new API.collections.SettingsSchema( [], { parent : this } );

			BaseModel.prototype.initialize.call( this, attributes, options );
		},

		/**
		 * Set nonce header before every Backbone sync.
		 *
		 * @since 3.0.0
		 *
		 * @param {string} method.
		 * @param {Backbone.Model} model.
		 * @param {{beforeSend}, *} options.
		 *
		 * @returns {*}.
		 */
		sync : function( method, model, options ) {

			if ( 'create' === method && model.has( 'validate' ) ) {
				model.url = model.url() + '/validate';
				model.set( model.get( 'validate' ) );
				delete model.attributes.validate;
			}

			return BaseModel.prototype.sync.apply( this, arguments );
		},

	});

	/**
	 * 'Actors' API Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.Actors = API.collections.Actors = BaseCollection.extend({

		base : 'actors',

		model : API.Actor,

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Collection models.
		 * @param {object} options    Collection options.
		 */
		initialize : function( models, options ) {

			var options = options || {};

			this.counts = new API.models.ActorsCount( [], { parent : this } );

			BaseCollection.prototype.initialize.call( this, models, options );
		},

	});

	/**
	 * 'Collections' API Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.Collections = API.collections.Collections = BaseCollection.extend({

		base : 'collections',

		model : API.Collection,

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Collection models.
		 * @param {object} options    Collection options.
		 */
		initialize : function( models, options ) {

			var options = options || {};

			this.counts = new API.models.CollectionsCount( [], { parent : this } );

			BaseCollection.prototype.initialize.call( this, models, options );
		},

	});

	/**
	 * 'Genres' API Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.Genres = API.collections.Genres = BaseCollection.extend({

		base : 'genres',

		model : API.Genre,

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Collection models.
		 * @param {object} options    Collection options.
		 */
		initialize : function( models, options ) {

			var options = options || {};

			this.counts = new API.models.GenresCount( [], { parent : this } );

			BaseCollection.prototype.initialize.call( this, models, options );
		},

	});

	/**
	 * 'Grids' API Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.Grids = API.collections.Grids = BaseCollection.extend({

		base : 'grids',

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Collection models.
		 * @param {object} options    Collection options.
		 */
		initialize : function( models, options ) {

			var options = options || {};

			this.counts = new API.models.GridsCount( [], { parent : this } );

			BaseCollection.prototype.initialize.call( this, models, options );
		},

	});

	/**
	 * 'Movies' API Collection.
	 *
	 * @since 3.0.0
	 *
	 * @param {object} attributes
	 * @param {object} options
	 */
	API.Movies = API.collections.Movies = BaseCollection.extend({

		base : 'movies',

		model : API.Movie,

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.counts = new API.models.MoviesCount( [], { parent : this } );

			BaseCollection.prototype.initialize.call( this, attributes, options );
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
	API.Persons = API.collections.Persons = BaseCollection.extend({

		base : 'persons',

		model : API.Person,

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Model attributes.
		 * @param {object} options    Model options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.counts = new API.models.PersonsCount( [], { parent : this } );

			BaseCollection.prototype.initialize.call( this, attributes, options );
		},

	});

})( _, Backbone, wp );
