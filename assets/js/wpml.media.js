
wpml = wpml || {};

var images;

wpml = {
	media: {
		init: undefined,
		images: undefined,
		posters: undefined
	}
};

wpml.media.images = images = {

	detailsContainerId: '#attachment-details',
	settingsContainerId: '#attachment-settings',

	frame: function() {

		if ( this._frame )
			return this._frame;

		this._frame = wp.media({
			title: 'Import images from "' + $('#tmdb_data_title').val() + '"',
			frame: 'select',
			searchable: false,
			library: {
				// Dummy: avoid any image to be loaded
				type : 'gallery',
				post__in:[ $('#post_ID').val() ],
				post__not_in:[0],
				s: 'TMDb_ID='+$('#tmdb_data_tmdb_id').val()+''
			},
			multiple: true,
			button: {
				text: 'Import images'
			}
		}),

		this._frame.state('library').unset('router');
		this._frame.options.button.reset = false;
		this._frame.options.button.close = false;

		this._frame.state('library').unbind('select').on('select', this.select);

		return this._frame;
	},

	select: function() {

		var $content = $(wpml.media.images._frame.content.selector);

		if ( ! $('#progressbar_bg').length )
			$content.append('<div id="progressbar_bg"><div id="progressbar"><div id="progress"></div></div><div id="progress_status">Please wait while images are uploaded...</div>');

		$('#progressbar_bg, #progressbar').show();

		var settings = wp.media.view.settings,
		    selection = this.get('selection'),
		    total = selection.length;

		$('.added').remove();

		images.total = total;
		selection.map( images.upload );

		return;
	},

	upload: function( image, i ) {

		var index = i + 1;
		var progress = index == images.total ? 100 : Math.round( ( index * 100 ) / images.total );

		$.ajax({
			type: 'GET',
			url: ajax_object.ajax_url,
			data: {
				action: 'wpml_upload_image',
				wpml_check: ajax_object.wpml_check,
				image: image.attributes.tmdb_data,
				title: ajax_object.image_from + ' ' + $('#tmdb_data_title').val(),
				post_id: $('#post_ID').val(),
				tmdb_id: $('#tmdb_data_tmdb_id').val()
			},
			success: function(_r) {
				console.log( _r );
				window.setTimeout( function() {$('#progressbar #progress').width(''+progress+'%');}, 1500 );
			}
		});
	},

	init: function() {
		images.frame().open();
	}
};

wpml.media.init = function() {
	$('#tmdb_save_images').on('click', function(e) {
		e.preventDefault();
		wpml.movie.images.save();
	});

	$('#tmdb_load_images').on('click', function(e) {
		e.preventDefault();
		wpml.media.images.init();
	});
};

wpml.media.init();