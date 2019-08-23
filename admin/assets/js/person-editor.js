wpmoly = window.wpmoly || {};

wpmoly.editor = wpmoly.editor || {};

(function( $, _, Backbone ) {

	var Dashboard = wpmoly.dashboard;

	/**
	 * Create a new Person Editor instance.
	 *
	 * Set snapshot, node and post models, editor controller and editor view. Load
	 * post first, then snapshot, then node. Editor view is rendered when post is
	 * fetched, editor regions are set when node is fetched.
	 *
	 * @since 3.0.0
	 *
	 * @param {Element} editor Person Editor DOM element.
	 *
	 * @return {object} Person instance.
	 */
	var Editor = function( editor ) {

		var post_id = parseInt( wpmoly.$( '#object_ID' ).val() ),
		    $parent = wpmoly.$( '#wpmoly-editor' );

		// Show loading animation.
		$parent.addClass( 'loading' );

		// Set editor models.
		var post = new wp.api.models.Persons( { id : post_id } ),
		    node = new wpmoly.api.models.Person( { id : post_id } ),
		settings = new wp.api.models.Settings;

		settings.fetch();

		// Snapshot and Meta shortcut.
		var meta = new PersonEditor.model.Meta( [], {
			defaults : node.defaults,
			model    : post,
		} ),
		snapshot = node.snapshot = new PersonEditor.model.Snapshot( [], { model : post } );

		// Set editor controllers.
		var search = new PersonEditor.controller.Search,
		  pictures = new PersonEditor.controller.PicturesEditor( [], { post : post, meta : meta, node : node } ),
		 backdrops = new PersonEditor.controller.BackdropsEditor( [], { post : post, meta : meta, node : node } );

		// Set editor controller.
		var controller = new PersonEditor.controller.Editor( [], {
			settings  : settings,
			search    : search,
			snapshot  : snapshot,
			meta      : meta,
			post      : post,
			node      : node,
			pictures   : pictures,
			backdrops : backdrops,
		} );

		pictures.controller  = controller;
		backdrops.controller = controller;

		// Set editor view.
		var view = new PersonEditor.view.Editor({
			el         : editor,
			controller : controller,
		});

		view.$el.addClass( 'post-editor person-editor' );

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
			controller.backdrops.loadAttachments();
			controller.pictures.loadAttachments();
			// Render the editor.
			view.render();
		} );

		// Load node.
		post.on( 'sync', function() {
			node.fetch( { data : { context : 'embed' } } );
		} );

		// Switch to search mode if no snapshot is found.
		post.once( 'sync', function() {
			if ( _.isEmpty( post.get( 'snapshot' ) ) ) {
				controller.set( { mode : 'download' } );
				search.set( { query : ( post.get( 'title' ) || {} ).raw || '' } );
			}
		} );

		// Load person.
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

			pictures : pictures,

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

	var PersonEditor = wpmoly.editor.person = _.extend( PostEditor, {

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

						if ( _.isArray( value ) ) {
							value = value.join( ', ' );
						} else if ( _.isObject( value ) ) {
							value = JSON.stringify( value );
						}

						// Apply defaults, if any.
						if ( _.has( this.defaults, key ) && ( ( _.isNumber( value ) && ! value ) || ( ! _.isNumber( value ) && _.isEmpty( value ) ) ) ) {
							value = this.defaults[ key ];
						}

						attributes.meta[ wpmolyApiSettings.person_prefix + key ] = value;
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

					attributes.meta[ wpmolyApiSettings.person_prefix + 'snapshot' ] = JSON.stringify( snapshot );

					return this.model.save( attributes, _.extend( { patch : true }, options || {} ) );
				},

			}),

		} ),

		controller : _.extend( PostEditor.controller, {

			/**
			 * PersonEditor 'Submit' Block Controller.
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
						window.location.href = PersonEditor.editor.controller.post.get( 'old_edit_link' );
					} else if ( 'view' === mode ) {
						window.location.href = PersonEditor.editor.controller.post.get( 'link' );
					} else {
						PersonEditor.editor.controller.set( { mode : mode } );
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

					return PersonEditor.editor.controller.get( 'mode' );
				},

				/**
				 * Update the node.
				 *
				 * @since 3.0.0
				 *
				 * @return xhr
				 */
				save : function() {

					return PersonEditor.editor.controller.save();
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
				 * @param {int} person_id Person ID.
				 *
				 * @return boolean|object Returns false if invalid ID, itself otherwise.
				 */
				import : function( person_id ) {

					var self = this,
					  result = this.results.get( person_id ),
					  person = new TMDb.Person( { id : person_id } );

					this.result.set( result.toJSON() );

					person.on( 'fetch:start', function( xhr, options ) {
						self.trigger( 'import:start', xhr, options );
					}, this );

					person.on( 'fetch:complete', function( xhr, status ) {
						self.trigger( 'import:stop', xhr, status );
					}, this );

					person.on( 'fetch:success', function( response, status, xhr ) {
						self.trigger( 'import:done', person.toJSON(), status, xhr );
					}, this );

					person.on( 'fetch:error', function( xhr, status, response ) {
						self.trigger( 'import:failed', xhr, status, response );
					}, this );

					person.on( 'fetch:images:success', function( model, status, xhr ) {
						self.trigger( 'import:images:done', model, status, xhr );
					}, this );

					person.on( 'fetch:taggedimages:success', function( model, status, xhr ) {
						self.trigger( 'import:taggedimages:done', model, status, xhr );
					}, this );

					person.fetchAll( { data : this._prepareQueryData() } );

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
					} else if ( /^(name):(.*)$/i.test( query ) ) {
						this.set( 'query', query.replace( 'name:', '' ) );
						return this._searchByName();
					} else {
						return this._searchByName();
					}

					return this;
				},

				/**
				 * Search persons based on title.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				_searchByName : function() {

					var self = this;
					if ( _.isUndefined( self.results ) ) {
						self.results = new TMDb.Persons;
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
				 * Search persons based on TMDb or IMDb ID.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				_searchById : function() {

					return this.import( this.get( 'query' ) );
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

					/*if ( ! _.isEmpty( settings.year ) ) {
						data.year = settings.year;
					}

					if ( ! _.isEmpty( settings.primary_year ) ) {
						data.primary_release_year = settings.primary_year;
					}*/

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

			ImagesUploader : Backbone.Model.extend({

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

					this.controller = options.controller;

					this.setUploaderParameters();

					this.listenTo( this.controller.node, 'change:id', this.setUploaderParameters );
				},

				/**
				 * Set uploader parameters.
				 *
				 * @since 3.0.0
				 *
				 * @return {object}
				 */
				setUploaderParameters : function() {

					this.uploadParameters = {
						params : {
							post_id : this.controller.post.get( 'id' ),
						},
						plupload : {
							multi_selection : false,
							filters : [{
								extensions : 'jpg,jpeg,png,gif',
							}],
						},
					};

					return this.uploadParameters;
				},

				/**
					* Create new PlUpload uploader instance.
					*
					* @since 3.0.0
					*
					* @param {object} options Uploader options.
					*
					* @return Returns itself to allow chaining.
					*/
				setUploader : function( options ) {

					var self = this,
					uploader = new wp.Uploader( _.extend( options || {}, this.uploadParameters ) );

					$( uploader ).on( 'uploader:ready', _.bind( this.bindEvents, this ) );

					this.uploader = uploader;

					return this;
				},

				/**
					* Bind uploader events.
					*
					* @since    1.0.0
					*/
				bindEvents : function() {

					var uploader = this.uploader.uploader;

					uploader.bind( 'FilesAdded',   _.bind( this.uploadStart, this ) );
					uploader.bind( 'UploadFile',   _.bind( this.uploadFile, this ) );
					uploader.bind( 'FileUploaded', _.bind( this.FileUploaded, this ) );
					uploader.bind( 'Error',        _.bind( this.uploadError, this ) );

				},

				/**
					* Load file from URL.
					*
					* @since 3.0.0
					*
					* @param {object} model File model.
					*
					* @return   Returns itself to allow chaining.
					*/
				loadFile : function( model ) {

					var self = this,
					   image = new mOxie.Image(),
					uploader = this.uploader.uploader;

					this.trigger( 'download:start', model );

					/**
						* Download progress.
						*
						* @since 3.0.0
						*
						* @param {object} event
						*/
					image.onprogress = function( event ) {

						var progress = event.loaded / event.total * 100;

						self.trigger( 'download:progress', model, progress );
					};

					/**
						* Upload downloaded file.
						*
						* @since 3.0.0
						*
						* @param {object} event
						*/
					image.onload = function( event ) {

						var data = image.getAsDataURL(),
						    file = new mOxie.File( null, data );

						file.name = s.trim( model.get( 'file_path' ), '/' );

						uploader.addFile( file );
					};

					/**
						* Download end.
						*
						* @since 3.0.0
						*
						* @param {object} event
						*/
					image.onloadend = function( event ) {

						self.trigger( 'download:stop', model );
					};

					image.load( 'https://image.tmdb.org/t/p/original' + model.get( 'file_path' ) );

					return this;

				},

				/**
					* Trigger an event when a file is added to the upload queue.
					*
					* @since    1.0.0
					*
					* @param    object    uploader Uploader instance.
					* @param    object    files Currently queued files.
					*
					* @return   Returns itself to allow chaining.
					*/
				uploadStart : function( uploader, files ) {

					this.trigger( 'upload:start', uploader, files );

					return this;
				},

				/**
					* Upload started, bind event on percent change.
					*
					* @since    1.0.0
					*
					* @param    object    uploader Uploader instance.
					* @param    object    file Currently uploaded file.
					*
					* @return   Returns itself to allow chaining.
					*/
				uploadFile : function( uploader, file ) {

					file.attachment.on( 'change:percent', function( model, value ) {
						this.trigger( 'upload:progress', uploader, file );
					}, this );

					return this;
				},

				/**
					* Upload done, update controllers.
					*
					* @since    1.0.0
					*
					* @param    object    uploader Uploader instance.
					* @param    object    file Currently uploaded file.
					*
					* @return   Returns itself to allow chaining.
					*/
				FileUploaded : function( uploader, file ) {

					if ( _.isUndefined( file.attachment ) || _.isUndefined( file.attachment.get( 'meta' ) ) ) {

						wpmoly.error( wpmolyEditorL10n.upload_fail );

						this.trigger( 'upload:failed', uploader, files );

						return this;
					}

					wpmoly.success( s.sprintf( wpmolyEditorL10n.upload_success, ( file.attachment.get( 'editLink' ) || '#' ) ) );

					this.trigger( 'upload:stop', uploader, file );

					this.controller.addAttachment( file.attachment );

					return this;
				},

				/**
					* Trigger an event when an upload failed.
					*
					* @since    1.0.0
					*
					* @param    object    uploader Uploader instance.
					* @param    object    file Currently uploaded file.
					*
					* @return   Returns itself to allow chaining.
					*/
				uploadError : function( uploader, file ) {

					var errorCode = Math.abs( file.code ).toString(),
					errorMap = {
						'4'   : pluploadL10n.upload_failed,
						'601' : pluploadL10n.invalid_filetype,
						'700' : pluploadL10n.not_an_image,
						'702' : pluploadL10n.image_dimensions_exceeded,
						'100' : pluploadL10n.upload_failed,
						'300' : pluploadL10n.io_error,
						'200' : pluploadL10n.http_error,
						'400' : pluploadL10n.security_error,
						'600' : function( file ) {
							return pluploadL10n.file_exceeds_size_limit.replace( '%s', file.name );
						},
					};

					wpmoly.error( errorMap[ errorCode ] );

					this.resetUploader( uploader, file );

					return this;
				},

				/**
					* Set uploader mode to default and remove any queued file.
					*
					* @since    1.0.0
					*
					* @param    object    uploader Uploader instance.
					* @param    object    files Currently uploaded files.
					*
					* @return   Returns itself to allow chaining.
					*/
				resetUploader : function( uploader, files ) {

					_.each( uploader.files, function( file ) {
						_.delay( _.bind( uploader.removeFile, uploader ), 50, file );
					} );

					return this;
				},

			}),

			ImagesEditor : Backbone.Model.extend({

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

					this.post = options.post;
					this.node = options.node;
					this.meta = options.meta;

					this.attachments = new wp.api.collections.Media;
				},

				/**
				 * Mirror uploader events.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				mirrorEvents : function() {

					if ( ! this.uploader ) {
						return this;
					}

					this.uploader.on( 'all', function() {
						Backbone.Model.prototype.trigger.apply( this, arguments );
					}, this );
				},

				/**
					* Load attachments.
					*
					* @since 3.0.0
					*
					* @return Returns itself to allow chaining.
					*/
				loadAttachments : function() {

					var images = _.pluck( this.node.get( this.types ), 'id' );
					if ( ! _.isEmpty( images ) ) {
						this.attachments.fetch({
							data : {
								include : images,
							},
						});
					}

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

					attributes.meta[ this.type + '_related_tmdb_id' ] = this.meta.get( 'tmdb_id' );

					if ( _.has( this.controller, 'settings' ) ) {
						var meta = this.meta,
						 replace = function( s ) {
							// replace year.
							s = s.replace( '{year}', meta.has( 'release_date' ) ? ( new Date( meta.get( 'release_date' ) ).getFullYear() ) || '' : '' );
							// Sorcery. Replace {property} with node.get( property ), if any.
							return s.replace( /{([a-z_]+)}/gi, function( m, p, d ) { return meta.has( p ) ? meta.get( p ) || m : m; } );
						};

						attributes.title       = replace( this.controller.settings.get( wpmolyApiSettings.option_prefix + 'person_' + this.type + '_title' ) || '' );
						attributes.caption     = replace( this.controller.settings.get( wpmolyApiSettings.option_prefix + 'person_' + this.type + '_description' ) || '' );
						attributes.alt_text    = replace( this.controller.settings.get( wpmolyApiSettings.option_prefix + 'person_' + this.type + '_title' ) || '' );
						attributes.description = replace( this.controller.settings.get( wpmolyApiSettings.option_prefix + 'person_' + this.type + '_description' ) || '' );
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
					* Download Image.
					*
					* @since 3.0.0
					*
					* @param {object} model Image model.
					*
					* @return Returns itself to allow chaining.
					*/
				downloadImage : function( model ) {

					this.uploader.loadFile( model );

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
					* Remove Image.
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

				/**
					* Edit Image.
					*
					* @since 3.0.0
					*
					* @param {object} attachment Attachment model.
					*
					* @return Returns itself to allow chaining.
					*/
				editImage : function( attachment ) {

					var attachment = new wp.media.model.Attachment( { id : attachment.get( 'id' ) } );

					if ( ! this.frame ) {
						this.frame = wp.media({
							uploader : false,
							modal    : true,
							frame    : 'manage',
						});
					}

					var self = this;
					attachment.fetch().done(function() {
						self.frame.openEditAttachmentModal( attachment );
					});

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
					this.meta      = options.meta;
					this.post      = options.post;
					this.node      = options.node;
					this.pictures  = options.pictures;
					this.backdrops = options.backdrops;

					//this.listenTo( this.post,     'saved', this.saveNode );
					this.listenTo( this.post,     'error', this.error );
					this.listenTo( this.node,     'error', this.error );
					this.listenTo( this.snapshot, 'error', this.error );
					this.listenTo( this.search,   'error', this.error );
					this.listenTo( this.search,   'import:failed', this.error );

					this.listenTo( this.search, 'import:start', function() {
						this.search.reset();
					} );

					this.listenTo( this.search, 'import:done', function( attributes ) {
						this.snapshot.save( attributes || [] );
						this.set( { mode : 'preview' } );
					} );

					this.listenTo( this.search, 'import:taggedimages:done', function( response ) {
						if ( _.has( response, 'results' ) ) {
							this.snapshot.save( _.extend( this.snapshot.toJSON(), {
								taggedimages : response.results || [],
							} ) );
						}
						if ( true === this.settings.get( wpmolyApiSettings.option_prefix + 'auto_import_person_backdrops' ) ) {
							this.backdrops.importBackdrop();
						}
					} );

					this.listenTo( this.search, 'import:images:done', function( response ) {
						if ( _.has( response, 'profiles' ) ) {
							this.snapshot.save( _.extend( this.snapshot.toJSON(), {
								images : response.profiles || [],
							} ) );
						}
						if ( true === this.settings.get( wpmolyApiSettings.option_prefix + 'auto_import_person_pictures' ) ) {
							this.pictures.importPicture();
						}
					} );

					this.listenTo( this.snapshot, 'change',      this.updateMeta );
					this.listenTo( this.post,     'change:meta', this.updateMeta );
				},

				/**
				 * Update snapshot by querying fresh data from the API.
				 *
				 * @since 3.0.0
				 *
				 * @return Returns itself to allow chaining.
				 */
				updateSnapshot : function() {

					var person = new TMDb.Person( { id : this.snapshot.get( 'id' ) } ),
					 snapshot = this.snapshot;

					person.on( 'fetch:success', function() {
						snapshot.save( person.toJSON() );
						wpmoly.success( wpmolyEditorL10n.snapshot_updated );
					}, this );

					person.on( 'fetch:error', function( xhr, status, response ) {
						wpmoly.error( xhr, { destroy : false } );
					}, this );

					person.fetchAll();

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

					_.each( this.post.getMetas() || [], function( value, key ) {

						var key = key.replace( wpmolyApiSettings.person_prefix, '' );

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

					this.meta.set( meta );

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
				setFeaturedPicture : function( id ) {

					this.post.save( { featured_media : id }, { patch : true } );

					return this;
				},

				/**
				 * Import a new movie.
				 *
				 * @since 3.0.0
				 *
				 * @param {int} tmdb_id
				 *
				 * @return Returns itself to allow chaining.
				 */
				importMovie : function( tmdb_id ) {

					var credits = this.snapshot.get( 'credits' ),
					      movie = _.findWhere( _.union( credits.cast, credits.crew ), { tmdb_id : parseInt( tmdb_id ) } );

					if ( movie ) {
						movie = new wp.api.models.Movies({
							title : movie.title,
							meta : {
								tmdb_id : movie.tmdb_id,
							},
						});
						movie.save();
					}

					return this;
				},

				/**
				 * Save person node and post data.
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
						},
						success : function( model, response, options ) {
							post.trigger( 'saved', model, response, options );
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
			 * PersonEditor 'Submit' Block View.
			 *
			 * @since 1.0.0
			 */
			MenuBlock : PostEditor.view.SubmitBlock.extend({

				template : wp.template( 'wpmoly-person-editor-submit' ),

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

					var options = _.pick( this.controller.post.toJSON(), [ 'old_edit_link' ] );

					_.extend( options, {
						mode : this.controller.getMode(),
					} );

					return options;
				},

			}),

			/**
			 * PersonEditor 'SearchResults' Block View.
			 *
			 * @since 3.0.0
			 */
			SearchResults : wpmoly.Backbone.View.extend({

				className : 'search-results',

				template : wp.template( 'wpmoly-person-editor-search-results' ),

				events : {
					'click [data-action="jump-to"]'  : 'browse',
					'click [data-action="first"]'    : 'first',
					'click [data-action="last"]'     : 'last',
					'click [data-action="next"]'     : 'next',
					'click [data-action="previous"]' : 'previous',
					'click [data-action="import"]'   : 'import',
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
				 * Import selected person.
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

					this.controller.import( person_id );

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

				template : wp.template( 'wpmoly-person-editor-search-loading' ),

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
				prepare : function( person_id ) {

					var movies = this.controller.result.get( 'known_for' ) || [],
					  backdrop;

					if ( movies.length ) {
						backdrop = _.first( movies ).backdrop_path;
					}

					var options = {
						backdrop : backdrop,
					};

					return options;
				},

			}),

			/**
			 * PersonEditor 'SearchForm' Block View.
			 *
			 * @since 3.0.0
			 */
			SearchForm : wpmoly.Backbone.View.extend({

				className : 'search-form',

				template : wp.template( 'wpmoly-person-editor-search-form' ),

				events : {
					'keypress [data-value="search-query"]'  : 'toggleSearch',
					'keypress [data-value="search-query"]'  : 'toggleSearch',
					'click [data-action="advanced-search"]' : 'advancedSearch',
					'click [data-action="search"]'          : 'startSearch',
					'click [data-action="reset"]'           : 'resetSearch',
					'change [data-setting]'                 : 'changeSetting',
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
			 * PersonEditor 'Search' Block View.
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
						this.downloading = new PersonEditor.view.SearchLoading( options );
					}

					if ( ! this.form ) {
						this.form = new PersonEditor.view.SearchForm( options );
					}

					if ( ! this.results ) {
						this.results = new PersonEditor.view.SearchResults( options );
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
			 * PersonEditor 'Snapshot' Block View.
			 *
			 * @since 3.0.0
			 */
			Snapshot : wpmoly.Backbone.View.extend({

				className : 'snapshot-section-inner mode-summary',

				template : wp.template( 'wpmoly-person-editor-snapshot' ),

				events : {
					'click [data-action="update-snapshot"]' : 'update',
					'click [data-tab]'                      : 'changeTab',
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
			 * PersonEditor Generic 'Editor' Block View.
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

	_.extend( PersonEditor.controller, {

		BackdropsUploader : PersonEditor.controller.ImagesUploader.extend({

			type : 'backdrop',

			types : 'backdrops',

			/**
			 * Set default uploader parameters.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setUploaderParameters : function() {

				var params = PersonEditor.controller.ImagesUploader.prototype.setUploaderParameters.call( this, arguments );

				this.uploadParameters = _.extend( params || {}, {
					post_data : {
						meta_input : {
							_wpmoly_backdrop_related_tmdb_id : this.controller.meta.get( 'tmdb_id' ),
						},
					},
				} );

				return this.uploadParameters;
			},

		}),

		BackdropsEditor : PersonEditor.controller.ImagesEditor.extend({

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

				PersonEditor.controller.ImagesEditor.prototype.initialize.apply( this, arguments );

				this.uploader = new PersonEditor.controller.BackdropsUploader( [], { controller : this } );

				this.mirrorEvents();
			},

			/**
			 * Import default backdrop.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			importBackdrop : function() {

				var model = new Backbone.Model({
					file_path : this.controller.snapshot.get( 'backdrop_path' ),
				});

				this.uploader.loadFile( model );

				return this;
			},
		}),

		PicturesUploader : PersonEditor.controller.ImagesUploader.extend({

			type : 'picture',

			types : 'pictures',

			/**
			 * Set default uploader parameters.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			setUploaderParameters : function() {

				var params = PersonEditor.controller.ImagesUploader.prototype.setUploaderParameters.call( this, arguments );

				this.uploadParameters = _.extend( params || {}, {
					post_data : {
						meta_input : {
							_wpmoly_picture_related_tmdb_id : this.controller.meta.get( 'tmdb_id' ),
						},
					},
				} );

				return this.uploadParameters;
			},

		}),

		PicturesEditor : PersonEditor.controller.ImagesEditor.extend({

			type : 'picture',

			types : 'pictures',

			/**
				* Initialize the Controller.
				*
				* @since 3.0.0
				*
				* @param {object} attributes Controller attributes.
				* @param {object} options    Controller options.
				*/
			initialize : function( attributes, options ) {

				PersonEditor.controller.ImagesEditor.prototype.initialize.apply( this, arguments );

				this.uploader = new PersonEditor.controller.PicturesUploader( [], { controller : this } );

				this.mirrorEvents();
			},

			/**
			 * Import default picture.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			 importPicture : function() {

				var model = new Backbone.Model({
 					file_path : this.controller.snapshot.get( 'profile_path' ),
 				});

				this.uploader.once( 'upload:stop', this.setFeaturedPicture, this );

 				this.uploader.loadFile( model );

 				return this;
 			},

			/**
			 * Set picture as featured image.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} uploader
			 * @param {object} file
			 *
			 * @return Returns itself to allow chaining.
			 */
			setFeaturedPicture : function( uploader, file ) {

				this.controller.setFeaturedPicture( file.attachment.get( 'id' ) );

				return this;
			},

		}),

	} );

	_.extend( PersonEditor.view, {

		Image : wpmoly.Backbone.View.extend({

			events : {
				'click [data-action="download"]' : 'downloadImage',
				'click [data-action="remove"]'   : 'removeImage',
				'click [data-action="set-as"]'   : 'setAsImage',
				'click [data-action="open"]'     : 'openImage',
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

			events : {
				'change [data-filter="language"]' : 'filterByLanguage',
				'change [data-filter="ratio"]'    : 'filterByRatio',
				'change [data-filter="size"]'     : 'filterBySize',
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

		ImagesEditorUploader : PersonEditor.view.EditorSection.extend({

			events : {
				'click [data-action="select-image"]' : 'select',
			},

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} options Options.
			 */
			initialize : function( options ) {

				PersonEditor.view.EditorSection.prototype.initialize.call( this, arguments );

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
		 * PersonEditor 'Images Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		ImagesEditor : PersonEditor.view.EditorSection.extend({

			events : function() {
				return _.extend( {}, _.result( PersonEditor.view.EditorSection.prototype, 'events' ), {
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

				PersonEditor.view.EditorSection.prototype.initialize.apply( this, arguments );

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

	_.extend( PersonEditor.view, {

		/**
		 * Backdrop Editor single backdrop View.
		 *
		 * @since 3.0.0
		 */
		Backdrop : PersonEditor.view.Image.extend({

			className : 'image backdrop',

			template : wp.template( 'wpmoly-person-backdrops-editor-item' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'backdrop';
				this.types     = 'backdrops';

				PersonEditor.view.Image.prototype.initialize.apply( this, arguments );
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
		BackdropsEditorMenu : PersonEditor.view.ImagesEditorMenu.extend({

			className : 'images-editor-menu backdrops-editor-menu',

			template : wp.template( 'wpmoly-person-backdrops-editor-menu' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'backdrop';
				this.types     = 'backdrops';

				PersonEditor.view.ImagesEditorMenu.prototype.initialize.apply( this, arguments );
			},

		}),

		/**
		 * Backdrop Editor Content View.
		 *
		 * @since 3.0.0
		 */
		BackdropsEditorContent : PersonEditor.view.ImagesEditorContent.extend({

			className : 'images-editor-content backdrops-editor-content',

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				var options = options || {};

				this.controller = options.controller;

				this.type      = 'backdrop';
				this.types     = 'backdrops';
				this.imageView = PersonEditor.view.Backdrop;

				this.listenTo( this.controller.snapshot, 'change:taggedimages', this.render );
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

				var images = this.controller.snapshot.get( 'taggedimages' ) || {};
				_.each( images || [], function( image ) {
					var view = new this.imageView({
						model      : new Backbone.Model( image ),
						controller : this.controller,
					});
					this.views.add( view );
				}, this );

				return this;
			},

		}),

		/**
		 * Backdrop Editor Uploader View.
		 *
		 * @since 3.0.0
		 */
		BackdropsEditorUploader : PersonEditor.view.ImagesEditorUploader.extend({

			className : 'images-uploader backdrops-uploader',

			template : wp.template( 'wpmoly-person-backdrops-editor-uploader' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'backdrop';
				this.types     = 'backdrops';
				this.imageView = PersonEditor.view.Backdrop;

				PersonEditor.view.ImagesEditorUploader.prototype.initialize.apply( this, arguments );
			},

		}),

		/**
		 * Picture Editor single picture View.
		 *
		 * @since 3.0.0
		 */
		Picture : PersonEditor.view.Image.extend({

			className : 'image picture',

			template : wp.template( 'wpmoly-person-pictures-editor-item' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'picture';
				this.types     = 'pictures';

				PersonEditor.view.Image.prototype.initialize.apply( this, arguments );
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
		 * Picture Editor Menu View.
		 *
		 * @since 3.0.0
		 */
		PicturesEditorMenu : PersonEditor.view.ImagesEditorMenu.extend({

			className : 'images-editor-menu pictures-editor-menu',

			template : wp.template( 'wpmoly-person-pictures-editor-menu' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'picture';
				this.types     = 'pictures';

				PersonEditor.view.ImagesEditorMenu.prototype.initialize.apply( this, arguments );
			},

		}),

		/**
		 * Picture Editor Content View.
		 *
		 * @since 3.0.0
		 */
		PicturesEditorContent : PersonEditor.view.ImagesEditorContent.extend({

			className : 'images-editor-content pictures-editor-content',

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'picture';
				this.types     = 'pictures';
				this.imageView = PersonEditor.view.Picture;

				PersonEditor.view.ImagesEditorContent.prototype.initialize.apply( this, arguments );
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
				_.each( images || [], function( image ) {
					var view = new this.imageView({
						model      : new Backbone.Model( image ),
						controller : this.controller,
					});
					this.views.add( view );
				}, this );

				return this;
			},

		}),

		/**
		 * Picture Editor Uploader View.
		 *
		 * @since 3.0.0
		 */
		PicturesEditorUploader : PersonEditor.view.ImagesEditorUploader.extend({

			className : 'images-uploader pictures-uploader',

			template : wp.template( 'wpmoly-person-pictures-editor-uploader' ),

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				this.type      = 'picture';
				this.types     = 'pictures';
				this.imageView = PersonEditor.view.Picture;

				PersonEditor.view.ImagesEditorUploader.prototype.initialize.apply( this, arguments );
			},

		}),

		/**
		 * Credits Editor item.
		 *
		 * @since 3.0.0
		 */
		CreditsEditorItem : wpmoly.Backbone.View.extend({

			className : 'credit-item',

			template : wp.template( 'wpmoly-person-credits-editor-item' ),

			events : function() {
				return _.extend( {}, _.result( PersonEditor.view.EditorSection.prototype, 'events' ), {
					'click [data-action="import"]' : 'import',
				} );
			},

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				var options = options || {};

				this.controller = options.controller;
			},

			/**
			 * Import item.
			 *
			 * @since 3.0.0
			 *
			 * @param {object} JS 'click' Event.
			 *
			 * @return Returns itself to allow chaining.
			 */
			import : function( event ) {

				var $target = this.$( event.currentTarget ),
				    tmdb_id = $target.attr( 'data-tmdb-id' );

				this.controller.importMovie( tmdb_id );

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

				var options = this.model.toJSON();

				options.year = ! _.isEmpty( options.release_date ) ? ( new Date( options.release_date ) ).getFullYear() : '';

				return options;
			},

		}),

		/**
		 * Credits Editor Section.
		 *
		 * @since 3.0.0
		 */
		CreditsEditorSection : wpmoly.Backbone.View.extend({

			/**
			 * Initialize the View.
			 *
			 * @since 3.0.0
			 */
			initialize : function( options ) {

				var options = options || {};

				this.controller = options.controller;

				this.listenTo( this.controller.node,     'sync',   this.render );
				this.listenTo( this.controller.meta,     'change', this.render );
				this.listenTo( this.controller.snapshot, 'change', this.render );
			},

			/**
			 * Render the view.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			render : function() {

				wpmoly.Backbone.View.prototype.render.apply( this, arguments );

				this.addItems();

				return this;
			}
		}),

	} );

	_.extend( PersonEditor.view, {

		/**
		 * PersonEditor 'Meta Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		MetaEditor : PersonEditor.view.EditorSection.extend({

			className : 'wpmoly person-headbox mode-preview',

			template : wp.template( 'wpmoly-person-meta-editor' ),

			events : function() {
				return _.extend( {}, _.result( PersonEditor.view.EditorSection.prototype, 'events' ), {
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

				PersonEditor.view.EditorSection.prototype.initialize.apply( this, arguments );

				var options = options || {};

				this.controller = options.controller;

				this.listenTo( this.controller.pictures,  'download:start', this.loadingPicture );
				this.listenTo( this.controller.pictures,  'upload:stop',    this.loadedPicture );
				this.listenTo( this.controller.backdrops, 'upload:stop',    this.loadingBackdrop );
				this.listenTo( this.controller.backdrops, 'download:start', this.loadedBackdrop );

				this.listenTo( this.controller.node,     'sync',   this.render );
				this.listenTo( this.controller.meta,     'change', this.render );
				this.listenTo( this.controller.snapshot, 'change', this.render );
			},

			/**
			 * Show picture loading animation.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			loadingPicture : function() {

				this.$( '.headbox-picture' ).addClass( 'loading' );

				return this;
			},

			/**
			 * Hide picture loading animation.
			 *
			 * @since 3.0.0
			 *
			 * @return Returns itself to allow chaining.
			 */
			loadedPicture : function() {

				this.$( '.headbox-picture' ).removeClass( 'loading' );

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
					} else if ( ! _.isNull( option.meta ) && option.meta !== this.controller.post.getMeta( wpmolyApiSettings.person_prefix + key ) ) {
						option.status = 'changed';
						option.node = option.meta;
					} else if ( ! _.isNull( option.meta ) && option.meta !== this.controller.post.getMeta( wpmolyApiSettings.person_prefix + key ) ) {
						option.status = 'saved';
					}

					options[ key ] = option;
				}, this );

				if ( _.has( node.picture || {}, 'id' ) && _.isNumber( node.picture.id ) ) {
					options.picture = node.picture.sizes.large.url;
				} else if ( _.has( snapshot, 'images' ) ) {
					var picture = _.first( snapshot.images );
					options.picture = ! _.isUndefined( picture ) ? 'https://image.tmdb.org/t/p/h632' + picture.file_path : node.picture.sizes.large.url;
				}

				if ( _.has( node.backdrop || {}, 'id' ) && _.isNumber( node.backdrop.id ) ) {
					options.backdrop = node.backdrop.sizes.large.url;
				} else if ( _.has( snapshot, 'taggedimages' ) ) {
					var backdrop = _.first( snapshot.taggedimages );
					options.backdrop = ! _.isUndefined( backdrop ) ? 'https://image.tmdb.org/t/p/original' + backdrop.file_path : node.backdrop.sizes.large.url;
				}

				// Use taxonomy.
				options.department = {
					meta     : '',
					node     : '',
					snapshot : '',
					default  : '',
					status   : '',
				};

				return options;
			},

		}),

		CastEditor : PersonEditor.view.CreditsEditorSection.extend({

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

				// Ignore TV Shows for now.
				var credits = this.controller.snapshot.get( 'credits' ) || {},
						credits = _.sortBy( _.where( credits.cast || {}, { media_type : 'movie' } ), 'release_date' ).reverse();

				_.each( credits || [], function( credit ) {
					var view = new PersonEditor.view.CreditsEditorItem({
						model      : new Backbone.Model( credit ),
						controller : this.controller,
					});
					this.views.add( view );
				}, this );

				return this;
			},

		}),

		CrewEditor : PersonEditor.view.CreditsEditorSection.extend({

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

				// Ignore TV Shows for now.
				var credits = this.controller.snapshot.get( 'credits' ) || {},
						credits = _.sortBy( _.where( credits.crew || {}, { media_type : 'movie' } ), 'release_date' ).reverse();

				_.each( credits || [], function( credit ) {
					var view = new PersonEditor.view.CreditsEditorItem({
						model      : new Backbone.Model( credit ),
						controller : this.controller,
					});
					this.views.add( view );
				}, this );

				return this;
			},

		}),

		/**
		 * PersonEditor 'Credits Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		CreditsEditor : PersonEditor.view.EditorSection.extend({

			className : 'wpmoly person-credits mode-preview',

			template : wp.template( 'wpmoly-person-credits-editor' ),

			events : function() {
				return _.extend( {}, _.result( PersonEditor.view.EditorSection.prototype, 'events' ), {
					'change [data-field]'            : 'change',
					//'click [data-action="download"]' : 'import',
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

				PersonEditor.view.EditorSection.prototype.initialize.apply( this, arguments );

				var options = options || {};

				this.controller = options.controller;

				this.setRegions();
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

				if ( ! this.cast ) {
					this.cast = new PersonEditor.view.CastEditor( options );
				}

				if ( ! this.crew ) {
					this.crew = new PersonEditor.view.CrewEditor( options );
				}

				this.views.set( '.cast-items', this.cast );
				this.views.set( '.crew-items', this.crew );

				return this;
			},

		}),

		/**
		 * PersonEditor 'Pictures Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		PicturesEditor : PersonEditor.view.ImagesEditor.extend({

			className : 'wpmoly person-images person-pictures mode-download',

			template : wp.template( 'wpmoly-person-pictures-editor' ),

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
					this.menu = new PersonEditor.view.PicturesEditorMenu( options );
				}

				if ( ! this.content ) {
					this.content = new PersonEditor.view.PicturesEditorContent( options );
				}

				if ( ! this.uploader ) {
					this.uploader = new PersonEditor.view.PicturesEditorUploader( options );
				}

				this.views.set( '.editor-content-download .panel.left',  this.menu );
				this.views.set( '.editor-content-download .panel.right', this.content );
				this.views.add( '.editor-content-upload .panel.right',   this.uploader );

				return this;
			},

		}),

		/**
		 * PersonEditor 'Backdrops Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		BackdropsEditor : PersonEditor.view.ImagesEditor.extend({

			className : 'wpmoly person-images person-backdrops mode-download',

			template : wp.template( 'wpmoly-person-backdrops-editor' ),

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
					this.menu = new PersonEditor.view.BackdropsEditorMenu( options );
				}

				if ( ! this.content ) {
					this.content = new PersonEditor.view.BackdropsEditorContent( options );
				}

				if ( ! this.uploader ) {
					this.uploader = new PersonEditor.view.BackdropsEditorUploader( options );
				}

				this.views.set( '.editor-content-download .panel.left',  this.menu );
				this.views.set( '.editor-content-download .panel.right', this.content );
				this.views.add( '.editor-content-upload .panel.right',   this.uploader );

				return this;
			},

		}),

		/**
		 * PersonEditor 'Preview' Block View.
		 *
		 * @since 3.0.0
		 */
		Preview : wpmoly.Backbone.View.extend({

			className : 'editor-preview',

			template : wp.template( 'wpmoly-person-editor-preview' ),

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
					this.meta = new PersonEditor.view.MetaEditor( options );
				}

				if ( ! this.credits ) {
					this.credits = new PersonEditor.view.CreditsEditor( options );
				}

				if ( ! this.backdrops ) {
					this.backdrops = new PersonEditor.view.BackdropsEditor( options );
				}

				if ( ! this.pictures ) {
					this.pictures = new PersonEditor.view.PicturesEditor( options );
				}

				this.views.set( '#wpmoly-person-meta',      this.meta );
				this.views.set( '#wpmoly-person-credits',   this.credits );
				this.views.set( '#wpmoly-person-backdrops', this.backdrops );
				this.views.set( '#wpmoly-person-pictures',  this.pictures );

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
		 * PersonEditor 'Editor' Block View.
		 *
		 * @since 3.0.0
		 */
		Editor : wpmoly.Backbone.View.extend({

			className : '',

			template : wp.template( 'wpmoly-person-editor' ),

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
					this.preview = new PersonEditor.view.Preview( options );
				}

				if ( ! this.search ) {
					this.search = new PersonEditor.view.Search( { controller : this.controller.search } );
				}

				if ( ! this.snapshot ) {
					this.snapshot = new PersonEditor.view.Snapshot( options );
				}

				this.views.set( '#wpmoly-person-preview',  this.preview );
				this.views.set( '#wpmoly-person-search',   this.search );
				this.views.set( '#wpmoly-person-snapshot', this.snapshot );

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
	 * Create person editor instance.
	 *
	 * @since 3.0.0
	 */
	PersonEditor.loadEditor = function() {

		var editor = document.querySelector( '#wpmoly-person-editor' );
		if ( editor ) {
			PersonEditor.editor = new Editor( editor );
		}
	};

	/**
	 * Run Forrest, run!
	 *
	 * @since 3.0.0
	 */
	PersonEditor.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( function() {
			PersonEditor.loadEditor();
			PostEditor.loadSidebar();
		} );

		return PersonEditor;
	};

})( jQuery, _, Backbone );

wpmoly.runners['person'] = wpmoly.editor.person;
