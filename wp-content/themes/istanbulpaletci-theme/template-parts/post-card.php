<?php
/**
 * Blog yazisi karti. Dongu icinde, gecerli yazi icin cagrilir.
 * One cikan gorsel yoksa kart yalnizca yaziyla durur; bos kutu basilmaz.
 *
 * Kartin tamami baglanti; bu yuzden panel isareti yalnizca baslikta:
 * onizlemede yazinin panel sayfasini acar, ozet ve kapak da orada.
 */

defined( 'ABSPATH' ) || exit;

$level   = $args['heading'] ?? 'h3';
$post_id = (int) get_the_ID();
?>
<a href="<?php the_permalink(); ?>" class="card card--link flex h-full flex-col overflow-hidden text-ink no-underline">

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="shot aspect-[16/10] border-b border-line">
			<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="flex flex-1 flex-col p-5 md:p-6">
		<time class="tabular text-sm text-steel" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
			<?php echo esc_html( get_the_date( 'j F Y' ) ); ?>
		</time>

		<<?php echo tag_escape( $level ); ?> class="mt-2 text-xl font-semibold" <?php ip_post_edit_attr( $post_id, 'title' ); ?>><?php the_title(); ?></<?php echo tag_escape( $level ); ?>>

		<p class="mt-3 text-[0.9375rem] leading-relaxed text-steel">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?>
		</p>

		<span class="mt-auto pt-5 font-semibold text-indigo" <?php nwcs_edit_attr( 'blog', 'head', 'read_label' ); ?>>
			<?php echo esc_html( nwcs_field( 'blog', 'head', 'read_label' ) ); ?>
		</span>
	</div>
</a>
