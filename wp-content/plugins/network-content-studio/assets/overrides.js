/**
 * Urun Havuzu -> Ozellestirmeler bolumu.
 * Site basina istisna degerlerini sayfadan ayrilmadan kaydeder/kaldirir.
 */
( function () {
	'use strict';

	var wrap = document.querySelector( '[data-nwcs-overrides]' );

	if ( ! wrap ) {
		return;
	}

	var cfg       = window.nwcsPanel || {};
	var productId = wrap.getAttribute( 'data-product' );

	var post = function ( action, fields ) {
		var body = new FormData();

		body.append( 'action', action );
		body.append( 'nonce', cfg.nonce );
		body.append( 'product', productId );

		Object.keys( fields ).forEach( function ( key ) {
			body.append( key, fields[ key ] );
		} );

		return fetch( cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( json ) {
				if ( ! json || ! json.success ) {
					throw new Error( ( json && json.data && json.data.message ) || 'Kaydedilemedi.' );
				}
				return json.data;
			} );
	};

	var readBlock = function ( block ) {
		var values = { blog: block.getAttribute( 'data-blog' ) };

		block.querySelectorAll( '[data-ovr]' ).forEach( function ( field ) {
			var key = field.getAttribute( 'data-ovr' );
			values[ key ] = 'checkbox' === field.type ? ( field.checked ? '1' : '' ) : field.value;
		} );

		return values;
	};

	var flash = function ( block, text, isError ) {
		var status = block.querySelector( '[data-ovr-status]' );

		status.textContent = text;
		status.classList.toggle( 'is-error', !! isError );

		window.setTimeout( function () {
			status.textContent = '';
			status.classList.remove( 'is-error' );
		}, 2500 );
	};

	var setTag = function ( block, count ) {
		var tag = block.querySelector( '.nwcs-ovr__tag' );

		if ( ! tag || tag.classList.contains( 'nwcs-ovr__tag--off' ) ) {
			return;
		}

		tag.classList.toggle( 'nwcs-ovr__tag--on', count > 0 );
		tag.textContent = count > 0 ? count + ' özelleştirme' : 'Havuzdaki gibi';
	};

	// Fiyat alani yalnizca kutucuk isaretliyken yazilabilir.
	wrap.addEventListener( 'change', function ( event ) {
		var box = event.target.closest( '[data-ovr="price_override"]' );

		if ( ! box ) {
			return;
		}

		var price = box.closest( '.nwcs-ovr__body' ).querySelector( '[data-ovr="price"]' );
		price.disabled = ! box.checked;

		if ( box.checked ) {
			price.focus();
		}
	} );

	wrap.addEventListener( 'click', function ( event ) {
		var block = event.target.closest( '.nwcs-ovr' );

		if ( ! block ) {
			return;
		}

		if ( event.target.closest( '[data-ovr-save]' ) ) {
			var button = event.target.closest( '[data-ovr-save]' );
			button.disabled = true;

			post( 'nwcs_override_save', readBlock( block ) )
				.then( function ( data ) {
					flash( block, data.message );
					setTag( block, data.count );
					block.querySelector( '[data-ovr-clear]' ).disabled = ! data.count;
				} )
				.catch( function ( error ) { flash( block, error.message, true ); } )
				.finally( function () { button.disabled = false; } );
		}

		if ( event.target.closest( '[data-ovr-clear]' ) ) {
			if ( ! window.confirm( 'Bu sitenin özelleştirmeleri silinecek; ürün havuzdaki hâline dönecek. Devam edilsin mi?' ) ) {
				return;
			}

			post( 'nwcs_override_clear', { blog: block.getAttribute( 'data-blog' ) } )
				.then( function ( data ) {
					block.querySelectorAll( '[data-ovr]' ).forEach( function ( field ) {
						if ( 'checkbox' === field.type ) {
							field.checked = false;
						} else if ( 'SELECT' === field.tagName ) {
							field.value = '0';
						} else {
							field.value = '';
						}
					} );

					block.querySelector( '[data-ovr="price"]' ).disabled = true;
					block.querySelector( '[data-ovr-clear]' ).disabled = true;
					setTag( block, 0 );
					flash( block, data.message );
				} )
				.catch( function ( error ) { flash( block, error.message, true ); } );
		}
	} );
}() );
