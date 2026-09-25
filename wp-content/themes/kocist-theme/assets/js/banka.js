/*
 * Banka Bilgilerimiz: IBAN kopyalama.
 *
 * Dugmeler sunucuda gizli gelir; pano kullanilabiliyorsa gorunur. Kopyalanan
 * deger bosluksuz IBAN'dir; ekran okuyucuya "Kopyalandi" duyurulur.
 */
( function () {
	var root = document.querySelector( '[data-k-banka]' );

	if ( ! root || ! navigator.clipboard ) {
		return;
	}

	var status  = root.querySelector( '[data-k-copy-status]' );
	var buttons = root.querySelectorAll( '[data-k-copy]' );

	Array.prototype.forEach.call( buttons, function ( button ) {
		var text  = button.querySelector( '[data-k-copy-text]' );
		var label = text.textContent;
		var timer = 0;

		button.hidden = false;

		button.addEventListener( 'click', function () {
			navigator.clipboard.writeText( button.getAttribute( 'data-k-copy' ) ).then(
				function () {
					text.textContent = 'Kopyalandı';
					button.classList.add( 'is-copied' );

					if ( status ) {
						status.textContent = 'IBAN kopyalandı';
					}

					clearTimeout( timer );
					timer = setTimeout( function () {
						text.textContent = label;
						button.classList.remove( 'is-copied' );
					}, 2000 );
				},
				function () {
					text.textContent = 'Kopyalanamadı, IBAN\'ı seçip kopyalayın';
				}
			);
		} );
	} );
}() );
