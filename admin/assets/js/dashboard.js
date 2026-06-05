
document.addEventListener( 'DOMContentLoaded', function() {
	document.querySelectorAll( '.wpmovielibrary-panel-content' ).forEach( ( content ) => {
		const wrapper = content.querySelector( '.wpmovielibrary-panel-scroll-wrapper' );
		if ( ! wrapper ) {
			return;
		}

		// Card slot width (card + flex gap), derived from the actual layout
		// so it stays accurate if the design changes.
		const metrics = () => {
			const card = wrapper.querySelector( '.wpmovielibrary-movie-card' );
			const gap = parseFloat( window.getComputedStyle( wrapper ).columnGap ) || 12;
			const step = card ? card.getBoundingClientRect().width + gap : wrapper.clientWidth;
			return { step, visible: Math.max( 1, Math.floor( ( wrapper.clientWidth + gap ) / step ) ) };
		};

		// Scroll one full page: the first partially visible card becomes the
		// first card of the next page. Snapping the target to a multiple of
		// the card slot width keeps every page aligned on a card boundary.
		const page = ( direction ) => {
			const { step, visible } = metrics();
			const target = ( Math.round( wrapper.scrollLeft / step ) + direction * visible ) * step;
			wrapper.scrollTo( { left: target, behavior: 'smooth' } );
		};

		content.querySelector( '.wpmovielibrary-panel-scroll-indicator-left button' )?.addEventListener( 'click', () => page( -1 ) );
		content.querySelector( '.wpmovielibrary-panel-scroll-indicator-right button' )?.addEventListener( 'click', () => page( 1 ) );

		// Translate vertical mouse wheel into horizontal scrolling over the
		// list. deltaMode 1 means line-based deltas (Firefox wheel events).
		wrapper.addEventListener( 'wheel', ( event ) => {
			if ( Math.abs( event.deltaY ) <= Math.abs( event.deltaX ) ) {
				return;
			}
			if ( wrapper.scrollWidth <= wrapper.clientWidth ) {
				return;
			}
			event.preventDefault();
			const delta = 1 === event.deltaMode ? 40 * event.deltaY : event.deltaY;
			wrapper.scrollBy( { left: delta, behavior: 'smooth' } );
		}, { passive: false } );

		// Hide the edge indicators when there is nothing left to scroll in
		// their direction.
		const update = () => {
			const max = wrapper.scrollWidth - wrapper.clientWidth;
			content.classList.toggle( 'is-scroll-start', 1 >= wrapper.scrollLeft );
			content.classList.toggle( 'is-scroll-end', wrapper.scrollLeft >= max - 1 );
		};

		wrapper.addEventListener( 'scroll', update, { passive: true } );
		window.addEventListener( 'resize', update );
		update();
	} );
} );
