
if ( undefined == window.wpmoly ) window.wpmoly = {};
if ( undefined == window.redux ) window.redux = {};
if ( undefined == window.redux.field_objects ) window.redux.field_objects = {};
if ( undefined == window.redux.field_objects.select ) window.redux.field_objects.select = {};

$ = $ || jQuery;

wpmoly = {};

	wpmoly.init = function() {};

	wpmoly.editor = {};
	wpmoly.importer = {};
	wpmoly.queue = {};
	wpmoly.movies = {};
	wpmoly.media = {};
	wpmoly.landing = {};
	wpmoly.settings = {};

		/**
		 * Movies Post Editor page's Metadata part
		 * 
		 * @since    1.0
		 */
		wpmoly.editor.meta = {};
			wpmoly.editor.meta.search = function( post_id, title, caller ) {};
			wpmoly.editor.meta.select = function( movies, message ) {};
			wpmoly.editor.meta.get = function( tmdb_id ) {};
			wpmoly.editor.meta.set = function( data ) {};
			wpmoly.editor.meta.prefill_title = function( title ) {};

		/**
		 * Movies Post Editor page's Details part
		 * 
		 * @since    1.0
		 */
		wpmoly.editor.details = {};
			wpmoly.editor.details = {};
				wpmoly.editor.details.init = function() {};
				wpmoly.editor.details.save = function() {};
				wpmoly.editor.details.inline_editor = function( type, link ) {};
				wpmoly.editor.details.inline_edit = function( type, link ) {};

			wpmoly.editor.details.media = {};
				wpmoly.editor.details.init = function() {};
				wpmoly.editor.details.show = function() {};
				wpmoly.editor.details.update = function() {};
				wpmoly.editor.details.revert = function() {};

			wpmoly.editor.details.rating = {};
				wpmoly.editor.details.init = function() {};
				wpmoly.editor.details.show = function() {};
				wpmoly.editor.details.update = function() {};
				wpmoly.editor.details.revert = function() {};

		/**
		 * Handles the Imported part of the Importer: search for movies'
		 * metadata, select movies in lists, set data fields.
		 * 
		 * @since    1.0
		 */
		wpmoly.importer.meta = {};
			wpmoly.importer.meta.do = function( action ) {};
			wpmoly.importer.meta.search = function( post_id ) {};
			wpmoly.importer.meta.select = function( movies, message ) {};
			wpmoly.importer.meta.get = function( post_id, tmdb_id ) {};
			wpmoly.importer.meta.set = function( data ) {};

		/**
		 * Handles the Import part of the Importer: import lists of movies
		 * or delete movies
		 * 
		 * @since    1.0
		 */
		wpmoly.importer.movies = {};
			wpmoly.importer.movies.delete = function() {};
			wpmoly.importer.movies.import = function() {};

		/**
		 * Handles the Importer view alterations like AJAX nav or counter
		 * updates.
		 * 
		 * @since    1.0
		 */
		wpmoly.importer.view = {};
			wpmoly.importer.view.reload = function() {};
			wpmoly.importer.view.navigate = function() {};
			wpmoly.importer.view.paginate = function() {};
			wpmoly.importer.view.update_count = function() {};

		/**
		 * TODO
		 * 
		 * @since    1.0
		 */
		wpmoly.queue.movies = {};
			wpmoly.queue.movies.add = function() {};
			wpmoly.queue.movies.remove = function() {};
			wpmoly.queue.movies.prepare = function() {};
			wpmoly.queue.movies.import = function() {};

		/**
		 * TODO
		 * 
		 * @since    1.0
		 */
		wpmoly.queue.utils = {};
			wpmoly.queue.utils.toggle_button = function() {};
			wpmoly.queue.utils.toggle_inputs = function() {};

		/**
		 * Movie Images handling
		 * 
		 * @since    1.0
		 */
		wpmoly.media.images = {};
			wpmoly.media.images.init = function() {};
			wpmoly.media.images.frame = function() {};
			wpmoly.media.images.select = function() {};
			wpmoly.media.images.upload = function() {};
			wpmoly.media.images.close = function() {};

		/**
		 * Movie Posters handling
		 * 
		 * @since    1.0
		 */
		wpmoly.media.posters = {};
			wpmoly.media.posters.init = function() {};
			wpmoly.media.posters.frame = function() {};
			wpmoly.media.posters.select = function() {};
			wpmoly.media.posters.set_featured = function() {};
			wpmoly.media.posters.close = function() {};

		/**
		 * TODO
		 * 
		 * @since    1.0
		 */
		wpmoly.landing.modal = {};
			wpmoly.landing.modal.open = function() {};
			wpmoly.landing.modal.close = function() {};
			wpmoly.landing.modal.resize = function() {};
			wpmoly.landing.modal.update = function() {};

		/**
		 * TODO
		 * 
		 * @since    1.0
		 */
		wpmoly.landing.dashboard = {};
			wpmoly.landing.dashboard.handle_widget = function() {};

		/**
		 * Settings & Import Panels
		 * 
		 * @since    1.0
		 */
		wpmoly.settings.panels = {};
			wpmoly.settings.panels.init = function() {};
			wpmoly.settings.panels.switch_panel = function() {};

		/**
		 * Settings Metadata sorting part
		 * 
		 * @since    1.0
		 */
		wpmoly.settings.sortable = {};
			wpmoly.settings.sortable.init = function() {};
			wpmoly.settings.sortable.update_item_style = function() {};
			wpmoly.settings.sortable.update_item = function() {};

		/**
		 * 
		 * 
		 * @since    1.0
		 */
		wpmoly.settings.utils = {};
			wpmoly.settings.utils.details_select = function() {};
			wpmoly.settings.utils.api_check = function() {};
			wpmoly.settings.utils.toggle_radio = function() {};


jQuery(document).ready(function() {
	wpmoly.init();
});