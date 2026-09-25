<?php
/**
 * YEREL: WordPress e-postalari Mailpit'e gider (http://localhost:8025).
 *
 * Yalnizca docker-compose ile bu kapsayiciya baglanir; canliya dagitilmaz
 * (.cpanel.yml yalnizca temalari ve eklentiyi kopyalar). Kapsayicida sendmail
 * olmadigi icin bu olmadan her wp_mail sessizce basarisiz olur.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'phpmailer_init',
	static function ( $mailer ): void {
		$host = getenv( 'WP_MAILPIT_HOST' );

		if ( ! $host ) {
			return;
		}

		$mailer->isSMTP();
		$mailer->Host     = $host;
		$mailer->Port     = 1025;
		$mailer->SMTPAuth = false;
	}
);

// Yerelde varsayilan gonderen "wordpress@localhost" gecersiz sayiliyor (alan adi yok).
add_filter(
	'wp_mail_from',
	static fn( $from ) => str_ends_with( (string) $from, '@localhost' ) ? 'wordpress@localhost.test' : $from
);
