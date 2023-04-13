<?php
$before_widget = '<section id="%1$s" class="widget card %2$s">';
$after_widget = '</section>';
$before_title = '<div class="card-header d-flex align-items-center"><h2 class="card-title mb-0 d-flex align-items-center"><i class="icon-folder font-xxl card-title__icon"></i><span>';
$after_title = '</span></h2></div>';

/* himer_widgets_init */
add_action( 'widgets_init', 'himer_widgets_init' );
function himer_widgets_init() {
	global $before_widget,$after_widget,$before_title,$after_title;
	
	$sidebars = himer_options('sidebars');
	if ($sidebars) {
		foreach ($sidebars as $sidebar) {
			register_sidebar( array(
				'name' => esc_html($sidebar["name"]),
				'id' => sanitize_title(esc_html($sidebar["name"])),
				'before_widget' => $before_widget , 'after_widget' => $after_widget , 'before_title' => $before_title , 'after_title' => $after_title ,
			) );
		}
	}
	
	$footer_layout = himer_options("footer_layout");
	
	if ($footer_layout == "footer_1c" || $footer_layout == "footer_2c" || $footer_layout == "footer_3c" || $footer_layout == "footer_4c" || $footer_layout == "footer_5c") {
		register_sidebar( array(
			'name' => esc_html__("First footer widget area","himer"),
			'id' => "footer_1c_sidebar",
			'before_widget' => $before_widget , 'after_widget' => $after_widget , 'before_title' => $before_title , 'after_title' => $after_title ,
		));
	}
	if ($footer_layout == "footer_2c" || $footer_layout == "footer_3c" || $footer_layout == "footer_4c" || $footer_layout == "footer_5c") {
		register_sidebar( array(
			'name' => esc_html__("Second footer widget area","himer"),
			'id' => "footer_2c_sidebar",
			'before_widget' => $before_widget , 'after_widget' => $after_widget , 'before_title' => $before_title , 'after_title' => $after_title ,
		));
	}
	if ($footer_layout == "footer_3c" || $footer_layout == "footer_4c" || $footer_layout == "footer_5c") {
		register_sidebar( array(
			'name' => esc_html__("Third footer widget area","himer"),
			'id' => "footer_3c_sidebar",
			'before_widget' => $before_widget , 'after_widget' => $after_widget , 'before_title' => $before_title , 'after_title' => $after_title ,
		));
	}
	if ($footer_layout == "footer_4c" || $footer_layout == "footer_5c") {
		register_sidebar( array(
			'name' => esc_html__("Fourth footer widget area","himer"),
			'id' => "footer_4c_sidebar",
			'before_widget' => $before_widget , 'after_widget' => $after_widget , 'before_title' => $before_title , 'after_title' => $after_title ,
		));
	}
	if ($footer_layout == "footer_5c") {
		register_sidebar( array(
			'name' => esc_html__("Fifth footer widget area","himer"),
			'id' => "footer_5c_sidebar",
			'before_widget' => $before_widget , 'after_widget' => $after_widget , 'before_title' => $before_title , 'after_title' => $after_title ,
		));
	}
}
if (function_exists('register_sidebar')) {
	register_sidebar(array('name' => esc_html__('Sidebar','himer'),'id' => 'sidebar_default',
		'before_widget' => $before_widget,
		'after_widget'  => $after_widget,	
		'before_title'  => $before_title,
		'after_title'   => $after_title
	));
	
	register_sidebar(array('name' => esc_html__('Sidebar 2','himer'),'id' => 'sidebar_default_2',
		'before_widget' => $before_widget,
		'after_widget'  => $after_widget,	
		'before_title'  => $before_title,
		'after_title'   => $after_title
	));
}?>