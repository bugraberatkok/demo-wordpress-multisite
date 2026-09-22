<?php
/**
 * Excel'den toplu urun yukleme sihirbazi.
 *
 * Akis:
 *   1. Dosya Sec   -> .xlsx yuklenir, ilk sayfasi okunur, satirlar gecici bir
 *                     JSON dosyasina yazilir. Havuza henuz hicbir sey yazilmaz.
 *   2. Eslestirme  -> Basliklar sistem alanlariyla tahmini olarak eslestirilir,
 *                     kullanici duzeltir. Ornek deger ilk veri satirindan gelir.
 *   3. Yukleme     -> Satirlar parcalar halinde islenir. Her yukleme bir "parti"
 *                     olarak kaydedilir; parti geri alinabilir.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_IMPORT_OPTION = 'nwcs_import_last';
const NWCS_IMPORT_CHUNK  = 100;

/**
 * Eslestirilebilecek sistem alanlari.
 */
function nwcs_import_targets(): array {
	return array(
		'name'       => array( 'label' => 'Ürün Adı', 'required' => true ),
		'code'       => array( 'label' => 'Ürün Kodu', 'required' => false ),
		'price'      => array( 'label' => 'Fiyat', 'required' => false ),
		'short'      => array( 'label' => 'Kısa Açıklama', 'required' => false ),
		'spec'       => array( 'label' => 'Ölçü / Not', 'required' => false ),
		'categories' => array( 'label' => 'Kategoriler', 'required' => false ),
		'body'       => array( 'label' => 'Detay Metni', 'required' => false ),
	);
}

/**
 * Baslik metnini karsilastirmaya uygun hale getirir (kucuk harf, Turkce
 * karakterler sadelestirilmis, harf/rakam disi atilmis).
 */
function nwcs_import_normalize( string $value ): string {
	$value = mb_strtolower( trim( $value ), 'UTF-8' );

	$map = array( 'ı' => 'i', 'İ' => 'i', 'ş' => 's', 'ğ' => 'g', 'ü' => 'u', 'ö' => 'o', 'ç' => 'c', 'â' => 'a', 'î' => 'i', 'û' => 'u' );
	$value = strtr( $value, $map );

	return preg_replace( '/[^a-z0-9]/', '', $value ) ?? '';
}

/**
 * Basliklari sistem alanlariyla tahmini olarak eslestirir.
 * Sira onemlidir: "aciklama" hem kisa aciklamaya hem detaya benzer, once
 * kisa aciklama denenir.
 *
 * @return array<int, string> sutun sirasi => alan anahtari
 */
function nwcs_import_guess( array $header ): array {
	$synonyms = array(
		'name'       => array( 'ad', 'adi', 'urunadi', 'urun', 'name', 'productname', 'product', 'baslik', 'title' ),
		'code'       => array( 'kod', 'urunkodu', 'stokkodu', 'sku', 'slug', 'barkod', 'code', 'stokkod', 'id' ),
		'price'      => array( 'fiyat', 'price', 'tutar', 'birimfiyat', 'satisfiyati', 'amount' ),
		'short'      => array( 'kisaaciklama', 'ozet', 'short', 'summary', 'aciklama', 'description', 'desc' ),
		'spec'       => array( 'olcu', 'ebat', 'boyut', 'olcunot', 'spec', 'not', 'ozellik', 'size' ),
		'categories' => array( 'kategori', 'kategoriler', 'category', 'categories', 'grup', 'tur' ),
		'body'       => array( 'detay', 'detaymetni', 'uzunaciklama', 'icerik', 'body', 'content', 'detail' ),
	);

	$guess = array();
	$taken = array();

	foreach ( $header as $index => $label ) {
		$needle = nwcs_import_normalize( (string) $label );

		if ( '' === $needle ) {
			continue;
		}

		foreach ( $synonyms as $target => $words ) {
			if ( isset( $taken[ $target ] ) ) {
				continue;
			}

			if ( in_array( $needle, $words, true ) ) {
				$guess[ $index ]  = $target;
				$taken[ $target ] = true;
				break;
			}
		}
	}

	return $guess;
}

/**
 * Gecici dosyalarin tutuldugu klasor. Web'den erisime kapali degildir; bu
 * yuzden icine yalnizca kullanicinin kendi yukledigi veri, kisa sureligine
 * konur ve is bitince silinir.
 */
function nwcs_import_dir(): string {
	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'nwcs-import';

	if ( ! file_exists( $dir ) ) {
		wp_mkdir_p( $dir );
		// Dizin listelemeyi ve dogrudan erisimi engelle.
		file_put_contents( $dir . '/.htaccess', "Deny from all\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		file_put_contents( $dir . '/index.php', "<?php // sessiz\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}

	return $dir;
}

/**
 * Token'a ait gecici veri dosyasinin yolu.
 */
function nwcs_import_data_path( string $token ): string {
	return nwcs_import_dir() . '/' . $token . '.json';
}

/**
 * Bir gun once kalmis gecici dosyalari temizler.
 */
function nwcs_import_sweep(): void {
	$files = glob( nwcs_import_dir() . '/*.json' );

	foreach ( $files ?: array() as $file ) {
		if ( filemtime( $file ) < time() - DAY_IN_SECONDS ) {
			wp_delete_file( $file );
		}
	}
}

/**
 * Ortak yetki ve nonce denetimi.
 */
function nwcs_import_guard(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_send_json_error( array( 'message' => 'Bu işlem için yetkiniz yok.' ), 403 );
	}

	if ( ! check_ajax_referer( 'nwcs_panel', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => 'Oturum doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.' ), 400 );
	}
}

/* ====================================================================== *
 * 1. Adim: dosya yukleme ve onizleme
 * ====================================================================== */

add_action( 'wp_ajax_nwcs_import_upload', 'nwcs_import_ajax_upload' );
function nwcs_import_ajax_upload(): void {
	nwcs_import_guard();
	nwcs_import_sweep();

	if ( empty( $_FILES['file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['file']['tmp_name'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		wp_send_json_error( array( 'message' => 'Dosya alınamadı. Boyut sınırını aşmış olabilir.' ) );
	}

	$name      = sanitize_file_name( (string) ( $_FILES['file']['name'] ?? '' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$extension = strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );

	if ( 'xlsx' !== $extension ) {
		wp_send_json_error( array( 'message' => 'Yalnızca .xlsx dosyası yükleyebilirsiniz. Excel’de “Farklı Kaydet → Excel Çalışma Kitabı (.xlsx)” seçin.' ) );
	}

	$temp = $_FILES['file']['tmp_name']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$read = nwcs_xlsx_read_rows( $temp );

	if ( is_wp_error( $read ) ) {
		wp_send_json_error( array( 'message' => $read->get_error_message() ) );
	}

	$rows = $read['rows'];

	if ( count( $rows ) < 2 ) {
		wp_send_json_error( array( 'message' => 'Dosyada başlık satırından sonra veri bulunamadı.' ) );
	}

	$header = array_map( 'strval', array_shift( $rows ) );
	$sample = $rows[0] ?? array();

	// Yalnizca kucuk harf onaltilik: sanitize_key() bunu degistirmez.
	$token = bin2hex( random_bytes( 10 ) );

	file_put_contents( // phpcs:ignore WordPress.WP.AlternativeFunctions
		nwcs_import_data_path( $token ),
		wp_json_encode( array( 'header' => $header, 'rows' => $rows ) )
	);

	$columns = array();

	foreach ( $header as $index => $label ) {
		$columns[] = array(
			'index'  => $index,
			'label'  => '' !== trim( $label ) ? $label : sprintf( '(%d. sütun)', $index + 1 ),
			'sample' => (string) ( $sample[ $index ] ?? '' ),
		);
	}

	wp_send_json_success(
		array(
			'token'     => $token,
			'file'      => $name,
			'columns'   => $columns,
			'guess'     => nwcs_import_guess( $header ),
			'total'     => count( $rows ),
			'truncated' => $read['truncated'],
			'chunk'     => NWCS_IMPORT_CHUNK,
		)
	);
}

/* ====================================================================== *
 * 3. Adim: parcali yukleme
 * ====================================================================== */

add_action( 'wp_ajax_nwcs_import_run', 'nwcs_import_ajax_run' );
function nwcs_import_ajax_run(): void {
	nwcs_import_guard();

	$token = (string) wp_unslash( $_POST['token'] ?? '' );
	$token = preg_match( '/^[a-f0-9]{20}$/', $token ) ? $token : '';
	$path  = $token ? nwcs_import_data_path( $token ) : '';

	if ( ! $token || ! file_exists( $path ) ) {
		wp_send_json_error( array( 'message' => 'Yükleme oturumu bulunamadı. Dosyayı yeniden seçin.' ) );
	}

	$mapping_raw = wp_unslash( $_POST['mapping'] ?? '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$mapping_in  = json_decode( is_string( $mapping_raw ) ? $mapping_raw : '', true );

	if ( ! is_array( $mapping_in ) ) {
		wp_send_json_error( array( 'message' => 'Sütun eşleştirmesi okunamadı.' ) );
	}

	$targets = nwcs_import_targets();
	$mapping = array();

	foreach ( $mapping_in as $index => $target ) {
		$target = sanitize_key( (string) $target );

		if ( isset( $targets[ $target ] ) ) {
			$mapping[ (int) $index ] = $target;
		}
	}

	if ( ! in_array( 'name', $mapping, true ) ) {
		wp_send_json_error( array( 'message' => 'Ürün Adı sütunu eşleştirilmeden yükleme yapılamaz.' ) );
	}

	$offset = max( 0, (int) ( $_POST['offset'] ?? 0 ) );
	$data   = json_decode( (string) file_get_contents( $path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$rows   = is_array( $data['rows'] ?? null ) ? $data['rows'] : array();
	$total  = count( $rows );

	$batch = get_site_option( NWCS_IMPORT_OPTION . '_draft_' . $token, array(
		'created' => array(),
		'updated' => array(),
		'skipped' => array(),
	) );

	$slice = array_slice( $rows, $offset, NWCS_IMPORT_CHUNK, true );

	switch_to_blog( nwcs_pool_blog_id() );

	foreach ( $slice as $line => $row ) {
		$values = array();

		foreach ( $mapping as $index => $target ) {
			$values[ $target ] = trim( (string) ( $row[ $index ] ?? '' ) );
		}

		$title = $values['name'] ?? '';

		if ( '' === $title ) {
			$batch['skipped'][] = array( 'line' => $line + 2, 'reason' => 'Ürün adı boş' );
			continue;
		}

		$slug = sanitize_title( $values['code'] ?? '' );

		if ( '' === $slug ) {
			$slug = sanitize_title( $title );
		}

		$existing = get_posts(
			array(
				'post_type'      => NWCS_PRODUCT_TYPE,
				'post_status'    => 'any',
				'name'           => $slug,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		$postarr = array(
			'post_type'   => NWCS_PRODUCT_TYPE,
			'post_status' => 'publish',
			'post_title'  => sanitize_text_field( $title ),
			'post_name'   => $slug,
		);

		if ( isset( $values['body'] ) ) {
			$postarr['post_content'] = wp_kses_post( $values['body'] );
		}

		if ( $existing ) {
			$id = (int) $existing[0];

			// Geri alabilmek icin yukleme oncesi hali bir kez saklanir.
			if ( ! isset( $batch['updated'][ $id ] ) ) {
				$batch['updated'][ $id ] = nwcs_import_snapshot( $id );
			}

			$postarr['ID'] = $id;
			wp_update_post( $postarr );
		} else {
			$id = (int) wp_insert_post( $postarr );

			if ( ! $id ) {
				$batch['skipped'][] = array( 'line' => $line + 2, 'reason' => 'Kayıt oluşturulamadı' );
				continue;
			}

			$batch['created'][] = $id;
		}

		if ( isset( $values['short'] ) ) {
			update_post_meta( $id, '_nwcs_short', sanitize_textarea_field( $values['short'] ) );
		}

		if ( isset( $values['price'] ) ) {
			update_post_meta( $id, '_nwcs_price', sanitize_text_field( $values['price'] ) );
		}

		if ( isset( $values['spec'] ) ) {
			update_post_meta( $id, '_nwcs_spec', sanitize_text_field( $values['spec'] ) );
		}

		if ( isset( $values['categories'] ) ) {
			$names = array_values( array_filter( array_map( 'trim', preg_split( '/[|,;]/', $values['categories'] ) ?: array() ) ) );
			wp_set_object_terms( $id, $names, NWCS_PRODUCT_TAX, false );
		}
	}

	restore_current_blog();

	$next = $offset + NWCS_IMPORT_CHUNK;
	$done = $next >= $total;

	if ( $done ) {
		delete_site_option( NWCS_IMPORT_OPTION . '_draft_' . $token );
		wp_delete_file( $path );

		$record = array(
			'time'    => time(),
			'user'    => get_current_user_id(),
			'file'    => sanitize_file_name( (string) ( $_POST['file'] ?? '' ) ),
			'created' => array_values( $batch['created'] ),
			'updated' => $batch['updated'],
			'skipped' => array_slice( $batch['skipped'], 0, 50 ),
			'counts'  => array(
				'created' => count( $batch['created'] ),
				'updated' => count( $batch['updated'] ),
				'skipped' => count( $batch['skipped'] ),
			),
		);

		update_site_option( NWCS_IMPORT_OPTION, $record );
		nwcs_pool_flush_cache();

		wp_send_json_success(
			array(
				'done'    => true,
				'counts'  => $record['counts'],
				'skipped' => $record['skipped'],
			)
		);
	}

	update_site_option( NWCS_IMPORT_OPTION . '_draft_' . $token, $batch );

	wp_send_json_success(
		array(
			'done'      => false,
			'offset'    => $next,
			'total'     => $total,
			'processed' => min( $next, $total ),
		)
	);
}

/**
 * Bir urunun geri alma icin gereken hali.
 */
function nwcs_import_snapshot( int $id ): array {
	$post = get_post( $id );

	if ( ! $post ) {
		return array();
	}

	return array(
		'title'      => $post->post_title,
		'content'    => $post->post_content,
		'name'       => $post->post_name,
		'short'      => (string) get_post_meta( $id, '_nwcs_short', true ),
		'price'      => (string) get_post_meta( $id, '_nwcs_price', true ),
		'spec'       => (string) get_post_meta( $id, '_nwcs_spec', true ),
		'categories' => wp_get_object_terms( $id, NWCS_PRODUCT_TAX, array( 'fields' => 'names' ) ),
	);
}

/* ====================================================================== *
 * Geri alma
 * ====================================================================== */

/**
 * Geri alinabilir son yukleme.
 */
function nwcs_import_last(): ?array {
	$record = get_site_option( NWCS_IMPORT_OPTION, null );

	return is_array( $record ) ? $record : null;
}

add_action( 'wp_ajax_nwcs_import_undo', 'nwcs_import_ajax_undo' );
function nwcs_import_ajax_undo(): void {
	nwcs_import_guard();

	$record = nwcs_import_last();

	if ( ! $record ) {
		wp_send_json_error( array( 'message' => 'Geri alınacak bir yükleme yok.' ) );
	}

	$time    = (int) ( $record['time'] ?? 0 );
	$removed = 0;
	$restored = 0;
	$kept    = array();

	switch_to_blog( nwcs_pool_blog_id() );

	foreach ( $record['created'] ?? array() as $id ) {
		$post = get_post( (int) $id );

		if ( ! $post ) {
			continue;
		}

		// Yuklemeden sonra elle duzenlenmisse dokunma.
		if ( strtotime( $post->post_modified_gmt . ' UTC' ) > $time + 5 ) {
			$kept[] = $post->post_title;
			continue;
		}

		wp_delete_post( (int) $id, true );
		++$removed;
	}

	foreach ( $record['updated'] ?? array() as $id => $snapshot ) {
		$post = get_post( (int) $id );

		if ( ! $post || ! is_array( $snapshot ) || ! $snapshot ) {
			continue;
		}

		if ( strtotime( $post->post_modified_gmt . ' UTC' ) > $time + 5 ) {
			$kept[] = $post->post_title;
			continue;
		}

		wp_update_post(
			array(
				'ID'           => (int) $id,
				'post_title'   => $snapshot['title'] ?? '',
				'post_content' => $snapshot['content'] ?? '',
				'post_name'    => $snapshot['name'] ?? '',
			)
		);

		update_post_meta( (int) $id, '_nwcs_short', $snapshot['short'] ?? '' );
		update_post_meta( (int) $id, '_nwcs_price', $snapshot['price'] ?? '' );
		update_post_meta( (int) $id, '_nwcs_spec', $snapshot['spec'] ?? '' );
		wp_set_object_terms( (int) $id, $snapshot['categories'] ?? array(), NWCS_PRODUCT_TAX, false );

		++$restored;
	}

	restore_current_blog();

	delete_site_option( NWCS_IMPORT_OPTION );
	nwcs_pool_flush_cache();

	wp_send_json_success(
		array(
			'removed'  => $removed,
			'restored' => $restored,
			'kept'     => array_slice( $kept, 0, 20 ),
		)
	);
}

/* ====================================================================== *
 * Arayuz
 * ====================================================================== */

/**
 * Sayfanin ustunde beliren "son yuklemeyi geri al" seridi.
 */
function nwcs_render_import_undo_bar(): void {
	$record = nwcs_import_last();

	if ( ! $record ) {
		return;
	}

	$counts = $record['counts'] ?? array();
	$when   = (int) ( $record['time'] ?? 0 );
	?>
	<div class="nwcs-undo" data-nwcs-undo-bar>
		<div class="nwcs-undo__text">
			<strong>Son Excel yüklemesi</strong>
			<span>
				<?php
				printf(
					'%s · %d yeni, %d güncellenen%s · %s',
					esc_html( $record['file'] ?: 'dosya' ),
					(int) ( $counts['created'] ?? 0 ),
					(int) ( $counts['updated'] ?? 0 ),
					( $counts['skipped'] ?? 0 ) ? esc_html( sprintf( ', %d atlanan', (int) $counts['skipped'] ) ) : '',
					esc_html( $when ? wp_date( 'd.m.Y H:i', $when ) : '' )
				);
				?>
			</span>
		</div>

		<button type="button" class="button" data-nwcs-undo>Bu yüklemeyi geri al</button>
	</div>
	<?php
}

/**
 * Uc adimli yukleme penceresi.
 */
function nwcs_render_import_modal(): void {
	$targets = nwcs_import_targets();
	?>
	<dialog class="nwcs-modal nwcs-wizard" id="nwcs-import-modal" data-nwcs-import
		data-targets="<?php echo esc_attr( wp_json_encode( $targets ) ); ?>">

		<div class="nwcs-modal__head">
			<div>
				<h2 class="nwcs-pool__title">Excel'den Ürün Yükle</h2>
				<p class="nwcs-wizard__sub" data-nwcs-step-sub>Dosyanızı seçin</p>
			</div>
			<button type="button" class="nwcs-modal__close" data-nwcs-import-close aria-label="Kapat">×</button>
		</div>

		<ol class="nwcs-steps" data-nwcs-steps>
			<li class="is-active" data-step="1"><span>1</span> Dosya Seç</li>
			<li data-step="2"><span>2</span> Eşleştirme</li>
			<li data-step="3"><span>3</span> Yükleme</li>
		</ol>

		<div class="nwcs-wizard__body">

			<!-- 1. adim -->
			<section data-nwcs-panel="1">
				<label class="nwcs-drop" for="nwcs-import-file">
					<strong>Excel dosyasını seçin</strong>
					<span>.xlsx · ilk sayfa okunur · en fazla <?php echo esc_html( number_format_i18n( NWCS_XLSX_MAX_ROWS ) ); ?> satır</span>
					<input type="file" id="nwcs-import-file" accept=".xlsx" data-nwcs-import-file />
				</label>

				<p class="nwcs-hint">
					İlk satır başlık satırı olmalıdır. Sütunların hangi alana karşılık geldiğini
					bir sonraki adımda siz seçeceksiniz; şimdilik havuza hiçbir şey yazılmaz.
				</p>
			</section>

			<!-- 2. adim -->
			<section data-nwcs-panel="2" hidden>
				<h3 class="nwcs-wizard__title">Kolon Eşleştirme</h3>

				<table class="nwcs-maptable">
					<thead>
						<tr>
							<th>EXCEL KOLONU</th>
							<th>SİSTEM ALANI</th>
							<th>ÖRNEK DEĞER</th>
						</tr>
					</thead>
					<tbody data-nwcs-map-rows></tbody>
				</table>

				<p class="nwcs-hint" data-nwcs-map-warning hidden></p>
			</section>

			<!-- 3. adim -->
			<section data-nwcs-panel="3" hidden>
				<div class="nwcs-progress">
					<div class="nwcs-progress__bar" data-nwcs-progress-bar></div>
				</div>
				<p class="nwcs-wizard__status" data-nwcs-progress-text>Hazırlanıyor…</p>
				<div class="nwcs-wizard__report" data-nwcs-report hidden></div>
			</section>
		</div>

		<footer class="nwcs-wizard__foot">
			<button type="button" class="button" data-nwcs-back hidden>← Geri</button>
			<span class="nwcs-wizard__count" data-nwcs-count></span>
			<button type="button" class="button button-primary" data-nwcs-next disabled>Devam</button>
		</footer>
	</dialog>
	<?php
}
