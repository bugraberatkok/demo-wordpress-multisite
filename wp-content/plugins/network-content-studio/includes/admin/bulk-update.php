<?php
/**
 * Ag yonetimi -> Icerik Studyosu -> Toplu Guncelleme.
 *
 * Firma bilgisinin sitelerde tek tip olmasi icin kurala dayali guncelleme:
 * tek adres (Kestanelik / Catalca) ve firma yasi (50 yil). Koçist sitesi
 * kapsam disi.
 *
 * Adres, yil ve baglanti kurallari sabit bir "eski deger" aramaz; her alanin
 * o anki degerine bakar (canli veritabani yereldekinden farkli olabilir).
 * Istisna: bulk-content.php'deki icerik duzeltmeleri (kopya metin) eski
 * metnin birebir aynisini arar; elle degistirilmis alana dokunmaz. Sayfa once yapilacak
 * degisiklikleri listeler, "Uygula" ile yazar. Uygulandiktan sonra kurallar
 * hicbir alanda degisiklik bulmaz; tekrar basmak bir sey yapmaz. Her
 * uygulama ag seceneginde kayit olarak tutulur.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_BULK_SLUG   = 'nwcs-bulk-update';
const NWCS_BULK_LOG    = 'nwcs_bulk_update_log';
const NWCS_BULK_STREET = 'Kestanelik Mahallesi Eski Edirne Asfaltı Cad. 2125/1';
const NWCS_BULK_CITY   = 'Çatalca, İstanbul';

/**
 * Kapsamdaki siteler tema adiyla secilir (site numarasi kurulumdan kuruluma
 * degisebilir).
 */
const NWCS_BULK_THEMES = array(
	'ahsapkasa-theme',
	'istanbulpaletci-theme',
	'ithalkeresteci-theme',
	'kavakkeresteci-theme',
	'istanbul-keresteci-theme',
	'sanayi-palet-theme',
	'ahsapambalaj-theme',
	'istanbulpaletcivi-theme',
);

add_action( 'network_admin_menu', 'nwcs_register_bulk_menu', 30 );
function nwcs_register_bulk_menu(): void {
	add_submenu_page(
		NWCS_MENU_SLUG,
		'Toplu Güncelleme',
		'Toplu Güncelleme',
		NWCS_CAPABILITY,
		NWCS_BULK_SLUG,
		'nwcs_render_bulk_update'
	);
}

/**
 * Tek adres: iki satirlik alanlarda cadde / ilce-il, tek satirlikta virgulle.
 */
function nwcs_bulk_address( bool $multiline ): string {
	return NWCS_BULK_STREET . ( $multiline ? "\n" : ', ' ) . NWCS_BULK_CITY;
}

/**
 * Bir alanin yeni degeri. Degismeyecekse ayni deger doner.
 *
 * @param mixed $value
 * @return mixed
 */
function nwcs_bulk_transform( string $page, string $component, string $field, $value ) {
	$is_address = is_string( $value ) && preg_match( '/Şahintepe|Kestanelik|%C5%9Eahintepe/u', $value );

	// Adres alanlari tamamen yazilir (bicimler de tek tip olsun).
	if ( $is_address && 'address' === $field ) {
		return nwcs_bulk_address( str_contains( $value, "\n" ) );
	}

	if ( $is_address && 'map_url' === $field ) {
		return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( nwcs_bulk_address( false ) );
	}

	if ( $is_address && 'query' === $field ) {
		return nwcs_bulk_address( false );
	}

	if ( NWCS_SEO_SITE_PAGE === $page && 'org' === $component ) {
		if ( 'street' === $field && $is_address ) {
			return NWCS_BULK_STREET;
		}

		if ( 'district' === $field && is_string( $value ) && '' !== $value ) {
			return 'Çatalca';
		}

		// 34494 Catalca'nin posta kodu degil; dogrusu bilinmeden yazilmaz.
		if ( 'postal_code' === $field && '34494' === $value ) {
			return '';
		}
	}

	return nwcs_bulk_prose( $value );
}

/**
 * Metin icindeki yil ve semt ifadeleri; tekrarli satirlarda her metin.
 *
 * @param mixed $value
 * @return mixed
 */
function nwcs_bulk_prose( $value ) {
	if ( is_array( $value ) ) {
		return array_map( 'nwcs_bulk_prose', $value );
	}

	if ( ! is_string( $value ) || '' === $value ) {
		return $value;
	}

	// Kurulus yili bilinmiyor: 50 yil ile celisen "1980'ler" ifadeleri cikar.
	$value = (string) preg_replace( '/1980[\'’]lerden bu yana o/u', 'O', $value );
	$value = (string) preg_replace( '/1980[\'’]li yıllardan beri faaliyet gösteren /u', '', $value );

	$value = (string) preg_replace( '/\b(?:37|40|45)(\s*yıl)/u', '50$1', $value );
	$value = (string) preg_replace( '/Kırk(\s+yıl)/u', 'Elli$1', $value );
	$value = (string) preg_replace( '/kırk(\s+yıl)/u', 'elli$1', $value );

	// Semt adresle ayni: Başakşehir'de / İkitelli'deki / ...'den -> Çatalca.
	$value = (string) preg_replace_callback(
		'/(?:İkitelli|Başakşehir)([\'’])(deki|de|den)\b/u',
		static fn( array $m ): string => 'Çatalca' . $m[1] . strtr( $m[2], array( 'e' => 'a' ) ),
		$value
	);

	// Eksiz kullanim: "İkitelli atölyesinde", "İstanbul Başakşehir".
	return (string) preg_replace( '/(?<!\p{L})(?:İkitelli|Başakşehir)(?![\p{L}\'’])/u', 'Çatalca', $value );
}

/**
 * Kendi alan adina baglanmis sitelerin eski panel adresleri: sitenin yeni
 * adresine. Kardes site baglantilari veritabaninda panel adresiyle kalmisti
 * (siteler tek tek alan adina baglandi); panel yolu artik 404 verir.
 *
 * @return array<string, string> eski adres on eki => yeni adres
 */
function nwcs_bulk_link_map(): array {
	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map     = array();
	$network = get_network();

	foreach ( get_sites( array( 'number' => 100 ) ) as $site ) {
		// Hala panel alan adinda duran site (ana site, Kocist...) atlanir.
		if ( $site->domain === $network->domain ) {
			continue;
		}

		$slug = nwcs_bulk_slug_for( $site );

		if ( '' === $slug ) {
			continue;
		}

		$new = untrailingslashit( get_home_url( (int) $site->blog_id, '/' ) );

		foreach ( array( 'http', 'https' ) as $scheme ) {
			$map[ $scheme . '://' . $network->domain . '/' . $slug ] = $new;
		}
	}

	$map = (array) apply_filters( 'nwcs_bulk_link_map', $map );

	// Uzun on ek once: /ahsapkasa ile /ahsapkasa-x karismasin.
	uksort( $map, static fn( string $a, string $b ): int => strlen( $b ) <=> strlen( $a ) );

	return $map;
}

/**
 * Alan adina baglanmis sitenin paneldeki eski yolu: temanin site anahtari
 * (manifest site_key) panel yolu olarak kullanildi (/ahsapkasa/, /sanayi-palet/).
 */
function nwcs_bulk_slug_for( WP_Site $site ): string {
	return sanitize_key( (string) ( nwcs_manifest_for_blog( (int) $site->blog_id )['site_key'] ?? '' ) );
}

/**
 * @param mixed $value
 * @return mixed
 */
function nwcs_bulk_links( $value ) {
	if ( is_array( $value ) ) {
		return array_map( 'nwcs_bulk_links', $value );
	}

	if ( ! is_string( $value ) || ! str_contains( $value, '://' ) ) {
		return $value;
	}

	foreach ( nwcs_bulk_link_map() as $old => $new ) {
		// Yalnizca tam yol: /ahsapkasa ya da /ahsapkasa/... ; /ahsapkasax degil.
		$replaced = preg_replace( '~' . preg_quote( $old, '~' ) . '(?=/|$|["\'\s?#<])~', $new, $value );

		// Ifade hata verirse deger oldugu gibi kalir; bos degerle ezilmez.
		if ( is_string( $replaced ) ) {
			$value = $replaced;
		}
	}

	return $value;
}

/**
 * Sitenin icerik duzeltmeleri (bulk-content.php): deger ya da tekrarli satir
 * hucresi eski metnin birebir aynisiysa yenisi. Kismi eslesme yok.
 *
 * @param mixed                 $value
 * @param array<string, string> $revisions eski => yeni
 * @return mixed
 */
function nwcs_bulk_revise( $value, array $revisions ) {
	if ( ! $revisions ) {
		return $value;
	}

	if ( is_array( $value ) ) {
		return array_map( static fn( $item ) => nwcs_bulk_revise( $item, $revisions ), $value );
	}

	if ( ! is_string( $value ) ) {
		return $value;
	}

	// Bastaki/sondaki bosluk ve Windows satir sonu farki eslesmeyi bozmasin.
	$key = trim( str_replace( "\r\n", "\n", $value ) );

	return $revisions[ $key ] ?? $value;
}

/**
 * Degisen metin ciftleri (tekrarli satirlarda yalnizca degisen hucreler).
 *
 * @param mixed $old
 * @param mixed $new
 * @return array<int, array{0:string, 1:string}>
 */
function nwcs_bulk_pairs( $old, $new ): array {
	if ( is_array( $old ) && is_array( $new ) ) {
		$pairs = array();

		foreach ( $new as $key => $item ) {
			array_push( $pairs, ...nwcs_bulk_pairs( $old[ $key ] ?? '', $item ) );
		}

		return $pairs;
	}

	return $old === $new ? array() : array( array( (string) $old, (string) $new ) );
}

/**
 * Yapilacak degisiklikler.
 *
 * @return array<int, array<string, mixed>>
 */
function nwcs_bulk_plan(): array {
	$plan = array();

	foreach ( get_sites( array( 'number' => 100 ) ) as $site ) {
		$blog_id = (int) $site->blog_id;

		if ( ! in_array( get_blog_option( $blog_id, 'stylesheet', '' ), NWCS_BULK_THEMES, true ) ) {
			continue;
		}

		switch_to_blog( $blog_id );
		$manifest  = nwcs_manifest();
		$stored    = nwcs_get_all();
		$revisions = nwcs_bulk_revisions()[ (string) get_option( 'stylesheet' ) ] ?? array();

		foreach ( $manifest['pages'] ?? array() as $page_key => $page ) {
			foreach ( $page['components'] as $component_key => $component ) {
				foreach ( $component['fields'] as $field_key => $definition ) {
					$saved = $stored[ $page_key ][ $component_key ][ $field_key ] ?? null;
					$value = ( null !== $saved && '' !== $saved ) ? $saved : ( $definition['default'] ?? '' );
					$new   = nwcs_bulk_links( nwcs_bulk_revise( nwcs_bulk_transform( $page_key, $component_key, $field_key, $value ), $revisions ) );

					if ( $new === $value ) {
						continue;
					}

					$plan[] = array(
						'blog_id'    => $blog_id,
						'site'       => (string) ( $manifest['site_label'] ?? get_option( 'blogname' ) ),
						'page'       => $page_key,
						'component'  => $component_key,
						'field'      => $field_key,
						'label'      => ( $page['label'] ?? $page_key ) . ' › ' . $component['label'] . ' › ' . ( $definition['label'] ?? $field_key ),
						'definition' => $definition,
						'old'        => $value,
						'new'        => $new,
					);
				}
			}
		}

		// Gorsellerin alt metinleri (arama motorlari ve ekran okuyucular okur).
		foreach ( get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => -1, 'fields' => 'ids' ) ) as $attachment_id ) {
			$alt = (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			$new = nwcs_bulk_prose( $alt );

			if ( $new !== $alt ) {
				$plan[] = array(
					'blog_id'       => $blog_id,
					'site'          => (string) ( $manifest['site_label'] ?? get_option( 'blogname' ) ),
					'attachment_id' => $attachment_id,
					'label'         => 'Görsel alt metni › ' . get_the_title( $attachment_id ),
					'old'           => $alt,
					'new'           => $new,
				);
			}
		}

		restore_current_blog();
	}

	return $plan;
}

add_action( 'admin_post_nwcs_bulk_apply', 'nwcs_bulk_apply' );
function nwcs_bulk_apply(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) || ! check_admin_referer( 'nwcs_bulk_apply' ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ) );
	}

	$count = nwcs_bulk_run();

	wp_safe_redirect( add_query_arg( array( 'page' => NWCS_BULK_SLUG, 'uygulandi' => $count ), network_admin_url( 'admin.php' ) ) );
	exit;
}

/**
 * Plani yazar ve kaydeder; yazilan alan sayisini dondurur.
 */
function nwcs_bulk_run(): int {
	$plan    = nwcs_bulk_plan();
	$written = array();

	foreach ( $plan as $change ) {
		switch_to_blog( $change['blog_id'] );

		if ( isset( $change['attachment_id'] ) ) {
			update_post_meta( $change['attachment_id'], '_wp_attachment_image_alt', sanitize_text_field( $change['new'] ) );
		} else {
			$data = nwcs_get_all();
			$data[ $change['page'] ][ $change['component'] ][ $change['field'] ] = nwcs_sanitize_value( $change['new'], $change['definition'] );
			update_option( NWCS_OPTION_CONTENT, $data );
		}

		restore_current_blog();

		$written[] = array_intersect_key( $change, array_flip( array( 'blog_id', 'site', 'label', 'old', 'new' ) ) );
	}

	$log   = (array) get_site_option( NWCS_BULK_LOG, array() );
	$log[] = array(
		'time'    => time(),
		'user'    => wp_get_current_user()->user_login,
		'changes' => $written,
	);
	update_site_option( NWCS_BULK_LOG, $log );

	return count( $written );
}

function nwcs_render_bulk_update(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu sayfaya erişim yetkiniz yok.' ) );
	}

	$plan    = nwcs_bulk_plan();
	$log     = (array) get_site_option( NWCS_BULK_LOG, array() );
	$last    = $log ? end( $log ) : null;
	$applied = isset( $_GET['uygulandi'] ) ? absint( $_GET['uygulandi'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- yalnizca bilgi.
	$by_site = array();

	foreach ( $plan as $change ) {
		$by_site[ $change['site'] ][] = $change;
	}
	?>
	<div class="wrap nwcs-wrap nwcs-wrap--pool nwcs-seo">
		<header class="nwcs-bar">
			<div class="nwcs-bar__brand">
				<button type="button" class="nwcs-bar__mark" data-nwcs-menu
					aria-label="Yönetim menüsünü aç/kapat" title="Yönetim menüsünü aç/kapat"></button>
				<h1>Toplu Güncelleme</h1>
			</div>
		</header>

		<section class="nwcs-pool__card nwcs-bulk">
			<h2 class="nwcs-pool__title">Tek adres ve firma yaşı</h2>
			<p class="nwcs-seo__lead">
				Koçist dışındaki sitelerde adres <strong><?php echo esc_html( nwcs_bulk_address( false ) ); ?></strong> olur;
				alt bilgi, iletişim sayfası, harita ve arama motorlarına verilen firma bilgisi dahil. 34494 posta kodu kaldırılır
				(Çatalca'nın kodu değil). Firma yaşı her yerde 50 yıl olur; Sanayi Palet'teki "1980'lerden bu yana" ifadeleri çıkar.
				Metinlerde geçen Başakşehir ve İkitelli de Çatalca olur. Kendi alan adına bağlanmış sitelere giden eski panel
				bağlantıları (kardeş site bağlantıları) sitenin alan adına çevrilir.
				Kardeş sitelerde birebir aynı olan metinler (Ahşap Ambalaj ile Ahşap Kasa; İthal ve Kavak Keresteci'nin soruları)
				her sitenin kendi odağına göre yeniden yazılır; elle değiştirilmiş alanlara dokunulmaz.
			</p>

			<?php if ( null !== $applied ) : ?>
				<p class="nwcs-badge nwcs-badge--ok"><?php echo esc_html( sprintf( '%d alan güncellendi.', $applied ) ); ?></p>
			<?php endif; ?>

			<?php if ( ! $plan ) : ?>
				<p class="nwcs-badge nwcs-badge--ok">Bekleyen değişiklik yok: bütün alanlar güncel.</p>
			<?php else : ?>
				<p><?php echo esc_html( sprintf( '%d sitede %d alan değişecek. Listeyi kontrol edip "Uygula"ya basın.', count( $by_site ), count( $plan ) ) ); ?></p>

				<?php foreach ( $by_site as $site => $changes ) : ?>
					<h3><?php echo esc_html( $site ); ?> <span class="description">(<?php echo esc_html( (string) count( $changes ) ); ?> alan)</span></h3>
					<table class="widefat striped nwcs-bulk__table">
						<thead><tr><th style="width:22%">Alan</th><th>Şimdiki</th><th>Yeni</th></tr></thead>
						<tbody>
							<?php foreach ( $changes as $change ) : ?>
								<?php foreach ( nwcs_bulk_pairs( $change['old'], $change['new'] ) as $pair ) : ?>
									<tr>
										<td><?php echo esc_html( $change['label'] ); ?></td>
										<td><?php echo nl2br( esc_html( '' === $pair[0] ? '(boş)' : $pair[0] ) ); ?></td>
										<td><?php echo nl2br( esc_html( '' === $pair[1] ? '(boş)' : $pair[1] ) ); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endforeach; ?>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1.5rem">
					<input type="hidden" name="action" value="nwcs_bulk_apply" />
					<?php wp_nonce_field( 'nwcs_bulk_apply' ); ?>
					<button type="submit" class="button button-primary button-hero">Uygula</button>
				</form>
			<?php endif; ?>

			<?php if ( $last ) : ?>
				<p class="description" style="margin-top:1.5rem">
					<?php echo esc_html( sprintf( 'Son uygulama: %s, %s, %d alan.', wp_date( 'j F Y H:i', (int) $last['time'] ), $last['user'], count( $last['changes'] ) ) ); ?>
				</p>
			<?php endif; ?>
		</section>
	</div>
	<?php
}
