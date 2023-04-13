<?php
/* Facebook */
add_action( 'widgets_init', 'himer_widget_facebook_widget' );
function himer_widget_facebook_widget() {
	register_widget( 'Widget_Facebook' );
}
class Widget_Facebook extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'facebook-widget' );
		$control_ops = array( 'id_base' => 'facebook-widget' );
		parent::__construct( 'facebook-widget',himer_theme_name.' - Facebook', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title		   = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$facebook_link = (isset($instance['facebook_link'])?esc_url($instance['facebook_link']):'');
		$width         = (isset($instance['width'])?esc_html($instance['width']):'');
		$height        = (isset($instance['height'])?esc_html($instance['height']):'');
		$border_color  = (isset($instance['border_color'])?esc_html($instance['border_color']):'');
		$background    = (isset($instance['background'])?esc_html($instance['background']):'');
			
		echo stripcslashes($before_widget);
			if ($title) {
				echo ("empty" == $title?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Facebook","himer")."</h3>";
			}?>
			<div class="widget-wrap">
				<div class="facebook_widget">
				    <iframe src="//www.facebook.com/plugins/likebox.php?href=<?php echo esc_url($facebook_link)?>&amp;width=<?php echo esc_attr($width)?>&amp;colorscheme=light&amp;show_faces=true&amp;border_color=%23<?php echo stripcslashes($border_color)?>&amp;stream=false&amp;header=false&amp;height=<?php echo stripcslashes($height)?>" style="border:none; overflow:hidden; width:<?php echo stripcslashes($width)?>px; height:<?php echo stripcslashes($height)?>px;"></iframe>
				</div>
			</div>
		<?php echo stripcslashes($after_widget);
	}
	
	public function form( $instance ) {
		/* Save Button */
	}
}?>