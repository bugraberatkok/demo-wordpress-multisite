<?php
/**
 * Ic sayfa basi: konum yolu, baslik, alt metin. Lacivert zemin, altinda
 * cetvel centikleri (metinsiz cetvel).
 *
 * $args: title, lead, page (manifest anahtari; duzenleme isaretleri icin),
 * crumbs (array of [label, url]).
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array( 'title' => '', 'lead' => '', 'page' => '', 'crumbs' => array() ) );
?>
<section class="pc-pagehead">
	<div class="pc-wrap">
		<nav class="pc-crumbs" aria-label="Konum">
			<ol>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Anasayfa</a></li>
				<?php foreach ( $args['crumbs'] as $crumb ) : ?>
					<li><a href="<?php echo esc_url( $crumb[1] ); ?>"><?php echo esc_html( $crumb[0] ); ?></a></li>
				<?php endforeach; ?>
				<li aria-current="page"><?php echo esc_html( wp_strip_all_tags( $args['title'] ) ); ?></li>
			</ol>
		</nav>
		<h1 class="pc-pagehead__title" <?php $args['page'] && nwcs_edit_attr( $args['page'], 'head', 'title' ); ?>><?php echo esc_html( $args['title'] ); ?></h1>
		<?php if ( '' !== $args['lead'] ) : ?>
			<p class="pc-pagehead__lead" <?php $args['page'] && nwcs_edit_attr( $args['page'], 'head', 'lead' ); ?>><?php echo esc_html( $args['lead'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php pc_part( 'ruler' ); ?>
