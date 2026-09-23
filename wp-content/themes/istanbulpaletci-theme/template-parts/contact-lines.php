<?php
/**
 * Hizli iletisim satirlari: telefon, WhatsApp, e-posta. Degerler Iletisim
 * sayfasinin bilgilerinden okunur; tek yerde degisir.
 */

defined( 'ABSPATH' ) || exit;

$lines = array(
	array( 'icon' => 'phone', 'key' => 'phone', 'external' => false ),
	array( 'icon' => 'whatsapp', 'key' => 'whatsapp', 'external' => true ),
	array( 'icon' => 'mail', 'key' => 'email', 'external' => false ),
);
?>
<ul class="mt-8 space-y-1">
	<?php foreach ( $lines as $line ) :
		$url = trim( (string) nwcs_field( 'contact', 'details', $line['key'] . '_url' ) );

		if ( '' === $url ) {
			continue;
		}
		?>
		<li>
			<a href="<?php echo esc_url( ip_link( $url ) ); ?>"
				<?php echo $line['external'] ? 'target="_blank" rel="noopener"' : ''; ?>
				class="group inline-flex items-center gap-3 py-1.5 text-ink no-underline"
				<?php nwcs_edit_attr( 'contact', 'details', $line['key'] . '_label' ); ?>>
				<span class="flex h-10 w-10 items-center justify-center rounded-[3px] border border-line bg-sheet transition-colors group-hover:border-indigo <?php echo 'whatsapp' === $line['icon'] ? 'text-whatsapp' : 'text-indigo'; ?>" aria-hidden="true">
					<?php nwcs_the_icon( $line['icon'], '', 18 ); ?>
				</span>
				<span>
					<span class="block text-[0.8125rem] text-steel"><?php echo esc_html( nwcs_field( 'contact', 'details', $line['key'] . '_title' ) ); ?></span>
					<span class="tabular block font-semibold transition-colors group-hover:text-indigo"><?php echo esc_html( nwcs_field( 'contact', 'details', $line['key'] . '_label' ) ); ?></span>
				</span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
