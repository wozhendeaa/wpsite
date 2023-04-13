<?php if ((!is_array($cats_tax) && ($cats_tax == wpqa_questions_type || $cats_tax == wpqa_question_categories)) || (is_array($cats_tax) && (in_array(wpqa_questions_type,$cats_tax) || in_array(wpqa_question_categories,$cats_tax)))) {
	$tax_question_available = true;
}
$follow_text = true;
$term_list .= '<div class="col '.$tax_col.'">';
	if (isset($tax_question_available) && ($cat_style == "with_icon" || $cat_style == "simple_follow" || $cat_style == "icon_color" || $cat_style == "with_icon_1" || $cat_style == "with_icon_2" || $cat_style == "with_icon_3" || $cat_style == "with_icon_4" || $cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6")) {
	$questions = (has_wpqa()?(int)wpqa_count_posts_by_category(wpqa_questions_type,wpqa_question_categories,$tax_id):0);
		if ($cat_style == "icon_color" || $cat_style == "with_icon_2" || $cat_style == "with_icon_3" || $cat_style == "with_icon_4") {
			$category_color = get_term_meta($tax_id,prefix_terms."category_color",true);
		}
		if ($cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6") {
			$custom_cat_cover = get_term_meta($tax_id,prefix_terms."custom_cat_cover",true);
			if ($custom_cat_cover == "on") {
				$cat_cover = get_term_meta($tax_id,prefix_terms."cat_cover",true);
				$cat_share = get_term_meta($tax_id,prefix_terms."cat_share",true);
			}else {
				$cat_cover = himer_options("active_cover_category");
				$cat_share = himer_options("cat_share");
			}
			if (has_wpqa() && $cat_cover == "on") {
				$cover_link = wpqa_get_cat_cover_link(array("tax_id" => $tax_id,"cat_name" => $term->name,"cat_tax" => (isset($tax_question_available)?wpqa_question_categories:"")));
				if ($cover_link != "") {
					$cover_link = himer_get_aq_resize_url($cover_link,500,200);
					$custom_css = ' style="background-image: url('.$cover_link.');"';
				}
			}
		}

		$cat_class = ($cat_style == "with_icon" || $cat_style == "with_icon_4" || $cat_style == "icon_color" || $cat_style == "simple_follow" || $cat_style == "simple"?" community-card-layout2":"").($cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6"?" community-card-layout4 bg-overlay":" d-flex flex-wrap justify-content-between").(isset($cover_link) && $cover_link != ""?" cat-section-cover":"");
		$cat_css = (isset($category_color) && $category_color != "" && ($cat_style == "icon_color" || $cat_style == "with_icon_3" || $cat_style == "with_icon_4")?" style='background-color: rgba(".implode(",",himer_hex2rgb($category_color)).",0.1);'":"").(isset($custom_css)?$custom_css:"");

		$term_list .= '<div class="community-card cat-sections cat-sections-icon cat-section-'.$cat_style.$cat_class.'"'.$cat_css.'>
			<a href="'.esc_url(get_term_link($term)).'" title="'.esc_attr(sprintf(esc_html__('View all questions under %s','discy'),$term->name)).'"></a>
			<div class="'.($cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6"?"community__content":"community__info").'">';
				if ($cat_style != "with_cover_1" && $cat_style != "with_cover_3" && $cat_style != "with_cover_4" && $cat_style != "with_cover_6") {
					$term_list .= '<div class="d-flex">';
				}
					if (isset($cover_link) && $cover_link != "") {
						$term_list .= '<div class="cover-opacity"></div><div class="wpqa-cover-inner">';
					}
					if ($cat_style != "with_cover_1" && $cat_style != "with_cover_4") {
						$term_list .= '<span class="community__icon '.($cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6"?"mb-3":"mr-3").' cat-section-icon"'.(isset($category_color) && $category_color != "" && ($cat_style == "with_icon_2" || $cat_style == "with_icon_3" || $cat_style == "with_icon_4")?" style='".($cat_style == "with_icon_4"?"":"background-")."color: ".$category_color."'":"").'><i class="'.($category_icon != ""?esc_html($category_icon):"icon-folder").'"></i></span>';
					}
					$term_list .= '<div>
						<div class="community__links"><a href="'.esc_url(get_term_link($term)).'" title="'.esc_attr(sprintf(esc_html__('View all questions under %s','himer'),$term->name)).'">'.$term->name.'</a></div>
						<div'.($cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6"?" class='d-flex'":"").'>
							<span class="community__count count-cat-question"><span>'.$questions.'</span> '._n("Question","Questions",$questions,"himer").'</span>';
							if ($follow_category == "on") {
								$cats_follwers = (int)(is_array($cat_follow)?count($cat_follow):0);
								$term_list .= '<span class="community__count count-cat-follow"><span class="follow-cat-count">'.himer_count_number($cats_follwers)."</span> "._n("Follower","Followers",$cats_follwers,"himer").'</span>';
							}
						$term_list .= '</div>';
						if (isset($cover_link) && $cover_link != "") {
							$term_list .= '</div>';
						}
					$term_list .= '</div>';
			if ($cat_style != "with_cover_1" && $cat_style != "with_cover_3" && $cat_style != "with_cover_4" && $cat_style != "with_cover_6") {
					$term_list .= '</div><! End d-flex -->';
				$term_list .= '</div><! End community__info -->';
			}
			if (has_wpqa() && ($cat_style == "with_icon_1" || $cat_style == "simple_follow" || $cat_style == "with_icon_2" || $cat_style == "with_icon_3" || $cat_style == "with_icon_4" || $cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6")) {
				if (isset($widget) && $widget == true) {
					$follow_text = ($cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6"?true:false);
				}
				$term_list .= '<div class="'.($cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6"?"mt-3":"community__meta d-flex justify-content-end align-items-center").'">'.wpqa_follow_cat_button($tax_id,$user_id,'cat',$follow_text,'btn btn__semi__height'.($follow_text == true?"":" widget_follow_button"),'cat-sections-icon','follow-cat-count','btn__success','btn__danger').'</div>';
			}
			if ($cat_style == "with_cover_1" || $cat_style == "with_cover_3" || $cat_style == "with_cover_4" || $cat_style == "with_cover_6") {
				$term_list .= '</div><! End community__content -->';
			}
		$term_list .= '</div><! End community-card -->';
		$custom_css = '';
	}else {
		$term_list .= (isset($tax_question_available) && $cat_style != "simple" && $follow_category == "on"?"<div class='cat-sections-follow'>":"").'
		<div class="cat-sections community-card community-card-layout2 d-flex flex-wrap justify-content-between">
			<div class="community__info">
				<div class="d-flex">
					<div class="community__icon mr-3"><i class="'.($category_icon != ""?esc_html($category_icon):"icon-folder").'"></i></div>
					<div class="d-flex flex-wrap align-items-center">
						<div class="community__links"><a href="'.esc_url(get_term_link($term)).'" title="'.esc_attr(sprintf(($cats_tax == 'post' || $cats_tax == 'category'?esc_html__('View all posts under %s','himer'):esc_html__('View all questions under %s','himer')),$term->name)).'">'.$term->name.'</a></div>
					</div>
				</div>
			</div>';
			if (isset($tax_question_available) && $cat_style != "simple" && $follow_category == "on") {
				$cats_follwers = (int)(is_array($cat_follow)?count($cat_follow):0);
				$term_list .= '<div class="community__meta d-flex justify-content-end align-items-center"><div class="cat-section-follow">
					<div class="cat-follow-button"><i class="icon-users"></i><span class="follow-cat-count">'.himer_count_number($cats_follwers)."</span> "._n("Follower","Followers",$cats_follwers,"himer").'</div>
					'.(has_wpqa()?wpqa_follow_cat_button($tax_id,$user_id,'cat',true,'button-default-4 btn btn__semi__height','cat-section-follow','follow-cat-count','btn__success','btn__danger'):"").'
					<div class="clearfix"></div>
				</div></div></div>';
			}
		$term_list .= '</div>';
	}
$term_list .= '</div>';?>