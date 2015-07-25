<?php get_header(); ?>
<section class="eleven columns row single-page">
	<?php if( have_posts()) : while (have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header>
			<h1><span class="post-heading"><?php the_title(); ?></span></h1>
		</header>
		<div class="single-news-page">
			<?php the_content(); ?>
		</div>
	</article>
	<?php endwhile; endif; ?>

	<div class="eleven columns comments-div"><hr/>
		<?php comments_template(); ?>
	</div>

</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
