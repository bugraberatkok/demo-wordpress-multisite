/**
 * Urun gami kartlarinin giris animasyonu.
 *
 * Kartlar ekrana girdikce sirayla yukselir. Siralama CSS tarafinda --i
 * degiskeninden gelen transition-delay ile yapiliyor; buradaki is yalnizca
 * dogru anda .is-in sinifini eklemek.
 *
 * Animasyon bir kez calisir: kullanici yukari asagi kaydirdikca kartlarin
 * tekrar tekrar belirmesi rahatsiz edici olurdu.
 */
( function () {
	'use strict';

	var container = document.querySelector( '[data-k-groups]' );

	if ( ! container ) {
		return;
	}

	// Bolum etiketi de ayni gozlemciyle beliriyor; kartlarin disinda durdugu
	// icin ayrica araniyor.
	var section = container.closest( 'section' ) || document;
	var targets = Array.prototype.slice.call( container.querySelectorAll( '[data-k-group]' ) );
	var label   = section.querySelector( '[data-k-reveal]' );

	if ( label ) {
		targets.unshift( label );
	}

	if ( ! targets.length ) {
		return;
	}

	// IntersectionObserver yoksa kartlari gizli birakma: dogrudan goster.
	if ( ! window.IntersectionObserver ) {
		document.body.classList.add( 'no-js-groups' );
		return;
	}

	var observer = new window.IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}

				entry.target.classList.add( 'is-in' );
				observer.unobserve( entry.target );
			} );
		},
		{
			// Kart tam gorunur olmadan, ucte biri girdiginde baslasin.
			threshold: 0.3,
			rootMargin: '0px 0px -40px 0px',
		}
	);

	targets.forEach( function ( target ) {
		observer.observe( target );
	} );
}() );
