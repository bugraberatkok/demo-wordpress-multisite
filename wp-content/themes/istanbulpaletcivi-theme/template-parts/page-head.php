<?php
/**
 * Ic sayfa basi: konum yolu, baslik, alt metin. Lacivert zemin, altinda
 * cetvel centikleri (metinsiz cetvel).
 *
 * $args: title, lead, page (manifest anahtari; duzenleme isaretleri icin),
 * crumbs (array of [label, url, isaret?]), title_edit / lead_edit (pc_edit()
 * isaret tanimi; verilmezse page.head.title / page.head.lead).
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array( 'title' => '', 'lead' => '', 'page' => '', 'crumbs' => array(), 'title_edit' => null, 'lead_edit' => null ) );

$title_edit = $args['title_edit'] ?? ( $args['page'] ? array( $args['page'], 'head', 'title' ) : null );
$lead_edit  = $args['lead_edit'] ?? ( $args['page'] ? array( $args['page'], 'head', 'lead' ) : null );
?>
<section class="pc-pagehead">
	<div class="pc-wrap">
		<nav class="pc-crumbs" aria-label="Konum">
			<ol>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php nwcs_edit_attr( 'global', 'common', 'crumb_home' ); ?>><?php echo esc_html( nwcs_field( 'global', 'common', 'crumb_home' ) ); ?></a></li>
				<?php foreach ( $args['crumbs'] as $crumb ) : ?>
					<li><a href="<?php echo esc_url( $crumb[1] ); ?>"<?php pc_edit( $crumb[2] ?? null ); ?>><?php echo esc_html( $crumb[0] ); ?></a></li>
				<?php endforeach; ?>
				<li aria-current="page"<?php pc_edit( $title_edit ); ?>><?php echo esc_html( wp_strip_all_tags( $args['title'] ) ); ?></li>
			</ol>
		</nav>
		<h1 class="pc-pagehead__title" <?php pc_edit( $title_edit ); ?>><?php echo esc_html( $args['title'] ); ?></h1>
		<?php if ( '' !== $args['lead'] ) : ?>
			<p class="pc-pagehead__lead" <?php pc_edit( $lead_edit ); ?>><?php echo esc_html( $args['lead'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php pc_part( 'ruler' ); ?>
