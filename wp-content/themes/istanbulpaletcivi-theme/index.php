<?php
/**
 * Yedek sablon (arsivler, arama). Kategori ve etiket arsivleri eklentide
 * noindex; icerik blog karti olarak listelenir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

pc_part( 'page-head', array( 'title' => wp_strip_all_tags( get_the_archive_title() ?: get_bloginfo( 'name' ) ) ) );
?>

<section class="pc-section">
	<div class="pc-wrap">
		<?php if ( have_posts() ) : ?>
			<div class="pc-posts">
				<?php
				while ( have_posts() ) :
					the_post();
					pc_part( 'post-card' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => 'Önceki', 'next_text' => 'Sonraki' ) ); ?>
		<?php else : ?>
			<p>Bu sayfada içerik yok.</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
