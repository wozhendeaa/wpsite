<?php do_action("himer_action_before_header");
$activate_register = himer_options("activate_register");
$activate_login = himer_options("activate_login");
$loader_option = himer_options("loader");
$mobile_bar = himer_options("mobile_bar");
$header_responsive_icon = himer_options("header_responsive_icon");
$mobile_sign = himer_options("mobile_sign");
$header_boxed = himer_options("header_boxed");
$header_skin = himer_options("header_skin");
$header_height = himer_options("header_height");
$header_fixed = himer_options("header_fixed");
$header_fixed_responsive = himer_options("header_fixed_responsive");
$search_value = apply_filters("wpqa_get_search_filter",false);
$header_search = himer_options("header_search");
$big_search = himer_options("big_search");
$live_search = himer_options('live_search');
$header_user_login = himer_options("header_user_login");
$user_login_style = himer_options("user_login_style");
$active_moderators = himer_options("active_moderators");
$activate_header_buttons = himer_options("activate_header_buttons");
$header_buttons_menu = himer_options("header_buttons_menu");
$header_buttons = himer_options("header_buttons");
$header_buttons_custom_menu = himer_options("header_buttons_custom_menu");
$header_buttons_style = himer_options("header_buttons_style");
$header_notifications = himer_options("header_notifications");
$active_notifications = himer_options("active_notifications");
$notifications_style = himer_options("notifications_style");
$custom_main_menu = himer_post_meta("custom_main_menu");
if ((is_single() || is_page()) && $custom_main_menu == "custom") {
	$main_menu = himer_post_meta("main_menu");
}
$second_header = himer_options("second_header");
$custom_second_menu = himer_post_meta("custom_second_menu");
if ((is_single() || is_page()) && ($custom_second_menu == "on" || $custom_second_menu == "off")) {
	$second_header = himer_post_meta("custom_second_menu");
}
if (is_author() || (has_wpqa() && wpqa_is_user_profile())) {
	$author_tabs = himer_options("author_tabs");
}
$second_menu_pages = himer_options("second_menu_pages");
$second_menu_custom_pages = himer_options("second_menu_custom_pages");
$second_menu_custom_pages = apply_filters("himer_second_menu_custom_pages",himer_options("second_menu_custom_pages"));
$second_menu_custom_pages = explode(",",$second_menu_custom_pages);
$second_header_tags = himer_options("second_header_tags");
$second_header_tags_type = himer_options("second_header_tags_type");
$second_header_more_tags = himer_options("second_header_more_tags");
$second_header_tags_page = himer_options("second_header_tags_page");
if ((isset($author_tabs) && $author_tabs == "on") || ($second_header == "on" && (((is_front_page() || is_home()) && $second_menu_pages == "home_page") || $second_menu_pages == "all_pages" || ($second_menu_pages == "all_posts" && is_singular("post")) || ($second_menu_pages == "all_questions" && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))) || (((is_single() || is_page()) && $custom_second_menu == "on")) || ($second_menu_pages == "custom_pages" && is_page() && isset($second_menu_custom_pages) && is_array($second_menu_custom_pages) && isset($post->ID) && in_array($post->ID,$second_menu_custom_pages))))) {
	$activate_second_menu = true;
}
$active_activity_log = himer_options("active_activity_log");
$active_referral = himer_options("active_referral");
$active_message = himer_options("active_message");
$header_messages = himer_options("header_messages");
$messages_style = himer_options("messages_style");
$active_points = himer_options("active_points");
$mobile_bar_apps = himer_options("mobile_bar_apps");
$mobile_apps_bar_skin = himer_options("mobile_apps_bar_skin");
$mobile_bar_apps_iphone = himer_options("mobile_bar_apps_iphone");
$mobile_bar_apps_android = himer_options("mobile_bar_apps_android");
$mobile_menu = himer_options("mobile_menu");

$custom_page_setting = himer_post_meta("custom_page_setting");
if ((is_single() || is_page()) && isset($custom_page_setting) && $custom_page_setting == "on") {
	$breadcrumbs = himer_post_meta("breadcrumbs");
	$sticky_sidebar = himer_post_meta("sticky_sidebar_s");
}else {
	$breadcrumbs = himer_options("breadcrumbs");
	$sticky_sidebar = himer_options("sticky_sidebar");
}
$breadcrumbs_style = himer_options("breadcrumbs_style");

$blog_h_where = himer_options("blog_h_where");
$cover_image = himer_options("cover_image");
$adv_404 = himer_options("adv_404");

$user_id = get_current_user_id();
$is_super_admin = is_super_admin($user_id);
$wpqa_profile_url = (has_wpqa() && $user_id > 0?wpqa_profile_url($user_id):"");
$confirm_email = (has_wpqa()?wpqa_users_confirm_mail():"");
$logged_only = himer_post_meta("logged_only");

$gender_class = '';
if (is_user_logged_in()) {
	$is_user_logged_in = true;
	$gender = get_the_author_meta('gender',$user_id);
	$gender_class = ($gender !== ''?($gender == "male" || $gender == 1?"him":"").($gender == "female" || $gender == 2?"her":"").($gender == "other" || $gender == 3?"other":""):'');
}

$tax_filter = apply_filters("himer_before_question_category",false);
$tax_question = apply_filters("himer_question_category",wpqa_question_categories);
$category_id = "";
if (is_category() || is_single() || is_tax(wpqa_question_categories) || $tax_filter == true) {
	if (is_tax(wpqa_question_categories) || $tax_filter == true) {
		$tax_id = get_term_by('slug',get_query_var('term'),$tax_question);
		$category_id = (isset($tax_id->term_id)?$tax_id->term_id:"");
	}else if (is_category()) {
		$category_id = esc_html(get_query_var('cat'));
	}else if (is_single()) {
		if (is_singular(wpqa_questions_type)) {
			$get_category = get_the_terms(get_the_ID(),wpqa_question_categories);
		}else {
			$get_category = get_the_category(get_the_ID());
		}
		if (!empty($get_category[0]->term_id)) {
			$category_single_id = $get_category[0]->term_id;
			$custom_logo = himer_term_meta("custom_logo",$category_single_id);
			if (isset($custom_logo) && $custom_logo == "on") {
				$logo_single = himer_term_meta("logo_single",$category_single_id);
				if ($logo_single == "on") {
					$category_id = $category_single_id;
				}
			}
		}
	}
	$custom_logo = himer_term_meta("custom_logo",$category_id);
	if ($custom_logo == "on") {
		$logo_display = himer_term_meta("logo_display",$category_id);
		$logo_img = himer_image_url_id(himer_term_meta("logo_img",$category_id));
		$retina_logo= himer_image_url_id(himer_term_meta("retina_logo",$category_id));
		$logo_height= himer_term_meta("logo_height",$category_id);
		$logo_width = himer_term_meta("logo_width",$category_id);
	}
}?>

<div class="background-cover"></div>
<?php if ($loader_option == "on") {?>
	<div class="loader"><i class="loader_html fa-spin"></i></div>
<?php }

/* Head content */
do_action("wpqa_head_content");

if ($mobile_bar_apps == "on" && ($mobile_bar_apps_iphone != "" || $mobile_bar_apps_android != "")) {
	$mobile_bar_apps_activate = true;
}
if (has_wpqa() && $confirm_email != "yes" && $mobile_bar == "on") {
	$mobile_bar_bottom_activate = true;
}

$dynamic_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebar_layout():"");
$wpqa_sidebars = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars():"");
$sidebar_where = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");?>

<div id="wrap" class="<?php echo (isset($is_user_logged_in)?"wrap-login":"wrap-not-login").(isset($activate_second_menu)?" w-header-full":"").("large" == $header_height?" w-header-large":"").($header_fixed == "on"?" w-fixed-hidden-header".($header_fixed_responsive == "on"?" stop-mobile-fixed":""):"").((isset($mobile_bar_apps_activate) && !isset($mobile_bar_bottom_activate)) || (!isset($mobile_bar_apps_activate) && isset($mobile_bar_bottom_activate))?" w-hidden-header-one":"").(isset($mobile_bar_apps_activate) && isset($mobile_bar_bottom_activate)?" w-hidden-header-both":"").($header_fixed == "on"?" fixed-enabled":"").($logged_only == "on"?" wrap-logged-only":"").($gender_class != ""?" wrap-gender-".$gender_class:"").apply_filters("himer_filter_wrap_css","")?>">
	<div class="hidden-header<?php echo ("on" != $header_search?" header-no-search":"").("large" == $header_height?" header-large":"").("on" == $big_search?" header-big-search":"").(isset($activate_second_menu)?" header-full":"").($header_fixed == "on"?" fixed-hidden-header":"").((isset($mobile_bar_apps_activate) && !isset($mobile_bar_bottom_activate)) || (!isset($mobile_bar_apps_activate) && isset($mobile_bar_bottom_activate))?" hidden-header-one":"").(isset($mobile_bar_apps_activate) && isset($mobile_bar_bottom_activate)?" hidden-header-both":"")." header-".$header_skin.(isset($mobile_bar_bottom_activate)?" mobile_bar_active":"").(isset($mobile_bar_apps_activate)?" mobile_apps_bar_active":"")?>">
		<?php $mobile_bar_layout = "top";
		include locate_template("header-parts/mobile-bar.php");?>
		<header class="header header-<?php echo esc_attr($header_skin)?>" itemscope="" itemtype="https://schema.org/WPHeader">
			<nav class="navbar navbar-expand-lg" itemscope="" itemtype="https://schema.org/SiteNavigationElement">
				<div class="container">
					<?php include locate_template("header-parts/logo.php");

					include locate_template("header-parts/navbar-nav.php");
					
					include locate_template("header-parts/header-actions.php");?>
				</div><!-- /.container -->
			</nav><!-- /.navabr -->
		</header><!-- /.Header -->
		<?php $mobile_bar_layout = "bottom";
		include locate_template("header-parts/mobile-bar.php");?>
	</div><!-- End hidden-header -->

	<?php $sticky_question_bar = himer_options("sticky_question_bar");
	if (is_singular(wpqa_questions_type) && $sticky_question_bar == "on") {
		include locate_template("header-parts/question-sticky-bar.php");
	}?>

	<main class="page-main">
		<?php do_action("himer_action_before_slider");

		$silder_file = apply_filters("himer_filter_silder_file",true);
		if ($silder_file == true) {
			include locate_template("header-parts/slider.php");
		}

		do_action("himer_action_after_slider");

		$filter_call_action = apply_filters("himer_filter_call_action",true);
		if ($filter_call_action == true) {
			include locate_template("header-parts/call-action.php");
		}

		$filter_cover_image = apply_filters("himer_filter_cover_image",true);
		if ($filter_cover_image == true) {
			do_action("wpqa_cover_image");
		}

		$filter_category_cover = apply_filters("himer_filter_category_cover",true);
		if ($filter_category_cover == true) {
			do_action("wpqa_category_cover");
		}

		$filter_group_cover = apply_filters("himer_filter_group_cover",true);
		if (has_wpqa() && $filter_group_cover == true) {
			include locate_template("includes/group-cover.php");
		}

		if ($blog_h_where == "header") {
			include locate_template("includes/blog-header-footer.php");
		}

		$update_profile = (has_wpqa() && isset($is_user_logged_in)?wpqa_update_profile($user_id):"");
		/* Adv */
		if (is_404() && $adv_404 == "on") {
			$adv_404 = "on";
		}else {
			$adv_404 = "";
		}
		if (has_wpqa() && $cover_image == "on" && wpqa_is_user_profile() && !wpqa_is_user_edit_profile() && !wpqa_is_user_owner()) {
			$hide_right_breadcrumb = true;
		}
		if ($breadcrumbs_style == "style_2") {
			if (!is_home() && !is_front_page() && isset($breadcrumbs) && $breadcrumbs == "on" && $confirm_email != "yes" && $site_users_only != "yes") {
				himer_breadcrumbs(($update_profile == "yes"?esc_html__("Edit profile","himer"):""),($update_profile == "yes" || (isset($hide_right_breadcrumb))?false:true),$breadcrumbs_style);
			}
			/* Header adv */
			if (has_wpqa() && wpqa_plugin_version >= "5.8" && (($adv_404 != "on" && is_404()) || !is_404())) {
				echo wpqa_ads("header_adv_type_1","header_adv_link_1","header_adv_code_1","header_adv_href_1","header_adv_img_1","","on","aalan-header","on");
			}
		}

		include locate_template("hero/hero.php");?>
		
		<section class="main-section">
			<div class="container">
				<div class="row-boot the-main-inner justify-content-md-center<?php echo ("menu_sidebar" == $sidebar_where?" main-menu-sidebar":"")?>">
					<?php do_action("himer_after_himer_main_inner");
					$tabs_menu_select = get_option("tabs_menu_select");
					$site_style = himer_options("site_style");
					if ($site_style != "style_3" && $site_style != "style_4" && $confirm_email != "yes" && $site_users_only != "yes" && ($tabs_menu_select == "left_menu" || $sidebar_where == "menu_sidebar" || $sidebar_where == "menu_left")) {?>
						<div class="col-boot-lg-2 warp-left-menu"> 
							<?php $left_area = himer_options("left_area");
							if ($left_area == "sidebar") {?>
								<div class="nav_menu_sidebar float_r<?php echo ($sticky_sidebar == "nav_menu" || $sticky_sidebar == "side_menu_bar"?" fixed_nav_menu":"")?>">
									<div class="nav_menu">
										<?php get_sidebar("left");?>
									</div><!-- End nav_menu -->
								</div><!-- End nav_menu_sidebar -->
							<?php }else {
								$left_menu_style = himer_options("left_menu_style");?>
								<nav class="nav_menu float_r<?php echo ($sticky_sidebar == "nav_menu" || $sticky_sidebar == "side_menu_bar"?" fixed_nav_menu":"").($left_menu_style == "style_2"?" nav_menu_2":"").($left_menu_style == "style_3"?" nav_menu_3":"")?>">
									<h3 class="screen-reader-text"><?php esc_html_e('Explore','himer')?></h3>
									<?php $class_left_menu = "menu";
									include locate_template("header-parts/left-menu.php");?>
								</nav><!-- End nav_menu -->
							<?php }
							if (has_wpqa() && wpqa_plugin_version >= "5.8" && (($adv_404 != "on" && is_404()) || !is_404())) {
								echo wpqa_ads("left_menu_adv_type","left_menu_adv_link","left_menu_adv_code","left_menu_adv_href","left_menu_adv_img","","on","aalan-left-menu","on");
							}?>
						</div><!-- /.col-boot-lg-2 -->
					<?php }?>
					<div class="col-boot-sm-12 col-boot-md-12 warp-main-content <?php echo ("yes" == $confirm_email || $site_users_only == "yes"?"main_full":($dynamic_sidebar != "" && is_active_sidebar($dynamic_sidebar)?$wpqa_sidebars:"main_full main_center col-boot-lg-8"))?>">
						<div class="wrap-main-content <?php echo apply_filters("himer_wrap_main_content_class",false)?>">
							<?php if ($breadcrumbs_style != "style_2" && !is_home() && !is_front_page() && isset($breadcrumbs) && $breadcrumbs == "on" && $confirm_email != "yes" && $site_users_only != "yes") {
								himer_breadcrumbs(($update_profile == "yes"?esc_html__("Edit profile","himer"):""),($update_profile == "yes" || (isset($hide_right_breadcrumb))?false:true),$breadcrumbs_style);
							}
							if (has_wpqa()) {
								/* Session */
								do_action("wpqa_show_session");
								/* Check the user account */
								wpqa_check_user_account(true,true);
								/* Header content */
								do_action("wpqa_header_content",array("user_id" => (isset($user_id)?$user_id:0),"update_profile" => $update_profile));
							}
							do_action("himer_after_inner_content");
							if ($breadcrumbs_style != "style_2") {
								/* Header adv */
								if (has_wpqa() && wpqa_plugin_version >= "5.8" && (($adv_404 != "on" && is_404()) || !is_404())) {
									echo wpqa_ads("header_adv_type_1","header_adv_link_1","header_adv_code_1","header_adv_href_1","header_adv_img_1","","on","aalan-header","on");
								}
							}?>
							<div class="clearfix"></div>