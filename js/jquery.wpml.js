(function($){

	nano_options = {
		preventPageScrolling: true
	};

	var init = function() {
		var h = $('#wpmovielibrary').height() - $('.row-header').height();
		$('.library-content').height(h);
		$('.nano').nanoScroller();
	};

	init();

	$(window).resize(init());

	$('.nano').nanoScroller(nano_options);
	
	$('.library-menu a').click(function(e) {
		e.preventDefault();
		if ( 'flat-view' == this.id )
			$('#library').removeClass('detailled').addClass('flat');
		else if ( 'detailled-view' == this.id )
			$('#library').removeClass('flat').addClass('detailled');
	});
	
	$('.movie a').click(function(e) {
		e.preventDefault();
		$('#movie_details, #movie_details_fader').toggle();
	});

	$('#movie_details_fader').click(function(e) {
		e.preventDefault();
		$('#movie_details, #movie_details_fader').hide();
	});

	$('#movie_details .movie-expend').on('click', function(e) {
		e.preventDefault();

		if ( ! $('#movie_details').hasClass('detailled') ) {
			$(this).find('i').removeClass('icon-plus-sign').addClass('icon-minus-sign');
			$('#movie_details').addClass('detailled');
		}
		else {
			$(this).find('i').removeClass('icon-minus-sign').addClass('icon-plus-sign');
			$('#movie_details').removeClass('detailled');
		}
	});
	
	
})(jQuery);
