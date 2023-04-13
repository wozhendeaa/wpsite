<?php

class wpForoCrossPostingDbManager {

    private $db;
    private $tablePostMeta;
    private $tableCommentMeta;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->tablePostMeta = $wpdb->postmeta;
        $this->tableCommentMeta = $wpdb->commentmeta;
    }

    public function getBlogPostID($topicID, $prefix) {
        $sql = $this->db->prepare("SELECT `post_id` FROM `{$this->tablePostMeta}` WHERE `meta_key` = %s AND `meta_value` = %s", '_' . $prefix .wpForoCrossPostingOptions::META_POST_TOPIC_ID, $topicID);
        $postID = $this->db->get_var($sql);
        if ($postID) {
            $forumID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, TRUE);
            $isCrossPostingDisabled = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
            return (($forumID && !$isCrossPostingDisabled) ? $postID : false);
        }
        return false;
    }

    public function getBlogCommentID($forumPostID, $prefix) {
        $commentParentID = 0;
        $sql = $this->db->prepare("SELECT `comment_id` FROM `{$this->tableCommentMeta}` WHERE `meta_key` = %s AND `meta_value` = %s", '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, $forumPostID);
        $commentID = $this->db->get_var($sql);
        if ($commentID) {
            $commentParentID = $commentID;
        }
        return $commentParentID;
    }
    
    public function deleteAutocrossOptions() {
        $this->db->query("DELETE FROM `{$this->db->options}` WHERE `option_name` LIKE '_wpf_autocross_posts%'");
    }

}
