
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Images: Backbone.Collection.extend({

		type: '',

		/**
		 * Initialize the Collection.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * @param    object    Options
		 * 
		 * @return   null
		 */
		initialize: function( models, options ) {

			this.post_id = options.post_id || '';
		},

		/**
		 * Set an image or multiple images as backdrops/posters.
		 *
		 * @since    3.0
		 *
		 * @param    object    Image Models
		 *
		 * @return   void
		 */
		setAs: function( models ) {

			var controller = wpmoly.editor.controller,
			          type = this.type,
			        images = [];
			_.each( models, function( model ) {

				// Set post_parent for auto imported images
				if ( ! model.get( 'uploadedTo' ) ) {
					model.set({ parent: this.post_id });
					model.save();
				}

				// Setting as backdrop/poster only requires Attachment ID
				images.push( model.get( 'id' ) );
			}, this );

			wp.ajax.send( 'wpmoly_set_' + type + 's', {
				data: {
					images:  images,
					tmdb_id: controller.meta.get( 'tmdb_id' ),
					//nonce:   ''
				},
				beforeSend: function() {
					wpmoly.trigger( 'editor:' + type + ':set-as:start', this );
				},
				success: function( response ) {
					wpmoly.trigger( 'editor:' + type + ':set-as:done', response, this );
				},
				error: function( response ) {
					wpmoly.trigger( 'editor:' + type + ':set-as:failed', response, this );
					wpmoly.error( response );
				},
				complete: function( response ) {
					wpmoly.trigger( 'editor:' + type + ':set-as:stop', this );
				}
			} );
		},

		setTexts: function( attachment ) {

			var meta = wpmoly.editor.controller.meta,
			   texts = {
				alt     : wpmolyL10n[ this.type + 'Alt' ],
				caption : wpmolyL10n[ this.type + 'Caption' ],
			};
			_.map( texts, function( text, slug ) {
				var tags = wpmoly.utils.matchTags( text );
				if ( tags.length ) {
					_.each( tags, function( tag ) {
						if ( _.contains( [ 'year', 'local_year' ], tag.meta ) ) {
							var data = meta.get( tag.meta.replace( 'year', 'release_date' ) ).substr( 0, 4 ) || '';
						} else {
							var data = meta.get( tag.meta ) || '';
						}
						if ( _.isUndefined( tag.n ) ) {
							text = text.replace( tag.tag, data );
						} else {
							data = data.split( ',' ).slice( 0, tag.n ).map( function( str ) {
									return str.trim();
								} ).join(', ');
							text = text.replace( tag.tag, data );
						}
					}, this );
					texts[ slug ] = text;
				}
			}, this );

			attachment.set({
				title       : texts.alt || attachment.get( 'title' ),
				alt         : texts.alt || attachment.get( 'alt' ),
				caption     : texts.caption || attachment.get( 'caption' ),
				description : texts.caption || attachment.get( 'description' )
			});

			attachment.save();
		},

		/**
		 * Trigger Images upload.
		 * 
		 * TODO Use a real, clean upload process
		 * TODO Fix DOM issue with dropzones in panels
		 * 
		 * @since    3.0
		 * 
		 * @param    array    images
		 *
		 * @return   void
		 */
		import: function( images ) {

			if ( ! images ) {
				return;
			}

			var frame = wp.media.frame, Uploader, uploader;
			if ( frame ) {
				uploader  = frame.uploader.uploader.uploader;
			} else {
				Uploader = wp.Uploader;
				Uploader.prototype.init = function() {
					this.uploader.bind( 'BeforeUpload', _.bind( function( uploader, file ) {
						wpmoly.trigger( 'editor:' + uploader.imageType + ':import:start', uploader, file );
					}, this ) );
					this.uploader.bind( 'FileUploaded', _.bind( function( uploader, file, response ) {
						wpmoly.trigger( 'editor:' + uploader.imageType + ':import:done', uploader, file, response );
						wpmoly.trigger( 'editor:' + uploader.imageType + ':set-as', [ file.attachment ] );
						wpmoly.trigger( 'editor:' + uploader.imageType + ':set-texts', file.attachment );
					}, this ) );
				};

				uploader  = new Uploader({
					browser  : '#wpmoly-' + this.type + 's-preview',
					dropzone : '#wpmoly-load-' + this.type
				});

				uploader = uploader.uploader;
			}

			if ( frame ) {
				frame.content.mode( 'upload' );
			}

			uploader.imageType = this.type;

			_.each( images, function( image ) {

				var api = wpmoly.api.configuration,
				setting = wpmoly.search.controller.settings,
				    url = wpmoly.api[ this.type ].getUrl( image.get( 'file_path' ), setting.get( this.type + 's_size' ) ),
				    xhr = new mOxie.XMLHttpRequest();

				xhr.open( 'GET', url );
				xhr.responseType = 'blob';

				xhr.onload = function() {

					this.response.blob_filename = image.get( 'name' );

					uploader.addFile( this.response, image.get( 'name' ) );
				};

				xhr.send();
			}, this );
		},

		/**
		 * Override Backbone.sync()
		 * 
		 * @since    3.0
		 * 
		 * @param    string    Method
		 * @param    object    Model
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		sync: function( method, model, options ) {

			if ( 'read' == method ) {

				_.extend( options || {}, {
					context: this,
					data: {
						action: 'wpmoly_query_' + this.type + 's',
						post_id: this.post_id
					}
				} );

				return wp.ajax.send( options )/*.done( function( data ) {
					
				} ).fail( function( data ) {
					
				} )*/;

			} else {
				return Backbone.sync.apply( this, arguments );
			}
		},

		/**
		 * Parse Ajax response to Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * @param    object    Model
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		parse: function( response, options ) {

			var models = [];
			if ( ! response.length || ! response.items ) {
				return models;
			}

			_.each( response.items, function( item ) {
				models.push( new this.model( item, { parse: true } ) );
			}, this );

			return models;
		}

	})
} );

_.extend( wpmoly.model, {

	Backdrops: wpmoly.model.Images.extend({

		type: 'backdrop',

		model: wpmoly.model.Backdrop,

		/**
		 * Initialize the Collection.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * @param    object    Options
		 * 
		 * @return   null
		 */
		initialize: function( models, options ) {

			wpmoly.model.Images.prototype.initialize.apply( this, arguments );

			wpmoly.on( 'editor:backdrops:refresh',  this.fetch, this );

			wpmoly.on( 'editor:backdrop:set-texts', this.setTexts, this );
			wpmoly.on( 'editor:backdrop:set-as',    this.setAs,    this );
			wpmoly.on( 'editor:backdrop:import',    this.import,   this );
		}
	}),

	Posters: wpmoly.model.Images.extend({

		type: 'poster',

		model: wpmoly.model.Poster,

		/**
		 * Initialize the Collection.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Model
		 * @param    object    Options
		 * 
		 * @return   null
		 */
		initialize: function( models, options ) {

			wpmoly.model.Images.prototype.initialize.apply( this, arguments );

			wpmoly.on( 'editor:posters:refresh',  this.fetch, this );

			wpmoly.on( 'editor:poster:set-texts', this.setTexts, this );
			wpmoly.on( 'editor:poster:set-as',    this.setAs,    this );
			wpmoly.on( 'editor:poster:import',    this.import,   this );
		}
	})
} );