<?php
/**
 * Magaza (/magaza/) ve kategori sayfalari (/urun-kategori/<seri>/<kategori>/).
 * Solda kategori agaci, sagda siralama ve urun izgarasi. Arama: ?ara=.
 * Suzgecler duz baglanti/form: JavaScript gerekmez, adresler paylasilabilir.
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- yalnizca gorunum suzgeci.
$slug   = sanitize_title( (string) get_query_var( 'wk_kat' ) );
$search = isset( $_GET['ara'] ) ? sanitize_text_field( wp_unslash( $_GET['ara'] ) ) : '';
$sort   = isset( $_GET['sirala'] ) ? sanitize_key( wp_unslash( $_GET['sirala'] ) ) : '';
// phpcs:enable

$category = '' !== $slug ? wk_category( $slug ) : null; // Bilinmeyen kategori: 404 (inc/catalog.php).

$sort     = isset( wk_sort_options()[ $sort ] ) ? $sort : '';
$products = wk_filter_products( $category ? $category['slug'] : '', $search, $sort );
$base     = $category ? $category['url'] : wk_shop_url();
$title    = $category ? $category['label'] : ( '' !== $search ? 'Arama sonuçları' : 'Mağaza' );
$parent   = $category['parent'] ?? null;
$tree     = wk_category_tree();

get_header();
?>

<section class="wk-pagehead wk-pagehead--compact">
	<div class="wk-wrap">
		<nav class="wk-crumbs wk-crumbs--light" aria-label="Konum">
			<ol>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Anasayfa</a></li>
				<?php if ( $category ) : ?>
					<li><a href="<?php echo esc_url( wk_shop_url() ); ?>">Mağaza</a></li>
					<?php if ( $parent ) : ?>
						<li><a href="<?php echo esc_url( $parent['url'] ); ?>"><?php echo esc_html( $parent['label'] ); ?></a></li>
					<?php endif; ?>
				<?php endif; ?>
				<li aria-current="page"><?php echo esc_html( $category ? $category['label'] : 'Mağaza' ); ?></li>
			</ol>
		</nav>
		<h1 class="wk-hero__title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $category && ! $parent && '' !== ( $category['text'] ?? '' ) ) : ?>
			<p class="wk-hero__lead"><?php echo esc_html( $category['text'] ); ?></p>
		<?php elseif ( ! $category && '' === $search ) : ?>
			<p class="wk-hero__lead" <?php nwcs_edit_attr( 'shop', 'head', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'shop', 'head', 'lead' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>

<div class="wk-wrap wk-shop">
	<aside class="wk-shop__side" aria-label="Kategoriler">
		<details class="wk-filter" data-filter-details open>
			<summary class="wk-filter__summary">Kategoriler</summary>
			<ul class="wk-tree">
				<li>
					<a href="<?php echo esc_url( wk_shop_url() ); ?>" <?php echo ! $category ? 'aria-current="page"' : ''; ?>>Tüm ürünler <span class="wk-num"><?php echo count( wk_products() ); ?></span></a>
				</li>
				<?php foreach ( $tree as $line ) : ?>
					<?php $open = $category && ( $category['slug'] === $line['slug'] || ( $parent && $parent['slug'] === $line['slug'] ) ); ?>
					<li class="wk-tree__line">
						<a href="<?php echo esc_url( $line['url'] ); ?>" <?php echo $category && $category['slug'] === $line['slug'] ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $line['label'] ); ?> <span class="wk-num"><?php echo (int) $line['count']; ?></span></a>
						<ul>
							<?php foreach ( $line['children'] as $child ) : ?>
								<li><a href="<?php echo esc_url( $child['url'] ); ?>" <?php echo $category && $category['slug'] === $child['slug'] ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $child['label'] ); ?> <span class="wk-num"><?php echo (int) $child['count']; ?></span></a></li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php endforeach; ?>
			</ul>
		</details>

		<div class="wk-help">
			<p class="wk-help__title">Aradığınızı bulamadınız mı?</p>
			<p>Ölçüye özel üretim yapıyoruz. Ürün kodunu ya da ihtiyacınızı yazın, fiyatla dönelim.</p>
			<?php if ( wk_whatsapp() ) : ?>
				<a class="wk-btn wk-btn--line" href="<?php echo esc_url( wk_whatsapp() ); ?>" target="_blank" rel="noopener"><?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> WhatsApp’tan sorun</a>
			<?php endif; ?>
		</div>
	</aside>

	<section class="wk-shop__main" aria-labelledby="wk-shop-count">
		<div class="wk-toolbar">
			<p id="wk-shop-count" class="wk-toolbar__count">
				<?php if ( '' !== $search ) : ?>
					“<?php echo esc_html( $search ); ?>” için <strong class="wk-num"><?php echo count( $products ); ?> ürün</strong>
					<a class="wk-toolbar__clear" href="<?php echo esc_url( $base ); ?>">Aramayı temizle</a>
				<?php else : ?>
					<strong class="wk-num"><?php echo count( $products ); ?> ürün</strong>
				<?php endif; ?>
			</p>
			<form class="wk-toolbar__sort" method="get" action="<?php echo esc_url( $base ); ?>">
				<?php if ( '' !== $search ) : ?>
					<input type="hidden" name="ara" value="<?php echo esc_attr( $search ); ?>" />
				<?php endif; ?>
				<label for="wk-sort">Sırala</label>
				<select id="wk-sort" name="sirala" data-autosubmit>
					<?php foreach ( wk_sort_options() as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $sort, $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<noscript><button type="submit" class="wk-btn wk-btn--line">Uygula</button></noscript>
			</form>
		</div>

		<?php if ( $category && ! $parent && $category['children'] ) : ?>
			<nav class="wk-subcats" aria-label="<?php echo esc_attr( $category['label'] ); ?> alt kategorileri">
				<?php foreach ( $category['children'] as $child ) : ?>
					<a class="wk-chip" href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['label'] ); ?> <span class="wk-num"><?php echo (int) $child['count']; ?></span></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( $products ) : ?>
			<div class="wk-grid">
				<?php foreach ( $products as $product ) : ?>
					<?php wk_part( 'product-card', array( 'product' => $product, 'heading' => 'h2' ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="wk-empty">
				<p><strong>Bu aramayla eşleşen ürün yok.</strong> Ürün kodunu (ör. W-KAM-300) ya da ürün türünü (kamelya, çardak, kulübe) yazmayı deneyin.</p>
				<p><a class="wk-btn wk-btn--primary" href="<?php echo esc_url( wk_shop_url() ); ?>">Tüm ürünleri göster</a></p>
			</div>
		<?php endif; ?>
	</section>
</div>

<?php
get_footer();
