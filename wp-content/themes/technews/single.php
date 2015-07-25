<?php get_header(); ?>
<section class="eleven columns row single-page">
	<?php if( have_posts()) : while (have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header>
			<h1><span class="post-heading"><?php the_title(); ?></span></h1>
		</header>
		<span class="post-info">Posted by <?php echo get_the_author(); ?> on <?php echo get_the_date(); ?></span>
		<div class="tags">
		<?php echo get_the_tag_list('<p>Tags: ',', ','</p>');?>
		</div>
		<div class="single-news-page">
		<p>
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</p>
		</div>
	</article>
	<?php endwhile; endif; ?>
<!--comments div here-->
<div class="eleven columns comments-div"><hr/>
<?php comments_template(); ?>
</div>	
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
