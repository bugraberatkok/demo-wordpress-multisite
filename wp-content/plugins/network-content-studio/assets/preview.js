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

	function closestEditable( element ) {
		return element && element.closest ? element.closest( '[data-nwcs-edit]' ) : null;
	}

	document.addEventListener( 'mousemove', function ( event ) {
		var editable = closestEditable( event.target );

		if ( ! editable ) {
			tip.classList.remove( 'is-visible' );
			return;
		}

		tip.innerHTML = '';
		tip.appendChild(
			document.createTextNode( labelFor( editable.getAttribute( 'data-nwcs-edit' ), editable.getAttribute( 'data-nwcs-row' ) ) )
		);
		var hint = document.createElement( 'small' );
		hint.textContent = 'Düzenlemek için tıklayın';
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
