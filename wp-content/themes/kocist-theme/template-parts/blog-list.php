<?php
/**
 * Blog listesi: baslik bandi, kategori suzgeci, kart izgarasi, sayfalama.
 *
 * home.php (yazilar sayfasi) ve archive.php (kategori arsivi) ana sorguyla
 * cagirir. Okuma ayari degistirilip /blog/ normal sayfa olarak acilirsa
 * page.php kendi sorgusunu $args['query'] ile verir.
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$list_query = ( $args['query'] ?? null ) instanceof WP_Query ? $args['query'] : $wp_query;
$current    = is_category() ? get_queried_object() : null;
$blog_url   = kocist_blog_url();
$categories = get_categories(
	array(
		'hide_empty' => true,
		'exclude'    => array( (int) get_option( 'default_category' ) ),
		'orderby'    => 'name',
	)
);
$title      = $current ? $current->name : nwcs_field( 'blog', 'page_head', 'title' );
?>
<div class="k-pagehead" data-nwcs-section="page_head">
	<div class="k-wrap">
		<nav class="k-pagehead__crumb" aria-label="Konum">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Ana Sayfa</a>
			<span aria-hidden="true">/</span>
			<?php if ( $current ) : ?>
				<a href="<?php echo esc_url( $blog_url ); ?>"><?php echo esc_html( nwcs_field( 'blog', 'page_head', 'title' ) ); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php echo esc_html( $current->name ); ?></span>
			<?php else : ?>
				<span aria-current="page"><?php echo esc_html( nwcs_field( 'blog', 'page_head', 'title' ) ); ?></span>
			<?php endif; ?>
		</nav>
		<h1 class="k-pagehead__title" <?php nwcs_edit_attr( 'blog', 'page_head', 'title' ); ?>><?php echo esc_html( $title ); ?></h1>
		<p class="k-pagehead__sub" <?php nwcs_edit_attr( 'blog', 'page_head', 'subtitle' ); ?>><?php echo esc_html( nwcs_field( 'blog', 'page_head', 'subtitle' ) ); ?></p>
	</div>
</div>

<section class="k-section k-blog" data-nwcs-section="listing">
	<div class="k-wrap">
		<?php if ( $categories ) : ?>
			<nav class="k-blog-filter" aria-label="<?php echo esc_attr( nwcs_field( 'blog', 'listing', 'filter_label' ) ); ?>">
				<ul class="k-blog-filter__list">
					<li>
						<a class="k-blog-filter__chip<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url( $blog_url ); ?>" <?php echo $current ? '' : 'aria-current="page"'; ?>>
							<?php echo esc_html( nwcs_field( 'blog', 'listing', 'all_label' ) ); ?>
						</a>
					</li>
					<?php foreach ( $categories as $category ) : ?>
						<?php $is_active = $current && (int) $current->term_id === (int) $category->term_id; ?>
						<li>
							<a class="k-blog-filter__chip<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $category ) ); ?>" <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
								<?php echo esc_html( $category->name ); ?>
								<span class="k-blog-filter__count"><?php echo (int) $category->count; ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<?php if ( $list_query->have_posts() ) : ?>
			<div class="k-post-grid">
				<?php
				while ( $list_query->have_posts() ) :
					$list_query->the_post();
					kocist_post_card( 'h2' );
				endwhile;
				?>
			</div>

			<?php
			$pager = paginate_links(
				array(
					'total'     => (int) $list_query->max_num_pages,
					'current'   => max( 1, (int) get_query_var( 'paged' ) ),
					'prev_text' => 'Önceki',
					'next_text' => 'Sonraki',
					'type'      => 'list',
				)
			);

			if ( $pager ) :
				?>
				<nav class="k-pager" aria-label="Sayfalar"><?php echo wp_kses_post( $pager ); ?></nav>
			<?php endif; ?>
		<?php else : ?>
			<p class="k-blog__empty"><?php echo esc_html( nwcs_field( 'blog', 'listing', 'empty_text' ) ); ?></p>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
	</div>
</section>
