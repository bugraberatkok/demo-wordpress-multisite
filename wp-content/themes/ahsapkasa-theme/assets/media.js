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
		var caption = dialog.querySelector( '[data-lightbox-caption]' );
		var counter = dialog.querySelector( '[data-lightbox-counter]' );
		var prev    = dialog.querySelector( '[data-lightbox-prev]' );
		var next    = dialog.querySelector( '[data-lightbox-next]' );
		var nav     = dialog.querySelector( '[data-lightbox-nav]' );

		var gallery = [];
		var index   = 0;

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
			}
		} );

		// Gorselin disina tiklayinca kapansin.
		dialog.addEventListener( 'click', function ( event ) {
			if ( event.target === dialog ) {
				dialog.close();
			}
		} );
	}() );
}() );
