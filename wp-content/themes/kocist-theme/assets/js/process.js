/*
 * Siparis sureci: bolum gorunume girince cizim animasyonunu bir kez baslatir.
 *
 * Bolum once "ready" isaretlenir (CSS baslangic durumunu gizler), gorunume
 * girince .is-in eklenir. Hareket azaltilmissa ya da IntersectionObserver
 * yoksa hic dokunulmaz: her sey son haliyle gorunur.
 */
( function () {
	var sections = document.querySelectorAll( '[data-k-process]' );

	if ( ! sections.length || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	var observer = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( ! entry.isIntersecting ) {
				return;
			}

			entry.target.classList.add( 'is-in' );
			observer.unobserve( entry.target );
		} );
	}, { threshold: 0.35 } );

	sections.forEach( function ( section ) {
		// Sayfa bu bolumun altindan acildiysa (geri donus, capa) animasyon oynatilmaz.
		if ( section.getBoundingClientRect().bottom < 0 ) {
			return;
		}

		section.setAttribute( 'data-k-process', 'ready' );

		// Baslangic durumu bir kare boyansin ki gecis oradan baslasin.
		requestAnimationFrame( function () {
			observer.observe( section );
		} );
	} );
}() );
