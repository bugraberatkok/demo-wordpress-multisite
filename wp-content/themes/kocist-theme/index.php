<?php
/**
 * Yedek sablon. Demo kapsaminda ana sayfa ve ornek alt sayfa kullanilir.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="k-pagehead">
	<div class="k-wrap">
		<h1 class="k-pagehead__title"><?php echo esc_html( wp_get_document_title() ); ?></h1>
	</div>
</div>

<div class="k-section">
	<div class="k-wrap">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
</div>
<?php
get_footer();
