<?php get_header();
	$breadcrumbs = himer_options('breadcrumbs');
	$tag_des     = himer_options('question_tag_description');
	$tag_rss     = himer_options("question_tag_rss");
	$tag_desc    = tag_description();
	$tax_id      = get_term_by('slug',get_query_var('term'),esc_html(get_query_var('taxonomy')));
	$category_id = $tax_id->term_id;
	if ($tag_des == "on" && !empty($tag_desc)) {?>
		<div class="question-category post-section category-description block-section-div">
			<div class="d-flex align-items-center">
				<h4><?php echo esc_html__("Tag","himer").": ".esc_html(single_tag_title("", false));?></h4>
				<?php if ($tag_rss == "on") {?>
					<a class="category-rss-i tooltip-n" title="<?php esc_attr_e("Tag feed","himer")?>" href="<?php echo esc_url(get_tag_feed_link(esc_attr(get_query_var('tag_id'))))?>"><i class="icon-social-rss"></i></a>
				<?php }?>
			</div>
			<?php echo stripcslashes($tag_desc);?>
		</div><!-- End post -->
	<?php }
	if ($breadcrumbs != "on") {
		echo "<div class='follow-tag'>".wpqa_follow_cat_button($category_id,get_current_user_id(),'tag')."</div>";
	}
	$its_post_type = wpqa_questions_type;
	$paged         = himer_paged();
	$active_sticky = true;
	$custom_args   = array('tax_query' => array(array('taxonomy' => wpqa_question_tags,'field' => 'id','terms' => $category_id)));
	$tabs_tag      = himer_options("tabs_tag");
	if ($tabs_tag == "on") {
		$exclude_tags = himer_options("exclude_tags");
		$exclude_tags = ($exclude_tags != ""?explode(",",$exclude_tags):array());
		$tab_tag = (is_array($exclude_tags) && !in_array($category_id,$exclude_tags)?true:false);
	}
	if (isset($tab_tag) && $tab_tag == true) {
		include locate_template("theme-parts/tabs.php");
	}else {
		$active_sticky = $show_sticky = true;
		include locate_template("theme-parts/loop.php");
	}
get_footer();?>