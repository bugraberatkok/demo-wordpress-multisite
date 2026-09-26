/**
 * Buyutme penceresinde ikinci kademe yakinlastirma ("zoom'un zoom'u").
 *
 *   var zoom = woodZoom( stage, image, { in, out, reset, level, swipe } );
 *   // swipe( 1 | -1 ): istege bagli; sigdirilmisken yatay kaydirmada cagrilir.
 *   zoom.reset();   // gorsel degisince
 *   zoom.key( event ); // + - 0 tuslari; islendiyse true
 *
 * Tiklama: 1x'te tiklanan noktaya 2,5x yaklasir, yakinken geri doner.
 * Tekerlek ve iki parmak: 1x-5x arasi, imlecin / parmaklarin altindaki nokta
 * sabit kalir. Yakinken surukleyerek gezilir; gorsel cerceveden kacmaz.
 * Bagimsiz dosya: hicbir kutuphane gerektirmez.
 */
( function () {
	'use strict';

	var MIN = 1;
	var MAX = 5;
	var STEP = 1.5;
	var TAP = 2.5;

	window.woodZoom = function ( stage, image, ui ) {
		ui = ui || {};

		var state = { scale: 1, x: 0, y: 0 };
		var pointers = new Map();
		var pinch = null;
		var drag = null;
		var moved = false;
		var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		stage.style.touchAction = 'none';
		stage.style.overflow = 'hidden';
		image.style.transformOrigin = '50% 50%';
		image.style.willChange = 'transform';
		image.draggable = false;

		var clamp = function () {
			var limitX = Math.max( 0, ( image.offsetWidth * state.scale - stage.clientWidth ) / 2 );
			var limitY = Math.max( 0, ( image.offsetHeight * state.scale - stage.clientHeight ) / 2 );

			state.x = Math.min( limitX, Math.max( -limitX, state.x ) );
			state.y = Math.min( limitY, Math.max( -limitY, state.y ) );
		};

		var render = function ( animate ) {
			clamp();
			image.style.transition = animate && ! reduce ? 'transform 180ms ease-out' : 'none';
			image.style.transform = 'translate(' + state.x + 'px,' + state.y + 'px) scale(' + state.scale + ')';
			stage.style.cursor = state.scale > 1 ? ( drag ? 'grabbing' : 'grab' ) : 'zoom-in';

			if ( ui.level ) {
				ui.level.textContent = '%' + Math.round( state.scale * 100 );
			}
			if ( ui.out ) {
				ui.out.disabled = state.scale <= MIN;
			}
			if ( ui.in ) {
				ui.in.disabled = state.scale >= MAX;
			}
			if ( ui.reset ) {
				ui.reset.disabled = state.scale <= MIN;
			}
		};

		// Ekrandaki (clientX, clientY) noktasi yerinde kalacak sekilde olcekle.
		var zoomAt = function ( scale, clientX, clientY, animate ) {
			var next = Math.min( MAX, Math.max( MIN, scale ) );
			var rect = stage.getBoundingClientRect();
			var px = ( undefined === clientX ? rect.left + rect.width / 2 : clientX ) - ( rect.left + rect.width / 2 );
			var py = ( undefined === clientY ? rect.top + rect.height / 2 : clientY ) - ( rect.top + rect.height / 2 );
			var ratio = next / state.scale;

			state.x = px - ( px - state.x ) * ratio;
			state.y = py - ( py - state.y ) * ratio;
			state.scale = next;

			if ( MIN === next ) {
				state.x = 0;
				state.y = 0;
			}

			render( animate );
		};

		var reset = function () {
			state = { scale: 1, x: 0, y: 0 };
			pointers.clear();
			pinch = null;
			drag = null;
			render( false );
		};

		stage.addEventListener( 'wheel', function ( event ) {
			event.preventDefault();
			zoomAt( state.scale * ( event.deltaY < 0 ? 1.25 : 0.8 ), event.clientX, event.clientY, false );
		}, { passive: false } );

		stage.addEventListener( 'pointerdown', function ( event ) {
			stage.setPointerCapture( event.pointerId );
			pointers.set( event.pointerId, { x: event.clientX, y: event.clientY } );
			moved = false;

			if ( 2 === pointers.size ) {
				var p = Array.from( pointers.values() );
				pinch = { distance: Math.hypot( p[0].x - p[1].x, p[0].y - p[1].y ), scale: state.scale };
				drag = null;
			} else if ( 1 === pointers.size ) {
				drag = { x: event.clientX, y: event.clientY, startX: state.x, startY: state.y };
			}
		} );

		stage.addEventListener( 'pointermove', function ( event ) {
			if ( ! pointers.has( event.pointerId ) ) {
				return;
			}

			pointers.set( event.pointerId, { x: event.clientX, y: event.clientY } );

			if ( pinch && 2 === pointers.size ) {
				var p = Array.from( pointers.values() );
				var distance = Math.hypot( p[0].x - p[1].x, p[0].y - p[1].y );
				moved = true;
				zoomAt( pinch.scale * ( distance / pinch.distance ), ( p[0].x + p[1].x ) / 2, ( p[0].y + p[1].y ) / 2, false );
			} else if ( drag && state.scale > 1 ) {
				var dx = event.clientX - drag.x;
				var dy = event.clientY - drag.y;

				if ( Math.abs( dx ) + Math.abs( dy ) > 3 ) {
					moved = true;
				}

				state.x = drag.startX + dx;
				state.y = drag.startY + dy;
				render( false );
			}
		} );

		var release = function ( event ) {
			// Sigdirilmisken yatay kaydirma: galeride onceki / sonraki gorsel
			// (ui.swipe verildiyse). Ardindan gelen tiklama yakinlastirmaz.
			if ( ui.swipe && drag && ! moved && ! pinch && state.scale <= MIN && 'pointerup' === event.type ) {
				var sx = event.clientX - drag.x;
				var sy = event.clientY - drag.y;

				if ( Math.abs( sx ) > 50 && Math.abs( sx ) > Math.abs( sy ) * 1.5 ) {
					moved = true;
					ui.swipe( sx < 0 ? 1 : -1 );
				}
			}

			pointers.delete( event.pointerId );

			if ( pointers.size < 2 ) {
				pinch = null;
			}

			if ( 0 === pointers.size ) {
				drag = null;
				render( false );
			}
		};

		stage.addEventListener( 'pointerup', release );
		stage.addEventListener( 'pointercancel', release );

		// Surukleme ya da iki parmak degilse: tiklama yakinlastirir / geri alir.
		stage.addEventListener( 'click', function ( event ) {
			if ( moved ) {
				moved = false;
				return;
			}

			if ( state.scale > 1 ) {
				zoomAt( 1, undefined, undefined, true );
			} else {
				zoomAt( TAP, event.clientX, event.clientY, true );
			}
		} );

		if ( ui.in ) {
			ui.in.addEventListener( 'click', function () { zoomAt( state.scale * STEP, undefined, undefined, true ); } );
		}
		if ( ui.out ) {
			ui.out.addEventListener( 'click', function () { zoomAt( state.scale / STEP, undefined, undefined, true ); } );
		}
		if ( ui.reset ) {
			ui.reset.addEventListener( 'click', function () { zoomAt( 1, undefined, undefined, true ); } );
		}

		image.addEventListener( 'load', reset );
		window.addEventListener( 'resize', function () { render( false ); } );

		reset();

		return {
			reset: reset,
			isZoomed: function () { return state.scale > 1; },
			key: function ( event ) {
				if ( '+' === event.key || '=' === event.key ) {
					zoomAt( state.scale * STEP, undefined, undefined, true );
				} else if ( '-' === event.key || '_' === event.key ) {
					zoomAt( state.scale / STEP, undefined, undefined, true );
				} else if ( '0' === event.key ) {
					zoomAt( 1, undefined, undefined, true );
				} else {
					return false;
				}

				event.preventDefault();
				return true;
			},
		};
	};
}() );
