/**
 * Urun gorselleri.
 *
 *  1. Urun sayfasi galerisi: kucuk gorsel buyugu degistirir; buyuk gorsele
 *     tiklayinca o gorselden baslayarak buyutme penceresi acilir.
 *  2. Buyutme penceresi: ileri/geri, klavye, Esc ve yakinlastirma.
 *
 * Gorunumun tamami Tailwind siniflarinda; burada yalnizca durum degisir.
 */
( function () {
	'use strict';

	var readGallery = function ( holder ) {
		try {
			return JSON.parse( holder.getAttribute( 'data-gallery' ) ) || [];
		} catch ( e ) {
			return [];
		}
	};

	/* ================================================================ *
	 * 1. Galeri kucukleri
	 * ================================================================ */
	document.querySelectorAll( '[data-product-gallery]' ).forEach( function ( holder ) {
		var main    = holder.querySelector( '[data-gallery-main]' );
		var image   = holder.querySelector( '[data-gallery-image]' );
		var thumbs  = holder.querySelectorAll( '[data-gallery-thumb]' );
		var gallery = readGallery( holder );

		if ( ! main || ! image || ! thumbs.length ) {
			return;
		}

		thumbs.forEach( function ( thumb ) {
			thumb.addEventListener( 'click', function () {
				var index = parseInt( thumb.getAttribute( 'data-gallery-thumb' ), 10 ) || 0;
				var item  = gallery[ index ];

				if ( ! item ) {
					return;
				}

				image.src = item.url;
				image.alt = item.alt || '';
				main.setAttribute( 'data-lightbox-open', String( index ) );

				thumbs.forEach( function ( other ) {
					other.setAttribute( 'aria-pressed', other === thumb ? 'true' : 'false' );
				} );
			} );
		} );
	} );

	/* ================================================================ *
	 * 2. Buyutme penceresi
	 * ================================================================ */
	var dialog = document.querySelector( '[data-lightbox]' );

	if ( ! dialog || 'function' !== typeof dialog.showModal ) {
		return;
	}

	var image   = dialog.querySelector( '[data-lightbox-image]' );
	var stage   = dialog.querySelector( '[data-lightbox-stage]' );
	var caption = dialog.querySelector( '[data-lightbox-caption]' );
	var counter = dialog.querySelector( '[data-lightbox-counter]' );
	var nav     = dialog.querySelector( '[data-lightbox-nav]' );
	var level   = dialog.querySelector( '[data-zoom-level]' );

	var gallery = [];
	var index   = 0;

	/* --- Yakinlastirma --- */
	var MIN  = 1;
	var MAX  = 5;
	var zoom = { scale: 1, x: 0, y: 0 };

	var applyZoom = function () {
		image.style.transform = 'translate(' + zoom.x + 'px,' + zoom.y + 'px) scale(' + zoom.scale + ')';
		level.textContent = '%' + Math.round( zoom.scale * 100 );
		stage.style.cursor = zoom.scale > 1 ? 'grab' : 'zoom-in';
	};

	// Yakinken gorsel cercevenin disina kaymasin.
	var clamp = function () {
		var box    = stage.getBoundingClientRect();
		var limitX = Math.max( 0, ( image.clientWidth * zoom.scale - box.width ) / 2 );
		var limitY = Math.max( 0, ( image.clientHeight * zoom.scale - box.height ) / 2 );

		zoom.x = Math.min( limitX, Math.max( -limitX, zoom.x ) );
		zoom.y = Math.min( limitY, Math.max( -limitY, zoom.y ) );
	};

	var setZoom = function ( value ) {
		var before = zoom.scale;
		zoom.scale = Math.min( MAX, Math.max( MIN, Math.round( value * 100 ) / 100 ) );

		if ( 1 === zoom.scale ) {
			zoom.x = 0;
			zoom.y = 0;
		} else {
			// Merkez korunsun: olcek degisince kaydirma da orantili buyusun.
			zoom.x *= zoom.scale / before;
			zoom.y *= zoom.scale / before;
			clamp();
		}

		applyZoom();
	};

	var resetZoom = function () {
		zoom = { scale: 1, x: 0, y: 0 };
		applyZoom();
	};

	dialog.querySelector( '[data-zoom-in]' ).addEventListener( 'click', function () { setZoom( zoom.scale + 0.5 ); } );
	dialog.querySelector( '[data-zoom-out]' ).addEventListener( 'click', function () { setZoom( zoom.scale - 0.5 ); } );
	dialog.querySelector( '[data-zoom-reset]' ).addEventListener( 'click', resetZoom );

	stage.addEventListener( 'wheel', function ( event ) {
		event.preventDefault();
		setZoom( zoom.scale + ( event.deltaY < 0 ? 0.35 : -0.35 ) );
	}, { passive: false } );

	/* --- Surukleme --- */
	var dragging = false;
	var dragged  = false;
	var origin   = { x: 0, y: 0 };

	stage.addEventListener( 'click', function ( event ) {
		if ( dragged ) {
			return;
		}
		event.stopPropagation();
		setZoom( zoom.scale > 1 ? 1 : 2 );
	} );

	stage.addEventListener( 'pointerdown', function ( event ) {
		if ( zoom.scale <= 1 ) {
			return;
		}
		dragging = true;
		dragged  = false;
		origin.x = event.clientX - zoom.x;
		origin.y = event.clientY - zoom.y;
		stage.setPointerCapture( event.pointerId );
		stage.style.cursor = 'grabbing';
	} );

	stage.addEventListener( 'pointermove', function ( event ) {
		if ( ! dragging ) {
			return;
		}
		zoom.x = event.clientX - origin.x;
		zoom.y = event.clientY - origin.y;
		dragged = true;
		clamp();
		applyZoom();
	} );

	var endDrag = function ( event ) {
		if ( ! dragging ) {
			return;
		}
		dragging = false;
		stage.releasePointerCapture( event.pointerId );
		applyZoom();
		// Surukleme bittiginde gelen tiklama yakinlastirmayi degistirmesin.
		window.setTimeout( function () { dragged = false; }, 0 );
	};

	stage.addEventListener( 'pointerup', endDrag );
	stage.addEventListener( 'pointercancel', endDrag );

	/* --- Gezinme --- */
	var render = function () {
		var item = gallery[ index ];

		if ( ! item ) {
			return;
		}

		image.src = item.url;
		image.alt = item.alt || '';
		caption.textContent = item.alt || '';
		counter.textContent = ( index + 1 ) + ' / ' + gallery.length;

		var many = gallery.length > 1;
		nav.hidden = ! many;
		counter.hidden = ! many;

		resetZoom();
	};

	var step = function ( delta ) {
		index = ( index + delta + gallery.length ) % gallery.length;
		render();
	};

	document.addEventListener( 'click', function ( event ) {
		var trigger = event.target.closest( '[data-lightbox-open]' );
		var holder  = trigger && trigger.closest( '[data-gallery]' );

		if ( ! holder ) {
			return;
		}

		gallery = readGallery( holder );

		if ( ! gallery.length ) {
			return;
		}

		index = parseInt( trigger.getAttribute( 'data-lightbox-open' ), 10 ) || 0;
		render();
		dialog.showModal();
	} );

	dialog.querySelector( '[data-lightbox-prev]' ).addEventListener( 'click', function () { step( -1 ); } );
	dialog.querySelector( '[data-lightbox-next]' ).addEventListener( 'click', function () { step( 1 ); } );

	dialog.addEventListener( 'keydown', function ( event ) {
		var actions = {
			ArrowRight: function () { step( 1 ); },
			ArrowLeft: function () { step( -1 ); },
			'+': function () { setZoom( zoom.scale + 0.5 ); },
			'=': function () { setZoom( zoom.scale + 0.5 ); },
			'-': function () { setZoom( zoom.scale - 0.5 ); },
			0: resetZoom
		};

		if ( actions[ event.key ] ) {
			event.preventDefault();
			actions[ event.key ]();
		}
	} );

	dialog.addEventListener( 'close', resetZoom );

	// Gorselin disina tiklayinca kapansin.
	dialog.addEventListener( 'click', function ( event ) {
		if ( event.target === dialog ) {
			dialog.close();
		}
	} );
}() );
