
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	wpmoly.tester = {

		regex: {

			match: function( string ) {

				var reg = /{([^}]+),(\d)}|{([^}]+)}/gi,
				m, tags = [];

				while ( null !== ( m = reg.exec( string ) ) ) {
					if ( m.index === reg.lastIndex ) {
						reg.lastIndex++;
					}

					// Simple tag {tag}
					if ( m[3] && ! m[1] ) {
						tags.push({
							tag  : m[0],
							meta : m[3]
						});
					// Complex tag {tag,n}
					} else if ( m[1] && ! m[3] ) {
						tags.push({
							tag  : '{' + m[1] + '}',
							meta : m[1],
							n    : m[2]
						});
					}
				}

				return tags;
			},

			replace: function() {

				
			}
		},

		import: {

			openEditor: function() {

				wp.media.editor.open();
				wp.media.frame.close();
			},

			autoImportBackdrops: function() {

				var controller = wp.media.frame.imagesController,
				     backdrops = controller.collection.where({ type: 'backdrop' });
				     backdrops = backdrops.slice( 0, 2 );

				controller.mode = 'backdrop';
				controller.selection.add( backdrops );

				wpmoly.trigger( 'modal:images:import' );
			}
		}
	}

})( jQuery, _, Backbone );
