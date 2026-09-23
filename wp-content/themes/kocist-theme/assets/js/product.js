/**
 * Urun sayfasi: galeri degistirici ve sikca sorulan sorular akordiyonu.
 */
( function () {
	'use strict';

	/* ---------- galeri ---------- */

	var gallery = document.querySelector( '[data-k-gallery]' );

	if ( gallery ) {
		var stage   = gallery.querySelector( '.k-product__stage' );
		var photo   = gallery.querySelector( '.k-product__photo' );
		var thumbs  = Array.prototype.slice.call( gallery.querySelectorAll( '[data-k-thumb]' ) );
		var prev    = gallery.querySelector( '[data-k-gallery-prev]' );
		var next    = gallery.querySelector( '[data-k-gallery-next]' );
		var counter = gallery.querySelector( '[data-k-gallery-counter]' );

		// Yer tutucu basiliysa degistirilecek bir <img> yoktur.
		if ( stage && photo && thumbs.length ) {
			var current = 0;

			/**
			 * @param {number} index Gosterilecek gorselin sirasi.
			 */
			function show( index ) {
				// Bas ve son arasinda dolanir: oklar hicbir zaman cikmaza girmez.
				var target = ( index + thumbs.length ) % thumbs.length;
				var thumb  = thumbs[ target ];
				var full   = thumb.getAttribute( 'data-full' );

				if ( ! full ) {
					return;
				}

				thumbs.forEach( function ( other, otherIndex ) {
					other.classList.toggle( 'is-active', otherIndex === target );
				} );

				if ( counter ) {
					counter.textContent = ( target + 1 ) + ' / ' + thumbs.length;
				}

				current = target;

				if ( full === photo.getAttribute( 'src' ) ) {
					return;
				}

				// Once soluklastir, gorsel yuklenince geri getir: yarim
				// yuklenmis gorsel ekranda gorunmesin.
				stage.classList.add( 'is-swapping' );

				var loader = new window.Image();

				loader.onload = function () {
					photo.src = full;
					photo.alt = thumb.getAttribute( 'data-alt' ) || '';
					stage.classList.remove( 'is-swapping' );
				};

				loader.onerror = function () {
					stage.classList.remove( 'is-swapping' );
				};

				loader.src = full;
			}

			thumbs.forEach( function ( thumb, index ) {
				thumb.addEventListener( 'click', function () {
					show( index );
				} );
			} );

			if ( prev ) {
				prev.addEventListener( 'click', function () {
					show( current - 1 );
				} );
			}

			if ( next ) {
				next.addEventListener( 'click', function () {
					show( current + 1 );
				} );
			}

			// Klavye: galeri odaktayken sag/sol oklar da calisir.
			gallery.addEventListener( 'keydown', function ( event ) {
				if ( 'ArrowLeft' === event.key ) {
					event.preventDefault();
					show( current - 1 );
				} else if ( 'ArrowRight' === event.key ) {
					event.preventDefault();
					show( current + 1 );
				}
			} );

			/* ---------- dokunmatik kaydirma ---------- */

			var startX = null;

			stage.addEventListener( 'touchstart', function ( event ) {
				startX = event.changedTouches[ 0 ].clientX;
			}, { passive: true } );

			stage.addEventListener( 'touchend', function ( event ) {
				if ( null === startX ) {
					return;
				}

				var delta = event.changedTouches[ 0 ].clientX - startX;

				// Kazara dokunuslari gorsel degisimi saymamak icin esik.
				if ( Math.abs( delta ) > 44 ) {
					show( current + ( delta < 0 ? 1 : -1 ) );
				}

				startX = null;
			}, { passive: true } );
		}
	}

	/* ---------- sikca sorulan sorular ---------- */

	var faq = document.querySelector( '[data-k-faq]' );

	if ( ! faq ) {
		return;
	}

	// Kapali hal CSS'te bu sinifa bagli: betik calismazsa cevaplar acik kalir.
	faq.classList.add( 'is-ready' );

	var items = Array.prototype.slice.call( faq.querySelectorAll( '[data-k-faq-item]' ) );

	items.forEach( function ( item ) {
		var toggle = item.querySelector( '[data-k-faq-toggle]' );

		if ( ! toggle ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var isOpen = item.classList.contains( 'is-open' );

			// Tek seferde tek soru acik: liste uzun, hepsi acilirsa
			// kullanici nerede oldugunu kaybediyor.
			items.forEach( function ( other ) {
				other.classList.remove( 'is-open' );

				var otherToggle = other.querySelector( '[data-k-faq-toggle]' );

				if ( otherToggle ) {
					otherToggle.setAttribute( 'aria-expanded', 'false' );
				}
			} );

			if ( ! isOpen ) {
				item.classList.add( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'true' );
			}
		} );
	} );
}() );
