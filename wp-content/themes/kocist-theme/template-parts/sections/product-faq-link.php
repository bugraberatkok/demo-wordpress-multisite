<?php
/**
 * Urun sayfasi: urunun grubuna ait sik sorulan sorulara tek baglanti
 * (akordeonun son satiri). Sorularin kendisi /sss/ sayfasinda.
 *
 * Baglanti, SSS'de adi urun grubuyla eslesen (sanitize_title) ve en az bir
 * cevapli sorusu olan gruba gider (/sss/#sss-kereste); oyle bir grup yoksa
 * sayfanin basina. Hic cevapli soru yoksa ya da sayfa yoksa baglanti basilmaz.
 */

defined( 'ABSPATH' ) || exit;

$faq_page = get_page_by_path( 'sss', OBJECT, 'page' );

if ( ! $faq_page || 'publish' !== $faq_page->post_status || ! function_exists( 'nwcs_faq_groups' ) ) {
	return;
}

$faq_groups = nwcs_faq_groups( 'faq', (string) nwcs_field( 'faq', 'more', 'group_other' ) );

if ( ! $faq_groups ) {
	return;
}

$faq_slug  = sanitize_title( (string) ( $args['group'] ?? '' ) );
$faq_url   = get_permalink( $faq_page );
$faq_field = 'product_all';
$faq_label = (string) nwcs_field( 'faq', 'more', 'product_all' );

foreach ( array_keys( $faq_groups ) as $faq_name ) {
	if ( '' !== $faq_slug && sanitize_title( $faq_name ) === $faq_slug ) {
		$faq_url   .= '#sss-' . $faq_slug;
		$faq_field  = 'product_link';
		$faq_label  = kocist_text( 'faq', 'more', 'product_link', array( 'grup' => $faq_name ) );
		break;
	}
}
?>
<p class="k-acc__faq">
	<a class="k-acc__faq-link" href="<?php echo esc_url( $faq_url ); ?>">
		<span <?php nwcs_edit_attr( 'faq', 'more', $faq_field ); ?>><?php echo esc_html( $faq_label ); ?></span>
		<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h9.5M8.5 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
	</a>
</p>
