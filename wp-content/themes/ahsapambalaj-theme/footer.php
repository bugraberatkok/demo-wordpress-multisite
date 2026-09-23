<?php
/**
 * Alt bilgi. Tum sayfalarda aynidir; icerik global.footer bileseninden gelir.
 */

defined( 'ABSPATH' ) || exit;

$menu = nwcs_rows( 'global', 'header', 'menu' );
?>
</main>

<footer class="mt-24 bg-night text-bone/85">
	<div class="mx-auto max-w-[76rem] px-6 py-16">

		<div class="grid gap-12 md:grid-cols-[1.4fr_1fr_1fr]">

			<div>
				<p class="stencil-mark text-[1.5rem] text-bone">
					<span><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_text' ) ); ?></span>
					<span class="stencil-mark__sub text-timber-soft"><?php echo esc_html( nwcs_field( 'global', 'header', 'logo_sub' ) ); ?></span>
				</p>
				<p class="reading mt-4 text-base/relaxed text-bone/70" <?php nwcs_edit_attr( 'global', 'footer', 'tagline' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'footer', 'tagline' ) ); ?>
				</p>

				<p class="mt-8 text-sm text-bone/60">
					<span <?php nwcs_edit_attr( 'global', 'footer', 'external_note' ); ?>><?php echo esc_html( nwcs_field( 'global', 'footer', 'external_note' ) ); ?></span>
					<a href="<?php echo esc_url( ahsapambalaj_link( nwcs_field( 'global', 'footer', 'external_url' ) ) ); ?>"
						class="border-b border-timber/60 pb-0.5 font-display text-timber-soft transition-colors hover:border-timber-soft hover:text-bone"
						<?php nwcs_edit_attr( 'global', 'footer', 'external_label' ); ?>>
						<?php echo esc_html( nwcs_field( 'global', 'footer', 'external_label' ) ); ?>
					</a>
				</p>
			</div>

			<div>
				<h2 class="font-display text-sm font-semibold tracking-[0.12em] text-bone/55" <?php nwcs_edit_attr( 'global', 'footer', 'nav_title' ); ?>>
					<?php echo esc_html( nwcs_field( 'global', 'footer', 'nav_title' ) ); ?>
				</h2>
				<ul class="mt-5 space-y-3">
					<?php foreach ( $menu as $item ) : ?>
						<li>
							<a href="<?php echo esc_url( ahsapambalaj_link( $item['url'] ?? '' ) ); ?>"
								class="font-display text-base text-bone/80 transition-colors hover:text-bone">
								<?php echo esc_html( $item['label'] ?? '' ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h2 class="font-display text-sm font-semibold tracking-[0.12em] text-bone/55">İletişim</h2>
				<ul class="footer-contact mt-5 space-y-3 text-base">
					<li>
						<a href="<?php echo esc_url( ahsapambalaj_link( nwcs_field( 'global', 'footer', 'phone_url' ) ) ); ?>"
							class="font-display text-bone transition-colors hover:text-timber-soft"
							<?php nwcs_edit_attr( 'global', 'footer', 'phone_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'global', 'footer', 'phone_label' ) ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( ahsapambalaj_link( nwcs_field( 'global', 'footer', 'email_url' ) ) ); ?>"
							class="font-display text-bone/80 transition-colors hover:text-timber-soft"
							<?php nwcs_edit_attr( 'global', 'footer', 'email_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'global', 'footer', 'email_label' ) ); ?>
						</a>
					</li>
					<li class="pt-2 text-base/relaxed text-bone/65" <?php nwcs_edit_attr( 'global', 'footer', 'address' ); ?>>
						<?php echo ahsapambalaj_multiline( nwcs_field( 'global', 'footer', 'address' ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
					</li>
				</ul>
			</div>
		</div>

		<div class="mt-14 flex flex-col gap-2 border-t border-bone/12 pt-6 text-sm text-bone/50 sm:flex-row sm:items-center sm:justify-between">
			<p <?php nwcs_edit_attr( 'global', 'footer', 'copyright' ); ?>>
				© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( nwcs_field( 'global', 'footer', 'copyright' ) ); ?>. Tüm hakları saklıdır.
			</p>
			<p class="font-display tracking-[0.1em]">ahsapambalajsanayi.com</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
