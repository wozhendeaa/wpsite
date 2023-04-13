<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package royal_news_magazine
 */

$royal_news_magazine_options = royal_news_magazine_theme_options();
$facebook = $royal_news_magazine_options['facebook'];
$pinterest = $royal_news_magazine_options['pinterest'];
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open();  ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'royal-news-magazine' ); ?></a>

	<header id="masthead" class="site-header">
        <div class="top-header">


    			<div class="container">
    				<div class="row">
                        <nav class="navbar navbar-default">
                            <div class="header-logo">
                                <?php
                                $description = get_bloginfo('description', 'display');
                                    the_custom_logo();

                                    ?>
                                    <div class="site-identity-wrap">
                                    <h3 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
                                    </h3>
                                    <p class="site-description"><?php echo esc_html($description) ?></p>
                                    </div>
                                    <?php
                                ?>
                            </div>

                            
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                    data-target="#navbar-collapse" aria-expanded="false">
                                <span class="sr-only"><?php echo esc_html__('Toggle navigation','royal-news-magazine'); ?></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        <!-- Collect the nav links, forms, and other content for toggling -->
            	            <div class="collapse navbar-collapse" id="navbar-collapse">

            	             <?php
            	                if (has_nav_menu('primary')) { ?>
            	                <?php
            	                    wp_nav_menu(array(
            	                        'theme_location' => 'primary',
            	                        'container' => '',
                                        'menu_id'=> 'menu-primary-menu',
            	                        'menu_class' => 'nav navbar-nav navbar-center',
            	                        'walker' => new royal_news_magazine_nav_walker(),
            	                        'fallback_cb' => 'royal_news_magazine_nav_walker::fallback',
            	                    ));
            	                ?>
            	                <?php } else { ?>
            	                    <nav id="site-navigation" class="main-navigation clearfix">
            	                        <?php   wp_page_menu(array('menu_class' => 'menu')); ?>
            	                    </nav>
            	                <?php } ?>

            	            </div><!-- End navbar-collapse -->

                                <ul class="header-icons">
                                    <?php if($facebook){ ?>
                                    <li><span class="social-icon"> <a href="<?php echo esc_url($facebook); ?>"><i class="fa fa-facebook"></i></a></span></li>
                                    <?php  } ?>

                                    <?php if($pinterest){ ?>
                                    <li><span  class="social-icon"><a href="<?php echo esc_url($pinterest); ?>"> <i class="fa fa-pinterest"></i></a></span></li>
                                    <?php  } ?>

                                </ul>
                        </nav>
                     </div>
                </div>

        </div>
	</header><!-- #masthead -->

	<div class="header-mobile">
		<div class="site-branding">
			<?php the_custom_logo(); ?>
			<div class="logo-wrap">

			<?php
   if (is_front_page() && is_home()): ?>
				<h2 class="site-title"><a href="<?php echo esc_url(
        home_url('/')
    ); ?>" rel="home"><?php bloginfo('name'); ?></a></h2>
				<?php else: ?>
				<h2 class="site-title"><a href="<?php echo esc_url(
        home_url('/')
    ); ?>" rel="home"><?php bloginfo('name'); ?></a></h2>
				<?php endif;
   $royal_news_magazine_description = get_bloginfo('description', 'display');
   if ($royal_news_magazine_description || is_customize_preview()): ?>
				<p class="site-description"><?php echo $royal_news_magazine_description;
       ?></p>
			<?php endif;
   ?>
			</div>
		</div><!-- .site-branding -->


		<div class="mobile-wrap">
	        <div class="header-social">

			<ul> <?php
       if ($facebook) {
           echo '<a class="social-btn facebook" href="' .
               esc_url($facebook) .
               '"><i class="fa fa-facebook" aria-hidden="true"></i></a>';
       }

       if ($pinterest) {
           echo '<a class="social-btn pinterest" href="' .
               esc_url($pinterest) .
               '"><i class="fa fa-pinterest" aria-hidden="true"></i></a>';
       }

       ?>
			                </ul>
			</div>

            <div id="mobile-menu-wrap">
	        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
	                data-target="#navbar-collapse1" aria-expanded="false">
	            <span class="sr-only"><?php echo esc_html__(
                 'Toggle navigation',
                 'royal-news-magazine'
             ); ?></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	        </button>

	        <div class="collapse navbar-collapse" id="navbar-collapse1">

	         <?php if (has_nav_menu('primary')) { ?>
	            <?php wp_nav_menu([
                 'theme_location' => 'primary',
                 'container' => '',
                 'menu_class' => 'nav navbar-nav navbar-center',
                 'menu_id' => 'menu-main',
                 'walker' => new royal_news_magazine_nav_walker(),
                 'fallback_cb' => 'royal_news_magazine_nav_walker::fallback',
             ]); ?>
	            <?php } else { ?>
	                <nav id="site-navigation" class="main-navigation clearfix">
	                    <?php wp_page_menu([
                         'menu_class' => 'menu',
                         'menu_id' => 'menuid',
                     ]); ?>
	                </nav>
	            <?php } ?>

				

		    
	        </div><!-- End navbar-collapse -->
    </div>
	    </div>
	</div>
	<!-- /main-wrap -->
