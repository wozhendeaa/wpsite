<?php get_header();
	$its_post_type = wpqa_questions_type;
	$paged         = himer_paged();
	$active_sticky = true;
	$custom_args   = array("post_type" => wpqa_questions_type);
	$show_sticky   = true;
	include locate_template("theme-parts/loop.php");
get_footer();?>