/*
 * Katalog sayfasi: sekmeler ve sertifika buyuk gorunumu.
 *
 * Betik yoksa sekmeler ?tab= baglantisiyla, gorseller dosyanin kendisiyle
 * acilir; betik bunlari sayfa icinde yapar.
 */
( function () {
	var root = document.querySelector( '[data-k-katalog]' );

	if ( ! root ) {
		return;
	}

	/* ---------- Sekmeler ---------- */

	var tabs   = Array.prototype.slice.call( root.querySelectorAll( '[data-k-tab]' ) );
	var panels = Array.prototype.slice.call( root.querySelectorAll( '[data-k-panel]' ) );

	function select( key, focus ) {
		tabs.forEach( function ( tab ) {
			var on = tab.getAttribute( 'data-k-tab' ) === key;

			tab.setAttribute( 'aria-selected', on ? 'true' : 'false' );
			tab.setAttribute( 'tabindex', on ? '0' : '-1' );

			if ( on && focus ) {
				tab.focus();
			}
		} );

		panels.forEach( function ( panel ) {
			panel.hidden = panel.getAttribute( 'data-k-panel' ) !== key;
		} );

		// Adres de sekmeyi tasisin: paylasilan baglanti ayni sekmeyi acar.
		if ( window.history && window.history.replaceState ) {
			var url = new URL( window.location.href );
			url.searchParams.set( 'tab', key );
			window.history.replaceState( null, '', url );
		}
	}

	tabs.forEach( function ( tab, index ) {
		tab.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			select( tab.getAttribute( 'data-k-tab' ), false );
		} );

		tab.addEventListener( 'keydown', function ( event ) {
			var step = 'ArrowRight' === event.key ? 1 : 'ArrowLeft' === event.key ? -1 : 0;

			if ( step ) {
				event.preventDefault();
				select( tabs[ ( index + step + tabs.length ) % tabs.length ].getAttribute( 'data-k-tab' ), true );
			}
		} );
	} );

	/* ---------- Buyuk gorunum ---------- */

	var dialog  = root.querySelector( '[data-k-zoom-dialog]' );
	var links   = Array.prototype.slice.call( root.querySelectorAll( '[data-k-zoom]' ) );

	if ( ! dialog || ! links.length || 'function' !== typeof dialog.showModal ) {
		return;
	}

	var img     = dialog.querySelector( '[data-k-zoom-img]' );
	var caption = dialog.querySelector( '[data-k-zoom-caption]' );
	var current = 0;
	var opener  = null;

	function show( index ) {
		current = ( index + links.length ) % links.length;

		var link = links[ current ];
		var text = link.getAttribute( 'data-k-caption' ) || '';

		img.src             = link.getAttribute( 'href' );
		img.alt             = text;
		caption.textContent = text;
	}

	links.forEach( function ( link, index ) {
		link.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			opener = link;
			show( index );
			dialog.showModal();
		} );
	} );

	dialog.querySelector( '[data-k-zoom-prev]' ).addEventListener( 'click', function () {
		show( current - 1 );
	} );

	dialog.querySelector( '[data-k-zoom-next]' ).addEventListener( 'click', function () {
		show( current + 1 );
	} );

	dialog.querySelector( '[data-k-zoom-close]' ).addEventListener( 'click', function () {
		dialog.close();
	} );

	dialog.addEventListener( 'keydown', function ( event ) {
		if ( 'ArrowLeft' === event.key ) {
			show( current - 1 );
		} else if ( 'ArrowRight' === event.key ) {
			show( current + 1 );
		}
	} );

	// Gorselin disina (arka plana) tiklayinca kapanir.
	dialog.addEventListener( 'click', function ( event ) {
		if ( event.target === dialog ) {
			dialog.close();
		}
	} );

	// Kapaninca odak acan karta doner.
	dialog.addEventListener( 'close', function () {
		img.removeAttribute( 'src' );

		if ( opener ) {
			opener.focus();
		}
	} );
}() );
