<?php
get_header(); ?>

	<div class="eleven columns">
		<main id="main" class="site-main" role="main">

			<section class="error">
				<header class="nothing">
					<h1><?php _e( 'Oops! That page can&rsquo;t be found.', 'technews' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe you can find it with a search ?', 'technews' ); ?></p>

					<?php get_search_form(); ?>

				</div>
			</section>

		</main>
	</div>

<?php get_footer(); ?>