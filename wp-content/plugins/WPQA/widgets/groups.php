<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Groups */
add_action( 'widgets_init', 'wpqa_widget_groups_widget' );
function wpqa_widget_groups_widget() {
	register_widget( 'Widget_Groups' );
}

class Widget_Groups extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'groups-widget' );
		$control_ops = array( 'id_base' => 'groups-widget' );
		parent::__construct( 'groups-widget',wpqa_widgets.' - Groups', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title         = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$group_style   = (isset($instance['group_style'])?esc_html($instance['group_style']):'');
		$no_of_groups  = (int)(isset($instance['no_of_groups'])?$instance['no_of_groups']:5);
		$group_display = (isset($instance['group_display'])?esc_html($instance['group_display']):'');
		$group_order   = (isset($instance['group_order'])?esc_html($instance['group_order']):'');
		echo stripcslashes($before_widget);
			if ($title) {
				echo stripcslashes($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Groups","wpqa")."</h3>";
			}
			$no_of_groups  = (isset($no_of_groups) && $no_of_groups != ""?$no_of_groups:5);
			$group_display = (isset($group_display) && $group_display != ""?$group_display:"all");
			$group_order   = (isset($group_order) && $group_order != ""?$group_order:"date");
			if ($group_order == "users") {
				$group_orderby = "group_users";
			}else if ($group_order == "posts") {
				$group_orderby = "group_posts";
			}
			$orderby_array = (isset($group_orderby)?array('orderby' => 'meta_value_num','order' => 'DESC',"meta_query" => array(array('type' => 'numeric',"key" => $group_orderby,"value" => 0,"compare" => ">="))):array());
			$group_display = ($group_display == "private" || $group_display == "public"?array("meta_query" => array(array("key" => "group_privacy","value" => $group_display,"compare" => "="))):array());
			$array_data = array_merge($group_display,$orderby_array,array("post_type" => "group","posts_per_page" => $no_of_groups));
			$wpqa_query = new WP_Query($array_data);
			$date_format = wpqa_options("date_format");
			$date_format = ($date_format?$date_format:get_option("date_format"));
			if ($wpqa_query->have_posts()) :
				global $post;
				echo '<div class="widget-wrap">
					<div class="widget_groups widget-posts">
						<div class="user-notifications user-profile-area widget-post-style-2 post-style-2-image">
							<div>
								<ul>';
									while ($wpqa_query->have_posts()) : $wpqa_query->the_post();
										$post_id = $post->ID;
										$the_title = get_the_title($post_id);
										$group_privacy = get_post_meta($post_id,"group_privacy",true);
										$group_image = get_post_meta($post_id,"group_image",true);
										if ($group_style == "style_2") {
											$group_users = (int)get_post_meta($post_id,"group_users",true);
											$group_posts = (int)get_post_meta($post_id,"group_posts",true);
											$post_stats = wpqa_get_post_stats($post_id);
										}
										$human_time_diff = human_time_diff(get_the_time('U'), current_time('timestamp'));
										echo '<li class="widget-posts-image widget-groups-image d-flex align-items-center">';
											if ((is_array($group_image) && isset($group_image["id"])) || (!is_array($group_image) && $group_image != "")) {
												echo '<div class="widget-post-image group_avatar mr-3"><a href="'.get_permalink($post_id).'" title="'.$the_title.'">'.wpqa_get_aq_resize_img(60,60,"",(is_array($group_image) && isset($group_image["id"])?$group_image["id"]:$group_image),"no",$the_title)."</a></div>";
											}
											echo '<div class="groups-widget-content">';
												if ($group_style != "style_2") {
													echo '<ul class="widget-post-meta widget-group-meta"><li><span>'.($group_privacy == "public"?esc_html__("Public group","wpqa"):esc_html__("Private group","wpqa")).'</span></li></ul>';
												}
												echo '<h3><a href="'.get_permalink($post_id).'" title="'.$the_title.'">'.$the_title.'</a></h3>';
												if ($group_style == "style_2") {
													echo '<ul class="groups__stats groups__stats__widget list-unstyled mb-0">
														<li>'.wpqa_count_number($group_users).'</span> '._n("User","Users",$group_users,"wpqa").'</li>
														<li>'.wpqa_count_number($group_posts).'</span> '._n("Post","Posts",$group_posts,"wpqa").'</li>
														<li>'.wpqa_count_number($post_stats).'</span> '._n("View","Views",$post_stats,"wpqa").'</li>
													</ul>';
												}
											echo '</div>
										</li>';
									endwhile;
								echo '</ul>
							</div>
						</div>
					</div>
				</div>';
			endif;
			wp_reset_postdata();
		echo stripcslashes($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>