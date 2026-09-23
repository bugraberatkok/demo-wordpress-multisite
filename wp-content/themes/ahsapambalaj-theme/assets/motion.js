/**
 * Kaydirma hareketleri: belirme, sayac ve hero parallax.
 *
 * Hepsi data-* nitelikleriyle baglanir:
 *   data-reveal               goruldugunde yerine oturur
 *   data-reveal-stagger       cocuklarindaki data-reveal'lara sirayla gecikme verir
 *   data-count="1200"         goruldugunde 0'dan sayar (data-count-suffix="+")
 *   data-parallax             hero gorunurken kaydirmaya gore hafifce kayar
 *   data-progress             menudeki okuma cizgisi (--progress: 0..1)
 *
 * Hareket azaltma tercihinde hicbiri calismaz; icerik son haliyle gorunur.
 */
( function () {
	'use strict';

	var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var format = new Intl.NumberFormat( 'tr-TR' );

	/* ---- Sayac ---------------------------------------------------------- */

	function writeCount( el, value ) {
		el.textContent = format.format( value ) + ( el.getAttribute( 'data-count-suffix' ) || '' );
	}

	function runCount( el ) {
		var target = parseInt( el.getAttribute( 'data-count' ), 10 ) || 0;
		var duration = 1400;
		var start = null;

		if ( reduce ) {
			writeCount( el, target );
			return;
		}

		function step( now ) {
			if ( null === start ) {
				start = now;
			}

			var t = Math.min( ( now - start ) / duration, 1 );
			var eased = 1 - Math.pow( 1 - t, 3 );

			writeCount( el, Math.round( target * eased ) );

			if ( t < 1 ) {
				requestAnimationFrame( step );
			}
		}

		requestAnimationFrame( step );
	}

	/* ---- Belirme ve sayac gozlemcisi ------------------------------------ */

	document.querySelectorAll( '[data-reveal-stagger]' ).forEach( function ( group ) {
		group.querySelectorAll( '[data-reveal]' ).forEach( function ( el, index ) {
			el.style.setProperty( '--reveal-delay', ( index * 90 ) + 'ms' );
		} );
	} );

	var revealables = document.querySelectorAll( '[data-reveal]' );
	var counters = document.querySelectorAll( '[data-count]' );

	// Icerik varsayilan olarak gorunur. Gizleme (.js [data-reveal]) ancak bu
	// betik calisinca devreye girer ve o anda ekranda ya da yukarida kalan
	// ogeler once acilir; boylece betik gec gelse bile gorunen bir sey
	// kaybolmaz, yalnizca ekranin altindakiler kaydirinca belirir.
	revealables.forEach( function ( el ) {
		if ( el.getBoundingClientRect().top < window.innerHeight ) {
			el.classList.add( 'is-revealed' );
		}
	} );
	document.documentElement.classList.add( 'js' );

	if ( reduce || ! ( 'IntersectionObserver' in window ) ) {
		revealables.forEach( function ( el ) {
			el.classList.add( 'is-revealed' );
		} );
		counters.forEach( function ( el ) {
			writeCount( el, parseInt( el.getAttribute( 'data-count' ), 10 ) || 0 );
		} );
	} else {
		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}

				var el = entry.target;

				if ( el.hasAttribute( 'data-count' ) ) {
					runCount( el );
				} else {
					el.classList.add( 'is-revealed' );
				}

				observer.unobserve( el );
			} );
		}, { rootMargin: '0px 0px -5% 0px', threshold: 0 } );

		revealables.forEach( function ( el ) {
			observer.observe( el );
		} );

		// Emniyet: gozlemci bir ogeyi kacirirsa (hizli kaydirma, sayfa sonuna
		// ziplama, tam sayfa ekran goruntusu icin pencere buyutme) kaydirma ve
		// boyut degisiminde ekranin ustunde kalan her oge yine acilir.
		var sweepQueued = false;

		var sweep = function () {
			sweepQueued = false;

			document.querySelectorAll( '[data-reveal]:not(.is-revealed)' ).forEach( function ( el ) {
				if ( el.getBoundingClientRect().top < window.innerHeight ) {
					el.classList.add( 'is-revealed' );
					observer.unobserve( el );
				}
			} );
		};

		var queueSweep = function () {
			if ( ! sweepQueued ) {
				sweepQueued = true;
				requestAnimationFrame( sweep );
			}
		};

		window.addEventListener( 'scroll', queueSweep, { passive: true } );
		window.addEventListener( 'resize', queueSweep );
		window.addEventListener( 'load', queueSweep );
		counters.forEach( function ( el ) {
			writeCount( el, 0 );
			observer.observe( el );
		} );
	}

	/* ---- Okuma cizgisi -------------------------------------------------- */

	var progress = document.querySelector( '[data-progress]' );

	if ( progress ) {
		var progressTicking = false;

		var paintProgress = function () {
			progressTicking = false;

			var max = document.documentElement.scrollHeight - window.innerHeight;
			var ratio = max > 0 ? Math.min( window.scrollY / max, 1 ) : 0;

			progress.style.setProperty( '--progress', ratio.toFixed( 4 ) );
		};

		window.addEventListener( 'scroll', function () {
			if ( ! progressTicking ) {
				progressTicking = true;
				requestAnimationFrame( paintProgress );
			}
		}, { passive: true } );

		window.addEventListener( 'resize', paintProgress );
		paintProgress();
	}

	/* ---- Hero parallax -------------------------------------------------- */

	var layer = document.querySelector( '[data-parallax]' );
	var coarse = window.matchMedia( '(pointer: coarse)' ).matches;

	if ( ! layer || reduce || coarse ) {
		return;
	}

	var section = layer.closest( 'section' ) || layer.parentElement;
	var ticking = false;

	function update() {
		ticking = false;

		var rect = section.getBoundingClientRect();

		if ( rect.bottom < 0 || rect.top > window.innerHeight ) {
			return;
		}

		layer.style.setProperty( '--parallax', ( -rect.top * 0.15 ).toFixed( 1 ) + 'px' );
	}

	window.addEventListener( 'scroll', function () {
		if ( ! ticking ) {
			ticking = true;
			requestAnimationFrame( update );
		}
	}, { passive: true } );

	update();
}() );
