<!DOCTYPE html>
<html<?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div class="container">
		<header>
			<div class="five columns clearfix">
				<?php
					$technews_logo = get_theme_mod('technews_image_logo_upload');
					if ( $technews_logo) { ?> 
 						 <a href="<?php echo esc_url(home_url());?>"><span id="logo"><img src="<?php echo esc_url($technews_logo); ?>" title="<?php bloginfo('title');?>"></span></a>
						<?php }else{ ?>
						<a href="<?php echo esc_url(home_url()); ?>"><span id="default-logo"><?php echo bloginfo(); ?></span></a>
 						<p id="tagline"><?php echo get_bloginfo( 'description', 'display' ); ?></p>
			
				<?php } ?>
			</div>
			<div class="main-nav sixteen columns" id="show-nav"><a href="#">Toggle Navigation</a></div>
			<div class="main-nav sixteen columns" id="close-nav"><a href="#">Close Navigation</a></div>
			<div class="sixteen columns nav-bar">
				<nav>
					<?php 
	                if(has_nav_menu('primary')){
	                    wp_nav_menu( array( 
	                        'theme_location'=> 'primary', 
	                        'container'     => false,
	                        'menu_class' => 'main-nav',
	                        'fallback_cb'   => 'wp_page_menu' 
	                    ) ); 
	                } else {
	                ?>
	                    <ul class="main-nav">
	                        <?php wp_list_pages('title_li='); ?>
	                    </ul>
	                <?php
	                }
	                ?>
                </nav>
			</div>
		</header>