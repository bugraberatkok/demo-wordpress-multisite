<?php
/**
 * Urun Havuzu sayfasindaki "Ozellestirmeler" bolumu.
 *
 * Bir urunun havuzdaki hali her sitede aynidir; bir site icin farkli bir ad,
 * aciklama, fiyat veya gorsel istendiginde o degerler ilgili sitenin
 * nwcs_products_overrides kaydina yazilir. Bu bolum o kayitlari sayfadan
 * ayrilmadan duzenlemeye yarar.
 *
 * Gorunurluk burada degistirilmez; onu sitenin kendi urun secimi belirler
 * (Icerik Studyosu -> ilgili sayfa -> urun bolumu).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bir sitedeki tek bir urunun istisna kaydi.
 */
function nwcs_override_get( int $blog_id, int $product_id ): array {
	$settings = nwcs_site_product_settings( $blog_id );

	$row = $settings['overrides'][ $product_id ] ?? array();

	return is_array( $row ) ? $row : array();
}

/**
 * Istisna kaydini yazar; bos kalirsa kaydi tamamen kaldirir.
 */
function nwcs_override_put( int $blog_id, int $product_id, array $values ): void {
	$settings  = nwcs_site_product_settings( $blog_id );
	$overrides = $settings['overrides'];

	$values = array_filter(
		$values,
		static fn( $value ): bool => '' !== $value && null !== $value && false !== $value && 0 !== $value
	);

	if ( $values ) {
		$overrides[ $product_id ] = $values;
	} else {
		unset( $overrides[ $product_id ] );
	}

	update_blog_option( $blog_id, NWCS_OPTION_OVERRIDES, $overrides );
}

/**
 * Bu urunun havuzdaki gorselleri; istisna olarak secilebilecek secenekler.
 */
function nwcs_override_image_choices( array $product ): array {
	$choices = array( 0 => 'Havuzdaki görsel (varsayılan)' );

	switch_to_blog( nwcs_pool_blog_id() );

	foreach ( $product['gallery_ids'] ?? array() as $index => $image_id ) {
		$post = get_post( (int) $image_id );

		$choices[ (int) $image_id ] = $post
			? sprintf( '%d. görsel — %s', $index + 1, $post->post_title )
			: sprintf( '%d. görsel (#%d)', $index + 1, (int) $image_id );
	}

	restore_current_blog();

	return $choices;
}

/* ====================================================================== *
 * Arayuz
 * ====================================================================== */

/**
 * Urunun sitelere gore ozellestirilmis hali; duzenlenebilir.
 */
function nwcs_render_product_customizations( ?array $product, array $media ): void {
	if ( ! $product ) {
		return;
	}

	$id          = (int) $product['id'];
	$images      = nwcs_override_image_choices( $product );
	$unsupported = array();
	$sites       = array();

	foreach ( nwcs_editable_sites() as $blog_id => $site ) {
		$blog_id = (int) $blog_id;

		if ( ! nwcs_site_supports_products( $blog_id ) ) {
			$unsupported[] = $site['label'];
			continue;
		}

		$settings = nwcs_site_product_settings( $blog_id );

		$sites[] = array(
			'blog_id'  => $blog_id,
			'label'    => $site['label'],
			'visible'  => 'all' === $settings['mode'] || in_array( $id, $settings['selected'], true ),
			'override' => nwcs_override_get( $blog_id, $id ),
		);
	}
	?>
	<div class="nwcs-field nwcs-custom" data-nwcs-overrides data-product="<?php echo esc_attr( (string) $id ); ?>">
		<span class="nwcs-field__label">Özelleştirmeler</span>

		<p class="nwcs-hint">
			Ürün her sitede havuzdaki hâliyle görünür. Bir site için farklı bir ad, açıklama,
			fiyat veya görsel istiyorsanız aşağıdan girin; boş bıraktığınız alanlar havuzdaki
			değeri kullanır.
		</p>

		<?php foreach ( $sites as $site ) :
			$override = $site['override'];
			$count    = count( array_filter( $override, static fn( $v ): bool => '' !== $v && 0 !== $v ) );
			?>
			<details class="nwcs-ovr" <?php echo $count ? 'open' : ''; ?> data-blog="<?php echo esc_attr( (string) $site['blog_id'] ); ?>">
				<summary class="nwcs-ovr__head">
					<span class="nwcs-ovr__name"><?php echo esc_html( $site['label'] ); ?></span>

					<?php if ( ! $site['visible'] ) : ?>
						<span class="nwcs-ovr__tag nwcs-ovr__tag--off">Bu sitede seçili değil</span>
					<?php elseif ( $count ) : ?>
						<span class="nwcs-ovr__tag nwcs-ovr__tag--on"><?php echo (int) $count; ?> özelleştirme</span>
					<?php else : ?>
						<span class="nwcs-ovr__tag">Havuzdaki gibi</span>
					<?php endif; ?>
				</summary>

				<div class="nwcs-ovr__body">
					<div class="nwcs-field">
						<label class="nwcs-sublabel">Ürün adı</label>
						<input class="nwcs-input" type="text" data-ovr="title"
							value="<?php echo esc_attr( (string) ( $override['title'] ?? '' ) ); ?>"
							placeholder="<?php echo esc_attr( $product['title'] ); ?>" />
					</div>

					<div class="nwcs-field">
						<label class="nwcs-sublabel">Kart açıklaması</label>
						<textarea class="nwcs-input" rows="2" data-ovr="short"
							placeholder="<?php echo esc_attr( $product['short'] ); ?>"><?php echo esc_textarea( (string) ( $override['short'] ?? '' ) ); ?></textarea>
					</div>

					<div class="nwcs-field">
						<label class="nwcs-check">
							<input type="checkbox" data-ovr="price_override" <?php checked( ! empty( $override['price_override'] ) ); ?> />
							Bu sitede farklı fiyat
						</label>

						<input class="nwcs-input" type="text" data-ovr="price"
							value="<?php echo esc_attr( (string) ( $override['price'] ?? '' ) ); ?>"
							placeholder="<?php echo esc_attr( '' !== trim( $product['price'] ) ? $product['price'] : 'Teklif al' ); ?>"
							<?php disabled( empty( $override['price_override'] ) ); ?> />
						<p class="nwcs-hint">İşaretleyip boş bırakırsanız bu sitede “Teklif al” görünür.</p>
					</div>

					<div class="nwcs-field">
						<label class="nwcs-sublabel">Öne çıkan görsel</label>
						<select class="nwcs-input" data-ovr="image">
							<?php foreach ( $images as $value => $label ) : ?>
								<option value="<?php echo esc_attr( (string) $value ); ?>"
									<?php selected( (int) ( $override['image'] ?? 0 ), (int) $value ); ?>>
									<?php echo esc_html( $label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="nwcs-actions">
						<button type="button" class="button button-primary" data-ovr-save>Kaydet</button>
						<button type="button" class="button" data-ovr-clear <?php disabled( ! $count ); ?>>Özelleştirmeyi kaldır</button>
						<span class="nwcs-ovr__status" data-ovr-status aria-live="polite"></span>
					</div>
				</div>
			</details>
		<?php endforeach; ?>

		<?php if ( $unsupported ) : ?>
			<p class="nwcs-hint">
				Şu siteler havuz ürünlerini göstermiyor, çünkü temalarında ürün bölümü tanımlı değil:
				<strong><?php echo esc_html( implode( ', ', $unsupported ) ); ?></strong>.
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/* ====================================================================== *
 * Kaydetme uclari
 * ====================================================================== */

/**
 * Ortak denetim; gecerli site kimligini dondurur.
 */
function nwcs_override_guard(): array {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_send_json_error( array( 'message' => 'Bu işlem için yetkiniz yok.' ), 403 );
	}

	if ( ! check_ajax_referer( 'nwcs_panel', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => 'Oturum doğrulaması başarısız.' ), 400 );
	}

	$blog_id    = isset( $_POST['blog'] ) ? absint( $_POST['blog'] ) : 0;
	$product_id = isset( $_POST['product'] ) ? absint( $_POST['product'] ) : 0;

	if ( ! $blog_id || ! $product_id || ! array_key_exists( $blog_id, nwcs_editable_sites() ) ) {
		wp_send_json_error( array( 'message' => 'Site ya da ürün bulunamadı.' ) );
	}

	return array( $blog_id, $product_id );
}

add_action( 'wp_ajax_nwcs_override_save', 'nwcs_ajax_override_save' );
function nwcs_ajax_override_save(): void {
	list( $blog_id, $product_id ) = nwcs_override_guard();

	$price_override = ! empty( $_POST['price_override'] );

	$values = array(
		'title'          => sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) ),
		'short'          => sanitize_textarea_field( wp_unslash( $_POST['short'] ?? '' ) ),
		'image'          => absint( $_POST['image'] ?? 0 ),
		'price_override' => $price_override ? 1 : 0,
		// Fiyat yalnizca isaretliyse anlamli; isaretliyken bos birakmak
		// "Teklif al" demektir, bu yuzden bos deger de saklanir.
		'price'          => $price_override ? sanitize_text_field( wp_unslash( $_POST['price'] ?? '' ) ) : '',
	);

	// price_override isaretliyse kayit bos sayilmasin diye ayri tutulur.
	$store = array_filter(
		$values,
		static fn( $value ): bool => '' !== $value && 0 !== $value
	);

	if ( $price_override ) {
		$store['price_override'] = 1;
		$store['price']          = $values['price'];
	}

	nwcs_override_put( $blog_id, $product_id, $store );
	nwcs_pool_flush_cache();

	$count = count( array_filter( $store, static fn( $v ): bool => '' !== $v && 0 !== $v ) );

	wp_send_json_success(
		array(
			'message' => $count ? 'Kaydedildi' : 'Özelleştirme kalmadı',
			'count'   => $count,
		)
	);
}

add_action( 'wp_ajax_nwcs_override_clear', 'nwcs_ajax_override_clear' );
function nwcs_ajax_override_clear(): void {
	list( $blog_id, $product_id ) = nwcs_override_guard();

	nwcs_override_put( $blog_id, $product_id, array() );
	nwcs_pool_flush_cache();

	wp_send_json_success( array( 'message' => 'Kaldırıldı' ) );
}
