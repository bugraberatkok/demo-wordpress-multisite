/**
 * Urun formu -> "Detay başlıkları": satir ekleme ve kaldirma.
 *
 * JavaScript olmadan da calisir: form her zaman bir bos satirla gelir,
 * kaydedince yeni bos satir cikar. Burada "+ Başlık ekle" ve "×"
 * dugmeleri acilir. Sunucu tarafi: includes/admin/pool-headings.php
 */
( function () {
	'use strict';

	document.querySelectorAll( '[data-nwcs-details]' ).forEach( function ( box ) {
		var list = box.querySelector( '[data-nwcs-details-list]' );
		var template = box.querySelector( '[data-nwcs-detail-template]' );
		var add = box.querySelector( '[data-nwcs-detail-add]' );

		if ( ! list || ! template || ! add ) {
			return;
		}

		add.hidden = false;

		box.querySelectorAll( '[data-nwcs-detail-remove]' ).forEach( function ( button ) {
			button.hidden = false;
		} );

		add.addEventListener( 'click', function () {
			var row = template.content.firstElementChild.cloneNode( true );
			list.appendChild( row );
			row.querySelector( 'input' ).focus();
		} );

		list.addEventListener( 'click', function ( event ) {
			var button = event.target.closest( '[data-nwcs-detail-remove]' );

			if ( ! button ) {
				return;
			}

			var row = button.closest( '[data-nwcs-detail-row]' );
			var next = row.nextElementSibling || row.previousElementSibling;

			row.remove();

			// Odak kaybolmasin: yandaki satira ya da ekle dugmesine.
			( next && next.querySelector( 'input' ) ? next.querySelector( 'input' ) : add ).focus();
		} );

		// Etiket yazilinca deger alaninin ekran okuyucu adi da guncellenir.
		list.addEventListener( 'input', function ( event ) {
			if ( ! event.target.classList.contains( 'nwcs-details__label' ) ) {
				return;
			}

			var value = event.target.nextElementSibling;

			if ( value ) {
				value.setAttribute( 'aria-label', event.target.value.trim() || 'Değer' );
			}
		} );
	} );
}() );
