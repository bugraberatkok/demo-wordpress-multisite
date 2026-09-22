/**
 * Excel'den urun yukleme sihirbazi (Urun Havuzu sayfasi).
 *
 * Uc adim: dosya sec -> sutunlari eslestir -> parcali yukle.
 * Sunucu tarafi: includes/admin/import.php
 */
( function () {
	'use strict';

	var modal = document.querySelector( '[data-nwcs-import]' );
	var undoBar = document.querySelector( '[data-nwcs-undo-bar]' );

	if ( ! modal && ! undoBar ) {
		return;
	}

	var cfg = window.nwcsPanel || {};

	var post = function ( action, payload ) {
		var body = payload instanceof FormData ? payload : new FormData();

		if ( ! ( payload instanceof FormData ) ) {
			Object.keys( payload || {} ).forEach( function ( key ) {
				body.append( key, payload[ key ] );
			} );
		}

		body.append( 'action', action );
		body.append( 'nonce', cfg.nonce );

		return fetch( cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
			.then( function ( response ) { return response.json(); } )
			.then( function ( json ) {
				if ( ! json || ! json.success ) {
					throw new Error( ( json && json.data && json.data.message ) || 'İşlem tamamlanamadı.' );
				}
				return json.data;
			} );
	};

	/* ---------------- Geri alma seridi ---------------- */
	if ( undoBar ) {
		var undoButton = undoBar.querySelector( '[data-nwcs-undo]' );

		undoButton.addEventListener( 'click', function () {
			if ( ! window.confirm( 'Son Excel yüklemesi geri alınacak. Eklenen ürünler silinecek, güncellenenler eski hâline dönecek. Devam edilsin mi?' ) ) {
				return;
			}

			undoButton.disabled = true;
			undoButton.textContent = 'Geri alınıyor…';

			post( 'nwcs_import_undo', {} )
				.then( function ( data ) {
					var message = data.removed + ' ürün silindi, ' + data.restored + ' ürün eski hâline döndü.';

					if ( data.kept && data.kept.length ) {
						message += '\n\nYüklemeden sonra elle düzenlendiği için dokunulmayanlar:\n' + data.kept.join( ', ' );
					}

					window.alert( message );
					window.location.reload();
				} )
				.catch( function ( error ) {
					window.alert( error.message );
					undoButton.disabled = false;
					undoButton.textContent = 'Bu yüklemeyi geri al';
				} );
		} );
	}

	if ( ! modal ) {
		return;
	}

	/* ---------------- Sihirbaz ---------------- */
	var targets = {};

	try {
		targets = JSON.parse( modal.getAttribute( 'data-targets' ) ) || {};
	} catch ( e ) {
		targets = {};
	}

	var steps       = modal.querySelectorAll( '[data-nwcs-steps] li' );
	var panels      = modal.querySelectorAll( '[data-nwcs-panel]' );
	var subtitle    = modal.querySelector( '[data-nwcs-step-sub]' );
	var fileInput   = modal.querySelector( '[data-nwcs-import-file]' );
	var mapRows     = modal.querySelector( '[data-nwcs-map-rows]' );
	var mapWarning  = modal.querySelector( '[data-nwcs-map-warning]' );
	var countLabel  = modal.querySelector( '[data-nwcs-count]' );
	var nextButton  = modal.querySelector( '[data-nwcs-next]' );
	var backButton  = modal.querySelector( '[data-nwcs-back]' );
	var progressBar = modal.querySelector( '[data-nwcs-progress-bar]' );
	var progressText = modal.querySelector( '[data-nwcs-progress-text]' );
	var report      = modal.querySelector( '[data-nwcs-report]' );

	var state = { step: 1, token: '', file: '', columns: [], total: 0, mapping: {} };

	var subtitles = {
		1: 'Dosyanızı seçin',
		2: 'Kolon eşleştirmelerini kontrol edin',
		3: 'Ürünler havuza yazılıyor'
	};

	var goTo = function ( step ) {
		state.step = step;

		steps.forEach( function ( item ) {
			var value = parseInt( item.getAttribute( 'data-step' ), 10 );
			item.classList.toggle( 'is-active', value === step );
			item.classList.toggle( 'is-done', value < step );
		} );

		panels.forEach( function ( panel ) {
			panel.hidden = parseInt( panel.getAttribute( 'data-nwcs-panel' ), 10 ) !== step;
		} );

		subtitle.textContent = subtitles[ step ] || '';
		backButton.hidden = 1 === step || 3 === step;
		nextButton.hidden = 1 === step;

		if ( 2 === step ) {
			nextButton.textContent = 'Ürünleri Yükle';
			validateMapping();
		}
	};

	var reset = function () {
		state = { step: 1, token: '', file: '', columns: [], total: 0, mapping: {} };
		fileInput.value = '';
		mapRows.innerHTML = '';
		report.hidden = true;
		report.innerHTML = '';
		countLabel.textContent = '';
		progressBar.style.width = '0%';
		nextButton.disabled = true;
		nextButton.textContent = 'Devam';
		goTo( 1 );
	};

	/* --- Eslestirme tablosu --- */
	var buildMapping = function ( data ) {
		mapRows.innerHTML = '';

		data.columns.forEach( function ( column ) {
			var row = document.createElement( 'tr' );

			var head = document.createElement( 'td' );
			var code = document.createElement( 'code' );
			code.textContent = column.label;
			head.appendChild( code );

			var pick = document.createElement( 'td' );
			var select = document.createElement( 'select' );
			select.className = 'nwcs-select';
			select.setAttribute( 'data-column', String( column.index ) );

			Object.keys( targets ).forEach( function ( key ) {
				var option = document.createElement( 'option' );
				option.value = key;
				option.textContent = targets[ key ].label + ( targets[ key ].required ? ' *' : '' );
				select.appendChild( option );
			} );

			var ignore = document.createElement( 'option' );
			ignore.value = '';
			ignore.textContent = '-- Yoksay --';
			select.appendChild( ignore );

			select.value = data.guess[ column.index ] || '';
			pick.appendChild( select );

			var sample = document.createElement( 'td' );
			sample.className = 'nwcs-maptable__sample';
			sample.textContent = column.sample;

			row.appendChild( head );
			row.appendChild( pick );
			row.appendChild( sample );
			mapRows.appendChild( row );

			select.addEventListener( 'change', function () {
				// Ayni alan iki sutuna atanmasin; oncekini serbest birak.
				if ( select.value ) {
					mapRows.querySelectorAll( 'select' ).forEach( function ( other ) {
						if ( other !== select && other.value === select.value ) {
							other.value = '';
						}
					} );
				}

				validateMapping();
			} );
		} );

		validateMapping();
	};

	var readMapping = function () {
		var mapping = {};

		mapRows.querySelectorAll( 'select' ).forEach( function ( select ) {
			if ( select.value ) {
				mapping[ select.getAttribute( 'data-column' ) ] = select.value;
			}
		} );

		return mapping;
	};

	var validateMapping = function () {
		var mapping = readMapping();
		var hasName = Object.keys( mapping ).some( function ( key ) { return 'name' === mapping[ key ]; } );

		nextButton.disabled = ! hasName;
		mapWarning.hidden = hasName;

		if ( ! hasName ) {
			mapWarning.textContent = 'Devam etmek için bir sütunu “Ürün Adı” ile eşleştirin.';
		}

		countLabel.textContent = state.total
			? state.total.toLocaleString( 'tr-TR' ) + ' ürün yüklenecek'
			: '';

		return hasName;
	};

	/* --- Parcali yukleme --- */
	var runChunk = function ( offset ) {
		return post( 'nwcs_import_run', {
			token: state.token,
			file: state.file,
			mapping: JSON.stringify( state.mapping ),
			offset: offset
		} ).then( function ( data ) {
			if ( data.done ) {
				progressBar.style.width = '100%';
				progressText.textContent = 'Tamamlandı.';
				finish( data );
				return;
			}

			var percent = data.total ? Math.round( ( data.processed / data.total ) * 100 ) : 100;
			progressBar.style.width = percent + '%';
			progressText.textContent = data.processed.toLocaleString( 'tr-TR' ) + ' / ' +
				data.total.toLocaleString( 'tr-TR' ) + ' satır işlendi';

			return runChunk( data.offset );
		} );
	};

	var finish = function ( data ) {
		var counts = data.counts || {};
		var lines = [
			'<strong>' + ( counts.created || 0 ) + '</strong> yeni ürün eklendi',
			'<strong>' + ( counts.updated || 0 ) + '</strong> ürün güncellendi'
		];

		if ( counts.skipped ) {
			lines.push( '<strong>' + counts.skipped + '</strong> satır atlandı' );
		}

		var html = '<ul class="nwcs-report__list"><li>' + lines.join( '</li><li>' ) + '</li></ul>';

		if ( data.skipped && data.skipped.length ) {
			html += '<p class="nwcs-hint">Atlanan satırlar:</p><ul class="nwcs-report__skips">';
			data.skipped.slice( 0, 12 ).forEach( function ( item ) {
				html += '<li>' + item.line + '. satır — ' + item.reason + '</li>';
			} );
			html += '</ul>';
		}

		html += '<p class="nwcs-hint">Yanlış bir aktarım olduysa sayfadaki “Bu yüklemeyi geri al” düğmesini kullanabilirsiniz.</p>';

		report.innerHTML = html;
		report.hidden = false;
		countLabel.textContent = '';

		nextButton.hidden = false;
		nextButton.disabled = false;
		nextButton.textContent = 'Bitti';
		nextButton.setAttribute( 'data-nwcs-finished', '1' );
	};

	/* --- Olaylar --- */
	document.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( '[data-nwcs-import-open]' ) ) {
			reset();
			modal.showModal();
		}

		if ( event.target.closest( '[data-nwcs-import-close]' ) ) {
			modal.close();
		}
	} );

	fileInput.addEventListener( 'change', function () {
		if ( ! fileInput.files || ! fileInput.files[0] ) {
			return;
		}

		var body = new FormData();
		body.append( 'file', fileInput.files[0] );

		subtitle.textContent = 'Dosya okunuyor…';

		post( 'nwcs_import_upload', body )
			.then( function ( data ) {
				state.token = data.token;
				state.file = data.file;
				state.columns = data.columns;
				state.total = data.total;

				buildMapping( data );
				goTo( 2 );

				if ( data.truncated ) {
					mapWarning.hidden = false;
					mapWarning.textContent = 'Dosya çok uzun olduğu için yalnızca ilk satırlar okundu.';
				}
			} )
			.catch( function ( error ) {
				subtitle.textContent = 'Dosyanızı seçin';
				fileInput.value = '';
				window.alert( error.message );
			} );
	} );

	backButton.addEventListener( 'click', function () {
		if ( 2 === state.step ) {
			reset();
		}
	} );

	nextButton.addEventListener( 'click', function () {
		if ( nextButton.getAttribute( 'data-nwcs-finished' ) ) {
			modal.close();
			window.location.reload();
			return;
		}

		if ( 2 !== state.step || ! validateMapping() ) {
			return;
		}

		state.mapping = readMapping();
		nextButton.hidden = true;
		backButton.hidden = true;
		goTo( 3 );
		progressText.textContent = 'Başlatılıyor…';

		runChunk( 0 ).catch( function ( error ) {
			progressText.textContent = error.message;
			nextButton.hidden = false;
			nextButton.disabled = false;
			nextButton.textContent = 'Kapat';
			nextButton.setAttribute( 'data-nwcs-finished', '1' );
		} );
	} );
}() );
