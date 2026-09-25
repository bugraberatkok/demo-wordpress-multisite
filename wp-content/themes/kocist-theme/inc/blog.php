<?php
/**
 * Blog: ilk kurulum yazilari ve sablon yardimcilari.
 *
 * Yazilar firmanin mevcut blogundaki basliklar, tarihler, kategoriler ve
 * ozetlerdir. Tam metinler henuz yok; govdeye ozet ve "hazirlaniyor" notu
 * konur. Tema yazilari yalnizca bir kez olusturur (kocist_blog_seeded):
 * kullanici bir yaziyi silerse geri gelmez. Olusturulan her yazi
 * _kocist_seed meta anahtariyla isaretlenir.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ilk kurulum yazilari: tarih | kategori | adres adi | baslik | ozet.
 */
function kocist_blog_seed_posts(): array {
	return array(
		array( '2025-01-12 10:00:00', 'İhracat', 'ispm-15-belgeli-ahsap-sandik-ve-kafes-uretimi', 'ISPM 15 Belgeli Ahşap Sandık ve Kafes Üretimi – Çatalca ve Çevresi', 'Ahşap ambalajda uluslararası ısıl işlem standardının kapsamı, işaretleme ve sevkiyat etkileri.' ),
		array( '2025-02-03 10:00:00', 'Kontrplak', 'kontrplak-secim-rehberi', 'Kontrplak Seçim Rehberi', 'Kalınlık, sınıf ve tutkal tipine göre projenize uygun kontrplak seçimi.' ),
		array( '2025-02-18 10:00:00', 'OSB', 'osb-mi-kontrplak-mi', 'OSB mi, Kontrplak mı?', 'Kalıp ve iskele uygulamalarında OSB-3/4 ile kontrplak karşılaştırması.' ),
		array( '2025-03-05 10:00:00', 'Ahşap Palet', 'ispm-15-palet-uretiminde-dikkat-edilecekler', 'ISPM-15 Palet Üretiminde Dikkat Edilecekler', 'Fırınlama, nem kontrolü ve standarda uygun işaretleme ile sorunsuz ihracat.' ),
		array( '2025-03-22 10:00:00', 'Ahşap Sandık', 'ahsap-sandik-ile-guvenli-ambalaj-cozumleri', 'Ahşap Sandık ile Güvenli Ambalaj Çözümleri', 'Hassas ekipman ve ihracat sevkiyatlarında sandık tasarım ipuçları.' ),
		array( '2025-04-10 10:00:00', 'Lojistik', 'lojistikte-ahsap-ambalajin-rolu', 'Lojistikte Ahşap Ambalajın Rolü', 'Tedarik zincirinde palet ve sandık standardizasyonunun operasyonel etkileri.' ),
		array( '2025-04-20 10:00:00', 'Ahşap Palet', 'ahsap-palet-fiyatlari-ve-ispm-15-uygulamalari', 'Ahşap Palet Fiyatları ve ISPM-15 Uygulamaları', 'Ahşap palet, palet fiyatları ve ISPM 15 ısıl işlem (HT) süreçleri; ihracat uyumu ve maliyet planı.' ),
		array( '2025-04-20 11:00:00', 'Ahşap Sandık', 'ahsap-sandik-fiyatlari-ve-ambalaj-sandigi-secimi', 'Ahşap Sandık Fiyatları ve Ambalaj Sandığı Seçimi', 'Ahşap sandık ve ambalaj sandığı fiyatlarını belirleyen etmenler; ISPM 15 uyumu ve koruma çözümleri.' ),
		array( '2025-04-21 10:00:00', 'Kereste', 'kereste-fiyatlari-ve-cesitleri', 'Kereste Fiyatları ve Çeşitleri', 'Kereste fiyatları ve kereste çeşitleri: inşaatlık, doğramalık, mobilyalık ve ısıl işlem kereste.' ),
		array( '2025-04-22 10:00:00', 'Kontrplak', 'kontrplak-ve-osb-plaka-secim-kilavuzu', 'Kontrplak ve OSB Plaka Seçim Kılavuzu', 'Kontrplak ile OSB plaka karşılaştırması: WBP tutkal, sınıf ve kalınlık seçimi; uygulama rehberi.' ),
		array( '2025-04-25 10:00:00', 'Ahşap Dekorasyon', 'ahsap-dekorasyon-kamelya-salincak-ve-kopek-kulubesi', 'Ahşap Dekorasyon: Kamelya, Salıncak ve Köpek Kulübesi', 'Ahşap kamelya, ahşap salıncak ve ahşap köpek kulübesi seçiminde malzeme, ölçü ve bakım ipuçları.' ),
		array( '2025-12-09 10:00:00', 'Kereste', 'tomruk-fiyatlari-olculer-siniflar-ve-kalite', 'Tomruk Fiyatları: Ölçüler, Sınıflar ve Kalite', 'Tomruk fiyatları; çap sınıfı, boy, tür (çam, ladin) ve kaliteye göre belirlenir.' ),
		array( '2026-01-20 10:00:00', 'Ahşap Ambalaj', 'ahsap-sandik-fiyatlari-2026', 'Ahşap Sandık Fiyatları 2026 – ISPM 15 Belgeli Özel Üretim Çözümleri', '2026 yılında ahşap sandık fiyatları; ölçü, ahşap türü, ISPM 15 belgesi ve kullanım amacına göre değişkenlik göstermektedir. Bu yazıda Koçist Orman Ürünleri olarak fiyatları etkileyen tüm faktörleri ve doğru ahşap sandık seçimini detaylıca ele alıyoruz.' ),
		array( '2026-01-20 11:00:00', 'Ahşap Dekorasyon', 'ahsap-cardak-modelleri-ve-fiyatlari', 'Ahşap Çardak Modelleri ve Fiyatları – Bahçeniz İçin Doğal Çözümler', 'Ahşap çardak modelleri; bahçe, villa, site ve sosyal alanlarda hem estetik hem de fonksiyonel çözümler sunar. Bu yazıda ahşap çardak nedir, hangi modeller tercih edilir, fiyatları neler etkiler ve Çatalca çevresinde neden ahşap çardak daha avantajlıdır detaylıca ele alıyoruz.' ),
		array( '2026-01-26 10:00:00', 'Ahşap Sandık', 'ihracat-sandigi-fiyatlari-2026-istanbul', 'İhracat Sandığı Fiyatları 2026 İstanbul – ISPM 15 Belgeli Özel Üretim', 'İhracat sandığı fiyatları 2026 yılında; üretim maliyetleri, ihracat standartları ve taşıma gereksinimlerine bağlı olarak değişiklik göstermektedir.' ),
	);
}

/**
 * Yazilari bir kez olusturur. Bayrak once yazilir: ayni anda gelen iki
 * istek yazilari iki kez eklemesin. Adres adi zaten varsa (cop kutusu dahil)
 * o yazi atlanir.
 */
function kocist_seed_blog_posts(): void {
	if ( get_option( 'kocist_blog_seeded' ) ) {
		return;
	}

	update_option( 'kocist_blog_seeded', 1 );

	// WordPress'in kendi ornek yazisi listede gorunmesin.
	$hello = get_page_by_path( 'hello-world', OBJECT, 'post' ) ?: get_page_by_path( 'merhaba-dunya', OBJECT, 'post' );

	if ( $hello && 'publish' === $hello->post_status ) {
		wp_update_post( array( 'ID' => $hello->ID, 'post_status' => 'draft' ) );
	}

	foreach ( kocist_blog_seed_posts() as $row ) {
		list( $date, $category, $slug, $title, $excerpt ) = $row;

		$existing = get_posts(
			array(
				'name'             => $slug,
				'post_type'        => 'post',
				'post_status'      => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
				'numberposts'      => 1,
				'fields'           => 'ids',
				'suppress_filters' => true,
			)
		);

		if ( $existing ) {
			continue;
		}

		$term = term_exists( $category, 'category' );

		if ( ! $term ) {
			$term = wp_insert_term( $category, 'category' );
		}

		$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;

		$post_id = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_name'     => $slug,
				'post_title'    => $title,
				'post_excerpt'  => $excerpt,
				'post_content'  => '<p>' . esc_html( $excerpt ) . "</p>\n\n<p class=\"k-post__pending\">Bu yazının tam metni hazırlanıyor.</p>",
				'post_date'     => $date,
				'post_category' => $term_id ? array( $term_id ) : array(),
			),
			true
		);

		if ( ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_kocist_seed', $slug );
		}
	}
}

/**
 * Yazilar sayfasinin adresi. Okuma ayari yoksa /blog/ yolu.
 */
function kocist_blog_url(): string {
	$page = (int) get_option( 'page_for_posts' );

	return $page ? (string) get_permalink( $page ) : home_url( '/blog/' );
}

/**
 * Yazinin ilk kategorisi (Kategorisiz haric).
 */
function kocist_post_category( ?int $post_id = null ): ?WP_Term {
	foreach ( get_the_category( $post_id ?? get_the_ID() ) as $term ) {
		if ( 'uncategorized' !== $term->slug && (int) get_option( 'default_category' ) !== (int) $term->term_id ) {
			return $term;
		}
	}

	return null;
}

/**
 * Yazinin kapak gorseli; yoksa kategorisine gore temadaki fotograf.
 */
function kocist_post_image( ?int $post_id = null, string $size = 'large' ): array {
	$post_id = $post_id ?? get_the_ID();
	$thumb   = (int) get_post_thumbnail_id( $post_id );

	if ( $thumb ) {
		$src = wp_get_attachment_image_src( $thumb, $size );

		if ( $src ) {
			return array(
				'id'  => $thumb,
				'url' => $src[0],
				'alt' => (string) get_post_meta( $thumb, '_wp_attachment_image_alt', true ),
			);
		}
	}

	$term  = kocist_post_category( $post_id );
	$key   = sanitize_title( ( $term ? $term->name : '' ) . ' ' . get_the_title( $post_id ) );
	$rules = array(
		'kamelya'    => 'ahsap-kamelya-3x3-zeminli.webp',
		'dekorasyon' => 's2-dekorasyon.jpg',
		'kontrplak'  => 'playwood.webp',
		'osb'        => 'playwood.webp',
		'kereste'    => 's2-kereste.jpg',
		'tomruk'     => 's2-kereste.jpg',
		'palet'      => 's2-ambalaj.jpg',
		'sandik'     => 's2-ambalaj.jpg',
		'ambalaj'    => 's2-ambalaj.jpg',
		'ihracat'    => 's2-ambalaj.jpg',
		'lojistik'   => 's2-ambalaj.jpg',
	);
	$file  = 'atolye.jpg';

	foreach ( $rules as $needle => $candidate ) {
		if ( str_contains( $key, $needle ) ) {
			$file = $candidate;
			break;
		}
	}

	return kocist_image_or_default( array(), $file, '' );
}

/**
 * Yazinin tarihi Turkce ay adiyla (12 Ocak 2025).
 *
 * Site dili Turkce olmayabilir (ag dil ayari tema isi degil); ay adlari
 * WordPress ceviri dosyasina baglanmadan buradan basilir.
 */
function kocist_post_date( ?int $post_id = null ): string {
	$months = array( 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık' );
	$time   = (int) get_post_time( 'U', false, $post_id ?? get_the_ID() );

	return gmdate( 'j', $time ) . ' ' . $months[ (int) gmdate( 'n', $time ) - 1 ] . ' ' . gmdate( 'Y', $time );
}

/**
 * Blog karti (liste, ana sayfa, ilgili yazilar ayni karti kullanir).
 */
function kocist_post_card( string $heading = 'h2' ): void {
	$term  = kocist_post_category();
	$image = kocist_post_image( null, 'medium_large' );
	$tag   = in_array( $heading, array( 'h2', 'h3' ), true ) ? $heading : 'h2';
	?>
	<article class="k-post-card">
		<a class="k-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true" <?php kocist_post_attr( (int) get_the_ID(), 'Öne çıkan görsel' ); ?>>
			<?php echo kocist_image_tag( $image, '', '' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</a>
		<div class="k-post-card__body">
			<p class="k-post-card__meta">
				<?php if ( $term ) : ?>
					<span class="k-post-card__cat" <?php kocist_term_attr( $term ); ?>><?php echo esc_html( $term->name ); ?></span>
				<?php endif; ?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" <?php kocist_post_attr( (int) get_the_ID(), 'Yayın tarihi' ); ?>><?php echo esc_html( kocist_post_date() ); ?></time>
			</p>
			<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapingOutput -- sabit liste. ?> class="k-post-card__title" <?php kocist_post_attr( (int) get_the_ID(), 'Yazı başlığı' ); ?>>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapingOutput ?>>
			<p class="k-post-card__excerpt" <?php kocist_post_attr( (int) get_the_ID(), 'Yazı özeti' ); ?>><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26, '…' ) ); ?></p>
		</div>
	</article>
	<?php
}
