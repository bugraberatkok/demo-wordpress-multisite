/**
 * SEO alanlari: karakter sayaci ve canli Google onizlemesi.
 *
 * - data-nwcs-count tasiyan alanin altinda "42 / 60" sayaci. Alan bossa
 *   otomatik degerin (gri yer tutucu) uzunlugu gosterilir.
 * - SEO sekmesindeki her sayfa kartinda, baslik ve aciklama yazildikca
 *   ustteki Google onizlemesi guncellenir; alan bosalinca otomatik degere doner.
 *
 * Icerik Studyosu'nda duzenleyici AJAX ile yuklendigi icin olay dinleme
 * belge duzeyindedir; sonradan gelen alanlar da calisir.
 */
( function () {
	'use strict';

	var counterFor = function ( input ) {
		var counter = input.nextElementSibling;

		if ( ! counter || ! counter.hasAttribute( 'data-nwcs-counter' ) ) {
			counter = document.createElement( 'span' );
			counter.setAttribute( 'data-nwcs-counter', '' );
			counter.className = 'nwcs-counter';
			input.insertAdjacentElement( 'afterend', counter );
		}

		return counter;
	};

	var updateCounter = function ( input ) {
		var limit = parseInt( input.getAttribute( 'data-nwcs-count' ), 10 );

		if ( ! limit ) {
			return;
		}

		var own = input.value.trim();
		var length = ( own || input.getAttribute( 'placeholder' ) || '' ).length;
		var counter = counterFor( input );

		counter.textContent = ( own ? '' : 'otomatik · ' ) + length + ' / ' + limit;
		counter.classList.toggle( 'is-over', length > limit );
	};

	var updatePreview = function ( input ) {
		var card = input.closest( '[data-nwcs-seo-page]' );

		if ( ! card ) {
			return;
		}

		var name = input.getAttribute( 'name' );
		var target = null;

		if ( 'fields[title]' === name ) {
			target = card.querySelector( '[data-nwcs-serp-title]' );
		} else if ( 'fields[description]' === name ) {
			target = card.querySelector( '[data-nwcs-serp-desc]' );
		}

		if ( target ) {
			target.textContent = input.value.trim() || input.getAttribute( 'placeholder' ) || '';
		}
	};

	var handle = function ( event ) {
		var input = event.target;

		if ( ! input.matches || ! input.matches( '[data-nwcs-count]' ) ) {
			return;
		}

		updateCounter( input );
		updatePreview( input );
	};

	document.addEventListener( 'input', handle );
	document.addEventListener( 'focusin', handle );

	document.querySelectorAll( '[data-nwcs-count]' ).forEach( updateCounter );
}() );
