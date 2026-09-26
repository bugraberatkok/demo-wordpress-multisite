<?php
/**
 * Hakkimizda. Ana sayfadakiyle ayni kurguda bir hero (fotograf + koyu perde),
 * altinda kisa firma metni ve Amacimiz / Kalite Politikamiz kutulari.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$image  = ahsapambalaj_image( nwcs_image( 'about', 'head', 'image', 'full' ), 'about' );
$story  = array( 'p1', 'p2' );
$blocks = array( 'purpose', 'quality' );
?>
<article>

	<section class="relative isolate flex min-h-[24rem] items-center justify-center overflow-hidden md:min-h-[30rem]" <?php nwcs_edit_attr( 'about', 'head', 'image' ); ?>>

		<?php if ( ! empty( $image['url'] ) ) : ?>
			<img src="<?php echo esc_url( $image['url'] ); ?>"
				alt="<?php echo esc_attr( $image['alt'] ?: '' ); ?>"
				class="absolute inset-0 -z-20 h-full w-full object-cover"
				fetchpriority="high" decoding="async" />
		<?php else : ?>
			<span class="absolute inset-0 -z-20 bg-ink" aria-hidden="true"></span>
		<?php endif; ?>

		<span class="absolute inset-0 -z-10 bg-night/66" aria-hidden="true"></span>
		<span class="absolute inset-0 -z-10 bg-gradient-to-b from-night/55 via-night/20 to-night/75" aria-hidden="true"></span>

		<div class="mx-auto w-full max-w-[52rem] px-6 py-20 text-center" <?php nwcs_edit_attr( 'about', 'head', 'image' ); ?>>
			<h1 class="font-display text-[2.5rem] font-semibold leading-[1.06] text-bone md:text-5xl"
				<?php nwcs_edit_attr( 'about', 'head', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'about', 'head', 'title' ) ); ?>
			</h1>

			<p class="mx-auto mt-7 max-w-[46rem] text-lg leading-[1.6] text-bone/90 md:text-xl"
				<?php nwcs_edit_attr( 'about', 'head', 'lead' ); ?>>
				<?php echo esc_html( nwcs_field( 'about', 'head', 'lead' ) ); ?>
			</p>
		</div>
	</section>

	<?php // Basliksiz birakildiginda bu blok ne hero'ya ne alttaki kutulara
	      // ait gorunuyordu; kendi basligiyla ayri bir bolum oldu. ?>
	<section class="mx-auto max-w-[76rem] px-6 pt-16 md:pt-20">

		<div class="mx-auto max-w-[66rem] text-center" data-reveal>
			<h2 class="font-display text-2xl font-semibold md:text-3xl" <?php nwcs_edit_attr( 'about', 'story', 'title' ); ?>>
				<?php echo esc_html( nwcs_field( 'about', 'story', 'title' ) ); ?>
			</h2>
			<p class="mt-3 text-base text-moss" <?php nwcs_edit_attr( 'about', 'story', 'lead' ); ?>>
				<?php echo esc_html( nwcs_field( 'about', 'story', 'lead' ) ); ?>
			</p>
		</div>

		<div class="card mx-auto mt-8 grid max-w-[66rem] gap-8 px-8 py-10 md:grid-cols-2 md:gap-12 md:px-14 md:py-12" data-reveal>
			<?php foreach ( $story as $key ) : ?>
				<p class="text-[1.0625rem] leading-[1.75] text-ink/85" <?php nwcs_edit_attr( 'about', 'story', $key ); ?>>
					<?php echo esc_html( nwcs_field( 'about', 'story', $key ) ); ?>
				</p>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="mx-auto max-w-[76rem] px-6 pt-10 md:pt-14">
		<div class="mx-auto grid max-w-[66rem] gap-6 md:grid-cols-2 md:gap-8" data-reveal-stagger>
			<?php foreach ( $blocks as $component ) : ?>
				<div class="panel flex h-full flex-col p-7 md:p-9" data-reveal>
					<h2 class="font-display text-xl font-semibold md:text-2xl" <?php nwcs_edit_attr( 'about', $component, 'title' ); ?>>
						<?php echo esc_html( nwcs_field( 'about', $component, 'title' ) ); ?>
					</h2>

					<p class="mt-4 text-[1.0625rem] leading-[1.7] text-ink/80" <?php nwcs_edit_attr( 'about', $component, 'text' ); ?>>
						<?php echo esc_html( nwcs_field( 'about', $component, 'text' ) ); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
</article>
<?php
get_footer();
