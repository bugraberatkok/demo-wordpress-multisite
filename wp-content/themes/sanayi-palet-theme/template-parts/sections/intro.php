<?php
/**
 * Kisaca biz: firmanin ne yaptigi tek paragrafta, yaninda dort one cikan.
 * Ikon kutulari cam renginde; ikonlar tiklanabilir olmadigi icin yesil degil.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="sp-section sp-intro" data-nwcs-section="intro">
	<div class="sp-wrap sp-intro__grid">
		<div class="sp-intro__lead">
			<h2 class="sp-title" <?php nwcs_edit_attr( 'home', 'intro', 'title' ); ?>><?php echo esc_html( nwcs_field( 'home', 'intro', 'title' ) ); ?></h2>
			<p class="sp-intro__text" <?php nwcs_edit_attr( 'home', 'intro', 'text' ); ?>><?php echo esc_html( nwcs_field( 'home', 'intro', 'text' ) ); ?></p>
		</div>

		<ul class="sp-intro__facts" <?php nwcs_edit_attr( 'home', 'intro', 'facts' ); ?>>
			<?php foreach ( nwcs_rows( 'home', 'intro', 'facts' ) as $index => $fact ) : ?>
				<li class="sp-intro__fact">
					<span class="sp-intro__icon" <?php nwcs_edit_attr( 'home', 'intro', 'facts', $index, 'icon' ); ?>><?php sanayi_palet_icon( (string) ( $fact['icon'] ?? '' ), 28 ); ?></span>
					<h3 class="sp-intro__name" <?php nwcs_edit_attr( 'home', 'intro', 'facts', $index, 'title' ); ?>><?php echo esc_html( $fact['title'] ?? '' ); ?></h3>
					<p class="sp-intro__desc" <?php nwcs_edit_attr( 'home', 'intro', 'facts', $index, 'text' ); ?>><?php echo esc_html( $fact['text'] ?? '' ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
