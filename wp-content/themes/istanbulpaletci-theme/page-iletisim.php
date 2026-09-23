<?php
/**
 * Iletisim: solda calisan teklif formu, sagda iletisim bilgileri ve yol tarifi.
 * Bos birakilan bilgi satirlari (orn. calisma saatleri) hic basilmaz.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$rows = array(
	array( 'key' => 'phone', 'icon' => 'phone', 'link' => true, 'external' => false ),
	array( 'key' => 'mobile', 'icon' => 'phone', 'link' => true, 'external' => false ),
	array( 'key' => 'whatsapp', 'icon' => 'whatsapp', 'link' => true, 'external' => true ),
	array( 'key' => 'email', 'icon' => 'mail', 'link' => true, 'external' => false ),
	array( 'key' => 'address', 'icon' => 'pin', 'link' => false, 'external' => false ),
	array( 'key' => 'hours', 'icon' => 'clock', 'link' => false, 'external' => false ),
);

$map = trim( (string) nwcs_field( 'contact', 'details', 'map_url' ) );
?>
<article>

	<?php get_template_part( 'template-parts/page-head', null, array( 'page' => 'contact' ) ); ?>

	<div class="mx-auto grid max-w-[80rem] items-start gap-8 px-5 pt-12 md:px-8 md:pt-16 lg:grid-cols-[1.2fr_0.8fr] lg:gap-10">

		<section id="teklif" class="sheet scroll-mt-24 p-6 md:p-9">
			<?php get_template_part( 'template-parts/quote-form', null, array( 'heading' => 'h2' ) ); ?>
		</section>

		<section class="sheet lg:sticky lg:top-28" aria-labelledby="iletisim-bilgileri">
			<h2 id="iletisim-bilgileri" class="sr-only">İletişim bilgileri</h2>

			<ul class="divide-y divide-line">
				<?php foreach ( $rows as $row ) :
					// Baglantili satirlarda deger "<anahtar>_label" alaninda, digerlerinde
					// dogrudan anahtarin kendisinde durur.
					$value_field = $row['link'] ? $row['key'] . '_label' : $row['key'];
					$value       = trim( (string) nwcs_field( 'contact', 'details', $value_field ) );

					if ( '' === $value ) {
						continue;
					}
					?>
					<li class="flex gap-4 px-6 py-5 md:px-7">
						<span class="mt-0.5 shrink-0 <?php echo 'whatsapp' === $row['icon'] ? 'text-whatsapp' : 'text-indigo'; ?>" aria-hidden="true"><?php nwcs_the_icon( $row['icon'], '', 22 ); ?></span>

						<div class="min-w-0">
							<h3 class="text-[0.8125rem] font-medium text-steel" <?php nwcs_edit_attr( 'contact', 'details', $row['key'] . '_title' ); ?>>
								<?php echo esc_html( nwcs_field( 'contact', 'details', $row['key'] . '_title' ) ); ?>
							</h3>

							<?php if ( $row['link'] ) : ?>
								<a href="<?php echo esc_url( ip_link( nwcs_field( 'contact', 'details', $row['key'] . '_url' ) ) ); ?>"
									<?php echo $row['external'] ? 'target="_blank" rel="noopener"' : ''; ?>
									class="tabular mt-0.5 block break-words text-lg font-semibold text-ink no-underline hover:text-indigo"
									<?php nwcs_edit_attr( 'contact', 'details', $value_field ); ?>>
									<?php echo esc_html( $value ); ?>
								</a>
							<?php else : ?>
								<p class="mt-0.5 leading-relaxed" <?php nwcs_edit_attr( 'contact', 'details', $value_field ); ?>>
									<?php echo ip_multiline( $value ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
								</p>

								<?php if ( 'address' === $row['key'] && $map ) : ?>
									<a href="<?php echo esc_url( $map ); ?>" target="_blank" rel="noopener"
										class="btn btn--sm btn--outline mt-4" <?php nwcs_edit_attr( 'contact', 'details', 'map_label' ); ?>>
										<?php nwcs_the_icon( 'pin', 'shrink-0', 16 ); ?>
										<?php echo esc_html( nwcs_field( 'contact', 'details', 'map_label' ) ); ?>
									</a>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	</div>
</article>
<?php
get_footer();
