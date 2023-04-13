<?php if (is_page()) {
	$tag_sort  = himer_post_meta("tag_sort");
	$tag_style = himer_post_meta("tag_style");
	$tag_order = himer_post_meta("tag_order");
	$tags_tax  = himer_post_meta("tags_tax");
	$number    = himer_post_meta("tags_per_page");
}else {
	$tag_style = himer_options("tag_style_pages");
	$tag_sort  = (isset($_GET["tag_filter"]) && $_GET["tag_filter"] != ""?esc_html($_GET["tag_filter"]):"count");
	$tag_order = "DESC";
	$tags_tax  = (has_wpqa() && wpqa_is_search()?wpqa_search_type():"");
}

if ($tags_tax == 'post' || $tags_tax == 'post_tag') {
	$tag_type = 'post_tag';
	$post_type_tags = 'post';
	if ($tag_style == "simple_follow") {
		$tag_style = "simple";
	}
}else {
	$tag_type = wpqa_question_tags;
	$post_type_tags = wpqa_questions_type;
}

$tag_sort = (isset($_GET["tag_filter"]) && $_GET["tag_filter"] != ""?esc_html($_GET["tag_filter"]):(isset($tag_sort) && $tag_sort != ""?$tag_sort:"count"));

$theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
$follow_category = himer_options("follow_category");

$search_value = (has_wpqa()?wpqa_search():"");
if ($search_value != "") {
	$search_args = array('search' => $search_value);
}else {
	$search_args = array();
}

$number = (isset($number) && $number > 0?$number:apply_filters('himer_tags_per_page',4*get_option('posts_per_page',10)));
$paged  = himer_paged();
$offset     = ($paged-1)*$number;
$tag_order  = (isset($_GET["tag_filter"]) && $_GET["tag_filter"] == "name"?"ASC":$tag_order);
$tag_sort   = ($tag_sort == "followers"?"meta_value_num":$tag_sort);
$meta_query = ($tag_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "tag_follow_count","compare" => "NOT EXISTS"),array("key" => "tag_follow_count","value" => 0,"compare" => ">="))):array());
$tags       = get_terms($tag_type,array_merge($search_args,$meta_query,array('hide_empty' => 0)));
$terms      = get_terms($tag_type,array_merge($search_args,$meta_query,array(
	'orderby'    => $tag_sort,
	'order'      => $tag_order,
	'number'     => $number,
	'offset'     => $offset,
	'hide_empty' => 0
)));

$all_tag_pages = ceil(count($tags)/$number);
if (!empty($terms) && !is_wp_error($terms)) {
	$term_list = '';
	echo '<div class="block-section-div '.($follow_category == "on" && $tag_style == "simple_follow" && ($tags_tax == wpqa_question_tags || $tags_tax == wpqa_questions_type)?'row cats-sections tags-sections':'tagcloud '.($tag_style == "advanced"?"row":"tagcloud-simple")).'">';
		if (isset($wp_page_template) && $wp_page_template == "template-tags.php") {
			include locate_template("theme-parts/title.php");
		}
		echo '<div class="d-flex flex-wrap'.($tag_style != "advanced" && $tag_style != "simple_follow"?"":" row-boot").'">';
			foreach ($terms as $term) {
				if ($follow_category == "on" && $tag_style == "simple_follow" && ($tags_tax == wpqa_question_tags || $tags_tax == wpqa_questions_type)) {
					$tax_id = $term->term_id;
					$tag_follow = get_term_meta($tax_id,"tag_follow",true);
					$tags_follwers = (int)(is_array($tag_follow)?count($tag_follow):0);
					$term_list .= '<div class="col-boot-lg-6 col '.($tags_tax == wpqa_question_tags && ($theme_sidebar == "full"?"col4 col-boot-sm-4 col-boot-md-4 col-boot-xl-4":"col6 col-boot-sm-6 col-boot-md-6 col-boot-xl-6")).'">
						<div class="cat-sections-follow community-card community-card-layout2 d-flex flex-wrap justify-content-between">
							<div class="cat-sections community__info">
								<div class="d-flex">
									<div class="community__icon mr-3">
										<i class="icon-ios-pricetags"></i>
									</div>
									<div>
										<div class="community__links">
											<a href="'.esc_url(get_term_link($term)).'" title="'.esc_attr(sprintf(esc_html__('View all questions under %s','himer'),$term->name)).'"><i class="icon-tag"></i>'.$term->name.'</a>
										</div>';
										if ($follow_category == "on") {
											$term_list .= '<div>
												<span class="community__count follow-cat-count">'.himer_count_number($tags_follwers)."</span>"._n("Follower","Followers",$tags_follwers,"himer").'
											</div>';
										}
									$term_list .= '</div>
								</div>
							</div>';
							if ($follow_category == "on") {
								$term_list .= '<div class="cat-follow-button community__meta d-flex justify-content-end align-items-center">
									'.(has_wpqa()?wpqa_follow_cat_button($tax_id,$user_id,'tag',true,'button-default-4 btn btn__semi__height','community-card','follow-cat-count','btn__success','btn__danger'):"").'
								</div>';
							}
						$term_list .= '</div>
					</div>';
				}else {
					if ($tag_style == "advanced") {
						$term_list .= '<div class="col-boot-lg-6 col '.($theme_sidebar == "full"?"col3 col-boot-sm-3 col-boot-md-3 col-boot-xl-3":"col4 col-boot-sm-4 col-boot-md-4 col-boot-xl-4").'">
							<div class="tag-sections tag-card">
								<div class="tag-counter d-flex align-items-center">';
					}
									$term_list .= '<a class="tag__name'.($tag_style == "advanced"?" mr-3":"").($tag_style != "advanced" && $tag_style != "simple_follow"?"":" mb-0").'" href="'.esc_url(get_term_link($term)).'" title="'.esc_attr(sprintf(($tags_tax == 'post' || $tags_tax == 'post_tag'?esc_html__('View all posts under %s','himer'):esc_html__('View all questions under %s','himer')),$term->name)).'">'.$term->name.'</a>';
					if ($tag_style == "advanced") {
									$term_list .= '<span class="tag__count"> x '.himer_count_number($term->count).'</span>
								</div>
								<div class="tag-section tags__stats">';
									$today = getdate();
									$tag = $term->term_id;
									$today_query = new WP_Query(array('post_type' => $post_type_tags,'year' => $today["year"],'monthnum' => $today["mon"],'day' => $today["mday"],'tax_query' => array(array('taxonomy' => $tag_type,'field' => 'term_id','terms' => $tag))));
									$week  = date('W');
									$year  = date('Y');
									$month = date('m');
									$week_query   = new WP_Query(array('post_type' => $post_type_tags,'year' => $year,'w' => $week,'tax_query' => array(array('taxonomy' => $tag_type,'field' => 'term_id','terms' => $tag))));
									$month_query  = new WP_Query(array('post_type' => $post_type_tags,'year' => $year,'monthnum' => $month,'tax_query' => array(array('taxonomy' => $tag_type,'field' => 'term_id','terms' => $tag))));
									$term_list .= "<span>".sprintf(esc_html__('%s asked today','himer'),himer_count_number($today_query->found_posts))."</span>";
									$term_list .= "<span>".sprintf(esc_html__('%s this week','himer'),himer_count_number($week_query->found_posts))."</span>";
									$term_list .= "<span>".sprintf(esc_html__('%s this month','himer'),himer_count_number($month_query->found_posts))."</span>";
								$term_list .= '</div>
							</div>
						</div>';
					}
				}
			}
		$term_list .= '</div>
	</div>';
	echo stripcslashes($term_list);
	if ($all_tag_pages > 1) {
		$pagination_args = array(
	    	'current'   => max(1, $paged),
	    	'total'     => $all_tag_pages,
	    	'prev_text' => '<i class="icon-arrow-left-b"></i>',
	    	'next_text' => '<i class="icon-arrow-right-b"></i>',
	    );
		if (!get_option('permalink_structure')) {
			$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
		}
		if (has_wpqa() && wpqa_is_search()) {
			$pagination_args['format'] = '?page=%#%';
		}
		echo '<div class="main-pagination"><div class="pagination">'.paginate_links($pagination_args).'</div></div>';
	}
}else {
	$no_tags = true;
}

if ($search_value != "" && isset($no_tags) && $no_tags == true) {
	include locate_template("theme-parts/search-none.php");
}?>