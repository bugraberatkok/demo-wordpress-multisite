<?php
/**
 * Ana sayfa: giris, seri secici + tur suzgeci + urun izgarasi (havuzdan),
 * siparis adimlari, siparis formu.
 *
 * Suzgec baglantidir (?seri=woodgarden, ?tur=adirondack): JavaScript yoksa
 * sayfa suzulmus haliyle gelir; varsa site.js sayfa yenilemeden suzer.
 * Suzulmus adreslerin canonical'i ana sayfadir (eklenti).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$products = wk_products();
$lines    = wk_lines();
$types    = wk_types();
$steps    = nwcs_rows( 'home', 'steps', 'items' );
$wa       = wk_whatsapp();

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- yalnizca gorunum suzgeci.
$line = isset( $_GET['seri'] ) ? sanitize_title( wp_unslash( $_GET['seri'] ) ) : '';
$type = isset( $_GET['tur'] ) ? sanitize_title( wp_unslash( $_GET['tur'] ) ) : '';
// phpcs:enable

$line_slugs = wp_list_pluck( $lines, 'slug' );
$line       = in_array( $line, $line_slugs, true ) ? $line : '';
$type       = isset( $types[ $type ] ) ? $type : '';
$active     = $type ?: $line;
$visible    = $active ? array_filter( $products, static fn( array $p ): bool => isset( $p['categories'][ $active ] ) ) : $products;
$base       = home_url( '/' );
?>

<section class="wk-hero">
	<div class="wk-wrap">
		<h1 class="wk-hero__title" <?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'title' ) ); ?></h1>
		<p class="wk-hero__lead" <?php nwcs_edit_attr( 'home', 'hero', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'lead' ) ); ?></p>
	</div>
</section>

<section id="urunler" class="wk-catalog" aria-labelledby="wk-catalog-title" data-catalog>
	<div class="wk-wrap">
		<h2 id="wk-catalog-title" class="wk-sr" <?php nwcs_edit_attr( 'home', 'catalog', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'catalog', 'title' ) ); ?></h2>

		<?php if ( $lines ) : ?>
			<nav class="wk-lines" aria-label="Seriler" <?php nwcs_edit_attr( 'home', 'catalog', 'lines' ); ?>>
				<a href="<?php echo esc_url( $base . '#urunler' ); ?>" class="wk-line" data-filter="" <?php echo '' === $active ? 'aria-current="true"' : ''; ?>>
					<span class="wk-line__name"><?php echo esc_html( nwcs_field( 'home', 'catalog', 'all_label' ) ); ?></span>
					<span class="wk-line__meta"><?php echo esc_html( sprintf( '%d ürün', count( $products ) ) ); ?></span>
				</a>
				<?php foreach ( $lines as $item ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'seri', $item['slug'], $base ) . '#urunler' ); ?>" class="wk-line" data-filter="<?php echo esc_attr( $item['slug'] ); ?>"
						<?php echo $item['slug'] === $line && '' === $type ? 'aria-current="true"' : ''; ?>>
						<span class="wk-line__name"><?php echo esc_html( $item['label'] ); ?></span>
						<?php if ( '' !== $item['text'] ) : ?>
							<span class="wk-line__text"><?php echo esc_html( $item['text'] ); ?></span>
						<?php endif; ?>
						<span class="wk-line__meta"><?php echo esc_html( sprintf( '%d ürün', $item['count'] ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( $types ) : ?>
			<nav class="wk-types" aria-label="Ürün türleri">
				<?php foreach ( $types as $slug => $item ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'tur', $slug, $base ) . '#urunler' ); ?>" class="wk-chip" data-filter="<?php echo esc_attr( $slug ); ?>"
						<?php echo $slug === $type ? 'aria-current="true"' : ''; ?>>
						<?php echo esc_html( $item['label'] ); ?> <span class="wk-num"><?php echo (int) $item['count']; ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<p class="wk-count" aria-live="polite" data-count><?php echo esc_html( sprintf( '%d ürün gösteriliyor', count( $visible ) ) ); ?></p>

		<?php if ( $products ) : ?>
			<div class="wk-grid" <?php nwcs_edit_attr( 'home', 'catalog', 'pool' ); ?>>
				<?php foreach ( $products as $product ) : ?>
					<?php
					// Suzgec disindaki kartlar gizli basilir: JavaScript suzgeci degistirince
					// sayfa yenilenmeden gorunur.
					$hidden = $active && ! isset( $product['categories'][ $active ] );
					ob_start();
					wk_part( 'product-card', array( 'product' => $product ) );
					$card = (string) ob_get_clean();
					echo $hidden ? str_replace( '<article class="wk-card"', '<article class="wk-card" hidden', $card ) : $card; // phpcs:ignore WordPress.Security.EscapingOutput -- sablon ciktisi.
					?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<p class="wk-empty" data-empty <?php echo $visible ? 'hidden' : ''; ?> <?php nwcs_edit_attr( 'home', 'catalog', 'empty' ); ?>>
			<?php echo esc_html( nwcs_field( 'home', 'catalog', 'empty' ) ); ?>
		</p>
	</div>
</section>

<?php if ( $steps ) : ?>
	<section id="siparis-adimlari" class="wk-steps" aria-labelledby="wk-steps-title">
		<div class="wk-wrap">
			<h2 id="wk-steps-title" class="wk-h2" <?php nwcs_edit_attr( 'home', 'steps', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'steps', 'title' ) ); ?></h2>
			<ol class="wk-steps__list" <?php nwcs_edit_attr( 'home', 'steps', 'items' ); ?>>
				<?php foreach ( $steps as $step ) : ?>
					<li>
						<h3><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
						<p><?php echo esc_html( $step['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
<?php endif; ?>

<section class="wk-order" aria-labelledby="wk-order-title">
	<div class="wk-wrap wk-order__grid">
		<div>
			<h2 id="wk-order-title" class="wk-h2" <?php nwcs_edit_attr( 'home', 'contact', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'contact', 'title' ) ); ?></h2>
			<p class="wk-lead" <?php nwcs_edit_attr( 'home', 'contact', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'home', 'contact', 'lead' ) ); ?></p>
			<?php if ( $wa ) : ?>
				<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="wk-btn wk-btn--wa wk-btn--lg">
					<?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					WhatsApp’tan yazın
				</a>
			<?php endif; ?>
		</div>
		<?php wk_part( 'order-form' ); ?>
	</div>
</section>

<?php
get_footer();
