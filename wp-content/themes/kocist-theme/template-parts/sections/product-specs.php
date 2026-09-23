<?php
/**
 * Urun teknik ozellikleri.
 *
 * Referanstaki yatay tablo yerine kendiliginden sarilan bir izgara:
 * her hucre ustte ozellik adi, altta degeri. Dokuz sutunlu bir tablo dar
 * ekranda yatay kaydirma gerektirirdi; izgara ayni gorunumu verip
 * telefonda tek sutuna iniyor.
 */

defined( 'ABSPATH' ) || exit;

$rows = nwcs_rows( 'product', 'specs', 'rows' );

if ( ! $rows ) {
	return;
}
?>
<section class="k-specs" data-nwcs-section="specs">
	<div class="k-wrap">
		<h2 class="k-specs__title"><?php echo esc_html( nwcs_field( 'product', 'specs', 'title' ) ); ?></h2>

		<div class="k-specs__card">
			<p class="k-specs__name"><?php echo esc_html( nwcs_field( 'product', 'specs', 'name' ) ); ?></p>

			<dl class="k-specs__grid">
				<?php foreach ( $rows as $row ) : ?>
					<div class="k-specs__cell">
						<dt class="k-specs__label"><?php echo esc_html( $row['label'] ?? '' ); ?></dt>
						<dd class="k-specs__value"><?php echo esc_html( $row['value'] ?? '' ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	</div>
</section>
