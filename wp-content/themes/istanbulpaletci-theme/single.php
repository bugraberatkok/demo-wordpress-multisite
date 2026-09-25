<?php
/**
 * Tekil blog yazisi: okuma kolonu ve yaninda urunlere kisa yol.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$blog_page = (int) get_option( 'page_for_posts' );

while ( have_posts() ) :
	the_post();
	$post_id = (int) get_the_ID();
	?>
	<article class="mx-auto max-w-[80rem] px-5 pt-10 md:px-8 md:pt-14">

		<?php if ( $blog_page ) : ?>
			<a href="<?php echo esc_url( get_permalink( $blog_page ) ); ?>" class="text-sm text-steel no-underline hover:text-indigo"
				<?php nwcs_edit_attr( 'blog', 'head', 'back_label' ); ?>>
				← <?php echo esc_html( nwcs_field( 'blog', 'head', 'back_label' ) ); ?>
			</a>
		<?php endif; ?>

		<div class="mt-6 grid gap-12 lg:grid-cols-[1fr_20rem] lg:gap-16">

			<div class="min-w-0">
				<header>
					<time class="tabular text-steel" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" <?php ip_post_attr( $post_id, 'Yayın tarihi' ); ?>>
						<?php echo esc_html( get_the_date( 'j F Y' ) ); ?>
					</time>
					<h1 class="mt-3 max-w-[20ch] text-[2.75rem] font-bold leading-[0.98] md:text-[4rem]" <?php ip_post_attr( $post_id, 'Yazı başlığı' ); ?>><?php the_title(); ?></h1>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="sheet mt-10 p-2" <?php ip_post_attr( $post_id, 'Öne çıkan görsel' ); ?>>
						<div class="shot aspect-[16/9]">
							<?php the_post_thumbnail( 'large', array( 'fetchpriority' => 'high', 'decoding' => 'async' ) ); ?>
						</div>
					</figure>
				<?php endif; ?>

				<div class="prose-ip mt-10" <?php ip_post_attr( $post_id, 'Yazı metni' ); ?>>
					<?php the_content(); ?>
				</div>
			</div>

			<aside class="lg:pt-16" aria-labelledby="yan-urunler">
				<div class="sheet p-6 lg:sticky lg:top-28">
					<h2 id="yan-urunler" class="font-body text-lg font-semibold" <?php nwcs_edit_attr( 'blog', 'aside', 'title' ); ?>>
						<?php echo esc_html( nwcs_field( 'blog', 'aside', 'title' ) ); ?>
					</h2>

					<ul class="mt-4 divide-y divide-line">
						<?php foreach ( ip_products() as $product ) : ?>
							<li>
								<a href="<?php echo esc_url( $product['url'] ); ?>" class="flex items-center gap-3 py-3 text-ink no-underline hover:text-indigo"
									<?php nwcs_edit_attr( $product['key'], 'card', 'name' ); ?>>
									<span class="shot aspect-[4/3] w-16 shrink-0 border border-line">
										<?php echo ip_image_tag( $product['image'], '', $product['name'] ); // phpcs:ignore WordPress.Security.EscapingOutput ?>
									</span>
									<span class="font-medium"><?php echo esc_html( $product['name'] ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>

					<a href="<?php echo esc_url( ip_link( nwcs_field( 'blog', 'aside', 'button_url' ) ) ); ?>" class="btn btn--md btn--solid mt-5 w-full"
						<?php nwcs_edit_attr( 'blog', 'aside', 'button_label' ); ?>>
						<?php echo esc_html( nwcs_field( 'blog', 'aside', 'button_label' ) ); ?>
					</a>
				</div>
			</aside>
		</div>
	</article>
	<?php
endwhile;

get_footer();
