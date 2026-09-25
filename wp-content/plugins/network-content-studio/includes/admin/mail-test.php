<?php
/**
 * Ag Yonetimi -> Icerik Studyosu -> E-posta Testi.
 *
 * Form bildirimlerinin (includes/forms.php) gercekten gidip gitmedigini canli
 * sitede form doldurmadan denemek icin: her site icin alici, gonderen adresi,
 * son form kayitlarinin bildirim durumu ve "Test e-postasi gonder" dugmesi.
 * Gonderim hatasi (wp_mail_failed) ekranda gosterilir.
 */

defined( 'ABSPATH' ) || exit;

const NWCS_MAILTEST_SLUG   = 'nwcs-mail-test';
const NWCS_MAILTEST_RESULT = 'nwcs_mail_test_result';

add_action( 'network_admin_menu', 'nwcs_register_mailtest_menu', 32 );
function nwcs_register_mailtest_menu(): void {
	add_submenu_page( NWCS_MENU_SLUG, 'E-posta Testi', 'E-posta Testi', NWCS_CAPABILITY, NWCS_MAILTEST_SLUG, 'nwcs_render_mailtest' );
}

/**
 * Sitenin form bildirim alicilari (forms.php ile ayni kural; kayit olmadan).
 * Cagiran site baglaminda olmali.
 */
function nwcs_mailtest_recipients(): array {
	$default = function_exists( 'nwcs_field' ) ? nwcs_form_clean_email( nwcs_field( NWCS_SEO_SITE_PAGE, 'org', 'email', '' ) ) : '';

	if ( '' === $default ) {
		$default = nwcs_form_clean_email( get_option( 'admin_email' ) );
	}

	$raw = apply_filters( 'nwcs_form_recipient', $default, null );
	$out = array();

	foreach ( is_array( $raw ) ? $raw : explode( ',', (string) $raw ) as $item ) {
		$email = nwcs_form_clean_email( $item );

		if ( '' !== $email ) {
			$out[ strtolower( $email ) ] = $email;
		}
	}

	return array_values( $out );
}

/**
 * WordPress'in bu sitede kullanacagi gonderen adresi (wp_mail ile ayni hesap).
 */
function nwcs_mailtest_from(): string {
	$host = (string) wp_parse_url( network_home_url(), PHP_URL_HOST );

	if ( str_starts_with( $host, 'www.' ) ) {
		$host = substr( $host, 4 );
	}

	return (string) apply_filters( 'wp_mail_from', 'wordpress@' . $host );
}

/**
 * Sitenin form kayitlarinin bildirim durumu: tur => [ toplam, sent, failed, pending, son tarih ].
 */
function nwcs_mailtest_form_stats(): array {
	$stats = array();

	global $wpdb;

	// Kayit turleri yalnizca sitenin kendi temasi yuklenince kayitlidir; ag
	// yonetiminde post_type_exists() yanlis doner. Tablo dogrudan okunur.
	foreach ( nwcs_form_post_types() as $type => $prefix ) {
		$ids = array_map(
			'intval',
			$wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status NOT IN ('trash','auto-draft') ORDER BY post_date DESC LIMIT 50", $type ) ) // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		);

		if ( ! $ids ) {
			continue;
		}

		$row = array( 'total' => count( $ids ), 'sent' => 0, 'failed' => 0, 'pending' => 0, 'last' => '' );

		foreach ( $ids as $id ) {
			$state = (string) get_post_meta( $id, NWCS_FORM_NOTIFIED_META, true );

			if ( isset( $row[ $state ] ) ) {
				++$row[ $state ];
			}
		}

		if ( $ids ) {
			$row['last'] = wp_date( 'd.m.Y H:i', (int) get_post_timestamp( $ids[0] ) );
		}

		$stats[ $type ] = $row;
	}

	return $stats;
}

add_action( 'admin_post_nwcs_mail_test', 'nwcs_handle_mail_test' );
function nwcs_handle_mail_test(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) || ! check_admin_referer( 'nwcs_mail_test' ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.' ) );
	}

	$blog_id = isset( $_POST['site'] ) ? absint( $_POST['site'] ) : 0;
	$to      = isset( $_POST['to'] ) ? nwcs_form_clean_email( wp_unslash( $_POST['to'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- nwcs_form_clean_email temizler.

	if ( ! $blog_id || ! get_site( $blog_id ) ) {
		wp_die( esc_html__( 'Site bulunamadı.' ) );
	}

	switch_to_blog( $blog_id );

	$recipients = '' !== $to ? array( $to ) : nwcs_mailtest_recipients();
	$site_name  = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$error      = '';
	$sent       = false;

	if ( $recipients ) {
		$listen = static function ( WP_Error $wp_error ) use ( &$error ): void {
			$error = $wp_error->get_error_message();
		};

		add_action( 'wp_mail_failed', $listen );

		$body = implode(
			"\n",
			array(
				sprintf( 'Bu bir deneme e-postasıdır: %s (%s).', $site_name, home_url( '/' ) ),
				'',
				'Sitenin teklif ve iletişim formlarından gelen bildirimler bu adrese, bu yolla gönderilir.',
				'Bu e-posta Gelen Kutusu yerine Spam klasörüne düştüyse gerçek form bildirimleri de düşer.',
				'',
				'Gönderim zamanı: ' . wp_date( 'd.m.Y H:i:s' ),
				'Gönderen adres: ' . nwcs_mailtest_from(),
			)
		);

		try {
			$sent = (bool) wp_mail( $recipients, sprintf( '[Test] %s form bildirimi', $site_name ), $body . "\n", array( 'Content-Type: text/plain; charset=UTF-8' ) );
		} catch ( Throwable $e ) {
			$error = $e->getMessage();
		}

		remove_action( 'wp_mail_failed', $listen );
	} else {
		$error = 'Geçerli alıcı yok: SEO ve GEO sekmesinde firma e-postası boş, yönetici e-postası da geçersiz.';
	}

	restore_current_blog();

	set_site_transient(
		NWCS_MAILTEST_RESULT . '_' . get_current_user_id(),
		array(
			'site'  => $blog_id,
			'to'    => implode( ', ', $recipients ),
			'sent'  => $sent,
			'error' => $error,
			'time'  => time(),
		),
		HOUR_IN_SECONDS
	);

	wp_safe_redirect( add_query_arg( array( 'page' => NWCS_MAILTEST_SLUG ), network_admin_url( 'admin.php' ) ) . '#site-' . $blog_id );
	exit;
}

function nwcs_render_mailtest(): void {
	if ( ! current_user_can( NWCS_CAPABILITY ) ) {
		wp_die( esc_html__( 'Bu sayfaya erişim yetkiniz yok.' ) );
	}

	$result = get_site_transient( NWCS_MAILTEST_RESULT . '_' . get_current_user_id() );
	$smtp   = has_action( 'phpmailer_init' ) ? 'Gönderim ayarı değiştirilmiş (SMTP eklentisi ya da yerel ayar)' : 'PHP mail(): sunucunun kendi gönderimi, SMTP eklentisi yok';
	?>
	<div class="wrap nwcs-wrap nwcs-wrap--pool nwcs-seo">
		<header class="nwcs-bar">
			<div class="nwcs-bar__brand">
				<button type="button" class="nwcs-bar__mark" data-nwcs-menu aria-label="Yönetim menüsünü aç/kapat" title="Yönetim menüsünü aç/kapat"></button>
				<h1>E-posta Testi</h1>
			</div>
		</header>

		<section class="nwcs-pool__card">
			<p class="nwcs-seo__lead">
				Formlardan gelen teklif ve mesaj bildirimleri her sitenin <strong>SEO ve GEO → firma e-postası</strong> adresine gider.
				Aşağıdaki düğme, form doldurmadan aynı yolla bir deneme e-postası gönderir. E-posta gelmezse ya da Spam’e düşerse gerçek bildirimler de aynı yere düşer.
			</p>
			<p class="nwcs-seo__lead"><strong>Gönderim yolu:</strong> <?php echo esc_html( $smtp ); ?>.</p>

			<?php if ( is_array( $result ) ) : ?>
				<p class="nwcs-badge <?php echo $result['sent'] ? 'nwcs-badge--ok' : 'nwcs-badge--warn'; ?>">
					<?php
					echo esc_html(
						$result['sent']
							? sprintf( 'Sunucu e-postayı kabul etti: %s. Gelen Kutusu ve Spam klasörünü kontrol edin.', $result['to'] )
							: sprintf( 'Gönderilemedi (%s): %s', $result['to'] ?: 'alıcı yok', $result['error'] ?: 'wp_mail false döndü' )
					);
					?>
				</p>
			<?php endif; ?>

			<table class="widefat striped">
				<thead>
					<tr>
						<th>Site</th>
						<th>Bildirim alıcısı</th>
						<th>Gönderen adres</th>
						<th>Son form kayıtları (50)</th>
						<th>Deneme</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( nwcs_editable_sites() as $blog_id => $site ) : ?>
						<?php
						switch_to_blog( (int) $blog_id );
						$recipients = nwcs_mailtest_recipients();
						$from       = nwcs_mailtest_from();
						$stats      = nwcs_mailtest_form_stats();
						restore_current_blog();
						?>
						<tr id="site-<?php echo (int) $blog_id; ?>">
							<td><strong><?php echo esc_html( $site['label'] ); ?></strong><br /><span class="description"><?php echo esc_html( untrailingslashit( $site['url'] ) ); ?></span></td>
							<td><?php echo $recipients ? esc_html( implode( ', ', $recipients ) ) : '<em>yok</em>'; ?></td>
							<td><?php echo esc_html( $from ); ?></td>
							<td>
								<?php if ( ! $stats ) : ?>
									<em>Henüz form kaydı yok.</em>
								<?php else : ?>
									<?php foreach ( $stats as $type => $row ) : ?>
										<?php
										echo esc_html(
											sprintf(
												'%s: %d kayıt, %d gönderildi, %d gönderilemedi%s%s',
												$type,
												$row['total'],
												$row['sent'],
												$row['failed'],
												$row['pending'] ? ', ' . $row['pending'] . ' bekliyor' : '',
												'' !== $row['last'] ? ' (son: ' . $row['last'] . ')' : ''
											)
										);
										?>
										<br />
									<?php endforeach; ?>
								<?php endif; ?>
							</td>
							<td>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
									<input type="hidden" name="action" value="nwcs_mail_test" />
									<input type="hidden" name="site" value="<?php echo (int) $blog_id; ?>" />
									<?php wp_nonce_field( 'nwcs_mail_test' ); ?>
									<input type="email" name="to" placeholder="Başka adrese (isteğe bağlı)" style="width:100%;margin-bottom:6px" />
									<button type="submit" class="button">Test e-postası gönder</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</section>
	</div>
	<?php
}
