/**
 * Icerik Studyosu panel davranislari.
 *
 * - Onizlemeden gelen tiklamayi karsilar, ilgili bilesenin formunu yukler
 * - Formu AJAX ile kaydeder, onizlemeyi tazeler
 * - Tekrarli satir ekle/sil/tasi, bolum sirasi (yukari/asagi)
 * - Kaydedilmemis degisiklik takibi ve "Vazgec"
 *
 * Sunucuya giden her istek panel nonce'u ile imzalanir. JavaScript kapaliyken
 * ayni islevler klasik form gonderimiyle calisir.
 */
( function () {
	'use strict';

	var cfg = window.nwcsPanel || {};
	var T = cfg.text || {};

	var wrap = document.querySelector( '.nwcs-wrap' );
	if ( ! wrap ) {
		return;
	}

	var siteId = wrap.getAttribute( 'data-nwcs-site' );
	var pageKey = wrap.getAttribute( 'data-nwcs-page' );
	var editorBox = wrap.querySelector( '[data-nwcs-editor]' );
	var previewWrap = wrap.querySelector( '[data-nwcs-preview-wrap]' );
	var frame = wrap.querySelector( '[data-nwcs-preview]' );
	var toast = wrap.querySelector( '[data-nwcs-toast]' );
	var screens = {
		list: wrap.querySelector( '[data-nwcs-screen="list"]' ),
		editor: wrap.querySelector( '[data-nwcs-screen="editor"]' )
	};

	var dirty = false;
	var rowCounter = 0;
	var pendingFocus = null;

	/* ---------------- yardimcilar ---------------- */

	function showToast( message, kind ) {
		if ( ! toast ) {
			return;
		}
		toast.textContent = message;
		toast.className = 'nwcs-toast' + ( kind ? ' is-' + kind : '' );
		toast.hidden = false;

		clearTimeout( showToast.timer );
		if ( 'busy' !== kind ) {
			showToast.timer = setTimeout( function () {
				toast.hidden = true;
			}, 3200 );
		}
	}

	function setDirty( value ) {
		dirty = value;
		var badge = wrap.querySelector( '[data-nwcs-dirty]' );
		if ( badge ) {
			badge.hidden = ! value;
		}
	}

	function showScreen( name ) {
		Object.keys( screens ).forEach( function ( key ) {
			if ( screens[ key ] ) {
				screens[ key ].classList.toggle( 'is-active', key === name );
			}
		} );
	}

	function post( action, data ) {
		var body = data instanceof FormData ? data : new FormData();

		if ( ! ( data instanceof FormData ) ) {
			Object.keys( data || {} ).forEach( function ( key ) {
				if ( Array.isArray( data[ key ] ) ) {
					data[ key ].forEach( function ( item ) {
						body.append( key + '[]', item );
					} );
				} else {
					body.append( key, data[ key ] );
				}
			} );
		}

		body.set( 'action', action );
		body.set( 'nonce', cfg.nonce );

		return fetch( cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' } )
			.then( function ( response ) {
				return response.json();
			} );
	}

	function reloadPreview() {
		if ( ! frame ) {
			return;
		}
		previewWrap.classList.add( 'is-loading' );
		frame.contentWindow.location.reload();
	}

	/* ---------------- duzenleyici yukleme ---------------- */

	function openComponent( componentKey, focus ) {
		if ( dirty && ! window.confirm( T.confirmDiscard ) ) {
			return;
		}

		pendingFocus = focus || null;
		setDirty( false );
		showScreen( 'editor' );
		editorBox.innerHTML = '<p class="nwcs-empty">' + ( T.loading || '' ) + '</p>';

		post( 'nwcs_editor', { site: siteId, content_page: pageKey, component: componentKey } )
			.then( function ( result ) {
				if ( ! result || ! result.success ) {
					editorBox.innerHTML = '<p class="nwcs-empty">Bölüm yüklenemedi.</p>';
					return;
				}

				editorBox.innerHTML = result.data.html;
				markActiveSection( componentKey );
				applyFocus();
				updateHistory( componentKey );
			} )
			.catch( function () {
				editorBox.innerHTML = '<p class="nwcs-empty">Bölüm yüklenemedi.</p>';
			} );
	}

	function markActiveSection( componentKey ) {
		wrap.querySelectorAll( '[data-nwcs-open-component]' ).forEach( function ( link ) {
			link.classList.toggle( 'is-active', link.getAttribute( 'data-nwcs-open-component' ) === componentKey );
		} );
	}

	function updateHistory( componentKey ) {
		var url = new URL( window.location.href );
		url.searchParams.set( 'component', componentKey );
		window.history.replaceState( {}, '', url.toString() );
	}

	/**
	 * Onizlemeden gelen alan yolunu forma yansitir.
	 * focus = { field: 'title' } veya { field: 'items.title', row: 2 }
	 */
	function applyFocus() {
		if ( ! pendingFocus ) {
			return;
		}

		var focus = pendingFocus;
		pendingFocus = null;

		var node = null;

		if ( null !== focus.row && undefined !== focus.row ) {
			var repeaterName = focus.field.split( '.' )[0];
			var repeater = editorBox.querySelector( '[data-nwcs-repeater][data-nwcs-field="' + repeaterName + '"]' );
			var row = repeater && repeater.querySelectorAll( '[data-nwcs-row]' )[ parseInt( focus.row, 10 ) ];

			if ( row ) {
				row.classList.add( 'is-nwcs-focus' );
				node = row.querySelector( '[data-nwcs-field="' + focus.field + '"]' ) || row;
			}
		} else {
			node = editorBox.querySelector( '[data-nwcs-field="' + focus.field + '"]' );
		}

		if ( ! node ) {
			return;
		}

		node.classList.add( 'is-nwcs-focus' );
		node.scrollIntoView( { block: 'center' } );

		var input = node.querySelector( 'input[type="text"], textarea, select' );
		if ( input ) {
			input.focus();
			if ( input.select ) {
				input.select();
			}
		}

		setTimeout( function () {
			node.classList.remove( 'is-nwcs-focus' );
		}, 2200 );
	}

	/* ---------------- onizleme kopru mesajlari ---------------- */

	window.addEventListener( 'message', function ( event ) {
		if ( ! event.data || 'object' !== typeof event.data ) {
			return;
		}

		if ( cfg.previewOrigin && event.origin !== cfg.previewOrigin ) {
			return;
		}

		if ( 'nwcs:ready' === event.data.type ) {
			previewWrap.classList.remove( 'is-loading' );
			return;
		}

		if ( 'nwcs:edit' !== event.data.type ) {
			return;
		}

		var parts = String( event.data.target || '' ).split( '.' );
		if ( parts.length < 2 ) {
			return;
		}

		var componentKey = parts[1];
		var fieldPath = parts.slice( 2 ).join( '.' );

		openComponent( componentKey, {
			field: fieldPath,
			row: null === event.data.row ? null : event.data.row
		} );
	} );

	/* ---------------- tiklamalar ---------------- */

	wrap.addEventListener( 'click', function ( event ) {
		var target = event.target;

		// Bolum ac
		var opener = target.closest ? target.closest( '[data-nwcs-open-component]' ) : null;
		if ( opener ) {
			event.preventDefault();
			openComponent( opener.getAttribute( 'data-nwcs-open-component' ), null );
			return;
		}

		// Bolumler ekranina don
		var back = target.closest ? target.closest( '[data-nwcs-back]' ) : null;
		if ( back ) {
			event.preventDefault();
			if ( dirty && ! window.confirm( T.confirmDiscard ) ) {
				return;
			}
			setDirty( false );
			showScreen( 'list' );
			return;
		}

		// Onizleme genisligi
		var device = target.closest ? target.closest( '[data-nwcs-device]' ) : null;
		if ( device ) {
			wrap.querySelectorAll( '[data-nwcs-device]' ).forEach( function ( button ) {
				button.classList.toggle( 'is-active', button === device );
			} );
			previewWrap.classList.toggle( 'is-mobile', 'mobile' === device.getAttribute( 'data-nwcs-device' ) );
			return;
		}

		// Onizlemeyi yenile
		if ( target.closest && target.closest( '[data-nwcs-reload]' ) ) {
			reloadPreview();
			return;
		}

		// Bolum sirasi
		var move = target.closest ? target.closest( '.nwcs-section__move [data-nwcs-move]' ) : null;
		if ( move ) {
			moveSection( move );
			return;
		}

		// Form ici dugmeler
		handleFormButtons( event );
	} );

	/* ---------------- bolum sirasi ---------------- */

	function moveSection( button ) {
		var item = button.closest( '.nwcs-section' );
		var list = item.parentNode;
		var direction = button.getAttribute( 'data-nwcs-move' );
		var sortableItems = Array.prototype.filter.call( list.children, function ( node ) {
			return node.classList.contains( 'is-sortable' );
		} );
		var index = sortableItems.indexOf( item );

		if ( 'up' === direction && index > 0 ) {
			list.insertBefore( item, sortableItems[ index - 1 ] );
		} else if ( 'down' === direction && index < sortableItems.length - 1 ) {
			list.insertBefore( sortableItems[ index + 1 ], item );
		} else {
			return;
		}

		item.classList.add( 'is-moving' );
		setTimeout( function () {
			item.classList.remove( 'is-moving' );
		}, 600 );

		saveOrder( list );
	}

	function saveOrder( list ) {
		var order = Array.prototype.filter
			.call( list.children, function ( node ) {
				return node.classList.contains( 'is-sortable' );
			} )
			.map( function ( node ) {
				return node.getAttribute( 'data-nwcs-section-key' );
			} );

		showToast( T.saving || '', 'busy' );

		post( 'nwcs_order', { site: siteId, content_page: pageKey, order: order } )
			.then( function ( result ) {
				if ( result && result.success ) {
					showToast( result.data.message, null );
					reloadPreview();
				} else {
					showToast( T.saveError || '', 'error' );
				}
			} )
			.catch( function () {
				showToast( T.saveError || '', 'error' );
			} );
	}

	/* ---------------- form davranislari ---------------- */

	function currentForm() {
		return editorBox ? editorBox.querySelector( '[data-nwcs-form]' ) : null;
	}

	function renumber( repeater ) {
		repeater.querySelectorAll( '[data-nwcs-rows] > [data-nwcs-row]' ).forEach( function ( row, index ) {
			var sort = row.querySelector( '[data-nwcs-sort]' );
			if ( sort ) {
				sort.value = String( index );
			}
			var number = row.querySelector( '[data-nwcs-row-number]' );
			if ( number ) {
				number.textContent = String( index + 1 );
			}
		} );
	}

	function handleFormButtons( event ) {
		var button = event.target.closest ? event.target.closest( 'button' ) : null;
		var form = currentForm();

		if ( ! button || ! form || ! form.contains( button ) ) {
			return;
		}

		// Vazgec
		if ( button.hasAttribute( 'data-nwcs-discard' ) ) {
			if ( dirty && ! window.confirm( T.confirmDiscard ) ) {
				return;
			}
			setDirty( false );
			openComponent( form.querySelector( '[name="component"]' ).value, null );
			return;
		}

		// Satir ekle
		if ( button.hasAttribute( 'data-nwcs-add-row' ) ) {
			event.preventDefault();
			var repeater = button.closest( '[data-nwcs-repeater]' );
			var list = repeater.querySelector( '[data-nwcs-rows]' );
			var max = parseInt( repeater.getAttribute( 'data-max' ), 10 ) || 20;

			if ( list.children.length >= max ) {
				showToast( 'Bu bölümde en fazla ' + max + ' satır olabilir.', 'error' );
				return;
			}

			rowCounter += 1;
			var markup = repeater
				.querySelector( '[data-nwcs-row-template]' )
				.innerHTML.split( '__ROW__' )
				.join( 'n' + rowCounter );
			var holder = document.createElement( 'div' );
			holder.innerHTML = markup;
			var newRow = holder.querySelector( '[data-nwcs-row]' );
			list.appendChild( newRow );
			renumber( repeater );
			setDirty( true );

			var first = newRow.querySelector( 'input[type="text"], textarea' );
			if ( first ) {
				first.focus();
			}
			return;
		}

		// Satir sil
		if ( button.hasAttribute( 'data-nwcs-remove-row' ) ) {
			event.preventDefault();
			if ( ! window.confirm( T.confirmRow ) ) {
				return;
			}
			var repeaterOfRow = button.closest( '[data-nwcs-repeater]' );
			button.closest( '[data-nwcs-row]' ).remove();
			renumber( repeaterOfRow );
			setDirty( true );
			return;
		}

		// Satir tasi
		if ( button.hasAttribute( 'data-nwcs-move' ) ) {
			event.preventDefault();
			var direction = button.getAttribute( 'data-nwcs-move' );
			var row = button.closest( '[data-nwcs-row]' );
			var sibling = 'up' === direction ? row.previousElementSibling : row.nextElementSibling;

			if ( ! sibling ) {
				return;
			}

			if ( 'up' === direction ) {
				row.parentNode.insertBefore( row, sibling );
			} else {
				row.parentNode.insertBefore( sibling, row );
			}

			renumber( button.closest( '[data-nwcs-repeater]' ) );
			setDirty( true );
		}
	}

	// Degisiklik takibi
	wrap.addEventListener( 'input', function ( event ) {
		if ( currentForm() && currentForm().contains( event.target ) ) {
			setDirty( true );
		}
	} );
	wrap.addEventListener( 'change', function ( event ) {
		var form = currentForm();

		if ( ! form || ! form.contains( event.target ) ) {
			return;
		}

		setDirty( true );

		// Ikon secimi
		if ( event.target.matches( '.nwcs-icons input[type="radio"]' ) ) {
			var group = event.target.closest( '.nwcs-icons' );
			group.querySelectorAll( '.nwcs-icon' ).forEach( function ( label ) {
				label.classList.toggle( 'is-active', label.contains( event.target ) );
			} );
		}

		// Yeni gorsel onizlemesi
		if ( event.target.matches( '.nwcs-image input[type="file"]' ) && event.target.files && event.target.files[0] ) {
			var box = event.target.closest( '.nwcs-image' ).querySelector( '.nwcs-image__preview' );
			box.innerHTML = '';
			var img = document.createElement( 'img' );
			img.src = URL.createObjectURL( event.target.files[0] );
			img.alt = '';
			box.appendChild( img );
		}
	} );

	// Kaydet
	wrap.addEventListener( 'submit', function ( event ) {
		var form = event.target.closest ? event.target.closest( '[data-nwcs-form]' ) : null;

		if ( ! form ) {
			return;
		}

		event.preventDefault();

		var data = new FormData( form );
		showToast( T.saving || '', 'busy' );

		post( 'nwcs_save_ajax', data )
			.then( function ( result ) {
				if ( ! result || ! result.success ) {
					showToast( ( result && result.data && result.data.message ) || T.saveError, 'error' );
					return;
				}

				setDirty( false );
				showToast( result.data.message, result.data.hasError ? 'error' : null );
				reloadPreview();

				// Yuklenen dosya girdileri temizlensin ki tekrar gonderilmesin.
				form.querySelectorAll( 'input[type="file"]' ).forEach( function ( input ) {
					input.value = '';
				} );
			} )
			.catch( function () {
				showToast( T.saveError || '', 'error' );
			} );
	} );

	window.addEventListener( 'beforeunload', function ( event ) {
		if ( ! dirty ) {
			return;
		}
		event.preventDefault();
		event.returnValue = T.confirmLeave || '';
	} );

	// Onizleme yuklendiginde gostergeyi kapat
	if ( frame ) {
		frame.addEventListener( 'load', function () {
			previewWrap.classList.remove( 'is-loading' );
		} );
	}
}() );
