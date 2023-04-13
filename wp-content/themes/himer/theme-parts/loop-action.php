<?php 
do_action("himer_before_include_content",$post);
include locate_template("theme-parts/content".(wpqa_questions_type == $post->post_type || wpqa_asked_questions_type == $post->post_type?"-question":"").".php");
?>