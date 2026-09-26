/**
 * Urun gorselini buyutme: tiklayinca tam boy gorsel pencerede acilir; pencere
 * icinde ikinci kademe yakinlastirma (zoom.js: tiklama, tekerlek, iki parmak,
 * surukleme, + - 0 tuslari). JavaScript yoksa gorsel sayfada oldugu gibi kalir.
 */
( function () {
	'use strict';

	var opener = document.querySelector( '[data-ik-zoom-open]' );
	var dialog = document.querySelector( '[data-ik-zoom]' );

	// Panel onizlemesinde gorsele tiklamak alan duzenleyicisini acar;
	// buyutme penceresi araya girmesin.
	if ( ! opener || ! dialog || 'function' !== typeof dialog.showModal || 'function' !== typeof window.woodZoom
		|| document.body.classList.contains( 'nwcs-preview-mode' ) ) {
		return;
	}

	var root = document.documentElement;

	var image = dialog.querySelector( '[data-ik-zoom-image]' );
	var zoom = window.woodZoom( dialog.querySelector( '[data-ik-zoom-stage]' ), image, {
		in: dialog.querySelector( '[data-ik-zoom-in]' ),
		out: dialog.querySelector( '[data-ik-zoom-out]' ),
		reset: dialog.querySelector( '[data-ik-zoom-reset]' ),
		level: dialog.querySelector( '[data-ik-zoom-level]' ),
	} );

	opener.addEventListener( 'click', function () {
		var full = opener.getAttribute( 'data-full' );

		zoom.reset();

		if ( full && image.getAttribute( 'src' ) !== full ) {
			image.src = full;
		}

		// Arkadaki sayfa kaymasin; kaybolan kaydirma cubugunun yeri korunur.
		root.style.paddingRight = ( window.innerWidth - root.clientWidth ) + 'px';
		root.style.overflow = 'hidden';

		dialog.showModal();
	} );

	// Kapaninca sayfa serbest, odak buyut dugmesine geri doner.
	dialog.addEventListener( 'close', function () {
		root.style.overflow = '';
		root.style.paddingRight = '';
		opener.focus();
	} );

	dialog.addEventListener( 'keydown', function ( event ) {
		zoom.key( event );
	} );

	// Pencerenin disina (karartmaya) tiklayinca kapanir.
	dialog.addEventListener( 'click', function ( event ) {
		if ( event.target === dialog ) {
			dialog.close();
		}
	} );
}() );
