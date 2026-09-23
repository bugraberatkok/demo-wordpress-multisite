/**
 * Mobil menu: dugme paneli acar/kapatir; Esc ve panel disina tiklama kapatir.
 */
( function () {
	document.documentElement.classList.remove( 'no-js' );

	var header = document.querySelector( '[data-sp-header]' );
	var toggle = header && header.querySelector( '[data-sp-toggle]' );

	if ( ! toggle ) {
		return;
	}

	function setOpen( open ) {
		header.setAttribute( 'data-open', open ? 'true' : 'false' );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	}

	toggle.addEventListener( 'click', function () {
		setOpen( header.getAttribute( 'data-open' ) !== 'true' );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key === 'Escape' && header.getAttribute( 'data-open' ) === 'true' ) {
			setOpen( false );
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( header.getAttribute( 'data-open' ) === 'true' && ! header.contains( event.target ) ) {
			setOpen( false );
		}
	} );

	// Masaustu genisligine gecilince acik kalan panel durumunu sifirla.
	window.matchMedia( '(min-width: 960px)' ).addEventListener( 'change', function ( mq ) {
		if ( mq.matches ) {
			setOpen( false );
		}
	} );
} )();
