/**
 * Urun gorseli buyutme penceresi.
 *
 * Tetikleyici: galerideki [data-k-zoom] dugmesi (kocist_zoom_button()).
 * Galeride kucuk gorseller varsa hepsi sirayla gezilir ve pencere o an
 * buyuk alanda duran gorselden acilir; yoksa yalnizca dugmenin data-full
 * gorseli acilir. Kapaninca galeri, pencerede en son bakilan gorsele gecer.
 *
 * Yerel <dialog>: odak pencerenin icinde kalir, Esc kapatir. Ayrica arka
 * plana tiklama, kapat dugmesi, sag/sol ok tuslari, dokunmatik kaydirma,
 * sayfa kaydirma kilidi ve kapaninca odagin dugmeye donmesi.
 *
 * Panel onizlemesinde bu dosya yuklenmez (functions.php).
 */
( function () {
	'use strict';

	var triggers = Array.prototype.slice.call( document.querySelectorAll( '[data-k-zoom]' ) );

	if ( ! triggers.length || 'function' !== typeof window.HTMLDialogElement ) {
		return;
	}

	// Dugme CSS'te bu sinif olmadan gizli: betik yoksa ise yaramaz dugme gorunmez.
	document.documentElement.classList.add( 'k-lb-ready' );

	var dialog, img, caption, counter, prevBtn, nextBtn;
	var items   = [];
	var index   = 0;
	var opener  = null;
	var gallery = null;

	function icon( path ) {
		return '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true"><path d="' + path + '" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>';
	}

	function build() {
		dialog = document.createElement( 'dialog' );
		dialog.className = 'k-lb';
		dialog.setAttribute( 'aria-label', 'Görsel büyütme' );
		dialog.innerHTML =
			'<div class="k-lb__bar">' +
				'<span class="k-lb__count" aria-live="polite"></span>' +
				'<button type="button" class="k-lb__btn k-lb__close" data-k-lb-close>' + icon( 'M5.5 5.5l11 11M16.5 5.5l-11 11' ) + '<span class="screen-reader-text">Kapat</span></button>' +
			'</div>' +
			'<figure class="k-lb__figure">' +
				'<img class="k-lb__img" alt="" decoding="async" />' +
				'<figcaption class="k-lb__caption"></figcaption>' +
			'</figure>' +
			'<button type="button" class="k-lb__btn k-lb__nav k-lb__nav--prev" data-k-lb-prev>' + icon( 'M13.5 4.5 7 11l6.5 6.5' ) + '<span class="screen-reader-text">Önceki görsel</span></button>' +
			'<button type="button" class="k-lb__btn k-lb__nav k-lb__nav--next" data-k-lb-next>' + icon( 'M8.5 4.5 15 11l-6.5 6.5' ) + '<span class="screen-reader-text">Sonraki görsel</span></button>';

		document.body.appendChild( dialog );

		img     = dialog.querySelector( '.k-lb__img' );
		caption = dialog.querySelector( '.k-lb__caption' );
		counter = dialog.querySelector( '.k-lb__count' );
		prevBtn = dialog.querySelector( '[data-k-lb-prev]' );
		nextBtn = dialog.querySelector( '[data-k-lb-next]' );

		dialog.querySelector( '[data-k-lb-close]' ).addEventListener( 'click', close );
		prevBtn.addEventListener( 'click', function () { show( index - 1 ); } );
		nextBtn.addEventListener( 'click', function () { show( index + 1 ); } );

		// Arka plan: gorsel ve dugmeler disinda bir yere tiklamak kapatir.
		dialog.addEventListener( 'click', function ( event ) {
			if ( event.target === dialog || event.target.classList.contains( 'k-lb__figure' ) || event.target.classList.contains( 'k-lb__bar' ) ) {
				close();
			}
		} );

		// Esc: tarayicinin kendi kapatmasi yerine ayni kapanis yolu.
		dialog.addEventListener( 'cancel', function ( event ) {
			event.preventDefault();
			close();
		} );

		dialog.addEventListener( 'keydown', function ( event ) {
			if ( 'ArrowLeft' === event.key ) {
				event.preventDefault();
				show( index - 1 );
			} else if ( 'ArrowRight' === event.key ) {
				event.preventDefault();
				show( index + 1 );
			}
		} );

		var startX = null;

		dialog.addEventListener( 'touchstart', function ( event ) {
			startX = event.changedTouches[ 0 ].clientX;
		}, { passive: true } );

		dialog.addEventListener( 'touchend', function ( event ) {
			if ( null === startX ) {
				return;
			}

			var delta = event.changedTouches[ 0 ].clientX - startX;

			if ( Math.abs( delta ) > 50 ) {
				show( index + ( delta < 0 ? 1 : -1 ) );
			}

			startX = null;
		}, { passive: true } );
	}

	function show( target ) {
		index = ( target + items.length ) % items.length;

		var item = items[ index ];

		dialog.classList.add( 'is-loading' );
		img.onload = img.onerror = function () {
			dialog.classList.remove( 'is-loading' );
		};
		img.src = item.src;
		img.alt = item.alt;

		caption.textContent = item.alt;
		caption.hidden      = ! item.alt;

		var many = items.length > 1;

		counter.textContent = many ? ( index + 1 ) + ' / ' + items.length : '';
		prevBtn.hidden      = ! many;
		nextBtn.hidden      = ! many;
	}

	/**
	 * Tetikleyicinin galerisindeki gorseller ve o an gosterilenin sirasi.
	 */
	function collect( trigger ) {
		gallery = trigger.closest( '[data-k-gallery]' );

		var thumbs = gallery ? Array.prototype.slice.call( gallery.querySelectorAll( '[data-k-thumb]' ) ) : [];
		var list   = [];
		var start  = 0;

		thumbs.forEach( function ( thumb ) {
			var src = thumb.getAttribute( 'data-full' );

			if ( src ) {
				if ( thumb.classList.contains( 'is-active' ) ) {
					start = list.length;
				}

				list.push( { src: src, alt: thumb.getAttribute( 'data-alt' ) || '', thumb: thumb } );
			}
		} );

		if ( ! list.length ) {
			list.push( { src: trigger.getAttribute( 'data-full' ), alt: trigger.getAttribute( 'data-alt' ) || '', thumb: null } );
		}

		return { list: list, start: start };
	}

	function open( trigger ) {
		if ( ! dialog ) {
			build();
		}

		var found = collect( trigger );

		items  = found.list;
		opener = trigger;

		show( found.start );

		// Kaydirma kilidi; kaydirma cubugu kaybolunca sayfa yana kaymasin.
		var gap = window.innerWidth - document.documentElement.clientWidth;

		document.documentElement.classList.add( 'k-lb-lock' );
		document.documentElement.style.setProperty( '--k-lb-gap', gap + 'px' );

		dialog.showModal();
		dialog.querySelector( '[data-k-lb-close]' ).focus();
	}

	function close() {
		if ( ! dialog || ! dialog.open ) {
			return;
		}

		dialog.close();
		document.documentElement.classList.remove( 'k-lb-lock' );
		document.documentElement.style.removeProperty( '--k-lb-gap' );

		// Galeri pencerede en son bakilan gorselde kalsin (product.js dinler).
		var last = items[ index ];

		if ( last && last.thumb && ! last.thumb.classList.contains( 'is-active' ) ) {
			last.thumb.click();
		}

		if ( opener ) {
			opener.focus();
		}
	}

	triggers.forEach( function ( trigger ) {
		trigger.addEventListener( 'click', function () {
			open( trigger );
		} );
	} );
}() );
