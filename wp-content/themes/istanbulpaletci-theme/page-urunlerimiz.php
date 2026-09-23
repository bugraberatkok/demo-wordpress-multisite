<?php
/**
 * Urunlerimiz: bes urunun yan yana karsilastirilabildigi liste.
 *
 * Her satirda gorsel, kisa aciklama ve sartnamenin ilk uc satiri var; alici
 * urun sayfasina girmeden olcu ve kullanim alanini gorebilsin.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$detail_label = (string) nwcs_field( 'products', 'shared', 'detail_label' );
?>
<article>

	<?php get_template_part( 'template-parts/page-head', null, array( 'page' => 'products' ) ); ?>

	<ul class="mx-auto mt-12 grid max-w-[80rem] gap-6 px-5 md:mt-16 md:px-8">
		<?php foreach ( ip_products() as $product ) :
			$specs = array_slice( nwcs_rows( $product['key'], 'specs', 'rows' ), 0, 3 );
			?>
			<li class="sheet grid md:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]" <?php nwcs_edit_attr( $product['key'], 'card' ); ?>>

				<a href="<?php echo esc_url( $product['url'] ); ?>" class="shot block aspect-[4/3] border-b border-line md:aspect-auto md:border-b-0 md:border-r"
					tabindex="-1" aria-hidden="true">
					<?php echo ip_image_tag( $product['image'], '', $product['name'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
				</a>

				<div class="flex flex-col p-6 md:px-9 md:py-8">
					<h2 class="text-[2rem] font-semibold md:text-[2.5rem]">
						<a href="<?php echo esc_url( $product['url'] ); ?>" class="text-ink no-underline hover:text-indigo">
							<?php echo esc_html( $product['name'] ); ?>
						</a>
					</h2>

					<p class="mt-2 max-w-[34rem] text-lg leading-relaxed text-steel"><?php echo esc_html( $product['short'] ); ?></p>

					<?php // Sartnamenin ilk uc satiri, hero'daki foy anteti gibi yan yana. ?>
					<?php if ( $specs ) : ?>
						<dl class="mt-6 grid border-t-2 border-signal sm:grid-cols-3">
							<?php foreach ( $specs as $row ) : ?>
								<div class="border-b border-line py-3 sm:border-b-0 sm:border-l sm:px-4 sm:first:border-l-0 sm:first:pl-0">
									<dt class="text-[0.8125rem] text-steel"><?php echo esc_html( $row['label'] ?? '' ); ?></dt>
									<dd class="tabular mt-0.5 font-semibold [text-wrap:balance]"><?php echo esc_html( $row['value'] ?? '' ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>

					<div class="mt-auto flex flex-wrap gap-3 pt-7">
						<a href="<?php echo esc_url( $product['url'] ); ?>" class="btn btn--md btn--solid">
							<?php echo esc_html( $detail_label ); ?>
							<span class="sr-only">: <?php echo esc_html( $product['name'] ); ?></span>
						</a>
						<a href="<?php echo esc_url( ip_quote_url( $product['name'] ) ); ?>" class="btn btn--md btn--outline">
							<?php echo esc_html( nwcs_field( 'products', 'shared', 'quote_label' ) ); ?>
							<span class="sr-only">: <?php echo esc_html( $product['name'] ); ?></span>
						</a>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php get_template_part( 'template-parts/cta-band', null, array( 'page' => 'products', 'component' => 'custom' ) ); ?>
</article>
<?php
get_footer();
