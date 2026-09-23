/**
 * Siparis sureci: bolum ekrana girince testere kesigi cizilir, adimlar
 * sirayla basilir. Hareket azaltma tercihinde hicbir sey saklanmaz.
 */
( function () {
	var body = document.querySelector( '[data-ik-process]' );

	if ( ! body || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	body.classList.add( 'is-ready' );

	var observer = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( entry.isIntersecting ) {
				body.classList.add( 'is-cut' );
				observer.disconnect();
			}
		} );
	}, { threshold: 0.45 } );

	observer.observe( body );
} )();
