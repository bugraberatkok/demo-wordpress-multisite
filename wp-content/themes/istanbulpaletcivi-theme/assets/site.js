/**
 * Mobil menu ve form sonrasi odak. Baska davranis yok; JavaScript kapaliyken
 * menu gizli kalir ama alt bilgideki sayfa listesi ve arama cubugu calisir.
 */
( function () {
	'use strict';

	var toggle = document.querySelector( '[data-menu-toggle]' );
	var panel = document.getElementById( 'pc-mobile-nav' );

	if ( toggle && panel ) {
		var setOpen = function ( open ) {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			panel.hidden = ! open;
		};

		toggle.addEventListener( 'click', function () {
			setOpen( 'true' !== toggle.getAttribute( 'aria-expanded' ) );
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && ! panel.hidden ) {
				setOpen( false );
				toggle.focus();
			}
		} );
	}

	// Form gonderildikten sonra sonuc mesaji ekran okuyucuya ve klavyeye gelsin.
	var notice = document.querySelector( '[data-focus]' );

	if ( notice ) {
		notice.focus( { preventScroll: true } );
	}
}() );
