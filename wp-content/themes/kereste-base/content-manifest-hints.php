<?php
/**
 * Panel ipuclari (content-manifest.php sonunda uygulanir).
 *
 * 1. Ayni iletisim bilgisi sitede birden cok yerde ayri alan olarak
 *    yaziliysa, her alanin altina "baska nerede yazili" notu eklenir;
 *    musteri birini degistirip digerini unutmasin.
 * 2. Tiklanan telefon, WhatsApp ve e-posta baglantisi alanlarina yazim
 *    hatirlatmasi: gorunen numara degisince baglanti da degismeli.
 *
 * Alanin kendi ipucu korunur, not sonuna eklenir. Saf PHP; manifest birden
 * cok kez okunabildigi icin fonksiyon tanimlamaz, closure dondurur.
 *
 * Kullanim: return ( require __DIR__ . '/content-manifest-hints.php' )( $manifest, array(
 *     'Telefon numarası' => array( 'global.header.phone_label', 'contact.details.phone_label' ),
 * ) );
 */

return static function ( array $manifest, array $groups ): array {
	$append = static function ( array &$field, string $text ): void {
		$field['hint'] = trim( (string) ( $field['hint'] ?? '' ) . ' ' . $text );
	};

	// Panelde gorunen yer adi: "İletişim › İletişim Bilgileri".
	$where = static function ( string $path ) use ( $manifest ): string {
		list( $page_key, $component_key ) = explode( '.', $path );
		$page = (string) preg_replace( '/\s*\(.*\)$/u', '', (string) ( $manifest['pages'][ $page_key ]['label'] ?? $page_key ) );

		return $page . ' › ' . ( $manifest['pages'][ $page_key ]['components'][ $component_key ]['label'] ?? $component_key );
	};

	foreach ( $groups as $what => $paths ) {
		foreach ( $paths as $path ) {
			list( $page_key, $component_key, $field_key ) = explode( '.', $path );

			if ( ! isset( $manifest['pages'][ $page_key ]['components'][ $component_key ]['fields'][ $field_key ] ) ) {
				continue;
			}

			$self   = $where( $path );
			$others = array_values( array_diff( array_unique( array_map( $where, $paths ) ), array( $self ) ) );

			if ( $others ) {
				$append(
					$manifest['pages'][ $page_key ]['components'][ $component_key ]['fields'][ $field_key ],
					sprintf( '%s sitede şurada da ayrıca yazılı: %s. Değiştirirseniz orayı da güncelleyin.', $what, implode( '; ', $others ) )
				);
			}
		}
	}

	$formats = array(
		'/(^|_)(phone|mobile)_url$/'  => 'Tıklanınca aranan numara; tel:+90 ile başlar, boşluksuz yazılır. Görünen numarayı değiştirdiyseniz bunu da değiştirin.',
		'/(^|_)(whatsapp|wa)_url$/'   => 'WhatsApp bağlantısı; https://wa.me/90 ile başlar, numara boşluksuz yazılır. Numara değiştiyse bunu da değiştirin.',
		'/(^|_)email_url$/'           => 'Tıklanınca açılan e-posta; mailto: ile başlar. Görünen adresi değiştirdiyseniz bunu da değiştirin.',
	);

	foreach ( $manifest['pages'] as $page_key => $page ) {
		foreach ( $page['components'] ?? array() as $component_key => $component ) {
			foreach ( $component['fields'] ?? array() as $field_key => $field ) {
				if ( 'url' !== ( $field['type'] ?? '' ) ) {
					continue;
				}

				foreach ( $formats as $pattern => $text ) {
					if ( preg_match( $pattern, (string) $field_key ) ) {
						$append( $manifest['pages'][ $page_key ]['components'][ $component_key ]['fields'][ $field_key ], $text );
						break;
					}
				}
			}
		}
	}

	return $manifest;
};
