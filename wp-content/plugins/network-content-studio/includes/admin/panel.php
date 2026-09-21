<?php
/**
 * Ag yonetimindeki tek giris noktasi: Icerik Studyosu.
 *
 * Duzen: solda sayfa secimi + bolum listesi + duzenleyici, ortada sitenin
 * calisan onizlemesi, sagda dikey site secici.
 *
 * JavaScript kapaliyken de calisir: bolum baglantilari normal baglanti,
 * duzenleyici sunucu tarafinda cizilir ve form admin-post.php'ye gider.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_MENU_SLUG  = 'nwcs-studio';
const NWCS_CAPABILITY = 'manage_network_options';

add_action( 'network_admin_menu', 'nwcs_register_menu' );
function nwcs_register_menu(): void {
	add_menu_page(
		'İçerik Stüdyosu',
		'İçerik Stüdyosu',
		NWCS_CAPABILITY,
		NWCS_MENU_SLUG,
		'nwcs_render_panel',
		'dashicons-edit-page',
		3
	);
}

add_action( 'admin_enqueue_scripts', 'nwcs_admin_assets' );
function nwcs_admin_assets( string $hook ): void {
	if ( ! str_contains( $hook, NWCS_MENU_SLUG ) ) {
		return;
	}

	wp_enqueue_style( 'nwcs-admin', NWCS_URL . 'assets/admin.css', array(), NWCS_VERSION );
	wp_enqueue_script( 'nwcs-admin', NWCS_URL . 'assets/admin.js', array(), NWCS_VERSION, true );
	wp_localize_script(
		'nwcs-admin',
		'nwcsPanel',
		array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'nwcs_panel' ),
			'previewOrigin' => untrailingslashit( network_site_url() ),
			'text'       => array(
				'confirmDiscard' => 'Kaydedilmemiş değişiklikleriniz var. Son kaydedilen hâle dönülsün mü?',
				'confirmLeave'   => 'Kaydedilmemiş değişiklikleriniz var.',
				'confirmRow'     => 'Bu satır silinsin mi? Kaydettiğinizde kalıcı olur.',
				'saving'         => 'Kaydediliyor…',
				'saved'          => 'Yayınlandı',
				'saveError'      => 'Kaydedilemedi. Lütfen tekrar deneyin.',
				'loading'        => 'Yükleniyor…',
			),
		)
	);
}

/**
 * Ag icindeki, manifesti olan siteler. Ana site (ag yonetimi) listeye girmez.
 */
function nwcs_editable_sites(): array {
	$sites = array();

	foreach ( get_sites( array( 'number' => 100 ) ) as $site ) {
		$blog_id  = (int) $site->blog_id;
		$manifest = nwcs_manifest_for_blog( $blog_id );

		if ( empty( $manifest['pages'] ) ) {
			continue;
		}

		$sites[ $blog_id ] = array(
			'blog_id'  => $blog_id,
			'label'    => $manifest['site_label'] ?? get_blog_option( $blog_id, 'blogname', '' ),
			'url'      => get_home_url( $blog_id, '/' ),
			'path'     => $site->path,
			'manifest' => $manifest,
		);
	}

	return $sites;
}

/**
 * Sayfadaki bilesenleri gosterim sirasina gore verir: once sabit bilesenler
 * (manifest sirasi), sonra kayitli siraya gore siralanabilir bolumler.
 */
function nwcs_ordered_components( array $manifest, string $page_key, int $blog_id ): array {
	$components = array_keys( $manifest['pages'][ $page_key ]['components'] ?? array() );
	$sortable   = nwcs_sortable_sections( $manifest, $page_key );

	if ( ! $sortable ) {
		return $components;
	}

	$fixed  = array_values( array_diff( $components, $sortable ) );
	$sorted = nwcs_section_order( $page_key, $blog_id, $manifest );

	return array_merge( $fixed, $sorted );
}

/**
 * Panel ana govdesi.
 */
function nwcs_render_panel(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu sayfaya erişim yetkiniz yok.' ) );
	}

	$sites = nwcs_editable_sites();

	if ( ! $sites ) {
		echo '<div class="wrap"><h1>İçerik Stüdyosu</h1><div class="notice notice-error"><p>';
		echo 'Alan manifesti olan bir alt site bulunamadı. Temaların ağ genelinde etkin olduğundan emin olun.';
		echo '</p></div></div>';

		return;
	}

	$site_ids = array_keys( $sites );

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- yalnizca gorunum secimi.
	$blog_id = isset( $_GET['site'] ) ? absint( $_GET['site'] ) : 0;
	if ( ! in_array( $blog_id, $site_ids, true ) ) {
		$blog_id = (int) reset( $site_ids );
	}

	$site     = $sites[ $blog_id ];
	$manifest = $site['manifest'];
	$pages    = $manifest['pages'];

	$page_key = isset( $_GET['content_page'] ) ? sanitize_key( wp_unslash( $_GET['content_page'] ) ) : '';
	if ( ! isset( $pages[ $page_key ] ) ) {
		$page_key = isset( $pages['home'] ) ? 'home' : (string) array_key_first( $pages );
	}

	$component_key = isset( $_GET['component'] ) ? sanitize_key( wp_unslash( $_GET['component'] ) ) : '';
	if ( ! isset( $pages[ $page_key ]['components'][ $component_key ] ) ) {
		$component_key = '';
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$ordered  = nwcs_ordered_components( $manifest, $page_key, $blog_id );
	$sortable = nwcs_sortable_sections( $manifest, $page_key );
	$preview  = nwcs_preview_url( $blog_id, $manifest, $page_key );
	?>
	<div class="wrap nwcs-wrap"
		data-nwcs-site="<?php echo esc_attr( (string) $blog_id ); ?>"
		data-nwcs-page="<?php echo esc_attr( $page_key ); ?>">

		<div class="nwcs-topbar">
			<div class="nwcs-topbar__title">
				<span class="dashicons dashicons-edit-page" aria-hidden="true"></span>
				<h1>İçerik Stüdyosu</h1>
			</div>

			<div class="nwcs-topbar__tools">
				<div class="nwcs-device" role="group" aria-label="Önizleme genişliği">
					<button type="button" class="is-active" data-nwcs-device="desktop">Masaüstü</button>
					<button type="button" data-nwcs-device="mobile">Telefon</button>
				</div>
				<a class="button" href="<?php echo esc_url( $site['url'] ); ?>" target="_blank" rel="noopener">Siteyi yeni sekmede aç ↗</a>
			</div>
		</div>

		<?php nwcs_render_notices(); ?>

		<p class="nwcs-help">
			<strong>Nasıl çalışır?</strong>
			Sağdan siteyi seçin, ortadaki önizlemede değiştirmek istediğiniz <em>yazıya veya görsele tıklayın</em>,
			soldaki formda düzenleyip <em>Kaydet ve Yayınla</em> deyin. Kaydettiğiniz anda sitede görünür.
		</p>

		<div class="nwcs-app">
			<!-- Sol: bolum listesi + duzenleyici -->
			<div class="nwcs-side">
				<div class="nwcs-pages" role="tablist" aria-label="Sayfalar">
					<?php foreach ( $pages as $key => $page ) : ?>
						<a class="nwcs-pages__tab<?php echo $key === $page_key ? ' is-active' : ''; ?>"
							href="<?php echo esc_url( nwcs_panel_url( $blog_id, $key ) ); ?>">
							<?php echo esc_html( nwcs_short_page_label( $page['label'] ) ); ?>
						</a>
					<?php endforeach; ?>
				</div>

				<!-- Ekran 1: bolum listesi -->
				<div class="nwcs-screen nwcs-screen--list<?php echo $component_key ? '' : ' is-active'; ?>" data-nwcs-screen="list">
					<p class="nwcs-screen__hint">Bir bölüme tıklayın ya da önizlemede düzenlemek istediğiniz yere tıklayın.</p>

					<ul class="nwcs-sections" data-nwcs-sections>
						<?php
						foreach ( $ordered as $index => $key ) :
							$component   = $pages[ $page_key ]['components'][ $key ] ?? null;
							$is_sortable = in_array( $key, $sortable, true );

							if ( ! $component ) {
								continue;
							}
							?>
							<li class="nwcs-section<?php echo $is_sortable ? ' is-sortable' : ''; ?>" data-nwcs-section-key="<?php echo esc_attr( $key ); ?>">
								<a class="nwcs-section__open<?php echo $key === $component_key ? ' is-active' : ''; ?>"
									href="<?php echo esc_url( nwcs_panel_url( $blog_id, $page_key, $key ) ); ?>"
									data-nwcs-open-component="<?php echo esc_attr( $key ); ?>">
									<span class="nwcs-section__name"><?php echo esc_html( $component['label'] ); ?></span>
									<span class="nwcs-section__meta"><?php echo (int) count( $component['fields'] ); ?> alan</span>
								</a>

								<?php if ( $is_sortable ) : ?>
									<span class="nwcs-section__move">
										<button type="button" class="nwcs-move" data-nwcs-move="up" aria-label="Yukarı taşı" title="Yukarı taşı">↑</button>
										<button type="button" class="nwcs-move" data-nwcs-move="down" aria-label="Aşağı taşı" title="Aşağı taşı">↓</button>
									</span>
								<?php else : ?>
									<span class="nwcs-section__fixed" title="Bu bölümün yeri sabittir">sabit</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>

					<?php if ( $sortable ) : ?>
						<p class="nwcs-screen__hint nwcs-screen__hint--muted">
							Okları kullanarak bölümlerin sırasını değiştirebilirsiniz. Üst menü ve footer sabittir.
						</p>
					<?php endif; ?>
				</div>

				<!-- Ekran 2: duzenleyici -->
				<div class="nwcs-screen nwcs-screen--editor<?php echo $component_key ? ' is-active' : ''; ?>" data-nwcs-screen="editor">
					<div class="nwcs-editor" data-nwcs-editor>
						<?php
						if ( $component_key ) {
							nwcs_render_editor_form( $blog_id, $manifest, $page_key, $component_key );
						}
						?>
					</div>
				</div>
			</div>

			<!-- Orta: calisan onizleme -->
			<div class="nwcs-preview" data-nwcs-preview-wrap>
				<div class="nwcs-preview__bar">
					<span class="nwcs-preview__url"><?php echo esc_html( str_replace( array( 'http://', 'https://' ), '', $preview ) ); ?></span>
					<button type="button" class="button button-small" data-nwcs-reload>Yenile</button>
				</div>
				<div class="nwcs-preview__frame">
					<iframe
						data-nwcs-preview
						title="Site önizlemesi"
						src="<?php echo esc_url( add_query_arg( 'nwcs_preview', '1', $preview ) ); ?>"></iframe>
				</div>
			</div>

			<!-- Sag: dikey site secici -->
			<aside class="nwcs-sites" aria-label="Site seçici">
				<div class="nwcs-sites__inner">
					<h2 class="nwcs-sites__title">Site</h2>
					<?php foreach ( $sites as $id => $s ) : ?>
						<a class="nwcs-site<?php echo $id === $blog_id ? ' is-active' : ''; ?>"
							href="<?php echo esc_url( nwcs_panel_url( $id ) ); ?>">
							<span class="nwcs-site__name"><?php echo esc_html( $s['label'] ); ?></span>
							<span class="nwcs-site__path"><?php echo esc_html( $s['path'] ); ?></span>
						</a>
					<?php endforeach; ?>

					<p class="nwcs-sites__note">
						Yaptığınız değişiklik yalnızca seçili siteye kaydedilir; diğer site etkilenmez.
					</p>
				</div>
			</aside>
		</div>

		<div class="nwcs-toast" data-nwcs-toast hidden></div>
	</div>
	<?php
}

/**
 * Sekmelerde kisa sayfa adi.
 */
function nwcs_short_page_label( string $label ): string {
	$short = preg_replace( '/\s*\(.*\)$/u', '', $label );

	return $short ? $short : $label;
}

/**
 * Panel baglantisi uretir.
 */
function nwcs_panel_url( int $blog_id, string $page_key = '', string $component_key = '' ): string {
	$args = array(
		'page' => NWCS_MENU_SLUG,
		'site' => $blog_id,
	);

	if ( $page_key ) {
		$args['content_page'] = $page_key;
	}

	if ( $component_key ) {
		$args['component'] = $component_key;
	}

	return add_query_arg( $args, network_admin_url( 'admin.php' ) );
}

/**
 * Secili sayfanin sitedeki gercek adresi.
 */
function nwcs_preview_url( int $blog_id, array $manifest, string $page_key ): string {
	$path = $manifest['pages'][ $page_key ]['path'] ?? '/';

	return get_home_url( $blog_id, $path );
}

/**
 * Secili bilesenin duzenleme formu.
 *
 * JavaScript acikken AJAX ile bu HTML alinip yerine konur; kapaliyken sunucu
 * tarafinda ayni cikti basilir ve form admin-post.php'ye gider.
 */
function nwcs_render_editor_form( int $blog_id, array $manifest, string $page_key, string $component_key ): void {
	$component = $manifest['pages'][ $page_key ]['components'][ $component_key ] ?? null;

	if ( ! $component ) {
		echo '<p class="nwcs-empty">Bu bölüm bulunamadı.</p>';

		return;
	}

	$values = nwcs_get_all( $blog_id );
	$media  = nwcs_site_media( $blog_id );
	?>
	<?php // admin-post.php yalnizca wp-admin kokunde bulunur; ag dizininde yoktur. ?>
	<form class="nwcs-form" method="post" enctype="multipart/form-data"
		action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
		data-nwcs-form>

		<input type="hidden" name="action" value="nwcs_save" />
		<input type="hidden" name="site" value="<?php echo esc_attr( (string) $blog_id ); ?>" />
		<input type="hidden" name="content_page" value="<?php echo esc_attr( $page_key ); ?>" />
		<input type="hidden" name="component" value="<?php echo esc_attr( $component_key ); ?>" />
		<?php wp_nonce_field( 'nwcs_save_' . $blog_id . '_' . $page_key . '_' . $component_key ); ?>

		<header class="nwcs-form__head">
			<a class="nwcs-back" href="<?php echo esc_url( nwcs_panel_url( $blog_id, $page_key ) ); ?>" data-nwcs-back>← Bölümler</a>
			<h2 class="nwcs-form__title"><?php echo esc_html( $component['label'] ); ?></h2>
		</header>

		<div class="nwcs-fields">
			<?php
			foreach ( $component['fields'] as $field_key => $definition ) {
				$value = $values[ $page_key ][ $component_key ][ $field_key ] ?? ( $definition['default'] ?? '' );
				nwcs_render_field( $field_key, $definition, $value, $media, $blog_id );
			}
			?>
		</div>

		<footer class="nwcs-actions">
			<button type="submit" class="button button-primary">Kaydet ve Yayınla</button>
			<button type="button" class="button" data-nwcs-discard>Vazgeç</button>
			<span class="nwcs-dirty" data-nwcs-dirty hidden>● kaydedilmedi</span>
		</footer>
		<p class="nwcs-actions__note">Kaydettiğiniz anda sitede yayınlanır; taslak tutulmaz.</p>
	</form>
	<?php
}

/**
 * Secili sitenin medya kitapligi (gorseller). Site baglamina gecip geri doner.
 */
function nwcs_site_media( int $blog_id ): array {
	switch_to_blog( $blog_id );

	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => 100,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$media = array();

	foreach ( $attachments as $attachment ) {
		$src = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );

		$media[ (int) $attachment->ID ] = array(
			'id'    => (int) $attachment->ID,
			'title' => $attachment->post_title ? $attachment->post_title : ( '#' . $attachment->ID ),
			'thumb' => $src ? $src[0] : '',
			'alt'   => (string) get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		);
	}

	restore_current_blog();

	return $media;
}
