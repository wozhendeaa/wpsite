<?php
/* About */
add_action( 'widgets_init', 'himer_widget_about_widget' );
function himer_widget_about_widget() {
	register_widget( 'Widget_About' );
}
class Widget_About extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'about-widget' );
		$control_ops = array( 'id_base' => 'about-widget' );
		parent::__construct( 'about-widget',himer_theme_name.' - About', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$text  = (isset($instance['text'])?himer_kses_stip($instance['text'],"","yes"):'');

		echo stripcslashes($before_widget);?>
			<div class="widget-wrap">
				<div class="about-text">
					<?php if ($title) {
						echo ("empty" == $title?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
					}else {
						echo "<h3 class='screen-reader-text'>".esc_html__("About","himer")."</h3>";
					}
					echo stripcslashes($text)?>
				</div>
			</div>
		<?php echo stripcslashes($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>