<?php /* Template Name: Blog */
get_header();
	include locate_template("theme-parts/logged-only.php");
	$page_id = $post_id_main = $post->ID;
	$wp_page_template = himer_post_meta("_wp_page_template",$post_id_main,false);
	do_action("himer_before_blog_action");
	include locate_template("theme-parts/loop.php");
	do_action("himer_after_blog_action");
get_footer();?>