<?php
if (is_admin() && isset($_GET['activated']) && $pagenow == "themes.php") {
	flush_rewrite_rules(true);
	wp_redirect(admin_url('admin.php?page=registration'));
	exit;
}
define('himer_framework_dir',get_template_directory_uri().'/admin/');

/* Require files */
require_once locate_template("admin/plugins/class-tgm-plugin-activation.php");
require_once locate_template("admin/plugins/plugins.php");
if (is_admin() && (isset($_GET['page']) && $_GET['page'] == "options") && ($pagenow == "admin.php" || $pagenow == "themes.php")) {
	require_once locate_template("admin/options.php");
}
require_once locate_template("admin/widgets.php");
require_once locate_template("admin/himer.php");
require_once locate_template("admin/functions/main_functions.php");
require_once locate_template("admin/functions/resizer.php");
require_once locate_template("admin/functions/widget_functions.php");
require_once locate_template("admin/functions/nav_menu.php");
require_once locate_template("admin/functions/register_post.php");

/* Updater */
require_once get_template_directory().'/admin/updater/elitepack-config.php';

/* Widgets */
include locate_template("admin/widgets/about.php");
include locate_template("admin/widgets/adv-120x240.php");
include locate_template("admin/widgets/adv-120x600.php");
include locate_template("admin/widgets/adv-125x125.php");
include locate_template("admin/widgets/adv-234x60.php");
include locate_template("admin/widgets/adv-250x250.php");
include locate_template("admin/widgets/counter.php");
include locate_template("admin/widgets/facebook.php");
include locate_template("admin/widgets/social.php");
include locate_template("admin/widgets/subscribe.php");
include locate_template("admin/widgets/twitter.php");
include locate_template("admin/widgets/video.php");

/* Titles */
include locate_template("includes/titles.php");

/* Body classes */
add_filter('body_class','himer_body_classes');
if (!function_exists('himer_body_classes')) {
	function himer_body_classes($classes) {
		if (is_single() || is_page()) {
			$custom_page_setting = himer_post_meta("custom_page_setting");
			if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
				$question_answers = himer_options("question_answers");
				if ($custom_page_setting == "on") {
					$question_answers = himer_post_meta("post_comments");
				}
				
				if ((comments_open() || get_comments_number()) && $question_answers == "on") {
					// Answers
				}else {
					$classes[] = 'question-no-answers';
				}
			}
			
			if (isset($custom_page_setting) && $custom_page_setting == "on") {
				$breadcrumbs = himer_post_meta("breadcrumbs");
			}else {
				$breadcrumbs = himer_options("breadcrumbs");
			}
			
			$classes[] = ($breadcrumbs == "on"?"page-with-breadcrumbs":"page-no-breadcrumbs");
		}
		
		if ((is_page() || is_single()) && !is_home() && !is_front_page()) {
			$classes[] = 'single_page';
			if (!is_page_template()) {
				$classes[] = 'single_page_no';
			}
		}
		$site_users_only = (has_wpqa()?wpqa_site_users_only():"");
		$under_construction = (has_wpqa()?wpqa_under_construction():"");
		$wp_page_template = himer_post_meta("_wp_page_template","",false);
		$classes[] = ($wp_page_template == "template-landing.php" || $under_construction == "on" || $site_users_only == "yes"?"main_users_only":"main_for_all");
		$active_lightbox = himer_options("active_lightbox");
		if ($active_lightbox == "on") {
			$classes[] = 'active-lightbox';
		}
		
		$site_width = himer_options("site_width");
		if ($site_width >= 1180) {
			$classes[] = "himer-custom-width";
		}
		$left_area = himer_options("left_area");
		if ($left_area == "sidebar") {
			$classes[] = "himer-left-sidebar";
		}
		$activate_male_female = apply_filters("wpqa_activate_male_female",false);
		if ($activate_male_female == true) {
			$classes[] = "activate-main-gender";
		}
		return $classes;
	}
}
/* himer_fonts_url */
function himer_fonts_url() {
	$font_url = '';
	$show_fonts = apply_filters("himer_show_fonts",true);
	if ($show_fonts == true) {
		if ('off' !== _x('on','Google font: on or off','himer')) {
			$main_font   = himer_options("main_font");
			$earlyaccess_main = himer_earlyaccess_fonts($main_font["face"]);
			$safe_fonts  = array(
				'arial'      => 'Arial',
				'verdana'    => 'Verdana',
				'trebuchet'  => 'Trebuchet',
				'times'      => 'Times New Roman',
				'tahoma'     => 'Tahoma',
				'geneva'     => 'Geneva',
				'georgia'    => 'Georgia',
				'palatino'   => 'Palatino',
				'helvetica'  => 'Helvetica',
				'museo_slab' => 'Museo Slab'
			);
			if ((isset($main_font["face"]) && $earlyaccess_main != "earlyaccess" && (($main_font["face"] != "Default font" && $main_font["face"] != "default" && $main_font["face"] != "") || $main_font["face"] == "default" || $main_font["face"] == "Default font" || $main_font["face"] == "") && !in_array($main_font["face"],$safe_fonts))) {
				$font_url = $font_url = "https://fonts.googleapis.com/css2?family=".((is_rtl()?"'Droid Arabic Kufi',":"").(isset($main_font["face"]) && $main_font["face"] != "Default font" && $main_font["face"] != "default" && $main_font["face"] != ""?str_ireplace("+"," ",$main_font["face"]):'Roboto').':wght@400;500;700&display=swap');
			}
		}
	}
	return $font_url;
}
/* himer_scripts_styles */
if (!function_exists('himer_scripts_styles')) {
	function himer_scripts_styles() {
		do_action('himer_scripts_styles');
		$search_type = (has_wpqa() && wpqa_is_search()?wpqa_search_type():'');
		$protocol = is_ssl() ? 'https' : 'http';
		wp_enqueue_style('himer-ionicons',get_template_directory_uri().'/css/ionicons.min.css');
		wp_enqueue_style('prettyPhoto',get_template_directory_uri().'/css/prettyPhoto.css');
		$active_awesome = himer_options('active_awesome');
		if ($active_awesome == 'on') {
			wp_enqueue_style('himer-font-awesome',get_template_directory_uri( __FILE__ ).'/css/fontawesome/css/fontawesome-all.min.css');
		}
		wp_enqueue_style('himer-main-style',get_template_directory_uri().'/style.css','',null,'all');
		$main_font = himer_options('main_font');
		if (isset($main_font['face'])) {
			$earlyaccess_main = himer_earlyaccess_fonts($main_font['face']);
			if ($earlyaccess_main == 'earlyaccess') {
				$main_font_style = strtolower(str_replace('+','',$main_font['face']));
				wp_enqueue_style('himer-'.$main_font_style, $protocol.'://fonts.googleapis.com/earlyaccess/'.$main_font_style.'.css');
			}else {
				wp_enqueue_style('himer-fonts',himer_fonts_url(),array(),himer_theme_version);
			}
		}
		
		if (is_rtl()) {
			wp_enqueue_style('himer-bootstrap',get_template_directory_uri().'/css/rtl-bootstrap.min.css');
			wp_enqueue_style('himer-rtl-css',get_template_directory_uri().'/css/rtl.css',array(),himer_theme_version);
			wp_enqueue_style('himer-rtl',get_template_directory_uri().'/rtl.css',array(),himer_theme_version);
		}else {
			wp_enqueue_style('himer-bootstrap',get_template_directory_uri().'/css/bootstrap.min.css');
			wp_enqueue_style('himer-main-css',get_template_directory_uri().'/css/main.css',array(),himer_theme_version);
		}
		$activate_male_female = apply_filters("wpqa_activate_male_female",false);
		if ($activate_male_female == true) {
			wp_enqueue_style('himer-him-her-css',get_template_directory_uri().'/css/him-her.css',array(),himer_theme_version);
		}
		wp_enqueue_style('himer-dark-css',get_template_directory_uri().'/css/dark.css',array(),himer_theme_version);

		/* Custom CSS */

		$custom_css = apply_filters("himer_custom_inline_css","");
		
		if (himer_options("header_fixed_responsive") == "on") {
			$custom_css .= '@media only screen and (max-width: 479px) {
				.fixed-hidden-header + *,.fixed-hidden-header.header-full + *,.fixed-hidden-header.hidden-header-one + *,.fixed-hidden-header.header-full.hidden-header-one + *,.fixed-hidden-header.hidden-header-both + *,.fixed-hidden-header.header-full.hidden-header-both + * {
					margin-top: 0 !important;
				}
				.fixed-enabled .fixed-hidden-header {
					position: relative !important;
				}
				.fixed-enabled .fixed-hidden-header.header-menu-opened {
					position: fixed !important;
				}
				.admin-bar .fixed-enabled .fixed-hidden-header {
					top: 0 !important;
				}
			}';
		}
		
		$site_width = (int)himer_options("site_width");
		if ($site_width >= 1180) {
			$custom_css .= '@media (min-width: '.($site_width+30).'px) {
				.himer-custom-width .container,
				.himer-custom-width .container-boot,
				.himer-custom-width .main_center .the-main-inner,
				.himer-custom-width .main_center .hide-main-inner,
				.himer-custom-width .main_center main.all-main-wrap,
				.himer-custom-width .main_right main.all-main-wrap,
				.himer-custom-width .main_full main.all-main-wrap,
				.himer-custom-width .main_full .the-main-inner,
				.himer-custom-width .main_full .hide-main-inner,
				.himer-custom-width .main_left main.all-main-wrap {
					max-width: '.$site_width.'px;
				}
			}';
		}

		$custom_heros = himer_post_meta("custom_heros");
		if ((is_single() || is_page()) && $custom_heros == "on") {
			$hero_h_logged = apply_filters("himer_hero_logged",himer_post_meta("hero_h_logged"));
		}else {
			$hero_h_logged = apply_filters("himer_hero_logged",himer_options("hero_h_logged"));
		}
		if ((is_user_logged_in() && ($hero_h_logged == "logged" || $hero_h_logged == "both")) || (!is_user_logged_in() && ($hero_h_logged == "unlogged" || $hero_h_logged == "both"))) {
			$custom_heros = himer_post_meta("custom_heros");
			if ((is_single() || is_page()) && $custom_heros == "on") {
				$hero_h = apply_filters("himer_hero",himer_post_meta("hero_h"));
			}else {
				$hero_h = apply_filters("himer_hero",himer_options("hero_h"));
				$hero_h_home_pages = apply_filters("himer_hero_h_home_pages",himer_options("hero_h_home_pages"));
				$hero_h_pages = apply_filters("himer_hero_home_pages",himer_options("hero_h_pages"));
				$hero_h_pages = explode(",",$hero_h_pages);
			}
			if ($hero_h == "on" && ($custom_heros == "on" || (((is_front_page() || is_home()) && $hero_h_home_pages == "home_page") || $hero_h_home_pages == "all_pages" || ($hero_h_home_pages == "all_posts" && is_singular("post")) || ($hero_h_home_pages == "all_questions" && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))) || ($hero_h_home_pages == "custom_pages" && is_page() && isset($hero_h_pages) && is_array($hero_h_pages) && isset($post->ID) && in_array($post->ID,$hero_h_pages))))) {
				if ((is_single() || is_page()) && $custom_heros == "on") {
					$custom_hero = himer_post_meta("custom_hero");
				}else {
					$custom_hero = himer_options("custom_hero");
				}
				if ($custom_hero == "slider") {
					if ((is_single() || is_page()) && $custom_heros == "on") {
						$hero_height = apply_filters("himer_hero_height",himer_post_meta("hero_height"));
					}else {
						$hero_height = apply_filters("himer_hero_height",himer_options("hero_height"));
					}
					$custom_css .= '.hero-wrap,.hero-inner {
						min-height: '.$hero_height.'px;
					}';
					if ((is_single() || is_page()) && $custom_heros == "on") {
						$add_hero_slides = apply_filters("himer_heros",himer_post_meta("add_hero_slides"));
					}else {
						$add_hero_slides = apply_filters("himer_heros",himer_options("add_hero_slides"));
					}
					if (is_array($add_hero_slides) && !empty($add_hero_slides)) {
						foreach ($add_hero_slides as $key => $value) {
							$color   = (isset($value["color"])?$value["color"]:"");
							$image   = (isset($value["image"])?$value["image"]:"");
							$opacity = (isset($value["opacity"])?$value["opacity"]:"");
							if (!empty($image) && isset($image["id"])) {
								$custom_css .= '.hero-item-'.$key.' .cta-banner,.dark-skin .hero-item-'.$key.' .cta-banner {
									'.($color != ''?'background-color: '.esc_attr($color).';':'').
									(himer_image_url_id($image) != ''?'background-image: url('.himer_image_url_id($image).');':'').
								'}';
							}
							if ($color != '' && $opacity != '' && $opacity > 0) {
								$custom_css .= '.hero-item-'.$key.' .cover-opacity {
									'.($color != ''?'background-color: '.esc_attr($color).';':'');
									if ($opacity != '') {
										$custom_css .= '-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=esc_attr($opacity))";
										filter: alpha(opacity=esc_attr($opacity));
										-moz-opacity: '.esc_attr($opacity/100).';
										-khtml-opacity: '.esc_attr($opacity/100).';
										opacity: '.esc_attr($opacity/100).';';
									}
								$custom_css .= '}';
							}
						}
					}
				}
			}
		}

		$active_referral = himer_options("active_referral");
		if (has_wpqa() && $active_referral == "on") {
			$referrals_background = himer_image_url_id(himer_options("referrals_background"));
			if ($referrals_background != "") {
				$custom_css .= '.referral-cover-background,.dark-skin .referral-cover-background {background-image: url('.$referrals_background.');}';
			}
		}
		
		/* Fonts */
		
		if (isset($main_font["face"]) && $main_font["face"] != "default" && $main_font["face"] != "Default font" && $main_font["face"] != "") {
			$main_font["face"] = str_replace("+"," ",$main_font["face"]);
			$custom_css .= '
			body,h1,h2,h3,h4,h5,h6 {
				font-family: "'.$main_font["face"].'";
			}';
		}
		
		wp_enqueue_style('himer-custom-css',get_template_directory_uri().'/css/custom.css',array(),himer_theme_version);
		wp_add_inline_style('himer-custom-css',$custom_css);
		
		wp_enqueue_script("html5",get_template_directory_uri()."/js/html5.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("modernizr",get_template_directory_uri()."/js/modernizr.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-flex-menu",get_template_directory_uri()."/js/flexMenu.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-scrollbar",get_template_directory_uri()."/js/scrollbar.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-theia",get_template_directory_uri()."/js/theia.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-owl",get_template_directory_uri()."/js/owl.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-match-height",get_template_directory_uri()."/js/matchHeight.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-pretty-photo",get_template_directory_uri()."/js/prettyPhoto.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-tabs",get_template_directory_uri()."/js/tabs.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-tipsy",get_template_directory_uri()."/js/tipsy.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-isotope",get_template_directory_uri()."/js/isotope.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-popper",get_template_directory_uri()."/js/popper.min.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("himer-bootstrap",get_template_directory_uri()."/js/bootstrap.min.js",array("jquery"),'1.0.0',true);

		$captcha_style = himer_options("captcha_style");
		if ($captcha_style == "google_recaptcha") {
			$recaptcha_language = himer_options("recaptcha_language");
			wp_enqueue_script("himer-recaptcha", "https://www.google.com/recaptcha/api.js".($recaptcha_language != ""?"?hl=".$recaptcha_language:""),array("jquery"),'1.0.0',true);
		}
		wp_enqueue_script("himer-custom-js",get_template_directory_uri()."/js/custom.js",array("jquery","imagesloaded"),himer_theme_version,true);
		
		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
	}
}
add_action('wp_enqueue_scripts','himer_scripts_styles');
/* himer_load_theme */
if (!function_exists('himer_load_theme')) {
	function himer_load_theme() {
		/* Default RSS feed links */
		add_theme_support('automatic-feed-links');
		/* Post Thumbnails */
	    add_theme_support('post-thumbnails');
	    set_post_thumbnail_size(830, 550, true);
	    set_post_thumbnail_size(330, 250, true);
	    set_post_thumbnail_size(1080, 565, true);
	    set_post_thumbnail_size(690, 430, true);
	    set_post_thumbnail_size(360, 202, true);
	    add_image_size('himer_img_1', 830, 550, array( 'center', 'top' ));
	    add_image_size('himer_img_2', 330, 250, array( 'center', 'top' ));
	    add_image_size('himer_img_3', 1080, 565, array( 'center', 'top' ));
	    add_image_size('himer_img_4', 690, 430, array( 'center', 'top' ));
	    add_image_size('himer_img_5', 360, 202, array( 'center', 'top' ));
		/* Valid HTML5 */
		add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));
		/* This theme uses its own gallery styles */
		add_filter('use_default_gallery_style', '__return_false');
		/* add title-tag */
		add_theme_support('title-tag');
		/* Load lang languages */
		load_theme_textdomain('himer',get_template_directory().'/languages');
		/* add post-thumbnails */
		add_theme_support('post-thumbnails');
	}
}
add_action('after_setup_theme','himer_load_theme');
/* wp head */
add_action('wp_head','himer_head');
if (!function_exists('himer_head')) {
	function himer_head() {
		if (!function_exists('wp_site_icon') || !has_site_icon()) {
		    $default_favicon    = get_template_directory_uri()."/images/favicon.png";
		    $favicon            = himer_image_url_id(himer_options("favicon"));
		    $iphone_icon        = himer_image_url_id(himer_options("iphone_icon"));
		    $iphone_icon_retina = himer_image_url_id(himer_options("iphone_icon_retina"));
		    $ipad_icon          = himer_image_url_id(himer_options("ipad_icon"));
		    $ipad_icon_retina   = himer_image_url_id(himer_options("ipad_icon_retina"));
		    
			echo '<link rel="shortcut icon" href="'.esc_url((isset($favicon) && $favicon != ""?$favicon:$default_favicon)).'" type="image/x-icon">' ."\n";
		
		    /* Favicon iPhone */
		    if (isset($iphone_icon) && $iphone_icon != "") {
		        echo '<link rel="apple-touch-icon-precomposed" href="'.esc_url($iphone_icon).'">' ."\n";
		    }
		
		    /* Favicon iPhone 4 Retina display */
		    if (isset($iphone_icon_retina) && $iphone_icon_retina != "") {
		        echo '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="'.esc_url($iphone_icon_retina).'">' ."\n";
		    }
		
		    /* Favicon iPad */
		    if (isset($ipad_icon) && $ipad_icon != "") {
		        echo '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="'.esc_url($ipad_icon).'">' ."\n";
		    }
		
		    /* Favicon iPad Retina display */
		    if (isset($ipad_icon_retina) && $ipad_icon_retina != "") {
		        echo '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="'.esc_url($ipad_icon_retina).'">' ."\n";
		    }
		}

		$primary_color = himer_options("primary_color");
		if ($primary_color != "") {
			$skin = $primary_color;
		}else {
			$skins = array("skin" => "#2e6ffd","violet" => "#9349b1","blue" => "#00aeef","bright_red" => "#fa4b2a","cyan" => "#058b7b","green" => "#81b441","red" => "#e91802");
			$site_skin = himer_options('site_skin');
			if ($site_skin == "skin" || $site_skin == "default" || $site_skin == "") {
				$skin = $skins["skin"];
			}else {
				$skin = $skins[$site_skin];
			}
		}
		if (isset($skin) && $skin != "") {
			echo '<meta name="theme-color" content="'.$skin.'">
			<meta name="msapplication-navbutton-color" content="'.$skin.'">
			<meta name="apple-mobile-web-app-capable" content="yes">
			<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">';
		}
	}
}
/* Content Width */
if (!isset($content_width)) {
	$content_width = 1170;
}
/* himer_meta */
if (!function_exists('himer_meta')) {
	function himer_meta($date = "",$category = "",$comment = "",$asked = "",$icons = "",$views = "",$post_id = 0,$post = object,$meta = 'count_post_all') {
		$post_id = ($post_id > 0?$post_id:get_the_ID());
		$post_type = (isset($post->post_type)?$post->post_type:get_post_type($post_id));
		if ($date == "on") {
			$time_string = '<span class="entry-date published">%1$s</span>';
			$format_date_ago = himer_options("format_date_ago");
			$format_date_ago_types = himer_options("format_date_ago_types");
			if ($format_date_ago == "on" && (($post_type == "post" && isset($format_date_ago_types["posts"]) && $format_date_ago_types["posts"] == "posts") || ((($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) && isset($format_date_ago_types["questions"]) && $format_date_ago_types["questions"] == "questions"))) {
				if (wpqa_questions_type === $post_type || wpqa_asked_questions_type === $post_type) {
					$date_string = esc_html__("Asked:","himer");
				}else {
					$date_string = esc_html__("Posted:","himer");
				}
				$human_time_diff = human_time_diff(get_the_time('U',$post_id),current_time('timestamp'))." ".esc_html__("ago","himer");
				$time_string = sprintf($time_string,$human_time_diff);
			}else {
				if (wpqa_questions_type === $post_type || wpqa_asked_questions_type === $post_type) {
					$date_string = esc_html__("Asked:","himer");
				}else {
					$date_string = esc_html__("On:","himer");
				}
				$time_string = sprintf($time_string,esc_html(get_the_time(himer_date_format,$post_id)));
			}
			$posted_on = (isset($date_string)?$date_string:'').'<span class="date-separator"></span> '.(wpqa_questions_type === $post_type || wpqa_asked_questions_type === $post_type?'<a href="'.get_the_permalink($post_id).'"'.(is_single()?' itemprop="url"':'').'>':'').$time_string.(wpqa_questions_type === $post_type || wpqa_asked_questions_type === $post_type?'</a>':'');
			echo '<span class="post-date">'.$posted_on;
			if (is_single() && (wpqa_questions_type === $post_type || wpqa_asked_questions_type === $post_type)) {
				$get_the_time = get_the_time('c',$post_id);
				$puplished_date = ($get_the_time?$get_the_time:get_the_modified_date('c',$post_id));
				echo '<span class="himer_hide" itemprop="dateCreated" datetime="'.$puplished_date.'">'.$puplished_date.'</span>
				<span class="himer_hide" itemprop="datePublished" datetime="'.$puplished_date.'">'.$puplished_date.'</span>';
			}
			echo '</span>';
		}
		
		if ($category == "on" && 'post' === $post_type) {
			$categories_list = get_the_category_list(', ');
			if ($categories_list) {
				$posted_in = sprintf('<span class="post-cat">'.esc_html__('Posted in %1$s','himer').'</span>',$categories_list);
				$posted_in = $categories_list;
				echo '<span class="byline"> '.$posted_in.'</span>';
			}
		}
		if (wpqa_asked_questions_type === $post_type && $asked == "on") {
			$get_question_user_id = himer_post_meta("user_id","",false);
			if ($get_question_user_id != "" && $get_question_user_id > 0) {
				$display_name = get_the_author_meta('display_name',$get_question_user_id);
				if (has_wpqa() && isset($display_name) && $display_name != "") {
					echo '<span class="asked-to">'.esc_html__("Asked to","himer").': <a href="'.wpqa_profile_url($get_question_user_id).'">'.esc_html($display_name).'</a></span>';
				}
			}
		}
		if (wpqa_questions_type === $post_type && $category == "on") {
			$first_span = '<span class="byline"><span class="post-cat">'.esc_html__('In:','himer').' ';
			$first_span = '<span class="byline"><span class="post-cat">';
			$second_span = '</span></span>';
			$out = '';
			$get_the_term_list = get_the_term_list($post_id,wpqa_question_categories,$first_span,apply_filters('himer_separator_categories',', '),$second_span);
			if (!isset($get_the_term_list->errors) && $get_the_term_list != "") {
				$out .= $get_the_term_list;
			}else {
				$category_meta = himer_post_meta("category_meta","",false);
				$term = get_term_by('slug',esc_html($category_meta),wpqa_question_categories);
				if (isset($term->slug)) {
					$out .= $first_span;
					$get_term_link = get_term_link($term);
					if (is_string($get_term_link)) {
						$out .= '<a href="'.$get_term_link.'">'.$term->name.'</a>';
					}
					$out .= $second_span;
				}else if ($category_meta != "") {
					$out .= $first_span.esc_html($category_meta).$second_span;
				}
			}
			echo apply_filters("himer_show_categroies",$out,$post_id,$first_span,$second_span);
			do_action("himer_after_question_category",$post_id);
		}
		do_action("himer_meta_before_comment",$post_id,$post_type,$category);
		$count_post_all = (int)(has_wpqa()?wpqa_count_comments($post_id):get_comments_number());
		if ($comment == "on" && !post_password_required() && ($post_type == "post" || ((wpqa_questions_type === $post_type || wpqa_asked_questions_type === $post_type) && (isset($post->comment_status) && $post->comment_status == "open") || $count_post_all > 0))) {
			$activate_male_female = apply_filters("wpqa_activate_male_female",false);
			if (wpqa_questions_type === $post_type || wpqa_asked_questions_type === $post_type) {
				if ($activate_male_female == true) {
					$count_comment_only = himer_options("count_comment_only");
					if ($meta == "female_comment_count" || $meta == "female_count_comments") {
						//$meta = ($count_comment_only == "on"?"female_count_comments":"female_comment_count");
						echo "<span".(is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?' itemprop="answerCount"':'')." class='number himer_hide'>".himer_count_number($count_post_all).'</span>';
					}
					$count_comments_meta = (int)(has_wpqa()?wpqa_count_comments($post_id,$meta,"like_meta"):get_comments_number());
					echo "<span class='number'>".himer_count_number($count_comments_meta).'</span>';
				}else {
					echo "<span".(is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?' itemprop="answerCount"':'')." class='number".($icons != "on"?" himer_hide":"")."'>".himer_count_number($count_post_all).'</span>';
				}
				if ($activate_male_female != true && $icons != "on") {
					echo " <span class='question-span'>".sprintf(_n("%s Answer","%s Answers",$count_post_all,"himer"),$count_post_all)."</span>";
				}
			}else {?>
				<span class="post-comment">
					<?php esc_html_e('Comments: ','himer');
					if (isset($post->comment_status) && $post->comment_status == "open") {?>
						<a href="<?php echo get_the_permalink($post_id)?>#comments">
							<?php echo himer_count_number($count_post_all);?>
						</a>
					<?php }else {
						esc_html_e("Closed","himer");
					}?>
				</span>
			<?php }
		}
		$active_post_stats = himer_options("active_post_stats");
		if (has_wpqa() && 'post' === $post_type && $views == "on" && $active_post_stats == "on") {
			global $post;?>
			<span class="post-views">
				<?php echo esc_html__('Views:','himer').' '.himer_count_number(wpqa_get_post_stats($post->ID))?>
			</span>
		<?php }
	}
}
add_filter("himer_separator_categories","himer_separator_categories");
function himer_separator_categories() {
	return '';
}
/* Update the plugin to the last version */
function himer_maintenance_mode() {
	$wpqa_custom_queries = get_option("wpqa_custom_queries");
	$active_groups = himer_options("active_groups");
	$active_message = himer_options("active_message");
	$active_notifications = himer_options("active_notifications");
	$active_activity_log = himer_options("active_activity_log");
	if (!isset($wpqa_custom_queries["asked_questions_convert"]) || !isset($wpqa_custom_queries["asked_question_answers"]) || !isset($wpqa_custom_queries["site_asked_question_answers"]) || !isset($wpqa_custom_queries["best_answer"]) || !isset($wpqa_custom_queries["questions"]) || !isset($wpqa_custom_queries["posts"]) || !isset($wpqa_custom_queries["answers"]) || !isset($wpqa_custom_queries["comments"]) || ($active_groups == "on" && !isset($wpqa_custom_queries["groups"])) || ($active_groups == "on" && !isset($wpqa_custom_queries["group_posts"])) || ($active_groups == "on" && !isset($wpqa_custom_queries["group_comments"])) || ($active_notifications == "on" && !isset($wpqa_custom_queries["notification"])) || ($active_activity_log == "on" && !isset($wpqa_custom_queries["activity"]))) {
		$stop_it = true;
	}
	if (has_wpqa() && (!function_exists('wpqa_custom_queries') || (!is_super_admin() && isset($stop_id)))) {
		wp_die('<h1>Under Maintenance</h1><br />Website under planned maintenance. Please check back later, also please make sure you update the WPQA plugin to the last version.');
	}
}
add_action('get_header','himer_maintenance_mode');?>