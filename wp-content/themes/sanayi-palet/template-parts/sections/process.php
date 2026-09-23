<?php
/**
 * Siparis sureci: solda adim adim akis (gercek bir sira oldugu icin
 * numarali, adimlar dikey bir hatla bagli), sagda teklif icin gerekenler.
 *
 * Sure alani bossa basilmaz; firmanin onaylamadigi bir soz verilmesin.
 */

defined( 'ABSPATH' ) || exit;

$whatsapp = nwcs_field( 'home', 'process', 'whatsapp_url' );
?>
<section class="sp-section sp-process" data-nwcs-section="process">
	<div class="sp-wrap">
		<header class="sp-section__head">
			<h2 class="sp-title" <?php nwcs_edit_attr( 'home', 'process', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'process', 'title' ) ); ?></h2>
			<p class="sp-lead" <?php nwcs_edit_attr( 'home', 'process', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'process', 'text' ) ); ?></p>
		</header>

		<div class="sp-process__grid">
			<ol class="sp-process__steps" <?php nwcs_edit_attr( 'home', 'process', 'steps' ); ?>>
				<?php foreach ( nwcs_rows( 'home', 'process', 'steps' ) as $index => $step ) : ?>
					<li class="sp-process__step">
						<span class="sp-process__marker" aria-hidden="true">
							<?php sanayi_palet_icon( (string) ( $step['icon'] ?? '' ), 26 ); ?>
						</span>

						<div class="sp-process__body">
							<p class="sp-process__meta">
								<span class="sp-process__number"><?php echo esc_html( sprintf( '%d. adım', $index + 1 ) ); ?></span>
								<?php if ( ! empty( $step['actor'] ) ) : ?>
									<?php // "Siz..." ile baslayan etiket musteriye, digerleri firmaya ait; renkle ayrilir. ?>
									<?php $is_customer = str_starts_with( mb_strtolower( trim( $step['actor'] ) ), 'siz' ); ?>
									<span class="sp-process__actor sp-process__actor--<?php echo $is_customer ? 'siz' : 'biz'; ?>" <?php nwcs_edit_attr( 'home', 'process', 'steps', $index, 'actor' ); ?>><?php echo esc_html( $step['actor'] ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $step['duration'] ) ) : ?>
									<span class="sp-process__duration" <?php nwcs_edit_attr( 'home', 'process', 'steps', $index, 'duration' ); ?>>
										<?php sanayi_palet_icon( 'clock', 16 ); ?><?php echo esc_html( $step['duration'] ); ?>
									</span>
								<?php endif; ?>
							</p>
							<h3 class="sp-process__name" <?php nwcs_edit_attr( 'home', 'process', 'steps', $index, 'title' ); ?>><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
							<p class="sp-process__text" <?php nwcs_edit_attr( 'home', 'process', 'steps', $index, 'text' ); ?>><?php echo esc_html( $step['text'] ?? '' ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>

			<aside class="sp-checklist" aria-labelledby="sp-checklist-title">
				<h3 class="sp-checklist__title" id="sp-checklist-title" <?php nwcs_edit_attr( 'home', 'process', 'checklist_title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'process', 'checklist_title' ) ); ?></h3>

				<ul class="sp-checklist__list" <?php nwcs_edit_attr( 'home', 'process', 'checklist' ); ?>>
					<?php foreach ( nwcs_rows( 'home', 'process', 'checklist' ) as $row ) : ?>
						<li class="sp-checklist__item">
							<?php sanayi_palet_icon( 'check', 22, 'sp-icon sp-checklist__tick' ); ?>
							<span>
								<span class="sp-checklist__label"><?php echo esc_html( $row['item'] ?? '' ); ?></span>
								<?php if ( ! empty( $row['hint'] ) ) : ?>
									<span class="sp-checklist__hint"><?php echo esc_html( $row['hint'] ); ?></span>
								<?php endif; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>

				<div class="sp-checklist__actions">
					<a class="btn btn--solid" href="<?php echo esc_url( sanayi_palet_link( nwcs_field( 'home', 'process', 'button_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'process', 'button_label' ); ?>>
						<?php echo esc_html( nwcs_field( 'home', 'process', 'button_label' ) ); ?>
					</a>
					<?php if ( '' !== trim( $whatsapp ) ) : ?>
						<a class="btn btn--outline sp-checklist__whatsapp" href="<?php echo esc_url( sanayi_palet_link( $whatsapp ) ); ?>" <?php nwcs_edit_attr( 'home', 'process', 'whatsapp_label' ); ?>>
							<?php sanayi_palet_icon( 'whatsapp', 18 ); ?><?php echo esc_html( nwcs_field( 'home', 'process', 'whatsapp_label' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			</aside>
		</div>
	</div>
</section>
