<?php
/**
 * Hero: perdeli indigo zemin uzerinde beyaz bir sartname foyu.
 * Foy hafif bir golgeyle zeminden kalkar.
 *
 * Foyun cevresindeki sari olcu cizgileri sayfa acilisinda bir kez cizilir;
 * sitenin tek gosterisli ani budur. Altta bilgi seridi (40 yil, 5 urun...).
 */

defined( 'ABSPATH' ) || exit;

$sheet = nwcs_image( 'home', 'hero', 'sheet_image', 'large' );
$rows  = nwcs_rows( 'home', 'hero', 'sheet_rows' );
$facts = nwcs_rows( 'home', 'facts', 'items' );
?>
<section class="on-dark hero-field overflow-hidden text-sheet">
	<div class="mx-auto grid max-w-[80rem] items-center gap-14 px-5 pb-16 pt-14 md:px-8 md:pb-20 md:pt-20 lg:grid-cols-[1fr_1.05fr] lg:gap-16 lg:pb-24">

		<div>
			<h1 class="font-display text-[3rem] font-bold leading-[0.95] sm:text-[4rem] lg:text-[5.25rem]" <?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>>
				<?php echo ip_multiline( nwcs_field( 'home', 'hero', 'title' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</h1>

			<p class="mt-6 max-w-[32rem] text-lg leading-relaxed text-sheet/85" <?php nwcs_edit_attr( 'home', 'hero', 'lead' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'lead' ) ); ?>
			</p>

			<div class="mt-9 flex flex-wrap gap-3">
				<a href="<?php echo esc_url( ip_link( nwcs_field( 'home', 'hero', 'primary_url' ) ) ); ?>"
					class="btn btn--lg btn--light max-sm:w-full" <?php nwcs_edit_attr( 'home', 'hero', 'primary_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'home', 'hero', 'primary_label' ) ); ?>
				</a>
				<a href="<?php echo esc_url( ip_link( nwcs_field( 'home', 'hero', 'secondary_url' ) ) ); ?>"
					class="btn btn--lg btn--light-outline max-sm:w-full" <?php nwcs_edit_attr( 'home', 'hero', 'secondary_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'home', 'hero', 'secondary_label' ) ); ?>
				</a>
			</div>
		</div>

		<?php // Foy: gorsel + antet. Olcu cizgileri foyun disinda, sagda ve altta. ?>
		<figure class="pr-8 sm:pr-12">
			<div class="sheet sheet--raised text-ink">
				<div class="relative">
					<div class="shot aspect-[9/5]" <?php nwcs_edit_attr( 'home', 'hero', 'sheet_image' ); ?>>
						<?php echo ip_image_tag( $sheet, '', 'Ürün görseli', true ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</div>

					<div class="dim dim--v dim--draw absolute left-full top-0 ml-3 sm:ml-5" aria-hidden="true">
						<span class="dim__line"></span>
						<span class="dim__label" <?php nwcs_edit_attr( 'home', 'hero', 'dim_y' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'dim_y' ) ); ?></span>
					</div>
				</div>

				<?php if ( $rows ) : ?>
					<dl class="grid border-t border-line" style="grid-template-columns: repeat(<?php echo (int) count( $rows ); ?>, minmax(0, 1fr));"
						<?php nwcs_edit_attr( 'home', 'hero', 'sheet_rows' ); ?>>
						<?php foreach ( $rows as $row ) : ?>
							<div class="border-l border-line px-3 py-2.5 first:border-l-0 sm:px-5 sm:py-3">
								<dt class="text-[0.8125rem] text-steel"><?php echo esc_html( $row['label'] ?? '' ); ?></dt>
								<dd class="tabular mt-0.5 text-[0.9375rem] font-semibold [text-wrap:balance] sm:text-lg"><?php echo esc_html( $row['value'] ?? '' ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

			<div class="dim dim--draw mt-4 sm:mt-5" aria-hidden="true">
				<span class="dim__line"></span>
				<span class="dim__label" <?php nwcs_edit_attr( 'home', 'hero', 'dim_x' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'dim_x' ) ); ?></span>
			</div>
		</figure>
	</div>

	<?php if ( $facts ) : ?>
		<div class="border-t border-sheet/15">
			<dl class="mx-auto grid max-w-[80rem] grid-cols-2 px-5 md:grid-cols-4 md:px-8" <?php nwcs_edit_attr( 'home', 'facts', 'items' ); ?>>
				<?php foreach ( $facts as $index => $fact ) :
					// Mobilde 2x2, masaustunde tek satir; ayiricilar hucrelerin
					// kendi kenarligi.
					$cell = array( 'border-sheet/15', 'py-6', 'md:py-8' );

					if ( $index % 2 ) {
						$cell[] = 'border-l pl-5';
					}
					if ( $index > 0 ) {
						$cell[] = 'md:border-l md:pl-8';
					}
					if ( $index > 1 ) {
						$cell[] = 'border-t md:border-t-0';
					}
					?>
					<div class="<?php echo esc_attr( implode( ' ', $cell ) ); ?>">
						<dt class="sr-only"><?php echo esc_html( $fact['label'] ?? '' ); ?></dt>
						<dd>
							<span class="block font-display text-[2rem] font-semibold leading-none md:text-[2.5rem]"><?php echo esc_html( $fact['value'] ?? '' ); ?></span>
							<span class="mt-2 block text-[0.9375rem] text-sheet/75"><?php echo esc_html( $fact['label'] ?? '' ); ?></span>
						</dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	<?php endif; ?>
</section>
