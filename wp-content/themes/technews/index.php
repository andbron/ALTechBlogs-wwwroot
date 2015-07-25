<?php get_header(); ?>
<?php $technews_featured = get_posts('category_name=Featured');?>
<section class="eleven columns main-content">
	<?php if(!is_paged()): ?>
    <?php 
      if(!empty($technews_featured)){
      ?>
      <section class="slider">
      <?php get_template_part('slider','index'); ?>
      </section>
    <?php } ?>
    <h1 class="latest-post"><b>Latest Posts</b></h1>
	<?php endif; ?>
		<?php get_template_part('loop',''); ?>
</section>
<?php get_sidebar(); ?>
<div class="clearfix"></div>

<?php get_footer(); ?>
