<?php
/**
 *
 * Template Name: Frontpage

 *
 * @package Royal News Magazine
 */

$royal_news_magazine_options = royal_news_magazine_theme_options();

$show_eight_post = $royal_news_magazine_options['show_eight_post'];
$show_three_post = $royal_news_magazine_options['show_three_post'];
$show_four_post1 = $royal_news_magazine_options['show_four_post1'];
$show_four_post2 = $royal_news_magazine_options['show_four_post2'];


get_header();

?>


<?php


get_template_part('template-parts/home/main-slider', 'section');

if ($show_eight_post==1): 
get_template_part('template-parts/home/eight-post', 'section');
endif;

if ($show_three_post==1): 
get_template_part('template-parts/home/three-column', 'section');
endif;

if ($show_four_post1==1): 
get_template_part('template-parts/home/first-post', 'section');
endif;

if ($show_four_post2==1): 
get_template_part('template-parts/home/second-post', 'section');
endif;



get_footer();
