/**
 * Kereste siteleri: ust menu, teklif penceresi ve urun galerisi. Hepsi JavaScript olmadan da calisan HTML'in ustune eklenir:
 * teklif dugmeleri iletisim sayfasindaki forma giden normal baglantidir.
 */
( function () {
	'use strict';

	/* ---------------- Mobil menu ---------------- */
	var toggle = document.querySelector( '[data-nav-toggle]' );
	var panel = document.querySelector( '[data-nav-panel]' );

	if ( toggle && panel ) {
		toggle.addEventListener( 'click', function () {
			var open = panel.hidden;
			panel.hidden = ! open;
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			toggle.setAttribute( 'aria-label', open ? 'Menüyü kapat' : 'Menüyü aç' );
		} );
	}

	/* ---------------- Teklif penceresi ---------------- */
	var dialog = document.querySelector( '[data-kr-dialog]' );

	var fillForm = function ( scope, product ) {
		var select = scope.querySelector( '[data-kr-product]' );

		if ( select && product ) {
			select.value = product;
		}
	};

	document.addEventListener( 'click', function ( event ) {
		var trigger = event.target.closest( '[data-kr-quote]' );

		if ( ! trigger ) {
			return;
		}

		var product = trigger.getAttribute( 'data-kr-product-name' ) || '';

		// Iletisim sayfasinda pencere yok; form sayfada, oraya kaydirip doldur.
		if ( ! dialog || 'function' !== typeof dialog.showModal ) {
			var inline = document.getElementById( 'teklif' );

			if ( inline ) {
				event.preventDefault();
				fillForm( inline, product );
				inline.scrollIntoView( { behavior: 'smooth' } );
			}

			return;
		}

		event.preventDefault();
		fillForm( dialog, product );
		dialog.showModal();

		var first = dialog.querySelector( 'input:not([type=hidden]):not([tabindex="-1"])' );
		if ( first ) {
			first.focus();
		}
	} );

	if ( dialog ) {
		// Gonderimden sonra (hata ya da basari) pencere kendiliginden acilir.
		if ( dialog.hasAttribute( 'data-open-on-load' ) && 'function' === typeof dialog.showModal ) {
			dialog.showModal();
		}

		dialog.addEventListener( 'click', function ( event ) {
			if ( event.target === dialog ) {
				dialog.close();
			}
		} );
	}

	/* ---------------- Urun galerisi ve buyutme ---------------- */
	var lightbox = document.querySelector( '[data-kr-lightbox]' );

	document.querySelectorAll( '[data-kr-gallery]' ).forEach( function ( holder ) {
		var gallery = [];

		try {
			gallery = JSON.parse( holder.getAttribute( 'data-kr-gallery' ) ) || [];
		} catch ( e ) {
			gallery = [];
		}

		var main = holder.querySelector( '[data-kr-main]' );
		var mainImage = holder.querySelector( '[data-kr-main-image]' );
		var thumbs = holder.querySelectorAll( '[data-kr-thumb]' );

		thumbs.forEach( function ( thumb ) {
			thumb.addEventListener( 'click', function () {
				var index = parseInt( thumb.getAttribute( 'data-kr-thumb' ), 10 ) || 0;

				if ( ! gallery[ index ] ) {
					return;
				}

				mainImage.src = gallery[ index ].url;
				mainImage.alt = gallery[ index ].alt || '';
				main.setAttribute( 'data-kr-open', String( index ) );
				thumbs.forEach( function ( other ) {
					other.setAttribute( 'aria-pressed', other === thumb ? 'true' : 'false' );
				} );
			} );
		} );

		if ( ! main || ! lightbox || 'function' !== typeof lightbox.showModal ) {
			return;
		}

		var image = lightbox.querySelector( '[data-kr-lightbox-image]' );
		var caption = lightbox.querySelector( '[data-kr-lightbox-caption]' );
		var nav = lightbox.querySelector( '[data-kr-lightbox-nav]' );
		var at = 0;

		var show = function ( index ) {
			at = ( index + gallery.length ) % gallery.length;
			image.src = gallery[ at ].url;
			image.alt = gallery[ at ].alt || '';
			caption.textContent = gallery[ at ].alt || '';
			nav.hidden = gallery.length < 2;
		};

		main.addEventListener( 'click', function () {
			show( parseInt( main.getAttribute( 'data-kr-open' ), 10 ) || 0 );
			lightbox.showModal();
		} );

		lightbox.querySelector( '[data-kr-lightbox-prev]' ).addEventListener( 'click', function () { show( at - 1 ); } );
		lightbox.querySelector( '[data-kr-lightbox-next]' ).addEventListener( 'click', function () { show( at + 1 ); } );

		lightbox.addEventListener( 'keydown', function ( event ) {
			if ( 'ArrowRight' === event.key ) {
				show( at + 1 );
			} else if ( 'ArrowLeft' === event.key ) {
				show( at - 1 );
			}
		} );

		lightbox.addEventListener( 'click', function ( event ) {
			if ( event.target === lightbox ) {
				lightbox.close();
			}
		} );
	} );
}() );
