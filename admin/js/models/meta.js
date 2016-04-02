
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Meta: Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 */
		initialize: function( attributes, options ) {

			this.post_id = options.post_id || '';

			this.on( 'change', this.autosave, this );
		},

		/**
		 * Save the Meta.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model Current model
		 * @param    object    options
		 * 
		 * @return   deferred
		 */
		autosave: function( model, options ) {

			var options = options || {},
			   autosave = options.autosave || true;

			if ( false === options.autosave ) {
				return;
			}

			return this.sync( 'autosave', model, {} );
		},

		/**
		 * Empty the Meta.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    options
		 * 
		 * @return   void
		 */
		reset: function( options ) {

			_.each( this.attributes, function( value, key ) {
				this.set( key, '', { autosave: false } );
			}, this );

			this.save();
		},

		/**
		 * Save the Meta.
		 * 
		 * @since    3.0
		 * 
		 * @return   deferred
		 */
		save: function() {

			return this.sync( 'save', this, {} );
		},

		/**
		 * Backbone.sync() override to allow custom queries.
		 * 
		 * @since    3.0
		 *
		 * @param    string    method Are we reading or is it a regular sync?
		 * @param    object    model Current model
		 * @param    object    options Query options
		 * 
		 * @return   mixed
		 */
		sync: function( method, model, options ) {

			if ( 'autosave' == method ) {

				if ( ! _.size( model.changed ) ) {
					return;
				}

				wpmoly.trigger( 'editor:meta:autosave:start', model.changed, this );

				options = options || {};
				_.extend( options, {
					context: this,
					data: {
						action:  'wpmoly_autosave_' + this.type,
						data:    model.changed,
						post_id: this.post_id
					}
				});

				return wp.ajax.send( options ).done( function() {
					wpmoly.trigger( 'editor:meta:autosave:done', model.changed, this );
				} ).fail( function() {
					wpmoly.trigger( 'editor:meta:autosave:failed', model.changed, this );
				} );

			} else if ( 'save' == method ) {

				wp.ajax.send( 'wpmoly_save_' + this.type, {
					context : this,
					data    : _.extend( options, {
						post_id : this.post_id,
						data    : model.attributes
					} ),
					beforeSend: function() {
						wpmoly.trigger( 'editor:meta:save:start', model.attributes, this );
					},
					success : function( response ) {
						wpmoly.trigger( 'editor:meta:save:done', response, this );
					},
					error   : function( response ) {
						wpmoly.trigger( 'editor:meta:save:failed', response, this );
					},
					complete : function() {
						wpmoly.trigger( 'editor:meta:save:stop', model.attributes, this );
					}
				} );

			} else {
				return Backbone.sync.apply( this, arguments );
			}
		}
	})

});

_.extend( wpmoly.model, {

	Metadata: wpmoly.model.Meta.extend({

		type: 'meta',

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 */
		initialize: function( attributes, options ) {

			wpmoly.model.Meta.prototype.initialize.apply( this, arguments );

			wpmoly.on( 'editor:meta:update', this.update, this );
			wpmoly.on( 'editor:meta:empty',  this.reset,  this );

			wpmoly.on( 'editor:meta:terms-autocomplete', this.updateTaxonomy, this );

			this.on( 'change:director', this.updateCollection, this );
			this.on( 'change:genres',   this.updateGenre,      this );
			this.on( 'change:cast',     this.updateActor,      this );

		},

		/**
		 * Update meta with response from the API.
		 * 
		 * This method should be used instead of set() to avoid errors
		 * on invalid meta formats. After
		 * 
		 * @since    3.0
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 * 
		 * @return   Returns itself to allow chaining
		 */
		update: function( attributes, options ) {

			var attrs = _.extend( {
				tmdb_id              : parseInt( attributes.id ),
				title                : attributes.title,
				original_title       : attributes.original_title,
				tagline              : attributes.tagline,
				overview             : attributes.overview,
				release_date         : attributes.release_date,
				local_release_date   : '',
				runtime              : parseInt( attributes.runtime ),
				production_companies : this.parseCompanies( attributes.production_companies ),
				production_countries : this.parseCountries( attributes.production_countries ),
				spoken_languages     : this.parseLanguages( attributes.spoken_languages ),
				director             : '',
				genres               : this.parseGenres( attributes.genres ),
				producer             : '',
				cast                 : '',
				photography          : '',
				composer             : '',
				author               : '',
				writer               : '',
				certification        : '',
				budget               : parseInt( attributes.budget ),
				revenue              : parseInt( attributes.revenue ),
				imdb_id              : attributes.imdb_id,
				adult                : attributes.adult,
				homepage             : attributes.homepage
			}, this.parseCredits( attributes.credits ) );

			if ( attributes.release_dates.results ) {
				var release = _.where( attributes.release_dates.results, { iso_3166_1: wpmolyL10n._country } ).shift(),
				release_alt = _.where( attributes.release_dates.results, { iso_3166_1: wpmolyL10n._country_alt } ).shift();
				if ( ! release && release_alt ) {
					release = release_alt;
				} else if ( ! release && ! release_alt ) {
					return;
				}

				release = release.release_dates;
				if ( 1 < release.length ) {
					// Type 3 = theatrical release date
					release = _.where( release, { type: 3 } );
				}
				release = release.shift();

				attrs.local_release_date = new Date( release.release_date ).toAPIDateString();
				attrs.certification = release.certification;
			}

			// If we actully changed something, save
			if ( ! _.isEmpty( attrs ) ) {
				this.set( attrs, { autosave: false } );
				this.save();
			}

			return this;
		},

		/**
		 * Parse genres list.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    values
		 * 
		 * @return   string
		 */
		parseCredits: function( credits ) {

			var cast = [], crew, jobs, attrs;
			_.each( credits.cast, function( actor ) {
				cast.push( actor.name );
			}, this );

			crew = { author: [], composer: [], director: [], producer: [], photography : [], writer: [] };
			jobs = _.invert( {
				author      : 'Author',
				composer    : 'Original Music Composer',
				director    : 'Director',
				producer    : 'Producer',
				photography : 'Director of Photography',
				writer      : 'Writer'
			} );

			_.each( credits.crew, function( credit ) {
				var job = jobs[ credit.job ];
				if ( job ) {
					crew[ job ].push( credit.name );
				}
			}, this );

			return _.extend(
				_.mapObject( crew, function( job ) {
					return job.join( ', ' );
				}, this ),
				{
				cast: cast.join( ', ' )
			} );
		},

		/**
		 * Parse genres list.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    values
		 * 
		 * @return   string
		 */
		parseGenres: function( values ) {

			return this.parseData( values );

			/*if ( _.isString( values ) ) {
				return values;
			}

			var data = [];
			_.each( values, function( genre ) {
				genre = _.where( wpmoly.api.genres || {}, { id: genre.id } );
				data.push( genre.name || '' );
			}, this );

			return data.join( ', ' );*/
		},

		/**
		 * Parse production companies list.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    values
		 * 
		 * @return   string
		 */
		parseCompanies: function( values ) {

			return this.parseData( values );
		},

		/**
		 * Parse production countries list.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    values
		 * 
		 * @return   string
		 */
		parseCountries: function( values ) {

			return this.parseData( values );
		},

		/**
		 * Parse spoken languages list.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    values
		 * 
		 * @return   string
		 */
		parseLanguages: function( values ) {

			return this.parseData( values );
		},

		/**
		 * Parse data list.
		 * 
		 * Some data from the API are returned as object and need to be
		 * converted to human-readable strings.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    values
		 * 
		 * @return   string
		 */
		parseData: function( values ) {

			if ( _.isString( values ) ) {
				return values;
			}

			var data = [];
			_.each( values, function( value ) {
				data.push( value.name || '' );
			}, this );

			return data.join( ', ' );
		},

		/**
		 * Update Collection taxonomy terms to match movie director(s).
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * @param    string    value
		 * @param    object    options
		 * 
		 * @return   Returns itself to allow chaining
		 */
		updateCollection: function( model, value, options ) {

			return this.updateTaxonomy( 'collection', model, value, options );
		},

		/**
		 * Update Genre taxonomy terms to match movie genre(s).
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * @param    string    value
		 * @param    object    options
		 * 
		 * @return   Returns itself to allow chaining
		 */
		updateGenre: function( model, value, options ) {

			return this.updateTaxonomy( 'genre', model, value, options );
		},

		/**
		 * Update Actor taxonomy terms to match movie cast.
		 * 
		 * Slice the array if a limit was defined in the settings.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * @param    string    value
		 * @param    object    options
		 * 
		 * @return   Returns itself to allow chaining
		 */
		updateActor: function( model, value, options ) {

			return this.updateTaxonomy( 'actor', model, value, options );
		},

		/**
		 * Update taxonomy's terms to match meta.
		 * 
		 * If 'actor' is passed as taxonomy, slice the array if a limit
		 * was defined in the settings.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    taxonomy
		 * @param    object    model
		 * @param    string    value
		 * @param    object    options
		 * 
		 * @return   Returns itself to allow chaining
		 */
		updateTaxonomy: function( taxonomy, model, value, options ) {

			var value = value.split( tagsBoxL10n.tagDelimiter || ',' );
			    value = value.map( function( v ) {
				return v.trim() || false;
			} );

			value = _.isArray( value ) ? _.compact( value ) : [ value ];

			if ( 'actor' == taxonomy ) {
				var limit = parseInt( wpmoly.search.controller.settings.get( 'actor_limit' ) );
				if ( limit ) {
					value = value.slice( 0, limit );
				}
			}

			wp.ajax.send( 'wpmoly_autosave_' + taxonomy + 's', {
				data: {
					post_id : this.post_id,
					data    : value,
					nonce   : wpmoly.nonce.get( 'autosave-' + taxonomy )
				},
				success: function( response ) {
					// Change taxonomy textarea value
					var $field = wpmoly.$( '#tax-input-' + taxonomy );
					    $field.val( value.join( tagsBoxL10n.tagDelimiter ) );
					// Update tagBox
					tagBox.quickClicks( '#' + taxonomy );
				},
				error: function( response ) {
					wpmoly.error( response );
				}
			} );

			return this;
		}
	}),

	Details: wpmoly.model.Meta.extend({

		type: 'details'
	}),

} );