<?php get_header(); ?>

	<section class="eleven columns search">
		<div>

		<?php if ( have_posts() ) : ?>
	
			<header>
				<b><h1 class="s-results"><?php printf( __( 'Search Results for: %s','technews'), '<span>' . get_search_query() . '</span>' ); ?></h1></b>
			</header>
		<?php get_template_part('loop',''); ?>
		<?php else : ?>

			<article>
				<header  class="nothing">
					<b><h1><?php _e( 'Nothing Found !!','technews'); ?></h1></b>
				</header>

				<div class="entry-content space">
					<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'technews' ); ?></p>
					<?php get_search_form(); ?>
				</div>
			</article>

		<?php endif; ?>

		</div>
	</section>
<?php get_footer(); ?>