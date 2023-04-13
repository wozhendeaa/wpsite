<?php /* Template Name: Contact us */
get_header();
	include locate_template("theme-parts/logged-only.php");
	$page_id = $post_id_main = $post->ID;
	$wp_page_template = himer_post_meta("_wp_page_template",$post_id_main,false);
	echo "<div class='post-content-text'>";
		include locate_template("theme-parts/loop.php");
	echo "</div>";
get_footer();?>