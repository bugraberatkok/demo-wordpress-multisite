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
		<h2 class="wk-h3" <?php nwcs_edit_attr( 'custom', 'process', 'process_title' ); ?>><?php echo esc_html( nwcs_field( 'custom', 'process', 'process_title' ) ); ?></h2>
		<ol class="wk-process" <?php nwcs_edit_attr( 'custom', 'process', 'steps' ); ?>>
			<?php foreach ( nwcs_rows( 'custom', 'process', 'steps' ) as $index => $step ) : ?>
				<li <?php nwcs_edit_attr( 'custom', 'process', 'steps', (int) $index, 'desc' ); ?>><strong <?php nwcs_edit_attr( 'custom', 'process', 'steps', (int) $index, 'title' ); ?>><?php echo esc_html( $step['title'] ?? '' ); ?></strong> <?php echo esc_html( $step['desc'] ?? '' ); ?></li>
			<?php endforeach; ?>
		</ol>
		<p class="wk-form__note" <?php nwcs_edit_attr( 'custom', 'process', 'returns_note' ); ?>><?php echo wk_text_html( 'custom', 'process', 'returns_note', array( 'baglanti' => '<a href="' . esc_url( wk_page_url( 'iptal-iade-kosullari' ) ) . '"' . wk_attr_string( 'custom', 'process', 'returns_link' ) . '>' . esc_html( nwcs_field( 'custom', 'process', 'returns_link' ) ) . '</a>' ) ); // phpcs:ignore WordPress.Security.EscapingOutput -- metin kacirildi, baglanti kacirilmis parcalardan. ?></p>
	</div>

	<div class="wk-contact__form">
		<h2 class="wk-h3" <?php nwcs_edit_attr( 'custom', 'form', 'title' ); ?>><?php echo esc_html( nwcs_field( 'custom', 'form', 'title' ) ); ?></h2>
		<p class="wk-lead" <?php nwcs_edit_attr( 'custom', 'form', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'custom', 'form', 'lead' ) ); ?></p>
		<?php wk_part( 'request-form', array( 'kind' => 'ozel' ) ); ?>
	</div>
</div>

<?php
get_footer();
