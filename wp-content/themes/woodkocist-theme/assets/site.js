/**
 * Mobil menu, sayfa yenilemeden seri/tur suzgeci, form sonrasi odak.
 * JavaScript yoksa suzgec baglantilari sayfayi suzulmus haliyle acar.
 */
( function () {
	'use strict';

	var toggle = document.querySelector( '[data-menu-toggle]' );
	var panel = document.getElementById( 'wk-mobile-nav' );

	if ( toggle && panel ) {
		toggle.addEventListener( 'click', function () {
			var open = 'true' !== toggle.getAttribute( 'aria-expanded' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			panel.hidden = ! open;
		} );
	}

	var catalog = document.querySelector( '[data-catalog]' );

	if ( catalog ) {
		var links = catalog.querySelectorAll( '[data-filter]' );
		var cards = catalog.querySelectorAll( '.wk-card' );
		var count = catalog.querySelector( '[data-count]' );
		var empty = catalog.querySelector( '[data-empty]' );

		var apply = function ( slug, link ) {
			var shown = 0;

			cards.forEach( function ( card ) {
				var cats = ( card.getAttribute( 'data-cats' ) || '' ).split( ' ' );
				var visible = '' === slug || -1 !== cats.indexOf( slug );
				card.hidden = ! visible;
				shown += visible ? 1 : 0;
			} );

			links.forEach( function ( item ) {
				if ( item === link ) {
					item.setAttribute( 'aria-current', 'true' );
				} else {
					item.removeAttribute( 'aria-current' );
				}
			} );

			if ( count ) {
				count.textContent = shown + ' ürün gösteriliyor';
			}

			if ( empty ) {
				empty.hidden = shown > 0;
			}
		};

		links.forEach( function ( link ) {
			link.addEventListener( 'click', function ( event ) {
				// Yeni sekmede acma (Ctrl/Cmd/orta tik) tarayicinin kendi isi kalsin.
				if ( 0 !== event.button || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey ) {
					return;
				}

				event.preventDefault();
				apply( link.getAttribute( 'data-filter' ) || '', link );

				// Adres suzgeci yansitsin (paylasilabilir baglanti; gecmise kayit eklemez).
				if ( window.history && window.history.replaceState ) {
					window.history.replaceState( null, '', link.getAttribute( 'href' ) );
				}
			} );
		} );
	}

	var notice = document.querySelector( '[data-focus]' );

	if ( notice ) {
		notice.focus( { preventScroll: true } );
	}
}() );
