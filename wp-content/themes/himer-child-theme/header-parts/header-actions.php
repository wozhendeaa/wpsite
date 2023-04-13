<ul class="header-actions list-unstyled mb-0 d-flex align-items-center">
	<?php $show_search = apply_filters("himer_show_search",true);
	if ($header_search == "on" && $show_search == true && $big_search == "on") {
		include locate_template("header-parts/header-search.php");
	}

	if (has_wpqa() && !isset($is_user_logged_in)) {
		if ($activate_register != "disabled" && $mobile_sign == "signup") {
			$title_mobile_button = esc_html__("Sign Up","himer");
			$class_mobile_button = "open-register-popup";
			$hook_mobile_button = "signup";
			$url_mobile_button = wpqa_signup_permalink();
		}else if ($activate_login != 'disabled') {
			$title_mobile_button = esc_html__("Sign In","himer");
			$class_mobile_button = "open-login-popup";
			$hook_mobile_button = "login";
			$url_mobile_button = wpqa_login_permalink();
		}
		if (isset($title_mobile_button) && isset($class_mobile_button) && isset($hook_mobile_button) && isset($url_mobile_button)) {?>
			<li class="menu-item menu-item-button-small himer_hide">
				<a title="<?php echo esc_attr($title_mobile_button)?>" class="button-sign-in <?php echo esc_attr($class_mobile_button).apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_'.esc_attr($hook_mobile_button),'')?>" href="<?php echo esc_url($url_mobile_button)?>"><i class="<?php echo ("" != $header_responsive_icon?$header_responsive_icon:"icon-lock")?>"></i></a>
			</li>
		<?php }
		if ($activate_login != 'disabled') {?>
			<li class="menu-item menu-item-sign-in">
				<a class="btn open-login-popup header-actions__btn button-sign-in<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_login','')?>" href="<?php echo (has_wpqa()?wpqa_login_permalink():"#")?>"><?php esc_html_e('Sign In','himer')?></a>
			</li>
		<?php }
		if ($activate_register != "disabled") {?>
			<li class="menu-item menu-item-sign-up">
				<a class="btn btn__secondary open-register-popup header-actions__btn button-sign-up<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_signup','')?>" href="<?php echo (has_wpqa()?wpqa_signup_permalink():"#")?>"><?php esc_html_e('Sign Up','himer')?></a>
			</li>
		<?php }
	}else {
		$header_profile_menu = 'header_profile_menu';
		$locations = get_nav_menu_locations();
		if (isset($locations[$header_profile_menu])) {
	        $header_profile_items = wp_get_nav_menu_items($locations[$header_profile_menu]);
			if (is_array($header_profile_items) && !empty($header_profile_items)) {
				foreach ($header_profile_items as $menu_key => $menu_value) {
					if (isset($menu_value->url) && $menu_value->url == "#wpqa-notifications") {
						$wpqa_notifications_item = true;
					}else if (isset($menu_value->url) && $menu_value->url == "#wpqa-messages") {
						$wpqa_messages_item = true;
					}else if (isset($menu_value->url) && $menu_value->url == "#wpqa-pending-questions") {
						$wpqa_pending_questions_item = true;
					}else if (isset($menu_value->url) && $menu_value->url == "#wpqa-pending-posts") {
						$wpqa_pending_posts_item = true;
					}
				}
			}
		}
		$num_message = (int)(isset($user_id)?get_user_meta($user_id,"wpqa_new_messages_count",true):0);
		$num_message = ($num_message > 0?$num_message:0);
		$num_notification = (int)get_user_meta($user_id,$user_id.'_new_notification',true);
		$num_notification = ($num_notification > 0?$num_notification:0);
		if (isset($wpqa_notifications_item)) {
			$all_num_notification = $num_notification;
		}
		$all_num_notification = (isset($all_num_notification) && $all_num_notification > 0?$all_num_notification:0);
		if (has_wpqa() && $active_message == "on" && isset($wpqa_messages_item)) {
			$all_num_message = $num_message;
		}
		$all_num_message = (isset($all_num_message) && $all_num_message > 0?$all_num_message:0);
		if (has_wpqa() && ($is_super_admin || $active_moderators == "on") && (isset($wpqa_pending_questions_item) || isset($wpqa_pending_posts_item))) {
			$user_moderator = get_user_meta($user_id,prefix_author."user_moderator",true);
			if ($is_super_admin || $user_moderator == "on") {
				$moderator_categories = get_user_meta($user_id,prefix_author."moderator_categories",true);
				if ($is_super_admin || (is_array($moderator_categories) && !empty($moderator_categories))) {
					$num_pending_questions = $num_pending_posts = 0;
					if (isset($wpqa_pending_questions_item)) {
						if ($is_super_admin || in_array("q-0",$moderator_categories)) {
							$num_pending_questions = wpqa_count_posts_by_type(array(wpqa_questions_type,wpqa_asked_questions_type),"draft");
						}else {
							$moderator_categories_questions = wpqa_remove_item_by_value($moderator_categories,"p-0");
							if (is_array($moderator_categories_questions) && !empty($moderator_categories_questions)) {
								$num_pending_questions = wpqa_count_posts_by_user(0,wpqa_questions_type,"draft",$moderator_categories_questions);
							}
						}
					}
					if (isset($wpqa_pending_posts_item)) {
						if ($is_super_admin || in_array("p-0",$moderator_categories)) {
							$num_pending_posts = wpqa_count_posts_by_type("post","draft");
						}else {
							$moderator_categories_posts = wpqa_remove_item_by_value($moderator_categories,"q-0");
							if (is_array($moderator_categories_posts) && !empty($moderator_categories_posts)) {
								$num_pending_posts = wpqa_count_posts_by_user(0,"post","draft",$moderator_categories_posts);
							}
						}
					}
					$num_pending = $num_pending_questions+$num_pending_posts;
				}
			}
		}
		$num_pending = (isset($num_pending) && $num_pending != "" && $num_pending > 0?$num_pending:0);
		$num_all = $all_num_message+$all_num_notification+$num_pending;
		if (has_wpqa() && $header_messages == "on" && $active_message == "on") {?>
			<li class="menu-item menu-item-has-children menu-item-messages<?php echo ("dark" == $messages_style?" user-messages-2":"")?>">
				<a href="<?php echo esc_url(wpqa_get_profile_permalink($user_id,"messages"))?>" data-toggle="dropdown" class="dropdown-toggle">
					<i class="icon-android-mail font-xxl"></i>
					<?php echo ("" != $num_message && $num_message > 0?'<span class="notifications-count">'.($num_message <= 99?$num_message:"99+").'</span>':"")?>
				</a>
				<div class="sub-menu notifications-dropdown-menu">
					<?php $messages_number = himer_options("messages_number");
					echo wpqa_get_messages($user_id,$messages_number,"on",false,true)?>
				</div><!-- /.sub-menu -->
			</li>
		<?php }
		if (has_wpqa() && $header_notifications == "on" && $active_notifications == "on") {?>
			<li class="menu-item menu-item-has-children menu-item-notifications<?php echo ("dark" == $notifications_style?" user-notifications-2":"")?>">
				<a href="<?php echo esc_url(wpqa_get_profile_permalink($user_id,"notifications"))?>" data-toggle="dropdown" class="dropdown-toggle">
					<i class="icon-ios-bell font-xxl"></i>
					<?php echo ("" != $num_notification && $num_notification > 0?'<span class="notifications-count">'.($num_notification <= 99?$num_notification:"99+").'</span>':"")?>
				</a>
				<div class="sub-menu notifications-dropdown-menu">
					<?php $notifications_number = himer_options("notifications_number");
					echo wpqa_get_notifications($user_id,$notifications_number,"on",false,true,'list-unstyled mb-0')?>
				</div><!-- /.sub-menu -->
			</li>
		<?php }
		$header_buttons = apply_filters("himer_header_buttons",$header_buttons);
		$header_buttons_keys = (isset($header_buttons) && is_array($header_buttons)?array_keys($header_buttons):"");
		$show_header_small_menu = apply_filters("himer_show_header_small_menu",true,$header_buttons,(isset($first_one)?$first_one:""),$user_id);
		if ($show_header_small_menu == true && $activate_header_buttons == "on") {
			if ($header_buttons_menu != "menu" && isset($header_buttons) && is_array($header_buttons) && !empty($header_buttons)) {
				$i_count = 0;
				while ($i_count < count($header_buttons)) {
					if (isset($header_buttons[$header_buttons_keys[$i_count]]["value"]) && $header_buttons[$header_buttons_keys[$i_count]]["value"] != "" && $header_buttons[$header_buttons_keys[$i_count]]["value"] != "0") {
						$first_one = $i_count;
						break;
					}
					$i_count++;
				}
				if (isset($first_one) && $first_one !== "") {
					$first_one = $header_buttons[$header_buttons_keys[$first_one]]["value"];
				}
			}
			if (has_wpqa() && (($header_buttons_menu == "menu" && $header_buttons_custom_menu != "") || ($header_buttons_menu != "menu" && isset($first_one) && $first_one !== ""))) {?>
				<li class="menu-item menu-item-has-children menu-item-action<?php echo ("dark" == $header_buttons_style?" header-button-2":"")?>">
					<a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="icon-plus-circled font-lg"></i></a>
					<?php if ($header_buttons_menu == "menu" && $header_buttons_custom_menu != "") {
						wp_nav_menu(array('container' => '','menu_class' => 'sub-menu','menu_id' => 'sub-menu','menu' => $header_buttons_custom_menu));
					}else if ($header_buttons_menu != "menu" && isset($first_one) && $first_one !== "") {?>
						<ul class="sub-menu">
							<?php foreach ($header_buttons as $key => $value) {
								do_action("himer_action_header_buttons",$header_buttons,$key,$value);
								if ($key == "question" && isset($header_buttons["question"]["value"]) && $header_buttons["question"]["value"] == "question") {?>
									<li class="menu-item">
										<a href="<?php echo esc_url(wpqa_add_question_permalink())?>" class="wpqa-question d-flex align-items-center">
											<i class="icon-android-textsms font-xl mr-2"></i>
											<span><?php esc_html_e("Ask A Question","himer")?></span>
										</a>
									</li><!-- /.nav-item -->
								<?php }
								if ($key == "post" && isset($header_buttons["post"]["value"]) && $header_buttons["post"]["value"] == "post") {?>
									<li class="menu-item">
										<a href="<?php echo esc_url(wpqa_add_post_permalink())?>" class="wpqa-post d-flex align-items-center">
											<i class="icon-ios-bookmarks font-xl mr-2"></i>
											<span><?php esc_html_e("Post An Article","himer")?></span>
										</a>
									</li><!-- /.nav-item -->
								<?php }
								if ($key == "group" && isset($header_buttons["group"]["value"]) && $header_buttons["group"]["value"] == "group") {?>
									<li class="menu-item">
										<a href="<?php echo esc_url(wpqa_add_group_permalink())?>" class="d-flex align-items-center">
											<i class="icon-android-contacts font-xl mr-2"></i>
											<span><?php esc_html_e("Create A Group","himer")?></span>
										</a>
									</li><!-- /.nav-item -->
								<?php }
							}?>
						</ul><!-- /.sub-menu -->
					<?php }?>
				</li>
			<?php }
		}
		if ($header_user_login == "on") {
			$display_name = get_the_author_meta('display_name',$user_id);
			$subscriptions_payment = himer_options("subscriptions_payment");
			$show_header_menu = apply_filters("himer_show_header_menu",true,$user_id);
			if ($show_header_menu == true) {
				if (!isset($locations[$header_profile_menu])) {
					$user_link_only = true;
				}?>
				<li class="menu-item menu-item-user<?php echo (isset($user_link_only)?"":" menu-item-has-children".($user_login_style == "dark"?" user-login-2":""))?>">
					<a href="<?php echo (isset($wpqa_profile_url)?$wpqa_profile_url:"")?>"<?php echo (isset($user_link_only)?"":" data-toggle='dropdown'")?> class="account-btn<?php echo (isset($user_link_only)?"":" dropdown-toggle")?>">
						<?php $user_avatar_header = apply_filters("himer_user_avatar_header",true);
						if ($user_avatar_header == true) {
							do_action("wpqa_user_avatar",array("user_id" => $user_id,"size" => apply_filters("himer_header_profile_size","30"),"name" => $display_name,"class" => "rounded-circle"));
						}else {
							do_action("himer_action_user_avatar_header",$user_id);
						}
						echo ("" != $num_all && $num_all > 0?'<span class="notifications-count">'.($num_all <= 99?$num_all:"99+").'</span>':"")?>
					</a>
					<?php if (!isset($user_link_only)) {
						include wpqa_get_template("header-profile-menu.php","profile/");
					}?>
				</li>
			<?php }
		}
	}

	if ($header_search == "on" && $show_search == true && $big_search != "on") {
		include locate_template("header-parts/header-search.php");
	}

	$skin_switcher = himer_options('skin_switcher');
	if ($skin_switcher == 'on') {
		$skin_switcher_position = himer_options('skin_switcher_position');
		if ($skin_switcher_position == "header") {
			if (is_user_logged_in()) {
				$get_dark = get_user_meta($user_id,'wpqa_get_dark',true);
			}else {
				$uniqid_cookie = himer_options('uniqid_cookie');
				$get_dark = (isset($_COOKIE[$uniqid_cookie.'wpqa_get_dark'])?$_COOKIE[$uniqid_cookie.'wpqa_get_dark']:'');
			}
			$get_dark = ($get_dark != ''?($get_dark == 'dark'?'dark':'light'):$site_skin);
			if ($get_dark == 'dark') {?>
				<li class="menu-item header-skin-li"><span class="dark-light-switcher light-switcher"><i class="icon-ios-sunny-outline font-xxxl"></i></span></li>
			<?php }else {?>
				<li class="menu-item header-skin-li"><span class="dark-light-switcher dark-switcher"><i class="icon-ios-sunny font-xxxl"></i></span></li>
			<?php }
		}
	}?>
</ul><!-- /.header-actions -->