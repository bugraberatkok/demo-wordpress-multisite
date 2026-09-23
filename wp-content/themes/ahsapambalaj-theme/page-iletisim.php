<?php
/**
 * Iletisim: telefon, adres, e-posta ve calisan bir teklif formu.
 *
 * Form sahte basari ekrani gostermez; gonderim gercekten kaydedilir ve
 * yonetimde "Teklif İstekleri" altinda gorunur.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$state   = ahsapambalaj_quote_state();
$errors  = $state['errors'];
$values  = $state['values'];
$success = $state['success'];

$products = nwcs_rows( 'services', 'grid', 'items' );

// Hizmetler sayfasindaki "bu urun icin teklif al" dugmesi urun adini adresle
// tasir. Hatali gonderimden sonra kullanicinin kendi secimi oncelikli.
$requested = isset( $_GET['urun'] ) ? sanitize_text_field( wp_unslash( $_GET['urun'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$picked    = '' !== ( $values['product'] ?? '' ) ? $values['product'] : $requested;

$field  = 'w-full rounded-sm border border-ink/18 bg-surface px-4 py-3 text-base text-ink transition-colors duration-150 placeholder:text-moss/70 focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/30';
$broken = 'w-full rounded-sm border border-alert bg-alert-soft px-4 py-3 text-base text-ink transition-colors duration-150 placeholder:text-moss/70 focus:border-alert focus:outline-none focus:ring-2 focus:ring-alert/30';
$label  = 'mb-2 block font-display text-sm font-medium text-ink/80';

$details = array(
	array( 'key' => 'phone',   'icon' => 'phone_icon',   'title' => 'phone_title',   'value' => 'phone_label',   'url' => 'phone_url' ),
	array( 'key' => 'email',   'icon' => 'email_icon',   'title' => 'email_title',   'value' => 'email_label',   'url' => 'email_url' ),
	array( 'key' => 'address', 'icon' => 'address_icon', 'title' => 'address_title', 'value' => 'address',       'url' => '' ),
	array( 'key' => 'hours',   'icon' => 'hours_icon',   'title' => 'hours_title',   'value' => 'hours',         'url' => '' ),
);
?>
<article>

	<?php // Baslik ustte ortada; altinda solda form, sagda iletisim bilgileri;
	      // en altta ortada aciklama cumlesi. ?>
	<header class="mx-auto max-w-[76rem] px-6 pt-14 text-center md:pt-20">
		<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] md:text-5xl" <?php nwcs_edit_attr( 'contact', 'head', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'contact', 'head', 'title' ) ); ?>
			</h1>
	</header>

	<div class="mx-auto grid max-w-[76rem] items-start gap-10 px-6 pt-12 md:pt-14 lg:grid-cols-[1.15fr_0.85fr] lg:gap-14">

		<section id="teklif" class="card scroll-mt-28 p-7 md:p-9">

			<?php if ( $success ) : ?>

				<div class="rounded-sm border border-forest/30 bg-forest/8 p-8 md:p-10" role="status">
					<span class="flex h-11 w-11 items-center justify-center rounded-full bg-forest text-bone" aria-hidden="true">
						<?php nwcs_the_icon( 'check', '', 22 ); ?>
					</span>

					<h2 class="mt-5 font-display text-2xl font-semibold" <?php nwcs_edit_attr( 'contact', 'form', 'success_title' ); ?>>
						<?php echo esc_html( nwcs_field( 'contact', 'form', 'success_title' ) ); ?>
					</h2>

					<p class="reading mt-3 text-base text-ink/80" <?php nwcs_edit_attr( 'contact', 'form', 'success_text' ); ?>>
						<?php echo esc_html( nwcs_field( 'contact', 'form', 'success_text' ) ); ?>
					</p>

					<a href="<?php echo esc_url( home_url( '/iletisim/' ) ); ?>"
						class="btn btn--sm btn--outline mt-7">
						Yeni bir istek gönderin
					</a>
				</div>

			<?php else : ?>

				<h2 class="text-center font-display text-2xl font-semibold md:text-3xl" <?php nwcs_edit_attr( 'contact', 'form', 'title' ); ?>>
					<?php echo esc_html( nwcs_field( 'contact', 'form', 'title' ) ); ?>
				</h2>

				<p class="mx-auto mt-3 max-w-[34rem] text-center text-base text-ink/75" <?php nwcs_edit_attr( 'contact', 'form', 'note' ); ?>>
					<?php echo esc_html( nwcs_field( 'contact', 'form', 'note' ) ); ?>
				</p>

				<?php if ( ! empty( $errors['form'] ) ) : ?>
					<p class="mt-6 rounded-sm border border-alert bg-alert-soft px-4 py-3 text-base text-alert" role="alert">
						<?php echo esc_html( $errors['form'] ); ?>
					</p>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mt-8 grid gap-6 sm:grid-cols-2" novalidate>
					<input type="hidden" name="action" value="ahsapambalaj_quote" />
					<?php wp_nonce_field( 'ahsapambalaj_quote', 'ahsapambalaj_quote_nonce' ); ?>

					<p class="hidden" aria-hidden="true">
						<label>Bu alanı boş bırakın
							<input type="text" name="ahsapambalaj_website" tabindex="-1" autocomplete="off" />
						</label>
					</p>

					<div>
						<label for="aas_name" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'name_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'name_label' ) ); ?>
						</label>
						<input type="text" id="aas_name" name="aas_name" required
							value="<?php echo esc_attr( $values['name'] ?? '' ); ?>"
							<?php echo isset( $errors['name'] ) ? 'aria-describedby="aas_name_err" aria-invalid="true"' : ''; ?>
							class="<?php echo esc_attr( isset( $errors['name'] ) ? $broken : $field ); ?>" />
						<?php if ( isset( $errors['name'] ) ) : ?>
							<p id="aas_name_err" class="mt-2 text-sm text-alert"><?php echo esc_html( $errors['name'] ); ?></p>
						<?php endif; ?>
					</div>

					<div>
						<label for="aas_company" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'company_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'company_label' ) ); ?>
						</label>
						<input type="text" id="aas_company" name="aas_company"
							value="<?php echo esc_attr( $values['company'] ?? '' ); ?>"
							class="<?php echo esc_attr( $field ); ?>" />
					</div>

					<div>
						<label for="aas_email" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'email_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'email_label' ) ); ?>
						</label>
						<input type="email" id="aas_email" name="aas_email" required
							value="<?php echo esc_attr( $values['email'] ?? '' ); ?>"
							<?php echo isset( $errors['email'] ) ? 'aria-describedby="aas_email_err" aria-invalid="true"' : ''; ?>
							class="<?php echo esc_attr( isset( $errors['email'] ) ? $broken : $field ); ?>" />
						<?php if ( isset( $errors['email'] ) ) : ?>
							<p id="aas_email_err" class="mt-2 text-sm text-alert"><?php echo esc_html( $errors['email'] ); ?></p>
						<?php endif; ?>
					</div>

					<div>
						<label for="aas_phone" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'phone_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'phone_label' ) ); ?>
						</label>
						<input type="tel" id="aas_phone" name="aas_phone"
							value="<?php echo esc_attr( $values['phone'] ?? '' ); ?>"
							class="<?php echo esc_attr( $field ); ?>" />
					</div>

					<div class="sm:col-span-2">
						<label for="aas_product" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'product_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'product_label' ) ); ?>
						</label>
						<select id="aas_product" name="aas_product" class="<?php echo esc_attr( $field ); ?>">
							<option value="">Seçin</option>
							<?php foreach ( $products as $product ) :
								$title = $product['title'] ?? '';
								?>
								<option value="<?php echo esc_attr( $title ); ?>" <?php selected( $picked, $title ); ?>>
									<?php echo esc_html( $title ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="sm:col-span-2">
						<label for="aas_size" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'size_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'size_label' ) ); ?>
						</label>
						<textarea id="aas_size" name="aas_size" rows="2" required
							placeholder="<?php echo esc_attr( nwcs_field( 'contact', 'form', 'size_hint' ) ); ?>"
							<?php echo isset( $errors['size'] ) ? 'aria-describedby="aas_size_err" aria-invalid="true"' : ''; ?>
							class="<?php echo esc_attr( isset( $errors['size'] ) ? $broken : $field ); ?>"><?php echo esc_textarea( $values['size'] ?? '' ); ?></textarea>
						<?php if ( isset( $errors['size'] ) ) : ?>
							<p id="aas_size_err" class="mt-2 text-sm text-alert"><?php echo esc_html( $errors['size'] ); ?></p>
						<?php endif; ?>
					</div>

					<div class="sm:col-span-2">
						<label for="aas_message" class="<?php echo esc_attr( $label ); ?>" <?php nwcs_edit_attr( 'contact', 'form', 'message_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'message_label' ) ); ?>
						</label>
						<textarea id="aas_message" name="aas_message" rows="4" class="<?php echo esc_attr( $field ); ?>"><?php echo esc_textarea( $values['message'] ?? '' ); ?></textarea>
					</div>

					<div class="flex flex-col gap-4 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
						<button type="submit"
							class="btn btn--md btn--solid shrink-0"
							<?php nwcs_edit_attr( 'contact', 'form', 'submit_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'submit_label' ) ); ?>
						</button>

						<p class="max-w-[22rem] text-sm text-moss" <?php nwcs_edit_attr( 'contact', 'form', 'privacy_note' ); ?>>
							<?php echo esc_html( nwcs_field( 'contact', 'form', 'privacy_note' ) ); ?>
						</p>
					</div>
				</form>

			<?php endif; ?>
		</section>

		<section aria-labelledby="iletisim-bilgileri">
			<h2 id="iletisim-bilgileri" class="sr-only">İletişim bilgileri</h2>

			<ul class="card px-6 py-2 md:px-7">
				<?php foreach ( $details as $row ) : ?>
					<li class="flex gap-4 border-b border-line py-5 last:border-0">
						<span class="mt-1 shrink-0 text-timber" aria-hidden="true">
							<?php nwcs_the_icon( nwcs_field( 'contact', 'details', $row['icon'] ), '', 22 ); ?>
						</span>

						<div>
							<h3 class="font-display text-sm font-semibold tracking-[0.04em] text-moss"
								<?php nwcs_edit_attr( 'contact', 'details', $row['title'] ); ?>>
								<?php echo esc_html( nwcs_field( 'contact', 'details', $row['title'] ) ); ?>
							</h3>

							<?php if ( $row['url'] ) : ?>
								<a href="<?php echo esc_url( ahsapambalaj_link( nwcs_field( 'contact', 'details', $row['url'] ) ) ); ?>"
									class="mt-1 block font-display text-lg font-medium text-ink transition-colors hover:text-forest"
									<?php nwcs_edit_attr( 'contact', 'details', $row['value'] ); ?>>
									<?php echo esc_html( nwcs_field( 'contact', 'details', $row['value'] ) ); ?>
								</a>
							<?php else : ?>
								<p class="mt-1 text-[1rem] leading-relaxed text-ink/85" <?php nwcs_edit_attr( 'contact', 'details', $row['value'] ); ?>>
									<?php echo ahsapambalaj_multiline( nwcs_field( 'contact', 'details', $row['value'] ) ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
								</p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

	</div>

	<div class="mx-auto max-w-[76rem] px-6 pt-14 md:pt-16">
		<p class="mx-auto max-w-[44rem] text-center text-xl leading-[1.55] md:text-2xl md:leading-[1.5]" <?php nwcs_edit_attr( 'contact', 'head', 'lead' ); ?>>
				<?php echo esc_html( nwcs_field( 'contact', 'head', 'lead' ) ); ?>
			</p>
	</div>
</article>
<?php
get_footer();
