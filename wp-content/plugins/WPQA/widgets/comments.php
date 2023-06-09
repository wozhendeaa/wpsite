<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Comments */
add_action( 'widgets_init', 'wpqa_widget_comments_widget' );
function wpqa_widget_comments_widget() {
	register_widget( 'Widget_Comments' );
}
class Widget_Comments extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'comments-post-widget' );
		$control_ops = array( 'id_base' => 'comments-post-widget' );
		parent::__construct( 'comments-post-widget',wpqa_widgets.' - Comments', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title            = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$comments_number  = (int)(isset($instance['comments_number'])?$instance['comments_number']:5);
		$comment_excerpt  = (int)(isset($instance['comment_excerpt'])?$instance['comment_excerpt']:10);
		$show_images      = (isset($instance['show_images'])?esc_html($instance['show_images']):'');
		$post_or_question = (isset($instance['post_or_question'])?esc_html($instance['post_or_question']):'');
		$display_date     = (isset($instance['display_date'])?esc_html($instance['display_date']):'');
		$specific_date    = (isset($instance['specific_date'])?$instance['specific_date']:"");
			
		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Comments","wpqa")."</h3>";
			}?>
			<div class="widget-wrap">
				<?php $args = array(
					'post_or_question' => $post_or_question,
					'comments_number'  => $comments_number,
					'comment_excerpt'  => $comment_excerpt,
					'show_images'      => $show_images,
					'display_date'     => $display_date,
					'specific_date'    => $specific_date,
				);
				wpqa_comments($args);?>
			</div>
		<?php echo ($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>