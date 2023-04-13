<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Get options */
if (!function_exists('wpqa_options')):
	function wpqa_options( $name, $default = false ) {
		$options = get_option(wpqa_prefix_theme."_options");
		if ( isset( $options[$name] ) ) {
			return $options[$name];
		}
		return $default;
	}
endif;
add_filter("the_content","do_shortcode");
add_filter("widget_text","do_shortcode");
/* Is Ajax */
if (!function_exists('wpqa_is_ajax')) :
	function wpqa_is_ajax() {
		if (defined('DOING_AJAX')) return true;
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
		return false;
	}
endif;
/* Skins css */
if (!function_exists('wpqa_skin')) :
	function wpqa_skin($skin) {
		if (!empty($skin) && $skin != "skin" && $skin != "default" && $skin != "") {
			wp_enqueue_style('wpqa-skin-'.$skin,get_template_directory_uri()."/css/skins/".$skin.".css",array(),wpqa_plugin_version);
		}else if ($skin == "skin" || $skin == "default" || $skin == "") {
			wp_enqueue_style('wpqa-skin-default',get_template_directory_uri()."/css/skins/skins.css",array(),wpqa_plugin_version);
		}
	}
endif;
/* CSS post type */
if (!function_exists('wpqa_css_post_type')) :
	function wpqa_css_post_type($quote_link = "",$wpqa_quote_color = "",$quote_icon_color = "",$post_id = 0,$link_icon_color = "",$wpqa_link_icon_hover_color = "",$wpqa_link_hover_color = "") {
		$custom_css = '';
		if ($quote_link == "quote") {
			if ((isset($wpqa_quote_color) && $wpqa_quote_color != "") || (isset($quote_icon_color) && $quote_icon_color != "")) {
				if (isset($wpqa_quote_color) && $wpqa_quote_color != "") {
					$custom_css .= '.post-'.esc_attr($post_id).'.post-quote .post-inner-quote p {
						color: '.esc_attr($wpqa_quote_color).';
					}';
				}
				if (isset($quote_icon_color) && $quote_icon_color != "") {
					$custom_css .= '.post-'.esc_attr($post_id).'.post-quote .post-type i {
						color: '.esc_attr($quote_icon_color).';
					}';
				}
			}
		}else if ($quote_link == "link") {
			if ((isset($link_icon_color) && $link_icon_color != "") || (isset($wpqa_link_icon_hover_color) && $wpqa_link_icon_hover_color != "") || (isset($wpqa_link_hover_color) && $wpqa_link_hover_color != "")) {
				if (isset($link_icon_color) && $link_icon_color != "") {
					$custom_css .= '.post-'.esc_attr($post_id).'.post-link .post-inner-link.link .fa-link {
						color: '.esc_attr($link_icon_color).' !important;
					}';
				}
				if (isset($wpqa_link_icon_hover_color) && $wpqa_link_icon_hover_color != "") {
					$custom_css .= '.post-'.esc_attr($post_id).'.post-link .post-inner-link.link:hover .fa-link {
						color: '.esc_attr($wpqa_link_icon_hover_color).' !important;
					}';
				}
				if (isset($wpqa_link_hover_color) && $wpqa_link_hover_color != "") {
					$custom_css .= '.post-'.esc_attr($post_id).'.post-link .post-inner-link.link:hover {
						color: '.esc_attr($wpqa_link_hover_color).' !important;
					}';
				}
			}
		}
		return $custom_css;
	}
endif;
/* Estimate reading time */
if (!function_exists('wpqa_estimate_reading_time')) :
	function wpqa_estimate_reading_time($text,$wpm = 200) {
		$totalWords = str_word_count(strip_tags($text));
		$minutes = floor($totalWords/$wpm);
		$seconds = floor($totalWords%$wpm/($wpm/60));
		return array(
			'minutes' => $minutes,
			'seconds' => $seconds
		);
	}
endif;
/* Estimate reading time text */
if (!function_exists('wpqa_estimate_reading_time_text')) :
	function wpqa_estimate_reading_time_text($text,$meta_icon) {
		$estimate = wpqa_estimate_reading_time($text);
		if ($meta_icon != "on") {
			$estimate_text = (isset($estimate["minutes"]) && $estimate["minutes"] > 0?sprintf(esc_html__("%s mins read","wpqa"),$estimate["minutes"]):esc_html__("1 mins read","wpqa"));
		}else {
			$estimate_text = (isset($estimate["minutes"]) && $estimate["minutes"] > 0?sprintf(esc_html__("%s mins","wpqa"),$estimate["minutes"]):esc_html__("1 mins","wpqa"));
		}
		return $estimate_text;
	}
endif;
/* Captcha */
if (!function_exists('wpqa_add_captcha')) :
	function wpqa_add_captcha($the_captcha,$type,$rand,$comment = "") {
		$captcha_style = wpqa_options("captcha_style");
		$captcha_question = wpqa_options("captcha_question");
		$captcha_answer = wpqa_options("captcha_answer");
		$show_captcha_answer = wpqa_options("show_captcha_answer");
		$out = "";
		$captcha_users = wpqa_options("captcha_users");
		if ($the_captcha == "on" && ($captcha_users == "both" || ($captcha_users == "unlogged" && !is_user_logged_in()))) {
			$out .= "<div class='".($captcha_style == "question_answer" || $captcha_style == "google_recaptcha"?"wpqa_captcha_question":"wpqa_captcha_normal")."'><".($comment == "comment" || $captcha_style == "google_recaptcha"?"div":"p")." class='wpqa_captcha_p".($captcha_style == "google_recaptcha"?" wpqa_captcha_p_google":" wpqa_captcha_p_other").($comment == "comment"?" form-input form-input-full clearfix":"")."'>";
					$out .= ($comment == "comment"?"":"<label for='wpqa_captcha_".$rand."'>".esc_html__('Captcha','wpqa')."<span class='required'>*</span></label>");
				if ($captcha_style != "google_recaptcha") {
					$out .= '<input'.($comment == "comment"?" placeholder='".esc_attr__("Captcha","wpqa")."'":"").' id="wpqa_captcha_'.$rand.'" name="wpqa_captcha" class="wpqa_captcha'.($captcha_style == "google_recaptcha"?" google_recaptcha":" form-control").($captcha_style == "question_answer"?" captcha_answer":"").'" type="text">
					'.($type == 'comment'?'':'<i class="icon-pencil"></i>');
				}
			if ($captcha_style == "google_recaptcha") {
				$out .= "<div class='g-recaptcha' data-sitekey='".wpqa_options("site_key_recaptcha")."'></div><br>";
			}else if ($captcha_style == "question_answer") {
				$out .= "<span class='wpqa_captcha_span'>".$captcha_question.($show_captcha_answer == "on"?" ( ".$captcha_answer." )":"")."</span>";
			}else {
				$out .= "<img class='wpqa_captcha_img' src='".add_query_arg(array("captcha_type" => $type),plugin_dir_url(dirname(__FILE__))."captcha/create_image.php")."' alt='".esc_attr__("Captcha","wpqa")."' title='".esc_attr__("Click here to update the captcha","wpqa")."' onclick=";$out .='"javascript:wpqa_get_captcha';$out .="('".add_query_arg(array("captcha_type" => $type),plugin_dir_url(dirname(__FILE__))."captcha/create_image.php")."', 'wpqa_captcha_img_".$rand."');";$out .='"';$out .=" id='wpqa_captcha_img_".$rand."'>
				<span class='wpqa_captcha_span'>".esc_html__("Click on image to update the captcha.","wpqa")."</span>";
			}
			$out .= "</".($comment == "comment" || $captcha_style == "google_recaptcha"?"div":"p")."></div>";
		}
		return $out;
	}
endif;
/* Check captcha */
if (!function_exists('wpqa_check_captcha')) :
	function wpqa_check_captcha($the_captcha,$type,$posted,$errors) {
		$captcha_users = wpqa_options("captcha_users");
		$captcha_style = wpqa_options("captcha_style");
		$captcha_question = wpqa_options("captcha_question");
		$captcha_answer = wpqa_options("captcha_answer");
		$show_captcha_answer = wpqa_options("show_captcha_answer");
		$the_captcha = (!isset($_POST['mobile']) && !isset($_GET['mobile']) && !isset($posted['mobile'])?$the_captcha:0);
		if ($the_captcha === "on" && ($captcha_users == "both" || ($captcha_users == "unlogged" && !is_user_logged_in()))) {
			if ($captcha_style == "google_recaptcha") {
				if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
					$secretKey = wpqa_options("secret_key_recaptcha");
					$data = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']);
					if (is_wp_error($data)) {
						$errors->add('required-captcha-error',esc_html__('Robot verification failed, Please try again.','wpqa'));
					}else {
						$json = json_decode($data['body'],true);
					}
					if ((isset($json["success"]) && $json["success"] == true) || (isset($json["error-codes"]) && isset($json["error-codes"][0]) && $json["error-codes"][0] == "timeout-or-duplicate")) {
						//success
					}else {
						$errors->add('required-captcha-error',esc_html__('Robot verification failed, Please try again.','wpqa'));
					}
				}else {
					$errors->add('required-captcha-error',esc_html__('Please check on the reCAPTCHA box.','wpqa'));
				}
			}else {
				if (empty($posted["wpqa_captcha"])) {
					$errors->add('required-captcha',esc_html__("There are required fields (captcha).","wpqa"));
				}
				else if ($captcha_style == "question_answer") {
					if ($captcha_answer != $posted["wpqa_captcha"]) {
						$errors->add('required-captcha-error',esc_html__('The captcha is incorrect, Please try again.','wpqa'));
					}
				}else {
					if (!session_id() && !headers_sent()) {
						session_start();
					}
					if (isset($_SESSION["wpqa_code_captcha_".$type]) && $_SESSION["wpqa_code_captcha_".$type] != $posted["wpqa_captcha"]) {
						$errors->add('required-captcha-error',esc_html__('The captcha is incorrect, Please try again.','wpqa'));
					}
				}
			}
		}
		return $errors;
	}
endif;
/* Allow the uploads */
add_action('admin_init','wpqa_allow_uploads');
function wpqa_allow_uploads() {
	if (is_user_logged_in()) {
		$user_info = get_userdata(get_current_user_id());
		$custom_permission = wpqa_options("custom_permission");
		$roles = $user_info->allcaps;
		$allow_to_upload = apply_filters('wpqa_allow_to_upload',true);
		if ($allow_to_upload == true && !current_user_can('upload_files') && ($custom_permission != "on" || ($custom_permission == "on" && isset($roles["upload_files"]) && $roles["upload_files"] == 1))) {
			$new_role = get_role(implode(', ',$user_info->roles));
			$new_role->add_cap('upload_files');
		}
	}
}
/* Sessions */
if (!function_exists('wpqa_session')) :
	function wpqa_session($message = "",$session = "") {
		$uniqid_cookie = wpqa_options("uniqid_cookie");
		if ($message) {
			if (isset($_COOKIE[$uniqid_cookie.$session]) && $_COOKIE[$uniqid_cookie.$session] != "") {
				unset($_COOKIE[$uniqid_cookie.$session]);
				setcookie($uniqid_cookie.$session,"",-1,COOKIEPATH,COOKIE_DOMAIN);
			}
			setcookie($uniqid_cookie.$session,$message,time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
		}else {
			if (isset($_COOKIE[$uniqid_cookie.$session]) && $_COOKIE[$uniqid_cookie.$session] != "") {
				$last_message = $_COOKIE[$uniqid_cookie.$session];
				unset($_COOKIE[$uniqid_cookie.$session]);
				setcookie($uniqid_cookie.$session,"",-1,COOKIEPATH,COOKIE_DOMAIN);
				return $last_message;
			}
		}
	}
endif;
/* Remove HTML tags */
function wpqa_strip_tags_content($string) {
	$string = strip_tags($string);
	$string = wp_strip_all_tags($string,true);
	$string = strip_shortcodes($string);
	$string = preg_replace('/<[^>]*>/',' ',$string);
	$string = str_replace("\r",'',$string);
	$string = str_replace("\n",' ',$string);
	$string = str_replace("\t",' ',$string);
	$string = str_replace("&nbsp;",' ',$string);
	$string = trim(preg_replace('/ {2,}/',' ',$string));
	return $string;
}
/* HTML tags */
if (!function_exists('wpqa_html_tags')) :
	function wpqa_html_tags($p_active = "") {
		global $allowedposttags,$allowedtags;
		$allowedposttags['table'] = array('id' => true,'data-toggle' => true,'data-search' => true,'data-filter-control' => true,'data-show-export' => true,'data-click-to-select' => true,'data-toolbar' => true);
		$allowedposttags['th'] = array('data-field' => true,'data-checkbox' => true,'data-filter-control' => true,'data-sortable' => true);
		$allowedposttags['math'] = array('xmlns' => true);
		$allowedposttags['mspace'] = array('linebreak' => true);
		$allowedposttags['mfrac'] = array('bevelled' => true);
		$allowedposttags['mfenced'] = array('open' => true,'close' => true);
		$allowedposttags['menclose'] = array('notation' => true);
		$allowedposttags['mi'] = array('mathvariant' => true);
		$allowedposttags['mo'] = array('largeop' => true);
		$allowedposttags['mstyle'] = array('displaystyle' => true);
		$allowedposttags['img'] = array('alt' => true, 'class' => true, 'id' => true, 'title' => true, 'src' => true);
		$allowedposttags['a'] = array('href' => true, 'title' => true, 'target' => true, 'class' => true);
		$allowedposttags['div'] = array('class' => true);
		$allowedposttags['span'] = array('style' => true);
		$allowedtags['table'] = array('id' => true,'data-toggle' => true,'data-search' => true,'data-filter-control' => true,'data-show-export' => true,'data-click-to-select' => true,'data-toolbar' => true);
		$allowedtags['th'] = array('data-field' => true,'data-checkbox' => true,'data-filter-control' => true,'data-sortable' => true);
		$allowedtags['math'] = array('xmlns' => true);
		$allowedtags['mspace'] = array('linebreak' => true);
		$allowedtags['mfrac'] = array('bevelled' => true);
		$allowedtags['mfenced'] = array('open' => true,'close' => true);
		$allowedtags['menclose'] = array('notation' => true);
		$allowedtags['mi'] = array('mathvariant' => true);
		$allowedtags['mo'] = array('largeop' => true);
		$allowedtags['mstyle'] = array('displaystyle' => true);
		$allowedtags['img'] = array('alt' => true, 'class' => true, 'id' => true, 'title' => true, 'src' => true);
		$allowedtags['a'] = array('href' => true, 'title' => true, 'target' => true, 'class' => true);
		$allowedtags['blockquote'] = array('class' => true, 'data-secret' => true, 'style' => true);
		$allowedtags['iframe'] = array('title' => true, 'width' => true, 'height' => true, 'src' => true, 'frameborder' => true, 'allow' => true, 'allowfullscreen' => true);
		$allowedtags['span'] = array('style' => true);
		$allowedtags['\\'] = array();
		$allowedtags['div'] = array('class' => true);
		$allowedtags['pre'] = array('class' => true, 'data-enlighter-language' => true);
		$array = array('hr','br','ul','ol','li','dl','dt','dd','td','tr','thead','tbody','h1','h2','h3','h4','h5','h6','cite','em','address','big','ins','sub','sup','tt','var','msqrt','mn','munder','mrow','msubsup','mroot','msup','msub','mover','munderover','mtable','mtr','mtd');
		foreach ($array as $value) {
			$allowedposttags[$value] = array();
			$allowedtags[$value] = array();
		}
		if ($p_active == "yes") {
			$allowedtags['p'] = array('style' => true);
			$allowedposttags['p'] = array('style' => true);
		}
	}
endif;
add_action('wpqa_init','wpqa_html_tags',10);
/* Kses stip */
if (!function_exists('wpqa_kses_stip')) :
	function wpqa_kses_stip($value,$ireplace = "",$p_active = "") {
		return wpqa_deslash(wp_kses(($ireplace == "yes"?str_ireplace(array("<br />","<br>","<br/>","</p>"), "\r\n",$value):$value),wpqa_html_tags(($p_active == "yes"?$p_active:""))));
	}
endif;
/* Kses stip wpautop */
if (!function_exists('wpqa_kses_stip_wpautop')) :
	function wpqa_kses_stip_wpautop($value,$ireplace = "",$p_active = "") {
		return wpqa_deslash(wpautop(wp_kses((($ireplace == "yes"?str_ireplace(array("<br />","<br>","<br/>","</p>"), "\r\n",$value):$value)),wpqa_html_tags(($p_active == "yes"?$p_active:"")))));
	}
endif;
/* Remove backslash */
function wpqa_deslash($content) {
	$content = preg_replace("/\\\+'/","'",$content);
	$content = preg_replace('/\\\+"/','"',$content);
	return $content;
}
/* Esc textarea */
function wpqa_esc_textarea($text = "") {
	return wpqa_kses_stip($text,"","yes");
}
/* Count meta */
function wpqa_meta_count($key,$value = '',$compare = '=') {
	global $wpdb;
	$count = $wpdb->get_var("SELECT COUNT(meta_id) FROM $wpdb->postmeta WHERE meta_key = '$key'".($value !== "" || $compare == "!="?" AND meta_value $compare '$value'":""));
	return $count;
}
/* Count number */
if (!function_exists('wpqa_count_number')) :
	function wpqa_count_number($input) {
		$active_separator = wpqa_options("active_separator");
		$number_separator = wpqa_options("number_separator");
		$input = number_format((int)$input,0,'',($active_separator != 'on'?',':$number_separator));
		$input_count = substr_count($input,',');
		if ($active_separator != 'on' && $input_count != '0') {
			if ($input_count == '1') {
				return (int)substr($input,0,-4).esc_html__('k','wpqa');
			}else if ($input_count == '2') {
				return (int)substr($input,0,-8).esc_html__('mil','wpqa');
			}else if ($input_count == '3') {
				return (int)substr($input,0,-12).esc_html__('bil','wpqa');
			}else {
				return;
			}
		}else {
			return $input;
		}
	}
endif;
/* Get resize img url */
if (!function_exists('wpqa_get_aq_resize_img_url')) :
	function wpqa_get_aq_resize_img_url($img_width_f,$img_height_f,$img_lightbox = "",$thumbs = "",$gif = false,$title = "") {
		if (empty($thumbs)) {
			$thumb = get_post_thumbnail_id();
		}else {
			$thumb = $thumbs;
		}
		if ($img_lightbox == "lightbox" || $img_width_f == "" || $img_height_f == "") {
			$full_image = wp_get_attachment_image_src($thumb,"full");
			if ($img_lightbox == "lightbox") {
				$img_url = $full_image[0];
			}
			$img_width_f = ($img_width_f != ""?$img_width_f:(isset($full_image[1])?$full_image[1]:0));
			$img_height_f = ($img_height_f != ""?$img_height_f:(isset($full_image[2])?$full_image[2]:0));
		}
		if (is_numeric($thumb)) {
			$image = wpqa_resize($thumb,'',$img_width_f,$img_height_f,true,$gif);
		}
		if (isset($image['url']) && $image['url'] != "") {
			$last_image = $image['url'];
		}else {
			$last_image = "https://placehold.jp/".$img_width_f."x".$img_height_f;
		}
		if (isset($last_image) && $last_image != "") {
			return $last_image;
		}
	}
endif;
/* Get resize img */
if (!function_exists('wpqa_get_aq_resize_img')) :
	function wpqa_get_aq_resize_img($img_width_f,$img_height_f,$img_lightbox = "",$thumbs = "",$gif = false,$title = "",$srcset = "",$class = "") {
		$last_image = wpqa_get_aq_resize_img_url($img_width_f,$img_height_f,$img_lightbox,$thumbs,$gif,$title);
		if (isset($last_image) && $last_image != "") {
			if ($srcset != "") {
				$last_image_2 = wpqa_get_aq_resize_img_url($img_width_f*2,$img_height_f*2,$img_lightbox,$thumbs,$gif,$title);
			}
			return ($img_lightbox == "lightbox"?"<a href='".esc_url($img_url)."'>":"")."<img".($class != ""?" class ='".$class."'":"").($srcset != ""?" srcset='".$last_image." 1x, ".$last_image_2." 2x'":"")." alt='".(isset($title) && $title != ""?$title:get_the_title())."' width='".$img_width_f."' height='".$img_height_f."' src='".$last_image."'>".($img_lightbox == "lightbox"?"</a>":"");
		}
	}
endif;
/* Get resize image with URL */
if (!function_exists('wpqa_get_aq_resize_url')) :
	function wpqa_get_aq_resize_url($url,$img_width_f,$img_height_f,$gif = false) {
		$image = wpqa_resize("", $url, $img_width_f, $img_height_f, true,$gif);
		if (isset($image['url']) && $image['url'] != "") {
			$last_image = $image['url'];
		}else {
			$last_image = "https://placehold.jp/".$img_width_f."x".$img_height_f;
		}
		return $last_image;
	}
endif;
/* Check image id or URL */
if (!function_exists('wpqa_image_url_id')) :
	function wpqa_image_url_id($url_id) {
		if (is_numeric($url_id)) {
			$image = wp_get_attachment_url($url_id);
		}
		
		if (!isset($image)) {
			if (is_array($url_id)) {
				if (isset($url_id['id']) && $url_id['id'] != '' && $url_id['id'] != 0) {
					$image = wp_get_attachment_url($url_id['id']);
				}else if (isset($url_id['url']) && $url_id['url'] != '') {
					$id    = wpqa_get_attachment_id($url_id['url']);
					$image = ($id?wp_get_attachment_url($id):'');
				}
				$image = (isset($image) && $image != ''?$image:$url_id['url']);
			}else {
				if (isset($url_id) && $url_id != '') {
					$id    = wpqa_get_attachment_id($url_id);
					$image = ($id?wp_get_attachment_url($id):'');
				}
				$image = (isset($image) && $image != ''?$image:$url_id);
			}
		}
		if (isset($image) && $image != "") {
			return $image;
		}
	}
endif;
/* Get the attachment ID */
if (!function_exists('wpqa_get_attachment_id')) :
	function wpqa_get_attachment_id($image_url) {
		global $wpdb;
		$pathinfo = pathinfo($image_url);
		$image_url = $pathinfo['filename'].'.'.$pathinfo['extension'];
		if (strpos($image_url,esc_url(home_url('/'))) !== false && strpos($image_url,"themes/".get_template()."/image") === false) {
			$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID,guid FROM $wpdb->posts WHERE guid RLIKE '%s';",$image_url));
			if (isset($attachment[0]) && $attachment[0] != "") {
				return $attachment[0];
			}
		}
	}
endif;
if (!function_exists('wpqa_get_attachment_id')) :
	function wpqa_get_attachment_id($image_url) {
		return attachment_url_to_postid($image_url);
	}
endif;
/* Get first image */
if (!function_exists('wpqa_image')) :
	function wpqa_image () {
		global $post;
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i',$post->post_content,$matches);
		if (isset($matches[1][0])) {
			return $matches[1][0];
		}else {
			return false;
		}
	}
endif;
/* Admin bar */
add_action('wp_before_admin_bar_render','wpqa_admin_bar');
if (!function_exists('wpqa_admin_bar')) :
	function wpqa_admin_bar() {
		global $wp_admin_bar;
		if (is_super_admin()) {
			if (wpqa_is_user_profile()) {
				$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
				if ($wpqa_user_id > 0) {
					$wp_admin_bar->add_menu( array(
						'parent' => 0,
						'id' => 'edit_user',
						'title' => '<span class="ab-icon dashicons-before dashicons-edit"></span>'.esc_html__("Edit User","wpqa").'</span></span>' ,
						'href' => admin_url('user-edit.php?user_id='.$wpqa_user_id)
					));
				}
			}
			if (is_singular(wpqa_asked_questions_type)) {
				global $post;
				if (isset($post->ID) && $post->ID > 0) {
					$wp_admin_bar->add_menu( array(
						'parent' => 0,
						'id' => 'edit_asked_question',
						'title' => '<span class="ab-icon dashicons-before dashicons-edit"></span>'.esc_html__("Edit Asked Question","wpqa").'</span></span>' ,
						'href' => admin_url('post.php?post='.$post->ID.'&action=edit')
					));
				}
			}
			$count_questions_by_type = wpqa_count_posts_by_type(array(wpqa_questions_type,wpqa_asked_questions_type),"draft");
			if ($count_questions_by_type > 0) {
				$wp_admin_bar->add_menu( array(
					'parent' => 0,
					'id' => 'questions_draft',
					'title' => '<span class="ab-icon dashicons-before dashicons-editor-help"></span><span class="count-'.$count_questions_by_type.'"><span>'.$count_questions_by_type.'</span></span>' ,
					'href' => admin_url('edit.php?post_status=draft&post_type='.wpqa_questions_type)
				));
			}
			$count_posts_by_type = wpqa_count_posts_by_type( "post", "draft" );
			if ($count_posts_by_type > 0) {
				$wp_admin_bar->add_menu( array(
					'parent' => 0,
					'id' => 'posts_draft',
					'title' => '<span class="ab-icon dashicons-before dashicons-media-text"></span><span class="count-'.$count_posts_by_type.'"><span>'.$count_posts_by_type.'</span></span>' ,
					'href' => admin_url('edit.php?post_status=draft&post_type=post')
				));
			}
			$payment_available = wpqa_payment_available();
			if ($payment_available == true) {
				$new_payments = (int)get_option("new_payments");
				$wp_admin_bar->add_menu( array(
					'parent' => 0,
					'id' => 'new_payments',
					'title' => '<span class="ab-icon dashicons-before dashicons-cart"></span><span class="count-'.$new_payments.'"><span>'.$new_payments.'</span></span>' ,
					'href' => admin_url('edit.php?post_type=statement')
				));
			}
			$active_message = wpqa_options('active_message');
			$count_messages_by_type = wpqa_count_posts_by_type( "message", "draft" );
			if ($active_message == "on" && $count_messages_by_type > 0) {
				$wp_admin_bar->add_menu( array(
					'parent' => 0,
					'id' => 'messages_draft',
					'title' => '<span class="ab-icon dashicons-before dashicons-email-alt"></span><span class="count-'.$count_messages_by_type.'"><span>'.$count_messages_by_type.'</span></span>' ,
					'href' => admin_url('edit.php?post_status=draft&post_type=message')
				));
			}
			$count_users = count_users();
			$count_user_under_review = (isset($count_users["avail_roles"]["wpqa_under_review"])?$count_users["avail_roles"]["wpqa_under_review"]:0);
			if ($count_user_under_review > 0) {
				$wp_admin_bar->add_menu( array(
					'parent' => 0,
					'id' => 'user_under_review',
					'title' => '<span class="ab-icon dashicons-before dashicons-admin-users"></span><span class="count-'.$count_user_under_review.'"><span>'.$count_user_under_review.'</span></span>' ,
					'href' => admin_url('users.php?role=wpqa_under_review')
				));
			}
			$support_activate = wpqa_updater();
			if ($support_activate) {
				$wp_admin_bar->add_menu(array(
					'parent' => 0,
					'id'     => wpqa_prefix_theme.'_page',
					'title'  => wpqa_name_theme.' Settings' ,
					'href'   => admin_url('admin.php?page=options')
				));
			}
		}
	}
endif;
/* Admin bar menu */
add_action('admin_bar_menu','wpqa_admin_bar_menu',70);
if (!function_exists('wpqa_admin_bar_menu')) :
	function wpqa_admin_bar_menu($wp_admin_bar) {
		if (is_super_admin()) {
			$count_comment_only = wpqa_options("count_comment_only");
			$answers_count = wpqa_comments_of_post_type(array(wpqa_questions_type,wpqa_asked_questions_type),0,array(),"",($count_comment_only == "on"?0:""));
			if ($answers_count > 0) {
				$wp_admin_bar->add_node( array(
					'parent' => 0,
					'id' => 'answers',
					'title' => '<span class="ab-icon dashicons-before dashicons-format-chat"></span><span class="count-'.$answers_count.'"><span>'.$answers_count.'</span></span>' ,
					'href' => admin_url('edit-comments.php?comment_status=answers')
				));
			}
		}
	}
endif;
/* Comments */
if (!function_exists('wpqa_comments')) :
	function wpqa_comments($args = array()) {
		$defaults = array(
			'post_or_question' => 'post',
			'comments_number'  => '5',
			'comment_excerpt'  => '20',
			'show_images'      => 'on',
			'display_date'     => 'on',
			'specific_date'    => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$post_or_question = $args['post_or_question'];
		$comments_number  = $args['comments_number'];
		$comment_excerpt  = $args['comment_excerpt'];
		$show_images      = $args['show_images'];
		$display_date     = $args['display_date'];
		$specific_date    = $args['specific_date'];

		if (isset($specific_date)) {
			if ($specific_date == "24" || $specific_date == "48" || $specific_date == "72" || $specific_date == "96" || $specific_date == "120" || $specific_date == "144") {
				$specific_date = $specific_date." hours";
			}else if ($specific_date == "week" || $specific_date == "month" || $specific_date == "year") {
				$specific_date = "1 ".$specific_date;
			}
		}
		$specific_date_array = (isset($specific_date) && $specific_date != "" && $specific_date != "all"?array('date_query' => array(array('after' => $specific_date.' ago'))):array());
		
		$block_users = wpqa_options("block_users");
		$author__not_in = array();
		if ($block_users == "on") {
			$user_id = get_current_user_id();
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
				}
			}
		}

		$comments = get_comments(array_merge($author__not_in,$specific_date_array,array("post_type" => $post_or_question,"status" => "approve","number" => $comments_number)));?>
		<div class="user-notifications user-profile-area">
			<div>
				<ul>
					<?php foreach ($comments as $comment) {
						$comment_user_id = $comment->user_id;
						$comment_id = $comment->comment_ID;
						$user = get_user_by('id',$comment_user_id);
						$anonymously_user = get_comment_meta($comment_id,'anonymously_user',true);
						$deleted_user = ($comment_user_id > 0 && isset($user->display_name)?$user->display_name:($comment_user_id == 0?$comment->comment_author:"delete"));
						$comment_author_name = ($deleted_user == "delete"?esc_html__("[Deleted User]","wpqa"):($anonymously_user > 0 || $anonymously_user == "anonymously"?esc_html__('Anonymous','wpqa'):$deleted_user));
						$user_profile_page = wpqa_profile_url($comment_user_id);?>
						<li class="notifications__item d-flex <?php echo wpqa_get_gender_class($comment_user_id,0,$comment_id)?>">
							<?php if ($show_images == "on") {?>
								<span class="span-icon author__avatar">
									<?php if ($comment_user_id > 0) {?>
										<a href="<?php echo esc_url($user_profile_page)?>">
									<?php }
										echo wpqa_get_user_avatar(array("user_id" => ($comment_user_id > 0?$comment_user_id:$comment->comment_author_email),"size" => 25,"user_name" => $comment->comment_author,"class" => "rounded-circle"));
									if ($comment_user_id > 0) {?>
										</a>
									<?php }?>
								</span>
							<?php }?>
							<div class="notification__body">
								<?php echo ($comment_user_id > 0?"<a class='author__name' href='".esc_url($user_profile_page)."'>":"").$comment_author_name.($comment_user_id > 0?"</a>":"") ." ". ($post_or_question == wpqa_questions_type?esc_html__("added an answer","wpqa"):esc_html__("added a comment","wpqa")) ?> <span class="question-title"><a class="notification__question notification__question-dark" href="<?php echo get_permalink($comment->comment_post_ID);?>#comment-<?php echo (int)$comment_id;?>"><?php echo wp_trim_words($comment->comment_content,$comment_excerpt);?></a></span>
								<?php if ($display_date == "on") {
									$time_format = wpqa_options("time_format");
									$time_format = ($time_format?$time_format:get_option("time_format"));
									$date_format = wpqa_options("date_format");
									$date_format = ($date_format?$date_format:get_option("date_format"));?>
									<span class="notifications-date notification__date d-block mt-2"><?php printf(esc_html__('%1$s at %2$s','wpqa'),get_comment_date($date_format,$comment_id),get_comment_date($time_format,$comment_id))?></span>
								<?php }?>
							</div>
						</li>
						<?php
					}?>
				</ul>
			</div>
		</div>
	<?php }
endif;
/* Posts */
if (!function_exists('wpqa_posts')) :
	function wpqa_posts($args = array()) {
		$defaults = array(
			'posts_per_page'           => '5',
			'orderby'                  => '',
			'excerpt_title'            => '5',
			'show_images'              => 'on',
			'post_or_question'         => 'post',
			'display_comment'          => 'on',
			'display'                  => '',
			'category'                 => '',
			'categories'               => array(),
			'e_categories'             => array(),
			'custom_posts'             => '',
			'display_question'         => '',
			'category_question'        => '',
			'categories_question'      => array(),
			'e_cats_question'          => array(),
			'custom_questions'         => '',
			'display_knowledgebase'    => '',
			'category_knowledgebase'   => '',
			'categories_knowledgebase' => array(),
			'e_cats_knowledgebase'     => array(),
			'custom_knowledgebases'    => '',
			'custom_args'              => array(),
			'no_query'                 => '',
			'display_image'            => 'on',
			'display_video'            => 'on',
			'display_date'             => 'on',
			'blog_h_button'            => '',
			'blog_h_button_text'       => esc_html__('Explore Our Blog','wpqa'),
			'blog_h_page'              => '',
			'blog_h_link'              => '',
			'post_style'               => 'style_1',
			'excerpt_post'             => '40',
			'specific_date'            => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$posts_per_page           = $args['posts_per_page'];
		$orderby                  = $args['orderby'];
		$excerpt_title            = $args['excerpt_title'];
		$show_images              = $args['show_images'];
		$post_or_question         = $args['post_or_question'];
		$display_comment          = $args['display_comment'];
		$display                  = $args['display'];
		$category                 = $args['category'];
		$categories               = $args['categories'];
		$e_categories             = $args['e_categories'];
		$custom_posts             = $args['custom_posts'];
		$display_question         = $args['display_question'];
		$category_question        = $args['category_question'];
		$categories_question      = $args['categories_question'];
		$e_cats_question          = $args['e_cats_question'];
		$custom_questions         = $args['custom_questions'];
		$display_knowledgebase    = $args['display_knowledgebase'];
		$category_knowledgebase   = $args['category_knowledgebase'];
		$categories_knowledgebase = $args['categories_knowledgebase'];
		$e_cats_knowledgebase     = $args['e_cats_knowledgebase'];
		$custom_knowledgebases    = $args['custom_knowledgebases'];
		$custom_args              = $args['custom_args'];
		$no_query                 = $args['no_query'];
		$display_image            = $args['display_image'];
		$display_video            = $args['display_video'];
		$display_date             = $args['display_date'];
		$blog_h_button            = $args['blog_h_button'];
		$blog_h_button_text       = $args['blog_h_button_text'];
		$blog_h_page              = $args['blog_h_page'];
		$blog_h_link              = $args['blog_h_link'];
		$post_style               = $args['post_style'];
		$excerpt_post             = $args['excerpt_post'];
		$specific_date            = $args['specific_date'];
		
		global $post;
		$updated_answers = wpqa_options("updated_answers");
		if (empty($custom_args)) {
			if ($updated_answers == "on" && $orderby == "recent" && $post_or_question == wpqa_questions_type) {
				global $wpdb;
			}
			$get_current_user_id = get_current_user_id();
			if ($orderby == "popular") {
				$orderby_array = array('orderby' => 'comment_count');
			}else if ($orderby == "random") {
				$orderby_array = array('orderby' => 'rand');
			}else if ($orderby == "most_visited") {
				$post_meta_stats = wpqa_get_meta_stats();
				$orderby_array = array('orderby' => array('post_stats_order' => "DESC"),"meta_query" => array('post_stats_order' => array('type' => 'numeric',"key" => $post_meta_stats,"value" => 0,"compare" => ">=")));
			}else if ($orderby == "most_voted") {
				if ($post_or_question == wpqa_knowledgebase_type) {
					$orderby_array = array('orderby' => array('knowledgebase_vote_order' => "DESC"),"meta_query" => array('knowledgebase_vote_order' => array('type' => 'numeric',"key" => "wpqa_rate_up","value" => 0,"compare" => ">=")));
				}else {
					$orderby_array = array('orderby' => array('question_vote_order' => "DESC"),"meta_query" => array('question_vote_order' => array('type' => 'numeric',"key" => "question_vote","value" => 0,"compare" => ">=")));
				}
			}else if ($orderby == "most_reacted") {
				$orderby_array = array('orderby' => array('question_reacted_order' => "DESC"),"meta_query" => array('question_reacted_order' => array('type' => 'numeric',"key" => "wpqa_reactions_count","value" => 0,"compare" => ">=")));
			}else if ($orderby == "most_rated") {
				$orderby_array = array("orderby" => "meta_value_num","meta_key" => "final_review","meta_query" => array(array('type' => 'numeric',"key" => "final_review","value" => 0,"compare" => ">=")));
			}else {
				$orderby_array = array();
			}
			
			if ($post_or_question == "post") {
				$display      = $display;
				$category     = $category;
				$categories   = $categories;
				$e_categories = $e_categories;
				$custom_posts = $custom_posts;
				$taxonomy     = "category";
			}else if ($post_or_question == wpqa_questions_type) {
				$display      = $display_question;
				$category     = $category_question;
				$categories   = $categories_question;
				$e_categories = $e_cats_question;
				$custom_posts = $custom_questions;
				$taxonomy     = wpqa_question_categories;
			}else if ($post_or_question == wpqa_knowledgebase_type) {
				$display      = $display_knowledgebase;
				$category     = $category_knowledgebase;
				$categories   = $categories_knowledgebase;
				$e_categories = $e_cats_knowledgebase;
				$custom_posts = $custom_knowledgebases;
				$taxonomy     = wpqa_knowledgebase_categories;
			}
			
			$categories_a = $exclude_categories_a = array();
			if (isset($categories) && is_array($categories)) {
				$categories_a = $categories;
			}
			
			if (isset($e_categories) && is_array($e_categories)) {
				$exclude_categories_a = $e_categories;
			}
			
			if ($display == "category") {
				$custom_catagories_updated = $category;
				$cat_query = array('tax_query' => array(array('taxonomy' => $taxonomy,'field' => 'id','terms' => $category,'operator' => 'IN')));
			}else if ($display == "categories") {
				$custom_catagories_updated = $categories_a;
				$cat_query = array('tax_query' => array(array('taxonomy' => $taxonomy,'field' => 'id','terms' => $categories_a,'operator' => 'IN')));
			}else if ($display == "exclude_categories") {
				$custom_catagories_updated = $exclude_categories_a;
				$cat_query = array('tax_query' => array(array('taxonomy' => $taxonomy,'field' => 'id','terms' => $exclude_categories_a,'operator' => 'NOT IN')));
			}else if ($display == "custom_posts") {
				$custom_posts_updated = $custom_posts;
				$custom_posts = explode(",",$custom_posts);
				$cat_query = array('post__in' => $custom_posts);
			}else {
				$cat_query = array();
			}
		}

		if ($specific_date == "24" || $specific_date == "48" || $specific_date == "72" || $specific_date == "96" || $specific_date == "120" || $specific_date == "144") {
			$specific_date = $specific_date." hours";
		}else if ($specific_date == "week" || $specific_date == "month" || $specific_date == "year") {
			$specific_date = "1 ".$specific_date;
		}

		$specific_date_array = ($specific_date != "" && $specific_date != "all"?array('date_query' => array(array('after' => $specific_date.' ago'))):array());
		$comment_count = ($orderby == "no_response"?array("comment_count" => "0"):array());

		$block_users = wpqa_options("block_users");
		$author__not_in = array();
		if ($block_users == "on") {
			$get_current_user_id = get_current_user_id();
			if ($get_current_user_id > 0) {
				$get_block_users = get_user_meta($get_current_user_id,"wpqa_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("author__not_in" => $get_block_users);
					if ($updated_answers == "on" && $orderby == "recent" && $post_or_question == wpqa_questions_type) {
						$blocked_users = (isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?"AND $wpdb->posts.post_author NOT IN (".implode(",",$get_block_users).")":"");
					}
				}
			}
		}

		if ($updated_answers == "on" && $orderby == "recent" && $post_or_question == wpqa_questions_type) {
			$blocked_users = (isset($blocked_users) && $blocked_users != ""?$blocked_users:"");
			$specific_date = (isset($specific_date) && $specific_date != "" && $specific_date != "all"?$specific_date.' ago':"");
			$date = ($specific_date != ""?"AND ($wpdb->posts.post_date > '".date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"). $specific_date))."')":"");
			$post_display = (isset($display) && $display?$display:"");
			$custom_catagories_updated = (isset($custom_catagories_updated) && is_array($custom_catagories_updated) && !empty($custom_catagories_updated)?$custom_catagories_updated:(isset($custom_catagories_updated) && !is_array($custom_catagories_updated) && $custom_catagories_updated != ""?array($custom_catagories_updated):""));
			$include_posts = (isset($post_display) && $post_display == "custom_posts"?"AND $wpdb->posts.ID IN (".$custom_posts_updated.")":"");
			$custom_catagories_query = (isset($post_display) && ($post_display == "single_category" || $post_display == "categories") && is_array($custom_catagories_updated) && !empty($custom_catagories_updated)?" AND $wpdb->term_relationships.term_taxonomy_id IN (".implode(",",$custom_catagories_updated).")":(isset($post_display) && $post_display == "exclude_categories" && is_array($custom_catagories_updated) && !empty($custom_catagories_updated)?" NOT IN (".implode(",",$custom_catagories_updated).")":""));
			$custom_catagories_updated = (isset($custom_catagories_updated) && $custom_catagories_updated != ""?"AND $wpdb->term_relationships.term_taxonomy_id".$custom_catagories_query:"");

			$custom_catagories_where = ($custom_catagories_updated != ""?"LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ":"");
			$feed_updated = "COALESCE((SELECT MAX(comment_date) FROM $wpdb->comments wpc WHERE wpc.comment_post_id = $wpdb->posts.id),$wpdb->posts.post_date)";
			$custom_where = "AND ( mt1.post_id IS NULL )
			AND $wpdb->posts.post_type = '".wpqa_questions_type."'
			AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')";

			$query_sql = $wpdb->prepare(
				"SELECT DISTINCT $wpdb->posts.*
				FROM $wpdb->posts
				$custom_catagories_where
				LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = 'user_id' )
				WHERE 1=%s
				$custom_catagories_updated $blocked_users $include_posts $date $custom_where
				ORDER BY $feed_updated DESC
				LIMIT $posts_per_page",
				1
			);

			$query = $wpdb->get_results($query_sql);
		}else {
			$query_args = (empty($custom_args)?array_merge($comment_count,$author__not_in,$specific_date_array,$orderby_array,$cat_query,array('post_type' => $post_or_question,'ignore_sticky_posts' => 1,'cache_results' => false,'no_found_rows' => true,'posts_per_page' => $posts_per_page)):$custom_args);
			$related_query = new WP_Query($query_args);
		}

		$out = '';
		if (($updated_answers == "on" && $orderby == "recent" && $post_or_question == wpqa_questions_type && isset($query) && is_array($query) && !empty($query)) || (isset($related_query) && $related_query->have_posts())) :
			$out .= '<div class="user-notifications user-profile-area questions-card'.($post_style == "style_2"?" widget-post-style-2".($display_image === "on"?" post-style-2-image":""):"").'">
				<div>
					<ul>';
						if ($updated_answers == "on" && $orderby == "recent" && $post_or_question == wpqa_questions_type) {
							foreach ($query as $post) :
								setup_postdata($post->ID);
								$out .= wpqa_get_posts($post,$args);
							endforeach;
							wp_reset_postdata();
						}else {
							while ( $related_query->have_posts() ) :
								$related_query->the_post();
								$out .= wpqa_get_posts($post,$args);
							endwhile;
						}
					$out .= '</ul>';
					if ($post_or_question == "post" && $post_style == "style_2" && $blog_h_button === "on") {
						$out .= '<div class="blog-post-button"><a href="'.esc_url(($blog_h_link != ""?$blog_h_link:($blog_h_page != "" && $blog_h_page > 0?get_page_link($blog_h_page):""))).'" class="button-default btn btn__primary btn__block btn__semi__height">'.($blog_h_button_text != ""?$blog_h_button_text:esc_html__("Explore Our Blog","wpqa")).'</a></div>';
					}
				$out .= '</div>
			</div>';
		else :
			$out .= (isset($no_query) && $no_query == "no_query"?"no_query":"");
		endif;
		if (isset($related_query)) {
			wp_reset_postdata();
		}
		return $out;
	}
endif;
/* Get post details */
if (!function_exists('wpqa_get_posts')) :
	function wpqa_get_posts($post,$args = array()) {
		$posts_per_page           = $args['posts_per_page'];
		$orderby                  = $args['orderby'];
		$excerpt_title            = $args['excerpt_title'];
		$show_images              = $args['show_images'];
		$post_or_question         = $args['post_or_question'];
		$display_comment          = $args['display_comment'];
		$display                  = $args['display'];
		$category                 = $args['category'];
		$categories               = $args['categories'];
		$e_categories             = $args['e_categories'];
		$custom_posts             = $args['custom_posts'];
		$display_question         = $args['display_question'];
		$category_question        = $args['category_question'];
		$categories_question      = $args['categories_question'];
		$e_cats_question          = $args['e_cats_question'];
		$custom_questions         = $args['custom_questions'];
		$display_knowledgebase    = $args['display_knowledgebase'];
		$category_knowledgebase   = $args['category_knowledgebase'];
		$categories_knowledgebase = $args['categories_knowledgebase'];
		$e_cats_knowledgebase     = $args['e_cats_knowledgebase'];
		$custom_knowledgebases    = $args['custom_knowledgebases'];
		$custom_args              = $args['custom_args'];
		$no_query                 = $args['no_query'];
		$display_image            = $args['display_image'];
		$display_video            = $args['display_video'];
		$display_date             = $args['display_date'];
		$blog_h_button            = $args['blog_h_button'];
		$blog_h_button_text       = $args['blog_h_button_text'];
		$blog_h_page              = $args['blog_h_page'];
		$blog_h_link              = $args['blog_h_link'];
		$post_style               = $args['post_style'];
		$excerpt_post             = $args['excerpt_post'];
		$specific_date            = $args['specific_date'];
		
		$post_link_target         = apply_filters("wpqa_post_link_target","");

		$get_permalink = get_permalink($post->ID);
		$what_post = get_post_meta($post->ID,'what_post',true);
		$video_type = get_post_meta($post->ID,'video_post_type',true);
		$out = '<li class="notifications__item question-item list-item-type-'.$post->post_type.' '.wpqa_get_gender_class($post->post_author,$post->ID).' d-flex widget-posts-';if (is_sticky()) {$out .= 'sticky';}else if ($what_post == "google") {$out .= 'google';}else if ($what_post == "audio") {$out .= 'volume-up';}else if ($what_post == "video") {if ($video_type == 'youtube') {$out .= 'youtube';}else if ($video_type == 'vimeo') {$out .= 'vimeo';}else if ($video_type == 'daily' || $video_type == 'embed' || $video_type == 'html5' || $video_type == 'facebook') {$out .= 'daily';}}else if ($what_post == "slideshow") {$out .= 'slideshow';}else if ($what_post == "quote") {$out .= 'quote';}else if ($what_post == "link") {$out .= 'link';}else if ($what_post == "soundcloud") {$out .= 'soundcloud';}else if ($what_post == "twitter") {$out .= 'twitter';}else if ($what_post == "facebook") {$out .= 'facebook';}else if ($what_post == "instagram") {$out .= 'instagram';}else {if (has_post_thumbnail()) {$out .= 'image';}else {$out .= 'text';}}$out .= (has_post_thumbnail()?'':' widget-no-img').($display_comment === "on" || ($post_style == "style_2" && $display_date === "on")?'':' widget-no-meta').'">';
			$video_description = get_post_meta($post->ID,"video_description",true);
			if ($post_style == "style_2" && $display_video === "on" && ($what_post == "video" || $video_description == "on")) {
				if ($post_or_question == wpqa_questions_type) {
					$ask_question_items = wpqa_options("ask_question_items");
					$video_desc_active = (isset($ask_question_items["video_desc_active"]["value"]) && $ask_question_items["video_desc_active"]["value"] == "video_desc_active"?"on":"");
					if ($video_desc_active == "on" && $video_description == "on") {
						$video_desc = get_post_meta($post->ID,'video_desc',true);
						$video_id = get_post_meta($post->ID,"video_id",true);
						$video_type = get_post_meta($post->ID,"video_type",true);
						if ($video_id != "") {
							$type = wpqa_video_iframe($video_type,$video_id,"post_meta","video_id",$post->ID);
							$las_video = '<iframe frameborder="0" allowfullscreen height="155" src="'.$type.'"></iframe>';
							$out .= '<div class="question-video-widget video-type-'.$video_type.'">'.$las_video.'</div>';
						}
					}
				}else if ($what_post == "video") {
					$video_id = get_post_meta($post->ID,prefix_meta.'video_post_id',true);
					if ($video_id != "") {
						$type = wpqa_video_iframe($video_type,$video_id,"post_meta",prefix_meta."video_post_id",$post->ID);
					}
					$video_mp4 = get_post_meta($post->ID,prefix_meta."video_mp4",true);
					$video_m4v = get_post_meta($post->ID,prefix_meta."video_m4v",true);
					$video_webm = get_post_meta($post->ID,prefix_meta."video_webm",true);
					$video_ogv = get_post_meta($post->ID,prefix_meta."video_ogv",true);
					$video_wmv = get_post_meta($post->ID,prefix_meta."video_wmv",true);
					$video_flv = get_post_meta($post->ID,prefix_meta."video_flv",true);
					$video_image = get_post_meta($post->ID,prefix_meta."video_image",true);
					$video_mp4 = (isset($video_mp4) && $video_mp4 != ""?" mp4='".$video_mp4."'":"");
					$video_m4v = (isset($video_m4v) && $video_m4v != ""?" m4v='".$video_m4v."'":"");
					$video_webm = (isset($video_webm) && $video_webm != ""?" webm='".$video_webm."'":"");
					$video_ogv = (isset($video_ogv) && $video_ogv != ""?" ogv='".$video_ogv."'":"");
					$video_wmv = (isset($video_wmv) && $video_wmv != ""?" wmv='".$video_wmv."'":"");
					$video_flv = (isset($video_flv) && $video_flv != ""?" flv='".$video_flv."'":"");
					$video_image = (isset($video_image) && $video_image != ""?" poster='".wpqa_image_url_id($video_image)."'":"");
					if ($video_type == "html5") {
						$out .= do_shortcode('[video'.$video_mp4.$video_m4v.$video_webm.$video_ogv.$video_wmv.$video_flv.$video_image.']');
					}else if ($video_type == "embed") {
						$out .= get_post_meta($post->ID,"custom_embed",true);
					}else if (isset($type) && $type != "") {
						$las_video = '<iframe frameborder="0" allowfullscreen height="155" src="'.$type.'"></iframe>';
						$out .= '<div class="question-video-widget video-type-'.$video_type.'">'.$las_video.'</div>';
					}
				}
			}else if ($post_style == "style_2" && $display_image === "on") {
				$out .= '<div class="widget-post-image"><a'.$post_link_target.' href="'.$get_permalink.'" title="'.sprintf('%s', the_title_attribute('echo=0')).'" rel="bookmark">';
				if (has_himer() || has_knowly()) {
					$img_width = "311";
					$img_height = "190";
				}else {
					$img_width = "229";
					$img_height = "155";
				}
				if (has_post_thumbnail()) {
					$out .= apply_filters("wpqa_filter_image_widget",wpqa_get_aq_resize_img($img_width,$img_height),$post,$img_width,$img_height);
				}else {
					$wpqa_image = wpqa_image();
					if (!is_single() && !empty($wpqa_image)) {
						$out .= "<img alt='".get_the_title()."' src='".wpqa_get_aq_resize_url(wpqa_image(),$img_width,$img_height)."'>";
					}
				}
				$out .= '</a></div>';
			}
			if ($post->post_type != wpqa_knowledgebase_type && ($post_style != "style_2" || has_himer() || has_knowly())) {
				if ($post->post_author > 0) {
					$user_name = get_the_author_meta("display_name",$post->post_author);
					$user_id = $post->post_author;
				}else {
					$user_id = get_post_meta($post->ID,$post_or_question.'_email',true);
					$anonymously_user     = get_post_meta($post->ID,"anonymously_user",true);
					$anonymously_question = get_post_meta($post->ID,"anonymously_question",true);
					if (($anonymously_question == "on" || $anonymously_question == 1) && $anonymously_user != "") {
						$user_name = esc_html__('Anonymous','wpqa');
					}else {
						$user_name = get_post_meta($post->ID,$post_or_question."_username",true);
						$user_name = ($user_name != ""?$user_name:esc_html__('Anonymous','wpqa'));
					}
				}
				$user_profile_page = wpqa_profile_url($user_id);
			}
			if ($post->post_type != wpqa_knowledgebase_type && $post_style != "style_2") {
				if ($show_images === "on") {
					$out .= '<span class="span-icon author__avatar">';
						if ($user_id > 0) {
							$out .= '<a href="'.esc_url($user_profile_page).'">';
						}
						$out .= wpqa_get_user_avatar(array("user_id" => $user_id,"size" => 20,"user_name" => $user_name,"class" => "rounded-circle"));
						if ($user_id > 0) {
							$out .= '</a>';
						}
					$out .= '</span>';
				}
			}
			$out .= '<div>';
				if ($post->post_type != wpqa_knowledgebase_type && (has_himer() || has_knowly())) {
					$out .= '<span class="author__name">';
						if ($user_id > 0) {
							$out .= '<a href="'.esc_url($user_profile_page).'">';
						}
						$out .= $user_name;
						if ($user_id > 0) {
							$out .= '</a>';
						}
					$out .= '</span>';
				}
				if (has_discy() && $post_style == "style_2") {
					$sort_title_meta = array("meta","title");
				}else {
					$sort_title_meta = array("title","meta");
				}
				foreach ($sort_title_meta as $key => $value) {
					if ($value == "title") {
						$out .= '<h3 class="question__title"><a class="color-dark"'.$post_link_target.' href="'.$get_permalink.'" title="'.sprintf('%s', the_title_attribute('echo=0')).'" rel="bookmark"><i class="icon-ios-paper-outline wpqa_hide"></i>'.wpqa_excerpt_title($excerpt_title,wpqa_excerpt_type,"return").'</a></h3>';
						if ($post_style == "style_2" && $excerpt_post > 0) {
							$out .= '<p>'.wpqa_excerpt($excerpt_post,wpqa_excerpt_type,false,"return").'</p>';
						}
					}else if ($value == "meta") {
						if ($display_comment === "on" || ($post_style == "style_2" && $display_date === "on")) {
							$out .= '<ul class="widget-post-meta question-item__meta list-unstyled mb-0 d-flex align-items-center">';
							if ($post_style == "style_2" && $display_date === "on") {
								$out .= '<li><span class="post-meta-date question-item__meta__date">';
									$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
									$date_format = wpqa_options("date_format");
									$date_format = ($date_format?$date_format:get_option("date_format"));
									$time_string = sprintf($time_string,esc_html(get_the_date('c')),esc_html(get_the_time($date_format)));
									$data_string = esc_html__("On","wpqa");
									$posted_on   = $data_string.': '.$time_string;
									if (has_himer() || has_knowly()) {
										$posted_on = $time_string;
									}
									$out .= $posted_on;
								$out .= '</span></li>';
							}
							if ($post_or_question != wpqa_knowledgebase_type && $display_comment === "on") {
								$activate_male_female = apply_filters("wpqa_activate_male_female",false);
								if ($activate_male_female == true) {
									$female_comment_count = (int)wpqa_count_comments($post->ID,"female_count_comments","like_meta");
									$male_comment_count = (int)wpqa_count_comments($post->ID,"male_count_comments","like_meta");
									$gender_answers_other = wpqa_options("gender_answers_other");
									if ($gender_answers_other == "on") {
										$count_post_comments = (int)wpqa_count_comments($post->ID,"count_post_comments","like_meta");
										$count_comments_other = (int)($count_post_comments-($female_comment_count+$male_comment_count));
									}
									$out .= '<li class="post-meta-comment-gender post-meta-comment-her"><a class="post-meta-comment" href="'.$get_permalink.'#comments-female"><i class="icon-android-chat"></i>'.$female_comment_count.'</a></li>
									<li class="post-meta-comment-gender post-meta-comment-him"><a class="post-meta-comment" href="'.$get_permalink.'#comments-male"><i class="icon-android-chat"></i>'.$male_comment_count.'</a></li>';
									if ($gender_answers_other == "on" && $count_comments_other > 0) {
										$out .= '<li class="post-meta-comment-gender post-meta-comment-other"><a class="post-meta-comment" href="'.$get_permalink.'#comments-other"><i class="icon-android-chat"></i>'.$count_comments_other.'</a></li>';
									}
								}else {
									$count_post_all = (int)wpqa_count_comments($post->ID);
									$out .= '<li><a class="post-meta-comment" href="'.$get_permalink.'#comments">';
										if (has_discy() && $post_style == "style_2") {
											$comment_string = ($post_or_question == wpqa_questions_type?_n("Answer","Answers",$count_post_all,"wpqa"):_n("Comment","Comments",$count_post_all,"wpqa"));
											$comments = $comment_string.': '.$count_post_all;
										}else if ($post_style != "style_2") {
											$comment_string = ($post_or_question == wpqa_questions_type?sprintf(_n("%s Answer","%s Answers",$count_post_all,"wpqa"),$count_post_all):sprintf(_n("%s Comment","%s Comments",$count_post_all,"wpqa"),$count_post_all));
											$out .= '<i class="icon-comment"></i>';
											$comments = $comment_string;
										}else if ($post_style == "style_2" && (has_himer() || has_knowly())) {
											$out .= '<i class="icon-android-chat"></i>';
											$comments = $count_post_all;
										}
										$out .= $comments;
									$out .= '</a></li>';
								}
							}
							$out .= '</ul>';
						}
					}
				}
			$out .= '</div>
		</li>';
		return $out;
	}
endif;
/* Post tag callback */
if (!function_exists('wpqa_post_tag_callback')) :
	function wpqa_post_tag_callback($count) {
		return sprintf(_n(esc_html__('%s post','wpqa'),esc_html__('%s posts','wpqa'),$count),number_format_i18n($count));
	}
endif;
/* Question tag callback */
if (!function_exists('wpqa_question_tags_callback')) :
	function wpqa_question_tags_callback($count) {
		return sprintf(_n(esc_html__('%s question','wpqa'),esc_html__('%s questions','wpqa'),$count),number_format_i18n($count));
	}
endif;
/* Knowledgebase tag callback */
if (!function_exists('wpqa_kb_tags_callback')) :
	function wpqa_kb_tags_callback($count) {
		return sprintf(_n(esc_html__('%s knowledgebase','wpqa'),esc_html__('%s knowledgebases','wpqa'),$count),number_format_i18n($count));
	}
endif;
/* Count posts by type */
if (!function_exists('wpqa_count_posts_by_type')) :
	function wpqa_count_posts_by_type($post_type = null,$post_status = "publish") {
		$post_type = (is_array($post_type)?$post_type:($post_type != ""?$post_type:"post"));
		$args = array(
			'post_type'   => $post_type,
			'post_status' => $post_status
		);
		$block_users = wpqa_options("block_users");
		$author__not_in = array();
		if ($block_users == "on") {
			$user_id = get_current_user_id();
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("author__not_in" => $get_block_users);
				}
			}
		}
		$the_query = new WP_Query(array_merge($author__not_in,$args));
		return $the_query->found_posts;
		wp_reset_postdata();
	}
endif;
/* Count posts by user */
if (!function_exists('wpqa_count_posts_by_user')) :
	function wpqa_count_posts_by_user($user_id,$post_type = null,$post_status = "publish",$category = 0,$date = 0) {
		$post_type = (is_array($post_type)?$post_type:($post_type != ""?$post_type:"post"));
		$author = ($user_id > 0?array("author" => $user_id):array());
		$tax = (is_array($category) && !empty($category)?array("tax_query" => array(array("taxonomy" => ($post_type == "post"?"category":wpqa_question_categories),"field" => "id","terms" => $category,'operator' => 'IN'))):array());
		$meta_query = ((is_string($post_type) && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) || (is_array($post_type) && (in_array(wpqa_questions_type,$post_type) || in_array(wpqa_asked_questions_type,$post_type)))?array("meta_query" => array("relation" => "OR",array("key" => "private_question","compare" => "NOT EXISTS"),array("key" => "private_question","compare" => "=","value" => 0))):array());
		$date_query = (is_array($date) && !empty($date)?array("date_query" => array($date)):array());
		$args = array(
			"post_type"   => $post_type,
			"post_status" => $post_status,
		);
		$block_users = wpqa_options("block_users");
		$args = array_merge($author,$tax,$meta_query,$date_query,$args);
		$the_query = new WP_Query($args);
		return $the_query->found_posts;
		wp_reset_postdata();
	}
endif;
/* Count new notifications */
if (!function_exists('wpqa_count_new_notifications')) :
	function wpqa_count_new_notifications( $user_id = "", $post_status = "publish" ) {
		$args = array(
			"post_type"   => "notification",
			"author"      => $user_id,
			"post_status" => $post_status,
			"meta_query"  => array(array("key" => "notification_new","compare" => "=","value" => 1))
		);
		$the_query = new WP_Query($args);
		return $the_query->found_posts;
		wp_reset_postdata();
	}
endif;
/* User table */
if (!function_exists('wpqa_user_table')) :
	function wpqa_user_table( $column ) {
		$user_meta_admin = wpqa_options("user_meta_admin");
		if (isset ($user_meta_admin) && is_array($user_meta_admin)) {
			$column['question'] = esc_html__('Questions','wpqa');
			if (isset($user_meta_admin["points"]) && $user_meta_admin["points"] == "points") {
				$column['points'] = esc_html__('Points','wpqa');
			}
			if (isset($user_meta_admin["phone"]) && $user_meta_admin["phone"] == "phone") {
				$column['phone'] = esc_html__('Phone','wpqa');
			}
			if (isset($user_meta_admin["country"]) && $user_meta_admin["country"] == "country") {
				$column['country'] = esc_html__('Country','wpqa');
			}
			if (isset($user_meta_admin["age"]) && $user_meta_admin["age"] == "age") {
				$column['age'] = esc_html__('Age','wpqa');
			}
			if (isset($user_meta_admin["registration"]) && $user_meta_admin["registration"] == "registration") {
				$column['registration'] = esc_html__('Registration date','wpqa');
			}
			if (isset($user_meta_admin["invitation"]) && $user_meta_admin["invitation"] == "invitation") {
				$column['invitation'] = esc_html__('Invitation','wpqa');
			}
		}
		return $column;
	}
endif;
add_filter( 'manage_users_columns', 'wpqa_user_table' );
if (!function_exists('wpqa_user_table_row')) :
	function wpqa_user_table_row( $val, $column_name, $user_id ) {
		$user = get_userdata( $user_id );
		switch ($column_name) {
			case 'question' :
				$count_user_ques_slugs = wpqa_count_posts_meta(wpqa_questions_type,$user_id);
				return ($count_user_ques_slugs > 0?'<a href="'.admin_url('edit.php?post_type='.wpqa_questions_type.'&author='.$user_id).'">':'').$count_user_ques_slugs.($count_user_ques_slugs > 0?'</a>':'');
				break;
			case 'points' :
				$points = (int)get_the_author_meta( 'points', $user_id );
				return $points;
				break;
			case 'phone' :
				$phone = get_the_author_meta( 'phone', $user_id );
				return ($phone != ""?apply_filters("wpqa_show_phone",esc_html($phone),$user_id):" - ");
				break;
			case 'country' :
				$get_countries = apply_filters('wpqa_get_countries',false);
				$country = get_the_author_meta( 'country', $user_id );
				if ($country && isset($get_countries[$country])) {
					return $get_countries[$country];
				}else {
					return ' - ';
				}
				break;
			case 'age' :
				$age = get_the_author_meta( 'age', $user_id );
				return (date_create($age)?date_diff(date_create($age),date_create('today'))->y:"");
				break;
			case 'registration' :
				$date_format = wpqa_options("date_format");
				$date_format = ($date_format?$date_format:get_option("date_format"));
				$registered = get_the_author_meta( 'registered', $user_id );
				$return = date($date_format,strtotime($registered));
				$save_ip_address = wpqa_options("save_ip_address");
				if ($save_ip_address == "on") {
					$get_ip_address = get_user_meta($user_id,'wpqa_ip_address',true);
					if ($get_ip_address != "") {
						$return .= "<br>".$get_ip_address;
					}
				}
				return $return;
				break;
			case 'invitation' :
				$invitation = (int)get_the_author_meta( 'wpqa_invitations', $user_id );
				return ($invitation > 0?'<a href="'.wpqa_profile_url($invitation).'" target="_blank">'.get_the_author_meta('display_name',$invitation).'</a>':' - ');
				break;
			default:
		}
	}
endif;
add_filter( 'manage_users_custom_column', 'wpqa_user_table_row', 10, 3 );
/* Media library */
add_action('pre_get_posts','wpqa_media_library');
if (!function_exists('wpqa_media_library')) :
	function wpqa_media_library($query) {
		global $current_user,$pagenow;
		if (!is_a($current_user,'WP_User') || is_super_admin($current_user->ID))
			return;
		if ('admin-ajax.php' != $pagenow || (isset($_REQUEST['action']) && $_REQUEST['action'] != 'query-attachments'))
			return;
		if (!current_user_can('manage_media_library'))
			$query->set('author',$current_user->ID);
		return;
	}
endif;
/* Remove item by value */
if (!function_exists('wpqa_remove_item_by_value')) :
	function wpqa_remove_item_by_value($array,$val = '',$preserve_keys = true) {
		if (empty($array) || !is_array($array)) {
			return false;
		}
		if (!in_array($val,$array)) {
			return $array;
		}
		foreach ($array as $key => $value) {
			if ($value == $val) unset($array[$key]);
		}
		return ($preserve_keys === true)?$array:array_values($array);
	}
endif;
/* Insert after key in array */
if (!function_exists('wpqa_array_insert_after')) :
function wpqa_array_insert_after( array $array, $key, array $new ) {
	$keys = array_keys( $array );
	$index = array_search( $key, $keys );
	$pos = false === $index ? count( $array ) : $index + 1;
	return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
}
endif;
/* Excerpt row */
if (!function_exists('wpqa_excerpt_row')) :
	function wpqa_excerpt_row($excerpt_length,$content) {
		$words = explode(' ',$content,$excerpt_length + 1);
		if (count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words,'...');
			$content = implode(' ',$words).'...';
		endif;
			$content = strip_tags($content);
		echo ($content);
	}
endif;
/* Excerpt title row */
if (!function_exists('wpqa_excerpt_title_row')) :
	function wpqa_excerpt_title_row($excerpt_length,$title) {
		$words = explode(' ',$title,$excerpt_length + 1);
		if (count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words,'');
			$title = implode(' ',$words).'...';
		endif;
			$title = strip_tags($title);
		echo ($title);
	}
endif;
/* Excerpts */
if (!defined("wpqa_excerpt_type")) {
	define("wpqa_excerpt_type",wpqa_options("excerpt_type"));
}
if (!function_exists('wpqa_excerpt_title')) :
	function wpqa_excerpt_title($excerpt_length,$excerpt_type = wpqa_excerpt_type,$return = "") {
		global $post;
		$title = "";
		$excerpt_length = (int)((isset($excerpt_length) && $excerpt_length != "") || $excerpt_length == 0?$excerpt_length:5);
		if ($excerpt_length > 0) {
			$title = $post->post_title;
		}
		if ($excerpt_type == "characters") {
			$title = mb_substr($title,0,$excerpt_length,"UTF-8");
		}else {
			$words = explode(' ',$title,$excerpt_length + 1);
			if (count($words) > $excerpt_length) :
				array_pop($words);
				array_push($words,'');
				$title = implode(' ',$words).'...';
			endif;
		}
		$title = strip_tags($title);
		if ($return == "return") {
			return esc_html($title);
		}else {
			echo esc_html($title);
		}
	}
endif;
if (!function_exists('wpqa_excerpt')) :
	function wpqa_excerpt($excerpt_length,$excerpt_type = wpqa_excerpt_type,$read_more = false,$return = "",$main_content = "",$content = "") {
		global $post;
		$excerpt_length = (isset($excerpt_length) && $excerpt_length != ""?$excerpt_length:5);
		if ($main_content == "yes") {
			$content = strip_shortcodes($content);
		}else {
			$get_the_excerpt = trim(get_the_excerpt($post->ID));
			$content = ($get_the_excerpt != "" && $post->post_type != wpqa_questions_type && $post->post_type != wpqa_asked_questions_type && $post->post_type != wpqa_knowledgebase_type?$get_the_excerpt:$post->post_content);
			$content = apply_filters('the_content',strip_shortcodes($content));
		}
		$content = apply_filters("wpqa_excerpt_filter",$content,(isset($post->post_content)?$post->post_content:""));
		if ($excerpt_type == "characters") {
			$number = mb_strlen(trim(strip_tags(trim($content))));
			$content = mb_substr($content,0,$excerpt_length,"UTF-8").($excerpt_length > 0 && $number > 0 && $number > $excerpt_length?' ...':'');
			if ($excerpt_length > 0 && $read_more == true) {
				$read_more_yes = "on";
			}
		}else {
			$words = explode(' ',$content,$excerpt_length + 1);
			if (count($words) > $excerpt_length) :
				array_pop($words);
				array_push($words,'');
				$content = implode(' ',$words).($excerpt_length > 0?'...':'');
				if ($excerpt_length > 0 && $read_more == true) {
					$read_more_yes = "on";
				}
			endif;
		}
		$excerpt = strip_tags($content).(isset($read_more_yes) && $read_more_yes == "on"?'<a class="post-read-more custom-post-link" href="'.esc_url(get_permalink($post->ID)).'" rel="bookmark" title="'.esc_attr__('Read','wpqa').' '.get_the_title($post->ID).'">'.esc_html__('Read more','wpqa').'</a>':'');
		if ($return == "return") {
			return $excerpt;
		}else {
			echo stripcslashes($excerpt);
		}
	}
endif;
if (!function_exists('wpqa_excerpt_any')) :
	function wpqa_excerpt_any($excerpt_length,$content,$more = '...',$excerpt_type = wpqa_excerpt_type) {
		$excerpt_length = (isset($excerpt_length) && $excerpt_length != ""?$excerpt_length:5);
		$content = strip_tags($content);
		if ($excerpt_type == "characters") {
			$content = mb_substr($content,0,$excerpt_length,"UTF-8");
		}else {
			$words = explode(' ',$content,$excerpt_length + 1);
			if (count(explode(' ',$content)) > $excerpt_length) {
				array_pop($words);
				array_push($words,'');
				$content = implode(' ',$words);
				$content = $content.$more;
			}
		}
		return $content;
	}
endif;
/* Admin menus */
if (!function_exists('wpqa_add_admin_page_menu')) :
	function wpqa_add_admin_page_menu() {
		if (is_super_admin(get_current_user_id())) {
			$user_review = wpqa_options("user_review");
			$confirm_email = wpqa_options("confirm_email");
			$subscriptions_payment = wpqa_options("subscriptions_payment");
			if ($user_review == "on") {
				add_users_page(esc_html__('Under review','wpqa'),esc_html__('Under review','wpqa'),'read','users.php?role=wpqa_under_review');
			}
			if ($confirm_email == "on") {
				add_users_page(esc_html__('Activation users','wpqa'),esc_html__('Activation users','wpqa'),'read','users.php?role=activation');
			}
			if ($subscriptions_payment == "on") {
				add_users_page(esc_html__('Subscription users','wpqa'),esc_html__('Subscription users','wpqa'),'read','users.php?role='.wpqa_options("subscriptions_group"));
			}
		}
	}
endif;
add_action('admin_menu','wpqa_add_admin_page_menu');
/* Before delete user */
add_action('delete_user','wpqa_before_delete_user');
if (!function_exists('wpqa_before_delete_user')) :
	function wpqa_before_delete_user($user_id) {
		update_user_meta($user_id,"password_changed","changed");
		
		$active_points = wpqa_options("active_points");
		$point_following_me = wpqa_options("point_following_me");
		
		$following_me = get_user_meta($user_id,"following_me",true);
		if (isset($following_me) && is_array($following_me)) {
			foreach ($following_me as $key => $value) {
				$following_me = get_user_meta($value,"following_me",true);
				$remove_following_me = wpqa_remove_item_by_value($following_me,$user_id);
				update_user_meta($value,"following_me",$remove_following_me);
				if ($active_points == "on" && $point_following_me > 0) {
					wpqa_add_points($value,$point_following_me,"-","delete_follow_user");
				}
				
				$following_you = get_user_meta($value,"following_you",true);
				$remove_following_you = wpqa_remove_item_by_value($following_you,$user_id);
				update_user_meta($value,"following_you",$remove_following_you);
			}
		}
	}
endif;
/* Action delete post */
if (!function_exists('wpqa_delete_post_nonce')) :
	function wpqa_delete_post_nonce() {
		$wpqa_delete_nonce = (isset($_GET["wpqa_delete_nonce"])?esc_html($_GET["wpqa_delete_nonce"]):"");
		if (wp_verify_nonce($wpqa_delete_nonce,'wpqa_delete_nonce')) {
			wpqa_delete_post($_GET);
		}
	}
endif;
add_action('wpqa_init','wpqa_delete_post_nonce');
if (!function_exists('wpqa_delete_post')) :
	function wpqa_delete_post($data = array(),$post_id = 0) {
		if (isset($data["mobile"]) || (!is_admin() && isset($data["delete"]) && $data["delete"] != "")) {
			$post_id  = (int)(isset($data["delete"])?$data["delete"]:(isset($data['id'])?$data['id']:""));
			$get_post = get_post($post_id);
			if (isset($get_post->ID)) {
				$post_type = $get_post->post_type;
				$post_author  = $get_post->post_author;
				$post_status  = $get_post->post_status;
				$filter_post_type = apply_filters("wpqa_filter_delete_post_type",false,$post_type);
				$filter_delete_post = apply_filters("wpqa_filter_delete_post",true,$post_id);
				if ($filter_delete_post == true && $post_id > 0 && (($post_type != "posts" && $post_status == "publish") || $post_type == "posts") && ($post_type == "post" || $post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == wpqa_knowledgebase_type || $post_type == "group" || $post_type == "posts" || $filter_post_type == true)) {
					$user_id      = get_current_user_id();
					$delete_post  = wpqa_options($post_type."_delete");
					$delete_trush = wpqa_options("delete_".$post_type);
					$moderators_permissions = wpqa_user_moderator($user_id);
					$is_super_admin = is_super_admin($user_id);
					if ($post_type == "posts") {
						$group_id = get_post_meta($post_id,"group_id",true);
						$group_moderators = get_post_meta($group_id,"group_moderators",true);
						if (($delete_post == "on" && $post_author == $user_id) || (isset($group_moderators) && $user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators)) || $is_super_admin) {
							$yes_can_delete = true;
						}
					}
					if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
						$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
					}
					if (isset($yes_can_delete) || ($user_id > 0 && ($post_author == $user_id || (isset($anonymously_user) && $anonymously_user == $user_id)) && $delete_post == "on") || (isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete") || $is_super_admin) {
						if ($user_id > 0) {
							wpqa_notifications_activities($user_id,"","","","","delete_".$post_type,"activities","",$post_type);
						}
						do_action("wpqa_after_deleted_post",$get_post);
						if ($delete_trush == "trash" && !$is_super_admin) {
							wp_trash_post($post_id);
						}else {
							wp_delete_post($post_id,true);
						}
						if (!isset($data["mobile"])) {
							wpqa_session('<div class="alert-message success"><i class="icon-check"></i><p>'.esc_html__("Deleted successfully.","wpqa").'</p></div>','wpqa_session');
							$protocol    = is_ssl() ? 'https' : 'http';
							$redirect_to = wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
							$redirect_to = (isset($data["page"]) && esc_html($data["page"]) != ""?esc_html($data["page"]):$redirect_to);
							$redirect_to = ((isset($data["page"]) && esc_html($data["page"]) != "") || is_page()?site_url("/").$redirect_to:esc_url(home_url('/')));
							if ($post_type == "posts") {
								if (isset($group_id) && $group_id != "") {
									$get_permalink = get_permalink($group_id);
									if ($get_permalink != "") {
										$redirect_to = $get_permalink;
									}
								}
							}
							wp_redirect($redirect_to);
							exit;
						}
					}
				}
			}
		}
	}
endif;
/* Before delete post */
add_action('before_delete_post','wpqa_before_delete_post');
if (!function_exists('wpqa_before_delete_post')) :
	function wpqa_before_delete_post($post_id) {
		$get_post = get_post($post_id);
		$post_type = $get_post->post_type;
		$post_author = $get_post->post_author;
		$active_points = wpqa_options("active_points");
		$active_points_category = wpqa_options("active_points_category");
		if ($active_points == "on" && $active_points_category == "on") {
			$categories = wp_get_post_terms($post_id,wpqa_question_categories,array('fields' => 'ids'));
			update_post_meta($post_id,"question_category",$categories);
		}
		$remove_best_answer_stats = wpqa_options("remove_best_answer_stats");
		if ($remove_best_answer_stats == "on" && $active_points == "on") {
			$post_approved_before = get_post_meta($post_id,'post_approved_before',true);
			if ($post_approved_before == "yes") {
				if ($post_author > 0) {
					$point_add_post = (int)wpqa_options("point_add_".$post_type);
					if ($point_add_post > 0) {
						wpqa_add_points($post_author,$point_add_post,"-","delete_".$post_type,$post_id);
					}
				}
			}
		}

		if ($post_id != "" && $post_type == "request") {
			$request_new = get_post_meta($post_id,"request_new",true);
			if ($request_new == 1) {
				$new_requests = get_option("new_requests");
				$new_requests--;
				update_option('new_requests',($new_requests < 0?0:$new_requests));
			}
		}
		if ($post_id != "" && $post_type == "statement") {
			$statement_type = get_post_meta($post_id,"statement_type",true);
			if ($statement_type != "refund") {
				$item_price = floatval(get_post_meta($post_id,"payment_item_price",true));
				if ($item_price > 0) {
					$item_currency = get_post_meta($post_id,"payment_item_currency",true);
					wpqa_site_user_money($item_price,"-",$item_currency,($post_author > 0?$post_author:0));
				}
			}
		}
		if ($post_id != "" && $post_type == "message") {
			$message_user_id = get_post_meta($post_id,'message_user_id',true);
			$message_user_array = get_post_meta($post_id,'message_user_array',true);
			$message_new = get_post_meta($post_id,'message_new',true);
			$delete_inbox_message = get_post_meta($post_id,'delete_inbox_message',true);
			$message_not_new = get_post_meta($post_id,'message_not_new_'.$post_author,true);
			$message_not_new = (isset($message_not_new) && $message_not_new != "" && $message_not_new != "no"?$message_not_new:"no");
			if (($message_new == 1 || $message_new == "on" || $message_not_new == "no") && $post_author != $message_user_id && $delete_inbox_message != 1 && (!is_array($message_user_array) || (is_array($message_user_array) && !empty($message_user_array) && !in_array($message_user_id,$message_user_array)))) {
				$count_new_message = (int)get_user_meta($message_user_id,"wpqa_new_messages_count",true);
				$count_new_message--;
				update_user_meta($message_user_id,"wpqa_new_messages_count",($count_new_message <= 0?0:$count_new_message));
			}
		}
		if ($post_id > 0 && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) {
			$args = array(
				"nopaging"   => true,
				"post_type"  => "report",
				'meta_query' => array(
					array(
						'key'     => 'report_post_id',
						'value'   => $post_id,
						'compare' => '=',
					)
				)
			);
			$get_posts = get_posts($args);
			foreach ($get_posts as $report_post) {
				wp_delete_post($report_post->ID,true);
			}
		}
		if ($post_id > 0 && $post_type == "posts") {
			$group_id = (int)get_post_meta($post_id,"group_id",true);
			$group_posts = (int)get_post_meta($group_id,"group_posts",true);
			$group_posts = $group_posts-1;
			update_post_meta($group_id,"group_posts",($group_posts <= 0?0:$group_posts));
			$sticky_posts = get_post_meta($group_id,"sticky_posts",true);
			if (is_array($sticky_posts) && !empty($sticky_posts)) {
				$remove_sticky_posts = wpqa_remove_item_by_value($sticky_posts,$post_id);
				update_post_meta($group_id,"sticky_posts",$remove_sticky_posts);
			}
		}
		if ($post_id != "" && ($post_type == "post" || $post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) {
			$favorites_questions = get_post_meta($post_id,"favorites_questions",true);
			if (isset($favorites_questions) && is_array($favorites_questions) && count($favorites_questions) > 0) {
				foreach ($favorites_questions as $user_id) {
					$favorites_questions_user = get_user_meta($user_id,$user_id."_favorites",true);
					$remove_favorites_questions = wpqa_remove_item_by_value($favorites_questions_user,$post_id);
					update_user_meta($user_id,$user_id."_favorites",$remove_favorites_questions);
				}
			}
			
			$following_questions = get_post_meta($post_id,"following_questions",true);
			$following_questions = (is_array($following_questions) && !empty($following_questions)?get_users(array('fields' => 'ID','include' => $following_questions,'orderby' => 'registered')):array());
			if (isset($following_questions) && is_array($following_questions) && count($following_questions) > 0) {
				foreach ($following_questions as $user_id) {
					$following_questions_user = get_user_meta($user_id,"following_questions",true);
					$remove_following_questions = wpqa_remove_item_by_value($following_questions_user,$post_id);
					update_user_meta($user_id,"following_questions",$remove_following_questions);
				}
			}
		}
		if ($post_type == wpqa_questions_type || $post_type == wpqa_knowledgebase_type || $post_type == "post") {
			$sticky_posts = get_option("sticky_".$post_type."s");
			if (is_array($sticky_posts) && !empty($sticky_posts)) {
				$remove_sticky_posts = wpqa_remove_item_by_value($sticky_posts,$post_id);
				update_option("sticky_".$post_type."s",$remove_sticky_posts);
			}
		}
	}
endif;
/* After deleted post */
add_action("wpqa_after_deleted_post","wpqa_after_deleted_post");
function wpqa_after_deleted_post($post) {
	$post_type = $post->post_type;
	$post_author = $post->post_author;
	wpqa_count_posts($post_type,$post_author,"-");
}
/* Count posts */
function wpqa_count_posts($post_type,$post_author,$type) {
	if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == wpqa_knowledgebase_type || $post_type == "group" || $post_type == "posts" || $post_type == "post" || $post_type == "notification" || $post_type == "activity") {
		if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
			$meta = "wpqa_questions_count";
		}else if ($post_type == wpqa_knowledgebase_type) {
			$meta = "wpqa_knowledgebases_count";
		}else if ($post_type == "group") {
			$meta = "wpqa_groups_count";
		}else if ($post_type == "posts") {
			$meta = "wpqa_group_posts_count";
		}else if ($post_type == "notification") {
			$meta = "wpqa_notification_count";
		}else if ($post_type == "activity") {
			$meta = "wpqa_activity_count";
		}else {
			$meta = "wpqa_posts_count";
		}
		$count_meta = (int)get_user_meta($post_author,$meta,true);
		if ($type == "-") {
			$count_meta--;
		}else {
			$count_meta++;
		}
		update_user_meta($post_author,$meta,($count_meta < 0?0:$count_meta));
	}
}
/* Transition the post status */
add_action('transition_post_status','wpqa_run_on_update_post',10,3);
if (!function_exists('wpqa_run_on_update_post')) :
	function wpqa_run_on_update_post($new_status,$old_status,$post) {
		$post_type = $post->post_type;
		$post_id = $post->ID;
		$post_author = $post->post_author;
		if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == wpqa_knowledgebase_type || $post_type == "group" || $post_type == "posts" || $post_type == "post" || $post_type == "notification" || $post_type == "activity") {
			if ($new_status != "publish" && $old_status == "publish") {
				wpqa_count_posts($post_type,$post_author,"-");
			}
			if ($new_status == "publish" && $old_status != "publish" && $old_status != "new") {
				wpqa_count_posts($post_type,$post_author,"+");
			}
		}
		if (is_admin()) {
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == wpqa_knowledgebase_type || $post_type == "group" || $post_type == "posts" || $post_type == "post" || $post_type == "message") {
				$post_from_front = get_post_meta($post_id,'post_from_front',true);
				if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
					$user_id = get_post_meta($post_id,"user_id",true);
					$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
					$question_username = get_post_meta($post_id,'question_username',true);
					$question_email = get_post_meta($post_id,'question_email',true);
					if ($question_username == "") {
						$question_no_username = get_post_meta($post_id,'question_no_username',true);
					}
				}
				if ($post_type == "post") {
					$post_username = get_post_meta($post_id,'post_username',true);
					$post_email = get_post_meta($post_id,'post_email',true);
				}
				if ($post_type == "message") {
					$message_username = get_post_meta($post_id,'message_username',true);
					$message_email = get_post_meta($post_id,'message_email',true);
				}
				
				if ((isset($anonymously_user) && $anonymously_user > 0) || (isset($question_no_username) && $question_no_username == "no_user") || (isset($question_username) && $question_username != "" && isset($question_email) && $question_email != "") || (isset($post_username) && $post_username != "" && isset($post_email) && $post_email != "") || (isset($message_username) && $message_username != "" && isset($message_email) && $message_email != "")) {
					$not_user = 0;
				}else {
					$not_user = $post_author;
				}
				
				$post_approved_before = get_post_meta($post_id,'post_approved_before',true);
				if ($post_approved_before != "yes") {
					if ('publish' == $new_status && $post_type == "group") {
						do_action("wpqa_after_added_group",$post_id,$post_author);
					}else if ('publish' == $new_status && $post_type == "message") {
						$get_message_user = get_post_meta($post_id,'message_user_id',true);
						wpqa_notification_send_message($post,$post_author,$get_message_user);
						if ($not_user > 0) {
							wpqa_notifications_activities($not_user,"","","","","approved_message","notifications");
						}
					}
					if ($post_id > 0 && 'publish' == $new_status && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == wpqa_knowledgebase_type || $post_type == "group" || $post_type == "posts" || $post_type == "post" || $post_type == "message")) {
						$user_get_current_user_id = get_current_user_id();
						if (($not_user > 0 && $not_user != $user_get_current_user_id && $user_get_current_user_id > 0) || (isset($anonymously_user) && $anonymously_user > 0 && $anonymously_user != $user_get_current_user_id > 0)) {
							if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
								update_post_meta($post_id,"question_vote",0);
								wpqa_notifications_activities(($anonymously_user > 0?$anonymously_user:$not_user),"","",$post_id,"","approved_question","notifications","",wpqa_questions_type);
								if ($post_author != $user_id && $user_id > 0) {
									wpqa_notifications_activities($user_id,($anonymously_user > 0?0:$not_user),"",$post_id,"","add_question_user","notifications","",wpqa_questions_type);
								}
							}else if ($not_user > 0) {
								wpqa_notifications_activities($not_user,"","",$post_id,"","approved_".$post_type,"notifications");
							}
						}
						
						if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
							wpqa_notifications_ask_question($post_id,$question_username,$user_id,$not_user,$anonymously_user,$user_get_current_user_id);
						}

						if ($post_type == wpqa_knowledgebase_type) {
							update_post_meta($post_id,"wpqa_rate_up",0);
						}

						if ($post_type == "post") {
							wpqa_notifications_add_post($post_id,$post_username,$not_user,$user_get_current_user_id);
						}
						
						if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == wpqa_knowledgebase_type || $post_type == "group" || $post_type == "posts" || $post_type == "post") {
							wpqa_post_publish($post,$not_user,"admin");
						}
						update_post_meta($post_id,'post_approved_before','yes');
						if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == "post") {
							$way_sending_notifications = wpqa_options("way_sending_notifications_".$post_type."s");
							$schedules_time_notification = wpqa_options("schedules_time_notification_".$post_type."s");
							if ($way_sending_notifications == "cronjob" && $schedules_time_notification != "") {
								update_post_meta($post_id,'wpqa_post_scheduled_email','yes');
								update_post_meta($post_id,'wpqa_post_scheduled_notification','yes');
							}
						}
					}
					do_action("wpqa_update_post",$post);
				}
			}
		}
	}
endif;
/* Count option */
function wpqa_count_option($name) {
	$name_count = (int)get_option($name);
	$name_count++;
	update_option($name,$name_count);
}
/* Get comment */
if (!function_exists('wpqa_comment')) :
	function wpqa_comment($comment,$args,$depth,$answer = "",$owner = "",$k_ad = "",$best_answer = "",$answer_args = array()) {
		if ($answer != "answer") {
			if ($k_ad == "") {
				global $k_ad;
			}
			$between_comments_adv_type_replies = wpqa_options("between_comments_adv_type_replies");
			if (($between_comments_adv_type_replies != "on" && $comment->comment_parent == 0) || $between_comments_adv_type_replies == "on") {
				$k_ad++;
			}
		}
		$format_date_ago = wpqa_options("format_date_ago");
		$format_date_ago_types = wpqa_options("format_date_ago_types");
		$show_replies = wpqa_options("show_replies");
		$user_get_current_user_id = get_current_user_id();
		$is_super_admin = is_super_admin($user_get_current_user_id);
		$comment_id = (int)$comment->comment_ID;
		$comment_user_id = (int)$comment->user_id;
		$post_id = (int)$comment->comment_post_ID;
		$post_data = get_post($post_id);
		$post_type = (isset($post_data->post_type)?$post_data->post_type:"");
		$comment_link = get_comment_link($comment_id);
		if ($post_type == "posts") {
			$comment_link = wpqa_custom_permalink($post_id,"view_posts_group","view_group_post").'#comment-'.esc_html($comment_id);
		}
		$user = get_user_by('id',$comment_user_id);
		$deleted_user = ($comment_user_id > 0 && isset($user->display_name)?$user->display_name:($comment_user_id == 0?$comment->comment_author:"delete"));
		$like_answer = apply_filters("wpqa_comment_like_answer",false,$post_type);
		$activate_male_female = apply_filters("wpqa_activate_male_female",false);
		$yes_private_answer = 1;
		if ($like_answer == true || ($answer != "answer" && isset($post_data->post_type) && ($post_data->post_type == wpqa_questions_type || $post_data->post_type == wpqa_asked_questions_type)) || ($answer == "answer" && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type))) {
			$yes_private_answer = wpqa_private_answer($comment_id,$comment_user_id,$user_get_current_user_id,$post_data->post_author);
			$the_best_answer = get_post_meta(($answer == "answer"?$post_id:(isset($post_data->ID)?$post_data->ID:0)),"the_best_answer",true);
			$best_answer_comment = get_comment_meta($comment_id,"best_answer_comment",true);
			$comment_class = ($best_answer_comment == "best_answer_comment" || $the_best_answer == $comment_id?"comment-best-answer":"");
			$comment_class = ($comment_class != ""?$comment_class." ":$comment_class).($activate_male_female == true?"activate-gender":"not-activate-gender");
			$_paid_answer = get_comment_meta($comment_id,'_paid_answer',true);
			$comment_class = ($comment_class != ""?$comment_class." ":$comment_class).($_paid_answer == 'paid'?'comment-paid-answer':'');
			$active_reports = wpqa_options("active_reports");
			$active_logged_reports = wpqa_options("active_logged_reports");
			$active_vote = wpqa_options("active_vote");
			$active_vote_unlogged = wpqa_options("active_vote_unlogged");
			$active_best_answer = wpqa_options("active_best_answer");
		}
		
		if ($like_answer == true || ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) {
			$comment_vote = get_comment_meta($comment_id,'comment_vote',true);
			if (isset($comment_vote) && is_array($comment_vote) && isset($comment_vote["vote"])) {
				update_comment_meta($comment_id,'comment_vote',$comment_vote["vote"]);
				$comment_vote = get_comment_meta($comment_id,'comment_vote',true);
			}else if ($comment_vote == "") {
				update_comment_meta($comment_id,'comment_vote',0);
				$comment_vote = get_comment_meta($comment_id,'comment_vote',true);
			}
		}
		$can_delete_comment = wpqa_options("can_delete_comment");
		$can_edit_comment = wpqa_options("can_edit_comment");
		$can_edit_comment_after = (int)wpqa_options("can_edit_comment_after");
		$can_edit_comment_after = (isset($can_edit_comment_after) && $can_edit_comment_after > 0?$can_edit_comment_after:0);
		if (version_compare(phpversion(), '5.3.0', '>')) {
			$time_now = strtotime(current_time('mysql'),date_create_from_format('Y-m-d H:i',current_time('mysql')));
		}else {
			list($year, $month, $day, $hour, $minute, $second) = sscanf(current_time('mysql'),'%04d-%02d-%02d %02d:%02d:%02d');
			$datetime = new DateTime("$year-$month-$day $hour:$minute:$second");
			$time_now = strtotime($datetime->format('r'));
		}
		$time_edit_comment = strtotime('+'.$can_edit_comment_after.' hour',strtotime($comment->comment_date));
		$time_end = ($time_now-$time_edit_comment)/60/60;
		$edit_comment = get_comment_meta($comment_id,"edit_comment",true);
		if (!isset($answer_args["inner_question"])) {
			$between_comments_position = (int)wpqa_options("between_comments_position");
			$adv_type_repeat = wpqa_options("between_comments_adv_type_repeat");
			$count_adv = ($between_comments_position > 0 && isset($k_ad) && $k_ad > 0?$k_ad % $between_comments_position:0);
			if (isset($k_ad) && (($k_ad == $between_comments_position) || ($adv_type_repeat == "on" && $k_ad != 0 && $count_adv == 0))) {
				echo wpqa_ads("between_comments_adv_type","between_comments_adv_link","between_comments_adv_code","between_comments_adv_href","between_comments_adv_img","li","","","on");
			}
		}
		if ($answer == "answer") {
			$k_ad++;
		}
		$answer_question_style = wpqa_options("answer_question_style");
		$profile_credential = ($comment_user_id > 0?get_the_author_meta('profile_credential',$comment_user_id):"");
		$privacy_credential = ($comment_user_id > 0?wpqa_check_user_privacy($comment_user_id,"credential"):"");
		if (isset($answer_args['custom_home_answer']) && $answer_args['custom_home_answer'] == "on") {
			$answer_image         = get_post_meta($answer_args['answer_question_id'],prefix_meta.'answers_image_h',true);
			$active_vote_answer   = get_post_meta($answer_args['answer_question_id'],prefix_meta.'active_vote_answer_h',true);
			$show_dislike_answers = get_post_meta($answer_args['answer_question_id'],prefix_meta.'show_dislike_answers_h',true);
		}else if (isset($answer_args['custom_answers']) && $answer_args['custom_answers'] == "on") {
			$answer_image         = get_post_meta($answer_args['answer_question_id'],prefix_meta.'answers_image_a',true);
			$active_vote_answer   = get_post_meta($answer_args['answer_question_id'],prefix_meta.'active_vote_answer_a',true);
			$show_dislike_answers = get_post_meta($answer_args['answer_question_id'],prefix_meta.'show_dislike_answers_a',true);
		}else {
			$answer_image         = wpqa_options("answer_image");
			$active_vote_answer   = wpqa_options("active_vote_answer");
			$show_dislike_answers = wpqa_options("show_dislike_answers");
		}
		if ($yes_private_answer != 1) {?>
			<li class="comment byuser">
				<div class="comment-body clearfix">
					<div class="comment-text"><?php esc_html_e("Sorry, this is a private answer.","wpqa");?></div>
				</div>
		<?php }else {
			$get_gender_class = wpqa_get_gender_class($comment_user_id,0,$comment_id);
			if ($activate_male_female == true) {
				$info_edited = '';
				$gender_author = ($comment_user_id > 0?get_user_meta($comment_user_id,'gender',true):"");
				$gender_comment = get_comment_meta($comment_id,'wpqa_comment_gender',true);
				if ($gender_comment == "" && $gender_author != "") {
					update_comment_meta($comment_id,'wpqa_comment_gender',$gender_author);
				}
				if ($gender_author != "" && $gender_author != $gender_comment) {
					$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this comment has been added","wpqa");
					if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
						$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this answer has been added","wpqa");
					}
				}
			}?>
			<li <?php comment_class(($answer_image == "on"?"":"comment-without-image ").($show_replies == "on"?"comment-show-replies ":"").(isset($answer_args["comment_read_more"])?"comment-read-more ":"").(isset($answer_args["comment_with_title"])?"comment-with-title ".($answer_question_style != ""?"comment-with-title-".str_replace('style_','',$answer_question_style)." ":""):"").($privacy_credential == true && $profile_credential != ""?"comment-credential ":"").($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?$comment_class." ":"").($comment->comment_type == "pingback"?"comment pingback":"comment")." ".$get_gender_class,$comment_id,$post_id);echo (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && is_single()?' itemscope itemtype="https://schema.org/'.($comment->comment_parent > 0?"Comment":"Answer").'"'.($comment->comment_parent == 0?' itemprop="'.($best_answer_comment == 'best_answer_comment' || $the_best_answer == $comment_id?'acceptedAnswer':'suggestedAnswer').'"':''):'');?> id="li-comment-<?php echo esc_attr($comment_id);?>">
				<div id="comment-<?php echo esc_attr($comment_id);?>" class="comment-body clearfix">
					<?php if (isset($answer_args["comment_with_title"])) {
						echo '<div class="comment-question-title"><header class="article-header"><div class="question-header"><div class="post-meta">';
							wpqa_theme_meta("on","on","","","","",$post_id,$post_data);
						echo '</div></div></header>
						<div class="clearfix"></div>
						<h2 class="post-title"><a class="post-title" href="' . esc_url( get_permalink($post_id) ) . '" rel="bookmark">'.get_the_title($post_id).'</a></h2></div>';
					}?>
					<div class="comment-text">
						<div class="d-flex align-items-center header-of-comment">
							<?php if ($answer_image == "on") {
								do_action("wpqa_action_avatar_link",array("user_id" => ($comment_user_id > 0 && $deleted_user != "delete"?$comment_user_id:0),"size" => apply_filters("wpqa_comment_profile_size","42"),"span" => "span","class" => "rounded-circle","pop" => "pop","comment" => $comment,"email" => ($comment_user_id > 0?"":$comment->comment_author_email)));
							}?>
							<div class="author clearfix">
								<?php if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
									if ($best_answer == "" && ($best_answer_comment == "best_answer_comment" || $the_best_answer == $comment_id)) {?>
										<div class="best-answer"><?php esc_html_e("Best Answer","wpqa")?></div>
									<?php }
									if (isset($_paid_answer) && $_paid_answer == 'paid') {?>
										<div class="best-answer paid-answer"><?php esc_html_e("Paid Answer","wpqa")?></div>
									<?php }
								}?>
								<div class="comment-meta">
									<div class="comment-author">
										<?php $wpqa_activate_comment_author = apply_filters('wpqa_activate_comment_author',true,$comment_id);
										if ($wpqa_activate_comment_author == true) {
											if ($comment_user_id > 0 && $deleted_user != "delete") {
												$wpqa_profile_url = wpqa_profile_url($comment_user_id);
											}else {
												$wpqa_profile_url = ($comment->comment_author_url != "" && $deleted_user != "delete"?$comment->comment_author_url:"wpqa_No_site");
											}
											if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && is_single()) {
												echo '<span itemprop="author" itemscope itemtype="http://schema.org/Person">';
											}
											if ($wpqa_profile_url != "" && $wpqa_profile_url != "wpqa_No_site") {?>
												<a<?php echo (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && is_single()?' itemprop="url"':'')?> href="<?php echo esc_url($wpqa_profile_url)?>">
											<?php }
												if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && is_single()) {
													echo '<span itemprop="name">';
												}
												$anonymously_user = get_comment_meta($comment_id,"anonymously_user",true);
												echo ($deleted_user == "delete"?esc_html__("[Deleted User]","wpqa"):($anonymously_user != ""?esc_html__("Anonymous","wpqa"):$deleted_user));
												if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
													echo '</span>';
												}
											if ($wpqa_profile_url != "" && $wpqa_profile_url != "wpqa_No_site") {?>
												</a>
											<?php }
											if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
												echo '</span>';
											}
										}
										if ($comment_user_id > 0 && $deleted_user != "delete") {
											do_action("wpqa_verified_user",$comment_user_id);
											$active_points_category = wpqa_options("active_points_category");
											if ($active_points_category == "on") {
												$get_terms = wp_get_post_terms($post_id,wpqa_question_categories,array('fields' => 'ids'));
												if (!empty($get_terms) && is_array($get_terms) && isset($get_terms[0])) {
													$points_category_user = (int)get_user_meta($comment_user_id,"points_category".$get_terms[0],true);
													echo apply_filters("wpqa_comments_before_badge",false,$get_terms[0]);
												}
											}
											do_action("wpqa_get_badge",$comment_user_id,"",(isset($points_category_user)?$points_category_user:""));
											do_action("wpqa_action_comment_after_badge",$comment,$post_type);
										}
										if ($privacy_credential == true && $profile_credential != "") {?>
											<span class="profile-credential"><?php echo esc_html($profile_credential)?></span>
										<?php }
										do_action("wpqa_action_after_credential",(isset($post_data->ID)?$post_data->ID:0),$comment_id,$comment_user_id);?>
									</div>
									<?php $show_date = apply_filters("wpqa_filter_comment_show_date",true,$comment,$post_type);
									if ($show_date == true) {
										$date_format = wpqa_options("date_format");
										$date_format = ($date_format?$date_format:get_option("date_format"));?>
										<a href="<?php echo esc_url($comment_link)?>" class="comment-date"<?php echo($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?" itemprop='url'":"")?>>
											<?php $get_comment_date = get_comment_date("c",$comment_id);
											echo (is_single()?'<span class="wpqa_hide" itemprop="dateCreated" datetime="'.$get_comment_date.'">'.$get_comment_date.'</span>':'');
											if ($format_date_ago == "on" && (($post_type == "post" && isset($format_date_ago_types["comments"]) && $format_date_ago_types["comments"] == "comments") || ((($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) || ($answer == "answer")) && isset($format_date_ago_types["answers"]) && $format_date_ago_types["answers"] == "answers") || ($post_type == "posts" && isset($format_date_ago_types["group_comments"]) && $format_date_ago_types["group_comments"] == "group_comments"))) {
												$human_time_diff = human_time_diff(strtotime(wpqa_get_comment_time("d-m-Y g:i a",false,false,$comment)),current_time('timestamp'))." ".esc_html__("ago","wpqa");
											}
											if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) || ($answer == "answer")) {
												echo ($comment->comment_parent > 0?esc_html__("Replied to","wpqa"):esc_html__("Added an","wpqa"))." ";
												if (isset($human_time_diff)) {
													printf(esc_html__('answer about %s','wpqa'),$human_time_diff);
												}else {
													printf(esc_html__('answer on %1$s at %2$s','wpqa'),get_comment_date($date_format,$comment_id),wpqa_get_comment_time(false,false,false,$comment));
												}
											}else {
												echo ($comment->comment_parent > 0?esc_html__("Replied to","wpqa"):esc_html__("Added a","wpqa"))." ";
												if (isset($human_time_diff)) {
													printf(esc_html__('comment about %s','wpqa'),$human_time_diff);
												}else {
													printf(esc_html__('comment on %1$s at %2$s','wpqa'),get_comment_date($date_format,$comment_id),wpqa_get_comment_time(false,false,false,$comment));
												}
											}?>
										</a>
									<?php }else {
										do_action("wpqa_action_comment_show_date",$comment,$post_type);
									}?>
								</div><!-- End comment-meta -->
							</div><!-- End author -->
						</div><!-- End header-of-comment -->
						<div class="text">
							<?php if ($edit_comment == "edited") {?>
								<em class="comment-edited">
									<?php if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
										esc_html_e('This answer was edited.','wpqa');
									}else {
										esc_html_e('This comment was edited.','wpqa');
									}?>
								</em>
							<?php }
							if ($comment->comment_approved == '0') : ?>
								<em class="comment-awaiting">
									<?php if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
										esc_html_e('Your answer is awaiting moderation.','wpqa');
									}else {
										esc_html_e('Your comment is awaiting moderation.','wpqa');
									}?>
								</em><br>
							<?php endif;
							
							$featured_image_answers_lightbox = wpqa_options("featured_image_answers_lightbox");
							$featured_image_answer_width = wpqa_options("featured_image_answer_width");
							$featured_image_answer_height = wpqa_options("featured_image_answer_height");
							if ($post_type == "posts") {
								$featured_image_in_answers = wpqa_options("featured_image_group_post_comments");
								$featured_image_answers_lightbox = wpqa_options("featured_image_group_post_comments_lightbox");
								$featured_image_answer_width = wpqa_options("featured_image_group_post_comments_width");
								$featured_image_answer_height = wpqa_options("featured_image_group_post_comments_height");
								$featured_answer_position = "before";
							}else if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
								$featured_image_in_answers = wpqa_options("featured_image_question_answers");
							}else {
								$featured_image_in_answers = wpqa_options("featured_image_in_answers");
							}
							if ($featured_image_in_answers == "on") {
								$featured_image = get_comment_meta($comment_id,'featured_image',true);
								if ($featured_image != "") {
									$img_url = wp_get_attachment_url($featured_image,"full");
									if ($img_url != "") {
										$featured_image_answer_width = ($featured_image_answer_width != ""?$featured_image_answer_width:260);
										$featured_image_answer_height = ($featured_image_answer_height != ""?$featured_image_answer_height:185);
										$link_url = ($featured_image_answers_lightbox == "on"?$img_url:$comment_link);
										$last_image = wpqa_get_aq_resize_img($featured_image_answer_width,$featured_image_answer_height,"",$featured_image);
										$featured_answer_position = wpqa_options("featured_answer_position");
										if ($featured_answer_position != "after" && isset($last_image) && $last_image != "") {
											echo "<div class='featured_image_answer'><a href='".$link_url."'>".$last_image."</a></div>
											<div class='clearfix'></div>";
										}
									}
								}
							}

							$answer_video = wpqa_options("answer_video");
							$video_answer_position = wpqa_options("video_answer_position");
							$video_answer_width = wpqa_options("video_answer_width");
							$video_answer_100 = wpqa_options("video_answer_100");
							$video_answer_height = wpqa_options("video_answer_height");
							$video_answer_description = get_comment_meta($comment_id,"video_answer_description",true);
							if ($answer_video == "on" && $video_answer_description == "on") {
								$video_answer_type = get_comment_meta($comment_id,"video_answer_type",true);
								$video_answer_id = get_comment_meta($comment_id,"video_answer_id",true);
								if ($video_answer_id != "") {
									$type = wpqa_video_iframe($video_answer_type,$video_answer_id,"comment_meta","video_answer_id",$comment_id);
									$las_video = '<div class="question-video-loop answer-video video-type-'.$video_answer_type.($video_answer_100 == "on"?' question-video-loop-100':'').($video_answer_position == "after"?' question-video-loop-after':'').'"><iframe frameborder="0" allowfullscreen width="'.$video_answer_width.'" height="'.$video_answer_height.'" src="'.$type.'"></iframe></div>';
									
									if ($video_answer_position == "before" && $answer_video == "on" && isset($video_answer_id) && $video_answer_id != "" && $video_answer_description == "on") {
										echo stripcslashes($las_video);
									}
								}
							}?>

							<div<?php echo (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && is_single()?" itemprop='text'":"")?>>
								<?php if (isset($answer_args["comment_with_title"]) || isset($answer_args["comment_read_more"]) || isset($args["comment_read_more"])) {
									$comment_excerpt_count = apply_filters('wpqa_answer_number',300);
									$strlen_comment = strlen(wp_html_excerpt($comment->comment_content,$comment_excerpt_count+10));
									echo '<p class="less_answer_text'.($strlen_comment < $comment_excerpt_count?" wpqa_hide":"").'">'.wp_html_excerpt($comment->comment_content,$comment_excerpt_count,'<a class="post-read-more comment-read-more read_more_answer custom-post-link" href="'.$comment_link.'" rel="bookmark" title="'.esc_attr__('Read more','wpqa').'">'.esc_html__('Read more','wpqa').'</a>').'</p>
									<div class="full_answer_text'.($strlen_comment < $comment_excerpt_count?"":" wpqa_hide").'">';
										comment_text($comment_id);
										do_action("wpqa_action_comment_after_comment_text",$comment,$post_type);
										echo '<a class="read_less_answer custom-post-link'.($strlen_comment < $comment_excerpt_count?" wpqa_hide":"").'" href="#">'.esc_html__("See less","wpqa").'</a>
									</div>';
								}else {
									comment_text($comment_id);
									do_action("wpqa_action_comment_after_comment_text",$comment,$post_type);
								}?>
							</div>
							<div class="clearfix"></div>
							<?php if ($video_answer_position == "after" && $answer_video == "on" && isset($video_answer_id) && $video_answer_id != "" && $video_answer_description == "on") {
								echo stripcslashes($las_video);
							}?>
							<div class="clearfix"></div>
							<?php if ($featured_image_in_answers == "on" && isset($featured_answer_position) && $featured_answer_position == "after" && isset($img_url) && $img_url != "" && isset($last_image) && $last_image != "") {
								echo "<div class='featured_image_answer featured_image_after'><a href='".$link_url."'>".$last_image."</a></div>
								<div class='clearfix'></div>";
							}
							
							if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
								$added_file = get_comment_meta($comment_id,'added_file', true);
								if ($added_file != "") {
									echo "<a class='attachment-link btn btn__primary btn__sm' href='".wp_get_attachment_url($added_file)."'><i class='icon-link'></i>".esc_html__("Attachment","wpqa")."</a><div class='clearfix'></div><br>";
								}
							}?>
							<div class="wpqa_error"></div>
							<div class="comment-footer-bottom">
								<?php if ($post_type == "posts" || ($like_answer == true || ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type))) {
									$active_reaction = wpqa_options("active_reaction");
									$active_reaction_answers = wpqa_options("active_reaction_answers");
									$active_reaction_group_comments = wpqa_options("active_reaction_group_comments");
									if ($active_reaction == "on" && ((($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && $active_reaction_answers == "on") || ($post_type == "posts" && $active_reaction_group_comments == "on"))) {
										do_action("wpqa_show_reactions","answers",$post_id,$comment_id);
									}
									if (($like_answer == true || ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) && $active_vote == "on" && $active_vote_answer == "on" && !$owner) {
										do_action("wpqa_answer_vote",$post_data->post_type,$user_get_current_user_id,$comment_user_id,$comment_id,$comment_vote,$show_dislike_answers);
									}
								}?>
								<ul class="comment-reply comment-reply-main">
									<?php if (($answer != "answer" && $answer != "comment") || (isset($args["comment_type"]) && $args["comment_type"] == "comment_group")) {
										if ($answer != "posts") {
											$custom_permission = wpqa_options("custom_permission");
											$add_answer = wpqa_options("add_answer");
											$add_comment = wpqa_options("add_comment");
											if (is_user_logged_in()) {
												$user_is_login = get_userdata($user_get_current_user_id);
												$roles = $user_is_login->allcaps;
											}
										}
										if ((isset($args["comment_type"]) && $args["comment_type"] == "comment_group") || (($post_type != wpqa_questions_type && $post_type != wpqa_asked_questions_type && ($is_super_admin || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["add_comment"]) && $roles["add_comment"] == 1) || (!is_user_logged_in() && $add_comment == "on"))) || (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && ($is_super_admin || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["add_answer"]) && $roles["add_answer"] == 1) || (!is_user_logged_in() && $add_answer == "on"))))) {
											$answer_per_question = wpqa_options("answer_per_question");
											if ($answer_per_question == "on" && !$is_super_admin && $user_get_current_user_id > 0) {
												$answers_question = get_comments(array('post_id' => $post_id,'user_id' => $user_get_current_user_id,'parent' => 0));
											}
											if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && !$is_super_admin && $answer_per_question == "on" && $user_get_current_user_id > 0 && isset($answers_question) && is_array($answers_question) && !empty($answers_question)) {
												$allow_to_answer = true;
											}
											if ((isset($args["comment_type"]) && $args["comment_type"] == "comment_group" && ((is_user_logged_in() && $args["group_comments"] == "on") || $args["allow_group_moderators"] == true || $is_super_admin))) {
												$allow_to_answer = $allow_to_reply = true;
											}
											if ((!isset($allow_to_answer) && $post_type != "posts") || ($post_type == "posts" && isset($allow_to_answer) && isset($allow_to_reply))) {
												comment_reply_link( array_merge( $args, array( 'reply_text' => '<i class="icon-reply"></i>'.esc_html__( 'Reply', 'wpqa' ),'login_text' => '<i class="icon-lock"></i>'.esc_html__( 'Login to Reply', 'wpqa' ), 'before' => '<li>', 'after' => '</li>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ), $comment, $post_data );
											}
										}
									}
									do_action("wpqa_action_after_reply_comment",$comment,($like_answer == true || ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)?"answer":"comment"));
									$group_id = get_post_meta($comment->comment_post_ID,"group_id",true);
									$comment_share  = wpqa_options("comment_share");
									$share_facebook = (isset($comment_share["share_facebook"]["value"])?$comment_share["share_facebook"]["value"]:"");
									$share_twitter  = (isset($comment_share["share_twitter"]["value"])?$comment_share["share_twitter"]["value"]:"");
									$share_linkedin = (isset($comment_share["share_linkedin"]["value"])?$comment_share["share_linkedin"]["value"]:"");
									$share_whatsapp = (isset($comment_share["share_whatsapp"]["value"])?$comment_share["share_whatsapp"]["value"]:"");
									if ($group_id == "" && $share_facebook == "share_facebook" || $share_twitter == "share_twitter" || $share_linkedin == "share_linkedin" || $share_whatsapp == "share_whatsapp") {?>
										<li class="comment-share question-share question-share-2">
											<i class="icon-share"></i>
											<span><?php esc_html_e("Share","wpqa");?></span>
											<?php wpqa_share($comment_share,$share_facebook,$share_twitter,$share_linkedin,$share_whatsapp,"style_2",$comment_id,"","","",$post_id);?>
										</li>
									<?php }
									do_action("wpqa_action_after_share_comment",$comment,($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?"answer":"comment"));
									if (isset($answer_args["show_replies"]) && $answer_args["show_replies"] == true || !isset($answer_args["show_replies"])) {
										do_action("wpqa_action_show_replies",$comment,($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?"answer":"comment"),$post_type);
									}
									if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && $answer != "answer") {
										$user_best_answer_filter = apply_filters("wpqa_user_best_answer_filter",true);
										$best_answer_userself = wpqa_options("best_answer_userself");
										$user_best_answer = esc_html(get_the_author_meta('user_best_answer',$user_get_current_user_id));
										$moderators_permissions = wpqa_user_moderator($user_get_current_user_id);
										$moderator_categories = wpqa_user_moderator_categories($user_get_current_user_id,$post_data->ID);
										if ($user_best_answer_filter == true && ((is_user_logged_in() && $active_best_answer == "on" && $user_get_current_user_id > 0 && (($comment_user_id != $user_get_current_user_id && $user_get_current_user_id == $post_data->post_author) || ($best_answer_userself == "on" && $comment_user_id == $user_get_current_user_id && $user_get_current_user_id == $post_data->post_author))) || $user_best_answer == "on" || ($moderator_categories == true && isset($moderators_permissions['best']) && $moderators_permissions['best'] == "best") || $is_super_admin)) {
											if ($the_best_answer != 0 && ($best_answer_comment == "best_answer_comment" || $the_best_answer == $comment_id)) {
												echo '<li><a class="best_answer_re" data-nonce="'.wp_create_nonce("wpqa_best_answer_nonce").'" title="'.esc_attr__("Cancel the best answer","wpqa").'" href="#"><i class="icon-cancel"></i>'.esc_html__("Cancel the best answer","wpqa").'</a></li>';
											}
											if ($the_best_answer == 0 || $the_best_answer == "") {?>
												<li><a class="best_answer_a" data-nonce="<?php echo wp_create_nonce("wpqa_best_answer_nonce")?>" title="<?php esc_attr_e("Select as best answer","wpqa");?>" href="#"><i class="icon-check"></i><?php esc_html_e("Select as best answer","wpqa");?></a></li>
											<?php }
										}
									}?>
									<li class="clearfix last-item-answers"></li>
								</ul>
								<?php do_action("wpqa_after_answer_links",$comment,$can_edit_comment,$can_edit_comment_after,$time_end,$can_delete_comment,$like_answer,(isset($post_type)?$post_type:false),(isset($active_reports)?$active_reports:false),(isset($active_logged_reports)?$active_logged_reports:false),$owner);
								if (isset($args["comment_type"]) && $args["comment_type"] == "comment_group") {
									$edit_delete_posts_comments = wpqa_options("edit_delete_posts_comments");
									$activate_edit = wpqa_group_edit_comments($post_id,$is_super_admin,$can_edit_comment,$comment_user_id,$user_get_current_user_id,$edit_delete_posts_comments);
									$activate_delete = wpqa_group_delete_comments($post_id,$is_super_admin,$can_delete_comment,$comment_user_id,$user_get_current_user_id,$edit_delete_posts_comments);
								}
								if ((isset($activate_edit) && $activate_edit == true) || (isset($activate_delete) && $activate_delete == true) || ((($can_edit_comment == "on" && $comment_user_id == $user_get_current_user_id && $comment_user_id != 0 && $user_get_current_user_id != 0 && ($can_edit_comment_after == 0 || $time_end <= $can_edit_comment_after))) || (($can_delete_comment == "on" && $comment_user_id == $user_get_current_user_id && $comment_user_id > 0 && $user_get_current_user_id > 0) || $is_super_admin) || (($like_answer == true || ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) && $active_reports == "on" && ((is_user_logged_in() && $comment_user_id != $user_get_current_user_id && $user_get_current_user_id != 0) || (!is_user_logged_in() && $active_logged_reports != "on"))))) {?>
									<ul class="comment-reply comment-list-links">
										<li class="question-list-details comment-list-details">
											<i class="icon-dot-3"></i>
											<ul>
												<?php if ((isset($activate_edit) && $activate_edit == true) || ($is_super_admin || ($can_edit_comment == "on" && $comment_user_id == $user_get_current_user_id && $comment_user_id != 0 && $user_get_current_user_id != 0 && ($can_edit_comment_after == 0 || $time_end <= $can_edit_comment_after)))) {
													echo "<li><a class='comment-edit-link edit-comment' href='".wpqa_edit_permalink($comment_id,"comment")."'><i class='icon-pencil'></i>".esc_html__("Edit","wpqa")."</a></li>";
												}
												if ((isset($activate_delete) && $activate_delete == true) || (($can_delete_comment == "on" && $comment_user_id == $user_get_current_user_id && $comment_user_id > 0 && $user_get_current_user_id > 0) || $is_super_admin)) {
													echo "<li><a class='delete-comment".($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?' delete-answer':'')."' href='".esc_url_raw(add_query_arg(array('activate_delete' => true,'delete_comment' => $comment_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),get_permalink($post_id)))."'><i class='icon-trash'></i>".esc_html__("Delete","wpqa")."</a></li>";
												}
												if (($like_answer == true || ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) && $active_reports == "on" && ((is_user_logged_in() && $comment_user_id != $user_get_current_user_id && $user_get_current_user_id != 0) || (!is_user_logged_in() && $active_logged_reports != "on"))) {
													if (!$owner) {?>
														<li class="report_activated"><a class="report_c" href="<?php echo esc_attr($comment_id)?>"><i class="icon-attention"></i><?php esc_html_e("Report","wpqa")?></a></li>
													<?php }
												}?>
											</ul>
										</li>
										<li class="clearfix last-item-answers"></li>
									</ul>
								<?php }
								if (isset($info_edited) && $info_edited != "") {
									echo '<i class="icon-alert tooltip-n icon-info-edited" title="'.$info_edited.'"></i>';
								}
								do_action("wpqa_action_after_list_comment",$comment,($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?"answer":"comment"));?>
							</div><!-- End comment-footer-bottom -->
						</div><!-- End text -->
						<div class="clearfix"></div>
					</div><!-- End comment-text -->
				</div><!-- End comment-body -->
				<?php $comments_type = esc_html(isset($args["comments_type"])?$args["comments_type"]:"");
				if ($comments_type != "" && $comments_type != "best" && $comments_type != "single") {
					$answers_sort = esc_html(isset($args["answers_sort"])?$args["answers_sort"]:"");
					wpqa_get_replies($comment_id,$post_id,$post_type,$user_get_current_user_id,$depth,$answers_sort);
				}?>
			<!-- End li -->
		<?php }
	}
endif;
/* Get replies */
if (!function_exists('wpqa_get_replies')) :
	function wpqa_get_replies($comment_id,$post_id,$post_type,$user_id,$depth,$answers_sort = '') {
		$thread_comments = get_option("thread_comments");
		$thread_comments_depth = get_option("thread_comments_depth");
		if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && isset($answers_sort) && $answers_sort == "voted") {
			$orderby = array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'comment_vote','order' => 'DESC');
		}else if (isset($answers_sort) && $answers_sort == "oldest") {
			$orderby = array('orderby' => 'comment_date','order' => 'ASC');
		}else {
			$orderby = array('orderby' => 'comment_date','order' => 'DESC');
		}
		$comments_args = array(
			'post_id' => $post_id,
			'parent'  => $comment_id,
			'status'  => 'approve'
		);

		$author__not_in = array();
		$block_users = wpqa_options("block_users");
		if ($block_users == "on") {
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
				}
			}
		}

		$args = array_merge($author__not_in,$orderby,$comments_args);
		$child_comments = get_comments($args);
		if (is_array($child_comments) && !empty($child_comments)) {
			foreach($child_comments as $comment) {?>
				<ul class="children">
					<?php wpqa_comment($comment,array("max_depth" => ($thread_comments == true?$thread_comments_depth:'-1')),$depth);
					wpqa_get_replies($comment->comment_ID,$post_id,$post_type,$user_id,$depth,$answers_sort);?>
					</li>
				</ul>
			<?php }
		}
	}
endif;
/* Get meta name for visits */
function wpqa_get_meta_stats() {
	$meta_stats = wpqa_options("post_meta_stats");
	$meta_stats = apply_filters("wpqa_post_meta_stats",$meta_stats);
	$meta_stats = ($meta_stats != ""?$meta_stats:"post_stats");
	return $meta_stats;
}
/* Set post & question stats */
add_action("wpqa_action_before_post_content","wpqa_set_page_visits",1,2);
add_action("wpqa_action_before_group_cover","wpqa_set_page_visits");
add_action("wpqa_action_on_user_page","wpqa_set_page_visits");
function wpqa_set_page_visits($post_id = 0,$post_author = 0) {
	$active_post_stats = wpqa_options("active_post_stats");
	if ($active_post_stats == "on" && (is_single($post_id) || (wpqa_is_user_profile() && !wpqa_is_user_edit_profile()))) {
		$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		$active_stats = true;
		$user_id = get_current_user_id();
		$yes_private = (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?wpqa_private($post_id,$post_author,$user_id):1);
		if (!is_super_admin($user_id) && $yes_private != 1) {
			$active_stats = false;
		}
		if ($active_stats == true) {?>
			<div class="activate-post-stats <?php echo esc_attr(wpqa_is_user_profile() && !wpqa_is_user_edit_profile()?"page-visits-user":"page-visits-post")?>" data-id="<?php echo (int)(wpqa_is_user_profile() && !wpqa_is_user_edit_profile()?$wpqa_user_id:$post_id)?>"></div>
		<?php }
	}
}
/* Update post stats */
add_action('wp_ajax_wpqa_update_post_stats','wpqa_update_post_stats');
add_action('wp_ajax_nopriv_wpqa_update_post_stats','wpqa_update_post_stats');
function wpqa_update_post_stats($post_id = 0,$user_id = 0) {
	$post_id = (int)($post_id > 0?$post_id:(isset($_POST["post_id"])?$_POST["post_id"]:0));
	$user_id = (int)($user_id > 0?$user_id:(isset($_POST["user_id"])?$_POST["user_id"]:0));
	$meta_id = ($user_id > 0?$user_id:$post_id);
	$meta_stats = wpqa_get_meta_stats();
	$cache_post_stats = wpqa_options("cache_post_stats");
	$current_stats = wpqa_get_meta($meta_stats,$post_id,0,0,$user_id);
	$visit_cookie = wpqa_options("visit_cookie");
	if ($visit_cookie != "on" || ($visit_cookie == "on" && !isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_meta_stats'.$meta_id]))) {
		if (!isset($current_stats)) {
			wpqa_update_meta($meta_stats,1,$post_id,0,0,$user_id);
			if ($cache_post_stats == "on") {
				set_transient($meta_stats.$meta_id,(int)$current_stats+1,60*60*24);
			}
		}else {
			wpqa_update_meta($meta_stats,(int)$current_stats+1,$post_id,0,0,$user_id);
			if ($cache_post_stats == "on") {
				$post_stats = get_transient($meta_stats.$meta_id);
				if ($post_stats == false) {
					set_transient($meta_stats.$meta_id,(int)$current_stats+1,60*60*24);
				}
			}
		}
	}
	if ($visit_cookie == "on") {
		$uniqid_cookie = wpqa_options("uniqid_cookie");
		if (isset($_COOKIE[$uniqid_cookie.'wpqa_meta_stats'.$meta_id]) && $_COOKIE[$uniqid_cookie.'wpqa_meta_stats'.$meta_id] == "wpqa_meta_stats") {
			unset($_COOKIE[$uniqid_cookie.'wpqa_meta_stats'.$meta_id]);
			setcookie($uniqid_cookie.'wpqa_meta_stats'.$meta_id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
		}
		setcookie($uniqid_cookie.'wpqa_meta_stats'.$meta_id,"wpqa_meta_stats",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
	}
}
/* Get post stats */
function wpqa_get_post_stats($post_id = 0,$user_id = 0) {
	$meta_id = ($user_id > 0?$user_id:$post_id);
	$post_meta_stats = wpqa_get_meta_stats();
	$cache_post_stats = wpqa_options("cache_post_stats");
	if ($cache_post_stats == "on") {
		$post_stats = get_transient($post_meta_stats.$meta_id);
		$post_stats = ($post_stats !== false?$post_stats:wpqa_get_meta($post_meta_stats,$post_id,0,0,$user_id));
	}else {
		$post_stats = wpqa_get_meta($post_meta_stats,$post_id,0,0,$user_id);
	}
	return (int)$post_stats;
}
/* Update profile */
if (!function_exists('wpqa_update_profile')) :
	function wpqa_update_profile($user_id) {
		$update_profile = "";
		if (is_user_logged_in()) {
			$update_profile = get_user_meta($user_id,"update_profile",true);
			if (is_page()) {
				global $post;
				$login_only = get_post_meta($post->ID,prefix_meta."login_only",true);
				$update_profile = ($update_profile == "yes" && $login_only != "on"?"yes":"no");
			}
		}
		return $update_profile;
	}
endif;
/* Get edit profile page */
if (!function_exists('wpqa_update_edit_profile')) :
	function wpqa_update_edit_profile($user_id,$update_profile) {
		if ($update_profile == "yes") {
			echo '<div class="alert-message"><i class="icon-lamp"></i><p>'.esc_html__("Kindly fill the required fields, You need to fill all the required fields.","wpqa").'</p></div>';
			$nicename = wpqa_get_user_nicename($user_id);
			$templates = array(
				'edit-'.$nicename.'.php',
				'edit-'.$user_id.'.php',
				'edit.php',
				'profile.php',
			);
			if (isset($templates) && is_array($templates) && !empty($templates)) {
				$wpqa_get_template = wpqa_get_template($templates,"profile/");
				if ($wpqa_get_template) {
					include $wpqa_get_template;
				}
			}
			get_footer();
			die();
		}
	}
endif;
/* Check if site for the users only */
if (!function_exists('wpqa_site_users_only')) :
	function wpqa_site_users_only() {
		$site_users_only = $active_confirm_email = $login_only = "no";
		$site_users_option = wpqa_options("site_users_only");
		$login_pages = wpqa_options("login_pages");
		
		if (is_user_logged_in()) {
			$if_user_id = get_user_by("id",get_current_user_id());
			if (isset($if_user_id->caps["activation"]) && $if_user_id->caps["activation"] == 1) {
				$active_confirm_email = "yes";
			}
		}
		
		if ((!is_user_logged_in() && $site_users_option == "on") || (is_user_logged_in() && $active_confirm_email == "yes" && $site_users_option == "on")) {
			if (is_page()) {
				global $post;
				$login_only = get_post_meta($post->ID,prefix_meta.'login_only',true);
			}
			if ($login_only != "on") {
				$site_users_only = "yes";
			}else {
				$site_users_only = "no";
			}

			if ((is_singular("post") && isset($login_pages["posts"]) && $login_pages["posts"] == "posts") || ((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) && isset($login_pages["questions"]) && $login_pages["questions"] == "questions") || (is_singular(wpqa_knowledgebase_type) && isset($login_pages["knowledgebases"]) && $login_pages["knowledgebases"] == "knowledgebases")) {
				$site_users_only = "no";
			}
		}
		
		return $site_users_only;
	}
endif;
/* Check if the site under construction */
if (!function_exists('wpqa_under_construction')) :
	function wpqa_under_construction() {
		$under_construction = wpqa_options("under_construction");
		if (!function_exists('wp_get_current_user')) {
			include(ABSPATH . "wp-includes/pluggable.php"); 
		}
		if (!is_super_admin() && $under_construction == "on") {
			return $under_construction;
		}
	}
endif;
/* Check if confirm mail active */
if (!function_exists('wpqa_users_confirm_mail')) :
	function wpqa_users_confirm_mail($make_pages_work = true) {
		$site_users_only = $login_only = "no";
		if (is_user_logged_in()) {
			$if_user_id = get_user_by("id",get_current_user_id());
			if (isset($if_user_id->caps["ban_group"]) && $if_user_id->caps["ban_group"] == 1) {
				$site_users_only = "yes";
				if (is_page()) {
					global $post;
					$banned_only = get_post_meta($post->ID,prefix_meta.'banned_only',true);
					if ($banned_only == "on") {
						$site_users_only = "no";
					}
				}
			}else {
				if ($make_pages_work == true && $site_users_only == "yes") {
					if (is_page()) {
						global $post;
						$login_only = get_post_meta($post->ID,prefix_meta.'login_only',true);
					}
					
					if ($login_only != "on") {
						$site_users_only = "yes";
					}else {
						$site_users_only = "no";
					}
				}
			}
		}
		return $site_users_only;
	}
endif;
/* Header content */
add_action('wpqa_header_content','wpqa_header_content');
if (!function_exists('wpqa_header_content')) :
	function wpqa_header_content($args = array()) {
		/* Session */
		do_action("wpqa_show_session");
		/* Update */
		if (is_user_logged_in()) {
			wpqa_update_edit_profile($args["user_id"],$args["update_profile"]);
		}
		do_action("wpqa_do_payments");
	}
endif;
/* Top bar wordpress */
add_filter('show_admin_bar','wpqa_disable_admin_bar',20,1);
if (!function_exists('wpqa_disable_admin_bar')) :
	function wpqa_disable_admin_bar( $show_admin_bar ) {
		$top_bar_wordpress = wpqa_options("top_bar_wordpress");
		$user_info = get_userdata(get_current_user_id());
		$user_group = wpqa_get_user_group($user_info);
		if ($user_group == "ban_group" || ($top_bar_wordpress == "on" && !current_user_can('administrator'))) {
			$top_bar_groups = wpqa_options("top_bar_groups");
			if ($user_group == "ban_group" || (is_array($top_bar_groups) && in_array($user_group,$top_bar_groups))) {
				$show_admin_bar = false;
			}
		}
		return $show_admin_bar;
	}
endif;
/* Pagination */
if (!function_exists('wpqa_pagination')) :
	function wpqa_pagination($args = array(),$max_num_pages = '',$query = '') {
		global $wp_rewrite,$wp_query;
		do_action('wpqa_pagination_start');
		if (isset($query)) {
			$o_wp_query = $wp_query;
			$wp_query = $query;
		}
		if ($max_num_pages == "" && isset($wp_query->max_num_pages)) {
			$max_num_pages = $wp_query->max_num_pages;
		}
		/* If there's not more than one page,return nothing. */
		if (1 >= $max_num_pages) {
			if (isset($o_wp_query)) {
				$wp_query = $o_wp_query;
			}
			return;
		}
		/* Get the current page. */
		$paged = $current = wpqa_paged();
		$page_paged = (get_query_var("paged") != ""?"paged":(get_query_var("page") != ""?"page":"paged"));
		$current = $paged;
		/* Get the max number of pages. */
		$max_num_pages = ($max_num_pages != ""?$max_num_pages:intval($wp_query->max_num_pages));
		/* Set up some default arguments for the paginate_links() function. */
		$defaults = array(
			'base'         => esc_url_raw(add_query_arg($page_paged,'%#%')),
			'format'       => '',
			'total'        => $max_num_pages,
			'current'      => esc_html($current),
			'prev_next'    => true,
			'prev_text'    => '<i class="icon-left-open"></i>',
			'next_text'    => '<i class="icon-right-open"></i>',
			'show_all'     => false,
			'end_size'     => 1,
			'mid_size'     => 1,
			'add_fragment' => '',
			'type'         => 'plain',
			'before'       => '<div class="main-pagination"><div class="pagination">',
			'after'        => '</div></div>',
			'echo'         => true,
		);
		/* Add the $base argument to the array if the user is using permalinks. */
		if (!wpqa_is_search() && $wp_rewrite->using_permalinks()) {
			$defaults['base'] = user_trailingslashit(trailingslashit(get_pagenum_link()) . 'page/%#%');
		}
		/* If we're on a search results page,we need to change this up a bit. */
		if (!wpqa_is_search() && is_search()) {
		/* If we're in BuddyPress,use the default "unpretty" URL structure. */
			if (class_exists('BP_Core_User')) {
				$search_query = esc_html(get_query_var('s'));
				$base = user_trailingslashit(esc_url(home_url('/'))) . '?s=' . $search_query . '&paged=%#%';
				$defaults['base'] = $base;
			}else {
				$search_permastruct = $wp_rewrite->get_search_permastruct();
				if (!empty($search_permastruct)) {
					$search_type  = (isset($args['search_type']) && $args['search_type'] != ""?$args['search_type']:wpqa_search_type());
					$search_value = wpqa_search();
					$base = wpqa_search_link($search_value,$search_type,'%#%/');
					$defaults['base'] = $base;
				}
			}
		}
		/* Merge the arguments input with the defaults. */
		$args = wp_parse_args($args,$defaults);
		/* Allow developers to overwrite the arguments with a filter. */
		$args = apply_filters('wpqa_pagination_args',$args);
		/* Don't allow the user to set this to an array. */
		if ('array' == $args['type'])
			$args['type'] = 'plain';
		/* Make sure raw querystrings are displayed at the end of the URL,if using pretty permalinks. */
		$pattern = '/\?(.*?)\//i';
		preg_match($pattern,$args['base'],$raw_querystring);
		if ($wp_rewrite->using_permalinks() && $raw_querystring)
			$raw_querystring[0] = str_replace('','',$raw_querystring[0]);
			if (!empty($raw_querystring)) {
				$args['base'] = str_replace($raw_querystring[0],'',$args['base']);
				$args['base'] .= substr($raw_querystring[0],0,-1);
			}
		/* Get the paginated links. */
		$page_links = paginate_links($args);
		/* Remove 'page/1' from the entire output since it's not needed. */
		if (!wpqa_is_search()) {
			$page_links = str_replace(array('&#038;paged=1\'','/page/1\''),'\'',$page_links);
		}
		/* Wrap the paginated links with the $before and $after elements. */
		$page_links = $args['before'] . $page_links . $args['after'];
		/* Allow devs to completely overwrite the output. */
		$page_links = apply_filters('wpqa_pagination',$page_links);
		do_action('wpqa_pagination_end');
		/* Return the paginated links for use in themes. */
		if (isset($o_wp_query)) {
			$wp_query = $o_wp_query;
		}
		if ($args['echo']) {
			echo ($page_links);
		}else {
			return $page_links;
		}
	}
endif;
/* Search load more */
add_filter("get_pagenum_link","wpqa_get_pagenum_link");
function wpqa_get_pagenum_link($result) {
	if (wpqa_is_search() || wpqa_is_user_questions() || wpqa_is_user_posts()) {
		global $wp_rewrite;
		$paged = wpqa_paged();
		$pagenum = $paged + 1;
		$request = remove_query_arg('page');
		$home_root = parse_url(home_url());
		$home_root = (isset($home_root['path']))?$home_root['path']:'';
		$home_root = preg_quote($home_root,'|');
		$request = preg_replace('|^'.$home_root.'|i','',$request);
		$request = preg_replace('|^/+|','',$request);
		if (wpqa_is_user_questions()) {
			$request = str_ireplace('page/'.$pagenum.'/','',$request);
		}
		$base = trailingslashit(get_bloginfo('url'));
		if ($pagenum > 1) {
			$result = add_query_arg('page',$pagenum,$base.$request);
		}else {
			$result = $base.$request;
		}
	}
	return $result;
}
/* Get the next page */
function wpqa_next_posts_page_link( $label = null, $max_page = 0 ) {
	global $paged;
	if ( ! $paged ) {
		$paged = 1;
	}
	$nextpage = (int) $paged + 1;
	if ( ! $max_page || $max_page >= $nextpage ) {
		return '<a href="' . get_pagenum_link( $nextpage ) . "\">" . preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) . '</a>';
	}
}
/* Pagination load */
if (!function_exists('wpqa_load_pagination')) :
	function wpqa_load_pagination($args_array = array()) {
		$defaults = array(
			'post_pagination' => 'pagination',
			'max_num_pages' => '',
			'it_answer_pagination' => false,
			'its_post_type' => false,
			'wpqa_query' => null,
			'it_comment_pagination' => false,
			'args' => array(),
			'comment' => '',
			'search_type' => '',
			'its_answer' => false,
		);
		
		$args_array = wp_parse_args($args_array,$defaults);

		$post_pagination = $args_array['post_pagination'];
		$max_num_pages = $args_array['max_num_pages'];
		$it_answer_pagination = $args_array['it_answer_pagination'];
		$its_post_type = $args_array['its_post_type'];
		$wpqa_query = $args_array['wpqa_query'];
		$it_comment_pagination = $args_array['it_comment_pagination'];
		$args = $args_array['args'];
		$comment = $args_array['comment'];
		$search_type = $args_array['search_type'];
		$its_answer = $args_array['its_answer'];

		if ($post_pagination != "none") {
			$get_post_type = get_post_type();
			if (isset($it_answer_pagination) && $it_answer_pagination == true) {
				if (isset($it_comment_pagination) && $it_comment_pagination == true) {
					$nomore_text = esc_html__("No more comments","wpqa");
					$load_text = esc_html__("Load More Comments","wpqa");
					$old_posts = esc_html__('Old Comments','wpqa');
					$new_posts = esc_html__('New Comments','wpqa');
				}else {
					$nomore_text = esc_html__("No more answers","wpqa");
					$load_text = esc_html__("Load More Answers","wpqa");
					$old_posts = esc_html__('Old Answers','wpqa');
					$new_posts = esc_html__('New Answers','wpqa');
				}
				$wrap_main_content = 'commentlist';
				$pagination_class = 'pagination-answer';
			}else if (isset($its_post_type) && ($its_post_type == wpqa_questions_type || $its_post_type == wpqa_asked_questions_type)) {
				$nomore_text = esc_html__("No more questions","wpqa");
				$load_text = esc_html__("Load More Questions","wpqa");
				$wrap_main_content = 'post-articles.question-articles';
				$pagination_class = 'pagination-question';
				$old_posts = esc_html__('Old Questions','wpqa');
				$new_posts = esc_html__('New Questions','wpqa');
			}else if (isset($its_post_type) && $its_post_type == wpqa_knowledgebase_type) {
				$nomore_text = esc_html__("No more knowledgebases","wpqa");
				$load_text = esc_html__("Load More knowledgebases","wpqa");
				$wrap_main_content = 'post-articles.knowledgebase-articles';
				$pagination_class = 'pagination-knowledgebase';
				$old_posts = esc_html__('Old Knowledgebases','wpqa');
				$new_posts = esc_html__('New Knowledgebases','wpqa');
			}else if (isset($its_post_type) && $its_post_type == "group") {
				$nomore_text = esc_html__("No more groups","wpqa");
				$load_text = esc_html__("Load More Groups","wpqa");
				$wrap_main_content = 'post-articles.group-articles';
				$pagination_class = 'pagination-group';
				$old_posts = esc_html__('Old Groups','wpqa');
				$new_posts = esc_html__('New Groups','wpqa');
			}else if (isset($its_post_type) && $its_post_type == "group_posts") {
				$nomore_text = esc_html__("No more posts","wpqa");
				$load_text = esc_html__("Load More Posts","wpqa");
				$wrap_main_content = 'post-articles.group-posts-articles';
				$pagination_class = 'pagination-group-posts';
				$old_posts = esc_html__('Old Posts','wpqa');
				$new_posts = esc_html__('New Posts','wpqa');
			}else {
				$nomore_text = esc_html__("No more posts","wpqa");
				$load_text = esc_html__("Load More Posts","wpqa");
				$wrap_main_content = 'post-articles';
				$pagination_class = 'pagination-post';
				$old_posts = esc_html__('Old Entries','wpqa');
				$new_posts = esc_html__('New Entries','wpqa');
			}
			if ($post_pagination == "infinite_scroll" || $post_pagination == "load_more") {
				if ($its_answer == true) {
					$default_comments_page = get_option("default_comments_page");
					$more_link = ($default_comments_page == "newest"?get_previous_comments_link(' '):get_next_comments_link(' ',$max_num_pages));
				}else if (isset($its_post_type) && $its_post_type == "group_posts") {
					$more_link = wpqa_next_posts_page_link(' ',$max_num_pages);
				}else {
					$more_link = get_next_posts_link(' ',$max_num_pages);
				}
				$get_theme_name = wpqa_prefix_theme;
				if (!empty($more_link)) :?>
					<script type="text/javascript">
						(function($) {
							jQuery(document).ready(function() {
								/* Load more */
								function wpqa_load_more(load_type,j_this,ajax_count) {
									var main_content = ".the-main-inner";
									if (load_type == "infinite-scroll") {
										var $link = jQuery('.posts-infinite-scroll a');
									}else {
										var $link = j_this;
									}
									var page_url = $link.attr("href");
									if (page_url != undefined) {
										if (load_type == "infinite-scroll") {
											$link.parent().parent().animate({ opacity: 1}, 300).css('padding', '10px');
										}else {
											$link.closest(main_content).find(".posts-"+load_type+" a").hide();
										}
										$link.closest(main_content).find(".posts-"+load_type+" .load_span").addClass("load_span_display");
										jQuery("<div>").load(page_url, function() {
											var p_count = ajax_count.toString();
											var $wrap = $link.closest(main_content).find('.<?php echo esc_js($wrap_main_content)?>');
											var $wrap_row = $wrap.find(" > .row, > .row-boot");
											$wrap = ($wrap_row.length > 0?$wrap_row:$wrap);
											<?php if ('post' === $get_post_type) {?>
												var $share = $link.closest(main_content).find('.post-articles article.post .post-share > ul').attr("style");
											<?php }?>
											var $this_div = jQuery(this);
											var $new = $this_div.find('.<?php echo (isset($it_answer_pagination) && $it_answer_pagination == true?"commentlist > li.comment":"post-articles article.".esc_js($get_post_type))?>,.aalan.aalan-inside').addClass('<?php echo (isset($it_answer_pagination) && $it_answer_pagination == true?"comment":"post-section")?>-new-'+p_count);
											<?php if ('post' === $get_post_type) {?>
												$new.find('.post-share > ul').attr("style",$share);
											<?php }?>
											$new.imagesLoaded(function() {
												$new.hide().appendTo($wrap).fadeIn(400);
												/* Lightbox */
												var lightboxArgs = {
													animation_speed: "fast",
													overlay_gallery: true,
													autoplay_slideshow: false,
													slideshow: 5000,
													theme: "pp_default",
													opacity: 0.8,
													show_title: false,
													social_tools: "",
													deeplinking: false,
													allow_resize: true,
													counter_separator_label: "/",
													default_width: 940,
													default_height: 529
												};
												jQuery("a[href$=jpg],a[href$=JPG],a[href$=jpeg],a[href$=JPEG],a[href$=png],a[href$=gif],a[href$=bmp]:has(img)").prettyPhoto(lightboxArgs);
												jQuery("a[class^='prettyPhoto'],a[rel^='prettyPhoto']").prettyPhoto(lightboxArgs);
												/* Facebook */
												jQuery(".facebook-remove").remove();
												/* Owl */
												jQuery(".post-section-new-"+p_count+" .slider-owl").each(function () {
													var $slider = jQuery(this);
													var $slider_item = $slider.find('.slider-item').length;
													$slider.find('.slider-item').css({"height":"auto"});
													if ($slider.find('img').length) {
														var $slider = jQuery(this).imagesLoaded(function() {
															$slider.owlCarousel({
																autoPlay: 5000,
																margin: 10,
																responsive: {
																	0: {
																		items: 1
																	}
																},
																stopOnHover: true,
																navText : ["", ""],
																nav: ($slider_item > 1)?true:false,
																rtl: jQuery('body.rtl').length?true:false,
																loop: ($slider_item > 1)?true:false,
															});
														});
													}else {
														$slider.owlCarousel({
															autoPlay: 5000,
															margin: 10,
															responsive: {
																0: {
																	items: 1
																}
															},
															stopOnHover: true,
															navText : ["", ""],
															nav: ($slider_item > 1)?true:false,
															rtl: jQuery('body.rtl').length?true:false,
															loop: ($slider_item > 1)?true:false,
														});
													}
												});
												/* Question masonry */
												if (jQuery(".post-section-new-"+p_count+".post-with-columns").length) {
													if ($new.eq(0).is('.question-masonry')) {
														var newItems = jQuery('.post-section-new-'+p_count);
														jQuery('.question-articles').isotope( 'insert', newItems );
														jQuery('.question-articles').isotope({
															filter: "*",
															animationOptions: {
																duration: 750,
																itemSelector: '.question-masonry',
																easing: "linear",
																queue: false,
															}
														});
														setTimeout(function() {
															if ($new.eq(0).is('.post-masonry')) {
																jQuery('.question-articles').isotope({
																	filter: "*",
																	animationOptions: {
																		duration: 750,
																		itemSelector: '.question-masonry',
																		easing: "linear",
																		queue: false,
																	}
																});
															}
														}, 1000);
													}else if (!$new.eq(0).is('.post-masonry')) {
														jQuery(".post-section-new-"+p_count+".post-with-columns").matchHeight();
														jQuery(".post-section-new-"+p_count+".post-with-columns > .article-question").matchHeight();
													}
												}
												/* Post masonry */
												if (jQuery(".post-section-new-"+p_count+".post-with-columns").length) {
													if ($new.eq(0).is('.post-masonry')) {
														var newItems = jQuery('.post-section-new-'+p_count);
														jQuery('.post-articles').isotope( 'insert', newItems );
														jQuery('.post-articles').isotope({
															filter: "*",
															animationOptions: {
																duration: 750,
																itemSelector: '.post-masonry',
																easing: "linear",
																queue: false,
															}
														});
														setTimeout(function() {
															if ($new.eq(0).is('.post-masonry')) {
																jQuery('.post-articles').isotope({
																	filter: "*",
																	animationOptions: {
																		duration: 750,
																		itemSelector: '.post-masonry',
																		easing: "linear",
																		queue: false,
																	}
																});
															}
														}, 1000);
													}
												}
												/* Poll */
												if (jQuery(".post-section-new-"+p_count+" .progressbar-percent").length) {
													jQuery(".post-section-new-"+p_count+" .progressbar-percent").each(function() {
														var $this = jQuery(this);
														var percent = $this.attr("attr-percent");
														$this.on("inview", function(event,isInView,visiblePartX,visiblePartY) {
															if (isInView) {
																$this.animate({"width": percent + "%"},700);
															}
														});
													});
												}
												/* Audio */
												if ($new.eq(0).find('.wp-audio-shortcode')) {
													<?php wp_enqueue_style('wp-mediaelement');
													wp_enqueue_script('wp-playlist');?>
													mejs.plugins.silverlight[0].types.push('video/x-ms-wmv');
													mejs.plugins.silverlight[0].types.push('audio/x-ms-wma');
													jQuery(function () {
														var settings = {};
														if (typeof _wpmejsSettings !== 'undefined') {
															settings = _wpmejsSettings;
														}
														settings.success = settings.success || function (mejs) {
															var autoplay, loop;
															if ('flash' === mejs.pluginType) {
																autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
																loop = mejs.attributes.loop && 'false' !== mejs.attributes.loop;
																autoplay && mejs.addEventListener('canplay',function() {
																	mejs.play();
																},false);
																loop && mejs.addEventListener('ended',function() {
																	mejs.play();
																},false);
															}
														};
														jQuery('.post-section-new-'+p_count+' .wp-audio-shortcode').mediaelementplayer(settings);
													});
												}
												$link.closest(main_content).find(".posts-"+load_type+" .load_span").removeClass("load_span_display");
												if (load_type == "load-more") {
													$link.closest(main_content).find(".posts-"+load_type+" a").show();
												}
												/* Content */
												StickySidebarContent();
												/* load more */
												if ($this_div.find(".posts-"+load_type).length) {
													if (load_type == "infinite-scroll") {
														$link.closest(main_content).find(".posts-infinite-scroll").html($this_div.find(".posts-infinite-scroll").html()).animate({opacity: 0}, 300).css("padding","0");
													}else {
														$link.closest(main_content).find(".posts-"+load_type).html($this_div.find(".posts-"+load_type).html());
													}
												}else {
													$link.closest(main_content).find(".pagination-wrap").html('<p class="no-comments"><?php echo esc_js($nomore_text)?></p>');
													$link.closest(main_content).find(".posts-"+load_type).fadeOut("fast").remove();
												}
												jQuery("<?php echo (isset($it_answer_pagination) && $it_answer_pagination == true?"comment":"post-section")?>-new-"+p_count).removeClass("<?php echo (isset($it_answer_pagination) && $it_answer_pagination == true?"comment":"post-section")?>-new-"+p_count);
												return false;
											});
										});
									}
								}
								var ajax_count = 0;
								/* infinite scroll */
								jQuery(".posts-infinite-scroll").each (function () {
									jQuery(this).on("inview",function(event,isInView,visiblePartX,visiblePartY) {
										if  (jQuery(".posts-infinite-scroll").length && isInView) {
											/* wpqa_load_more */
											ajax_count++;
											wpqa_load_more("infinite-scroll","",ajax_count);
										}
									});
								});
								/* load more */
								jQuery("body").on("click",".posts-load-more a",function(e) {
									e.preventDefault();
									/* wpqa_load_more */
									ajax_count++;
									wpqa_load_more("load-more",jQuery(this),ajax_count);
								});
							});
						})(jQuery);
					</script>
				<?php endif;
			}?>
			<div class="clearfix"></div>
			<div class="pagination-wrap <?php echo esc_attr($pagination_class).(empty($more_link)?" no-pagination-wrap":"")?>">
				<?php if ($post_pagination == "load_more" || $post_pagination == "infinite_scroll") {
					if ($its_answer == true) {
						$more_link = ($post_pagination == "load_more"?($default_comments_page == "newest"?get_previous_comments_link($load_text):get_next_comments_link($load_text,$max_num_pages)):$more_link);
					}else if (isset($its_post_type) && $its_post_type == "group_posts") {
						$more_link = ($post_pagination == "load_more"?wpqa_next_posts_page_link($load_text,$max_num_pages):$more_link);
					}else {
						$more_link = ($post_pagination == "load_more"?get_next_posts_link($load_text,$max_num_pages):$more_link);
					}
					if (!empty($more_link)) {?>
						<div class="pagination-nav <?php echo ($post_pagination == "infinite_scroll"?"posts-infinite-scroll":"posts-load-more")?>"<?php (is_array($args) && !empty($args)?" data-query='".json_encode($args)."'":"")?>>
							<span class="load_span"><span class="loader_2"></span></span>
							<div class="load-more"><?php echo ($more_link)?></div>
						</div><!-- End pagination-nav -->
					<?php }
				}else if ($post_pagination == "pagination") {
					wpqa_pagination(array("search_type" => $search_type),$max_num_pages,(isset($wpqa_query)?$wpqa_query:null));
				}else {?>
					<div class="page-navigation page-navigation-before clearfix">
						<div class="row row-boot row-warp">
							<div class="col col6 col-boot-sm-6 nav-more-next">
								<div class="nav-custom-link nav-next<?php echo ($its_answer == true?' nav-next-comment':'')?>">
									<?php if ($its_answer == true) {
										next_comments_link('<i class="icon-left-thin"></i><span>'.$old_posts.'</span>',$max_num_pages);
									}else {
										next_posts_link('<i class="icon-left-thin"></i><span>'.$old_posts.'</span>',$max_num_pages);
									}?>
								</div>
							</div>
							<div class="col col6 col-boot-sm-6 nav-more-previous">
								<div class="nav-custom-link nav-previous<?php echo ($its_answer == true?' nav-previous-comment':'')?>">
									<?php if ($its_answer == true) {
										previous_comments_link('<span>'.$new_posts.'</span><i class="icon-right-thin"></i>',$max_num_pages);
									}else {
										previous_posts_link('<span>'.$new_posts.'</span><i class="icon-right-thin"></i>',$max_num_pages);
									}?>
								</div>
							</div>
						</div>
					</div>
				<?php }?>
			</div>
		<?php }
	}
endif;
/* Share links */
if (!function_exists('wpqa_share')) :
	function wpqa_share($post_share,$share_facebook,$share_twitter,$share_linkedin,$share_whatsapp,$share_style = "style_1",$comment_id = "",$tax_id = "",$url = "",$title = "",$post_id = 0,$send_email = "") {
		if ($post_id == 0) {
			global $post;
			$post_id = (int)(isset($post->ID)?$post->ID:0);
		}
		if ($share_facebook == "share_facebook" || $share_twitter == "share_twitter" || $share_linkedin == "share_linkedin" || $share_whatsapp == "share_whatsapp") {
			if ($url == "" && $title == "") {
				if ($tax_id > 0 && (is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories))) {
					$url = get_term_link($tax_id,(is_tax(wpqa_question_categories)?wpqa_question_categories:wpqa_knowledgebase_categories));
					$title = esc_html(get_query_var('wpqa_term_name'));
				}else {
					$url = urlencode(get_permalink($post_id).($comment_id > 0?"#comment-".$comment_id:""));
					$title = urlencode($comment_id > 0?wp_html_excerpt(strip_shortcodes(get_comment_text($comment_id)),160):get_the_title($post_id));
				}
			}
			$url = rawurldecode($url);
			if (isset($post_id) && "post" == get_post_type($post_id) && !is_tax() && !is_category()) {
				$window_title = esc_html__("Share This Article","wpqa");
			}else {
				$window_title = esc_html__("Share","wpqa");
			}?>
			<div class="post-share">
				<span><i class="icon-share"></i><span><?php echo ($window_title)?></span></span>
				<ul class="social-icons list-unstyled mb-0 d-flex align-items-center">
					<?php foreach ($post_share as $key => $value) {
						if ($share_facebook == "share_facebook" && isset($value["value"]) && $value["value"] == "share_facebook") {?>
							<li class="share-facebook">
								<a target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo ($url)?>&amp;t=<?php echo ($title);?>">
									<i class="icon-facebook"></i><span><?php echo ($share_style == "style_2"?esc_html__("Share on","wpqa"):"")."<span>".esc_html__("Facebook","wpqa")."</span>"?></span>
								</a>
							</li>
						<?php }else if ($share_twitter == "share_twitter" && isset($value["value"]) && $value["value"] == "share_twitter") {?>
							<li class="share-twitter">
								<a target="_blank" href="http://twitter.com/share?text=<?php echo ($title);?>&amp;url=<?php echo ($url);?>">
									<i class="icon-twitter"></i><span><?php echo ($share_style == "style_2"?esc_html__("Share on","wpqa")." ".esc_html__("Twitter","wpqa"):"")?></span>
								</a>
							</li>
						<?php }else if ($share_linkedin == "share_linkedin" && isset($value["value"]) && $value["value"] == "share_linkedin") {?>
							<li class="share-linkedin">
								<a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo ($url);?>&amp;title=<?php echo ($title);?>">
									<i class="icon-linkedin"></i><span><?php echo ($share_style == "style_2"?esc_html__("Share on","wpqa")." ".esc_html__("LinkedIn","wpqa"):"")?></span>
								</a>
							</li>
						<?php }else if ($share_whatsapp == "share_whatsapp" && isset($value["value"]) && $value["value"] == "share_whatsapp") {?>
							<li class="share-whatsapp">
								<a target="_blank" href="https://api.whatsapp.com/send?text=<?php echo ($title)?> - <?php echo ($url);?>">
									<i class="fab fa-whatsapp"></i><span><?php echo ($share_style == "style_2"?esc_html__("Share on","wpqa")." ".esc_html__("WhatsApp","wpqa"):"")?></span>
								</a>
							</li>
						<?php }
					}
					if ($send_email == "send_email") {?>
						<li class="share-email">
							<a target="_blank" title="<?php esc_html_e("Send an email","wpqa")?>" href="mailto:?subject=<?php echo ($title)?>&amp;body=<?php echo ($url);?>">
								<i class="icon-mail"></i>
							</a>
						</li>
					<?php }?>
				</ul>
			</div><!-- End post-share -->
		<?php }
	}
endif;
/* Return video iframe */
function wpqa_video_iframe($video_type,$video_id,$meta_type = "",$meta_name = "",$meta_id = 0) {
	if ($video_type == 'youtube') {
		preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/",$video_id,$matches);
		if (isset($matches[1])) {
			$video_id = $matches[1];
		}
		$type = "https://www.youtube.com/embed/".$video_id;
	}else if ($video_type == 'vimeo') {
		preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/",$video_id,$matches);
		if (isset($matches[3])) {
			$video_id = $matches[3];
		}
		$type = "https://player.vimeo.com/video/".$video_id;
	}else if ($video_type == 'daily') {
		preg_match("!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!",$video_id,$matches);
		if (isset($matches[2])) {
			$video_id = $matches[2];
		}
		$type = "https://www.dailymotion.com/embed/video/".$video_id;
	}else if ($video_type == 'facebook') {
		preg_match("~(?:t\.\d+/)?(\d+)~i",$video_id,$matches);
		if (isset($matches[1])) {
			$video_id = $matches[1];
		}
		$type = "https://www.facebook.com/video/embed?video_id=".$video_id;
		$type = "https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Ffacebook%2Fvideos%2F".$video_id."%2F&width=500&show_text=false&height=612&appId";
	}else if ($video_type == 'tiktok') {
		if (strpos($video_id,'/vm.tiktok.com/') !== false) {
			$response = wp_remote_get(esc_url($video_id));
			if (!is_wp_error($response) && isset($response['body'])) {
				$json = json_encode($response['body'],true);
				preg_match('/property="og:url" content="(.*?)}"/', stripslashes($json),$matchs);
				if (isset($matchs[1]) && $matchs[1] != "") {
					$video_id = explode("&amp;",$matchs[1]);
					if (isset($video_id[0]) && $video_id[0] != "") {
						$video_id = $video_id[0];
						$full_link = str_replace("?_r=1","",$video_id);
						$explode = explode("/video/",$full_link);
						preg_match("~(?:t\.\d+/)?(\d+)~i",$full_link,$matches);
						if (isset($matches[1])) {
							$video_id = $matches[1];
						}
						if (isset($matches[1]) && isset($explode[1]) && $explode[1] != "" && $matches[1] != $explode[1]) {
							$video_id = $explode[1];
						}
						if (isset($full_link) && $full_link != "") {
							if ($meta_type == "options") {
								$get_option = get_option(wpqa_options);
								$get_option[$meta_name] = $full_link;
								update_option(wpqa_options,$get_option);
							}else if ($meta_type == "post_meta") {
								update_post_meta($meta_id,$meta_name,$full_link);
							}else if ($meta_type == "comment_meta") {
								update_comment_meta($meta_id,$meta_name,$full_link);
							}
						}
					}
				}
			}
		}else {
			$explode = explode("/video/",$video_id);
			preg_match("~(?:t\.\d+/)?(\d+)~i",$video_id,$matches);
			if (isset($matches[1])) {
				$video_id = $matches[1];
			}
			if (isset($matches[1]) && isset($explode[1]) && $explode[1] != "" && $matches[1] != $explode[1]) {
				$video_id = $explode[1];
			}
		}
		if (isset($video_id)) {
			$type = "https://www.tiktok.com/embed/".$video_id;
		}
	}
	return (isset($type)?$type:"");
}
/* Get image for share */
if (!function_exists('wpqa_image_for_share')) :
	function wpqa_image_for_share() {
		global $post;
		$post_thumb = wpqa_get_the_image($post->ID,apply_filters("wpqa_image_share_size","large"));
		if (!empty($post_thumb)) {
			$post_thumb = $post_thumb;
		}else {
			$fb_share_image = wpqa_image_url_id(wpqa_options("fb_share_image"));
			if ($fb_share_image != "") {
				$post_thumb = $fb_share_image;
			}
		}
		return $post_thumb;
	}
endif;
/* Get the image */
if (!function_exists('wpqa_get_the_image')) :
	function wpqa_get_the_image($post_id,$imge_size = "large",$aq_resize = "") {
		$what_post = get_post_meta($post_id,'what_post',true);
		$post_thumb = "";
		if ($what_post == "video") {
			$protocol = is_ssl() ? 'https' : 'http';
			$video_id = get_post_meta($post_id,prefix_meta.'video_post_id',true);
			$video_type = get_post_meta($post_id,prefix_meta.'video_post_type',true);
			if (!empty($video_id)) {
				if ($video_type == 'youtube') {
					$post_thumb = $protocol.'://img.youtube.com/vi/'.$video_id.'/0.jpg';
				}else if ($video_type == 'vimeo') {
					$url = $protocol.'://vimeo.com/api/v2/video/'.$video_id.'.php';
					$data = wp_remote_get($url);
					if (!is_wp_error($data)) {
						$thumb = @unserialize(trim($data['body']));
						$post_thumb = $thumb[0]['thumbnail_large'];
					}
				}else if ($video_type == 'daily') {
					$post_thumb = $protocol.'://www.dailymotion.com/thumbnail/video/'.$video_id;
				}else if ($video_type == 'facebook') {
					$post_thumb = $protocol.'://graph.facebook.com/'.$video_id.'/picture';
				}
			}else if (has_post_thumbnail($post_id)) {
				$post_thumb = wpqa_get_aq_resize_img_url(600,315,"on",get_post_thumbnail_id($post_id));
			}else {
				$wpqa_image = wpqa_image();
				if ($wpqa_image != "") {
					$post_thumb = wpqa_get_aq_resize_url($wpqa_image,600,315);
				}
			}
		}else if ($what_post == "slideshow") {
			$slideshow_type = get_post_meta($post_id,prefix_meta.'slideshow_type',true);
			if ($slideshow_type == "custom_slide") {
				$slideshow_post = get_post_meta($post_id,prefix_meta.'slideshow_post',true);
				if (isset($slideshow_post[1]['image_url']['id'])) {
					$post_thumb = wpqa_get_aq_resize_img_url(600,315,"on",$slideshow_post[1]['image_url']['id']);
				}
			}else if ($slideshow_type == "upload_images") {
				$upload_images = get_post_meta($post_id,prefix_meta.'upload_images',true);
				if (isset($upload_images[1])) {
					$post_thumb = wpqa_get_aq_resize_img_url(600,315,"on",$upload_images[1]);
				}
			}
		}else {
			if (has_post_thumbnail($post_id)) {
				$post_thumb = wpqa_get_aq_resize_img_url(600,315,"on",get_post_thumbnail_id($post_id));
			}else {
				$wpqa_image = wpqa_image();
				if ($wpqa_image != "") {
					$post_thumb = wpqa_get_aq_resize_url($wpqa_image,600,315);
				}
			}
		}
		return (isset($post_thumb)?$post_thumb:"");
	}
endif;
/* hex2rgb */
if (!function_exists('wpqa_hex2rgb')) :
	function wpqa_hex2rgb ($hex) {
	   $hex = str_replace("#","",$hex);
	   if (strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   }else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   return $rgb;
	}
endif;
/* Mention */
add_filter("the_content","wpqa_mention");
add_filter("comment_text","wpqa_mention");
if (!function_exists('wpqa_mention')) :
	function wpqa_mention($content) {
		$active_mention = wpqa_options("active_mention");
		if ($active_mention == "on") {
			if (preg_match_all('/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i',$content, $matches_email)) {
				if (isset($matches[0])) {}
			}else if (preg_match_all('/@[\w\s+]+/',$content,$matches)) {
				if (isset($matches[0])) {
					foreach ($matches[0] as $key => $match) {
						$first_match = str_ireplace("@","",$match);
						$middle_match = preg_split("/[\s,]+/",$first_match);
						$match = trim((isset($middle_match[0]) && $middle_match[0] !== ""?$middle_match[0]:"")." ".(isset($middle_match[1]) && $middle_match[1] !== ""?$middle_match[1]:""));
						$last_match = trim(preg_replace('/\s+/', ' ', $match));
						if ($last_match != "") {
							$display_name = (isset($middle_match[0]) && $middle_match[0] !== ""?$middle_match[0]:$last_match);
							add_action('pre_user_query','wpqa_custom_search_mention');
							$args = array(
								'meta_query' => array('relation' => 'OR',array("key" => "display_name","value" => $display_name,"compare" => "RLIKE")),
								'orderby'    => "user_registered",
								'order'      => "ASC",
								'search'     => '*'.$last_match.'*',
								'number'     => 1,
								'fields'     => 'ID',
							);
							$query = new WP_User_Query($args);
							$query = $query->get_results();
							if (isset($query[0])) {
								$user_id = $query[0];
								$content = str_ireplace('@'.$last_match,'<a target="_blank" href="'.wpqa_profile_url($user_id).'">'.get_the_author_meta('display_name',$user_id).'</a>',$content);
							}else {
								$args = array(
									'meta_query' => array('relation' => 'OR',array("key" => "display_name","value" => $display_name,"compare" => "RLIKE")),
									'orderby'    => "user_registered",
									'order'      => "ASC",
									'search'     => '*'.$display_name.'*',
									'number'     => 1,
									'fields'     => 'ID',
								);
								$query = new WP_User_Query($args);
								$query = $query->get_results();
								if (isset($query[0])) {
									$user_id = $query[0];
									$content = str_ireplace('@'.$display_name,'<a target="_blank" href="'.wpqa_profile_url($user_id).'">'.get_the_author_meta('display_name',$user_id).'</a>',$content);
								}
							}
							remove_action('pre_user_query','wpqa_custom_search_mention');
						}
					}
				}
			}
		}
		return $content;
	}
endif;
/* Custom search for mention */
if (!function_exists('wpqa_custom_search_mention')) :
	function wpqa_custom_search_mention($user_query) {
		global $wpdb;
		$search_value = $user_query->query_vars;
		if (is_array($search_value) && isset($search_value['search'])) {
			$search_value = str_replace("*","",$search_value['search']);
		}
		$search_value = apply_filters("wpqa_search_value_filter",$search_value);
		$user_query->query_where .= " OR ($wpdb->users.user_login LIKE '".$search_value."' OR $wpdb->users.user_login RLIKE '".$search_value."')";
	}
endif;
/* Random token */
function wpqa_token($length){
	$token = "";
	$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	$codeAlphabet.= "0123456789";
	$max = strlen($codeAlphabet);
	for ($i=0; $i < $length; $i++) {
		$token .= $codeAlphabet[random_int(0, $max-1)];
	}
	return $token;
}
/* Remove buttons from the editor */
add_filter("mce_buttons","wpqa_mce_buttons");
function wpqa_mce_buttons($return) {
	if (!is_admin()) {
		$return = array('formatselect','bold','italic','bullist','numlist','blockquote','alignleft','aligncenter','alignright','link','unlink','spellchecker','wp_adv');
	}
	return $return;
}
/* Paged */
function wpqa_paged() {
	if (get_query_var("paged") != "") {
		$paged = (int)get_query_var("paged");
	}else if (get_query_var("page") != "") {
		$paged = (int)get_query_var("page");
	}
	if (get_query_var("paged") > get_query_var("page") && get_query_var("paged") > 0) {
		$paged = (int)get_query_var("paged");
	}
	if (get_query_var("page") > get_query_var("paged") && get_query_var("page") > 0) {
		$paged = (int)get_query_var("page");
	}
	if (!isset($paged) || (isset($paged) && $paged <= 1)) {
		$paged = 1;
	}
	return $paged;
}
/* Count posts */
function wpqa_count_posts_meta($post_type,$user_id) {
	$count = (int)get_user_meta($user_id,"wpqa_".$post_type."s_count",true);
	return $count;
}
/* Count comments */
function wpqa_count_comments_meta($post_type,$user_id = 0) {
	if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
		$meta = "wpqa_answers_count";
	}else {
		$meta = "wpqa_comments_count";
	}
	$count = (int)get_user_meta($user_id,$meta,true);
	return $count;
}
/* Count best answers */
function wpqa_count_best_answers_meta($user_id = 0) {
	$count = (int)get_user_meta($user_id,"wpqa_count_best_answers",true);
	return $count;
}
/* Count comments */
function wpqa_comments_of_post_type($post_type = null,$user_id = 0,$date = array(),$search = "",$parent = "") {
	global $wpdb;
	$post_type = (is_array($post_type)?$post_type:($post_type != ""?$post_type:"post"));
	$custom_post_type = "AND (";
	if (is_array($post_type)) {
		$key = 0;
		foreach ($post_type as $value) {
			if ($key != 0) {
				$custom_post_type .= " OR ";
			}
			$custom_post_type .= "$wpdb->posts.post_type = '$value'";
			$key++;
		}
	}else {
		$custom_post_type .= "$wpdb->posts.post_type = '$post_type'";
	}
	$custom_post_type .= ")";
	$date_query = "";
	if (is_array($date) && !empty($date)) {
		$date_query = "AND ( ( ";
		$date_query .= "YEAR( $wpdb->comments.comment_date ) = ".(isset($date["year"]) && $date["year"] != ""?$date["year"]:date("Y"));
		if (isset($date["month"]) && $date["month"] != "") {
			$date_query .= " AND MONTH( $wpdb->comments.comment_date ) = ".$date["month"];
		}
		if (isset($date["day"]) && $date["day"] != "") {
			$date_query .= " AND DAY( $wpdb->comments.comment_date ) = ".$date["day"];
		}
		$date_query .= " ) )";
	}
	$search = ($search === ""?"":"AND (comment_author LIKE '%".$search."%' OR comment_author_email LIKE '%".$search."%' OR comment_author_url LIKE '%".$search."%' OR comment_author_IP LIKE '%".$search."%' OR comment_content LIKE '%".$search."%')");
	$count = $wpdb->get_var(
		"SELECT COUNT($wpdb->comments.comment_ID)
		FROM $wpdb->comments JOIN $wpdb->posts
		ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID
		WHERE ($wpdb->comments.comment_approved = '1'
		".$search."
		".($user_id > 0?"AND $wpdb->comments.user_id = $user_id":"")."
		".$date_query."
		".($parent === 0?" AND $wpdb->comments.comment_parent = 0":"")."
		)
		".$custom_post_type."
		AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')"
	);
	return $count;
}
/* Count custom answers */
function wpqa_count_the_comments($custom_answers = "best_answer",$user_ids = array(),$date = "",$search = "",$author__not_in = array()) {
	global $wpdb;
	$date_query = "";
	if ($custom_answers == "comments") {
		$custom_post_type = "AND $wpdb->posts.post_type = 'post'";
	}else {
		$custom_post_type = "AND ($wpdb->posts.post_type = '".wpqa_questions_type."' OR $wpdb->posts.post_type = '".wpqa_asked_questions_type."')";
	}
	if (is_array($date) && !empty($date)) {
		$date_query = "AND ( ( ";
		$date_query .= "YEAR( $wpdb->comments.comment_date ) = ".(isset($date["year"]) && $date["year"] != ""?$date["year"]:date("Y"));
		if (isset($date["month"]) && $date["month"] != "") {
			$date_query .= " AND MONTH( $wpdb->comments.comment_date ) = ".$date["month"];
		}
		if (isset($date["day"]) && $date["day"] != "") {
			$date_query .= " AND DAY( $wpdb->comments.comment_date ) = ".$date["day"];
		}
		$date_query .= " ) )";
	}
	if ($custom_answers == "best_answer") {
		$custom_answers_query = "AND ($wpdb->commentmeta.meta_key = 'best_answer_comment')";
	}else if ($custom_answers == "asked_question_answers") {
		$custom_answers_query = "AND ($wpdb->commentmeta.meta_key = 'answer_question_user')";
	}
	$author__not_in_query = (is_array($author__not_in) && !empty($author__not_in)?"AND $wpdb->comments.user_id NOT IN (".implode(",",$author__not_in).") AND $wpdb->posts.post_author NOT IN (".implode(",",$author__not_in).")":"");
	if (is_array($user_ids) && !empty($user_ids)) {
		$activate_comments = true;
		if (count($user_ids) == 1) {
			$user_id_implode = implode(",",$user_ids);
			$user_id_query = "AND $wpdb->comments.user_id = ".$user_id_implode;
		}else {
			$user_ids = (is_array($author__not_in) && !empty($author__not_in)?array_merge($user_ids,$author__not_in):$user_ids);
			$user_id_implode = implode(",",$user_ids);
			$user_id_query = "AND $wpdb->comments.user_id IN ( ".$user_id_implode." )";
			$author__not_in_query = (is_array($author__not_in) && !empty($author__not_in)?$user_id_query." AND $wpdb->posts.post_author NOT IN (".implode(",",$author__not_in).")":$user_id_query);
		}
	}
	$count = $wpdb->get_var("SELECT COUNT(ID)
	FROM $wpdb->comments
	".(isset($custom_answers_query)?"JOIN $wpdb->commentmeta ON $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id LEFT":"")."
	JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID
	".($custom_answers == "comments"?"":"LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = 'user_id' )")."
	WHERE ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')
	".$author__not_in_query."
	".(isset($user_id_query)?$user_id_query:"")."
	".$date_query."
	".($custom_answers == "comments"?"":"AND mt1.post_id IS NULL")."
	".$custom_post_type."
	AND $wpdb->comments.comment_approved = '1'
	".($search != ""?"AND ($wpdb->comments.comment_author LIKE '%".$search."%' OR $wpdb->comments.comment_author_email LIKE '%".$search."%' OR $wpdb->comments.comment_author_url LIKE '%".$search."%' OR $wpdb->comments.comment_author_IP LIKE '%".$search."%' OR $wpdb->comments.comment_content LIKE '%".$search."%')":"")."
	".(isset($custom_answers_query)?$custom_answers_query:""));
	return $count;
}
/* Count custom comments */
function wpqa_count_custom_comments($post_type = null,$specific_date = "",$count_custom_posts = "") {
	global $wpdb;
	$post_type = (is_array($post_type)?$post_type:($post_type != ""?$post_type:"post"));
	$custom_post_type = "AND (";
	if (is_array($post_type)) {
		$key = 0;
		foreach ($post_type as $value) {
			if ($key != 0) {
				$custom_post_type .= " OR ";
			}
			$custom_post_type .= "$wpdb->posts.post_type = '$value'";
			$key++;
		}
	}else {
		$custom_post_type .= "$wpdb->posts.post_type = '$post_type'";
	}
	$custom_post_type .= ")";
	$specific_date = ($specific_date != "" && $specific_date != "all"?"AND $wpdb->comments.comment_date > NOW() - INTERVAL ".str_replace("hours","hour",$specific_date):"");
	$count = $wpdb->get_var(
		"SELECT COUNT($wpdb->posts.ID)
		FROM $wpdb->posts
		LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
		LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = 'user_id' )
		".($specific_date != "" && $specific_date != "all"?"LEFT JOIN $wpdb->comments ON ($wpdb->posts.ID = $wpdb->comments.comment_post_ID)":"")."
		WHERE
		( ( $wpdb->postmeta.meta_key = 'count_post_all' AND $wpdb->postmeta.meta_value > 0 )
		AND mt1.post_id IS NULL )
		".($count_custom_posts != ""?"AND $wpdb->posts.ID IN (".$count_custom_posts.")":"")."
		".$specific_date."
		".$custom_post_type."
		AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')"
	);
	return $count;
}
/* Count custom banned comments */
function wpqa_count_custom_banned_comments($post_type = null,$specific_date = "",$count_custom_posts = "",$author__not_in = array()) {
	global $wpdb;
	$post_type = (is_array($post_type)?$post_type:($post_type != ""?$post_type:"post"));
	$custom_post_type = "AND (";
	if (is_array($post_type)) {
		$key = 0;
		foreach ($post_type as $value) {
			if ($key != 0) {
				$custom_post_type .= " OR ";
			}
			$custom_post_type .= "$wpdb->posts.post_type = '$value'";
			$key++;
		}
	}else {
		$custom_post_type .= "$wpdb->posts.post_type = '$post_type'";
	}
	$custom_post_type .= ")";
	$specific_date = ($specific_date != "" && $specific_date != "all"?"AND $wpdb->comments.comment_date > NOW() - INTERVAL ".str_replace("hours","hour",$specific_date):"");
	$author__not_in = (is_array($author__not_in) && !empty($author__not_in)?"AND ($wpdb->comments.user_id IN (".implode(",",$author__not_in).") OR $wpdb->posts.post_author IN (".implode(",",$author__not_in)."))":"");
	$count = $wpdb->get_var(
		"SELECT COUNT($wpdb->posts.ID)
		FROM $wpdb->posts
		LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
		LEFT JOIN $wpdb->postmeta AS mt1 ON ($wpdb->posts.ID = mt1.post_id AND mt1.meta_key = 'user_id' )
		".(($specific_date != "" && $specific_date != "all") || $author__not_in != ""?"LEFT JOIN $wpdb->comments ON ($wpdb->posts.ID = $wpdb->comments.comment_post_ID)":"")."
		WHERE
		( ( $wpdb->postmeta.meta_key = 'count_post_all' AND $wpdb->postmeta.meta_value > 0 )
		AND mt1.post_id IS NULL )
		".($count_custom_posts != ""?"AND $wpdb->posts.ID IN (".$count_custom_posts.")":"")."
		".$specific_date."
		".$author__not_in."
		".$custom_post_type."
		AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')"
	);
	return $count;
}
/* Custom queries */
function wpqa_custom_queries() {
	global $wpdb;
	$array = array("asked_questions_convert","questions","knowledgebases","posts","answers","comments","best_answer","site_best_answer","asked_question_answers","site_asked_question_answers","groups","group_posts","group_comments","notification","activity");
	$wpqa_custom_queries = get_option("wpqa_custom_queries");
	$active_groups = wpqa_options("active_groups");
	$active_message = wpqa_options("active_message");
	$active_notifications = wpqa_options("active_notifications");
	$active_activity_log = wpqa_options("active_activity_log");
	foreach ($array as $value) {
		if (!isset($wpqa_custom_queries["asked_questions_convert"])) {
			$query = array('posts_per_page' => -1,'post_status' => 'any','post_type' => wpqa_questions_type,"meta_query" => array(array("key" => "user_id","compare" => "EXISTS")));
			$items = get_posts($query);
			if (is_array($items) && !empty($items)) {
				foreach ($items as $post) {
					set_post_type($post->ID,wpqa_asked_questions_type);
				}
				$wpqa_custom_queries["asked_questions_convert"] = true;
			}
			update_option("wpqa_custom_queries",$wpqa_custom_queries);
			wp_reset_postdata();
			break;
		}else if (!isset($wpqa_custom_queries["site_best_answer"])) {
			$wpqa_best_answer = get_option("wpqa_best_answer");
			if ($wpqa_best_answer == "") {
				$the_query = new WP_Query(array("post_type" => array(wpqa_questions_type,wpqa_asked_questions_type),"meta_key" => "the_best_answer","nopaging" => true));
				update_option("wpqa_best_answer",$the_query->found_posts);
				$wpqa_custom_queries["site_best_answer"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
				wp_reset_postdata();
			}
			break;
		}else if (!isset($wpqa_custom_queries["site_asked_question_answers"])) {
			$sql = $wpdb->get_var("SELECT COUNT($wpdb->comments.comment_ID)
			FROM $wpdb->comments
			INNER JOIN $wpdb->commentmeta ON ( $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
			WHERE $wpdb->comments.comment_approved = 1
			AND $wpdb->commentmeta.meta_key = 'answer_question_user'");
			update_option("wpqa_count_site_asked_question_answers",$sql);
			$wpqa_custom_queries["site_asked_question_answers"] = true;
			update_option("wpqa_custom_queries",$wpqa_custom_queries);
			break;
		}else if (!isset($wpqa_custom_queries["best_answer"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			(SELECT user_id, %s as meta_key, COUNT($wpdb->comments.comment_ID) as meta_value
				FROM $wpdb->comments
				INNER JOIN $wpdb->commentmeta ON ( $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
				WHERE $wpdb->comments.comment_approved = 1
				AND $wpdb->commentmeta.meta_key = 'best_answer_comment'
				GROUP BY user_id)", "wpqa_count_best_answers");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["best_answer"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if (!isset($wpqa_custom_queries["asked_question_answers"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			(SELECT user_id, %s as meta_key, COUNT($wpdb->comments.comment_ID) as meta_value
				FROM $wpdb->comments
				INNER JOIN $wpdb->commentmeta ON ( $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
				WHERE $wpdb->comments.comment_approved = 1
				AND $wpdb->commentmeta.meta_key = 'answer_question_user'
				GROUP BY user_id)", "wpqa_count_asked_question_answers");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["asked_question_answers"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if (!isset($wpqa_custom_queries["questions"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT post_author as user_id, %s as meta_key, COUNT(*) as meta_value
			FROM $wpdb->posts
			WHERE (
				post_type = '".wpqa_questions_type."'
				AND ( post_status = 'publish' OR post_status = 'private' )
			)
			GROUP BY post_author", "wpqa_questions_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["questions"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if (!isset($wpqa_custom_queries["knowledgebases"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT post_author as user_id, %s as meta_key, COUNT(*) as meta_value
			FROM $wpdb->posts
			WHERE (
				post_type = '".wpqa_knowledgebase_type."'
				AND ( post_status = 'publish' OR post_status = 'private' )
			)
			GROUP BY post_author", "wpqa_knowledgebases_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["knowledgebases"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if (!isset($wpqa_custom_queries["posts"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT post_author as user_id, %s as meta_key, COUNT(*) as meta_value
			FROM $wpdb->posts
			WHERE (
				post_type = 'post'
				AND ( post_status = 'publish' OR post_status = 'private' )
			)
			GROUP BY post_author", "wpqa_posts_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["posts"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if ($active_notifications == "on" && !isset($wpqa_custom_queries["notification"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT post_author as user_id, %s as meta_key, COUNT(*) as meta_value
			FROM $wpdb->posts
			WHERE (
				post_type = 'notification'
				AND ( post_status = 'publish' OR post_status = 'private' )
			)
			GROUP BY post_author", "wpqa_notification_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["notification"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if ($active_activity_log == "on" && !isset($wpqa_custom_queries["activity"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT post_author as user_id, %s as meta_key, COUNT(*) as meta_value
			FROM $wpdb->posts
			WHERE (
				post_type = 'activity'
				AND ( post_status = 'publish' OR post_status = 'private' )
			)
			GROUP BY post_author", "wpqa_activity_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["activity"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if ($active_groups == "on" && !isset($wpqa_custom_queries["groups"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT post_author as user_id, %s as meta_key, COUNT(*) as meta_value
			FROM $wpdb->posts
			WHERE (
				post_type = 'group'
				AND ( post_status = 'publish' OR post_status = 'private' )
			)
			GROUP BY post_author", "wpqa_groups_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["groups"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if ($active_groups == "on" && !isset($wpqa_custom_queries["group_posts"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT post_author as user_id, %s as meta_key, COUNT(*) as meta_value
			FROM $wpdb->posts
			WHERE (
				post_type = 'posts'
				AND ( post_status = 'publish' OR post_status = 'private' )
			)
			GROUP BY post_author", "wpqa_group_posts_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["group_posts"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if (!isset($wpqa_custom_queries["answers"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT user_id, %s as `meta_key`, COUNT(*) as `meta_value`
			FROM $wpdb->comments
			INNER JOIN $wpdb->posts ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID )
			WHERE ($wpdb->posts.post_type = '".wpqa_questions_type."' OR $wpdb->posts.post_type = '".wpqa_asked_questions_type."')
			AND $wpdb->comments.comment_approved = 1
			AND ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private' )
			GROUP BY user_id", "wpqa_answers_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["answers"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if (!isset($wpqa_custom_queries["comments"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT user_id, %s as `meta_key`, COUNT(*) as `meta_value`
			FROM $wpdb->comments
			INNER JOIN $wpdb->posts ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID )
			WHERE $wpdb->posts.post_type = 'post'
			AND $wpdb->comments.comment_approved = 1
			AND ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private' )
			GROUP BY user_id", "wpqa_comments_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["comments"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}else if ($active_groups == "on" && !isset($wpqa_custom_queries["group_comments"])) {
			$sql = $wpdb->prepare("INSERT INTO $wpdb->usermeta (`user_id`, `meta_key`, `meta_value`)
			SELECT user_id, %s as `meta_key`, COUNT(*) as `meta_value`
			FROM $wpdb->comments
			INNER JOIN $wpdb->posts ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID )
			WHERE $wpdb->posts.post_type = 'posts'
			AND $wpdb->comments.comment_approved = 1
			AND ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private' )
			GROUP BY user_id", "wpqa_group_comments_count");
			if ($wpdb->query($sql)) {
				$wpqa_custom_queries["group_comments"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
			}
			break;
		}
	}
}
add_action("wpqa_init","wpqa_play_custom_queries");
function wpqa_play_custom_queries() {
	$wpqa_custom_queries = get_option("wpqa_custom_queries");
	$active_groups = wpqa_options("active_groups");
	$active_message = wpqa_options("active_message");
	$active_notifications = wpqa_options("active_notifications");
	$active_activity_log = wpqa_options("active_activity_log");
	if (!isset($wpqa_custom_queries["asked_questions_convert"]) || !isset($wpqa_custom_queries["asked_question_answers"]) || !isset($wpqa_custom_queries["site_asked_question_answers"]) || !isset($wpqa_custom_queries["best_answer"]) || !isset($wpqa_custom_queries["questions"]) || !isset($wpqa_custom_queries["posts"]) || !isset($wpqa_custom_queries["answers"]) || !isset($wpqa_custom_queries["comments"]) || ($active_groups == "on" && !isset($wpqa_custom_queries["groups"])) || ($active_groups == "on" && !isset($wpqa_custom_queries["group_posts"])) || ($active_groups == "on" && !isset($wpqa_custom_queries["group_comments"])) || ($active_notifications == "on" && !isset($wpqa_custom_queries["notification"])) || ($active_activity_log == "on" && !isset($wpqa_custom_queries["activity"]))) {
		wpqa_custom_queries();
	}
}
/* Fix the counts */
function wpqa_fix_counts($user_id = 0) {
	global $wpdb;
	$array = array(wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type,"post","group","posts","notification","activity","answers","comments","best_answer","group_comments","asked_question_answers","new_message");
	$active_groups = wpqa_options("active_groups");
	$active_message = wpqa_options("active_message");
	$active_notifications = wpqa_options("active_notifications");
	$active_activity_log = wpqa_options("active_activity_log");
	foreach ($array as $value) {
		if ($value == "asked_question_answers") {
			$sql = $wpdb->get_var("SELECT COUNT($wpdb->comments.comment_ID) FROM $wpdb->comments INNER JOIN $wpdb->commentmeta ON ( $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id) WHERE $wpdb->comments.comment_approved = 1 AND $wpdb->commentmeta.meta_key = 'answer_question_user'".($user_id > 0?" AND $wpdb->comments.user_id = $user_id":""));
			if ($user_id > 0) {
				update_user_meta($user_id,"wpqa_count_asked_question_answers",$sql);
			}
		}else if ($value == "best_answer") {
			$sql = $wpdb->get_var("SELECT COUNT($wpdb->comments.comment_ID) FROM $wpdb->comments INNER JOIN $wpdb->commentmeta ON ( $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id) WHERE $wpdb->comments.comment_approved = 1 AND $wpdb->commentmeta.meta_key = 'best_answer_comment'".($user_id > 0?" AND $wpdb->comments.user_id = $user_id":""));
			if ($user_id > 0) {
				update_user_meta($user_id,"wpqa_count_best_answers",$sql);
			}
		}else if ($value == wpqa_questions_type || $value == wpqa_asked_questions_type || $value == wpqa_knowledgebase_type || $value == "post" || ($active_groups == "on" && $value == "group") || ($active_groups == "on" && $value == "posts") || ($active_notifications == "on" && $value == "notification") || ($active_activity_log == "on" && $value == "activity")) {
			$post_type = $value;
			$post_types = "post_type = '$post_type'";
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
				$post_types = "(post_type = '".wpqa_questions_type."' OR post_type = '".wpqa_asked_questions_type."')";
			}
			$sql = $wpdb->get_var("SELECT COUNT($wpdb->posts.ID) FROM $wpdb->posts WHERE $post_types AND ( post_status = 'publish' OR post_status = 'private' )".($user_id > 0?" AND $wpdb->posts.post_author = $user_id":""));
			if ($user_id > 0) {
				if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
					$meta = "wpqa_questions_count";
				}else if ($post_type == wpqa_knowledgebase_type) {
					$meta = "wpqa_knowledgebases_count";
				}else if ($post_type == "group") {
					$meta = "wpqa_groups_count";
				}else if ($post_type == "posts") {
					$meta = "wpqa_group_posts_count";
				}else if ($post_type == "notification") {
					$meta = "wpqa_notification_count";
				}else if ($post_type == "activity") {
					$meta = "wpqa_activity_count";
				}else {
					$meta = "wpqa_posts_count";
				}
				update_user_meta($user_id,$meta,$sql);
			}
		}else if ($value == "answers" || $value == "comments" || ($active_groups == "on" && $value == "group_comments")) {
			if ($value == "answers") {
				$post_type = wpqa_questions_type;
				$custom_post_type = "($wpdb->posts.post_type = '$post_type' OR $wpdb->posts.post_type = '".wpqa_asked_questions_type."')";
			}else if ($value == "comments") {
				$post_type = "post";
				$custom_post_type = "$wpdb->posts.post_type = '$post_type'";
			}else {
				$post_type = "posts";
				$custom_post_type = "$wpdb->posts.post_type = '$post_type'";
			}
			$sql = $wpdb->get_var("SELECT COUNT($wpdb->posts.ID) FROM $wpdb->posts INNER JOIN $wpdb->comments ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID ) WHERE ".$custom_post_type." AND ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private' ) AND $wpdb->comments.comment_approved = 1".($user_id > 0?" AND $wpdb->comments.user_id = $user_id":""));
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
				$meta = "wpqa_answers_count";
			}else if ($post_type == "posts") {
				$meta = "wpqa_group_comments_count";
			}else {
				$meta = "wpqa_comments_count";
			}
			if ($user_id > 0) {
				update_user_meta($user_id,$meta,$sql);
			}
		}else if ($value == "new_message") {
			$args = array(
				"post_type"   => "message",
				"post_status" => "publish",
				"meta_query"  => array(
					"relation" => "AND",
					array("key" => "delete_inbox_message","compare" => "NOT EXISTS"),
					array("key" => "message_user_id","compare" => "=","value" => $user_id),
					array(
						"relation" => "OR",
						array("key" => "message_new","compare" => "=","value" => 1),
						array("key" => "message_new","compare" => "=","value" => "on")
					)
				)
			);
			$the_query = new WP_Query($args);
			$count = $the_query->found_posts;
			wp_reset_postdata();
			update_user_meta($user_id,"wpqa_new_messages_count",$count);
		}
	}
}
/* Pull all the groups into an array */
function wpqa_options_roles() {
	$options_groups = array();
	global $wp_roles;
	$options_groups_obj = $wp_roles->roles;
	foreach ($options_groups_obj as $key_r => $value_r) {
		if ($key_r != "wpqa_under_review" && $key_r != "activation" && $key_r != "ban_group") {
			$options_groups[$key_r] = $value_r['name'];
		}
	}
	return $options_groups;
}
/* Get gender class */
function wpqa_get_gender_class($user_id,$post_id = 0,$comment_id = 0) {
	$activate_male_female = apply_filters("wpqa_activate_male_female",false);
	if ($activate_male_female == true) {
		$return = 'other-user';
		if ($post_id > 0) {
			$gender = get_post_meta($post_id,'wpqa_post_gender',true);
		}else if ($comment_id > 0) {
			$gender = get_comment_meta($comment_id,'wpqa_comment_gender',true);
		}
		if (!isset($gender) || (isset($gender) && $gender == "")) {
			$gender = get_user_meta($user_id,'gender',true);
			if ($gender != "") {
				if ($post_id > 0) {
					update_post_meta($post_id,'wpqa_post_gender',$gender);
				}else if ($comment_id > 0) {
					update_comment_meta($comment_id,'wpqa_comment_gender',$gender);
				}
			}
		}
		if (isset($gender)) {
			if ($gender == 'male' || $gender == 1) {
				$return = 'him-user';
			}else if ($gender == 'female' || $gender == 2) {
				$return = 'her-user';
			}else if ($gender == '' || $gender == 'other' || $gender == 3) {
				$return = 'other-user';
			}
		}
	}
	return (isset($return)?$return:"");
}
/* Roles array */
function wpqa_roles_array() {
	$roles_array = array(
		"ask_question"           => esc_html__("Select ON to allow users to ask questions.","wpqa"),
		"ask_question_payment"   => esc_html__("Select ON to allow users to ask a question without payment.","wpqa"),
		"show_question"          => esc_html__("Select ON to allow users to view questions.","wpqa"),
		"approve_question"       => esc_html__("Select ON to auto approve the questions for the user.","wpqa"),
		"approve_question_media" => esc_html__("Select ON to auto approve the questions for the user when media has been attached.","wpqa"),
		"add_category"           => esc_html__("Select ON to allow users to add category.","wpqa"),
		"add_answer"             => esc_html__("Select ON to allow users to add an answer.","wpqa"),
		"add_answer_payment"     => esc_html__("Select ON to allow users to add an answer without payment.","wpqa"),
		"show_answer"            => esc_html__("Select ON to allow users to view answers.","wpqa"),
		"approve_answer"         => esc_html__("Select ON to auto approve the answers for the user.","wpqa"),
		"approve_answer_media"   => esc_html__("Select ON to auto approve the answers for the user when media has been attached.","wpqa"),
		"add_group"              => esc_html__("Select ON to allow users to create a group.","wpqa"),
		"add_group_payment"      => esc_html__("Select ON to allow users to create group without payment.","wpqa"),
		"approve_group"          => esc_html__("Select ON to auto approve the group for the user.","wpqa"),
		"edit_other_groups"      => esc_html__("Select ON to allow users to edit any group with full editing.","wpqa"),
		"add_post"               => esc_html__("Select ON to allow users to add post.","wpqa"),
		"add_post_payment"       => esc_html__("Select ON to allow users to add post without payment.","wpqa"),
		"show_post"              => esc_html__("Select ON to allow users to view posts.","wpqa"),
		"approve_post"           => esc_html__("Select ON to auto approve the posts for the user.","wpqa"),
		"show_comment"           => esc_html__("Select ON to allow users to view comments.","wpqa"),
		"approve_comment"        => esc_html__("Select ON to auto approve the comments for the user.","wpqa"),
		"add_comment"            => esc_html__("Select ON to allow users to add a comment.","wpqa"),
		"send_message"           => esc_html__("Select ON to allow users to send message.","wpqa"),
		"upload_files"           => esc_html__("Select ON to allow users to be able to upload files.","wpqa"),
		"without_ads"            => esc_html__("Select ON to remove ads for the user.","wpqa"),
	);
	$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);
	if ($activate_knowledgebase == true) {
		$roles_array["show_knowledgebase"] = esc_html__("Select ON to allow users to view articles.","wpqa");
	}
	return $roles_array;
}
/* The default meta for posts and questions */
add_action('wpqa_finished_add_post','wpqa_add_meta_posts');
add_action('wpqa_finished_add_question','wpqa_add_meta_posts');
add_action('wpqa_finished_add_user_question','wpqa_add_meta_posts');
add_action('wpqa_finished_add_posts','wpqa_add_meta_posts');
function wpqa_add_meta_posts($post_id) {
	update_post_meta($post_id,"count_post_all",0);
	update_post_meta($post_id,"count_post_comments",0);
	update_post_meta($post_id,"male_comment_count",0);
	update_post_meta($post_id,"male_count_comments",0);
	update_post_meta($post_id,"female_comment_count",0);
	update_post_meta($post_id,"female_count_comments",0);
	update_post_meta($post_id,"other_comment_count",0);
	update_post_meta($post_id,"other_count_comments",0);
	$post_meta_stats = wpqa_get_meta_stats();
	update_post_meta($post_id,$post_meta_stats,0);
}
add_action('wpqa_finished_add_post','wpqa_add_meta_post_type',1,4);
add_action('wpqa_finished_add_question','wpqa_add_meta_post_type',1,4);
add_action('wpqa_finished_add_user_question','wpqa_add_meta_post_type',1,4);
add_action('wpqa_finished_add_group','wpqa_add_meta_post_type',1,4);
add_action('wpqa_finished_add_posts','wpqa_add_meta_post_type',1,4);
add_action('wpqa_finished_send_message','wpqa_add_meta_post_type',1,4);
function wpqa_add_meta_post_type($post_id,$posted,$type,$post_type) {
	$user_id = get_current_user_id();
	$gender = get_user_meta($user_id,'gender',true);
	$display_name = get_the_author_meta('display_name',$user_id);
	if ($gender != "") {
		update_post_meta($post_id,"wpqa_post_gender",$gender);
	}
	if ($display_name != "") {
		update_post_meta($post_id,"wpqa_post_author",$display_name);
	}
	update_post_meta($post_id,"wpqa_post_type",$post_type);
	update_post_meta($post_id,"post_from_front","from_front");
	wpqa_ip_address($post_id);
}
/* Save IP address */
function wpqa_ip_address($post_id = 0,$user_id = 0) {
	$save_ip_address = wpqa_options("save_ip_address");
	if ($save_ip_address == "on") {
		if (!empty($_SERVER['REMOTE_ADDR']) && rest_is_ip_address(wp_unslash($_SERVER['REMOTE_ADDR']))) {
			$user_ip_address = wp_unslash($_SERVER['REMOTE_ADDR']);
		}else {
			$user_ip_address = '127.0.0.1';
		}
		$user_ip_address = preg_replace('/[^0-9a-fA-F:., ]/','',$user_ip_address);
		if ($user_id > 0) {
			update_user_meta($user_id,"wpqa_ip_address",$user_ip_address);
		}else {
			update_post_meta($post_id,"wpqa_ip_address",$user_ip_address);
		}

		$user_agent = (isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'');
		$user_agent = substr($user_agent,0,254);
		if ($user_id > 0) {
			update_user_meta($user_id,"wpqa_user_agent",$user_agent);
		}else {
			update_post_meta($post_id,"wpqa_user_agent",$user_agent);
		}
	}
}
/* Base encode */
function wpqa_base_encode($value) {
	return base64_encode($value);
}
/* Base decode */
function wpqa_base_decode($value) {
	return base64_decode($value);
}
/* Backup settings */
add_action(wpqa_prefix_theme."_options_page","wpqa_backup_settings");
function wpqa_backup_settings() {
	if (isset($_GET['backup']) && $_GET['backup'] == 'settings') {
		$json = "backup-".date('Y-m-d',time()).".txt";
		$file = fopen($json,'w');
		$export = array(wpqa_prefix_theme."_options");
		$current_options = array();
		foreach ($export as $option) {
			$get_option_ = get_option($option);
			if ($get_option_) {
				$current_options[$option] = $get_option_;
			}else {
				$current_options[$option] = array();
			}
		}
		$array_json = json_encode($current_options);
		$array_json = base64_encode($array_json);
		fwrite($file,$array_json);
		fclose($file);
		header('Content-disposition: attachment; filename='.$json);
		header('Content-type: text/xml');
		ob_clean();
		flush();
		readfile($json);
		exit();
	}
}
/* Remove the slugs */
function wpqa_get_current_url() {
	$REQUEST_URI = strtok($_SERVER['REQUEST_URI'],'?');
	$real_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')?'https://':'http://';
	$real_url .= $_SERVER['SERVER_NAME'].$REQUEST_URI;
	return $real_url;
}
add_action('template_redirect','wpqa_auto_redirect_old');
function wpqa_auto_redirect_old() {
	$remove_question_slug = wpqa_options("remove_question_slug");
	$remove_asked_question_slug = wpqa_options("remove_asked_question_slug");
	$remove_group_slug = wpqa_options("remove_group_slug");
	$remove_knowledgebase_slug = wpqa_options("remove_knowledgebase_slug");
	if ($remove_question_slug == "on" || $remove_asked_question_slug == "on" || $remove_group_slug == "on") {
		global $post;
		if (!is_preview() && is_single() && is_object($post) && (($remove_question_slug == "on" && $post->post_type == wpqa_questions_type) || ($remove_asked_question_slug == "on" && $post->post_type == wpqa_asked_questions_type) || ($remove_group_slug == "on" && $post->post_type == 'group') || ($remove_knowledgebase_slug == "on" && $post->post_type == wpqa_knowledgebase_type))) {
			$new_url = get_permalink();
			$real_url = wpqa_get_current_url();
			if (substr_count($new_url,'/') != substr_count($real_url,'/') && strstr($real_url,$new_url) == false) {
				wp_redirect($new_url,301);
				die();
			}
		}
	}
}
/* Get meta */
function wpqa_get_meta($meta,$post_id = 0,$comment_id = 0,$term_id = 0,$user_id = 0) {
	if ($comment_id > 0) {
		$value = get_comment_meta($comment_id,$meta,true);
	}else if ($post_id > 0) {
		$value = get_post_meta($post_id,$meta,true);
	}else if ($term_id > 0) {
		$value = get_term_meta($term_id,$meta,true);
	}else if ($user_id > 0) {
		$value = get_user_meta($user_id,$meta,true);
	}
	if (isset($value)) {
		return $value;
	}
}
/* Add meta */
function wpqa_add_meta($meta,$value,$post_id = 0,$comment_id = 0,$term_id = 0,$user_id = 0) {
	if ($comment_id > 0) {
		add_comment_meta($comment_id,$meta,$value);
	}else if ($post_id > 0) {
		add_post_meta($post_id,$meta,$value);
	}else if ($term_id > 0) {
		add_term_meta($term_id,$meta,$value);
	}else if ($user_id > 0) {
		add_user_meta($user_id,$meta,$value);
	}
}
/* Update meta */
function wpqa_update_meta($meta,$value,$post_id = 0,$comment_id = 0,$term_id = 0,$user_id = 0) {
	if ($comment_id > 0) {
		update_comment_meta($comment_id,$meta,$value);
	}else if ($post_id > 0) {
		update_post_meta($post_id,$meta,$value);
	}else if ($term_id > 0) {
		update_term_meta($term_id,$meta,$value);
	}else if ($user_id > 0) {
		update_user_meta($user_id,$meta,$value);
	}
}
/* Delete meta */
function wpqa_delete_meta($meta,$post_id = 0,$comment_id = 0,$term_id = 0,$user_id = 0) {
	if ($comment_id > 0) {
		delete_comment_meta($comment_id,$meta);
	}else if ($post_id > 0) {
		delete_post_meta($post_id,$meta);
	}else if ($term_id > 0) {
		delete_term_meta($term_id,$meta);
	}else if ($user_id > 0) {
		delete_user_meta($user_id,$meta);
	}
}
/* Select menu */
function wpqa_display_select_options(array $options,$placeholder = null,$selected = null) {
	if (empty($placeholder) === false) {?>
		<option value=""><?php echo esc_html($placeholder);?></option>
	<?php }
	foreach ($options as $key => $value) {
		$is_selected = !empty($selected) && $selected == $key;?>
		<option <?php echo esc_attr($is_selected?'selected':'');?> value="<?php echo esc_attr($key);?>"><?php echo esc_html($value);?></option>
	<?php }
}
/* wpqa_post_meta */
if (!function_exists('wpqa_post_meta')):
	function wpqa_post_meta($key,$post_id = null,$prefix = true,$default = false) {
		if (!$post_id) {
			$post_id = get_the_ID();
		}
		
		$value = get_post_meta($post_id,($prefix == true?prefix_meta:"").$key,true);
		
		if ('' !== $value && array() !== $value) {
			return $value;
		}else if ($default) {
			return $default;
		}
		
		return false;
	}
endif;
/* wpqa_term_meta */
if (!function_exists('wpqa_term_meta')):
	function wpqa_term_meta($key,$term_id = null,$prefix = true,$default = false) {
		$value = get_term_meta($term_id,($prefix == true?prefix_terms:"").$key,true);
		
		if ('' !== $value && array() !== $value) {
			return $value;
		}else if ($default) {
			return $default;
		}
		
		return false;
	}
endif;
/* InstaScrape */
class wpqa_InstaScrape {
	public $sessionid;
	
	function __construct($sessionid) {
		$this->sessionid = $sessionid;	
	}
	/**
	* Get instagram pics for a specific user using his numeric ID
	* 
	* @param string $usrID : the user id
	* @param number $index : the start index of reurned items (id of the first item) by default starts from the first image
	* 
	* @return: array of items 
	*/
	function getUserItems($usrID,$itemsCount = 12) {
		$response = wp_remote_get("https://www.instagram.com/graphql/query/?query_id=17880160963012870&id=$usrID&first=$itemsCount",array("sessionid" => $this->sessionid,'csrftoken' => 'eqYUPd3nV0gDSWw43IYZjydziMndrn4l'));
		
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			throw new Exception("Something went wrong: $error_message");
		}else {
			$json = json_decode($response['body'],true);
			return $json;
		}
	}
	/**
	* Get Instagram pics by a specific hashtag
	* 
	* @param string $hashTag Instagram Hashtag
	* @param integer $itemsCount Number of items to return
	* @param string $index Last cursor from a previous request for the same hashtag 
	*/
	function getItemsByHashtag($hashTag,$itemsCount = 12) {
		$url = "https://www.instagram.com/graphql/query/?query_id=17882293912014529&tag_name=". urlencode(trim($hashTag)) ."&first=".$itemsCount;
		$response = wp_remote_get($url,array("sessionid" => $this->sessionid,'csrftoken' => 'eqYUPd3nV0gDSWw43IYZjydziMndrn4l'));
		
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			throw new Exception("Something went wrong: $error_message");
		}else {
			$json = json_decode($response['body'],true);
			return $json;
		}
	}
	/**
	* @param string $name the name of instagram user for example "cnn"
	* @return: numeric id of the user
	*/
	function getUserIDFromName($name) {
		$url = 'https://www.instagram.com/'.trim($name).'/?__a=1';
		$result = wp_remote_get($url);
		if (is_wp_error($result)) {
			$error_message = $result->get_error_message();
			throw new Exception("Something went wrong: $error_message");
		}else {
			$json = json_decode($result['body'],true);
			// Extract the id
			$possibleID = str_replace('profilePage_','',$json["logging_page_id"]);
			
			// Validate extracted id
			if (!is_numeric($possibleID) || trim($possibleID) == '') {
				throw new Exception('Can not extract the id from instagram page'.$x);
			}
		}
		// Return ID
		return $possibleID;
	}
	/**
	* 
	* @param string $itmID id of the item for example "BGUTAhbtLrA" for https://www.instagram.com/p/BGUTAhbtLrA/
	*/
	function getItemByID($itmID) {
		// Preparing uri
		$url = "https://www.instagram.com/p/".trim($itmID)."/?__a=1";
		$response = wp_remote_get($url,array("sessionid" => $this->sessionid,'csrftoken' => 'eqYUPd3nV0gDSWw43IYZjydziMndrn4l'));
		
		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			throw new Exception("Something went wrong: $error_message");
		}else {
			$json = json_decode($response['body'],true);
			return $json;
		}
	}
	/**
	* @param string $name username of the user for example "cnn"
	* @throws Exception
	* @return integer count of the followers
	*/
	function getFollowersCountFromName($name) {
		$url = 'https://www.instagram.com/'.trim($name);
		$result = wp_remote_get($url,array("sessionid" => $this->sessionid,'csrftoken' => 'eqYUPd3nV0gDSWw43IYZjydziMndrn4l'));
		
		if (is_wp_error($result)) {
			$error_message = $result->get_error_message();
			throw new Exception("Something went wrong: $error_message");
		}else {
			$json = json_encode($result['body'],true);
			preg_match('/edge_followed_by":{"count":(.*?)}/', stripslashes($json),$matchs);
			$count = (isset($matchs[1])?$matchs[1]:0);
			// Validate extracted id
			if (!is_numeric($count) || trim($count) == '') {
				throw new Exception('Can not extract the id from instagram page');
			}
		}
		// Return number
		return $count;
	}
}
/* wpqa_counter_facebook */
function wpqa_counter_facebook($page_id) {
	$count = get_transient('wpqa_facebook_followers');
	if ($count !== false) return $count;
	$count = 0;
	$get_request = wp_remote_get("https://www.facebook.com/plugins/likebox.php?href=https://facebook.com/".$page_id."&show_faces=true&header=false&stream=false&show_border=false&locale=en_US",array('timeout' => 20));
	$the_request = wp_remote_retrieve_body($get_request);
	$pattern = '/_1drq[^>]+>(.*?)<\/div/s';
	preg_match($pattern,$the_request,$matches);
	if (!empty($matches[1])) {
		$number = strip_tags($matches[1]);
		preg_match('!\d+!',$number,$matches);
		if (!empty($matches[0])) {
			$count .= $matches[0];
			set_transient('wpqa_facebook_followers', $count, 60*60*24);
		}
	}
	return $count;
}
/* get_twitter_count */
function wpqa_api_credentials($credentials) {
	$data = 'edocnexzyesab';
	$data = str_replace('xzy','_'.(153-107),$data);
	$data = strrev( $data );
	return $data(wpqa_remove_spaces($credentials));
}
function wpqa_remove_spaces($string) {
	return preg_replace('/\s+/','',$string);
}
function wpqa_counter_twitter($twitter_username) {
	$count = get_transient('wpqa_twitter_followers');
	if ($count !== false) return $count;
	
	$count           = 0;
	$access_token    = get_option('wpqa_twitter_token');
	$consumer_key    = wpqa_options('twitter_consumer_key');
	$consumer_secret = wpqa_options('twitter_consumer_secret');
	if ($access_token == "") {
		$credentials = $consumer_key . ':' . $consumer_secret;
		$toSend 	 = wpqa_api_credentials($credentials);
		
		$args = array(
			'method'      => 'POST',
			'httpversion' => '1.1',
			'blocking' 		=> true,
			'headers' 		=> array(
				'Authorization' => 'Basic ' . $toSend,
				'Content-Type' 	=> 'application/x-www-form-urlencoded;charset=UTF-8'
			),
			'body' 				=> array('grant_type' => 'client_credentials')
		);
		
		add_filter('https_ssl_verify', '__return_false');
		$response = wp_remote_post('https://api.twitter.com/oauth2/token', $args);
		
		$keys = json_decode(wp_remote_retrieve_body($response));
		
		if ( !empty($keys->access_token) ) {
			update_option('wpqa_twitter_token', $keys->access_token);
			$access_token = $keys->access_token;
		}
	}

	$args = array(
		'httpversion' => '1.1',
		'blocking' 		=> true,
		'timeout'     => 10,
		'headers'     => array('Authorization' => "Bearer $access_token")
	);
	
	add_filter('https_ssl_verify', '__return_false');
	$api_url  = "https://api.twitter.com/1.1/users/show.json?screen_name=$twitter_username";
	
	$get_request = wp_remote_get( $api_url , $args );
	$request = wp_remote_retrieve_body( $get_request );
	$request = @json_decode( $request , true );
	
	if ( isset($request['followers_count']) && !empty( $request['followers_count'] ) ) {
		$count = $request['followers_count'];
	}
	set_transient('wpqa_twitter_followers', $count, 60*60*24);
	return $count;
}
/* Get access token for dribbble */
add_filter('framework_hidden_'.wpqa_prefix_theme.'_options[dribbble_access_token]','wpqa_api_dribbble');
function wpqa_api_dribbble($val) {
	$dribbble_client_id = wpqa_options('dribbble_client_id');
	$dribbble_client_secret = wpqa_options('dribbble_client_secret');
	if (isset($_GET["api"]) && $_GET["api"] == "dribbble" && isset($_GET["code"]) && $_GET["code"] != "") {
		$data = wp_remote_post('https://dribbble.com/oauth/token/?client_id='.$dribbble_client_id.'&client_secret='.$dribbble_client_secret.'&code='.esc_html($_GET["code"]));
		if (!is_wp_error($data)) {
			$json = json_decode($data['body'],true);
			if (isset($json["access_token"])) {
				$wpqa_options = get_option(wpqa_options);
				$val = esc_html($json["access_token"]);
				$wpqa_options["dribbble_access_token"] = $val;
				update_option(wpqa_options,$wpqa_options);
				wp_redirect(admin_url('admin.php?page=options'));
				exit;
			}
		}
	}
	return $val;
}
/* wpqa_counter_dribbble */
function wpqa_counter_dribbble($dribbble, $return = 'count') {
	$count = get_transient('wpqa_dribbble_followers');
	$link = get_transient('wpqa_dribbble_page_url');
	$access_token = wpqa_options('dribbble_access_token');
	if ($return == 'link') {
		if ($link !== false) return $link;
	}else {
		if ($count !== false) return $count;
	}
	$count = 0;
	$link = '';
	$data = wp_remote_get('https://api.dribbble.com/v2/user/?access_token='.$access_token);
	if (!is_wp_error($data)) {
		$json = json_decode($data['body'],true);
		$count = (isset($json['followers_count'])?intval($json['followers_count']):0);
		$link = (isset($json['html_url'])?intval($json['html_url']):"");
		set_transient('wpqa_dribbble_followers', $count, 60*60*24);
		set_transient('wpqa_dribbble_page_url', $link, 60*60*24);
	}
	if ($return == 'link') {
		return $link;
	}else {
		return $count;
	}
}
/* wpqa_counter_youtube */
function wpqa_counter_youtube($youtube, $return = 'count') {
	$count = get_transient('wpqa_youtube_followers');
	$api_key = wpqa_options('google_api');
	if ($count !== false) return $count;
	$count = 0;
	$data = wp_remote_get('https://www.googleapis.com/youtube/v3/channels?part=statistics&id='.$youtube.'&key='.$api_key);
	if (!is_wp_error($data)) {
		$json = json_decode($data['body'],true);
		$count = (isset($json['items'][0]['statistics']['subscriberCount'])?intval($json['items'][0]['statistics']['subscriberCount']):0);
		set_transient('wpqa_youtube_followers', $count, 60*60*24);
	}
	return $count;
}
/* wpqa_counter_vimeo */
function wpqa_counter_vimeo($vimeo, $return = 'count') {
	$count = get_transient('wpqa_vimeo_followers');
	$link = get_transient('wpqa_vimeo_page_url');
	if ($return == 'link') {
		if ($link !== false) return $link;
	}else {
		if ($count !== false) return $count;
	}
	$count = 0;
	$link = '';
	$data = wp_remote_get('https://vimeo.com/api/v2/channel/'.$vimeo.'/info.json');
	if (!is_wp_error($data)) {
		$json = json_decode($data['body'],true);
		$count = (isset($json['total_subscribers'])?intval($json['total_subscribers']):0);
		$link = (isset($json['url'])?$json['url']:"");
		set_transient('wpqa_vimeo_followers', $count, 60*60*24);
		set_transient('wpqa_vimeo_page_url', $link, 60*60*24);
	}
	if ($return == 'link') {
		return $link;
	}else {
		return $count;
	}
}
/* wpqa_counter_soundcloud */
function wpqa_counter_soundcloud($soundcloud, $return = 'count') {
	$count = get_transient('wpqa_soundcloud_followers');
	$link = get_transient('wpqa_soundcloud_page_url');
	if ($return == 'link') {
		if ($link !== false) return $link;
	}else {
		if ($count !== false) return $count;
	}
	$count = 0;
	$link = '';
	$client_id = wpqa_options('soundcloud_client_id');
	if ($client_id != '') {
		$data = wp_remote_get('https://api.soundcloud.com/users/'.$soundcloud.'.json?client_id='.$client_id);
		if (!is_wp_error($data)) {
			$json = json_decode($data['body'],true);
			$count = (isset($json['followers_count'])?intval($json['followers_count']):0);
			$link = (isset($json['permalink_url'])?$json['permalink_url']:'');
			set_transient('wpqa_soundcloud_followers', $count, 60*60*24);
			set_transient('wpqa_soundcloud_page_url', $link, 60*60*24);
		}
	}
	if ($return == 'link') {
		return $link;
	}else {
		return $count;
	}
}
/* wpqa_counter_behance */
function wpqa_counter_behance($behance, $return = 'count') {
	$count = get_transient('wpqa_behance_followers');
	$link = get_transient('wpqa_behance_page_url');
	if ($return == 'link') {
		if ($link !== false) return $link;
	}else {
		if ($count !== false) return $count;
	}
	$count = 0;
	$link = '';
	$api_key = wpqa_options('behance_api_key');
	if ($api_key != '') {
		$data = wp_remote_get('https://www.behance.net/v2/users/'.$behance.'?api_key='.$api_key);
		if (!is_wp_error($data)) {
			$json = json_decode($data['body'],true);
			$count = intval($json['user']['stats']['followers']);
			$link = $json['user']['url'];
			set_transient('wpqa_behance_followers', $count, 60*60*24);
			set_transient('wpqa_behance_page_url', $link, 60*60*24);
		}
	}
	if ($return == 'link') {
		return $link;
	}else {
		return $count;
	}
}
/* wpqa_counter_instagram */
function wpqa_counter_instagram($instagram, $return = 'count') {
	$count = get_transient('wpqa_instagram_followers');
	$link = get_transient('wpqa_instagram_page_url');
	if ($return == 'link') {
		if ($link !== false) return $link;
	}else {
		if ($count !== false) return $count;
	}
	$count = 0;
	$link = '';
	
	$instaScrape = new wpqa_InstaScrape(wpqa_options("instagram_sessionid"));
	try {
		$res = $instaScrape->getFollowersCountFromName($instagram);
		$count = $res;
	}catch (Exception $e) {
		//echo 'Failed:'.$e->getMessage();
	}
	
	$link = 'https://instagram.com/'.$instagram;
	set_transient('wpqa_instagram_followers', $count, 60*60*24);
	set_transient('wpqa_instagram_page_url', $link, 60*60*24);
	if ($return == 'link') {
		return $link;
	}else {
		return $count;
	}
}
/* wpqa_counter_pinterest */
function wpqa_counter_pinterest($pinterest) {
	$count = get_transient('wpqa_pinterest_followers');
	if ($count !== false) return $count;
	$pin_metas = @get_meta_tags($pinterest);
	if (isset($pin_metas['pinterestapp:followers'])) {
		$count = $pin_metas['pinterestapp:followers'];
	}else if (isset($pin_metas['followers'])) {
		$count = $pin_metas['followers'];
	}else {
		$count = 0;
	}
	set_transient('wpqa_pinterest_followers', $count, 60*60*24);
	return $count;        
}
/* wpqa_counter_github */
function wpqa_counter_github($github, $return = 'count') {
	$count = get_transient('wpqa_github_followers');
	$link = get_transient('wpqa_github_page_url');
	if ($return == 'link') {
		if ($link !== false) return $link;
	}else {
		if ($count !== false) return $count;
	}
	$count = 0;
	$link = '';
	$data = wp_remote_get('https://api.github.com/users/'.$github);
	if (!is_wp_error($data)) {
		$json = json_decode($data['body'],true);
		$count = (isset($json['followers'])?intval($json['followers']):0);
		$link = 'https://github.com/'.$github;
		set_transient('wpqa_github_followers', $count, 60*60*24);
		set_transient('wpqa_github_page_url', $link, 60*60*24);
	} 
	if ($return == 'link') {
		return $link;
	}else {
		return $count;
	}
}
/* wpqa_instagram_images */
function wpqa_instagram_images($instagram,$count = 5) {
	$images = '';
	$instaScrape = new wpqa_InstaScrape(wpqa_options("instagram_sessionid"));
	try {
		$res = $instaScrape->getUserIDFromName($instagram);
		// Get user items from id
		$res = $instaScrape->getUserItems($res,$count);
		$i = 0;
		if (is_array($res["data"]["user"]["edge_owner_to_timeline_media"]["edges"]) && !empty($res["data"]["user"]["edge_owner_to_timeline_media"]["edges"])) {
			foreach ($res["data"]["user"]["edge_owner_to_timeline_media"]["edges"] as $post) {
				$i++;
				$images .= '<li class="instagram-image"><a target="_blank" href="https://www.instagram.com/p/'.esc_attr($post["node"]["shortcode"]).'/" title="'.(isset($post["node"]["edge_media_to_caption"]["edges"][0]["node"]["text"])?$post["node"]["edge_media_to_caption"]["edges"][0]["node"]["text"].'&nbsp;&nbsp;/&nbsp;&nbsp;&hearts;&nbsp;':'').$post["node"]["edge_liked_by"]["count"].'&nbsp;likes">
					<img class="instagram-image" src="'.$post["node"]["thumbnail_src"].'">
				</a></li>';
				if ($count >= $i) {
					continue;
				}
			}
		}else {
			$images .= "<p>No photos for this query</p>";
		}
	}catch (Exception $e) {
		$images .= 'Failed:'.$e->getMessage();
	}
	
	return $images;
}
/* wpqa_twitter_tweets */
if (!function_exists('wpqa_twitter_tweets')) :
	function wpqa_twitter_tweets($username = '',$tweets_count = 3) {
		$twitter_data    = "";
		$access_token    = get_option('wpqa_twitter_token');
		$consumer_key    = wpqa_options('twitter_consumer_key');
		$consumer_secret = wpqa_options('twitter_consumer_secret');
		if ($access_token == "") {
			$credentials = $consumer_key . ':' . $consumer_secret;
			$toSend 	 = wpqa_api_credentials($credentials);
			
			$args = array(
				'method'      => 'POST',
				'httpversion' => '1.1',
				'blocking' 		=> true,
				'headers' 		=> array(
					'Authorization' => 'Basic ' . $toSend,
					'Content-Type' 	=> 'application/x-www-form-urlencoded;charset=UTF-8'
				),
				'body' 				=> array( 'grant_type' => 'client_credentials' )
			);
			
			add_filter('https_ssl_verify', '__return_false');
			$response = wp_remote_post('https://api.twitter.com/oauth2/token',$args);
			
			$keys = json_decode(wp_remote_retrieve_body($response));
			
			if ( !empty($keys->access_token) ) {
				update_option('wpqa_twitter_token', $keys->access_token);
				$access_token = $keys->access_token;
			}
		}
		
		$args = array(
			'httpversion' => '1.1',
			'blocking'    => true,
			'headers'     => array(
			'Authorization' => "Bearer $access_token",
		));
		
		add_filter('https_ssl_verify', '__return_false');
		
		$api_url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$username&count=$tweets_count";
		$response = wp_remote_get( $api_url, $args );
		
		if ( ! is_wp_error( $response )) {
			$twitter_data = json_decode(wp_remote_retrieve_body($response));
		}

		return $twitter_data;
	}
endif;
/* wpqa_general_typography */
function wpqa_general_typography($wpqa_general_typography,$wpqa_css) {
	$custom_css = '';
	$general_typography = wpqa_options($wpqa_general_typography);
	if (
	(isset($general_typography["style"]) && $general_typography["style"] != "" && $general_typography["style"] != "default") || 
	(isset($general_typography["size"]) && $general_typography["size"] != "" && $general_typography["size"] != "default" && is_string($general_typography["size"])) || 
	(isset($general_typography["color"]) && $general_typography["color"] != "")) {
	$custom_css .= '
		'.$wpqa_css.' {';
			if (isset($general_typography["size"]) && $general_typography["size"] != "" && $general_typography["size"] != "default" && is_string($general_typography["size"])) {
				$custom_css .= 'font-size: '.$general_typography["size"].';';
			}
			if (isset($general_typography["color"]) && $general_typography["color"] != "") {
				$custom_css .= 'color: '.$general_typography["color"].';';
			}
			if (isset($general_typography["style"]) && $general_typography["style"] != "default" && $general_typography["style"] != "Style") {
				if ($general_typography["style"] == "bold italic" || $general_typography["style"] == "bold") {
					$custom_css .= 'font-weight: bold;';
				}
				if ($general_typography["style"] == "normal") {
					$custom_css .= 'font-weight: normal;';
				}
				if ($general_typography["style"] == "italic" || $general_typography["style"] == "bold italic") {
					$custom_css .= 'font-style: italic;';
				}
			}
		$custom_css .= '}';
	}
	return $custom_css;
}
/* wpqa_general_color */
function wpqa_general_color($wpqa_general_color,$wpqa_css,$wpqa_type,$important = false) {
	$custom_css = '';
	$important = ($important == true?" !important":"");
	$general_link_color = wpqa_options($wpqa_general_color);
	if (isset($general_link_color) && $general_link_color != "") {
		$custom_css .= '
		'.$wpqa_css.' {
			'.$wpqa_type.': '.$general_link_color.$important.';
		}';
	}
	return $custom_css;
}
/* wpqa_general_background */
function wpqa_general_background($wpqa_general_background,$full_screen_background,$wpqa_css) {
	$custom_css = '';
	$general_image = wpqa_options($wpqa_general_background);
	$general_background_color = $general_image["color"];
	$general_background_img = $general_image["image"];
	$general_background_repeat = $general_image["repeat"];
	$general_background_position = $general_image["position"];
	$general_background_fixed = $general_image["attachment"];
	$general_full_screen_background = wpqa_options($full_screen_background);
	
	if ($general_full_screen_background == "on") {
		$custom_css .= $wpqa_css.' {';
			if (!empty($background_color)) {
				$custom_css .= 'background-color: '.$general_background_color.';';
			}
			$custom_css .= 'background-image : url("'.$general_background_img.'") ;
			filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.$general_background_img.'",sizingMethod="scale");
			-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.$general_background_img.'\',sizingMethod=\'scale\')";
			background-size: cover;
		}';
	}else {
		if (!empty($general_image)) {
			if ($general_full_screen_background != "on") {
				if ((isset($general_background_img) && $general_background_img != "") || isset($general_background_color) && $general_background_color != "") {
					$custom_css .= $wpqa_css.'{background:'.esc_attr($general_background_color).(isset($general_background_img) && $general_background_img != ""?' url("'.esc_attr($general_background_img).'") '.esc_attr($general_background_repeat).' '.esc_attr($general_background_fixed).' '.esc_attr($general_background_position):'').';}';
				}
			}
		}
	}
	return $custom_css;
}
/* wpqa_backgrounds */
function wpqa_backgrounds($custom_background = "",$background_type = "",$background_pattern = "",$background_color = "",$background_full = "") {
	$custom_css = '';
	if ($background_type != "none") {
		if ($background_full == "on" && $background_type != "patterns" && $background_type != "") {
			$custom_css .= '.background-cover,.main-content,.dark-skin .background-cover,.dark-skin .main-content {';
				$background_color_s = (isset($custom_background["color"])?$custom_background["color"]:"");
				if (!empty($background_color_s)) {
					$custom_css .= 'background-color: '.esc_attr($background_color_s) .';';
				}
				if (wpqa_image_url_id($custom_background["image"]) != "") {
					$custom_css .= 'background-image: url("'.esc_attr(wpqa_image_url_id($custom_background["image"])).'") ;
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.esc_attr(wpqa_image_url_id($custom_background["image"])).'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.esc_attr(wpqa_image_url_id($custom_background["image"])).'\',sizingMethod=\'scale\')";';
				}
			$custom_css .= '}';
		}else {
			if ($background_type == "patterns" || !empty($custom_background)) {
				$custom_css .= 'body,.main-content,.dark-skin body,.dark-skin .main-content {
					background:';
					if ($background_type == "patterns") {
						if ($background_pattern != "default" && $background_pattern != "") {
							$custom_css .= $background_color.' url('.get_template_directory_uri().'/images/patterns/'.$background_pattern.'.png) repeat;';
						}
					}
					if (!empty($custom_background)) {
						if ($background_full != "on") {
							$custom_css .= esc_attr((isset($custom_background["color"])?$custom_background["color"]:"")).' url('.esc_attr(wpqa_image_url_id($custom_background["image"])).') '.esc_attr($custom_background["repeat"]).' '.esc_attr($custom_background["position"]).' '.esc_attr($custom_background["attachment"]).';';
						}
					}
				$custom_css .= '}';
			}
		}
	}
	return $custom_css;
}
/* Custom post types */
function wpqa_custom_post_types() {
	$get_post_types = get_post_types(array(),false);
	$unset_post_types = array("attachment","revision","nav_menu_item","custom_css","customize_changeset","oembed_cache","user_request","wp_block","wp_template","wp_template_part","wp_global_styles","wp_navigation","wpcf7_contact_form");
	foreach($unset_post_types as $key) {
		if (isset($get_post_types[$key])) {
			unset($get_post_types[$key]);
		}
	}
	$options_post_types = array();
	$options_post_types_obj = $get_post_types;
	if (is_array($options_post_types_obj) && !empty($options_post_types_obj)) {
		foreach ($options_post_types_obj as $post_type) {
			$options_post_types[$post_type->name] = $post_type->label;
		}
	}

	return $options_post_types;
}
/* Delete old notifications, activities, reports, and points */
add_action("wpqa_init","wpqa_init_delete_old_notifications_activities");
function wpqa_init_delete_old_notifications_activities() {
	if (is_admin()) {
		$deletions_notifications_activities = wpqa_options("deletions_notifications_activities");
		if ($deletions_notifications_activities == "on") {
			wpqa_delete_old_notifications_activities();
		}
	}
}
function wpqa_delete_old_notifications_activities() {
	global $post;
	$specific_date = wpqa_options("time_deletions_notifications_activities");
	$type_deletions = wpqa_options("type_deletions_notifications_activities");
	if ($specific_date == "month" || $specific_date == "year") {
		$specific_date = "1 ".$specific_date;
	}else {
		$specific_date = "2 months";
	}
	$type_deletions = (is_array($type_deletions) && !empty($type_deletions)?$type_deletions:array());
	foreach ($type_deletions as $key => $value) {
		if ($key !== $value) {
			unset($type_deletions[$key]);
		}
	}
	if (!empty($type_deletions)) {
		$args = array("post_type" => $type_deletions,"posts_per_page" => 200,"date_query" => array(array('before' => $specific_date.' ago')));
		$query_wp = new WP_Query($args);
		if (isset($query_wp)) {
			if ($query_wp->have_posts()) :
				while ($query_wp->have_posts()) : $query_wp->the_post();
					wp_delete_post($post->ID,true);
				endwhile;
			endif;
		}
		wp_reset_postdata();
	}
}
/* Delete old trash and pending post type and comments */
add_action("wpqa_init","wpqa_init_delete_old_post_trash_pending");
function wpqa_init_delete_old_post_trash_pending() {
	if (is_admin()) {
		$deletions_spam_trash = wpqa_options("deletions_spam_trash");
		if ($deletions_spam_trash == "on") {
			wpqa_delete_old_post_trash_pending();
			wpqa_delete_old_comment_trash_pending();
		}
	}
}
function wpqa_delete_old_post_trash_pending() {
	global $post;
	$specific_date = wpqa_options("time_deletions_spam_trash");
	$type_deletions = wpqa_options("type_deletions_spam_trash");
	$status_deletions = wpqa_options("kind_of_deletions_spam_trash");
	if ($specific_date == "month" || $specific_date == "year") {
		$specific_date = "1 ".$specific_date;
	}else {
		$specific_date = "2 months";
	}
	$type_deletions = (is_array($type_deletions) && !empty($type_deletions)?$type_deletions:array());
	if (isset($type_deletions["comment"])) {
		unset($type_deletions["comment"]);
	}
	if (isset($type_deletions["answer"])) {
		unset($type_deletions["answer"]);
	}
	$status_deletions = (is_array($status_deletions) && !empty($status_deletions)?$status_deletions:array());
	if (isset($status_deletions["spam"])) {
		unset($status_deletions["spam"]);
	}
	if (isset($status_deletions["pending"])) {
		$status_deletions["pending"] = "pending";
		$status_deletions["draft"] = "draft";
	}
	if (!empty($type_deletions) && !empty($status_deletions)) {
		$args = array("post_type" => $type_deletions,"post_status" => $status_deletions,"posts_per_page" => 200,"date_query" => array(array('before' => $specific_date.' ago')));
		$query_wp = new WP_Query($args);
		if (isset($query_wp)) {
			if ($query_wp->have_posts()) :
				while ($query_wp->have_posts()) : $query_wp->the_post();
					wp_delete_post($post->ID,true);
				endwhile;
			endif;
		}
		wp_reset_postdata();
	}
}
function wpqa_delete_old_comment_trash_pending() {
	$specific_date = wpqa_options("time_deletions_spam_trash");
	$type_deletions = wpqa_options("type_deletions_spam_trash");
	$status_deletions = wpqa_options("kind_of_deletions_spam_trash");
	if ($specific_date == "month" || $specific_date == "year") {
		$specific_date = "1 ".$specific_date;
	}else {
		$specific_date = "2 months";
	}
	$original_type_deletions = $type_deletions = (is_array($type_deletions) && !empty($type_deletions)?$type_deletions:array());
	if (in_array("answer",$original_type_deletions) || in_array("comment",$original_type_deletions)) {
		if (!in_array("comment",$original_type_deletions) && in_array("answer",$original_type_deletions)) {
			$type_deletions = array(wpqa_questions_type,wpqa_asked_questions_type);
		}
		if (in_array("comment",$original_type_deletions)) {
			$get_post_types = wpqa_custom_post_types();
			$type_deletions = $get_post_types;
		}
		if (!in_array("answer",$original_type_deletions)) {
			if (isset($type_deletions[wpqa_questions_type])) {
				unset($type_deletions[wpqa_questions_type]);
			}
			if (isset($type_deletions[wpqa_asked_questions_type])) {
				unset($type_deletions[wpqa_asked_questions_type]);
			}
		}
		if (isset($type_deletions["comment"])) {
			unset($type_deletions["comment"]);
		}
		if (isset($type_deletions["answer"])) {
			unset($type_deletions["answer"]);
		}
		$status_comment_deletions = (is_array($status_deletions) && !empty($status_deletions)?$status_deletions:array("spam","trash","hold"));
		foreach ($status_deletions as $key => $value) {
			if ($key !== $value) {
				unset($status_deletions[$key]);
			}
		}
		if (isset($status_deletions["pending"])) {
			$status_deletions["pending"] = "hold";
		}
		if (!empty($type_deletions) && !empty($status_deletions)) {
			$args = array("post_type" => $type_deletions,"status" => $status_deletions,"number" => 200,"date_query" => array(array('before' => $specific_date.' ago')));
			$comments_query = new WP_Comment_Query($args);
			$comments = $comments_query->comments;
			if (!empty($comments)) {
				foreach ($comments as $comment) {
					wp_delete_comment($comment->comment_ID,true);
				}
			}
		}
	}
}
/* Delete old under review and activation users */
add_action("wpqa_init","wpqa_init_delete_old_users_review_activation");
function wpqa_init_delete_old_users_review_activation() {
	if (is_admin()) {
		$deletions_users = wpqa_options("deletions_users");
		if ($deletions_users == "on") {
			wpqa_delete_old_users_review_activation();
		}
	}
}
function wpqa_delete_old_users_review_activation() {
	$specific_date = wpqa_options("time_deletions_spam_trash");
	$type_deletions = wpqa_options("type_deletions_users");
	if ($specific_date == "month" || $specific_date == "year") {
		$specific_date = "1 ".$specific_date;
	}else {
		$specific_date = "2 months";
	}
	$specific_date = strtotime("-".$specific_date,strtotime(current_time("mysql")));
	$specific_date = date("Y-m-d 00:00:00",$specific_date);
	$type_deletions = (is_array($type_deletions) && !empty($type_deletions)?$type_deletions:array());
	foreach ($type_deletions as $key => $value) {
		if ($key !== $value) {
			unset($type_deletions[$key]);
		}
	}
	if (!empty($type_deletions)) {
		$args = array("fields" => "ID","role__in" => $type_deletions,"number" => 200,"wpqa_registered" => $specific_date,"wpqa_registered_compare" => "<=");
		$users_query = new WP_User_Query($args);
		$users = $users_query->get_results();
		if (!empty($users)) {
			require_once(ABSPATH.'wp-admin/includes/user.php');
			foreach ($users as $user) {
				wp_delete_user($user,0);
			}
		}
	}
}
add_action('pre_user_query','wpse_registered_filter');
function wpse_registered_filter($q) {
	if (isset($q->query_vars['wpqa_registered']) && isset($q->query_vars['wpqa_registered_compare']) && is_string($q->query_vars['wpqa_registered_compare'])) {
		$registered = $q->query_vars['wpqa_registered'];
		$compare    = $q->query_vars['wpqa_registered_compare'];
		
		$available_compares = array('=','<','>','<=','>=','!=');
		
		if (!in_array($compare,$available_compares,TRUE))
			$compare = '=';

		global $wpdb;
		$q->query_where .= $wpdb->prepare(" AND {$wpdb->users}.user_registered {$compare} '%s' ",$registered);
	}
}
/* Get category name */
function wpqa_get_tax_name($post_type,$tax_type = 'category') {
	$taxonomies = get_object_taxonomies($post_type,'objects');
	if (is_array($taxonomies) && !empty($taxonomies)) {
		foreach ($taxonomies as $key_tax => $value_tax) {
			if (strpos($key_tax,$tax_type) !== false) {
				$tax = $key_tax;
			}
		}
	}
	return (isset($tax)?$tax:'category');
}?>