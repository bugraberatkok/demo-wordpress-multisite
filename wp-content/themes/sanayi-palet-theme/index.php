<?php
/**
 * Genel yedek sablon. Urunler, Hakkimizda, Blog ve Iletisim sayfalari
 * kendi tasarimlarini sonraki adimlarda alacak; o zamana kadar bu sade duzen.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$title = is_home() ? single_post_title( '', false ) : get_the_title();
?>
<section class="sp-wrap sp-section">
	<h1 class="sp-page__title" <?php is_singular() && sanayi_palet_post_attr( (int) get_queried_object_id(), 'Sayfa metni' ); ?>><?php echo esc_html( $title ?: get_bloginfo( 'name' ) ); ?></h1>

	<div class="sp-page__body" <?php is_singular() && sanayi_palet_post_attr( (int) get_queried_object_id(), 'Sayfa metni' ); ?>>
		<?php
		while ( have_posts() ) :
			the_post();

			if ( is_home() ) :
				?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php
				the_excerpt();
			else :
				the_content();
			endif;
		endwhile;
		?>
	</div>
</section>
<?php
get_footer();
