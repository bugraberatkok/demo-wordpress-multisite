<?php
/**
 * SMTP ile gonderim.
 *
 * Natro sunucusunda PHP'nin mail() islevi kapali: WordPress'in varsayilan
 * gonderimi "Call to undefined function PHPMailer\PHPMailer\mail()" hatasiyla
 * dusuyor ve hicbir form bildirimi gitmiyor. Gonderim bu yuzden gercek bir
 * e-posta kutusunun SMTP hesabindan yapilir.
 *
 * Bilgiler depoda degil, sunucudaki wp-config.php'de durur:
 *
 *   define( 'NWCS_SMTP_HOST', 'mail.ornek.com' );
 *   define( 'NWCS_SMTP_PORT', 587 );            // istege bagli; 465 ise SSL
 *   define( 'NWCS_SMTP_USER', 'bildirim@ornek.com' );
 *   define( 'NWCS_SMTP_PASS', '...' );
 *   define( 'NWCS_SMTP_FROM', 'bildirim@ornek.com' ); // istege bagli; bossa kullanici adi
 *
 * Gonderen adresi SMTP hesabinin adresi olur (sunucular baska adresle
 * gonderimi reddeder). Gonderen adi site adi kalir; musteriye "Yanitla"
 * Reply-To ile musterinin adresine gider (forms.php).
 */

defined( 'ABSPATH' ) || exit;

/**
 * SMTP ayari tamam mi? Sunucu ve kullanici adi yeterli; parola bos olabilir.
 */
function nwcs_smtp_configured(): bool {
	return defined( 'NWCS_SMTP_HOST' ) && '' !== trim( (string) NWCS_SMTP_HOST )
		&& defined( 'NWCS_SMTP_USER' ) && '' !== trim( (string) NWCS_SMTP_USER );
}

/**
 * Gonderen adres: NWCS_SMTP_FROM, yoksa SMTP kullanici adi.
 */
function nwcs_smtp_from(): string {
	$from = defined( 'NWCS_SMTP_FROM' ) ? trim( (string) NWCS_SMTP_FROM ) : '';

	return is_email( $from ) ? $from : trim( (string) NWCS_SMTP_USER );
}

/**
 * Baglanti noktasi ve sifreleme: 465 SSL, digerleri STARTTLS. NWCS_SMTP_SECURE
 * ('ssl', 'tls' ya da '') ile acikca verilebilir.
 *
 * @return array{0:int, 1:string}
 */
function nwcs_smtp_transport(): array {
	$port   = defined( 'NWCS_SMTP_PORT' ) ? (int) NWCS_SMTP_PORT : 587;
	$port   = $port > 0 ? $port : 587;
	$secure = defined( 'NWCS_SMTP_SECURE' ) ? strtolower( (string) NWCS_SMTP_SECURE ) : ( 465 === $port ? 'ssl' : 'tls' );

	return array( $port, in_array( $secure, array( 'ssl', 'tls' ), true ) ? $secure : '' );
}

// Oncelik 20: yereldeki Mailpit ayarindan (10) sonra calisir; ayar yoksa dokunmaz.
add_action(
	'phpmailer_init',
	static function ( $mailer ): void {
		if ( ! nwcs_smtp_configured() ) {
			return;
		}

		list( $port, $secure ) = nwcs_smtp_transport();

		$mailer->isSMTP();
		$mailer->Host        = trim( (string) NWCS_SMTP_HOST );
		$mailer->Port        = $port;
		$mailer->SMTPSecure  = $secure;
		$mailer->SMTPAutoTLS = '' !== $secure;
		$mailer->SMTPAuth    = true;
		$mailer->Username    = trim( (string) NWCS_SMTP_USER );
		$mailer->Password    = defined( 'NWCS_SMTP_PASS' ) ? (string) NWCS_SMTP_PASS : '';
		$mailer->Timeout     = 15;
	},
	20
);

add_filter(
	'wp_mail_from',
	static fn( $from ) => nwcs_smtp_configured() ? nwcs_smtp_from() : $from,
	20
);
