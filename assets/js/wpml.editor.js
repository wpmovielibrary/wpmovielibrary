
$ = $ || jQuery;

wpml = wpml || {};

var meta, details, media, status, rating;

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
		rating: undefined
	},
	init: function() {
		wpml.editor.meta.init();
		wpml.editor.details.init();

		/*$('input#wpml_save').click(function() {
			wpml.movie.save_details();
		});*/
	}
}

// Edit Movie Status
wpml.editor.details.status = status = {

	select: '#movie-status-select',
	hidden: '#hidden-movie-status',
	display: '#movie-status-display',

	edit: '#edit-movie-status',
	save: '#save-movie-status',
	cancel: '#cancel-movie-status',

	init: function() {

		$(status.edit).on( 'click', function( e ) {
			e.preventDefault();
			status.show();
		});

		$(status.save).on( 'click', function( e ) {
			e.preventDefault();
			status.update();
		});

		$(status.cancel).on( 'click', function( e ) {
			e.preventDefault();
			status.revert();
		});
	},

	show: function() {
		if ( $(status.select).is(":hidden") ) {
			$(status.select).slideDown('fast');
			$(status.edit).hide();
		}
	},

	update: function() {
		$(status.select).slideUp('fast');
		$(status.edit).show();
		$(status.display).text( $('#movie-status > option:selected').text() );
	},

	revert: function() {
		$(status.select).slideUp('fast');
		$('#movie-status').val( $(status.hidden).val() );
		$(status.display).text( $(status.hidden).val() );
		$(status.edit).show();
	}
}

// Edit Movie Media
wpml.editor.details.media = media = {

	select: '#movie-media-select',
	hidden: '#hidden-movie-media',
	display: '#movie-media-display',

	edit: '#edit-movie-media',
	save: '#save-movie-media',
	cancel: '#cancel-movie-media',

	init: function() {

		$(media.edit).on( 'click', function( e ) {
			e.preventDefault();
			media.show();
		});

		$(media.save).on( 'click', function( e ) {
			e.preventDefault();
			media.update();
		});

		$(media.cancel).on( 'click', function( e ) {
			e.preventDefault();
			media.revert();
		});
	},

	show: function() {
		if ( $(media.select).is(":hidden") ) {
			$(media.select).slideDown('fast');
			$(media.edit).hide();
		}
	},

	update: function() {
		$(media.select).slideUp('fast');
		$(media.edit).show();
		$(media.display).text( $('#movie-media > option:selected').text() );
	},

	revert: function() {
		$(media.select).slideUp('fast');
		$('#movie-media').val( $(media.hidden).val() );
		$(media.display).text( $(media.hidden).val() );
		$(media.edit).show();
	}
};

// Edit Movie Rating
wpml.editor.details.rating = rating = {

	stars: '#stars, #bulk_stars',

	select: '#movie-rating-select',
	hidden: '#hidden-movie-rating',
	display: '#movie-rating-display',

	edit: '#edit-movie-rating',
	save: '#save-movie-rating',
	cancel: '#cancel-movie-rating',

	init: function() {

		$(rating.edit).on( 'click', function( e ) {
			e.preventDefault();
			rating.show();
		});

		$(rating.cancel).on( 'click', function( e ) {
			e.preventDefault();
			rating.revert();
		});

		$(rating.save).on( 'click', function( e ) {
			e.preventDefault();
			rating.update();
		});

		$(rating.stars).on( 'click', function( e ) {
			e.preventDefault();
			rating.rate();
		});

		$(rating.stars).on( 'mousemove', function( e ) {
			rating.change_in( e );
		});

		$(rating.stars).on( 'mouseleave', function( e ) {
			rating.change_out( e );
		});


	},

	show: function() {
		if ( $(rating.select).is(":hidden") ) {
			$(rating.display).hide();
			$(rating.select).slideDown('fast');
			$(rating.edit).hide();
		}
	},

	revert: function() {
		$(rating.select).slideUp('fast');
		$(rating.edit).show();
		$(rating.display).show();
	},

	change_in: function( e ) {

		var classes = 'stars-0 stars-0-0 stars-0-5 stars-1-0 stars-1-5 stars-2-0 stars-2-5 stars-3-0 stars-3-5 stars-4-0 stars-4-5 stars-5-0';

		var parentOffset = $(rating.stars).offset(); 
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

		$(rating.stars).removeClass( classes ).addClass( _class );
		$('.stars-label').removeClass('show');
		$('#'+_label).addClass('show');
		$(rating.stars).attr('data-rating', _rate);
	},

	change_out: function( e ) {

		var classes = 'stars-0 stars-0-0 stars-0-5 stars-1-0 stars-1-5 stars-2-0 stars-2-5 stars-3-0 stars-3-5 stars-4-0 stars-4-5 stars-5-0';

		if ( 'true' == $(rating.stars).attr('data-rated') )
			return false;

		var _class = '';

		if ( $('#hidden-movie-rating, #bulk-hidden-movie-rating').length ) {
			_class = $('#hidden-movie-rating, #bulk-hidden-movie-rating').val();
			_class = 'stars-' + _class.replace('.','-');
		}

		$(rating.stars).removeClass( classes ).addClass( _class );
		$('.stars-label').removeClass('show');
	},

	rate: function() {

		var _rate = $(rating.stars).attr('data-rating');

		if ( undefined == _rate )
			return false;

		_rate = _rate.replace('stars-','');
		_rate = _rate.replace('-','.');

		$('#movie-rating, #bulk-movie-rating').val(_rate);
		$(rating.stars).attr('data-rating', _rate);
		$(rating.stars).attr('data-rated', true);
	},

	update: function() {
		var n = $('#movie-rating').val();
		$(rating.select).slideUp('fast');
		$(rating.edit).show();
		$(rating.display).removeClass().addClass('stars-'+n.replace('.','-')).show();
		$('#movie-rating, #hidden-movie-rating').val(n);
	}
}

wpml.editor.init();