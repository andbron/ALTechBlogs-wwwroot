<?php /* Theme Setup */ ?>
<?php
if ( ! function_exists( 'technews_setup' ) ) :
function technews_setup() {

	load_theme_textdomain( 'technews', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Enable support for HTML5 markup.
	add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form', ) );

	//Navigation menu
	register_nav_menus (
		array(
			'primary' => __('Header Navigation','technews')
			)
	);

	//Enable background customization
	add_theme_support('custom-background');

	//Enable post thumbnails
	add_theme_support('post-thumbnails');

	//Thumbnail Size
	add_image_size('featured',640,360,true);
	add_image_size('post-thumb',220,125,true);
}
endif;
add_action( 'after_setup_theme', 'technews_setup' );
?>
<?php
function technews_init_widgets() {
//Registering the sidebars and footer
	register_sidebar(
		array(
		'name' => 'Footer Widgets',
		'id' => 'footer-widgets',
		'description' => 'Place Your Footer Widgets Here',
		'before_widget' => '<div class="five columns">',
		'after_widget' => '</div>'
		)
	);
	
	register_sidebar(
		array(
		'name' => 'Sidebar Widgets',
		'id' => 'sidebar-widgets',
		'description' => 'Place Your sidebar Widgets Here',
		)
	);
}
add_action( 'widgets_init', 'technews_init_widgets' );
?>
<?php
function technews_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'technews' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'technews_title', 10, 2 );
?>
<?php if ( ! isset( $content_width ) ) {
	$content_width = 960; /* pixels */
}
?>
<?php
/**
 * Enqueue scripts and styles.
 */
function technews_scripts() {
	/*Loading our main stylesheet */
	wp_enqueue_style( 'technews-style', get_stylesheet_uri() );

	/*Loading our jquery flexslider */
	wp_enqueue_script( 'technews-script', get_template_directory_uri() . '/js/jquery.flexslider-min.js',array('jquery'),'', true);
	
	/*Loading our navigation toggle and slider javascript */
	wp_enqueue_script( 'toggle-slider-script', get_template_directory_uri() . '/js/toggle-slider.js',array('jquery'),'',true);
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'technews_scripts' );
?>
<?php 
/* add ie conditional html5 shim to header */
function technews_html5_shim(){
	echo '<!--[if lt IE 9]>';
	echo '<script type="text/javascript" src="'.get_template_directory_uri().'/js/html5shiv.js"></script>';
	echo '<![endif]-->';
	}
add_action('wp_head','technews_html5_shim');
?>
<?php
//removin [..] from the excerpt
function technews_excerpt_more($more){
	return '...';
}
add_filter('excerpt_more','technews_excerpt_more');
?>
<?php
function technews_favicon_setup(){
			$favicon = get_theme_mod('technews_favicon_upload');
			if (!empty($favicon)) {
				echo "<link rel='Shortcut Icon' href='".esc_url($favicon)."' type='image/png'/>";
			}
		}
add_action('wp_head','technews_favicon_setup');
?>
<?php
function technews_customize_register($wp_customize){
	$wp_customize->add_section('technews_colors',array(
		'title' => __('Customize Colors','technews'),
		'description' => 'Modify the colors of the theme.'
	));
	$wp_customize->add_setting('body_text_color',array(
		'default' => '#000',
		'sanitize_callback' => 'sanitize_hex_color'
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize,'body_text_color',array(
		'label' => __('Edit the body text color','technews'),
		'section' => 'technews_colors',
		'settings' => 'body_text_color'
	)));
	$wp_customize->add_setting('sidebar_color',array(
		'default' => '#e6e6dc',
		'sanitize_callback' => 'sanitize_hex_color'
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize,'sidebar_color',array(
		'label' => __('Edit the sidebar color','technews'),
		'section' => 'technews_colors',
		'settings' => 'sidebar_color'
	)));
	$wp_customize->add_setting('nav_bar_color',array(
		'default' => '#5ca5cc',
		'sanitize_callback' => 'sanitize_hex_color'
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize,'nav_bar_color',array(
		'label' => __('Edit the navigation bar color','technews'),
		'section' => 'technews_colors',
		'settings' => 'nav_bar_color'
	)));
	
	//logo upload section
	$wp_customize->add_section('technews_image_logo',array(
		'title' => __('Upload your logo.','technews'),
		'description' => 'Browse the image file of your logo and upload it.'
	));
	$wp_customize->add_setting('technews_image_logo_upload',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw'
	));
	$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'technews_image_logo_upload',array(
        'label' => 'Logo Upload',
        'section' => 'technews_image_logo',
        'settings' => 'technews_image_logo_upload'
	)));

	//favicon upload section
	$wp_customize->add_section('technews_favicon',array(
		'title' => __('Upload your favicon.','technews'),
		'description' => 'Browse the small png file (16*16) and upload it.'
	));
	$wp_customize->add_setting('technews_favicon_upload',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw'
	));
	$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'technews_favicon_upload',array(
        'label' => 'Favicon Upload',
        'section' => 'technews_favicon',
        'settings' => 'technews_favicon_upload'
	)));

	//Show sidebar on the left
	$wp_customize->add_section('technews_left_sidebar',array(
		'title' => __('Sidebar','technews'),
		'description' => 'Check it if you want sidebar on the left'
	));
	$wp_customize->add_setting('technews_sidebar_left',array(
		'default' => '',
		'sanitize_callback' => 'sanitize_key'
	));
	$wp_customize->add_control('technews_sidebar_left',array(
        'label' => 'Sidebar on left side.',
        'section' => 'technews_left_sidebar',
        'settings' => 'technews_sidebar_left',
        'type' => 'checkbox'
	));

	//social media section
	$wp_customize->add_section('technews_social_urls',array(
		'title' => __('Social Media Links','technews'),
		'description' => 'Enter the URLs of the respective social media.'
	));

	//facebook
	$wp_customize->add_setting('technews_facebook_url',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw'
	));
	$wp_customize->add_control('technews_facebook_url',array(
		'label' => __('Enter your Facebook URL','technews'),
		'section' => 'technews_social_urls',
		'settings' => 'technews_facebook_url',
		'type' => 'text'
	));
	
	//google-plus
	$wp_customize->add_setting('technews_google_plus_url',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw'
	));
	$wp_customize->add_control('technews_google_plus_url',array(
		'label' => __('Enter your Google Plus URL','technews'),
		'section' => 'technews_social_urls',
		'settings' => 'technews_google_plus_url',
		'type' => 'text'
	));

	//twitter
	$wp_customize->add_setting('technews_twitter_url',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw'
	));
	$wp_customize->add_control('technews_twitter_url',array(
		'label' => __('Enter your Twitter URL','technews'),
		'section' => 'technews_social_urls',
		'settings' => 'technews_twitter_url',
		'type' => 'text'
	));

	//YouTube
	$wp_customize->add_setting('technews_youtube_url',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw'
	));
	$wp_customize->add_control('technews_youtube_url',array(
		'label' => __('Enter your YouTube URL','technews'),
		'section' => 'technews_social_urls',
		'settings' => 'technews_youtube_url',
		'type' => 'text'
	));

	//Pinterest
	$wp_customize->add_setting('technews_pinterest_url',array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw'
	));
	$wp_customize->add_control('technews_pinterest_url',array(
		'label' => __('Enter your Pinterest URL','technews'),
		'section' => 'technews_social_urls',
		'settings' => 'technews_pinterest_url',
		'type' => 'text'
	));
}
add_action('customize_register','technews_customize_register');
?>
<?php
function technews_css_modifier(){
?>
	<style type="text/css">
		body{
			color: <?php echo get_theme_mod('body_text_color');?>;
		} 
		#ad{
			background-color: <?php echo get_theme_mod('sidebar_color');?>;
		}
		.sidepostlist{
			background-color: <?php echo get_theme_mod('sidebar_color');?>;
		}
		.nav-bar{
			background-color:<?php echo get_theme_mod('nav_bar_color');?>;
		}
		#default-logo{
			color: <?php echo get_theme_mod('nav_bar_color');?>;
		}
		.main-nav ul{
			background-color:<?php echo get_theme_mod('nav_bar_color');?>;
		}
		.foot{
			background-color:<?php echo get_theme_mod('nav_bar_color');?>;
		}
		#show-nav{
			background-color:<?php echo get_theme_mod('nav_bar_color');?>;
		}
		#close-nav{
			background-color:<?php echo get_theme_mod('nav_bar_color');?>;
		}
	</style>
<?php }
add_action('wp_head','technews_css_modifier');
?>
<?php
//sidebar on the left
function technews_sidebar_on_left(){
	if(get_theme_mod('technews_sidebar_left') == 1){
		echo '<style type="text/css">.main-content,.single-page,.archive-page{float: right !important;}
		      </style>';
	}
}
add_action('wp_head','technews_sidebar_on_left');
?>
<?php
// preventing the 28px push down of the wp-admin bar
 function technews_my_filter_head() {
    remove_action('wp_head', '_admin_bar_bump_cb');
  }
  add_action('get_header', 'technews_my_filter_head');
?>
<?php
function technews_add_editor_styles() {
    add_editor_style(get_stylesheet_uri());
}
add_action( 'init', 'technews_add_editor_styles' );
?>