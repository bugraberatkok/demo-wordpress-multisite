<?php
/**
 * Urun Havuzu -> urun formu -> "Ürün Tabloları".
 *
 * Temasi tablo gosteren (manifestte 'product_tables') bir sitede yayinlanan
 * urunlerde acilir; bugun yalnizca Kocist. Tablolar hucre hucre girilir
 * (assets/pool-tables.js), form gonderilirken JSON olarak tek alana yazilir
 * ve urunun '_nwcs_tables' kaydina temizlenerek saklanir.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Urunun, tablo gosteren sitelerden hangilerinde yayinda oldugu (site adlari).
 * Yeni urun (kimlik 0) yalnizca "tum urunler" kipindeki sitelerde gorunur.
 *
 * @return string[]
 */
function nwcs_product_table_sites( int $product_id ): array {
	$labels = array();

	foreach ( nwcs_editable_sites() as $blog_id => $site ) {
		if ( ! nwcs_site_supports_product_tables( (int) $blog_id ) ) {
			continue;
		}

		$settings = nwcs_site_product_settings( (int) $blog_id );

		if ( $product_id && ! empty( $settings['overrides'][ $product_id ]['hidden'] ) ) {
			continue;
		}

		if ( 'all' === $settings['mode'] || ( $product_id && in_array( $product_id, $settings['selected'], true ) ) ) {
			$labels[] = $site['label'];
		}
	}

	return $labels;
}

/**
 * Formdaki bolum. Urun tablo gosteren bir sitede degilse ve kayitli tablosu
 * da yoksa hic cizilmez; kayitli tablo varsa (urun siteden kaldirilmis olsa
 * bile) kaybolmasin diye gosterilir.
 */
function nwcs_render_product_tables_field( ?array $product ): void {
	$id     = (int) ( $product['id'] ?? 0 );
	$tables = $product['tables'] ?? array();
	$sites  = nwcs_product_table_sites( $id );

	if ( ! $sites && ! $tables ) {
		return;
	}
	?>
	<div class="nwcs-field nwcs-ptables" data-nwcs-ptables>
		<span class="nwcs-field__label">Ürün tabloları</span>
		<p class="nwcs-hint">
			<?php if ( $sites ) : ?>
				<strong><?php echo esc_html( implode( ', ', $sites ) ); ?></strong> sitesinde, ürün sayfasında detay metninin altında görünür.
			<?php else : ?>
				Bu ürün tablo gösteren bir sitede yayında değil; tablolar saklanır ama şu an görünmez.
			<?php endif; ?>
			Satırı boş kalan tablo sitede gösterilmez.
		</p>

		<input type="hidden" name="tables_present" value="1" />
		<input type="hidden" name="tables_json" value="<?php echo esc_attr( (string) wp_json_encode( $tables ) ); ?>" data-nwcs-ptables-data />

		<?php // Ozet: tablo adlari ve boyutlari; duzenleme genis pencerede. ?>
		<ul class="nwcs-ptables__summary" data-nwcs-ptables-summary></ul>

		<button type="button" class="nwcs-ptables__add" data-nwcs-ptables-open>Tabloları düzenle</button>

		<?php
		// Pencere formun icinde: gizli alan formla birlikte gonderilir.
		$thumb = (string) ( $product['images'][0]['url'] ?? '' );
		?>
		<dialog class="nwcs-ptm" data-nwcs-ptables-modal aria-labelledby="nwcs-ptm-heading">
			<header class="nwcs-ptm__head">
				<?php if ( '' !== $thumb ) : ?>
					<img class="nwcs-ptm__thumb" src="<?php echo esc_url( $thumb ); ?>" alt="" />
				<?php endif; ?>
				<div class="nwcs-ptm__heading">
					<p class="nwcs-ptm__kicker">Ürün tabloları</p>
					<h2 class="nwcs-ptm__product" id="nwcs-ptm-heading"><?php echo esc_html( $product['title'] ?? 'Yeni ürün' ); ?></h2>
				</div>
				<button type="button" class="nwcs-ptm__close" data-nwcs-ptables-close aria-label="Pencereyi kapat">×</button>
			</header>

			<div class="nwcs-ptm__sheet" data-nwcs-ptables-list></div>

			<nav class="nwcs-ptm__tabs" aria-label="Tablolar">
				<div class="nwcs-ptm__tablist" data-nwcs-ptables-tabs></div>
				<button type="button" class="nwcs-ptm__newtab" data-nwcs-ptables-add>+ Yeni tablo</button>
			</nav>

			<footer class="nwcs-ptm__foot">
				<p class="nwcs-ptm__tip">
					Excel'den hücreleri kopyalayıp buradaki bir hücreye yapıştırabilirsiniz. Değişiklikler ürünü
					<strong>Kaydet</strong> ile kaydedince sitede görünür.
				</p>
				<button type="button" class="button button-primary nwcs-ptm__done" data-nwcs-ptables-close>Tamam</button>
			</footer>
		</dialog>
	</div>
	<?php
}

/**
 * Formdan gelen tablolari kaydeder. Havuz baglaminda cagrilir. Bolum formda
 * yoksa (urun tablo gosteren sitede degil) kayda dokunulmaz.
 */
function nwcs_save_product_tables( int $product_id ): void {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- nwcs_handle_pool_save denetledi.
	if ( empty( $_POST['tables_present'] ) ) {
		return;
	}

	$decoded = isset( $_POST['tables_json'] ) ? json_decode( wp_unslash( $_POST['tables_json'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- asagida temizlenir.
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	$tables = nwcs_sanitize_product_tables( is_array( $decoded ) ? $decoded : array() );

	if ( $tables ) {
		// update_post_meta ters egik cizgileri siler; hucrelerdekiler korunsun.
		update_post_meta( $product_id, '_nwcs_tables', wp_slash( $tables ) );
	} else {
		delete_post_meta( $product_id, '_nwcs_tables' );
	}
}
