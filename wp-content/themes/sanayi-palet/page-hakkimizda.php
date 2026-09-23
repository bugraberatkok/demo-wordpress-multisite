<?php
/**
 * Hakkimizda: baslik + atolye fotografi, firma metni, ilkeler,
 * Kocist Grup ve ISPM 15 belgesi, teklif seridi.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$head_image = sanayi_palet_image( nwcs_image( 'about', 'head', 'image', 'large' ), 'about_head' );
$doc        = sanayi_palet_image( nwcs_image( 'about', 'group', 'image', 'large' ), 'certificate' );
$doc_full   = sanayi_palet_image( nwcs_image( 'about', 'group', 'image', 'full' ), 'certificate' );
?>

<header class="sp-abouthead">
	<div class="sp-wrap sp-abouthead__grid">
		<div class="sp-abouthead__copy">
			<p class="sp-abouthead__kicker" <?php nwcs_edit_attr( 'about', 'head', 'kicker' ); ?>><?php echo esc_html( nwcs_field( 'about', 'head', 'kicker' ) ); ?></p>
			<h1 class="sp-abouthead__title" <?php nwcs_edit_attr( 'about', 'head', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'head', 'title' ) ); ?></h1>
		</div>
		<div class="sp-abouthead__media" <?php nwcs_edit_attr( 'about', 'head', 'image' ); ?>>
			<?php echo sanayi_palet_image_tag( $head_image, 'sp-abouthead__image' ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
		</div>
	</div>
</header>

<section class="sp-section sp-story">
	<div class="sp-wrap sp-story__grid">
		<p class="sp-story__lead" <?php nwcs_edit_attr( 'about', 'story', 'lead' ); ?>><?php echo esc_html( nwcs_field( 'about', 'story', 'lead' ) ); ?></p>
		<div class="sp-story__body" <?php nwcs_edit_attr( 'about', 'story', 'body' ); ?>>
			<?php sanayi_palet_paragraphs( nwcs_field( 'about', 'story', 'body' ) ); ?>
		</div>
	</div>
</section>

<section class="sp-section sp-values">
	<div class="sp-wrap">
		<h2 class="sp-title sp-values__title" <?php nwcs_edit_attr( 'about', 'values', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'values', 'title' ) ); ?></h2>

		<ul class="sp-values__list" <?php nwcs_edit_attr( 'about', 'values', 'items' ); ?>>
			<?php foreach ( nwcs_rows( 'about', 'values', 'items' ) as $index => $item ) : ?>
				<li class="sp-values__item">
					<span class="sp-intro__icon" <?php nwcs_edit_attr( 'about', 'values', 'items', $index, 'icon' ); ?>><?php sanayi_palet_icon( (string) ( $item['icon'] ?? '' ), 28 ); ?></span>
					<h3 class="sp-values__name" <?php nwcs_edit_attr( 'about', 'values', 'items', $index, 'title' ); ?>><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
					<p class="sp-values__text" <?php nwcs_edit_attr( 'about', 'values', 'items', $index, 'text' ); ?>><?php echo esc_html( $item['text'] ?? '' ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="sp-section sp-aboutgroup">
	<div class="sp-wrap sp-aboutgroup__grid">
		<div>
			<h2 class="sp-title" <?php nwcs_edit_attr( 'about', 'group', 'title' ); ?>><?php echo esc_html( nwcs_field( 'about', 'group', 'title' ) ); ?></h2>
			<p class="sp-aboutgroup__text" <?php nwcs_edit_attr( 'about', 'group', 'text' ); ?>><?php echo esc_html( nwcs_field( 'about', 'group', 'text' ) ); ?></p>
			<?php sanayi_palet_stamp( nwcs_field( 'home', 'hero', 'stamp_code' ), nwcs_field( 'home', 'hero', 'stamp_standard' ), 'sp-stamp--small' ); ?>
		</div>

		<figure class="sp-aboutgroup__doc" <?php nwcs_edit_attr( 'about', 'group', 'image' ); ?>>
			<?php if ( ! empty( $doc['url'] ) ) : ?>
				<a class="sp-cert__doc-link" href="<?php echo esc_url( $doc_full['url'] ); ?>">
					<img src="<?php echo esc_url( $doc['url'] ); ?>" alt="<?php echo esc_attr( $doc['alt'] ); ?>" loading="lazy" decoding="async" />
					<span class="screen-reader-text">Belgeyi büyük boyutta açın</span>
				</a>
			<?php endif; ?>
			<figcaption class="sp-aboutgroup__caption" <?php nwcs_edit_attr( 'about', 'group', 'image_note' ); ?>><?php echo esc_html( nwcs_field( 'about', 'group', 'image_note' ) ); ?></figcaption>
		</figure>
	</div>
</section>

<?php
sanayi_palet_section( 'ctaband' );

get_footer();
