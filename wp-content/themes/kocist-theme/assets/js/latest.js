/*
 * Son eklenen urunler seridi: oklar, ilerleme cizgisi, fareyle surukleme.
 *
 * Kaydirmanin kendisi tarayicinin (overflow + scroll-snap); betik yalnizca
 * yardimci. Betik calismazsa serit dokunmatik/tekerlekle yine kayar, oklar
 * gizli kalir.
 */
( function () {
	document.querySelectorAll( '.k-latest' ).forEach( function ( section ) {
		var track = section.querySelector( '[data-k-latest-track]' );
		var nav   = section.querySelector( '[data-k-latest-nav]' );
		var prev  = section.querySelector( '[data-k-latest-prev]' );
		var next  = section.querySelector( '[data-k-latest-next]' );
		var bar   = section.querySelector( '[data-k-latest-bar]' );

		if ( ! track ) {
			return;
		}

		// Bir adim: gorunen tam kart sayisi kadar (en az bir kart).
		function step() {
			var item = track.querySelector( '.k-latest__item' );
			var gap  = parseFloat( getComputedStyle( track ).columnGap ) || 0;
			var size = item ? item.getBoundingClientRect().width + gap : track.clientWidth;

			return Math.max( 1, Math.floor( ( track.clientWidth - size * 0.5 ) / size ) ) * size;
		}

		function update() {
			var max = track.scrollWidth - track.clientWidth;
			var pos = track.scrollLeft;

			if ( nav ) {
				nav.hidden = max <= 1;
			}
			if ( prev ) {
				prev.disabled = pos <= 1;
			}
			if ( next ) {
				next.disabled = pos >= max - 1;
			}
			if ( bar ) {
				// Gorunen kisim + kaydirilan yol: bastan dolu baslar, sonda tamamlanir.
				var ratio = track.scrollWidth ? ( pos + track.clientWidth ) / track.scrollWidth : 1;
				bar.style.width = Math.min( 100, ratio * 100 ) + '%';
			}
		}

		if ( prev ) {
			prev.addEventListener( 'click', function () {
				track.scrollBy( { left: -step() } );
			} );
		}
		if ( next ) {
			next.addEventListener( 'click', function () {
				track.scrollBy( { left: step() } );
			} );
		}

		track.addEventListener( 'scroll', update, { passive: true } );
		window.addEventListener( 'resize', update );

		/*
		 * Fareyle surukleme. Dokunmatikte tarayicinin kendi kaydirmasi
		 * kullanilir. Birkac pikselden fazla surukleyince tiklama iptal:
		 * kart birakildigi anda acilmasin.
		 */
		var startX   = 0;
		var startPos = 0;
		var moved    = false;
		var down     = false;

		track.addEventListener( 'pointerdown', function ( e ) {
			if ( 'mouse' !== e.pointerType || 0 !== e.button ) {
				return;
			}
			down     = true;
			moved    = false;
			startX   = e.clientX;
			startPos = track.scrollLeft;
		} );

		window.addEventListener( 'pointermove', function ( e ) {
			if ( ! down ) {
				return;
			}
			var dx = e.clientX - startX;

			if ( ! moved && Math.abs( dx ) > 6 ) {
				moved = true;
				track.classList.add( 'is-dragging' );
			}
			if ( moved ) {
				track.scrollLeft = startPos - dx;
			}
		} );

		window.addEventListener( 'pointerup', function () {
			if ( ! down ) {
				return;
			}
			down = false;

			if ( moved ) {
				// Miknatis geri acilinca serit en yakin karta oturur.
				track.classList.remove( 'is-dragging' );
				var item = track.querySelector( '.k-latest__item' );
				var gap  = parseFloat( getComputedStyle( track ).columnGap ) || 0;
				var size = item ? item.getBoundingClientRect().width + gap : 1;
				track.scrollTo( { left: Math.round( track.scrollLeft / size ) * size } );
			}
		} );

		track.addEventListener( 'click', function ( e ) {
			if ( moved ) {
				e.preventDefault();
				moved = false;
			}
		}, true );

		update();
	} );
}() );
