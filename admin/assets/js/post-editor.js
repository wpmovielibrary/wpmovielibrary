wpmoly = window.wpmoly || {};

wpmoly.editor = wpmoly.editor || {};

(function( $, _, Backbone ) {

	var Dashboard = wpmoly.dashboard;

	/**
	 * Create a new Block instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} block Block DOM element.
	 *
	 * @return {object} Block instance.
	 */
	var Block = function( block ) {

		var controller, view;
		if ( _.has( block.dataset, 'controller' )
		  && _.has( PostEditor.controller, block.dataset['controller'] )
		  && _.has( PostEditor.view, block.dataset['controller'] ) ) {
			controller = new PostEditor.controller[ block.dataset['controller'] ]({
				post_id : parseInt( wpmoly.$( '#object_ID' ).val() ),
			});
			view = new PostEditor.view[ block.dataset['controller'] ]({
				el         : block,
				controller : controller,
			});
		} else {
			view = new Dashboard.view.Block( { el : block } );
		}

		/**
		 * Block instance.
		 *
		 * Provide a set of useful functions to interact with the block
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var block = {

			id : block.dataset['controller'] || wp.api.utils.camelCaseDashes( block.id.replace( 'editor-', '' ) ),

			view : view,

			controller : controller,

		};

		return block;
	};

	/**
	 * Create a new Sidebar instance.
	 *
	 * @since 1.0.0
	 *
	 * @param {Element} sidebar Sidebar DOM element.
	 *
	 * @return {object} Sidebar instance.
	 */
	var Sidebar = function( sidebar ) {

		var view = new PostEditor.view.Sidebar( { el : sidebar } );

		var blocks = {};
		_.each( document.querySelectorAll( '.editor-block' ), function( block ) {
			// Create Block instance.
			var block = new Block( block );
			// Add Block view to sidebar.
			view.views.add( block.view, { silent : true } );
			// Keep track of Blocks.
			blocks[ block.id ] = block;
		}, this );

		/**
		 * Sidebar instance.
		 *
		 * Provide a set of useful functions to interact with the sidebar
		 * without directly calling controllers and views.
		 *
		 * @since 1.0.0
		 */
		var sidebar = {

			blocks : blocks,

		};

		return sidebar;
	};

	/**
	 * PostEditor wrapper.
	 *
	 * @since 1.0.0
	 */
	var PostEditor = wpmoly.editor.post = {

		/**
		 * List of post editor models.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		model : {},

		/**
		 * List of post editor controllers.
		 *
		 * @since    1.0.0
		 *
		 * @var      object
		 */
		controller : {},

		/**
		 * List of post editor views.
		 *
		 * @since 1.0.0
		 *
		 * @var object
		 */
		view : {},
	};

	/**
	 * PostEditor 'Submit' Block Controller.
	 *
	 * @since 1.0.0
	 */
	PostEditor.controller.SubmitBlock = Backbone.Model.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.post = PostEditor.editor.controller.post;
		},

		/**
		 * Update the post.
		 *
		 * @since 1.0.0
		 *
		 * @return xhr
		 */
		save : function() {

			var post = this.post;

			return post.save( [], {
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
			} );
		},

		/**
		 * Trash the post.
		 *
		 * @since 1.0.0
		 *
		 * @return xhr
		 */
		destroy : function() {

			var post = this.post;

			// Don't delete permanently.
			post.requireForceForDelete = false;
			post.trigger( 'trashing' );

			return post.destroy({
				wait : true,
				success : function( model, response, options ) {
					post.trigger( 'trashed', model, response, options );
				},
				error : function( model, response, options ) {
					post.trigger( 'nottrashed', model, response, options );
				},
			});
		},

		/**
		 * Post updated!
		 *
		 * @since 3.0.0
		 *
		 * @return Return itself to allow chaining.
		 */
		saved : function() {

			wpmoly.success( wpmolyEditorL10n.post_udpated );

			return this;
		},

	});

	/**
	 * PostEditor 'Rename' Block View.
	 *
	 * @since 1.0.0
	 */
	PostEditor.controller.RenameBlock = Backbone.Model.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			this.post = PostEditor.editor.controller.post;
		},

		/**
		 * Save new grid title.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} title New title.
		 *
		 * @return Returns itself to allow chaining.
		 */
		updateTitle : function( title ) {

			this.post.save( { title : title }, { patch : true } );

			return this;
		},

	});

	/**
	 * PostEditor 'Taxonomy' Block View.
	 *
	 * The should be extended on a per-taxonomy basis.
	 *
	 * @since 1.0.0
	 */
	PostEditor.controller.TaxonomyBlock = Backbone.Model.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			this.post = PostEditor.editor.controller.post;

			if ( ! this.term || ! this.terms ) {
				return this;
			}

			this.listenTo( this.terms, 'sync', function() {
				if ( this.terms.hasMore() ) {
					this.terms.more( { remove : false } );
				}
			} );

			this.terms.fetch({
				remove : false,
				data : {
					per_page : 100,
					post     : this.get( 'post_id' ),
					context  : 'edit',
				},
			});

			this.on( 'update:terms', this.assignTerms, this );
		},

		/**
		 * Save assigned terms.
		 *
		 * @since 3.0.0
		 *
		 * @param {mixed} term_ids Term IDs.
		 *
		 * @return Returns itself to allow chaining.
		 */
		assignTerms : function( term_ids ) {

			this.post.save( { [ this.taxonomy ] : term_ids || [] }, { patch : true, silent : true } );

			return this;
		},

		/**
		 * Save terms.
		 *
		 * @since 3.0.0
		 *
		 * @param {array} names List of term names.
		 *
		 * @return Return itself to allow chaining.
		 */
		save : function() {

			if ( this.terms.isEmpty() ) {
				return this.trigger( 'update:terms', [] );
			}

			var self = this;

			/**
			 * Here's some magic. Loop through the terms list to create missing terms;
			 * if terms already exist REST API responses will include exiting term IDs
			 * which we'll store to later update the post. Magic: use deferred objects
			 * to wait until all REST API requests are done AND catch any response be
			 * it error or success. This avoids sending multiple requests to check for
			 * existing terms by creating or updating all terms indistinctly.
			 *
			 * @see https://blog.timsommer.be/using-promises-to-merge-async-jquery-ajax-calls/
			 * @see https://stackoverflow.com/questions/5824615#5825233
			 */
			$.when.apply( this, this.terms.map( function( term ) {
				var dfd = $.Deferred(),
				   term = this.createTerm( term ).complete( dfd.resolve );
				return dfd;
			}, this ) ).done( function() {

				var term_ids = [];

				_.each( arguments, function( response ) {
					if ( _.isArray( response ) && _.isObject( response[0] ) && _.isString( response[1] ) ) {
						var response = response[0];
					}
					if ( response.then ) {
						if ( _.has( response.responseJSON, 'code' ) && 'term_exists' === response.responseJSON.code ) {
							term_ids.push( response.responseJSON.data.term_id );
						} else if ( _.has( response.responseJSON, 'id' ) ) {
							term_ids.push( response.responseJSON.id );
						}
					}
				} );

				self.trigger( 'update:terms', term_ids );
			} );

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

			var terms = [];

			_.each( names || [], function( name ) {
				var existing = this.terms.where( { name : name } );
				if ( existing.length ) {
					terms = _.union( terms, existing );
				} else {
					terms.push( { name : name } );
				}
			}, this );

			this.terms.set( terms );

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

			var model = new this.term( { name : term.get( 'name' ) } );

			return model.save( null, { patch : true } );
		},

	});

	/**
	 * PostEditor 'Categories' Block Controller.
	 *
	 * @since 3.0.0
	 */
	PostEditor.controller.CategoriesBlock = PostEditor.controller.TaxonomyBlock.extend({

		taxonomy : 'categories',

		/**
		 * Initialize the Controller.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			this.post = PostEditor.editor.controller.post;

			this.term  = wp.api.models.Category;
			this.terms = new wp.api.collections.Categories;

			PostEditor.controller.TaxonomyBlock.prototype.initialize.apply( this, arguments );

			this.listenTo( this.post, 'saved', this.save );
		},

	});

	/**
	 * PostEditor 'Tags' Block Controller.
	 *
	 * @since 3.0.0
	 */
	PostEditor.controller.TagsBlock = PostEditor.controller.TaxonomyBlock.extend({

		taxonomy : 'tags',

		/**
		 * Initialize the Controller.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} attributes Controller attributes.
		 * @param {object} options    Controller options.
		 */
		initialize : function( attributes, options ) {

			this.post = PostEditor.editor.controller.post;

			this.term  = wp.api.models.Tag;
			this.terms = new wp.api.collections.Tags;

			PostEditor.controller.TaxonomyBlock.prototype.initialize.apply( this, arguments );

			this.listenTo( this.post, 'saved', this.save );
		},

	});

	PostEditor.controller.ImagesUploader = Backbone.Model.extend({

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

			this.listenTo( this.controller.post, 'change:id', this.setUploaderParameters );
		},

		/**
		 * Default uploader parameters.
		 *
		 * @since 3.0.0
		 *
		 * @return {object}
		 */
		defaultUploadParameters : function() {

			return {
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
		},

		/**
		 * Set uploader parameters.
		 *
		 * @since 3.0.0
		 *
		 * @return {object}
		 */
		setUploaderParameters : function( params ) {

			this.uploadParameters = _.extend( this.defaultUploadParameters(), params || {} );

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

	});

	PostEditor.controller.ImagesEditor = Backbone.Model.extend({

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

			this.attachments = new wp.api.collections.Media;

			this.uploader = new PostEditor.controller.ImagesUploader( [], { controller : this } );

			this.mirrorEvents();
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
		loadAttachments : function( images ) {

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

			/*var attachment = new wp.api.models.Media( { id : attachment.id } ),
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

			this.loadAttachments();*/

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

			/*var self = this,
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

			attachment.save( data, options );*/

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

	});

	/**
	 * PostEditor Block View.
	 *
	 * @since 1.0.0
	 */
	PostEditor.view.Block = Dashboard.view.Block.extend({

		events : function() {
			return _.extend( Dashboard.view.Block.prototype.events.call( this, arguments ) || {}, {
				'click [data-action="edit"]' : 'edit',
			} );
		},

		/**
		 * Toggle edit/preview block modes.
		 *
		 * @since 1.0.0
		 *
		 * @return Return itself to allow chaining.
		 */
		edit : function() {

			this.$el.toggleClass( 'mode-edit mode-preview' );

			return this;
		},

	});

	/**
	 * PostEditor 'Submit' Block View.
	 *
	 * @since 1.0.0
	 */
	PostEditor.view.SubmitBlock = PostEditor.view.Block.extend({

		events : function() {
			return _.extend( {}, _.result( PostEditor.view.Block.prototype, 'events' ), {
				'click [data-action="save"]'          : 'save',
				'click [data-action="trash"]'         : 'trash',
				'click [data-action="dismiss"]'       : 'dismiss',
				'click [data-action="confirm-trash"]' : 'confirmTrash',
			} );
		},

		template : wp.template( 'wpmoly-post-editor-submit' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.listenTo( this.controller.post, 'saving',             this.saving );
			this.listenTo( this.controller.post, 'saved',              this.saved );
			this.listenTo( this.controller.post, 'notsaved',           this.saved );
			this.listenTo( this.controller.post, 'trashing',           this.trashing );
			this.listenTo( this.controller.post, 'trashed nottrashed', this.trashed );
			this.listenTo( this.controller.post, 'sync',               this.render );

			this.render();
		},

		/**
		 * Save the post.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		save : function() {

			this.saving();
			this.controller.save();

			return this;
		},

		/**
		 * Dismiss confirmation request.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		dismiss : function() {

			this.$el.removeClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Delete the post.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		confirmTrash : function() {

			this.controller.destroy();

			this.$el.removeClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Delete the post.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trash : function() {

			this.$el.addClass( 'confirmation-asked' );

			return this;
		},

		/**
		 * Show loading animation.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		saving : function() {

			this.$el.addClass( 'loading' );

			return this;
		},

		/**
		 * Show temporary checked icon.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		saved : function() {

			this.$el.removeClass( 'loading' );

			var $icon = this.$( '.button.save .wpmolicon' );

			$icon.removeClass( 'icon-save' ).addClass( 'icon-yes-alt' );

			_.delay( function() {
				$icon.removeClass( 'icon-yes-alt' ).addClass( 'icon-save' );
			}, 2500 );

			return this;
		},

		/**
		 * Show temporary error icon.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		notsaved : function() {

			this.$el.removeClass( 'loading' );

			var $icon = this.$( '.button.save .wpmolicon' );

			$icon.removeClass( 'icon-save' ).addClass( 'icon-warning' );

			_.delay( function() {
				$icon.removeClass( 'icon-warning' ).addClass( 'icon-save' );
			}, 2500 );

			return this;
		},

		/**
		 * Show trashing animation.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashing : function() {

			this.$el.addClass( 'trashing' );

			return this;
		},

		/**
		 * Hide trashing animation.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		trashed : function() {

			this.$el.removeClass( 'trashing' );

			return this;
		},

		/**
		 * Prepare rendering options.
		 *
		 * @since 1.0.0
		 *
		 * @return {object}
		 */
		prepare : function() {

			var options = _.pick( this.controller.post.toJSON(), [ 'old_edit_link' ] );

			return options;
		},

		/**
		 * Render the View.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		render : function() {

			var options = this.prepare();

			this.$el.html( this.template( options ) );

			return this;
		},

	});

	/**
	 * PostEditor 'Rename' Block View.
	 *
	 * @since 1.0.0
	 */
	PostEditor.view.RenameBlock = PostEditor.view.Block.extend({

		events : function() {
			return _.extend( PostEditor.view.Block.prototype.events.call( this, arguments ) || {}, {
				'input [data-value="new-title"]'     : 'change',
				'click [data-action="update-title"]' : 'update',
			} );
		},

		template : wp.template( 'wpmoly-post-editor-rename' ),

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options Options.
		 */
		initialize : function( options ) {

			this.controller = options.controller;

			this.listenTo( this.controller.post, 'change',  this.render );
		},

		/**
		 * Enable/disable submit button based on title input length.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		change : function() {

			var title = this.$( '[data-value="new-title"]' ).val().trim();

			this.$( '#update-title' ).prop( 'disabled', ( 2 >= title.length ) );

			return this;
		},

		/**
		 * Update title, if title input length reach the minimum.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		update : function() {

			var title = this.$( '[data-value="new-title"]' ).val().trim();

			if ( 3 > title.length ) {
				return this;
			}

			this.$( '#update-title' ).prop( 'disabled', true );
			this.$( '[data-value="new-title"]' ).val( '' );

			this.controller.updateTitle( title );

			return this;
		},

		/**
		 * Prepare rendering options.
		 *
		 * @since 1.0.0
		 *
		 * @return {object}
		 */
		prepare : function() {

			var options = _.pick( this.controller.post.toJSON(), 'title' );

			return options;
		},

	}),

	/**
	 * PostEditor 'TaxonomyBlock' Block View.
	 *
	 * @since 1.0.0
	 */
	PostEditor.view.TaxonomyBlock = PostEditor.view.Block.extend({

		events : function() {
			return _.extend( PostEditor.view.Block.prototype.events.call( this, arguments ) || {}, {
				'click [data-action="edit"]'        : 'edit',
				'click [data-action="reload"]'      : 'reload',
				'click [data-action="clear-terms"]' : 'clear',
			} );
		},

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
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

			this.listenTo( this.controller.terms, 'update reset', this.render );

			this.render();
		},

		/**
		 * Toggle edit mode.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		edit : function() {

			this.$el.toggleClass( 'mode-preview mode-edit' );

			this.render();

			return this;
		},

		/**
		 * Clear terms list.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		clear : function() {

			this.controller.terms.reset();

			this.$el.toggleClass( 'mode-preview mode-edit' );

			return this;
		},

		/**
		 * Prepare rendering options.
		 *
		 * @since 1.0.0
		 *
		 * @return {object}
		 */
		prepare : function() {

			var options = {
				terms : this.controller.terms.toJSON() || {},
			};

			return options;
		},

	}),

	/**
	 * PostEditor 'Categories' Block View.
	 *
	 * @since 3.0.0
	 */
	PostEditor.view.CategoriesBlock = PostEditor.view.TaxonomyBlock.extend({

		events : function() {
			return _.extend( PostEditor.view.TaxonomyBlock.prototype.events.call( this, arguments ) || {}, {
				'change [data-field]' : 'change',
			} );
		},

		template : wp.template( 'wpmoly-post-editor-categories' ),

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

	});

	/**
	 * PostEditor 'Tags' Block View.
	 *
	 * @since 3.0.0
	 */
	PostEditor.view.TagsBlock = PostEditor.view.TaxonomyBlock.extend({

		events : function() {
			return _.extend( PostEditor.view.TaxonomyBlock.prototype.events.call( this, arguments ) || {}, {
				'change [data-field]' : 'change',
			} );
		},

		template : wp.template( 'wpmoly-post-editor-tags' ),

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

	});

	/**
	 * PostEditor Sidebar View.
	 *
	 * @since 1.0.0
	 */
	PostEditor.view.Sidebar = wp.Backbone.View;

	/**
	 * Create sidebar instance.
	 *
	 * @since 1.0.0
	 */
	PostEditor.loadSidebar = function() {

		var sidebar = document.querySelector( '#wpmoly-editor-sidebar' );
		if ( sidebar ) {
			PostEditor.sidebar = new Sidebar( sidebar );
		}
	};

})( jQuery, _, Backbone );
