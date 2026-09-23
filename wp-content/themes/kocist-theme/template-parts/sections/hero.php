<?php
/**
 * Ana sayfa hero bolumu.
 *
 * Iki kart: solda buyuk sabit kart, sagda ok tuslariyla ilerleyen slayt.
 * Altinda guven seridi. Referans tuin.co.uk; davranis assets/js/hero.js,
 * gorunum assets/css/hero.css.
 */

defined( 'ABSPATH' ) || exit;

$image  = kocist_image_or_default( nwcs_image( 'home', 'hero', 'image', 'full' ), 'atolye.jpg', 'Ahşap atölyesinde el aletleri ve talaş' );

// Slayt gorseli panelde secilmemisse sirayla temadaki urun fotograflari.
$slide_defaults = array( 'ahsap-kamelya-3x3-zeminli.webp', 'playwood.webp', 'ahsap-salincak-4-kisilik-oval-golgelikli.webp' );
$slides = nwcs_rows( 'home', 'hero', 'slides' );
$trust  = nwcs_rows( 'home', 'hero', 'trust' );
?>
<section class="k-hero" id="hero" data-nwcs-section="hero">
	<div class="k-wrap">
		<div class="k-hero__grid">

			<?php
			/*
			 * Sol kart: atolye fotografi + ortasina oturan metin. Fotografin
			 * ortasi bos acik ahsap oldugu icin yazi koyu yesil basiliyor.
			 */
			?>
			<article class="k-hero__feature" <?php nwcs_edit_attr( 'home', 'hero', 'image' ); ?>>
				<?php echo kocist_image_tag( $image, 'k-hero__img', 'Örnek görsel — Koçist atölye' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>

				<div class="k-hero__feature-body">
					<?php
					/*
					 * Logo, ust menudekiyle ayni alandan okunuyor: marka gorseli
					 * tek yerden yonetilsin diye hero'ya ayri bir alan acilmadi.
					 *
					 * Slogan logonun arkasindan cikarak saga aciliyor; bunun icin
					 * baslik kirpma maskesi gorevi goruyor, ic span oteleniyor.
					 */
					$brand = kocist_image_or_default( nwcs_image( 'global', 'header', 'logo_image', 'medium' ), 'logo.png', 'Koçist Orman Ürünleri logosu' );
					?>
					<div class="k-hero__brand">
						<?php if ( ! empty( $brand['url'] ) ) : ?>
							<img
								class="k-hero__logo"
								src="<?php echo esc_url( $brand['url'] ); ?>"
								alt="<?php echo esc_attr( $brand['alt'] ); ?>"
							/>
						<?php endif; ?>

						<h1 class="k-hero__title">
							<span class="k-hero__title-inner" <?php nwcs_edit_attr( 'home', 'hero', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'hero', 'title' ) ); ?></span>
						</h1>
					</div>

					<?php if ( nwcs_field( 'home', 'hero', 'cta_label' ) ) : ?>
						<a class="k-hero__btn k-hero__btn--dark" href="<?php echo esc_url( kocist_link( nwcs_field( 'home', 'hero', 'cta_url' ) ) ); ?>" <?php nwcs_edit_attr( 'home', 'hero', 'cta_label' ); ?>>
							<?php echo esc_html( nwcs_field( 'home', 'hero', 'cta_label' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			</article>

			<?php if ( $slides ) : ?>
				<article class="k-hero__slider" data-k-slider aria-roledescription="carousel" aria-label="Öne çıkan ürünler">
					<div class="k-hero__track" data-k-slider-track>
						<?php foreach ( $slides as $slide_index => $slide ) : ?>
							<?php $slide_image = kocist_image_or_default( nwcs_image_by_id( (int) ( $slide['image'] ?? 0 ), 'large' ), $slide_defaults[ $slide_index ] ?? '', (string) ( $slide['title'] ?? '' ) ); ?>
							<div
								class="k-hero__slide<?php echo 0 === $slide_index ? ' is-active' : ''; ?>"
								data-k-slide
								role="group"
								aria-roledescription="slide"
								aria-label="<?php echo esc_attr( sprintf( '%d / %d', $slide_index + 1, count( $slides ) ) ); ?>"
								<?php echo 0 === $slide_index ? '' : 'aria-hidden="true"'; ?>
							>
								<div class="k-hero__slide-media" <?php nwcs_edit_attr( 'home', 'hero', 'slides', $slide_index, 'image' ); ?>>
									<?php echo kocist_image_tag( $slide_image, 'k-hero__slide-img', 'Örnek görsel — slayt' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
								</div>

								<div class="k-hero__slide-body">
									<h2 class="k-hero__slide-title" <?php nwcs_edit_attr( 'home', 'hero', 'slides', $slide_index, 'title' ); ?>><?php echo esc_html( $slide['title'] ?? '' ); ?></h2>
									<p class="k-hero__slide-text" <?php nwcs_edit_attr( 'home', 'hero', 'slides', $slide_index, 'text' ); ?>><?php echo esc_html( $slide['text'] ?? '' ); ?></p>

									<?php if ( ! empty( $slide['cta_label'] ) ) : ?>
										<a class="k-hero__btn" href="<?php echo esc_url( kocist_link( $slide['cta_url'] ?? '', '/#katalog' ) ); ?>">
											<?php echo esc_html( $slide['cta_label'] ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<?php if ( count( $slides ) > 1 ) : ?>
						<button type="button" class="k-hero__arrow k-hero__arrow--prev" data-k-slider-prev>
							<span class="screen-reader-text">Önceki slayt</span>
							<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
								<path d="M11 3.5 5.5 9l5.5 5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>

						<button type="button" class="k-hero__arrow k-hero__arrow--next" data-k-slider-next>
							<span class="screen-reader-text">Sonraki slayt</span>
							<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
								<path d="M7 3.5 12.5 9 7 14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</button>

						<div class="k-hero__dots" data-k-slider-dots>
							<?php foreach ( $slides as $dot_index => $dot ) : ?>
								<button
									type="button"
									class="k-hero__dot<?php echo 0 === $dot_index ? ' is-active' : ''; ?>"
									data-k-slider-dot="<?php echo (int) $dot_index; ?>"
									aria-label="<?php echo esc_attr( sprintf( '%d. slayta git', $dot_index + 1 ) ); ?>"
								></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</article>
			<?php endif; ?>
		</div>

		<?php if ( $trust ) : ?>
			<ul class="k-trust">
				<?php foreach ( $trust as $trust_index => $entry ) : ?>
					<li class="k-trust__item" <?php nwcs_edit_attr( 'home', 'hero', 'trust', $trust_index, 'label' ); ?>>
						<?php nwcs_the_icon( (string) ( $entry['icon'] ?? '' ), 'k-trust__icon', 22 ); ?>
						<span><?php echo esc_html( $entry['label'] ?? '' ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
