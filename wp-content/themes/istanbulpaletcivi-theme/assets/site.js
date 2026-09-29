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

	/* ---------------------------------------------------------------- */
	/* Urun gorseli: fareyle uzerine gelince imlecin oldugu yer buyur;   */
	/* tiklayinca tam ekran; tam ekranda tiklayinca tiklanan yer         */
	/* yakinlasir, yakinken surukleyerek gezilir (assets/zoom.js).       */
	/* ---------------------------------------------------------------- */

	document.querySelectorAll( '[data-pc-stage]' ).forEach( function ( stage ) {
		var lens = function ( event ) {
			// Yer tutucuda (gorsel yok) buyutulecek bir sey yok.
			if ( 'mouse' !== event.pointerType || ! stage.querySelector( 'img' ) ) {
				return;
			}

			var box = stage.getBoundingClientRect();

			stage.style.setProperty( '--zx', ( ( event.clientX - box.left ) / box.width * 100 ) + '%' );
			stage.style.setProperty( '--zy', ( ( event.clientY - box.top ) / box.height * 100 ) + '%' );
			stage.classList.add( 'is-lens' );
		};

		stage.addEventListener( 'pointerenter', lens );
		stage.addEventListener( 'pointermove', lens );
		stage.addEventListener( 'pointerleave', function () {
			stage.classList.remove( 'is-lens' );
		} );

		var dialog = stage.closest( '.pc-product' ).querySelector( '[data-pc-lb]' );

		if ( ! dialog || 'function' !== typeof dialog.showModal ) {
			return;
		}

		var image = dialog.querySelector( '[data-pc-lb-img]' );
		var zoom = 'function' === typeof window.woodZoom ? window.woodZoom( dialog.querySelector( '[data-pc-lb-stage]' ), image, {} ) : null;

		stage.addEventListener( 'click', function () {
			stage.classList.remove( 'is-lens' );

			if ( ! stage.querySelector( 'img' ) ) {
				return;
			}
			image.src = stage.getAttribute( 'data-full' );

			if ( zoom ) {
				zoom.reset();
			}

			dialog.showModal();
		} );

		dialog.querySelector( '[data-pc-lb-close]' ).addEventListener( 'click', function () {
			dialog.close();
		} );

		dialog.addEventListener( 'keydown', function ( event ) {
			if ( zoom ) {
				zoom.key( event );
			}
		} );

		dialog.addEventListener( 'close', function () {
			stage.focus();
		} );
	} );
}() );
