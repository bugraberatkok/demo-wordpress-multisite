<?php
/**
 * Hero: tam genislik orman fotografi, sol altta baslik.
 *
 * Baslik iki genislikte basilir: marka adi dar ve agir, alt baslik genis ve
 * ince. Tipografinin kendisi gorsel ogedir; ayrica suslemeye gerek yok.
 */

defined( 'ABSPATH' ) || exit;

$image = ik_image_or_default( nwcs_image( 'home', 'hero', 'image', 'full' ), 'hero-orman.jpg' );
?>
<?php // Zemine (fotografa) tiklaninca fotograf alani acilir; yazilar kendi alanlarini acar. ?>
<section class="ik-hero" aria-labelledby="ik-hero-title" <?php nwcs_edit_attr( 'home', 'hero', 'image' ); ?>>
	<?php echo ik_image_tag( $image, 'ik-hero__image', '', 'eager', ik_edit_attrs( 'home', 'hero', 'image' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

	<div class="ik-wrap ik-hero__inner">
		<h1 class="ik-hero__title" id="ik-hero-title">
			<span class="ik-hero__name" <?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'title' ) ); ?></span>
			<span class="ik-hero__sub" <?php nwcs_edit_attr( 'home', 'hero', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'subtitle' ) ); ?></span>
		</h1>

		<p class="ik-hero__text" <?php nwcs_edit_attr( 'home', 'hero', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'text' ) ); ?></p>

		<div class="ik-hero__actions">
			<a class="btn btn--solid" href="<?php echo esc_url( ik_link( nwcs_field( 'home', 'hero', 'primary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'hero', 'primary_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'primary_label' ) ); ?>
			</a>
			<a class="btn btn--ghost" href="<?php echo esc_url( ik_link( nwcs_field( 'home', 'hero', 'secondary_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'hero', 'secondary_label' ); ?>>
				<?php ik_icon( 'phone', 20 ); ?>
				<?php echo esc_html( nwcs_field( 'home', 'hero', 'secondary_label' ) ); ?>
			</a>
		</div>
	</div>
</section>
