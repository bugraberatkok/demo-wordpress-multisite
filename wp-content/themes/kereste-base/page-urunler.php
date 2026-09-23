<?php
/**
 * Urunler: butun kereste cesitleri, olcu/tur ve ilk teknik bilgilerle yan
 * yana; alici urun sayfasina girmeden karsilastirabilsin.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$detail = (string) nwcs_field( 'products', 'shared', 'detail_label' );
$quote  = (string) nwcs_field( 'products', 'shared', 'quote_label' );
?>
<article>
	<?php get_template_part( 'template-parts/page-head', null, array( 'page' => 'products' ) ); ?>

	<ul class="mx-auto mt-12 grid max-w-[78rem] gap-6 px-5 md:mt-16 md:px-8">
		<?php foreach ( kr_products() as $product ) :
			$specs = array_slice( nwcs_rows( $product['key'], 'specs', 'rows' ), 0, 3 );
			?>
			<li class="grid border-[1.5px] border-ink/15 md:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]" <?php nwcs_edit_attr( $product['key'], 'card' ); ?>>
				<a href="<?php echo esc_url( $product['url'] ); ?>" class="shot block aspect-[16/10] md:aspect-auto" tabindex="-1" aria-hidden="true">
					<?php echo kr_image_tag( $product['image'], '', $product['name'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				</a>

				<div class="flex flex-col p-6 md:p-8">
					<span class="stencil text-[1.35rem]"><?php echo esc_html( $product['mark'] ); ?></span>
					<h2 class="mt-2 text-[1.9rem] md:text-[2.25rem]">
						<a href="<?php echo esc_url( $product['url'] ); ?>" class="text-ink no-underline hover:text-mark"><?php echo esc_html( $product['name'] ); ?></a>
					</h2>
					<p class="mt-2 max-w-[34rem] text-lg text-muted"><?php echo esc_html( $product['short'] ); ?></p>

					<?php if ( $specs ) : ?>
						<dl class="mt-5 border-t-2 border-ink">
							<?php foreach ( $specs as $row ) : ?>
								<div class="spec__row">
									<dt class="spec__key"><?php echo esc_html( $row['label'] ?? '' ); ?></dt>
									<dd class="spec__value"><?php echo esc_html( $row['value'] ?? '' ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>

					<div class="mt-auto flex flex-wrap gap-3 pt-6">
						<a href="<?php echo esc_url( $product['url'] ); ?>" class="btn btn--md btn--ink"><?php echo esc_html( $detail ); ?><span class="sr-only">: <?php echo esc_html( $product['name'] ); ?></span></a>
						<a href="<?php echo esc_url( kr_quote_fallback_url( $product['name'] ) ); ?>" data-kr-quote data-kr-product-name="<?php echo esc_attr( $product['name'] ); ?>" class="btn btn--md btn--outline"><?php echo esc_html( $quote ); ?><span class="sr-only">: <?php echo esc_html( $product['name'] ); ?></span></a>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</article>
<?php
get_footer();
