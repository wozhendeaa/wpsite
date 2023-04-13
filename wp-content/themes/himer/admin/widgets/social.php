<?php
/* Social */
add_action( 'widgets_init', 'himer_widget_social_widget' );
function himer_widget_social_widget() {
	register_widget( 'Widget_Social' );
}
class Widget_Social extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'social-widget' );
		$control_ops = array( 'id_base' => 'social-widget' );
		parent::__construct( 'social-widget',himer_theme_name.' - Social media', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title  = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		echo stripcslashes($before_widget);
			if ($title) {
				echo ("empty" == $title?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Social media","himer")."</h3>";
			}?>
			<div class="widget-wrap">
				<?php
				$sort_social = himer_options("sort_social");
				$rss_icon_h = himer_options("rss_icon_h");
				$rss_icon_h_other = himer_options("rss_icon_h_other");
				$social = array(
					array("name" => "Facebook",   "value" => "facebook",   "icon" => "social-facebook"),
					array("name" => "Twitter",    "value" => "twitter",    "icon" => "social-twitter"),
					array("name" => "TikTok",     "value" => "tiktok",     "icon" => " fab fa-tiktok"),
					array("name" => "Linkedin",   "value" => "linkedin",   "icon" => "social-linkedin"),
					array("name" => "Dribbble",   "value" => "dribbble",   "icon" => "social-dribbble"),
					array("name" => "Youtube",    "value" => "youtube",    "icon" => "social-youtube"),
					array("name" => "Vimeo",      "value" => "vimeo",      "icon" => "social-vimeo"),
					array("name" => "Skype",      "value" => "skype",      "icon" => "social-skype"),
					array("name" => "WhatsApp",   "value" => "whatsapp",   "icon" => "social-whatsapp"),
					array("name" => "Soundcloud", "value" => "soundcloud", "icon" => " fab fa-soundcloud"),
					array("name" => "Instagram",  "value" => "instagram",  "icon" => " fab fa-instagram"),
					array("name" => "Pinterest",  "value" => "pinterest",  "icon" => "social-pinterest"),
					array("name" => "Rss",        "value" => "rss",        "icon" => "social-rss")
				);?>
				<ul class="social-icons list-unstyled mb-0">
					<?php if (isset($sort_social) && is_array($sort_social)) {
						$k = 0;
						foreach ($sort_social as $key_r => $value_r) {$k++;
							if (isset($sort_social[$key_r]["value"])) {
								$sort_social_value = $sort_social[$key_r]["value"];
								$social_icon_h = himer_options($sort_social_value."_icon_h");
							}else {
								$sort_social_value = $sort_social[$key_r]["icon"]["value"];
								$social_icon_h = $sort_social[$key_r]["url"]["value"];
							}
							
							if (isset($sort_social[$key_r]["default"]) && $sort_social[$key_r]["default"] == "yes") {
								if ($sort_social_value != "rss") {
									if ($social_icon_h != "") {?>
										<li class="social-<?php echo esc_attr($sort_social_value)?>"><a title="<?php echo esc_attr($sort_social[$key_r]["name"])?>" href="<?php echo ("skype" == $sort_social_value?"skype:":"").($sort_social_value == "whatsapp"?"whatsapp://send?abid=":"").($sort_social_value != "skype" && $sort_social_value != "whatsapp"?esc_url($social_icon_h):$social_icon_h).($sort_social_value == "skype"?"?call":"").($sort_social_value == "whatsapp"?"&text=".esc_html__("Hello","himer"):"")?>"<?php echo ("skype" != $sort_social_value && $sort_social_value != "whatsapp"?" target='_blank'":"")?>><i class="icon-<?php echo esc_attr($sort_social[$key_r]["icon"] == "instagrem"?"instagram":$sort_social[$key_r]["icon"])?>"></i></a></li>
									<?php }
								}else {
									if ($rss_icon_h == "on") {
										$rss2_url = get_bloginfo('rss2_url');
										$rss_other = ($rss_icon_h_other != ""?$rss_icon_h_other:$rss2_url);
										if ($rss_other != "") {?>
											<li class="social-<?php echo esc_attr($sort_social_value)?>"><a title="<?php esc_attr_e("Feed","himer")?>" href="<?php echo esc_url($rss_other)?>" target="_blank"><i class="icon-<?php echo esc_attr($sort_social[$key_r]["icon"])?>"></i></a></li>
										<?php }
									}
								}
							}else {
								$icon = $sort_social[$key_r]["icon"]["value"];
								$social_class = str_ireplace(" ","_",$sort_social_value)?>
								<li class="social-<?php echo esc_attr($social_class)?>"><a title="<?php echo esc_attr($sort_social[$key_r]["name"]["value"])?>" href="<?php echo ("skype" == $sort_social_value?"skype:":"").($sort_social_value == "whatsapp"?"whatsapp://send?abid=":"").($sort_social_value != "skype" && $sort_social_value != "whatsapp"?esc_url($social_icon_h):$social_icon_h).($sort_social_value == "skype"?"?call":"").($sort_social_value == "whatsapp"?"&text=".esc_html__("Hello","himer"):"")?>"<?php echo ("skype" != $sort_social_value && $sort_social_value != "whatsapp"?" target='_blank'":"")?>><i class="<?php echo esc_attr($icon)?>"></i></a></li>
							<?php }
						}
					}?>
				</ul>
			</div>
		<?php echo stripcslashes($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>