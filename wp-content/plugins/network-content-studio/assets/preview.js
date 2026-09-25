/**
 * Onizleme icindeki tiklama koprusu.
 *
 * Sayfadaki isaretli alana tiklandiginda ust pencereye (panel) hangi alanin
 * duzenlenecegi bildirilir. Burada hicbir icerik yazilmaz; yalnizca mesaj
 * gonderilir. Panel de mesajin kaynagini dogrular.
 */
( function () {
	'use strict';

	var config = window.nwcsPreview || {};
	var labels = config.labels || {};
	var sources = config.sources || [];
	var parentOrigin = config.origin || window.location.origin;
	var navigationLocked = true;

	document.documentElement.classList.add( 'nwcs-lock-links' );

	var tip = document.createElement( 'div' );
	tip.id = 'nwcs-tip';
	document.addEventListener( 'DOMContentLoaded', function () {
		document.body.appendChild( tip );
	} );

	function labelFor( target, row ) {
		var text = labels[ target ] || 'Düzenle';
		var parts = target.split( '.' );

		if ( parts.length > 2 ) {
			var componentLabel = labels[ parts[0] + '.' + parts[1] ];
			if ( componentLabel ) {
				text = componentLabel + ' › ' + text;
			}
		}

		if ( null !== row && undefined !== row ) {
			text += ' (' + ( parseInt( row, 10 ) + 1 ) + '. satır)';
		}

		return text;
	}

	// En yakin isaretli oge: panelde duzenlenen alan ya da kaynagi baska yerde olan oge.
	function closestEditable( element ) {
		return element && element.closest ? element.closest( '[data-nwcs-edit],[data-nwcs-source]' ) : null;
	}

	var sourceHints = {
		product: 'Ürün Havuzu’nda düzenlenir, bu ürünü gösteren tüm sitelerde değişir. Açmak için tıklayın',
		post: 'Blog yazısından gelir. Yazıyı açmak için tıklayın',
		admin: 'Başka bir yönetim sayfasından gelir. Açmak için tıklayın'
	};

	// Yalnizca panelin kendi yonetim adresleri acilir.
	function sourceUrl( editable ) {
		var url = editable.getAttribute( 'data-nwcs-source-url' ) || '';

		for ( var i = 0; i < sources.length; i++ ) {
			if ( sources[ i ] && 0 === url.indexOf( sources[ i ] ) ) {
				return url;
			}
		}

		return '';
	}

	document.addEventListener( 'mousemove', function ( event ) {
		var editable = closestEditable( event.target );

		if ( ! editable ) {
			tip.classList.remove( 'is-visible' );
			return;
		}

		var kind = editable.getAttribute( 'data-nwcs-source' );
		var hint = document.createElement( 'small' );

		tip.innerHTML = '';
		tip.classList.toggle( 'is-source', !! kind );

		if ( kind ) {
			tip.appendChild( document.createTextNode( ( 'product' === kind ? 'Ürüne ait özellik › ' : '' ) + ( editable.getAttribute( 'data-nwcs-source-label' ) || 'Kaynak' ) ) );
			hint.textContent = sourceHints[ kind ] || sourceHints.admin;
		} else {
			tip.appendChild(
				document.createTextNode( labelFor( editable.getAttribute( 'data-nwcs-edit' ), editable.getAttribute( 'data-nwcs-row' ) ) )
			);
			hint.textContent = 'Düzenlemek için tıklayın';
		}

		tip.appendChild( hint );

		tip.style.left = event.clientX + 'px';
		tip.style.top = event.clientY + 'px';
		tip.classList.add( 'is-visible' );
	}, true );

	document.addEventListener( 'mouseleave', function () {
		tip.classList.remove( 'is-visible' );
	} );

	document.addEventListener(
		'click',
		function ( event ) {
			var editable = closestEditable( event.target );
			var link = event.target.closest ? event.target.closest( 'a' ) : null;

			// Duzenleme kilidi aciksa onizleme icinde gezinme yapilmaz.
			if ( navigationLocked && ( link || editable ) ) {
				event.preventDefault();
				event.stopPropagation();
			}

			if ( ! editable ) {
				return;
			}

			// Kaynagi baska yerde: panel sekmesi korunur, kaynak yeni sekmede acilir.
			// Gezinme acikken tiklama sayfada gezinir; kaynak acilmaz.
			if ( editable.hasAttribute( 'data-nwcs-source' ) ) {
				var url = navigationLocked ? sourceUrl( editable ) : '';

				if ( url ) {
					window.open( url, '_blank', 'noopener' );
				}

				return;
			}

			document.querySelectorAll( '.is-nwcs-active' ).forEach( function ( node ) {
				node.classList.remove( 'is-nwcs-active' );
			} );
			editable.classList.add( 'is-nwcs-active' );

			window.parent.postMessage(
				{
					type: 'nwcs:edit',
					blogId: config.blogId,
					target: editable.getAttribute( 'data-nwcs-edit' ),
					row: editable.getAttribute( 'data-nwcs-row' ),
					component: editable.getAttribute( 'data-nwcs-component' )
				},
				parentOrigin
			);
		},
		true
	);

	// Panelden gelen komutlar
	window.addEventListener( 'message', function ( event ) {
		if ( event.origin !== parentOrigin || ! event.data || 'object' !== typeof event.data ) {
			return;
		}

		if ( 'nwcs:lock' === event.data.type ) {
			navigationLocked = !! event.data.locked;
			document.documentElement.classList.toggle( 'nwcs-lock-links', navigationLocked );
			tip.classList.remove( 'is-visible' );
		}

		if ( 'nwcs:highlight' === event.data.type && event.data.target ) {
			var node = document.querySelector( '[data-nwcs-edit="' + event.data.target + '"]' );
			document.querySelectorAll( '.is-nwcs-active' ).forEach( function ( element ) {
				element.classList.remove( 'is-nwcs-active' );
			} );
			if ( node ) {
				node.classList.add( 'is-nwcs-active' );
				node.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			}
		}
	} );

	// Panel, onizlemenin hazir oldugunu bilsin.
	window.addEventListener( 'load', function () {
		window.parent.postMessage( { type: 'nwcs:ready', blogId: config.blogId }, parentOrigin );
	} );
}() );
