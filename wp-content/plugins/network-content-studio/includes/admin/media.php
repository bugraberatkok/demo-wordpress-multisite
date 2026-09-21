<?php
/**
 * Ag Yonetimi -> Medya Havuzu.
 *
 * Urun gorsellerinin toplu yuklendigi, alt metinlerinin duzenlendigi ve
 * silindigi sayfa. Dosyalar havuz sitesinin (ag ana sitesi) WordPress medya
 * kitapliginda durur; ayri bir depo kullanilmaz.
 *
 * Bir urune bagli gorsel silinemez: once urunun galerisinden cikarilmalidir.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_MEDIA_SLUG     = 'nwcs-media';
const NWCS_MEDIA_PER_PAGE = 24;

add_action( 'network_admin_menu', 'nwcs_register_media_menu' );
function nwcs_register_media_menu(): void {
	add_menu_page(
		'Medya Havuzu',
		'Medya Havuzu',
		NWCS_CAPABILITY,
		NWCS_MEDIA_SLUG,
		'nwcs_render_media',
		'dashicons-format-gallery',
		5
	);
}

function nwcs_media_url( array $args = array() ): string {
	return add_query_arg(
		array_merge( array( 'page' => NWCS_MEDIA_SLUG ), $args ),
		network_admin_url( 'admin.php' )
	);
}

/**
 * Hangi gorsel hangi urunlerde kullaniliyor.
 *
 * @return array<int, string[]>
 */
function nwcs_media_usage(): array {
	$usage = array();

	foreach ( nwcs_pool_products() as $product ) {
		foreach ( $product['gallery_ids'] as $attachment_id ) {
			$usage[ $attachment_id ][] = $product['title'];
		}
	}

	return $usage;
}

/**
 * Medya kitapligini arama ve sayfalama ile getirir.
 */
function nwcs_media_query( string $search, int $page ): array {
	switch_to_blog( nwcs_pool_blog_id() );

	$query = new WP_Query(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => NWCS_MEDIA_PER_PAGE,
			'paged'          => max( 1, $page ),
			's'              => $search,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$items = array();

	foreach ( $query->posts as $attachment ) {
		$src  = wp_get_attachment_image_src( $attachment->ID, 'medium' );
		$file = get_attached_file( $attachment->ID );

		$items[] = array(
			'id'    => (int) $attachment->ID,
			'title' => $attachment->post_title,
			'alt'   => (string) get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
			'thumb' => $src ? $src[0] : '',
			'name'  => $file ? basename( $file ) : '',
			'size'  => $file && file_exists( $file ) ? size_format( (int) filesize( $file ) ) : '',
		);
	}

	$total = (int) $query->found_posts;
	$pages = (int) $query->max_num_pages;

	restore_current_blog();

	return array(
		'items' => $items,
		'total' => $total,
		'pages' => max( 1, $pages ),
	);
}

/**
 * Sayfa govdesi.
 */
function nwcs_render_media(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu sayfaya erişim yetkiniz yok.' ) );
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- gorunum filtresi.
	$search = isset( $_GET['ara'] ) ? sanitize_text_field( wp_unslash( $_GET['ara'] ) ) : '';
	$page   = isset( $_GET['sayfa'] ) ? max( 1, absint( $_GET['sayfa'] ) ) : 1;
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$result = nwcs_media_query( $search, $page );
	$usage  = nwcs_media_usage();
	?>
	<div class="wrap nwcs-wrap nwcs-wrap--pool">
		<header class="nwcs-bar">
			<div class="nwcs-bar__brand">
				<button type="button" class="nwcs-menutoggle" data-nwcs-menu
					aria-label="Yönetim menüsünü aç/kapat" title="Yönetim menüsünü aç/kapat">☰</button>
				<span class="nwcs-bar__mark" aria-hidden="true"></span>
				<h1>Medya Havuzu</h1>
			</div>
			<div class="nwcs-bar__tools">
				<a class="nwcs-linkout" href="<?php echo esc_url( nwcs_pool_url() ); ?>">Ürün Havuzu ↗</a>
				<a class="nwcs-linkout" href="<?php echo esc_url( nwcs_panel_url( 0 ) ); ?>">İçerik Stüdyosu ↗</a>
			</div>
		</header>

		<p class="nwcs-help">
			<span class="nwcs-help__step"><b>1</b> Görselleri buraya yükleyin (bir seferde birden fazla olabilir)</span>
			<span class="nwcs-help__step"><b>2</b> Alt metni doldurun</span>
			<span class="nwcs-help__step"><b>3</b> Ürün Havuzu'nda ürüne ekleyin</span>
		</p>

		<?php nwcs_render_media_notices(); ?>

		<div class="nwcs-pool">
			<section class="nwcs-pool__list">
				<div class="nwcs-toolbar">
					<form method="get" class="nwcs-toolbar__search">
						<input type="hidden" name="page" value="<?php echo esc_attr( NWCS_MEDIA_SLUG ); ?>" />
						<input class="nwcs-input" type="search" name="ara" value="<?php echo esc_attr( $search ); ?>"
							placeholder="Görsel ara…" />
						<button type="submit" class="button">Ara</button>
						<?php if ( '' !== $search ) : ?>
							<a class="button" href="<?php echo esc_url( nwcs_media_url() ); ?>">Temizle</a>
						<?php endif; ?>
					</form>
					<span class="nwcs-toolbar__count"><?php echo (int) $result['total']; ?> görsel</span>
				</div>

				<?php if ( ! $result['items'] ) : ?>
					<p class="nwcs-empty">Görsel bulunamadı.</p>
				<?php else : ?>
					<div class="nwcs-mediagrid">
						<?php foreach ( $result['items'] as $item ) : ?>
							<?php $used = $usage[ $item['id'] ] ?? array(); ?>
							<figure class="nwcs-mediacard">
								<div class="nwcs-mediacard__thumb">
									<?php if ( $item['thumb'] ) : ?>
										<img src="<?php echo esc_url( $item['thumb'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" />
									<?php endif; ?>
								</div>

								<figcaption>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="nwcs-mediacard__form">
										<input type="hidden" name="action" value="nwcs_media_update" />
										<input type="hidden" name="attachment" value="<?php echo esc_attr( (string) $item['id'] ); ?>" />
										<?php wp_nonce_field( 'nwcs_media_update_' . $item['id'] ); ?>

										<label class="nwcs-sublabel">Başlık</label>
										<input class="nwcs-input" type="text" name="title" value="<?php echo esc_attr( $item['title'] ); ?>" />

										<label class="nwcs-sublabel">Alt metin</label>
										<input class="nwcs-input" type="text" name="alt" value="<?php echo esc_attr( $item['alt'] ); ?>"
											placeholder="Görseli kısaca tarif edin" />

										<p class="nwcs-mediacard__meta">
											<?php echo esc_html( $item['name'] ); ?>
											<?php if ( $item['size'] ) : ?>· <?php echo esc_html( $item['size'] ); ?><?php endif; ?>
										</p>

										<?php if ( $used ) : ?>
											<p class="nwcs-mediacard__used">Kullanımda: <?php echo esc_html( implode( ', ', $used ) ); ?></p>
										<?php endif; ?>

										<div class="nwcs-mediacard__actions">
											<button type="submit" class="button button-small">Kaydet</button>
										</div>
									</form>

									<?php if ( ! $used ) : ?>
										<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
											onsubmit="return confirm('Bu görsel kalıcı olarak silinsin mi?');">
											<input type="hidden" name="action" value="nwcs_media_delete" />
											<input type="hidden" name="attachment" value="<?php echo esc_attr( (string) $item['id'] ); ?>" />
											<?php wp_nonce_field( 'nwcs_media_delete_' . $item['id'] ); ?>
											<button type="submit" class="button button-small nwcs-row__delete">Sil</button>
										</form>
									<?php endif; ?>
								</figcaption>
							</figure>
						<?php endforeach; ?>
					</div>

					<?php
					nwcs_render_pagination(
						$result['pages'],
						$page,
						static fn( int $target ): string => nwcs_media_url( array( 'ara' => $search, 'sayfa' => $target ) )
					);
					?>
				<?php endif; ?>
			</section>

			<section class="nwcs-pool__form">
				<div class="nwcs-pool__card">
					<h2 class="nwcs-pool__title">Görsel yükle</h2>

					<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="nwcs_media_upload" />
						<?php wp_nonce_field( 'nwcs_media_upload' ); ?>

						<div class="nwcs-field">
							<label class="nwcs-field__label" for="nwcs-media-files">Dosyalar</label>
							<input class="nwcs-file" type="file" id="nwcs-media-files" name="files[]" accept="image/*" multiple />
							<p class="nwcs-hint">Birden fazla dosya seçebilirsiniz. Dosyalar ağ ana sitesinin medya kitaplığına yüklenir.</p>
						</div>

						<div class="nwcs-field">
							<label class="nwcs-field__label" for="nwcs-media-alt">Ortak alt metin (isteğe bağlı)</label>
							<input class="nwcs-input" type="text" id="nwcs-media-alt" name="alt" />
							<p class="nwcs-hint">Doldurursanız yüklenen tüm görsellere aynı alt metin yazılır; sonra tek tek düzenleyebilirsiniz.</p>
						</div>

						<div class="nwcs-actions">
							<button type="submit" class="button button-primary">Yükle</button>
						</div>
					</form>
				</div>
			</section>
		</div>
	</div>
	<?php
}

/**
 * Sayfalama baglantilari.
 */
function nwcs_render_pagination( int $pages, int $current, callable $url_for ): void {
	if ( $pages < 2 ) {
		return;
	}

	echo '<nav class="nwcs-pagination" aria-label="Sayfalama">';

	for ( $i = 1; $i <= $pages; $i++ ) {
		printf(
			'<a class="nwcs-pagination__link%1$s" href="%2$s">%3$d</a>',
			$i === $current ? ' is-active' : '',
			esc_url( $url_for( $i ) ),
			$i
		);
	}

	echo '</nav>';
}

function nwcs_render_media_notices(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- yalnizca bildirim.
	$key   = isset( $_GET['nwcs_media'] ) ? sanitize_key( wp_unslash( $_GET['nwcs_media'] ) ) : '';
	$count = isset( $_GET['adet'] ) ? absint( $_GET['adet'] ) : 0;
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$map = array(
		'uploaded' => array( 'success', sprintf( '%d görsel yüklendi.', $count ) ),
		'updated'  => array( 'success', 'Görsel bilgileri kaydedildi.' ),
		'deleted'  => array( 'success', 'Görsel silindi.' ),
		'in_use'   => array( 'error', 'Bu görsel bir üründe kullanılıyor; önce üründen çıkarın.' ),
		'failed'   => array( 'error', 'Yükleme başarısız oldu. Dosya türü veya boyutu uygun olmayabilir.' ),
	);

	if ( isset( $map[ $key ] ) ) {
		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			esc_attr( $map[ $key ][0] ),
			esc_html( $map[ $key ][1] )
		);
	}
}

/* ------------------------------------------------------------------ */
/* Yazma islemleri                                                      */
/* ------------------------------------------------------------------ */

add_action( 'admin_post_nwcs_media_upload', 'nwcs_handle_media_upload' );
function nwcs_handle_media_upload(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	check_admin_referer( 'nwcs_media_upload' );

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$alt   = isset( $_POST['alt'] ) ? sanitize_text_field( wp_unslash( $_POST['alt'] ) ) : '';
	$files = $_FILES['files'] ?? null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- asagida tek tek islenir.
	$count = 0;

	if ( ! is_array( $files ) || ! isset( $files['name'] ) || ! is_array( $files['name'] ) ) {
		nwcs_media_redirect( 'failed', 0 );
	}

	switch_to_blog( nwcs_pool_blog_id() );

	foreach ( array_keys( $files['name'] ) as $index ) {
		if ( UPLOAD_ERR_NO_FILE === (int) $files['error'][ $index ] ) {
			continue;
		}

		// media_handle_upload tek dosya bekler; gecici olarak tek girise indiriyoruz.
		$_FILES['nwcs_single'] = array(
			'name'     => $files['name'][ $index ],
			'type'     => $files['type'][ $index ],
			'tmp_name' => $files['tmp_name'][ $index ],
			'error'    => $files['error'][ $index ],
			'size'     => $files['size'][ $index ],
		);

		$attachment_id = media_handle_upload( 'nwcs_single', 0 );

		if ( is_wp_error( $attachment_id ) ) {
			continue;
		}

		if ( '' !== $alt ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
		}

		++$count;
	}

	unset( $_FILES['nwcs_single'] );

	restore_current_blog();

	nwcs_pool_flush_cache();
	nwcs_media_redirect( $count ? 'uploaded' : 'failed', $count );
}

add_action( 'admin_post_nwcs_media_update', 'nwcs_handle_media_update' );
function nwcs_handle_media_update(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	$attachment_id = isset( $_POST['attachment'] ) ? absint( $_POST['attachment'] ) : 0;
	check_admin_referer( 'nwcs_media_update_' . $attachment_id );

	if ( $attachment_id ) {
		switch_to_blog( nwcs_pool_blog_id() );

		wp_update_post(
			array(
				'ID'         => $attachment_id,
				'post_title' => isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '',
			)
		);

		update_post_meta(
			$attachment_id,
			'_wp_attachment_image_alt',
			isset( $_POST['alt'] ) ? sanitize_text_field( wp_unslash( $_POST['alt'] ) ) : ''
		);

		restore_current_blog();
		nwcs_pool_flush_cache();
	}

	nwcs_media_redirect( 'updated', 0 );
}

add_action( 'admin_post_nwcs_media_delete', 'nwcs_handle_media_delete' );
function nwcs_handle_media_delete(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	$attachment_id = isset( $_POST['attachment'] ) ? absint( $_POST['attachment'] ) : 0;
	check_admin_referer( 'nwcs_media_delete_' . $attachment_id );

	if ( isset( nwcs_media_usage()[ $attachment_id ] ) ) {
		nwcs_media_redirect( 'in_use', 0 );
	}

	if ( $attachment_id ) {
		switch_to_blog( nwcs_pool_blog_id() );
		wp_delete_attachment( $attachment_id, true );
		restore_current_blog();
		nwcs_pool_flush_cache();
	}

	nwcs_media_redirect( 'deleted', 0 );
}

function nwcs_media_redirect( string $key, int $count ): void {
	$args = array( 'nwcs_media' => $key );

	if ( $count ) {
		$args['adet'] = $count;
	}

	wp_safe_redirect( nwcs_media_url( $args ) );
	exit;
}
