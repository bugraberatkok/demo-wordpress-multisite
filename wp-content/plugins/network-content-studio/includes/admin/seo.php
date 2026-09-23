<?php
/**
 * SEO ve GEO sekmesi (ag yonetimi).
 *
 * Iki gorunum:
 *   - Ag ozeti: butun sitelerin durumu tek tabloda ve siteler arasi firma
 *     bilgisi tutarliligi (ad, unvan, telefon, adres).
 *   - Site: firma bilgisi, varsayilanlar ve her sayfanin Google onizlemesiyle
 *     birlikte arama basligi / aciklamasi / paylasim gorseli.
 *
 * Yeni kaydetme kodu yok: formlar Icerik Studyosu'nun AJAX kaydetme yolunu
 * (nwcs_save_ajax) ve ayni guvenlik zincirini kullanir. SEO alanlari
 * manifeste eklenti tarafindan eklenen alanlardir (includes/manifest.php).
 */

defined( 'ABSPATH' ) || exit;

const NWCS_SEO_SLUG = 'nwcs-seo';

add_action( 'network_admin_menu', 'nwcs_register_seo_menu' );
function nwcs_register_seo_menu(): void {
	add_menu_page(
		'SEO ve GEO',
		'SEO ve GEO',
		NWCS_CAPABILITY,
		NWCS_SEO_SLUG,
		'nwcs_render_seo',
		'dashicons-search',
		5
	);
}

add_action( 'admin_enqueue_scripts', 'nwcs_seo_admin_assets', 20 );
function nwcs_seo_admin_assets( string $hook ): void {
	// Studyo'da da yuklenir: sayfalarin "Arama ve Paylasim" bolumu orada da var.
	if ( str_contains( $hook, NWCS_SEO_SLUG ) || str_contains( $hook, NWCS_MENU_SLUG ) ) {
		wp_enqueue_script( 'nwcs-seo', NWCS_URL . 'assets/seo.js', array( 'nwcs-admin' ), NWCS_VERSION, true );
	}
}

/**
 * Sekme adresi. $blog_id 0 ise ag ozeti.
 */
function nwcs_seo_url( int $blog_id = 0 ): string {
	$args = array( 'page' => NWCS_SEO_SLUG );

	if ( $blog_id ) {
		$args['site'] = $blog_id;
	}

	return add_query_arg( $args, network_admin_url( 'admin.php' ) );
}

/* ====================================================================== *
 * Durum raporu
 * ====================================================================== */

/**
 * Firma bilgisinde bulunmasi gereken alanlar ve Turkce adlari.
 */
function nwcs_seo_required_org_fields(): array {
	return array(
		'name'        => 'firma adı',
		'legal_name'  => 'resmî unvan',
		'description' => 'firma tanımı',
		'phone'       => 'telefon',
		'street'      => 'adres',
		'city'        => 'il',
	);
}

/**
 * Bir sitenin SEO durumu. Site baglamina gecip geri doner.
 */
function nwcs_seo_site_report( int $blog_id ): array {
	switch_to_blog( $blog_id );

	$manifest = nwcs_manifest();
	$pages    = array();
	$custom   = 0;
	$empty    = 0;

	foreach ( nwcs_seo_pages( $manifest ) as $key => $page ) {
		$resolved = nwcs_seo_page_resolved( $key, $manifest );

		if ( '' !== $resolved['custom']['title'] || '' !== $resolved['custom']['description'] ) {
			++$custom;
		}

		if ( '' === $resolved['description'] ) {
			++$empty;
		}

		$pages[ $key ] = array(
			'label'    => $page['label'],
			'url'      => home_url( $page['path'] ),
			'resolved' => $resolved,
		);
	}

	$org     = nwcs_seo_org_data();
	$missing = array();

	foreach ( nwcs_seo_required_org_fields() as $key => $label ) {
		if ( '' === ( 'name' === $key ? nwcs_seo_clean( nwcs_field( NWCS_SEO_SITE_PAGE, 'org', 'name' ) ) : $org[ $key ] ) ) {
			$missing[] = $label;
		}
	}

	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
		)
	);

	$report = array(
		'pages'   => $pages,
		'custom'  => $custom,
		'empty'   => $empty,
		'org'     => $org,
		'missing' => $missing,
		'public'  => (bool) get_option( 'blog_public' ),
		'home'    => home_url( '/' ),
		'sitemap' => home_url( '/wp-sitemap.xml' ),
		'llms'    => home_url( '/llms.txt' ),
		'reading' => admin_url( 'options-reading.php' ),
		'posts'   => array_map(
			static fn( WP_Post $post ): array => array(
				'title'       => nwcs_seo_clean( get_the_title( $post ) ),
				'url'         => (string) get_permalink( $post ),
				'edit'        => (string) get_edit_post_link( $post, 'raw' ),
				'description' => nwcs_seo_clean( has_excerpt( $post ) ? $post->post_excerpt : $post->post_content, 160 ),
				'own'         => has_excerpt( $post ),
			),
			$posts
		),
	);

	restore_current_blog();

	return $report;
}

/**
 * Siteler arasi firma bilgisi tutarliligi. Ayni firmanin sitelerinde unvan,
 * telefon ve adres birebir ayni yazilmali; bos alanlar karsilastirilmaz.
 *
 * @return array<string, array{label:string, values:array<string, string[]>}>
 */
function nwcs_seo_consistency( array $reports, array $sites ): array {
	$checks = array(
		'legal_name' => 'Resmî unvan',
		'phone'      => 'Telefon',
		'address'    => 'Adres',
	);

	$result = array();

	foreach ( $checks as $key => $label ) {
		$values = array();

		foreach ( $reports as $blog_id => $report ) {
			$org   = $report['org'];
			$value = 'address' === $key
				? implode( ', ', array_filter( array( $org['street'], $org['district'], $org['city'] ) ) )
				: $org[ $key ];

			if ( '' === $value ) {
				continue;
			}

			// Bosluk ve buyuk-kucuk harf farki tutarsizlik sayilmaz.
			$normal = mb_strtolower( (string) preg_replace( '/[\s.,]+/u', ' ', $value ) );

			$values[ $normal ]['text']    = $value;
			$values[ $normal ]['sites'][] = $sites[ $blog_id ]['label'];
		}

		$result[ $key ] = array(
			'label'  => $label,
			'values' => array_values( $values ),
		);
	}

	return $result;
}

/* ====================================================================== *
 * Arayuz
 * ====================================================================== */

function nwcs_render_seo(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu sayfaya erişim yetkiniz yok.' ) );
	}

	$sites   = nwcs_editable_sites();
	$blog_id = isset( $_GET['site'] ) ? absint( $_GET['site'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- yalnizca gorunum.

	if ( ! isset( $sites[ $blog_id ] ) ) {
		$blog_id = 0;
	}
	?>
	<div class="wrap nwcs-wrap nwcs-wrap--pool nwcs-seo" data-nwcs-site="<?php echo esc_attr( (string) $blog_id ); ?>">
		<header class="nwcs-bar">
			<div class="nwcs-bar__brand">
				<button type="button" class="nwcs-bar__mark" data-nwcs-menu
					aria-label="Yönetim menüsünü aç/kapat" title="Yönetim menüsünü aç/kapat"></button>
				<h1>SEO ve GEO</h1>
			</div>

			<nav class="nwcs-bar__sites" aria-label="Görünüm seçici">
				<a class="nwcs-sitepill<?php echo 0 === $blog_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( nwcs_seo_url() ); ?>">
					<span class="nwcs-sitepill__dot" aria-hidden="true"></span>
					<span class="nwcs-sitepill__name">Ağ özeti</span>
				</a>
				<?php foreach ( $sites as $id => $site ) : ?>
					<a class="nwcs-sitepill<?php echo $id === $blog_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( nwcs_seo_url( $id ) ); ?>">
						<span class="nwcs-sitepill__dot" aria-hidden="true"></span>
						<span class="nwcs-sitepill__name"><?php echo esc_html( $site['label'] ); ?></span>
						<span class="nwcs-sitepill__path"><?php echo esc_html( $site['path'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		</header>

		<?php
		if ( ! $sites ) {
			echo '<p class="nwcs-empty">Alan manifesti olan bir site yok.</p>';
		} elseif ( $blog_id ) {
			nwcs_render_seo_site( $blog_id, $sites[ $blog_id ] );
		} else {
			nwcs_render_seo_overview( $sites );
		}
		?>

		<div class="nwcs-toast" data-nwcs-toast hidden></div>
	</div>
	<?php
}

/**
 * Ag ozeti.
 */
function nwcs_render_seo_overview( array $sites ): void {
	$reports = array();

	foreach ( $sites as $id => $site ) {
		$reports[ $id ] = nwcs_seo_site_report( $id );
	}

	$consistency = nwcs_seo_consistency( $reports, $sites );
	?>
	<p class="nwcs-help">
		<span class="nwcs-help__step"><b>1</b> Her sayfa, hiçbir şey girilmese de içeriğinden başlık, açıklama ve görsel alır</span>
		<span class="nwcs-help__step"><b>2</b> Firma bilgisini her site için bir kez girin</span>
		<span class="nwcs-help__step"><b>3</b> Önemli sayfaların başlığını ve açıklamasını elle iyileştirin</span>
	</p>

	<div class="nwcs-seo__grid">
		<section class="nwcs-pool__card">
			<h2 class="nwcs-pool__title">Siteler</h2>

			<table class="nwcs-table nwcs-seo__table">
				<thead>
					<tr>
						<th>Site</th>
						<th>Sayfa</th>
						<th>Elle iyileştirilen</th>
						<th>Firma bilgisi</th>
						<th>Arama motorları</th>
						<th>Dosyalar</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $reports as $id => $report ) : ?>
						<tr>
							<td>
								<a class="nwcs-seo__site" href="<?php echo esc_url( nwcs_seo_url( $id ) ); ?>"><?php echo esc_html( $sites[ $id ]['label'] ); ?></a>
								<span class="nwcs-table__spec"><?php echo esc_html( str_replace( array( 'http://', 'https://' ), '', $report['home'] ) ); ?></span>
							</td>
							<td><?php echo (int) count( $report['pages'] ); ?></td>
							<td>
								<?php echo (int) $report['custom']; ?> / <?php echo (int) count( $report['pages'] ); ?>
								<?php if ( $report['empty'] ) : ?>
									<span class="nwcs-table__spec nwcs-seo__warn"><?php echo (int) $report['empty']; ?> sayfanın açıklaması yok</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $report['missing'] ) : ?>
									<span class="nwcs-badge nwcs-badge--warn">Eksik</span>
									<span class="nwcs-table__spec"><?php echo esc_html( implode( ', ', $report['missing'] ) ); ?></span>
								<?php else : ?>
									<span class="nwcs-badge nwcs-badge--ok">Tamam</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $report['public'] ) : ?>
									<span class="nwcs-badge nwcs-badge--ok">Açık</span>
								<?php else : ?>
									<span class="nwcs-badge nwcs-badge--warn">Kapalı</span>
									<a class="nwcs-table__spec" href="<?php echo esc_url( $report['reading'] ); ?>">Okuma ayarları</a>
								<?php endif; ?>
							</td>
							<td class="nwcs-seo__files">
								<a href="<?php echo esc_url( $report['sitemap'] ); ?>" target="_blank" rel="noopener">Site haritası</a>
								<a href="<?php echo esc_url( $report['llms'] ); ?>" target="_blank" rel="noopener">llms.txt</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</section>

		<section class="nwcs-pool__card">
			<h2 class="nwcs-pool__title">Siteler arası tutarlılık</h2>
			<p class="nwcs-hint nwcs-seo__lead">
				Aynı firmanın sitelerinde unvan, telefon ve adres birebir aynı yazılmalı. Arama
				motorları ve yapay zekâ aramaları firmayı bu bilgilerden tek bir şirket olarak tanır.
			</p>

			<ul class="nwcs-seo__checks">
				<?php foreach ( $consistency as $check ) :
					$count = count( $check['values'] );
					?>
					<li class="nwcs-seo__check">
						<div class="nwcs-seo__check-head">
							<strong><?php echo esc_html( $check['label'] ); ?></strong>
							<?php if ( 0 === $count ) : ?>
								<span class="nwcs-badge">Girilmemiş</span>
							<?php elseif ( 1 === $count ) : ?>
								<span class="nwcs-badge nwcs-badge--ok">Tutarlı</span>
							<?php else : ?>
								<span class="nwcs-badge nwcs-badge--warn"><?php echo (int) $count; ?> farklı yazım</span>
							<?php endif; ?>
						</div>

						<?php if ( $count > 1 ) : ?>
							<ul class="nwcs-seo__variants">
								<?php foreach ( $check['values'] as $variant ) : ?>
									<li>
										<span class="nwcs-seo__variant"><?php echo esc_html( $variant['text'] ); ?></span>
										<span class="nwcs-table__spec"><?php echo esc_html( implode( ', ', $variant['sites'] ) ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	</div>
	<?php
}

/**
 * Tek site: firma bilgisi, varsayilanlar, sayfalar, blog yazilari.
 */
function nwcs_render_seo_site( int $blog_id, array $site ): void {
	$manifest = $site['manifest'];
	$report   = nwcs_seo_site_report( $blog_id );
	$values   = nwcs_get_all( $blog_id );
	$media    = nwcs_site_media( $blog_id );
	$host     = str_replace( array( 'http://', 'https://' ), '', untrailingslashit( $report['home'] ) );
	?>
	<div class="nwcs-seo__layout">

		<div class="nwcs-seo__main">
			<section class="nwcs-pool__card">
				<h2 class="nwcs-pool__title">
					Sayfalar
					<span><?php echo (int) count( $report['pages'] ); ?></span>
				</h2>
				<p class="nwcs-hint nwcs-seo__lead">
					Gri yazılar otomatik değerdir; sayfanın içeriğinden gelir ve içerik değişince kendiliğinden güncellenir.
					Bir alanı doldurduğunuzda o sayfa için otomatik değerin yerine sizinki kullanılır.
				</p>

				<?php foreach ( $report['pages'] as $key => $page ) :
					$resolved = $page['resolved'];
					$path     = str_replace( $report['home'], '', $page['url'] );
					$is_own   = '' !== $resolved['custom']['title'] || '' !== $resolved['custom']['description'] || $resolved['custom']['image'];
					?>
					<article class="nwcs-seo__page" id="seo-<?php echo esc_attr( $key ); ?>" data-nwcs-seo-page>
						<header class="nwcs-seo__page-head">
							<h3>
								<?php echo esc_html( nwcs_short_page_label( preg_replace( '/^[^:]+:\s*/u', '', $page['label'] ) ) ); ?>
								<span class="nwcs-badge<?php echo $is_own ? ' nwcs-badge--ok' : ''; ?>"><?php echo $is_own ? 'Elle iyileştirildi' : 'Otomatik'; ?></span>
							</h3>
							<a class="nwcs-table__spec" href="<?php echo esc_url( $page['url'] ); ?>" target="_blank" rel="noopener">Sayfayı aç ↗</a>
						</header>

						<div class="nwcs-serp" aria-label="Google önizlemesi">
							<span class="nwcs-serp__url"><?php echo esc_html( $host . ( '' !== trim( $path, '/' ) ? ' › ' . str_replace( '/', ' › ', trim( $path, '/' ) ) : '' ) ); ?></span>
							<span class="nwcs-serp__title" data-nwcs-serp-title><?php echo esc_html( $resolved['title'] ); ?></span>
							<span class="nwcs-serp__desc" data-nwcs-serp-desc><?php echo esc_html( '' !== $resolved['description'] ? $resolved['description'] : 'Açıklama yok: Google sayfadan kendi seçtiği bir bölümü gösterir.' ); ?></span>
						</div>

						<?php // Varsayilan kapali: once on izlemeler taranir, gerekirse acilir. ?>
						<details class="nwcs-seo__edit">
							<summary>Başlığı, açıklamayı ve paylaşım görselini düzenle</summary>
							<?php
							nwcs_seo_render_form(
								$blog_id,
								$manifest,
								$key,
								NWCS_SEO_COMPONENT,
								$values,
								$media,
								array(
									'title'       => $resolved['auto']['title'],
									'description' => $resolved['auto']['description'],
								)
							);
							?>
						</details>
					</article>
				<?php endforeach; ?>
			</section>

			<?php if ( $report['posts'] ) : ?>
				<section class="nwcs-pool__card">
					<h2 class="nwcs-pool__title">
						Blog yazıları
						<span><?php echo (int) count( $report['posts'] ); ?></span>
					</h2>
					<p class="nwcs-hint nwcs-seo__lead">
						Yazıların arama açıklaması, WordPress yazı düzenleyicisindeki <strong>Özet</strong> alanından gelir; özet
						boşsa yazının ilk cümleleri kullanılır. Başlık yazının kendi başlığıdır.
					</p>

					<table class="nwcs-table">
						<tbody>
							<?php foreach ( $report['posts'] as $post ) : ?>
								<tr>
									<td>
										<a href="<?php echo esc_url( $post['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $post['title'] ); ?></a>
										<span class="nwcs-table__spec"><?php echo esc_html( $post['description'] ); ?></span>
									</td>
									<td class="nwcs-table__actions">
										<span class="nwcs-badge<?php echo $post['own'] ? ' nwcs-badge--ok' : ''; ?>"><?php echo $post['own'] ? 'Özet var' : 'Otomatik'; ?></span>
										<a class="button button-small" href="<?php echo esc_url( $post['edit'] ); ?>">Düzenle</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</section>
			<?php endif; ?>
		</div>

		<aside class="nwcs-seo__side">
			<section class="nwcs-pool__card">
				<h2 class="nwcs-pool__title">
					Firma bilgisi
					<?php if ( $report['missing'] ) : ?>
						<span class="nwcs-seo__warn-pill"><?php echo (int) count( $report['missing'] ); ?> eksik</span>
					<?php endif; ?>
				</h2>
				<p class="nwcs-hint nwcs-seo__lead">
					Sitenin bütün sayfalarına işlenir: Google Haritalar eşleşmesi, arama sonucundaki firma kutusu ve
					yapay zekâ aramalarının firmayı tanıması bu bilgilerden olur.
				</p>
				<?php nwcs_seo_render_form( $blog_id, $manifest, NWCS_SEO_SITE_PAGE, 'org', $values, $media ); ?>
			</section>

			<section class="nwcs-pool__card">
				<h2 class="nwcs-pool__title">Varsayılanlar</h2>
				<?php nwcs_seo_render_form( $blog_id, $manifest, NWCS_SEO_SITE_PAGE, 'defaults', $values, $media ); ?>
			</section>

			<section class="nwcs-pool__card">
				<h2 class="nwcs-pool__title">Otomatik üretilenler</h2>
				<ul class="nwcs-seo__auto">
					<li>
						<strong>Arama motorları</strong>
						<?php echo $report['public'] ? 'Siteyi dizine ekleyebilir.' : 'Site arama motorlarına <em>kapalı</em>.'; ?>
						<a href="<?php echo esc_url( $report['reading'] ); ?>">Okuma ayarları</a>
					</li>
					<li>
						<strong>Site haritası</strong>
						Sayfalar ve yazılar eklendikçe güncellenir.
						<a href="<?php echo esc_url( $report['sitemap'] ); ?>" target="_blank" rel="noopener">Aç</a>
					</li>
					<li>
						<strong>llms.txt</strong>
						Yapay zekâ aramaları için sitenin özeti: sayfalar, ürün ölçüleri, yazılar, iletişim.
						<a href="<?php echo esc_url( $report['llms'] ); ?>" target="_blank" rel="noopener">Aç</a>
					</li>
					<li>
						<strong>Yapılandırılmış veri</strong>
						Firma, konum yolu, ürün ve yazı bilgisi her sayfaya kendiliğinden eklenir.
					</li>
				</ul>
			</section>
		</aside>
	</div>
	<?php
}

/**
 * Bir bilesenin formu; Icerik Studyosu'nun kaydetme yolunu kullanir.
 * $placeholders: bos alanlarda gri gorunecek otomatik degerler.
 */
function nwcs_seo_render_form( int $blog_id, array $manifest, string $page_key, string $component_key, array $values, array $media, array $placeholders = array() ): void {
	$component = $manifest['pages'][ $page_key ]['components'][ $component_key ] ?? null;

	if ( ! $component ) {
		return;
	}
	?>
	<form class="nwcs-form nwcs-seo__form" method="post" enctype="multipart/form-data"
		action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-nwcs-form>

		<input type="hidden" name="action" value="nwcs_save" />
		<input type="hidden" name="site" value="<?php echo esc_attr( (string) $blog_id ); ?>" />
		<input type="hidden" name="content_page" value="<?php echo esc_attr( $page_key ); ?>" />
		<input type="hidden" name="component" value="<?php echo esc_attr( $component_key ); ?>" />
		<?php wp_nonce_field( 'nwcs_save_' . $blog_id . '_' . $page_key . '_' . $component_key ); ?>

		<div class="nwcs-fields">
			<?php
			foreach ( $component['fields'] as $field_key => $definition ) {
				if ( isset( $placeholders[ $field_key ] ) && '' !== $placeholders[ $field_key ] ) {
					$definition['placeholder'] = $placeholders[ $field_key ];
				}

				$value = $values[ $page_key ][ $component_key ][ $field_key ] ?? ( $definition['default'] ?? '' );
				nwcs_render_field( $field_key, $definition, $value, $media, $blog_id );
			}
			?>
		</div>

		<footer class="nwcs-actions">
			<button type="submit" class="button button-primary">Kaydet ve Yayınla</button>
			<span class="nwcs-dirty" data-nwcs-dirty hidden>● kaydedilmedi</span>
		</footer>
	</form>
	<?php
}

/**
 * Icerik Studyosu'ndaki "Arama ve Paylasim" bolumu icin otomatik degerler.
 */
function nwcs_seo_placeholders( int $blog_id, string $page_key ): array {
	switch_to_blog( $blog_id );
	$resolved = nwcs_seo_page_resolved( $page_key );
	restore_current_blog();

	return array(
		'title'       => $resolved['auto']['title'],
		'description' => $resolved['auto']['description'],
	);
}
