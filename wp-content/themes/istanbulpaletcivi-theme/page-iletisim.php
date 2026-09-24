<?php
/**
 * Iletisim: bilgiler, form, harita (Google Haritalar yerlestirmesi; adres
 * degisirse panelden "Haritada Aranacak Adres" guncellenir).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$map = trim( (string) nwcs_field( 'contact', 'details', 'map_query' ) );

pc_part(
	'page-head',
	array(
		'title' => (string) nwcs_field( 'contact', 'head', 'title' ),
		'lead'  => (string) nwcs_field( 'contact', 'head', 'lead' ),
		'page'  => 'contact',
	)
);
?>

<section class="pc-section">
	<div class="pc-wrap pc-order">
		<div>
			<?php pc_part( 'contact-lines' ); ?>
		</div>
		<div>
			<h2 class="pc-order__title" <?php nwcs_edit_attr( 'contact', 'form', 'title' ); ?>><?php echo esc_html( nwcs_field( 'contact', 'form', 'title' ) ); ?></h2>
			<?php pc_part( 'quote-form' ); ?>
		</div>
	</div>
</section>

<?php if ( $map ) : ?>
	<section class="pc-map" <?php nwcs_edit_attr( 'contact', 'details', 'map_query' ); ?>>
		<iframe title="Harita: <?php echo esc_attr( $map ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
			src="<?php echo esc_url( 'https://www.google.com/maps?output=embed&q=' . rawurlencode( $map ) ); ?>"></iframe>
	</section>
<?php endif; ?>

<?php
get_footer();
