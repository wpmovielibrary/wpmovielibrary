(function($){
// 	$(window).load(function(){
		$('#library').mCustomScrollbar({
			scrollInertia: 50
		});
// 	});
	
	$('.library-menu a').click(function(e) {
		e.preventDefault();
		if ( 'flat-view' == this.id )
			$('#library').removeClass('detailled').addClass('flat');
		else if ( 'detailled-view' == this.id )
			$('#library').removeClass('flat').addClass('detailled');
	});
	
	$('.movie-poster a').click(function(e) {
		e.preventDefault();
	});
	
	
})(jQuery);
