
$ = $ || jQuery;

wpml = wpml || {};

var wpml_meta, wpml_details, wpml_media, wpml_status, wpml_rating;

// Init
wpml.editor = {
	meta: {
		init: function() {}
	},
	details: {
		init: function() {
			wpml.editor.details.status.init();
			wpml.editor.details.media.init();
			wpml.editor.details.rating.init();
		},
		status: undefined,
		media: undefined,
		rating: undefined,
		save: undefined
	},
	init: function() {
		wpml.editor.meta.init();
		wpml.editor.details.init();

		$('input#wpml_save').click(function() {
			wpml.editor.details.save();
		});
	}
}

// Edit Movie Status
wpml.editor.details.status = wpml_status = {

	select: '#movie-status-select',
	hidden: '#hidden-movie-status',
	display: '#movie-status-display',

	edit: '#edit-movie-status',
	save: '#save-movie-status',
	cancel: '#cancel-movie-status',

	init: function() {

		$(wpml_status.edit).on( 'click', function( e ) {
			e.preventDefault();
			wpml_status.show();
		});

		$(wpml_status.save).on( 'click', function( e ) {
			e.preventDefault();
			wpml_status.update();
		});

		$(wpml_status.cancel).on( 'click', function( e ) {
			e.preventDefault();
			wpml_status.revert();
		});
	},

	show: function() {
		if ( $(wpml_status.select).is(":hidden") ) {
			$(wpml_status.select).slideDown('fast');
			$(wpml_status.edit).hide();
		}
	},

	update: function() {
		var option = '#movie-status > option:selected';
		$(wpml_status.select).slideUp('fast');
		$(wpml_status.edit).show();
		$(wpml_status.display).text( $(option).text() );
		$(wpml_status.hidden).val( $(option).prop('id') );
	},

	revert: function() {
		var option = '#movie-status #'+$(wpml_status.hidden).val();
		$(wpml_status.select).slideUp('fast');
		$(option).prop('selected', true);
		$(wpml_status.display).text( $(option).text() );
		$(wpml_status.edit).show();
	}
}

// Edit Movie Media
wpml.editor.details.media = wpml_media = {

	select: '#movie-media-select',
	hidden: '#hidden-movie-media',
	display: '#movie-media-display',

	edit: '#edit-movie-media',
	save: '#save-movie-media',
	cancel: '#cancel-movie-media',

	init: function() {

		$(wpml_media.edit).on( 'click', function( e ) {
			e.preventDefault();
			wpml_media.show();
		});

		$(wpml_media.save).on( 'click', function( e ) {
			e.preventDefault();
			wpml_media.update();
		});

		$(wpml_media.cancel).on( 'click', function( e ) {
			e.preventDefault();
			wpml_media.revert();
		});
	},

	show: function() {
		if ( $(wpml_media.select).is(":hidden") ) {
			$(wpml_media.select).slideDown('fast');
			$(wpml_media.edit).hide();
		}
	},

	update: function() {
		var option = '#movie-media > option:selected';
		$(wpml_media.select).slideUp('fast');
		$(wpml_media.edit).show();
		$(wpml_media.display).text( $(option).text() );
		$(wpml_status.hidden).val( $(option).prop('id') );
	},

	revert: function() {
		var option = '#movie-media #'+$(wpml_media.hidden).val();
		$(wpml_media.select).slideUp('fast');
		$(option).prop('selected', true);
		$(wpml_media.display).text( $(option).text() );
		$(wpml_media.edit).show();
	}
};

// Edit Movie Rating
wpml.editor.details.rating = wpml_rating = {

	stars: '#stars, #bulk_stars',

	select: '#movie-rating-select',
	hidden: '#hidden-movie-rating',
	display: '#movie-rating-display',

	edit: '#edit-movie-rating',
	save: '#save-movie-rating',
	cancel: '#cancel-movie-rating',

	init: function() {

		$(wpml_rating.edit).on( 'click', function( e ) {
			e.preventDefault();
			wpml_rating.show();
		});

		$(wpml_rating.cancel).on( 'click', function( e ) {
			e.preventDefault();
			wpml_rating.revert();
		});

		$(wpml_rating.save).on( 'click', function( e ) {
			e.preventDefault();
			wpml_rating.update();
		});

		$(wpml_rating.stars).on( 'click', function( e ) {
			e.preventDefault();
			wpml_rating.rate();
		});

		$(wpml_rating.stars).on( 'mousemove', function( e ) {
			wpml_rating.change_in( e );
		});

		$(wpml_rating.stars).on( 'mouseleave', function( e ) {
			wpml_rating.change_out( e );
		});


	},

	show: function() {
		if ( $(wpml_rating.select).is(":hidden") ) {
			$(wpml_rating.display).hide();
			$(wpml_rating.select).slideDown('fast');
			$(wpml_rating.edit).hide();
		}
	},

	revert: function() {
		$(wpml_rating.select).slideUp('fast');
		$(wpml_rating.edit).show();
		$(wpml_rating.display).show();
	},

	change_in: function( e ) {

		var classes = 'stars-0 stars-0-0 stars-0-5 stars-1-0 stars-1-5 stars-2-0 stars-2-5 stars-3-0 stars-3-5 stars-4-0 stars-4-5 stars-5-0';

		var parentOffset = $(wpml_rating.stars).offset(); 
		var relX = e.pageX - parentOffset.left;

		if ( relX <= 0 ) var _rate = '0';
		if ( relX > 0 && relX < 8 ) var _rate = '0.5';
		if ( relX >= 8 && relX < 16 ) var _rate = '1.0';
		if ( relX >= 16 && relX < 24 ) var _rate = '1.5';
		if ( relX >= 24 && relX < 32 ) var _rate = '2.0';
		if ( relX >= 32 && relX < 40 ) var _rate = '2.5';
		if ( relX >= 40 && relX < 48 ) var _rate = '3.0';
		if ( relX >= 48 && relX < 56 ) var _rate = '3.5';
		if ( relX >= 56 && relX < 64 ) var _rate = '4.0';
		if ( relX >= 64 && relX < 80 ) var _rate = '4.5';
		if ( relX >= 80 ) var _rate = '5.0';

		var _class = 'stars-' + _rate.replace('.','-');
		var _label = _class.replace('stars-','stars-label-');

		$(wpml_rating.stars).removeClass( classes ).addClass( _class );
		$('.stars-label').removeClass('show');
		$('#'+_label).addClass('show');
		$(wpml_rating.stars).attr('data-rating', _rate);
	},

	change_out: function( e ) {

		var classes = 'stars-0 stars-0-0 stars-0-5 stars-1-0 stars-1-5 stars-2-0 stars-2-5 stars-3-0 stars-3-5 stars-4-0 stars-4-5 stars-5-0';

		if ( 'true' == $(wpml_rating.stars).attr('data-rated') )
			return false;

		var _class = '';

		if ( $('#hidden-movie-rating, #bulk-hidden-movie-rating').length ) {
			_class = $('#hidden-movie-rating, #bulk-hidden-movie-rating').val();
			_class = 'stars-' + _class.replace('.','-');
		}

		$(wpml_rating.stars).removeClass( classes ).addClass( _class );
		$('.stars-label').removeClass('show');
	},

	rate: function() {

		var _rate = $(wpml_rating.stars).attr('data-rating');

		if ( undefined == _rate )
			return false;

		_rate = _rate.replace('stars-','');
		_rate = _rate.replace('-','.');

		$('#movie-rating, #bulk-movie-rating').val(_rate);
		$(wpml_rating.stars).attr('data-rating', _rate);
		$(wpml_rating.stars).attr('data-rated', true);
	},

	update: function() {
		var n = $('#movie-rating').val();
		$(wpml_rating.select).slideUp('fast');
		$(wpml_rating.edit).show();
		$(wpml_rating.display).removeClass().addClass('stars-'+n.replace('.','-')).show();
		$('#movie-rating, #hidden-movie-rating').val(n);
	}
};

wpml.editor.details.save = function() {
	$.ajax({
		type: 'POST',
		url: ajax_object.ajax_url,
		data: {
			action: 'wpml_save_details',
			wpml_check: ajax_object.wpml_check,
			post_id: $('#post_ID').val(),
			wpml_details: {
				media: $('#movie-media').val(),
				status: $('#movie-status').val(),
				rating: $('#movie-rating').val()
			}
		},
		beforeSend: function() {
			$('input#wpml_save').addClass('button-loading');
		},
		complete: function() {
			$('input#wpml_save').removeClass('button-loading');
		},
	});
};

wpml.editor.init();