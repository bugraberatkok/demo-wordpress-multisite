/**
 * Hero tesis videosu.
 *
 * Video sessiz ve dongulu; ekrandayken oynar, ekrandan cikinca durur.
 * Durdur/oynat dugmesi her zaman var (WCAG 2.2.2). Hareket azaltma aciksa
 * video kendiliginden baslamaz, kapak karesi gorunur; ziyaretci isterse
 * dugmeyle oynatir.
 */
( function () {
	'use strict';

	var stage = document.querySelector( '[data-k-hero-video]' );

	if ( ! stage ) {
		return;
	}

	var video  = stage.querySelector( 'video' );
	var toggle = stage.querySelector( '[data-k-hero-toggle]' );
	var label  = stage.querySelector( '[data-k-hero-toggle-label]' );

	if ( ! video || ! toggle ) {
		return;
	}

	var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	// Ziyaretci durdurduysa, ekrana geri donunce de durmus kalsin.
	var userPaused = reduced;

	function sync() {
		var paused = video.paused;

		toggle.classList.toggle( 'is-paused', paused );
		label.textContent = paused ? 'Videoyu oynat' : 'Videoyu durdur';
	}

	function play() {
		var attempt = video.play();

		// Tarayici otomatik oynatmayi reddederse dugme "oynat"ta kalir.
		if ( attempt && attempt.catch ) {
			attempt.catch( sync );
		}
	}

	video.addEventListener( 'play', sync );
	video.addEventListener( 'pause', sync );

	toggle.addEventListener( 'click', function () {
		if ( video.paused ) {
			userPaused = false;
			play();
		} else {
			userPaused = true;
			video.pause();
		}
	} );

	toggle.hidden = false;
	sync();

	// Ekran disindayken bant genisligi ve pil harcamasin.
	if ( 'IntersectionObserver' in window ) {
		new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					if ( ! userPaused ) {
						play();
					}
				} else if ( ! video.paused ) {
					video.pause();
				}
			} );
		}, { threshold: 0.15 } ).observe( stage );
	} else if ( ! userPaused ) {
		play();
	}
} )();
