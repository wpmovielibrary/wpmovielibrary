
wpmoly = window.wpmoly || {};

_.extend( wpmoly.controller, {

	Editor: Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			// Metadata block
			this.meta      = new wpmoly.model.Metadata( {}, { post_id: options.post_id } );
			this.details   = new wpmoly.model.Details( {}, { post_id: options.post_id } );
			this.backdrops = new wpmoly.model.Backdrops( [], { post_id: options.post_id } );
			this.posters   = new wpmoly.model.Posters( [], { post_id: options.post_id } );

			// Bind events
			this.mirror();
			this.bindEvents();
		},

		/**
		 * Mirror the Meta and Details changes to the Model.
		 * 
		 * Allows the View to listen for changes on the Movie Model
		 * instead of having to listen for both Movie, Meta and Details
		 * Models.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		mirror: function() {

			this.meta.on( 'all', function( event, arguments ) {
				wpmoly.trigger( 'editor:meta:' + event, arguments );
			}, this );

			this.details.on( 'all', function( event, arguments ) {
				wpmoly.trigger( 'editor:details:' + event, arguments );
			}, this );
		},

		/**
		 * Bind controller events.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		bindEvents: function() {

			wpmoly.on( 'editor:backdrop:import:open', function() {
				return this.openMediaModal( 'movie-images', 'backdrop', { type: 'backdrop' } );
			}, this );

			wpmoly.on( 'editor:poster:import:open', function() {
				return this.openMediaModal( 'movie-images', 'poster', { type: 'poster' } );
			}, this );

			wpmoly.on( 'editor:backdrop:upload:open', function() {
				return this.openMediaModal( 'insert', 'upload', { type: 'backdrop' } );
			}, this );

			wpmoly.on( 'editor:poster:upload:open', function() {
				return this.openMediaModal( 'insert', 'upload', { type: 'poster' } );
			}, this );

			wpmoly.on( 'editor:backdrop:set-as:done', function() {
				return this.backdrops.reset();
			}, this );

			wpmoly.on( 'editor:poster:set-as:done', function() {
				return this.posters.reset();
			}, this );

			wpmoly.on( 'editor:images:autoimport',    this.autoImportImages,    this );
			wpmoly.on( 'editor:backdrops:autoimport', this.autoImportBackdrops, this );
			wpmoly.on( 'editor:posters:autoimport',   this.autoImportPosters,   this );

			wpmoly.on( 'editor:image:featured',    this.setFeatured, this );
			wpmoly.on( 'editor:image:remove',      this.removeImage, this );

			wpmoly.on( 'editor:meta:save:done', function() {
				wpmoly.trigger( 'status:stop', {
					icon    : 'icon-yes',
					message : wpmolyL10n.metaSaved
				} );
			}, this );
		},

		/**
		 * Open wp.media Modal.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    Frame state ID
		 * @param    string    Frame content mode ID
		 * @param    object    options
		 * 
		 * @return   void
		 */
		openMediaModal: function( state, mode, options ) {

			var options = options || {};

			wp.media.editor.open();
			wp.media.frame.setState( state )
			wp.media.frame.content.mode( mode );

			if ( options.type ) {
				wp.media.frame.imagesController.mode = options.type;
			}
		},

		autoImportImages: function( images ) {

			
		},

		autoImportBackdrops: function( backdrops ) {

			
		},

		autoImportPosters: function( posters ) {

			
		},

		/**
		 * Set poster as featured image.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event
		 * 
		 * @return   void
		 */
		setFeatured: function( model ) {

			var id = model.get( 'id' ) || 0,
			currentImage = wp.media.featuredImage.get();

			if ( id && id != currentImage ) {

				wpmoly.trigger( 'status:start', {
					icon    : 'icon-images',
					effect  : 'bounce',
					message : wpmolyL10n.settingFeatured
				} );

				wp.media.featuredImage.set( id );

				wpmoly.trigger( 'status:stop', {
					icon    : 'icon-images',
					effect  : 'bounce',
					message : wpmolyL10n.featuredImageSet
				} );
			}
		},

		/**
		 * Unset an image from backdrops/posters.
		 * 
		 * TODO: move to wpmoly.model.Image
		 * 
		 * @since    3.0
		 * 
		 * @param    object    Image Model
		 * @param    string    Image type
		 * @param    object    Image Collection
		 * 
		 * @return   Returns itself to allow chaining
		 */
		removeImage: function( model, type, collection ) {

			wp.ajax.send( 'wpmoly_remove_' + type, {
				data: {
					post_id: model.get( 'id' ),
					tmdb_id: this.meta.get( 'tmdb_id' ),
					//nonce  : ''
				},
				success: function( response ) {
					collection.remove( model );
				},
				error: function( response ) {
					wpmoly.error( response );
				}
			} );
		}
	})
} );
