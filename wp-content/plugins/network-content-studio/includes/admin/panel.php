<?php
/**
 * Ag yonetimindeki tek giris noktasi: Icerik Studyosu.
 *
 * Duzen: solda sayfa/bilesen listesi, ortada duzenleyici, sagda dikey site
 * secici. Tum yazma islemleri admin-post.php uzerinden save.php'ye gider.
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
	if ( 'toplevel_page_' . NWCS_MENU_SLUG . '-network' !== $hook && 'toplevel_page_' . NWCS_MENU_SLUG !== $hook ) {
		return;
	}

	wp_enqueue_style( 'nwcs-admin', NWCS_URL . 'assets/admin.css', array(), NWCS_VERSION );
	wp_enqueue_script( 'nwcs-admin', NWCS_URL . 'assets/admin.js', array(), NWCS_VERSION, true );
	wp_localize_script(
		'nwcs-admin',
		'nwcsL10n',
		array(
			'confirmDiscard' => 'Kaydedilmemiş değişiklikler var. Son kaydedilen hâle dönülsün mü?',
			'confirmLeave'   => 'Kaydedilmemiş değişiklikleriniz var.',
			'unsaved'        => 'Kaydedilmemiş değişiklik var',
			'confirmRow'     => 'Bu satır silinsin mi? Kaydettiğinizde kalıcı olur.',
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
		$page_key = (string) array_key_first( $pages );
	}

	$components    = $pages[ $page_key ]['components'];
	$component_key = isset( $_GET['component'] ) ? sanitize_key( wp_unslash( $_GET['component'] ) ) : '';
	if ( ! isset( $components[ $component_key ] ) ) {
		$component_key = (string) array_key_first( $components );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$component = $components[ $component_key ];
	$values    = nwcs_get_all( $blog_id );
	$preview   = nwcs_preview_url( $blog_id, $manifest, $page_key );

	?>
	<div class="wrap nwcs-wrap">
		<h1 class="nwcs-page-title">
			İçerik Stüdyosu
			<span class="nwcs-page-title__sub">tek panel, iki site</span>
		</h1>

		<?php nwcs_render_notices(); ?>

		<div class="nwcs-layout">
			<!-- Sol: sayfa ve bilesen listesi -->
			<nav class="nwcs-col nwcs-nav" aria-label="Sayfa ve bileşenler">
				<?php foreach ( $pages as $key => $page ) : ?>
					<?php $is_current_page = ( $key === $page_key ); ?>
					<div class="nwcs-nav__group<?php echo $is_current_page ? ' is-open' : ''; ?>">
						<a class="nwcs-nav__page<?php echo $is_current_page ? ' is-active' : ''; ?>"
							href="<?php echo esc_url( nwcs_panel_url( $blog_id, $key ) ); ?>">
							<?php echo esc_html( $page['label'] ); ?>
						</a>

						<?php if ( $is_current_page ) : ?>
							<ul class="nwcs-nav__components">
								<?php foreach ( $page['components'] as $c_key => $c ) : ?>
									<li>
										<a class="<?php echo $c_key === $component_key ? 'is-active' : ''; ?>"
											href="<?php echo esc_url( nwcs_panel_url( $blog_id, $key, $c_key ) ); ?>">
											<?php echo esc_html( $c['label'] ); ?>
											<span class="nwcs-nav__count"><?php echo (int) count( $c['fields'] ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</nav>

			<!-- Orta: duzenleyici -->
			<main class="nwcs-col nwcs-editor">
				<?php
				nwcs_render_editor_form(
					array(
						'blog_id'       => $blog_id,
						'site'          => $site,
						'manifest'      => $manifest,
						'page_key'      => $page_key,
						'page'          => $pages[ $page_key ],
						'component_key' => $component_key,
						'component'     => $component,
						'values'        => $values,
						'preview'       => $preview,
					)
				);
				?>
			</main>

			<!-- Sag: dikey site secici -->
			<aside class="nwcs-col nwcs-sites" aria-label="Site seçici">
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
						Değişiklikler yalnızca seçili siteye yazılır; diğer site etkilenmez.
					</p>
				</div>
			</aside>
		</div>
	</div>
	<?php
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
 * Kaydetme sonrasi bildirimleri.
 */
function nwcs_render_notices(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- yalnizca bildirim gosterimi.
	if ( isset( $_GET['nwcs_saved'] ) ) {
		$count = absint( $_GET['nwcs_saved'] );
		$media = isset( $_GET['nwcs_media'] ) ? absint( $_GET['nwcs_media'] ) : 0;

		$message = sprintf( '%d alan kaydedildi ve sitede yayınlandı.', $count );

		if ( $media ) {
			$message .= sprintf( ' %d görsel güncellendi.', $media );
		}

		printf(
			'<div class="notice notice-success is-dismissible"><p><strong>Yayınlandı.</strong> %s</p></div>',
			esc_html( $message )
		);
	}

	if ( isset( $_GET['nwcs_error'] ) ) {
		printf(
			'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
			esc_html( sanitize_text_field( wp_unslash( $_GET['nwcs_error'] ) ) )
		);
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
}

/**
 * Secili bilesenin duzenleme formu.
 */
function nwcs_render_editor_form( array $args ): void {
	$blog_id       = $args['blog_id'];
	$page_key      = $args['page_key'];
	$component_key = $args['component_key'];
	$component     = $args['component'];
	$values        = $args['values'];
	$manifest      = $args['manifest'];

	$media = nwcs_site_media( $blog_id );
	?>
	<?php // admin-post.php yalnizca wp-admin kokunde bulunur; ag dizininde yoktur. ?>
	<form class="nwcs-form" method="post" enctype="multipart/form-data"
		action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

		<input type="hidden" name="action" value="nwcs_save" />
		<input type="hidden" name="site" value="<?php echo esc_attr( (string) $blog_id ); ?>" />
		<input type="hidden" name="content_page" value="<?php echo esc_attr( $page_key ); ?>" />
		<input type="hidden" name="component" value="<?php echo esc_attr( $component_key ); ?>" />
		<?php wp_nonce_field( 'nwcs_save_' . $blog_id . '_' . $page_key . '_' . $component_key ); ?>

		<header class="nwcs-editor__head">
			<div>
				<p class="nwcs-editor__crumb">
					<?php echo esc_html( $args['site']['label'] ); ?>
					<span aria-hidden="true">›</span>
					<?php echo esc_html( $args['page']['label'] ); ?>
				</p>
				<h2 class="nwcs-editor__title"><?php echo esc_html( $component['label'] ); ?></h2>
			</div>

			<a class="button" href="<?php echo esc_url( $args['preview'] ); ?>" target="_blank" rel="noopener">
				Sayfayı önizle ↗
			</a>
		</header>

		<p class="nwcs-publish-note">
			<strong>Kaydettiğiniz anda yayınlanır.</strong> Bu demoda taslak/revizyon akışı yoktur.
			Kaydetmeden önce <em>Vazgeç</em> ile son kaydedilen hâle dönebilirsiniz.
		</p>

		<div class="nwcs-fields">
			<?php
			foreach ( $component['fields'] as $field_key => $definition ) {
				$value = $values[ $page_key ][ $component_key ][ $field_key ] ?? ( $definition['default'] ?? '' );
				nwcs_render_field( $field_key, $definition, $value, $media, $blog_id );
			}
			?>
		</div>

		<footer class="nwcs-actions">
			<button type="submit" class="button button-primary button-hero">Kaydet ve Yayınla</button>
			<button type="button" class="button button-hero" data-nwcs-discard>Vazgeç</button>
			<span class="nwcs-dirty" data-nwcs-dirty hidden>● Kaydedilmemiş değişiklik var</span>
		</footer>
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
