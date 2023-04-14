<?php /* Template Name: professor home1 */
get_header();

	include locate_template("theme-parts/logged-only.php");
	$page_id = $post_id_main = $post->ID;
	$wp_page_template = himer_post_meta("_wp_page_template",$post_id_main,false);

get_footer();?>