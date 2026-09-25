<?php
/**
 * Ana sayfa (woodkocist.com.tr'nin sirasiyla): giris, seri kutulari, populer
 * kategoriler, kategori satirlari (Urun Havuzu'ndan), tanitim bandi, siparis
 * adimlari, marka bolumu. Metinler panelden (home.*).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$tree   = wk_category_tree();
$rows   = nwcs_rows( 'home', 'rows', 'items' );
$steps  = nwcs_rows( 'home', 'steps', 'items' );
$trust  = nwcs_rows( 'home', 'about', 'items' );
$title  = preg_split( '/\R/u', trim( (string) nwcs_field( 'home', 'hero', 'title' ) ) ) ?: array();
$custom = wk_existing_pages( array( 'ozel-uretim-talep-formu' => '' ) );

/** Kategori adindan (havuzdaki) kategori bilgisi. */
$by_name = static function ( string $name ) use ( $tree ): ?array {
	foreach ( $tree as $line ) {
		if ( $line['label'] === $name ) {
			return $line;
		}
		foreach ( $line['children'] as $child ) {
			if ( $child['label'] === $name ) {
				return $child;
			}
		}
	}

	return null;
};

$banner_cat = $by_name( trim( (string) nwcs_field( 'home', 'banner', 'category' ) ) );
?>

<section class="wk-hero wk-hero--home">
	<div class="wk-wrap">
		<h1 class="wk-hero__title wk-hero__title--home" <?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>>
			<?php foreach ( $title as $i => $line ) : ?>
				<span><?php echo esc_html( $line ); ?></span>
			<?php endforeach; ?>
		</h1>
		<p class="wk-hero__lead" <?php nwcs_edit_attr( 'home', 'hero', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'lead' ) ); ?></p>
		<div class="wk-hero__actions">
			<a class="wk-btn wk-btn--primary wk-btn--lg" href="<?php echo esc_url( wk_shop_url() ); ?>" <?php nwcs_edit_attr( 'home', 'hero', 'cta' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'cta' ) ); ?></a>
			<?php if ( $custom ) : ?>
				<a class="wk-btn wk-btn--ghost wk-btn--lg" href="<?php echo esc_url( $custom[0]['url'] ); ?>" <?php nwcs_edit_attr( 'home', 'hero', 'cta_alt' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'cta_alt' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( $tree ) : ?>
	<section class="wk-series" aria-label="Seriler" <?php nwcs_edit_attr( 'home', 'catalog', 'lines' ); ?>>
		<div class="wk-wrap wk-series__grid">
			<?php foreach ( $tree as $line ) : ?>
				<a class="wk-serie" href="<?php echo esc_url( $line['url'] ); ?>" <?php nwcs_edit_attr( 'home', 'catalog', 'lines', $line['row'], 'label' ); ?>>
					<span class="wk-serie__mark" aria-hidden="true"><?php echo esc_html( mb_substr( $line['label'], 0, 1 ) ); ?></span>
					<span class="wk-serie__body">
						<span class="wk-serie__name"><?php echo esc_html( $line['label'] ); ?> <span <?php nwcs_edit_attr( 'home', 'sections', 'serie_suffix' ); ?>><?php echo esc_html( nwcs_field( 'home', 'sections', 'serie_suffix' ) ); ?></span></span>
						<span class="wk-serie__text" <?php nwcs_edit_attr( 'home', 'catalog', 'lines', $line['row'], 'text' ); ?>><?php echo esc_html( $line['text'] ); ?></span>
						<span class="wk-serie__more" <?php nwcs_edit_attr( 'home', 'sections', 'serie_more' ); ?>><?php echo esc_html( wk_text( 'home', 'sections', 'serie_more', array( 'sayi' => $line['count'] ) ) ); ?> <?php echo wk_icon( 'next' ); // phpcs:ignore WordPress.Security.EscapingOutput ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="wk-popular" aria-labelledby="wk-popular-title">
		<div class="wk-wrap">
			<div class="wk-sechead">
				<h2 id="wk-popular-title" class="wk-sechead__title" <?php nwcs_edit_attr( 'home', 'sections', 'cats_title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'sections', 'cats_title' ) ); ?></h2>
				<a class="wk-sechead__link" href="<?php echo esc_url( wk_shop_url() ); ?>" <?php nwcs_edit_attr( 'home', 'sections', 'cats_all' ); ?>><?php echo esc_html( nwcs_field( 'home', 'sections', 'cats_all' ) ); ?></a>
			</div>
			<ul class="wk-cats">
				<?php foreach ( $tree as $line ) : ?>
					<?php foreach ( $line['children'] as $child ) : ?>
						<?php $child_page = wk_category_page_key( $child['slug'] ); ?>
						<li>
							<a class="wk-cat" href="<?php echo esc_url( $child['url'] ); ?>">
								<span class="wk-cat__name" <?php $child_page && nwcs_edit_attr( $child_page, 'head', 'name' ); ?>><?php echo esc_html( $child['label'] ); ?></span>
								<span class="wk-cat__meta" <?php nwcs_edit_attr( 'home', 'catalog', 'lines', $line['row'], 'label' ); ?>><?php echo esc_html( $line['label'] ); ?></span>
								<span class="wk-cat__count wk-num" <?php nwcs_edit_attr( 'global', 'card', 'count' ); ?>><?php echo esc_html( wk_text( 'global', 'card', 'count', array( 'sayi' => $child['count'] ) ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php foreach ( $rows as $index => $row ) : ?>
	<?php
	$category = $by_name( trim( (string) ( $row['category'] ?? '' ) ) );

	if ( ! $category ) {
		continue;
	}

	$items = wk_filter_products( $category['slug'] );

	if ( ! $items ) {
		continue;
	}

	$row_id   = 'wk-row-' . (int) $index;
	$row_page = wk_category_page_key( $category['slug'] );
	// Satirdaki karta tiklayinca o kategorinin urun listesi acilir (panel onizlemesi).
	$row_edit = $row_page ? array( $row_page, 'products', 'pool' ) : array();
	?>
	<section class="wk-rowsec<?php echo 1 === $index % 2 ? ' wk-rowsec--tint' : ''; ?>" aria-labelledby="<?php echo esc_attr( $row_id ); ?>" <?php nwcs_edit_attr( 'home', 'rows', 'items' ); ?>>
		<div class="wk-wrap" data-rail>
			<div class="wk-sechead">
				<h2 id="<?php echo esc_attr( $row_id ); ?>" class="wk-sechead__title" <?php nwcs_edit_attr( 'home', 'rows', 'items', (int) $index, 'title' ); ?>><?php echo esc_html( $row['title'] ?? $category['label'] ); ?></h2>
				<div class="wk-sechead__tools">
					<a class="wk-sechead__link" href="<?php echo esc_url( $category['url'] ); ?>" <?php nwcs_edit_attr( 'home', 'sections', 'row_all' ); ?>><?php echo esc_html( wk_text( 'home', 'sections', 'row_all', array( 'sayi' => count( $items ) ) ) ); ?></a>
					<button type="button" class="wk-railbtn" data-rail-prev><?php echo wk_icon( 'prev' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><span class="wk-sr">Önceki ürünler</span></button>
					<button type="button" class="wk-railbtn" data-rail-next><?php echo wk_icon( 'next' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><span class="wk-sr">Sonraki ürünler</span></button>
				</div>
			</div>
			<div class="wk-rail" data-rail-track>
				<?php foreach ( array_slice( $items, 0, 10 ) as $product ) : ?>
					<?php wk_part( 'product-card', array( 'product' => $product, 'heading' => 'h3', 'edit' => $row_edit ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php if ( 1 === $index && $banner_cat ) : ?>
		<section class="wk-banner" aria-label="Tanıtım">
			<div class="wk-wrap wk-banner__row">
				<p class="wk-banner__text" <?php nwcs_edit_attr( 'home', 'banner', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'banner', 'title' ) ); ?></p>
				<a class="wk-btn wk-btn--light wk-btn--lg" href="<?php echo esc_url( $banner_cat['url'] ); ?>" <?php nwcs_edit_attr( 'home', 'banner', 'button' ); ?>><?php echo esc_html( nwcs_field( 'home', 'banner', 'button' ) ); ?></a>
			</div>
		</section>
	<?php endif; ?>
<?php endforeach; ?>

<?php if ( $steps ) : ?>
	<section id="siparis-adimlari" class="wk-steps" aria-labelledby="wk-steps-title">
		<div class="wk-wrap">
			<h2 id="wk-steps-title" class="wk-h2" <?php nwcs_edit_attr( 'home', 'steps', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'steps', 'title' ) ); ?></h2>
			<ol class="wk-steps__list" <?php nwcs_edit_attr( 'home', 'steps', 'items' ); ?>>
				<?php foreach ( $steps as $step_index => $step ) : ?>
					<li>
						<h3 <?php nwcs_edit_attr( 'home', 'steps', 'items', (int) $step_index, 'title' ); ?>><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
						<p <?php nwcs_edit_attr( 'home', 'steps', 'items', (int) $step_index, 'text' ); ?>><?php echo esc_html( $step['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
<?php endif; ?>

<section class="wk-brandsec" aria-labelledby="wk-brand-title">
	<div class="wk-wrap wk-brandsec__grid">
		<div>
			<h2 id="wk-brand-title" class="wk-h2" <?php nwcs_edit_attr( 'home', 'about', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'about', 'title' ) ); ?></h2>
			<?php if ( $trust ) : ?>
				<ul class="wk-ticks" <?php nwcs_edit_attr( 'home', 'about', 'items' ); ?>>
					<?php foreach ( $trust as $trust_index => $item ) : ?>
						<li <?php nwcs_edit_attr( 'home', 'about', 'items', (int) $trust_index, 'text' ); ?>><?php echo wk_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><?php echo esc_html( $item['text'] ?? '' ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<div>
			<p class="wk-brandsec__text" <?php nwcs_edit_attr( 'home', 'about', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'about', 'text' ) ); ?></p>
			<p class="wk-brandsec__links">
				<a href="<?php echo esc_url( wk_page_url( 'sirketimiz' ) ); ?>" <?php nwcs_edit_attr( 'home', 'sections', 'link_story' ); ?>><?php echo esc_html( nwcs_field( 'home', 'sections', 'link_story' ) ); ?></a>
				<a href="<?php echo esc_url( wk_page_url( 'sss' ) ); ?>" <?php nwcs_edit_attr( 'home', 'sections', 'link_faq' ); ?>><?php echo esc_html( nwcs_field( 'home', 'sections', 'link_faq' ) ); ?></a>
				<a href="<?php echo esc_url( wk_page_url( 'iletisim' ) ); ?>" <?php nwcs_edit_attr( 'home', 'sections', 'link_contact' ); ?>><?php echo esc_html( nwcs_field( 'home', 'sections', 'link_contact' ) ); ?></a>
			</p>
		</div>
	</div>
</section>

<?php
get_footer();
