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

	$('#movie_details, #movie_details_fader').click(function(e) {
		e.preventDefault();
		$('#movie_details, #movie_details_fader').hide();
	});
	
	
})(jQuery);
