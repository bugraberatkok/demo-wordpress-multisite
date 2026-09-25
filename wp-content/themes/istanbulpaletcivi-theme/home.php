<?php
/**
 * Blog ve Haberler listesi (/haberler-blog/, yazilar sayfasi).
 */

defined( 'ABSPATH' ) || exit;

get_header();

pc_part(
	'page-head',
	array(
		'title' => (string) nwcs_field( 'blog', 'head', 'title' ),
		'lead'  => (string) nwcs_field( 'blog', 'head', 'lead' ),
		'page'  => 'blog',
	)
);
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
			<?php pc_posts_pagination(); ?>
		<?php else : ?>
			<p<?php nwcs_edit_attr( 'blog', 'list', 'empty' ); ?>><?php echo esc_html( nwcs_field( 'blog', 'list', 'empty' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
