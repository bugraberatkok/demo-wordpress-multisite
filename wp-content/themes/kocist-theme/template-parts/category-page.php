<?php
/**
 * Kategori sayfalari (inc/catalog.php):
 *   /kategoriler/                    tum gruplar, her birinin alt kategorileri
 *   /kategoriler/<grup>/             grubun urunleri
 *   /kategoriler/<grup>/<kategori>/  tek kategorinin urunleri
 *
 * Grup ve kategori sayfalarinda solda grubun kategori listesi, sagda urunler.
 * Gorunum assets/css/category.css.
 *
 * @var array $args { group: ?array, sub: ?array }
 */

defined( 'ABSPATH' ) || exit;

$group  = $args['group'] ?? null;
$sub    = $args['sub'] ?? null;
$groups = kocist_catalog_groups();
$counts = kocist_catalog_counts();

$quote_url = kocist_link( '#teklif' );

get_header();

if ( ! $group ) :
	$sub_total = array_sum( array_map( static fn( array $item ): int => count( $item['subs'] ), $groups ) );
	?>
	<div class="k-pagehead">
		<div class="k-wrap">
			<?php kocist_the_trail( kocist_catalog_trail(), 'k-crumbs--on-dark' ); ?>
			<h1 class="k-pagehead__title">Ürün kategorileri</h1>
			<p class="k-pagehead__sub">
				<?php
				echo esc_html(
					sprintf(
						'%d ürün grubunda %d kategori. Aradığınızı listede görmüyorsanız ölçüsünü yazın, fiyatlandıralım.',
						count( $groups ),
						$sub_total
					)
				);
				?>
			</p>
		</div>
	</div>

	<section class="k-section">
		<div class="k-wrap">
			<div class="k-shelf">
				<?php foreach ( $groups as $item ) : ?>
					<?php $item_count = (int) ( $counts[ $item['slug'] ][''] ?? 0 ); ?>
					<article class="k-shelf__row">
						<a class="k-shelf__media" href="<?php echo esc_url( $item['url'] ); ?>" tabindex="-1" aria-hidden="true">
							<?php echo kocist_image_tag( $item['image'], 'k-shelf__img', $item['name'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
						</a>

						<div class="k-shelf__body">
							<h2 class="k-shelf__title">
								<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
							</h2>
							<p class="k-shelf__meta">
								<?php
								echo esc_html(
									$item_count
										? sprintf( '%d kategori, %d ürün', count( $item['subs'] ), $item_count )
										: sprintf( '%d kategori', count( $item['subs'] ) )
								);
								?>
							</p>

							<ul class="k-shelf__subs">
								<?php foreach ( $item['subs'] as $sub_item ) : ?>
									<li><a href="<?php echo esc_url( $sub_item['url'] ); ?>"><?php echo esc_html( $sub_item['name'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>

							<a class="k-shelf__all" href="<?php echo esc_url( $item['url'] ); ?>">
								Tüm <?php echo esc_html( $item['name'] ); ?> ürünleri
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
									<path d="M3 8h9.5M8.5 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
else :
	$products = kocist_catalog_products_in( $group['slug'], $sub['slug'] ?? '' );
	$title    = $sub['name'] ?? $group['name'];
	?>
	<div class="k-pagehead">
		<div class="k-wrap">
			<?php kocist_the_trail( kocist_catalog_trail( $group['slug'], $sub['slug'] ?? '' ), 'k-crumbs--on-dark' ); ?>
			<h1 class="k-pagehead__title"><?php echo esc_html( $title ); ?></h1>
			<p class="k-pagehead__sub">
				<?php
				if ( $products ) {
					echo esc_html( sprintf( '%d ürün listeleniyor. Fiyatlar ölçü ve adede göre değişir; aynı gün teklif veriyoruz.', count( $products ) ) );
				} elseif ( $sub ) {
					echo esc_html( sprintf( '%s grubunda, ölçü ve adede göre üretim ve tedarik.', $group['name'] ) );
				} else {
					echo esc_html( sprintf( '%d kategoride ölçü ve adede göre üretim ve tedarik.', count( $group['subs'] ) ) );
				}
				?>
			</p>
		</div>
	</div>

	<?php
	/*
	 * Kategori listesi solda, urunler sagda. Grubun tum kategorileri tek
	 * sutunda: 24 kategorili Hirdavat'ta bile satir satir okunur. Bos
	 * kategoriler soluk; secili olanin solunda yesil cizgi. Dar ekranda liste
	 * "Kategori: ..." basligiyla acilir kapanir (details).
	 */
	$group_total = (int) ( $counts[ $group['slug'] ][''] ?? 0 );
	$current_row = $sub ? $sub['name'] : 'Tümü';
	?>
	<section class="k-section k-section--tight">
		<div class="k-wrap k-browse">
			<aside class="k-browse__side">
				<details class="k-cats" data-k-cats open>
					<summary class="k-cats__summary">
						<span class="k-cats__summary-label">Kategori</span>
						<span class="k-cats__summary-value"><?php echo esc_html( $current_row ); ?></span>
						<svg class="k-cats__chevron" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></svg>
					</summary>

					<nav aria-label="<?php echo esc_attr( $group['name'] . ' kategorileri' ); ?>">
						<p class="k-cats__group"><?php echo esc_html( $group['name'] ); ?></p>
						<ul class="k-cats__list">
							<li>
								<a class="k-cats__link<?php echo $sub ? '' : ' is-current'; ?>" href="<?php echo esc_url( $group['url'] ); ?>" <?php echo $sub ? '' : 'aria-current="page"'; ?>>
									<span>Tümü</span>
									<?php if ( $group_total ) : ?>
										<span class="k-cats__count"><?php echo (int) $group_total; ?></span>
									<?php endif; ?>
								</a>
							</li>
							<?php foreach ( $group['subs'] as $sub_item ) : ?>
								<?php
								$is_here   = $sub && $sub['slug'] === $sub_item['slug'];
								$sub_count = (int) ( $counts[ $group['slug'] ][ $sub_item['slug'] ] ?? 0 );
								?>
								<li>
									<a class="k-cats__link<?php echo $is_here ? ' is-current' : ''; ?><?php echo $sub_count ? '' : ' is-empty'; ?>" href="<?php echo esc_url( $sub_item['url'] ); ?>" <?php echo $is_here ? 'aria-current="page"' : ''; ?>>
										<span><?php echo esc_html( $sub_item['name'] ); ?></span>
										<?php if ( $sub_count ) : ?>
											<span class="k-cats__count"><?php echo (int) $sub_count; ?></span>
										<?php endif; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>

						<?php $others = array_filter( $groups, static fn( array $item ): bool => $item['slug'] !== $group['slug'] ); ?>
						<?php if ( $others ) : ?>
							<p class="k-cats__group k-cats__group--others">Diğer ürün grupları</p>
							<ul class="k-cats__list k-cats__list--others">
								<?php foreach ( $others as $other ) : ?>
									<li><a class="k-cats__link" href="<?php echo esc_url( $other['url'] ); ?>"><span><?php echo esc_html( $other['name'] ); ?></span></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</nav>
				</details>
				<script>
					// Dar ekranda liste kapali baslar; genis ekranda hep acik.
					( function () {
						var box = document.querySelector( '[data-k-cats]' );
						if ( box && window.matchMedia( '(max-width: 860px)' ).matches ) {
							box.open = false;
						}
					}() );
				</script>
			</aside>

			<div class="k-browse__main">
			<?php if ( $products ) : ?>
				<ul class="k-plist">
					<?php foreach ( $products as $product ) : ?>
						<?php
						$has_page = '' !== trim( (string) $product['body'] );
						$href     = $has_page ? $product['url'] : $quote_url;
						$sub_name = $group['subs'][ $product['sub'] ]['name'] ?? '';
						?>
						<li class="k-pcard">
							<a class="k-pcard__link" href="<?php echo esc_url( $href ); ?>">
								<span class="k-pcard__media">
									<?php echo kocist_image_tag( kocist_product_image( $product ), 'k-pcard__img', 'Örnek görsel' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
								</span>

								<span class="k-pcard__body">
									<?php if ( '' !== $sub_name && ! $sub ) : ?>
										<span class="k-pcard__kind"><?php echo esc_html( $sub_name ); ?></span>
									<?php endif; ?>

									<span class="k-pcard__title"><?php echo esc_html( $product['title'] ); ?></span>

									<?php if ( $product['short'] ) : ?>
										<span class="k-pcard__text"><?php echo esc_html( $product['short'] ); ?></span>
									<?php endif; ?>

									<span class="k-pcard__foot">
										<span class="k-pcard__price<?php echo $product['has_price'] ? '' : ' is-quote'; ?>"><?php echo esc_html( $product['has_price'] ? $product['price_label'] : 'Fiyat teklifle' ); ?></span>
										<span class="k-pcard__go"><?php echo esc_html( $has_page ? 'İncele' : 'Teklif iste' ); ?></span>
									</span>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<div class="k-empty">
					<h2 class="k-empty__title">
						<?php echo esc_html( sprintf( '%s için listelenmiş ürün yok', $title ) ); ?>
					</h2>
					<p class="k-empty__text">Bu kategorideki ürünleri siparişe göre hazırlıyoruz. Ölçü ve adedi yazın, aynı gün fiyat verelim.</p>
					<div class="k-empty__actions">
						<a class="k-product__btn k-product__btn--primary" href="<?php echo esc_url( $quote_url ); ?>">Teklif isteyin</a>
						<?php if ( $sub ) : ?>
							<a class="k-product__btn k-product__btn--ghost" href="<?php echo esc_url( $group['url'] ); ?>">Tüm <?php echo esc_html( $group['name'] ); ?> ürünleri</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
endif;

get_footer();
