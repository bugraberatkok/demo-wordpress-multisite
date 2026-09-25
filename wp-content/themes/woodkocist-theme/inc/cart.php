<?php
/**
 * Sepet. Hesap ve oturum gerekmez: sepet ziyaretcinin tarayicisinda tek bir
 * cerezde durur ("urunKimligi:adet|..."). Cerezde yalnizca kimlik ve adet
 * var; ad ve fiyat her zaman sunucuda Urun Havuzu'ndan okunur (ziyaretci
 * fiyati degistiremez; havuzda fiyat degisirse sepet yeni fiyati gosterir).
 *
 * JavaScript varsa site.js cerezi kendisi yazar ve sayfa yenilenmez; yoksa
 * formlar admin-post.php'ye gider, cerez burada yazilir. Iki yol ayni bicim.
 *
 * Sepete yalnizca fiyati olan urun girer; fiyatsiz urun "Fiyat sor" ile
 * WhatsApp'a / iletisim formuna yonlenir.
 */

defined( 'ABSPATH' ) || exit;

const WK_CART_COOKIE = 'wk_cart';
const WK_CART_MAX    = 30;
const WK_QTY_MAX     = 99;

/**
 * Cerezdeki ham sepet: [ urunKimligi => adet ].
 *
 * @return array<int, int>
 */
function wk_cart_raw(): array {
	$raw   = isset( $_COOKIE[ WK_CART_COOKIE ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ WK_CART_COOKIE ] ) ) : '';
	$items = array();

	foreach ( explode( '|', $raw ) as $pair ) {
		if ( ! preg_match( '/^(\d{1,10}):(\d{1,3})$/', $pair, $m ) ) {
			continue;
		}

		$qty = min( WK_QTY_MAX, (int) $m[2] );

		if ( $qty > 0 && count( $items ) < WK_CART_MAX ) {
			$items[ (int) $m[1] ] = $qty;
		}
	}

	return $items;
}

function wk_cart_save( array $items ): void {
	$pairs = array();

	foreach ( $items as $id => $qty ) {
		$qty = min( WK_QTY_MAX, (int) $qty );

		if ( (int) $id > 0 && $qty > 0 ) {
			$pairs[] = (int) $id . ':' . $qty;
		}
	}

	$value   = implode( '|', array_slice( $pairs, 0, WK_CART_MAX ) );
	$options = array(
		'expires'  => '' === $value ? time() - HOUR_IN_SECONDS : time() + 30 * DAY_IN_SECONDS,
		'path'     => '/', // site.js ile ayni yol; farkli olursa iki ayri cerez olusur.
		'secure'   => is_ssl(),
		'httponly' => false, // site.js de okuyup yazar.
		'samesite' => 'Lax',
	);

	setcookie( WK_CART_COOKIE, $value, $options );
	$_COOKIE[ WK_CART_COOKIE ] = $value;
}

/**
 * Sitedeki urunler kimlige gore.
 *
 * @return array<int, array<string, mixed>>
 */
function wk_products_by_id(): array {
	static $map = null;

	if ( null === $map ) {
		$map = array();

		foreach ( wk_products() as $product ) {
			$map[ (int) $product['id'] ] = $product;
		}
	}

	return $map;
}

function wk_can_buy( array $product ): bool {
	return ! empty( $product['has_price'] ) && wk_price_number( (string) $product['price'] ) > 0;
}

/**
 * Sepetin sunucuda hesaplanmis hali. Artik sitede olmayan ya da fiyati
 * kaldirilan urunler satirlardan cikar ve 'removed' listesinde doner.
 *
 * @return array{lines: array<int, array{product:array, qty:int, unit:float, total:float}>, count:int, subtotal:float, removed:array<int, string>}
 */
function wk_cart(): array {
	$products = wk_products_by_id();
	$lines    = array();
	$removed  = array();
	$count    = 0;
	$subtotal = 0.0;

	foreach ( wk_cart_raw() as $id => $qty ) {
		$product = $products[ $id ] ?? null;

		if ( ! $product || ! wk_can_buy( $product ) ) {
			if ( $product ) {
				$removed[] = $product['title'];
			}
			continue;
		}

		$unit      = wk_price_number( (string) $product['price'] );
		$lines[]   = array(
			'product' => $product,
			'qty'     => $qty,
			'unit'    => $unit,
			'total'   => $unit * $qty,
		);
		$count    += $qty;
		$subtotal += $unit * $qty;
	}

	return array(
		'lines'    => $lines,
		'count'    => $count,
		'subtotal' => $subtotal,
		'removed'  => $removed,
	);
}

/**
 * Ara toplam + kargo + KDV. Fiyatlarin KDV'yi icerip icermedigi panelden
 * (Magaza Ayarlari) gelir; "haric" ise KDV satiri eklenir.
 *
 * @return array{subtotal:float, shipping:float, vat:float, vat_rate:float, total:float, vat_included:bool}
 */
function wk_cart_totals( array $cart ): array {
	$rate     = max( 0.0, (float) str_replace( ',', '.', (string) nwcs_field( 'global', 'shop', 'vat_rate' ) ) );
	$included = 'haric' !== wk_vat_mode();
	$shipping = 0.0; // Kargo ucreti teslimatta/teyitte bildirilir (bkz. Magaza Ayarlari metni).
	$vat      = $included ? 0.0 : round( $cart['subtotal'] * $rate / 100, 2 );

	return array(
		'subtotal'     => $cart['subtotal'],
		'shipping'     => $shipping,
		'vat'          => $vat,
		'vat_rate'     => $rate,
		'total'        => $cart['subtotal'] + $shipping + $vat,
		'vat_included' => $included,
	);
}

function wk_vat_mode(): string {
	return 'haric' === trim( (string) nwcs_field( 'global', 'shop', 'vat_mode' ) ) ? 'haric' : 'dahil';
}

/** Fiyatin yaninda gorunen kisa not ("+ KDV" / "KDV dahil"). */
function wk_price_note(): string {
	return (string) nwcs_field( 'global', 'card', 'haric' === wk_vat_mode() ? 'vat_excluded' : 'vat_included' );
}

/** Onizlemede fiyat notunun panel alani. */
function wk_price_note_attr(): void {
	nwcs_edit_attr( 'global', 'card', 'haric' === wk_vat_mode() ? 'vat_excluded' : 'vat_included' );
}

/* ---------------------------------------------------------------------- */
/* JavaScript'siz yol: sepete ekle / guncelle / sil                        */
/* ---------------------------------------------------------------------- */

add_action( 'admin_post_wk_cart', 'wk_handle_cart' );
add_action( 'admin_post_nopriv_wk_cart', 'wk_handle_cart' );
function wk_handle_cart(): void {
	// Nonce yok (bilincli): sayfalar onbellekte beklerken nonce eskir; sepete
	// urun eklemek/cikarmak baskasi adina yapilsa da zarar vermez. Siparis
	// (odeme formu) nonce ile korunur.
	$cart_url = home_url( '/sepet/' );

	$items    = wk_cart_raw();
	$products = wk_products_by_id();
	$do       = sanitize_key( wp_unslash( $_POST['wk_do'] ?? 'add' ) );

	if ( 'add' === $do ) {
		$id  = absint( $_POST['wk_id'] ?? 0 );
		$qty = max( 1, min( WK_QTY_MAX, absint( $_POST['wk_qty'] ?? 1 ) ) );

		if ( isset( $products[ $id ] ) && wk_can_buy( $products[ $id ] ) ) {
			$items[ $id ] = min( WK_QTY_MAX, ( $items[ $id ] ?? 0 ) + $qty );
		}
	} elseif ( 'update' === $do ) {
		foreach ( (array) ( $_POST['wk_qty'] ?? array() ) as $id => $qty ) {
			$id = absint( $id );

			if ( isset( $items[ $id ] ) ) {
				$items[ $id ] = min( WK_QTY_MAX, absint( $qty ) );
			}
		}
	} elseif ( 'remove' === $do ) {
		unset( $items[ absint( $_POST['wk_id'] ?? 0 ) ] );
	}

	// Sepet dugmesi "Sil" ile gonderildiyse (guncelleme formu icinde) o satir silinir.
	if ( isset( $_POST['wk_remove'] ) ) {
		unset( $items[ absint( $_POST['wk_remove'] ) ] );
	}

	wk_cart_save( $items );
	wp_safe_redirect( $cart_url );
	exit;
}

/**
 * Sepet ve odeme sayfalari onbellege alinmaz (cerezle degisir).
 */
add_action( 'template_redirect', 'wk_cart_nocache', 1 );
function wk_cart_nocache(): void {
	if ( is_page( array( 'sepet', 'odeme' ) ) ) {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		nocache_headers();
		header( 'X-LiteSpeed-Cache-Control: no-cache' );
	}
}

/* Sepet ve odeme site haritasinda yok ve arama motorlarina kapali. */
add_filter(
	'wp_sitemaps_posts_query_args',
	static function ( array $args, string $post_type ): array {
		if ( 'page' === $post_type ) {
			foreach ( array( 'sepet', 'odeme' ) as $slug ) {
				$page = get_page_by_path( $slug );

				if ( $page ) {
					$args['post__not_in'][] = $page->ID;
				}
			}
		}

		return $args;
	},
	10,
	2
);

add_filter(
	'wp_robots',
	static function ( array $robots ): array {
		if ( is_page( array( 'sepet', 'odeme' ) ) ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}

		return $robots;
	}
);

/**
 * Sepete ekle formu (kart ve urun sayfasi). JavaScript bu formu yakalar.
 */
function wk_add_to_cart_form( array $product, bool $with_qty = false, string $class = '' ): void {
	?>
	<form class="wk-add<?php echo $class ? ' ' . esc_attr( $class ) : ''; ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
		data-add
		data-id="<?php echo esc_attr( (string) $product['id'] ); ?>"
		data-title="<?php echo esc_attr( $product['title'] ); ?>"
		data-code="<?php echo esc_attr( $product['code'] ); ?>"
		data-price="<?php echo esc_attr( $product['price'] ); ?>">
		<input type="hidden" name="action" value="wk_cart" />
		<input type="hidden" name="wk_do" value="add" />
		<input type="hidden" name="wk_id" value="<?php echo esc_attr( (string) $product['id'] ); ?>" />
		<?php if ( $with_qty ) : ?>
			<div class="wk-qty" data-qty>
				<button type="button" class="wk-qty__btn" data-step="-1" aria-label="Adedi azalt">−</button>
				<label class="wk-sr" for="wk-qty-<?php echo esc_attr( (string) $product['id'] ); ?>">Adet</label>
				<input id="wk-qty-<?php echo esc_attr( (string) $product['id'] ); ?>" class="wk-qty__input" type="number" name="wk_qty" value="1" min="1" max="<?php echo (int) WK_QTY_MAX; ?>" inputmode="numeric" />
				<button type="button" class="wk-qty__btn" data-step="1" aria-label="Adedi artır">+</button>
			</div>
		<?php else : ?>
			<input type="hidden" name="wk_qty" value="1" />
		<?php endif; ?>
		<button type="submit" class="wk-btn wk-btn--primary<?php echo $with_qty ? ' wk-btn--lg' : ''; ?>" <?php nwcs_edit_attr( 'global', 'card', 'add_button' ); ?>>
			<?php echo wk_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			<?php echo esc_html( nwcs_field( 'global', 'card', 'add_button' ) ); ?>
		</button>
	</form>
	<?php
}
