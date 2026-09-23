<?php
/**
 * Bolum basligi: solda baslik ve metin, sagda bolumun tamamina giden baglanti.
 *
 * $args: page, component, url (baglanti adresi; bossa baglanti basilmaz).
 * Alan adlari sabittir: title, text, link_label.
 */

defined( 'ABSPATH' ) || exit;

$page      = $args['page'];
$component = $args['component'];
$url       = $args['url'] ?? '';
?>
<div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between md:gap-10">
	<div class="max-w-[40rem]">
		<h2 class="text-[2.5rem] font-semibold md:text-5xl" <?php nwcs_edit_attr( $page, $component, 'title' ); ?>>
			<?php echo esc_html( nwcs_field( $page, $component, 'title' ) ); ?>
		</h2>
		<p class="mt-4 text-lg leading-relaxed text-steel" <?php nwcs_edit_attr( $page, $component, 'text' ); ?>>
			<?php echo esc_html( nwcs_field( $page, $component, 'text' ) ); ?>
		</p>
	</div>

	<?php if ( $url ) : ?>
		<a href="<?php echo esc_url( $url ); ?>" class="btn btn--md btn--outline shrink-0 self-start md:self-auto"
			<?php nwcs_edit_attr( $page, $component, 'link_label' ); ?>>
			<?php echo esc_html( nwcs_field( $page, $component, 'link_label' ) ); ?>
		</a>
	<?php endif; ?>
</div>
