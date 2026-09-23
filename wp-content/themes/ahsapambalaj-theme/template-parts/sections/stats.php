<?php
/**
 * Rakamlar seridi: cetvel gibi bolunmus tek kart. Sayisal degerler
 * goruldugunde sayarak gelir (data-count, assets/motion.js); sayi olmayan
 * degerler oldugu gibi yazilir.
 */

defined( 'ABSPATH' ) || exit;

// Degeri bos satirlar gosterilmez. Gercek rakam girilmeden bolum hic basilmaz;
// uydurma rakam yerine bos alan tercih edildi.
$items = array_values(
	array_filter(
		nwcs_rows( 'home', 'stats', 'items' ),
		static fn( $row ) => '' !== trim( (string) ( $row['value'] ?? '' ) )
	)
);

if ( ! $items ) {
	return;
}

// Tailwind sinif adlarini kaynakta tam haliyle gormeli; birlestirilmis ad derlenmez.
$items = array_slice( $items, 0, 3 );
$cols  = array( 1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3' )[ count( $items ) ];
?>
<section class="mx-auto max-w-[76rem] px-6 pt-16 md:pt-20">

	<ul class="card mx-auto grid max-w-[66rem] divide-y divide-line sm:divide-x sm:divide-y-0 <?php echo esc_attr( $cols ); ?>"
		data-reveal <?php nwcs_edit_attr( 'home', 'stats', 'items' ); ?>>
		<?php foreach ( $items as $item ) :
			$value  = trim( (string) ( $item['value'] ?? '' ) );
			$prefix = (string) ( $item['prefix'] ?? '' );
			$suffix = (string) ( $item['suffix'] ?? '' );
			$number = ctype_digit( str_replace( '.', '', $value ) ) ? (int) str_replace( '.', '', $value ) : null;
			?>
			<li class="px-6 py-8 text-center md:py-10">
				<span class="tick-draw mx-auto block h-[18px] w-px bg-timber" aria-hidden="true"></span>

				<p class="mt-4 font-display text-4xl font-semibold leading-none text-ink md:text-5xl">
					<?php echo esc_html( $prefix ); ?><span class="tally"
						<?php if ( null !== $number ) : ?>data-count="<?php echo esc_attr( (string) $number ); ?>"<?php endif; ?>><?php
						echo esc_html( null !== $number ? number_format( $number, 0, ',', '.' ) : $value );
					?></span><?php if ( '' !== $suffix ) : ?><span class="ml-1.5 text-xl font-medium text-moss md:text-2xl"><?php echo esc_html( $suffix ); ?></span><?php endif; ?>
				</p>

				<p class="mt-3 text-sm leading-relaxed text-moss">
					<?php echo esc_html( $item['label'] ?? '' ); ?>
				</p>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
