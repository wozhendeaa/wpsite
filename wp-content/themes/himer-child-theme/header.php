<?php $site_skin = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_dark_skin():"");
$site_users_only = (has_wpqa()?wpqa_site_users_only():"");
$under_construction = (has_wpqa()?wpqa_under_construction():"");
$wp_page_template = himer_post_meta("_wp_page_template","",false);
$theme_data = wp_get_theme();
$theme_version = !empty($theme_data['Version'])?' '.$theme_data['Version']:'';?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo esc_attr($site_skin)."-skin ".($site_users_only == "yes" || $under_construction == "on" || $wp_page_template == "template-landing.php"?"site-html-login ":"")?>no-svg">
<head>
	<meta charset="<?php bloginfo('charset');?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo('pingback_url');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="generator" content="<?php echo esc_attr($theme_data.$theme_version)?>">
	<?php wp_head();?>
</head>
<body <?php body_class();echo (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?' itemscope itemtype="https://schema.org/QAPage"':'')?>>
	<?php wp_body_open();
	$logo_display  = himer_options("logo_display");
	$logo_img      = himer_image_url_id(himer_options("logo_img"));
	$retina_logo   = himer_image_url_id(himer_options("retina_logo"));
	$logo_height   = himer_options("logo_height");
	$logo_width    = himer_options("logo_width");
	$skin_switcher = himer_options("skin_switcher");
	if ($skin_switcher == "on") {
		$custom_dark_logo = himer_options("custom_dark_logo");
		if ($custom_dark_logo == "on") {
			$dark_logo_img = himer_image_url_id(himer_options("dark_logo_img"));
			$dark_retina_logo = himer_image_url_id(himer_options("dark_retina_logo"));
		}
	}
	if ($site_users_only == "yes" || $under_construction == "on" || $wp_page_template == "template-landing.php") {
		include locate_template("includes/login-page.php");
		get_footer();
		die();
	}else {
		include locate_template("includes/header-code.php");
	}?>