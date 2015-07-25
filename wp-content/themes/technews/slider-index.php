<?php if(!is_paged()): ?>

<div class="flexslider">
	<ul class="slides">
		
		<?php
		$technews_args = array('category_name' => 'Featured', 'posts_per_page' => 3);
		$technews_query = new WP_Query($technews_args);
		while($technews_query -> have_posts()) : $technews_query -> the_post();
		?>
	
		  <li class="featured">
			<?php the_post_thumbnail('featured'); ?>
			<div class="caption">
				<a href="<?php the_permalink(); ?>" class="featured-title"><?php the_title();?></a>
			</div>
		  </li>
	  
		<?php
		    endwhile;
			wp_reset_query();
		?>
	</ul>
  </div>
  <?php endif; ?>