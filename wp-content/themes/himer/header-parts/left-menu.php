<?php $left_menu = apply_filters("himer_left_menu",true);
if ($left_menu == true) {
	if ($tabs_menu_select == "left_menu") {
		$home_page_id = get_option("home_page_id");
		$get_home_tabs = himer_post_meta("home_tabs",$home_page_id);
		$first_one = (has_wpqa() && wpqa_plugin_version >= "5.9.3"?wpqa_home_setting($get_home_tabs):"");
		if (isset($get_home_tabs) && is_array($get_home_tabs)) {
			if (isset($first_one) && $first_one != "") {?>
				<ul>
					<?php if (has_wpqa() && wpqa_plugin_version >= "5.9.3") {
						wpqa_home_tabs($get_home_tabs,$first_one,"",$home_page_id);
					}?>
				</ul>
			<?php }
		}
	}else {
		$left_menu_s = (is_single() || is_page()?himer_post_meta("left_menu"):"");
		$left_menu   = (is_user_logged_in()?"wpqa_explore_login":"wpqa_explore");
		$left_menu_s = ($left_menu_s != "" && $left_menu_s != 0?$left_menu_s:"");
		wp_nav_menu(array('menu_class' => $class_left_menu,'container' => '','container_class' => 'nav_menu float_r',($left_menu_s != ""?"menu":"theme_location") => ($left_menu_s != "" && $left_menu_s != 0?$left_menu_s:$left_menu)));
	}
}?>