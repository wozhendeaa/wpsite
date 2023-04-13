<?php

class MOBILE_API_Tag {
	
	var $id;          // Integer
	var $slug;        // String
	var $name;       // String
	var $description; // String
	
	function __construct($wp_tag = null) {
		if ($wp_tag) {
			$user_id = get_current_user_id();
			$this->import_wp_object($wp_tag,$user_id);
		}
	}
	
	function import_wp_object($wp_tag,$user_id) {
		$tax_id = (int)$wp_tag->term_id;
		$cat_follow = get_term_meta($tax_id,"tag_follow",true);
		$cats_follwers = (int)(is_array($cat_follow)?count($cat_follow):0);
		$user_cat_follow = get_user_meta($user_id,'user_tag_follow',true);
		$user_cat_follow = (is_array($user_cat_follow) && !empty($user_cat_follow)?$user_cat_follow:array());
		$cat_follow_true = (!empty($user_cat_follow) && in_array($tax_id,$user_cat_follow)?true:false);
		if (is_array($cat_follow)) {
			$sliced_array = array_slice($cat_follow,0,3);
			foreach ($sliced_array as $key => $value) {
				$followers[] = mobile_api_user_avatar_link(array("user_id" => $value,"size" => 128));
			}
		}
		$this->id = $tax_id;
		$this->slug = $wp_tag->slug;
		$this->name = $wp_tag->name;
		$this->description = $wp_tag->description;
		$this->post_count = (int)$wp_tag->count;
		$this->term_id = (int)$wp_tag->term_id;
		$this->url = mobile_api_post_tags."?id=".$wp_tag->term_id."&taxonomy=".$wp_tag->taxonomy;
		$this->taxonomy = $wp_tag->taxonomy;
		$this->category_followers = $cats_follwers;
		if ($user_id > 0) {
			$this->followed = $cat_follow_true;
		}
		$this->followers = (isset($followers) && is_array($followers)?$followers:array());
	}
	
}?>