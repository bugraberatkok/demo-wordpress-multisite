<?php
/**
 * Urunler: tek sayfa, uc grup (paletler, sandiklar, kafesler).
 *
 * Sayfanin tek gosterisli ogesi palet kartlarindaki olcekli ustten cizim
 * (sanayi_palet_pallet_drawing). Her kartin teklif dugmesi iletisim formunu
 * konusu doldurulmus acar.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$groups = array(
	'paletler'  => nwcs_field( 'products', 'pallets', 'title' ),
	'sandiklar' => nwcs_field( 'products', 'crates', 'title' ),
	'kafesler'  => nwcs_field( 'products', 'cages', 'title' ),
);
?>

<header class="sp-pagehead">
	<div class="sp-wrap">
		<h1 class="sp-pagehead__title" <?php nwcs_edit_attr( 'products', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'head', 'title' ) ); ?></h1>
		<p class="sp-pagehead__text" <?php nwcs_edit_attr( 'products', 'head', 'text' ); ?>><?php echo esc_html( nwcs_field( 'products', 'head', 'text' ) ); ?></p>

		<nav class="sp-jump" aria-label="Ürün grupları">
			<ul>
				<?php foreach ( $groups as $anchor => $label ) : ?>
					<li><a href="#<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	</div>
</header>

<?php // Paletler ------------------------------------------------------------ ?>
<section class="sp-section sp-pallets" id="paletler">
	<div class="sp-wrap">
		<div class="sp-pallets__intro">
			<h2 class="sp-title" <?php nwcs_edit_attr( 'products', 'pallets', 'title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'pallets', 'title' ) ); ?></h2>
			<div>
				<p class="sp-pallets__lead" <?php nwcs_edit_attr( 'products', 'pallets', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'products', 'pallets', 'lead' ) ); ?></p>
				<p class="sp-pallets__text" <?php nwcs_edit_attr( 'products', 'pallets', 'text' ); ?>><?php echo esc_html( nwcs_field( 'products', 'pallets', 'text' ) ); ?></p>
			</div>
		</div>

		<ul class="sp-pallets__grid" <?php nwcs_edit_attr( 'products', 'pallets', 'types' ); ?>>
			<?php foreach ( nwcs_rows( 'products', 'pallets', 'types' ) as $index => $type ) : ?>
				<?php
				$name = (string) ( $type['name'] ?? '' );

				if ( '' === trim( $name ) ) {
					continue;
				}
				?>
				<li class="sp-pallet">
					<div class="sp-pallet__drawing">
						<?php sanayi_palet_pallet_drawing( (string) ( $type['length'] ?? '' ), (string) ( $type['width'] ?? '' ), (string) ( $type['deck'] ?? '' ), $name ); ?>
					</div>

					<h3 class="sp-pallet__name" <?php nwcs_edit_attr( 'products', 'pallets', 'types', $index, 'name' ); ?>><?php echo esc_html( $name ); ?></h3>
					<p class="sp-pallet__summary" <?php nwcs_edit_attr( 'products', 'pallets', 'types', $index, 'summary' ); ?>><?php echo esc_html( $type['summary'] ?? '' ); ?></p>

					<?php $specs = sanayi_palet_spec_rows( (string) ( $type['specs'] ?? '' ) ); ?>
					<?php if ( $specs ) : ?>
						<dl class="sp-pallet__specs" <?php nwcs_edit_attr( 'products', 'pallets', 'types', $index, 'specs' ); ?>>
							<?php foreach ( $specs as $spec ) : ?>
								<div class="sp-pallet__spec">
									<dt><?php echo esc_html( $spec['label'] ); ?></dt>
									<dd><?php echo esc_html( $spec['value'] ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>

					<?php if ( ! empty( $type['text'] ) ) : ?>
						<details class="sp-pallet__more">
							<summary><?php echo esc_html( nwcs_field( 'products', 'pallets', 'more_label' ) ); ?></summary>
							<p <?php nwcs_edit_attr( 'products', 'pallets', 'types', $index, 'text' ); ?>><?php echo esc_html( $type['text'] ); ?></p>
						</details>
					<?php endif; ?>

					<a class="btn btn--sm btn--outline sp-pallet__quote" href="<?php echo esc_url( sanayi_palet_quote_url( $name ) ); ?>">
						<?php echo esc_html( nwcs_field( 'products', 'pallets', 'quote_label' ) ); ?><span class="screen-reader-text">: <?php echo esc_html( $name ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>

			<?php // Ozel olcu: listedeki son kutu, cizimi yerine cetvel ikonu. ?>
			<li class="sp-pallet sp-pallet--custom">
				<span class="sp-intro__icon"><?php sanayi_palet_icon( 'ruler', 28 ); ?></span>
				<h3 class="sp-pallet__name" <?php nwcs_edit_attr( 'products', 'pallets', 'custom_title' ); ?>><?php echo esc_html( nwcs_field( 'products', 'pallets', 'custom_title' ) ); ?></h3>
				<p class="sp-pallet__summary" <?php nwcs_edit_attr( 'products', 'pallets', 'custom_text' ); ?>><?php echo esc_html( nwcs_field( 'products', 'pallets', 'custom_text' ) ); ?></p>
				<a class="btn btn--sm btn--solid sp-pallet__quote" href="<?php echo esc_url( sanayi_palet_quote_url( 'Özel ölçü palet' ) ); ?>" <?php nwcs_edit_attr( 'products', 'pallets', 'custom_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'products', 'pallets', 'custom_label' ) ); ?>
				</a>
			</li>
		</ul>
	</div>
</section>

<?php
// Sandiklar ve kafesler ayni duzende; kafeste gorsel saga gecer.
foreach ( array( 'crates' => 'sandiklar', 'cages' => 'kafesler' ) as $component => $anchor ) :
	$image = sanayi_palet_image( nwcs_image( 'products', $component, 'image', 'large' ), 'crates' === $component ? 'range_1' : 'range_2' );
	$title = nwcs_field( 'products', $component, 'title' );
	?>
	<section class="sp-section sp-group-product sp-group-product--<?php echo esc_attr( $component ); ?>" id="<?php echo esc_attr( $anchor ); ?>">
		<div class="sp-wrap sp-group-product__grid">
			<div class="sp-group-product__media" <?php nwcs_edit_attr( 'products', $component, 'image' ); ?>>
				<?php echo sanayi_palet_image_tag( $image, 'sp-group-product__image' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
			</div>

			<div class="sp-group-product__body">
				<h2 class="sp-title" <?php nwcs_edit_attr( 'products', $component, 'title' ); ?>><?php echo esc_html( $title ); ?></h2>
				<p class="sp-group-product__text" <?php nwcs_edit_attr( 'products', $component, 'text' ); ?>><?php echo esc_html( nwcs_field( 'products', $component, 'text' ) ); ?></p>

				<h3 class="sp-group-product__list-title" <?php nwcs_edit_attr( 'products', $component, 'list_title' ); ?>><?php echo esc_html( nwcs_field( 'products', $component, 'list_title' ) ); ?></h3>
				<ul class="sp-checklist__list" <?php nwcs_edit_attr( 'products', $component, 'list' ); ?>>
					<?php foreach ( sanayi_palet_lines( nwcs_field( 'products', $component, 'list' ) ) as $line ) : ?>
						<li class="sp-checklist__item">
							<?php sanayi_palet_icon( 'check', 22, 'sp-icon sp-checklist__tick' ); ?>
							<span class="sp-checklist__label"><?php echo esc_html( $line ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>

				<a class="btn btn--solid sp-group-product__quote" href="<?php echo esc_url( sanayi_palet_quote_url( $title ) ); ?>" <?php nwcs_edit_attr( 'products', $component, 'quote_label' ); ?>>
					<?php echo esc_html( nwcs_field( 'products', $component, 'quote_label' ) ); ?>
				</a>
			</div>
		</div>
	</section>
<?php endforeach; ?>

<?php
sanayi_palet_section( 'ctaband' );

get_footer();
