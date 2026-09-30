/**
 * Urun Havuzu Excel adimlari icin kucuk iyilestirmeler. Her sey JavaScript
 * olmadan da calisir (formlar admin-post.php'ye gider); burada yalnizca:
 *  - sonuc/onizleme kartina odak (klavye ve ekran okuyucu icin);
 *  - Detay basliklari ekraninda sira degisince tasinan satirin dugmesine odak;
 *  - uygula dugmesine iki kez basilmasin.
 * Sunucu tarafi: includes/admin/import.php, sync.php, pool-headings.php
 */
( function () {
	'use strict';

	var result = document.getElementById( 'nwcs-excel-result' );

	if ( result ) {
		result.setAttribute( 'tabindex', '-1' );
		result.focus( { preventScroll: false } );
	}

	var moved = document.querySelector( '[data-nwcs-focus]' );

	if ( moved ) {
		// Adresteki #bolum kaydirmasi odagi sifirlayabilir: sayfa yuklendikten sonra.
		window.addEventListener( 'load', function () {
			window.setTimeout( function () {
				moved.focus();
			}, 0 );
		} );
	}

	/*
	 * Detay basliklari: ↑ ↓ sayfayi yenilemeden satiri tasir; sira tablodaki
	 * gizli "sira[]" alanlariyla "Değişiklikleri kaydet" ile tek seferde yazilir.
	 * JavaScript yoksa dugmeler formu gonderir, sunucu tasir (eski davranis).
	 */
	var hform = document.getElementById( 'nwcs-hform' );

	if ( hform ) {
		var status = hform.querySelector( '[data-nwcs-hstatus]' );
		var refresh = function () {
			var rows = hform.querySelectorAll( '[data-nwcs-hrow]' );

			rows.forEach( function ( row, index ) {
				row.querySelector( 'button[value^="up:"]' ).disabled = 0 === index;
				row.querySelector( 'button[value^="down:"]' ).disabled = rows.length - 1 === index;
			} );
		};

		hform.addEventListener( 'click', function ( event ) {
			var button = event.target.closest( 'button[value^="up:"], button[value^="down:"]' );

			if ( ! button ) {
				return;
			}

			event.preventDefault();

			var row = button.closest( '[data-nwcs-hrow]' );
			var up = 0 === button.value.indexOf( 'up:' );
			var other = up ? row.previousElementSibling : row.nextElementSibling;

			if ( ! other ) {
				return;
			}

			row.parentNode.insertBefore( row, up ? other : other.nextElementSibling );
			row.classList.add( 'is-moved' );
			refresh();

			// Odak tasinan satirda kalir; kenara gelindiyse diger yon dugmesine.
			( button.disabled ? row.querySelector( up ? 'button[value^="down:"]' : 'button[value^="up:"]' ) : button ).focus();

			if ( status ) {
				status.textContent = 'Sıra değişti. Kaydetmek için “Değişiklikleri kaydet”e basın.';
			}
		} );
	}

	document.querySelectorAll( '.nwcs-sync__form' ).forEach( function ( form ) {
		form.addEventListener( 'submit', function () {
			var button = form.querySelector( '.button-primary' );

			if ( button ) {
				// Bir sonraki olay dongusunde: once form gonderilsin.
				window.setTimeout( function () {
					button.disabled = true;
					button.textContent = 'Uygulanıyor…';
				}, 0 );
			}
		} );
	} );
}() );

/**
 * Ürün Havuzu -> Kategoriler (includes/admin/pool-categories.php). JavaScript
 * yoksa arama formu sunucuda suzer; burada yalnizca:
 *  - sol listeyi yazdikca suzer;
 *  - yeni kategori adinda benzer bir kategori varsa haber verir (engellemez);
 *  - sitede isaret kalkinca "hangi baslik" secimi soluklasir.
 */
( function () {
	'use strict';

	var fold = function ( text ) {
		var map = { 'ı': 'i', 'İ': 'i', 'I': 'i', 'ğ': 'g', 'ü': 'u', 'ş': 's', 'ö': 'o', 'ç': 'c', 'â': 'a', 'î': 'i', 'û': 'u' };

		return String( text ).replace( /[ıİIğüşöçâîû]/g, function ( ch ) {
			return map[ ch ] || ch;
		} ).toLowerCase();
	};

	var filter = document.querySelector( '[data-nwcs-rail-filter]' );
	var list = document.querySelector( '[data-nwcs-rail]' );

	if ( filter && list ) {
		filter.addEventListener( 'input', function () {
			var needle = fold( filter.value.trim() );

			list.querySelectorAll( '[data-nwcs-rail-name]' ).forEach( function ( item ) {
				item.hidden = '' !== needle && -1 === item.getAttribute( 'data-nwcs-rail-name' ).indexOf( needle );
			} );
		} );
	}

	var name = document.querySelector( '[data-nwcs-similar]' );

	if ( name ) {
		var note = document.getElementById( name.getAttribute( 'aria-describedby' ) );
		var known = [];
		var ignore = [ 'ahsap', 've', 'ile', 'icin', 'urun', 'urunleri' ];
		var words = function ( text ) {
			return fold( text ).split( /[^a-z0-9]+/ ).filter( function ( word ) {
				return word.length >= 4 && -1 === ignore.indexOf( word );
			} );
		};

		try {
			known = JSON.parse( name.getAttribute( 'data-nwcs-similar' ) ) || [];
		} catch ( e ) {
			known = [];
		}

		name.addEventListener( 'input', function () {
			var mine = words( name.value );
			var hit = null;

			known.some( function ( item ) {
				if ( fold( item.name ) === fold( name.value.trim() ) ) {
					hit = { item: item, same: true };
					return true;
				}

				return words( item.name ).some( function ( other ) {
					return mine.some( function ( word ) {
						if ( 0 === other.indexOf( word ) || 0 === word.indexOf( other ) ) {
							hit = { item: item, same: false };
							return true;
						}

						return false;
					} );
				} );
			} );

			note.textContent = '';

			if ( ! hit ) {
				return;
			}

			var link = document.createElement( 'a' );
			link.href = hit.item.url;
			link.textContent = hit.item.name + ' kategorisine git';
			note.appendChild( document.createTextNode( hit.same
				? 'Bu adda bir kategori zaten var (' + hit.item.count + ' ürün). '
				: 'Benzer ad var: “' + hit.item.name + '” (' + hit.item.count + ' ürün). Aynı ürünler içinse onu kullanın: ' ) );
			note.appendChild( link );
		} );
	}

	document.querySelectorAll( '[data-nwcs-place]' ).forEach( function ( box ) {
		var row = box.closest( '.nwcs-place' );
		var sync = function () {
			row.classList.toggle( 'is-off', ! box.checked );
		};

		box.addEventListener( 'change', sync );
		sync();
	} );
}() );
