/**
 * Menu (acilir menuler, mobil cekmece), arama, sepet (cerez), adet
 * secicileri, urun satirlari, form sonrasi odak.
 * JavaScript yoksa her sey duz baglanti ve form olarak calisir.
 */
( function () {
	'use strict';

	var doc = document;
	var CART = 'wk_cart';
	var MAX_LINES = 30;
	var MAX_QTY = 99;

	/* ---------------------------------------------------------------- */
	/* Acilir menuler: fareyle uzerine gelince, dugmeyle ya da klavyeyle. */
	/* ---------------------------------------------------------------- */

	var dropdowns = doc.querySelectorAll( '[data-dropdown]' );

	var setOpen = function ( item, open ) {
		var toggle = item.querySelector( '.wk-nav__toggle' );
		item.classList.toggle( 'is-open', open );

		if ( toggle ) {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}
	};

	var closeAll = function ( except ) {
		dropdowns.forEach( function ( item ) {
			if ( item !== except ) {
				setOpen( item, false );
			}
		} );
	};

	dropdowns.forEach( function ( item ) {
		var toggle = item.querySelector( '.wk-nav__toggle' );
		var timer;

		// Fare: kucuk bir gecikmeyle kapanir; imlec menuye gecerken kapanmasin.
		item.addEventListener( 'mouseenter', function () {
			if ( window.matchMedia( '(hover: hover)' ).matches ) {
				clearTimeout( timer );
				closeAll( item );
				setOpen( item, true );
			}
		} );

		item.addEventListener( 'mouseleave', function () {
			if ( window.matchMedia( '(hover: hover)' ).matches ) {
				timer = setTimeout( function () {
					setOpen( item, false );
				}, 180 );
			}
		} );

		if ( toggle ) {
			toggle.addEventListener( 'click', function () {
				var open = ! item.classList.contains( 'is-open' );
				closeAll( item );
				setOpen( item, open );

				if ( open ) {
					var first = item.querySelector( '[data-panel] a' );
					if ( first ) {
						first.focus();
					}
				}
			} );
		}

		// Klavye odagi menunun disina cikinca kapanir.
		item.addEventListener( 'focusout', function ( event ) {
			if ( ! item.contains( event.relatedTarget ) ) {
				setOpen( item, false );
			}
		} );
	} );

	doc.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}

		dropdowns.forEach( function ( item ) {
			if ( item.classList.contains( 'is-open' ) ) {
				setOpen( item, false );
				var toggle = item.querySelector( '.wk-nav__toggle' );
				if ( toggle && item.contains( doc.activeElement ) ) {
					toggle.focus();
				}
			}
		} );

		closeDrawer();
		closeAdded();
	} );

	doc.addEventListener( 'click', function ( event ) {
		if ( ! event.target.closest( '[data-dropdown]' ) ) {
			closeAll();
		}
	} );

	/* Ust menu kaydirinca golge alir. */
	var header = doc.querySelector( '[data-header]' );

	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	/* ---------------------------------------------------------------- */
	/* Mobil cekmece                                                     */
	/* ---------------------------------------------------------------- */

	var drawer = doc.querySelector( '[data-drawer]' );
	var drawerOpeners = doc.querySelectorAll( '[data-drawer-open]' );
	var lastFocus = null;

	function openDrawer() {
		if ( ! drawer ) {
			return;
		}
		lastFocus = doc.activeElement;
		drawer.hidden = false;
		// Bir kare sonra sinif: gecis animasyonu calissin.
		window.requestAnimationFrame( function () {
			drawer.classList.add( 'is-open' );
		} );
		doc.body.classList.add( 'wk-lock' );
		drawerOpeners.forEach( function ( b ) {
			b.setAttribute( 'aria-expanded', 'true' );
		} );
		var close = drawer.querySelector( '[data-drawer-close].wk-iconbtn' );
		if ( close ) {
			close.focus();
		}
	}

	function closeDrawer() {
		if ( ! drawer || drawer.hidden ) {
			return;
		}
		drawer.classList.remove( 'is-open' );
		doc.body.classList.remove( 'wk-lock' );
		drawerOpeners.forEach( function ( b ) {
			b.setAttribute( 'aria-expanded', 'false' );
		} );
		setTimeout( function () {
			drawer.hidden = true;
		}, 220 );
		if ( lastFocus ) {
			lastFocus.focus();
		}
	}

	drawerOpeners.forEach( function ( b ) {
		b.addEventListener( 'click', openDrawer );
	} );

	if ( drawer ) {
		drawer.querySelectorAll( '[data-drawer-close]' ).forEach( function ( b ) {
			b.addEventListener( 'click', closeDrawer );
		} );

		// Odak cekmecenin icinde doner.
		drawer.addEventListener( 'keydown', function ( event ) {
			if ( 'Tab' !== event.key ) {
				return;
			}
			var focusable = Array.prototype.filter.call(
				drawer.querySelectorAll( 'a[href], button' ),
				function ( el ) {
					return null !== el.offsetParent;
				}
			);
			var first = focusable[ 0 ];
			var last = focusable[ focusable.length - 1 ];

			if ( event.shiftKey && doc.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && doc.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		} );
	}

	doc.querySelectorAll( '[data-accordion]' ).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			var panel = doc.getElementById( button.getAttribute( 'aria-controls' ) );
			var open = 'true' !== button.getAttribute( 'aria-expanded' );
			button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( panel ) {
				panel.hidden = ! open;
			}
		} );
	} );

	/* ---------------------------------------------------------------- */
	/* Arama cubugu                                                      */
	/* ---------------------------------------------------------------- */

	var searchToggle = doc.querySelector( '[data-search-toggle]' );
	var search = doc.querySelector( '[data-search]' );

	if ( searchToggle && search ) {
		searchToggle.setAttribute( 'aria-expanded', search.hidden ? 'false' : 'true' );
		searchToggle.addEventListener( 'click', function () {
			search.hidden = ! search.hidden;
			searchToggle.setAttribute( 'aria-expanded', search.hidden ? 'false' : 'true' );
			if ( ! search.hidden ) {
				search.querySelector( 'input' ).focus();
			}
		} );
	}

	/* ---------------------------------------------------------------- */
	/* Sepet cerezi ("kimlik:adet|..."); inc/cart.php ile ayni bicim.     */
	/* ---------------------------------------------------------------- */

	function readCart() {
		var match = doc.cookie.match( new RegExp( '(?:^|; )' + CART + '=([^;]*)' ) );
		var items = {};
		var order = [];

		if ( match ) {
			decodeURIComponent( match[ 1 ] ).split( '|' ).forEach( function ( pair ) {
				var m = /^(\d{1,10}):(\d{1,3})$/.exec( pair );
				if ( m && order.length < MAX_LINES ) {
					items[ m[ 1 ] ] = Math.min( MAX_QTY, parseInt( m[ 2 ], 10 ) );
					order.push( m[ 1 ] );
				}
			} );
		}

		return { items: items, order: order };
	}

	function writeCart( cart ) {
		var pairs = cart.order
			.filter( function ( id ) {
				return cart.items[ id ] > 0;
			} )
			.slice( 0, MAX_LINES )
			.map( function ( id ) {
				return id + ':' + cart.items[ id ];
			} );
		var value = pairs.join( '|' );
		var secure = 'https:' === window.location.protocol ? '; secure' : '';
		var age = value ? 30 * 24 * 3600 : 0;

		doc.cookie = CART + '=' + encodeURIComponent( value ) + '; path=/; max-age=' + age + '; samesite=lax' + secure;
	}

	function cartCount() {
		var cart = readCart();
		return cart.order.reduce( function ( sum, id ) {
			return sum + cart.items[ id ];
		}, 0 );
	}

	function renderCount() {
		var count = cartCount();
		doc.querySelectorAll( '[data-cart-count]' ).forEach( function ( el ) {
			el.textContent = count > 99 ? '99+' : String( count );
			el.hidden = 0 === count;
		} );
	}

	renderCount();

	/* "Sepete eklendi" paneli: ne eklendigini ve sonraki adimi gosterir. */
	var added = null;
	var addedTimer;

	function closeAdded() {
		if ( added && ! added.hidden ) {
			added.classList.remove( 'is-open' );
			setTimeout( function () {
				added.hidden = true;
			}, 200 );
		}
	}

	function showAdded( form, qty ) {
		if ( ! added ) {
			added = doc.createElement( 'div' );
			added.className = 'wk-added';
			added.setAttribute( 'role', 'status' );
			added.hidden = true;
			added.innerHTML =
				'<div class="wk-added__head"><span class="wk-added__ok" aria-hidden="true">✓</span><strong>Sepete eklendi</strong>' +
				'<button type="button" class="wk-iconbtn wk-iconbtn--light" data-added-close><span aria-hidden="true">×</span><span class="wk-sr">Kapat</span></button></div>' +
				'<p class="wk-added__item"></p>' +
				'<div class="wk-added__actions"><a class="wk-btn wk-btn--primary" data-added-cart>Sepete git</a>' +
				'<button type="button" class="wk-btn wk-btn--line" data-added-close>Alışverişe devam et</button></div>';
			doc.body.appendChild( added );
			added.querySelectorAll( '[data-added-close]' ).forEach( function ( b ) {
				b.addEventListener( 'click', closeAdded );
			} );
			var link = doc.querySelector( '[data-cart-link]' );
			added.querySelector( '[data-added-cart]' ).href = link ? link.href : '/sepet/';
		}

		var item = added.querySelector( '.wk-added__item' );
		item.textContent = '';
		var code = doc.createElement( 'span' );
		code.className = 'wk-added__code';
		code.textContent = form.getAttribute( 'data-code' ) + ' · ' + qty + ' adet';
		item.appendChild( code );
		item.appendChild( doc.createTextNode( form.getAttribute( 'data-title' ) ) );

		added.hidden = false;
		window.requestAnimationFrame( function () {
			added.classList.add( 'is-open' );
		} );

		clearTimeout( addedTimer );
		addedTimer = setTimeout( closeAdded, 7000 );
	}

	doc.querySelectorAll( '[data-add]' ).forEach( function ( form ) {
		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();

			var id = form.getAttribute( 'data-id' );
			var input = form.querySelector( '[name="wk_qty"]' );
			var qty = Math.max( 1, Math.min( MAX_QTY, parseInt( input ? input.value : '1', 10 ) || 1 ) );
			var cart = readCart();

			if ( ! cart.items[ id ] ) {
				if ( cart.order.length >= MAX_LINES ) {
					form.submit();
					return;
				}
				cart.order.push( id );
				cart.items[ id ] = 0;
			}

			cart.items[ id ] = Math.min( MAX_QTY, cart.items[ id ] + qty );
			writeCart( cart );
			renderCount();
			showAdded( form, qty );

			var button = form.querySelector( '[type="submit"]' );
			if ( button ) {
				button.classList.add( 'is-added' );
				setTimeout( function () {
					button.classList.remove( 'is-added' );
				}, 1200 );
			}
		} );
	} );

	/* ---------------------------------------------------------------- */
	/* Adet secicileri (+/−); sepet sayfasinda degisiklik kendisi kaydedilir. */
	/* ---------------------------------------------------------------- */

	doc.querySelectorAll( '[data-qty]' ).forEach( function ( box ) {
		var input = box.querySelector( 'input' );
		var min = parseInt( input.getAttribute( 'min' ), 10 ) || 0;
		var cartForm = box.closest( '[data-cart-form]' );

		box.querySelectorAll( '[data-step]' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var next = ( parseInt( input.value, 10 ) || 0 ) + parseInt( button.getAttribute( 'data-step' ), 10 );
				input.value = Math.max( min, Math.min( MAX_QTY, next ) );
				input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			} );
		} );

		if ( cartForm ) {
			var timer;
			input.addEventListener( 'change', function () {
				clearTimeout( timer );
				timer = setTimeout( function () {
					cartForm.submit();
				}, 450 );
			} );
		}
	} );

	var cartUpdate = doc.querySelector( '[data-cart-update]' );
	if ( cartUpdate ) {
		cartUpdate.hidden = true; // Adet degisince kendiliginden kaydedilir.
	}

	/* Siralama secimi degisince form gonderilir. */
	doc.querySelectorAll( '[data-autosubmit]' ).forEach( function ( select ) {
		select.addEventListener( 'change', function () {
			select.form.submit();
		} );
	} );

	/* Dar ekranda magazadaki kategori listesi kapali baslar. */
	var filter = doc.querySelector( '[data-filter-details]' );
	if ( filter && window.matchMedia( '(max-width: 959px)' ).matches ) {
		filter.open = false;
	}

	/* ---------------------------------------------------------------- */
	/* Urun satirlari (ana sayfa): onceki / sonraki dugmeleri             */
	/* ---------------------------------------------------------------- */

	doc.querySelectorAll( '[data-rail]' ).forEach( function ( rail ) {
		var track = rail.querySelector( '[data-rail-track]' );
		var prev = rail.querySelector( '[data-rail-prev]' );
		var next = rail.querySelector( '[data-rail-next]' );

		if ( ! track || ! prev || ! next ) {
			return;
		}

		var update = function () {
			var max = track.scrollWidth - track.clientWidth - 2;
			prev.disabled = track.scrollLeft <= 2;
			next.disabled = track.scrollLeft >= max;
			rail.classList.toggle( 'is-static', max <= 0 );
		};

		var step = function ( dir ) {
			var card = track.querySelector( '.wk-card' );
			var width = card ? card.getBoundingClientRect().width + 20 : track.clientWidth * 0.8;
			var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
			track.scrollBy( { left: dir * width * Math.max( 1, Math.floor( track.clientWidth / width ) ), behavior: reduce ? 'auto' : 'smooth' } );
		};

		prev.addEventListener( 'click', function () {
			step( -1 );
		} );
		next.addEventListener( 'click', function () {
			step( 1 );
		} );
		track.addEventListener( 'scroll', update, { passive: true } );
		window.addEventListener( 'resize', update );
		update();
	} );

	/* ---------------------------------------------------------------- */
	/* Odeme formu: secime gore alanlar (data-show-when).                 */
	/* "a" = kutu isaretli, "a=b" = secili deger; "|" veya, "+" ve.       */
	/* ---------------------------------------------------------------- */

	var checkout = doc.querySelector( '[data-checkout]' );

	if ( checkout ) {
		var state = function () {
			var s = {};
			checkout.querySelectorAll( '[data-toggle-target]' ).forEach( function ( input ) {
				if ( 'radio' === input.type ) {
					if ( input.checked ) {
						s[ input.name.replace( 'wk_', '' ) ] = input.value;
					}
				} else {
					s[ input.getAttribute( 'data-toggle-target' ) ] = input.checked;
				}
			} );
			return s;
		};

		var test = function ( rule, s ) {
			return rule.split( '|' ).some( function ( any ) {
				return any.split( '+' ).every( function ( all ) {
					var pair = all.split( '=' );
					return 2 === pair.length ? s[ pair[ 0 ] ] === pair[ 1 ] : !! s[ pair[ 0 ] ];
				} );
			} );
		};

		var sync = function () {
			var s = state();
			checkout.querySelectorAll( '[data-show-when]' ).forEach( function ( el ) {
				el.hidden = ! test( el.getAttribute( 'data-show-when' ), s );
			} );
		};

		checkout.addEventListener( 'change', sync );
		sync();
	}

	/* Hatali alan duzeltilince isaret ve hata metni kalkar. */
	doc.addEventListener( 'input', clearError );
	doc.addEventListener( 'change', clearError );

	function clearError( event ) {
		var field = event.target;

		if ( ! field.matches || ! field.matches( '[aria-invalid="true"]' ) ) {
			return;
		}

		var filled = 'checkbox' === field.type ? field.checked : '' !== field.value.trim();

		if ( filled ) {
			field.removeAttribute( 'aria-invalid' );
			var message = doc.getElementById( field.id + '-err' );
			if ( message ) {
				message.remove();
			}
		}
	}

	/* Form gonderiminden sonra bildirim odaga alinir. */
	var notice = doc.querySelector( '[data-focus]' );

	if ( notice ) {
		notice.focus( { preventScroll: false } );
	}

	/* Siparis onayindan sonra sepet temizlenir (sunucu da temizler). */
	if ( doc.querySelector( '[data-order-done]' ) ) {
		writeCart( { items: {}, order: [] } );
		renderCount();
	}
}() );
