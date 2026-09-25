<?php
/**
 * Banka Bilgilerimiz sayfasi govdesi: hesap kartlari ve odeme notu.
 *
 * IBAN dort haneli gruplar halinde gosterilir, kopyala dugmesi bosluksuz
 * halini kopyalar (assets/js/banka.js; betik yoksa dugme gizli kalir, IBAN
 * elle secilebilir). Panelde hesap girilmemisse ziyaretci hesap bilgisini
 * telefonla ya da WhatsApp'tan istemeye yonlendirilir.
 */

defined( 'ABSPATH' ) || exit;

$accounts = array_values(
	array_filter(
		nwcs_rows( 'banka', 'accounts', 'items' ),
		static function ( $row ): bool {
			return '' !== trim( (string) ( $row['bank'] ?? '' ) ) || '' !== trim( (string) ( $row['iban'] ?? '' ) );
		}
	)
);
$note = trim( (string) nwcs_field( 'banka', 'accounts', 'note' ) );
?>
<section class="k-section k-banka" data-k-banka data-nwcs-section="accounts">
	<div class="k-wrap k-banka__layout">

		<?php if ( $accounts ) : ?>
			<ul class="k-banka__list" <?php nwcs_edit_attr( 'banka', 'accounts', 'items' ); ?>>
				<?php
				foreach ( $accounts as $index => $row ) :
					$bank     = trim( (string) ( $row['bank'] ?? '' ) );
					$currency = trim( (string) ( $row['currency'] ?? '' ) );
					$iban_raw = strtoupper( preg_replace( '/\s+/', '', (string) ( $row['iban'] ?? '' ) ) );
					$iban     = trim( chunk_split( $iban_raw, 4, ' ' ) );
					$details  = array_filter(
						array(
							'Hesap sahibi' => trim( (string) ( $row['holder'] ?? '' ) ),
							'Şube'         => trim( (string) ( $row['branch'] ?? '' ) ),
							'Hesap no'     => trim( (string) ( $row['account'] ?? '' ) ),
						)
					);
					$iban_id  = 'k-iban-' . $index;
					?>
					<li class="k-bank" <?php nwcs_edit_attr( 'banka', 'accounts', 'items', $index, 'bank' ); ?>>
						<div class="k-bank__head">
							<h2 class="k-bank__name"><?php echo esc_html( $bank ); ?></h2>
							<?php if ( '' !== $currency ) : ?>
								<span class="k-bank__currency"><?php echo esc_html( $currency ); ?></span>
							<?php endif; ?>
						</div>

						<?php if ( '' !== $iban_raw ) : ?>
							<div class="k-bank__iban">
								<span class="k-bank__label" id="<?php echo esc_attr( $iban_id ); ?>-label">IBAN</span>
								<p class="k-bank__iban-value" id="<?php echo esc_attr( $iban_id ); ?>" aria-labelledby="<?php echo esc_attr( $iban_id ); ?>-label <?php echo esc_attr( $iban_id ); ?>"><?php echo esc_html( $iban ); ?></p>
								<button
									type="button"
									class="k-bank__copy"
									data-k-copy="<?php echo esc_attr( $iban_raw ); ?>"
									aria-describedby="<?php echo esc_attr( $iban_id ); ?>"
									hidden
								>
									<svg class="k-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><rect x="8" y="8" width="12" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="2" /><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2" fill="none" stroke="currentColor" stroke-width="2" /></svg>
									<span data-k-copy-text>IBAN'ı kopyala</span>
								</button>
							</div>
						<?php endif; ?>

						<?php if ( $details ) : ?>
							<dl class="k-bank__details">
								<?php foreach ( $details as $label => $value ) : ?>
									<div class="k-bank__row">
										<dt><?php echo esc_html( $label ); ?></dt>
										<dd><?php echo esc_html( $value ); ?></dd>
									</div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<?php
			$phone_url   = kocist_link( nwcs_field( 'global', 'topbar', 'phone_url' ) );
			$phone_label = nwcs_field( 'global', 'topbar', 'phone_label' );
			$wa_url      = kocist_link( nwcs_field( 'banka', 'empty', 'wa_url' ) );
			?>
			<div class="k-banka__empty" data-nwcs-section="empty">
				<h2 class="k-banka__empty-title" <?php nwcs_edit_attr( 'banka', 'empty', 'title' ); ?>><?php echo esc_html( nwcs_field( 'banka', 'empty', 'title' ) ); ?></h2>
				<p class="k-banka__empty-text" <?php nwcs_edit_attr( 'banka', 'empty', 'text' ); ?>><?php echo esc_html( nwcs_field( 'banka', 'empty', 'text' ) ); ?></p>
				<div class="k-banka__actions">
					<a class="k-btn k-btn--primary" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener" <?php nwcs_edit_attr( 'banka', 'empty', 'wa_label' ); ?>>
						<?php kocist_social_icon( 'whatsapp', 'WhatsApp', 'k-icon', 18 ); ?>
						<?php echo esc_html( nwcs_field( 'banka', 'empty', 'wa_label' ) ); ?>
					</a>
					<a class="k-btn k-btn--outline" href="<?php echo esc_url( $phone_url ); ?>">
						<?php nwcs_the_icon( 'phone', 'k-icon', 18 ); ?>
						<?php echo esc_html( $phone_label ); ?>
					</a>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $note ) : ?>
			<aside class="k-banka__note" aria-label="Ödeme notu">
				<svg class="k-banka__note-icon" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M12 3 4 6v6c0 4.4 3.4 8.1 8 9 4.6-.9 8-4.6 8-9V6z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" /><path d="m8.5 12 2.5 2.5 4.5-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
				<p <?php nwcs_edit_attr( 'banka', 'accounts', 'note' ); ?>><?php echo esc_html( $note ); ?></p>
			</aside>
		<?php endif; ?>

		<p class="screen-reader-text" role="status" data-k-copy-status></p>
	</div>
</section>
