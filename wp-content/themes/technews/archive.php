<?php get_header(); ?>
	<section class="eleven columns archive-page">
	<div class="archive">
		<header>
				<b><h1><?php
					if ( is_day() ) :
						printf( __( 'Daily Archives: %s', 'technews' ), get_the_date() );
					elseif ( is_month() ) :
						printf( __( 'Monthly Archives: %s', 'technews' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'technews' ) ) );
					elseif ( is_year() ) :
						printf( __( 'Yearly Archives: %s', 'technews' ), get_the_date( _x( 'Y', 'yearly archives date format', 'technews' ) ) );
					else :
						_e( 'Archives', 'technews' );
					endif;
				?></h1></b>
		</header>
	<?php if ( have_posts() ) : ?>
		<?php get_template_part('loop',''); ?>
	<?php endif; ?>

	</div>
	</section>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>