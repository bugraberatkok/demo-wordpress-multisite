<?php
/**
 * Ag Yonetimi -> Urun Havuzu.
 *
 * Urunler burada bir kez girilir; siteler bu havuzdan beslenir. Gorseller
 * havuz sitesinin WordPress medya kitapligina yuklenir ve oradan silinir.
 *
 * Tum yazma islemleri admin-post.php uzerinden, nonce + yetki kontrolu ile.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_POOL_SLUG = 'nwcs-pool';

add_action( 'network_admin_menu', 'nwcs_register_pool_menu' );
function nwcs_register_pool_menu(): void {
	add_menu_page(
		'Ürün Havuzu',
		'Ürün Havuzu',
		NWCS_CAPABILITY,
		NWCS_POOL_SLUG,
		'nwcs_render_pool',
		'dashicons-screenoptions',
		4
	);
}

/**
 * Havuz sayfasi adresi.
 */
function nwcs_pool_url( array $args = array() ): string {
	return add_query_arg(
		array_merge( array( 'page' => NWCS_POOL_SLUG ), $args ),
		network_admin_url( 'admin.php' )
	);
}

/**
 * Sayfa govdesi: liste + duzenleme formu + medya kitapligi.
 */
function nwcs_render_pool(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu sayfaya erişim yetkiniz yok.' ) );
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- gorunum secimi.
	$edit_id = isset( $_GET['urun'] ) ? absint( $_GET['urun'] ) : 0;
	$is_new  = isset( $_GET['yeni'] );
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$products = nwcs_pool_products();
	$media    = nwcs_pool_media();
	$editing  = $edit_id && isset( $products[ $edit_id ] ) ? $products[ $edit_id ] : null;

	?>
	<div class="wrap nwcs-wrap nwcs-wrap--pool">
		<header class="nwcs-bar">
			<div class="nwcs-bar__brand">
				<span class="nwcs-bar__mark" aria-hidden="true"></span>
				<h1>Ürün Havuzu</h1>
			</div>
			<div class="nwcs-bar__tools">
				<a class="nwcs-linkout" href="<?php echo esc_url( nwcs_pool_url( array( 'yeni' => 1 ) ) ); ?>">+ Yeni ürün</a>
				<a class="nwcs-linkout" href="<?php echo esc_url( nwcs_panel_url( 0 ) ); ?>">İçerik Stüdyosu ↗</a>
			</div>
		</header>

		<p class="nwcs-help">
			<span class="nwcs-help__step"><b>1</b> Ürünü burada bir kez girin</span>
			<span class="nwcs-help__step"><b>2</b> Görsel WordPress medya kitaplığına yüklenir</span>
			<span class="nwcs-help__step"><b>3</b> Hangi sitede görüneceğini İçerik Stüdyosu'ndan seçin</span>
		</p>

		<?php nwcs_render_pool_notices(); ?>

		<div class="nwcs-pool">
			<section class="nwcs-pool__list">
				<h2 class="nwcs-pool__title">Ürünler <span><?php echo (int) count( $products ); ?></span></h2>

				<?php if ( ! $products ) : ?>
					<p class="nwcs-empty">Havuzda henüz ürün yok. Sağ üstten “+ Yeni ürün” ile başlayın.</p>
				<?php else : ?>
					<table class="nwcs-table">
						<thead>
							<tr>
								<th>Görsel</th>
								<th>Ürün</th>
								<th>Fiyat</th>
								<th>Kategori</th>
								<th>Sitelerde</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $products as $product ) : ?>
								<tr<?php echo $editing && $editing['id'] === $product['id'] ? ' class="is-editing"' : ''; ?>>
									<td class="nwcs-table__thumb">
										<?php if ( $product['image']['url'] ) : ?>
											<img src="<?php echo esc_url( $product['image']['url'] ); ?>" alt="" />
										<?php else : ?>
											<span class="nwcs-image__empty">görsel yok</span>
										<?php endif; ?>
									</td>
									<td>
										<strong><?php echo esc_html( $product['title'] ); ?></strong>
										<?php if ( $product['spec'] ) : ?>
											<span class="nwcs-table__spec"><?php echo esc_html( $product['spec'] ); ?></span>
										<?php endif; ?>
									</td>
									<td>
										<?php if ( '' !== trim( $product['price'] ) ) : ?>
											<?php echo esc_html( $product['price'] ); ?>
										<?php else : ?>
											<em class="nwcs-quote">Teklif al</em>
										<?php endif; ?>
									</td>
									<td><?php echo esc_html( implode( ', ', $product['categories'] ) ); ?></td>
									<td><?php echo esc_html( nwcs_product_usage_label( $product['id'] ) ); ?></td>
									<td>
										<div class="nwcs-table__actions">
											<a class="button button-small" href="<?php echo esc_url( nwcs_pool_url( array( 'urun' => $product['id'] ) ) ); ?>">Düzenle</a>
											<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
												onsubmit="return confirm('Bu ürün havuzdan silinsin mi? Ürünü gösteren sitelerden de kalkar.');">
												<input type="hidden" name="action" value="nwcs_pool_delete" />
												<input type="hidden" name="urun" value="<?php echo esc_attr( (string) $product['id'] ); ?>" />
												<?php wp_nonce_field( 'nwcs_pool_delete_' . $product['id'] ); ?>
												<button type="submit" class="button button-small nwcs-row__delete">Sil</button>
											</form>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<?php nwcs_render_pool_media_library( $media, $products ); ?>
			</section>

			<section class="nwcs-pool__form">
				<?php nwcs_render_pool_form( $editing, $media, $is_new ); ?>
			</section>
		</div>
	</div>
	<?php
}

/**
 * Urunun hangi sitelerde gorundugunu ozetler.
 */
function nwcs_product_usage_label( int $product_id ): string {
	$labels = array();

	foreach ( nwcs_editable_sites() as $blog_id => $site ) {
		$settings = nwcs_site_product_settings( $blog_id );
		$override = $settings['overrides'][ $product_id ] ?? array();

		if ( ! empty( $override['hidden'] ) ) {
			continue;
		}

		$visible = 'all' === $settings['mode'] || in_array( $product_id, $settings['selected'], true );

		if ( $visible ) {
			$labels[] = $site['label'];
		}
	}

	return $labels ? implode( ', ', $labels ) : '—';
}

/**
 * Yeni/duzenleme formu.
 */
function nwcs_render_pool_form( ?array $product, array $media, bool $is_new ): void {
	if ( ! $product && ! $is_new ) {
		echo '<div class="nwcs-pool__card nwcs-pool__card--hint">';
		echo '<h2 class="nwcs-pool__title">Ürün ekle / düzenle</h2>';
		echo '<p class="nwcs-empty">Soldaki listeden bir ürün seçin ya da “+ Yeni ürün” deyin.</p>';
		echo '</div>';

		return;
	}

	$id = $product['id'] ?? 0;
	?>
	<div class="nwcs-pool__card">
		<h2 class="nwcs-pool__title"><?php echo $id ? 'Ürünü düzenle' : 'Yeni ürün'; ?></h2>

		<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="nwcs_pool_save" />
			<input type="hidden" name="urun" value="<?php echo esc_attr( (string) $id ); ?>" />
			<?php wp_nonce_field( 'nwcs_pool_save_' . $id ); ?>

			<div class="nwcs-field">
				<label class="nwcs-field__label" for="nwcs-p-title">Ürün adı</label>
				<input class="nwcs-input" type="text" id="nwcs-p-title" name="title" required
					value="<?php echo esc_attr( $product['title'] ?? '' ); ?>" />
			</div>

			<div class="nwcs-field">
				<label class="nwcs-field__label" for="nwcs-p-short">Kart açıklaması (kısa)</label>
				<textarea class="nwcs-input" id="nwcs-p-short" name="short" rows="3"><?php echo esc_textarea( $product['short'] ?? '' ); ?></textarea>
				<p class="nwcs-hint">Sitelerdeki ürün kartında görünür.</p>
			</div>

			<div class="nwcs-field">
				<label class="nwcs-field__label" for="nwcs-p-price">Fiyat</label>
				<input class="nwcs-input" type="text" id="nwcs-p-price" name="price"
					value="<?php echo esc_attr( $product['price'] ?? '' ); ?>" placeholder="örn. 450 TL" />
				<p class="nwcs-hint"><strong>Boş bırakırsanız</strong> sitede fiyat yerine <em>“Teklif al”</em> görünür.</p>
			</div>

			<div class="nwcs-field">
				<label class="nwcs-field__label" for="nwcs-p-spec">Ölçü / not</label>
				<input class="nwcs-input" type="text" id="nwcs-p-spec" name="spec"
					value="<?php echo esc_attr( $product['spec'] ?? '' ); ?>" placeholder="örn. 80 × 120 cm" />
			</div>

			<div class="nwcs-field">
				<label class="nwcs-field__label" for="nwcs-p-cats">Kategoriler</label>
				<input class="nwcs-input" type="text" id="nwcs-p-cats" name="categories"
					value="<?php echo esc_attr( implode( ', ', $product['categories'] ?? array() ) ); ?>"
					placeholder="virgülle ayırın" />
			</div>

			<div class="nwcs-field">
				<label class="nwcs-field__label">Görsel</label>
				<div class="nwcs-image">
					<div class="nwcs-image__preview">
						<?php if ( ! empty( $product['image']['url'] ) ) : ?>
							<img src="<?php echo esc_url( $product['image']['url'] ); ?>" alt="" />
						<?php else : ?>
							<span class="nwcs-image__empty">Görsel yok</span>
						<?php endif; ?>
					</div>
					<div class="nwcs-image__controls">
						<label class="nwcs-sublabel" for="nwcs-p-image">Medya kitaplığından seç</label>
						<select class="nwcs-input" id="nwcs-p-image" name="image">
							<option value="0">— Görsel yok —</option>
							<?php foreach ( $media as $item ) : ?>
								<option value="<?php echo esc_attr( (string) $item['id'] ); ?>" <?php selected( $item['id'], $product['image_id'] ?? 0 ); ?>>
									<?php echo esc_html( $item['title'] ); ?>
								</option>
							<?php endforeach; ?>
						</select>

						<label class="nwcs-sublabel" for="nwcs-p-upload">Yeni görsel yükle</label>
						<input class="nwcs-file" type="file" id="nwcs-p-upload" name="image_upload" accept="image/*" />

						<label class="nwcs-sublabel" for="nwcs-p-alt">Alt metin</label>
						<input class="nwcs-input" type="text" id="nwcs-p-alt" name="image_alt"
							value="<?php echo esc_attr( $product['image']['alt'] ?? '' ); ?>" />
					</div>
				</div>
			</div>

			<div class="nwcs-field">
				<label class="nwcs-field__label" for="nwcs-p-body">Detay sayfası metni</label>
				<textarea class="nwcs-input" id="nwcs-p-body" name="body" rows="5"><?php echo esc_textarea( $product['body'] ?? '' ); ?></textarea>
				<p class="nwcs-hint">
					Doldurulursa ürünün kendi sayfası oluşur:
					<code>/urun/<?php echo esc_html( $product['slug'] ?? 'urun-adi' ); ?>/</code>
				</p>
			</div>

			<div class="nwcs-actions">
				<button type="submit" class="button button-primary">Kaydet</button>
				<a class="button" href="<?php echo esc_url( nwcs_pool_url() ); ?>">Vazgeç</a>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Havuzun medya kitapligi: yukle / sil.
 */
function nwcs_render_pool_media_library( array $media, array $products ): void {
	$used = array();

	foreach ( $products as $product ) {
		if ( $product['image_id'] ) {
			$used[ $product['image_id'] ] = $product['title'];
		}
	}
	?>
	<h2 class="nwcs-pool__title nwcs-pool__title--spaced">
		Medya kitaplığı <span><?php echo (int) count( $media ); ?></span>
	</h2>
	<p class="nwcs-hint">
		Görseller ağ ana sitesinin WordPress medya kitaplığında durur. Bir ürüne bağlı görsel silinemez;
		önce ürünün görselini değiştirin.
	</p>

	<div class="nwcs-media">
		<?php foreach ( $media as $item ) : ?>
			<figure class="nwcs-media__item">
				<?php if ( $item['thumb'] ) : ?>
					<img src="<?php echo esc_url( $item['thumb'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" />
				<?php endif; ?>
				<figcaption><?php echo esc_html( $item['title'] ); ?></figcaption>

				<?php if ( isset( $used[ $item['id'] ] ) ) : ?>
					<span class="nwcs-media__used"><?php echo esc_html( $used[ $item['id'] ] ); ?></span>
				<?php else : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
						onsubmit="return confirm('Bu görsel medya kitaplığından kalıcı olarak silinsin mi?');">
						<input type="hidden" name="action" value="nwcs_pool_media_delete" />
						<input type="hidden" name="attachment" value="<?php echo esc_attr( (string) $item['id'] ); ?>" />
						<?php wp_nonce_field( 'nwcs_pool_media_delete_' . $item['id'] ); ?>
						<button type="submit" class="button button-small nwcs-row__delete">Sil</button>
					</form>
				<?php endif; ?>
			</figure>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Havuz bildirimleri.
 */
function nwcs_render_pool_notices(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- yalnizca bildirim.
	$map = array(
		'saved'        => array( 'success', 'Ürün kaydedildi.' ),
		'deleted'      => array( 'success', 'Ürün havuzdan silindi.' ),
		'media_gone'   => array( 'success', 'Görsel medya kitaplığından silindi.' ),
		'media_in_use' => array( 'error', 'Bu görsel bir ürüne bağlı; önce ürünün görselini değiştirin.' ),
		'upload'       => array( 'error', 'Görsel yüklenemedi.' ),
		'title'        => array( 'error', 'Ürün adı boş olamaz.' ),
	);

	$key = isset( $_GET['nwcs_pool'] ) ? sanitize_key( wp_unslash( $_GET['nwcs_pool'] ) ) : '';

	if ( isset( $map[ $key ] ) ) {
		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			esc_attr( $map[ $key ][0] ),
			esc_html( $map[ $key ][1] )
		);
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
}

/* ------------------------------------------------------------------ */
/* Yazma islemleri                                                      */
/* ------------------------------------------------------------------ */

add_action( 'admin_post_nwcs_pool_save', 'nwcs_handle_pool_save' );
function nwcs_handle_pool_save(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	$id = isset( $_POST['urun'] ) ? absint( $_POST['urun'] ) : 0;
	check_admin_referer( 'nwcs_pool_save_' . $id );

	$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';

	if ( '' === $title ) {
		nwcs_pool_redirect( 'title', $id );
	}

	switch_to_blog( nwcs_pool_blog_id() );

	$postarr = array(
		'post_type'    => NWCS_PRODUCT_TYPE,
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_content' => isset( $_POST['body'] ) ? wp_kses_post( wp_unslash( $_POST['body'] ) ) : '',
	);

	if ( $id ) {
		$postarr['ID'] = $id;
		wp_update_post( $postarr );
	} else {
		$id = (int) wp_insert_post( $postarr );
	}

	if ( ! $id ) {
		restore_current_blog();
		nwcs_pool_redirect( 'title', 0 );
	}

	update_post_meta( $id, '_nwcs_short', isset( $_POST['short'] ) ? sanitize_textarea_field( wp_unslash( $_POST['short'] ) ) : '' );
	update_post_meta( $id, '_nwcs_price', isset( $_POST['price'] ) ? sanitize_text_field( wp_unslash( $_POST['price'] ) ) : '' );
	update_post_meta( $id, '_nwcs_spec', isset( $_POST['spec'] ) ? sanitize_text_field( wp_unslash( $_POST['spec'] ) ) : '' );

	$categories = isset( $_POST['categories'] ) ? sanitize_text_field( wp_unslash( $_POST['categories'] ) ) : '';
	$terms      = array_values( array_filter( array_map( 'trim', explode( ',', $categories ) ) ) );
	wp_set_object_terms( $id, $terms, NWCS_PRODUCT_TAX, false );

	// Gorsel: yeni dosya varsa yuklenir, yoksa secilen kullanilir.
	$attachment_id = isset( $_POST['image'] ) ? absint( $_POST['image'] ) : 0;

	if ( isset( $_FILES['image_upload'] ) && UPLOAD_ERR_NO_FILE !== (int) ( $_FILES['image_upload']['error'] ?? UPLOAD_ERR_NO_FILE ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$uploaded = media_handle_upload( 'image_upload', 0 );

		if ( is_wp_error( $uploaded ) ) {
			restore_current_blog();
			nwcs_pool_redirect( 'upload', $id );
		}

		$attachment_id = (int) $uploaded;
	}

	if ( $attachment_id ) {
		set_post_thumbnail( $id, $attachment_id );

		if ( isset( $_POST['image_alt'] ) ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( wp_unslash( $_POST['image_alt'] ) ) );
		}
	} else {
		delete_post_thumbnail( $id );
	}

	restore_current_blog();

	nwcs_pool_redirect( 'saved', $id );
}

add_action( 'admin_post_nwcs_pool_delete', 'nwcs_handle_pool_delete' );
function nwcs_handle_pool_delete(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	$id = isset( $_POST['urun'] ) ? absint( $_POST['urun'] ) : 0;
	check_admin_referer( 'nwcs_pool_delete_' . $id );

	if ( $id ) {
		switch_to_blog( nwcs_pool_blog_id() );
		wp_delete_post( $id, true );
		restore_current_blog();

		// Sitelerdeki secim ve istisnalardan da temizle.
		foreach ( nwcs_editable_sites() as $blog_id => $site ) {
			switch_to_blog( $blog_id );

			$settings = nwcs_site_product_settings();
			$settings['selected'] = array_values( array_diff( $settings['selected'], array( $id ) ) );
			unset( $settings['overrides'][ $id ] );

			update_option( NWCS_OPTION_SELECTED, $settings['selected'] );
			update_option( NWCS_OPTION_OVERRIDES, $settings['overrides'] );

			restore_current_blog();
		}
	}

	nwcs_pool_redirect( 'deleted', 0 );
}

add_action( 'admin_post_nwcs_pool_media_delete', 'nwcs_handle_pool_media_delete' );
function nwcs_handle_pool_media_delete(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	$attachment_id = isset( $_POST['attachment'] ) ? absint( $_POST['attachment'] ) : 0;
	check_admin_referer( 'nwcs_pool_media_delete_' . $attachment_id );

	// Bir urune bagliysa silinmez.
	foreach ( nwcs_pool_products() as $product ) {
		if ( $product['image_id'] === $attachment_id ) {
			nwcs_pool_redirect( 'media_in_use', 0 );
		}
	}

	if ( $attachment_id ) {
		switch_to_blog( nwcs_pool_blog_id() );
		wp_delete_attachment( $attachment_id, true );
		restore_current_blog();
	}

	nwcs_pool_redirect( 'media_gone', 0 );
}

/**
 * Havuz sayfasina bildirimli donus.
 */
function nwcs_pool_redirect( string $key, int $product_id ): void {
	$args = array( 'nwcs_pool' => $key );

	if ( $product_id ) {
		$args['urun'] = $product_id;
	}

	wp_safe_redirect( nwcs_pool_url( $args ) );
	exit;
}
