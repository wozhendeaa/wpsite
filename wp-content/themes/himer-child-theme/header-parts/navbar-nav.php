<button class="open-mobileMenu d-block d-lg-none" type="button"><i class="icon-navicon-round"></i></button>
<div class="navbar-collapse<?php echo ("light" == $mobile_menu?" light-mobile-menu":($mobile_menu == "dark"?" dark-mobile-menu":" gray-mobile-menu"))?>" id="mainNavigation">
	<button type="button" class="close-mobileMenu d-block d-lg-none btn btn__danger btn__sm"><i class="icon-close-round"></i></button>
	<?php if (!isset($its_not_login)) {?>
		<form role="search" method="get" class="main-search-form search-form-mobile d-block d-lg-none" action="<?php do_action("wpqa_search_permalink")?>">
			<input type="search" name="search" class="form-control<?php echo ("on" == $live_search?" live-search-icon live-search":"")?>"<?php echo ("on" == $live_search?" autocomplete='off'":"")?> value="<?php if ($search_value != "") {echo esc_html($search_value);}else {esc_html_e("Hit enter to search","himer");}?>" onfocus="if(this.value=='<?php esc_attr_e("Hit enter to search","himer")?>')this.value='';" onblur="if(this.value=='')this.value='<?php esc_attr_e("Hit enter to search","himer")?>';">
			<?php if ($live_search == "on") {?>
				<div class="loader_2 search_loader"></div>
				<div class="live-search-results mt-2 search-results results-empty"></div>
			<?php }?>
			<input type="hidden" name="search_type" class="search_type" value="<?php do_action("wpqa_search_type")?>">
			<button type="submit" class="search-form__btn"><i class="icon-ios-search-strong"></i></button>
		</form>
	<?php }

	if (($header_search == "on" && $big_search != "on") || $header_search != "on") {
		$main_menu_s = ((is_single() || is_page()) && isset($main_menu)?$main_menu:"");
		$main_menu   = (isset($is_user_logged_in)?"header_menu_login":"header_menu");
		$main_menu_s = ($main_menu_s != "" && $main_menu_s != 0?$main_menu_s:"");
		wp_nav_menu(array('container' => '','menu_class' => 'navbar-nav menu flex',($main_menu_s != ""?"menu":"theme_location") => ($main_menu_s != "" && $main_menu_s != 0?$main_menu_s:$main_menu),'fallback_cb' => 'himer_nav_fallback'));
	}

	if (isset($activate_second_menu)) {?>
		<div class="navbar-secondary align-items-center<?php echo (isset($author_tabs) && $author_tabs == "on"?" navbar-secondary__centerd":"")?>">
			<?php $tabs_menu = get_option("tabs_menu");
			$tabs_menu_select = get_option("tabs_menu_select");
			if ((is_single() || is_page()) && $custom_second_menu == "on") {
				$custom_menu = true;
			}
			if (isset($author_tabs) && $author_tabs == "on") {
				$wpqa_user_id          = esc_html(get_query_var(apply_filters('wpqa_user_id','wpqa_user_id')));
				$show_point_favorite   = get_user_meta($wpqa_user_id,"show_point_favorite",true);
				$ask_question_to_users = wpqa_options("ask_question_to_users");
				$pay_ask               = wpqa_options("pay_ask");
				if ($ask_question_to_users == "on") {
					$asked_questions = wpqa_count_asked_question($wpqa_user_id,"=");
				}
				$list_child = "li";
				$menu_class = "navbar-nav menu flex";
				include wpqa_get_template("head-tabs.php","profile/");
			}else if (($tabs_menu == "on" || $tabs_menu_select == "second_menu") && !isset($custom_menu)) {
				$home_page_id = get_option("home_page_id");
				$get_home_tabs = himer_post_meta("home_tabs",$home_page_id);
				$tabs_menu_icons = himer_post_meta("tabs_menu_icons",$home_page_id);
				$first_one = (has_wpqa() && wpqa_plugin_version >= "5.9.3"?wpqa_home_setting($get_home_tabs):"");
				if (isset($get_home_tabs) && is_array($get_home_tabs) && isset($first_one) && $first_one != "") {?>
					<ul class="navbar-nav menu flex">
						<?php if (has_wpqa() && wpqa_plugin_version >= "5.9.3") {
							wpqa_home_tabs($get_home_tabs,$first_one,"",$home_page_id,"",$tabs_menu_icons);
						}?>
					</ul>
				<?php }
			}else {
				$second_menu_s = (is_single() || is_page()?himer_post_meta("second_menu"):"");
				$second_menu   = (isset($is_user_logged_in)?"header_2_menu_login":"header_2_menu");
				$second_menu_s = ($second_menu_s != "" && $second_menu_s != 0?$second_menu_s:"");
				wp_nav_menu(array('container' => '','menu_class' => 'navbar-nav menu flex',($second_menu_s != ""?"menu":"theme_location") => ($second_menu_s != "" && $second_menu_s != 0?$second_menu_s:$second_menu),'fallback_cb' => 'himer_nav_fallback'));
			}

			if ($sidebar_where == "left_menu") {
				$class_left_menu = "navbar-nav-mobile navbar-nav menu flex";
				include locate_template("header-parts/left-menu.php");
			}

			if (!isset($author_tabs) && $second_header_tags == "on") {?>
				<div class="tags-list tagcloud d-flex flex-wrap list-unstyled mb-0">
					<?php if ($second_header_tags_type == 'questions') {
						$tag_type = array('taxonomy' => wpqa_question_tags);
						$tag_tax = wpqa_question_tags;
					}else {
						$tag_type = array();
						$tag_tax = "post_tag";
					}
					$args = array_merge(array('smallest' => 8,'largest' => 22,'unit' => 'pt','number' => 5,'topic_count_text_callback' => 'wpqa_'.$tag_tax.'_callback'),$tag_type);
					wp_tag_cloud($args);
					if ($second_header_more_tags == "on") {
						$tags = get_terms($tag_tax);
						if (is_array($tags) && !empty($tags) && count($tags) > 5) {
							$pages = get_pages(array('meta_key' => '_wp_page_template','meta_value' => 'template-tags.php'));
							$second_header_tags_page = ($second_header_tags_page > 0?$second_header_tags_page:(isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?$pages[0]->ID:0));
							if ($second_header_tags_page > 0 && isset($pages) && isset($pages[0]) && isset($pages[0]->ID)) {
								echo '<a target="_blank" href="'.get_permalink($pages[0]->ID).'" class="active">'.sprintf(esc_html__("+%s More","himer"),count($tags) - 5).'</a>';
							}
						}
					}?>
				</div>
			<?php }?>
		</div>
	<?php }?>
</div><!-- /.navbar-collapse -->