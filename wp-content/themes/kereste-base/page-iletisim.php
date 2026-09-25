<?php
/**
 * Iletisim: solda teklif formu (sayfa icinde), sagda iletisim bilgileri.
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Satir basligi (panel alani), deger alani, baglanti alani.
$rows = array(
	array( 'phone_title', 'phone_label', 'phone_url' ),
	array( 'mobile_title', 'mobile_label', 'mobile_url' ),
	array( 'email_title', 'email', 'mailto' ),
	array( 'address_title', 'address', '' ),
	array( 'hours_title', 'hours', '' ),
);
$map      = trim( (string) nwcs_field( 'contact', 'details', 'map_url' ) );
$whatsapp = trim( (string) nwcs_field( 'global', 'header', 'whatsapp_url' ) );
?>
<article>
	<?php get_template_part( 'template-parts/page-head', null, array( 'page' => 'contact' ) ); ?>

	<div class="mx-auto grid max-w-[78rem] items-start gap-10 px-5 pt-12 md:px-8 md:pt-16 lg:grid-cols-[1.25fr_0.75fr] lg:gap-14">
		<section id="teklif" class="scroll-mt-24 border-t-4 border-mark bg-paper p-6 ring-1 ring-line sm:p-8">
			<?php get_template_part( 'template-parts/quote-form', null, array( 'prefix' => 'sayfa', 'heading' => 'h2' ) ); ?>
		</section>

		<section aria-labelledby="bilgiler" class="bg-stone p-6 sm:p-8">
			<h2 id="bilgiler" class="sr-only">İletişim bilgileri</h2>
			<dl class="grid gap-6">
				<?php foreach ( $rows as $row ) :
					$value = trim( (string) nwcs_field( 'contact', 'details', $row[1] ) );
					if ( '' === $value ) {
						continue;
					}
					$href = 'mailto' === $row[2] ? 'mailto:' . $value : ( $row[2] ? kr_link( nwcs_field( 'contact', 'details', $row[2] ) ) : '' );
					?>
					<div>
						<dt class="text-sm font-bold text-muted" <?php nwcs_edit_attr( 'contact', 'details', $row[0] ); ?>><?php echo esc_html( nwcs_field( 'contact', 'details', $row[0] ) ); ?></dt>
						<dd class="mt-1 text-lg" <?php nwcs_edit_attr( 'contact', 'details', $row[1] ); ?>>
							<?php if ( $href ) : ?>
								<a href="<?php echo esc_url( $href ); ?>" class="tabular font-semibold text-ink no-underline hover:text-mark"><?php echo esc_html( $value ); ?></a>
							<?php else : ?>
								<?php echo kr_multiline( $value ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
							<?php endif; ?>
						</dd>
					</div>
				<?php endforeach; ?>
			</dl>

			<?php if ( $whatsapp || $map ) : ?>
				<div class="mt-7 flex flex-wrap gap-3">
					<?php if ( $whatsapp ) : ?>
						<a href="<?php echo esc_url( kr_link( $whatsapp ) ); ?>" target="_blank" rel="noopener" class="btn btn--md btn--whatsapp"
							<?php nwcs_edit_attr( 'global', 'header', 'whatsapp_label' ); ?>>
							<?php nwcs_the_icon( 'whatsapp', 'shrink-0', 18 ); ?>
							<?php echo esc_html( nwcs_field( 'global', 'header', 'whatsapp_label' ) ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $map ) : ?>
						<a href="<?php echo esc_url( $map ); ?>" target="_blank" rel="noopener" class="btn btn--md btn--outline"
							<?php nwcs_edit_attr( 'contact', 'details', 'map_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'details', 'map_label' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</section>
	</div>
</article>
<?php
get_footer();
