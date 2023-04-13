<?php
/* Video */
add_action( 'widgets_init', 'himer_widget_video_widget' );
function himer_widget_video_widget() {
	register_widget( 'Widget_Video' );
}
class Widget_Video extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'video-widget' );
		$control_ops = array( 'id_base' => 'video-widget' );
		parent::__construct( 'video-widget',himer_theme_name.' - Video', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title		= apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$video_type = (isset($instance['video_type'])?esc_html($instance['video_type']):'');
		$video_id   = (isset($instance['video_id'])?esc_html($instance['video_id']):'');
		$embed_code = (isset($instance['embed_code'])?$instance['embed_code']:'');
		$width		= 'width="100"';
		$height		= (isset($instance['height'])?esc_html($instance['height']):'');
		$embed_code = preg_replace('/width="([3-9][0-9]{2,}|[1-9][0-9]{3,})"/',$width,$embed_code);
		$embed_code = preg_replace( '/height="([0-9]*)"/' , $height , $embed_code );
		$width1		= 'width: 100';
		$height1	= 'height: 170';
		$embed_code = preg_replace('/width:"([3-9][0-9]{2,}|[1-9][0-9]{3,})"/',$width1,$embed_code);
		$embed_code = preg_replace( '/height: ([0-9]*)/' , $height1 , $embed_code );  
			
		echo stripcslashes($before_widget);
			if ($title) {
				echo ("empty" == $title?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Video","himer")."</h3>";
			}?>
			<div class="widget-wrap">
				<?php if ($video_id != "") {
					$type = (has_wpqa()?wpqa_video_iframe($video_type,$video_id):"");
				}
				if ($video_type == 'embed' && $embed_code != "") {
					echo stripcslashes($embed_code);
				}else if (isset($type) && $type != "") {
					echo '<div class="question-video video-type-'.$video_type.'"><iframe allowfullscreen '.$width.' height="'.$height.'" src="'.$type.'"></iframe></div>';
				}?>
			</div>
		<?php echo stripcslashes($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>