
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	TagBox: Backbone.View.extend({

		events: {
			'click [data-action="terms-autocomplete"]' : 'autocomplete'
		},

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			this.prepare();
		},

		/**
		 * Add a new link to the tagBox to manually trigger terms
		 * autocompletion.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		prepare: function() {

			var $link = this.$( 'a#link-' + this.type ),
			     link = '<a href="#" data-action="terms-autocomplete">' + wpmolyL10n.autocomplete + '</a>';
			    $link.after( '<span> ' + s.sprintf( wpmolyL10n.termsAutocomplete, link, wpmolyL10n[ this.type + 's' ] ) + '</span>' );
		},

		/**
		 * Trigger terms autocompletion.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' event.
		 * 
		 * @return   void
		 */
		autocomplete: function( event ) {

			event.preventDefault();

			var tab = {
				collection : 'director',
				genre      : 'genres',
				actor      : 'cast'
			};
			var terms = wpmoly.editor.controller.meta.get( tab[ this.type ] );

			wpmoly.trigger( 'editor:meta:terms-autocomplete', this.type, null, terms );
		}

	})

} );

_.extend( wpmoly.view, {

	CollectionsBox: wpmoly.view.TagBox.extend({

		el: '#tagsdiv-collection',

		type: 'collection'
	}),

	GenresBox: wpmoly.view.TagBox.extend({

		el: '#tagsdiv-genre',

		type: 'genre'
	}),

	ActorsBox: wpmoly.view.TagBox.extend({

		el: '#tagsdiv-actor',

		type: 'actor'
	}),

} );