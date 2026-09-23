/**
 * Ana menu.
 *
 * - Dar ekran: "Menü" dugmesi paneli acar/kapatir.
 * - Urunlerimiz: ok dugmesi listeyi acar/kapatir. Masaustunde liste imlec ve
 *   klavye odagiyla da acilir (CSS); Esc hepsini kapatir.
 */
( function () {
	document.documentElement.classList.remove( 'no-js' );

	var header = document.querySelector( '[data-ik-header]' );

	if ( ! header ) {
		return;
	}

	var toggle  = header.querySelector( '[data-ik-toggle]' );
	var parents = Array.prototype.slice.call( header.querySelectorAll( '[data-ik-parent]' ) );

	function setPanel( open ) {
		header.setAttribute( 'data-open', open ? 'true' : 'false' );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	}

	function setDrop( item, state ) {
		item.setAttribute( 'data-open', state );
		item.querySelector( '[data-ik-caret]' ).setAttribute( 'aria-expanded', state === 'true' ? 'true' : 'false' );
	}

	if ( toggle ) {
		toggle.addEventListener( 'click', function () {
			setPanel( header.getAttribute( 'data-open' ) !== 'true' );
		} );
	}

	parents.forEach( function ( item ) {
		var caret = item.querySelector( '[data-ik-caret]' );

		caret.addEventListener( 'click', function () {
			setDrop( item, item.getAttribute( 'data-open' ) === 'true' ? 'false' : 'true' );
		} );

		// Esc ile kapatilan liste, imlec ayrilinca yeniden acilabilir olsun.
		item.addEventListener( 'mouseleave', function () {
			if ( item.getAttribute( 'data-open' ) === 'closed' ) {
				setDrop( item, 'false' );
			}
		} );

		item.addEventListener( 'focusout', function ( event ) {
			if ( ! item.contains( event.relatedTarget ) && item.getAttribute( 'data-open' ) !== 'false' ) {
				setDrop( item, 'false' );
			}
		} );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key !== 'Escape' ) {
			return;
		}

		parents.forEach( function ( item ) {
			if ( item.contains( document.activeElement ) || item.matches( ':hover' ) || item.getAttribute( 'data-open' ) === 'true' ) {
				setDrop( item, 'closed' );
				item.querySelector( '[data-ik-caret]' ).focus();
			}
		} );

		if ( header.getAttribute( 'data-open' ) === 'true' ) {
			setPanel( false );
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( header.contains( event.target ) ) {
			return;
		}

		setPanel( false );
		parents.forEach( function ( item ) {
			setDrop( item, 'false' );
		} );
	} );

	// Masaustune gecilince acik kalan panel durumunu sifirla.
	window.matchMedia( '(min-width: 1120px)' ).addEventListener( 'change', function ( mq ) {
		if ( mq.matches ) {
			setPanel( false );
		}
	} );
} )();
