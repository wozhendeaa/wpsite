<?php get_header();
	include locate_template("theme-parts/logged-only.php");
	$page_id = $post_id_main = $post->ID;
	$theme_sidebar_all = $theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
	$remove_question_slug = himer_options("remove_question_slug");
	$remove_asked_question_slug = himer_options("remove_asked_question_slug");
	if (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && is_singular("post")) {
		$array_data = array("p" => $page_id);
		$himer_query = new WP_Query($array_data);
	}
	include locate_template("includes/".(is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?'question':'loop')."-setting.php");
	if ( (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && isset($himer_query) && $himer_query->have_posts()) || have_posts() ) :?>
		<div class="post-articles<?php echo (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?" question-articles".(isset($question_columns) && $question_columns == "style_2" && isset($masonry_style) && $masonry_style == "on"?" isotope":""):"")?>">
			<?php if (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && isset($himer_query) && $himer_query->have_posts()) :
				while ($himer_query->have_posts()) : $himer_query->the_post();
					do_action("himer_action_before_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_before_post_content",$post->ID,$post->post_author);
					include locate_template("theme-parts/content.php");
					do_action("himer_action_after_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_after_post_content",$post->ID,$post->post_author);
				endwhile;
			else :
				while ( have_posts() ) : the_post();
					do_action("himer_action_before_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_before_post_content",$post->ID,$post->post_author);
					include locate_template("theme-parts/loop-action.php");
					do_action("himer_action_after_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_after_post_content",$post->ID,$post->post_author);
				endwhile;
			endif;?>
		</div><!-- End post-articles -->
	<?php else :
		include locate_template("theme-parts/content-none.php");
	endif;
	if (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && isset($himer_query)) {
		wp_reset_postdata();
	}
get_footer();?>