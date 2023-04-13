<?php

class wpFAutoCrossTool {

    public static function addAutoCrossRow($termID, $forumID, $postType, $autocrossEnebled = 1) {
        $termName = '';
        if ($termID != 0) {
            $term = get_term($termID);
            if (!is_wp_error($term)) {
                $termName = $term->name;
            }
        } else {
            $termName = $postType;
        }
        $forum = WPF()->forum->get_forum($forumID);
        if (!($termName && $forum)) {
            return __('Term or Forum not found ', 'wpforo_cross') . "( $termID / $forumID)";
        }
        $row = 'output_buffering not work.';
        $ob_stat = ini_get("output_buffering");
        if ($ob_stat || $ob_stat === "" || $ob_stat == "0") {
            ob_start();
            ?>
            <div class="wpf-autocross-relation">
                <div class="wpf-autocross-relation-title">
                    <?php echo '<span class="wpfcp-cat">' . $termName . '</span> ' . __('(category)', 'wpforo_cross') . ' > <span class="wpfcp-forum">' . $forum['title'] . '</span> ' . __('(forum)', 'wpforo_cross'); ?>
                </div> 
                <div class="wpf-autocross-action-buttons">
                    <?php
                    $onButtonStyle = '';
                    $offButtonStyle = '';
                    $syncDisabled = ' disabled="disabled" ';
                    if ($autocrossEnebled) {
                        $onButtonStyle = ' style="display:none;" ';
                        $syncDisabled = '';
                    } else {
                        $offButtonStyle = ' style="display:none;" ';
                    }
                    ?>
                    <button class="button wpf-autocross-on wpf-autocross-action" <?php echo $onButtonStyle; ?>><?php _e('Enable', 'wpforo_cross'); ?></button>
                    <button class="button wpf-autocross-off wpf-autocross-action" <?php echo $offButtonStyle; ?>><?php _e('Disable', 'wpforo_cross'); ?></button>  
                    <button  <?php echo $syncDisabled; ?> class="button wpf-autocross-sync wpf-autocross-action"><?php _e('Synchronize', 'wpforo_cross'); ?></button>  
                    <button class="button wpf-autocross-delete wpf-autocross-action"><?php _e('Delete', 'wpforo_cross'); ?></button>
                    <span class="wpf-autocross-info" 
                          data-posttype="<?php echo $postType; ?>" 
                          data-blogterm="<?php echo $termID; ?>" 
                          data-blogtaxanomy="<?php echo $termID ? $term->taxonomy : ''; ?>" 
                          data-forum="<?php echo $forumID; ?>"
                          data-autocrossenabled="<?php echo $autocrossEnebled; ?>"></span>
                </div>
                <div class="wpf-autocross-statistic-log"></div>
            </div>
            <?php
            $row = ob_get_clean();
        }
        return $row;
    }

    public static function autoSync($savedPosts, $termID, $forumID, $options) {
        $count = 0;
        $boardid = WPF()->board->get_current('boardid');
        $prefix = WPF()->generate_prefix($boardid);
        $suffix = $boardid ? $boardid . '_' : '';
        $response = array('complete' => 0, 'posts' => array(), 'comments' => array());
        $crossPostStatisticOptionKey = '_wpf_' . $suffix . 'autocross_posts_' . $termID . '_' . $forumID . '_statistic';
        $statistic = get_option($crossPostStatisticOptionKey, array('cross_posted' => 0, 'skipped' => 0));
        foreach ($savedPosts as $postID) {
            if ($count < 5) {
                $commentsSyncOptionKey = '_wpf_autocross_comments_' . $postID;
                $savedComments = get_option($commentsSyncOptionKey);
                $turnOff = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, true);
                $topicID = get_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, true);
                if ($turnOff || $topicID) {
                    $statistic['skipped'] = $statistic['skipped'] + 1;
                    $commentSyncStat = self::autoSyncComments($savedComments, $savedPosts, $postID, $topicID, $forumID, $termID, $count, $options);
                    continue;
                }
                if (!$topicID && empty($savedComments)) {
                    $post = get_post($postID);
                    $content = $post->post_content;
                    if ($options->crossPostingContent == wpForoCrossPostingOptions::OPTION_CROSPOSTING_CONTENT_EXCERPT) {
                        $excerpt = $post->post_excerpt;
                        if (trim($excerpt)) {
                            $content = $excerpt;
                        } else {
                            $excerpt_length = apply_filters('excerpt_length', 55);
                            $content = wp_trim_words($content, $excerpt_length);
                        }
                    }
                    $args = array(
                        'forumid' => $forumID,
                        'title' => $post->post_title,
                        'userid' => $post->post_author,
                        'body' => $content,
                        'created' => $post->post_date_gmt,
                        'slug' => $post->post_name,
                        'tags' => self::getPostTags($post->ID)
                    );
                    $newTopicID = WPF()->topic->add($args);
                    if ($newTopicID) {
                        $statistic['cross_posted'] = $statistic['cross_posted'] + 1;
                        update_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_FORUM_ID, $forumID);
                        update_post_meta($post->ID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_TOPIC_ID, $newTopicID);
                        update_post_meta($postID, '_' . $prefix . wpForoCrossPostingOptions::META_POST_CROSPOSTING_DISABLED, 0);
                        $topicID = $newTopicID;
                        $response['posts'][] = __('Content Type', 'wpforo_cross') . ' : ' . $post->post_type . '  ID : ' . $postID;
                        $count++;
                    }
                    WPF()->notice->clear();
                }
                $commentSyncStat = self::autoSyncComments($savedComments, $savedPosts, $postID, $topicID, $forumID, $termID, $count, $options);
            }
        }
        $response['complete'] = $commentSyncStat['complete'];
        if ($commentSyncStat['comments']) {
            $response['comments'] = array_merge($response['comments'], $commentSyncStat['comments']);
        }
        if ($response['complete'] == 1) {
            delete_option($crossPostStatisticOptionKey);
            $response['message'] = self::generateStatisticMessage($statistic);
        } else {
            update_option($crossPostStatisticOptionKey, $statistic);
        }

        return $response;
    }

    private static function autoSyncComments($savedComments, &$savedPosts, $postID, $topicID, $forumID, $termID, &$count, $options, $skip = false) {
        $comments = array();
        $boardid = WPF()->board->get_current('boardid');
        $prefix = WPF()->generate_prefix($boardid);
        $suffix = $boardid ? $boardid . '_' : '';
        if ($options->commentCrossingAdd && !$skip) {
            if (!$savedComments) {
                $savedComments = get_comments(array('post_id' => $postID, 'orderby' => 'comment_date', 'order' => 'ASC', 'status' => 'approve', 'fields' => 'ids'));
            }

            while ($savedComments && $count < 5) {
                $commentID = array_shift($savedComments);
                $ref = get_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_SOURCE, true);
                if ($ref) {
                    continue;
                }
                $comment = get_comment($commentID);
                $forumPostParent = get_comment_meta($comment->comment_parent, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, true);
                $userID = isset($comment->user_id) ? $comment->user_id : 0;
                $userName = $comment->comment_author;
                $userEmail = $comment->comment_author_email;
                $args['forumid'] = $forumID;
                $args['topicid'] = $topicID;
                $args['title'] = wpforo_phrase('RE:', false) . ' ' . get_the_title($postID);
                $args['body'] = $comment->comment_content;
                $args['parentid'] = $forumPostParent ? $forumPostParent : 0;
                $args['userid'] = $userID;
                $args['name'] = $userName;
                $args['email'] = $userEmail;
                $args['referrer'] = wpForoCrossPostingOptions::META_COMMENT_SOURCE_BLOG;
                $args['created'] = $comment->comment_date_gmt;
                $forumPostID = WPF()->post->add($args);
                if ($forumPostID) {
                    add_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_FORUM_POST_ID, $forumPostID);
                    add_comment_meta($commentID, '_' . $prefix . wpForoCrossPostingOptions::META_COMMENT_SOURCE, wpForoCrossPostingOptions::META_COMMENT_SOURCE_BLOG);
                    $comments[] = __('Comment', 'wpforo_cross') . ' (ID) : ' . $commentID;
                    $count++;
                }
                WPF()->notice->clear();
            }

            $commentsSyncOptionKey = '_wpf_' . $suffix . 'autocross_comments_' . $postID;
            if ($savedComments) {
                update_option($commentsSyncOptionKey, $savedComments, 'no');
            } else {
                delete_option($commentsSyncOptionKey);
            }
        }
        $complete = 0;
        if (($options->commentCrossingAdd && empty($savedComments)) || $options->commentCrossingAdd == 0) {
            array_shift($savedPosts);
        }
        $savedPostsOptionKey = '_wpf_' . $suffix . 'autocross_posts_' . $termID . '_' . $forumID;
        if ($savedPosts) {
            update_option($savedPostsOptionKey, $savedPosts, 'no');
        } else {
            delete_option($savedPostsOptionKey);
            $complete = 1;
        }
        return array('comments' => $comments, 'complete' => $complete);
    }

    public static function generateStatisticMessage($statistic) {
        $allPosts = $statistic['cross_posted'] + $statistic['skipped'];
        return '<strong><i>' . __('Done!', 'wpforo_cross') . '</i></strong><br>' .
                '<ul>' .
                '<li>' . __('Total', 'wpforo_cross') . ': ' . sprintf(_n('%s post', '%s posts', $allPosts, 'wpforo_cross'), $allPosts) . '</li>' .
                '<li>' . __('Cross-posted', 'wpforo_cross') . ': ' . sprintf(_n('%s post', '%s posts', $statistic['cross_posted'], 'wpforo_cross'), $statistic['cross_posted']) . '</li>' .
                '<li>' . __('Skipped (already cross-posted)', 'wpforo_cross') . ': ' . sprintf(_n('%s post', '%s posts', $statistic['skipped'], 'wpforo_cross'), $statistic['skipped']) . '</li>' .
                '</ul>';
    }
    
    public static function getPostTags($postID){
        $tags = [];
        $postTags = get_the_tags($postID);
        if($postTags){
            foreach ($postTags as $tag){
                $tags[] = $tag->name;
            }
        }
        return $tags;
    }

}
