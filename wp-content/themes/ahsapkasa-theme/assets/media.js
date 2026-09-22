/**
 * Iki is yapar:
 *  1. Hero slayt gosterisi (capraz gecis, noktalar, otomatik ilerleme).
 *  2. Urun gorsellerini buyuten pencere: ileri/geri, klavye, Esc.
 *
 * Gorunumun tamami Tailwind siniflarinda; burada yalnizca durum degisir.
 */
( function () {
	'use strict';

	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* ================================================================ *
	 * 1. Hero slayt gosterisi
	 * ================================================================ */
	( function () {
		var stage = document.querySelector( '[data-slideshow]' );

		if ( ! stage ) {
			return;
		}

		var slides = Array.prototype.slice.call( stage.querySelectorAll( '[data-slide]' ) );
		var dots   = Array.prototype.slice.call( document.querySelectorAll( '[data-slide-dot]' ) );

		if ( slides.length < 2 ) {
			return;
		}

		var current = 0;
		var timer   = null;
		var DELAY   = 6000;

		var show = function ( index ) {
			current = ( index + slides.length ) % slides.length;

			slides.forEach( function ( slide, i ) {
				slide.classList.toggle( 'opacity-100', i === current );
				slide.classList.toggle( 'opacity-0', i !== current );
			} );

			dots.forEach( function ( dot, i ) {
				dot.setAttribute( 'aria-selected', i === current ? 'true' : 'false' );
			} );
		};

		var stop = function () {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
		};

		var start = function () {
			if ( reduceMotion ) {
				return;
			}
			stop();
			timer = window.setInterval( function () {
				show( current + 1 );
			}, DELAY );
		};

		dots.forEach( function ( dot, i ) {
			dot.addEventListener( 'click', function () {
				show( i );
				start();
			} );
		} );

		// Fare uzerindeyken ya da odak icerideyken beklesin.
		var section = stage.closest( 'section' ) || stage;
		section.addEventListener( 'mouseenter', stop );
		section.addEventListener( 'mouseleave', start );
		section.addEventListener( 'focusin', stop );
		section.addEventListener( 'focusout', start );

		// Sekme arka plandayken bosuna donmesin.
		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) {
				stop();
			} else {
				start();
			}
		} );

		start();
	}() );

	/* ================================================================ *
	 * 2. Gorsel buyutme penceresi
	 * ================================================================ */
	( function () {
		var dialog = document.querySelector( '[data-lightbox]' );

		if ( ! dialog || 'function' !== typeof dialog.showModal ) {
			return;
		}

		var image   = dialog.querySelector( '[data-lightbox-image]' );
		var stage   = dialog.querySelector( '[data-lightbox-stage]' );
		var caption = dialog.querySelector( '[data-lightbox-caption]' );
		var counter = dialog.querySelector( '[data-lightbox-counter]' );
		var prev    = dialog.querySelector( '[data-lightbox-prev]' );
		var next    = dialog.querySelector( '[data-lightbox-next]' );
		var nav     = dialog.querySelector( '[data-lightbox-nav]' );
		var level   = dialog.querySelector( '[data-zoom-level]' );

		var gallery = [];
		var index   = 0;

		/* --- Yakinlastirma durumu --- */
		var MIN = 1;
		var MAX = 5;
		var zoom = { scale: 1, x: 0, y: 0 };

		var applyZoom = function () {
			image.style.transform = 'translate(' + zoom.x + 'px,' + zoom.y + 'px) scale(' + zoom.scale + ')';
			level.textContent = '%' + Math.round( zoom.scale * 100 );
			stage.style.cursor = zoom.scale > 1 ? 'grab' : 'zoom-in';
		};

		// Yakinlasinca gorsel cercevenin disina tasmasin.
		var clamp = function () {
			var box = stage.getBoundingClientRect();
			var w   = image.clientWidth * zoom.scale;
			var h   = image.clientHeight * zoom.scale;

			// Yatayda gorsel cerceveden genisse kaydirma payi kadar, degilse ortalanir.
			var limitX = Math.max( 0, ( w - box.width ) / 2 );
			var limitY = Math.max( 0, ( h - box.height ) / 2 );

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
				// Merkezi koru: olcek degisince kaydirmayi orantili buyut.
				zoom.x = zoom.x * ( zoom.scale / before );
				zoom.y = zoom.y * ( zoom.scale / before );
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

		// Tekerlekle yakinlastirma.
		stage.addEventListener( 'wheel', function ( event ) {
			event.preventDefault();
			setZoom( zoom.scale + ( event.deltaY < 0 ? 0.35 : -0.35 ) );
		}, { passive: false } );

		// Tiklayinca 1x <-> 2x.
		stage.addEventListener( 'click', function ( event ) {
			if ( dragged ) {
				return;
			}

			event.stopPropagation();
			setZoom( zoom.scale > 1 ? 1 : 2 );
		} );

		/* --- Yakinken surukleyerek gezinme --- */
		var dragging = false;
		var dragged  = false;
		var origin   = { x: 0, y: 0 };

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

			// Surukleme bittiginde gelen tiklamayi yut.
			window.setTimeout( function () { dragged = false; }, 0 );
		};

		stage.addEventListener( 'pointerup', endDrag );
		stage.addEventListener( 'pointercancel', endDrag );

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

			if ( ! trigger ) {
				return;
			}

			var holder = trigger.closest( '[data-gallery]' );

			if ( ! holder ) {
				return;
			}

			try {
				gallery = JSON.parse( holder.getAttribute( 'data-gallery' ) ) || [];
			} catch ( e ) {
				gallery = [];
			}

			if ( ! gallery.length ) {
				return;
			}

			index = parseInt( trigger.getAttribute( 'data-lightbox-open' ), 10 ) || 0;
			render();
			dialog.showModal();
		} );

		prev.addEventListener( 'click', function () { step( -1 ); } );
		next.addEventListener( 'click', function () { step( 1 ); } );

		dialog.addEventListener( 'keydown', function ( event ) {
			if ( 'ArrowRight' === event.key ) {
				event.preventDefault();
				step( 1 );
			} else if ( 'ArrowLeft' === event.key ) {
				event.preventDefault();
				step( -1 );
			} else if ( '+' === event.key || '=' === event.key ) {
				event.preventDefault();
				setZoom( zoom.scale + 0.5 );
			} else if ( '-' === event.key ) {
				event.preventDefault();
				setZoom( zoom.scale - 0.5 );
			} else if ( '0' === event.key ) {
				event.preventDefault();
				resetZoom();
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
}() );
