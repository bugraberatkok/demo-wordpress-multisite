<?php
/**
 * Sikca Sorulan Sorular (/sss/). Sorular panelden (faq.items.rows), grup
 * adina gore basliklar altinda; her soru acilir kapanir (details). Eklenti
 * sayfayi FAQPage olarak isaretler.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$groups = array();

foreach ( nwcs_rows( 'faq', 'items', 'rows' ) as $row ) {
	if ( '' === trim( (string) ( $row['question'] ?? '' ) ) ) {
		continue;
	}
	$groups[ trim( (string) ( $row['group'] ?? '' ) ) ?: 'Diğer' ][] = $row;
}
?>

<section class="wk-pagehead wk-pagehead--compact">
	<div class="wk-wrap">
		<h1 class="wk-hero__title" <?php nwcs_edit_attr( 'faq', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'head', 'title' ) ); ?></h1>
		<p class="wk-hero__lead" <?php nwcs_edit_attr( 'faq', 'head', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'faq', 'head', 'lead' ) ); ?></p>
	</div>
</section>

<div class="wk-wrap wk-section wk-doc">
	<div class="wk-doc__main" <?php nwcs_edit_attr( 'faq', 'items', 'rows' ); ?>>
		<?php foreach ( $groups as $group => $rows ) : ?>
			<section class="wk-faq" aria-labelledby="<?php echo esc_attr( 'sss-' . sanitize_title( $group ) ); ?>">
				<h2 id="<?php echo esc_attr( 'sss-' . sanitize_title( $group ) ); ?>" class="wk-faq__title"><?php echo esc_html( $group ); ?></h2>
				<?php foreach ( $rows as $row ) : ?>
					<details class="wk-faq__item">
						<summary><?php echo esc_html( $row['question'] ); ?><?php echo wk_icon( 'chevron' ); // phpcs:ignore WordPress.Security.EscapingOutput ?></summary>
						<div class="wk-faq__answer"><?php echo wk_paragraphs( (string) ( $row['answer'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?></div>
					</details>
				<?php endforeach; ?>
			</section>
		<?php endforeach; ?>

		<div class="wk-help wk-help--wide">
			<p class="wk-help__title">Sorunuzun cevabı burada yok mu?</p>
			<p>Çözüm Merkezi’ne yazın ya da WhatsApp’tan sorun; ilgili ekip size dönsün.</p>
			<p class="wk-cta">
				<a class="wk-btn wk-btn--primary" href="<?php echo esc_url( wk_page_url( 'iletisim' ) ); ?>">Çözüm Merkezi’ne yazın</a>
				<?php if ( wk_whatsapp() ) : ?>
					<a class="wk-btn wk-btn--line" href="<?php echo esc_url( wk_whatsapp() ); ?>" target="_blank" rel="noopener"><?php echo wk_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapingOutput ?> WhatsApp</a>
				<?php endif; ?>
			</p>
		</div>
	</div>

	<nav class="wk-doc__side wk-docnav" aria-label="Soru grupları">
		<p class="wk-docnav__title">Konular</p>
		<ul>
			<?php foreach ( array_keys( $groups ) as $group ) : ?>
				<li><a href="#<?php echo esc_attr( 'sss-' . sanitize_title( $group ) ); ?>"><?php echo esc_html( $group ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</div>

<?php
get_footer();
