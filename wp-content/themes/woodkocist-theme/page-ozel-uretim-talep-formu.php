<?php
/**
 * Ozel Uretim (/ozel-uretim-talep-formu/): proje talep formu, dosya ekli
 * (inc/requests.php). Surecin adimlari SSS'deki cevaptan (gercek bir sira).
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="wk-pagehead">
	<div class="wk-wrap">
		<h1 class="wk-hero__title" <?php nwcs_edit_attr( 'custom', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'custom', 'head', 'title' ) ); ?></h1>
		<p class="wk-hero__lead" <?php nwcs_edit_attr( 'custom', 'head', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'custom', 'head', 'lead' ) ); ?></p>
	</div>
</section>

<div class="wk-wrap wk-section wk-contact">
	<div class="wk-contact__info">
		<h2 class="wk-h3">Süreç nasıl ilerliyor?</h2>
		<ol class="wk-process">
			<li><strong>Talebinizi paylaşın.</strong> Ölçüleri, kullanım yerini ve varsa çizim ya da fotoğrafı formla gönderin.</li>
			<li><strong>Projelendirme.</strong> Mühendislik ve mimari ekibimiz statik ve estetik dengeyi hesaplayıp üç boyutlu modeli hazırlar.</li>
			<li><strong>Onay ve fiyat.</strong> Tüm maliyet tablosu onaydan önce size sunulur; sonradan ek ödeme çıkmaz.</li>
			<li><strong>Üretim ve teslim.</strong> Onaylanan tasarım üretime alınır, her aşamada bilgilendirilirsiniz.</li>
		</ol>
		<p class="wk-form__note">Özel ölçüyle üretilen ürünlerde yasal cayma hakkı yoktur; ayrıntılar <a href="<?php echo esc_url( wk_page_url( 'iptal-iade-kosullari' ) ); ?>">iptal ve iade koşullarında</a>.</p>
	</div>

	<div class="wk-contact__form">
		<h2 class="wk-h3" <?php nwcs_edit_attr( 'custom', 'form', 'title' ); ?>><?php echo esc_html( nwcs_field( 'custom', 'form', 'title' ) ); ?></h2>
		<p class="wk-lead" <?php nwcs_edit_attr( 'custom', 'form', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'custom', 'form', 'lead' ) ); ?></p>
		<?php wk_part( 'request-form', array( 'kind' => 'ozel' ) ); ?>
	</div>
</div>

<?php
get_footer();
