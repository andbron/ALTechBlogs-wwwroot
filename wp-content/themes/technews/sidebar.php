<section class="five columns sidepostlist">
	<span class="sidebar-widgets" id="sidebar">
		<?php
  			$technews_facebook = get_theme_mod('technews_facebook_url');
  			$technews_gplus = get_theme_mod('technews_google_plus_url');
  			$technews_twitter = get_theme_mod('technews_twitter_url');
  			$technews_youtube = get_theme_mod('technews_youtube_url');
  			$technews_pinterest = get_theme_mod('technews_pinterest_url');
		?>
			<?php if(empty($technews_facebook) && empty($technews_gplus) && empty($technews_twitter) && empty($technews_youtube) && empty($technews_pinterest)){

			}else{ ?>
				<div class="social">
				<h2>Connect With Us</h2>		  	
		  	<?php if(!empty($technews_facebook)){ ?>
		  		<a href="<?php echo esc_url($technews_facebook); ?>" target=_blank><img src="<?php echo get_template_directory_uri();?>/img/social_icons/facebook-icon.png" title="Facebook"/></a>
		  	<?php } ?>
		  	<?php if (!empty($technews_gplus)) { ?>
		  		<a href="<?php echo esc_url($technews_gplus); ?>" target=_blank><img src="<?php echo get_template_directory_uri();?>/img/social_icons/gplus-icon.png" title="Google-Plus"/></a>
		  	<?php } ?>
		  	<?php if (!empty($technews_twitter)) { ?>
		  		<a href="<?php echo esc_url($technews_twitter); ?>" target=_blank><img src="<?php echo get_template_directory_uri();?>/img/social_icons/twitter-icon.png" title="Twitter"/></a>
		  	<?php } ?>  	
		  	<?php if (!empty($technews_youtube)) { ?>
		  		<a href="<?php echo esc_url($technews_youtube); ?>" target=_blank><img src="<?php echo get_template_directory_uri();?>/img/social_icons/youtube-icon.png" title="YouTube"/></a>
		  	<?php } ?>
		  	<?php if (!empty($technews_pinterest)) { ?>
		  		<a href="<?php echo esc_url($technews_pinterest); ?>" target=_blank><img src="<?php echo get_template_directory_uri();?>/img/social_icons/pinterest.png" title="Pinterest"/></a>
		  	<?php } ?> 
		  	 	</div>
		  	<?php } ?>

		<div class="random-posts-widget">
			<h2>Random Posts</h2>
			<ul>
				<?php
					$technews_args = array('posts_per_page' => 5, 'orderby' => 'rand');
					$technews_query = new WP_Query($technews_args);
					while($technews_query -> have_posts()) : $technews_query -> the_post();
				?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title();?></a></li>
				<?php
					endwhile;
					wp_reset_query();
				?>
			</ul>
		</div>	
    <?php dynamic_sidebar('sidebar-widgets'); ?>
    </span>
</section>