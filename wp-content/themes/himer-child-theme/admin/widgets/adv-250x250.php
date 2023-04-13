<?php
/* Adv 250x250 */
add_action( 'widgets_init', 'himer_widget_adv250x250_widget' );
function himer_widget_adv250x250_widget() {
	register_widget( 'Widget_Adv250x250' );
}
class Widget_Adv250x250 extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'adv250x250-widget' );
		$control_ops = array( 'id_base' => 'adv250x250-widget' );
		parent::__construct( 'adv250x250-widget',himer_theme_name.' - Adv 250x250', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title    = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$show_ads = (has_wpqa() && wpqa_plugin_version >= "5.8"?wpqa_check_without_ads():true);
		if ($show_ads == true) {
			$adv_type = (isset($instance['adv_type'])?esc_html($instance['adv_type']):'');
			$adv_link = (isset($instance['adv_link'])?esc_html($instance['adv_link']):'');
			$adv_href = (isset($instance['adv_href'])?esc_url($instance['adv_href']):'');
			$adv_img  = (isset($instance['adv_img'])?esc_html(himer_image_url_id($instance['adv_img'])):'');
			$adv_code = (isset($instance['adv_code'])?$instance['adv_code']:'');
			echo stripcslashes($before_widget);
				if ($title) {
					echo ("empty" == $title?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
				}else {
					echo "<h3 class='screen-reader-text'>".esc_html__("Adv 250x250","himer")."</h3>";
				}?>
				<div class="aalan-wrap">
					<?php if (has_wpqa() && wpqa_plugin_version >= "5.8") {
						echo wpqa_widget_ads($adv_type,$adv_link,$adv_href,$adv_img,$adv_code);
					}?>
					<div class="clearfix"></div>
				</div>
			<?php echo stripcslashes($after_widget);
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>