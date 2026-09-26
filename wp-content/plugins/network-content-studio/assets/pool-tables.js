/**
 * Urun Havuzu -> urun formu -> "Ürün Tabloları": hucreli tablo duzenleyici.
 *
 * Formda yalnizca ozet durur; duzenleme genis pencerede, her seferinde tek
 * tablo (altta Excel'deki gibi sayfa sekmeleri). Durum tek dizide tutulur
 * ({title, head[], rows[][]}); her degisiklikte gizli alana JSON yazilir,
 * sunucu kaydederken temizler. Satir/sutun ekleme-silme tabloyu yeniden
 * cizer; yazarken yalnizca durum guncellenir. Excel'den yapistirilan
 * hucreler (sekme + satir sonu) tiklanan hucreden baslayarak yerlesir.
 */
( function () {
	'use strict';

	var MAX_COLS = 30;
	var MAX_ROWS = 500;

	var root = document.querySelector( '[data-nwcs-ptables]' );

	if ( ! root ) {
		return;
	}

	var field = root.querySelector( '[data-nwcs-ptables-data]' );
	var sheet = root.querySelector( '[data-nwcs-ptables-list]' );
	var tabs = root.querySelector( '[data-nwcs-ptables-tabs]' );
	var summary = root.querySelector( '[data-nwcs-ptables-summary]' );
	var modal = root.querySelector( '[data-nwcs-ptables-modal]' );
	var tables = parse( field.value );
	var active = 0;

	function parse( json ) {
		var data;

		try {
			data = JSON.parse( json || '[]' );
		} catch ( e ) {
			data = [];
		}

		return ( Array.isArray( data ) ? data : [] ).map( function ( table ) {
			var head = Array.isArray( table.head ) ? table.head.map( String ) : [];
			var rows = Array.isArray( table.rows ) ? table.rows.map( function ( row ) {
				return Array.isArray( row ) ? row.map( String ) : [];
			} ) : [];

			return normalize( { title: String( table.title || '' ), head: head, rows: rows } );
		} );
	}

	/** Basliklar ve her satir ayni sutun sayisinda; en az 1 sutun, 1 satir. */
	function normalize( table ) {
		var cols = Math.max( 1, table.head.length );

		table.rows.forEach( function ( row ) {
			cols = Math.max( cols, row.length );
		} );

		if ( ! table.rows.length ) {
			table.rows.push( [] );
		}

		pad( table.head, cols );
		table.rows.forEach( function ( row ) {
			pad( row, cols );
		} );

		return table;
	}

	function pad( row, cols ) {
		while ( row.length < cols ) {
			row.push( '' );
		}
	}

	function blankTable() {
		return { title: '', head: [ '', '', '' ], rows: [ [ '', '', '' ], [ '', '', '' ], [ '', '', '' ] ] };
	}

	function hasRows( table ) {
		return table.rows.some( function ( row ) {
			return row.join( '' ).trim();
		} );
	}

	function isEmpty( table ) {
		return ! table.title.trim() && ! table.head.join( '' ).trim() && ! hasRows( table );
	}

	function tableName( table, index ) {
		return table.title.trim() || 'Tablo ' + ( index + 1 );
	}

	function sync() {
		field.value = JSON.stringify( tables );
	}

	function el( tag, className, text ) {
		var node = document.createElement( tag );

		if ( className ) {
			node.className = className;
		}

		if ( undefined !== text ) {
			node.textContent = text;
		}

		return node;
	}

	function action( label, kind, className, attrs ) {
		var node = el( 'button', className, label );

		node.type = 'button';
		node.setAttribute( 'data-nwcs-pt', kind );

		Object.keys( attrs || {} ).forEach( function ( key ) {
			node.setAttribute( key, attrs[ key ] );
		} );

		return node;
	}

	function cellInput( value, r, c ) {
		var input = el( 'input', 'nwcs-ptm__cell' );

		input.type = 'text';
		input.value = value;
		input.setAttribute( 'data-r', r );
		input.setAttribute( 'data-c', c );

		if ( r < 0 ) {
			input.placeholder = 'Sütun adı';
			input.setAttribute( 'aria-label', ( c + 1 ) + '. sütunun adı' );
		} else {
			input.setAttribute( 'aria-label', 'Satır ' + ( r + 1 ) + ', sütun ' + ( c + 1 ) );
		}

		return input;
	}

	/* ---------- Pencere: etkin tablo ---------- */
	function renderSheet( focus ) {
		sheet.textContent = '';

		var table = tables[ active ];

		if ( ! table ) {
			var empty = el( 'div', 'nwcs-ptm__empty' );
			empty.appendChild( el( 'p', 'nwcs-ptm__empty-title', 'Bu üründe tablo yok' ) );
			empty.appendChild( el( 'p', 'nwcs-ptm__empty-text', 'Ölçü, kesit ya da model tablosu ekleyin; ürün sayfasında detay metninin altında görünür.' ) );
			empty.appendChild( action( '+ İlk tabloyu ekle', 'table-add', 'button button-primary' ) );
			sheet.appendChild( empty );
			renderTabs();
			sync();

			return;
		}

		// Ust serit: tablo basligi, boyut, sil.
		var bar = el( 'div', 'nwcs-ptm__bar' );
		var title = el( 'input', 'nwcs-ptm__title' );

		title.type = 'text';
		title.value = table.title;
		title.placeholder = 'Tablo başlığı yazın (örn. Kesit ve Uzunluklar)';
		title.setAttribute( 'aria-label', 'Tablo başlığı' );
		title.setAttribute( 'data-title', '' );
		bar.appendChild( title );

		bar.appendChild( el( 'span', 'nwcs-ptm__size', table.rows.length + ' satır × ' + table.head.length + ' sütun' ) );
		bar.appendChild( action( 'Tabloyu sil', 'table-remove', 'nwcs-ptm__remove' ) );
		sheet.appendChild( bar );

		// Hucreler: iki yonde kayar; baslik satiri ve satir numaralari sabit.
		var scroll = el( 'div', 'nwcs-ptm__scroll' );
		var grid = el( 'table', 'nwcs-ptm__grid' );
		var thead = el( 'thead' );
		var headRow = el( 'tr' );

		headRow.appendChild( el( 'th', 'nwcs-ptm__corner' ) );

		table.head.forEach( function ( value, c ) {
			var th = el( 'th', 'nwcs-ptm__colhead' );
			th.appendChild( cellInput( value, -1, c ) );

			if ( table.head.length > 1 ) {
				th.appendChild( action( '×', 'col-remove', 'nwcs-ptm__x', {
					'data-c': c,
					'aria-label': ( c + 1 ) + '. sütunu sil',
					title: 'Sütunu sil',
				} ) );
			}

			headRow.appendChild( th );
		} );

		// Sagdaki dar sutun: basliktaki + yeni sutun acar.
		var addColCell = el( 'th', 'nwcs-ptm__addcol' );
		addColCell.appendChild( action( '+', 'col-add', 'nwcs-ptm__addcol-btn', {
			'aria-label': 'Sütun ekle',
			title: 'Sütun ekle',
		} ) );
		headRow.appendChild( addColCell );

		thead.appendChild( headRow );
		grid.appendChild( thead );

		var tbody = el( 'tbody' );

		table.rows.forEach( function ( row, r ) {
			var tr = el( 'tr' );
			var num = el( 'th', 'nwcs-ptm__num' );

			num.setAttribute( 'scope', 'row' );
			num.appendChild( el( 'span', 'nwcs-ptm__numtext', String( r + 1 ) ) );

			if ( table.rows.length > 1 ) {
				num.appendChild( action( '×', 'row-remove', 'nwcs-ptm__x nwcs-ptm__x--row', {
					'data-r': r,
					'aria-label': ( r + 1 ) + '. satırı sil',
					title: 'Satırı sil',
				} ) );
			}

			tr.appendChild( num );

			row.forEach( function ( value, c ) {
				var td = el( 'td' );
				td.appendChild( cellInput( value, r, c ) );
				tr.appendChild( td );
			} );

			tr.appendChild( el( 'td', 'nwcs-ptm__addcol' ) );
			tbody.appendChild( tr );
		} );

		// Son satir: yeni satir ekleme seridi.
		var addRow = el( 'tr', 'nwcs-ptm__addrow' );
		var addRowCell = el( 'td' );

		addRowCell.setAttribute( 'colspan', table.head.length + 2 );
		addRowCell.appendChild( action( '+ Satır ekle', 'row-add', 'nwcs-ptm__addrow-btn' ) );
		addRow.appendChild( addRowCell );
		tbody.appendChild( addRow );

		grid.appendChild( tbody );
		scroll.appendChild( grid );
		sheet.appendChild( scroll );

		renderTabs();
		sync();

		if ( focus ) {
			var target = sheet.querySelector( focus );

			if ( target ) {
				target.focus();

				if ( target.scrollIntoView ) {
					target.scrollIntoView( { block: 'nearest', inline: 'nearest' } );
				}
			}
		}
	}

	/* ---------- Pencere: alttaki sayfa sekmeleri ---------- */
	function renderTabs() {
		tabs.textContent = '';

		tables.forEach( function ( table, t ) {
			var tab = action( tableName( table, t ), 'tab', 'nwcs-ptm__tab' + ( t === active ? ' is-active' : '' ), {
				'data-t': t,
			} );

			if ( t === active ) {
				tab.setAttribute( 'aria-current', 'true' );
			}

			tabs.appendChild( tab );
		} );
	}

	/* ---------- Formdaki ozet ---------- */
	function renderSummary() {
		summary.textContent = '';

		if ( ! tables.length ) {
			summary.appendChild( el( 'li', 'nwcs-ptables__none', 'Henüz tablo yok.' ) );

			return;
		}

		tables.forEach( function ( table, t ) {
			var item = el( 'li' );
			var open = el( 'button', 'nwcs-ptables__item' );

			open.type = 'button';
			open.setAttribute( 'data-nwcs-ptables-open', t );
			open.appendChild( el( 'strong', '', tableName( table, t ) ) );
			open.appendChild( el( 'span', '', hasRows( table )
				? table.rows.length + ' satır × ' + table.head.length + ' sütun'
				: 'Boş, sitede görünmez' ) );

			item.appendChild( open );
			summary.appendChild( item );
		} );
	}

	function cellSelector( r, c ) {
		return '.nwcs-ptm__cell[data-r="' + r + '"][data-c="' + c + '"]';
	}

	function num( node, name ) {
		return parseInt( node.getAttribute( name ), 10 );
	}

	function addColumn( table ) {
		if ( table.head.length >= MAX_COLS ) {
			return false;
		}

		table.head.push( '' );
		table.rows.forEach( function ( row ) {
			row.push( '' );
		} );

		return true;
	}

	function addRow( table ) {
		if ( table.rows.length >= MAX_ROWS ) {
			return false;
		}

		var row = [];
		pad( row, table.head.length );
		table.rows.push( row );

		return true;
	}

	function openModal( index ) {
		if ( ! tables.length ) {
			tables.push( blankTable() );
		}

		active = Math.max( 0, Math.min( index || 0, tables.length - 1 ) );
		renderSheet( null );
		modal.showModal();

		// Bos tabloda ilk sutun adina, dolu tabloda basliga odaklan.
		var first = isEmpty( tables[ active ] )
			? sheet.querySelector( cellSelector( -1, 0 ) )
			: sheet.querySelector( '[data-title]' );

		if ( first ) {
			first.focus();
		}
	}

	/* ---------- Tiklamalar ---------- */
	root.addEventListener( 'click', function ( event ) {
		var opener = event.target.closest( '[data-nwcs-ptables-open]' );

		if ( opener ) {
			openModal( parseInt( opener.getAttribute( 'data-nwcs-ptables-open' ), 10 ) || 0 );

			return;
		}

		if ( event.target.closest( '[data-nwcs-ptables-close]' ) ) {
			modal.close();

			return;
		}

		if ( event.target.closest( '[data-nwcs-ptables-add]' ) ) {
			tables.push( blankTable() );
			active = tables.length - 1;
			renderSheet( cellSelector( -1, 0 ) );

			return;
		}

		var button = event.target.closest( '[data-nwcs-pt]' );

		if ( ! button ) {
			return;
		}

		var kind = button.getAttribute( 'data-nwcs-pt' );
		var table = tables[ active ];

		if ( 'tab' === kind ) {
			active = num( button, 'data-t' );
			renderSheet( null );

			return;
		}

		if ( 'table-add' === kind ) {
			tables.push( blankTable() );
			active = tables.length - 1;
			renderSheet( cellSelector( -1, 0 ) );

			return;
		}

		if ( ! table ) {
			return;
		}

		if ( 'table-remove' === kind ) {
			// eslint-disable-next-line no-alert
			if ( ! isEmpty( table ) && ! window.confirm( '“' + tableName( table, active ) + '” silinsin mi? Ürünü kaydettiğinizde sitede de kalkar.' ) ) {
				return;
			}

			tables.splice( active, 1 );
			active = Math.max( 0, active - 1 );
			renderSheet( null );
		} else if ( 'row-add' === kind ) {
			addRow( table );
			renderSheet( cellSelector( table.rows.length - 1, 0 ) );
		} else if ( 'col-add' === kind ) {
			addColumn( table );
			renderSheet( cellSelector( -1, table.head.length - 1 ) );
		} else if ( 'row-remove' === kind && table.rows.length > 1 ) {
			var r = num( button, 'data-r' );
			table.rows.splice( r, 1 );
			renderSheet( cellSelector( Math.min( r, table.rows.length - 1 ), 0 ) );
		} else if ( 'col-remove' === kind && table.head.length > 1 ) {
			var c = num( button, 'data-c' );
			table.head.splice( c, 1 );
			table.rows.forEach( function ( row ) {
				row.splice( c, 1 );
			} );
			renderSheet( cellSelector( -1, Math.min( c, table.head.length - 1 ) ) );
		}
	} );

	/* ---------- Yazma ---------- */
	root.addEventListener( 'input', function ( event ) {
		var input = event.target;
		var table = tables[ active ];

		if ( ! table ) {
			return;
		}

		if ( input.hasAttribute( 'data-title' ) ) {
			table.title = input.value;

			// Sekmedeki ad yazarken guncellensin.
			var tab = tabs.querySelector( '.is-active' );

			if ( tab ) {
				tab.textContent = tableName( table, active );
			}
		} else if ( input.classList.contains( 'nwcs-ptm__cell' ) ) {
			var r = num( input, 'data-r' );
			var c = num( input, 'data-c' );

			if ( r < 0 ) {
				table.head[ c ] = input.value;
			} else {
				table.rows[ r ][ c ] = input.value;
			}
		}

		sync();
	} );

	/* ---------- Enter: alttaki hucreye; son satirda yeni satir ---------- */
	root.addEventListener( 'keydown', function ( event ) {
		var input = event.target;

		if ( 'Enter' !== event.key ) {
			return;
		}

		// Tablo alanlarinda Enter formu gondermesin.
		if ( input.hasAttribute && input.hasAttribute( 'data-title' ) ) {
			event.preventDefault();

			var firstHead = sheet.querySelector( cellSelector( -1, 0 ) );

			if ( firstHead ) {
				firstHead.focus();
			}

			return;
		}

		if ( ! input.classList || ! input.classList.contains( 'nwcs-ptm__cell' ) ) {
			return;
		}

		event.preventDefault();

		var r = num( input, 'data-r' );
		var c = num( input, 'data-c' );
		var table = tables[ active ];
		var next = sheet.querySelector( cellSelector( r + 1, c ) );

		if ( next ) {
			next.focus();
		} else if ( addRow( table ) ) {
			renderSheet( cellSelector( r + 1, c ) );
		}
	} );

	/* ---------- Excel'den yapistirma ---------- */
	root.addEventListener( 'paste', function ( event ) {
		var input = event.target;

		if ( ! input.classList || ! input.classList.contains( 'nwcs-ptm__cell' ) ) {
			return;
		}

		var text = event.clipboardData.getData( 'text' );

		if ( ! text || ( -1 === text.indexOf( '\t' ) && -1 === text.indexOf( '\n' ) ) ) {
			return; // Tek hucrelik metin: tarayici normal yapistirsin.
		}

		event.preventDefault();

		var lines = text.replace( /\r\n?/g, '\n' ).replace( /\n+$/, '' ).split( '\n' );
		var r0 = num( input, 'data-r' );
		var c0 = num( input, 'data-c' );
		var table = tables[ active ];

		lines.forEach( function ( line, i ) {
			var r = r0 + i;

			line.split( '\t' ).forEach( function ( value, j ) {
				var c = c0 + j;

				while ( c >= table.head.length && addColumn( table ) ) {
					// sutun acildi
				}

				if ( c >= table.head.length ) {
					return;
				}

				if ( r < 0 ) {
					table.head[ c ] = value.trim();

					return;
				}

				while ( r >= table.rows.length && addRow( table ) ) {
					// satir acildi
				}

				if ( r < table.rows.length ) {
					table.rows[ r ][ c ] = value.trim();
				}
			} );
		} );

		renderSheet( cellSelector( r0, c0 ) );
	} );

	// Gonderirken son hal yazilsin (bir hucre hala odaktayken de).
	var form = root.closest( 'form' );

	if ( form ) {
		form.addEventListener( 'submit', sync );
	}

	// Esc, x ya da Tamam: ozet guncellenir; degisiklik formda kalir.
	modal.addEventListener( 'close', function () {
		// Hic dokunulmamis bos tablolar ozette kalabalik etmesin.
		tables = tables.filter( function ( table ) {
			return ! isEmpty( table );
		} );
		active = 0;

		sync();
		renderSummary();
	} );

	renderSummary();
}() );
