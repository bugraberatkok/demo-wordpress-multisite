<?php
/**
 * Sinirli ve guvenilir ikon listesi.
 *
 * Ikonlar sabit, satir ici SVG olarak tutulur. Panelden yalnizca bu listedeki
 * anahtarlar secilebilir; keyfi SVG/HTML yazilamaz.
 */

defined( 'ABSPATH' ) || exit;

/**
 * anahtar => [ Turkce etiket, SVG govde ]
 */
function nwcs_icon_library(): array {
	return array(
		'truck'    => array(
			'label' => 'Kamyon / Sevkiyat',
			'path'  => '<path d="M3 7h10v9H3z"/><path d="M13 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
		),
		'box'      => array(
			'label' => 'Kutu / Palet',
			'path'  => '<path d="M12 3 3 7.5V17l9 4.5 9-4.5V7.5z"/><path d="M3 7.5 12 12l9-4.5"/><path d="M12 12v9.5"/>',
		),
		'factory'  => array(
			'label' => 'Fabrika / Uretim',
			'path'  => '<path d="M3 20V10l5 3V10l5 3V7l6 3v10z"/><path d="M7 20v-3"/><path d="M12 20v-3"/><path d="M17 20v-3"/>',
		),
		'tools'    => array(
			'label' => 'Alet / Hirdavat',
			'path'  => '<path d="M14.5 5.5a3.5 3.5 0 0 0 4.6 4.6L21 12l-9 9-3-3 9-9z"/><path d="M6 6l4 4"/><path d="M3 9l3-6 3 3-3 6z"/>',
		),
		'tree'     => array(
			'label' => 'Agac / Orman Urunu',
			'path'  => '<path d="M12 3 6 12h3l-4 6h14l-4-6h3z"/><path d="M12 18v3"/>',
		),
		'recycle'  => array(
			'label' => 'Geri Donusum',
			'path'  => '<path d="M7 8 4 13l3 2"/><path d="M12 4l3 5-3 2"/><path d="M20 13l-3 5h-4"/><path d="M4 13h4l3-5"/><path d="M15 9l3 4"/>',
		),
		'shield'   => array(
			'label' => 'Guvence / Sertifika',
			'path'  => '<path d="M12 3 5 6v6c0 4 3 7 7 9 4-2 7-5 7-9V6z"/><path d="m9 12 2 2 4-4"/>',
		),
		'ruler'    => array(
			'label' => 'Olcu / Ozel Uretim',
			'path'  => '<path d="m3 15 6-6 6 6-6 6z" transform="rotate(-45 12 12)"/><path d="M8 10.5 9.5 12"/><path d="M11 8 12.5 9.5"/><path d="M14 5.5 15.5 7"/>',
		),
		'clock'    => array(
			'label' => 'Saat / Zamaninda Teslim',
			'path'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		),
		'phone'    => array(
			'label' => 'Telefon',
			'path'  => '<path d="M5 3h4l2 5-3 2a12 12 0 0 0 6 6l2-3 5 2v4a2 2 0 0 1-2 2C10 21 3 14 3 5a2 2 0 0 1 2-2z"/>',
		),
		'whatsapp' => array(
			'label' => 'WhatsApp',
			'path'  => '<path d="M12 3a9 9 0 0 0-7.7 13.6L3 21l4.5-1.2A9 9 0 1 0 12 3z"/><path d="M9 9c0 3 3 6 6 6l1-2-2.5-1-1 1c-1 0-2-1-2-2l1-1L10.5 8z"/>',
		),
		'mail'     => array(
			'label' => 'E-posta',
			'path'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		),
		'pin'      => array(
			'label' => 'Adres / Konum',
			'path'  => '<path d="M12 21s7-6 7-11a7 7 0 1 0-14 0c0 5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
		),
		'check'    => array(
			'label' => 'Onay',
			'path'  => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
		),
		'star'     => array(
			'label' => 'Yildiz / One Cikan',
			'path'  => '<path d="m12 4 2.4 4.9 5.4.8-3.9 3.8.9 5.4-4.8-2.6-4.8 2.6.9-5.4L4.2 9.7l5.4-.8z"/>',
		),
		'arrow'    => array(
			'label' => 'Ok / Devam',
			'path'  => '<path d="M4 12h15"/><path d="m13 6 6 6-6 6"/>',
		),
	);
}

/**
 * Gecerli ikon anahtarlari.
 */
function nwcs_icon_keys(): array {
	return array_keys( nwcs_icon_library() );
}

/**
 * Listede olmayan anahtarlari bos degere dusurur.
 */
function nwcs_sanitize_icon( string $key ): string {
	$key = sanitize_key( $key );

	return in_array( $key, nwcs_icon_keys(), true ) ? $key : '';
}

/**
 * Satir ici SVG dondurur. Girdi listeden dogrulanir, kullanici HTML'i basilmaz.
 */
function nwcs_icon_svg( string $key, string $class = 'nwcs-icon', int $size = 24 ): string {
	$key = nwcs_sanitize_icon( $key );

	if ( '' === $key ) {
		return '';
	}

	$icon = nwcs_icon_library()[ $key ];

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $class ),
		$size,
		$icon['path']
	);
}

/**
 * Tema tarafinda dogrudan basmak icin.
 */
function nwcs_the_icon( string $key, string $class = 'nwcs-icon', int $size = 24 ): void {
	echo nwcs_icon_svg( $key, $class, $size ); // phpcs:ignore WordPress.Security.EscapingOutput -- sabit ikon listesi.
}
