<?php if ($title_post == "on" && !is_attachment()) {
	if ( (is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php"))) ) {
		$custom_page_setting = himer_post_meta("custom_page_setting");
		if ($custom_page_setting == "on") {
			$breadcrumbs = himer_post_meta("breadcrumbs");
		}else {
			$breadcrumbs = himer_options("breadcrumbs");
		}
		$breadcrumbs_style = himer_options("breadcrumbs_style");
		$breadcrumbs_content_title = himer_options("breadcrumbs_content_title");
		if ($breadcrumbs != "on" || ($breadcrumbs == "on" && ($breadcrumbs_style != "style_2" || ($breadcrumbs_content_title != "on" && $breadcrumbs_style == "style_2")))) {
			the_title( '<'.($breadcrumbs == "on" && $breadcrumbs_style == "style_2"?"h2":"h1").' class="'.(isset($title_post_style) && $title_post_style == "style_2"?"post-title-2":"post-title").'">'.(isset($title_post_style) && $title_post_style == "style_2" && isset($title_post_icon) && $title_post_icon != ""?"<i class='".$title_post_icon."'></i>":"").(is_sticky()?"<i class='icon-pencil'></i>":""), '</'.($breadcrumbs == "on" && $breadcrumbs_style == "style_2"?"h2":"h1").'>' );
		}
	}else {
		the_title( '<h2 class="post-title"><a'.$post_link_target.' class="post-title" href="' . esc_url( get_permalink() ) . '" rel="bookmark">'.(is_sticky()?"<i class='icon-pencil'></i>":""), '</a></h2>' );
	}
}?>