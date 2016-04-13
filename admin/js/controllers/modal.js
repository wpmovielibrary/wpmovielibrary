
wpmoly = window.wpmoly || {};

var Modal = wpmoly.controller.Modal = {};

_.extend( Modal, {

	Modal: Backbone.Model.extend({

		/**
		 * Initialize the Controller.
		 *
		 * @since    3.0
		 *
		 * @param    object    attributes
		 * @param    object    options
		 *
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			var options = options || {};

			this.frame = options.frame;
			this.uploader = this.frame.uploader;

			this.post_id = wpmoly.$( '#post_ID' ).val() || '';
			this.tmdb_id = wpmoly.editor.controller.meta.get( 'tmdb_id' ) || '';

			this.selection = new wp.media.model.Selection( [], {
				multiple: true
			} );

			this.collection = new wpmoly.model.Modal.Images( [], {
				controller: this
			} );

			if ( options.autoload ) {
				this.collection.fetch();
			}

			wpmoly.on( 'modal:images:reload', this.loadImages,   this );
			wpmoly.on( 'modal:images:import', this.importImages, this );

			wpmoly.on( 'editor:backdrop:set-as:done', this.closeModal, this );
			wpmoly.on( 'editor:poster:set-as:done',   this.closeModal, this );

			this.listenTo( this.frame, 'uploader:ready', this.bindUploader );
			this.listenTo( this.frame, 'content:activate:backdrop', function() {
				this.uploader.imageType = 'backdrop';
			}, this );
			this.listenTo( this.frame, 'content:activate:poster', function() {
				this.uploader.imageType = 'poster';
			}, this );
		},

		/**
		 * Bind the Uploader events.
		 *
		 * Mostly used to intercept uploaded images to set them as backdrops/posters
		 * when needed.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining
		 */
		bindUploader: function() {

			// Upload completed
			this.uploader.uploader.uploader.bind( 'UploadComplete', _.bind( function() {

				// Set selected images as existing
				this.selection.each( function( image ) {
					image.set({ existing: true });
				} );

				// Reset selection
				this.selection.reset();

				// Go back to images browser
				this.frame.content.mode( this.uploader.imageType );
			}, this ) );

			this.uploader.uploader.uploader.bind( 'BeforeUpload', _.bind( function( uploader, file ) {
				wpmoly.trigger( 'editor:' + uploader.imageType + ':import:start', uploader, file );
			}, this ) );

			// File successfully uploaded
			this.uploader.uploader.uploader.bind( 'FileUploaded', _.bind( function( uploader, file, response ) {
				wpmoly.trigger( 'editor:' + uploader.imageType + ':import:done', uploader, file, response );
				wpmoly.trigger( 'editor:' + uploader.imageType + ':set-as', [ file.attachment ] );
				wpmoly.trigger( 'editor:' + uploader.imageType + ':set-texts', file.attachment );
			}, this ) );

			return this;
		},

		/**
		 * Load Images.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		loadImages: function() {

			// Reset and reload the collection
			this.collection.reset();
			this.collection.fetch({
				reload: true
			});
		},

		/**
		 * Import selected Images.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		importImages: function() {

			if ( ! this.selection.length ) {
				return;
			}

			wpmoly.trigger( 'editor:' + this.uploader.imageType + ':import', this.selection.models );
		},

		/**
		 * Set selected Images as...
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		setImagesAs: function( type ) {

			if ( ! this.selection.length ) {
				return;
			}

			wpmoly.trigger( 'editor:' + this.uploader.imageType + ':set-as', this.selection.models );
		},

		/**
		 * Close the Modal.
		 * 
		 * Reset selections and close the media frame.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		closeModal: function() {

			this.selection.reset();

			//wp.media.frame.state().get( 'selection' ).reset();
			wp.media.frame.close();
		}

	}),

	State: wp.media.controller.State.extend({

		/**
		 * Initialize the State.
		 *
		 * @since    3.0
		 *
		 * @param    object    options
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			this.props = new Backbone.Collection();

			for ( var tab in options.tabs ) {

				this.props.add( new Backbone.Model({
					id     : tab,
					params : {},
					fetchOnRender : options.tabs[ tab ].fetchOnRender,
				}) );

			}

			this.props.add( new Backbone.Model({
				id        : '_all',
				selection : new Backbone.Collection()
			}) );

			this.props.on( 'change:selection', this.refresh, this );

		},

		/**
		 * Refresh Toolbar.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		refresh: function() {

			this.frame.toolbar.get().refresh();
		}

	})
} );
