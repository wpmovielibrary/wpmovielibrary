
$ = jQuery;

wpml = {};

	wpml.editor = {};
	wpml.importer = {};
	wpml.queue = {};
	wpml.movies = {};
	wpml.media = {};
	wpml.landing = {};
	wpml.settings = {};

		/**
		 * Movies Post Editor page's Metadata part
		 * 
		 * @since    1.0.0
		 */
		wpml.editor.meta = {};
			wpml.editor.meta.search = function( post_id, title, caller ) {};
			wpml.editor.meta.select = function( movies, message ) {};
			wpml.editor.meta.get = function( tmdb_id ) {};
			wpml.editor.meta.set = function( data ) {};
			wpml.editor.meta.prefill_title = function( title ) {};

		/**
		 * Movies Post Editor page's Details part
		 * 
		 * @since    1.0.0
		 */
		wpml.editor.details = {};
			wpml.editor.details = {};
				wpml.editor.details.init = function() {};
				wpml.editor.details.save = function() {};
				wpml.editor.details.inline_editor = function( type, link ) {};
				wpml.editor.details.inline_edit = function( type, link ) {};

			wpml.editor.details.media = {};
				wpml.editor.details.init = function() {};
				wpml.editor.details.show = function() {};
				wpml.editor.details.update = function() {};
				wpml.editor.details.revert = function() {};

			wpml.editor.details.rating = {};
				wpml.editor.details.init = function() {};
				wpml.editor.details.show = function() {};
				wpml.editor.details.update = function() {};
				wpml.editor.details.revert = function() {};

		/**
		 * Handles the Imported part of the Importer: search for movies'
		 * metadata, select movies in lists, set data fields.
		 * 
		 * @since    1.0.0
		 */
		wpml.importer.meta = {};
			wpml.importer.meta.do = function( action ) {};
			wpml.importer.meta.search = function( post_id ) {};
			wpml.importer.meta.select = function( movies, message ) {};
			wpml.importer.meta.get = function( post_id, tmdb_id ) {};
			wpml.importer.meta.set = function( data ) {};

		/**
		 * Handles the Import part of the Importer: import lists of movies
		 * or delete movies
		 * 
		 * @since    1.0.0
		 */
		wpml.importer.movies = {};
			wpml.importer.movies.delete = function() {};
			wpml.importer.movies.import = function() {};

		/**
		 * Handles the Importer view alterations like AJAX nav or counter
		 * updates.
		 * 
		 * @since    1.0.0
		 */
		wpml.importer.view = {};
			wpml.importer.view.reload = function() {};
			wpml.importer.view.navigate = function() {};
			wpml.importer.view.paginate = function() {};
			wpml.importer.view.update_count = function() {};

		/**
		 * TODO
		 * 
		 * @since    1.0.0
		 */
		wpml.queue.movies = {};
			wpml.queue.movies.add = function() {};
			wpml.queue.movies.remove = function() {};
			wpml.queue.movies.prepare = function() {};
			wpml.queue.movies.import = function() {};

		/**
		 * TODO
		 * 
		 * @since    1.0.0
		 */
		wpml.queue.utils = {};
			wpml.queue.utils.toggle_button = function() {};
			wpml.queue.utils.toggle_inputs = function() {};

		/**
		 * Movie Images handling
		 * 
		 * @since    1.0.0
		 */
		wpml.media.images = {};
			wpml.media.images.init = function() {};
			wpml.media.images.frame = function() {};
			wpml.media.images.select = function() {};
			wpml.media.images.upload = function() {};
			wpml.media.images.close = function() {};

		/**
		 * Movie Posters handling
		 * 
		 * @since    1.0.0
		 */
		wpml.media.posters = {};
			wpml.media.posters.init = function() {};
			wpml.media.posters.frame = function() {};
			wpml.media.posters.select = function() {};
			wpml.media.posters.set_featured = function() {};
			wpml.media.posters.close = function() {};

		/**
		 * TODO
		 * 
		 * @since    1.0.0
		 */
		wpml.landing.modal = {};
			wpml.landing.modal.open = function() {};
			wpml.landing.modal.close = function() {};
			wpml.landing.modal.resize = function() {};
			wpml.landing.modal.update = function() {};

		/**
		 * TODO
		 * 
		 * @since    1.0.0
		 */
		wpml.landing.dashboard = {};
			wpml.landing.dashboard.handle_widget = function() {};

		/**
		 * Settings & Import Panels
		 * 
		 * @since    1.0.0
		 */
		wpml.settings.panels = {};
			wpml.settings.panels.init = function() {};
			wpml.settings.panels.switch_panel = function() {};

		/**
		 * Settings Metadata sorting part
		 * 
		 * @since    1.0.0
		 */
		wpml.settings.sortable = {};
			wpml.settings.sortable.init = function() {};
			wpml.settings.sortable.update_item_style = function() {};
			wpml.settings.sortable.update_item = function() {};

		/**
		 * 
		 * 
		 * @since    1.0.0
		 */
		wpml.settings.utils = {};
			wpml.settings.utils.details_select = function() {};
			wpml.settings.utils.api_check = function() {};
			wpml.settings.utils.toggle_radio = function() {};
			