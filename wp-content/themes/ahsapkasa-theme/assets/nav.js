/**
 * Ust menu davranisi.
 *
 * Iki is yapar:
 *  1. Sayfa kaydirilinca serit incelir ve zemin kazanir (data-stuck).
 *  2. Mobilde menu panelini acar/kapatir (data-open).
 *
 * Gorunumun tamami Tailwind siniflarinda; burada yalnizca durum degisir.
 */
( function () {
	'use strict';

	var header = document.querySelector( '[data-header]' );
	var toggle = document.querySelector( '[data-nav-toggle]' );
	var panel  = document.querySelector( '[data-nav-panel]' );

	/* --- 1. Kaydirma durumu --------------------------------------- */
	if ( header ) {
		var syncStuck = function () {
			header.dataset.stuck = window.scrollY > 24 ? 'true' : 'false';
		};

		var ticking = false;
		window.addEventListener(
			'scroll',
			function () {
				if ( ticking ) {
					return;
				}
				ticking = true;
				window.requestAnimationFrame( function () {
					syncStuck();
					ticking = false;
				} );
			},
			{ passive: true }
		);

		syncStuck();
	}

	/* --- 2. Mobil menu -------------------------------------------- */
	if ( ! toggle || ! panel ) {
		return;
	}

	var setOpen = function ( open ) {
		panel.dataset.open = open ? 'true' : 'false';
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		toggle.setAttribute( 'aria-label', open ? 'Menüyü kapat' : 'Menüyü aç' );
	};

	toggle.addEventListener( 'click', function () {
		setOpen( 'true' !== panel.dataset.open );
	} );

	// Panel disina tiklayinca kapansin.
	document.addEventListener( 'click', function ( event ) {
		if ( 'true' !== panel.dataset.open ) {
			return;
		}
		if ( panel.contains( event.target ) || toggle.contains( event.target ) ) {
			return;
		}
		setOpen( false );
	} );

	// Esc ile kapansin, odak dugmeye donsun.
	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && 'true' === panel.dataset.open ) {
			setOpen( false );
			toggle.focus();
		}
	} );

	// Masaustune gecince acik panel kapali sayilsin.
	var wide = window.matchMedia( '(min-width: 48rem)' );
	var onWide = function ( event ) {
		if ( event.matches ) {
			setOpen( false );
		}
	};

	if ( wide.addEventListener ) {
		wide.addEventListener( 'change', onWide );
	} else if ( wide.addListener ) {
		wide.addListener( onWide );
	}
}() );
