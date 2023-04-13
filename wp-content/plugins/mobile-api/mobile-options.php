<?php /* Update the options */
add_action("askme_update_options","mobile_api_update_version");
add_action("discy_update_options","mobile_api_update_version");
add_action("himer_update_options","mobile_api_update_version");
add_action("knowly_update_options","mobile_api_update_version");
add_action("questy_update_options","mobile_api_update_version");
function mobile_api_update_version() {
	$version = (int)get_option("mobile-config-version",1);
	$version++;
	update_option("mobile-config-version",$version);
}
/* Mobile options */
$support_activate = mobile_api_updater()->is_active();
if ($support_activate) {
	add_filter(mobile_api_theme_name."_options_end_of_points","mobile_api_setting_points_options");
	add_filter(mobile_api_theme_name."_options_after_general_setting","mobile_api_setting_options",1,2);
}
function mobile_api_setting_points_options($options) {
	$options[] = array(
		'name'      => esc_html__('Points earned when viewing the rewarded ads.','wpqa'),
		'desc'      => esc_html__('Put the points to earn when viewing the rewarded ads','wpqa'),
		'id'        => 'points_rewarded',
		'condition' => 'mobile_rewarded_adv:not(0)',
		'type'      => 'text',
		'std'       => 10
	);
	return $options;
}
function mobile_api_setting_options($options,$options_pages) {
	$directory_uri = get_template_directory_uri();
	$imagepath_theme =  $directory_uri.'/images/';

	// Pull all the pages into an array
	$not_template_pages = array();
	$args = array('post_type' => 'page','nopaging' => true,"meta_query" => array('relation' => 'OR',array("key" => "_wp_page_template","compare" => "NOT EXISTS"),array("key" => "_wp_page_template","compare" => "=","value" => ''),array("key" => "_wp_page_template","compare" => "=","value" => 'default')));
	$not_template_pages[''] = 'Select a page:';
	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$page_post = $the_query->post;
			$not_template_pages[$page_post->ID] = $page_post->post_title;
		}
	}
	wp_reset_postdata();
	
	// Pull all the roles into an array
	global $wp_roles;
	$new_roles = array();
	foreach ($wp_roles->roles as $key => $value) {
		$new_roles[$key] = $value['name'];
	}

	$array_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"question_vote"   => "question_vote",
		"tags"            => "tags",
		"answer_button"   => "answer_button",
		"answers_count"   => "answers_count",
		"views_count"     => "views_count",
		"followers_count" => "followers_count",
		"favourite"       => "favourite",
	);

	$array_options = array(
		"category"        => esc_html__('Category','mobile-api'),
		"date"            => esc_html__('Date','mobile-api'),
		"author_image"    => esc_html__('Author Image','mobile-api'),
		"author"          => esc_html__('Author','mobile-api'),
		"question_vote"   => esc_html__('Question vote','mobile-api'),
		"poll"            => esc_html__('Poll','mobile-api'),
		"tags"            => esc_html__('Tags','mobile-api'),
		"answer_button"   => esc_html__('Answer button','mobile-api'),
		"answers_count"   => esc_html__('Answers count','mobile-api'),
		"views_count"     => esc_html__('Views count','mobile-api'),
		"followers_count" => esc_html__('Followers count','mobile-api'),
		"favourite"       => esc_html__('Favourite','mobile-api'),
	);

	$array_single_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"question_vote"   => "question_vote",
		"tags"            => "tags",
		"answer_button"   => "answer_button",
		"answers_count"   => "answers_count",
		"views_count"     => "views_count",
		"followers_count" => "followers_count",
		"favourite"       => "favourite",
		"share"           => "share",
	);

	$array_single_options = array(
		"category"        => esc_html__('Category','mobile-api'),
		"date"            => esc_html__('Date','mobile-api'),
		"author_image"    => esc_html__('Author Image','mobile-api'),
		"author"          => esc_html__('Author','mobile-api'),
		"question_vote"   => esc_html__('Question vote','mobile-api'),
		"tags"            => esc_html__('Tags','mobile-api'),
		"answer_button"   => esc_html__('Answer button','mobile-api'),
		"answers_count"   => esc_html__('Answers count','mobile-api'),
		"views_count"     => esc_html__('Views count','mobile-api'),
		"followers_count" => esc_html__('Followers count','mobile-api'),
		"favourite"       => esc_html__('Favourite','mobile-api'),
		"share"           => esc_html__('Share','mobile-api'),
	);

	$array_post_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"tags"            => "tags",
		"comment_button"  => "comment_button",
		"comments_count"  => "comments_count",
		"views_count"     => "views_count",
	);

	$array_post_options = array(
		"category"        => esc_html__('Category','mobile-api'),
		"date"            => esc_html__('Date','mobile-api'),
		"author_image"    => esc_html__('Author Image','mobile-api'),
		"author"          => esc_html__('Author','mobile-api'),
		"tags"            => esc_html__('Tags','mobile-api'),
		"comment_button"  => esc_html__('Comment button','mobile-api'),
		"comments_count"  => esc_html__('Comments count','mobile-api'),
		"views_count"     => esc_html__('Views count','mobile-api'),
	);

	$array_single_post_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"tags"            => "tags",
		"comment_button"  => "comment_button",
		"comments_count"  => "comments_count",
		"views_count"     => "views_count",
		"share"           => "share",
	);

	$array_single_post_options = array(
		"category"        => esc_html__('Category','mobile-api'),
		"date"            => esc_html__('Date','mobile-api'),
		"author_image"    => esc_html__('Author Image','mobile-api'),
		"author"          => esc_html__('Author','mobile-api'),
		"tags"            => esc_html__('Tags','mobile-api'),
		"comment_button"  => esc_html__('Comment button','mobile-api'),
		"comments_count"  => esc_html__('Comments count','mobile-api'),
		"views_count"     => esc_html__('Views count','mobile-api'),
		"share"           => esc_html__('Share','mobile-api'),
	);

	$mobile_applications = array(
		"request_app"         => esc_html__('Request my APP','mobile-api'),
		"general_mobile"      => esc_html__('General settings','mobile-api'),
		"guide_pages"         => esc_html__('Guide pages','mobile-api'),
		"setting_page"        => esc_html__('Setting page','mobile-api'),
		"header_mobile"       => esc_html__('Mobile header','mobile-api'),
		"bottom_bar"          => esc_html__('Bottom bar','mobile-api'),
		"side_navbar"         => esc_html__('Side navbar','mobile-api'),
		"mobile_question"     => esc_html__('Ask questions','mobile-api'),
		"ads_mobile"          => esc_html__('Advertising','mobile-api'),
		"app_notifications"   => esc_html__('Notifications','mobile-api'),
		"captcha_mobile"      => esc_html__('Captcha settings','mobile-api'),
		"home_mobile"         => esc_html__('Home settings','mobile-api'),
		"categories_mobile"   => esc_html__('Categories settings','mobile-api'),
		"search_mobile"       => esc_html__('Search settings','mobile-api'),
		"favourites_mobile"   => esc_html__('Favourites settings','mobile-api'),
		"followed_questions"  => esc_html__('Followed Questions','mobile-api'),
		"questions_mobile"    => esc_html__('Questions page settings','mobile-api'),
		"users_mobile"        => esc_html__('Users settings','mobile-api'),
		"comments_mobile"     => esc_html__('Comments and answers','mobile-api'),
		"blog_mobile"         => esc_html__('Blog settings','mobile-api'),
		"single_mobile"       => esc_html__('Single question settings','mobile-api'),
		"single_post_mobile"  => esc_html__('Single post settings','mobile-api'),
		"styling_mobile"      => esc_html__('Mobile styling','mobile-api'),
		"lang_mobile"         => esc_html__('Language settings','mobile-api'),
		"mobile_icons"        => esc_html__('Icons settings','mobile-api'),
		"mobile_construction" => esc_html__('Under construction','mobile-api')
	);

	$array_comment_std = array(
		"author_image" => "author_image",
		"author"       => "author",
	);

	$array_comment_options = array(
		"author_image" => esc_html__('Author Image','mobile-api'),
		"author"       => esc_html__('Author','mobile-api'),
	);

	$mobile_applications = apply_filters("mobile_api_applications_options",$mobile_applications);

	$options[] = array(
		'name'    => esc_html__('Mobile APP','mobile-api'),
		'id'      => 'mobile_applications',
		'type'    => 'heading',
		'icon'    => 'phone',
		'new'     => true,
		'std'     => 'request_app',
		'options' => $mobile_applications
	);
	
	$options[] = array(
		'name' => esc_html__('Request my APP','mobile-api'),
		'id'   => 'request_app',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('Any changes in this page you will need to required new app files to apply the changes.','mobile-api'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Activate a custom URL for your site different than the main URL','mobile-api'),
		'desc' => esc_html__('Something like with www or without it, or with https or with http','mobile-api'),
		'id'   => 'activate_custom_baseurl',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Type your custom URL for your site different than the main URL','mobile-api'),
		'id'        => 'custom_baseurl',
		'std'       => esc_url(home_url('/')),
		'condition' => 'activate_custom_baseurl:not(0)',
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('App Name','mobile-api'),
		'desc' => esc_html__("Your app's name shown on Play Store and App Store","mobile-api"),
		'id'   => 'app_name',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Upload the application icon and it must be (1024*1024px), PNG and NOT transparent','mobile-api'),
		'id'   => 'application_icon',
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('App bundle id','mobile-api'),
		'desc' => esc_html__("It must be small letters (from 'a' to 'z'), like info.2code.app","mobile-api"),
		'id'   => 'app_bundle_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('App IOS bundle id','mobile-api'),
		'desc' => esc_html__("It must be small letters (from 'a' to 'z'), like info.2code.app","mobile-api"),
		'id'   => 'app_ios_bundle_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Application splash screen background color (hex code, ex: #FFFFFF)','mobile-api'),
		'id'   => 'splash_screen_background',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Upload the application splash screen and it must be (512*512px), PNG and NOT transparent','mobile-api'),
		'id'   => 'application_splash_screen',
		'type' => 'upload',
	);

	$options[] = array(
		'name'  => '<a href="https://2code.info/docs/mobile/apple-ios-app/" target="_blank">'.esc_html__('You can get the Issuer ID, KEY ID, Password of APP-SPECIFIC PASSWORDS and AuthKey file from here and these are required if you need the IOS version.','mobile-api').'</a>',
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Issuer ID *','mobile-api'),
		'id'   => 'app_issuer_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Key ID *','mobile-api'),
		'id'   => 'app_key_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Add the AuthKey file content, this file for the IOS app *','mobile-api'),
		'id'   => 'authkey_content',
		'type' => 'textarea',
	);

	if (has_askme()) {
		$app_link = esc_html__('An iOS application’s store ID number can be found in the iTunes store URL as the string of numbers directly after id. For Example, in https://apps.apple.com/app/ask-me-application/id1542559413 the ID is: 1542559413','mobile-api');
	}else if (has_discy()) {
		$app_link = esc_html__('An iOS application’s store ID number can be found in the iTunes store URL as the string of numbers directly after id. For Example, in https://apps.apple.com/app/discy/id1535374585 the ID is: 1535374585','mobile-api');
	}else if (has_himer()) {
		$app_link = esc_html__('An iOS application’s store ID number can be found in the iTunes store URL as the string of numbers directly after id. For Example, in https://apps.apple.com/app/himer/id1604445650 the ID is: 1604445650','mobile-api');
	}else if (has_knowly()) {
		$app_link = esc_html__('An iOS application’s store ID number can be found in the iTunes store URL as the string of numbers directly after id. For Example, in https://apps.apple.com/app/questy/id1604445650 the ID is: 1604445650','mobile-api');
	}else if (has_questy()) {
		$app_link = esc_html__('An iOS application’s store ID number can be found in the iTunes store URL as the string of numbers directly after id. For Example, in https://apps.apple.com/app/knowly/id1604445650 the ID is: 1604445650','mobile-api');
	}

	if (isset($app_link)) {
		$ios_store_id = mobile_api_options("ios_store_id");
		$options[] = array(
			'name' => esc_html__('Apple ID *','mobile-api'),
			'id'   => 'app_apple_id',
			'std'  => ($ios_store_id != ''?$ios_store_id:''),
			'desc' => $app_link,
			'type' => 'text'
		);
	}

	$options[] = array(
		'name'  => esc_html__('Small notifications icon for android.','mobile-api'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the small notifications icon for android','mobile-api'),
		'id'   => 'android_notification',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'android_notification:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('The color of the small notifications icon for android (hex code, ex: #FFFFFF)','mobile-api'),
		'id'   => 'android_notification_color',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('The icon of the small notifications icon for android and it must be (20*20px), PNG and transparent','mobile-api'),
		'id'   => 'android_notification_icon',
		'type' => 'upload',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('General settings','mobile-api'),
		'id'   => 'general_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the icons to use it in the app from: %s','mobile-api'),'<a href="https://2code.info/mobile/icons/" target="_blank">'.esc_html__('here','mobile-api').'</a>'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the force update','mobile-api'),
		'desc' => esc_html__('The force update to allow the users must update the app to continue using it','mobile-api'),
		'id'   => 'force_update',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'force_update:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Last Android version','mobile-api'),
		'id'   => 'android_version',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Last IOS version','mobile-api'),
		'id'   => 'ios_version',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('The app language','mobile-api'),
		'id'   => 'app_lang',
		'type' => 'text',
		'std'  => 'en',
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the circle button to ask a question or add a post','mobile-api'),
		'id'   => 'addaction_mobile',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$addaction_mobile_action = apply_filters("mobile_api_addaction_button",array("question" => esc_html__("Ask a question","mobile-api"),"post" => esc_html__("Add a post","mobile-api")));

	$options[] = array(
		'name'      => esc_html__('Choose the circle button to ask a question or add a post','mobile-api'),
		'id'        => 'addaction_mobile_action',
		'std'       => 'question',
		'options'   => $addaction_mobile_action,
		'condition' => 'addaction_mobile:not(0)',
		'type'      => 'radio'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to activate the follow questions on the app','mobile-api'),
		'id'   => 'mobile_setting_follow_questions',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to hide the dislike on the app','mobile-api'),
		'id'   => 'mobile_setting_dislike',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to make the app for the logged users only.','mobile-api'),
		'id'   => 'mobile_logged_only',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'  => esc_html__('The next two options to make the changes for your app live, and the other users will see the changes after making refresh or reopen the app.','mobile-api'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the roles you need to show for them the live change for the app","mobile-api"),
		'id'      => 'mobile_live_change_groups',
		'type'    => 'multicheck',
		'options' => $new_roles,
		'std'     => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor'),
	);

	$options[] = array(
		'name' => esc_html__('Add more specific user ids to show the live change','mobile-api'),
		'id'   => 'mobile_live_change_specific_users',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable to show the parent categories with child category','mobile-api'),
		'desc' => esc_html__('Show the parent categories with child category, in following categories page, ask question form, and categories page','mobile-api'),
		'id'   => 'mobile_parent_categories',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Write the number of categories which show in the categories page or add 0 to show all of them','mobile-api'),
		'id'   => 'mobile_categories_page',
		'std'  => 0,
		'type' => 'text'
	);

	if (!has_askme()) {
		$options[] = array(
			'name' => esc_html__('Write the number of categories which show in the following steps in the register and edit profile pages or add 0 to show all of them','mobile-api'),
			'id'   => 'mobile_api_following_categories',
			'std'  => 0,
			'type' => 'text'
		);
	}
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Guide pages','mobile-api'),
		'id'   => 'guide_pages',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Enable or disable the guide pages','mobile-api'),
		'id'   => 'onboardmodels_mobile',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'onboardmodels_mobile:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upload the image for first guide page','mobile-api'),
		'id'   => 'onboardmodels_img_1_mobile',
		'std'  => $imagepath_theme."1.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Add the title for first guide page','mobile-api'),
		'id'   => 'onboardmodels_title_1_mobile',
		'std'  => "Welcome",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the sub title for first guide page','mobile-api'),
		'id'   => 'onboardmodels_subtitle_1_mobile',
		'std'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Upload the image for second guide page','mobile-api'),
		'id'   => 'onboardmodels_img_2_mobile',
		'std'  => $imagepath_theme."2.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Add the title for second guide page','mobile-api'),
		'id'   => 'onboardmodels_title_2_mobile',
		'std'  => "You are here",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the sub title for second guide page','mobile-api'),
		'id'   => 'onboardmodels_subtitle_2_mobile',
		'std'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Upload the image for third guide page','mobile-api'),
		'id'   => 'onboardmodels_img_3_mobile',
		'std'  => $imagepath_theme."3.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Add the title for third guide page','mobile-api'),
		'id'   => 'onboardmodels_title_3_mobile',
		'std'  => "Continue to ".mobile_api_theme_name(),
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the sub title for third guide page','mobile-api'),
		'id'   => 'onboardmodels_subtitle_3_mobile',
		'std'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Setting page','mobile-api'),
		'id'   => 'setting_page',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the text size','mobile-api'),
		'id'   => 'text_size_app',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the rate app','mobile-api'),
		'id'   => 'rate_app',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the edit profile page','mobile-api'),
		'id'   => 'edit_profile_app',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the notifications page','mobile-api'),
		'id'   => 'notifications_page_app',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the users to stop the notifications or not on the app','mobile-api'),
		'id'   => 'activate_stop_notification',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	if (!has_askme()) {
		$options[] = array(
			'name'      => esc_html__('Choose the place of the delete user button','mobile-api'),
			'id'        => 'delete_account_location',
			'std'       => 'edit_profile',
			'condition' => 'delete_account:not(0)',
			'type'      => 'radio',
			'options'   => array("edit_profile" => esc_html__("Edit profile","mobile-api"),"settings" => esc_html__("Settings page","mobile-api"))
		);
	}

	$options = apply_filters("mobile_api_options_in_settings_page",$options);

	$options[] = array(
		'name' => esc_html__('Enable or disable the about us page','mobile-api'),
		'id'   => 'about_us_app',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the about us page','mobile-api'),
		'id'        => 'about_us_page_app',
		'type'      => 'select',
		'condition' => 'about_us_app:not(0)',
		'options'   => $not_template_pages
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the privacy policy page','mobile-api'),
		'id'   => 'privacy_policy_app',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('You must choose the privacy page.','mobile-api'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the privacy policy page','mobile-api'),
		'id'        => 'privacy_policy_page_app',
		'type'      => 'select',
		'condition' => 'privacy_policy_app:not(0)',
		'options'   => $not_template_pages
	);

	$options[] = array(
		'name'  => esc_html__('You must choose the terms page.','mobile-api'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Choose the terms and conditions page','mobile-api'),
		'id'      => 'terms_page_app',
		'type'    => 'select',
		'options' => $not_template_pages
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the FAQs page','mobile-api'),
		'id'   => 'faqs_app',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the FAQs page','mobile-api'),
		'id'        => 'faqs_page_app',
		'type'      => 'select',
		'condition' => 'faqs_app:not(0)',
		'options'   => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the contact us page','mobile-api'),
		'id'   => 'contact_us_app',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the share app','mobile-api'),
		'id'   => 'share_app',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Share title','mobile-api'),
		'id'   => 'share_title',
		'std'  => mobile_api_theme_name(),
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Share image','mobile-api'),
		'id'   => 'share_image',
		'std'  => $directory_uri."/screenshot.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Share android URL','mobile-api'),
		'id'   => 'share_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Share IOS URL','mobile-api'),
		'id'   => 'share_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Mobile header','mobile-api'),
		'id'   => 'header_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Logo position','mobile-api'),
		'id'      => 'mobile_logo_position',
		'std'     => 'start',
		'type'    => 'radio',
		'options' => array("start" => esc_html__("Left","mobile-api"),"center" => esc_html__("Center","mobile-api"))
	);

	$options[] = array(
		'name' => esc_html__('Upload the logo','mobile-api'),
		'id'   => 'mobile_logo',
		'std'  => $imagepath_theme."logo-light-2x.png",
		'type' => 'upload',
	);
	
	$options[] = array(
		'name' => esc_html__('Upload the dark logo','mobile-api'),
		'id'   => 'mobile_logo_dark',
		'std'  => $imagepath_theme."logo-colored.png",
		'type' => 'upload',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options = apply_filters("mobile_api_after_header_settings",$options);

	$options[] = array(
		'name' => esc_html__('Bottom bar','mobile-api'),
		'id'   => 'bottom_bar',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name' => esc_html__('Enable or disable the bottom bar','mobile-api'),
		'id'   => 'bottom_bar_activate',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'bottom_bar_activate:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('You must choose 4 items only to show in the bottom bar.','mobile-api'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$main_pages = array(
		"home"            => esc_html__('Home','mobile-api'),
		"ask"             => esc_html__('Ask Question','mobile-api'),
		"categories"      => esc_html__('Question Categories','mobile-api'),
		"favorite"        => esc_html__('Favorite','mobile-api'),
		"followed"        => esc_html__('Followed Questions','mobile-api'),
		"settings"        => esc_html__('Settings','mobile-api'),
		"questions"       => esc_html__('Questions','mobile-api'),
		"blog"            => esc_html__('Blog','mobile-api'),
		"users"           => esc_html__('Users','mobile-api'),
		"post_categories" => esc_html__('Post Categories','mobile-api'),
		"search"          => esc_html__('Search','mobile-api'),
		"contact_us"      => esc_html__('Contact Us','mobile-api'),
		"post"            => esc_html__('Add Post','mobile-api'),
		"points"          => esc_html__('Badges and points','mobile-api'),
		"answers"         => esc_html__('Answers','mobile-api'),
		"comments"        => esc_html__('Comments','mobile-api'),
		"notifications"   => esc_html__('Notifications','mobile-api'),
	);
	$main_pages = apply_filters("mobile_api_options_main_pages",$main_pages);

	$bottom_bar_elements = array(
		array(
			"type"    => "radio",
			"id"      => "type",
			"name"    => esc_html__('Type','mobile-api'),
			'options' => array(
				'main'       => esc_html__('Main page','mobile-api'),
				'q_category' => esc_html__('Question category','mobile-api'),
				'p_category' => esc_html__('Post category','mobile-api'),
				'page'       => esc_html__('Page','mobile-api'),
				'webview'    => esc_html__('Webview page','mobile-api'),
			),
			'std'     => 'main',
		),
		array(
			"type"      => "select",
			"id"        => "main",
			"name"      => esc_html__('Main pages','mobile-api'),
			'options'   => $main_pages,
			"condition" => "[%id%]type:is(main)",
			'std'       => 'home',
		),
		array(
			"type"      => "select",
			"id"        => "feed",
			"name"      => esc_html__('Feed page','mobile-api'),
			'options'   => $options_pages,
			"condition" => "[%id%]type:is(main),[%id%]main:is(feed)",
			'std'       => 'home',
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','mobile-api'),
			"id"          => "q_category",
			"taxonomy"    => mobile_api_question_categories,
			"name"        => esc_html__('Question category','mobile-api'),
			"condition"   => "[%id%]type:is(q_category)",
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','mobile-api'),
			"id"          => "p_category",
			"taxonomy"    => "category",
			"name"        => esc_html__('Post category','mobile-api'),
			"condition"   => "[%id%]type:is(p_category)",
		),
		array(
			"type"      => "select",
			"id"        => "page",
			"options"   => $not_template_pages,
			"name"      => esc_html__('Page','mobile-api'),
			"condition" => "[%id%]type:is(page)",
		),
		array(
			"type"      => "select",
			"id"        => "webview",
			"options"   => $options_pages,
			"name"      => esc_html__('Webview Page','mobile-api'),
			"condition" => "[%id%]type:is(webview)",
		),
		array(
			"type"      => "text",
			"id"        => "link",
			"name"      => esc_html__('Or add your custom link','mobile-api'),
			"condition" => "[%id%]type:is(webview)"
		),
		array(
			"type" => "text",
			"id"   => "title",
			"name" => esc_html__('New title','mobile-api')
		),
		array(
			"type" => "text",
			"id"   => "icon",
			"name" => esc_html__('Icon','mobile-api')
		),
	);

	$old_bottom_bar = array();
	$mobile_bottom_bar = mobile_api_options("mobile_bottom_bar");
	if (!is_array($mobile_bottom_bar) || (is_array($mobile_bottom_bar) && empty($mobile_bottom_bar))) {
		$mobile_bottom_bar = array(
			"home" => "home",
			"categories" => "categories",
			"favorite" => "favorite",
			"settings" => "settings",
		);
	}
	if (is_array($mobile_bottom_bar) && !empty($mobile_bottom_bar)) {
		foreach ($mobile_bottom_bar as $key => $value) {
			if ($value != "" && $value == $key) {
				if ($key == "ask") {
					$icon = "0xe826";
				}else if ($key == "home") {
					$icon = "0xe800";
				}else if ($key == "categories") {
					$icon = "0xe801";
				}else if ($key == "favorite") {
					$icon = "0xe803";
				}else if ($key == "settings") {
					$icon = "0xe804";
				}else if ($key == "blog") {
					$icon = "0xedcb";
				}else if ($key == "post") {
					$icon = "0xeb90";
				}else if ($key == "points") {
					$icon = "0xe827";
				}
				$old_bottom_bar[] = array(
					"type" => "main",
					"main" => $key,
					"icon" => $icon
				);
			}
		}
	}
	
	$options[] = array(
		'id'      => "add_bottom_bars",
		'type'    => "elements",
		'button'  => esc_html__('Add a new link','mobile-api'),
		'hide'    => "yes",
		'std'     => $old_bottom_bar,
		'options' => $bottom_bar_elements,
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar','mobile-api'),
		'id'   => 'side_navbar',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the side navbar','mobile-api'),
		'id'   => 'side_navbar_activate',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'side_navbar_activate:not(0)',
		'type'      => 'heading-2'
	);

	$sidenav_elements = array(
		array(
			"type"    => "radio",
			"id"      => "type",
			"name"    => esc_html__('Type','mobile-api'),
			'options' => array(
				'main'       => esc_html__('Main page','mobile-api'),
				'q_category' => esc_html__('Question category','mobile-api'),
				'p_category' => esc_html__('Post category','mobile-api'),
				'page'       => esc_html__('Page','mobile-api'),
				'webview'    => esc_html__('Webview page','mobile-api'),
			),
			'std'     => 'main',
		),
		array(
			"type"      => "select",
			"id"        => "main",
			"name"      => esc_html__('Main pages','mobile-api'),
			'options'   => $main_pages,
			"condition" => "[%id%]type:is(main)",
			'std'       => 'home',
		),
		array(
			"type"      => "select",
			"id"        => "feed",
			"name"      => esc_html__('Feed page','mobile-api'),
			'options'   => $options_pages,
			"condition" => "[%id%]type:is(main),[%id%]main:is(feed)",
			'std'       => 'home',
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','mobile-api'),
			"id"          => "q_category",
			"taxonomy"    => mobile_api_question_categories,
			"name"        => esc_html__('Question category','mobile-api'),
			"condition"   => "[%id%]type:is(q_category)",
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','mobile-api'),
			"id"          => "p_category",
			"taxonomy"    => "category",
			"name"        => esc_html__('Post category','mobile-api'),
			"condition"   => "[%id%]type:is(p_category)",
		),
		array(
			"type"      => "select",
			"id"        => "page",
			"options"   => $not_template_pages,
			"name"      => esc_html__('Page','mobile-api'),
			"condition" => "[%id%]type:is(page)",
		),
		array(
			"type"      => "select",
			"id"        => "webview",
			"options"   => $options_pages,
			"name"      => esc_html__('Webview Page','mobile-api'),
			"condition" => "[%id%]type:is(webview)",
		),
		array(
			"type"      => "text",
			"id"        => "link",
			"name"      => esc_html__('Or add your custom link','mobile-api'),
			"condition" => "[%id%]type:is(webview)"
		),
		array(
			"type" => "text",
			"id"   => "title",
			"name" => esc_html__('New title','mobile-api')
		),
		array(
			"type" => "text",
			"id"   => "icon",
			"name" => esc_html__('Icon','mobile-api')
		),
	);

	$old_side_nav = array();
	$mobile_side_navbar = mobile_api_options("mobile_side_navbar");
	if (!is_array($mobile_side_navbar) || (is_array($mobile_side_navbar) && empty($mobile_side_navbar))) {
		$mobile_side_navbar = array(
			"home"       => array("sort" => esc_html__('Home','mobile-api'),"value" => "home"),
			"ask"        => array("sort" => esc_html__('Ask Question','mobile-api'),"value" => "ask"),
			"categories" => array("sort" => esc_html__('Categories','mobile-api'),"value" => "categories"),
			"favorite"   => array("sort" => esc_html__('Favorite','mobile-api'),"value" => "favorite"),
			"settings"   => array("sort" => esc_html__('Settings','mobile-api'),"value" => "settings"),
			"blog"       => array("sort" => esc_html__('Blog','mobile-api'),"value" => "blog"),
			"post"       => array("sort" => esc_html__('Add Post','mobile-api'),"value" => "post"),
			"points"     => array("sort" => esc_html__('Badges and points','mobile-api'),"value" => "points"),
		);
	}
	if (is_array($mobile_side_navbar) && !empty($mobile_side_navbar)) {
		foreach ($mobile_side_navbar as $key => $value) {
			if (isset($value["value"]) && $value["value"] != "" && $value["value"] == $key) {
				if ($key == "ask") {
					$icon = "0xe826";
				}else if ($key == "home") {
					$icon = "0xe800";
				}else if ($key == "categories") {
					$icon = "0xe801";
				}else if ($key == "favorite") {
					$icon = "0xe803";
				}else if ($key == "settings") {
					$icon = "0xe804";
				}else if ($key == "blog") {
					$icon = "0xedcb";
				}else if ($key == "post") {
					$icon = "0xeb90";
				}else if ($key == "points") {
					$icon = "0xe827";
				}
				$old_side_nav[] = array(
					"type" => "main",
					"main" => $key,
					"icon" => $icon
				);
			}
		}
	}
	
	$options[] = array(
		'id'      => "add_sidenavs",
		'type'    => "elements",
		'button'  => esc_html__('Add a new link','mobile-api'),
		'hide'    => "yes",
		'std'     => $old_side_nav,
		'options' => $sidenav_elements,
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Ask questions','mobile-api'),
		'id'   => 'mobile_question',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Write the number of categories which show in the ask question form or add 0 to show all of them','mobile-api'),
		'id'   => 'mobile_question_categories',
		'std'  => 0,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Advertising','mobile-api'),
		'id'   => 'ads_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Activate the advertising','mobile-api'),
		'id'   => 'mobile_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Activate the funding choices for the GDPR','mobile-api'),
		'id'   => 'funding_choices',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id','mobile-api'),
		'id'   => 'ad_mob_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id','mobile-api'),
		'id'   => 'ad_mob_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'group',
		'end'  => 'end'
	);

	$options[] = array(
		'type'      => 'group',
		'id'        => 'ads_mobile',
		'condition' => 'mobile_adv:not(0)',
		'name'      => esc_html__('Interstitial adv','mobile-api')
	);

	$options[] = array(
		'name' => esc_html__('Activate the mobile interstitial adv','mobile-api'),
		'id'   => 'mobile_interstitial_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_interstitial_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id for the interstitial','mobile-api'),
		'id'   => 'ad_interstitial_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id for the interstitial','mobile-api'),
		'id'   => 'ad_interstitial_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		"name" => esc_html__('Choose how many time will open the ad, you can leave it 0 to open the ad each time opened the questions and posts','mobile-api'),
		"id"   => "ad_interstitial_count",
		"type" => "sliderui",
		'std'  => 0,
		"step" => "1",
		"min"  => "0",
		"max"  => "10"
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'group',
		'end'  => 'end'
	);

	$options[] = array(
		'type'      => 'group',
		'id'        => 'ads_mobile',
		'condition' => 'mobile_adv:not(0)',
		'name'      => esc_html__('Rewarded adv','mobile-api')
	);

	$options[] = array(
		'name' => esc_html__('Activate the mobile rewarded adv','mobile-api'),
		'id'   => 'mobile_rewarded_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_rewarded_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id for the rewarded','mobile-api'),
		'id'   => 'ad_rewarded_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id for the rewarded','mobile-api'),
		'id'   => 'ad_rewarded_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		"name" => esc_html__('Choose how many time will open the ad, you can leave it 0 to open the ad each time opened the questions and posts','mobile-api'),
		"id"   => "ad_rewarded_count",
		"type" => "sliderui",
		'std'  => 0,
		"step" => "1",
		"min"  => "0",
		"max"  => "10"
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'group',
		'end'  => 'end'
	);

	$options[] = array(
		'type'      => 'group',
		'id'        => 'ads_mobile',
		'condition' => 'mobile_adv:not(0)',
		'name'      => esc_html__('Banner adv','mobile-api')
	);

	$options[] = array(
		'name' => esc_html__('Activate the mobile banner adv','mobile-api'),
		'id'   => 'mobile_banner_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_banner_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id for the banner','mobile-api'),
		'id'   => 'ad_banner_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id for the banner','mobile-api'),
		'id'   => 'ad_banner_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$mobile_api_options = get_option(mobile_api_options_name());
	$banner_top = (isset($mobile_api_options["banner_top"])?$mobile_api_options["banner_top"]:"");
	$banner_bottom = (isset($mobile_api_options["banner_bottom"])?$mobile_api_options["banner_bottom"]:"");
	$banner_after_post = (isset($mobile_api_options["banner_after_post"])?$mobile_api_options["banner_after_post"]:"");
	$banner_webview = (isset($mobile_api_options["banner_webview"])?$mobile_api_options["banner_webview"]:"");

	$options[] = array(
		'name'      => esc_html__('Select where do you need to activate the ads','mobile-api'),
		'id'        => 'mobile_ads',
		'condition' => 'mobile_banner_adv:not(0)',
		'type'      => 'multicheck',
		'std'       => array(
			"top"            => ($banner_top == mobile_api_checkbox_value?"top":""),
			"bottom"         => ($banner_bottom == mobile_api_checkbox_value?"bottom":""),
			"post_top"       => "post_top",
			"post_bottom"    => "post_bottom",
			"after_post"     => ($banner_after_post == mobile_api_checkbox_value?"after_post":""),
			"banner_webview" => ($banner_webview == mobile_api_checkbox_value?"banner_webview":""),
		),
		'options' => array(
			"top"             => esc_html__('Banner ad in the top','mobile-api'),
			"bottom"          => esc_html__('Banner ad in the bottom','mobile-api'),
			"before_home"     => esc_html__('Banner ad before home','mobile-api'),
			"after_home"      => esc_html__('Banner ad after home','mobile-api'),
			"post_top"        => esc_html__('Banner ad on the post or question in the top','mobile-api'),
			"post_bottom"     => esc_html__('Banner ad on the post or question in the bottom','mobile-api'),
			"before_comments" => esc_html__('Banner ad before the comments or answers on the posts or questions','mobile-api'),
			"after_post"      => esc_html__('Banner ad after the post or question','mobile-api'),
			"banner_posts"    => esc_html__('Banner ad after each x number of posts and questions','mobile-api'),
			"banner_comments" => esc_html__('Banner ad after each x number of comments and answers','mobile-api'),
			"banner_webview"  => esc_html__('Banner ad on the webview page','mobile-api'),
		)
	);

	$array_ads = array(
		"top" => array("title" => esc_html__('Banner ad in the top','mobile-api'),"key" => "top","value" => esc_html__('Activate custom HTML or custom image for the top ad','mobile-api')),
		"bottom" => array("title" => esc_html__('Banner ad in the bottom','mobile-api'),"key" => "bottom","value" => esc_html__('Activate custom HTML or custom image for the bottom ad','mobile-api')),
		"before_home" => array("title" => esc_html__('Banner ad before home','mobile-api'),"key" => "before_home","value" => esc_html__('Activate custom HTML or custom image for the before home ad','mobile-api')),
		"after_home" => array("title" => esc_html__('Banner ad after home','mobile-api'),"key" => "after_home","value" => esc_html__('Activate custom HTML or custom image for the after home ad','mobile-api')),
		"post_top" => array("title" => esc_html__('Banner ad on the post or question in the top','mobile-api'),"key" => "post_top","value" => esc_html__('Activate custom HTML or custom image on the post or question in the top','mobile-api')),
		"post_bottom" => array("title" => esc_html__('Banner ad on the post or question in the bottom','mobile-api'),"key" => "post_bottom","value" => esc_html__('Activate custom HTML or custom image on the post or question in the bottom','mobile-api')),
		"before_comments" => array("title" => esc_html__('Banner ad before the comments or answers on the posts or questions','mobile-api'),"key" => "before_comments","value" => esc_html__('Activate custom HTML or custom image on before the comments or answers on the posts or questions','mobile-api')),
		"after_post" => array("title" => esc_html__('Banner ad after the post or question','mobile-api'),"key" => "after_post","value" => esc_html__('Activate custom HTML or custom image on after the post or question','mobile-api')),
		"posts" => array("title" => esc_html__('Banner ad after each x number of posts and questions','mobile-api'),"key" => "banner_posts","value" => esc_html__('Activate custom HTML or custom image for the posts ad','mobile-api'),"position" => esc_html__('Display after x posts and questions','mobile-api')),
		"comments" => array("title" => esc_html__('Banner ad after each x number of comments and answers','mobile-api'),"key" => "banner_comments","value" => esc_html__('Activate custom HTML or custom image for the comments ad','mobile-api'),"position" => esc_html__('Display after x comments and answers','mobile-api')),
		"banner_webview" => array("title" => esc_html__('Banner ad on the webview page','mobile-api'),"key" => "banner_webview","value" => esc_html__('Activate custom HTML or custom image the webview page','mobile-api')),
	);

	if (is_array($array_ads) && !empty($array_ads)) {
		$options[] = array(
			'type' => 'group',
			'end'  => 'end'
		);

		foreach ($array_ads as $key => $value) {
			$options[] = array(
				'type'      => 'group',
				'id'        => 'ads_mobile',
				'condition' => 'mobile_adv:not(0),mobile_banner_adv:not(0),mobile_ads:has('.$value["key"].')',
				'name'      => $value["title"]
			);

			if (isset($value["position"])) {
				$options[] = array(
					'name' => $value["position"],
					'id'   => 'mobile_ad_'.$key.'_position',
					'std'  => '2',
					'type' => 'text'
				);
			}

			$options[] = array(
				'name' => $value["value"],
				'id'   => 'mobile_ad_html_'.$key.'',
				'type' => 'checkbox'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => 'mobile_ad_html_'.$key.':not(0)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name'    => esc_html__('Advertising type','mobile-api'),
				'id'      => 'mobile_ad_html_'.$key.'_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","mobile-api"),"custom_image" => esc_html__("Custom Image","mobile-api"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','mobile-api'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','mobile-api'),
				'id'        => 'mobile_ad_html_'.$key.'_img',
				'condition' => 'mobile_ad_html_'.$key.'_type:is(custom_image)',
				'type'      => 'upload'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising URL','mobile-api'),
				'id'        => 'mobile_ad_html_'.$key.'_href',
				'std'       => '#',
				'condition' => 'mobile_ad_html_'.$key.'_type:is(custom_image)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html','mobile-api'),
				'id'        => 'mobile_ad_html_'.$key.'_code',
				'condition' => 'mobile_ad_html_'.$key.'_type:not(custom_image)',
				'type'      => 'textarea'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'type' => 'group',
				'end'  => 'end'
			);
		}
		$options[] = array(
			'type' => 'html',
			'html'  => '<div><div>'
		);
	}
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Captcha settings','mobile-api'),
		'id'   => 'captcha_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable reCaptcha','mobile-api'),
		'id'   => 'activate_captcha_mobile',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_captcha_mobile:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Select where do you need to activate the captcha','mobile-api'),
		'id'      => 'captcha_positions',
		'type'    => mobile_api_multicheck_type,
		'std'     => array(
			"login"    => "login",
			"register" => "register",
		),
		'options' => array(
			"login"    => esc_html__('Sign in','mobile-api'),
			"register" => esc_html__('Sign up','mobile-api'),
			"answer"   => esc_html__('Add a new answer','mobile-api'),
			"question" => esc_html__('Ask a new question','mobile-api'),
		)
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the reCaptcha v2 site and secret keys from: %s','mobile-api'),'<a href="https://www.google.com/recaptcha/admin/" target="_blank">'.esc_html__('here','mobile-api').'</a> > <a href="https://ahmed.d.pr/DUAKq5" target="_blank">'.esc_html__('like that','mobile-api').'</a>'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('Add this in the domain option: %s','mobile-api'),'recaptcha-flutter-plugin.firebaseapp.com'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Site key reCaptcha','mobile-api'),
		'id'   => 'site_key_recaptcha_mobile',
		'type' => 'text',
	);
	
	$options[] = array(
		'name' => esc_html__('Secret key reCaptcha','mobile-api'),
		'id'   => 'secret_key_recaptcha_mobile',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Notifications','mobile-api'),
		'id'   => 'app_notifications',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the tabs on the notifications page.","mobile-api"),
		'id'      => 'mobile_notifications_tabs',
		'type'    => 'multicheck',
		'std'     => array(
			"unread" => "unread",
			"all"    => "all",
		),
		'options' => array(
			"unread" => esc_html__('Unread','mobile-api'),
			"all"    => esc_html__('All','mobile-api'),
		)
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable push notifications','mobile-api'),
		'id'   => 'push_notifications',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'push_notifications:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => '<a href="https://2code.info/docs/mobile/push-notifications-key/" target="_blank">'.esc_html__('You can get the key from here.','mobile-api').'</a>',
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Add the app key','mobile-api'),
		'id'   => 'app_key',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Home settings','mobile-api'),
		'id'   => 'home_mobile',
		'type' => 'heading-2'
	);

	$pages = get_pages(array('meta_key' => '_wp_page_template','meta_value' => 'template-home.php'));
	
	$options[] = array(
		'name'    => esc_html__('Choose the home page','mobile-api'),
		'id'      => 'home_page_app',
		'type'    => 'select',
		'std'     => (isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?$pages[0]->ID:''),
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the homepage','mobile-api'),
		'id'   => 'count_posts_home',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the home options for questions','mobile-api'),
		'id'      => 'mobile_setting_home',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_std,
		'options' => $array_options
	);

	$options[] = array(
		'name'    => esc_html__('Select the home options for posts','mobile-api'),
		'id'      => 'mobile_setting_home_posts',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_post_std,
		'options' => $array_post_options
	);

	$options[] = array(
		'name'      => esc_html__('Activate the ad in the first tab in the top','mobile-api'),
		'id'        => 'ads_mobile_top',
		'condition' => 'mobile_adv:not(0)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Activate the ad in the first tab in the bottom','mobile-api'),
		'id'        => 'ads_mobile_bottom',
		'condition' => 'mobile_adv:not(0)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Categories settings','mobile-api'),
		'id'   => 'categories_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the categories','mobile-api'),
		'id'   => 'count_posts_categories',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the categories options for questions','mobile-api'),
		'id'      => 'mobile_setting_categories',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_std,
		'options' => $array_options
	);

	$options[] = array(
		'name'    => esc_html__('Select the categories for posts','mobile-api'),
		'id'      => 'mobile_setting_categories_posts',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_post_std,
		'options' => $array_post_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Search settings','mobile-api'),
		'id'   => 'search_mobile',
		'type' => 'heading-2'
	);

	$options = apply_filters("mobile_api_before_search_question_settings",$options);

	$options[] = array(
		'name' => esc_html__('Items per page in the search','mobile-api'),
		'id'   => 'count_posts_search',
		'std'  => "3",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the search options for questions','mobile-api'),
		'id'      => 'mobile_setting_search',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_std,
		'options' => $array_options
	);

	$options = apply_filters("mobile_api_after_search_question_settings",$options);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Favourites settings','mobile-api'),
		'id'   => 'favourites_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the favourite page','mobile-api'),
		'id'   => 'count_posts_favourites',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the favourite page','mobile-api'),
		'id'      => 'mobile_setting_favourites',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_std,
		'options' => $array_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Followed Questions','mobile-api'),
		'id'   => 'followed_questions',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the followed page','mobile-api'),
		'id'   => 'count_posts_followed',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the followed page','mobile-api'),
		'id'      => 'mobile_setting_followed',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_std,
		'options' => $array_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Questions page settings','mobile-api'),
		'id'   => 'questions_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the questions page','mobile-api'),
		'id'   => 'count_posts_questions',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the blog page','mobile-api'),
		'id'      => 'mobile_setting_questions',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_std,
		'options' => $array_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Users settings','mobile-api'),
		'id'   => 'users_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the timeframe to allow the user stay login before logout automatically.","mobile-api"),
		'id'      => 'mobile_time_login',
		'type'    => 'radio',
		'options' => array(
			'hours'  => esc_html__('Hours','mobile-api'),
			'days' => esc_html__('Days','mobile-api'),
		),
		'std'     => "days",
	);
	
	$options[] = array(
		"name"      => esc_html__('Choose the hours to stay login','mobile-api'),
		"id"        => "mobile_time_login_hours",
		"type"      => "sliderui",
		'condition' => 'mobile_time_login:is(hours)',
		'std'       => 1,
		"step"      => "1",
		"min"       => "0",
		"max"       => "100"
	);
	
	$options[] = array(
		"name"      => esc_html__('Choose the days to stay login','mobile-api'),
		"id"        => "mobile_time_login_days",
		"type"      => "sliderui",
		'condition' => 'mobile_time_login:is(days)',
		'std'       => 7,
		"step"      => "1",
		"min"       => "0",
		"max"       => "1000"
	);

	$options[] = array(
		'name' => esc_html__('Do you want to add a custom link of the signup button on the app?','mobile-api'),
		'id'   => 'activate_custom_register_link',
		'type' => 'checkbox',
	);
	
	$options[] = array(
		'name'      => esc_html__('Add the custom link','mobile-api'),
		'desc'      => esc_html__('Type the custom link of the register button from here.','mobile-api'),
		'id'        => 'custom_register_link',
		'condition' => 'activate_custom_register_link:not(0)',
		'type'      => 'text'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the style of the social icons style on the user profile page.","mobile-api"),
		'id'      => 'mobile_social_icon_style',
		'type'    => 'radio',
		'options' => array(
			'icons' => esc_html__('Icons','mobile-api'),
			'links' => esc_html__('Links','mobile-api'),
		),
		'std'     => "icons",
	);

	if (!has_askme()) {
		$options[] = array(
			'name'    => esc_html__("Choose the roles you need to show for the users in the following steps in the register and edit profile pages.","mobile-api"),
			'id'      => 'mobile_api_following_users',
			'type'    => 'multicheck',
			'options' => $new_roles,
			'std'     => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
		);
	}

	$options[] = array(
		'name' => esc_html__('Write the number of users which show in the following steps in the register and edit profile pages.','mobile-api'),
		'id'   => 'mobile_api_following_pages',
		'std'  => 6,
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the roles you need to show for the users in the users page.","mobile-api"),
		'id'      => 'mobile_users_roles_page',
		'type'    => 'multicheck',
		'options' => $new_roles,
		'std'     => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
	);

	$options[] = array(
		'name' => esc_html__('Write the number of users which show in the users page.','mobile-api'),
		'id'   => 'mobile_users_page',
		'std'  => 6,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Comments settings','mobile-api'),
		'id'   => 'comments_mobile',
		'type' => 'heading-2'
	);

	$activate_male_female = apply_filters("wpqa_activate_male_female",false);
	if ($activate_male_female == true) {
		$options[] = array(
			'name' => esc_html__('Enable or disable filter tabs for the gender','mobile-api'),
			'id'   => 'answer_gender_mobile',
			'std'  => mobile_api_checkbox_value,
			'type' => 'checkbox',
		);

		$options[] = array(
			'name'      => esc_html__('Enable or disable the all answers tab','mobile-api'),
			'id'        => 'answer_all_tab_mobile',
			'condition' => 'answer_gender_mobile:not(0)',
			'type'      => 'checkbox',
		);
	}

	$options[] = array(
		'name' => esc_html__('Items per page in the comments or answers page','mobile-api'),
		'id'   => 'count_comments_mobile',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the comments for the blog posts','mobile-api'),
		'id'      => 'mobile_setting_comments',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_comment_std,
		'options' => $array_comment_options
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the vote on answers page','mobile-api'),
		'id'   => 'vote_answer_mobile',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox',
	);

	$answers_sort = array("voted" => esc_html__("Voted","mobile-api"),"oldest" => esc_html__("Oldest","mobile-api"),"recent" => esc_html__("Recent","mobile-api"));
	if (has_himer() || has_knowly() || has_questy()) {
		$answers_sort["reacted"] = esc_html__('Reacted','mobile-api');
	}

	$options[] = array(
		'name'    => esc_html__('Answer sort','mobile-api'),
		'id'      => 'mobile_answers_sort',
		'std'     => 'voted',
		'type'    => 'radio',
		'options' => $answers_sort
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the answers for the questions','mobile-api'),
		'id'      => 'mobile_setting_answers',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_comment_std,
		'options' => $array_comment_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Blog settings','mobile-api'),
		'id'   => 'blog_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the blog page','mobile-api'),
		'id'   => 'count_posts_blog',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the blog page','mobile-api'),
		'id'      => 'mobile_setting_blog',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_post_std,
		'options' => $array_post_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Single question settings','mobile-api'),
		'id'   => 'single_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Tye menu style of report, delete and close for questions or report and delete for answers','mobile-api'),
		'id'      => 'menu_style_of_report',
		'std'     => 'menu',
		'options' => array(
			'menu'  => 'Menu style',
			'icons' => 'With icons',
		),
		'type'    => 'radio'
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the single question page','mobile-api'),
		'id'      => 'mobile_setting_single',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_single_std,
		'options' => $array_single_options
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the related questions?','mobile-api'),
		'id'   => 'app_related_questions',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'app_related_questions:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Related style','mobile-api'),
		'desc'    => esc_html__('Type related question style from here.','mobile-api'),
		'id'      => 'app_related_style_questions',
		'std'     => 'with_images',
		'options' => array(
			'with_images' => 'With images',
			'list_style'  => 'List style',
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Related questions number','mobile-api'),
		'desc' => esc_html__('Type the number of related questions from here.','mobile-api'),
		'id'   => 'app_related_number_questions',
		'std'  => 5,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Query type','mobile-api'),
		'desc'    => esc_html__('Select what will the related questions show.','mobile-api'),
		'id'      => 'app_query_related_questions',
		'std'     => 'categories',
		'options' => array(
			'categories' => esc_html__('Questions in the same categories','mobile-api'),
			'tags'       => esc_html__('Questions in the same tags (If not find any tags will show by the same categories)','mobile-api'),
			'author'     => esc_html__('Questions by the same author','mobile-api'),
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Single post settings','mobile-api'),
		'id'   => 'single_post_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the single post page','mobile-api'),
		'id'      => 'mobile_setting_single_post',
		'type'    => mobile_api_multicheck_type,
		'std'     => $array_single_post_std,
		'options' => $array_single_post_options
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the related posts?','mobile-api'),
		'id'   => 'app_related_posts',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'app_related_posts:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Related style','mobile-api'),
		'desc'    => esc_html__('Type related post style from here.','mobile-api'),
		'id'      => 'app_related_style',
		'std'     => 'with_images',
		'options' => array(
			'with_images' => 'With images',
			'list_style'  => 'List style',
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Related posts number','mobile-api'),
		'desc' => esc_html__('Type the number of related posts from here.','mobile-api'),
		'id'   => 'app_related_number',
		'std'  => 5,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Query type','mobile-api'),
		'desc'    => esc_html__('Select what will the related posts show.','mobile-api'),
		'id'      => 'app_query_related',
		'std'     => 'categories',
		'options' => array(
			'categories' => esc_html__('Posts in the same categories','mobile-api'),
			'tags'       => esc_html__('Posts in the same tags (If not find any tags will show by the same categories)','mobile-api'),
			'author'     => esc_html__('Posts by the same author','mobile-api'),
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Mobile styling','mobile-api'),
		'id'   => 'styling_mobile',
		'type' => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('APP skin by default','mobile-api'),
		'id'      => 'app_skin',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","mobile-api"),"dark" => esc_html__("Dark","mobile-api"))
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the users to choose their skin from the settings page?','mobile-api'),
		'id'   => 'activate_switch_mode',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the users to choose their skin from the header icon?','mobile-api'),
		'id'   => 'activate_dark_from_header',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the border bottom color only for the inputs?','mobile-api'),
		'id'   => 'activate_input_border_bottom',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('Light mode settings.','mobile-api'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Input background color','mobile-api'),
		'id'        => 'inputsbackgroundcolor',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:is(0)',
		'std'       => '#000000'
	);

	$options[] = array(
		'name'      => esc_html__('Input border bottom color','mobile-api'),
		'id'        => 'input_border_bottom_color',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:not(0)',
		'std'       => '#000000'
	);

	$options[] = array(
		'name' => esc_html__('Login, signup, and forgot password background color','mobile-api'),
		'id'   => 'loginbackground',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Header Background color','mobile-api'),
		'id'   => 'appbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Background color','mobile-api'),
		'id'   => 'tabbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Background color','mobile-api'),
		'id'   => 'bottombarbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Header Text color','mobile-api'),
		'id'   => 'appbarcolor',
		'type' => 'color',
		'std'  => '#283952'
	);

	$options[] = array(
		'name' => esc_html__('Tabs underline/border color','mobile-api'),
		'id'   => 'tabbarindicatorcolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Tabs text color','mobile-api'),
		'id'   => 'tabbartextcolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Active color','mobile-api'),
		'id'   => 'tabbaractivetextcolor',
		'type' => 'color',
		'std'  => '#283952'
	);

	$options[] = array(
		'name' => esc_html__('Checkboxes active color','mobile-api'),
		'id'   => 'checkboxactivecolor',
		'type' => 'color',
		'std'  => '#505050'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar text color','mobile-api'),
		'id'   => 'bottombarinactivecolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Active color','mobile-api'),
		'id'   => 'bottombaractivecolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Primary color','mobile-api'),
		'id'   => 'mobile_primary',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Secondary color','mobile-api'),
		'id'   => 'mobile_secondary',
		'type' => 'color',
		'std'  => '#283952'
	);

	$options[] = array(
		'name' => esc_html__('Meta color','mobile-api'),
		'id'   => 'secondaryvariant',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar background','mobile-api'),
		'id'   => 'mobile_background',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar color','mobile-api'),
		'id'   => 'sidemenutextcolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Background','mobile-api'),
		'id'   => 'scaffoldbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Button color','mobile-api'),
		'id'   => 'buttontextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Divider color','mobile-api'),
		'id'   => 'dividercolor',
		'type' => 'color',
		'std'  => '#EEEEEE'
	);

	$options[] = array(
		'name' => esc_html__('Shadow color','mobile-api'),
		'id'   => 'shadowcolor',
		'type' => 'color',
		'std'  => '#000000'
	);

	$options[] = array(
		'name' => esc_html__('Button background color','mobile-api'),
		'id'   => 'buttonsbackgroudcolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Settings page background color','mobile-api'),
		'id'   => 'settingbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Settings page text color','mobile-api'),
		'id'   => 'settingtextcolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Error background color','mobile-api'),
		'id'   => 'errorcolor',
		'type' => 'color',
		'std'  => '#dd3333'
	);

	$options[] = array(
		'name' => esc_html__('Error text color','mobile-api'),
		'id'   => 'errortextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Alert background color','mobile-api'),
		'id'   => 'alertcolor',
		'type' => 'color',
		'std'  => '#FDEDD3'
	);

	$options[] = array(
		'name' => esc_html__('Alert text color','mobile-api'),
		'id'   => 'alerttextcolor',
		'type' => 'color',
		'std'  => '#f5a623'
	);

	$options[] = array(
		'name' => esc_html__('Success background color','mobile-api'),
		'id'   => 'successcolor',
		'type' => 'color',
		'std'  => '#4be1ab'
	);

	$options[] = array(
		'name' => esc_html__('Success text color','mobile-api'),
		'id'   => 'successtextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tooltip Menu color','mobile-api'),
		'id'   => 'tooltipmenucolor',
		'type' => 'color',
		'std'  => '#FFFFFF'
	);

	$options[] = array(
		'name' => esc_html__('Highlight background color','mobile-api'),
		'id'   => 'highlightcolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Highlight text color','mobile-api'),
		'id'   => 'highlighttextcolor',
		'type' => 'color',
		'std'  => '#FFFFFF'
	);

	$options[] = array(
		'name' => esc_html__('Close question button background color','mobile-api'),
		'id'   => 'closequestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#EEEEEE'
	);

	$options[] = array(
		'name' => esc_html__('Close question button color','mobile-api'),
		'id'   => 'closequestionbuttoncolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Open question button background color','mobile-api'),
		'id'   => 'openquestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#EEEEEE'
	);

	$options[] = array(
		'name' => esc_html__('Open question button color','mobile-api'),
		'id'   => 'openquestionbuttoncolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Favourite color','mobile-api'),
		'id'   => 'favouritecolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Un favourite color','mobile-api'),
		'id'   => 'unfavouritecolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Best answer color','mobile-api'),
		'id'   => 'bestanswercolor',
		'type' => 'color',
		'std'  => '#26aa6c'
	);

	$options[] = array(
		'name' => esc_html__('Add best answer color','mobile-api'),
		'id'   => 'addbestanswercolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Remove best answer color','mobile-api'),
		'id'   => 'removebestanswercolor',
		'type' => 'color',
		'std'  => '#AA0000'
	);

	$options[] = array(
		'name' => esc_html__('Verified icon color','mobile-api'),
		'id'   => 'verifiedcolor',
		'type' => 'color',
		'std'  => '#5890ff'
	);

	$options[] = array(
		'name'  => esc_html__('Dark mode settings.','mobile-api'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Input background color','mobile-api'),
		'id'        => 'dark_inputsbackgroundcolor',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:is(0)',
		'std'       => '#2c2c2c'
	);

	$options[] = array(
		'name'      => esc_html__('Input border bottom color','mobile-api'),
		'id'        => 'dark_input_border_bottom_color',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:not(0)',
		'std'       => '#232323'
	);

	$options[] = array(
		'name' => esc_html__('Login, signup, and forgot password background color','mobile-api'),
		'id'   => 'dark_loginbackground',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Header Background color','mobile-api'),
		'id'   => 'dark_appbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#252525'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Background color','mobile-api'),
		'id'   => 'dark_tabbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Background color','mobile-api'),
		'id'   => 'dark_bottombarbackgroundcolor',
		'type' => 'color',
		'std'  => '#252525'
	);

	$options[] = array(
		'name' => esc_html__('Header Text color','mobile-api'),
		'id'   => 'dark_appbarcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tabs underline/border color','mobile-api'),
		'id'   => 'dark_tabbarindicatorcolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Tabs text color','mobile-api'),
		'id'   => 'dark_tabbartextcolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Active color','mobile-api'),
		'id'   => 'dark_tabbaractivetextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Checkboxes active color','mobile-api'),
		'id'   => 'dark_checkboxactivecolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar text color','mobile-api'),
		'id'   => 'dark_bottombarinactivecolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Active color','mobile-api'),
		'id'   => 'dark_bottombaractivecolor',
		'type' => 'color',
		'std'  => '#F0F8FF'
	);

	$options[] = array(
		'name' => esc_html__('General color','mobile-api'),
		'id'   => 'dark_mobile_primary',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Primary color','mobile-api'),
		'id'   => 'dark_mobile_secondary',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Meta color','mobile-api'),
		'id'   => 'dark_secondaryvariant',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar background','mobile-api'),
		'id'   => 'dark_mobile_background',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar color','mobile-api'),
		'id'   => 'dark_sidemenutextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Background','mobile-api'),
		'id'   => 'dark_scaffoldbackgroundcolor',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Button color','mobile-api'),
		'id'   => 'dark_buttontextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Divider color','mobile-api'),
		'id'   => 'dark_dividercolor',
		'type' => 'color',
		'std'  => '#333333'
	);

	$options[] = array(
		'name' => esc_html__('Shadow color','mobile-api'),
		'id'   => 'dark_shadowcolor',
		'type' => 'color',
		'std'  => '#2F4F4F'
	);

	$options[] = array(
		'name' => esc_html__('Button background color','mobile-api'),
		'id'   => 'dark_buttonsbackgroudcolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Settings page background color','mobile-api'),
		'id'   => 'dark_settingbackgroundcolor',
		'type' => 'color',
		'std'  => '#232323'
	);

	$options[] = array(
		'name' => esc_html__('Settings page text color','mobile-api'),
		'id'   => 'dark_settingtextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Error background color','mobile-api'),
		'id'   => 'dark_errorcolor',
		'type' => 'color',
		'std'  => '#dd3333'
	);

	$options[] = array(
		'name' => esc_html__('Error text color','mobile-api'),
		'id'   => 'dark_errortextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Alert background color','mobile-api'),
		'id'   => 'dark_alertcolor',
		'type' => 'color',
		'std'  => '#FDEDD3'
	);

	$options[] = array(
		'name' => esc_html__('Alert text color','mobile-api'),
		'id'   => 'dark_alerttextcolor',
		'type' => 'color',
		'std'  => '#f5a623'
	);

	$options[] = array(
		'name' => esc_html__('Success background color','mobile-api'),
		'id'   => 'dark_successcolor',
		'type' => 'color',
		'std'  => '#4be1ab'
	);

	$options[] = array(
		'name' => esc_html__('Success text color','mobile-api'),
		'id'   => 'dark_successtextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tooltip Menu color','mobile-api'),
		'id'   => 'dark_tooltipmenucolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Highlight background color','mobile-api'),
		'id'   => 'dark_highlightcolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Highlight text color','mobile-api'),
		'id'   => 'dark_highlighttextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Close question button background color','mobile-api'),
		'id'   => 'dark_closequestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#333333'
	);

	$options[] = array(
		'name' => esc_html__('Close question button color','mobile-api'),
		'id'   => 'dark_closequestionbuttoncolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Open question button background color','mobile-api'),
		'id'   => 'dark_openquestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#333333'
	);

	$options[] = array(
		'name' => esc_html__('Open question button color','mobile-api'),
		'id'   => 'dark_openquestionbuttoncolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Favourite color','mobile-api'),
		'id'   => 'dark_favouritecolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Un favourite color','mobile-api'),
		'id'   => 'dark_unfavouritecolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Best answer color','mobile-api'),
		'id'   => 'dark_bestanswercolor',
		'type' => 'color',
		'std'  => '#26aa6c'
	);

	$options[] = array(
		'name' => esc_html__('Add best answer color','mobile-api'),
		'id'   => 'dark_addbestanswercolor',
		'type' => 'color',
		'std'  => mobile_api_theme_color
	);

	$options[] = array(
		'name' => esc_html__('Remove best answer color','mobile-api'),
		'id'   => 'dark_removebestanswercolor',
		'type' => 'color',
		'std'  => '#AA0000'
	);

	$options[] = array(
		'name' => esc_html__('Verified icon color','mobile-api'),
		'id'   => 'dark_verifiedcolor',
		'type' => 'color',
		'std'  => '#5890ff'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Language settings','mobile-api'),
		'id'   => 'lang_mobile',
		'type' => 'heading-2'
	);

	$options = apply_filters("mobile_api_language_options",$options);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Icons settings','mobile-api'),
		'id'   => 'mobile_icons',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the icons to use it in the app from: %s','mobile-api'),'<a href="https://2code.info/mobile/icons/" target="_blank">'.esc_html__('here','mobile-api').'</a>'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Add a new question icon','mobile-api'),
		'id'        => 'mobile_addaction_question',
		'condition' => 'addaction_mobile_action:is(question)',
		'std'       => "0xe965",
		'type'      => 'text'
	);

	$options[] = array(
		'name'      => esc_html__('Add a new post icon','mobile-api'),
		'id'        => 'mobile_addaction_post',
		'condition' => 'addaction_mobile_action:is(post)',
		'std'       => "0xf0ca",
		'type'      => 'text'
	);

	$options[] = array(
		'name'      => esc_html__('Add a new group icon','mobile-api'),
		'id'        => 'mobile_addaction_group',
		'condition' => 'addaction_mobile_action:is(group)',
		'std'       => "0xe963",
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Answers icon','mobile-api'),
		'id'   => 'mobile_answers_icon',
		'std'  => "0xe907",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Best answers icon','mobile-api'),
		'id'   => 'mobile_best_answers_icon',
		'std'  => "0xe906",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Delete icon','mobile-api'),
		'id'   => 'mobile_delete_icon',
		'std'  => "0xf041",
		'type' => 'text'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'menu_style_of_report:is(icons)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Close question icon','mobile-api'),
		'id'   => 'mobile_close_icon',
		'std'  => "0xedf1",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Open question icon','mobile-api'),
		'id'   => 'mobile_open_icon',
		'std'  => "0xedf0",
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Add favourite icon','mobile-api'),
		'id'   => 'mobile_favourite_icon',
		'std'  => "0xe9cb",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Remove favourite icon','mobile-api'),
		'id'   => 'mobile_unfavourite_icon',
		'std'  => "0xe931",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Views icon','mobile-api'),
		'id'   => 'mobile_views_icon',
		'std'  => "fa-eye",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Do you want to change the verified icon?','mobile-api'),
		'id'   => 'activate_verified_icon',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Verified icon','mobile-api'),
		'id'        => 'mobile_verified_icon',
		'condition' => 'activate_verified_icon:not(0)',
		'std'       => "0xef82",
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Do you want to change the vote icons?','mobile-api'),
		'id'   => 'activate_vote_icons',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_vote_icons:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upvote icon','mobile-api'),
		'id'   => 'mobile_upvote_icon',
		'std'  => "0xe825",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Downvote icon','mobile-api'),
		'id'   => 'mobile_downvote_icon',
		'std'  => "0xe824",
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Under construction','mobile-api'),
		'id'   => 'mobile_construction',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the under construction on the mobile apps','mobile-api'),
		'id'   => 'activate_mobile_construction',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_mobile_construction:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Type your title on the construction page','mobile-api'),
		'id'   => 'construction_title',
		'std'  => 'CLOSED!',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Type your content on the construction page','mobile-api'),
		'id'   => 'construction_content',
		'std'  => 'This app is coming soon',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Upload the image on the construction page','mobile-api'),
		'id'   => 'construction_image',
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the icon on the construction page','mobile-api'),
		'id'   => 'activate_construction_icon',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the button on the construction page','mobile-api'),
		'id'   => 'activate_construction_button',
		'std'  => mobile_api_checkbox_value,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_construction_button:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Type your text on the button','mobile-api'),
		'id'   => 'construction_button_text',
		'std'  => 'Contact',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Type your link on the button','mobile-api'),
		'id'   => 'construction_button_url',
		'std'  => 'https://2code.info/',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('The color of the button on the construction page (hex code, ex: #FFFFFF)','mobile-api'),
		'id'   => 'construction_button_color',
		'type' => 'color',
		'std'  => mobile_api_theme_color
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
		'type' => 'heading-2',
		'end'  => 'end'
	);

	return $options;
}?>