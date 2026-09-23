/**
 * Hero sag kartindaki slayt.
 *
 * Slaytlar ust uste durur; gecis opaklik + gidis yonune gore kayma ile yapilir.
 * Ok tuslari, noktalar, klavye ve dokunmatik kaydirma desteklenir.
 * Otomatik ilerleme yok: kullanici hangi urunu okudugunu kaybetmesin.
 */
( function () {
	'use strict';

	var slider = document.querySelector( '[data-k-slider]' );

	if ( ! slider ) {
		return;
	}

	var slides = Array.prototype.slice.call( slider.querySelectorAll( '[data-k-slide]' ) );
	var dots   = Array.prototype.slice.call( slider.querySelectorAll( '[data-k-slider-dot]' ) );
	var prev   = slider.querySelector( '[data-k-slider-prev]' );
	var next   = slider.querySelector( '[data-k-slider-next]' );

	if ( slides.length < 2 ) {
		return;
	}

	var current = 0;

	/**
	 * @param {number} index Gidilecek slayt.
	 * @param {number} direction 1 ileri, -1 geri. Kayma yonunu belirler.
	 */
	function show( index, direction ) {
		// Bas ve son arasinda dolanir.
		var target = ( index + slides.length ) % slides.length;

		if ( target === current ) {
			return;
		}

		var offset  = 26 * direction;
		var leaving = slides[ current ];
		var arriving = slides[ target ];

		leaving.style.setProperty( '--k-slide-x', -offset + 'px' );
		leaving.classList.remove( 'is-active' );
		leaving.setAttribute( 'aria-hidden', 'true' );

		arriving.style.setProperty( '--k-slide-x', offset + 'px' );

		// Baslangic konumunu ayni karede uygula ki gecis oradan baslasin.
		arriving.getBoundingClientRect();

		arriving.classList.add( 'is-active' );
		arriving.removeAttribute( 'aria-hidden' );

		dots.forEach( function ( dot, dotIndex ) {
			dot.classList.toggle( 'is-active', dotIndex === target );
		} );

		current = target;
	}

	if ( prev ) {
		prev.addEventListener( 'click', function () {
			show( current - 1, -1 );
		} );
	}

	if ( next ) {
		next.addEventListener( 'click', function () {
			show( current + 1, 1 );
		} );
	}

	dots.forEach( function ( dot, index ) {
		dot.addEventListener( 'click', function () {
			show( index, index > current ? 1 : -1 );
		} );
	} );

	slider.addEventListener( 'keydown', function ( event ) {
		if ( 'ArrowLeft' === event.key ) {
			event.preventDefault();
			show( current - 1, -1 );
		} else if ( 'ArrowRight' === event.key ) {
			event.preventDefault();
			show( current + 1, 1 );
		}
	} );

	/* ---------- dokunmatik kaydirma ---------- */

	var startX = null;

	slider.addEventListener( 'touchstart', function ( event ) {
		startX = event.changedTouches[ 0 ].clientX;
	}, { passive: true } );

	slider.addEventListener( 'touchend', function ( event ) {
		if ( null === startX ) {
			return;
		}

		var delta = event.changedTouches[ 0 ].clientX - startX;

		// Kazara dokunuslari slayt degisimi saymamak icin esik.
		if ( Math.abs( delta ) > 44 ) {
			show( current + ( delta < 0 ? 1 : -1 ), delta < 0 ? 1 : -1 );
		}

		startX = null;
	}, { passive: true } );
}() );
