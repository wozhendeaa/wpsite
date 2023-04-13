<?php $follow_category = himer_options("follow_category");
$user_id = get_current_user_id();
if (is_page()) {
	$cat_sort  = himer_post_meta("cat_sort");
	$cat_order = himer_post_meta("cat_order");
	$cats_tax  = himer_post_meta("cats_tax");
	$number    = himer_post_meta("cats_per_page");
	$cat_style = himer_post_meta("cat_style_pages");
}else {
	$cat_sort  = (isset($_GET["cat_filter"]) && $_GET["cat_filter"] != ""?esc_html($_GET["cat_filter"]):"count");
	$cat_order = "DESC";
	$cats_tax  = (has_wpqa() && wpqa_is_search()?wpqa_search_type():"");
	$cat_style = himer_options("cat_style_pages");
}

if ($cats_tax == 'post' || $cats_tax == 'category') {
	$cat_type = 'category';
	$post_type_cats = 'post';
}else {
	$cat_type = wpqa_question_categories;
	$post_type_cats = wpqa_questions_type;
}

$cat_sort = (isset($_GET["cat_filter"]) && $_GET["cat_filter"] != ""?esc_html($_GET["cat_filter"]):(isset($cat_sort) && $cat_sort != ""?$cat_sort:"count"));
$cat_sort = ($cat_sort == "followers"?"meta_value_num":$cat_sort);

$theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");

$search_value = (has_wpqa()?wpqa_search():"");
if ($search_value != "") {
	$search_args = array('search' => $search_value);
}else {
	$search_args = array();
}

$number = (isset($number) && $number > 0?$number:apply_filters('himer_cats_per_page',4*get_option('posts_per_page',10)));
$paged  = himer_paged();
$offset = ($paged-1)*$number;
$cat_order = (isset($_GET["cat_filter"]) && $_GET["cat_filter"] == "name"?"ASC":$cat_order);
$meta_query = ($cat_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "cat_follow_count","compare" => "NOT EXISTS"),array("key" => "cat_follow_count","value" => 0,"compare" => ">="))):array());
$exclude = apply_filters('wpqa_exclude_question_category',array());
$cats  = get_terms($cat_type,array_merge($exclude,$search_args,$meta_query,array('hide_empty' => 0)));
$terms = get_terms($cat_type,array_merge($exclude,$search_args,$meta_query,array(
	'orderby'    => $cat_sort,
	'order'      => $cat_order,
	'number'     => $number,
	'offset'     => $offset,
	'hide_empty' => 0
)));

$all_cat_pages = ceil(count($cats)/$number);
if (!empty($terms) && !is_wp_error($terms)) {
	$term_list = '';
	echo '<div class="block-section-div cats-sections cat_'.$cat_style.'">';
		if (isset($wp_page_template) && $wp_page_template == "template-categories.php") {
			include locate_template("theme-parts/title.php");
		}
		echo '<div class="row-boot">';
			foreach ($terms as $term) {
				$tax_id = $term->term_id;
				$category_icon = get_term_meta($tax_id,prefix_terms."category_icon",true);
				if ($follow_category == "on") {
					$cat_follow = get_term_meta($tax_id,"cat_follow",true);
				}
				$tax_col = "col-boot-sm-6";
				if (($cats_tax == wpqa_questions_type || $cats_tax == wpqa_question_categories) && ($cat_style == "simple_follow" || $cat_style == "with_icon_1" || $cat_style == "with_icon_2" || $cat_style == "with_icon_3" || $cat_style == "with_icon_4")) {
					$tax_col = "col-boot-12";
				}else if (($cats_tax == wpqa_questions_type || $cats_tax == wpqa_question_categories) && ($cat_style == "with_cover_1" || $cat_style == "with_cover_3")) {
					$tax_col = "col-boot-sm-4";
					if ($theme_sidebar == "full") {
						$tax_col = "col-boot-sm-3";
					}
				}else if ($theme_sidebar == "full" && ($cats_tax == wpqa_questions_type || $cats_tax == wpqa_question_categories) && ($cat_style == "with_cover_4" || $cat_style == "with_cover_6")) {
					$tax_col = "col-boot-sm-4";
				}
				include locate_template("theme-parts/show-categories.php");
			}
		$term_list .= '</div>
	</div>';
	echo stripcslashes($term_list);
	if ($all_cat_pages > 1) {
		$pagination_args = array(
			'current'   => max(1, $paged),
			'total'     => $all_cat_pages,
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
	$no_cats = true;
}

if ($search_value != "" && isset($no_cats) && $no_cats == true) {
	include locate_template("theme-parts/search-none.php");
}?>