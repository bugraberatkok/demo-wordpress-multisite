/**
 * Ust menu davranisi.
 *
 *  1. Sayfa kaydirilinca serit incelir (data-stuck).
 *  2. "Urunlerimiz" acilir paneli: fareyle uzerine gelince, dugmeyle ya da
 *     klavyeyle acilir; Esc, disari tiklama ve odak cikisi kapatir.
 *  3. Mobil panel ve icindeki urun akordeonu.
 *
 * Gorunumun tamami Tailwind siniflarinda; burada yalnizca durum degisir.
 */
( function () {
	'use strict';

	var header = document.querySelector( '[data-header]' );

	/* --- 1. Kaydirma durumu --------------------------------------- */
	if ( header ) {
		var ticking = false;

		var syncStuck = function () {
			header.dataset.stuck = window.scrollY > 16 ? 'true' : 'false';
			ticking = false;
		};

		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( syncStuck );
			}
		}, { passive: true } );

		syncStuck();
	}

	/* --- 2. Acilir urun paneli ------------------------------------ */
	document.querySelectorAll( '[data-dropdown]' ).forEach( function ( root ) {
		var toggle = root.querySelector( '[data-dropdown-toggle]' );
		var panel  = root.querySelector( '[data-dropdown-panel]' );
		var timer  = null;

		if ( ! toggle || ! panel ) {
			return;
		}

		var isOpen = function () {
			return 'true' === panel.dataset.open;
		};

		var setOpen = function ( open ) {
			window.clearTimeout( timer );
			panel.dataset.open = open ? 'true' : 'false';
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		};

		toggle.addEventListener( 'click', function () {
			setOpen( ! isOpen() );

			if ( isOpen() ) {
				var first = panel.querySelector( 'a' );
				if ( first ) {
					first.focus();
				}
			}
		} );

		// Fareyle: kisa gecikmeyle ac, imlec panele gecerken kapanmasin.
		var hover = window.matchMedia( '(hover: hover) and (pointer: fine)' );

		root.addEventListener( 'mouseenter', function () {
			if ( ! hover.matches ) {
				return;
			}
			window.clearTimeout( timer );
			timer = window.setTimeout( function () { setOpen( true ); }, 90 );
		} );

		root.addEventListener( 'mouseleave', function () {
			if ( ! hover.matches ) {
				return;
			}
			window.clearTimeout( timer );
			timer = window.setTimeout( function () { setOpen( false ); }, 220 );
		} );

		// Odak menunun disina cikarsa kapansin.
		root.addEventListener( 'focusout', function ( event ) {
			if ( isOpen() && ! root.contains( event.relatedTarget ) ) {
				setOpen( false );
			}
		} );

		root.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && isOpen() ) {
				setOpen( false );
				toggle.focus();
				return;
			}

			// Panel icinde yukari/asagi oklarla gezinme.
			if ( 'ArrowDown' !== event.key && 'ArrowUp' !== event.key ) {
				return;
			}

			var links = Array.prototype.slice.call( panel.querySelectorAll( 'a' ) );
			var at    = links.indexOf( document.activeElement );

			if ( -1 === at && 'ArrowDown' === event.key && event.target === toggle ) {
				event.preventDefault();
				setOpen( true );
				links[0].focus();
				return;
			}

			if ( -1 === at ) {
				return;
			}

			event.preventDefault();
			var next = 'ArrowDown' === event.key ? at + 1 : at - 1;
			links[ ( next + links.length ) % links.length ].focus();
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( isOpen() && ! root.contains( event.target ) ) {
				setOpen( false );
			}
		} );
	} );

	/* --- 3. Mobil panel ------------------------------------------- */
	var navToggle = document.querySelector( '[data-nav-toggle]' );
	var navPanel  = document.querySelector( '[data-nav-panel]' );

	if ( navToggle && navPanel ) {
		var setPanel = function ( open ) {
			navPanel.dataset.open = open ? 'true' : 'false';
			navToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			navToggle.setAttribute( 'aria-label', open ? 'Menüyü kapat' : 'Menüyü aç' );
		};

		navToggle.addEventListener( 'click', function () {
			setPanel( 'true' !== navPanel.dataset.open );
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && 'true' === navPanel.dataset.open ) {
				setPanel( false );
				navToggle.focus();
			}
		} );

		// Masaustune gecince acik panel kapali sayilsin.
		var wide = window.matchMedia( '(min-width: 64rem)' );
		var onWide = function ( event ) {
			if ( event.matches ) {
				setPanel( false );
			}
		};

		if ( wide.addEventListener ) {
			wide.addEventListener( 'change', onWide );
		} else if ( wide.addListener ) {
			wide.addListener( onWide );
		}
	}

	document.querySelectorAll( '[data-accordion-toggle]' ).forEach( function ( button ) {
		var target = document.getElementById( button.getAttribute( 'aria-controls' ) );

		if ( ! target ) {
			return;
		}

		button.addEventListener( 'click', function () {
			var open = 'true' !== button.getAttribute( 'aria-expanded' );
			button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			target.hidden = ! open;
		} );
	} );
}() );
