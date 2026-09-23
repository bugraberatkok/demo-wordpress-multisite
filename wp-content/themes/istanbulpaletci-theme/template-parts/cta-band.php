<?php
/**
 * Teklif seridi: sayfa sonundaki eylem cagrisi.
 * $args: page, component (alan adlari: title, text (istege bagli),
 * button_label, button_url).
 *
 * Zemin indigo'nun en acik tonu: eylem rengi ailesinden, ama hero'daki
 * dolu indigo alanla yarismayacak kadar sessiz.
 */

defined( 'ABSPATH' ) || exit;

$page      = $args['page'];
$component = $args['component'];
$text      = trim( (string) nwcs_field( $page, $component, 'text' ) );
?>
<section class="mx-auto max-w-[80rem] px-5 pt-24 md:px-8 md:pt-32">
	<div class="flex flex-col gap-8 rounded-[4px] bg-indigo-wash px-6 py-10 md:flex-row md:items-center md:justify-between md:px-12 md:py-12">
		<div class="max-w-[40rem]">
			<h2 class="text-[2rem] font-semibold text-indigo-deep md:text-[2.5rem]" <?php nwcs_edit_attr( $page, $component, 'title' ); ?>>
				<?php echo esc_html( nwcs_field( $page, $component, 'title' ) ); ?>
			</h2>

			<?php if ( $text ) : ?>
				<p class="mt-3 text-lg leading-relaxed text-ink/80" <?php nwcs_edit_attr( $page, $component, 'text' ); ?>>
					<?php echo esc_html( $text ); ?>
				</p>
			<?php endif; ?>
		</div>

		<a href="<?php echo esc_url( ip_link( nwcs_field( $page, $component, 'button_url' ) ) ); ?>"
			class="btn btn--lg btn--solid shrink-0 self-start md:self-auto"
			<?php nwcs_edit_attr( $page, $component, 'button_label' ); ?>>
			<?php echo esc_html( nwcs_field( $page, $component, 'button_label' ) ); ?>
		</a>
	</div>
</section>
