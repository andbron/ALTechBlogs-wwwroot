<?php
		while(have_posts()) : the_post();
?>

<div class="news-box animated fadeInUp">
	<h2 class="post-heading"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>
	<span class="post-info">Posted by <?php echo get_the_author(); ?> on <?php echo get_the_date(); ?></span><hr/>
		<div class="post-thumbs">
			<?php the_post_thumbnail('post-thumb');?>
		</div>
		<div class="post">
			<p><?php the_excerpt(); ?></p>
				<a href="<?php the_permalink(); ?>" id="read-more">Read More</a>
		</div>
</div>

<?php
		endwhile;
		wp_reset_query();
?>
<?php if(function_exists('wp_pagenavi')) { ?>
	<div><?php wp_pagenavi(); ?></div>
	
	<?php } else { ?>
		<div><p><span id="older-entries"><?php next_posts_link(__('&larr; OLDER POSTS', 'technews')); ?></span>
		<span id="new-entries"><?php previous_posts_link(__('NEWER POSTS &rarr;', 'technews')); ?></span></p></div>
<?php } ?>
