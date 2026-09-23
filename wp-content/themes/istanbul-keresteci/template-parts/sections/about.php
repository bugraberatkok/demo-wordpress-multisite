<?php
/**
 * Biz kimiz: solda baslik ve metin, sagda tomruk fotografi.
 *
 * Baslik musterinin anlayacagi bir cumledir; ticari unvan altinda kucuk
 * yazi olarak durur. "Kirk yil" bilgisi metinde gectigi icin ayrica rozet yok.
 */

defined( 'ABSPATH' ) || exit;

$image = ik_image_or_default( nwcs_image( 'home', 'about', 'image', 'large' ), 'tomruk-orman.jpg', 'Ormanda yere serilmiş, kabuğu yer yer soyulmuş çam tomrukları' );
?>
<section class="ik-section ik-about" aria-labelledby="ik-about-title">
	<div class="ik-wrap ik-about__grid">
		<div class="ik-about__text">
			<h2 class="ik-title" id="ik-about-title" <?php nwcs_edit_attr( 'home', 'about', 'heading' ); ?>><?php echo esc_html( nwcs_field( 'home', 'about', 'heading' ) ); ?></h2>
			<p class="ik-about__company" <?php nwcs_edit_attr( 'home', 'about', 'company' ); ?>><?php echo esc_html( nwcs_field( 'home', 'about', 'company' ) ); ?></p>
			<div class="ik-prose" <?php nwcs_edit_attr( 'home', 'about', 'text' ); ?>>
				<?php ik_paragraphs( nwcs_field( 'home', 'about', 'text' ) ); ?>
			</div>
			<a class="ik-textlink" href="<?php echo esc_url( ik_link( nwcs_field( 'home', 'about', 'link_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'about', 'link_label' ); ?>>
				<?php echo esc_html( nwcs_field( 'home', 'about', 'link_label' ) ); ?>
				<?php ik_icon( 'arrow', 18 ); ?>
			</a>
		</div>

		<figure class="ik-about__media">
			<?php echo ik_image_tag( $image, 'ik-about__image' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</figure>
	</div>
</section>
