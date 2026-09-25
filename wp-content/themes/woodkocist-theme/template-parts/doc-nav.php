<?php
/**
 * Kurumsal ve yasal sayfalarin yan listesi; bulunulan sayfa isaretli.
 */

defined( 'ABSPATH' ) || exit;

$current = get_queried_object_id();
$groups  = array(
	'Kurumsal'       => wk_corporate_pages(),
	'Çözüm Merkezi'  => wk_legal_pages(),
);
?>
<aside class="wk-doc__side">
	<?php foreach ( $groups as $title => $pages ) : ?>
		<?php if ( $pages ) : ?>
			<nav class="wk-docnav" aria-label="<?php echo esc_attr( $title ); ?>">
				<p class="wk-docnav__title"><?php echo esc_html( $title ); ?></p>
				<ul>
					<?php foreach ( $pages as $page ) : ?>
						<?php $is = get_page_by_path( $page['slug'] ); ?>
						<li><a href="<?php echo esc_url( $page['url'] ); ?>" <?php echo $is && $is->ID === $current ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $page['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	<?php endforeach; ?>
</aside>
