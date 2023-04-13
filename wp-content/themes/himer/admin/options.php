<?php /* Admin options */
add_filter("wpqa_header_setting","himer_header_setting",1,7);
function himer_header_setting($options,$options_pages,$new_roles,$imagepath,$imagepath_theme,$new_sidebars,$menus) {
	$options[] = array(
		'name'    => esc_html__('Header height','himer'),
		'desc'    => esc_html__('Choose the header height.','himer'),
		'id'      => 'header_height',
		'std'     => 'small',
		'type'    => 'radio',
		'options' => array("small" => esc_html__("Small","himer"),"large" => esc_html__("Large","himer"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Header skin','himer'),
		'desc'    => esc_html__('Choose the header skin.','himer'),
		'id'      => 'header_skin',
		'std'     => 'dark',
		'type'    => 'radio',
		'options' => array("dark" => esc_html__("Dark","himer"),"light" => esc_html__("Light","himer"),"primary" => esc_html__("Color 1","himer"),"secondary" => esc_html__("Color 2","himer"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Logo display','himer'),
		'desc'    => esc_html__('Choose the logo display.','himer'),
		'id'      => 'logo_display',
		'std'     => 'custom_image',
		'type'    => 'radio',
		'options' => array("display_title" => esc_html__("Display site title","himer"),"custom_image" => esc_html__("Custom Image","himer"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Upload the logo','himer'),
		'desc'      => esc_html__('Upload your custom logo.','himer'),
		'id'        => 'logo_img',
		'std'       => $imagepath_theme."logo-light.png",
		'type'      => 'upload',
		'condition' => 'logo_display:is(custom_image)',
		'options'   => array("height" => "logo_height","width" => "logo_width"),
	);
	
	$options[] = array(
		'name'      => esc_html__('Upload the retina logo','himer'),
		'desc'      => esc_html__('Upload your custom retina logo.','himer'),
		'id'        => 'retina_logo',
		'std'       => $imagepath_theme."logo-2x.png",
		'type'      => 'upload',
		'condition' => 'logo_display:is(custom_image)'
	);
	
	$options[] = array(
		'name'      => esc_html__('Logo height','himer'),
		"id"        => "logo_height",
		"type"      => "sliderui",
		'std'       => '30',
		"step"      => "1",
		"min"       => "0",
		"max"       => "80",
		'condition' => 'logo_display:is(custom_image)'
	);
	
	$options[] = array(
		'name'      => esc_html__('Logo width','himer'),
		"id"        => "logo_width",
		"type"      => "sliderui",
		'std'       => '96',
		"step"      => "1",
		"min"       => "0",
		"max"       => "170",
		'condition' => 'logo_display:is(custom_image)'
	);
	
	$options[] = array(
		'name' => esc_html__('Header search option','himer'),
		'desc' => esc_html__('Select ON to enable header search.','himer'),
		'id'   => 'header_search',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Activate the bigger search bar?','himer'),
		'desc'      => esc_html__('Select ON to enable header bigger search bar.','himer'),
		'id'        => 'big_search',
		'condition' => 'header_search:not(0)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'name' => esc_html__('Activate the header buttons menu?','himer'),
		'desc' => esc_html__('Select ON to enable header buttons menu.','himer'),
		'id'   => 'activate_header_buttons',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_header_buttons:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header buttons menu with default buttons or custom menu','himer'),
		'id'      => 'header_buttons_menu',
		'std'     => 'default',
		'type'    => 'radio',
		'options' => array("default" => esc_html__("Default","himer"),"menu" => esc_html__("Custom menu","himer"))
	);

	$options[] = array(
		'name'      => esc_html__('Choose from here which menu will show at header buttons menu.','himer'),
		'id'        => 'header_buttons_custom_menu',
		'type'      => 'select',
		'condition' => 'header_buttons_menu:is(menu)',
		'options'   => $menus
	);
	
	$header_buttons = array(
		"question" => array("sort" => esc_html__('Ask A Question','himer'),"value" => "question"),
		"post"     => array("sort" => esc_html__('Add A Post','himer'),"value" => "post"),
		"group"    => array("sort" => esc_html__('Create A Group','himer'),"value" => "group"),
	);
	
	$options[] = array(
		'name'         => esc_html__('Select the pages to add at the header buttons menu','himer'),
		'id'           => 'header_buttons',
		'condition'    => 'header_buttons_menu:not(menu)',
		'type'         => 'multicheck',
		'sort'         => 'yes',
		'limit-height' => 'yes',
		'std'          => $header_buttons,
		'options'      => $header_buttons
	);
	
	$options[] = array(
		'name'    => esc_html__('Header buttons menu style','himer'),
		'desc'    => esc_html__('Choose header buttons menu style.','himer'),
		'id'      => 'header_buttons_style',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","himer"),"dark" => esc_html__("Dark","himer"))
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('User icon profile or login and register buttons','himer'),
		'desc' => esc_html__('Select ON to enable header user login area.','himer'),
		'id'   => 'header_user_login',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'header_user_login:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header user login style','himer'),
		'desc'    => esc_html__('Choose header user login style.','himer'),
		'id'      => 'user_login_style',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","himer"),"dark" => esc_html__("Dark","himer"))
	);

	$options[] = array(
		'name' => esc_html__('The settings of header profile menu on the WordPress menus, on menu named Header Profile Menu','himer'),
		'type' => 'info'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'active_message:not(0)'
	);
	
	$options[] = array(
		'name' => esc_html__('Header messages','himer'),
		'desc' => esc_html__('Select ON to enable header messages.','himer'),
		'id'   => 'header_messages',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'active_message:not(0),header_messages:not(0)'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header messages style','himer'),
		'desc'    => esc_html__('Choose header messages style.','himer'),
		'id'      => 'messages_style',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","himer"),"dark" => esc_html__("Dark","himer"))
	);
	
	$options[] = array(
		'name' => esc_html__('Header messages number','himer'),
		'desc' => esc_html__('Put the header messages number.','himer'),
		'id'   => 'messages_number',
		'std'  => 3,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Header notifications','himer'),
		'desc' => esc_html__('Select ON to enable header notifications.','himer'),
		'id'   => 'header_notifications',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'active_notifications:not(0),header_notifications:not(0)'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header notifications style','himer'),
		'desc'    => esc_html__('Choose header notifications style.','himer'),
		'id'      => 'notifications_style',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","himer"),"dark" => esc_html__("Dark","himer"))
	);
	
	$options[] = array(
		'name' => esc_html__('Header notifications number','himer'),
		'desc' => esc_html__('Put the header notifications number.','himer'),
		'id'   => 'notifications_number',
		'std'  => 3,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Activate the second menu','himer'),
		'desc' => esc_html__('Select ON to enable the second menu.','himer'),
		'id'   => 'second_header',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'second_header:not(0)'
	);
	
	$options[] = array(
		'name'    => esc_html__('Second menu works at all the pages, custom pages, or home page only?','himer'),
		'id'      => 'second_menu_pages',
		'options' => array(
			'home_page'     => esc_html__('Home page','himer'),
			'all_pages'     => esc_html__('All site pages','himer'),
			'all_posts'     => esc_html__('All single post pages','himer'),
			'all_questions' => esc_html__('All single question pages','himer'),
			'custom_pages'  => esc_html__('Custom pages','himer'),
		),
		'std'     => 'home_page',
		'type'    => 'radio'
	);

	$options[] = array(
		'name'      => esc_html__('Page ids','himer'),
		'desc'      => esc_html__('Type from here the page ids','himer'),
		'id'        => 'second_menu_custom_pages',
		'type'      => 'text',
		'condition' => 'second_menu_pages:is(custom_pages)'
	);
	
	$options[] = array(
		'name' => esc_html__('Activate the tags on the second menu','himer'),
		'desc' => esc_html__('Select ON to enable the tags on the second menu.','himer'),
		'id'   => 'second_header_tags',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'second_header_tags:not(0)'
	);

	$options[] = array(
		'name'    => esc_html__('Choose the tags from the questions or posts','himer'),
		'id'      => 'second_header_tags_type',
		'options' => array(
			'questions' => esc_html__('Questions','himer'),
			'posts'     => esc_html__('Posts','himer'),
		),
		'std'     => 'questions',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Activate the more tags button','himer'),
		'desc' => esc_html__('Select ON to enable the more tags button.','himer'),
		'id'   => 'second_header_more_tags',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Tags page','himer'),
		'desc'      => esc_html__('Select the tags page','himer'),
		'id'        => 'second_header_tags_page',
		'type'      => 'select',
		'options'   => $options_pages,
		'condition' => 'second_header_more_tags:not(0)'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Fixed header option','himer'),
		'desc' => esc_html__('Select ON to enable fixed header.','himer'),
		'id'   => 'header_fixed',
		'type' => 'checkbox'
	);
	return $options;
}
add_filter("wpqa_after_slider_setting","himer_after_slider_setting",1,7);
function himer_after_slider_setting($options,$options_pages,$new_roles,$imagepath,$imagepath_theme,$new_sidebars,$menus) {
	$options[] = array(
		'name' => esc_html__('Hero settings','himer'),
		'id'   => 'hero',
		'type' => 'heading',
		'icon' => 'welcome-widgets-menus',
	);
	
	$options[] = array(
		'type' => 'heading-2',
	);
	
	$options[] = array(
		'name' => esc_html__('Activate the hero or not','himer'),
		'desc' => esc_html__('Select ON to enable the hero.','himer'),
		'id'   => 'hero_h',
		'type' => 'checkbox',
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'hero_h:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Hero works at all the pages, custom pages, or home page only?','himer'),
		'id'      => 'hero_h_home_pages',
		'options' => array(
			'home_page'     => esc_html__('Home page','himer'),
			'all_pages'     => esc_html__('All site pages','himer'),
			'all_posts'     => esc_html__('All single post pages','himer'),
			'all_questions' => esc_html__('All single question pages','himer'),
			'custom_pages'  => esc_html__('Custom pages','himer'),
		),
		'std'     => 'home_page',
		'type'    => 'radio'
	);

	$options[] = array(
		'name'      => esc_html__('Page ids','himer'),
		'desc'      => esc_html__('Type from here the page ids','himer'),
		'id'        => 'hero_h_pages',
		'type'      => 'text',
		'condition' => 'hero_h_home_pages:is(custom_pages)'
	);

	$options[] = array(
		'name'    => esc_html__('Hero works for "Unlogged users", "Logged users" or both','himer'),
		'id'      => 'hero_h_logged',
		'options' => array(
			'unlogged' => esc_html__('Unlogged users','himer'),
			'logged'   => esc_html__('Logged users','himer'),
			'both'     => esc_html__('Both','himer'),
		),
		'std'     => 'both',
		'type'    => 'radio',
	);

	$options[] = array(
		'name'    => esc_html__('Hero layout','himer'),
		'id'      => "hero_layout",
		'std'     => "right",
		'type'    => "images",
		'options' => array(
			'right'  => $imagepath.'hero-right.png',
			'full'   => $imagepath.'hero-full.png',
			'left'   => $imagepath.'hero-left.png',
		)
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'hero_layout:not(full)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Sidebar of hero settings','himer'),
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Sidebar style','himer'),
		'id'      => 'hero_sidebar',
		'options' => array(
			'stats'        => esc_html__("Stats","himer"),
			'small_banner' => esc_html__("Posts with small banner","himer"),
		),
		'std'     => 'stats',
		'type'    => 'radio'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'hero_sidebar:is(stats)',
		'type'      => 'heading-2'
	);

	$stats_array = array(
		"questions"    => array("sort" => esc_html__("Questions","himer"),"value" => "questions"),
		"answers"      => array("sort" => esc_html__("Answers","himer"),"value" => "answers"),
		"posts"        => array("sort" => esc_html__("Posts","himer"),"value" => "posts"),
		"comments"     => array("sort" => esc_html__("Comments","himer"),"value" => "comments"),
		"best_answers" => array("sort" => esc_html__("Best Answers","himer"),"value" => "best_answers"),
		"users"        => array("sort" => esc_html__("Users","himer"),"value" => "users"),
		"groups"       => array("sort" => esc_html__("Groups","himer"),"value" => "groups"),
		"group_posts"  => array("sort" => esc_html__("Group Posts","himer"),"value" => "group_posts"),
	);
	$stats_array = apply_filters("himer_widget_stats_array",$stats_array);

	$options[] = array(
		'name'    => esc_html__('Choose the stats','himer'),
		'id'      => 'hero_stats',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $stats_array,
		'options' => $stats_array
	);

	$options[] = array(
		'name'    => esc_html__('Style','himer'),
		'id'      => 'hero_stats_style',
		'options' => array(
			'style_1' => 'Style 1',
			'style_2' => 'Style 2',
			'style_3' => 'Style 3',
		),
		'std'     => 'style_1',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('Content of hero settings','himer'),
		'type' => 'info'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the hero that works with the theme or add your custom hero by inserting the code or shortcodes','himer'),
		'id'        => 'custom_hero',
		'options'   => array(
			'posts'  => esc_html__('Posts','himer'),
			'slider' => esc_html__('Theme Slider','himer'),
			'custom' => esc_html__('Custom Slider','himer'),
		),
		'std'     => 'posts',
		'type'    => 'radio',
	);

	$options[] = array(
		'name'      => esc_html__('Questions or posts','himer'),
		'id'        => 'hero_posts',
		'options'   => array(
			'questions' => esc_html__('Questions','himer'),
			'posts'     => esc_html__('Posts','himer')
		),
		'condition' => 'hero_sidebar:is(small_banner),custom_hero:is(posts)',
		'std'       => 'questions',
		'operator'  => 'or',
		'type'      => 'radio'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'custom_hero:is(slider)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Hero height','himer'),
		"id"   => "hero_height",
		"type" => "sliderui",
		"step" => "50",
		"min"  => "400",
		"max"  => "1000",
		"std"  => "500"
	);

	$slide_elements = array(
		array(
			"type" => "color",
			"id"   => "color",
			"name" => esc_html__('Color','himer')
		),
		array(
			"type" => "upload",
			"id"   => "image",
			"name" => esc_html__('Image','himer')
		),
		array(
			"type"  => "slider",
			"name"  => esc_html__('Choose the background opacity','himer'),
			"id"    => "opacity",
			"std"   => "0",
			"step"  => "1",
			"min"   => "0",
			"max"   => "100",
			"value" => "0"
		),
		array(
			"type" => "text",
			"id"   => "title",
			"name" => esc_html__('Title','himer')
		),
		array(
			"type" => "textarea",
			"id"   => "paragraph",
			"name" => esc_html__('Paragraph','himer')
		),
		array(
			"type" => "checkbox",
			"id"   => "button_active",
			"name" => esc_html__('Button enable or disable?','himer'),
			'std'  => 'on',
		),
		array(
			'div'       => 'div',
			'condition' => '[%id%]button_active:is(on)',
			'type'      => 'heading-2'
		),
		array(
			"type"    => "radio",
			"id"      => "button",
			"name"    => esc_html__('Button','himer'),
			'options' => array(
				'signup'   => esc_html__('Create A New Account','himer'),
				'login'    => esc_html__('Login','himer'),
				'question' => esc_html__('Ask A Question','himer'),
				'post'     => esc_html__('Add A Post','himer'),
				'custom'   => esc_html__('Custom link','himer'),
			),
			'std'     => 'signup',
		),
		array(
			"type"    => "radio",
			"id"      => "button_style",
			"name"    => esc_html__('Button style','himer'),
			'options' => array(
				'style_3' => esc_html__('Style 1','himer'),
				'style_2' => esc_html__('Style 2','himer'),
				'style_1' => esc_html__('Style 3','himer'),
			),
			'std'     => 'style_3',
		),
		array(
			'div'       => 'div',
			'condition' => '[%id%]button:is(custom)',
			'type'      => 'heading-2'
		),
		array(
			'name'    => esc_html__('Open the page in same page or a new page?','himer'),
			'id'      => 'button_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","himer"),"new_page" => esc_html__("New page","himer"))
		),
		array(
			'name' => esc_html__('Type the button link','himer'),
			'id'   => 'button_link',
			'type' => 'text'
		),
		array(
			'name' => esc_html__('Type the button text','himer'),
			'id'   => 'button_text',
			'type' => 'text'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
	);
	
	$options[] = array(
		'id'      => "add_hero_slides",
		'type'    => "elements",
		'button'  => esc_html__('Add a new slide','himer'),
		'hide'    => "yes",
		'options' => $slide_elements,
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'id'        => "custom_hero_slides",
		'type'      => "textarea",
		'name'      => esc_html__('Add your custom slide or shortcode','himer'),
		'condition' => 'custom_hero:is(custom)',
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'hero_posts:is(questions),custom_hero:is(posts),hero_layout:not(full)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Make the big questions banner with answers or not','himer'),
		'id'   => "hero_questions_answer",
		'std'  => "on",
		'type' => "checkbox",
	);
	
	$options[] = array(
		'name'      => esc_html__('Style of the answer','himer'),
		'desc'      => esc_html__('Choose the style of the answer.','himer'),
		'id'        => 'hero_questions_answer_style',
		'std'       => 'male_female',
		'type'      => 'radio',
		'condition' => 'hero_questions_answer:is(on)',
		'options'   => array(
			"male_female" => esc_html__("Male and female answers","himer"),
			"male"        => esc_html__("Male answer","himer"),
			"female"      => esc_html__("Female answer","himer"),
			"best_answer" => esc_html__("Best answer","himer"),
			"last_answer" => esc_html__("Last answer","himer")
		)
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'hero_layout:is(full),custom_hero:is(posts)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Style of the posts','himer'),
		'desc'    => esc_html__('Choose the style of the posts.','himer'),
		'id'      => 'hero_posts_full',
		'std'     => 'banner_article',
		'type'    => 'radio',
		'options' => array(
			"banner_article" => esc_html__("Big banner and posts like article style","himer"),
			"big_banner"     => esc_html__("Big banner","himer"),
			"article_style"  => esc_html__("Like article style","himer"),
			"small_banner"   => esc_html__("Posts with small banner","himer")
		)
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'custom_hero:is(posts)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'      => esc_html__('Note: this option will not work if you choose stats in the sidebar of the hero and do not choose the slider of the posts.','himer'),
		'condition' => 'hero_sidebar:is(stats),hero_posts_slider:not(on)',
		'type'      => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Add the number of posts you want to show','himer'),
		'id'   => "hero_posts_number",
		'type' => "text",
		'std'  => "1",
	);

	$options[] = array(
		'name' => esc_html__('Make the posts as slider','himer'),
		'id'   => "hero_posts_slider",
		'type' => "checkbox",
	);

	$options[] = array(
		'name'      => esc_html__('Show the meta of the post','himer'),
		'id'        => "hero_small_posts_meta",
		'type'      => "checkbox",
		'std'       => "on",
		'condition' => 'hero_posts_full:is(small_banner)',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options = apply_filters('himer_options_after_hero_setting',$options);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);
	return $options;
}
add_filter("wpqa_after_options_setting","himer_after_options_setting",1,7);
function himer_after_options_setting($options,$options_pages,$new_roles,$imagepath,$imagepath_theme,$new_sidebars,$menus) {
	$options[] = array(
		'name' => esc_html__('Himer Change log','himer'),
		'icon' => 'hammer',
		'type' => 'heading',
		'link' => 'https://2code.info/docs/himer/change-log/',
	);
	
	$options[] = array(
		'name' => esc_html__('Mobile APP Change log','himer'),
		'icon' => 'lightbulb',
		'type' => 'heading',
		'link' => 'https://2code.info/docs/mobile/change-log/',
	);
	
	$options[] = array(
		'name' => esc_html__('Support','himer'),
		'icon' => 'megaphone',
		'type' => 'heading',
		'link' => 'https://2code.info/support/',
	);
	return $options;
}
add_filter("wpqa_options_advanced_setting","himer_options_advanced_setting");
function himer_options_advanced_setting($options) {
	$ask_me = himer_options("ask_me");
	$options[] = array(
		'name'    => esc_html__('Choose the your old theme if you use one of our themes before','himer'),
		'id'      => 'old_themes',
		'options' => array(
			'nothing' => esc_html__('No thing, just a new site','himer'),
			'ask_me'  => esc_html__('Ask Me','himer'),
			'discy'   => esc_html__('Discy','himer'),
		),
		'std'     => ($ask_me == 'on'?'ask_me':'nothing'),
		'type'    => 'radio'
	);
	if (!has_wpqa() || (has_wpqa() && wpqa_plugin_version < "5.9.2")) {
		$options[] = array(
			'name' => esc_html__('Import Setting','himer'),
			'desc' => esc_html__('Put here the import setting','himer'),
			'id'   => 'import_setting',
			'type' => 'import'
		);
	}
	return $options;
}?>