/**
 * Icerik Studyosu panel davranislari.
 *
 * - Kaydedilmemis degisiklik takibi ve "Vazgec"
 * - Tekrarli satir ekle / sil / yukari-asagi tasi
 * - Ikon seciminde gorsel geri bildirim
 *
 * Sunucuya giden tek yol form gonderimidir; burada hicbir icerik yazilmaz.
 */
( function () {
	'use strict';

	var l10n = window.nwcsL10n || {};
	var form = document.querySelector( '.nwcs-form' );

	if ( ! form ) {
		return;
	}

	var dirty = false;
	var badge = form.querySelector( '[data-nwcs-dirty]' );
	var rowCounter = 0;

	function markDirty() {
		if ( dirty ) {
			return;
		}
		dirty = true;
		if ( badge ) {
			badge.hidden = false;
		}
	}

	form.addEventListener( 'input', markDirty );
	form.addEventListener( 'change', markDirty );

	form.addEventListener( 'submit', function () {
		dirty = false;
	} );

	window.addEventListener( 'beforeunload', function ( event ) {
		if ( ! dirty ) {
			return;
		}
		event.preventDefault();
		event.returnValue = l10n.confirmLeave || '';
	} );

	// "Vazgec": son kaydedilen hali sunucudan yeniden yukler.
	var discard = form.querySelector( '[data-nwcs-discard]' );
	if ( discard ) {
		discard.addEventListener( 'click', function () {
			if ( dirty && ! window.confirm( l10n.confirmDiscard ) ) {
				return;
			}
			dirty = false;
			window.location.reload();
		} );
	}

	// Ikon secimi
	form.addEventListener( 'change', function ( event ) {
		var input = event.target;
		if ( ! input.matches || ! input.matches( '.nwcs-icons input[type="radio"]' ) ) {
			return;
		}
		var group = input.closest( '.nwcs-icons' );
		group.querySelectorAll( '.nwcs-icon' ).forEach( function ( label ) {
			label.classList.toggle( 'is-active', label.contains( input ) );
		} );
	} );

	// Gorsel secimi degisince onizlemeyi guncelle
	form.addEventListener( 'change', function ( event ) {
		var input = event.target;
		if ( ! input.matches || ! input.matches( '.nwcs-image input[type="file"]' ) ) {
			return;
		}
		var wrap = input.closest( '.nwcs-image' );
		var preview = wrap && wrap.querySelector( '.nwcs-image__preview' );
		if ( ! preview || ! input.files || ! input.files[0] ) {
			return;
		}
		var url = URL.createObjectURL( input.files[0] );
		preview.innerHTML = '';
		var img = document.createElement( 'img' );
		img.src = url;
		img.alt = '';
		preview.appendChild( img );
	} );

	/**
	 * Satir numaralarini ve _sort degerlerini DOM sirasina gore yeniler.
	 */
	function renumber( repeater ) {
		var rows = repeater.querySelectorAll( '[data-nwcs-rows] > [data-nwcs-row]' );
		rows.forEach( function ( row, index ) {
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

	form.addEventListener( 'click', function ( event ) {
		var button = event.target.closest ? event.target.closest( 'button' ) : null;

		if ( ! button || ! form.contains( button ) ) {
			return;
		}

		// Satir ekle
		if ( button.hasAttribute( 'data-nwcs-add-row' ) ) {
			var repeater = button.closest( '[data-nwcs-repeater]' );
			var list = repeater.querySelector( '[data-nwcs-rows]' );
			var max = parseInt( repeater.getAttribute( 'data-max' ), 10 ) || 20;

			if ( list.children.length >= max ) {
				window.alert( 'Bu bileşende en fazla ' + max + ' satır olabilir.' );
				return;
			}

			var template = repeater.querySelector( '[data-nwcs-row-template]' );
			rowCounter += 1;
			var markup = template.innerHTML.split( '__ROW__' ).join( 'n' + rowCounter );
			var holder = document.createElement( 'div' );
			holder.innerHTML = markup;
			var newRow = holder.querySelector( '[data-nwcs-row]' );
			list.appendChild( newRow );
			renumber( repeater );
			markDirty();
			var firstInput = newRow.querySelector( 'input[type="text"], textarea' );
			if ( firstInput ) {
				firstInput.focus();
			}
			return;
		}

		// Satir sil
		if ( button.hasAttribute( 'data-nwcs-remove-row' ) ) {
			if ( ! window.confirm( l10n.confirmRow ) ) {
				return;
			}
			var rowToRemove = button.closest( '[data-nwcs-row]' );
			var parentRepeater = button.closest( '[data-nwcs-repeater]' );
			rowToRemove.remove();
			renumber( parentRepeater );
			markDirty();
			return;
		}

		// Yukari / asagi tasi
		if ( button.hasAttribute( 'data-nwcs-move' ) ) {
			var direction = button.getAttribute( 'data-nwcs-move' );
			var row = button.closest( '[data-nwcs-row]' );
			var target = null;

			if ( 'up' === direction ) {
				target = row.previousElementSibling;
				if ( target ) {
					row.parentNode.insertBefore( row, target );
				}
			} else {
				target = row.nextElementSibling;
				if ( target ) {
					row.parentNode.insertBefore( target, row );
				}
			}

			if ( target ) {
				renumber( button.closest( '[data-nwcs-repeater]' ) );
				markDirty();
			}
		}
	} );
}() );
