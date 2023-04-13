<?php get_header();
	$tag_des     = himer_options('tag_description');
	$tag_rss     = himer_options("tag_rss");
	$tag_desc    = tag_description();
	$category_id = (int)get_query_var('wpqa_term_id');
	if ($tag_des == "on" && !empty($tag_desc)) {?>
		<div class="post-section category-description block-section-div">
			<div class="d-flex align-items-center">
				<h4><?php echo esc_html__("Tag","himer").": ".esc_html(single_tag_title("", false));?></h4>
				<?php if ($tag_rss == "on") {?>
					<a class="category-rss-i tooltip-n" title="<?php esc_attr_e("Tag feed","himer")?>" href="<?php echo esc_url(get_tag_feed_link(esc_attr(get_query_var('tag_id'))))?>"><i class="icon-social-rss"></i></a>
				<?php }?>
			</div>
			<?php echo stripcslashes($tag_desc);?>
		</div><!-- End post -->
	<?php }
	include locate_template("theme-parts/loop.php");
get_footer();?>