/**
 * Ana menu davranisi.
 *
 * Iki ayri hareket var:
 *
 * 1) Kayan gosterge — menunun arkasinda duran tek bir kutu. Imlec hangi ogeye
 *    giderse oraya suzulup genisligini o ogeye gore ayarlar, menuden cikinca
 *    bulunulan sayfanin ogesine geri doner.
 *
 * 2) Morph eden acilir panel (revenuecat.com hareketi) — her menu ogesinin
 *    ayri kutusu YOK. Tek bir kutu var; bir ust menuden digerine gecerken
 *    yeni icerigin olcusune dogru buyuyup kucularak kayiyor, icerik de gidis
 *    yonune gore capraz geciyor.
 *
 * Dar ekranda ortak kutu devre disi kalir, her oge kendi akordiyonunu acar.
 */
( function () {
	'use strict';

	var nav = document.querySelector( '[data-k-nav]' );

	if ( ! nav ) {
		return;
	}

	var header    = document.querySelector( '[data-k-header]' );
	var burger    = document.querySelector( '[data-k-burger]' );
	var wrap      = nav.closest( '.k-wrap' );
	var indicator = nav.querySelector( '[data-k-nav-indicator]' );
	var dropdown  = nav.querySelector( '[data-k-dropdown]' );
	var shell     = nav.querySelector( '[data-k-dropdown-shell]' );
	var links     = Array.prototype.slice.call( nav.querySelectorAll( '[data-k-nav-link]' ) );
	var items     = Array.prototype.slice.call( nav.querySelectorAll( '[data-k-nav-item]' ) );
	var desktop   = window.matchMedia( '(min-width: 1180px)' );

	var activeLink  = nav.querySelector( '.k-nav__link.is-current' );
	var openedItem  = null;
	var currentPane = null;
	var paneIndex   = null;
	var closeTimer  = null;

	var panes = {};

	Array.prototype.forEach.call( nav.querySelectorAll( '[data-k-dropdown-pane]' ), function ( pane ) {
		panes[ pane.getAttribute( 'data-k-dropdown-pane' ) ] = pane;
	} );

	/* ---------- kayan gosterge ---------- */

	function hideIndicator() {
		if ( indicator ) {
			indicator.classList.remove( 'is-visible' );
		}
	}

	/**
	 * Gostergeyi verilen baglantinin uzerine tasir.
	 *
	 * animate = false oldugunda gecis tek kare kapatilir; ilk yerlesimde ve
	 * yeniden boyutlandirmada gosterge ekranda kaymasin diye.
	 */
	function moveIndicator( link, animate ) {
		if ( ! indicator ) {
			return;
		}

		if ( ! link || ! desktop.matches ) {
			hideIndicator();
			return;
		}

		var navRect  = nav.getBoundingClientRect();
		var linkRect = link.getBoundingClientRect();
		var right    = linkRect.right;

		/*
		 * Alt menusu olan ogede ok, baglantinin disinda ayri bir dugme; gosterge
		 * yalnizca baglantiyi olcerse ok hapin disinda kalir. Hap oku da kapsar;
		 * okun sagindaki 6px pay, yandaki ogenin yazisina degmeyecek kadar.
		 */
		var caret = link.parentNode.querySelector( '.k-nav__caret' );

		if ( caret ) {
			var chevron = caret.querySelector( '.k-nav__chevron' ) || caret;

			right = chevron.getBoundingClientRect().right + 6;
		}

		if ( ! animate ) {
			indicator.style.transition = 'none';
		}

		indicator.style.width     = ( right - linkRect.left ) + 'px';
		indicator.style.transform = 'translateX(' + ( linkRect.left - navRect.left ) + 'px)';
		indicator.classList.add( 'is-visible' );

		if ( ! animate ) {
			indicator.getBoundingClientRect();
			indicator.style.transition = '';
		}
	}

	function resetIndicator( animate ) {
		if ( activeLink ) {
			moveIndicator( activeLink, animate );
		} else {
			hideIndicator();
		}
	}

	/* ---------- morph eden acilir panel ---------- */

	function clamp( value, min, max ) {
		if ( max < min ) {
			return min;
		}

		return Math.min( Math.max( value, min ), max );
	}

	/**
	 * Kutuyu, acilan ogenin altina hizalar.
	 *
	 * Sol kenar ogenin soluna oturur, ancak kutu sayfanin icerik genisligini
	 * tasmasin diye sagdan ve soldan sinirlanir.
	 */
	function positionShell( item, width ) {
		var navRect = nav.getBoundingClientRect();
		var minX    = 0;
		var maxX    = navRect.width - width;

		if ( wrap ) {
			var wrapRect = wrap.getBoundingClientRect();

			minX = wrapRect.left - navRect.left;
			maxX = wrapRect.right - navRect.left - width;
		}

		dropdown.style.setProperty( '--k-dd-x', clamp( item.offsetLeft, minX, maxX ) + 'px' );
	}

	/**
	 * Panolar arasi gecis. Yon, onceki panoya gore belirlenir: saga gidiliyorsa
	 * yeni pano sagdan girer, eski pano sola cikar.
	 */
	function swapPane( pane, index ) {
		var direction = ( null === paneIndex || index > paneIndex ) ? 1 : -1;
		var offset    = 18 * direction;

		if ( currentPane && currentPane !== pane ) {
			currentPane.style.setProperty( '--k-pane-x', -offset + 'px' );
			currentPane.classList.remove( 'is-active' );
		}

		if ( currentPane !== pane ) {
			pane.style.setProperty( '--k-pane-x', offset + 'px' );

			// Baslangic konumunu ayni karede uygula ki gecis oradan baslasin.
			pane.getBoundingClientRect();
		}

		pane.classList.add( 'is-active' );

		currentPane = pane;
		paneIndex   = index;
	}

	function openDropdown( item ) {
		if ( ! dropdown || ! shell ) {
			return;
		}

		var key  = item.getAttribute( 'data-k-nav-pane' );
		var pane = panes[ key ];

		if ( ! pane ) {
			return;
		}

		var wasClosed = ! nav.classList.contains( 'is-open' );

		// Pano gorunmez ama dusuk seviyede yerlesimi var (display:none degil,
		// visibility:hidden), bu yuzden olculeri simdi okunabiliyor.
		var width  = pane.offsetWidth;
		var height = pane.offsetHeight;

		// Kapaliyken kutu 0 olculudur; acilirken sifirdan buyuyor gibi
		// gorunmesin diye ilk olcu gecissiz yazilir.
		if ( wasClosed ) {
			dropdown.classList.add( 'is-instant' );
		}

		shell.style.setProperty( '--k-dd-w', width + 'px' );
		shell.style.setProperty( '--k-dd-h', height + 'px' );
		positionShell( item, width );

		if ( wasClosed ) {
			dropdown.getBoundingClientRect();
			dropdown.classList.remove( 'is-instant' );
		}

		swapPane( pane, parseInt( key, 10 ) );
		nav.classList.add( 'is-open' );
	}

	function closeDropdown() {
		if ( ! nav.classList.contains( 'is-open' ) ) {
			return;
		}

		nav.classList.remove( 'is-open' );

		// Pano bilgisi korunuyor: ayni menu tekrar acilirsa yon hesabi
		// kaldigi yerden devam etsin.
		if ( currentPane ) {
			currentPane.classList.remove( 'is-active' );
			currentPane = null;
		}
	}

	/* ---------- oge acma / kapama ---------- */

	function setExpanded( item, expanded ) {
		var toggle = item.querySelector( '[data-k-nav-toggle]' );

		if ( toggle ) {
			toggle.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
		}
	}

	function closeItem( item ) {
		if ( ! item ) {
			return;
		}

		item.classList.remove( 'is-open' );
		setExpanded( item, false );

		if ( openedItem === item ) {
			openedItem = null;
		}

		if ( ! openedItem ) {
			closeDropdown();
		}
	}

	function openItem( item ) {
		if ( openedItem && openedItem !== item ) {
			openedItem.classList.remove( 'is-open' );
			setExpanded( openedItem, false );
		}

		item.classList.add( 'is-open' );
		setExpanded( item, true );
		openedItem = item;

		if ( desktop.matches ) {
			openDropdown( item );
		}
	}

	function closeAll() {
		items.forEach( function ( item ) {
			item.classList.remove( 'is-open' );
			setExpanded( item, false );
		} );

		openedItem = null;
		closeDropdown();
	}

	/* ---------- olaylar ---------- */

	links.forEach( function ( link ) {
		link.addEventListener( 'pointerenter', function () {
			moveIndicator( link, true );
		} );

		link.addEventListener( 'focus', function () {
			moveIndicator( link, true );
		} );
	} );

	items.forEach( function ( item ) {
		var hasPane = item.hasAttribute( 'data-k-nav-pane' );

		item.addEventListener( 'pointerenter', function () {
			if ( ! desktop.matches ) {
				return;
			}

			window.clearTimeout( closeTimer );

			if ( hasPane ) {
				openItem( item );
			} else if ( openedItem ) {
				// Alt menusu olmayan bir ogeye gelindi: acik panel kapansin.
				closeItem( openedItem );
			}
		} );

		if ( ! hasPane ) {
			return;
		}

		var toggle = item.querySelector( '[data-k-nav-toggle]' );

		if ( toggle ) {
			// Ok dugmesi dokunmatik ve klavye icin acip kapatir; baglantinin
			// kendisi her zaman kendi sayfasina gider.
			toggle.addEventListener( 'click', function ( event ) {
				event.preventDefault();

				if ( item.classList.contains( 'is-open' ) ) {
					closeItem( item );
				} else {
					openItem( item );
				}
			} );
		}
	} );

	// Kutu menunun altinda durdugu icin imlec ona giderken menuden cikar;
	// kisa gecikme panelin gozunu kirpmasini onler.
	nav.addEventListener( 'pointerleave', function () {
		resetIndicator( true );

		if ( ! desktop.matches ) {
			return;
		}

		closeTimer = window.setTimeout( closeAll, 160 );
	} );

	nav.addEventListener( 'pointerenter', function () {
		window.clearTimeout( closeTimer );
	} );

	nav.addEventListener( 'focusout', function ( event ) {
		if ( ! nav.contains( event.relatedTarget ) ) {
			resetIndicator( true );
			closeAll();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}

		// Dar ekranda Esc tum menuyu kapatir, odak hamburgere doner.
		if ( header && header.classList.contains( 'is-nav-open' ) ) {
			setMobileMenu( false );

			if ( burger ) {
				burger.focus();
			}

			return;
		}

		if ( ! openedItem ) {
			return;
		}

		var toggle = openedItem.querySelector( '[data-k-nav-toggle]' );

		closeItem( openedItem );
		resetIndicator( true );

		if ( toggle ) {
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( ! nav.contains( event.target ) ) {
			closeAll();
		}
	} );

	/* ---------- mobil menu ---------- */

	/*
	 * Menu acikken arkadaki sayfa kaymasin. Header yapiskan; uzun menu kendi
	 * icinde kayar, yuksekligi icin ust kenarin o anki konumu CSS'e verilir.
	 */
	function setMobileMenu( open ) {
		if ( ! header ) {
			return;
		}

		header.classList.toggle( 'is-nav-open', open );
		document.documentElement.classList.toggle( 'k-nav-locked', open );

		if ( open ) {
			header.style.setProperty( '--k-nav-top', Math.max( 0, nav.getBoundingClientRect().top ) + 'px' );
		}

		if ( burger ) {
			burger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}

		if ( ! open ) {
			closeAll();
		}
	}

	if ( burger && header ) {
		burger.addEventListener( 'click', function () {
			setMobileMenu( ! header.classList.contains( 'is-nav-open' ) );
		} );

		// Ayni sayfadaki capaya (/#katalog) gidilince menu acik ve sayfa
		// kilitli kalmasin.
		nav.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( 'a' ) && header.classList.contains( 'is-nav-open' ) ) {
				setMobileMenu( false );
			}
		} );
	}

	/* ---------- ilk yerlesim ve yeniden olcum ---------- */

	function settle() {
		resetIndicator( false );
	}

	if ( document.fonts && document.fonts.ready ) {
		// Yazi tipi yuklenmeden olculen genislik yaniltici olur.
		document.fonts.ready.then( settle );
	}

	if ( 'complete' === document.readyState ) {
		settle();
	} else {
		window.addEventListener( 'load', settle );
	}

	if ( window.ResizeObserver ) {
		new window.ResizeObserver( settle ).observe( nav );
	} else {
		window.addEventListener( 'resize', settle );
	}

	if ( desktop.addEventListener ) {
		desktop.addEventListener( 'change', function () {
			setMobileMenu( false );
			settle();
		} );
	}
}() );
