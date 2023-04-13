<?php
/* Counter */
add_action( 'widgets_init', 'himer_widget_counter_widget' );
function himer_widget_counter_widget() {
	register_widget( 'Widget_Counter' );
}
class Widget_Counter extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget-statistics' );
		$control_ops = array( 'id_base' => 'widget_counter' );
		parent::__construct( 'widget_counter',himer_theme_name.' - Social counter', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title     = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$facebook  = (isset($instance['facebook'])?esc_html($instance['facebook']):'');
		$twitter   = (isset($instance['twitter'])?esc_html($instance['twitter']):'');
		$pinterest = (isset($instance['pinterest'])?esc_html($instance['pinterest']):'');
		$vimeo     = (isset($instance['vimeo'])?esc_html($instance['vimeo']):'');
		$instagram = (isset($instance['instagram'])?esc_html($instance['instagram']):'');
		$dribbble  = (isset($instance['dribbble'])?esc_html($instance['dribbble']):'');
		$youtube   = (isset($instance['youtube'])?esc_html($instance['youtube']):'');
		$behance   = (isset($instance['behance'])?esc_html($instance['behance']):'');
		$github    = (isset($instance['github'])?esc_html($instance['github']):'');
		
		if (has_wpqa() && wpqa_plugin_version >= "5.8") {
			$counter_facebook  = wpqa_counter_facebook($facebook);
			$counter_twitter   = wpqa_counter_twitter($twitter);
			$counter_pinterest = wpqa_counter_pinterest($pinterest);
			$counter_vimeo     = wpqa_counter_vimeo($vimeo);
			$counter_instagram = wpqa_counter_instagram($instagram);
			$counter_dribbble  = wpqa_counter_dribbble($dribbble);
			$counter_youtube   = wpqa_counter_youtube($youtube);
			$counter_behance   = wpqa_counter_behance($behance);
			$counter_github    = wpqa_counter_github($github);
			
			echo stripcslashes($before_widget);
				if ($title) {
					echo ("empty" == $title?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
				}else {
					echo "<h3 class='screen-reader-text'>".esc_html__("Social counter","himer")."</h3>";
				}
				
				$s_cs = array(
					"facebook"  => ($facebook != ""?array("https://facebook.com".$facebook,$counter_facebook,_n("Follower","Followers",$counter_facebook,"himer")):""),
					"twitter"   => ($twitter != ""?array("https://twitter.com/".$twitter,$counter_twitter,_n("Follower","Followers",$counter_twitter,"himer")):""),
					"pinterest" => ($pinterest != ""?array($pinterest,$counter_pinterest,_n("Follower","Followers",$counter_pinterest,"himer")):""),
					"vimeo"     => ($vimeo != ""?array(wpqa_counter_vimeo($vimeo, 'link'),$counter_vimeo,_n("Subscriber","Subscribers",$counter_vimeo,"himer")):""),
					"instagram" => ($instagram != ""?array(wpqa_counter_instagram($instagram, 'link'),$counter_instagram,_n("Follower","Followers",$counter_instagram,"himer"),"instagram"):""),
					"dribbble"  => ($dribbble != ""?array(wpqa_counter_dribbble($dribbble, 'link'),$counter_dribbble,_n("Follower","Followers",$counter_dribbble,"himer")):""),
					"youtube"   => ($youtube != ""?array("https://www.youtube.com/channel/".$youtube,$counter_youtube,_n("Subscriber","Subscribers",$counter_youtube,"himer"),"play"):""),
					"behance"   => ($behance != ""?array(wpqa_counter_behance($behance, 'link'),$counter_behance,_n("Follower","Followers",$counter_behance,"himer")):""),
					"github"    => ($github != ""?array(wpqa_counter_github($github, 'link'),$counter_github,_n("Follower","Followers",$counter_github,"himer")):""),
				);?>
				<div class="widget-wrap">
					<ul class="social-background">
						<?php if (isset($s_cs) && is_array($s_cs)) {
							foreach ($s_cs as $s_k => $s_v) {
								if (is_array($s_v)) {?>
									<li class="social-<?php echo esc_attr($s_k)?>">
										<a href="<?php echo esc_url($s_v[0])?>" target="_blank">
											<i class="<?php echo "icon-".esc_attr((isset($s_v[3]) && $s_v[3] != ""?$s_v[3]:$s_k))?>"></i>
											<span class="social-content">
												<span class="social-followers"><?php echo himer_count_number((int)$s_v[1])?></span>
												<span class="social-text"><?php echo stripcslashes($s_v[2])?></span>
											</span>
										</a>
									</li>
								<?php }
							}
						}?>
					</ul>
					<div class="clearfix"></div>
				</div>
			<?php echo stripcslashes($after_widget);
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>