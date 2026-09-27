<?php
/**
 * Ag Yonetimi -> Urun Havuzu.
 *
 * Urunler burada bir kez girilir; siteler bu havuzdan beslenir. Gorseller
 * havuz sitesinin WordPress medya kitapligindadir (bkz. Medya Havuzu sayfasi).
 *
 * Tum yazma islemleri admin-post.php uzerinden, nonce + yetki kontrolu ile.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_POOL_SLUG     = 'nwcs-pool';
const NWCS_POOL_PER_PAGE = 20;

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
 * Sayfa govdesi: arac cubugu + liste + urun formu + kategori yonetimi.
 */
function nwcs_render_pool(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu sayfaya erişim yetkiniz yok.' ) );
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- gorunum secimi.
	$edit_id  = isset( $_GET['urun'] ) ? absint( $_GET['urun'] ) : 0;
	$is_new   = isset( $_GET['yeni'] );
	$search   = isset( $_GET['ara'] ) ? nwcs_clean_text( wp_unslash( $_GET['ara'] ) ) : '';
	$category = isset( $_GET['kategori'] ) ? sanitize_title( wp_unslash( $_GET['kategori'] ) ) : '';
	$page     = isset( $_GET['sayfa'] ) ? max( 1, absint( $_GET['sayfa'] ) ) : 1;
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$all        = nwcs_pool_products();
	$categories = nwcs_pool_categories();
	$result     = nwcs_pool_query(
		array(
			'search'   => $search,
			'category' => $category,
			'page'     => $page,
			'per_page' => NWCS_POOL_PER_PAGE,
		)
	);

	$editing = $edit_id && isset( $all[ $edit_id ] ) ? $all[ $edit_id ] : null;

	// Kaydedilemeyen form: girilenler geri gelir.
	if ( isset( $_GET['taslak'] ) && ( $editing || $is_new ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$editing = nwcs_pool_take_draft( $editing ) ?? $editing;
	}
	$filters = array( 'ara' => $search, 'kategori' => $category );
	?>
	<div class="wrap nwcs-wrap nwcs-wrap--pool">
		<header class="nwcs-bar">
			<div class="nwcs-bar__brand">
				<button type="button" class="nwcs-bar__mark" data-nwcs-menu
					aria-label="Yönetim menüsünü aç/kapat" title="Yönetim menüsünü aç/kapat"></button>
				<h1>Ürün Havuzu</h1>
			</div>
			<div class="nwcs-bar__tools">
				<a class="nwcs-linkout" href="<?php echo esc_url( nwcs_pool_url( array( 'yeni' => 1 ) ) ); ?>">+ Yeni ürün</a>
				<button type="button" class="nwcs-linkout nwcs-linkout--accent" data-nwcs-import-open>Excel'den ürün yükle</button>
				<a class="nwcs-linkout" href="<?php echo esc_url( nwcs_media_url() ); ?>">Medya Havuzu ↗</a>
				<a class="nwcs-linkout" href="<?php echo esc_url( nwcs_panel_url( 0 ) ); ?>">İçerik Stüdyosu ↗</a>
			</div>
		</header>

		<p class="nwcs-help">
			<span class="nwcs-help__step"><b>1</b> Ürünü burada bir kez girin</span>
			<span class="nwcs-help__step"><b>2</b> Görseller WordPress medya kitaplığına yüklenir</span>
			<span class="nwcs-help__step"><b>3</b> Hangi sitede görüneceğini İçerik Stüdyosu'ndan seçin</span>
		</p>

		<?php nwcs_render_import_undo_bar(); ?>

		<?php nwcs_render_pool_notices(); ?>

		<div class="nwcs-pool">
			<section class="nwcs-pool__list">
				<?php nwcs_render_pool_toolbar( $search, $category, $categories, (int) $result['total'] ); ?>

				<?php if ( ! $result['items'] ) : ?>
					<p class="nwcs-empty">
						<?php echo $all ? 'Bu filtreye uyan ürün yok.' : 'Havuzda henüz ürün yok. Sağ üstten “+ Yeni ürün” ile başlayın.'; ?>
					</p>
				<?php else : ?>
					<?php nwcs_render_pool_table( $result['items'], $editing, $categories, $filters ); ?>

					<?php
					nwcs_render_pagination(
						(int) $result['pages'],
						(int) $result['page'],
						static fn( int $target ): string => nwcs_pool_url( array_merge( $filters, array( 'sayfa' => $target ) ) )
					);
					?>
				<?php endif; ?>
			</section>

			<section class="nwcs-pool__form">
				<?php nwcs_render_pool_form( $editing, $categories, $is_new ); ?>
				<?php nwcs_render_category_manager( $categories ); ?>
			</section>
		</div>

		<?php nwcs_render_import_modal(); ?>
	</div>
	<?php
}

/**
 * Arama, kategori filtresi ve sonuc sayisi.
 */
function nwcs_render_pool_toolbar( string $search, string $category, array $categories, int $total ): void {
	?>
	<div class="nwcs-toolbar">
		<form method="get" class="nwcs-toolbar__search">
			<input type="hidden" name="page" value="<?php echo esc_attr( NWCS_POOL_SLUG ); ?>" />

			<input class="nwcs-input" type="search" name="ara" value="<?php echo esc_attr( $search ); ?>"
				placeholder="Ürün adı veya kodu ara…" />

			<select class="nwcs-input" name="kategori">
				<option value="">Tüm kategoriler</option>
				<?php foreach ( $categories as $slug => $term ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $slug, $category ); ?>>
						<?php echo esc_html( $term['name'] ); ?> (<?php echo (int) $term['count']; ?>)
					</option>
				<?php endforeach; ?>
			</select>

			<button type="submit" class="button">Filtrele</button>

			<?php if ( '' !== $search || '' !== $category ) : ?>
				<a class="button" href="<?php echo esc_url( nwcs_pool_url() ); ?>">Temizle</a>
			<?php endif; ?>
		</form>

		<span class="nwcs-toolbar__count"><?php echo (int) $total; ?> ürün</span>
	</div>
	<?php
}

/**
 * Urun tablosu + toplu islemler.
 */
function nwcs_render_pool_table( array $items, ?array $editing, array $categories, array $filters ): void {
	$sites = nwcs_editable_sites();
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="nwcs-bulkform">
		<input type="hidden" name="action" value="nwcs_pool_bulk" />
		<?php wp_nonce_field( 'nwcs_pool_bulk' ); ?>
		<?php foreach ( $filters as $key => $value ) : ?>
			<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		<?php endforeach; ?>

		<div class="nwcs-bulkbar">
			<label class="nwcs-bulkbar__all">
				<input type="checkbox" data-nwcs-check-all /> Tümünü seç
			</label>

			<select class="nwcs-input" name="bulk_action">
				<option value="">Toplu işlem…</option>
				<?php foreach ( $sites as $blog_id => $site ) : ?>
					<option value="show:<?php echo esc_attr( (string) $blog_id ); ?>">
						<?php echo esc_html( $site['label'] ); ?> sitesinde göster
					</option>
					<option value="hide:<?php echo esc_attr( (string) $blog_id ); ?>">
						<?php echo esc_html( $site['label'] ); ?> sitesinde gizle
					</option>
				<?php endforeach; ?>
				<?php foreach ( $categories as $slug => $term ) : ?>
					<option value="cat:<?php echo esc_attr( $slug ); ?>">
						“<?php echo esc_html( $term['name'] ); ?>” kategorisine ekle
					</option>
				<?php endforeach; ?>
			</select>

			<button type="submit" class="button">Uygula</button>
		</div>

		<table class="nwcs-table">
			<thead>
				<tr>
					<th></th>
					<th>Görsel</th>
					<th>Ürün</th>
					<th>Fiyat</th>
					<th>Kategori</th>
					<th>Sitelerde</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $items as $product ) : ?>
					<tr<?php echo $editing && $editing['id'] === $product['id'] ? ' class="is-editing"' : ''; ?>>
						<td class="nwcs-table__check">
							<input type="checkbox" name="urunler[]" value="<?php echo esc_attr( (string) $product['id'] ); ?>" data-nwcs-check />
						</td>
						<td class="nwcs-table__thumb">
							<?php if ( $product['image']['url'] ) : ?>
								<img src="<?php echo esc_url( $product['image']['url'] ); ?>" alt="" />
							<?php else : ?>
								<span class="nwcs-image__empty">yok</span>
							<?php endif; ?>
							<?php if ( count( $product['images'] ) > 1 ) : ?>
								<span class="nwcs-table__count">+<?php echo (int) ( count( $product['images'] ) - 1 ); ?></span>
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
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</form>
	<?php
}

/**
 * Urunun hangi sitelerde gorundugunu ozetler.
 */
function nwcs_product_usage_label( int $product_id ): string {
	$labels = array();

	foreach ( nwcs_editable_sites() as $blog_id => $site ) {
		// Temasinda urun alani olmayan site urun gosteremez; sayilmaz.
		if ( ! nwcs_site_supports_products( (int) $blog_id ) ) {
			continue;
		}

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
function nwcs_render_pool_form( ?array $product, array $categories, bool $is_new ): void {
	if ( ! $product && ! $is_new ) {
		echo '<div class="nwcs-pool__card nwcs-pool__card--hint">';
		echo '<h2 class="nwcs-pool__title">Ürün ekle / düzenle</h2>';
		echo '<p class="nwcs-empty">Soldaki listeden bir ürün seçin ya da “+ Yeni ürün” deyin.</p>';
		echo '</div>';

		return;
	}

	$id       = $product['id'] ?? 0;
	$selected = $product['categories'] ?? array();
	$media    = nwcs_pool_media();
	?>
	<div class="nwcs-pool__card">
		<h2 class="nwcs-pool__title"><?php echo $id ? 'Ürünü düzenle' : 'Yeni ürün'; ?></h2>

		<?php if ( isset( $product['taken_by'] ) ) : ?>
			<div class="notice notice-warning inline">
				<p>
					<strong>Kaydedilmedi.</strong> Yazdıklarınız aşağıda duruyor; düzeltip yeniden kaydedin.
					<?php if ( '' !== $product['taken_by'] ) : ?>
						Bu kod şu üründe kullanılıyor: <strong><?php echo esc_html( $product['taken_by'] ); ?></strong>.
					<?php endif; ?>
					<?php if ( ! empty( $product['had_upload'] ) ) : ?>
						Seçtiğiniz yeni görsel dosyalarını yeniden seçmeniz gerekiyor.
					<?php endif; ?>
				</p>
			</div>
		<?php endif; ?>

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
				<label class="nwcs-field__label" for="nwcs-p-code">Ürün kodu</label>
				<input class="nwcs-input" type="text" id="nwcs-p-code" name="code"
					value="<?php echo esc_attr( $product['code'] ?? '' ); ?>" placeholder="örn. PAL-120" />
				<p class="nwcs-hint">
					Her ürünün kendine ait kimliği. Boş bırakırsanız otomatik atanır.
					Excel'den yükleme bu kodla eşleştirme yapar.
				</p>
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

			<?php nwcs_render_product_customizations( $product, $media ); ?>

			<div class="nwcs-field">
				<span class="nwcs-field__label">Kategoriler</span>

				<?php if ( ! $categories ) : ?>
					<p class="nwcs-hint">Henüz kategori yok. Aşağıdaki “Kategoriler” kutusundan ekleyebilirsiniz.</p>
				<?php else : ?>
					<div class="nwcs-tags">
						<?php foreach ( $categories as $slug => $term ) : ?>
							<label class="nwcs-tag<?php echo isset( $selected[ $slug ] ) ? ' is-active' : ''; ?>">
								<input type="checkbox" name="categories[]" value="<?php echo esc_attr( $slug ); ?>"
									<?php checked( isset( $selected[ $slug ] ) ); ?> data-nwcs-tag />
								<?php echo esc_html( $term['name'] ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<label class="nwcs-sublabel" for="nwcs-p-newcat">Yeni kategori ekle</label>
				<input class="nwcs-input" type="text" id="nwcs-p-newcat" name="new_category"
					value="<?php echo esc_attr( $product['new_category'] ?? '' ); ?>"
					placeholder="Yazıp kaydedin; bu ürüne de eklenir" />
			</div>

			<div class="nwcs-field">
				<span class="nwcs-field__label">Görseller</span>
				<p class="nwcs-hint">
					İlk görsel kartta kullanılır. Sıra değiştirmek için <strong>↑ ↓</strong> düğmelerini kullanın.
					Yeni dosyalar <a href="<?php echo esc_url( nwcs_media_url() ); ?>">Medya Havuzu</a>'na eklenir.
				</p>

				<div class="nwcs-gallery" data-nwcs-gallery>
					<?php foreach ( $product['images'] ?? array() as $image ) : ?>
						<div class="nwcs-gallery__item" data-nwcs-gallery-item>
							<input type="hidden" name="gallery[]" value="<?php echo esc_attr( (string) $image['id'] ); ?>" />
							<?php if ( $image['url'] ) : ?>
								<img src="<?php echo esc_url( $image['url'] ); ?>" alt="" />
							<?php endif; ?>
							<div class="nwcs-gallery__tools">
								<button type="button" class="nwcs-move" data-nwcs-gallery-move="up" aria-label="Öne al">↑</button>
								<button type="button" class="nwcs-move" data-nwcs-gallery-move="down" aria-label="Geri al">↓</button>
								<button type="button" class="nwcs-move nwcs-row__delete" data-nwcs-gallery-remove aria-label="Çıkar">×</button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<label class="nwcs-sublabel" for="nwcs-p-addimage">Kitaplıktan ekle</label>
				<select class="nwcs-input" id="nwcs-p-addimage" data-nwcs-gallery-add>
					<option value="">— Görsel seçin —</option>
					<?php foreach ( $media as $item ) : ?>
						<option value="<?php echo esc_attr( (string) $item['id'] ); ?>"
							data-thumb="<?php echo esc_url( $item['thumb'] ); ?>">
							<?php echo esc_html( $item['title'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>

				<label class="nwcs-sublabel" for="nwcs-p-upload">Yeni görsel yükle</label>
				<input class="nwcs-file" type="file" id="nwcs-p-upload" name="gallery_upload[]" accept="image/*" multiple />
			</div>

			<div class="nwcs-field">
				<label class="nwcs-field__label" for="nwcs-p-body">Detay sayfası metni</label>
				<textarea class="nwcs-input" id="nwcs-p-body" name="body" rows="5"><?php echo esc_textarea( $product['body'] ?? '' ); ?></textarea>
				<p class="nwcs-hint">
					Doldurulursa ürünün kendi sayfası oluşur:
					<code>/urun/<?php echo esc_html( $product['slug'] ?? 'urun-adi' ); ?>/</code>
				</p>
			</div>

			<?php nwcs_render_product_tables_field( $product ); ?>

			<div class="nwcs-actions">
				<button type="submit" class="button button-primary">Kaydet</button>
				<a class="button" href="<?php echo esc_url( nwcs_pool_url() ); ?>">Vazgeç</a>

				<?php if ( $id ) : ?>
					<span class="nwcs-actions__spacer"></span>
				<?php endif; ?>
			</div>
		</form>

		<?php if ( $id ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="nwcs-danger"
				onsubmit="return confirm('Bu ürün havuzdan silinsin mi? Ürünü gösteren sitelerden de kalkar.');">
				<input type="hidden" name="action" value="nwcs_pool_delete" />
				<input type="hidden" name="urun" value="<?php echo esc_attr( (string) $id ); ?>" />
				<?php wp_nonce_field( 'nwcs_pool_delete_' . $id ); ?>
				<button type="submit" class="button button-small nwcs-row__delete">Ürünü sil</button>
			</form>
		<?php endif; ?>
	</div>
	<?php
}


/**
 * Kategori yonetimi kutusu.
 */
function nwcs_render_category_manager( array $categories ): void {
	?>
	<div class="nwcs-pool__card">
		<h2 class="nwcs-pool__title">Kategoriler <span><?php echo (int) count( $categories ); ?></span></h2>

		<?php if ( $categories ) : ?>
			<ul class="nwcs-catlist">
				<?php foreach ( $categories as $slug => $term ) : ?>
					<li>
						<span><?php echo esc_html( $term['name'] ); ?> <small><?php echo (int) $term['count']; ?> ürün</small></span>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
							onsubmit="return confirm('Kategori silinsin mi? Ürünler silinmez, yalnızca bu etiket kalkar.');">
							<input type="hidden" name="action" value="nwcs_pool_category_delete" />
							<input type="hidden" name="kategori" value="<?php echo esc_attr( (string) $term['id'] ); ?>" />
							<?php wp_nonce_field( 'nwcs_pool_category_delete_' . $term['id'] ); ?>
							<button type="submit" class="button button-small nwcs-row__delete">Sil</button>
						</form>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="nwcs-empty">Henüz kategori yok.</p>
		<?php endif; ?>

		<?php
		$orphans = array_filter( $categories, static fn( array $term ): bool => 0 === (int) $term['count'] );
		?>

		<?php if ( count( $orphans ) > 1 ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
				onsubmit="return confirm('Ürünü olmayan <?php echo (int) count( $orphans ); ?> kategori silinecek. Devam edilsin mi?');">
				<input type="hidden" name="action" value="nwcs_pool_category_prune" />
				<?php wp_nonce_field( 'nwcs_pool_category_prune' ); ?>

				<div class="nwcs-actions">
					<button type="submit" class="button">
						Ürünü olmayan <?php echo (int) count( $orphans ); ?> kategoriyi sil
					</button>
				</div>
			</form>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="nwcs_pool_category_add" />
			<?php wp_nonce_field( 'nwcs_pool_category_add' ); ?>

			<div class="nwcs-field">
				<label class="nwcs-sublabel" for="nwcs-newcat">Yeni kategori</label>
				<input class="nwcs-input" type="text" id="nwcs-newcat" name="isim" placeholder="örn. Ahşap Ambalaj" required />
			</div>

			<div class="nwcs-actions">
				<button type="submit" class="button">Kategori ekle</button>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Havuz bildirimleri.
 */
function nwcs_render_pool_notices(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- yalnizca bildirim.
	$key   = isset( $_GET['nwcs_pool'] ) ? sanitize_key( wp_unslash( $_GET['nwcs_pool'] ) ) : '';
	$count = isset( $_GET['adet'] ) ? absint( $_GET['adet'] ) : 0;
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$map = array(
		'saved'        => array( 'success', 'Ürün kaydedildi.' ),
		'deleted'      => array( 'success', 'Ürün havuzdan silindi.' ),
		'cat_added'    => array( 'success', 'Kategori eklendi.' ),
		'cat_deleted'  => array( 'success', 'Kategori silindi.' ),
		'cats_pruned'  => array( 'success', sprintf( 'Ürünü olmayan %d kategori silindi.', $count ) ),
		'bulk'         => array( 'success', sprintf( '%d ürün güncellendi.', $count ) ),
		'bulk_empty'   => array( 'error', 'Ürün seçilmedi ya da işlem seçilmedi.' ),
		'upload'       => array( 'error', 'Görsel yüklenemedi.' ),
		'title'        => array( 'error', 'Ürün adı boş olamaz.' ),
		'code_taken'   => array( 'error', 'Bu ürün kodu başka bir üründe kullanılıyor. Farklı bir kod yazın.' ),
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

add_action( 'admin_post_nwcs_pool_save', 'nwcs_handle_pool_save' );
function nwcs_handle_pool_save(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	$id = isset( $_POST['urun'] ) ? absint( $_POST['urun'] ) : 0;
	check_admin_referer( 'nwcs_pool_save_' . $id );

	$title = isset( $_POST['title'] ) ? nwcs_clean_text( wp_unslash( $_POST['title'] ) ) : '';
	$code  = isset( $_POST['code'] ) ? nwcs_normalize_product_code( wp_unslash( $_POST['code'] ) ) : '';

	if ( '' === $title ) {
		nwcs_pool_keep_draft( $id, 'title' );
	}

	switch_to_blog( nwcs_pool_blog_id() );

	// Kod, hicbir sey yazilmadan once denetlenir: cakisirsa yeni urun
	// olusmaz, mevcut urun degismez; girilenler formda geri gelir.
	$taken = '' !== $code ? nwcs_product_id_by_code( $code, $id ) : 0;

	if ( $taken ) {
		$taken_title = get_the_title( $taken );
		restore_current_blog();
		nwcs_pool_keep_draft( $id, 'code_taken', $taken_title );
	}

	// wp_insert_post ve update_post_meta ters egik cizgiyi siler; metin aynen kalsin.
	$postarr = array(
		'post_type'    => NWCS_PRODUCT_TYPE,
		'post_status'  => 'publish',
		'post_title'   => wp_slash( $title ),
		'post_content' => isset( $_POST['body'] ) ? wp_slash( wp_kses_post( wp_unslash( $_POST['body'] ) ) ) : '',
	);

	if ( $id ) {
		$postarr['ID'] = $id;
		wp_update_post( $postarr );
	} else {
		$id = (int) wp_insert_post( $postarr );
	}

	if ( ! $id ) {
		restore_current_blog();
		nwcs_pool_keep_draft( 0, 'title' );
	}

	// Urun kodu: verilmisse (benzersizligi yukarida denetlendi) yazilir, verilmemisse uretilir.
	if ( '' !== $code ) {
		update_post_meta( $id, '_nwcs_code', $code );
	} else {
		nwcs_ensure_product_code( $id );
	}

	update_post_meta( $id, '_nwcs_short', wp_slash( isset( $_POST['short'] ) ? nwcs_clean_text( wp_unslash( $_POST['short'] ), true ) : '' ) );
	update_post_meta( $id, '_nwcs_price', wp_slash( isset( $_POST['price'] ) ? nwcs_clean_text( wp_unslash( $_POST['price'] ) ) : '' ) );
	update_post_meta( $id, '_nwcs_spec', wp_slash( isset( $_POST['spec'] ) ? nwcs_clean_text( wp_unslash( $_POST['spec'] ) ) : '' ) );

	// Kategoriler: listeden secilenler + varsa yeni olusturulan.
	$slugs = isset( $_POST['categories'] ) && is_array( $_POST['categories'] )
		? array_map( 'sanitize_title', wp_unslash( $_POST['categories'] ) )
		: array();

	$term_ids = array();

	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, NWCS_PRODUCT_TAX );

		if ( $term ) {
			$term_ids[] = (int) $term->term_id;
		}
	}

	$new_category = isset( $_POST['new_category'] ) ? nwcs_clean_text( wp_unslash( $_POST['new_category'] ) ) : '';

	if ( '' !== $new_category ) {
		$created = wp_insert_term( $new_category, NWCS_PRODUCT_TAX );

		if ( ! is_wp_error( $created ) ) {
			$term_ids[] = (int) $created['term_id'];
		} elseif ( $created->get_error_data( 'term_exists' ) ) {
			$term_ids[] = (int) $created->get_error_data( 'term_exists' );
		}
	}

	wp_set_object_terms( $id, $term_ids, NWCS_PRODUCT_TAX, false );

	// Galeri: formdaki sira + yeni yuklenenler sona eklenir.
	$gallery = isset( $_POST['gallery'] ) && is_array( $_POST['gallery'] )
		? array_values( array_filter( array_map( 'absint', wp_unslash( $_POST['gallery'] ) ) ) )
		: array();

	$uploaded = nwcs_handle_gallery_upload();

	if ( null === $uploaded ) {
		restore_current_blog();
		nwcs_pool_redirect( 'upload', $id );
	}

	$gallery = array_values( array_unique( array_merge( $gallery, $uploaded ) ) );

	update_post_meta( $id, '_nwcs_gallery', $gallery );

	nwcs_save_product_tables( $id );

	// One cikan gorsel, galerinin ilki (eski kodla uyum icin).
	if ( $gallery ) {
		set_post_thumbnail( $id, (int) $gallery[0] );
	} else {
		delete_post_thumbnail( $id );
	}

	restore_current_blog();
	nwcs_pool_flush_cache();

	nwcs_pool_redirect( 'saved', $id );
}

/**
 * Kaydedilemeyen formu kullanicinin taslagina alir ve forma geri doner.
 *
 * Kod cakismasi ya da bos ad gibi bir hatada yazilanlar kaybolmasin diye
 * ham degerler 15 dakikalik bir gecici kayda yazilir; form acilirken
 * nwcs_pool_take_draft ile geri doldurulur. Yuklenen dosyalar tasinamaz;
 * kullaniciya yeniden secmesi soylenir.
 */
function nwcs_pool_keep_draft( int $id, string $error, string $taken_by = '' ): void {
	// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput -- cagiran denetledi; degerler formda kacisla basilir.
	$field = static fn( string $key ): string => isset( $_POST[ $key ] ) && is_scalar( $_POST[ $key ] ) ? (string) wp_unslash( $_POST[ $key ] ) : '';

	$draft = array(
		'id'           => $id,
		'title'        => nwcs_clean_text( $field( 'title' ) ),
		'code'         => nwcs_clean_text( $field( 'code' ) ),
		'short'        => nwcs_clean_text( $field( 'short' ), true ),
		'price'        => nwcs_clean_text( $field( 'price' ) ),
		'spec'         => nwcs_clean_text( $field( 'spec' ) ),
		'body'         => wp_kses_post( $field( 'body' ) ),
		'new_category' => nwcs_clean_text( $field( 'new_category' ) ),
		'categories'   => isset( $_POST['categories'] ) && is_array( $_POST['categories'] ) ? array_map( 'sanitize_title', array_filter( wp_unslash( $_POST['categories'] ), 'is_scalar' ) ) : array(),
		'gallery'      => isset( $_POST['gallery'] ) && is_array( $_POST['gallery'] ) ? array_values( array_filter( array_map( 'absint', array_filter( $_POST['gallery'], 'is_scalar' ) ) ) ) : array(),
		'tables'       => nwcs_sanitize_product_tables( (array) json_decode( $field( 'tables_json' ), true ) ),
		'had_upload'   => ! empty( $_FILES['gallery_upload']['name'][0] ),
		'taken_by'     => $taken_by,
	);
	// phpcs:enable

	set_transient( 'nwcs_pool_draft_' . get_current_user_id(), $draft, 15 * MINUTE_IN_SECONDS );

	$args = array( 'nwcs_pool' => $error, 'taslak' => 1 );
	$args += $id ? array( 'urun' => $id ) : array( 'yeni' => 1 );

	wp_safe_redirect( nwcs_pool_url( $args ) );
	exit;
}

/**
 * nwcs_pool_keep_draft ile saklanan taslak; bir kez okunur. Urun formunun
 * bekledigi bicime cevrilir (gorseller ve kategoriler dahil).
 */
function nwcs_pool_take_draft( ?array $product ): ?array {
	$key   = 'nwcs_pool_draft_' . get_current_user_id();
	$draft = get_transient( $key );

	if ( ! is_array( $draft ) || (int) $draft['id'] !== (int) ( $product['id'] ?? 0 ) ) {
		return null;
	}

	delete_transient( $key );

	$categories = array();
	$pool       = nwcs_pool_categories();

	foreach ( $draft['categories'] as $slug ) {
		if ( isset( $pool[ $slug ] ) ) {
			$categories[ $slug ] = $pool[ $slug ]['name'];
		}
	}

	switch_to_blog( nwcs_pool_blog_id() );

	$images = array();

	foreach ( $draft['gallery'] as $image_id ) {
		$src      = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		$images[] = array( 'id' => $image_id, 'url' => $src ? $src[0] : '' );
	}

	restore_current_blog();

	return array_merge(
		(array) $product,
		array(
			'id'           => (int) $draft['id'],
			'title'        => $draft['title'],
			'code'         => $draft['code'],
			'short'        => $draft['short'],
			'price'        => $draft['price'],
			'spec'         => $draft['spec'],
			'body'         => $draft['body'],
			'categories'   => $categories,
			'images'       => $images,
			'tables'       => $draft['tables'],
			'new_category' => $draft['new_category'],
			'had_upload'   => $draft['had_upload'],
			'taken_by'     => $draft['taken_by'],
		)
	);
}

/**
 * Formdaki coklu gorsel yuklemesini isler. Havuz baglaminda cagrilir.
 *
 * @return int[]|null Yuklenen ek kimlikleri; hata durumunda null.
 */
function nwcs_handle_gallery_upload(): ?array {
	$files = $_FILES['gallery_upload'] ?? null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- asagida tek tek islenir.

	if ( ! is_array( $files ) || ! isset( $files['name'] ) || ! is_array( $files['name'] ) ) {
		return array();
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$ids = array();

	foreach ( array_keys( $files['name'] ) as $index ) {
		if ( UPLOAD_ERR_NO_FILE === (int) $files['error'][ $index ] ) {
			continue;
		}

		$_FILES['nwcs_single'] = array(
			'name'     => $files['name'][ $index ],
			'type'     => $files['type'][ $index ],
			'tmp_name' => $files['tmp_name'][ $index ],
			'error'    => $files['error'][ $index ],
			'size'     => $files['size'][ $index ],
		);

		$attachment_id = media_handle_upload( 'nwcs_single', 0 );

		if ( is_wp_error( $attachment_id ) ) {
			unset( $_FILES['nwcs_single'] );

			return null;
		}

		$ids[] = (int) $attachment_id;
	}

	unset( $_FILES['nwcs_single'] );

	return $ids;
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

		nwcs_forget_product( $id );
		nwcs_pool_flush_cache();
	}

	nwcs_pool_redirect( 'deleted', 0 );
}

/**
 * Silinen urunu sitelerin secim ve istisnalarindan temizler.
 */
function nwcs_forget_product( int $product_id ): void {
	foreach ( array_keys( nwcs_editable_sites() ) as $blog_id ) {
		switch_to_blog( $blog_id );

		$settings             = nwcs_site_product_settings();
		$settings['selected'] = array_values( array_diff( $settings['selected'], array( $product_id ) ) );
		unset( $settings['overrides'][ $product_id ] );

		update_option( NWCS_OPTION_SELECTED, $settings['selected'] );
		update_option( NWCS_OPTION_OVERRIDES, $settings['overrides'] );

		restore_current_blog();
	}
}

/* ---------------- Toplu islemler ---------------- */

add_action( 'admin_post_nwcs_pool_bulk', 'nwcs_handle_pool_bulk' );
function nwcs_handle_pool_bulk(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	check_admin_referer( 'nwcs_pool_bulk' );

	$ids = isset( $_POST['urunler'] ) && is_array( $_POST['urunler'] )
		? array_values( array_filter( array_map( 'absint', wp_unslash( $_POST['urunler'] ) ) ) )
		: array();

	$action = isset( $_POST['bulk_action'] ) ? sanitize_text_field( wp_unslash( $_POST['bulk_action'] ) ) : '';

	if ( ! $ids || '' === $action ) {
		nwcs_pool_redirect( 'bulk_empty', 0 );
	}

	[ $verb, $target ] = array_pad( explode( ':', $action, 2 ), 2, '' );
	$pool              = nwcs_pool_products();
	$ids               = array_values( array_filter( $ids, static fn( int $id ): bool => isset( $pool[ $id ] ) ) );

	if ( 'cat' === $verb ) {
		$term = get_term_by( 'slug', sanitize_title( $target ), NWCS_PRODUCT_TAX );

		if ( $term ) {
			switch_to_blog( nwcs_pool_blog_id() );

			foreach ( $ids as $id ) {
				wp_set_object_terms( $id, array( (int) $term->term_id ), NWCS_PRODUCT_TAX, true );
			}

			restore_current_blog();
		}
	} elseif ( in_array( $verb, array( 'show', 'hide' ), true ) ) {
		$blog_id = absint( $target );
		$sites   = nwcs_editable_sites();

		if ( isset( $sites[ $blog_id ] ) ) {
			switch_to_blog( $blog_id );

			$settings = nwcs_site_product_settings();

			foreach ( $ids as $id ) {
				$override = $settings['overrides'][ $id ] ?? array();

				if ( 'hide' === $verb ) {
					$override['hidden'] = 1;
				} else {
					$override['hidden'] = 0;

					// "Secilenler" kipinde gosterebilmek icin listeye de eklenir.
					if ( 'selected' === $settings['mode'] && ! in_array( $id, $settings['selected'], true ) ) {
						$settings['selected'][] = $id;
					}
				}

				$settings['overrides'][ $id ] = $override;
			}

			nwcs_save_site_product_settings( $settings );
			restore_current_blog();
		}
	}

	nwcs_pool_flush_cache();
	nwcs_pool_redirect( 'bulk', count( $ids ), true );
}

/* ---------------- Kategoriler ---------------- */

add_action( 'admin_post_nwcs_pool_category_add', 'nwcs_handle_category_add' );
function nwcs_handle_category_add(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	check_admin_referer( 'nwcs_pool_category_add' );

	$name = isset( $_POST['isim'] ) ? nwcs_clean_text( wp_unslash( $_POST['isim'] ) ) : '';

	if ( '' !== $name ) {
		switch_to_blog( nwcs_pool_blog_id() );
		wp_insert_term( $name, NWCS_PRODUCT_TAX );
		restore_current_blog();
	}

	nwcs_pool_redirect( 'cat_added', 0 );
}

add_action( 'admin_post_nwcs_pool_category_delete', 'nwcs_handle_category_delete' );
function nwcs_handle_category_delete(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	$term_id = isset( $_POST['kategori'] ) ? absint( $_POST['kategori'] ) : 0;
	check_admin_referer( 'nwcs_pool_category_delete_' . $term_id );

	if ( $term_id ) {
		switch_to_blog( nwcs_pool_blog_id() );
		wp_delete_term( $term_id, NWCS_PRODUCT_TAX );
		restore_current_blog();
		nwcs_pool_flush_cache();
	}

	nwcs_pool_redirect( 'cat_deleted', 0 );
}

/**
 * Urunu kalmamis kategorileri toplu siler. Excel yuklemesi geri alindiginda
 * o partinin kategorileri zaten temizlenir; bu, elle birikenler icindir.
 */
add_action( 'admin_post_nwcs_pool_category_prune', 'nwcs_handle_pool_category_prune' );
function nwcs_handle_pool_category_prune(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ), '', array( 'response' => 403 ) );
	}

	check_admin_referer( 'nwcs_pool_category_prune' );

	switch_to_blog( nwcs_pool_blog_id() );

	$terms   = get_terms( array( 'taxonomy' => NWCS_PRODUCT_TAX, 'hide_empty' => false ) );
	$removed = 0;

	foreach ( is_array( $terms ) ? $terms : array() as $term ) {
		if ( 0 === (int) $term->count ) {
			wp_delete_term( (int) $term->term_id, NWCS_PRODUCT_TAX );
			++$removed;
		}
	}

	restore_current_blog();
	nwcs_pool_flush_cache();

	nwcs_pool_redirect( 'cats_pruned', $removed, true );
}

/**
 * Havuz sayfasina bildirimli donus.
 *
 * $context: duzenleme formunu acik tutmak icin urun kimligi ya da bildirimde
 * kullanilacak adet (toplu islem, ice aktarma).
 */
function nwcs_pool_redirect( string $key, int $context = 0, bool $as_count = false ): void {
	$args = array( 'nwcs_pool' => $key );

	if ( $context ) {
		$args[ $as_count ? 'adet' : 'urun' ] = $context;
	}

	wp_safe_redirect( nwcs_pool_url( $args ) );
	exit;
}

/**
 * Tek seferlik temizlik: kaynak sitelerden kopyalanan bazi urun adlarinin
 * sonunda gorunmez satir ayirici (U+2028/U+2029) kalmis; sayfa basliginda
 * ve aramada sorun cikariyordu. Ad nwcs_clean_text'ten gecirilir, adres
 * (post_name) degismez. Bir kez calisir.
 */
add_action( 'admin_init', 'nwcs_pool_cleanup_line_separators' );
function nwcs_pool_cleanup_line_separators(): void {
	if ( get_site_option( 'nwcs_cleanup_line_separators' ) || ! current_user_can( NWCS_CAPABILITY ) ) {
		return;
	}

	update_site_option( 'nwcs_cleanup_line_separators', 1 );

	global $wpdb;

	switch_to_blog( nwcs_pool_blog_id() );

	$ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND ( post_title LIKE %s OR post_title LIKE %s )",
			NWCS_PRODUCT_TYPE,
			'%' . $wpdb->esc_like( "\u{2028}" ) . '%',
			'%' . $wpdb->esc_like( "\u{2029}" ) . '%'
		)
	);

	foreach ( $ids as $id ) {
		wp_update_post(
			array(
				'ID'         => (int) $id,
				'post_title' => wp_slash( nwcs_clean_text( get_post_field( 'post_title', (int) $id ) ) ),
			)
		);
	}

	restore_current_blog();

	if ( $ids ) {
		nwcs_pool_flush_cache();
	}
}
