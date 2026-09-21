<?php
/**
 * Yedek sablon.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="p-pagehead">
	<div class="p-wrap">
		<h1 class="p-pagehead__title"><?php echo esc_html( wp_get_document_title() ); ?></h1>
	</div>
</div>

<div class="p-section">
	<div class="p-wrap">
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
