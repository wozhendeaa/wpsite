<?php
/* Adv 120x240 */
add_action( 'widgets_init', 'himer_widget_adv120x240_widget' );
function himer_widget_adv120x240_widget() {
	register_widget( 'Widget_Adv120x240' );
}
class Widget_Adv120x240 extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'adv120x240-widget' );
		$control_ops = array( 'id_base' => 'adv120x240-widget' );
		parent::__construct( 'adv120x240-widget',himer_theme_name.' - Adv 120x240', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title    = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$show_ads = (has_wpqa() && wpqa_plugin_version >= "5.8"?wpqa_check_without_ads():true);
		if ($show_ads == true) {
			$adv_type_1 = (isset($instance['adv_type_1'])?esc_html($instance['adv_type_1']):'');
			$adv_link_1 = (isset($instance['adv_link_1'])?esc_html($instance['adv_link_1']):'');
			$adv_href_1 = (isset($instance['adv_href_1'])?esc_url($instance['adv_href_1']):'');
			$adv_img_1  = (isset($instance['adv_img_1'])?esc_html(himer_image_url_id($instance['adv_img_1'])):'');
			$adv_code_1 = (isset($instance['adv_code_1'])?$instance['adv_code_1']:'');
			
			$adv_type_2 = (isset($instance['adv_type_2'])?esc_html($instance['adv_type_2']):'');
			$adv_link_2 = (isset($instance['adv_link_2'])?esc_html($instance['adv_link_2']):'');
			$adv_href_2 = (isset($instance['adv_href_2'])?esc_url($instance['adv_href_2']):'');
			$adv_img_2  = (isset($instance['adv_img_2'])?esc_html(himer_image_url_id($instance['adv_img_2'])):'');
			$adv_code_2 = (isset($instance['adv_code_2'])?$instance['adv_code_2']:'');
			
			$adv_type_3 = (isset($instance['adv_type_3'])?esc_html($instance['adv_type_3']):'');
			$adv_link_3 = (isset($instance['adv_link_3'])?esc_html($instance['adv_link_3']):'');
			$adv_href_3 = (isset($instance['adv_href_3'])?esc_url($instance['adv_href_3']):'');
			$adv_img_3  = (isset($instance['adv_img_3'])?esc_html(himer_image_url_id($instance['adv_img_3'])):'');
			$adv_code_3 = (isset($instance['adv_code_3'])?$instance['adv_code_3']:'');
			
			$adv_type_4 = (isset($instance['adv_type_4'])?esc_html($instance['adv_type_4']):'');
			$adv_link_4 = (isset($instance['adv_link_4'])?esc_html($instance['adv_link_4']):'');
			$adv_href_4 = (isset($instance['adv_href_4'])?esc_url($instance['adv_href_4']):'');
			$adv_img_4  = (isset($instance['adv_img_4'])?esc_html(himer_image_url_id($instance['adv_img_4'])):'');
			$adv_code_4 = (isset($instance['adv_code_4'])?$instance['adv_code_4']:'');
			echo stripcslashes($before_widget);
				if ($title) {
					echo ("empty" == $title?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
				}else {
					echo "<h3 class='screen-reader-text'>".esc_html__("Adv 120x240","himer")."</h3>";
				}?>
				<div class="aalan-wrap">
					<div class="aalan aalan-4a-2">
						<?php echo wpqa_widget_ads($adv_type_1,$adv_link_1,$adv_href_1,$adv_img_1,$adv_code_1,'aalan-1').
						wpqa_widget_ads($adv_type_2,$adv_href_2,$adv_link_2,$adv_img_2,$adv_code_2,'aalan-1').
						wpqa_widget_ads($adv_type_3,$adv_href_3,$adv_link_3,$adv_img_3,$adv_code_3,'aalan-1').
						wpqa_widget_ads($adv_type_4,$adv_href_4,$adv_link_4,$adv_img_4,$adv_code_4,'aalan-1')?>
					</div><!-- End aalan -->
					<div class="clearfix"></div>
				</div>
			<?php echo stripcslashes($after_widget);
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>