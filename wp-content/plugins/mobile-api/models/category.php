<?php

class MOBILE_API_Category {
	var $id;          // Integer
	var $slug;        // String
	var $name;       // String
	var $description; // String
	var $parent;      // Integer
	var $post_count;  // Integer

	function __construct($wp_category = null) {
		if ($wp_category) {
			$user_id = get_current_user_id();
			$this->import_wp_object($wp_category,$user_id);
		}
	}

	public function get_catobject($wp_cat) {
		if (!$wp_cat) {
			return null;
		}
		return new MOBILE_API_Category($wp_cat);
	}

	function import_wp_object($wp_category,$user_id = 0) {
		$user_id = ($user_id > 0?$user_id:get_current_user_id());
		$tax_id = $wp_category->term_id;
		$cat_follow = get_term_meta($tax_id,"cat_follow",true);
		$cats_follwers = (int)(is_array($cat_follow)?count($cat_follow):0);
		$user_cat_follow = get_user_meta($user_id,'user_cat_follow',true);
		$user_cat_follow = (is_array($user_cat_follow) && !empty($user_cat_follow)?$user_cat_follow:array());
		$cat_follow_true = (!empty($user_cat_follow) && in_array($tax_id,$user_cat_follow)?true:false);
		if (is_array($cat_follow)) {
			$sliced_array = array_slice($cat_follow,0,3);
			foreach ($sliced_array as $key => $value) {
				$followers[] = mobile_api_user_avatar_link(array("user_id" => $value,"size" => 128));
			}
		}
		$this->id = (int)$tax_id;
		$this->slug = $wp_category->slug;
		$this->name = htmlspecialchars_decode($wp_category->name);
		$this->description = $wp_category->description;
		$this->parent = (int)$wp_category->parent;
		$this->post_count = (int)$wp_category->count;
		$this->term_id = (int)$tax_id;
		$this->url = mobile_api_posts_category."?id=".$tax_id."&taxonomy=".$wp_category->taxonomy;
		$this->taxonomy = $wp_category->taxonomy;
		$this->category_followers = $cats_follwers;
		if ($user_id > 0) {
			$this->followed = $cat_follow_true;
		}
		$this->followers = (isset($followers) && is_array($followers)?$followers:array());
		$parent_categories = mobile_api_options("mobile_parent_categories");
		if ($parent_categories != mobile_api_checkbox_value) {
			$hide_empty = 0;
			$cat_order  = apply_filters('mobile_api_cat_order',"DESC");// ASC
			$cat_sort   = apply_filters('mobile_api_cat_sort',"count");// followers - name
			$cat_sort   = ($cat_sort == "followers"?"meta_value_num":$cat_sort);
			$meta_query = ($cat_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "cat_follow_count","compare" => "NOT EXISTS"),array("key" => "cat_follow_count","value" => 0,"compare" => ">="))):array());
			$wp_cats = (array) get_terms($wp_category->taxonomy,array_merge($meta_query,array('parent' => $tax_id,'orderby' => $cat_sort,'order' => $cat_order,'hide_empty' => $hide_empty)));
		}
		$this->categories = (isset($wp_cats) && is_array($wp_cats) && !empty($wp_cats)?array_map(array(&$this,'get_catobject'),$wp_cats):array());
	}
}?>