<?php
/**
 * Yedek sablon (arsivler, arama). Kategori ve etiket arsivleri eklentide
 * noindex; icerik blog karti olarak listelenir.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$term       = get_queried_object();
$title_edit = $term instanceof WP_Term
	? array( 'source', 'admin', admin_url( 'term.php?taxonomy=' . $term->taxonomy . '&tag_ID=' . $term->term_id ), 'category' === $term->taxonomy ? 'Blog kategorisi' : 'Etiket' )
	: null;
$title      = wp_strip_all_tags( get_the_archive_title() ?: get_bloginfo( 'name' ) );

// Arama sonuclari: WordPress'in genel arsiv basligi yerine paneldeki metin.
if ( is_search() ) {
	$title      = (string) nwcs_field( 'blog', 'list', 'search_title' );
	$title_edit = array( 'blog', 'list', 'search_title' );
}

pc_part(
	'page-head',
	array(
		'title'      => $title,
		'title_edit' => $title_edit,
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
			<p<?php nwcs_edit_attr( 'blog', 'list', 'archive_empty' ); ?>><?php echo esc_html( nwcs_field( 'blog', 'list', 'archive_empty' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
