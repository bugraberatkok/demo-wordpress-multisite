<?php
/**
 * Blog: yazi listesi. Yazilarin kendisi WordPress yazilaridir; sayfa basligi
 * Icerik Studyosu'ndan (Blog -> Sayfa Basligi) gelir.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<article>

	<?php get_template_part( 'template-parts/page-head', null, array( 'page' => 'blog' ) ); ?>

	<div class="mx-auto max-w-[80rem] px-5 pt-12 md:px-8 md:pt-16">
		<?php if ( have_posts() ) : ?>
			<ul class="grid gap-5 md:grid-cols-2 lg:grid-cols-3 lg:gap-6">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li><?php get_template_part( 'template-parts/post-card', null, array( 'heading' => 'h2' ) ); ?></li>
				<?php endwhile; ?>
			</ul>

			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 1,
					'prev_text'          => 'Önceki',
					'next_text'          => 'Sonraki',
					'screen_reader_text' => 'Sayfalar',
					'class'              => 'ip-pagination mt-12',
				)
			);
			?>
		<?php else : ?>
			<p class="text-lg text-steel">Henüz yazı yok.</p>
		<?php endif; ?>
	</div>
</article>
<?php
get_footer();
