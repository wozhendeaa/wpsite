<?php get_header();
	do_action("himer_before_archive_action");
	include locate_template("theme-parts/loop.php");
	do_action("himer_after_archive_action");
get_footer();?>