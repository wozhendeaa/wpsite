<?php get_header();
	do_action("himer_before_category_action");
	$category_des  = himer_options('category_description');
	$category_rss  = himer_options("category_rss");
	$category_desc = category_description();
	$category_id   = (int)get_query_var('wpqa_term_id');
	if ($category_des == "on" && !empty($category_desc)) {?>
		<div class="post-section category-description block-section-div">
			<div class="d-flex align-items-center">
				<h4><?php echo esc_html__("Category","himer").": ".esc_html(single_cat_title("",false));?></h4>
				<?php if ($category_rss == "on") {?>
					<a class="category-rss-i tooltip-n" title="<?php esc_attr_e("Category feed","himer")?>" href="<?php echo esc_url(get_category_feed_link($category_id))?>"><i class="icon-social-rss"></i></a>
				<?php }?>
			</div>
			<?php echo stripcslashes($category_desc);?>
		</div><!-- End post -->
	<?php }
	include locate_template("theme-parts/loop.php");
	do_action("himer_after_category_action");
get_footer();?>