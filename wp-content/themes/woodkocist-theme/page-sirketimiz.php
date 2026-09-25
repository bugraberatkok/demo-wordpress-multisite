<?php
/**
 * Sirketimiz / Hikayemiz (/sirketimiz/). Metin panelden (about.*); yaninda
 * kurumsal sayfalar listesi.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$items = nwcs_rows( 'about', 'story', 'items' );
?>

<section class="wk-pagehead">
	<div class="wk-wrap">
		<p class="wk-pagehead__kicker" <?php nwcs_edit_attr( 'about', 'head', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'about', 'head', 'lead' ) ); ?></p>
		<h1 class="wk-hero__title" <?php nwcs_edit_attr( 'about', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'head', 'title' ) ); ?></h1>
	</div>
</section>

<div class="wk-wrap wk-section wk-doc">
	<div class="wk-doc__main">
		<p class="wk-intro" <?php nwcs_edit_attr( 'about', 'story', 'intro' ); ?>><?php echo esc_html( nwcs_field( 'about', 'story', 'intro' ) ); ?></p>

		<?php if ( $items ) : ?>
			<ul class="wk-ticks wk-ticks--row" <?php nwcs_edit_attr( 'about', 'story', 'items' ); ?>>
				<?php foreach ( $items as $index => $item ) : ?>
					<li <?php nwcs_edit_attr( 'about', 'story', 'items', (int) $index, 'text' ); ?>><?php echo wk_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapingOutput ?><?php echo esc_html( $item['text'] ?? '' ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="wk-prose">
			<h2 <?php nwcs_edit_attr( 'about', 'story', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'story', 'title' ) ); ?></h2>
			<div <?php nwcs_edit_attr( 'about', 'story', 'text' ); ?>><?php echo wk_paragraphs( (string) nwcs_field( 'about', 'story', 'text' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?></div>
		</div>

		<div class="wk-cta">
			<a class="wk-btn wk-btn--primary wk-btn--lg" href="<?php echo esc_url( wk_shop_url() ); ?>" <?php nwcs_edit_attr( 'about', 'cta', 'shop_button' ); ?>><?php echo esc_html( nwcs_field( 'about', 'cta', 'shop_button' ) ); ?></a>
			<a class="wk-btn wk-btn--line wk-btn--lg" href="<?php echo esc_url( wk_page_url( 'ozel-uretim-talep-formu' ) ); ?>" <?php nwcs_edit_attr( 'about', 'cta', 'custom_button' ); ?>><?php echo esc_html( nwcs_field( 'about', 'cta', 'custom_button' ) ); ?></a>
		</div>
	</div>
	<?php wk_part( 'doc-nav' ); ?>
</div>

<?php
get_footer();
